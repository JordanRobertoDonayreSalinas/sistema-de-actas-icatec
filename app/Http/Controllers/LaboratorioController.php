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

class LaboratorioController extends Controller
{
    private $modulo = 'laboratorio';

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
        $equipos = EquipoComputo::where('cabecera_monitoreo_id', $id)->where('modulo', $this->modulo)->get();
        $esHistorico = false;

        // Lógica de carga histórica de equipos si la acta actual no tiene registros
        if ($equipos->isEmpty()) {
            $ultimaActaId = CabeceraMonitoreo::where('establecimiento_id', $acta->establecimiento_id)
                ->where('id', '<', $id)->orderBy('id', 'desc')->value('id');
            if ($ultimaActaId) {
                $equipos = EquipoComputo::where('cabecera_monitoreo_id', $ultimaActaId)->where('modulo', $this->modulo)->get();
                $esHistorico = true;
            }
        }
        
        $detalle = MonitoreoModulos::where('cabecera_monitoreo_id', $id)->where('modulo_nombre', $this->modulo)->first();
        return view('usuario.monitoreo.modulos.laboratorio', compact('acta', 'detalle', 'equipos', 'esHistorico'));
    }

    public function store(Request $request, $id)
    {
        $request->validate([
            'foto_evidencia' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        try {
            DB::beginTransaction();
            $datos = $request->input('contenido', []);

            // 1. Sincronizar Profesional (Responsable de Laboratorio)
            if (isset($datos['responsable']) && !empty($datos['responsable']['doc'])) {
                Profesional::updateOrCreate(
                    ['doc' => trim($datos['responsable']['doc'])],
                    [
                        'tipo_doc'         => $datos['responsable']['tipo_doc'] ?? 'DNI',
                        'apellido_paterno' => mb_strtoupper(trim($datos['responsable']['apellido_paterno'] ?? ''), 'UTF-8'),
                        'apellido_materno' => mb_strtoupper(trim($datos['responsable']['apellido_materno'] ?? ''), 'UTF-8'),
                        'nombres'          => mb_strtoupper(trim($datos['responsable']['nombres'] ?? ''), 'UTF-8'),
                        'email'            => isset($datos['responsable']['email']) ? strtolower(trim($datos['responsable']['email'])) : null,
                        'telefono'         => $datos['responsable']['telefono'] ?? null,
                    ]
                );
            }

            // 2. Equipos (Corregido para manejar categorías de texto en 'propio')
            EquipoComputo::where('cabecera_monitoreo_id', $id)->where('modulo', $this->modulo)->delete();
            if ($request->has('equipos')) {
                foreach ($request->equipos as $eq) {
                    if (!empty($eq['descripcion'])) {
                        
                        // Normalización de la propiedad del equipo
                        $valorPropio = mb_strtoupper($eq['propio'] ?? 'PERSONAL', 'UTF-8');
                        if (!in_array($valorPropio, ['ESTABLECIMIENTO', 'SERVICIO', 'PERSONAL'])) {
                            $valorPropio = 'PERSONAL';
                        }

                        EquipoComputo::create([
                            'cabecera_monitoreo_id' => $id,
                            'modulo'      => $this->modulo,
                            'descripcion' => mb_strtoupper(trim($eq['descripcion']), 'UTF-8'),
                            'cantidad'    => 1,
                            'estado'      => $eq['estado'] ?? 'BUENO',
                            'nro_serie'   => mb_strtoupper($eq['nro_serie'] ?? 'S/N', 'UTF-8'),
                            'propio'      => $valorPropio, // Ahora guarda el texto descriptivo
                        ]);
                    }
                }
            }

            // 3. Gestión de Foto de Evidencia
            $registroPrevio = MonitoreoModulos::where('cabecera_monitoreo_id', $id)->where('modulo_nombre', $this->modulo)->first();
            if ($request->hasFile('foto_evidencia')) {
                // Borrar foto anterior si existe
                if ($registroPrevio && isset($registroPrevio->contenido['foto_evidencia'])) {
                    Storage::disk('public')->delete($registroPrevio->contenido['foto_evidencia']);
                }
                $datos['foto_evidencia'] = $request->file('foto_evidencia')->store('evidencias_monitoreo', 'public');
            } elseif ($registroPrevio) {
                // Mantener la foto actual si no se subió una nueva
                $datos['foto_evidencia'] = $registroPrevio->contenido['foto_evidencia'] ?? null;
            }

            // 4. Guardar JSON Detalle del Módulo
            MonitoreoModulos::updateOrCreate(
                ['cabecera_monitoreo_id' => $id, 'modulo_nombre' => $this->modulo],
                ['contenido' => $datos]
            );

            DB::commit();
            return redirect()->route('usuario.monitoreo.modulos', $id)->with('success', 'Módulo de Laboratorio guardado con éxito.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error en LaboratorioController@store: " . $e->getMessage());
            return back()->withInput()->withErrors(['error' => 'Hubo un error al guardar: ' . $e->getMessage()]);
        }
    }
}