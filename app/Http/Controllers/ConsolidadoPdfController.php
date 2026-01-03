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
        // 1. Datos del Acta y Establecimiento
        // Se cargan las relaciones para evitar múltiples consultas a la base de datos
        $acta = CabeceraMonitoreo::with(['establecimiento'])->findOrFail($id);

        // 2. Módulos registrados y Equipos asociados
        // Traemos todos los detalles guardados en mon_detalle_modulos para esta acta
        $modulos = MonitoreoModulos::where('cabecera_monitoreo_id', $id)
            ->orderBy('modulo_nombre', 'asc')
            ->get();

        // Traemos todos los equipos de cómputo registrados en todos los módulos de esta acta
        $equipos = EquipoComputo::where('cabecera_monitoreo_id', $id)->get();

        // 3. Equipo de Monitoreo (Personal que acompaña la supervisión)
        $equipoMonitoreo = DB::table('mon_equipo_monitoreo')
            ->where('cabecera_monitoreo_id', $id)
            ->get();

        // 4. Datos del Monitor Responsable (Usuario actual en sesión)
        $user = Auth::user();
        $monitor = [
            'nombre' => mb_strtoupper("{$user->apellido_paterno} {$user->apellido_materno}, {$user->name}", 'UTF-8'),
            'dni'    => $user->documento ?? $user->username ?? '________'
        ];

        // 5. Preparación de la data para la vista Blade
        $data = [
            'acta'            => $acta,
            'modulos'         => $modulos,
            'equipos'         => $equipos,
            'monitor'         => $monitor,
            'equipoMonitoreo' => $equipoMonitoreo
        ];

        // 6. Generación del PDF
        // Asegúrate de que la vista exista en resources/views/usuario/monitoreo/pdf/consolidado_pdf.blade.php
        $pdf = Pdf::loadView('usuario.monitoreo.pdf.consolidado_pdf', $data);

        return $pdf->setPaper('a4', 'portrait')
                   ->stream("ACTA_MONITOREO_N_" . str_pad($id, 5, '0', STR_PAD_LEFT) . ".pdf");
    }
}