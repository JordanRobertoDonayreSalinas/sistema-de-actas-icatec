<?php

namespace App\Http\Controllers;

use App\Models\CabeceraMonitoreo;
use App\Models\EquipoComputo;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class PlanificacionPdfController extends Controller
{
    private $modulo = 'planificacion_familiar';

    public function generar($id)
    {
        $acta = CabeceraMonitoreo::with(['establecimiento', 'user'])->findOrFail($id);

        $detalle = DB::table('mon_detalle_modulos')
                    ->where('cabecera_monitoreo_id', $id)
                    ->where('modulo_nombre', $this->modulo)
                    ->first();

        // Inicializamos equipos como array vacío
        $equipos = [];

        if ($detalle) {
            $detalle->contenido = json_decode($detalle->contenido, true);
            
            // EXTRAEMOS LOS EQUIPOS DEL JSON
            if (isset($detalle->contenido['equipos_data'])) {
                // Convertimos el array en una colección de objetos para no romper el Blade
                $equipos = collect($detalle->contenido['equipos_data'])->map(function($item) {
                    return (object) $item;
                });
            }
        }

        $pdf = Pdf::loadView('usuario.monitoreo.pdf.planificacion_familiar_pdf', compact('acta', 'detalle', 'equipos'))
                ->setPaper('a4', 'portrait');

        return $pdf->stream("MONITOREO_PLANIFICACION_ACTA_{$acta->id}.pdf");
    }
}