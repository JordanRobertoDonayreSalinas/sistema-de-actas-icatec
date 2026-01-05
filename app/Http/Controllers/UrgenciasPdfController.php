<?php

namespace App\Http\Controllers;

use App\Models\CabeceraMonitoreo;
use App\Models\MonitoreoModulos;
use App\Models\EquipoComputo;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class UrgenciasPdfController extends Controller
{
    /**
     * Nombre del módulo para filtrar en las tablas detalle y equipos.
     * Debe coincidir con el valor usado en UrgenciasController.
     */
    private $modulo = 'urgencias';

    /**
     * Genera el reporte PDF del Módulo de Urgencias y Emergencias.
     *
     * @param int $id ID de la Cabecera de Monitoreo (Acta)
     * @return \Illuminate\Http\Response
     */
    public function generar($id)
    {
        // 1. Cargar el acta con la información del establecimiento
        $acta = CabeceraMonitoreo::with('establecimiento')->findOrFail($id);

        // 2. Cargar el detalle guardado en JSON para el Módulo de Urgencias (ID 18 o nombre específico)
        $detalle = MonitoreoModulos::where('cabecera_monitoreo_id', $id)
                                    ->where('modulo_nombre', $this->modulo)
                                    ->firstOrFail();

        // 3. Cargar el inventario de equipos vinculado a este acta y módulo
        $equipos = EquipoComputo::where('cabecera_monitoreo_id', $id)
                                 ->where('modulo', $this->modulo)
                                 ->get();

        // 4. Obtener datos del Monitor Responsable (Usuario en sesión) para el pie de página
        $user = Auth::user();
        $monitor = [
            'nombre'    => mb_strtoupper("{$user->apellido_paterno} {$user->apellido_materno}, {$user->name}", 'UTF-8'),
            'tipo_doc'  => $user->tipo_documento ?? 'DNI',
            'documento' => $user->documento ?? $user->username ?? '________'
        ];

        // 5. Preparar el conjunto de datos para la vista
        $data = [
            'acta'    => $acta,
            'detalle' => $detalle,
            'equipos' => $equipos,
            'monitor' => $monitor
        ];

        // 6. Cargar la vista Blade corregida (asegúrate de que el nombre del archivo sea este)
        // Si el archivo está en resources/views/pdf/urgencias_pdf.blade.php usa 'pdf.urgencias_pdf'
        $pdf = Pdf::loadView('usuario.monitoreo.pdf.urgencias_pdf', $data);

        // 7. Configuración de DomPDF para renderizar el pie de página y numeración
        // Se define tamaño A4 y orientación vertical
        return $pdf->setPaper('a4', 'portrait')
                   ->stream("Reporte_Urgencias_Acta_" . str_pad($id, 5, '0', STR_PAD_LEFT) . ".pdf");
    }
}