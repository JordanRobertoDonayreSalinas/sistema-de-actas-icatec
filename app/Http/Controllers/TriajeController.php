<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\Acta;
use App\Models\Profesional;

class TriajeController extends Controller
{
    public function index($id){
        $acta = Acta::with('establecimiento')->findOrFail($id);
        return view('usuario.monitoreo.modulos.triaje', compact('acta'));
    }

    public function store(Request $request, $id) {
        try {
            DB::beginTransaction();

            // 1. Decodificar el JSON que viene del frontend
            $data = json_decode($request->input('data'), true);
            
            // Validar que el JSON sea válido
            if (!$data) {
                throw new \Exception("Datos inválidos recibidos.");
            }

            // --- A. GUARDAR DATOS DEL PROFESIONAL ---
            // Asumiendo que tienes una tabla 'mon_profesionales' vinculada al monitoreo_id
            // $profesionalData = $data['profesional'];
            // MonProfesional::create([
            //     'monitoreo_id' => $id,
            //     'tipo_doc' => $profesionalData['tipo_doc'],
            //     'numero_documento' => $profesionalData['doc'],
            //     'nombres' => $profesionalData['nombres'],
            //     'apellido_paterno' => $profesionalData['apellido_paterno'],
            //     'apellido_materno' => $profesionalData['apellido_materno'],
            //     'telefono' => $profesionalData['telefono'],
            //     'email' => $profesionalData['email'],
            // ]);

            // --- B. GUARDAR RESPUESTAS (Capacitación, Dificultades, etc.) ---
            // Puedes guardar esto en una tabla de respuestas o campos JSON en el acta
            // Ejemplo:
            // $respuestas = [
            //    'capacitacion' => $data['capacitacion'],
            //    'dificultades' => $data['dificultades'],
            //    'seccion_dni'  => $data['seccion_dni']
            // ];
            // Si tienes una tabla mon_respuestas:
            // foreach($respuestas as $categoria => $contenido) {
            //      MonRespuestaEntrevistado::create([
            //          'monitoreo_id' => $id,
            //          'modulo' => 'TRIAJE',
            //          'pregunta' => $categoria, // O el ID de la pregunta
            //          'respuesta' => json_encode($contenido)
            //      ]);
            // }

            // --- C. GUARDAR INVENTARIO (Equipos) ---
            if (!empty($data['inventario'])) {
                foreach ($data['inventario'] as $equipo) {
                    // Usando el modelo que vi en tus migraciones 'mon_equipos_computo'
                    // MonEquipoComputo::create([
                    //     'monitoreo_id' => $id,
                    //     'area' => 'TRIAJE',
                    //     'descripcion' => $equipo['descripcion'],
                    //     'propiedad' => $equipo['propiedad'],
                    //     'estado' => $equipo['estado'],
                    //     'cod_patrimonial' => $equipo['codigo'],
                    //     'observacion' => $equipo['observacion']
                    // ]);
                }
            }

            // --- D. SUBIDA DE IMÁGENES (EVIDENCIAS) ---
            if ($request->hasFile('evidencias')) {
                foreach ($request->file('evidencias') as $file) {
                    $path = $file->store('evidencias/triaje/' . $id, 'public');
                    
                    // Guardar la ruta en la tabla correspondiente (ej. tabla evidencias o actualizar campos en acta)
                    // Evidencia::create(['monitoreo_id' => $id, 'ruta' => $path, 'modulo' => 'TRIAJE']);
                }
            }

            DB::commit();

            return response()->json(['message' => 'Datos de Triaje guardados correctamente.']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error en el servidor: ' . $e->getMessage()], 500);
        }
    }

    public function buscarProfesional($doc)
    {
        $profesional = Profesional::where('doc', $doc)->first();

        if ($profesional) {
            return response()->json([
                'found' => true,
                'data' => $profesional
            ]);
        } else {
            return response()->json(['found' => false], 404);
        }
    }
}
