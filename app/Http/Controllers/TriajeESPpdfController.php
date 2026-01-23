<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CabeceraMonitoreo;
use App\Models\MonitoreoModulos;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class TriajeESPpdfController extends Controller
{
    /**
     * Genera el PDF del módulo "Triaje" (CSMC).
     */
    public function generar($id)
    {
        // 1. Obtener datos de la cabecera (Establecimiento, Equipo, Usuario)
        $monitoreo = CabeceraMonitoreo::with(['establecimiento', 'equipo', 'user'])->findOrFail($id);

        // 2. Obtener los datos guardados del módulo específico ('triaje_esp')
        $modulo = MonitoreoModulos::where('cabecera_monitoreo_id', $id)
                                  ->where('modulo_nombre', 'triaje_esp') // Clave correcta para CSMC
                                  ->first();

        // 3. Decodificar JSON (si existe)
        $data = $modulo ? json_decode($modulo->contenido, true) : [];

        // 4. Definir nombre del archivo
        $numero = $monitoreo->numero_acta ?? $monitoreo->id;
        $numeroActa = str_pad($numero, 5, '0', STR_PAD_LEFT);
        
        $fileName = "REPORTE_TRIAJE_CSMC_ACTA_{$numeroActa}.pdf";

        // 5. Cargar la vista del PDF
        // Asegúrate de crear este archivo en resources/views/usuario/monitoreo/pdf_especializados/triaje.blade.php
        $pdf = Pdf::loadView('usuario.monitoreo.pdf_especializados.triaje', compact('monitoreo', 'data', 'modulo'));

        // 6. Configurar hoja y renderizar
        $pdf->setPaper('a4', 'portrait');

        return $pdf->stream($fileName);
    }
}