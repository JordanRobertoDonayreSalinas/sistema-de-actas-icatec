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

    public function index($id)
    {
        $acta = CabeceraMonitoreo::with('establecimiento')->findOrFail($id);

        if ($acta->tipo_origen !== 'ESPECIALIZADA') {
            return redirect()->route('usuario.monitoreo.modulos', $id)
                ->with('error', 'Este módulo no corresponde al tipo de establecimiento.');
        }

        $detalle = MonitoreoModulos::where('cabecera_monitoreo_id', $id)
                                    ->where('modulo_nombre', 'triaje_esp')
                                    ->first();

        if (!$detalle) {
            $detalle = new MonitoreoModulos();
            $detalle->contenido = []; 
        } else {
            if (is_string($detalle->contenido)) {
                $detalle->contenido = json_decode($detalle->contenido, true) ?? [];
            }
        }

        $equiposFormateados = [];
        if (isset($detalle->contenido['equipos']) && is_array($detalle->contenido['equipos'])) {
            $equiposFormateados = collect($detalle->contenido['equipos'])->map(function($item) {
                return (object) $item;
            });
        } 

        return view('usuario.monitoreo.modulos_especializados.triaje', [
            'acta' => $acta,
            'detalle' => $detalle,
            'equipos' => $equiposFormateados
        ]);
    }

    public function store(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            // 1. Obtener datos base del formulario
            $datosFormulario = $request->input('contenido', []);

            // ----------------------------------------------------------------------
            // LÓGICA ROBUSTA PARA DETECTAR Y GUARDAR PROFESIONAL (RRHH)
            // ----------------------------------------------------------------------
            
            // Paso A: Intentar obtener 'rrhh' directo del request
            $datosRRHH = $request->input('rrhh');

            // Paso B: Si está vacío, buscarlo dentro de 'contenido'
            if (empty($datosRRHH) && isset($datosFormulario['rrhh'])) {
                $datosRRHH = $datosFormulario['rrhh'];
            }

            // Paso C: Procesar si encontramos datos válidos
            if (!empty($datosRRHH) && !empty($datosRRHH['doc'])) {
                
                // Fusionar datosRRHH en datosFormulario para que se persista en el JSON del módulo
                $datosFormulario['rrhh'] = $datosRRHH;

                // Debug: Verificamos en logs qué llega (Revisar storage/logs/laravel.log)
                Log::info('Guardando Profesional Triaje:', $datosRRHH);

                // Paso D: Actualizar Tabla Maestra 'mon_profesionales'
                // NOTA: Asegúrate que tu tabla se llama 'mon_profesionales' y la columna DNI es 'doc' o 'dni'
                DB::table('mon_profesionales')->updateOrInsert(
                    ['doc' => $datosRRHH['doc']], // Clave: Buscamos por el número de documento
                    [
                        'tipo_doc'          => $datosRRHH['tipo_doc'] ?? 'DNI',
                        'nombres'           => mb_strtoupper($datosRRHH['nombres'] ?? '', 'UTF-8'),
                        'apellido_paterno'  => mb_strtoupper($datosRRHH['apellido_paterno'] ?? '', 'UTF-8'),
                        'apellido_materno'  => mb_strtoupper($datosRRHH['apellido_materno'] ?? '', 'UTF-8'),
                        'cargo'             => mb_strtoupper($datosRRHH['cargo'] ?? '', 'UTF-8'), // <--- CAMPO CARGO
                        'institucion'       => mb_strtoupper($datosRRHH['institucion'] ?? '', 'UTF-8'),
                        'updated_at'        => now(),
                    ]
                );
            }
            // ----------------------------------------------------------------------

            // 2. Gestionar el registro del Módulo
            $registro = MonitoreoModulos::where('cabecera_monitoreo_id', $id)
                                        ->where('modulo_nombre', 'triaje_esp')
                                        ->first();

            $contenidoActual = [];

            if (!$registro) {
                $registro = new MonitoreoModulos();
                $registro->cabecera_monitoreo_id = $id;
                $registro->modulo_nombre = 'triaje_esp';
            } else {
                $contenidoActual = json_decode($registro->contenido, true) ?? [];
            }

            // 3. Procesar Imagen
            if ($request->hasFile('foto_evidencia')) {
                $request->validate([
                    'foto_evidencia' => 'image|mimes:jpeg,png,jpg|max:10240'
                ]);

                if (!empty($contenidoActual['foto_evidencia'])) {
                    if (Storage::disk('public')->exists($contenidoActual['foto_evidencia'])) {
                        Storage::disk('public')->delete($contenidoActual['foto_evidencia']);
                    }
                }

                $path = $request->file('foto_evidencia')->store('evidencias_csmc/triaje', 'public');
                $datosFormulario['foto_evidencia'] = $path;
            } else {
                if (!empty($contenidoActual['foto_evidencia'])) {
                    $datosFormulario['foto_evidencia'] = $contenidoActual['foto_evidencia'];
                }
            }

            // 4. Procesar Equipos
            if ($request->has('equipos')) {
                $datosFormulario['equipos'] = $request->input('equipos');
            } else {
                $datosFormulario['equipos'] = [];
            }

            // 5. Guardar
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