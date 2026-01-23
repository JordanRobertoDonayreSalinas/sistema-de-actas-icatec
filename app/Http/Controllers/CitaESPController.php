<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CabeceraMonitoreo;
use App\Models\MonitoreoModulos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use stdClass;

class CitaESPController extends Controller
{
    public function index($id)
    {
        // 1. Validar Cabecera
        $acta = CabeceraMonitoreo::with('establecimiento')->findOrFail($id);
        
        if ($acta->tipo_origen !== 'ESPECIALIZADA') {
            return redirect()->route('usuario.monitoreo.modulos', $id)
                ->with('error', 'Módulo incorrecto.');
        }

        // 2. Recuperar el JSON guardado
        $registro = MonitoreoModulos::where('cabecera_monitoreo_id', $id)
                                    ->where('modulo_nombre', 'citas_esp')
                                    ->first();

        $data = $registro ? json_decode($registro->contenido, true) : [];

        // 3. MAPEO DE DATOS (Para que la vista no se rompa)
        // Creamos un objeto genérico que agrupa toda la data para los componentes Blade
        $dataMap = new stdClass();
        
        // A. Contenido General (Componentes 1 y 3 usan $detalle->contenido['...'])
        $dataMap->contenido = $data['contenido'] ?? [];

        // B. Soporte (Componente 6 usa propiedades directas como $detalle->dificultad_comunica_a)
        $dataMap->dificultad_comunica_a = $data['contenido']['dificultades']['comunica'] ?? null;
        $dataMap->dificultad_medio_uso  = $data['contenido']['dificultades']['medio'] ?? null;

        // C. Comentarios (Componente 7 usa $comentario->comentario_esp)
        $dataMap->comentario_esp = $data['comentarios_esp']['comentario_esp'] ?? null;
        $dataMap->foto_url_esp   = $data['comentarios_esp']['foto_url_esp'] ?? null;

        // 4. DATOS PARA ALPINE JS (Componentes 4 y 5)
        // Estos se pasan directo como Arrays
        $valCapacitacion = $data['capacitacion'] ?? ['recibieron_cap' => 'NO', 'institucion_cap' => ''];
        $valInventario   = $data['inventario'] ?? [];

        return view('usuario.monitoreo.modulos_especializados.citas', compact(
            'acta', 
            'dataMap', 
            'valCapacitacion', 
            'valInventario'
        ));
    }

    public function store(Request $request, $id)
    {
        try {
            // 1. Recuperar registro previo (para no perder la foto si no suben una nueva)
            $registroPrevio = MonitoreoModulos::where('cabecera_monitoreo_id', $id)
                                              ->where('modulo_nombre', 'citas_esp')
                                              ->first();
            
            $jsonPrevio = $registroPrevio ? json_decode($registroPrevio->contenido, true) : [];

            // 2. Lógica de FOTO
            $rutaFoto = $jsonPrevio['comentarios_esp']['foto_url_esp'] ?? null;

            if ($request->hasFile('foto_esp_file')) {
                if ($rutaFoto && Storage::disk('public')->exists($rutaFoto)) {
                    Storage::disk('public')->delete($rutaFoto);
                }
                $rutaFoto = $request->file('foto_esp_file')->store('evidencias_esp', 'public');
            }

            // 3. Armar el JSON ÚNICO
            $contenidoParaJson = [
                'profesional'     => $request->input('profesional', []),
                'contenido'       => $request->input('contenido', []), // Fechas, DNI, Soporte
                'capacitacion'    => $request->input('capacitacion', []),
                'inventario'      => $request->input('inventario', []),
                'comentarios_esp' => [
                    'comentario_esp' => $request->input('comentario_esp'),
                    'foto_url_esp'   => $rutaFoto
                ]
            ];

            // 4. Guardar
            MonitoreoModulos::updateOrCreate(
                [
                    'cabecera_monitoreo_id' => $id,
                    'modulo_nombre'         => 'citas_esp'
                ],
                [
                    'contenido'        => json_encode($contenidoParaJson, JSON_UNESCAPED_UNICODE),
                    'pdf_firmado_path' => null
                ]
            );

            return redirect()
                ->route('usuario.monitoreo.modulos', $id)
                ->with('success', 'Información guardada correctamente.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage())->withInput();
        }
    }
}