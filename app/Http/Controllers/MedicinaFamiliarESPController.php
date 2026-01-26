<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CabeceraMonitoreo;
use App\Models\MonitoreoModulos;
use App\Models\EquipoComputo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Profesional;

class MedicinaFamiliarESPController extends Controller
{
    public function index($id)
    {
        $monitoreo = CabeceraMonitoreo::with('establecimiento')->findOrFail($id);

        if ($monitoreo->tipo_origen !== 'ESPECIALIZADA') {
            return redirect()->route('usuario.monitoreo.modulos', $id)
                ->with('error', 'Este módulo no corresponde al tipo de establecimiento.');
        }

        // Recuperar equipos
        $equipos = EquipoComputo::where('cabecera_monitoreo_id', $id)
                                ->where('modulo', 'sm_med_familiar')
                                ->get();

        $detalle = MonitoreoModulos::where('cabecera_monitoreo_id', $id)
                                    ->where('modulo_nombre', 'sm_med_familiar')
                                    ->first();

        // Si no existe, creamos una instancia vacía
        if (!$detalle) {
            $detalle = new MonitoreoModulos();
            $detalle->contenido = [];
        }

        // TRANSFORMACIÓN DE DATOS (BD -> VISTA)
        if ($detalle && !empty($detalle->contenido)) {
            $dbData = $detalle->contenido;
            
            $defaults = [
                'dificultades' => ['comunica' => null, 'medio' => null],
                'profesional' => [],
                'detalle_dni' => [],
                'detalle_capacitacion' => []
            ];

            $viewData = array_replace_recursive($defaults, $dbData);

            // 1. Datos Generales
            $viewData['fecha'] = $dbData['fecha'] ?? null;
            $consultorio = $dbData['detalle_consultorio'] ?? [];
            $viewData['turno'] = $consultorio['turno'] ?? null;
            $viewData['num_ambientes'] = $consultorio['num_ambientes'] ?? null;
            $viewData['denominacion_ambiente'] = $consultorio['denominacion_ambiente'] ?? null;

            // 2. Profesional (Array directo)
            $viewData['profesional'] = $dbData['profesional'] ?? [];

            // 3. Detalle DNI (Aplanamos para los inputs)
            $dni = $dbData['detalle_dni'] ?? [];
            $viewData['tipo_dni_fisico']  = $dni['tipo_dni_fisico'] ?? null;
            $viewData['dnie_version']     = $dni['dnie_version'] ?? null;
            $viewData['dnie_firma_sihce'] = $dni['dnie_firma_sihce'] ?? null;
            $viewData['dni_observacion']  = $dni['dni_observacion'] ?? null;

            // 4. Capacitación (Adaptador para Componente 4)
            $cap = $dbData['detalle_capacitacion'] ?? [];
            $viewData['recibio_capacitacion'] = $cap['recibio_capacitacion'] ?? null;
            $viewData['inst_capacitacion']    = $cap['inst_capacitacion'] ?? null;
            // Traducción para Componente AlpineJS
            $viewData['recibieron_cap']       = $cap['recibio_capacitacion'] ?? null;
            $viewData['institucion_cap']      = $cap['inst_capacitacion'] ?? null;

            // 5. Dificultades / Soporte (Adaptador para Componente 6)
            $diff = $dbData['dificultades'] ?? [];
            $viewData['dificultades'] = $diff;
            // Inyección de propiedades directas para Componente 6
            $detalle->dificultad_comunica_a = $diff['comunica'] ?? null;
            $detalle->dificultad_medio_uso  = $diff['medio'] ?? null;

            // 6. Evidencia y Comentarios
            $viewData['comentario_esp'] = $dbData['comentario_esp'] ?? null;
            $viewData['foto_evidencia'] = $dbData['foto_evidencia'] ?? [];
            
            // Traducción para Componente 7 (Foto única visual)
            $evidencia = $viewData['foto_evidencia'];
            $viewData['foto_url_esp'] = is_array($evidencia) ? ($evidencia[0] ?? null) : $evidencia;

            // Asignamos la data aplanada a memoria para la vista
            $detalle->contenido = $viewData;
        }

        $data = is_array($detalle->contenido) ? $detalle->contenido : [];

        return view('usuario.monitoreo.modulos_especializados.medicina_familiar', compact('monitoreo', 'data', 'equipos', 'detalle'));
    }

