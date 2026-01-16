<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\CabeceraMonitoreo;
use App\Models\ComCapacitacion;
use App\Models\ComDificultad;
use App\Models\ComFotos;
use App\Models\EquipoComputo;
use App\Models\ComDocuAsisten; 
use App\Models\MonitoreoModulos;
use App\Models\ComDni;

class TriajePdfController extends Controller
{
    public function generar($id)
    {
        // 1. Cargar datos generales del Acta
        $acta = CabeceraMonitoreo::with(['establecimiento', 'user'])->findOrFail($id);

        // 2. Cargar datos específicos del módulo triaje

        // --- NUEVO CÓDIGO AQUÍ ---
        // Buscamos el registro en MonitoreoModulos
        $monitoreoModulo = MonitoreoModulos::where('cabecera_monitoreo_id', $id)
                            ->where('modulo_nombre', 'triaje')
                            ->first();

        // Inyectamos el updated_at como una nueva propiedad dentro de $acta
        // Le pondremos 'fecha_validacion' para no sobrescribir el updated_at original del acta
        $acta->fecha_validacion = $monitoreoModulo ? $monitoreoModulo->updated_at : null;
        // -------------------------
        
        // Capacitación y Profesional
        $dbCapacitacion = ComCapacitacion::with('profesional')
                            ->where('acta_id', $id)
                            ->where('modulo_id', 'triaje')
                            ->first();

        // NUEVO: Inicio de Labores
        $dbInicioLabores = ComDocuAsisten::where('acta_id', $id)
                            ->where('modulo_id', 'triaje')->first();
        
        // Inventario
        $dbInventario = EquipoComputo::where('cabecera_monitoreo_id', $id)
                            ->where('modulo', 'triaje')
                            ->get();

        // Dificultades
        $dbDificultad = ComDificultad::where('acta_id', $id)
                            ->where('modulo_id', 'triaje')
                            ->first();

        $dbDni = ComDni::where('acta_id', $id)
                            ->where('modulo_id', 'triaje')->first();
        
        // Fotos
        $dbFotos = ComFotos::where('acta_id', $id)
                        ->where('modulo_id', 'triaje')
                        ->get();

        // 3. Preparar el PDF
        // 'usuario.monitoreo.pdf.triaje_pdf' será la vista que crearemos a continuación
        $pdf = Pdf::loadView('usuario.monitoreo.pdf.triaje_pdf', compact(
            'acta', 
            'dbCapacitacion', 
            'dbInventario', 
            'dbDificultad', 
            'dbInicioLabores',
            'dbDni',
            'dbFotos'
        ));


        $pdf->setOption('isPhpEnabled', true);

        $pdf->setPaper('a4', 'portrait');

        // 4. Retornar el PDF al navegador
        // 'stream' lo muestra en el navegador, 'download' lo descarga directo.
        return $pdf->stream('Reporte_Triaje_' . $acta->id . '.pdf');
    }
}