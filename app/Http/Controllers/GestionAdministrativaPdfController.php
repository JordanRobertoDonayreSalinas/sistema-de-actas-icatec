<?php

namespace App\Http\Controllers;

use App\Models\CabeceraMonitoreo;
use App\Models\MonitoreoModulos;
use App\Models\EquipoComputo; 
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class GestionAdministrativaPdfController extends Controller
{
    /**
     * Genera el reporte PDF del Módulo 01 asegurando los datos del monitor.
     */
    public function generar($id)
    {
        // 1. Cargar el acta con la relación del establecimiento
        $acta = CabeceraMonitoreo::with('establecimiento')->findOrFail($id);

        // 2. Cargar el detalle guardado para el Módulo 01
        $detalle = MonitoreoModulos::where('cabecera_monitoreo_id', $id)
                                    ->where('modulo_nombre', 'gestion_administrativa')
                                    ->firstOrFail();

        // 3. Cargar el inventario de equipos de este acta y módulo
        $equipos = EquipoComputo::where('cabecera_monitoreo_id', $id)
                                ->where('modulo', 'gestion_administrativa')
                                ->get();

        // 4. CAPTURA DEL MONITOR (Usuario que genera el PDF)
        // Obtenemos el usuario autenticado
        $user = Auth::user();

        // Creamos el array con el orden: APELLIDOS, NOMBRES
        // Usamos los nombres de columna exactos de tu SQL: tipo_documento y documento
        // Si 'documento' es nulo, intentamos usar 'username' como respaldo
        $monitor = [
            'nombre'    => mb_strtoupper("{$user->apellido_paterno} {$user->apellido_materno}, {$user->name}", 'UTF-8'),
            'tipo_doc'  => $user->tipo_documento ?? 'DNI',
            'documento' => $user->documento ?? $user->username ?? '________'
        ];

        // 5. Cargar la vista pasando el array 'monitor'
        // Asegúrate que la vista se llame 'gestion_administrativa_pdf'
        $pdf = Pdf::loadView('usuario.monitoreo.pdf.gestion_administrativa_pdf', compact(
            'acta', 
            'detalle', 
            'equipos', 
            'monitor'
        ));

        // 6. Configuración de formato y visualización
        return $pdf->setPaper('a4', 'portrait')
                   ->stream("Modulo01_Acta_" . str_pad($id, 5, '0', STR_PAD_LEFT) . ".pdf");
    }
}