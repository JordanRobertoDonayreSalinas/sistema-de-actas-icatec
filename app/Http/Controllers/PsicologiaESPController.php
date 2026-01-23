<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CabeceraMonitoreo;
use App\Models\MonitoreoModulos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PsicologiaESPController extends Controller
{
    /**
     * Muestra el formulario de Psicología.
     * Ruta Sugerida: GET /usuario/monitoreo/{id}/psicologia
     */
    public function index($id)
    {
        $monitoreo = CabeceraMonitoreo::with('establecimiento')->findOrFail($id);

        if ($monitoreo->tipo_origen !== 'ESPECIALIZADA') {
            return redirect()->route('usuario.monitoreo.modulos', $id)
                ->with('error', 'Este módulo no corresponde al tipo de establecimiento.');
        }

        $registro = MonitoreoModulos::where('cabecera_monitoreo_id', $id)
                                    ->where('modulo_nombre', 'sm_psicologia')
                                    ->first();
        
        if (!$registro) {
            $registro = new MonitoreoModulos();
            $registro->contenido = []; 
        }

        $data = is_array($registro->contenido) ? $registro->contenido : json_decode($registro->contenido, true) ?? [];

        // CAMBIO AQUÍ: Se agrega 'registro' al compact
        return view('usuario.monitoreo.modulos_especializados.psicologia', compact('monitoreo', 'data', 'registro'));
    }

    /**
     * Guarda la información del módulo de Psicología.
     */
    public function store(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $monitoreo = CabeceraMonitoreo::findOrFail($id);

            // Buscamos por el nombre del módulo 'psicologia'
            $registro = MonitoreoModulos::where('cabecera_monitoreo_id', $id)
                                        ->where('modulo_nombre', 'sm_psicologia')
                                        ->first();

            $contenidoActual = [];

            if (!$registro) {
                $registro = new MonitoreoModulos();
                $registro->cabecera_monitoreo_id = $id;
                $registro->modulo_nombre = 'sm_psicologia'; // Cambiado a psicologia
            } else {
                $contenidoActual = json_decode($registro->contenido, true) ?? [];
            }

            $nuevosDatos = $request->except(['_token', 'foto_evidencia']);

            // 5. Procesar Imagen (Carpeta específica: evidencias_psicologia)
            if ($request->hasFile('foto_evidencia')) {
                $request->validate([
                    'foto_evidencia' => 'image|mimes:jpeg,png,jpg|max:10240' 
                ]);

                if (!empty($contenidoActual['foto_evidencia'])) {
                    if (Storage::disk('public')->exists($contenidoActual['foto_evidencia'])) {
                        Storage::disk('public')->delete($contenidoActual['foto_evidencia']);
                    }
                }

                // Guardar en subcarpeta de psicología
                $path = $request->file('foto_evidencia')->store('evidencias_psicologia', 'public');
                $nuevosDatos['foto_evidencia'] = $path;
            } else {
                if (!empty($contenidoActual['foto_evidencia'])) {
                    $nuevosDatos['foto_evidencia'] = $contenidoActual['foto_evidencia'];
                }
            }

            $registro->contenido = json_encode($nuevosDatos, JSON_UNESCAPED_UNICODE);
            $registro->save();

            DB::commit();

            return redirect()
                ->route('usuario.monitoreo.modulos', $id)
                ->with('success', 'Módulo de Psicología guardado correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error guardando Psicología: " . $e->getMessage());
            
            return back()
                ->with('error', 'Error al guardar: ' . $e->getMessage())
                ->withInput();
        }
    }
}