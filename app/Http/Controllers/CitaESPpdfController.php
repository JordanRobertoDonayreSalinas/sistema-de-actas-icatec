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

class CitaESPpdfController extends Controller
{
    public function generar($id)
    {
        // 1. Cargar datos generales del Acta
        $acta = CabeceraMonitoreo::with(['establecimiento', 'user'])->findOrFail($id);

        // 2. Cargar datos específicos del módulo citas_esp

        // --- NUEVO CÓDIGO AQUÍ ---
        // Buscamos el registro en MonitoreoModulos
        $monitoreoModulo = MonitoreoModulos::where('cabecera_monitoreo_id', $id)
                            ->where('modulo_nombre', 'citas_esp')
                            ->first();

        // Inyectamos el updated_at como una nueva propiedad dentro de $acta
        // Le pondremos 'fecha_validacion' para no sobrescribir el updated_at original del acta
        $acta->fecha_validacion = $monitoreoModulo ? $monitoreoModulo->updated_at : null;
        // -------------------------
        
        // Capacitación y Profesional
        $dbCapacitacion = ComCapacitacion::with('profesional')
                            ->where('acta_id', $id)
                            ->where('modulo_id', 'citas_esp')
                            ->first();

        // NUEVO: Inicio de Labores
        $dbInicioLabores = ComDocuAsisten::where('acta_id', $id)
                            ->where('modulo_id', 'citas_esp')->first();
        
        // Inventario
        $dbInventario = EquipoComputo::where('cabecera_monitoreo_id', $id)
                            ->where('modulo', 'citas_esp')
                            ->get();

        // Dificultades
        $dbDificultad = ComDificultad::where('acta_id', $id)
                            ->where('modulo_id', 'citas_esp')
                            ->first();

        $dbDni = ComDni::where('acta_id', $id)
                            ->where('modulo_id', 'citas_esp')->first();
        
        // Fotos
        $dbFotos = ComFotos::where('acta_id', $id)
                        ->where('modulo_id', 'citas_esp')
                        ->get();

        // 3. Preparar el PDF
        // 'usuario.monitoreo.pdf_especializados.citas_pdf' será la vista que crearemos a continuación
        $pdf = Pdf::loadView('usuario.monitoreo.pdf_especializados.citas_pdf', compact(
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
        return $pdf->stream('Reporte_Citas_CSMC_' . $acta->id . '.pdf');
    }
}