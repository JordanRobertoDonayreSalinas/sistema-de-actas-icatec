<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CabeceraMonitoreo;
use App\Models\MonitoreoModulos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Profesional;
use stdClass;


class EnfermeriaESPController extends Controller
{
    public function index($id) 
    {
        // 1. Validar Cabecera
        $acta = CabeceraMonitoreo::with('establecimiento')->findOrFail($id);
        
        if ($acta->tipo_origen !== 'ESPECIALIZADA') {
            return redirect()->route('usuario.monitoreo.modulos', $id)
                ->with('error', 'Módulo incorrecto.');
        }

        // 2. Recuperar el JSON guardado
        $registro = MonitoreoModulos::where('cabecera_monitoreo_id', $id)
                                    ->where('modulo_nombre', 'enfermeria_esp')
                                    ->first();

        // --- CORRECCIÓN DEL ERROR DE JSON_DECODE ---
        // Si $registro->contenido ya es un array (por el cast del modelo), lo usamos directo.
        // Si es null, usamos array vacío.
        $data = $registro ? $registro->contenido : [];
        
        // Blindaje extra: Si por alguna razón sigue llegando como string (datos viejos), lo decodificamos.
        if (is_string($data)) {
            $data = json_decode($data, true);
        }

        // 3. MAPEO DE DATOS (JSON -> VISTA)
        $dataMap = new stdClass();
        
        // Mapeo del contenido general
        $dataMap->contenido = [
            'fecha'                 => $data['fecha_registro'] ?? null,
            'turno'                 => $data['turno'] ?? null,
            'num_ambientes'         => $data['num_consultorios'] ?? null,
            'denominacion_ambiente' => $data['denominacion_consultorio'] ?? null,
            'tipo_dni_fisico'       => $data['seccion_dni']['tipo_dni'] ?? null,
            'dnie_version'          => $data['seccion_dni']['version_dnie'] ?? null,
            'dnie_firma_sihce'      => $data['seccion_dni']['firma_sihce'] ?? null,
            'dni_observacion'       => $data['seccion_dni']['comentarios'] ?? null,
            'dificultades' => [
                'comunica' => $data['comunica_a'] ?? null,
                'medio'    => $data['medio_soporte'] ?? null
            ]
        ];

        // Mapeo del Profesional
        if (isset($data['profesional'])) {
            $profTemp = $data['profesional'];
            // Aseguramos que las claves coincidan con lo que espera la vista
            $profTemp['cuenta_sihce'] = $data['profesional']['utiliza_sihce'] ?? ($data['profesional']['cuenta_sihce'] ?? ''); 
            $profTemp['firmo_dj'] = $data['firmo_dj'] ?? ($data['profesional']['firmo_dj'] ?? '');
            $profTemp['firmo_confidencialidad'] = $data['firmo_confidencialidad'] ?? ($data['profesional']['firmo_confidencialidad'] ?? '');
            
            $dataMap->contenido['profesional'] = $profTemp;
        }

        // Variables sueltas para componentes específicos
        $dataMap->dificultad_comunica_a = $data['comunica_a'] ?? null;
        $dataMap->dificultad_medio_uso  = $data['medio_soporte'] ?? null;
        $dataMap->comentario_esp = $data['comentarios_generales'] ?? ($data['comentarios'] ?? null);
        $dataMap->foto_url_esp = isset($data['foto_evidencia'][0]) ? $data['foto_evidencia'][0] : null;

        // Alpine JS Data - Capacitación
        $valCapacitacion = [
            'recibieron_cap'  => $data['recibio_capacitacion'] ?? 'NO',
            'institucion_cap' => $data['inst_capacitacion'] ?? null
        ];
        
        // Mapeo de Inventario (Soluciona el error $propio vs propiedad)
        $rawInventario = $data['inventario'] ?? [];
        $valInventario = [];
        
        foreach($rawInventario as $item) {
            // A veces el cast devuelve objetos, a veces arrays. Forzamos array para lectura segura.
            $itemArray = (array)$item; 
            
            $obj = new stdClass();
            $obj->descripcion = $itemArray['descripcion'] ?? '';
            $obj->cantidad    = $itemArray['cantidad'] ?? 1;
            $obj->estado      = $itemArray['estado'] ?? 'OPERATIVO';
            // Le damos a la vista lo que pide ('propio') leyendo de la BD ('propiedad')
            $obj->propio      = $itemArray['propiedad'] ?? ($itemArray['propio'] ?? 'COMPARTIDO'); 
            $obj->nro_serie   = $itemArray['nro_serie'] ?? ($itemArray['codigo'] ?? ''); 
            $obj->observacion = $itemArray['observacion'] ?? '';
            $valInventario[] = $obj;
        }

        return view('usuario.monitoreo.modulos_especializados.enfermeria', compact(
            'acta', 'dataMap', 'valCapacitacion', 'valInventario'
        ));
    }

