<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\CabeceraMonitoreo;
use App\Models\ComCapacitacion;
use App\Models\ComEquipamiento;
use App\Models\ComDificultad;
use App\Models\ComFotos;
use App\Models\ComDocuAsisten; 
use App\Models\ComDni;

class OdontologiaPdfController extends Controller
{
    public function generar($id)
    {
        // 1. Cargar datos generales del Acta
        $acta = CabeceraMonitoreo::with(['establecimiento', 'user'])->findOrFail($id);
        
        // Identificador constante
        $modId = 'ODONTOLOGIA';

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
        $dbInventario = ComEquipamiento::where('acta_id', $id)
                            ->where('modulo_id', $modId)->get();

        // Dificultades
        $dbDificultad = ComDificultad::where('acta_id', $id)
                            ->where('modulo_id', $modId)->first();

        // Fotos
        $dbFotos = ComFotos::where('acta_id', $id)
                        ->where('modulo_id', $modId)->get();

        // 3. Preparar el PDF
        $pdf = Pdf::loadView('usuario.monitoreo.pdf.odontologia_pdf', compact(
            'acta', 
            'dbCapacitacion', 
            'dbInicioLabores', // <--- Pasamos variable
            'dbDni',           // <--- Pasamos variable
            'dbInventario', 
            'dbDificultad', 
            'dbFotos'
        ));

        $pdf->setPaper('a4', 'portrait');

        return $pdf->stream('Reporte_Odontologia_' . $acta->id . '.pdf');
    }
}
