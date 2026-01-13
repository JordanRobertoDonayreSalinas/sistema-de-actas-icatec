<?php

namespace App\Http\Controllers;

use App\Models\CabeceraMonitoreo;
use App\Models\EquipoComputo;
use App\Models\ModuloCita;
use App\Models\MonitoreoModulos;
use App\Models\Profesional;
use App\Models\RespuestaEntrevistado;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class CitaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($id)
    {

        $acta = CabeceraMonitoreo::findOrFail($id);
        $registro = ModuloCita::where('monitoreo_id', $id)->first();

        return view('usuario.monitoreo.modulos.citas', compact('acta', 'registro'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request, $idActa)
    {
        // 1. Procesar Fotos (Tu lógica original)
        $rutasFotos = [];
        if ($request->has('rutas_servidor') && !empty($request->rutas_servidor)) {
            $rutasFotos = json_decode($request->rutas_servidor, true) ?? [];
        }
        if ($request->hasFile('fotos')) {
            foreach ($request->file('fotos') as $foto) {
                $path = $foto->store('evidencias', 'public');
                $rutasFotos[] = asset('storage/' . $path);
            }
        }
        $rutasFotos = array_slice($rutasFotos, 0, 2);

        // 2. Extraer Inputs (Usaremos $input para todo)
        $input = $request->input('contenido');

        // =========================================================================
        // PASO UNIFICADO: LOGICA DE PROFESIONAL (CREAR O ACTUALIZAR)
        // =========================================================================
        $dni = $input['personal_dni'] ?? null;

        if ($dni) {
            // 1. Buscar o Instanciar
            $profesional = \App\Models\Profesional::firstOrNew(['doc' => $dni]);

            // 2. Actualizar datos de contacto y tipo de documento siempre
            $profesional->tipo_doc = $input['personal_tipo_doc'] ?? 'DNI';

            // Solo actualizamos si el input tiene valor, para no borrar datos existentes con null
            if (!empty($input['personal_correo'])) {
                $profesional->email = $input['personal_correo'];
            }

            if (!empty($input['personal_celular'])) {
                $profesional->telefono = $input['personal_celular'];
            }

            // 3. Actualizar Nombres (Separación Inteligente)
            if (!empty($input['personal_nombre'])) {
                $nombreCompleto = mb_strtoupper(trim($input['personal_nombre']), 'UTF-8');

                // Solo procesamos si el nombre cambió o es un registro nuevo
                $nombreActual = trim(($profesional->apellido_paterno ?? '') . ' ' . ($profesional->apellido_materno ?? '') . ' ' . ($profesional->nombres ?? ''));

                if ($nombreCompleto !== $nombreActual) {
                    $partes = explode(' ', $nombreCompleto);
                    $num = count($partes);

                    if ($num >= 4) {
                        // Caso: PEREZ LOPEZ JUAN CARLOS (4 o más)
                        $profesional->apellido_paterno = array_shift($partes); // PEREZ
                        $profesional->apellido_materno = array_shift($partes); // LOPEZ
                        $profesional->nombres          = implode(' ', $partes); // JUAN CARLOS
                    } elseif ($num == 3) {
                        // Caso: PEREZ LOPEZ JUAN
                        $profesional->apellido_paterno = $partes[0];
                        $profesional->apellido_materno = $partes[1];
                        $profesional->nombres          = $partes[2];
                    } elseif ($num == 2) {
                        // Caso: PEREZ JUAN (Asumimos Paterno y Nombre)
                        $profesional->apellido_paterno = $partes[0];
                        $profesional->apellido_materno = '';
                        $profesional->nombres          = $partes[1];
                    } else {
                        // Caso raro: JUAN
                        $profesional->nombres = $nombreCompleto;
                    }
                }
            }

            // 4. GUARDAR CAMBIOS EN LA TABLA mon_profesionales
            $profesional->save();
        }

        // =========================================================================
        // PASO 1: Preparamos los datos para guardar en el módulo
        // =========================================================================
        $datosCita = [
            // Personal
            'personal_nombre' => $input['personal_nombre'] ?? null,
            'personal_dni'    => $input['personal_dni'] ?? null,
            'personal_turno'  => $input['personal_turno'] ?? null,
            'personal_roles'  => $input['personal_rol'] ?? [],

            'personal_correo'   => $input['personal_correo'] ?? null,
            'personal_celular'  => $input['personal_celular'] ?? null,
            'personal_cargo'    => $input['personal_cargo'] ?? null,
            'personal_tipo_doc' => $input['personal_tipo_doc'],

            'utiliza_sihce'          => $input['utiliza_sihce'] ?? 'NO',
            'firma_dj'               => $input['firma_dj'] ?? null,
            'firma_confidencialidad' => $input['firma_confidencialidad'] ?? null,
            'tipo_dni_fisico'        => $input['tipo_dni_fisico'] ?? null,
            'dnie_version'           => $input['dnie_version'] ?? null,
            'firma_sihce'            => $input['firma_sihce'] ?? null,

            'capacitacion_recibida'      => $input['capacitacion'] ?? null,
            'capacitacion_entes'         => $input['capacitacion_ente'] ?? null,

            // Logística
            'insumos_disponibles'   => $input['insumos'] ?? [],
            'equipos_listado'       => array_values($input['equipos'] ?? []),
            'equipos_observaciones' => $input['equipos_observaciones'] ?? null,

            // Gestión
            'nro_ventanillas'    => $input['nro_ventanillas'] ?? 0,
            'produccion_listado' => array_values($input['produccion'] ?? []),

            'calidad_tiempo_espera'       => $input['calidad']['espera'] ?? null,
            'calidad_paciente_satisfecho' => $input['calidad']['satisfaccion'] ?? null,
            'calidad_usa_reportes'        => $input['calidad']['reportes'] ?? null,
            'calidad_socializa_con'       => $input['calidad']['reportes_socializa'] ?? null,

            'dificultad_comunica_a' => $input['dificultades']['comunica'] ?? null,
            'dificultad_medio_uso'  => $input['dificultades']['medio'] ?? null,

            // Evidencias
            'fotos_evidencia' => $rutasFotos,
        ];

        // =========================================================================
        // PASO 2: Guardar en ModuloCita (Tabla principal)
        // =========================================================================
        \App\Models\ModuloCita::updateOrCreate(
            ['monitoreo_id' => $idActa],
            $datosCita
        );

        // =========================================================================
        // PASO 3: Guardar en MonitoreoModulos (JSON)
        // =========================================================================
        \App\Models\MonitoreoModulos::updateOrCreate(
            [
                'cabecera_monitoreo_id' => $idActa,
                'modulo_nombre'         => 'citas'
            ],
            [
                'contenido' => $datosCita,
                'pdf_firmado_path' => null
            ]
        );

        // =========================================================================
        // PASO 4: Guardar Equipos
        // =========================================================================
        $datosEquipos = $input['equipos'] ?? [];
        // Ojo: verifica si $request->id es correcto, usualmente sería $idActa si esa es la FK
        \App\Models\EquipoComputo::where('cabecera_monitoreo_id', $idActa)->where('modulo', 'citas')->delete();

        foreach ($datosEquipos as $item) {
            \App\Models\EquipoComputo::create([
                'cabecera_monitoreo_id' => $idActa,
                'modulo'      => 'citas',
                'descripcion' => $item['nombre'] ?? 'Desconocido',
                'cantidad'    => 1,
                'estado'      => $item['estado'] ?? 'Regular',
                'nro_serie'   => $item['serie'] ?? null,
                'propio'      => $item['propiedad'] ?? '',
                'observacion' => $item['observaciones'] ?? null,
            ]);
        }

        // =========================================================================
        // PASO 5: Guardar en RespuestaEntrevistado
        // =========================================================================
        \App\Models\RespuestaEntrevistado::updateOrCreate(
            [
                'cabecera_monitoreo_id' => $idActa,
                'modulo'                => 'citas'
            ],
            [
                'doc_profesional'       => $datosCita['personal_dni'],
                'recibio_capacitacion'  => $datosCita['capacitacion_recibida'],
                'inst_que_lo_capacito'  => $datosCita['capacitacion_entes'],
                'inst_a_quien_comunica' => $datosCita['dificultad_comunica_a'],
                'medio_que_utiliza'     => $datosCita['dificultad_medio_uso'],
            ]
        );

        return redirect()->route('usuario.monitoreo.modulos', $idActa)
            ->with('success', 'Módulo de Citas finalizado y profesional actualizado correctamente.');
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


    public function generar($idActa)
    {
        // 1. Configuración inicial
        set_time_limit(120);

        $acta = CabeceraMonitoreo::findOrFail($idActa);
        $registro = ModuloCita::where('monitoreo_id', $idActa)->firstOrFail();

        // 2. Lógica de Imágenes a Base64
        $fotosBase64 = [];
        if (!empty($registro->fotos_evidencia)) {
            foreach ($registro->fotos_evidencia as $url) {
                $rutaRelativa = str_replace(url('/'), '', $url);
                $path = public_path($rutaRelativa);

                if (file_exists($path)) {
                    $type = pathinfo($path, PATHINFO_EXTENSION);
                    $data = file_get_contents($path);
                    $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
                    $fotosBase64[] = $base64;
                }
            }
        }
        $registro->fotos_evidencia = $fotosBase64;

        // 3. Lógica de Firma a Base64
        if ($registro->firma_grafica && str_contains($registro->firma_grafica, 'http')) {
            $rutaRelativa = str_replace(url('/'), '', $registro->firma_grafica);
            $path = public_path($rutaRelativa);
            if (file_exists($path)) {
                $type = pathinfo($path, PATHINFO_EXTENSION);
                $data = file_get_contents($path);
                $registro->firma_grafica = 'data:image/' . $type . ';base64,' . base64_encode($data);
            }
        }

        $profesional = \App\Models\Profesional::where('doc', $registro->personal_dni)->first();

        // ---------------------------------------------------------
        // 4. GENERACIÓN DEL PDF (CORREGIDO)
        // ---------------------------------------------------------

        // A. Cargamos la vista
        $pdf = Pdf::loadView('usuario.monitoreo.pdf.citas', compact('acta', 'registro', 'profesional'));

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

        return $pdf->stream('reporte_citas.pdf');
    }

    public function buscarProfesional(Request $request)
    {
        $tipo = $request->get('type'); // 'doc' o 'nombre'
        $valor = $request->get('q');

        // Si no hay valor, devolver array vacío
        if (!$valor) return response()->json([]);

        if ($tipo === 'doc') {
            // CORRECCIÓN: Usar el modelo directamente
            $profesional = Profesional::where('doc', $valor)->first();

            return response()->json($profesional ? [$profesional] : []);
        } else {
            // Búsqueda por nombre
            // CORRECCIÓN: Usar DB::raw() para la concatenación
            $profesionales = Profesional::where(Profesional::raw("CONCAT(apellido_paterno, ' ', apellido_materno, ' ', nombres)"), 'LIKE', "%{$valor}%")
                ->orWhere(Profesional::raw("CONCAT(nombres, ' ', apellido_paterno, ' ', apellido_materno)"), 'LIKE', "%{$valor}%")
                ->limit(10)
                ->get();

            return response()->json($profesionales);
        }
    }
}
