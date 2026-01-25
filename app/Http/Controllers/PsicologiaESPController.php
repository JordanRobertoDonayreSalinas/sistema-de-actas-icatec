<?php

namespace App\Http\Controllers;

use App\Models\CabeceraMonitoreo;
use App\Models\MonitoreoModulos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PsicologiaESPController extends Controller
{
    public function index($id)
    {
        $monitoreo = CabeceraMonitoreo::with('establecimiento')->findOrFail($id);

        if ($monitoreo->tipo_origen !== 'ESPECIALIZADA') {
            return redirect()->route('usuario.monitoreo.modulos', $id)->with('error', 'Acceso no permitido.');
        }

        $registro = MonitoreoModulos::where('cabecera_monitoreo_id', $id)
                                    ->where('modulo_nombre', 'sm_psicologia')
                                    ->first() ?? new MonitoreoModulos(['contenido' => []]);

        // Evitamos el error de json_decode si ya es un array por el casting del modelo
        $data = is_array($registro->contenido) ? $registro->contenido : (json_decode($registro->contenido, true) ?? []);

        return view('usuario.monitoreo.modulos_especializados.psicologia', compact('monitoreo', 'data', 'registro'));
    }

    public function store(Request $request, $id)
{
    try {
        DB::beginTransaction();

        $registro = MonitoreoModulos::where('cabecera_monitoreo_id', $id)
                                    ->where('modulo_nombre', 'sm_psicologia')
                                    ->first() ?? new MonitoreoModulos();

        // 1. Preparamos el array de contenido
        $contenido = $request->input('contenido', []);

        // AGREGAMOS EL COMENTARIO AL ARRAY (Para no tocar el modelo)
        $contenido['comentario_especialista'] = $request->input('comentario_esp');

        // 2. Procesar foto y guardarla en el array de contenido
        if ($request->hasFile('foto_esp_file')) {
            // Borrar foto previa si existe en el JSON
            if (isset($registro->contenido['foto_final_path'])) {
                Storage::disk('public')->delete($registro->contenido['foto_final_path']);
            }
            $path = $request->file('foto_esp_file')->store('evidencias_psicologia', 'public');
            $contenido['foto_final_path'] = $path;
        } else {
            // Mantener la foto anterior si no se sube una nueva
            $contenido['foto_final_path'] = $registro->contenido['foto_final_path'] ?? null;
        }

        $registro->cabecera_monitoreo_id = $id;
        $registro->modulo_nombre = 'sm_psicologia';
        $registro->contenido = $contenido; // Aquí se guarda todo lo nuevo
        $registro->save();

        // 3. GESTIÓN DE EQUIPOS (Igual que antes)
        $inventario = $contenido['inventario'] ?? [];
        if (!empty($inventario)) {
            DB::table('mon_equipos_computo')
                ->where('cabecera_id', $id)
                ->where('modulo', 'sm_psicologia')
                ->delete();

            foreach ($inventario as $item) {
                if (!empty($item['descripcion'])) {
                    DB::table('mon_equipos_computo')->insert([
                        'cabecera_id' => $id,
                        'modulo'      => 'sm_psicologia',
                        'tipo_equipo' => strtoupper($item['descripcion']),
                        'estado'      => strtoupper($item['estado'] ?? 'OPERATIVO'),
                        'serie_cod'   => ($item['tipo_codigo'] ?? '') . ': ' . ($item['codigo'] ?? 'S/N'),
                        'propiedad'   => strtoupper($item['propiedad'] ?? 'COMPARTIDO'),
                        'observacion' => strtoupper($item['observacion'] ?? ''),
                        'created_at'  => now(),
                        'updated_at'  => now(),
                    ]);
                }
            }
        }

        DB::commit();
        return redirect()->route('usuario.monitoreo.salud_mental_group.index', $id)
                         ->with('success', 'Psicología guardada exitosamente.');

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error("Error en Psicología: " . $e->getMessage());
        return back()->with('error', 'Error: ' . $e->getMessage())->withInput();
    }
}
}