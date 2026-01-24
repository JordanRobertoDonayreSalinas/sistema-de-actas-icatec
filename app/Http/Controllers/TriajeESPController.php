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
     * Muestra el formulario de "Triaje" específico para CSMC.
     */
    public function index($id)
    {
        $acta = CabeceraMonitoreo::with('establecimiento')->findOrFail($id);

        if ($acta->tipo_origen !== 'ESPECIALIZADA') {
            return redirect()->route('usuario.monitoreo.modulos', $id)
                ->with('error', 'Este módulo no corresponde al tipo de establecimiento.');
        }

        // 1. Obtener el registro de la BD
        $registro = MonitoreoModulos::where('cabecera_monitoreo_id', $id)
                                   ->where('modulo_nombre', 'triaje_esp')
                                   ->first();

        // 2. Inicializar objeto detalle
        $detalle = $registro ?? new MonitoreoModulos();
        
        // Decodificar el JSON guardado
        // Ahora soportamos si Laravel ya lo entrega como Array (por el Cast) o como String
        $contenidoDB = [];
        
        if ($registro) {
            if (is_array($registro->contenido)) {
                $contenidoDB = $registro->contenido;
            } elseif (is_string($registro->contenido)) {
                $contenidoDB = json_decode($registro->contenido, true) ?? [];
            }
        }

        // 3. ADAPTADOR (DB -> VISTA)
        $datosParaVista = [];

        // Mapeo Directo
        $datosParaVista['fecha'] = $contenidoDB['fecha_monitoreo_triaje'] ?? date('Y-m-d');
        $datosParaVista['turno'] = $contenidoDB['turno'] ?? '';
        $datosParaVista['num_ambientes'] = $contenidoDB['num_consultorios'] ?? '';
        $datosParaVista['denominacion_ambiente'] = $contenidoDB['denominacion_consultorio'] ?? '';
        
        // Mapeo RRHH
        $datosParaVista['rrhh'] = $contenidoDB['profesional'] ?? [];
        // Inyectamos administrativos dentro de rrhh para la vista
        $datosParaVista['rrhh']['cuenta_sihce'] = $contenidoDB['utiliza_sihce'] ?? '';
        $datosParaVista['rrhh']['firmo_dj'] = $contenidoDB['firmo_dj'] ?? '';
        $datosParaVista['rrhh']['firmo_confidencialidad'] = $contenidoDB['firmo_confidencialidad'] ?? '';

        // Mapeo DNI
        $datosParaVista['tipo_dni_fisico'] = $contenidoDB['tipo_dni_fisico'] ?? '';
        $datosParaVista['dnie_version'] = $contenidoDB['dnie_version'] ?? '';
        $datosParaVista['dni_observacion'] = $contenidoDB['dni_observacion'] ?? '';

        // Mapeo Capacitación
        $datosParaVista['capacitacion'] = [
            'recibieron_cap' => $contenidoDB['recibio_capacitacion'] ?? '',
            'institucion_cap' => $contenidoDB['inst_capacitacion'] ?? ''
        ];

        // Mapeo Dificultades / Soporte
        $datosParaVista['dificultades'] = [
            'comunica' => $contenidoDB['comunica_a'] ?? '',
            'medio' => $contenidoDB['medio_soporte'] ?? ''
        ];

        // Mapeo Comentarios
        $datosParaVista['comentario_esp'] = $contenidoDB['comentarios'] ?? '';

        // Mapeo Foto (Extraer string del array)
        $datosParaVista['foto_evidencia'] = null;
        if (!empty($contenidoDB['foto_evidencia'])) {
            if (is_array($contenidoDB['foto_evidencia'])) {
                $datosParaVista['foto_evidencia'] = $contenidoDB['foto_evidencia'][0] ?? null;
            } else {
                $datosParaVista['foto_evidencia'] = $contenidoDB['foto_evidencia'];
            }
        }

        // Mapeo Equipos
        $datosParaVista['equipos'] = $contenidoDB['equipos'] ?? [];

        // Asignamos la estructura adaptada al objeto para la vista
        $detalle->contenido = $datosParaVista;

        // Formatear equipos como objetos para la vista
        $equiposFormateados = collect($datosParaVista['equipos'])->map(function($item) {
            return (object) $item;
        });

        return view('usuario.monitoreo.modulos_especializados.triaje', [
            'acta' => $acta,
            'detalle' => $detalle,
            'equipos' => $equiposFormateados
        ]);
    }

    /**
     * Guarda la información.
     */
    public function store(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            // 1. Obtener datos crudos
            $rawContenido = $request->input('contenido', []);
            $rawRRHH = $request->input('rrhh', []);
            $rawComentario = $request->input('comentario_esp');
            $rawEquipos = $request->input('equipos', []);

            // 2. Construir la estructura JSON FINAL
            $jsonToSave = [];

            // A. Datos Generales
            $jsonToSave['fecha_monitoreo_triaje] = $rawContenido['fecha'] ?? date('Y-m-d');
            $jsonToSave['turno'] = $rawContenido['turno'] ?? '';
            $jsonToSave['num_consultorios'] = $rawContenido['num_ambientes'] ?? '';
            $jsonToSave['denominacion_consultorio'] = $rawContenido['denominacion_ambiente'] ?? mb_strtoupper($rawContenido['denominacion_ambiente'] ?? '', 'UTF-8');

            // B. Profesional
            if (empty($rawRRHH) && isset($rawContenido['rrhh'])) {
                $rawRRHH = $rawContenido['rrhh'];
            }

            $jsonToSave['profesional'] = [
                'doc' => $rawRRHH['doc'] ?? '',
                'tipo_doc' => $rawRRHH['tipo_doc'] ?? '',
                'nombres' => mb_strtoupper($rawRRHH['nombres'] ?? '', 'UTF-8'),
                'apellido_paterno' => mb_strtoupper($rawRRHH['apellido_paterno'] ?? '', 'UTF-8'),
                'apellido_materno' => mb_strtoupper($rawRRHH['apellido_materno'] ?? '', 'UTF-8'),
                'email' => $rawRRHH['email'] ?? '',
                'telefono' => $rawRRHH['telefono'] ?? '',
                'cargo' => mb_strtoupper($rawRRHH['cargo'] ?? '', 'UTF-8'),
            ];

            // C. Administrativos
            $jsonToSave['utiliza_sihce'] = $rawRRHH['cuenta_sihce'] ?? 'NO';
            $jsonToSave['firmo_dj'] = $rawRRHH['firmo_dj'] ?? 'NO';
            $jsonToSave['firmo_confidencialidad'] = $rawRRHH['firmo_confidencialidad'] ?? 'NO';

            // D. DNI
            $jsonToSave['tipo_dni_fisico'] = $rawContenido['tipo_dni_fisico'] ?? '';
            $jsonToSave['dnie_version'] = $rawContenido['dnie_version'] ?? '';
            $jsonToSave['dni_observacion'] = mb_strtoupper($rawContenido['dni_observacion'] ?? '', 'UTF-8');

            // E. Capacitación
            $cap = $rawContenido['capacitacion'] ?? [];
            $jsonToSave['recibio_capacitacion'] = $cap['recibieron_cap'] ?? '';
            $jsonToSave['inst_capacitacion'] = mb_strtoupper($cap['institucion_cap'] ?? '', 'UTF-8');

            // F. Soporte / Dificultades
            $dif = $rawContenido['dificultades'] ?? [];
            $jsonToSave['comunica_a'] = mb_strtoupper($dif['comunica'] ?? '', 'UTF-8');
            $jsonToSave['medio_soporte'] = mb_strtoupper($dif['medio'] ?? '', 'UTF-8');

            // G. Comentarios
            $jsonToSave['comentarios'] = mb_strtoupper($rawComentario ?? ($rawContenido['comentario_esp'] ?? ''), 'UTF-8');

            // H. Equipos
            $equiposLimpios = array_values(array_filter($rawEquipos, function($e) {
                return !empty($e['descripcion']);
            }));
            
            $equiposFinales = array_map(function($e) {
                return array_map(function($val) {
                    return is_string($val) ? mb_strtoupper($val, 'UTF-8') : $val;
                }, $e);
            }, $equiposLimpios);
            
            $jsonToSave['equipos'] = $equiposFinales;

            // I. Foto
            $registroPrevio = MonitoreoModulos::where('cabecera_monitoreo_id', $id)
                                              ->where('modulo_nombre', 'triaje_esp')
                                              ->first();
            
            // Intentar recuperar foto previa limpiamente
            $rutaFoto = null;
            if ($registroPrevio) {
                $prevContent = $registroPrevio->contenido;
                // Si es string lo decodificamos, si es array lo usamos directo
                $prevJson = is_string($prevContent) ? json_decode($prevContent, true) : $prevContent;
                
                if (isset($prevJson['foto_evidencia']) && is_array($prevJson['foto_evidencia'])) {
                    $rutaFoto = $prevJson['foto_evidencia'][0] ?? null;
                }
            }

            $fileInput = null;
            if ($request->hasFile('foto_evidencia')) $fileInput = 'foto_evidencia';
            elseif ($request->hasFile('foto_esp_file')) $fileInput = 'foto_esp_file';

            if ($fileInput) {
                $request->validate([$fileInput => 'image|mimes:jpeg,png,jpg|max:10240']);
                if ($rutaFoto && Storage::disk('public')->exists($rutaFoto)) {
                    Storage::disk('public')->delete($rutaFoto);
                }
                $rutaFoto = $request->file($fileInput)->store('evidencias_monitoreo', 'public');
            }

            $jsonToSave['foto_evidencia'] = $rutaFoto ? [$rutaFoto] : [];

            // ---------------------------------------------------------
            // 3. GUARDADO EN BD
            // ---------------------------------------------------------
            
            // Actualizar Maestro Profesionales
            if (!empty($jsonToSave['profesional']['doc'])) {
                $p = $jsonToSave['profesional'];
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

            // Guardar Registro
            $registro = MonitoreoModulos::firstOrNew([
                'cabecera_monitoreo_id' => $id,
                'modulo_nombre' => 'triaje_esp'
            ]);

            // [CORRECCIÓN CRÍTICA]
            // Asignamos el ARRAY directamente. NO usamos json_encode().
            // Esto asume que tu modelo tiene 'protected $casts = [ 'contenido' => 'array' ];'
            // Si no lo tiene, Laravel intentará convertir array a string y fallará.
            // Si eso pasa, cambia esta línea por: $registro->contenido = json_encode($jsonToSave, JSON_UNESCAPED_UNICODE);
            
            // Opción Segura (Detecta si necesita encoding):
            // Si asignamos el array y el modelo NO tiene cast, fallará.
            // Para asegurar JSON limpio SIN doble encoding:
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