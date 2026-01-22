<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\CabeceraMonitoreo;
use App\Models\Profesional;
use App\Models\ComCapacitacion;
use App\Models\ComDificultad;
use App\Models\ComFotos;
use App\Models\ComDocuAsisten;
use App\Models\ComDni;
use App\Models\MonitoreoModulos;
use App\Models\EquipoComputo;

class OdontologiaController extends Controller
{
    // Constante para identificar el módulo en la BD
    const MODULO_ID = 'consulta_odontologia';

    // 1. MÉTODO INDEX
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
        

        return view('usuario.monitoreo.modulos.odontologia', compact('acta', 'dbCapacitacion', 'dbInventario', 'dbDificultad', 'dbFotos', 'dbInicioLabores', 'dbDni'));
    }

    // 2. BUSCADOR PROFESIONAL
    public function buscarProfesional($doc)
    {
        $profesional = Profesional::where('doc', $doc)->first();

        if ($profesional) {
            return response()->json(['success' => true, 'data' => $profesional]);
        } else {
            return response()->json(['success' => false, 'message' => 'Profesional no encontrado.']);
        }
    }

    // 3. STORE: Guardar datos
    public function store(Request $request, $id)
    {
        $data = json_decode($request->input('data'), true);

        if (!$data || !isset($data['profesional']['doc'])) {
                return response()->json(['success' => false, 'message' => 'Faltan datos del profesional'], 422);
        }

        try {
            DB::beginTransaction();

            // =========================================================
            // A. GUARDADO EN TABLAS SQL
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
                    // Aseguramos mayúsculas y sin espacios para evitar duplicados "OTROS"
                    'cargo'            => isset($datosProfesional['cargo']) ? mb_strtoupper(trim($datosProfesional['cargo']), 'UTF-8') : null,
                    'telefono'         => $datosProfesional['telefono'] ?? null,
                ]
            );

            // 2. CAPACITACIÓN
            $datosCapacitacion = $data['capacitacion'];
            ComCapacitacion::updateOrCreate(
                ['acta_id' => $id, 'modulo_id' => self::MODULO_ID],
                [
                    'profesional_id'        => $profesional->id,
                    'recibieron_cap'        => $datosCapacitacion['recibieron_cap'] ?? 'NO',
                    'institucion_cap'       => ($datosCapacitacion['recibieron_cap'] === 'SI') ? ($datosCapacitacion['institucion_cap'] ?? null) : null,
                    'decl_jurada'           => $datosCapacitacion['decl_jurada'] ?? 'NO',
                    'comp_confidencialidad' => $datosCapacitacion['comp_confidencialidad'] ?? 'NO',
                ]
            );

            // 3. INVENTARIO
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
                        'cantidad'              => 1, 
                        'estado'                => $item['estado'] ?? 'OPERATIVO',
                        'nro_serie'             => $item['codigo'] ? str($item['codigo'])->upper() : null,
                        'propio'                => $item['propiedad'] ?? 'COMPARTIDO',
                        'observacion'           => $item['observacion'] ? str($item['observacion'])->upper() : null,
                    ]);
                }
            }

            // 4. DIFICULTADES
            $datosDificultad = $data['dificultades'];
            ComDificultad::updateOrCreate(
                ['acta_id' => $id, 'modulo_id' => self::MODULO_ID],
                [
                    'profesional_id' => $profesional->id,
                    'insti_comunica' => $datosDificultad['institucion'] ?? null,
                    'medio_comunica' => $datosDificultad['medio'] ?? null,
                ]
            );

            // 5. INICIO LABORES / TABLAS AUXILIARES (AQUÍ ESTÁ EL CAMBIO PRINCIPAL)
            $datosInicio = $data['inicio_labores'] ?? [];
            ComDocuAsisten::updateOrCreate(
                ['acta_id' => $id, 'modulo_id' => self::MODULO_ID],
                [
                    'profesional_id'    => $profesional->id,
                    'cant_consultorios' => $datosInicio['consultorios'] ?? null,
                    'nombre_consultorio'=> $datosInicio['nombre_consultorio'] ?? null,
                    'turno'             => $datosInicio['turno'] ?? null,
                    
                    // --- NUEVOS CAMPOS AGREGADOS ---
                    'fecha_registro'    => $datosInicio['fecha_registro'] ?? null,
                    'comentarios'       => isset($datosInicio['comentarios']) ? str($datosInicio['comentarios'])->upper() : null,
                    // -------------------------------

                    'fua'               => $datosInicio['fua'] ?? null,
                    'referencia'        => $datosInicio['referencia'] ?? null,
                    'receta'            => $datosInicio['receta'] ?? null,
                    'orden_laboratorio' => $datosInicio['orden_lab'] ?? null, // Ojo: en blade es 'orden_lab', mapea a 'orden_laboratorio' en BD
                ]
            );

            // 6. SECCION DNI
            $datosDni = $data['seccion_dni'] ?? [];     
            $esElectronico = ($datosDni['tipo_dni'] ?? '') === 'DNI_ELECTRONICO';
            
            ComDni::updateOrCreate(
                ['acta_id' => $id, 'modulo_id' => self::MODULO_ID],
                [
                    'profesional_id' => $profesional->id,
                    'tip_dni'        => $datosDni['tipo_dni'] ?? null,
                    'version_dni'    => $esElectronico ? ($datosDni['version_dnie'] ?? null) : null,
                    'firma_sihce'    => $esElectronico ? ($datosDni['firma_sihce'] ?? null) : null,
                    'comentarios'    => isset($datosDni['comentarios']) ? str($datosDni['comentarios'])->upper() : null,
                ]
            );

            // =========================================================
            // B. PREPARACIÓN DEL JSON PARA EL PDF
            // =========================================================
            
            $contenidoParaPDF = [
                'profesional'            => $data['profesional'],
                
                // Mapeo de Inicio de Labores
                'num_consultorios'       => $datosInicio['consultorios'] ?? '1',
                'denominacion_consultorio' => $datosInicio['nombre_consultorio'] ?? '',
                'turno'                  => $datosInicio['turno'] ?? 'MAÑANA',
                'fecha_registro'         => $datosInicio['fecha_registro'] ?? null, // <--- AGREGADO
                
                // Mapeo de Capacitación
                'firmo_dj'               => $datosCapacitacion['decl_jurada'] ?? 'NO',
                'firmo_confidencialidad' => $datosCapacitacion['comp_confidencialidad'] ?? 'NO',
                'recibio_capacitacion'   => $datosCapacitacion['recibieron_cap'] ?? 'NO',
                'inst_capacitacion'      => ($datosCapacitacion['recibieron_cap'] === 'SI') ? ($datosCapacitacion['institucion_cap'] ?? null) : null,
                
                // Mapeo de DNI
                'tipo_dni_fisico'        => ($datosDni['tipo_dni'] ?? '') === 'DNI_ELECTRONICO' ? 'ELECTRONICO' : 'AZUL',
                'dnie_version'           => $esElectronico ? ($datosDni['version_dnie'] ?? null) : null,
                'dnie_firma_sihce'       => $esElectronico ? ($datosDni['firma_sihce'] ?? null) : null,
                'dni_observacion'        => $datosDni['comentarios'] ?? null,
                
                // Mapeo de Dificultades
                'comunica_a'             => $datosDificultad['institucion'] ?? null,
                'medio_soporte'          => $datosDificultad['medio'] ?? null,

                // Datos específicos Odontología
                'fua'                    => $datosInicio['fua'] ?? null,
                'referencia'             => $datosInicio['referencia'] ?? null,
                'receta'                 => $datosInicio['receta'] ?? null,
                'orden_laboratorio'      => $datosInicio['orden_lab'] ?? null,

                // Comentarios Generales
                'comentarios_generales'  => $datosInicio['comentarios'] ?? null, // <--- AGREGADO
                
                // Snapshot de inventario
                'inventario'             => $listaInventario,
            ];

            // =========================================================
            // C. GESTIÓN DE FOTOS
            // =========================================================
            
            $rutasFotos = [];
            $registroPrevio = MonitoreoModulos::where('cabecera_monitoreo_id', $id)
                                ->where('modulo_nombre', 'consulta_odontologia')->first();

            if ($registroPrevio && isset($registroPrevio->contenido['foto_evidencia'])) {
                $prev = $registroPrevio->contenido['foto_evidencia'];
                $rutasFotos = is_array($prev) ? $prev : [$prev];
            }

            if ($request->hasFile('fotos')) {
                foreach ($request->file('fotos') as $foto) {
                    $path = $foto->store('evidencia_fotos', 'public');
                    ComFotos::create([
                        'acta_id'        => $id,
                        'modulo_id'      => self::MODULO_ID,
                        'profesional_id' => $profesional->id,
                        'url_foto'       => $path
                    ]);
                    $rutasFotos[] = $path;
                }
            }

            $contenidoParaPDF['foto_evidencia'] = $rutasFotos;

            // =========================================================
            // D. GUARDAR JSON FINAL
            // =========================================================

            MonitoreoModulos::updateOrCreate(
                [
                    'cabecera_monitoreo_id' => $id,
                    'modulo_nombre'         => 'consulta_odontologia'
                ],
                [
                    'contenido' => $contenidoParaPDF,
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
            if (Storage::disk('public')->exists($foto->url_foto)) {
                Storage::disk('public')->delete($foto->url_foto);
            }
            $foto->delete();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}