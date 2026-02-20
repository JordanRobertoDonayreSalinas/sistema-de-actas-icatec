<?php

namespace App\Http\Controllers;

use App\Models\CabeceraMonitoreo;
use App\Models\MonitoreoModulos;
use App\Models\EquipoComputo;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf; // Facade de DOMPDF
use Illuminate\Support\Facades\Auth;

class LaboratorioPdfController extends Controller
{
    private $modulo = 'laboratorio';

    public function generar($id)
    {
        // 1. Obtener Datos
        $acta = CabeceraMonitoreo::with('establecimiento')->findOrFail($id);

        $detalle = MonitoreoModulos::where('cabecera_monitoreo_id', $id)
                                   ->where('modulo_nombre', $this->modulo)
                                   ->firstOrFail();

        $equipos = EquipoComputo::where('cabecera_monitoreo_id', $id)
                                ->where('modulo', $this->modulo)
                                ->get();

        $user = Auth::user();
        $monitor = [
            'nombre'    => mb_strtoupper("{$user->apellido_paterno} {$user->apellido_materno}, {$user->name}", 'UTF-8'),
            'tipo_doc'  => $user->tipo_documento ?? 'DNI',
            'documento' => $user->documento ?? $user->username ?? '________'
        ];

        // 2. Cargar Vista (SIN renderizar aún)
        $data = compact('acta', 'detalle', 'equipos', 'monitor');
        $pdf = Pdf::loadView('usuario.monitoreo.pdf.laboratorio_pdf', $data);
        
        // 3. Configurar Papel
        $pdf->setPaper('a4', 'portrait');

        // ==========================================================
        //       LÓGICA DEL PIE DE PÁGINA (ESTILO MEDICINA)
        // ==========================================================

        // A. Renderizar primero para calcular el total de páginas
        $pdf->render();

        // B. Obtener el lienzo (Canvas) para dibujar
        $dompdf = $pdf->getDomPDF();
        $canvas = $dompdf->get_canvas();

        // C. Variables de diseño y dimensiones
        $h = $canvas->get_height();
        $w = $canvas->get_width();
        
        $fontMetrics = $dompdf->getFontMetrics();
        $font = $fontMetrics->get_font("Helvetica", "normal");
        $size = 8;
        $color = array(0.58, 0.64, 0.72); // Gris similar a Slate-400

        // D. TEXTO IZQUIERDA: "SISTEMA DE ACTAS"
        // Margen izquierdo: 42px
        $textLeft = "SISTEMA DE ACTAS"; 
        $canvas->page_text(42, $h - 40, $textLeft, $font, $size, $color);

        // E. TEXTO DERECHA: "PAG. X / Y"
        $textPag = "PAG. {PAGE_NUM} / {PAGE_COUNT}";
        
        // Calculamos ancho para alinear a la derecha respetando margen 42px
        $widthPag = $fontMetrics->getTextWidth("PAG. 00 / 00", $font, $size); 
        $canvas->page_text($w - 42 - $widthPag, $h - 40, $textPag, $font, $size, $color);

        // F. LÍNEA DIVISORIA
        // Dibuja una línea de color gris claro encima del texto
        $canvas->page_script('
            $pdf->line(42, $pdf->get_height() - 50, $pdf->get_width() - 42, $pdf->get_height() - 50, array(0.88, 0.91, 0.94), 1);
        ');

        // ==========================================================

        // 4. Retornar PDF
        return $pdf->stream("17_Laboratorio_Acta_NOESP_{$acta->numero_acta}.pdf");
    }
}