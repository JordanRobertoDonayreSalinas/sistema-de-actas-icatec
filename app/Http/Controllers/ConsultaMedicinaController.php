<?php

namespace App\Http\Controllers;

use App\Models\CabeceraMonitoreo;
use App\Models\MonitoreoModulos;
use App\Models\Profesional;
use App\Models\EquipoComputo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ConsultaMedicinaController extends Controller
{
    public function index($id)
    {
        $acta = CabeceraMonitoreo::with('establecimiento')->findOrFail($id);
        $modulo = 'consulta_medicina';

        $equipos = EquipoComputo::where('cabecera_monitoreo_id', $id)
                                ->where('modulo', $modulo)
                                ->get();

        $detalle = MonitoreoModulos::where('cabecera_monitoreo_id', $id)
                    ->where('modulo_nombre', $modulo)
                    ->first();

        return view('usuario.monitoreo.modulos.consulta_medicina', compact('acta', 'detalle', 'equipos'));
    }

    public function store(Request $request, $id)
    {
        $request->validate([
            'foto_evidencia' => 'array|max:5',
            'foto_evidencia.*' => 'nullable|image|mimes:jpeg,png,jpg|max:4096',
        ]);

        try {
            DB::beginTransaction();

            $modulo = 'consulta_medicina';

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

            // ---------------------------------------------------------
            // 2. APLICAR REGLAS DE NEGOCIO (LIMPIEZA DE NULOS)
            // ---------------------------------------------------------
            
            // REGLA A: Si NO utiliza SIHCE -> Limpiar campos administrativos y soporte
            if (($datos['utiliza_sihce'] ?? '') === 'NO') {
                $datos['firmo_dj']               = null;
                $datos['firmo_confidencialidad'] = null;
                $datos['recibio_capacitacion']   = null;
                $datos['inst_capacitacion']      = null;
                $datos['comunica_a']             = null;
                $datos['medio_soporte']          = null;
            }

            // REGLA B: Si el Doc NO es DNI -> Limpiar campos de DNIe físico
            // Nota: Como ya corrimos el paso 1, comparamos con 'DNI' en mayúscula
            $tipoDoc = $datos['profesional']['tipo_doc'] ?? '';
            if ($tipoDoc !== 'DNI') {
                $datos['tipo_dni_fisico']  = null;
                $datos['dnie_version']     = null;
                $datos['dnie_firma_sihce'] = null;
                $datos['dni_observacion']  = null;
            }

            // REGLA C: Si NO recibió capacitación -> Limpiar institución
            if (($datos['recibio_capacitacion'] ?? '') === 'NO') {
                $datos['inst_capacitacion'] = null;
            }
            
            // REGLA E: Si NO se seleccionó tipo de conectividad -> Limpiar campos dependientes
            if (empty($datos['tipo_conectividad'])) {
                $datos['tipo_conectividad']  = null;
                $datos['wifi_fuente']        = null;
                $datos['operador_servicio']  = null;
            }

            // REGLA D:LÓGICA DE LIMPIEZA DE DATOS (DNI AZUL vs DNI ELECTRÓNICO)
            $tipoDni = $datos['tipo_dni_fisico'] ?? null;

            if ($tipoDni === 'AZUL') {
                // Si es DNI AZUL, no debe tener versión ni firma digital
                $datos['dnie_version'] = null;
                $datos['dnie_firma_sihce'] = null;
            }

            // ---------------------------------------------------------
            // 3. SINCRONIZACIÓN DE PROFESIONALES
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
                    ]
                );
            }

            // ---------------------------------------------------------
            // 4. GESTIÓN DE EQUIPOS (TABLA EXTERNA)
            // ---------------------------------------------------------
            EquipoComputo::where('cabecera_monitoreo_id', $id)->where('modulo', $modulo)->delete();
            
            if ($request->has('equipos') && is_array($request->equipos)) {
                foreach ($request->equipos as $eq) {
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

            if ($request->hasFile('foto_evidencia')) {
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
                // Subir nuevas fotos
                $fotosFinales = [];
                foreach ($request->file('foto_evidencia') as $file) {
                    // Guardamos en la carpeta pública
                    $path = $file->store('evidencias_monitoreo', 'public');
                    $fotosFinales[] = $path;
                }
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
                             ->with('success', 'Módulo 04 (Consulta Medicina) sincronizado correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error Módulo Medicina (Store) - Acta {$id}: " . $e->getMessage());
            return back()->withErrors(['error' => 'Error al guardar: ' . $e->getMessage()])->withInput();
        }
    }
}

