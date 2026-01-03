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
        // Se carga la cabecera. El objeto $acta ya contiene los campos del jefe.
        $acta = CabeceraMonitoreo::with(['establecimiento'])->findOrFail($id);

        // 2. Extraer datos del Jefe (desde la tabla mon_cabecera_monitoreo)
        // Asegúrate de que estos nombres de columna coincidan con tu migración/BD
        $jefe = [
            'nombre' => mb_strtoupper($acta->jefe_nombre ?? 'NO REGISTRADO', 'UTF-8'),
            'dni'    => $acta->jefe_dni ?? '________',
            'cargo'  => mb_strtoupper($acta->jefe_cargo ?? 'JEFE DE ESTABLECIMIENTO', 'UTF-8'),
        ];

        // 3. Módulos registrados y Equipos asociados
        $modulos = MonitoreoModulos::where('cabecera_monitoreo_id', $id)
            ->orderBy('modulo_nombre', 'asc')
            ->get();

        $equipos = EquipoComputo::where('cabecera_monitoreo_id', $id)->get();

        // 4. Equipo de Monitoreo
        $equipoMonitoreo = DB::table('mon_equipo_monitoreo')
            ->where('cabecera_monitoreo_id', $id)
            ->get();

        // 5. Datos del Monitor (Usuario en sesión)
        $user = Auth::user();
        $monitor = [
            'nombre' => mb_strtoupper("{$user->apellido_paterno} {$user->apellido_materno}, {$user->name}", 'UTF-8'),
            'dni'    => $user->documento ?? $user->username ?? '________'
        ];

        // 6. Preparación de la data para la vista Blade
        $data = [
            'acta'            => $acta,
            'jefe'            => $jefe, // <--- Enviamos los datos del jefe por separado para fácil acceso
            'modulos'         => $modulos,
            'equipos'         => $equipos,
            'monitor'         => $monitor,
            'equipoMonitoreo' => $equipoMonitoreo
        ];

        // 7. Generación del PDF
        $pdf = Pdf::loadView('usuario.monitoreo.pdf.consolidado_pdf', $data);

        return $pdf->setPaper('a4', 'portrait')
                   ->stream("ACTA_MONITOREO_N_" . str_pad($id, 5, '0', STR_PAD_LEFT) . ".pdf");
    }
}