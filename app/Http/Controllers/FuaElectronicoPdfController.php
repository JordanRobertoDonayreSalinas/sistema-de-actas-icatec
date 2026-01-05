<?php

namespace App\Http\Controllers;

use App\Models\CabeceraMonitoreo;
use App\Models\EquipoComputo;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class FuaElectronicoPdfController extends Controller
{
    public function generar($id)
    {
        $acta = CabeceraMonitoreo::with(['establecimiento', 'user'])->findOrFail($id);
        $modulo = 'fua_electronico';

        $detalle = \App\Models\MonitoreoModulos::where('cabecera_monitoreo_id', $id)
                    ->where('modulo_nombre', $modulo)
                    ->first();

        $imagenesData = [];
        if ($detalle && isset($detalle->contenido['foto_evidencia'])) {
            $fotos = $detalle->contenido['foto_evidencia'];
            $fotosArray = is_array($fotos) ? $fotos : [$fotos];
            
            foreach ($fotosArray as $foto) {
                if (!empty($foto)) {
                    $rutaCompleta = public_path('storage/' . $foto);
                    if (file_exists($rutaCompleta)) {
                        $imagenesData[] = $rutaCompleta;
                    }
                }
            }
        }

        $pdf = Pdf::loadView('usuario.monitoreo.pdf.fua_electronico_pdf', compact('acta', 'detalle', 'imagenesData'))
                  ->setPaper('a4', 'portrait');
        //return $pdf->stream("MONITOREO_FUA_ELECTRONICO_{$acta->id}.pdf");
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
        return $pdf->stream("Modulo14_FUA_ELECTRONICO_Acta_{$acta->id}.pdf");
    }
}