    public function store(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $monitoreo = CabeceraMonitoreo::findOrFail($id);
            $modulo = 'sm_med_familiar';

            // 1. RECIBIMOS LOS DATOS (Estructura Plana del Formulario)
            $input = $request->input('contenido', []);

            // ---------------------------------------------------------
            // FUSIÓN DE COMPONENTES EXTERNOS (Capacitación y Dificultades)
            // ---------------------------------------------------------
            if ($request->has('capacitacion')) {
                $cap = $request->input('capacitacion');
                $input['recibio_capacitacion'] = $cap['recibieron_cap'] ?? null;
                $input['inst_capacitacion']    = $cap['institucion_cap'] ?? null;
            }
            if ($request->has('dificultades')) {
                $input['dificultades'] = $request->input('dificultades');
            }

            // ---------------------------------------------------------
            // REGLAS DE NEGOCIO (Limpieza de Datos)
            // ---------------------------------------------------------
            
            // Regla SIHCE = NO
            $usaSihce = $input['profesional']['cuenta_sihce'] ?? 'NO';
            if ($usaSihce === 'NO') {
                $input['recibio_capacitacion'] = null;
                $input['inst_capacitacion']    = null;
                $input['dificultades']         = ['comunica' => null, 'medio' => null];
                $input['profesional']['firmo_dj'] = null;
                $input['profesional']['firmo_confidencialidad'] = null;
            }

            // Regla NO Capacitación
            if (($input['recibio_capacitacion'] ?? '') === 'NO') {
                $input['inst_capacitacion'] = null;
            }

            // Regla Documento != DNI
            $tipoDoc = $input['profesional']['tipo_doc'] ?? '';
            if ($tipoDoc !== 'DNI') {
                $input['tipo_dni_fisico']  = null;
                $input['dnie_version']     = null;
                $input['dnie_firma_sihce'] = null;
                $input['dni_observacion']  = null;
            }

            // Regla DNI Azul
            if (($input['tipo_dni_fisico'] ?? '') === 'AZUL') {
                $input['dnie_version']     = null;
                $input['dnie_firma_sihce'] = null;
            }

            // ---------------------------------------------------------
            // CONSTRUCCIÓN DE LA ESTRUCTURA JERÁRQUICA (JSON ESTRUCTURADO)
            // ---------------------------------------------------------
            $structuredData = [
                "fecha" => $input['fecha'] ?? date('Y-m-d'),
                
                "detalle_consultorio" => [
                    "turno" => $input['turno'] ?? null,
                    "num_ambientes" => $input['num_ambientes'] ?? null,
                    "denominacion_ambiente" => $input['denominacion_ambiente'] ?? null
                ],
                
                "profesional" => [
                    "doc" => $input['profesional']['doc'] ?? null,
                    "tipo_doc" => $input['profesional']['tipo_doc'] ?? null,
                    "nombres" => $input['profesional']['nombres'] ?? null,
                    "apellido_paterno" => $input['profesional']['apellido_paterno'] ?? null,
                    "apellido_materno" => $input['profesional']['apellido_materno'] ?? null,
                    "email" => $input['profesional']['email'] ?? null,
                    "telefono" => $input['profesional']['telefono'] ?? null,
                    "cargo" => $input['profesional']['cargo'] ?? null,
                    "cuenta_sihce" => $input['profesional']['cuenta_sihce'] ?? null,
                    "firmo_dj" => $input['profesional']['firmo_dj'] ?? null,
                    "firmo_confidencialidad" => $input['profesional']['firmo_confidencialidad'] ?? null
                ],
                
                "detalle_dni" => [
                    "tipo_dni_fisico" => $input['tipo_dni_fisico'] ?? null,
                    "dnie_version" => $input['dnie_version'] ?? null,
                    "dnie_firma_sihce" => $input['dnie_firma_sihce'] ?? null,
                    "dni_observacion" => $input['dni_observacion'] ?? null
                ],
                
                "dificultades" => [
                    "comunica" => $input['dificultades']['comunica'] ?? null,
                    "medio" => $input['dificultades']['medio'] ?? null
                ],
                
                "detalle_capacitacion" => [
                    "recibio_capacitacion" => $input['recibio_capacitacion'] ?? null,
                    "inst_capacitacion" => $input['inst_capacitacion'] ?? null
                ],
                
                "comentario_esp" => $request->input('comentario_esp') ?? ($input['comentario_esp'] ?? null),
                "foto_evidencia" => [] // Se llenará más abajo
            ];

            // ---------------------------------------------------------
            // NORMALIZACIÓN DE TEXTO (Mayúsculas)
            // ---------------------------------------------------------
            array_walk_recursive($structuredData, function (&$value, $key) {
                if (is_string($value)) {
                    if ($key === 'email' || str_contains($value, 'evidencias_')) return; 
                    $value = mb_strtoupper(trim($value), 'UTF-8');
                }
            });

            // ---------------------------------------------------------
            // SINCRONIZACIÓN DE PROFESIONALES (Tabla Externa)
            // ---------------------------------------------------------
            if (!empty($structuredData['profesional']['doc'])) {
                Profesional::updateOrCreate(
                    ['doc' => trim($structuredData['profesional']['doc'])],
                    [
                        'tipo_doc'         => $structuredData['profesional']['tipo_doc'] ?? 'DNI',
                        'apellido_paterno' => $structuredData['profesional']['apellido_paterno'],
                        'apellido_materno' => $structuredData['profesional']['apellido_materno'],
                        'nombres'          => $structuredData['profesional']['nombres'],
                        'email'            => strtolower($structuredData['profesional']['email']),
                        'telefono'         => $structuredData['profesional']['telefono'],
                        'cargo'            => $structuredData['profesional']['cargo'],
                    ]
                );
            }

            // ---------------------------------------------------------
            // GESTIÓN DE EQUIPOS (Tabla Externa)
            // ---------------------------------------------------------
            EquipoComputo::where('cabecera_monitoreo_id', $id)->where('modulo', $modulo)->delete();
            if ($request->has('equipos') && is_array($request->equipos)) {
                foreach ($request->equipos as $eq) {
                    if (!empty($eq['descripcion'])) {
                        EquipoComputo::create([
                            'cabecera_monitoreo_id' => $id,
                            'modulo'      => $modulo,
                            'descripcion' => mb_strtoupper(trim($eq['descripcion']), 'UTF-8'),
                            'cantidad'    => (int)($eq['cantidad'] ?? 1),
                            'estado'      => $eq['estado'] ?? 'OPERATIVO',
                            'nro_serie'   => isset($eq['nro_serie']) ? mb_strtoupper(trim($eq['nro_serie']), 'UTF-8') : null,
                            'propio'      => $eq['propio'] ?? 'SERVICIO',
                            'observacion' => isset($eq['observacion']) ? mb_strtoupper(trim($eq['observacion']), 'UTF-8') : null,
                        ]);
                    }
                }
            }

            // ---------------------------------------------------------
            // GESTIÓN DE FOTOS
            // ---------------------------------------------------------
            $registroPrevio = MonitoreoModulos::where('cabecera_monitoreo_id', $id)
                                ->where('modulo_nombre', $modulo)
                                ->first();
            $fotosFinales = [];
            
            if ($registroPrevio && isset($registroPrevio->contenido['foto_evidencia'])) {
                $prev = $registroPrevio->contenido['foto_evidencia'];
                $fotosFinales = is_array($prev) ? $prev : [$prev];
            }
            
            if ($request->hasFile('foto_esp_file')) {
                // Borrar foto anterior
                if (count($fotosFinales) > 0) {
                    foreach ($fotosFinales as $pathViejo) {
                        if (Storage::disk('public')->exists($pathViejo)) {
                            Storage::disk('public')->delete($pathViejo);
                        }
                    }
                }
                $file = $request->file('foto_esp_file');
                $path = $file->store('evidencias_esp', 'public');
                $fotosFinales = [$path];
            }
            $structuredData['foto_evidencia'] = $fotosFinales;

            // ---------------------------------------------------------
            // GUARDAR
            // ---------------------------------------------------------
            MonitoreoModulos::updateOrCreate(
                ['cabecera_monitoreo_id' => $id, 'modulo_nombre' => $modulo],
                ['contenido' => $structuredData]
            );

            DB::commit();
            return redirect()->route('usuario.monitoreo.salud_mental_group.index', $id)
                             ->with('success', 'Módulo Medicina Familiar ESP sincronizado correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error Módulo Medicina Familiar ESP (Store) - Acta {$id}: " . $e->getMessage());
            return back()->withErrors(['error' => 'Error al guardar: ' . $e->getMessage()])->withInput();
        }
    }
}
