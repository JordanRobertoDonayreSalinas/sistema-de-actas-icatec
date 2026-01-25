<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CabeceraMonitoreo;
use App\Models\MonitoreoModulos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TriajeESPController extends Controller
{
    private function getFotoPath($contenidoDB)
    {
        // Prioridad 1: Estructura Nueva
        if (!empty($contenidoDB['comentarios_y_evidencias']['foto_evidencia'])) {
            $f = $contenidoDB['comentarios_y_evidencias']['foto_evidencia'];
            return is_array($f) ? ($f[0] ?? null) : $f;
        }
        // Prioridad 2: Estructura Antigua
        if (!empty($contenidoDB['foto_evidencia'])) {
            $f = $contenidoDB['foto_evidencia'];
            return is_array($f) ? ($f[0] ?? null) : $f;
        }
        return null;
    }

    public function index($id)
    {
        $acta = CabeceraMonitoreo::with('establecimiento')->findOrFail($id);

        if ($acta->tipo_origen !== 'ESPECIALIZADA') {
            return redirect()->route('usuario.monitoreo.modulos', $id)->with('error', 'Módulo incorrecto.');
        }

        $registro = MonitoreoModulos::where('cabecera_monitoreo_id', $id)
                                   ->where('modulo_nombre', 'triaje_esp')
                                   ->first();

        $detalle = $registro ?? new MonitoreoModulos();
        
        $contenidoDB = [];
        if ($registro) {
            $contenidoDB = is_string($registro->contenido) ? json_decode($registro->contenido, true) : $registro->contenido;
            $contenidoDB = $contenidoDB ?? [];
        }

        $datosParaVista = [];

        // 1. Consultorio
        $grupoDetalle = $contenidoDB['detalle_del_consultorio'] ?? [];
        $datosParaVista['fecha'] = $grupoDetalle['fecha_monitoreo'] ?? ($contenidoDB['fecha'] ?? date('Y-m-d'));
        $datosParaVista['turno'] = $grupoDetalle['turno'] ?? '';
        $datosParaVista['num_ambientes'] = $grupoDetalle['num_consultorios'] ?? '';
        $datosParaVista['denominacion_ambiente'] = $grupoDetalle['denominacion'] ?? '';
        
        // 2. Profesional y Doc
        $grupoRRHH = $contenidoDB['datos_del_profesional'] ?? ($contenidoDB['rrhh'] ?? []);
        $datosParaVista['rrhh'] = $grupoRRHH;
        $grupoDoc = $contenidoDB['documentacion_administrativa'] ?? [];
        $datosParaVista['rrhh']['cuenta_sihce'] = $grupoDoc['utiliza_sihce'] ?? ($grupoRRHH['cuenta_sihce'] ?? '');
        $datosParaVista['rrhh']['firmo_dj'] = $grupoDoc['firmo_dj'] ?? ($grupoRRHH['firmo_dj'] ?? '');
        $datosParaVista['rrhh']['firmo_confidencialidad'] = $grupoDoc['firmo_confidencialidad'] ?? ($grupoRRHH['firmo_confidencialidad'] ?? '');

        // 3. DNI
        $grupoDni = $contenidoDB['detalle_de_dni_y_firma_digital'] ?? ($contenidoDB['uso_del_dnie'] ?? []);
        $datosParaVista['tipo_dni_fisico'] = $grupoDni['tipo_dni'] ?? ($grupoDni['tipo_fisico'] ?? ($contenidoDB['tipo_dni_fisico'] ?? ''));
        $datosParaVista['dnie_version'] = $grupoDni['version_dnie'] ?? ($grupoDni['version'] ?? ($contenidoDB['dnie_version'] ?? ''));
        $datosParaVista['dnie_firma_sihce'] = $grupoDni['firma_digital_sihce'] ?? ($grupoDni['firma_sihce'] ?? ($contenidoDB['dnie_firma_sihce'] ?? ''));
        $datosParaVista['dni_observacion'] = $grupoDni['observaciones_dni'] ?? ($grupoDni['observacion'] ?? ($contenidoDB['dni_observacion'] ?? ''));

        // 4. Capacitación
        $grupoCap = $contenidoDB['detalles_de_capacitacion'] ?? [];
        $datosParaVista['capacitacion'] = [
            'recibieron_cap' => $grupoCap['recibio_capacitacion'] ?? ($grupoCap['recibieron_cap'] ?? ''),
            'institucion_cap' => $grupoCap['inst_que_lo_capacito'] ?? ($grupoCap['institucion_cap'] ?? '')
        ];

        // 6. Soporte
        $grupoSoporte = $contenidoDB['soporte'] ?? ($contenidoDB['dificultades'] ?? []);
        $datosParaVista['dificultades'] = [
            'comunica' => $grupoSoporte['inst_a_quien_comunica'] ?? ($grupoSoporte['comunica'] ?? ''),
            'medio' => $grupoSoporte['medio_que_utiliza'] ?? ($grupoSoporte['medio'] ?? '')
        ];

        // 7. Equipos
        $datosParaVista['equipos'] = $contenidoDB['equipos_de_computo'] ?? ($contenidoDB['equipamiento_biomedico_y_mobiliario'] ?? []);

        // 8. Comentarios y Foto
        $grupoEvidencia = $contenidoDB['comentarios_y_evidencias'] ?? [];
        $comentarioTexto = $grupoEvidencia['comentarios'] ?? ($contenidoDB['comentario_esp'] ?? '');
        $fotoPath = $this->getFotoPath($contenidoDB);

        // ASIGNACIONES
        $detalle->contenido = $datosParaVista;
        $detalle->comentario_esp = $comentarioTexto;
        $detalle->foto_url_esp = $fotoPath;

        $equiposFormateados = collect($datosParaVista['equipos'])->map(fn($item) => (object)$item);

        return view('usuario.monitoreo.modulos_especializados.triaje', [
            'acta' => $acta,
            'detalle' => $detalle,
            'equipos' => $equiposFormateados
        ]);
    }

    public function store(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $raw = $request->input('contenido', []);
            $rawRRHH = $request->input('rrhh', []) ?: ($raw['rrhh'] ?? []);
            $rawEquipos = $request->input('equipos', []);
            $rawComentario = $request->input('comentario_esp') ?: ($raw['comentario_esp'] ?? '');
            $inputComunica = $request->input('comunica_a') ?? ($raw['dificultades']['comunica'] ?? '');
            $inputMedio = $request->input('medio_soporte') ?? ($raw['dificultades']['medio'] ?? '');

            $jsonToSave = [];

            // Secciones 1-7 (Simplificadas para brevedad, son las mismas)
            $jsonToSave['detalle_del_consultorio'] = [
                'fecha_monitoreo' => $raw['fecha'] ?? date('Y-m-d'),
                'turno' => $raw['turno'] ?? '',
                'num_consultorios'=> $raw['num_ambientes'] ?? '',
                'denominacion' => mb_strtoupper($raw['denominacion_ambiente'] ?? '', 'UTF-8'),
            ];
            $jsonToSave['datos_del_profesional'] = [
                'doc' => $rawRRHH['doc'] ?? '',
                'tipo_doc' => $rawRRHH['tipo_doc'] ?? 'DNI',
                'nombres' => mb_strtoupper($rawRRHH['nombres'] ?? '', 'UTF-8'),
                'apellido_paterno' => mb_strtoupper($rawRRHH['apellido_paterno'] ?? '', 'UTF-8'),
                'apellido_materno' => mb_strtoupper($rawRRHH['apellido_materno'] ?? '', 'UTF-8'),
                'email' => $rawRRHH['email'] ?? '',
                'telefono' => $rawRRHH['telefono'] ?? '',
                'cargo' => mb_strtoupper($rawRRHH['cargo'] ?? '', 'UTF-8'),
            ];
            $jsonToSave['documentacion_administrativa'] = [
                'utiliza_sihce' => $rawRRHH['cuenta_sihce'] ?? 'NO',
                'firmo_dj' => $rawRRHH['firmo_dj'] ?? 'NO',
                'firmo_confidencialidad'=> $rawRRHH['firmo_confidencialidad'] ?? 'NO',
            ];
            $jsonToSave['detalle_de_dni_y_firma_digital'] = [
                'tipo_dni' => $raw['tipo_dni_fisico'] ?? ($rawRRHH['tipo_dni_fisico'] ?? ''),
                'version_dnie' => $raw['dnie_version'] ?? ($rawRRHH['dnie_version'] ?? ''),
                'firma_digital_sihce' => $raw['dnie_firma_sihce'] ?? ($rawRRHH['dnie_firma_sihce'] ?? ''),
                'observaciones_dni' => mb_strtoupper($raw['dni_observacion'] ?? ($rawRRHH['dni_observacion'] ?? ''), 'UTF-8'),
            ];
            $cap = $raw['capacitacion'] ?? [];
            $jsonToSave['detalles_de_capacitacion'] = [
                'recibio_capacitacion' => $cap['recibieron_cap'] ?? '',
                'inst_que_lo_capacito' => mb_strtoupper($cap['institucion_cap'] ?? '', 'UTF-8'),
            ];
            $jsonToSave['soporte'] = [
                'inst_a_quien_comunica' => mb_strtoupper($inputComunica, 'UTF-8'),
                'medio_que_utiliza' => mb_strtoupper($inputMedio, 'UTF-8'),
            ];
            $equiposLimpios = array_values(array_filter($rawEquipos, fn($e) => !empty($e['descripcion'])));
            $jsonToSave['equipos_de_computo'] = array_map(function($e) {
                return array_map(fn($v) => is_string($v) ? mb_strtoupper($v, 'UTF-8') : $v, $e);
            }, $equiposLimpios);

            // 8. FOTO (Lógica crítica)
            $registroPrevio = MonitoreoModulos::where('cabecera_monitoreo_id', $id)
                                              ->where('modulo_nombre', 'triaje_esp')
                                              ->first();
            
            $rutaFoto = null;
            if ($registroPrevio) {
                $prevJson = is_string($registroPrevio->contenido) ? json_decode($registroPrevio->contenido, true) : $registroPrevio->contenido;
                $rutaFoto = $this->getFotoPath($prevJson ?? []);
            }

            if ($request->hasFile('foto_esp_file')) {
                if ($rutaFoto && Storage::disk('public')->exists($rutaFoto)) {
                    Storage::disk('public')->delete($rutaFoto);
                }
                $rutaFoto = $request->file('foto_esp_file')->store('evidencias_monitoreo', 'public');
            }

            $jsonToSave['comentarios_y_evidencias'] = [
                'comentarios' => mb_strtoupper($rawComentario, 'UTF-8'),
                'foto_evidencia' => $rutaFoto ? [$rutaFoto] : []
            ];

            // Update Profesional
            if (!empty($jsonToSave['datos_del_profesional']['doc'])) {
                $p = $jsonToSave['datos_del_profesional'];
                DB::table('mon_profesionales')->updateOrInsert(
                    ['doc' => $p['doc']],
                    [
                        'tipo_doc' => $p['tipo_doc'],
                        'nombres' => $p['nombres'],
                        'apellido_paterno' => $p['apellido_paterno'],
                        'apellido_materno' => $p['apellido_materno'],
                        'cargo' => $p['cargo'],
                        'updated_at' => now(),
                    ]
                );
            }

            $registro = MonitoreoModulos::firstOrNew([
                'cabecera_monitoreo_id' => $id,
                'modulo_nombre' => 'triaje_esp'
            ]);

            $registro->contenido = $jsonToSave;
            $registro->save();

            DB::commit();

            return redirect()
                ->route('usuario.monitoreo.modulos', $id)
                ->with('success', 'Módulo Triaje guardado correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error TriajeESP Store: " . $e->getMessage());
            return back()->with('error', 'Error: ' . $e->getMessage())->withInput();
        }
    }
}