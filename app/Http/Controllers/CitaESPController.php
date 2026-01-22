<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CabeceraMonitoreo;
use App\Models\MonitoreoModulos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class CitaESPController extends Controller
{
    /**
     * Muestra el formulario de "Admisión y Citas" específico para CSMC.
     * Ruta: GET /usuario/monitoreo/{id}/citas-especializada
     */
    public function index($id)
    {
        // 1. Obtener la cabecera del monitoreo
        $monitoreo = CabeceraMonitoreo::with('establecimiento')->findOrFail($id);

        // 2. Validación de seguridad: Verificar que sea una IPRESS Especializada
        if ($monitoreo->tipo_origen !== 'ESPECIALIZADA') {
            return redirect()->route('usuario.monitoreo.modulos', $id)
                ->with('error', 'Este módulo no corresponde al tipo de establecimiento.');
        }

        // 3. Buscar datos existentes usando la clave estandarizada 'citas_esp'
        $registro = MonitoreoModulos::where('cabecera_monitoreo_id', $id)
                                    ->where('modulo_nombre', 'citas_esp') // <--- CLAVE CORREGIDA
                                    ->first();

        // 4. Decodificar el JSON guardado (si existe) para llenar el formulario
        $data = $registro ? json_decode($registro->contenido, true) : [];

        // 5. Retornar la vista especializada
        return view('usuario.monitoreo.modulos_especializados.citas', compact('monitoreo', 'data'));
    }

    /**
     * Guarda la información del módulo.
     * Ruta: POST /usuario/monitoreo/{id}/citas-especializada
     */
    public function store(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $monitoreo = CabeceraMonitoreo::findOrFail($id);

            // 1. Buscar registro previo o crear una nueva instancia
            // IMPORTANTE: Usamos 'citas_esp' para que coincida con el tablero y se marque en verde
            $registro = MonitoreoModulos::firstOrNew([
                'cabecera_monitoreo_id' => $id,
                'modulo_nombre' => 'citas_esp' // <--- CLAVE CORREGIDA
            ]);

            // 2. Obtener datos actuales para gestionar la foto (no perderla si no se sube una nueva)
            $contenidoActual = $registro->exists ? json_decode($registro->contenido, true) : [];

            // 3. Recoger todos los campos del formulario excepto token y archivo
            $nuevosDatos = $request->except(['_token', 'foto_evidencia']);

            // 4. Procesar Subida de Imagen
            if ($request->hasFile('foto_evidencia')) {
                $request->validate([
                    'foto_evidencia' => 'image|mimes:jpeg,png,jpg|max:5120' // Máx 5MB
                ]);

                // Borrar imagen anterior si existe
                if (!empty($contenidoActual['foto_evidencia'])) {
                    Storage::disk('public')->delete($contenidoActual['foto_evidencia']);
                }

                // Guardar la nueva imagen
                $path = $request->file('foto_evidencia')->store('evidencias_csmc', 'public');
                $nuevosDatos['foto_evidencia'] = $path;
            } else {
                // Si no subió foto nueva, mantener la ruta de la anterior
                if (!empty($contenidoActual['foto_evidencia'])) {
                    $nuevosDatos['foto_evidencia'] = $contenidoActual['foto_evidencia'];
                }
            }

            // 5. Guardar en la base de datos (columna JSON)
            $registro->contenido = json_encode($nuevosDatos);
            $registro->save();

            DB::commit();

            // 6. Redirigir al panel de módulos
            return redirect()
                ->route('usuario.monitoreo.modulos', $id)
                ->with('success', 'Módulo de Citas CSMC guardado correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al guardar el módulo: ' . $e->getMessage())->withInput();
        }
    }
}