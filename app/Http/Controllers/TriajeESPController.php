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
    /**
     * Muestra el formulario.
     * Mapea los datos de la BD a la estructura EXACTA que exigen los componentes 'esp_'.
     */
    public function index($id)
    {
        $acta = CabeceraMonitoreo::with('establecimiento')->findOrFail($id);

        if ($acta->tipo_origen !== 'ESPECIALIZADA') {
            return redirect()->route('usuario.monitoreo.modulos', $id)
                ->with('error', 'Este módulo no corresponde al tipo de establecimiento.');
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

        // =================================================================================
        // ADAPTADOR (DB -> VISTA)
        // Objetivo: Servir los datos donde los componentes esp_* los buscan.
        // =================================================================================
        $datosParaVista = [];

        // 1. DETALLE DEL CONSULTORIO (Componente: esp_1)
        $grupoDetalle = $contenidoDB['detalle_del_consultorio'] ?? [];
        $datosParaVista['fecha']                 = $grupoDetalle['fecha_monitoreo'] ?? date('Y-m-d');
        $datosParaVista['turno']                 = $grupoDetalle['turno'] ?? '';
        $datosParaVista['num_ambientes']         = $grupoDetalle['num_consultorios'] ?? '';
        $datosParaVista['denominacion_ambiente'] = $grupoDetalle['denominacion'] ?? '';
        
        // 2. DATOS DEL PROFESIONAL (Componente: esp_2 con prefix="rrhh")
        $grupoRRHH = $contenidoDB['datos_del_profesional'] ?? [];
        $datosParaVista['rrhh'] = $grupoRRHH;

        // 2.1 COMPATIBILIDAD VISIBILIDAD (Componente: esp_3 usa data_get(..., 'profesional.tipo_doc'))
        // Aunque esp_2 usa 'rrhh', esp_3 revisa 'profesional' para ocultarse/mostrarse. Duplicamos la data aquí.
        $datosParaVista['profesional'] = $grupoRRHH;

        // 3. DOCUMENTACIÓN ADMINISTRATIVA (Componente: esp_2_1 con prefix="rrhh")
        $grupoDoc = $contenidoDB['documentacion_administrativa'] ?? [];
        $datosParaVista['rrhh']['cuenta_sihce']           = $grupoDoc['utiliza_sihce'] ?? '';
        $datosParaVista['rrhh']['firmo_dj']               = $grupoDoc['firmo_dj'] ?? '';
        $datosParaVista['rrhh']['firmo_confidencialidad'] = $grupoDoc['firmo_confidencialidad'] ?? '';

        // 4. DETALLE DE DNI Y FIRMA (Componente: esp_3)
        // EL PROBLEMA ERA AQUÍ: El componente busca en la RAÍZ (ej: contenido['tipo_dni_fisico']).
        // Extraemos los datos de su grupo en BD y los ponemos en la raíz de $datosParaVista.
        $grupoDni = $contenidoDB['detalle_de_dni_y_firma_digital'] ?? [];
        
        $datosParaVista['tipo_dni_fisico']  = $grupoDni['tipo_dni'] ?? '';
        $datosParaVista['dnie_version']     = $grupoDni['version_dnie'] ?? '';
        $datosParaVista['dnie_firma_sihce'] = $grupoDni['firma_digital_sihce'] ?? '';
        $datosParaVista['dni_observacion']  = $grupoDni['observaciones_dni'] ?? '';

        // 5. CAPACITACIÓN (Componente: esp_4)
        $grupoCap = $contenidoDB['detalles_de_capacitacion'] ?? [];
        $datosParaVista['capacitacion'] = [
            'recibieron_cap'  => $grupoCap['recibio_capacitacion'] ?? '',
            'institucion_cap' => $grupoCap['inst_que_lo_capacito'] ?? ''
        ];

        // 6. SOPORTE TÉCNICO (Componente: esp_6)
        $grupoSoporte = $contenidoDB['soporte'] ?? [];
        $datosParaVista['dificultades'] = [
            'comunica' => $grupoSoporte['inst_a_quien_comunica'] ?? '',
            'medio'    => $grupoSoporte['medio_que_utiliza'] ?? ''
        ];
        // Alias para componentes antiguos que busquen 'soporte'
        $datosParaVista['soporte'] = $datosParaVista['dificultades'];

        // 7. EQUIPOS (Componente: esp_5)
        $datosParaVista['equipos'] = $contenidoDB['equipos_de_computo'] ?? [];

        // 8. COMENTARIOS Y FOTOS (Componente: esp_7)
        // Este componente usa propiedades directas del objeto $detalle (no solo dentro de contenido)
        $grupoEvidencia = $contenidoDB['comentarios_y_evidencias'] ?? [];
        $comentarioTexto = $grupoEvidencia['comentarios'] ?? '';
        $fotoPath = isset($grupoEvidencia['foto_evidencia'][0]) ? $grupoEvidencia['foto_evidencia'][0] : null;

        // Inyectamos al array para consistencia
        $datosParaVista['comentario_esp'] = $comentarioTexto;
        $datosParaVista['foto_evidencia'] = $fotoPath;

        // =================================================================================
        // ASIGNACIÓN AL MODELO (Esto es lo que reciben los @props(['detalle']))
        // =================================================================================
        $detalle->contenido = $datosParaVista;
        
        // Propiedades directas requeridas por esp_7_comentariosEvid:
        // {{ old('comentario_esp', $comentario->comentario_esp ?? '') }}
        $detalle->comentario_esp = $comentarioTexto;
        $detalle->foto_url_esp   = $fotoPath;

        // Convertir equipos a objetos para x-esp_5_equipos
        $equiposFormateados = collect($datosParaVista['equipos'])->map(fn($item) => (object)$item);

        return view('usuario.monitoreo.modulos_especializados.triaje', [
            'acta' => $acta,
            'detalle' => $detalle,
            'equipos' => $equiposFormateados
        ]);
    }

    /**
     * Guarda la información.
     * Lee los inputs planos (como los envía esp_3) y los estructura para la BD.
     */
    public function store(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $raw = $request->input('contenido', []);
            
            // Componente esp_2 envía datos en 'rrhh'
            $rawRRHH = $request->input('rrhh', []) ?: ($raw['rrhh'] ?? []);
            
            $rawEquipos = $request->input('equipos', []);
            $rawComentario = $request->input('comentario_esp') ?: ($raw['comentario_esp'] ?? '');
            
            // Inputs ocultos manejados por JS
            $inputComunica = $request->input('comunica_a') ?? ($raw['dificultades']['comunica'] ?? '');
            $inputMedio    = $request->input('medio_soporte') ?? ($raw['dificultades']['medio'] ?? '');

            $jsonToSave = [];

            // 1. CONSULTORIO
            $jsonToSave['detalle_del_consultorio'] = [
                'fecha_monitoreo' => $raw['fecha'] ?? date('Y-m-d'),
                'turno'           => $raw['turno'] ?? '',
                'num_consultorios'=> $raw['num_ambientes'] ?? '',
                'denominacion'    => mb_strtoupper($raw['denominacion_ambiente'] ?? '', 'UTF-8'),
            ];

            // 2. PROFESIONAL
            $jsonToSave['datos_del_profesional'] = [
                'doc'              => $rawRRHH['doc'] ?? '',
                'tipo_doc'         => $rawRRHH['tipo_doc'] ?? 'DNI',
                'nombres'          => mb_strtoupper($rawRRHH['nombres'] ?? '', 'UTF-8'),
                'apellido_paterno' => mb_strtoupper($rawRRHH['apellido_paterno'] ?? '', 'UTF-8'),
                'apellido_materno' => mb_strtoupper($rawRRHH['apellido_materno'] ?? '', 'UTF-8'),
                'email'            => $rawRRHH['email'] ?? '',
                'telefono'         => $rawRRHH['telefono'] ?? '',
                'cargo'            => mb_strtoupper($rawRRHH['cargo'] ?? '', 'UTF-8'),
            ];

            // 3. DOCUMENTACIÓN
            $jsonToSave['documentacion_administrativa'] = [
                'utiliza_sihce'         => $rawRRHH['cuenta_sihce'] ?? 'NO',
                'firmo_dj'              => $rawRRHH['firmo_dj'] ?? 'NO',
                'firmo_confidencialidad'=> $rawRRHH['firmo_confidencialidad'] ?? 'NO',
            ];

            // 4. DNI Y FIRMA (Componente esp_3 envía estos datos en la RAÍZ de 'contenido')
            // Por eso leemos $raw['tipo_dni_fisico'] directamente.
            $jsonToSave['detalle_de_dni_y_firma_digital'] = [
                'tipo_dni'            => $raw['tipo_dni_fisico'] ?? '',
                'version_dnie'        => $raw['dnie_version'] ?? '',
                'firma_digital_sihce' => $raw['dnie_firma_sihce'] ?? '',
                'observaciones_dni'   => mb_strtoupper($raw['dni_observacion'] ?? '', 'UTF-8'),
            ];

            // 5. CAPACITACIÓN
            $cap = $raw['capacitacion'] ?? [];
            $jsonToSave['detalles_de_capacitacion'] = [
                'recibio_capacitacion' => $cap['recibieron_cap'] ?? '',
                'inst_que_lo_capacito' => mb_strtoupper($cap['institucion_cap'] ?? '', 'UTF-8'),
            ];

            // 6. SOPORTE
            $jsonToSave['soporte'] = [
                'inst_a_quien_comunica' => mb_strtoupper($inputComunica, 'UTF-8'),
                'medio_que_utiliza'     => mb_strtoupper($inputMedio, 'UTF-8'),
            ];

            // 7. EQUIPOS
            $equiposLimpios = array_values(array_filter($rawEquipos, fn($e) => !empty($e['descripcion'])));
            $jsonToSave['equipos_de_computo'] = array_map(function($e) {
                return array_map(fn($v) => is_string($v) ? mb_strtoupper($v, 'UTF-8') : $v, $e);
            }, $equiposLimpios);

            // 8. COMENTARIOS Y EVIDENCIAS
            $registroPrevio = MonitoreoModulos::where('cabecera_monitoreo_id', $id)
                                              ->where('modulo_nombre', 'triaje_esp')
                                              ->first();
            
            $rutaFoto = null;
            if ($registroPrevio) {
                $prevJson = is_string($registroPrevio->contenido) ? json_decode($registroPrevio->contenido, true) : $registroPrevio->contenido;
                $rutaFoto = $prevJson['comentarios_y_evidencias']['foto_evidencia'][0] ?? null;
            }

            if ($request->hasFile('foto_esp_file')) {
                if ($rutaFoto && Storage::disk('public')->exists($rutaFoto)) {
                    Storage::disk('public')->delete($rutaFoto);
                }
                $rutaFoto = $request->file('foto_esp_file')->store('evidencias_monitoreo', 'public');
            }

            $jsonToSave['comentarios_y_evidencias'] = [
                'comentarios'    => mb_strtoupper($rawComentario, 'UTF-8'),
                'foto_evidencia' => $rutaFoto ? [$rutaFoto] : []
            ];

            // Actualizar Maestro Profesionales
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