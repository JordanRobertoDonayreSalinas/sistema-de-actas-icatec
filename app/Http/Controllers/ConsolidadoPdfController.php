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
        // 1. Cargamos el acta con la relación del establecimiento
        $acta = CabeceraMonitoreo::with(['establecimiento'])->findOrFail($id);

        // 2. Datos del Jefe de Establecimiento (Usando el campo 'responsable')
        $jefe = [
            'nombre' => mb_strtoupper($acta->responsable ?? 'NO REGISTRADO', 'UTF-8'),
            'cargo'  => 'JEFE DE ESTABLECIMIENTO'
        ];

        // 3. Módulos de Monitoreo (Hallazgos)
        $modulos = MonitoreoModulos::where('cabecera_monitoreo_id', $id)->get();

        // 4. Inventario de Equipos (Para la nueva sección de equipamiento por módulos)
        $equipos = EquipoComputo::where('cabecera_monitoreo_id', $id)->get();

        // 5. Equipo de Monitoreo / Acompañantes (Tabla mon_equipo_monitoreo)
        $equipoMonitoreo = DB::table('mon_equipo_monitoreo')
            ->where('cabecera_monitoreo_id', $id)
            ->get();

        // 6. Datos del Monitor (Estandarizado: SIN COMA entre apellidos y nombre)
        $user = Auth::user();
        $monitor = [
            'nombre' => mb_strtoupper("{$user->apellido_paterno} {$user->apellido_materno} {$user->name}", 'UTF-8')
        ];

        // 7. Consolidación de datos para la vista
        $data = [
            'acta'            => $acta,
            'jefe'            => $jefe,
            'modulos'         => $modulos,
            'equipos'         => $equipos,
            'monitor'         => $monitor,
            'equipoMonitoreo' => $equipoMonitoreo
        ];

        // 8. Generación del PDF
        $pdf = Pdf::loadView('usuario.monitoreo.pdf.consolidado_pdf', $data);

        // Retornamos el PDF con orientación vertical y nombre dinámico
        return $pdf->setPaper('a4', 'portrait')
                   ->stream("ACTA_MONITOREO_" . ltrim($id, '0') . ".pdf");
    }
}