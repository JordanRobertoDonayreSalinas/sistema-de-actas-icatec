<?php

namespace App\Http\Controllers;

use App\Models\CabeceraMonitoreo;
use App\Models\MonitoreoModulos;
use App\Models\EquipoComputo;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class LaboratorioPdfController extends Controller
{
    /**
     * Nombre del módulo para filtrar en las tablas detalle y equipos.
     */
    private $modulo = 'laboratorio';

    /**
     * Genera el reporte PDF del Módulo de Laboratorio.
     * * @param int $id ID de la Cabecera de Monitoreo
     * @return \Illuminate\Http\Response
     */
    public function generar($id)
    {
        // 1. Cargar el acta con la información del establecimiento
        $acta = CabeceraMonitoreo::with('establecimiento')->findOrFail($id);

        // 2. Cargar el detalle guardado en JSON para el Módulo de Laboratorio
        $detalle = MonitoreoModulos::where('cabecera_monitoreo_id', $id)
                                    ->where('modulo_nombre', $this->modulo)
                                    ->firstOrFail();

        // 3. Cargar el inventario de equipos (Tecnológicos/Biomédicos) de este acta y módulo
        $equipos = EquipoComputo::where('cabecera_monitoreo_id', $id)
                                ->where('modulo', $this->modulo)
                                ->get();

        // 4. Obtener datos del Monitor Responsable desde la sesión activa
        $user = Auth::user();
        $monitor = [
            'nombre'    => mb_strtoupper("{$user->apellido_paterno} {$user->apellido_materno}, {$user->name}", 'UTF-8'),
            'tipo_doc'  => $user->tipo_documento ?? 'DNI',
            'documento' => $user->documento ?? $user->username ?? '________'
        ];

        // 5. Preparar la data para la vista PDF
        $data = [
            'acta'    => $acta,
            'detalle' => $detalle,
            'equipos' => $equipos,
            'monitor' => $monitor
        ];

        // 6. Cargar la vista Blade específica para el PDF de Laboratorio
        // Ruta: resources/views/usuario/monitoreo/pdf/laboratorio_pdf.blade.php
        $pdf = Pdf::loadView('usuario.monitoreo.pdf.laboratorio_pdf', $data);

        // 7. Configurar tamaño de papel y retornar el flujo del PDF
        return $pdf->setPaper('a4', 'portrait')
                   ->stream("Modulo_Laboratorio_Acta_" . str_pad($id, 5, '0', STR_PAD_LEFT) . ".pdf");
    }
}