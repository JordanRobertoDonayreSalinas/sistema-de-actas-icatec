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
     * Ruta: GET /usuario/monitoreo/modulo/triaje-especializada/{id}
     */
    public function index($id)
    {
        // 1. Obtener la cabecera del monitoreo
        $acta = CabeceraMonitoreo::with('establecimiento')->findOrFail($id);

        // 2. Validación de seguridad
        if ($acta->tipo_origen !== 'ESPECIALIZADA') {
            return redirect()->route('usuario.monitoreo.modulos', $id)
                ->with('error', 'Este módulo no corresponde al tipo de establecimiento.');
        }

        // 3. Buscar datos existentes (Clave: triaje_esp)
        $detalle = MonitoreoModulos::where('cabecera_monitoreo_id', $id)
                                    ->where('modulo_nombre', 'triaje_esp')
                                    ->first();

        // 4. Si no existe, creamos una instancia vacía
        if (!$detalle) {
            $detalle = new MonitoreoModulos();
            $detalle->contenido = []; 
        } else {
            if (is_string($detalle->contenido)) {
                $detalle->contenido = json_decode($detalle->contenido, true) ?? [];
            }
        }

        // 5. LÓGICA DE EQUIPOS (MODIFICADA: INICIO VACÍO)
        $equiposFormateados = [];

        // Si YA existen equipos guardados en la base de datos, los cargamos
        if (isset($detalle->contenido['equipos']) && is_array($detalle->contenido['equipos'])) {
            $equiposFormateados = collect($detalle->contenido['equipos'])->map(function($item) {
                // Convertimos el array asociativo a objeto para que la vista no de error
                return (object) $item;
            });
        } 
        // CASO CONTRARIO: Se envía un array vacío [] para que la tabla inicie sin filas.

        // 6. Retornar la vista
        return view('usuario.monitoreo.modulos_especializados.triaje', [
            'acta' => $acta,
            'detalle' => $detalle,
            'equipos' => $equiposFormateados
        ]);
    }

    /**
     * Guarda la información del módulo.
     * Ruta: POST /usuario/monitoreo/modulo/triaje-especializada/{id}
     */
    public function store(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $datosFormulario = $request->input('contenido', []);

            $registro = MonitoreoModulos::where('cabecera_monitoreo_id', $id)
                                        ->where('modulo_nombre', 'triaje_esp')
                                        ->first();

            $contenidoActual = [];

            if (!$registro) {
                $registro = new MonitoreoModulos();
                $registro->cabecera_monitoreo_id = $id;
                $registro->modulo_nombre = 'triaje_esp';
            } else {
                $contenidoActual = json_decode($registro->contenido, true) ?? [];
            }

            // Procesar Imagen
            if ($request->hasFile('foto_evidencia')) {
                $request->validate([
                    'foto_evidencia' => 'image|mimes:jpeg,png,jpg|max:10240'
                ]);

                if (!empty($contenidoActual['foto_evidencia'])) {
                    if (Storage::disk('public')->exists($contenidoActual['foto_evidencia'])) {
                        Storage::disk('public')->delete($contenidoActual['foto_evidencia']);
                    }
                }

                $path = $request->file('foto_evidencia')->store('evidencias_csmc/triaje', 'public');
                $datosFormulario['foto_evidencia'] = $path;
            } else {
                if (!empty($contenidoActual['foto_evidencia'])) {
                    $datosFormulario['foto_evidencia'] = $contenidoActual['foto_evidencia'];
                }
            }

            // Procesar Equipos: Guardamos lo que el usuario haya agregado en el formulario
            if ($request->has('equipos')) {
                $datosFormulario['equipos'] = $request->input('equipos');
            } else {
                // Si borró todos los equipos, guardamos un array vacío
                $datosFormulario['equipos'] = [];
            }

            $registro->contenido = json_encode($datosFormulario, JSON_UNESCAPED_UNICODE);
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