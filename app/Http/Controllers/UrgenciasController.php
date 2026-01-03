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

class UrgenciasController extends Controller
{
    // Definimos el nombre del módulo para que coincida en la base de datos
    private $modulo = 'urgencias';

    /**
     * Endpoint para búsqueda de profesionales vía AJAX
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
     * Muestra la vista del módulo
     */
    public function index($id)
    {
        // 1. Cargar acta y equipos (Lógica de carga histórica incluida)
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
        
        // 2. Cargar detalle guardado como JSON
        $detalle = MonitoreoModulos::where('cabecera_monitoreo_id', $id)->where('modulo_nombre', $this->modulo)->first();
        
        // Retornamos la vista en la ruta correcta
        return view('usuario.monitoreo.modulos.urgencias', compact('acta', 'detalle', 'equipos', 'esHistorico'));
    }

    /**
     * Guarda la información del módulo
     */
    public function store(Request $request, $id)
    {
        $request->validate([
            'foto_evidencia' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        try {
            DB::beginTransaction();
            $datos = $request->input('contenido', []);

            // 1. Sincronizar Profesional (Responsable del Módulo)
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

            // 2. Gestión de Equipos de Cómputo / Tecnológicos
            EquipoComputo::where('cabecera_monitoreo_id', $id)->where('modulo', $this->modulo)->delete();
            if ($request->has('equipos')) {
                foreach ($request->equipos as $eq) {
                    if (!empty($eq['descripcion'])) {
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
                            'propio'      => $valorPropio,
                        ]);
                    }
                }
            }

            // 3. Gestión de Archivo de Imagen (Evidencia)
            $registroPrevio = MonitoreoModulos::where('cabecera_monitoreo_id', $id)->where('modulo_nombre', $this->modulo)->first();
            if ($request->hasFile('foto_evidencia')) {
                // Eliminar foto previa si existe para ahorrar espacio
                if ($registroPrevio && isset($registroPrevio->contenido['foto_evidencia'])) {
                    Storage::disk('public')->delete($registroPrevio->contenido['foto_evidencia']);
                }
                $datos['foto_evidencia'] = $request->file('foto_evidencia')->store('evidencias_monitoreo', 'public');
            } elseif ($registroPrevio) {
                // Mantener foto anterior si no se sube una nueva
                $datos['foto_evidencia'] = $registroPrevio->contenido['foto_evidencia'] ?? null;
            }

            // 4. Guardar o Actualizar el JSON en mon_detalle_modulos
            MonitoreoModulos::updateOrCreate(
                ['cabecera_monitoreo_id' => $id, 'modulo_nombre' => $this->modulo],
                ['contenido' => $datos]
            );

            DB::commit();
            return redirect()->route('usuario.monitoreo.modulos', $id)->with('success', 'Módulo de Urgencias y Emergencias guardado con éxito.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error en UrgenciasController@store: " . $e->getMessage());
            return back()->withInput()->withErrors(['error' => 'Hubo un error al guardar: ' . $e->getMessage()]);
        }
    }
}