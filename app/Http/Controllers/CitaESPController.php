<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CabeceraMonitoreo;
use App\Models\MonitoreoModulos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use App\Models\EquipoComputo;
use App\Models\Profesional;
use App\Models\ComCapacitacion;
use App\Models\ComDificultad;
use App\Models\ComFotos;
use App\Models\ComDocuAsisten;
use App\Models\ComDni;

class CitaESPController extends Controller
{
    /**
     * Muestra el formulario de "Admisión y Citas" específico para CSMC.
     * Ruta: GET /usuario/monitoreo/{id}/citas-especializada
     */

    
    // public function index($id)
    // {
    //     // 1. Obtener la cabecera del monitoreo
    //     $monitoreo = CabeceraMonitoreo::with('establecimiento')->findOrFail($id);

    //     // 2. Validación de seguridad
    //     if ($monitoreo->tipo_origen !== 'ESPECIALIZADA') {
    //         return redirect()->route('usuario.monitoreo.modulos', $id)
    //             ->with('error', 'Este módulo no corresponde al tipo de establecimiento.');
    //     }

    //     // 3. Buscar datos existentes (Clave: citas_esp)
    //     $registro = MonitoreoModulos::where('cabecera_monitoreo_id', $id)
    //                                 ->where('modulo_nombre', 'citas_esp')
    //                                 ->first();

    //     // 4. Decodificar JSON
    //     $data = $registro ? json_decode($registro->contenido, true) : [];

    //     return view('usuario.monitoreo.modulos_especializados.citas', compact('monitoreo', 'data'));
    // }

