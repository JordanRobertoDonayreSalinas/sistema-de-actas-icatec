<?php

namespace App\Http\Controllers;

use App\Models\CabeceraMonitoreo;
use App\Models\MonitoreoModulos;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class PsicologiaESPpdfController extends Controller
{
    public function generar($id)
    {
        // 1. Obtener datos de la cabecera (Establecimiento, Equipo, Usuario)
        $monitoreo = CabeceraMonitoreo::with(['establecimiento', 'equipo', 'user'])->findOrFail($id);

        // 2. Obtener los datos guardados del módulo específico ('psicologia_esp')
        $modulo = MonitoreoModulos::where('cabecera_monitoreo_id', $id)
                                  ->where('modulo_nombre', 'sm_psicologia')
                                  ->first();

        if (!$modulo) {
            return back()->with('error', 'No hay datos guardados para este módulo.');
        }

        $equipos = DB::table('mon_equipos_computo')
                 ->where('cabecera_id', $id)
                 ->where('modulo', 'sm_psicologia')
                 ->get();

        // Como el modelo tiene cast 'array', no necesitas json_decode
        $data = $modulo->contenido;
        // 5. Cargar la vista del PDF
        $pdf = Pdf::loadView('usuario.monitoreo.pdf_especializados.psicologia_pdf', [
            'monitoreo' => $monitoreo,
            'data' => $data,
            'modulo' => $modulo,
            'equipos' => $equipos // <--- IMPORTANTE PASAR ESTO
        ]);

        return $pdf->setPaper('a4', 'portrait')->stream("REPORTE_PSICOLOGIA_CSMC.pdf");
    }
}