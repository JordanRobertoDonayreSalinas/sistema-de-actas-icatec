<?php

namespace App\Http\Controllers;

use App\Models\CabeceraMonitoreo;
use App\Models\EquipoComputo; // Importante
use App\Models\ModuloParto;
use App\Models\MonitoreoModulos;
use App\Models\Profesional; // Importante para la búsqueda
use App\Models\RespuestaEntrevistado; // Importante
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class PartoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($idActa)
    {
        $acta = CabeceraMonitoreo::findOrFail($idActa);
        $registro = ModuloParto::where('monitoreo_id', $idActa)->first();
        return view('usuario.monitoreo.modulos.parto', compact('acta', 'registro'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request, $idActa)
    {
        try {
            // 1. Procesar Fotos (Lógica mantenida)
            $rutasFotos = [];
            if ($request->has('rutas_servidor') && !empty($request->rutas_servidor)) {
                $rutasFotos = json_decode($request->rutas_servidor, true) ?? [];
            }
            if ($request->hasFile('fotos')) {
                foreach ($request->file('fotos') as $foto) {
                    $path = $foto->store('evidencias_parto', 'public');
                    $rutasFotos[] = asset('storage/' . $path);
                }
            }
            $rutasFotos = array_slice($rutasFotos, 0, 2);

            // 2. Extraer Inputs
            $input = $request->input('contenido');

            // =========================================================================
            // PASO 0: LOGICA UNIFICADA DE PROFESIONAL (BUSCAR, CREAR O ACTUALIZAR)
            // =========================================================================
            $dni = $input['personal_dni'] ?? null;

            if ($dni) {
                // A. Busca o crea una instancia vacía
                $profesional = \App\Models\Profesional::firstOrNew(['doc' => $dni]);

                // B. Asignar datos obligatorios
                $profesional->tipo_doc = $input['personal_tipo_doc'] ?? 'DNI';

                // C. Actualizar contacto si vienen datos
                if (!empty($input['personal_correo'])) {
                    $profesional->email = $input['personal_correo'];
                }
                if (!empty($input['personal_celular'])) {
                    $profesional->telefono = $input['personal_celular'];
                }

                // D. Lógica inteligente para Nombres (Solo si se envió nombre)
                if (!empty($input['personal_nombre'])) {
                    $nombreCompleto = mb_strtoupper(trim($input['personal_nombre']), 'UTF-8');

                    // Separamos en partes
                    $partes = explode(' ', $nombreCompleto);
                    $num = count($partes);

                    if ($num >= 3) {
                        // Asume: PATERNO MATERNO NOMBRES
                        $profesional->apellido_paterno = array_shift($partes);
                        $profesional->apellido_materno = array_shift($partes);
                        $profesional->nombres          = implode(' ', $partes);
                    } elseif ($num == 2) {
                        // Asume: PATERNO NOMBRES
                        $profesional->apellido_paterno = $partes[0];
                        $profesional->apellido_materno = '';
                        $profesional->nombres          = $partes[1];
                    } elseif ($num == 1) {
                        $profesional->nombres          = $partes[0];
                    }
                }

                // E. Guardar cambios en tabla maestra (mon_profesionales)
                $profesional->save();
            }

            // =========================================================================
            // PASO 1: Preparar datos para el Módulo (Array Maestro)
            // =========================================================================
            $datosParto = [
                // Datos Generales
                'nombre_consultorio'    => $input['nombre_consultorio'] ?? null,

                // Personal (Incluyendo los nuevos campos)
                'personal_tipo_doc'     => $input['personal_tipo_doc'] ?? null,
                'personal_dni'          => $input['personal_dni'] ?? null,
                'personal_especialidad' => $input['personal_especialidad'] ?? null,
                'personal_nombre'       => $input['personal_nombre'] ?? null,

                // Nuevos campos
                'personal_cargo'        => $input['personal_cargo'] ?? null,
                'personal_correo'       => $input['personal_correo'] ?? null,
                'personal_celular'      => $input['personal_celular'] ?? null,
                'utiliza_sihce'         => $input['utiliza_sihce'] ?? 'NO',

                'firma_dj'               => $input['firma_dj'] ?? null,
                'firma_confidencialidad' => $input['firma_confidencialidad'] ?? null,
                'tipo_dni_fisico'        => $input['tipo_dni_fisico'] ?? null,
                'dnie_version'           => $input['dnie_version'] ?? null,
                'firma_sihce'            => $input['firma_sihce'] ?? null,

                // Capacitación
                'capacitacion_recibida' => $input['capacitacion'] ?? null,
                'capacitacion_entes'    => $input['capacitacion_ente'] ?? null,

                // Materiales e Insumos
                'insumos_disponibles'   => $input['insumos'] ?? [],

                // Equipos
                'equipos_listado'       => array_values($input['equipos'] ?? []),
                'equipos_observaciones' => $input['equipos_observaciones'] ?? null,

                // Gestión
                'nro_consultorios'      => $input['nro_consultorios'] ?? 0,
                'nro_gestantes_mes'     => $input['nro_gestantes_mes'] ?? 0,
                'gestion_hisminsa'      => $input['gestion_hisminsa'] ?? null,
                'gestion_reportes'      => $input['gestion_reportes'] ?? null,
                'gestion_reportes_socializa' => $input['gestion_reportes_socializa'] ?? null,

                'dificultad_comunica_a' => $input['dificultades']['comunica'] ?? null,
                'dificultad_medio_uso'  => $input['dificultades']['medio'] ?? null,

                // Evidencias
                'fotos_evidencia'       => $rutasFotos,
                // 'firma_grafica'      => Eliminado según migración
            ];

            // =========================================================================
            // PASO 2: Guardar en ModuloParto (Tabla Específica)
            // =========================================================================
            \App\Models\ModuloParto::updateOrCreate(
                ['monitoreo_id' => $idActa],
                $datosParto
            );

            // =========================================================================
            // PASO 3: Guardar JSON en MonitoreoModulos (Tabla General)
            // =========================================================================
            \App\Models\MonitoreoModulos::updateOrCreate(
                [
                    'cabecera_monitoreo_id' => $idActa,
                    'modulo_nombre'         => 'atencion_parto'
                ],
                [
                    'contenido'        => $datosParto,
                    'pdf_firmado_path' => null
                ]
            );

            // =========================================================================
            // PASO 4: Lógica de Equipos
            // =========================================================================
            $datosEquipos = $input['equipos'] ?? [];
            \App\Models\EquipoComputo::where('cabecera_monitoreo_id', $idActa)
                ->where('modulo', 'parto') // Usamos 'parto'
                ->delete();

            foreach ($datosEquipos as $item) {
                \App\Models\EquipoComputo::create([
                    'cabecera_monitoreo_id' => $idActa,
                    'modulo'      => 'parto',
                    'descripcion' => $item['nombre'] ?? 'Desconocido',
                    'cantidad'    => 1,
                    'estado'      => $item['estado'] ?? 'Regular',
                    'nro_serie'   => $item['serie'] ?? null,
                    'propio'      => $item['propiedad'] ?? null,
                    'observacion' => $item['observaciones'] ?? null,
                ]);
            }

            // =========================================================================
            // PASO 5: Guardar en RespuestaEntrevistado
            // =========================================================================
            \App\Models\RespuestaEntrevistado::updateOrCreate(
                [
                    'cabecera_monitoreo_id' => $idActa,
                    'modulo'                => 'parto'
                ],
                [
                    'doc_profesional'       => $datosParto['personal_dni'],
                    'recibio_capacitacion'  => $datosParto['capacitacion_recibida'],
                    'inst_que_lo_capacito'  => $datosParto['capacitacion_entes'],
                    'inst_a_quien_comunica' => $datosParto['dificultad_comunica_a'],
                    'medio_que_utiliza'     => $datosParto['dificultad_medio_uso'],
                ]
            );

            return redirect()->route('usuario.monitoreo.modulos', $idActa)
                ->with('success', 'Módulo Parto guardado con éxito y profesional actualizado.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Error Parto: " . $e->getMessage());
            return dd("Error al guardar: " . $e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    // BUSCADOR DE PROFESIONAL (Agregado para que funcione el Step 1)
    public function buscarProfesional(Request $request)
    {
        $tipo = $request->get('type');
        $valor = $request->get('q');

        if (!$valor) return response()->json([]);

        if ($tipo === 'doc') {
            $profesional = Profesional::where('doc', $valor)->first();
            return response()->json($profesional ? [$profesional] : []);
        } else {
            $profesionales = Profesional::where(Profesional::raw("CONCAT(apellido_paterno, ' ', apellido_materno, ' ', nombres)"), 'LIKE', "%{$valor}%")
                ->orWhere(Profesional::raw("CONCAT(nombres, ' ', apellido_paterno, ' ', apellido_materno)"), 'LIKE', "%{$valor}%")
                ->limit(10)
                ->get();
            return response()->json($profesionales);
        }
    }

    public function generar($idActa)
    {
        set_time_limit(120);

        $acta = CabeceraMonitoreo::findOrFail($idActa);
        $registro = ModuloParto::where('monitoreo_id', $idActa)->firstOrFail();

        // Convertir fotos a Base64
        if (!empty($registro->fotos_evidencia)) {
            $base64 = [];
            foreach ($registro->fotos_evidencia as $url) {
                $rutaRelativa = str_replace(url('/'), '', $url);
                $path = public_path($rutaRelativa);

                if (file_exists($path)) {
                    $type = pathinfo($path, PATHINFO_EXTENSION);
                    $data = file_get_contents($path);
                    $base64[] = 'data:image/' . $type . ';base64,' . base64_encode($data);
                }
            }
            $registro->fotos_evidencia = $base64;
        }

        // Convertir firma a Base64
        if ($registro->firma_grafica && str_contains($registro->firma_grafica, 'http')) {
            $rutaRelativa = str_replace(url('/'), '', $registro->firma_grafica);
            $path = public_path($rutaRelativa);
            if (file_exists($path)) {
                $type = pathinfo($path, PATHINFO_EXTENSION);
                $data = file_get_contents($path);
                $registro->firma_grafica = 'data:image/' . $type . ';base64,' . base64_encode($data);
            }
        }

        $pdf = Pdf::loadView('usuario.monitoreo.pdf.parto', compact('acta', 'registro'));
        return $pdf->stream('Parto_' . $idActa . '.pdf');
    }
}