    public function store(Request $request, $id)
    {
        try {
            $registroPrevio = MonitoreoModulos::where('cabecera_monitoreo_id', $id)
                                              ->where('modulo_nombre', 'enfermeria_esp')
                                              ->first();
            
            // Aquí también quitamos el json_decode si ya viene como array, o usamos un helper seguro
            $contenidoPrevio = $registroPrevio ? $registroPrevio->contenido : [];
            if(is_string($contenidoPrevio)) $contenidoPrevio = json_decode($contenidoPrevio, true);

            // 1. GESTIÓN DE FOTO (Array)
            $rutaFotoAnterior = isset($contenidoPrevio['foto_evidencia'][0]) ? $contenidoPrevio['foto_evidencia'][0] : null;
            $rutaFotoFinal = $rutaFotoAnterior;

            if ($request->hasFile('foto_esp_file')) {
                if ($rutaFotoAnterior && Storage::disk('public')->exists($rutaFotoAnterior)) {
                    Storage::disk('public')->delete($rutaFotoAnterior);
                }
                $rutaFotoFinal = $request->file('foto_esp_file')->store('evidencias_esp', 'public');
            }
            $arrayFotos = $rutaFotoFinal ? [$rutaFotoFinal] : [];

            // 2. SINCRONIZACIÓN PROFESIONAL
            $rawProf = $request->input('contenido.profesional', []);
            $profesionalDB = null;

            if (!empty($rawProf['doc'])) {
                $profesionalDB = Profesional::updateOrCreate(
                    ['doc' => trim($rawProf['doc'])],
                    [
                        'tipo_doc'         => $rawProf['tipo_doc'] ?? 'DNI',
                        'apellido_paterno' => mb_strtoupper(trim($rawProf['apellido_paterno'] ?? ''), 'UTF-8'),
                        'apellido_materno' => mb_strtoupper(trim($rawProf['apellido_materno'] ?? ''), 'UTF-8'),
                        'nombres'          => mb_strtoupper(trim($rawProf['nombres'] ?? ''), 'UTF-8'),
                        'email'            => !empty($rawProf['email']) ? strtolower(trim($rawProf['email'])) : null,
                        'telefono'         => $rawProf['telefono'] ?? null,
                        'cargo'            => $rawProf['cargo'] ?? null,
                    ]
                );
            }

            // 3. TRANSFORMACIÓN DE INVENTARIO
            $rawEquipos = $request->input('equipos', []);
            $inventarioMapeado = [];

            foreach($rawEquipos as $keyId => $item) {
                // Separar serie
                $fullSerie = $item['nro_serie'] ?? '';
                $tipoCodigo = 'S';
                $codigo = $fullSerie;
                
                if(str_contains($fullSerie, ':')) {
                    $parts = explode(':', $fullSerie, 2);
                    $tipoCodigo = $parts[0];
                    $codigo = $tipoCodigo . ' ' . ($parts[1] ?? '');
                }

                $inventarioMapeado[] = [
                    'id'          => (float)$keyId,
                    'descripcion' => $item['descripcion'] ?? '',
                    'propiedad'   => $item['propio'] ?? 'COMPARTIDO', 
                    'estado'      => $item['estado'] ?? 'OPERATIVO',
                    'tipo_codigo' => $tipoCodigo,
                    'codigo'      => $codigo, 
                    'observacion' => $item['observacion'] ?? ''
                ];
            }

            // 4. JSON FINAL
            $cont = $request->input('contenido', []);
            $cap  = $request->input('capacitacion', []);
            $dif  = $cont['dificultades'] ?? [];
            $com  = $request->input('comentario_esp');

            $contenidoParaJson = [
                'profesional' => [
                    'tipo_doc'         => $profesionalDB ? $profesionalDB->tipo_doc : ($rawProf['tipo_doc'] ?? ''),
                    'doc'              => $profesionalDB ? $profesionalDB->doc : ($rawProf['doc'] ?? ''),
                    'nombres'          => $profesionalDB ? $profesionalDB->nombres : '',
                    'apellido_paterno' => $profesionalDB ? $profesionalDB->apellido_paterno : '',
                    'apellido_materno' => $profesionalDB ? $profesionalDB->apellido_materno : '',
                    'email'            => $profesionalDB ? $profesionalDB->email : '',
                    'cargo'            => $profesionalDB ? $profesionalDB->cargo : '',
                    'telefono'         => $profesionalDB ? $profesionalDB->telefono : '',
                    'utiliza_sihce'    => $rawProf['cuenta_sihce'] ?? '',
                    'id'               => $profesionalDB ? $profesionalDB->id : null,
                    'created_at'       => $profesionalDB ? $profesionalDB->created_at : null,
                    'updated_at'       => $profesionalDB ? $profesionalDB->updated_at : null,
                ],
                'inicio_labores' => [
                    'fecha_registro'     => $cont['fecha'] ?? date('Y-m-d'),
                    'consultorios'       => $cont['num_ambientes'] ?? '',
                    'nombre_consultorio' => $cont['denominacion_ambiente'] ?? '',
                    'turno'              => $cont['turno'] ?? '',
                    'comentarios'        => mb_strtoupper($com, 'UTF-8')
                ],
                // Campos Raíz
                'fecha_registro'           => $cont['fecha'] ?? date('Y-m-d'),
                'comentarios_generales'    => mb_strtoupper($com, 'UTF-8'),
                'num_consultorios'         => $cont['num_ambientes'] ?? '',
                'denominacion_consultorio' => $cont['denominacion_ambiente'] ?? '',
                'turno'                    => $cont['turno'] ?? '',
                'recibio_capacitacion'     => $cap['recibieron_cap'] ?? '',
                'inst_capacitacion'        => $cap['institucion_cap'] ?? null,
                'firmo_dj'                 => $rawProf['firmo_dj'] ?? '',
                'firmo_confidencialidad'   => $rawProf['firmo_confidencialidad'] ?? '',
                'comunica_a'               => $dif['comunica'] ?? '',
                'medio_soporte'            => $dif['medio'] ?? '',
                // Arrays
                'inventario' => $inventarioMapeado,
                'seccion_dni' => [
                    'tipo_dni'     => $cont['tipo_dni_fisico'] ?? '',
                    'version_dnie' => $cont['dnie_version'] ?? '',
                    'firma_sihce'  => $cont['dnie_firma_sihce'] ?? '',
                    'comentarios'  => str($cont['dni_observacion'])->upper() ?? ''
                ],
                'comentarios' => null,
                'foto_evidencia' => $arrayFotos
            ];

            // --- CORRECCIÓN: GUARDADO DIRECTO DEL ARRAY ---
            // Como tu modelo ya debe tener 'casts' => ['contenido' => 'array'], 
            // pasamos el array directamente. Laravel se encarga de convertirlo a JSON.
            MonitoreoModulos::updateOrCreate(
                ['cabecera_monitoreo_id' => $id, 'modulo_nombre' => 'enfermeria_esp'],
                [
                    'contenido'        => $contenidoParaJson,
                    'pdf_firmado_path' => null
                ]
            );

            return redirect()
                ->route('usuario.monitoreo.salud_mental_group.index', $id)
                ->with('success', 'Información guardada correctamente.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage())->withInput();
        }
    }
}
