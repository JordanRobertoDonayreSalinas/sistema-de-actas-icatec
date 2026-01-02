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

class FuaElectronicoController extends Controller
{
    public function index($id)
    {
        $acta = CabeceraMonitoreo::with('establecimiento')->findOrFail($id);
        $modulo = 'fua_electronico';

        $equipos = EquipoComputo::where('cabecera_monitoreo_id', $id)
                                ->where('modulo', $modulo)
                                ->get();

        if ($equipos->isEmpty()) {
            $ultimaActaId = CabeceraMonitoreo::where('establecimiento_id', $acta->establecimiento_id)
                ->where('id', '<', $id) 
                ->orderBy('id', 'desc')
                ->value('id');

            if ($ultimaActaId) {
                $equipos = EquipoComputo::where('cabecera_monitoreo_id', $ultimaActaId)
                                        ->where('modulo', $modulo)
                                        ->get();
            }
        }

        $detalle = MonitoreoModulos::where('cabecera_monitoreo_id', $id)
                    ->where('modulo_nombre', $modulo)
                    ->first();

        return view('usuario.monitoreo.modulos.fua_electronico', compact('acta', 'detalle', 'equipos'));
    }

    public function store(Request $request, $id)
    {
        $request->validate([
            'foto_evidencia' => 'array|max:5',
            'foto_evidencia.*' => 'nullable|image|mimes:jpeg,png,jpg|max:4096',
        ]);

        try {
            DB::beginTransaction();

            $modulo = 'fua_electronico';
            $datos = $request->input('contenido', []);

            if (isset($datos['recibio_capacitacion']) && $datos['recibio_capacitacion'] === 'NO') {
                $datos['inst_capacitacion'] = null;
            }

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

            $registroPrevio = MonitoreoModulos::where('cabecera_monitoreo_id', $id)
                                ->where('modulo_nombre', $modulo)
                                ->first();

            $fotosFinales = [];
            if ($registroPrevio && isset($registroPrevio->contenido['foto_evidencia'])) {
                $prev = $registroPrevio->contenido['foto_evidencia'];
                $fotosFinales = is_array($prev) ? $prev : [$prev];
            }

            if ($request->hasFile('foto_evidencia')) {
                if (count($fotosFinales) > 0) {
                    foreach ($fotosFinales as $pathViejo) {
                        if (Storage::disk('public')->exists($pathViejo)) {
                            Storage::disk('public')->delete($pathViejo);
                        }
                    }
                }

                $fotosFinales = [];
                foreach ($request->file('foto_evidencia') as $file) {
                    $path = $file->store('evidencias_monitoreo', 'public');
                    $fotosFinales[] = $path;
                }
            }

            $datos['foto_evidencia'] = $fotosFinales;

            MonitoreoModulos::updateOrCreate(
                ['cabecera_monitoreo_id' => $id, 'modulo_nombre' => $modulo],
                ['contenido' => $datos]
            );

            DB::commit();
            return redirect()->route('usuario.monitoreo.modulos', $id)
                             ->with('success', 'Módulo 14 (FUA Electrónico) sincronizado correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error Módulo FUA Electrónico (Store) - Acta {$id}: " . $e->getMessage());
            return back()->withErrors(['error' => 'Error al guardar: ' . $e->getMessage()])->withInput();
        }
    }
}