<?php

namespace App\Http\Controllers;

use App\Models\CabeceraMonitoreo;
use App\Models\MonitoreoModulos;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ConsultaMedicinaPdfController extends Controller
{
    public function generar($id)
    {
        $acta = CabeceraMonitoreo::with('establecimiento')->findOrFail($id);

        $detalle = MonitoreoModulos::where('cabecera_monitoreo_id', $id)
                    ->where('modulo_nombre', 'consulta_medicina')
                    ->firstOrFail();

        $pdf = Pdf::loadView('usuario.monitoreo.pdf.consulta_medicina', compact('acta', 'detalle'));

        return $pdf->setPaper('a4', 'portrait')->stream("Modulo04_Consulta_Medicina_Acta_{$id}.pdf");
    }
}
