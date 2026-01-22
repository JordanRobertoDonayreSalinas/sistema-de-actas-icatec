<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CabeceraMonitoreo;
use App\Models\MonitoreoModulos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FarmaciaESPController extends Controller
{
    /**
     * Muestra el formulario de "Admisión y Citas" específico para CSMC.
     * Ruta: GET /usuario/monitoreo/{id}/farmacia-especializada
     */
    public function index($id)
    {
        // 1. Obtener la cabecera del monitoreo
        $monitoreo = CabeceraMonitoreo::with('establecimiento')->findOrFail($id);

        // 2. Validación de seguridad
        if ($monitoreo->tipo_origen !== 'ESPECIALIZADA') {
            return redirect()->route('usuario.monitoreo.modulos', $id)
                ->with('error', 'Este módulo no corresponde al tipo de establecimiento.');
        }

        // 3. Buscar datos existentes (Clave: farmacia_esp)
        $registro = MonitoreoModulos::where('cabecera_monitoreo_id', $id)
                                    ->where('modulo_nombre', 'farmacia_esp')
                                    ->first();

        // 4. Decodificar JSON
        $data = $registro ? json_decode($registro->contenido, true) : [];

        return view('usuario.monitoreo.modulos_especializados.farmacia', compact('monitoreo', 'data'));
    }

    /**
     * Guarda la información del módulo.
     * Ruta: POST /usuario/monitoreo/{id}/farmacia-especializada
     */
    public function store(Request $request, $id)
    {
        // DEBUG: Si sigue sin guardar, descomenta la siguiente línea para ver qué llega
        // dd($request->all());

        try {
            DB::beginTransaction();

            // 1. Validar que exista la cabecera
            $monitoreo = CabeceraMonitoreo::findOrFail($id);

            // 2. Buscar si ya existe el registro de este módulo
            $registro = MonitoreoModulos::where('cabecera_monitoreo_id', $id)
                                        ->where('modulo_nombre', 'farmacia_esp')
                                        ->first();

            $contenidoActual = [];

            // 3. Si no existe, creamos la instancia y asignamos claves forzosamente
            if (!$registro) {
                $registro = new MonitoreoModulos();
                $registro->cabecera_monitoreo_id = $id;
                $registro->modulo_nombre = 'farmacia_esp';
            } else {
                // Si existe, recuperamos su contenido actual para no perder la foto
                $contenidoActual = json_decode($registro->contenido, true) ?? [];
            }

            // 4. Recoger datos del formulario (Quitamos token y archivo físico)
            $nuevosDatos = $request->except(['_token', 'foto_evidencia']);

            // 5. Procesar Imagen
            if ($request->hasFile('foto_evidencia')) {
                // Validación estricta de imagen
                $request->validate([
                    'foto_evidencia' => 'image|mimes:jpeg,png,jpg|max:10240' // 10MB Máx
                ]);

                // Borrar anterior si existe
                if (!empty($contenidoActual['foto_evidencia'])) {
                    if (Storage::disk('public')->exists($contenidoActual['foto_evidencia'])) {
                        Storage::disk('public')->delete($contenidoActual['foto_evidencia']);
                    }
                }

                // Guardar nueva
                $path = $request->file('foto_evidencia')->store('evidencias_csmc', 'public');
                $nuevosDatos['foto_evidencia'] = $path;
            } else {
                // Mantener anterior si no se subió nueva
                if (!empty($contenidoActual['foto_evidencia'])) {
                    $nuevosDatos['foto_evidencia'] = $contenidoActual['foto_evidencia'];
                }
            }

            // 6. Guardar JSON
            // JSON_UNESCAPED_UNICODE asegura que las tildes se guarden bien
            $registro->contenido = json_encode($nuevosDatos, JSON_UNESCAPED_UNICODE);
            
            $registro->save();

            DB::commit();

            return redirect()
                ->route('usuario.monitoreo.modulos', $id)
                ->with('success', 'Módulo de Farmacia CSMC guardado correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            // Esto imprimirá el error en tu archivo de log (storage/logs/laravel.log)
            Log::error("Error guardando Farmacia CSMC: " . $e->getMessage());
            
            return back()
                ->with('error', 'Error al guardar: ' . $e->getMessage())
                ->withInput();
        }
    }
}