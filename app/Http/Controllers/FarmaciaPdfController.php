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
        // 1. Cargar Cabecera con relaciones
        $acta = CabeceraMonitoreo::with(['establecimiento', 'user'])->findOrFail($id);

        // 2. Buscar Detalle en la tabla mon_detalle_modulos
        $detalle = DB::table('mon_detalle_modulos')
                    ->where('cabecera_monitoreo_id', $id)
                    ->where('modulo_nombre', $this->modulo)
                    ->first();

        // 3. Cargar Equipos del módulo Farmacia
        $equipos = EquipoComputo::where('cabecera_monitoreo_id', $id)
                                ->where('modulo', $this->modulo)
                                ->get();

        // 4. Decodificar el JSON del contenido
        $datos = [];
        if ($detalle) {
            $datos = is_string($detalle->contenido) 
                ? json_decode($detalle->contenido, true) 
                : (array)$detalle->contenido;
        }

        // 5. Mapeo de Documentación (Llaves: firma_dj, firma_confidencialidad)
        $documentacion = [
            'firma_dj' => $datos['documentacion']['firma_dj'] ?? 'NO',
            'firma_confidencialidad' => $datos['documentacion']['firma_confidencialidad'] ?? 'NO'
        ];

        // 6. Mapeo de Identidad Digital
        $identidad = [
            'tipo_dni_fisico' => $datos['dni_firma']['tipo_dni_fisico'] ?? 'AZUL',
            'dnie_version'    => $datos['dni_firma']['dnie_version'] ?? 'N/A',
            'firma_sihce'     => $datos['dni_firma']['firma_sihce'] ?? 'NO'
        ];

        // 7. Generación del PDF
        // IMPORTANTE: Incluimos 'datos', 'documentacion' e 'identidad' en el compact
        $pdf = Pdf::loadView('usuario.monitoreo.pdf.farmacia_pdf', compact(
            'acta', 
            'detalle', 
            'equipos', 
            'datos', 
            'documentacion', 
            'identidad'
        ))->setPaper('a4', 'portrait');

        return $pdf->stream("MONITOREO_FARMACIA_ACTA_{$id}.pdf");
    }
}
