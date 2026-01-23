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
        $dataMap = new stdClass();
        
        // A. Contenido General (Componentes 1, 2, 3 y 6)
        // Aseguramos que 'contenido' exista
        $dataMap->contenido = $data['contenido'] ?? [];

        // B. Soporte (Componente 6 - Mapeo específico)
        $dataMap->dificultad_comunica_a = $data['contenido']['dificultades']['comunica'] ?? null;
        $dataMap->dificultad_medio_uso  = $data['contenido']['dificultades']['medio'] ?? null;

        // C. Comentarios (Componente 7)
        $dataMap->comentario_esp = $data['comentarios_esp']['comentario_esp'] ?? null;
        $dataMap->foto_url_esp   = $data['comentarios_esp']['foto_url_esp'] ?? null;

        // 4. DATOS PARA ALPINE/BLADE
        // Capacitación: Recuperamos del JSON o valores por defecto
        $valCapacitacion = $data['capacitacion'] ?? ['recibieron_cap' => 'NO', 'institucion_cap' => ''];
        
        // Inventario: Recuperamos del JSON y lo convertimos a Objetos para que el blade ( ->descripcion ) funcione
        $arrayInventario = $data['inventario'] ?? [];
        $valInventario = [];
        foreach($arrayInventario as $item){
            $obj = new stdClass();
            $obj->descripcion = $item['descripcion'] ?? '';
            $obj->cantidad    = $item['cantidad'] ?? 1;
            $obj->estado      = $item['estado'] ?? 'OPERATIVO';
            $obj->propio      = $item['propio'] ?? 'COMPARTIDO';
            $obj->nro_serie   = $item['nro_serie'] ?? '';
            $obj->observacion = $item['observacion'] ?? '';
            $valInventario[] = $obj;
        }

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
            $registroPrevio = MonitoreoModulos::where('cabecera_monitoreo_id', $id)
                                              ->where('modulo_nombre', 'citas_esp')
                                              ->first();
            
            $jsonPrevio = $registroPrevio ? json_decode($registroPrevio->contenido, true) : [];

            // 1. Lógica de FOTO (Componente 7)
            $rutaFoto = $jsonPrevio['comentarios_esp']['foto_url_esp'] ?? null;

            if ($request->hasFile('foto_esp_file')) {
                if ($rutaFoto && Storage::disk('public')->exists($rutaFoto)) {
                    Storage::disk('public')->delete($rutaFoto);
                }
                $rutaFoto = $request->file('foto_esp_file')->store('evidencias_esp', 'public');
            }

            // 2. Preparar Contenido
            // El componente 2 (Profesional) guarda dentro de name="contenido[profesional]..."
            // así que ya viene dentro del array 'contenido'.
            $contenidoGeneral = $request->input('contenido', []);

            // 3. Armar el JSON ÚNICO
            $contenidoParaJson = [
                'contenido'       => $contenidoGeneral, 
                'capacitacion'    => $request->input('capacitacion', []), // Recibido gracias a los inputs hidden
                'inventario'      => $request->input('equipos', []), // OJO: El componente envía 'equipos', guardamos como 'inventario'
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
                ->with('success', 'Módulo de Citas guardado correctamente.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage())->withInput();
        }
    }
}