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
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class GestionAdministrativaController extends Controller
{
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

            // 1. Sincronizar Profesionales (Entrevistados)
            if (isset($datos['rrhh']) && !empty($datos['rrhh']['doc'])) {
                $info = $datos['rrhh'];
                Profesional::updateOrCreate(
                    ['doc' => trim($info['doc'])],
                    [
                        'tipo_doc'         => $info['tipo_doc'] ?? 'DNI',
                        'apellido_paterno' => mb_strtoupper(trim($info['apellido_paterno'] ?? ''), 'UTF-8'),
                        'apellido_materno' => mb_strtoupper(trim($info['apellido_materno'] ?? ''), 'UTF-8'),
                        'nombres'          => mb_strtoupper(trim($info['nombres'] ?? ''), 'UTF-8'),
                        'email'            => isset($info['email']) ? strtolower(trim($info['email'])) : null,
                        'telefono'         => $info['telefono'] ?? null,
                    ]
                );
            }

            // 2. Equipos de Cómputo (Actualizado para guardar texto en 'propio')
            EquipoComputo::where('cabecera_monitoreo_id', $id)->where('modulo', $modulo)->delete();
            
            if ($request->has('equipos') && is_array($request->equipos)) {
                foreach ($request->equipos as $eq) {
                    if (!empty($eq['descripcion'])) {
                        EquipoComputo::create([
                            'cabecera_monitoreo_id' => $id,
                            'modulo'      => $modulo,
                            'descripcion' => mb_strtoupper(trim($eq['descripcion']), 'UTF-8'),
                            'cantidad'    => 1,
                            'estado'      => $eq['estado'] ?? 'BUENO',
                            'nro_serie'   => mb_strtoupper($eq['nro_serie'] ?? '', 'UTF-8'),
                            // CAMBIO: Ahora guarda el valor directo (INSTITUCIONAL / PERSONAL) en lugar de 1/0
                            'propio'      => mb_strtoupper($eq['propio'] ?? 'PERSONAL', 'UTF-8'),
                        ]);
                    }
                }
            }

            // 3. Respuestas de Monitoreo
            DB::table('mon_respuesta_entrevistado')->updateOrInsert(
                ['cabecera_monitoreo_id' => $id, 'modulo' => $modulo],
                [
                    'doc_profesional'       => $datos['rrhh']['doc'] ?? null,
                    'recibio_capacitacion'  => $datos['recibio_capacitacion'] ?? 'NO',
                    'inst_que_lo_capacito'  => $datos['inst_que_lo_capacito'] ?? null,
                    'inst_a_quien_comunica' => $datos['inst_a_quien_comunica'] ?? null,
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
                $datos['foto_evidencia'] = $request->file('foto_evidencia')->store('evidencias_monitoreo', 'public');
            } elseif ($registroPrevio) {
                $datos['foto_evidencia'] = $registroPrevio->contenido['foto_evidencia'] ?? null;
            }

            // 5. Guardar JSON de detalle
            MonitoreoModulos::updateOrCreate(
                ['cabecera_monitoreo_id' => $id, 'modulo_nombre' => $modulo],
                ['contenido' => $datos]
            );

            DB::commit();
            return redirect()->route('usuario.monitoreo.modulos', $id)
                             ->with('success', 'Módulo 01 sincronizado correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error en GestionAdministrativaController@store: " . $e->getMessage());
            return back()->withInput()->withErrors(['error' => 'Error al guardar: ' . $e->getMessage()]);
        }
    }

    public function pdf($id)
    {
        $acta = CabeceraMonitoreo::with(['establecimiento'])->findOrFail($id);
        $detalle = MonitoreoModulos::where('cabecera_monitoreo_id', $id)->where('modulo_nombre', 'gestion_administrativa')->firstOrFail();
        $equipos = EquipoComputo::where('cabecera_monitoreo_id', $id)->where('modulo', 'gestion_administrativa')->get();
        
        $user = Auth::user();
        $monitor = [
            'nombre'    => mb_strtoupper("{$user->apellido_paterno} {$user->apellido_materno}, {$user->name}", 'UTF-8'),
            'tipo_doc'  => $user->tipo_documento ?? 'DNI',
            'documento' => $user->documento ?? $user->username ?? '________'
        ];

        $pdf = Pdf::loadView('usuario.monitoreo.pdf.gestion_administrativa', compact('acta', 'detalle', 'equipos', 'monitor'));
        return $pdf->stream("Acta_Gestion_Adm_{$id}.pdf");
    }
}
