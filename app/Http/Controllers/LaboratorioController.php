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

class LaboratorioController extends Controller
{
    private $modulo = 'laboratorio';

    // ... (Mantén las funciones buscarProfesional e index igual que antes) ...
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
                'cargo'            => $profesional->cargo,
            ]);
        }
        return response()->json(['exists' => false]);
    }

    public function index($id)
    {
        $acta = CabeceraMonitoreo::with(['establecimiento'])->findOrFail($id);
        
        $equipos = EquipoComputo::where('cabecera_monitoreo_id', $id)
            ->where('modulo', $this->modulo)
            ->get();

        if ($equipos->isEmpty()) {
            $ultimaActaId = CabeceraMonitoreo::where('establecimiento_id', $acta->establecimiento_id)
                ->where('id', '<', $id)
                ->orderBy('id', 'desc')
                ->value('id');

            if ($ultimaActaId) {
                $equipos = EquipoComputo::where('cabecera_monitoreo_id', $ultimaActaId)
                    ->where('modulo', $this->modulo)
                    ->get();
            }
        }
        
        $detalle = MonitoreoModulos::where('cabecera_monitoreo_id', $id)
                    ->where('modulo_nombre', $this->modulo)
                    ->first();

        return view('usuario.monitoreo.modulos.laboratorio', compact('acta', 'detalle', 'equipos'));
    }

    public function store(Request $request, $id)
    {
        $request->validate([
            'contenido.fecha' => 'required|date',
            'foto_evidencia'  => 'nullable|image|mimes:jpeg,png,jpg|max:4096',
        ]);

        try {
            DB::beginTransaction();
            $datos = $request->input('contenido', []);

            // 1. Sincronizar Profesional
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
                        'cargo'            => isset($info['cargo']) ? mb_strtoupper(trim($info['cargo']), 'UTF-8') : null,
                    ]
                );
            }

            // 2. Sincronizar Equipos (AQUÍ ESTABA EL PROBLEMA DE PROPIEDAD)
            EquipoComputo::where('cabecera_monitoreo_id', $id)
                ->where('modulo', $this->modulo)
                ->delete();

            if ($request->has('equipos') && is_array($request->equipos)) {
                foreach ($request->equipos as $eq) {
                    // Validamos que tenga descripción para guardarlo
                    if (!empty($eq['descripcion'])) {
                        
                        // CORRECCIÓN: Aceptamos el valor que venga del formulario directamente
                        // Si no viene nada, por defecto es PERSONAL.
                        $propiedad = !empty($eq['propio']) ? mb_strtoupper($eq['propio'], 'UTF-8') : 'PERSONAL';

                        EquipoComputo::create([
                            'cabecera_monitoreo_id' => $id,
                            'modulo'      => $this->modulo,
                            'descripcion' => mb_strtoupper(trim($eq['descripcion']), 'UTF-8'),
                            'cantidad'    => $eq['cantidad'] ?? 1, // Aseguramos que guarde la cantidad
                            'estado'      => $eq['estado'] ?? 'BUENO',
                            'nro_serie'   => mb_strtoupper($eq['nro_serie'] ?? '', 'UTF-8'),
                            'propio'      => $propiedad, // Aquí guardará EXCLUSIVO o COMPARTIDO
                            // CORRECCIÓN: Aseguramos mapear 'observacion'
                            'observacion' => isset($eq['observacion']) ? mb_strtoupper(trim($eq['observacion']), 'UTF-8') : null,
                        ]);
                    }
                }
            }

            // 3. Sincronizar Respuestas
            DB::table('mon_respuesta_entrevistado')->updateOrInsert(
                ['cabecera_monitoreo_id' => $id, 'modulo' => $this->modulo],
                [
                    'doc_profesional'       => $datos['rrhh']['doc'] ?? null,
                    'recibio_capacitacion'  => $datos['recibio_capacitacion'] ?? 'NO',
                    'inst_que_lo_capacito'  => $datos['inst_que_lo_capacito'] ?? null,
                    'inst_a_quien_comunica' => $datos['inst_a_quien_comunica'] ?? null,
                    'medio_que_utiliza'     => $datos['medio_que_utiliza'] ?? null,
                    'updated_at'            => now()
                ]
            );

            // 4. Gestión de Evidencia Fotográfica
            $registroPrevio = MonitoreoModulos::where('cabecera_monitoreo_id', $id)
                                ->where('modulo_nombre', $this->modulo)
                                ->first();

            if ($request->hasFile('foto_evidencia')) {
                if ($registroPrevio && !empty($registroPrevio->contenido['foto_evidencia'])) {
                    $oldPath = $registroPrevio->contenido['foto_evidencia'];
                    if (is_string($oldPath) && Storage::disk('public')->exists($oldPath)) {
                        Storage::disk('public')->delete($oldPath);
                    }
                }
                $datos['foto_evidencia'] = $request->file('foto_evidencia')->store('evidencias_monitoreo', 'public');
            } elseif ($registroPrevio && isset($registroPrevio->contenido['foto_evidencia'])) {
                $datos['foto_evidencia'] = $registroPrevio->contenido['foto_evidencia'];
            } else {
                $datos['foto_evidencia'] = null;
            }

            // 5. Guardar JSON
            MonitoreoModulos::updateOrCreate(
                ['cabecera_monitoreo_id' => $id, 'modulo_nombre' => $this->modulo],
                ['contenido' => $datos]
            );

            DB::commit();
            return redirect()->route('usuario.monitoreo.modulos', $id)
                             ->with('success', 'Módulo de Laboratorio guardado con éxito.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error en LaboratorioController@store: " . $e->getMessage());
            return back()->withInput()->withErrors(['error' => 'Hubo un error al guardar: ' . $e->getMessage()]);
        }
    }
}