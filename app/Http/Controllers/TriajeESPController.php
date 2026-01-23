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
     * Lista de equipos requeridos para Triaje en CSMC (Referencia interna).
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

        // 2. Validación de seguridad: solo actas ESPECIALIZADAS pueden entrar aquí
        if ($acta->tipo_origen !== 'ESPECIALIZADA') {
            return redirect()->route('usuario.monitoreo.modulos', $id)
                ->with('error', 'Este módulo no corresponde al tipo de establecimiento.');
        }

        // 3. Buscar datos existentes del módulo (Clave: triaje_esp)
        $detalle = MonitoreoModulos::where('cabecera_monitoreo_id', $id)
                                    ->where('modulo_nombre', 'triaje_esp')
                                    ->first();

        // 4. Si no existe, inicializamos vacío
        if (!$detalle) {
            $detalle = new MonitoreoModulos();
            $detalle->contenido = []; 
        } else {
            // Aseguramos que sea array
            if (is_string($detalle->contenido)) {
                $detalle->contenido = json_decode($detalle->contenido, true) ?? [];
            }
        }

        // 5. Preparar equipos para la vista (Convertir array a objetos para Blade)
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

    /**
     * Guarda la información del módulo.
     * Ruta: POST /usuario/monitoreo/modulo/triaje-especializada/{id}
     */
    public function store(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            // 1. Recoger datos base del formulario (array 'contenido')
            $datosFormulario = $request->input('contenido', []);

            // ----------------------------------------------------------------------
            // LÓGICA DE RRHH (PROFESIONAL)
            // ----------------------------------------------------------------------
            // Los datos del profesional vienen en un array aparte 'rrhh' en el request
            $datosRRHH = $request->input('rrhh');

            // Si no vino suelto, buscamos si se coló dentro de 'contenido'
            if (empty($datosRRHH) && isset($datosFormulario['rrhh'])) {
                $datosRRHH = $datosFormulario['rrhh'];
            }

            // Si tenemos datos válidos del profesional (al menos el documento)
            if (!empty($datosRRHH) && !empty($datosRRHH['doc'])) {
                
                // A. Fusionar en $datosFormulario para guardar en el JSON del módulo
                $datosFormulario['rrhh'] = $datosRRHH;

                // B. Actualizar Tabla Maestra 'mon_profesionales' para el autocompletado
                // NOTA: Se eliminó 'institucion' porque no existe en la tabla de la BD.
                DB::table('mon_profesionales')->updateOrInsert(
                    ['doc' => $datosRRHH['doc']], // Clave única (según tu log es 'doc')
                    [
                        'tipo_doc'          => $datosRRHH['tipo_doc'] ?? 'DNI',
                        'nombres'           => mb_strtoupper($datosRRHH['nombres'] ?? '', 'UTF-8'),
                        'apellido_paterno'  => mb_strtoupper($datosRRHH['apellido_paterno'] ?? '', 'UTF-8'),
                        'apellido_materno'  => mb_strtoupper($datosRRHH['apellido_materno'] ?? '', 'UTF-8'),
                        'cargo'             => mb_strtoupper($datosRRHH['cargo'] ?? '', 'UTF-8'), // Guardamos el CARGO
                        // 'institucion'    => ... (ELIMINADO PARA EVITAR ERROR SQL 1054)
                        'updated_at'        => now(),
                    ]
                );
            }
            // ----------------------------------------------------------------------

            // 2. Buscar o Crear registro en monitoreo_modulos
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

            // 3. Procesar Imagen de Evidencia
            if ($request->hasFile('foto_evidencia')) {
                $request->validate([
                    'foto_evidencia' => 'image|mimes:jpeg,png,jpg|max:10240'
                ]);

                // Borrar foto anterior si existe
                if (!empty($contenidoActual['foto_evidencia'])) {
                    if (Storage::disk('public')->exists($contenidoActual['foto_evidencia'])) {
                        Storage::disk('public')->delete($contenidoActual['foto_evidencia']);
                    }
                }

                $path = $request->file('foto_evidencia')->store('evidencias_csmc/triaje', 'public');
                $datosFormulario['foto_evidencia'] = $path;
            } else {
                // Mantener foto anterior si no se subió una nueva
                if (!empty($contenidoActual['foto_evidencia'])) {
                    $datosFormulario['foto_evidencia'] = $contenidoActual['foto_evidencia'];
                }
            }

            // 4. Procesar lista de Equipos
            if ($request->has('equipos')) {
                $datosFormulario['equipos'] = $request->input('equipos');
            } else {
                $datosFormulario['equipos'] = [];
            }

            // 5. Guardar JSON final en la BD
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