<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\CabeceraMonitoreo;
use App\Models\ComCapacitacion;
//use App\Models\ComEquipamiento;
use App\Models\ComDificultad;
use App\Models\ComFotos;
use App\Models\ComDocuAsisten; 
use App\Models\ComDni;

use App\Models\EquipoComputo;
use App\Models\MonitoreoModulos;

class PsicologiaPdfController extends Controller
{
    public function generar($id)
    {
        // 1. Cargar datos generales del Acta
        $acta = CabeceraMonitoreo::with(['establecimiento', 'user'])->findOrFail($id);
        
        // Identificador constante
        $modId = 'consulta_psicologia';

        // --- NUEVO CÓDIGO AQUÍ ---
        // Buscamos el registro en MonitoreoModulos
        $monitoreoModulo = MonitoreoModulos::where('cabecera_monitoreo_id', $id)
                            ->where('modulo_nombre', $modId)
                            ->first();

        // Inyectamos el updated_at como una nueva propiedad dentro de $acta
        // Le pondremos 'fecha_validacion' para no sobrescribir el updated_at original del acta
        $acta->fecha_validacion = $monitoreoModulo ? $monitoreoModulo->updated_at : null;

        // 2. Cargar datos específicos
        
        // Capacitación y Profesional
        $dbCapacitacion = ComCapacitacion::with('profesional')
                            ->where('acta_id', $id)->where('modulo_id', $modId)->first();

        // NUEVO: Inicio de Labores
        $dbInicioLabores = ComDocuAsisten::where('acta_id', $id)
                            ->where('modulo_id', $modId)->first();

        // NUEVO: Sección DNI
        $dbDni = ComDni::where('acta_id', $id)
                            ->where('modulo_id', $modId)->first();

        // Inventario
        $dbInventario = EquipoComputo::where('cabecera_monitoreo_id', $id)
                            ->where('modulo', $modId)->get();

        // Dificultades
        $dbDificultad = ComDificultad::where('acta_id', $id)
                            ->where('modulo_id', $modId)->first();

        // Fotos
        $dbFotos = ComFotos::where('acta_id', $id)
                        ->where('modulo_id', $modId)->get();

        // 3. Preparar el PDF
        $pdf = Pdf::loadView('usuario.monitoreo.pdf.psicologia_pdf', compact(
            'acta', 
            'dbCapacitacion', 
            'dbInicioLabores', // <--- Pasamos variable
            'dbDni',           // <--- Pasamos variable
            'dbInventario', 
            'dbDificultad', 
            'dbFotos'
        ));

        $pdf->setOption('isPhpEnabled', true);

        $pdf->setPaper('a4', 'portrait');

        return $pdf->stream('07_Psicologia_Acta_NOESP_' . $acta->numero_acta . '.pdf');
    }
}
