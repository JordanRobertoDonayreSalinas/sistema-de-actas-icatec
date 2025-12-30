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

class GestionAdministrativaController extends Controller
{
    /**
     * Busca un profesional en la tabla maestra 'mon_profesionales'.
     */
    public function buscarProfesional($doc)
    {
        $profesional = Profesional::where('doc', trim($doc))->first();

        if ($profesional) {
            return response()->json([
                'exists'           => true,
                'tipo_doc'         => $profesional->tipo_doc,
                'apellido_paterno' => $profesional->apellido_paterno,
                'apellido_materno' => $profesional->apellido_materno,
                'nombres'          => $profesional->nombres,
                'email'            => $profesional->email,
                'telefono'         => $profesional->telefono,
            ]);
        }
        return response()->json(['exists' => false]);
    }

    /**
     * Carga la interfaz del Módulo 01.
     */
    public function index($id)
    {
        $acta = CabeceraMonitoreo::with(['establecimiento'])->findOrFail($id);
        $modulo = 'gestion_administrativa';

        $equipos = EquipoComputo::where('cabecera_monitoreo_id', $id)
                                ->where('modulo', $modulo)
                                ->get();

        // Lógica de Guía Histórica si no hay equipos en el acta actual
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

        return view('usuario.monitoreo.modulos.gestion_administrativa', compact('acta', 'detalle', 'equipos'));
    }

    /**
     * Guarda los datos y sincroniza las tablas.
     */
    public function store(Request $request, $id)
    {
        $request->validate([
            'foto_evidencia' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        try {
            DB::beginTransaction();

            $modulo = 'gestion_administrativa';
            $datos = $request->input('contenido', []);

            // 1. SINCRONIZACIÓN DE PROFESIONALES
            $profesionalesKeys = ['rrhh', 'programador'];
            foreach ($profesionalesKeys as $key) {
                if (isset($datos[$key]) && !empty($datos[$key]['doc'])) {
                    Profesional::updateOrCreate(
                        ['doc' => trim($datos[$key]['doc'])],
                        [
                            'tipo_doc'         => $datos[$key]['tipo_doc'] ?? 'DNI',
                            'apellido_paterno' => mb_strtoupper(trim($datos[$key]['apellido_paterno']), 'UTF-8'),
                            'apellido_materno' => mb_strtoupper(trim($datos[$key]['apellido_materno']), 'UTF-8'),
                            'nombres'          => mb_strtoupper(trim($datos[$key]['nombres']), 'UTF-8'),
                            'email'            => isset($datos[$key]['email']) ? strtolower(trim($datos[$key]['email'])) : null,
                            'telefono'         => $datos[$key]['telefono'] ?? null,
                        ]
                    );
                }
            }

            // 2. GESTIÓN DE EQUIPOS (INCLUYE NRO_SERIE Y OBSERVACION)
            EquipoComputo::where('cabecera_monitoreo_id', $id)->where('modulo', $modulo)->delete();
            
            if ($request->has('equipos') && is_array($request->equipos)) {
                foreach ($request->equipos as $eq) {
                    if (!empty($eq['descripcion'])) {
                        EquipoComputo::create([
                            'cabecera_monitoreo_id' => $id,
                            'modulo'      => $modulo,
                            'descripcion' => mb_strtoupper(trim($eq['descripcion']), 'UTF-8'),
                            'cantidad'    => (int)($eq['cantidad'] ?? 1),
                            'estado'      => $eq['estado'] ?? 'BUENO',
                            'nro_serie'   => isset($eq['nro_serie']) ? mb_strtoupper(trim($eq['nro_serie']), 'UTF-8') : null, // NUEVO
                            'propio'      => (isset($eq['propio']) && $eq['propio'] === 'SI') ? 1 : 0,
                            'observacion' => isset($eq['observacion']) ? mb_strtoupper(trim($eq['observacion']), 'UTF-8') : null, // NUEVO
                        ]);
                    }
                }
            }

            // 3. ACTUALIZAR RESPUESTA DEL ENTREVISTADO
            $mapInst = ['MINSA' => 1, 'DIRESA' => 2, 'OTROS' => 3, 'JEFE DE ESTABLECIMIENTO' => 4, 'OTRO' => 5];
            DB::table('mon_respuesta_entrevistado')->updateOrInsert(
                ['cabecera_monitoreo_id' => $id, 'modulo' => $modulo],
                [
                    'doc_profesional'       => $datos['rrhh']['doc'] ?? null,
                    'recibio_capacitacion'  => (isset($datos['recibio_capacitacion']) && $datos['recibio_capacitacion'] === 'SI') ? 1 : 0,
                    'inst_que_lo_capacito'  => $mapInst[$datos['inst_que_lo_capacito'] ?? ''] ?? null,
                    'inst_a_quien_comunica' => $mapInst[$datos['inst_a_quien_comunica'] ?? ''] ?? null,
                    'medio_que_utiliza'     => $datos['medio_que_utiliza'] ?? null,
                    'updated_at'            => now()
                ]
            );

            // 4. GESTIÓN DE ARCHIVOS
            $registroPrevio = MonitoreoModulos::where('cabecera_monitoreo_id', $id)
                                ->where('modulo_nombre', $modulo)
                                ->first();

            if ($request->hasFile('foto_evidencia')) {
                if ($registroPrevio && isset($registroPrevio->contenido['foto_evidencia'])) {
                    Storage::disk('public')->delete($registroPrevio->contenido['foto_evidencia']);
                }
                $path = $request->file('foto_evidencia')->store('evidencias_monitoreo', 'public');
                $datos['foto_evidencia'] = $path;
            } elseif ($registroPrevio && isset($registroPrevio->contenido['foto_evidencia'])) {
                $datos['foto_evidencia'] = $registroPrevio->contenido['foto_evidencia'];
            }

            // 5. GUARDADO DEL JSON
            MonitoreoModulos::updateOrCreate(
                ['cabecera_monitoreo_id' => $id, 'modulo_nombre' => $modulo],
                ['contenido' => $datos]
            );

            DB::commit();
            return redirect()->route('usuario.monitoreo.modulos', $id)
                             ->with('success', 'Módulo 01 sincronizado correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error Módulo 01 (Store) - Acta {$id}: " . $e->getMessage());
            return back()->withErrors(['error' => 'Error al guardar: ' . $e->getMessage()])->withInput();
        }
    }
}