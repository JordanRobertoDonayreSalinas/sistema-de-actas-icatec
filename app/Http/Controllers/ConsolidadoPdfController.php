<?php

namespace App\Http\Controllers;

use App\Models\CabeceraMonitoreo;
use App\Models\MonitoreoModulos;
use App\Models\EquipoComputo;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class ConsolidadoPdfController extends Controller
{
    public function generar($id)
    {
        // 1. Datos del Acta y Jefe (desde mon_cabecera_monitoreo)
        $acta = CabeceraMonitoreo::with(['establecimiento'])->findOrFail($id);

        // 2. Módulos y Equipos
        $modulos = MonitoreoModulos::where('cabecera_monitoreo_id', $id)
            ->orderBy('modulo_nombre', 'asc')
            ->get();
        $equipos = EquipoComputo::where('cabecera_monitoreo_id', $id)->get();

        // 3. Equipo de Monitoreo (de la tabla mon_equipo_monitoreo)
        $equipoMonitoreo = DB::table('mon_equipo_monitoreo')
            ->where('cabecera_monitoreo_id', $id)
            ->get();

        // 4. Datos del Monitor (Usuario en sesión)
        $user = Auth::user();
        $monitor = [
            'nombre' => mb_strtoupper("{$user->apellido_paterno} {$user->apellido_materno}, {$user->name}", 'UTF-8'),
            'dni'    => $user->documento ?? $user->username ?? '________'
        ];

        $data = [
            'acta'            => $acta,
            'modulos'         => $modulos,
            'equipos'         => $equipos,
            'monitor'         => $monitor,
            'equipoMonitoreo' => $equipoMonitoreo
        ];

        $pdf = Pdf::loadView('usuario.monitoreo.pdf.consolidado_pdf', $data);

        return $pdf->setPaper('a4', 'portrait')
                   ->stream("ACTA_MONITOREO_N_" . str_pad($id, 5, '0', STR_PAD_LEFT) . ".pdf");
    }
}