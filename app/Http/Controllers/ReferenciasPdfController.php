<?php

namespace App\Http\Controllers;

use App\Models\CabeceraMonitoreo;
use App\Models\EquipoComputo;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class ReferenciasPdfController extends Controller
{
    // Se cambia a minúsculas para coincidir con lo guardado por ReferenciasController
    private $modulo = 'referencias'; 

    public function generar($id)
    {
        // 1. Cargar la cabecera con relaciones
        $acta = CabeceraMonitoreo::with(['establecimiento', 'user'])->findOrFail($id);

        /**
         * 2. Buscar el detalle en la tabla mon_detalle_modulos.
         * Es CRÍTICO usar esta tabla porque aquí es donde ReferenciasController 
         * guarda las columnas foto_1 y foto_2.
         */
        $detalle = DB::table('mon_detalle_modulos')
                    ->where('cabecera_monitoreo_id', $id)
                    ->where('modulo_nombre', $this->modulo)
                    ->first();

        // 3. Buscar equipos asociados al módulo
        $equipos = EquipoComputo::where('cabecera_monitoreo_id', $id)
                                ->where('modulo', $this->modulo)
                                ->get();

        // 4. Procesar datos del JSON contenido
        $datos = [];
        if ($detalle) {
            // Al usar DB::table el contenido llega como string, debemos decodificarlo
            $datos = json_decode($detalle->contenido, true) ?? [];
            
            /** * Mapeo de fotos para la vista:
             * Como usamos DB::table, el objeto $detalle ya tiene las propiedades 
             * foto_1 y foto_2 directamente desde las columnas de la tabla.
             */
        }

        // 5. Generar el PDF enviando las variables a la vista
        $pdf = Pdf::loadView('usuario.monitoreo.pdf.referencias_pdf', compact('acta', 'detalle', 'equipos', 'datos'))
                  ->setPaper('a4', 'portrait');

        return $pdf->stream("MONITOREO_REFERENCIAS_ACTA_{$acta->id}.pdf");
    }
}