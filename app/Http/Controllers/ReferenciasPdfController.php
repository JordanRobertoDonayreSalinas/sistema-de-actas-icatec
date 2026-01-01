<?php

namespace App\Http\Controllers;

use App\Models\CabeceraMonitoreo;
use App\Models\EquipoComputo;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class ReferenciasPdfController extends Controller
{
    private $modulo = 'referencias';

    public function generar($id)
    {
        // 1. Cargar Cabecera con Establecimiento y Monitor
        $acta = CabeceraMonitoreo::with(['establecimiento', 'user'])->findOrFail($id);

        // 2. Buscar Detalle en mon_detalle_modulos
        $detalle = DB::table('mon_detalle_modulos')
                    ->where('cabecera_monitoreo_id', $id)
                    ->where('modulo_nombre', $this->modulo)
                    ->first();

        // 3. Cargar Equipos del módulo Referencias
        $equipos = EquipoComputo::where('cabecera_monitoreo_id', $id)
                                ->where('modulo', $this->modulo)
                                ->get();

        if ($detalle) {
            $detalle->contenido = is_string($detalle->contenido) 
                ? json_decode($detalle->contenido, true) 
                : $detalle->contenido;
        }

        // 4. Generación del PDF
        $pdf = Pdf::loadView('usuario.monitoreo.pdf.referencias_pdf', compact('acta', 'detalle', 'equipos'))
                  ->setPaper('a4', 'portrait');

        return $pdf->stream("MONITOREO_REFERENCIAS_ACTA_{$acta->id}.pdf");
    }
}