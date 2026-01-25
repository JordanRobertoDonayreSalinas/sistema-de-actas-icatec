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
     * Lee la estructura AGRUPADA de la BD y la aplana para que la Vista la entienda.
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
        
        // Decodificar JSON (Soporta si viene como string o array)
        $contenidoDB = [];
        if ($registro) {
            $contenidoDB = is_string($registro->contenido) ? json_decode($registro->contenido, true) : $registro->contenido;
            $contenidoDB = $contenidoDB ?? [];
        }

        // ----------------------------------------------------------------------
        // ADAPTADOR: (BD AGRUPADA -> VISTA PLANA)
        // ----------------------------------------------------------------------
        $datosParaVista = [];

        // 1. Detalle del Consultorio
        $grupoDetalle = $contenidoDB['detalle_del_consultorio'] ?? [];
        $datosParaVista['fecha']                 = $grupoDetalle['fecha_monitoreo'] ?? date('Y-m-d');
        $datosParaVista['turno']                 = $grupoDetalle['turno'] ?? '';
        $datosParaVista['num_ambientes']         = $grupoDetalle['num_consultorios'] ?? '';
        $datosParaVista['denominacion_ambiente'] = $grupoDetalle['denominacion'] ?? '';
        
        // 2. Datos del Profesional
        $grupoProf = $contenidoDB['datos_del_profesional'] ?? [];
        $datosParaVista['rrhh'] = $grupoProf; 

        // 3. Documentación Administrativa (Se inyecta en 'rrhh' para el componente 2.1)
        $grupoDoc = $contenidoDB['documentacion_administrativa'] ?? [];
        $datosParaVista['rrhh']['cuenta_sihce']       = $grupoDoc['utiliza_sihce'] ?? '';
        $datosParaVista['rrhh']['firmo_dj']           = $grupoDoc['firmo_dj'] ?? '';
        $datosParaVista['rrhh']['firmo_confidencialidad'] = $grupoDoc['firmo_confidencialidad'] ?? '';

        // 4. Uso del DNIe
        $grupoDNI = $contenidoDB['uso_del_dnie'] ?? [];
        $datosParaVista['tipo_dni_fisico'] = $grupoDNI['tipo_fisico'] ?? '';
        $datosParaVista['dnie_version']    = $grupoDNI['version'] ?? '';
        $datosParaVista['dnie_firma_sihce'] = $grupoDNI['firma_sihce'] ?? ''; // <--- RECUPERADO CORRECTAMENTE
        $datosParaVista['dni_observacion'] = $grupoDNI['observacion'] ?? '';

        // 5. Capacitación
        $grupoCap = $contenidoDB['detalles_de_capacitacion'] ?? [];
        $datosParaVista['detalles_de_capacitacion'] = [
            'recibieron_cap'  => $grupoCap['recibio'] ?? '',
            'institucion_cap' => $grupoCap['institucion'] ?? ''
        ];

        // 6. Soporte Técnico
        $grupoSoporte = $contenidoDB['soporte'] ?? [];
        $datosParaVista['dificultades'] = [
            'comunica' => $grupoSoporte['comunica_a'] ?? '',
            'medio'    => $grupoSoporte['medio_soporte'] ?? ''
        ];
        $datosParaVista['soporte'] = $datosParaVista['dificultades']; // Duplicado para compatibilidad

        // 7. Comentarios y Evidencias
        $grupoEvidencia = $contenidoDB['comentarios_y_evidencias'] ?? [];
        $datosParaVista['comentario_esp'] = $grupoEvidencia['comentarios'] ?? '';
        
        // Foto (La BD guarda array, vista espera string)
        $datosParaVista['foto_evidencia'] = null;
        if (!empty($grupoEvidencia['foto_evidencia'])) {
            $fotos = $grupoEvidencia['foto_evidencia'];
            $datosParaVista['foto_evidencia'] = is_array($fotos) ? ($fotos[0] ?? null) : $fotos;
        }

        // 8. Equipos
        $datosParaVista['equipos'] = $contenidoDB['equipos_de_computo'] ?? [];

        // Asignación final al objeto
        $detalle->contenido = $datosParaVista;

        // INYECCIONES DIRECTAS (Parches para componentes antiguos que leen directo del objeto)
        $detalle->dificultad_comunica_a = $datosParaVista['dificultades']['comunica'];
        $detalle->dificultad_medio_uso  = $datosParaVista['dificultades']['medio'];
        $detalle->comentario_esp        = $datosParaVista['comentario_esp'];
        $detalle->foto_url_esp          = $datosParaVista['foto_evidencia'];

        // Formateo equipos
        $equiposFormateados = collect($datosParaVista['equipos'])->map(fn($item) => (object)$item);

        return view('usuario.monitoreo.modulos_especializados.triaje', [
            'acta' => $acta,
            'detalle' => $detalle,
            'equipos' => $equiposFormateados
        ]);
    }

    /**
     * Guarda la información.
     * CONVIERTE Inputs Planos -> JSON AGRUPADO
     */
    public function store(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            // 1. Obtener inputs crudos de la vista
            $raw = $request->input('contenido', []);
            $rawRRHH = $request->input('rrhh', []) ?: ($raw['rrhh'] ?? []);
            $rawEquipos = $request->input('equipos', []);
            
            // Inputs sueltos de componentes legacy
            $rawComentario = $request->input('comentario_esp') ?: ($raw['comentario_esp'] ?? '');
            
            // Soporte (buscar en todas las variantes posibles)
            $rawSoporte = $raw['dificultades'] ?? ($raw['soporte'] ?? []);
            $inputComunica = $request->input('comunica_a') ?? ($rawSoporte['comunica'] ?? '');
            $inputMedio    = $request->input('medio_soporte') ?? ($rawSoporte['medio'] ?? '');

            // ----------------------------------------------------------
            // 2. CONSTRUIR JSON AGRUPADO (Tu estructura deseada)
            // ----------------------------------------------------------
            $jsonToSave = [];

            // SECCIÓN 1: DETALLE DEL CONSULTORIO
            $jsonToSave['detalle_del_consultorio'] = [
                'fecha_monitoreo' => $raw['fecha'] ?? date('Y-m-d'),
                'turno'           => $raw['turno'] ?? '',
                'num_consultorios'=> $raw['num_ambientes'] ?? '',
                'denominacion'    => mb_strtoupper($raw['denominacion_ambiente'] ?? '', 'UTF-8'),
            ];

            // SECCIÓN 2: DATOS DEL PROFESIONAL
            $jsonToSave['datos_del_profesional'] = [
                'doc'              => $rawRRHH['doc'] ?? '',
                'tipo_doc'         => $rawRRHH['tipo_doc'] ?? '',
                'nombres'          => mb_strtoupper($rawRRHH['nombres'] ?? '', 'UTF-8'),
                'apellido_paterno' => mb_strtoupper($rawRRHH['apellido_paterno'] ?? '', 'UTF-8'),
                'apellido_materno' => mb_strtoupper($rawRRHH['apellido_materno'] ?? '', 'UTF-8'),
                'email'            => $rawRRHH['email'] ?? '',
                'telefono'         => $rawRRHH['telefono'] ?? '',
                'cargo'            => mb_strtoupper($rawRRHH['cargo'] ?? '', 'UTF-8'),
            ];

            // SECCIÓN 3: DOCUMENTACIÓN ADMINISTRATIVA
            $jsonToSave['documentacion_administrativa'] = [
                'utiliza_sihce'         => $rawRRHH['cuenta_sihce'] ?? 'NO',
                'firmo_dj'              => $rawRRHH['firmo_dj'] ?? 'NO',
                'firmo_confidencialidad'=> $rawRRHH['firmo_confidencialidad'] ?? 'NO',
            ];

            // SECCIÓN 4: USO DEL DNIe (Aquí agregamos el campo que faltaba)
            $jsonToSave['uso_del_dnie'] = [
                'tipo_fisico' => $raw['tipo_dni_fisico'] ?? '',
                'version'     => $raw['dnie_version'] ?? '',
                'firma_sihce' => $raw['dnie_firma_sihce'] ?? '', // <--- ¡AQUÍ ESTÁ!
                'observacion' => mb_strtoupper($raw['dni_observacion'] ?? '', 'UTF-8'),
            ];

            // SECCIÓN 5: CAPACITACIÓN
            $cap = $raw['detalles_de_capacitacion'] ?? [];
            $jsonToSave['detalles_de_capacitacion'] = [
                'recibio'     => $cap['recibieron_cap'] ?? '',
                'institucion' => mb_strtoupper($cap['institucion_cap'] ?? '', 'UTF-8'),
            ];

            // SECCIÓN 6: SOPORTE TÉCNICO
            $jsonToSave['soporte'] = [
                'comunica_a'    => mb_strtoupper($inputComunica, 'UTF-8'),
                'medio_soporte' => mb_strtoupper($inputMedio, 'UTF-8'),
            ];

            // SECCIÓN 7: EQUIPOS DE CÓMPUTO
            $equiposLimpios = array_values(array_filter($rawEquipos, fn($e) => !empty($e['descripcion'])));
            $jsonToSave['equipos_de_computo'] = array_map(function($e) {
                return array_map(fn($v) => is_string($v) ? mb_strtoupper($v, 'UTF-8') : $v, $e);
            }, $equiposLimpios);

            // SECCIÓN 8: COMENTARIOS Y EVIDENCIAS
            // Procesar foto
            $registroPrevio = MonitoreoModulos::where('cabecera_monitoreo_id', $id)
                                              ->where('modulo_nombre', 'triaje_esp')
                                              ->first();
            
            $rutaFoto = null;
            if ($registroPrevio) {
                // Recuperar foto de estructura vieja O nueva
                $prevContent = $registroPrevio->contenido;
                $prevJson = is_string($prevContent) ? json_decode($prevContent, true) : $prevContent;
                
                // 1. Buscar en estructura NUEVA
                $rutaFoto = $prevJson['comentarios_y_evidencias']['foto_evidencia'][0] ?? null;
                // 2. Si no, buscar en estructura VIEJA (para migración suave)
                if (!$rutaFoto) $rutaFoto = $prevJson['foto_evidencia'][0] ?? ($prevJson['foto_evidencia'] ?? null);
            }

            // Detectar input file
            $fileInput = null;
            if ($request->hasFile('foto_esp_file')) $fileInput = 'foto_esp_file';
            elseif ($request->hasFile('foto_evidencia')) $fileInput = 'foto_evidencia';

            if ($fileInput) {
                $request->validate([$fileInput => 'image|mimes:jpeg,png,jpg|max:10240']);
                if ($rutaFoto && Storage::disk('public')->exists($rutaFoto)) {
                    Storage::disk('public')->delete($rutaFoto);
                }
                $rutaFoto = $request->file($fileInput)->store('evidencias_monitoreo', 'public');
            }

            $jsonToSave['comentarios_y_evidencias'] = [
                'comentarios'    => mb_strtoupper($rawComentario, 'UTF-8'),
                'foto_evidencia' => $rutaFoto ? [$rutaFoto] : []
            ];

            // ----------------------------------------------------------
            // 3. GUARDADO FINAL
            // ----------------------------------------------------------
            
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

            // Asignación directa del array agrupado (Laravel lo castea a JSON limpio)
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