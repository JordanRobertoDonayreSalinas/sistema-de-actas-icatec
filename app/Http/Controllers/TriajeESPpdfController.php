<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CabeceraMonitoreo;
use App\Models\MonitoreoModulos;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class TriajeESPpdfController extends Controller
{
    /**
     * Genera el PDF del módulo "Triaje" (CSMC) con pie de página inyectado.
     */
    public function generar($id)
    {
        // 1. Cargar Datos de la Cabecera (Usamos $monitoreo para coincidir con la vista)
        $monitoreo = CabeceraMonitoreo::with(['establecimiento', 'equipo', 'user'])->findOrFail($id);

        // 2. Obtener los datos guardados del módulo específico ('triaje_esp')
        $modulo = MonitoreoModulos::where('cabecera_monitoreo_id', $id)
                                  ->where('modulo_nombre', 'triaje_esp')
                                  ->first();

        // 3. Decodificar JSON
        $data = $modulo ? json_decode($modulo->contenido, true) : [];

        // 4. Preparar datos del Monitor (Usuario logueado o del acta)
        $user = Auth::user() ?? $monitoreo->user; 
        $monitor = [
            'nombre'    => mb_strtoupper("{$user->apellido_paterno} {$user->apellido_materno}, {$user->name}", 'UTF-8'),
            'tipo_doc'  => $user->tipo_documento ?? 'DNI',
            'documento' => $user->documento ?? $user->username ?? '________'
        ];

        // 5. Configurar Nombre del Archivo
        $prefijo = $monitoreo->tipo_origen === 'ESPECIALIZADA' ? 'CSMC' : 'IPRESS';
        $numero = str_pad($monitoreo->numero_acta ?? $monitoreo->id, 5, '0', STR_PAD_LEFT);
        $fileName = "REPORTE_TRIAJE_{$prefijo}_ACTA_{$numero}.pdf";

        // 6. Cargar Vista
        // CORRECCIÓN: Enviamos 'monitoreo' en lugar de 'acta'
        $pdf = Pdf::loadView('usuario.monitoreo.pdf_especializados.triaje_pdf', compact('monitoreo', 'data', 'monitor', 'modulo'));

        // 7. Configuración de Papel
        $pdf->setPaper('a4', 'portrait');

        // =========================================================================
        //  LÓGICA DE PIE DE PÁGINA (CANVAS INJECTION)
        // =========================================================================
        
        $pdf->render();
        $dompdf = $pdf->getDomPDF();
        $canvas = $dompdf->get_canvas();

        $h = $canvas->get_height();
        $w = $canvas->get_width();
        
        $fontMetrics = $dompdf->getFontMetrics();
        $font = $fontMetrics->get_font("Helvetica", "normal"); 
        $size = 8;
        $color = array(0.39, 0.45, 0.54); 

        // Texto Izquierda
        $canvas->page_text(42, $h - 40, "SISTEMA DE ACTAS", $font, $size, $color);

        // Texto Derecha (Paginación)
        $textPag = "PAG. {PAGE_NUM} / {PAGE_COUNT}";
        $widthPag = $fontMetrics->getTextWidth("PAG. 00 / 00", $font, $size); 
        $canvas->page_text($w - 42 - $widthPag, $h - 40, $textPag, $font, $size, $color);

        // Línea divisoria
        $canvas->page_script('
            $pdf->line(42, $pdf->get_height() - 50, $pdf->get_width() - 42, $pdf->get_height() - 50, array(0.88, 0.91, 0.94), 1);
        ');

        return $pdf->stream($fileName);
    }
}