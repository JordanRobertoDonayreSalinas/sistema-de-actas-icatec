<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CabeceraMonitoreo;
use App\Models\MonitoreoModulos;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class TerapiaESPpdfController extends Controller
{
    /**
     * Genera el PDF del módulo "Terapia" (CSMC).
     */
    public function generar($id)
    {
        // 1. Obtener datos de la cabecera (Establecimiento, Equipo, Usuario)
        $monitoreo = CabeceraMonitoreo::with(['establecimiento', 'equipo', 'user'])->findOrFail($id);

        // 2. Obtener los datos guardados del módulo específico ('terapia_esp')
        $modulo = MonitoreoModulos::where('cabecera_monitoreo_id', $id)
                                  ->where('modulo_nombre', 'terapia')
                                  ->first();

        // 3. Decodificar JSON (si existe)
        $data = $modulo ? json_decode($modulo->contenido, true) : [];

        // 4. Definir nombre del archivo
        $numero = $monitoreo->numero_acta ?? $monitoreo->id;
        $numeroActa = str_pad($numero, 5, '0', STR_PAD_LEFT);
        
        $fileName = "REPORTE_TERAPIA_CSMC_ACTA_{$numeroActa}.pdf";

        // 5. Cargar la vista del PDF
        $pdf = Pdf::loadView('usuario.monitoreo.pdf_especializados.terapia_pdf', compact('monitoreo', 'data', 'modulo'));

        // 6. Configurar hoja y renderizar
        $pdf->setPaper('a4', 'portrait');

        return $pdf->stream($fileName);
    }
}
