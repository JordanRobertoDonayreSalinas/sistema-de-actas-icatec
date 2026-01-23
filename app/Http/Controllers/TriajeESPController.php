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
            // Asegurar que sea array
            if (is_string($detalle->contenido)) {
                $detalle->contenido = json_decode($detalle->contenido, true) ?? [];
            }
        }

        // 5. Preparar equipos (Usando data_get para evitar errores de índice indefinido)
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
     */
    public function store(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            // 1. Datos nuevos del formulario
            $nuevosDatos = $request->input('contenido', []);

            // ----------------------------------------------------------------------
            // LÓGICA DE RRHH (PROFESIONAL) - Fusión inteligente
            // ----------------------------------------------------------------------
            $datosRRHH = $request->input('rrhh');

            // Si rrhh viene dentro de contenido, lo extraemos
            if (empty($datosRRHH) && isset($nuevosDatos['rrhh'])) {
                $datosRRHH = $nuevosDatos['rrhh'];
            }

            // Si hay datos de RRHH, los preparamos
            if (!empty($datosRRHH) && !empty($datosRRHH['doc'])) {
                
                // Aseguramos que se guarde en el JSON bajo la llave 'rrhh'
                $nuevosDatos['rrhh'] = $datosRRHH;

                // Actualizar Tabla Maestra (Solo campos esenciales)
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
            // ----------------------------------------------------------------------

            // 2. Obtener registro actual de la BD para no perder datos no enviados
            $registro = MonitoreoModulos::firstOrNew([
                'cabecera_monitoreo_id' => $id,
                'modulo_nombre' => 'triaje_esp'
            ]);

            // Recuperar contenido anterior (si existe)
            $contenidoAnterior = is_string($registro->contenido) 
                ? (json_decode($registro->contenido, true) ?? []) 
                : ($registro->contenido ?? []);

            // 3. Procesar Imagen (Solo si se sube nueva)
            if ($request->hasFile('foto_evidencia')) {
                $request->validate([
                    'foto_evidencia' => 'image|mimes:jpeg,png,jpg|max:10240'
                ]);

                // Borrar anterior
                if (!empty($contenidoAnterior['foto_evidencia'])) {
                    if (Storage::disk('public')->exists($contenidoAnterior['foto_evidencia'])) {
                        Storage::disk('public')->delete($contenidoAnterior['foto_evidencia']);
                    }
                }

                $path = $request->file('foto_evidencia')->store('evidencias_csmc/triaje', 'public');
                $nuevosDatos['foto_evidencia'] = $path;
            } else {
                // Mantener la foto vieja si no se envió una nueva
                if (isset($contenidoAnterior['foto_evidencia'])) {
                    $nuevosDatos['foto_evidencia'] = $contenidoAnterior['foto_evidencia'];
                }
            }

            // 4. Procesar Equipos (Reemplazo total de la lista)
            if ($request->has('equipos')) {
                $nuevosDatos['equipos'] = $request->input('equipos');
            } else {
                // Si no se envían equipos, asumimos lista vacía (borrado)
                $nuevosDatos['equipos'] = [];
            }

            // 5. FUSIÓN FINAL: Usamos array_replace_recursive para que los nuevos datos
            // sobrescriban a los viejos, pero manteniendo claves viejas que no vinieron en el request
            // (Útil por si algún input disabled no se envió)
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