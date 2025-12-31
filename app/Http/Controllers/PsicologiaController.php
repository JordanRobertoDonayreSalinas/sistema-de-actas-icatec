<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\Acta;
use App\Models\Profesional;
use App\Models\ComCapacitacion;
use App\Models\ComEquipamiento;
use App\Models\ComDificultad;
use App\Models\ComFotos;
use App\Models\ComDocuAsisten;
use App\Models\ComDni;

class PsicologiaController extends Controller
{
    // 1. MÉTODO INDEX: Carga el formulario Y los datos guardados previamente
    const MODULO_ID = 'PSICOLOGIA';

    public function index($id){
        $acta = Acta::with('establecimiento')->findOrFail($id);
        
        $dbCapacitacion = ComCapacitacion::with('profesional')
                    ->where('acta_id', $id)->where('modulo_id', self::MODULO_ID)->first();

        $dbInventario = ComEquipamiento::where('acta_id', $id)
                            ->where('modulo_id', self::MODULO_ID)->get();
        
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
    public function store(Request $request, $id)
    {
        // AHORA RECIBIMOS UN FORM-DATA HÍBRIDO.
        // El texto viene en un campo llamado 'data' (string JSON)
        // Las fotos vienen en 'fotos[]'
        
        // 1. Decodificar el JSON de datos
        $data = json_decode($request->input('data'), true);

        // Validamos manualmente porque $request->validate no lee directo del JSON string
        if (!$data || !isset($data['profesional']['doc'])) {
             return response()->json(['success' => false, 'message' => 'Faltan datos del profesional'], 422);
        }

        try {
            DB::beginTransaction();

            // 1. PROFESIONAL
            $datosProfesional = $data['profesional'];
            $profesional = Profesional::updateOrCreate(
                ['doc' => $datosProfesional['doc'], 'tipo_doc' => $datosProfesional['tipo_doc'] ?? 'DNI'],
                [
                    'apellido_paterno' => $datosProfesional['apellido_paterno'],
                    'apellido_materno' => $datosProfesional['apellido_materno'],
                    'nombres'          => $datosProfesional['nombres'],
                    'email'            => $datosProfesional['email'],
                    'telefono'         => $datosProfesional['telefono'],
                ]
            );

            // 2. CAPACITACIÓN
            $datosCapacitacion = $data['capacitacion'];
            ComCapacitacion::updateOrCreate(
                ['acta_id' => $id, 'modulo_id' => 'PSICOLOGIA'],
                [
                    'profesional_id'  => $profesional->id,
                    'recibieron_cap'  => $datosCapacitacion['recibieron_cap'],
                    'institucion_cap' => ($datosCapacitacion['recibieron_cap'] === 'SI') ? $datosCapacitacion['institucion_cap'] : null
                ]
            );


            // 3. INVENTARIO
            ComEquipamiento::where('acta_id', $id)->where('modulo_id', 'PSICOLOGIA')->delete();
            $listaInventario = $data['inventario'] ?? [];
            $comentarioGeneral = $data['inventario_comentarios'] ?? '';

            if (!empty($listaInventario)) {
                foreach ($listaInventario as $item) {
                    
                    // YA NO CONCATENAMOS. Guardamos directo.
                    
                    ComEquipamiento::create([
                        'acta_id'        => $id,
                        'modulo_id'      => 'PSICOLOGIA',
                        'profesional_id' => $profesional->id,
                        'descripcion'    => $item['descripcion'],
                        'cantidad'       => '1', // Siempre 1 según tu indicación
                        'propiedad'      => $item['propiedad'],
                        'estado'         => $item['estado'],
                        
                        // Mapeo directo: JS 'codigo' -> BD 'cod_barras'
                        'cod_barras'     => $item['codigo'] ?? null, 
                        
                        'observaciones'  => $item['observacion'] ?? '',
                        'comentarios'    => $comentarioGeneral
                    ]);
                }
            }

            // 4. DIFICULTADES
            $datosDificultad = $data['dificultades'];
            ComDificultad::updateOrCreate(
                ['acta_id' => $id, 'modulo_id' => 'PSICOLOGIA'],
                [
                    'profesional_id' => $profesional->id,
                    'insti_comunica' => $datosDificultad['institucion'] ?? null,
                    'medio_comunica' => $datosDificultad['medio'] ?? null,
                ]
            );

            // 3. INICIO LABORES (NUEVO BLOQUE)
            $datosInicio = $data['inicio_labores'] ?? [];
            ComDocuAsisten::updateOrCreate(
                ['acta_id' => $id, 'modulo_id' => self::MODULO_ID],
                [
                    'profesional_id'    => $profesional->id,
                    // Mapeo JS -> BD
                    'cant_consultorios' => $datosInicio['consultorios'] ?? null,
                    'fua'               => $datosInicio['fua'] ?? null,
                    'referencia'        => $datosInicio['referencia'] ?? null,
                    'receta'            => $datosInicio['receta'] ?? null,
                    'orden_laboratorio' => $datosInicio['orden_lab'] ?? null,
                    // 'comentarios'    => $datosInicio['comentarios'] ?? null, // Si agregas campo de texto
                ]
            );


            // 4. SECCIÓN DNI (NUEVO BLOQUE)
            $datosDni = $data['seccion_dni'] ?? [];     
            // Lógica: Si es DNI AZUL, limpiamos versión y firma
            $esElectronico = ($datosDni['tipo_dni'] ?? '') === 'DNI_ELECTRONICO';
            
            ComDni::updateOrCreate(
                ['acta_id' => $id, 'modulo_id' => self::MODULO_ID],
                [
                    'profesional_id' => $profesional->id,
                    'tip_dni'        => $datosDni['tipo_dni'] ?? null,
                    // Si no es electrónico, guardamos null en versión y firma
                    'version_dni'    => $esElectronico ? ($datosDni['version_dnie'] ?? null) : null,
                    'firma_sihce'    => $esElectronico ? ($datosDni['firma_sihce'] ?? null) : null,
                    'comentarios'    => $datosDni['comentarios'] ?? null,
                ]
            );

            // ----------------------------------------------------
            // 5. FOTOS (NUEVO BLOQUE)
            // ----------------------------------------------------
            if ($request->hasFile('fotos')) {
                foreach ($request->file('fotos') as $foto) {
                    // Guardar en carpeta: storage/app/public/evidencia_fotos
                    // Asegúrate de correr: php artisan storage:link
                    $path = $foto->store('evidencia_fotos', 'public');

                    ComFotos::create([
                        'acta_id'        => $id,
                        'modulo_id'      => 'PSICOLOGIA',
                        'profesional_id' => $profesional->id,
                        'url_foto'       => $path
                    ]);
                }
            }

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
