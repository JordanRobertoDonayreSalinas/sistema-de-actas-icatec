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

use App\Models\MonitoreoModulos;

class TriajeController extends Controller
{
    // 1. MÉTODO INDEX: Carga el formulario Y los datos guardados previamente
    public function index($id){
        $acta = Acta::with('establecimiento')->findOrFail($id);
        
        $dbCapacitacion = ComCapacitacion::with('profesional')
                    ->where('acta_id', $id)->where('modulo_id', 'TRIAJE')->first();

        $dbInventario = ComEquipamiento::where('acta_id', $id)
                            ->where('modulo_id', 'TRIAJE')->get();
        
        $dbDificultad = ComDificultad::where('acta_id', $id)
                            ->where('modulo_id', 'TRIAJE')->first();

        // 4. CARGAR FOTOS (NUEVO)
        $dbFotos = ComFotos::where('acta_id', $id)
                        ->where('modulo_id', 'TRIAJE')->get();

        return view('usuario.monitoreo.modulos.triaje', compact('acta', 'dbCapacitacion', 'dbInventario', 'dbDificultad', 'dbFotos'));
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
                ['acta_id' => $id, 'modulo_id' => 'TRIAJE'],
                [
                    'profesional_id'  => $profesional->id,
                    'recibieron_cap'  => $datosCapacitacion['recibieron_cap'],
                    'institucion_cap' => ($datosCapacitacion['recibieron_cap'] === 'SI') ? $datosCapacitacion['institucion_cap'] : null
                ]
            );


            // 3. INVENTARIO
            ComEquipamiento::where('acta_id', $id)->where('modulo_id', 'TRIAJE')->delete();
            $listaInventario = $data['inventario'] ?? [];
            $comentarioGeneral = $data['inventario_comentarios'] ?? '';

            if (!empty($listaInventario)) {
                foreach ($listaInventario as $item) {
                    
                    // YA NO CONCATENAMOS. Guardamos directo.
                    
                    ComEquipamiento::create([
                        'acta_id'        => $id,
                        'modulo_id'      => 'TRIAJE',
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
                ['acta_id' => $id, 'modulo_id' => 'TRIAJE'],
                [
                    'profesional_id' => $profesional->id,
                    'insti_comunica' => $datosDificultad['institucion'] ?? null,
                    'medio_comunica' => $datosDificultad['medio'] ?? null,
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
                        'modulo_id'      => 'TRIAJE',
                        'profesional_id' => $profesional->id,
                        'url_foto'       => $path
                    ]);
                }
            }



            // Parar actualizar el estado en la tabla
            MonitoreoModulos::updateOrCreate(
                [
                    'cabecera_monitoreo_id' => $id, // Relación con el ID del acta
                    'modulo_nombre'         => 'triaje'   // Identificador de este formulario
                ],
                [
                    'contenido' => 'FINALIZADO' // Texto fijo que solicitaste
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
