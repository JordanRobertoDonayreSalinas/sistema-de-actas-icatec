<?php

namespace App\Http\Controllers;

use App\Models\CabeceraMonitoreo;
use App\Models\MonitoreoModulos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Infraestructura2DController extends Controller
{
    public function index($id)
    {
        $acta = CabeceraMonitoreo::findOrFail($id);
        
        $modulo = MonitoreoModulos::where('cabecera_monitoreo_id', $id)
            ->where('modulo_nombre', 'infraestructura_2d')
            ->first();

        $contenido = $modulo ? $modulo->contenido : [
            'consultorios' => []
        ];

        return view('usuario.monitoreo.modulos.infraestructura_2d', compact('acta', 'contenido'));
    }

    public function store(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $moduloNombre = 'infraestructura_2d';
            $contenido = $request->contenido;

            // Asegurar que el directorio de croquis existe
            if (!\Illuminate\Support\Facades\Storage::disk('public')->exists('croquis')) {
                \Illuminate\Support\Facades\Storage::disk('public')->makeDirectory('croquis');
            }

            // Procesar imágenes de los pisos si se envían (Multi-piso)
            if ($request->has('croquis_images')) {
                $images = $request->croquis_images;
                $floorPaths = [];
                foreach ($images as $piso => $imageData) {
                    $imageData = preg_replace('/^data:image\/\w+;base64,/', '', $imageData);
                    $imageData = str_replace(' ', '+', $imageData);
                    $imageName = 'croquis_acta_' . $id . '_piso_' . $piso . '.png';
                    $path = 'croquis/' . $imageName;
                    
                    $success = \Illuminate\Support\Facades\Storage::disk('public')->put($path, base64_decode($imageData));
                    if ($success) {
                        $floorPaths[$piso] = $path;
                        \Illuminate\Support\Facades\Log::info("Croquis PISO $piso guardado en: $path");
                    } else {
                        \Illuminate\Support\Facades\Log::error("Fallo al guardar croquis PISO $piso en: $path");
                    }
                }
                $contenido['piso_images'] = $floorPaths;
            }

            // Procesar la imagen del croquis principal (para compatibilidad)
            if ($request->has('croquis_image')) {
                $imageData = $request->croquis_image;
                $imageData = preg_replace('/^data:image\/\w+;base64,/', '', $imageData);
                $imageData = str_replace(' ', '+', $imageData);
                $imageName = 'croquis_acta_' . $id . '.png';
                $path = 'croquis/' . $imageName;
                
                $success = \Illuminate\Support\Facades\Storage::disk('public')->put($path, base64_decode($imageData));
                if ($success) {
                    $contenido['imagen_path'] = $path;
                    \Illuminate\Support\Facades\Log::info("Croquis PRINCIPAL guardado en: $path");
                }
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
                'message' => 'Croquis 2D guardado correctamente.'
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
