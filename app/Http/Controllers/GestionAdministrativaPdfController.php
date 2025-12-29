<?php

namespace App\Http\Controllers;

use App\Models\CabeceraMonitoreo;
use App\Models\MonitoreoModulos;
use App\Models\EquipoComputo;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class GestionAdministrativaPdfController extends Controller
{
    /**
     * Genera el PDF del Módulo 01: Gestión Administrativa.
     * * @param int $id ID de la cabecera de monitoreo
     */
    public function generar($id)
    {
        try {
            // 1. Obtener la cabecera con la relación del establecimiento
            $acta = CabeceraMonitoreo::with(['establecimiento'])->findOrFail($id);
            $modulo = 'gestion_administrativa';

            // 2. Obtener el detalle guardado en formato JSON
            $detalle = MonitoreoModulos::where('cabecera_monitoreo_id', $id)
                        ->where('modulo_nombre', $modulo)
                        ->first();

            // 3. Obtener el listado de equipos de cómputo vinculados
            $equipos = EquipoComputo::where('cabecera_monitoreo_id', $id)
                                    ->where('modulo', $modulo)
                                    ->get();

            // Validación: Si no existe el registro en la tabla de módulos, no se puede generar el PDF
            if (!$detalle) {
                return back()->with('error', 'No se encontraron datos registrados para el Módulo 01 de esta acta.');
            }

            // 4. Configurar y cargar la vista del PDF
            // Nota: Se usa la ruta 'usuario.monitoreo.pdf.gestion_administrativa_pdf' 
            // que apunta a: resources/views/usuario/monitoreo/pdf/gestion_administrativa_pdf.blade.php
            $pdf = Pdf::loadView('usuario.monitoreo.pdf.gestion_administrativa_pdf', [
                'acta'    => $acta,
                'detalle' => $detalle,
                'equipos' => $equipos
            ]);

            // Configuraciones opcionales para asegurar el renderizado correcto
            $pdf->setPaper('a4', 'portrait');

            // 5. Retornar el flujo del archivo al navegador
            return $pdf->stream("Modulo_01_Acta_" . str_pad($id, 5, '0', STR_PAD_LEFT) . ".pdf");

        } catch (\Exception $e) {
            // Log del error para depuración en storage/logs/laravel.log
            \Log::error("Error al generar PDF Módulo 01: " . $e->getMessage());
            
            return back()->with('error', 'Ocurrió un error al intentar generar el documento PDF.');
        }
    }
}