<?php

namespace App\Http\Controllers;

use App\Models\CabeceraMonitoreo;
use App\Models\MonitoreoModulos;
use App\Models\MonitoreoEquipo; 
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class GestionAdministrativaPdfController extends Controller
{
    // Cambiamos el nombre de 'generarPDF' a 'generar'
    public function generar($id)
    {
        $acta = CabeceraMonitoreo::with('establecimiento')->findOrFail($id);

        $detalle = MonitoreoModulos::where('cabecera_monitoreo_id', $id)
                                    ->where('modulo_nombre', 'gestion_administrativa')
                                    ->firstOrFail();

        // IMPORTANTE: Verifica el nombre de tu modelo de equipos de computo
        $equipos = \App\Models\EquipoComputo::where('cabecera_monitoreo_id', $id)
                                    ->where('modulo', 'gestion_administrativa')
                                    ->get();

        $pdf = Pdf::loadView('usuario.monitoreo.pdf.gestion_administrativa_pdf', compact('acta', 'detalle', 'equipos'));

        return $pdf->setPaper('a4', 'portrait')->stream("Modulo01_Acta_{$id}.pdf");
    }
}