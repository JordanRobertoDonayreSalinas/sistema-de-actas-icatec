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

        $monitoreo = CabeceraMonitoreo::findOrFail($id);

        // 1. GESTIÓN DEL REGISTRO EN mon_monitoreo_modulos
        $registro = MonitoreoModulos::where('cabecera_monitoreo_id', $id)
                                    ->where('modulo_nombre', 'sm_psicologia')
                                    ->first() ?? new MonitoreoModulos();

        $registro->cabecera_monitoreo_id = $id;
        $registro->modulo_nombre = 'sm_psicologia';
        $registro->comentario_esp = $request->comentario_esp;

        // Foto de evidencia final
        if ($request->hasFile('foto_esp_file')) {
            if ($registro->foto_url_esp) Storage::disk('public')->delete($registro->foto_url_esp);
            $registro->foto_url_esp = $request->file('foto_esp_file')->store('evidencias_psicologia', 'public');
        }

        // Capturamos el contenido y lo guardamos en la tabla modular
        $contenido = $request->input('contenido', []);
        $registro->contenido = $contenido;
        $registro->save();

        // 2. GESTIÓN DE EQUIPOS EN mon_equipos_computo (Tabla Independiente)
        // Extraemos el inventario del array principal
        $inventario = isset($contenido['inventario']) ? $contenido['inventario'] : [];

        if (is_array($inventario) && count($inventario) > 0) {
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
        // Redirección al panel de Nivel 2
        return redirect()->route('usuario.monitoreo.salud_mental_group.index', $id)
                         ->with('success', 'Módulo de Psicología guardado con éxito.');

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error("Error Fatal Guardando Psicología: " . $e->getMessage());
        return back()->with('error', 'Error crítico: ' . $e->getMessage())->withInput();
    }
}
}