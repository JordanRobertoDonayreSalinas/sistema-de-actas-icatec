<?php

namespace App\Http\Controllers;

use App\Models\CabeceraMonitoreo;
use App\Models\EquipoComputo;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class FarmaciaPdfController extends Controller
{
    private $modulo = 'farmacia';

    public function generar($id)
    {
        $acta = CabeceraMonitoreo::with(['establecimiento', 'user'])->findOrFail($id);

        $detalle = DB::table('mon_detalle_modulos')
                    ->where('cabecera_monitoreo_id', $id)
                    ->where('modulo_nombre', $this->modulo)
                    ->first();

        $equipos = EquipoComputo::where('cabecera_monitoreo_id', $id)
                                ->where('modulo', $this->modulo)
                                ->get();

        if ($detalle) {
            $detalle->contenido = is_string($detalle->contenido) 
                ? json_decode($detalle->contenido, true) 
                : $detalle->contenido;
        }

        $pdf = Pdf::loadView('usuario.monitoreo.pdf.farmacia_pdf', compact('acta', 'detalle', 'equipos'))
                  ->setPaper('a4', 'portrait');

        return $pdf->stream("MONITOREO_FARMACIA_{$acta->id}.pdf");
    }
}