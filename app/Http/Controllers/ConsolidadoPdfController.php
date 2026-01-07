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
        // 1. Cargamos el acta con el establecimiento Y el usuario que la creó (relación 'user')
        // Asegúrate de tener definida la relación 'user' en tu modelo CabeceraMonitoreo
        $acta = CabeceraMonitoreo::with(['establecimiento', 'user'])->findOrFail($id);

        // 2. Datos del Jefe de Establecimiento
        $jefe = [
            'nombre' => mb_strtoupper($acta->responsable ?? 'NO REGISTRADO', 'UTF-8'),
            'cargo'  => 'JEFE DE ESTABLECIMIENTO'
        ];

        // 3. Módulos de Monitoreo (Hallazgos)
        $modulos = MonitoreoModulos::where('cabecera_monitoreo_id', $id)->get();

        // 4. Inventario de Equipos
        $equipos = EquipoComputo::where('cabecera_monitoreo_id', $id)->get();

        // 5. Equipo de Monitoreo / Acompañantes
        $equipoMonitoreo = DB::table('mon_equipo_monitoreo')
            ->where('cabecera_monitoreo_id', $id)
            ->get();

        // 6. Datos del Monitor / Implementador (CORREGIDO)
        // Usamos $acta->user para obtener los datos del creador, no de quien está logueado.
        $creador = $acta->user; 
        
        $monitor = [
            'nombre' => $creador 
                ? mb_strtoupper("{$creador->apellido_paterno} {$creador->apellido_materno} {$creador->name}", 'UTF-8')
                : 'USUARIO NO IDENTIFICADO'
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

        return $pdf->setPaper('a4', 'portrait')
                   ->stream("ACTA_MONITOREO_" . ltrim($id, '0') . ".pdf");
    }
}