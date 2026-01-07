<?php

namespace App\Http\Controllers;

use App\Models\CabeceraMonitoreo;
use App\Models\EquipoComputo;
use App\Models\MonitoreoModulos; 
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ReferenciasPdfController extends Controller
{
    private $modulo = 'REFERENCIAS'; 

    public function generar($id)
    {
        // 1. Cargar la cabecera
        $acta = CabeceraMonitoreo::with(['establecimiento', 'user'])->findOrFail($id);

        // 2. Buscar el detalle en la tabla mon_monitoreo_modulos
        $detalle = MonitoreoModulos::where('cabecera_monitoreo_id', $id)
                    ->where('modulo_nombre', $this->modulo)
                    ->first();

        // 3. Buscar equipos
        $equipos = EquipoComputo::where('cabecera_monitoreo_id', $id)
                                ->where('modulo', $this->modulo)
                                ->get();

        // 4. Procesar datos del JSON
        $datos = [];
        if ($detalle) {
            // El modelo MonitoreoModulos ya tiene cast 'array' para 'contenido'
            $datos = $detalle->contenido ?? [];
            
            /** * PUNTO CRÍTICO PARA LAS FOTOS:
             * Si las fotos se guardaron dentro del JSON 'contenido', las extraemos 
             * para que el objeto $detalle las tenga disponibles como propiedades
             * y la validación !empty($detalle->foto_1) de la vista funcione.
             */
            if (!isset($detalle->foto_1) && isset($datos['foto_1'])) {
                $detalle->foto_1 = $datos['foto_1'];
            }
            if (!isset($detalle->foto_2) && isset($datos['foto_2'])) {
                $detalle->foto_2 = $datos['foto_2'];
            }
        }

        // 5. Generar el PDF
        $pdf = Pdf::loadView('usuario.monitoreo.pdf.referencias_pdf', compact('acta', 'detalle', 'equipos', 'datos'))
                  ->setPaper('a4', 'portrait');

        return $pdf->stream("MONITOREO_REFERENCIAS_ACTA_{$acta->id}.pdf");
    }
}