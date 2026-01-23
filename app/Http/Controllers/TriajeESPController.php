<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CabeceraMonitoreo;
use App\Models\MonitoreoModulos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TriajeESPController extends Controller
{
    /**
     * Muestra el formulario de "Triaje" específico para CSMC.
     */
    public function index($id)
    {
        // 1. Obtener la cabecera
        $acta = CabeceraMonitoreo::with('establecimiento')->findOrFail($id);

        // 2. Validación de seguridad
        if ($acta->tipo_origen !== 'ESPECIALIZADA') {
            return redirect()->route('usuario.monitoreo.modulos', $id)
                ->with('error', 'Este módulo no corresponde al tipo de establecimiento.');
        }

        // 3. Buscar datos existentes
        $detalle = MonitoreoModulos::where('cabecera_monitoreo_id', $id)
                                   ->where('modulo_nombre', 'triaje_esp')
                                   ->first();

        // 4. Inicializar si no existe
        if (!$detalle) {
            $detalle = new MonitoreoModulos();
            $detalle->contenido = []; 
        } else {
            if (is_string($detalle->contenido)) {
                $detalle->contenido = json_decode($detalle->contenido, true) ?? [];
            }
        }

        // 5. Preparar equipos
        $listaEquiposRaw = data_get($detalle->contenido, 'equipos', []);
        $equiposFormateados = [];
        
        if (is_array($listaEquiposRaw)) {
            $equiposFormateados = collect($listaEquiposRaw)->map(function($item) {
                return (object) $item;
            });
        } 

        return view('usuario.monitoreo.modulos_especializados.triaje', [
            'acta' => $acta,
            'detalle' => $detalle,
            'equipos' => $equiposFormateados
        ]);
    }

    /**
     * Guarda la información del módulo.
     * SOLUCIÓN: Captura inputs que están fuera del array 'contenido'.
     */
    public function store(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            // 1. Obtener datos base del array 'contenido'
            $nuevosDatos = $request->input('contenido', []);

            // ==============================================================================
            // PARCHE: CAPTURAR DATOS DE COMPONENTES QUE NO TIENEN EL NAME CORRECTO
            // ==============================================================================

            // A. Capturar COMENTARIO (Componente 7 envía 'comentario_esp' suelto)
            if ($request->has('comentario_esp')) {
                $nuevosDatos['comentario_esp'] = $request->input('comentario_esp');
            }

            // ==============================================================================

            // 2. LÓGICA DE RRHH (PROFESIONAL)
            $datosRRHH = $request->input('rrhh');
            if (empty($datosRRHH) && isset($nuevosDatos['rrhh'])) {
                $datosRRHH = $nuevosDatos['rrhh'];
            }

            if (!empty($datosRRHH) && !empty($datosRRHH['doc'])) {
                $nuevosDatos['rrhh'] = $datosRRHH;

                // Actualizar Tabla Maestra
                DB::table('mon_profesionales')->updateOrInsert(
                    ['doc' => $datosRRHH['doc']],
                    [
                        'tipo_doc'          => $datosRRHH['tipo_doc'] ?? 'DNI',
                        'nombres'           => mb_strtoupper($datosRRHH['nombres'] ?? '', 'UTF-8'),
                        'apellido_paterno'  => mb_strtoupper($datosRRHH['apellido_paterno'] ?? '', 'UTF-8'),
                        'apellido_materno'  => mb_strtoupper($datosRRHH['apellido_materno'] ?? '', 'UTF-8'),
                        'cargo'             => mb_strtoupper($datosRRHH['cargo'] ?? '', 'UTF-8'),
                        'updated_at'        => now(),
                    ]
                );
            }

            // 3. Obtener registro actual de la BD
            $registro = MonitoreoModulos::firstOrNew([
                'cabecera_monitoreo_id' => $id,
                'modulo_nombre' => 'triaje_esp'
            ]);

            $contenidoAnterior = is_string($registro->contenido) 
                ? (json_decode($registro->contenido, true) ?? []) 
                : ($registro->contenido ?? []);

            // ==============================================================================
            // B. Capturar FOTO (Componente 7 envía 'foto_esp_file' en lugar de 'foto_evidencia')
            // ==============================================================================
            
            // Detectar cuál nombre de input está enviando el archivo
            $inputNameFoto = null;
            if ($request->hasFile('foto_esp_file')) {
                $inputNameFoto = 'foto_esp_file';
            } elseif ($request->hasFile('foto_evidencia')) {
                $inputNameFoto = 'foto_evidencia';
            }

            if ($inputNameFoto) {
                $request->validate([
                    $inputNameFoto => 'image|mimes:jpeg,png,jpg|max:10240'
                ]);

                // Borrar foto anterior (La clave en JSON que usa tu vista es 'foto_url_esp')
                $claveFotoEnJson = 'foto_url_esp'; 

                if (!empty($contenidoAnterior[$claveFotoEnJson])) {
                    if (Storage::disk('public')->exists($contenidoAnterior[$claveFotoEnJson])) {
                        Storage::disk('public')->delete($contenidoAnterior[$claveFotoEnJson]);
                    }
                }

                // Guardar nueva foto
                $path = $request->file($inputNameFoto)->store('evidencias_csmc/triaje', 'public');
                
                // GUARDAR CON LA CLAVE QUE ESPERA TU VISTA ('foto_url_esp')
                $nuevosDatos['foto_url_esp'] = $path;
                
                // Opcional: guardar también como 'foto_evidencia' por si acaso
                $nuevosDatos['foto_evidencia'] = $path;
            } else {
                // Mantener la foto vieja si no se subió una nueva
                if (isset($contenidoAnterior['foto_url_esp'])) {
                    $nuevosDatos['foto_url_esp'] = $contenidoAnterior['foto_url_esp'];
                }
            }
            // ==============================================================================

            // 4. Procesar Equipos
            if ($request->has('equipos')) {
                $nuevosDatos['equipos'] = $request->input('equipos');
            } else {
                $nuevosDatos['equipos'] = [];
            }

            // 5. FUSIÓN FINAL (Sobrescribir datos viejos con los nuevos)
            $contenidoFinal = array_replace_recursive($contenidoAnterior, $nuevosDatos);

            // 6. Guardar
            $registro->contenido = json_encode($contenidoFinal, JSON_UNESCAPED_UNICODE);
            $registro->save();

            DB::commit();

            return redirect()
                ->route('usuario.monitoreo.modulos', $id)
                ->with('success', 'Módulo de Triaje CSMC guardado correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error guardando Triaje CSMC: " . $e->getMessage());
            
            return back()
                ->with('error', 'Error al guardar: ' . $e->getMessage())
                ->withInput();
        }
    }
}