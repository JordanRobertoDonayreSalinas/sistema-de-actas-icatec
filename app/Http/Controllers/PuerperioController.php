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
        
        $equipos = EquipoComputo::where('cabecera_monitoreo_id', $id)
            ->where('modulo', $this->modulo)
            ->get();
            
        $esHistorico = false;

        // Si no hay equipos actuales, buscamos del acta anterior para precargar
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
        
        $detalle = MonitoreoModulos::where('cabecera_monitoreo_id', $id)
            ->where('modulo_nombre', $this->modulo)
            ->first();
            
        return view('usuario.monitoreo.modulos.puerperio', compact('acta', 'detalle', 'equipos', 'esHistorico'));
    }

    public function store(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $datos = $request->input('contenido', []);

            // ----------------------------------------------------------------------
            // 1. FORZAR MAYÚSCULAS EN CAMPOS DE TEXTO DEL JSON
            // (Para asegurar que comentarios, observaciones, etc. se guarden bien)
            // ----------------------------------------------------------------------
            $camposTexto = [
                'denominacion_ambiente',
                'inst_a_quien_comunica',
                'medio_que_utiliza',
                'inst_que_lo_capacito',
                'observaciones_dni',
                'comentarios',
                'turno'
            ];

            foreach ($camposTexto as $campo) {
                if (isset($datos[$campo]) && is_string($datos[$campo])) {
                    $datos[$campo] = mb_strtoupper(trim($datos[$campo]), 'UTF-8');
                }
            }

            // ----------------------------------------------------------------------
            // 2. SINCRONIZAR PROFESIONAL (Corregido 'responsable' por 'rrhh')
            // ----------------------------------------------------------------------
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
            // 3. GUARDAR EQUIPOS (Con lógica de mayúsculas)
            // ----------------------------------------------------------------------
            EquipoComputo::where('cabecera_monitoreo_id', $id)
                ->where('modulo', $this->modulo)
                ->delete();
            
            if ($request->has('equipos')) {
                foreach ($request->equipos as $eq) {
                    if (!empty($eq['descripcion'])) {
                        
                        // Normalización de propiedad
                        $propiedad = !empty($eq['propio']) ? mb_strtoupper($eq['propio'], 'UTF-8') : 'PERSONAL';

                        EquipoComputo::create([
                            'cabecera_monitoreo_id' => $id,
                            'modulo'      => $this->modulo,
                            'descripcion' => mb_strtoupper(trim($eq['descripcion']), 'UTF-8'),
                            'cantidad'    => $eq['cantidad'] ?? 1, // Guardamos la cantidad real del form
                            'estado'      => mb_strtoupper($eq['estado'] ?? 'BUENO', 'UTF-8'),
                            'nro_serie'   => mb_strtoupper($eq['nro_serie'] ?? '', 'UTF-8'),
                            'propio'      => $propiedad,
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
                // Borrar anterior si existe
                if ($registroPrevio && isset($registroPrevio->contenido['foto_evidencia'])) {
                    $oldPath = $registroPrevio->contenido['foto_evidencia'];
                    if (is_string($oldPath) && Storage::disk('public')->exists($oldPath)) {
                        Storage::disk('public')->delete($oldPath);
                    }
                }
                $datos['foto_evidencia'] = $request->file('foto_evidencia')->store('evidencias_monitoreo', 'public');
            } elseif ($registroPrevio && isset($registroPrevio->contenido['foto_evidencia'])) {
                // Mantener la existente
                $datos['foto_evidencia'] = $registroPrevio->contenido['foto_evidencia'];
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
                             ->with('success', 'Módulo de Puerperio guardado correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error en PuerperioController@store: " . $e->getMessage());
            return back()->withInput()->withErrors(['error' => 'Error al guardar el módulo: ' . $e->getMessage()]);
        }
    }
}