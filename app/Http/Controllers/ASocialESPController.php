<?php

namespace App\Http\Controllers;

use App\Models\CabeceraMonitoreo;
use App\Models\MonitoreoModulos;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ASocialESPController extends Controller
{
    public function index($actaId)
    {
        // 1. Recuperar el Acta
        $acta = CabeceraMonitoreo::findOrFail($actaId);

        // 2. BUSCAR O CREAR EL DETALLE
        $detalle = MonitoreoModulos::where('cabecera_monitoreo_id', $actaId)
            ->where('modulo_nombre', 'sm_servicio_social')
            ->firstOrNew();

        // Si es nuevo, inicializamos contenido como array vacío
        if ($detalle->contenido === null) {
            $detalle->contenido = [];
        }

        // 3. RECUPERAR EQUIPOS DESDE LA BD
        $equipos = $detalle->contenido['equipos'] ?? [];

        // 4. RETORNO DE LA VISTA
        // Pasamos 'detalle' (para los inputs normales) y 'equipos' (para la tabla dinámica)
        return view('usuario.monitoreo.modulos_especializados.asocial_especializado', compact('acta', 'equipos', 'detalle'));
    }

    public function store(Request $request, $actaId)
    {
        // 1. Recoger 'contenido' (Datos de componentes normales)
        $contenido = $request->input('contenido', []);

        // 2. FUSIONAR EQUIPOS (Componente 5 - JS)
        if ($request->has('equipos')) {
            $contenido['equipos'] = array_values($request->input('equipos'));
        }

        // 3. FUSIONAR COMENTARIOS (Componente 7)
        if ($request->has('comentario_esp')) {
            $contenido['comentarios']['texto'] = $request->input('comentario_esp');
        }

        // 4. FUSIONAR FOTO (Componente 7 - Archivo)
        if ($request->hasFile('foto_esp_file')) {
            $path = $request->file('foto_esp_file')->store('evidencias_esp', 'public');
            $contenido['comentarios']['foto'] = $path;
        } else {
            // Mantener foto anterior si existe
            $anterior = MonitoreoModulos::where('cabecera_monitoreo_id', $actaId)
                ->where('modulo_nombre', 'sm_servicio_social')->first();

            if ($anterior && isset($anterior->contenido['comentarios']['foto'])) {
                $contenido['comentarios']['foto'] = $anterior->contenido['comentarios']['foto'];
            }
        }

        // 5. GUARDAR
        MonitoreoModulos::updateOrCreate(
            [
                'cabecera_monitoreo_id' => $actaId,
                'modulo_nombre' => 'sm_servicio_social'
            ],
            [
                'contenido' => $contenido
            ]
        );

        // 6. REDIRECCIÓN CORREGIDA
        return redirect()
            ->route('usuario.monitoreo.salud_mental_group.index', $actaId)
            ->with('success', 'Ficha guardada correctamente.');
    }

    public function generar($id)
    {
        // 1. Recuperar datos
        $acta = CabeceraMonitoreo::findOrFail($id);

        $detalle = MonitoreoModulos::where('cabecera_monitoreo_id', $id)
            ->where('modulo_nombre', 'sm_servicio_social')
            ->first();

        // Evitar error si no hay detalle guardado aún
        if (!$detalle) {
            return back()->with('error', 'Primero debe guardar la ficha antes de generar el PDF.');
        }

        // 2. Procesar la Imagen de Evidencia (Si existe)
        // DomPDF necesita base64 o ruta absoluta para mostrar imágenes
        $imagenesData = [];

        // Buscamos la ruta en tu estructura JSON: comentarios -> foto
        $rutaFoto = $detalle->contenido['comentarios']['foto'] ?? null;

        if ($rutaFoto) {
            // Generamos la ruta completa en el disco
            $path = public_path('storage/' . $rutaFoto);

            if (file_exists($path)) {
                $type = pathinfo($path, PATHINFO_EXTENSION);
                $data = file_get_contents($path);
                // Convertimos a Base64 para incrustar en el PDF
                $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
                $imagenesData[] = $base64;
            }
        }

        // 3. Generar PDF
        // Asegúrate de que la ruta de la vista coincida con tu carpeta real
        $pdf = Pdf::loadView('usuario.monitoreo.pdf_especializados.asocial_especializado_pdf', [
            'acta' => $acta,
            'detalle' => $detalle,
            'imagenesData' => $imagenesData
        ]);

        // B. Configuramos papel
        $pdf->setPaper('A4', 'portrait');

        // C. Renderizamos en memoria
        $pdf->render();

        $dom_pdf = $pdf->getDomPDF();
        $canvas = $dom_pdf->getCanvas();

        // Configuración de fuente
        $fontMetrics = $dom_pdf->getFontMetrics();
        $font = $fontMetrics->get_font("Helvetica", "bold");

        // --- CAMBIOS DE AJUSTE FINO ---
        $size = 8; // Igualamos a 8pt del CSS para que se vean idénticos
        $color = [0.392, 0.455, 0.545];

        $w = $canvas->get_width();
        $h = $canvas->get_height();

        // Coordenadas:
        $x = $w - 75;

        // AQUÍ ESTÁ EL TRUCO VERTICAL:
        // Antes tenías ($h - 43). Al poner ($h - 49), "restamos más",
        // lo que hace que el texto SUBA unos milímetros en la hoja.
        $y = $h - 49;

        $canvas->page_text($x, $y, "PAG. {PAGE_NUM} / {PAGE_COUNT}", $font, $size, $color);

        return $pdf->stream('Acta_Asistenta_Social_' . $acta->numero_acta . '.pdf');
    }
}
