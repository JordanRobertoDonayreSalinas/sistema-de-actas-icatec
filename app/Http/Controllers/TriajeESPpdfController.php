<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CabeceraMonitoreo;
use App\Models\MonitoreoModulos;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TriajeESPpdfController extends Controller
{
    /**
     * Helper para encontrar la foto sin importar si el registro es viejo o nuevo.
     */
    private function getFotoPath($contenidoDB)
    {
        // 1. Estructura NUEVA (Array dentro de comentarios_y_evidencias)
        $fotoNew = data_get($contenidoDB, 'comentarios_y_evidencias.foto_evidencia');
        if (!empty($fotoNew)) {
            return is_array($fotoNew) ? ($fotoNew[0] ?? null) : $fotoNew;
        }

        // 2. Estructura ANTIGUA (Raíz)
        $fotoOld = data_get($contenidoDB, 'foto_evidencia');
        if (!empty($fotoOld)) {
            return is_array($fotoOld) ? ($fotoOld[0] ?? null) : $fotoOld;
        }

        return null;
    }

    public function generar($id)
    {
        // 1. Obtener Datos Principales
        // Usamos 'acta' para coincidir con la vista
        $acta = CabeceraMonitoreo::with('establecimiento')->findOrFail($id);
        
        $registro = MonitoreoModulos::where('cabecera_monitoreo_id', $id)
                                   ->where('modulo_nombre', 'triaje_esp')
                                   ->first();

        // 2. Decodificar Contenido JSON
        $contenidoDB = [];
        if ($registro && $registro->contenido) {
            $contenidoDB = is_string($registro->contenido) ? json_decode($registro->contenido, true) : $registro->contenido;
        }
        $contenidoDB = $contenidoDB ?? [];

        // 3. MAPEO DE DATOS (CRÍTICO: La vista triaje_pdf.blade.php espera este array $datos)
        $datos = [];

        // --- DETALLE CONSULTORIO ---
        $grupoDetalle = $contenidoDB['detalle_del_consultorio'] ?? [];
        $datos['fecha']                 = $grupoDetalle['fecha_monitoreo'] ?? ($contenidoDB['fecha'] ?? date('Y-m-d'));
        $datos['turno']                 = $grupoDetalle['turno'] ?? '';
        $datos['num_ambientes']         = $grupoDetalle['num_consultorios'] ?? '';
        $datos['denominacion_ambiente'] = $grupoDetalle['denominacion'] ?? '';

        // --- RRHH ---
        $grupoRRHH = $contenidoDB['datos_del_profesional'] ?? ($contenidoDB['rrhh'] ?? []);
        $datos['rrhh'] = $grupoRRHH;

        // --- DOCUMENTACIÓN ---
        $grupoDoc = $contenidoDB['documentacion_administrativa'] ?? [];
        $datos['rrhh']['cuenta_sihce']           = $grupoDoc['utiliza_sihce'] ?? ($grupoRRHH['cuenta_sihce'] ?? '');
        $datos['rrhh']['firmo_dj']               = $grupoDoc['firmo_dj'] ?? ($grupoRRHH['firmo_dj'] ?? '');
        $datos['rrhh']['firmo_confidencialidad'] = $grupoDoc['firmo_confidencialidad'] ?? ($grupoRRHH['firmo_confidencialidad'] ?? '');

        // --- DNI ---
        $grupoDni = $contenidoDB['detalle_de_dni_y_firma_digital'] ?? ($contenidoDB['uso_del_dnie'] ?? []);
        $datos['tipo_dni_fisico']  = $grupoDni['tipo_dni'] ?? ($grupoDni['tipo_fisico'] ?? ($contenidoDB['tipo_dni_fisico'] ?? ''));
        $datos['dnie_version']     = $grupoDni['version_dnie'] ?? ($grupoDni['version'] ?? ($contenidoDB['dnie_version'] ?? ''));
        $datos['dnie_firma_sihce'] = $grupoDni['firma_digital_sihce'] ?? ($grupoDni['firma_sihce'] ?? ($contenidoDB['dnie_firma_sihce'] ?? ''));
        $datos['dni_observacion']  = $grupoDni['observaciones_dni'] ?? ($grupoDni['observacion'] ?? ($contenidoDB['dni_observacion'] ?? ''));

        // --- CAPACITACIÓN ---
        $grupoCap = $contenidoDB['detalles_de_capacitacion'] ?? [];
        $datos['capacitacion'] = [
            'recibieron_cap'  => $grupoCap['recibio_capacitacion'] ?? ($grupoCap['recibieron_cap'] ?? ''),
            'institucion_cap' => $grupoCap['inst_que_lo_capacito'] ?? ($grupoCap['institucion_cap'] ?? '')
        ];

        // --- SOPORTE ---
        $grupoSoporte = $contenidoDB['soporte'] ?? ($contenidoDB['dificultades'] ?? []);
        $datos['dificultades'] = [
            'comunica' => $grupoSoporte['inst_a_quien_comunica'] ?? ($grupoSoporte['comunica'] ?? ''),
            'medio'    => $grupoSoporte['medio_que_utiliza'] ?? ($grupoSoporte['medio'] ?? '')
        ];

        // --- EQUIPOS ---
        $datos['equipos'] = $contenidoDB['equipos_de_computo'] ?? ($contenidoDB['equipamiento_biomedico_y_mobiliario'] ?? []);

        // --- COMENTARIOS ---
        $grupoEvidencia = $contenidoDB['comentarios_y_evidencias'] ?? [];
        $datos['comentario_esp'] = $grupoEvidencia['comentarios'] ?? ($contenidoDB['comentario_esp'] ?? '');
        
        // 4. PROCESAMIENTO DE IMAGEN (Conversión a Base64 para máxima compatibilidad)
        $relPath = $this->getFotoPath($contenidoDB);
        $fotoBase64 = null;

        if ($relPath && Storage::disk('public')->exists($relPath)) {
            $rutaAbsoluta = storage_path("app/public/{$relPath}");
            $extension = pathinfo($rutaAbsoluta, PATHINFO_EXTENSION);
            
            // Intentamos leer y convertir
            try {
                $dataImg = file_get_contents($rutaAbsoluta);
                if ($dataImg !== false) {
                    $fotoBase64 = 'data:image/' . $extension . ';base64,' . base64_encode($dataImg);
                }
            } catch (\Exception $e) {
                // Si falla, se queda en null y el PDF mostrará "No foto"
            }
        }
        
        // Asignamos el Base64 a la clave que espera la vista
        $datos['foto_path_pdf'] = $fotoBase64;

        // 5. GENERAR PDF
        $pdf = Pdf::loadView('usuario.monitoreo.pdf_especializados.triaje_pdf', [
            'acta'  => $acta,  // ¡AQUÍ ESTÁ LA SOLUCIÓN! Pasamos 'acta', no 'monitoreo'
            'datos' => $datos  // Pasamos el array estructurado que la vista necesita
        ]);

        $pdf->setPaper('a4', 'portrait');

        // 6. INYECCIÓN DEL PIE DE PÁGINA
        $pdf->render();
        $dompdf = $pdf->getDomPDF();
        $canvas = $dompdf->get_canvas();
        
        $h = $canvas->get_height();
        $w = $canvas->get_width();
        
        $fontMetrics = $dompdf->getFontMetrics();
        $font = $fontMetrics->get_font("Helvetica", "normal");
        $size = 8;
        $color = array(0.58, 0.64, 0.72); 

        // Texto Izquierda
        $canvas->page_text(42, $h - 40, "SISTEMA DE ACTAS", $font, $size, $color);

        // Texto Derecha (Paginación)
        $textPag = "PAG. {PAGE_NUM} / {PAGE_COUNT}";
        $widthPag = $fontMetrics->getTextWidth("PAG. 00 / 00", $font, $size); 
        $canvas->page_text($w - 42 - $widthPag, $h - 40, $textPag, $font, $size, $color);

        // Línea divisoria
        $canvas->page_script('
            $pdf->line(42, $pdf->get_height() - 50, $pdf->get_width() - 42, $pdf->get_height() - 50, array(0.88, 0.91, 0.94), 1);
        ');

        return $pdf->stream('03_Triaje_Acta_ESP_' . $acta->numero_acta . '.pdf');
    }
}