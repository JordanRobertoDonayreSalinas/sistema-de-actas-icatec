<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\CabeceraMonitoreo;
use App\Models\Profesional;
use App\Models\ComCapacitacion;
//use App\Models\ComEquipamiento;
use App\Models\ComDificultad;
use App\Models\ComFotos;
use App\Models\ComDocuAsisten;

use App\Models\MonitoreoModulos;
use App\Models\EquipoComputo;

class TriajeController extends Controller
{
    // 1. MÉTODO INDEX: Carga el formulario Y los datos guardados previamente
    public function index($id){
        $acta = CabeceraMonitoreo::with('establecimiento')->findOrFail($id);
        
        $dbCapacitacion = ComCapacitacion::with('profesional')
                ->where('acta_id', $id)->where('modulo_id', 'triaje')->first();

        $dbInventario = EquipoComputo::where('cabecera_monitoreo_id', $id)
                    ->where('modulo', 'triaje')->get();
        
        $dbDificultad = ComDificultad::where('acta_id', $id)
                    ->where('modulo_id', 'triaje')->first();

        $dbFotos = ComFotos::where('acta_id', $id)
                ->where('modulo_id', 'triaje')->get();
        
        $dbInicioLabores = ComDocuAsisten::where('acta_id', $id)
                            ->where('modulo_id', 'triaje')->first();

        return view('usuario.monitoreo.modulos.triaje', compact('acta', 'dbCapacitacion', 'dbInventario', 'dbDificultad', 'dbFotos', 'dbInicioLabores'));
    }

    // 2. BUSCADOR (Sin cambios)
    public function buscarProfesional($doc)
    {
        $profesional = Profesional::where('doc', $doc)->first();

        if ($profesional) {
            return response()->json(['success' => true, 'data' => $profesional]);
        } else {
            return response()->json(['success' => false, 'message' => 'Profesional no encontrado.']);
        }
    }

