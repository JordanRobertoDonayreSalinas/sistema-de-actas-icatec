<?php

namespace App\Http\Controllers;

use App\Models\CabeceraMonitoreo;
use App\Models\EquipoComputo;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class PlanificacionPdfController extends Controller
{
    private $modulo = 'planificacion_familiar';

    public function generar($id)
    {
        $acta = CabeceraMonitoreo::with(['establecimiento', 'user'])->findOrFail($id);

        $detalle = DB::table('mon_detalle_modulos')
                    ->where('cabecera_monitoreo_id', $id)
                    ->where('modulo_nombre', $this->modulo)
                    ->first();

        // 1. Cargamos equipos de la tabla (Sincronizado previamente en el Store)
        $equipos = EquipoComputo::where('cabecera_monitoreo_id', $id)
                                ->where('modulo', $this->modulo)
                                ->get();

        if ($detalle) {
            $detalle->contenido = json_decode($detalle->contenido, true);
            
            // 2. Si la tabla está vacía, extraemos del JSON (Respaldo)
            if ($equipos->isEmpty() && isset($detalle->contenido['equipos_data'])) {
                $equipos = collect($detalle->contenido['equipos_data'])->map(function($item) {
                    return (object) [
                        'descripcion' => $item['descripcion'] ?? 'N/A',
                        'cantidad'    => $item['cantidad'] ?? 1,
                        'estado'      => $item['estado'] ?? 'N/A',
                        'nro_serie'   => $item['nro_serie'] ?? null,
                        'observacion' => $item['observaciones'] ?? '', // Nota: Usamos observacion (singular) para el objeto
                        'propio'      => $item['propiedad'] ?? ($item['propio'] ?? 'ESTABLECIMIENTO')
                    ];
                });
            }
        }

        // 3. Extraemos los datos del contenido para acceso directo en el Blade
        $datos = $detalle ? $detalle->contenido : [];

        // 4. Mapeo explícito de los nuevos campos para evitar errores de índices nulos en el PDF
        $identidad = $datos['dni_firma'] ?? [
            'tipo_dni' => 'N/A',
            'version_dnie' => 'N/A',
            'firma_digital_sihce' => 'no'
        ];

        $documentacion = $datos['documentacion'] ?? [
            'declaracion_jurada' => 'no',
            'compromiso_confidencialidad' => 'no'
        ];

        

        $pdf = Pdf::loadView('usuario.monitoreo.pdf.planificacion_familiar_pdf', [
            'acta' => $acta,
            'detalle' => $detalle,
            'equipos' => $equipos,
            'datos' => $datos,
            'identidad' => $identidad,     // Nueva variable para el Blade
            'documentacion' => $documentacion // Nueva variable para el Blade
        ])->setPaper('a4', 'portrait');

        // 2. CONFIGURACIÓN DEL MOTOR DOMPDF (AQUÍ VAN LAS OPCIONES)
            $pdf->setOption([
                'isPhpEnabled'      => true,    // <--- ESTA ES LA LÍNEA QUE NECESITAS
                'isRemoteEnabled'   => true,    // Para cargar imágenes externas/storage
                'chroot'            => base_path(),
            ]);

        return $pdf->stream("11_PFamiliar_Acta_NOESP_{$acta->numero_acta}.pdf");
    }
}
