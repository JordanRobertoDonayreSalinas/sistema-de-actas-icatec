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
use App\Models\ComDni;

use App\Models\MonitoreoModulos;
use App\Models\EquipoComputo;

class PsicologiaController extends Controller
{
    // 1. MÉTODO INDEX: Carga el formulario Y los datos guardados previamente
    const MODULO_ID = 'consulta_psicologia';

    public function index($id){
        $acta = CabeceraMonitoreo::with('establecimiento')->findOrFail($id);
        
        $dbCapacitacion = ComCapacitacion::with('profesional')
                    ->where('acta_id', $id)->where('modulo_id', self::MODULO_ID)->first();

        $dbInventario = EquipoComputo::where('cabecera_monitoreo_id', $id)
                            ->where('modulo', self::MODULO_ID)->get();
        
        $dbDificultad = ComDificultad::where('acta_id', $id)
                            ->where('modulo_id', self::MODULO_ID)->first();

        $dbFotos = ComFotos::where('acta_id', $id)
                        ->where('modulo_id', self::MODULO_ID)->get();

        $dbInicioLabores = ComDocuAsisten::where('acta_id', $id)
                            ->where('modulo_id', self::MODULO_ID)->first();
        $dbDni = ComDni::where('acta_id', $id)
                    ->where('modulo_id', self::MODULO_ID)->first();

        // Enviamos la nueva variable a la vista
        return view('usuario.monitoreo.modulos.psicologia', compact('acta', 'dbCapacitacion', 'dbInventario', 'dbDificultad', 'dbFotos', 'dbInicioLabores', 'dbDni'));
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

    // 3. STORE: Guarda y Redirige
    // 3. STORE: Guarda y Redirige
    public function store(Request $request, $id)
    {
        $data = json_decode($request->input('data'), true);

        if (!$data || !isset($data['profesional']['doc'])) {
             return response()->json(['success' => false, 'message' => 'Faltan datos del profesional'], 422);
        }

        try {
            DB::beginTransaction();

            // ---------------------------------------------------------
            // A. GUARDADO SQL (TABLAS RELACIONALES) - ESTO ESTÁ BIEN
            // ---------------------------------------------------------

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

            // 2. CAPACITACIÓN (SQL)
            $datosCapacitacion = $data['capacitacion'];
            ComCapacitacion::updateOrCreate(
                ['acta_id' => $id, 'modulo_id' => self::MODULO_ID],
                [
                    'profesional_id'  => $profesional->id,
                    'recibieron_cap'  => $datosCapacitacion['recibieron_cap'],
                    'institucion_cap' => ($datosCapacitacion['recibieron_cap'] === 'SI') ? ($datosCapacitacion['institucion_cap'] ?? null) : null,
                    'decl_jurada'           => $datosCapacitacion['decl_jurada'] ?? null,
                    'comp_confidencialidad' => $datosCapacitacion['comp_confidencialidad'] ?? null,
                ]
            );

            // 3. INVENTARIO (SQL)
            EquipoComputo::where('cabecera_monitoreo_id', $id)
                         ->where('modulo', self::MODULO_ID)
                         ->delete();

            $listaInventario = $data['inventario'] ?? [];
            if (!empty($listaInventario)) {
                foreach ($listaInventario as $item) {
                    EquipoComputo::create([
                        'cabecera_monitoreo_id' => $id,
                        'modulo'                => self::MODULO_ID,
                        'descripcion'           => $item['descripcion'],
                        'cantidad'              => '1',
                        'estado'                => $item['estado'],
                        'nro_serie'             => $item['codigo'] ?? null, 
                        'propio'                => $item['propiedad'],
                        'observacion'           => $item['observacion'] ?? ''
                    ]);
                }
            }

            // 4. DIFICULTADES (SQL)
            $datosDificultad = $data['dificultades'];
            ComDificultad::updateOrCreate(
                ['acta_id' => $id, 'modulo_id' => self::MODULO_ID],
                [
                    'profesional_id' => $profesional->id,
                    'insti_comunica' => $datosDificultad['institucion'] ?? null,
                    'medio_comunica' => $datosDificultad['medio'] ?? null,
                ]
            );

            // 5. INICIO LABORES (SQL)
            $datosInicio = $data['inicio_labores'] ?? [];
            ComDocuAsisten::updateOrCreate(
                ['acta_id' => $id, 'modulo_id' => self::MODULO_ID],
                [
                    'profesional_id'    => $profesional->id,
                    'cant_consultorios' => $datosInicio['consultorios'] ?? null,
                    'nombre_consultorio'=> $datosInicio['nombre_consultorio'] ?? null,
                    'turno'             => $datosInicio['turno'] ?? null,
                    'fua'               => $datosInicio['fua'] ?? null,
                    'referencia'        => $datosInicio['referencia'] ?? null,
                    'receta'            => $datosInicio['receta'] ?? null,
                    'orden_laboratorio' => $datosInicio['orden_lab'] ?? null,
                ]
            );

            // 6. SECCIÓN DNI (SQL)
            $datosDni = $data['seccion_dni'] ?? [];     
            $esElectronico = ($datosDni['tipo_dni'] ?? '') === 'DNI_ELECTRONICO';
            
            ComDni::updateOrCreate(
                ['acta_id' => $id, 'modulo_id' => self::MODULO_ID],
                [
                    'profesional_id' => $profesional->id,
                    'tip_dni'        => $datosDni['tipo_dni'] ?? null,
                    'version_dni'    => $esElectronico ? ($datosDni['version_dnie'] ?? null) : null,
                    'firma_sihce'    => $esElectronico ? ($datosDni['firma_sihce'] ?? null) : null,
                    'comentarios'    => $datosDni['comentarios'] ?? null,
                ]
            );

            // ---------------------------------------------------------
            // B. TRANSFORMACIÓN JSON (AQUÍ ESTÁ LA SOLUCIÓN AL PDF)
            // ---------------------------------------------------------
            // Creamos un array nuevo "plano" que coincida con lo que pide el PDF
            
            $contenidoParaPDF = [
                'profesional'            => $data['profesional'],
                
                // Mapeo manual de claves anidadas a claves planas
                'num_consultorios'       => $datosInicio['consultorios'] ?? '1',
                'denominacion_consultorio' => $datosInicio['nombre_consultorio'] ?? '',
                'turno'                  => $datosInicio['turno'] ?? 'MAÑANA',
                
                'firmo_dj'               => $datosCapacitacion['decl_jurada'] ?? 'NO',
                'firmo_confidencialidad' => $datosCapacitacion['comp_confidencialidad'] ?? 'NO',
                'recibio_capacitacion'   => $datosCapacitacion['recibieron_cap'] ?? 'NO',
                'inst_capacitacion'      => ($datosCapacitacion['recibieron_cap'] === 'SI') ? ($datosCapacitacion['institucion_cap'] ?? null) : null,
                
                'tipo_dni_fisico'        => ($datosDni['tipo_dni'] ?? '') === 'DNI_ELECTRONICO' ? 'ELECTRONICO' : 'AZUL',
                'dnie_version'           => $esElectronico ? ($datosDni['version_dnie'] ?? null) : null,
                'dnie_firma_sihce'       => $esElectronico ? ($datosDni['firma_sihce'] ?? null) : null,
                'dni_observacion'        => $datosDni['comentarios'] ?? null,
                
                'comunica_a'             => $datosDificultad['institucion'] ?? null,
                'medio_soporte'          => $datosDificultad['medio'] ?? null,

                'fua'                    => $datosInicio['fua'] ?? null,
                'receta'                 => $datosInicio['receta'] ?? null,
                
                // Estos campos extra aseguran que no falle si la vista los pide
                'comentarios'            => null 
            ];

            // ---------------------------------------------------------
            // C. FOTOS
            // ---------------------------------------------------------
            $rutasFotos = [];
            
            // Recuperar fotos anteriores si existen en la BD
            $registroPrevio = MonitoreoModulos::where('cabecera_monitoreo_id', $id)
                                ->where('modulo_nombre', 'consulta_psicologia')->first();
            
            if ($registroPrevio && isset($registroPrevio->contenido['foto_evidencia'])) {
                $prev = $registroPrevio->contenido['foto_evidencia'];
                $rutasFotos = is_array($prev) ? $prev : [$prev];
            }

            // Guardar nuevas fotos
            if ($request->hasFile('fotos')) {
                foreach ($request->file('fotos') as $foto) {
                    $path = $foto->store('evidencia_fotos', 'public');
                    
                    ComFotos::create([
                        'acta_id'        => $id,
                        'modulo_id'      => 'consulta_psicologia',
                        'profesional_id' => $profesional->id,
                        'url_foto'       => $path
                    ]);
                    
                    $rutasFotos[] = $path;
                }
            }

            // Agregamos las fotos al JSON "Plano" (Usando singular para estandarizar con medicina)
            $contenidoParaPDF['foto_evidencia'] = $rutasFotos;

            // ---------------------------------------------------------
            // D. GUARDAR EN MONITOREO_MODULOS
            // ---------------------------------------------------------
            MonitoreoModulos::updateOrCreate(
                [
                    'cabecera_monitoreo_id' => $id, 
                    'modulo_nombre'         => 'consulta_psicologia'
                ],
                [
                    'contenido' => $contenidoParaPDF, // ¡AQUÍ GUARDAMOS EL ARRAY TRANSFORMADO!
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
