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
            // 1. Procesar Fotos
            $rutasFotos = [];

            // Recuperar fotos antiguas si existen
            if ($request->has('rutas_servidor') && !empty($request->rutas_servidor)) {
                $rutasFotos = json_decode($request->rutas_servidor, true) ?? [];
            }

            // Guardar nuevas fotos
            if ($request->hasFile('fotos')) {
                foreach ($request->file('fotos') as $foto) {
                    $path = $foto->store('evidencias_parto', 'public');
                    $rutasFotos[] = asset('storage/' . $path);
                }
            }

            // Limitar a 2 fotos máximo
            $rutasFotos = array_slice($rutasFotos, 0, 2);

            // 2. Extraer Inputs
            $input = $request->input('contenido');

            // =========================================================================
            // PASO 0: LOGICA DE GUARDADO AUTOMÁTICO DE PROFESIONAL (NUEVO)
            // =========================================================================
            $dni = $input['personal_dni'] ?? null;
            $nombreCompleto = $input['personal_nombre'] ?? null;
            $tipoDoc = $input['personal_tipo_doc'] ?? 'DNI';

            if ($dni && $nombreCompleto) {
                // Buscamos si ya existe un profesional con ese documento
                $profesional = Profesional::where('doc', $dni)->first();

                if (!$profesional) {
                    // Si NO existe, intentamos desglosar el nombre completo
                    // Asumimos formato simple: "ApellidoPaterno ApellidoMaterno Nombres"
                    // Esta es una aproximación básica, el usuario podría editarlo después si es necesario.
                    $partes = explode(' ', $nombreCompleto);
                    $paterno = array_shift($partes) ?? ''; // Primer elemento
                    $materno = array_shift($partes) ?? ''; // Segundo elemento
                    $nombres = implode(' ', $partes);      // El resto

                    // Si solo pusieron un nombre y un apellido, ajustamos para que no quede vacío
                    if (empty($nombres)) {
                        $nombres = $materno;
                        $materno = '';
                    }
                    if (empty($nombres)) { // Caso extremo solo 1 palabra
                        $nombres = $paterno;
                        $paterno = '';
                    }

                    Profesional::create([
                        'tipo_doc'         => $tipoDoc,
                        'doc'              => $dni,
                        'nombres'          => strtoupper($nombres),
                        'apellido_paterno' => strtoupper($paterno),
                        'apellido_materno' => strtoupper($materno),
                        // Puedes agregar campos por defecto si tu tabla los requiere
                        'especialidad'     => null,
                        'condicion'        => null,
                    ]);
                }
            }



            // =========================================================================
            // PASO 1: Guardamos todos los datos en una variable maestra ($datosParto)
            // =========================================================================
            $datosParto = [
                // Datos Generales
                'nombre_consultorio'    => $input['nombre_consultorio'] ?? null,

                // Personal
                'personal_tipo_doc'     => $input['personal_tipo_doc'] ?? null,
                'personal_dni'          => $input['personal_dni'] ?? null,
                'personal_especialidad' => $input['personal_especialidad'] ?? null,
                'personal_nombre'       => $input['personal_nombre'] ?? null,

                // Capacitación
                'capacitacion_recibida' => $input['capacitacion'] ?? null,
                'capacitacion_entes'    => $input['capacitacion_ente'] ?? null, // Radio button (string)
                'capacitacion_otros_detalle' => $input['capacitacion_otros_detalle'] ?? null,

                // Materiales e Insumos
                'insumos_disponibles'   => $input['insumos'] ?? [],
                'materiales_otros'      => $input['materiales_otros'] ?? null,

                // Equipos (Array limpio para el JSON)
                'equipos_listado'       => array_values($input['equipos'] ?? []),
                'equipos_observaciones' => $input['equipos_observaciones'] ?? null,

                // Gestión
                'nro_consultorios'      => $input['nro_consultorios'] ?? 0,
                'nro_gestantes_mes'     => $input['nro_gestantes_mes'] ?? 0, // En la vista es "Partos Registrados"
                'gestion_hisminsa'      => $input['gestion_hisminsa'] ?? null,
                'gestion_reportes'      => $input['gestion_reportes'] ?? null,
                'gestion_reportes_socializa' => $input['gestion_reportes_socializa'] ?? null,

                // Dificultades (Nuevos campos)
                'dificultad_comunica_a' => $input['dificultades']['comunica'] ?? null,
                'dificultad_medio_uso'  => $input['dificultades']['medio'] ?? null,

                // Evidencias
                'fotos_evidencia'       => $rutasFotos,
                // Firma (aunque se quitó del step 5, mantenemos el campo por compatibilidad)
                'firma_grafica'         => $request->input('firma_grafica_data'),
            ];

            // =========================================================================
            // PASO 2: Guardamos en la tabla principal (ModuloParto)
            // =========================================================================
            ModuloParto::updateOrCreate(
                ['monitoreo_id' => $idActa],
                $datosParto
            );

            // =========================================================================
            // PASO 3: Guardamos el JSON completo en MonitoreoModulos
            // =========================================================================
            MonitoreoModulos::updateOrCreate(
                [
                    'cabecera_monitoreo_id' => $idActa,
                    'modulo_nombre'         => 'parto'
                ],
                [
                    'contenido'        => $datosParto, // JSON Completo
                    'pdf_firmado_path' => null
                ]
            );

            // =========================================================================
            // PASO 4: Lógica de Equipos (Borrar e Insertar)
            // =========================================================================
            $datosEquipos = $request->input('contenido.equipos', []);

            // Borramos los equipos previos de este módulo y acta
            EquipoComputo::where('cabecera_monitoreo_id', $idActa)
                ->where('modulo', 'parto') // Cambiado a 'parto'
                ->delete();

            // Insertamos los nuevos
            foreach ($datosEquipos as $item) {
                EquipoComputo::create([
                    'cabecera_monitoreo_id' => $idActa,
                    'modulo'      => 'parto', // Cambiado a 'parto'
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
            RespuestaEntrevistado::updateOrCreate(
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
                ->with('success', 'Módulo Parto guardado con éxito.');
        } catch (\Exception $e) {
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