    // 1. MÉTODO INDEX: Carga el formulario Y los datos guardados previamente
    public function index($id){

        // 1. Obtener la cabecera del monitoreo
        $monitoreo = CabeceraMonitoreo::with('establecimiento')->findOrFail($id);
        
        // 2. Validación de seguridad
        if ($monitoreo->tipo_origen !== 'ESPECIALIZADA') {
            return redirect()->route('usuario.monitoreo.modulos', $id)
                ->with('error', 'Este módulo no corresponde al tipo de establecimiento.');
        }

        $acta = CabeceraMonitoreo::with('establecimiento')->findOrFail($id);
        
        $dbCapacitacion = ComCapacitacion::with('profesional')
                        ->where('acta_id', $id)->where('modulo_id', 'citas_esp')->first();

        $dbInventario = EquipoComputo::where('cabecera_monitoreo_id', $id)
                        ->where('modulo', 'citas_esp')->get();
        
        $dbDificultad = ComDificultad::where('acta_id', $id)
                        ->where('modulo_id', 'citas_esp')->first();

        $dbFotos = ComFotos::where('acta_id', $id)
                        ->where('modulo_id', 'citas_esp')->get();
        
        $dbInicioLabores = ComDocuAsisten::where('acta_id', $id)
                        ->where('modulo_id', 'citas_esp')->first();
        
        $dbDni = ComDni::where('acta_id', $id)
                        ->where('modulo_id', 'citas_esp')->first();



        return view('usuario.monitoreo.modulos_especializados.citas', compact('acta', 'dbCapacitacion', 'dbInventario', 'dbDificultad', 'dbFotos', 'dbInicioLabores', 'dbDni'));
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
                    'cargo'            => isset($datosProfesional['cargo']) ? mb_strtoupper(trim($datosProfesional['cargo']), 'UTF-8') : null,
                    'telefono'         => $datosProfesional['telefono'] ?? null,
                ]
            );

            // 2. CAPACITACIÓN (Tabla SQL)
            $datosCapacitacion = $data['capacitacion'] ?? [];
            if (!empty($datosCapacitacion)) {
                ComCapacitacion::updateOrCreate(
                    ['acta_id' => $id, 'modulo_id' => 'citas_esp'],
                    [
                        'profesional_id'        => $profesional->id,
                        'recibieron_cap'        => $datosCapacitacion['recibieron_cap'] ?? 'NO',
                        'institucion_cap'       => ($datosCapacitacion['recibieron_cap'] === 'SI') ? ($datosCapacitacion['institucion_cap'] ?? null) : null,
                        'decl_jurada'           => $datosCapacitacion['decl_jurada'] ?? 'NO',
                        'comp_confidencialidad' => $datosCapacitacion['comp_confidencialidad'] ?? 'NO',
                    ]
                );
            }

            // 3. INVENTARIO (Tabla SQL)
            EquipoComputo::where('cabecera_monitoreo_id', $id)
                            ->where('modulo', 'citas_esp') 
                            ->delete();

            $listaInventario = $data['inventario'] ?? [];
            if (!empty($listaInventario)) {
                foreach ($listaInventario as $item) {
                    EquipoComputo::create([
                        'cabecera_monitoreo_id' => $id,
                        'modulo'                => 'citas_esp', 
                        'descripcion'           => $item['descripcion'] ?? 'SIN DESCRIPCION',
                        'cantidad'              => 1, 
                        'estado'                => $item['estado'] ?? 'OPERATIVO',
                        'nro_serie'             => $item['codigo'] ? str($item['codigo'])->upper() : null, // Aquí se guarda ya concatenado "NS 123"
                        'propio'                => $item['propiedad'] ?? 'COMPARTIDO',
                        'observacion'           => $item['observacion'] ? str($item['observacion'])->upper() : null,
                    ]);
                }
            }

            // 4. DIFICULTADES (Tabla SQL)
            $datosDificultad = $data['dificultades'] ?? [];
            ComDificultad::updateOrCreate(
                ['acta_id' => $id, 'modulo_id' => 'citas_esp'],
                [
                    'profesional_id' => $profesional->id,
                    'insti_comunica' => $datosDificultad['institucion'] ?? null,
                    'medio_comunica' => $datosDificultad['medio'] ?? null,
                ]
            );

            // 5. INICIO LABORES (Tabla SQL)
            $datosInicio = $data['inicio_labores'] ?? [];
            $datosProfesionalParaSihce = $data['profesional'] ?? [];
            ComDocuAsisten::updateOrCreate(
                ['acta_id' => $id, 'modulo_id' => 'citas_esp'],
                [
                    'profesional_id'    => $profesional->id,
                    // Usamos ?? null para evitar el error "cannot be null" si falta el dato
                    'cant_consultorios' => $datosInicio['consultorios'] ?? null,
                    'nombre_consultorio'=> str($datosInicio['nombre_consultorio'])->upper() ?? null,
                    'turno'             => $datosInicio['turno'] ?? null,
                    'fecha_registro'    => $datosInicio['fecha_registro'] ?? null,
                    'comentarios'       => isset($datosInicio['comentarios']) ? str($datosInicio['comentarios'])->upper() : null,
                    'utiliza_sihce'     => $datosProfesionalParaSihce['utiliza_sihce'] ?? null,
                    
                    // Estos campos no existen en el form de citas_esp, enviamos null o defaults
                    'fua'               => null,
                    'referencia'        => null,
                    'receta'            => null,
                    'orden_laboratorio' => null,
                ]
            );

            // 6. SECCIÓN DNI (Tabla SQL - CORREGIDO)
            $datosDni = $data['seccion_dni'] ?? [];     
            $esElectronico = ($datosDni['tipo_dni'] ?? '') === 'ELECTRONICO';
            
            // Usamos 'citas_esp' en lugar de self::MODULO_ID si no está definido
            ComDni::updateOrCreate(
                ['acta_id' => $id, 'modulo_id' => 'citas_esp'],
                [
                    'profesional_id' => $profesional->id,
                    'tip_dni'        => $datosDni['tipo_dni'] ?? null,
                    'version_dni'    => $esElectronico ? ($datosDni['version_dnie'] ?? null) : null,
                    'firma_sihce'    => $esElectronico ? ($datosDni['firma_sihce'] ?? null) : null,
                    'comentarios'    => str($datosDni['comentarios'])->upper() ?? null,
                ]
            );

            // =========================================================
            // B. PREPARACIÓN DEL JSON PARA EL PDF (SNAPSHOT COMPLETO)
            // =========================================================

            $contenidoParaPDF = [
                'profesional'            => $data['profesional'], 

                // Inicio Labores
                'inicio_labores'         => $datosInicio, // Guardamos el objeto completo también por si acaso
                'fecha_registro'         => $datosInicio['fecha_registro'] ?? null,
                'comentarios_generales'  => $datosInicio['comentarios'] ?? null,
                'num_consultorios'       => $datosInicio['consultorios'] ?? '1',
                'denominacion_consultorio' => $datosInicio['nombre_consultorio'] ?? '',
                'turno'                  => $datosInicio['turno'] ?? 'MAÑANA',
                
                // Capacitación
                'recibio_capacitacion'   => $datosCapacitacion['recibieron_cap'] ?? 'NO',
                'inst_capacitacion'      => ($datosCapacitacion['recibieron_cap'] ?? 'NO') === 'SI' ? ($datosCapacitacion['institucion_cap'] ?? null) : null,
                'firmo_dj'               => $datosCapacitacion['decl_jurada'] ?? 'NO',
                'firmo_confidencialidad' => $datosCapacitacion['comp_confidencialidad'] ?? 'NO',
                
                // Dificultades
                'comunica_a'             => $datosDificultad['institucion'] ?? null,
                'medio_soporte'          => $datosDificultad['medio'] ?? null,
                
                // --- AGREGADO: INVENTARIO (Snapshot para el PDF/Vista) ---
                'inventario'             => $listaInventario,

                // --- AGREGADO: SECCIÓN DNI (Snapshot) ---
                'seccion_dni'            => $datosDni,
                
                'comentarios'            => null
            ];

            // =========================================================
            // C. GESTIÓN DE FOTOS
            // =========================================================
            
            $rutasFotos = [];
            // Recuperar fotos previas del JSON
            $registroPrevio = MonitoreoModulos::where('cabecera_monitoreo_id', $id)
                                ->where('modulo_nombre', 'citas_esp')->first();
            
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
                        'modulo_id'      => 'citas_esp',
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
                    'modulo_nombre'         => 'citas_esp'
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

    /**
     * Guarda la información del módulo.
     * Ruta: POST /usuario/monitoreo/{id}/citas-especializada
     */
    // public function store(Request $request, $id)
    // {
    //     // DEBUG: Si sigue sin guardar, descomenta la siguiente línea para ver qué llega
    //     // dd($request->all());

    //     try {
    //         DB::beginTransaction();

    //         // 1. Validar que exista la cabecera
    //         $monitoreo = CabeceraMonitoreo::findOrFail($id);

    //         // 2. Buscar si ya existe el registro de este módulo
    //         $registro = MonitoreoModulos::where('cabecera_monitoreo_id', $id)
    //                                     ->where('modulo_nombre', 'citas_esp')
    //                                     ->first();

    //         $contenidoActual = [];

    //         // 3. Si no existe, creamos la instancia y asignamos claves forzosamente
    //         if (!$registro) {
    //             $registro = new MonitoreoModulos();
    //             $registro->cabecera_monitoreo_id = $id;
    //             $registro->modulo_nombre = 'citas_esp';
    //         } else {
    //             // Si existe, recuperamos su contenido actual para no perder la foto
    //             $contenidoActual = json_decode($registro->contenido, true) ?? [];
    //         }

    //         // 4. Recoger datos del formulario (Quitamos token y archivo físico)
    //         $nuevosDatos = $request->except(['_token', 'foto_evidencia']);

    //         // 5. Procesar Imagen
    //         if ($request->hasFile('foto_evidencia')) {
    //             // Validación estricta de imagen
    //             $request->validate([
    //                 'foto_evidencia' => 'image|mimes:jpeg,png,jpg|max:10240' // 10MB Máx
    //             ]);

    //             // Borrar anterior si existe
    //             if (!empty($contenidoActual['foto_evidencia'])) {
    //                 if (Storage::disk('public')->exists($contenidoActual['foto_evidencia'])) {
    //                     Storage::disk('public')->delete($contenidoActual['foto_evidencia']);
    //                 }
    //             }

    //             // Guardar nueva
    //             $path = $request->file('foto_evidencia')->store('evidencias_csmc', 'public');
    //             $nuevosDatos['foto_evidencia'] = $path;
    //         } else {
    //             // Mantener anterior si no se subió nueva
    //             if (!empty($contenidoActual['foto_evidencia'])) {
    //                 $nuevosDatos['foto_evidencia'] = $contenidoActual['foto_evidencia'];
    //             }
    //         }

    //         // 6. Guardar JSON
    //         // JSON_UNESCAPED_UNICODE asegura que las tildes se guarden bien
    //         $registro->contenido = json_encode($nuevosDatos, JSON_UNESCAPED_UNICODE);
            
    //         $registro->save();

    //         DB::commit();

    //         return redirect()
    //             ->route('usuario.monitoreo.modulos', $id)
    //             ->with('success', 'Módulo de Citas CSMC guardado correctamente.');

    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         // Esto imprimirá el error en tu archivo de log (storage/logs/laravel.log)
    //         Log::error("Error guardando Citas CSMC: " . $e->getMessage());
            
    //         return back()
    //             ->with('error', 'Error al guardar: ' . $e->getMessage())
    //             ->withInput();
    //     }
    // }
}