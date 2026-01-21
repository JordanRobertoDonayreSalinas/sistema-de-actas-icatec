<?php

namespace App\Http\Controllers;

use App\Models\CabeceraMonitoreo;
use App\Models\MonitoreoModulos;
use App\Models\EquipoComputo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class FarmaciaController extends Controller
{
    private $modulo = 'farmacia';

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

        // 2. BUSCAR DETALLE (Prioridad tabla mon_detalle_modulos)
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

        $fechaParaVista = $detalle->fecha_registro ?? $acta->fecha;

        return view('usuario.monitoreo.modulos.farmacia', compact('acta', 'detalle', 'equipos', 'fechaParaVista'));
    }

    public function store(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $acta = CabeceraMonitoreo::findOrFail($id);

            // 1. CAPTURAR LA FECHA
            $fecha_monitoreo = $request->input('fecha_monitoreo') ?? ($acta->fecha ?? now()->format('Y-m-d'));
            $acta->fecha = $fecha_monitoreo;
            $acta->save();
            
            // 2. CAPTURA DE DATOS BASE
            $datosForm = $request->input('contenido', []);
            $personal = $datosForm['personal'] ?? null;
            $equiposForm = $request->input('equipos', []);

            // --- 1. GESTIÓN DE FOTOS ---
            $foto1 = $request->input('foto_1_actual'); 
            $foto2 = $request->input('foto_2_actual');

            if ($request->hasFile('foto_evidencia_1')) {
                if ($foto1) Storage::disk('public')->delete($foto1);
                $foto1 = $request->file('foto_evidencia_1')->store('evidencias/farmacia', 'public');
            }
            if ($request->hasFile('foto_evidencia_2')) {
                if ($foto2) Storage::disk('public')->delete($foto2);
                $foto2 = $request->file('foto_evidencia_2')->store('evidencias/farmacia', 'public');
            }

            // --- 3. PROCESAR DATOS DE DNI Y DOCUMENTACIÓN (ANTES DE GUARDAR) ---
            $tipoDniFisico = $request->input('contenido.dni_firma.tipo_dni_fisico', 'AZUL');
            
            $dniData = [
                'tipo_dni_fisico' => $tipoDniFisico,
                'dnie_version'    => ($tipoDniFisico === 'AZUL') ? null : $request->input('contenido.dni_firma.dnie_version'),
                'firma_sihce'     => ($tipoDniFisico === 'AZUL') ? null : $request->input('contenido.dni_firma.firma_sihce'),
                'observaciones'   => $request->input('contenido.dni_firma.observaciones') // Captura de nuevas obs
            ];

            $docData = [
                'firma_dj'               => $request->input('contenido.documentacion.firma_dj'),
                'firma_confidencialidad' => $request->input('contenido.documentacion.firma_confidencialidad')
            ];

            // --- 4. CONSOLIDAR EL JSON ---
            $datosForm['equipos_data'] = $equiposForm;
            $datosForm['fecha_registro'] = $fecha_monitoreo;
            $datosForm['dni_firma'] = $dniData;
            $datosForm['documentacion'] = $docData;

            // --- 3. GUARDAR EN mon_detalle_modulos ---
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
                    'fecha_registro'  => $fecha_monitoreo,
                    'updated_at'      => now()
                ]
            );

            // --- 4. GUARDAR EN TABLA MAESTRA DE PROFESIONALES (mon_profesionales) ---
            // Solo si el DNI no está vacío para evitar el error Column not found o Integrity constraint
            if (!empty($personal['dni'])) {

                $profesionFinal = $personal['profesion'] ?? null;
                // Si seleccionó OTROS, usamos el valor del campo de texto
                if ($profesionFinal === 'OTROS' && !empty($personal['profesion_otro'])) {
                    $profesionFinal = $personal['profesion_otro'];
                }
                DB::table('mon_profesionales')->updateOrInsert(
                    ['doc' => $personal['dni']], 
                    [
                        'nombres'          => mb_strtoupper($personal['nombre'] ?? 'SIN NOMBRE', 'UTF-8'),
                        'apellido_paterno' => mb_strtoupper($personal['apellido_paterno'] ?? '', 'UTF-8'),
                        'apellido_materno' => mb_strtoupper($personal['apellido_materno'] ?? '', 'UTF-8'),
                        'email'            => mb_strtoupper($personal['email'] ?? '', 'UTF-8'),
                        'telefono'         => mb_strtoupper($personal['contacto'] ?? '', 'UTF-8'),
                        'cargo'            => mb_strtoupper($profesionFinal, 'UTF-8'),
                        'updated_at'       => now(),
                        'created_at'       => now()
                    ]
                );
            }
            // --- 4. GUARDAR EN TABLA mon_monitoreo_modulos (Respaldo) ---
            MonitoreoModulos::updateOrCreate(
                ['cabecera_monitoreo_id' => $id, 'modulo_nombre' => $this->modulo],
                ['contenido' => $datosForm]
            );

            // --- 5. SINCRONIZAR EQUIPOS EN TABLA INDEPENDIENTE ---
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
                            'observacion' => !empty($eq['observacion']) ? mb_strtoupper($eq['observacion'], 'UTF-8') : null,
                        ]);
                    }
                }
            }

            DB::commit();
            return redirect()->route('usuario.monitoreo.modulos', $id)->with('success', 'Módulo Farmacia guardado correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error Farmacia Store: " . $e->getMessage());
            return back()->with('error', 'Error al guardar Farmacia: ' . $e->getMessage())->withInput();
        }
    }
}
