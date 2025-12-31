<?php

namespace App\Http\Controllers;

use App\Models\CabeceraMonitoreo;
use App\Models\MonitoreoModulos;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;

class ConsultaNutricionPdfController extends Controller
{
    public function generar($id)
    {
        // 1. Obtener datos
        $acta = CabeceraMonitoreo::with('establecimiento')->findOrFail($id);

        $detalle = MonitoreoModulos::where('cabecera_monitoreo_id', $id)
                    ->where('modulo_nombre', 'consulta_nutricion')
                    ->firstOrFail();

        // 2. Procesar imágenes (Comprimir y convertir a Base64)
        $imagenesData = [];
        $fotos = $detalle->contenido['foto_evidencia'] ?? null;

        if ($fotos) {
            // Aseguramos que sea un array
            $paths = is_array($fotos) ? $fotos : [$fotos];

            foreach ($paths as $p) {
                // Verificamos existencia
                if ($p && Storage::disk('public')->exists($p)) {
                    //try {
                        // Obtenemos la ruta real del sistema para Intervention Image
                        $realPath = storage_path('app/public/' . $p);

                        // A. Redimensionar (Máximo 600px de ancho, alto automático)
                        // B. Codificar a JPG con 60% de calidad
                        $img = Image::make($realPath)
                            ->resize(600, null, function ($constraint) {
                                $constraint->aspectRatio();
                                $constraint->upsize();
                            })
                            ->encode('jpg', 60);

                        // C. Guardar en el array como Base64 listo para HTML
                        $imagenesData[] = 'data:image/jpeg;base64,' . base64_encode($img);

                        // Límite de seguridad: solo procesar las primeras 5 para no reventar memoria
                        if (count($imagenesData) >= 5) break;

                    //} catch (\Exception $e) {
                        // Si una imagen falla al comprimirse, la ignoramos y seguimos
                      //  continue;
                    //}
                }
            }
        }

        // 3. Generar PDF
        // Pasamos $imagenesData que contiene las cadenas Base64 optimizadas
        $usuarioLogeado = Auth::user();
        $pdf = Pdf::loadView('usuario.monitoreo.pdf.consulta_nutricion', compact('acta', 'detalle', 'imagenesData', 'usuarioLogeado'));

        return $pdf->setPaper('a4', 'portrait')->stream("Modulo06_Consulta_Nutricion_Acta_{$id}.pdf");
    }
}
