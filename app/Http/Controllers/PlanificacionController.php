<?php

namespace App\Http\Controllers;

use App\Models\CabeceraMonitoreo;
use App\Models\MonitoreoModulos;
use App\Models\EquipoComputo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PlanificacionController extends Controller
{
    private $modulo = 'planificacion_familiar';

    public function index($id)
    {
        $acta = CabeceraMonitoreo::with('establecimiento')->findOrFail($id);
        
        // 1. Cargar Equipos (Arrastre histórico)
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

        // --- TRUCO DE COMPATIBILIDAD FORZADA ---
        // Como tu componente usa $eq->propio para el 'selected', 
        // nos aseguramos de que el valor sea idéntico a las opciones del HTML.
        $equipos = $equipos->map(function($item) {
            // Limpiamos espacios y estandarizamos para que el Blade haga match
            $item->propio = trim(strtoupper($item->propio ?? 'ESTABLECIMIENTO'));
            return $item;
        });

        // 2. BUSCAR DETALLE
        $detalle = DB::table('mon_detalle_modulos')
                    ->where('cabecera_monitoreo_id', $id)
                    ->where('modulo_nombre', $this->modulo)
                    ->first();

        if (!$detalle) {
            $detalle = MonitoreoModulos::where('cabecera_monitoreo_id', $id)
                        ->where('modulo_nombre', $this->modulo)
                        ->first();
        }

        if ($detalle) {
            $detalle->contenido = is_string($detalle->contenido) ? json_decode($detalle->contenido, true) : $detalle->contenido;
        }

        return view('usuario.monitoreo.modulos.planificacion_familiar', compact('acta', 'detalle', 'equipos'));
    }

    public function store(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $acta = CabeceraMonitoreo::findOrFail($id);
            $datosForm = $request->input('contenido', []);
            $personal = $datosForm['personal'] ?? null;
            $equiposForm = $request->input('equipos', []);

            // --- 1. GESTIÓN DE FOTOS ---
            $foto1 = $request->input('foto_1_actual'); 
            $foto2 = $request->input('foto_2_actual');

            if ($request->hasFile('foto_evidencia_1')) {
                if ($foto1) Storage::disk('public')->delete($foto1);
                $foto1 = $request->file('foto_evidencia_1')->store('evidencias/planificacion', 'public');
            }
            if ($request->hasFile('foto_evidencia_2')) {
                if ($foto2) Storage::disk('public')->delete($foto2);
                $foto2 = $request->file('foto_evidencia_2')->store('evidencias/planificacion', 'public');
            }

            // --- 2. GUARDAR EN mon_detalle_modulos ---
            $nombreFull = mb_strtoupper(($personal['nombre'] ?? '').' '.($personal['apellido_paterno'] ?? '').' '.($personal['apellido_materno'] ?? ''), 'UTF-8');
            
            DB::table('mon_detalle_modulos')->updateOrInsert(
                ['cabecera_monitoreo_id' => $id, 'modulo_nombre' => $this->modulo],
                [
                    'personal_nombre' => !empty(trim($nombreFull)) ? $nombreFull : 'SIN NOMBRE',
                    'personal_dni'    => $personal['dni'] ?? null,
                    'personal_turno'  => mb_strtoupper($personal['turno'] ?? 'N/A', 'UTF-8'),
                    'personal_roles'  => mb_strtoupper($personal['rol'] ?? 'RESPONSABLE', 'UTF-8'),
                    'contenido'       => json_encode($datosForm),
                    'foto_1'          => $foto1,
                    'foto_2'          => $foto2,
                    'updated_at'      => now()
                ]
            );

            // --- 3. GUARDAR EN TABLA PROFESIONALES ---
            if (!empty($personal['dni'])) {
                DB::table('mon_profesionales')->updateOrInsert(
                    ['doc' => $personal['dni']], 
                    [
                        'nombres'          => mb_strtoupper($personal['nombre'] ?? 'SIN NOMBRE', 'UTF-8'),
                        'apellido_paterno' => mb_strtoupper($personal['apellido_paterno'] ?? '', 'UTF-8'),
                        'apellido_materno' => mb_strtoupper($personal['apellido_materno'] ?? '', 'UTF-8'),
                        'email'            => mb_strtoupper($personal['email'] ?? '', 'UTF-8'),
                        'telefono'         => mb_strtoupper($personal['contacto'] ?? '', 'UTF-8'),
                        'updated_at'       => now(),
                        'created_at'       => now()
                    ]
                );
            }

            // --- 4. SINCRONIZAR EQUIPOS (AJUSTADO AL COMPONENTE) ---
            // Borramos para evitar duplicados y re-insertamos con la llave correcta
            EquipoComputo::where('cabecera_monitoreo_id', $id)->where('modulo', $this->modulo)->delete();

            if (!empty($equiposForm)) {
                foreach ($equiposForm as $eq) {
                    if (!empty($eq['descripcion'])) {
                        
                        // EL SECRETO: Tu componente usa 'propiedad', pero tu tabla usa 'propio'.
                        // Mapeamos el dato del formulario al nombre de tu columna en DB.
                        $valorCapturado = $eq['propiedad'] ?? ($eq['propio'] ?? 'ESTABLECIMIENTO');

                        EquipoComputo::create([
                            'cabecera_monitoreo_id' => $id,
                            'modulo'        => $this->modulo,
                            'descripcion'   => mb_strtoupper($eq['descripcion'], 'UTF-8'),
                            'cantidad'      => $eq['cantidad'] ?? 1,
                            'estado'        => mb_strtoupper($eq['estado'] ?? 'BUENO', 'UTF-8'),
                            'propio'        => trim(strtoupper($valorCapturado)), 
                            'nro_serie'     => !empty($eq['nro_serie']) ? mb_strtoupper($eq['nro_serie'], 'UTF-8') : null,
                            'observaciones' => !empty($eq['observaciones']) ? mb_strtoupper($eq['observaciones'], 'UTF-8') : null,
                        ]);
                    }
                }
            }

            DB::commit();
            return redirect()->route('usuario.monitoreo.modulos', $id)->with('success', 'Planificación Familiar guardada exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error PF Store: " . $e->getMessage());
            return back()->with('error', 'Error al guardar: ' . $e->getMessage())->withInput();
        }
    }
}
