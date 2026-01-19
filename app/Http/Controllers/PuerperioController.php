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

class PuerperioController extends Controller
{
    private $modulo = 'puerperio';

    public function index($id)
    {
        $acta = CabeceraMonitoreo::with(['establecimiento'])->findOrFail($id);
        $equipos = EquipoComputo::where('cabecera_monitoreo_id', $id)->where('modulo', $this->modulo)->get();
        $esHistorico = false;

        if ($equipos->isEmpty()) {
            $ultimaActaId = CabeceraMonitoreo::where('establecimiento_id', $acta->establecimiento_id)
                ->where('id', '<', $id)->orderBy('id', 'desc')->value('id');
            if ($ultimaActaId) {
                $equipos = EquipoComputo::where('cabecera_monitoreo_id', $ultimaActaId)->where('modulo', $this->modulo)->get();
                $esHistorico = true;
            }
        }
        
        $detalle = MonitoreoModulos::where('cabecera_monitoreo_id', $id)->where('modulo_nombre', $this->modulo)->first();
        return view('usuario.monitoreo.modulos.puerperio', compact('acta', 'detalle', 'equipos', 'esHistorico'));
    }

    public function store(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $datos = $request->input('contenido', []);

            // 1. Sincronizar Responsable
            if (isset($datos['responsable']) && !empty($datos['responsable']['doc'])) {
                Profesional::updateOrCreate(
                    ['doc' => trim($datos['responsable']['doc'])],
                    [
                        'tipo_doc'         => $datos['responsable']['tipo_doc'] ?? 'DNI',
                        // Se fuerza mayúscula en nombres y apellidos
                        'apellido_paterno' => mb_strtoupper(trim($datos['responsable']['apellido_paterno'] ?? ''), 'UTF-8'),
                        'apellido_materno' => mb_strtoupper(trim($datos['responsable']['apellido_materno'] ?? ''), 'UTF-8'),
                        'nombres'          => mb_strtoupper(trim($datos['responsable']['nombres'] ?? ''), 'UTF-8'),
                    ]
                );
            }

            // 2. Guardar Equipos
            EquipoComputo::where('cabecera_monitoreo_id', $id)->where('modulo', $this->modulo)->delete();
            
            if ($request->has('equipos')) {
                foreach ($request->equipos as $eq) {
                    if (!empty($eq['descripcion'])) {
                        
                        // Validar propiedad del equipo (Lista Blanca)
                        $valorPropio = mb_strtoupper(trim($eq['propio'] ?? 'PERSONAL'), 'UTF-8');
                        $valoresPermitidos = ['PERSONAL', 'ESTABLECIMIENTO', 'SERVICIO', 'COMPARTIDO', 'EXCLUSIVO'];

                        if (!in_array($valorPropio, $valoresPermitidos)) {
                            $valorPropio = 'PERSONAL'; 
                        }

                        EquipoComputo::create([
                            'cabecera_monitoreo_id' => $id,
                            'modulo'      => $this->modulo,
                            // Descripción en Mayúscula
                            'descripcion' => mb_strtoupper(trim($eq['descripcion']), 'UTF-8'),
                            'cantidad'    => 1,
                            'estado'      => $eq['estado'] ?? 'BUENO',
                            // Serie en Mayúscula
                            'nro_serie'   => mb_strtoupper($eq['nro_serie'] ?? 'S/N', 'UTF-8'),
                            'propio'      => $valorPropio,
                            
                            // --- CAMBIO AQUÍ: OBSERVACIÓN EN MAYÚSCULA ---
                            'observacion' => isset($eq['observacion']) 
                                ? mb_strtoupper(trim($eq['observacion']), 'UTF-8') 
                                : null,
                        ]);
                    }
                }
            }

            // 3. Foto de Evidencia
            $registroPrevio = MonitoreoModulos::where('cabecera_monitoreo_id', $id)->where('modulo_nombre', $this->modulo)->first();
            if ($request->hasFile('foto_evidencia')) {
                if ($registroPrevio && isset($registroPrevio->contenido['foto_evidencia'])) {
                    Storage::disk('public')->delete($registroPrevio->contenido['foto_evidencia']);
                }
                $datos['foto_evidencia'] = $request->file('foto_evidencia')->store('evidencias_monitoreo', 'public');
            } elseif ($registroPrevio) {
                $datos['foto_evidencia'] = $registroPrevio->contenido['foto_evidencia'] ?? null;
            }

            // 4. Guardar JSON de contenido del módulo
            MonitoreoModulos::updateOrCreate(
                ['cabecera_monitoreo_id' => $id, 'modulo_nombre' => $this->modulo],
                ['contenido' => $datos]
            );

            DB::commit();
            return redirect()->route('usuario.monitoreo.modulos', $id)->with('success', 'Módulo de Puerperio guardado correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error en PuerperioController@store: " . $e->getMessage());
            return back()->withErrors(['error' => 'Error al guardar el módulo: ' . $e->getMessage()]);
        }
    }
}