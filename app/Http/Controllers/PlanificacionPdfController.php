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

        // 1. Intentamos cargar desde la tabla independiente (más seguro)
        $equipos = EquipoComputo::where('cabecera_monitoreo_id', $id)
                                ->where('modulo', $this->modulo)
                                ->get();

        if ($detalle) {
            $detalle->contenido = json_decode($detalle->contenido, true);
            
            // 2. Si la tabla independiente está vacía, extraemos del JSON
            if ($equipos->isEmpty() && isset($detalle->contenido['equipos_data'])) {
                $equipos = collect($detalle->contenido['equipos_data'])->map(function($item) {
                    // Convertimos a objeto y mapeamos 'propiedad' a 'propio' para el PDF
                    return (object) [
                        'descripcion' => $item['descripcion'] ?? 'N/A',
                        'cantidad'    => $item['cantidad'] ?? 1,
                        'estado'      => $item['estado'] ?? 'N/A',
                        'nro_serie'   => $item['nro_serie'] ?? null,
                        'observaciones' => $item['observaciones'] ?? '',
                        // MAPEO CRÍTICO: El PDF busca 'propio', el componente envía 'propiedad'
                        'propio'      => $item['propiedad'] ?? ($item['propio'] ?? 'ESTABLECIMIENTO')
                    ];
                });
            }
        }

        // 3. Estandarizamos los datos del detalle para evitar errores de "Array a String" en el PDF
        $datos = $detalle ? $detalle->contenido : [];

        $pdf = Pdf::loadView('usuario.monitoreo.pdf.planificacion_familiar_pdf', [
            'acta' => $acta,
            'detalle' => $detalle,
            'equipos' => $equipos,
            'datos' => $datos // Pasamos 'datos' por separado para facilitar el acceso en el Blade
        ])->setPaper('a4', 'portrait');

        return $pdf->stream("MONITOREO_PLANIFICACION_ACTA_{$acta->id}.pdf");
    }
}
