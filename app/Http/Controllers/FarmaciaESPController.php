<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CabeceraMonitoreo;
use App\Models\MonitoreoModulos;
use App\Models\EquipoComputo; // <--- [1] IMPORTANTE: FALTABA ESTE MODELO
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Profesional;

class FarmaciaESPController extends Controller
{
    public function index($id)
    {
        $monitoreo = CabeceraMonitoreo::with('establecimiento')->findOrFail($id);

        if ($monitoreo->tipo_origen !== 'ESPECIALIZADA') {
            return redirect()->route('usuario.monitoreo.modulos', $id)
                ->with('error', 'Este módulo no corresponde al tipo de establecimiento.');
        }

        // [2] AGREGADO: CONSULTA DE EQUIPOS
        // Recuperamos los equipos asociados a este monitoreo y a este módulo específico
        $equipos = EquipoComputo::where('cabecera_monitoreo_id', $id)
                                ->where('modulo', 'farmacia_esp')
                                ->get();

        $detalle = MonitoreoModulos::where('cabecera_monitoreo_id', $id)
                                    ->where('modulo_nombre', 'farmacia_esp')
                                    ->first();

        // 3. Si no existe, creamos una instancia vacía para evitar errores en los componentes
        if (!$detalle) {
            $detalle = new MonitoreoModulos();
            $detalle->contenido = []; // Inicializamos como array vacío
        }

        // 4. Preparar data suelta (por si la usas en inputs manuales fuera de componentes)
        $data = is_array($detalle->contenido) 
                ? $detalle->contenido 
                : json_decode($detalle->contenido, true);
        
        if (!is_array($data)) $data = [];

        // [3] AGREGADO: PASAR $equipos A LA VISTA
        return view('usuario.monitoreo.modulos_especializados.farmacia', compact('monitoreo', 'data', 'equipos', 'detalle'));
    }

