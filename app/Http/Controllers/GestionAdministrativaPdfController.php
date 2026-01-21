<?php

namespace App\Http\Controllers;

use App\Models\CabeceraMonitoreo;
use App\Models\MonitoreoModulos;
use App\Models\EquipoComputo; 
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class GestionAdministrativaPdfController extends Controller
{
    /**
     * Genera el reporte PDF del Módulo 01 asegurando los datos del monitor y contenido técnico.
     */
    public function generar($id)
    {
        // 1. Cargar el acta optimizando la relación del establecimiento
        $acta = CabeceraMonitoreo::with('establecimiento:id,nombre,codigo')->findOrFail($id);

        // 2. Cargar el detalle guardado para el Módulo 01
        $detalle = MonitoreoModulos::where('cabecera_monitoreo_id', $id)
                                   ->where('modulo_nombre', 'gestion_administrativa')
                                   ->firstOrFail();

        // 3. Cargar el inventario de equipos de este acta y módulo específico
        $equipos = EquipoComputo::where('cabecera_monitoreo_id', $id)
                                ->where('modulo', 'gestion_administrativa')
                                ->get();

        // 4. CAPTURA DEL MONITOR (Usuario autenticado que genera el reporte)
        $user = Auth::user();

        $monitor = [
            'nombre'    => mb_strtoupper("{$user->apellido_paterno} {$user->apellido_materno}, {$user->name}", 'UTF-8'),
            'tipo_doc'  => $user->tipo_documento ?? 'DNI',
            'documento' => $user->documento ?? $user->username ?? '________'
        ];

        // 5. Cargar la vista técnica del PDF (SIN RENDERIZAR AÚN)
        $pdf = Pdf::loadView('usuario.monitoreo.pdf.gestion_administrativa_pdf', compact(
            'acta', 
            'detalle', 
            'equipos', 
            'monitor'
        ));

        // 6. Configuración del PDF y Papel
        $pdf->setPaper('a4', 'portrait');

        // =========================================================================
        //  LÓGICA DE DIBUJO DIRECTO DEL PIE DE PÁGINA (CANVAS INJECTION)
        // =========================================================================
        
        // A. Renderizar primero para calcular el total de páginas
        $pdf->render();

        // B. Obtener el lienzo (Canvas)
        $dompdf = $pdf->getDomPDF();
        $canvas = $dompdf->get_canvas();

        // C. Variables de diseño
        $h = $canvas->get_height();
        $w = $canvas->get_width();
        
        // Configuración de Fuente y Color (Gris Pizarra)
        $fontMetrics = $dompdf->getFontMetrics();
        $font = $fontMetrics->get_font("Helvetica", "normal"); 
        $size = 8;
        $color = array(0.39, 0.45, 0.54); // #64748b (Slate-500)

        // D. DIBUJAR TEXTO IZQUIERDO: "SISTEMA DE ACTAS"
        // Coordenada X = 42 (Margen izquierdo aprox)
        $textLeft = "SISTEMA DE ACTAS"; 
        $canvas->page_text(42, $h - 40, $textLeft, $font, $size, $color);

        // E. DIBUJAR TEXTO DERECHO: "PAG. X / Y"
        $textPag = "PAG. {PAGE_NUM} / {PAGE_COUNT}";
        
        // Calculamos ancho para alinearlo a la derecha
        $widthPag = $fontMetrics->getTextWidth("PAG. 00 / 00", $font, $size); 
        $canvas->page_text($w - 42 - $widthPag, $h - 40, $textPag, $font, $size, $color);

        // F. DIBUJAR LÍNEA DIVISORIA SUPERIOR (Gris claro)
        $canvas->page_script('
            $pdf->line(42, $pdf->get_height() - 50, $pdf->get_width() - 42, $pdf->get_height() - 50, array(0.88, 0.91, 0.94), 1);
        ');

        // =========================================================================

        return $pdf->stream("Acta_M01_ID" . str_pad($id, 5, '0', STR_PAD_LEFT) . ".pdf");
    }
}