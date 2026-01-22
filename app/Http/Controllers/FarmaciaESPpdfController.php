<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CabeceraMonitoreo;
use App\Models\MonitoreoModulos;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

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
                                  ->first();

        // 3. Decodificar JSON (si existe)
        $data = $modulo ? json_decode($modulo->contenido, true) : [];

        // 4. Definir nombre del archivo
        // Usamos numero_acta si existe, sino el ID (fallback)
        $numero = $monitoreo->numero_acta ?? $monitoreo->id;
        $numeroActa = str_pad($numero, 5, '0', STR_PAD_LEFT);
        
        $fileName = "REPORTE_FARMACIA_CSMC_ACTA_{$numeroActa}.pdf";

        // 5. Cargar la vista del PDF
        // CORRECCIÓN: Apuntando a la carpeta 'pdf_especializados' que creaste
        $pdf = Pdf::loadView('usuario.monitoreo.pdf_especializados.farmacia_pdf', compact('monitoreo', 'data', 'modulo'));

        // 6. Configurar hoja y renderizar
        $pdf->setPaper('a4', 'portrait');

        return $pdf->stream($fileName);
    }
}