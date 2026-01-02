<?php

namespace App\Http\Controllers;

use App\Models\CabeceraMonitoreo;
use App\Models\EquipoComputo;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class FuaElectronicoPdfController extends Controller
{
    public function generar($id)
    {
        $acta = CabeceraMonitoreo::with(['establecimiento', 'user'])->findOrFail($id);
        $modulo = 'fua_electronico';

        $detalle = \App\Models\MonitoreoModulos::where('cabecera_monitoreo_id', $id)
                    ->where('modulo_nombre', $modulo)
                    ->first();

        $imagenesData = [];
        if ($detalle && isset($detalle->contenido['foto_evidencia'])) {
            $fotos = $detalle->contenido['foto_evidencia'];
            $fotosArray = is_array($fotos) ? $fotos : [$fotos];
            
            foreach ($fotosArray as $foto) {
                if (!empty($foto)) {
                    $rutaCompleta = public_path('storage/' . $foto);
                    if (file_exists($rutaCompleta)) {
                        $imagenesData[] = $rutaCompleta;
                    }
                }
            }
        }

        $pdf = Pdf::loadView('usuario.monitoreo.pdf.fua_electronico_pdf', compact('acta', 'detalle', 'imagenesData'))
                  ->setPaper('a4', 'portrait');

        return $pdf->stream("MONITOREO_FUA_ELECTRONICO_{$acta->id}.pdf");
    }
}