    public function store(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $monitoreo = CabeceraMonitoreo::findOrFail($id);
            $modulo = 'farmacia_esp'; // Definimos el nombre del módulo para usarlo abajo

            // ---------------------------------------------------------
            // 1. OBTENER Y NORMALIZAR DATOS (TODO A MAYÚSCULAS)
            // ---------------------------------------------------------
            $datos = $request->input('contenido', []);

            // Usamos array_walk_recursive para recorrer todo el árbol JSON (incluyendo hijos)
            array_walk_recursive($datos, function (&$value, $key) {
                // Solo procesamos cadenas de texto
                if (is_string($value)) {
                    // EXCEPCIÓN A: El campo 'email' se queda tal cual (o lo forzamos a minúsculas luego)
                    if ($key === 'email') {
                        return; 
                    }
                    
                    // EXCEPCIÓN B: Rutas de imágenes (detectamos por extensión o carpeta)
                    // Esto protege 'foto_evidencia' si viniera como texto, aunque se procesa aparte
                    if (str_contains($value, 'evidencias_monitoreo/') || preg_match('/\.(jpg|jpeg|png)$/i', $value)) {
                        return;
                    }

                    // TODO LO DEMÁS -> A MAYÚSCULAS
                    $value = mb_strtoupper(trim($value), 'UTF-8');
                }
            });

            if ($request->has('comentario_esp')) {
                $datos['comentario_esp'] = mb_strtoupper($request->input('comentario_esp'), 'UTF-8');
            }
            // ---------------------------------------------------------
            // 2. SINCRONIZACIÓN DE PROFESIONALES
            // ---------------------------------------------------------
            if (isset($datos['profesional']) && !empty($datos['profesional']['doc'])) {
                Profesional::updateOrCreate(
                    ['doc' => trim($datos['profesional']['doc'])],
                    [
                        'tipo_doc'         => $datos['profesional']['tipo_doc'] ?? 'DNI',
                        'apellido_paterno' => mb_strtoupper(trim($datos['profesional']['apellido_paterno']), 'UTF-8'),
                        'apellido_materno' => mb_strtoupper(trim($datos['profesional']['apellido_materno']), 'UTF-8'),
                        'nombres'          => mb_strtoupper(trim($datos['profesional']['nombres']), 'UTF-8'),
                        'email'            => isset($datos['profesional']['email']) ? strtolower(trim($datos['profesional']['email'])) : null,
                        'telefono'         => $datos['profesional']['telefono'] ?? null,
                        'cargo'            => $datos['profesional']['cargo'] ?? null,
                    ]
                );
            }
            // ---------------------------------------------------------
            // A. GESTIÓN DE EQUIPOS (TABLA EXTERNA)
            // ---------------------------------------------------------
            // 1. Borramos los equipos anteriores de este módulo para evitar duplicados
            EquipoComputo::where('cabecera_monitoreo_id', $id)
                         ->where('modulo', $modulo)
                         ->delete();
            
            // 2. Insertamos los nuevos si vienen en el request
            if ($request->has('equipos') && is_array($request->equipos)) {
                foreach ($request->equipos as $eq) {
                    // Solo guardamos si tiene descripción
                    if (!empty($eq['descripcion'])) {
                        EquipoComputo::create([
                            'cabecera_monitoreo_id' => $id,
                            'modulo'      => $modulo,
                            'descripcion' => mb_strtoupper(trim($eq['descripcion']), 'UTF-8'),
                            'cantidad'    => (int)($eq['cantidad'] ?? 1),
                            'estado'      => $eq['estado'] ?? 'OPERATIVO',
                            'nro_serie'   => isset($eq['nro_serie']) ? mb_strtoupper(trim($eq['nro_serie']), 'UTF-8') : null,
                            'propio'      => $eq['propio'] ?? 'SERVICIO',
                            'observacion' => isset($eq['observacion']) ? mb_strtoupper(trim($eq['observacion']), 'UTF-8') : null,
                        ]);
                    }
                }
            }
            
            // ---------------------------------------------------------
            // 5. GESTIÓN DE ARCHIVOS (FOTOS)
            // ---------------------------------------------------------
            $registroPrevio = MonitoreoModulos::where('cabecera_monitoreo_id', $id)
                                ->where('modulo_nombre', $modulo)
                                ->first();
            $fotosFinales = [];
            // Recuperar fotos existentes del JSON previo
            if ($registroPrevio && isset($registroPrevio->contenido['foto_evidencia'])) {
                $prev = $registroPrevio->contenido['foto_evidencia'];
                $fotosFinales = is_array($prev) ? $prev : [$prev];
            }
            if ($request->hasFile('foto_esp_file')) {
                // --- NUEVO: BORRADO FÍSICO DE ARCHIVOS ANTERIORES ---
                if (count($fotosFinales) > 0) {
                    foreach ($fotosFinales as $pathViejo) {
                        // Verificamos si existe en el disco 'public' y lo borramos
                        if (Storage::disk('public')->exists($pathViejo)) {
                            Storage::disk('public')->delete($pathViejo);
                        }
                    }
                }
                // -----------------------------------------------------
                // 2. Subir la nueva foto única
                $file = $request->file('foto_esp_file');
                $path = $file->store('evidencias_esp', 'public');
                // 3. Guardarla en el array (como único elemento)
                $fotosFinales = [$path];
            }
            // Asignamos el array final al JSON
            $datos['foto_evidencia'] = $fotosFinales;

            // ---------------------------------------------------------
            // 6. GUARDAR JSON FINAL EN BASE DE DATOS
            // ---------------------------------------------------------
            MonitoreoModulos::updateOrCreate(
                ['cabecera_monitoreo_id' => $id, 'modulo_nombre' => $modulo],
                ['contenido' => $datos]
            );

            DB::commit();
            return redirect()->route('usuario.monitoreo.modulos', $id)
                             ->with('success', 'Módulo Farmacia ESP sincronizado correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error Módulo Farmacia ESP (Store) - Acta {$id}: " . $e->getMessage());
            return back()->withErrors(['error' => 'Error al guardar: ' . $e->getMessage()])->withInput();
        }
    }
}