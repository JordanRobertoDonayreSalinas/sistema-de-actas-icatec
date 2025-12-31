<?php

namespace App\Http\Controllers;

use App\Models\CabeceraMonitoreo;
use App\Models\MonitoreoModulos;
use App\Models\EquipoComputo;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

// CORRECCIÓN: El nombre de la clase debe coincidir con el nombre del archivo
class LaboratorioPdfController extends Controller
{
    private $modulo = 'laboratorio';

    /**
     * Genera el reporte PDF del Módulo de Laboratorio.
     */
    public function generar($id)
    {
        // 1. Cargar el acta con la relación del establecimiento
        $acta = CabeceraMonitoreo::with('establecimiento')->findOrFail($id);

        // 2. Cargar el detalle guardado para el Módulo de Laboratorio
        $detalle = MonitoreoModulos::where('cabecera_monitoreo_id', $id)
                                    ->where('modulo_nombre', $this->modulo)
                                    ->firstOrFail();

        // 3. Cargar el inventario de equipos de este acta y módulo
        $equipos = EquipoComputo::where('cabecera_monitoreo_id', $id)
                                ->where('modulo', $this->modulo)
                                ->get();

        // 4. Captura de datos del Monitor (Usuario que genera el reporte)
        $user = Auth::user();
        $monitor = [
            'nombre'    => mb_strtoupper("{$user->apellido_paterno} {$user->apellido_materno}, {$user->name}", 'UTF-8'),
            'tipo_doc'  => $user->tipo_documento ?? 'DNI',
            'documento' => $user->documento ?? $user->username ?? '________'
        ];

        // 5. Cargar la vista pasándole 'acta', 'detalle', 'equipos' y 'monitor'
        // Asegúrate de que el archivo sea resources/views/usuario/monitoreo/pdf/laboratorio_pdf.blade.php
        $pdf = Pdf::loadView('usuario.monitoreo.pdf.laboratorio_pdf', compact(
            'acta', 
            'detalle', 
            'equipos', 
            'monitor'
        ));

        // 6. Configuración de formato y descarga/visualización
        return $pdf->setPaper('a4', 'portrait')
                   ->stream("Modulo_Laboratorio_Acta_" . str_pad($id, 5, '0', STR_PAD_LEFT) . ".pdf");
    }
}