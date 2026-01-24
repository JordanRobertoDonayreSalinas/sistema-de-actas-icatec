<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EnfermeriaESPpdfController extends Controller
{
    public function generar($id)
    {
        // 1. Cargar datos generales
        $acta = CabeceraMonitoreo::with(['establecimiento', 'user'])->findOrFail($id);

        // 2. Cargar el JSON guardado
        $registro = MonitoreoModulos::where('cabecera_monitoreo_id', $id)
                            ->where('modulo_nombre', 'enfermeria_esp')
                            ->first();

        // Obtener datos de forma segura
        $data = $registro ? $registro->contenido : [];
        if (is_string($data)) {
            $data = json_decode($data, true);
        }
        if (!is_array($data)) {
            $data = [];
        }

        $acta->fecha_validacion = $registro ? $registro->updated_at : null;

        // ---------------------------------------------------------
        // 3. MAPEO DE DATOS
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
        $profObj->cuenta_sihce     = $pData['utiliza_sihce'] ?? ''; 

        // B. INICIO DE LABORES
        $dbInicioLabores = new stdClass();
        $initData = $data['inicio_labores'] ?? [];
        
        $dbInicioLabores->fecha_registro     = $initData['fecha_registro'] ?? ($data['fecha_registro'] ?? null);
        $dbInicioLabores->turno              = $initData['turno'] ?? ($data['turno'] ?? null);
        $dbInicioLabores->cant_consultorios  = $initData['consultorios'] ?? ($data['num_consultorios'] ?? null);
        $dbInicioLabores->nombre_consultorio = $initData['nombre_consultorio'] ?? ($data['denominacion_consultorio'] ?? null);
        $dbInicioLabores->comentarios        = $initData['comentarios'] ?? ($data['comentarios_generales'] ?? null);

        // C. CAPACITACIÓN
        $dbCapacitacion = new stdClass();
        $dbCapacitacion->recibieron_cap  = $data['recibio_capacitacion'] ?? 'NO';
        $dbCapacitacion->institucion_cap = $data['inst_capacitacion'] ?? '-';
        $dbCapacitacion->profesional     = $profObj; 

        // D. DNI
        $dbDni = new stdClass();
        $dniData = $data['seccion_dni'] ?? [];
        $dbDni->tip_dni     = $dniData['tipo_dni'] ?? null;
        $dbDni->version_dni = $dniData['version_dnie'] ?? null;
        $dbDni->firma_sihce = $dniData['firma_sihce'] ?? null;
        $dbDni->comentarios = $dniData['comentarios'] ?? null;

        // E. INVENTARIO
        $invArray = $data['inventario'] ?? [];
        $dbInventario = collect($invArray)->map(function($item) {
            $item = (array)$item; 
            $obj = new stdClass();
            $obj->descripcion = $item['descripcion'] ?? '';
            $obj->cantidad    = $item['cantidad'] ?? 1;
            $obj->estado      = $item['estado'] ?? '';
            $obj->propio      = $item['propiedad'] ?? ($item['propio'] ?? ''); 
            $obj->nro_serie   = $item['codigo'] ?? ($item['nro_serie'] ?? '');
            $obj->observacion = $item['observacion'] ?? '';
            return $obj;
        });

        // F. DIFICULTADES
        $dbDificultad = new stdClass();
        $dbDificultad->insti_comunica = $data['comunica_a'] ?? null;
        $dbDificultad->medio_comunica = $data['medio_soporte'] ?? null;

        // G. FOTOS
        $fotosArray = $data['foto_evidencia'] ?? [];
        $fotoUrl = (is_array($fotosArray) && count($fotosArray) > 0) ? $fotosArray[0] : null;

        // 4. Generar PDF
        // CORRECCIÓN: Pasamos 'prof' directamente en el array, eliminamos el ->with()
        $pdf = Pdf::loadView('usuario.monitoreo.pdf_especializados.enfermeria_pdf', [
            'acta'            => $acta,
            'dbCapacitacion'  => $dbCapacitacion,
            'dbInventario'    => $dbInventario,
            'dbDificultad'    => $dbDificultad,
            'dbInicioLabores' => $dbInicioLabores,
            'dbDni'           => $dbDni,
            'fotoUrl'         => $fotoUrl,
            'profObj'         => $profObj,
            'prof'            => $profObj // <--- Alias añadido aquí directamente
        ]);

        $pdf->setOption('isPhpEnabled', true);
        $pdf->setPaper('a4', 'portrait');

        return $pdf->stream('Reporte_Enfermeria_' . $acta->id . '.pdf');
    }
}
