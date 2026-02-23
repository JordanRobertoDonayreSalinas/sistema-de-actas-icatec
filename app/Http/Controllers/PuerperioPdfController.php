<?php

namespace App\Http\Controllers;

use App\Models\CabeceraMonitoreo;
use App\Models\MonitoreoModulos;
use App\Models\EquipoComputo;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class PuerperioPdfController extends Controller
{
    /**
     * Genera el PDF del Módulo de Puerperio con pie de página inyectado.
     */
    public function generar($id)
    {
        // 1. Cargar Datos
        $acta = CabeceraMonitoreo::with('establecimiento')->findOrFail($id);
        
        $detalle = MonitoreoModulos::where('cabecera_monitoreo_id', $id)
                                   ->where('modulo_nombre', 'puerperio')
                                   ->firstOrFail();
                                   
        $equipos = EquipoComputo::where('cabecera_monitoreo_id', $id)
                                ->where('modulo', 'puerperio')
                                ->get();

        $user = Auth::user();
        $monitor = [
            'nombre'    => mb_strtoupper("{$user->apellido_paterno} {$user->apellido_materno}, {$user->name}", 'UTF-8'),
            'tipo_doc'  => $user->tipo_documento ?? 'DNI',
            'documento' => $user->documento ?? $user->username ?? '________'
        ];

        // 2. Cargar Vista (Sin renderizar aún)
        $pdf = Pdf::loadView('usuario.monitoreo.pdf.puerperio_pdf', compact('acta', 'detalle', 'equipos', 'monitor'));
        
        // 3. Configuración de Papel
        $pdf->setPaper('a4', 'portrait');

        // =========================================================================
        //  LÓGICA DE PIE DE PÁGINA (CANVAS INJECTION)
        // =========================================================================
        
        // A. Renderizar HTML para calcular el total de páginas
        $pdf->render();

        // B. Obtener el Lienzo (Canvas)
        $dompdf = $pdf->getDomPDF();
        $canvas = $dompdf->get_canvas();

        // C. Configuración Visual
        $h = $canvas->get_height();
        $w = $canvas->get_width();
        
        // Fuente Helvética Bold, Tamaño 8, Color Gris Pizarra (#64748b)
        $fontMetrics = $dompdf->getFontMetrics();
        $font = $fontMetrics->get_font("Helvetica", "normal"); 
        $size = 8;
        $color = array(0.39, 0.45, 0.54); 

        // D. TEXTO IZQUIERDA: "SISTEMA DE ACTAS"
        $canvas->page_text(42, $h - 40, "SISTEMA DE ACTAS", $font, $size, $color);

        // E. TEXTO DERECHA: "PAG. X / Y"
        $textPag = "PAG. {PAGE_NUM} / {PAGE_COUNT}";
        // Calcular ancho para alinear a la derecha (Margen derecho 42px)
        $widthPag = $fontMetrics->getTextWidth("PAG. 00 / 00", $font, $size); 
        $canvas->page_text($w - 42 - $widthPag, $h - 40, $textPag, $font, $size, $color);

        // F. LÍNEA SUPERIOR DEL PIE DE PÁGINA
        $canvas->page_script('
            $pdf->line(42, $pdf->get_height() - 50, $pdf->get_width() - 42, $pdf->get_height() - 50, array(0.88, 0.91, 0.94), 1);
        ');

        // =========================================================================

        return $pdf->stream('13_Puerperio_Acta_NOESP_' . $acta->numero_acta . '.pdf');
    }
}