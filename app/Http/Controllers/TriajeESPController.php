<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CabeceraMonitoreo;
use App\Models\MonitoreoModulos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TriajeESPController extends Controller
{
    /**
     * Lista de equipos requeridos para Triaje en CSMC.
     */
    private $listaEquipos = [
        'BALANZA DE PIE CON TALLÍMETRO (ADULTO)',
        'BALANZA PEDIÁTRICA (Si aplica)',
        'TENSIÓMETRO ANEROIDE ADULTO',
        'TENSIÓMETRO PEDIÁTRICO (Si aplica)',
        'ESTETOSCOPIO ADULTO',
        'ESTETOSCOPIO PEDIÁTRICO (Si aplica)',
        'TERMÓMETRO CLÍNICO DIGITAL',
        'OXÍMETRO DE PULSO',
        'CINTA MÉTRICA FLEXIBLE',
        'LINTERNA PARA EXAMEN CLÍNICO',
        'CAMILLA DE EXAMEN CLÍNICO',
        'ESCALINATA DE DOS PELDAÑOS',
        'COMPUTADORA / LAPTOP',
        'IMPRESORA MULTIFUNCIONAL'
    ];

    /**
     * Muestra el formulario de "Triaje" específico para CSMC.
     * Ruta: GET /usuario/monitoreo/modulo/triaje-especializada/{id}
     */
    public function index($id)
    {
        // 1. Obtener la cabecera del monitoreo
        $acta = CabeceraMonitoreo::with('establecimiento')->findOrFail($id);

        // 2. Validación de seguridad
        if ($acta->tipo_origen !== 'ESPECIALIZADA') {
            return redirect()->route('usuario.monitoreo.modulos', $id)
                ->with('error', 'Este módulo no corresponde al tipo de establecimiento.');
        }

        // 3. Buscar datos existentes (Clave: triaje_esp)
        $detalle = MonitoreoModulos::where('cabecera_monitoreo_id', $id)
                                    ->where('modulo_nombre', 'triaje_esp')
                                    ->first();

        // 4. Si no existe, creamos una instancia vacía
        if (!$detalle) {
            $detalle = new MonitoreoModulos();
            $detalle->contenido = []; 
        } else {
            if (is_string($detalle->contenido)) {
                $detalle->contenido = json_decode($detalle->contenido, true) ?? [];
            }
        }

        // 5. SOLUCIÓN DEL ERROR: Transformar Strings a Objetos
        // El componente x-tabla-equipos espera objetos con propiedad ->descripcion
        $equiposFormateados = collect($this->listaEquipos)->map(function($nombre, $index) {
            return (object) [
                'id' => $index + 1, // Generamos un ID virtual
                'descripcion' => $nombre,
                'estado' => 'OPERATIVO' // Estado por defecto visual
            ];
        });

        // 6. Retornar la vista
        return view('usuario.monitoreo.modulos_especializados.triaje', [
            'acta' => $acta,
            'detalle' => $detalle,
            'equipos' => $equiposFormateados // Enviamos la lista convertida
        ]);
    }

    /**
     * Guarda la información del módulo.
     * Ruta: POST /usuario/monitoreo/modulo/triaje-especializada/{id}
     */
    public function store(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            // 1. Obtener datos básicos del Request
            $datosFormulario = $request->input('contenido', []);

            // 2. Buscar si ya existe el registro
            $registro = MonitoreoModulos::where('cabecera_monitoreo_id', $id)
                                        ->where('modulo_nombre', 'triaje_esp')
                                        ->first();

            $contenidoActual = [];

            // 3. Instanciar o recuperar
            if (!$registro) {
                $registro = new MonitoreoModulos();
                $registro->cabecera_monitoreo_id = $id;
                $registro->modulo_nombre = 'triaje_esp';
            } else {
                $contenidoActual = json_decode($registro->contenido, true) ?? [];
            }

            // 4. Procesar Imagen (foto_evidencia)
            if ($request->hasFile('foto_evidencia')) {
                $request->validate([
                    'foto_evidencia' => 'image|mimes:jpeg,png,jpg|max:10240' // 10MB Máx
                ]);

                // Borrar anterior si existe
                if (!empty($contenidoActual['foto_evidencia'])) {
                    if (Storage::disk('public')->exists($contenidoActual['foto_evidencia'])) {
                        Storage::disk('public')->delete($contenidoActual['foto_evidencia']);
                    }
                }

                // Guardar nueva
                $path = $request->file('foto_evidencia')->store('evidencias_csmc/triaje', 'public');
                $datosFormulario['foto_evidencia'] = $path;
            } else {
                // Mantener la foto anterior si no se subió una nueva
                if (!empty($contenidoActual['foto_evidencia'])) {
                    $datosFormulario['foto_evidencia'] = $contenidoActual['foto_evidencia'];
                }
            }

            // 5. Procesar Equipos
            // Capturamos el array de equipos enviado por el componente
            if ($request->has('equipos')) {
                $datosFormulario['equipos'] = $request->input('equipos');
            }

            // 6. Guardar JSON
            $registro->contenido = json_encode($datosFormulario, JSON_UNESCAPED_UNICODE);
            $registro->save();

            DB::commit();

            return redirect()
                ->route('usuario.monitoreo.modulos', $id)
                ->with('success', 'Módulo de Triaje CSMC guardado correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error guardando Triaje CSMC: " . $e->getMessage());
            
            return back()
                ->with('error', 'Error al guardar: ' . $e->getMessage())
                ->withInput();
        }
    }
}