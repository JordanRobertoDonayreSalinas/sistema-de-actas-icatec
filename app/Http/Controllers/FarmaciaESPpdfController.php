<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CabeceraMonitoreo;
use App\Models\MonitoreoModulos;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // <--- AGREGA ESTO
use Intervention\Image\Facades\Image; // <--- 1. IMPORTANTE: Agregar esta librería
use Illuminate\Support\Facades\Storage; // <--- 2. IMPORTANTE: Agregar esta librería

class FarmaciaESPpdfController extends Controller
{
    /**
     * Genera el PDF del módulo "Admisión y Citas" (CSMC).
     */
    public function generar($id)
    {
        // 1. Obtener datos de la cabecera (Establecimiento, Equipo, Usuario)
        $monitoreo = CabeceraMonitoreo::with(['establecimiento', 'equipo', 'user'])->findOrFail($id);

        // 2. Obtener los datos guardados del módulo específico ('farmacia_esp')
        $modulo = MonitoreoModulos::where('cabecera_monitoreo_id', $id)
                                  ->where('modulo_nombre', 'farmacia_esp') // Clave correcta
                                  ->firstOrFail();
        
        // 2. Procesar imágenes (Comprimir y convertir a Base64)
        $imagenesData = [];
        $fotos = $modulo->contenido['comentarios_y_evidencias']['foto_evidencia'] ?? [];
        if (is_string($fotos)) $fotos = [$fotos];

        foreach ($fotos as $path) {
            // Verificamos si el archivo existe en el disco 'public'
            if ($path && Storage::disk('public')->exists($path)) {
                
                // Obtenemos la ruta absoluta del archivo en el servidor
                $rutaAbsoluta = storage_path("app/public/{$path}");
                
                // Obtenemos el tipo de archivo (jpg, png, etc.)
                $extension = pathinfo($rutaAbsoluta, PATHINFO_EXTENSION);
                
                // Leemos el contenido del archivo y lo convertimos a Base64
                $data = file_get_contents($rutaAbsoluta);
                $base64 = 'data:image/' . $extension . ';base64,' . base64_encode($data);
                
                $imagenesData[] = $base64;
            }
        }

        // 3. Generar PDF
        // Pasamos $imagenesData que contiene las cadenas Base64 optimizadas
        $usuarioLogeado = Auth::user();
        $pdf = Pdf::loadView('usuario.monitoreo.pdf_especializados.farmacia_pdf', compact('monitoreo', 'modulo', 'imagenesData', 'usuarioLogeado'));
        //return $pdf->setPaper('a4', 'portrait')->stream("Modulo04_Consulta_Medicina_Acta_{$id}.pdf");
        
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

        // A. TEXTO DEL SISTEMA (IZQUIERDA)
        // Coordenada X = 42 (Margen izquierdo fijo)
        $textLeft = "SISTEMA DE ACTAS"; 
        $canvas->page_text(42, $h - 40, $textLeft, $font, $size, $color);

        // B. PAGINACIÓN (DERECHA)
        // Calculamos el ancho aproximado de "PAG. 00 / 00" para alinearlo bien a la derecha
        $textPag = "PAG. {PAGE_NUM} / {PAGE_COUNT}";
        $widthPag = $fontMetrics->getTextWidth("PAG. 00 / 00", $font, $size); 
        
        // Coordenada X = AnchoTotal - Margen(42) - AnchoTexto
        $canvas->page_text($w - 42 - $widthPag, $h - 40, $textPag, $font, $size, $color);

        // -----------------------------------------------------------
        
        // 6. Dibujar línea divisoria superior
        $canvas->page_script('
            $pdf->line(42, $pdf->get_height() - 50, $pdf->get_width() - 42, $pdf->get_height() - 50, array(0.88, 0.91, 0.94), 1);
        ');
        // -----------------------------------------------------------
       
        return $pdf->stream("06_Farmacia_ESP_Acta_{$monitoreo->numero_acta}.pdf");
    }
}