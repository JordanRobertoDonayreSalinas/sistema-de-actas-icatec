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
        //return $pdf->setPaper('a4', 'portrait')->stream("Modulo06_Consulta_Nutricion_Acta_{$id}.pdf");
        
        // Configuramos el papel
        $pdf->setPaper('a4', 'portrait');

        // -----------------------------------------------------------
        // INYECCIÓN DEL PIE DE PÁGINA
        // -----------------------------------------------------------
        
        // 1. Renderizamos el HTML en memoria (Vital para saber el total de páginas)
        $pdf->render();

        // 2. Obtenemos el objeto Canvas para "dibujar" sobre el PDF ya generado
        $dompdf = $pdf->getDomPDF();
        $canvas = $dompdf->get_canvas();
        
        // 3. Variables de diseño
        $h = $canvas->get_height();
        $w = $canvas->get_width();
        
        // Fuente y Color (Gris Slate-400 aprox)
        $fontMetrics = $dompdf->getFontMetrics();
        $font = $fontMetrics->get_font("Helvetica", "normal");
        $size = 8;
        $color = array(0.58, 0.64, 0.72); 

        // 4. Escribir Paginación (Izquierda) -> "Página 1 / 2"
        // {PAGE_NUM} y {PAGE_COUNT} son variables mágicas que DomPDF reemplaza
        $canvas->page_text(42, $h - 40, "Página {PAGE_NUM} / {PAGE_COUNT}", $font, $size, $color);

        // 5. Escribir Fecha del Sistema (Derecha)
        $fechaBd = \Carbon\Carbon::parse($acta->fecha)->format('d/m/Y');
        //$textRight = "Generado por Sistema de Monitoreo | Fecha: " . date('d/m/Y H:i:s');
        $textRight = "Generado por Sistema de Monitoreo | Fecha: " . $fechaBd;

        // Calculamos el ancho del texto para alinearlo a la derecha perfectamente
        $textWidth = $fontMetrics->getTextWidth($textRight, $font, $size);
        $canvas->page_text($w - 42 - $textWidth, $h - 40, $textRight, $font, $size, $color);
        
        // 6. Dibujar línea divisoria superior
        $canvas->page_script('
            $pdf->line(42, $pdf->get_height() - 50, $pdf->get_width() - 42, $pdf->get_height() - 50, array(0.88, 0.91, 0.94), 1);
        ');
        // -----------------------------------------------------------
        return $pdf->stream("Modulo06_Consulta_Nutricion_Acta_{$id}.pdf");

    }
}
