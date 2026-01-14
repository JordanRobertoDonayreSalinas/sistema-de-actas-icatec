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
     * Genera el reporte PDF del Módulo 01 asegurando los datos del monitor y contenido técnico.
     */
    public function generar($id)
    {
        // 1. Cargar el acta optimizando la relación del establecimiento
        // CORRECCIÓN: Se agrega 'codigo' para que se muestre en el encabezado del PDF
        $acta = CabeceraMonitoreo::with('establecimiento:id,nombre,codigo')->findOrFail($id);

        // 2. Cargar el detalle guardado para el Módulo 01 (JSON con Turno, DNI, Capacitación, etc.)
        $detalle = MonitoreoModulos::where('cabecera_monitoreo_id', $id)
                                    ->where('modulo_nombre', 'gestion_administrativa')
                                    ->firstOrFail();

        // 3. Cargar el inventario de equipos de este acta y módulo específico
        $equipos = EquipoComputo::where('cabecera_monitoreo_id', $id)
                                ->where('modulo', 'gestion_administrativa')
                                ->get();

        // 4. CAPTURA DEL MONITOR (Usuario autenticado que genera el reporte)
        $user = Auth::user();

        // Estructura de datos del monitor siguiendo el formato: APELLIDOS, NOMBRES
        $monitor = [
            'nombre'    => mb_strtoupper("{$user->apellido_paterno} {$user->apellido_materno}, {$user->name}", 'UTF-8'),
            'tipo_doc'  => $user->tipo_documento ?? 'DNI',
            'documento' => $user->documento ?? $user->username ?? '________'
        ];

        // 5. Cargar la vista técnica del PDF. 
        $pdf = Pdf::loadView('usuario.monitoreo.pdf.gestion_administrativa_pdf', compact(
            'acta', 
            'detalle', 
            'equipos', 
            'monitor'
        ));

        // 6. Configuración del PDF
        // CORRECCIÓN: Habilitar PHP para permitir el script de contador de páginas (PAG X/Y)
        $pdf->setOption('isPhpEnabled', true);

        return $pdf->setPaper('a4', 'portrait')
                   ->stream("Acta_M01_ID" . str_pad($id, 5, '0', STR_PAD_LEFT) . ".pdf");
    }
}