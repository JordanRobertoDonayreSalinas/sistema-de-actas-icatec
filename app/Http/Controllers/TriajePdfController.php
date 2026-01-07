<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\CabeceraMonitoreo;
use App\Models\MonitoreoModulos; 
use App\Models\EquipoComputo;

class OdontologiaPdfController extends Controller
{
    public function generar($id)
    {
        // 1. Cargar la cabecera (Igual que en Triaje)
        $acta = CabeceraMonitoreo::with(['establecimiento', 'user'])->findOrFail($id);
        
        // 2. Buscar en la NUEVA tabla (Diferente a Triaje)
        $monitoreo = MonitoreoModulos::where('cabecera_monitoreo_id', $id)
                                    ->where('modulo_id', 'odontologia')
                                    ->first();

        if (!$monitoreo) {
            return "Aviso: No se encontraron datos en 'mon_monitoreo_modulos' para Odontología. Asegúrese de haber guardado el formulario.";
        }

        // 3. Extraer el contenido JSON
        $data = (object) $monitoreo->contenido;

        // 4. Mapear datos para que la vista Blade los reconozca
        $profesional = isset($data->profesional) ? (object) $data->profesional : null;
        
        $dbCapacitacion = (object)[
            'profesional' => $profesional,
            'recibieron_cap' => $data->recibio_capacitacion ?? '-',
            'institucion_cap' => $data->inst_capacitacion ?? 'N/A',
            'decl_jurada' => $data->firmo_dj ?? '-',
            'comp_confidencialidad' => $data->firmo_confidencialidad ?? '-'
        ];

        $dbInicioLabores = (object)[
            'cant_consultorios' => $data->num_consultorios ?? '-',
            'nombre_consultorio' => $data->denominacion_consultorio ?? '-',
            'fua' => $data->fua ?? '-',
            'referencia' => '-', 
            'receta' => $data->receta ?? '-',
            'orden_laboratorio' => '-' 
        ];

        $dbDni = (object)[
            'tip_dni' => $data->tipo_dni_fisico ?? '-',
            'version_dni' => $data->dnie_version ?? 'N/A',
            'firma_sihce' => $data->dnie_firma_sihce ?? 'N/A',
            'comentarios' => $data->dni_observacion ?? ''
        ];

        $dbDificultad = (object)[
            'insti_comunica' => $data->comunica_a ?? '-',
            'medio_comunica' => $data->medio_soporte ?? '-'
        ];

        $dbFotos = collect($data->foto_evidencia ?? [])->map(function($path) {
            return (object)['url_foto' => $path];
        });

        // El inventario sigue en la tabla de equipos de cómputo
        $dbInventario = EquipoComputo::where('cabecera_monitoreo_id', $id)
                                    ->where('modulo', 'ODONTOLOGIA') 
                                    ->get();

        // 5. Generar PDF
        $pdf = Pdf::loadView('usuario.monitoreo.pdf.odontologia_pdf', compact(
            'acta', 'dbCapacitacion', 'dbInicioLabores', 'dbDni', 'dbInventario', 'dbDificultad', 'dbFotos'
        ));

        return $pdf->setPaper('a4')->stream("Reporte_Odontologia_$id.pdf");
    }
}