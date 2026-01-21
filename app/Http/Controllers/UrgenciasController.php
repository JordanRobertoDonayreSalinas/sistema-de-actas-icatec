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
                'cargo'            => $profesional->cargo,
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
        
        $equipos = EquipoComputo::where('cabecera_monitoreo_id', $id)
                                ->where('modulo', $this->modulo)
                                ->get();
                                
        $esHistorico = false;

        if ($equipos->isEmpty()) {
            $ultimaActaId = CabeceraMonitoreo::where('establecimiento_id', $acta->establecimiento_id)
                ->where('id', '<', $id)
                ->orderBy('id', 'desc')
                ->value('id');
                
            if ($ultimaActaId) {
                $equipos = EquipoComputo::where('cabecera_monitoreo_id', $ultimaActaId)
                                        ->where('modulo', $this->modulo)
                                        ->get();
                $esHistorico = true;
            }
        }
        
        // 2. Cargar detalle guardado como JSON
        $detalle = MonitoreoModulos::where('cabecera_monitoreo_id', $id)
                                   ->where('modulo_nombre', $this->modulo)
                                   ->first();
        
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

            // ----------------------------------------------------------------------
            // 1. FORZAR MAYÚSCULAS EN CAMPOS DE TEXTO DEL JSON
            // ----------------------------------------------------------------------
            $camposTexto = [
                'inst_a_quien_comunica',
                'medio_que_utiliza',
                'inst_que_lo_capacito', // Si aplica
                'observaciones_dni',
                'comentarios',
                'turno',
                'triaje_prioridad', // Aunque venga de select, aseguramos
                'tiempo_espera'
            ];

            foreach ($camposTexto as $campo) {
                if (isset($datos[$campo]) && is_string($datos[$campo])) {
                    $datos[$campo] = mb_strtoupper(trim($datos[$campo]), 'UTF-8');
                }
            }

            // ----------------------------------------------------------------------
            // 2. SINCRONIZAR PROFESIONAL (Corregido 'responsable' por 'rrhh')
            // ----------------------------------------------------------------------
            // La vista envía los datos en contenido[rrhh][...]
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

            // ----------------------------------------------------------------------
            // 3. GESTIÓN DE EQUIPOS (Con lógica de mayúsculas y validación)
            // ----------------------------------------------------------------------
            EquipoComputo::where('cabecera_monitoreo_id', $id)
                         ->where('modulo', $this->modulo)
                         ->delete();
            
            if ($request->has('equipos')) {
                foreach ($request->equipos as $eq) {
                    if (!empty($eq['descripcion'])) {
                        
                        // Validar propiedad del equipo (Lista Blanca)
                        $valorPropio = mb_strtoupper($eq['propio'] ?? 'PERSONAL', 'UTF-8');
                        $valoresPermitidos = ['PERSONAL', 'ESTABLECIMIENTO', 'SERVICIO', 'COMPARTIDO', 'EXCLUSIVO'];

                        if (!in_array($valorPropio, $valoresPermitidos)) {
                            $valorPropio = 'PERSONAL'; 
                        }

                        EquipoComputo::create([
                            'cabecera_monitoreo_id' => $id,
                            'modulo'      => $this->modulo,
                            'descripcion' => mb_strtoupper(trim($eq['descripcion']), 'UTF-8'),
                            'cantidad'    => $eq['cantidad'] ?? 1,
                            'estado'      => mb_strtoupper($eq['estado'] ?? 'BUENO', 'UTF-8'),
                            'nro_serie'   => mb_strtoupper($eq['nro_serie'] ?? 'S/N', 'UTF-8'),
                            'propio'      => $valorPropio,
                            'observacion' => isset($eq['observacion']) ? mb_strtoupper(trim($eq['observacion']), 'UTF-8') : null,
                        ]);
                    }
                }
            }

            // ----------------------------------------------------------------------
            // 4. GESTIÓN DE FOTO DE EVIDENCIA
            // ----------------------------------------------------------------------
            $registroPrevio = MonitoreoModulos::where('cabecera_monitoreo_id', $id)
                                              ->where('modulo_nombre', $this->modulo)
                                              ->first();
            
            if ($request->hasFile('foto_evidencia')) {
                // Eliminar foto previa si existe
                if ($registroPrevio && isset($registroPrevio->contenido['foto_evidencia'])) {
                    $oldPath = $registroPrevio->contenido['foto_evidencia'];
                    if (is_string($oldPath) && Storage::disk('public')->exists($oldPath)) {
                        Storage::disk('public')->delete($oldPath);
                    }
                }
                $datos['foto_evidencia'] = $request->file('foto_evidencia')->store('evidencias_monitoreo', 'public');
            } elseif ($registroPrevio && isset($registroPrevio->contenido['foto_evidencia'])) {
                // Mantener foto anterior
                $datos['foto_evidencia'] = $registroPrevio->contenido['foto_evidencia'] ?? null;
            } else {
                $datos['foto_evidencia'] = null;
            }

            // ----------------------------------------------------------------------
            // 5. GUARDAR JSON FINAL
            // ----------------------------------------------------------------------
            MonitoreoModulos::updateOrCreate(
                ['cabecera_monitoreo_id' => $id, 'modulo_nombre' => $this->modulo],
                ['contenido' => $datos]
            );

            DB::commit();
            return redirect()->route('usuario.monitoreo.modulos', $id)
                             ->with('success', 'Módulo de Urgencias y Emergencias guardado con éxito.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error en UrgenciasController@store: " . $e->getMessage());
            return back()->withInput()->withErrors(['error' => 'Hubo un error al guardar: ' . $e->getMessage()]);
        }
    }
}