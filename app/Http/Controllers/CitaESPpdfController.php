<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\CabeceraMonitoreo;
use App\Models\MonitoreoModulos;
use stdClass;
use Carbon\Carbon;

class CitaESPpdfController extends Controller
{
    public function generar($id)
    {
        // 1. Cargar datos generales
        $acta = CabeceraMonitoreo::with(['establecimiento', 'user'])->findOrFail($id);

        // 2. Cargar el JSON centralizado
        $registro = MonitoreoModulos::where('cabecera_monitoreo_id', $id)
                            ->where('modulo_nombre', 'citas_esp')
                            ->first();

        $data = $registro ? json_decode($registro->contenido, true) : [];
        $acta->fecha_validacion = $registro ? $registro->updated_at : null;

        // ---------------------------------------------------------
        // 3. MAPEO DE DATOS (JSON -> Objetos para la Vista)
        // ---------------------------------------------------------

        // A. PROFESIONAL
        $profObj = new stdClass();
        $pData = $data['profesional'] ?? [];
        $profObj->tipo_doc         = $pData['tipo_doc'] ?? '';
        $profObj->doc              = $pData['doc'] ?? '';
        $profObj->nombres          = $pData['nombres'] ?? '';
        $profObj->apellido_paterno = $pData['apellido_paterno'] ?? '';
        $profObj->apellido_materno = $pData['apellido_materno'] ?? '';
        $profObj->cargo            = $pData['cargo'] ?? '';
        $profObj->telefono         = $pData['telefono'] ?? '';
        $profObj->email            = $pData['email'] ?? '';

        // B. INICIO DE LABORES (Componente 1)
        $dbInicioLabores = new stdClass();
        $cont = $data['contenido'] ?? [];
        
        $dbInicioLabores->fecha_registro     = $cont['fecha'] ?? null;
        $dbInicioLabores->turno              = $cont['turno'] ?? null;
        $dbInicioLabores->cant_consultorios  = $cont['num_ambientes'] ?? null;
        $dbInicioLabores->nombre_consultorio = $cont['denominacion_ambiente'] ?? null;
        // Mapeamos el comentario del Componente 7 aquí porque el PDF lo muestra como "Comentarios Generales"
        $dbInicioLabores->comentarios        = $data['comentarios_esp']['comentario_esp'] ?? null;

        // C. CAPACITACIÓN (Componente 4)
        $dbCapacitacion = new stdClass();
        $cData = $data['capacitacion'] ?? [];
        $dbCapacitacion->recibieron_cap  = $cData['recibieron_cap'] ?? 'NO';
        $dbCapacitacion->institucion_cap = $cData['institucion_cap'] ?? '-';
        $dbCapacitacion->profesional     = $profObj; // Vinculamos el profesional

        // D. DNI (Componente 3)
        $dbDni = new stdClass();
        $dbDni->tip_dni     = $cont['tipo_dni_fisico'] ?? null;
        $dbDni->version_dni = $cont['dnie_version'] ?? null;
        $dbDni->firma_sihce = $cont['dnie_firma_sihce'] ?? null;
        $dbDni->comentarios = $cont['dni_observacion'] ?? null;

        // E. INVENTARIO (Componente 5)
        $invArray = $data['inventario'] ?? [];
        $dbInventario = collect($invArray)->map(function($item) {
            $obj = new stdClass();
            $obj->descripcion = $item['descripcion'] ?? '';
            $obj->cantidad    = $item['cantidad'] ?? 1;
            $obj->estado      = $item['estado'] ?? '';
            $obj->propio      = $item['propio'] ?? ''; // Propiedad
            $obj->nro_serie   = $item['nro_serie'] ?? '';
            $obj->observacion = $item['observacion'] ?? '';
            return $obj;
        });

        // F. DIFICULTADES (Componente 6)
        $dbDificultad = new stdClass();
        $difData = $data['contenido']['dificultades'] ?? [];
        $dbDificultad->insti_comunica = $difData['comunica'] ?? null;
        $dbDificultad->medio_comunica = $difData['medio'] ?? null;

        // G. FOTOS (Componente 7)
        // Pasamos la URL directa para manejarla en la vista
        $fotoUrl = $data['comentarios_esp']['foto_url_esp'] ?? null;

        // 4. Generar PDF
        $pdf = Pdf::loadView('usuario.monitoreo.pdf_especializados.citas_pdf', compact(
            'acta', 
            'dbCapacitacion', 
            'dbInventario', 
            'dbDificultad', 
            'dbInicioLabores',
            'dbDni',
            'fotoUrl' // Pasamos la variable nueva
        ));

        $pdf->setOption('isPhpEnabled', true);
        $pdf->setPaper('a4', 'portrait');

        return $pdf->stream('Reporte_Citas_' . $acta->id . '.pdf');
    }
}