<?php

namespace App\Http\Controllers;

use App\Models\CabeceraMonitoreo;
use App\Models\EquipoComputo;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CredPdfController extends Controller
{
    private $modulo = 'cred';

    public function generar($id)
    {
        try {
            $acta = CabeceraMonitoreo::with(['establecimiento'])->findOrFail($id);

            // Buscar en la tabla nueva
            $detalle = DB::table('mon_detalle_modulos')
                        ->where('cabecera_monitoreo_id', $id)
                        ->where('modulo_nombre', $this->modulo)
                        ->first();

            if (!$detalle) {
                return back()->with('error', 'No hay datos registrados.');
            }

            // Decodificación del JSON para acceder a email y teléfono
            $datos = is_string($detalle->contenido) ? json_decode($detalle->contenido, true) : (array)$detalle->contenido;

            $equipos = EquipoComputo::where('cabecera_monitoreo_id', $id)
                                    ->where('modulo', $this->modulo)
                                    ->get();

            $pdf = Pdf::loadView('usuario.monitoreo.pdf.cred_pdf', [
                'acta'    => $acta,
                'detalle' => $detalle, 
                'datos'   => $datos, // Importante para email y teléfono
                'equipos' => $equipos
            ]);

            $pdf->getDomPDF()->set_option("isRemoteEnabled", true);
            $pdf->getDomPDF()->set_option("chroot", base_path());

            return $pdf->stream("Reporte_CRED_{$id}.pdf");

        } catch (\Exception $e) {
            return "Error: " . $e->getMessage();
        }
    }
}