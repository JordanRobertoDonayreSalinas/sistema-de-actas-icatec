<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\CabeceraMonitoreo;
use App\Models\ComCapacitacion;
use App\Models\ComEquipamiento;
use App\Models\ComDificultad;
use App\Models\ComFotos;

class TriajePdfController extends Controller
{
    public function generar($id)
    {
        // 1. Cargar datos generales del Acta
        $acta = CabeceraMonitoreo::with(['establecimiento', 'user'])->findOrFail($id);

        // 2. Cargar datos específicos del módulo TRIAJE
        
        // Capacitación y Profesional
        $dbCapacitacion = ComCapacitacion::with('profesional')
                            ->where('acta_id', $id)
                            ->where('modulo_id', 'TRIAJE')
                            ->first();

        // Inventario
        $dbInventario = ComEquipamiento::where('acta_id', $id)
                            ->where('modulo_id', 'TRIAJE')
                            ->get();

        // Dificultades
        $dbDificultad = ComDificultad::where('acta_id', $id)
                            ->where('modulo_id', 'TRIAJE')
                            ->first();

        // Fotos
        $dbFotos = ComFotos::where('acta_id', $id)
                        ->where('modulo_id', 'TRIAJE')
                        ->get();

        // 3. Preparar el PDF
        // 'usuario.monitoreo.pdf.triaje_pdf' será la vista que crearemos a continuación
        $pdf = Pdf::loadView('usuario.monitoreo.pdf.triaje_pdf', compact(
            'acta', 
            'dbCapacitacion', 
            'dbInventario', 
            'dbDificultad', 
            'dbFotos'
        ));

        // Configuración opcional de papel
        $pdf->setPaper('a4', 'portrait');

        // 4. Retornar el PDF al navegador
        // 'stream' lo muestra en el navegador, 'download' lo descarga directo.
        return $pdf->stream('Reporte_Triaje_' . $acta->id . '.pdf');
    }
}