    public function store(Request $request, $id)
    {
        // 1. Decodificar el JSON de datos que viene del frontend
        $data = json_decode($request->input('data'), true);

        // Validación básica
        if (!$data || !isset($data['profesional']['doc'])) {
            return response()->json(['success' => false, 'message' => 'Faltan datos del profesional'], 422);
        }

        try {
            DB::beginTransaction();

            // =========================================================
            // A. GUARDADO EN TABLAS SQL (Para reportes y estadísticas)
            // =========================================================

            // 1. PROFESIONAL
            $datosProfesional = $data['profesional'];
            $profesional = Profesional::updateOrCreate(
                ['doc' => $datosProfesional['doc']], 
                [
                    'tipo_doc'         => $datosProfesional['tipo_doc'] ?? 'DNI',
                    'apellido_paterno' => $datosProfesional['apellido_paterno'],
                    'apellido_materno' => $datosProfesional['apellido_materno'],
                    'nombres'          => $datosProfesional['nombres'],
                    'email'            => $datosProfesional['email'] ?? null,
                    'telefono'         => $datosProfesional['telefono'] ?? null,
                ]
            );

            // 2. CAPACITACIÓN (Tabla SQL)
            $datosCapacitacion = $data['capacitacion'] ?? [];
            if (!empty($datosCapacitacion)) {
                ComCapacitacion::updateOrCreate(
                    ['acta_id' => $id, 'modulo_id' => 'triaje'],
                    [
                        'profesional_id'        => $profesional->id,
                        'recibieron_cap'        => $datosCapacitacion['recibieron_cap'] ?? 'NO',
                        'institucion_cap'       => ($datosCapacitacion['recibieron_cap'] === 'SI') ? ($datosCapacitacion['institucion_cap'] ?? null) : null,
                        'decl_jurada'           => $datosCapacitacion['decl_jurada'] ?? 'NO',
                        'comp_confidencialidad' => $datosCapacitacion['comp_confidencialidad'] ?? 'NO',
                    ]
                );
            }

            // 3. INVENTARIO (Tabla SQL - CRUCIAL para que ConsolidadoPdfController línea 25 funcione)
            EquipoComputo::where('cabecera_monitoreo_id', $id)
                            ->where('modulo', 'triaje') 
                            ->delete();

            $listaInventario = $data['inventario'] ?? [];
            if (!empty($listaInventario)) {
                foreach ($listaInventario as $item) {
                    EquipoComputo::create([
                        'cabecera_monitoreo_id' => $id,
                        'modulo'                => 'triaje', 
                        'descripcion'           => $item['descripcion'] ?? 'SIN DESCRIPCION',
                        'cantidad'              => 1, 
                        'estado'                => $item['estado'] ?? 'REGULAR',
                        'nro_serie'             => $item['codigo'] ?? null, 
                        'propio'                => $item['propiedad'] ?? 'ESTABLECIMIENTO',
                        'observacion'           => $item['observacion'] ?? ''
                    ]);
                }
            }

            // 4. DIFICULTADES (Tabla SQL)
            $datosDificultad = $data['dificultades'] ?? [];
            ComDificultad::updateOrCreate(
                ['acta_id' => $id, 'modulo_id' => 'triaje'],
                [
                    'profesional_id' => $profesional->id,
                    'insti_comunica' => $datosDificultad['institucion'] ?? null,
                    'medio_comunica' => $datosDificultad['medio'] ?? null,
                ]
            );

            // 5. INICIO LABORES / TABLAS AUXILIARES
            $datosInicio = $data['inicio_labores'] ?? [];
            ComDocuAsisten::updateOrCreate(
                ['acta_id' => $id, 'modulo_id' => 'triaje'],
                [
                    'profesional_id'    => $profesional->id,
                    'cant_consultorios' => $datosInicio['consultorios'] ?? null,
                    'nombre_consultorio'=> $datosInicio['nombre_consultorio'] ?? null,
                    'turno'             => $datosInicio['turno'] ?? null,
                    'fua'               => null,
                    'referencia'        => null,
                    'receta'            => null,
                    'orden_laboratorio' => null,
                ]
            );

            // =========================================================
            // B. PREPARACIÓN DEL JSON PARA EL PDF (TRANSFORMACIÓN)
            // =========================================================
            
            // AQUÍ ESTÁ LA CLAVE: "Aplanamos" los datos para que tengan la misma estructura 
            // que 'ConsultaMedicina' y el PDF los reconozca automáticamente.

            $contenidoParaPDF = [
                'profesional'            => $data['profesional'], // Mantenemos objeto profesional

                // Mapeo de Inicio de Labores
                'num_consultorios'       => $datosInicio['consultorios'] ?? '1',
                'denominacion_consultorio' => $datosInicio['nombre_consultorio'] ?? '',
                'turno'                  => $datosInicio['turno'] ?? 'MAÑANA',
                
                // Mapeamos Capacitación (Sacamos los datos del array anidado al nivel raíz)
                'recibio_capacitacion'   => $datosCapacitacion['recibieron_cap'] ?? 'NO',
                'inst_capacitacion'      => ($datosCapacitacion['recibieron_cap'] ?? 'NO') === 'SI' ? ($datosCapacitacion['institucion_cap'] ?? null) : null,
                'firmo_dj'               => $datosCapacitacion['decl_jurada'] ?? 'NO',
                'firmo_confidencialidad' => $datosCapacitacion['comp_confidencialidad'] ?? 'NO',
                
                // Mapeamos Dificultades/Comunicación
                'comunica_a'             => $datosDificultad['institucion'] ?? null,
                'medio_soporte'          => $datosDificultad['medio'] ?? null,
                
                // Campos extra que Medicina usa y Triaje podría necesitar vacíos para no romper la vista
                'num_consultorios'       => '1', // Opcional, según lógica
                'comentarios'            => null
            ];

            // =========================================================
            // C. GESTIÓN DE FOTOS
            // =========================================================
            
            $rutasFotos = [];
            // 1. Recuperar fotos existentes si no se han borrado
            $registroPrevio = MonitoreoModulos::where('cabecera_monitoreo_id', $id)
                                ->where('modulo_nombre', 'triaje')->first();
            
            // Intentamos recuperar fotos del JSON anterior si existen
            if ($registroPrevio && isset($registroPrevio->contenido['foto_evidencia'])) {
                $prev = $registroPrevio->contenido['foto_evidencia'];
                $rutasFotos = is_array($prev) ? $prev : [$prev];
            }
            
            // Si el frontend envía 'foto_evidencia' (array de strings) con las fotos que deben quedar
            // usamos eso para filtrar. (Opcional, depende de tu frontend).
            // Por ahora asumimos que agregamos las nuevas:

            // 2. Guardar nuevas fotos
            if ($request->hasFile('fotos')) {
                foreach ($request->file('fotos') as $foto) {
                    $path = $foto->store('evidencia_fotos', 'public');
                    
                    // Tabla auxiliar
                    ComFotos::create([
                        'acta_id'        => $id,
                        'modulo_id'      => 'triaje',
                        'profesional_id' => $profesional->id,
                        'url_foto'       => $path
                    ]);

                    $rutasFotos[] = $path;
                }
            }
            
            // Agregamos las fotos al JSON estandarizado
            $contenidoParaPDF['foto_evidencia'] = $rutasFotos;

            // =========================================================
            // D. GUARDAR JSON FINAL EN MONITOREO_MODULOS
            // =========================================================

            MonitoreoModulos::updateOrCreate(
                [
                    'cabecera_monitoreo_id' => $id,
                    'modulo_nombre'         => 'triaje'
                ],
                [
                    'contenido' => $contenidoParaPDF, // Ahora guardamos el array transformado
                    'pdf_firmado_path' => null
                ]
            );

            DB::commit();
            return response()->json(['success' => true, 'redirect' => route('usuario.monitoreo.modulos', $id)]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function eliminarFoto($id)
    {
        try {
            $foto = ComFotos::findOrFail($id);

            // 1. Borrar archivo del almacenamiento (disco 'public')
            if (Storage::disk('public')->exists($foto->url_foto)) {
                Storage::disk('public')->delete($foto->url_foto);
            }

            // 2. Borrar registro de la BD
            $foto->delete();

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
