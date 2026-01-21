<?php

namespace App\Http\Controllers;

use App\Models\CabeceraMonitoreo;
use App\Models\MonitoreoModulos;
use App\Models\EquipoComputo;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class UrgenciasPdfController extends Controller
{
    /**
     * Nombre del módulo para filtrar en las tablas detalle y equipos.
     */
    private $modulo = 'urgencias';

    /**
     * Genera el reporte PDF del Módulo de Urgencias y Emergencias con Pie de Página Inyectado.
     *
     * @param int $id ID de la Cabecera de Monitoreo (Acta)
     * @return \Illuminate\Http\Response
     */
    public function generar($id)
    {
        // 1. Cargar el acta con la información del establecimiento
        $acta = CabeceraMonitoreo::with('establecimiento')->findOrFail($id);

        // 2. Cargar el detalle guardado en JSON para el Módulo de Urgencias
        $detalle = MonitoreoModulos::where('cabecera_monitoreo_id', $id)
                                   ->where('modulo_nombre', $this->modulo)
                                   ->firstOrFail();

        // 3. Cargar el inventario de equipos vinculado a este acta y módulo
        $equipos = EquipoComputo::where('cabecera_monitoreo_id', $id)
                                ->where('modulo', $this->modulo)
                                ->get();

        // 4. Obtener datos del Monitor Responsable
        $user = Auth::user();
        $monitor = [
            'nombre'    => mb_strtoupper("{$user->apellido_paterno} {$user->apellido_materno}, {$user->name}", 'UTF-8'),
            'tipo_doc'  => $user->tipo_documento ?? 'DNI',
            'documento' => $user->documento ?? $user->username ?? '________'
        ];

        // 5. Preparar datos y Cargar Vista (SIN renderizar aún)
        $data = compact('acta', 'detalle', 'equipos', 'monitor');
        $pdf = Pdf::loadView('usuario.monitoreo.pdf.urgencias_pdf', $data);

        // 6. Configurar Papel
        $pdf->setPaper('a4', 'portrait');

        // =========================================================================
        //  LÓGICA DE DIBUJO DIRECTO DEL PIE DE PÁGINA (CANVAS INJECTION)
        // =========================================================================
        
        // A. Renderizar primero para calcular el total de páginas real
        $pdf->render();

        // B. Obtener el lienzo (Canvas)
        $dompdf = $pdf->getDomPDF();
        $canvas = $dompdf->get_canvas();

        // C. Variables de diseño
        $h = $canvas->get_height();
        $w = $canvas->get_width();
        
        // Configuración de Fuente y Color (Gris Pizarra #64748b)
        $fontMetrics = $dompdf->getFontMetrics();
        $font = $fontMetrics->get_font("Helvetica", "normal"); 
        $size = 8;
        $color = array(0.39, 0.45, 0.54); 

        // D. DIBUJAR TEXTO IZQUIERDO: "SISTEMA DE ACTAS"
        // Coordenada X = 42 (Margen izquierdo aprox)
        $textLeft = "SISTEMA DE ACTAS"; 
        $canvas->page_text(42, $h - 40, $textLeft, $font, $size, $color);

        // E. DIBUJAR TEXTO DERECHO: "PAG. X / Y"
        $textPag = "PAG. {PAGE_NUM} / {PAGE_COUNT}";
        
        // Calculamos ancho para alinearlo a la derecha (Margen derecho 42px)
        $widthPag = $fontMetrics->getTextWidth("PAG. 00 / 00", $font, $size); 
        $canvas->page_text($w - 42 - $widthPag, $h - 40, $textPag, $font, $size, $color);

        // F. DIBUJAR LÍNEA DIVISORIA SUPERIOR (Gris claro)
        $canvas->page_script('
            $pdf->line(42, $pdf->get_height() - 50, $pdf->get_width() - 42, $pdf->get_height() - 50, array(0.88, 0.91, 0.94), 1);
        ');

        // =========================================================================

        // 7. Retornar PDF
        return $pdf->stream("Reporte_Urgencias_Acta_" . str_pad($id, 5, '0', STR_PAD_LEFT) . ".pdf");
    }
}