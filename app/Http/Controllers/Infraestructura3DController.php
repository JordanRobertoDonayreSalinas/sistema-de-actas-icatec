<?php

namespace App\Http\Controllers;

use App\Models\CabeceraMonitoreo;
use App\Models\MonitoreoModulos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Infraestructura3DController extends Controller
{
    public function index($id)
    {
        $acta = CabeceraMonitoreo::findOrFail($id);
        
        $modulo = MonitoreoModulos::where('cabecera_monitoreo_id', $id)
            ->where('modulo_nombre', 'infraestructura_3d')
            ->first();

        $contenido = $modulo ? $modulo->contenido : [
            'consultorios' => []
        ];

        return view('usuario.monitoreo.modulos.infraestructura_3d', compact('acta', 'contenido'));
    }

    public function store(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $moduloNombre = 'infraestructura_3d';
            $contenido = $request->contenido;

            // Procesar la imagen del croquis si se envía
            if ($request->has('croquis_image')) {
                $imageData = $request->croquis_image;
                $imageData = str_replace('data:image/png;base64,', '', $imageData);
                $imageData = str_replace(' ', '+', $imageData);
                $imageName = 'croquis_acta_' . $id . '.png';
                $path = 'croquis/' . $imageName;
                
                // Guardar en storage/app/public/croquis/
                \Illuminate\Support\Facades\Storage::disk('public')->put($path, base64_decode($imageData));
                
                // Guardar la ruta en el JSON para los reportes
                $contenido['imagen_path'] = $path;
            }
            
            // Buscamos si ya existe el registro
            $modulo = MonitoreoModulos::where('cabecera_monitoreo_id', $id)
                                      ->where('modulo_nombre', $moduloNombre)
                                      ->first();

            if ($modulo) {
                // Si existe, actualizamos
                $modulo->update([
                    'contenido' => $contenido
                ]);
            } else {
                // Si no existe, insertamos manualmente
                $nextId = (MonitoreoModulos::max('id') ?? 0) + 1;
                
                MonitoreoModulos::create([
                    'id' => $nextId,
                    'cabecera_monitoreo_id' => $id,
                    'modulo_nombre' => $moduloNombre,
                    'contenido' => $contenido
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Croquis 3D guardado correctamente.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al guardar: ' . $e->getMessage()
            ], 500);
        }
    }
}
