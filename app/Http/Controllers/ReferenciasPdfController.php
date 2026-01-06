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
        // 1. Cargar Cabecera
        $acta = CabeceraMonitoreo::with(['establecimiento', 'user'])->findOrFail($id);

        // 2. Buscar Detalle
        $detalle = DB::table('mon_detalle_modulos')
                    ->where('cabecera_monitoreo_id', $id)
                    ->where('modulo_nombre', $this->modulo)
                    ->first();

        // 3. Cargar Equipos
        $equipos = EquipoComputo::where('cabecera_monitoreo_id', $id)
                                ->where('modulo', $this->modulo)
                                ->get();

        // 4. EXTRAER DATOS DEL JSON (Punto Crítico)
        $datos = [];
        if ($detalle && !empty($detalle->contenido)) {
            // Decodificamos el JSON a un array asociativo
            $datos = is_string($detalle->contenido) 
                ? json_decode($detalle->contenido, true) 
                : (array)$detalle->contenido;
        }

        // 5. Generación del PDF (Pasamos 'datos' explícitamente)
        $pdf = Pdf::loadView('usuario.monitoreo.pdf.referencias_pdf', compact('acta', 'detalle', 'equipos', 'datos'))
                  ->setPaper('a4', 'portrait');

        return $pdf->stream("MONITOREO_REFERENCIAS_ACTA_{$acta->id}.pdf");
    }
}
