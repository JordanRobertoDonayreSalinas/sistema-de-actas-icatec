<?php

namespace App\Http\Controllers;

use App\Models\CabeceraMonitoreo;
use App\Models\MonitoreoModulos;
use App\Models\EquipoComputo;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class PuerperioPdfController extends Controller
{
    public function generar($id)
    {
        $acta = CabeceraMonitoreo::with('establecimiento')->findOrFail($id);
        $detalle = MonitoreoModulos::where('cabecera_monitoreo_id', $id)
                                    ->where('modulo_nombre', 'puerperio')
                                    ->firstOrFail();
        $equipos = EquipoComputo::where('cabecera_monitoreo_id', $id)
                                ->where('modulo', 'puerperio')
                                ->get();

        $user = Auth::user();
        $monitor = [
            'nombre'    => mb_strtoupper("{$user->apellido_paterno} {$user->apellido_materno}, {$user->name}", 'UTF-8'),
            'tipo_doc'  => $user->tipo_documento ?? 'DNI',
            'documento' => $user->documento ?? $user->username ?? '________'
        ];

        $pdf = Pdf::loadView('usuario.monitoreo.pdf.puerperio_pdf', compact('acta', 'detalle', 'equipos', 'monitor'));
        
        return $pdf->setPaper('a4', 'portrait')
                   ->stream("Modulo_Puerperio_Acta_" . str_pad($id, 5, '0', STR_PAD_LEFT) . ".pdf");
    }
}