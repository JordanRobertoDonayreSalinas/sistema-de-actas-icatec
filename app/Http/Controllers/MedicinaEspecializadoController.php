<?php

namespace App\Http\Controllers;

use App\Models\Acta;
use App\Models\MonitoreoModulos;
use Illuminate\Http\Request;

class MedicinaEspecializadoController extends Controller
{
    public function index($actaId)
    {
        // 1. Recuperar el Acta
        $acta = Acta::findOrFail($actaId);

        // 2. BUSCAR O CREAR EL DETALLE
        $detalle = MonitoreoModulos::where('cabecera_monitoreo_id', $actaId)
            ->where('modulo_nombre', 'sm_medicina_general')
            ->firstOrNew();

        // Si es nuevo, inicializamos contenido como array vacío
        if ($detalle->contenido === null) {
            $detalle->contenido = [];
        }

        // 3. RECUPERAR EQUIPOS DESDE LA BD
        $equipos = $detalle->contenido['equipos'] ?? [];

        // 4. RETORNO DE LA VISTA
        // Pasamos 'detalle' (para los inputs normales) y 'equipos' (para la tabla dinámica)
        return view('usuario.monitoreo.modulos_especializados.medicina_especializado', compact('acta', 'equipos', 'detalle'));
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
                ->where('modulo_nombre', 'sm_medicina_general')->first();

            if ($anterior && isset($anterior->contenido['comentarios']['foto'])) {
                $contenido['comentarios']['foto'] = $anterior->contenido['comentarios']['foto'];
            }
        }

        // 5. GUARDAR
        MonitoreoModulos::updateOrCreate(
            [
                'cabecera_monitoreo_id' => $actaId,
                'modulo_nombre' => 'sm_medicina_general'
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
}
