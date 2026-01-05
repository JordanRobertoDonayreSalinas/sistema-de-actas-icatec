<?php

namespace App\Http\Controllers;

use App\Models\CabeceraMonitoreo;
use App\Models\EquipoComputo; // Asegúrate de importar este modelo
use App\Models\ModuloPrenatal;
use App\Models\MonitoreoModulos;
use App\Models\Profesional; // Si usas búsqueda, impórtalo
use App\Models\RespuestaEntrevistado; // Asegúrate de importar este modelo
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class PrenatalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($idActa)
    {
        $acta = CabeceraMonitoreo::findOrFail($idActa);
        $registro = ModuloPrenatal::where('monitoreo_id', $idActa)->first();
        return view('usuario.monitoreo.modulos.atencion_prenatal', compact('acta', 'registro'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request, $idActa)
    {
        try {
            // 1. Procesar Fotos (Lógica mantenida y optimizada)
            $rutasFotos = [];

            // Recuperar fotos antiguas si existen (del input hidden)
            if ($request->has('rutas_servidor') && !empty($request->rutas_servidor)) {
                $rutasFotos = json_decode($request->rutas_servidor, true) ?? [];
            }

            // Guardar nuevas fotos
            if ($request->hasFile('fotos')) {
                foreach ($request->file('fotos') as $foto) {
                    $path = $foto->store('evidencias_prenatal', 'public');
                    $rutasFotos[] = asset('storage/' . $path);
                }
            }

            // Limitar a 2 fotos máximo (opcional, como en Citas)
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
            // PASO 1: Guardamos todos los datos en una variable maestra ($datosPrenatal)
            // =========================================================================
            $datosPrenatal = [
                // Datos Generales
                'nombre_consultorio'    => $input['nombre_consultorio'] ?? null,

                // Personal
                'personal_tipo_doc'     => $input['personal_tipo_doc'] ?? null,
                'personal_dni'          => $input['personal_dni'] ?? null,
                'personal_especialidad' => $input['personal_especialidad'] ?? null,
                'personal_nombre'       => $input['personal_nombre'] ?? null,

                'firma_dj' => $input['firma_dj'] ?? null,
                'firma_confidencialidad' => $input['firma_confidencialidad'] ?? null,
                'tipo_dni_fisico' => $input['tipo_dni_fisico'] ?? null,
                'dnie_version' => $input['dnie_version'] ?? null,
                'firma_sihce' => $input['firma_sihce'] ?? null,

                // Capacitación
                'capacitacion_recibida' => $input['capacitacion'] ?? null,
                'capacitacion_entes'    => $input['capacitacion_ente'] ?? null, // Ahora es string (radio)
                'capacitacion_otros_detalle' => $input['capacitacion_otros_detalle'] ?? null,

                // Materiales e Insumos
                'insumos_disponibles'   => $input['insumos'] ?? [],
                'materiales_otros'      => $input['materiales_otros'] ?? null,

                // Equipos (Guardamos el array puro para el JSON)
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
                // Nota: Ya no usamos firma gráfica en el step 5, pero si envias algo vacío no pasa nada
                'firma_grafica'         => $request->input('firma_grafica_data'),
            ];

            // =========================================================================
            // PASO 2: Guardamos en la tabla principal (ModuloPrenatal)
            // =========================================================================
            ModuloPrenatal::updateOrCreate(
                ['monitoreo_id' => $idActa],
                $datosPrenatal // Pasamos el array maestro
            );

            // =========================================================================
            // PASO 3: Guardamos el JSON completo en MonitoreoModulos
            // =========================================================================
            MonitoreoModulos::updateOrCreate(
                [
                    'cabecera_monitoreo_id' => $idActa,
                    'modulo_nombre'         => 'atencion_prenatal'
                ],
                [
                    'contenido'        => $datosPrenatal, // Aquí se guarda el JSON completo
                    'pdf_firmado_path' => null
                ]
            );

            // =========================================================================
            // PASO 4: Lógica de Equipos (Borrar e Insertar)
            // =========================================================================
            $datosEquipos = $request->input('contenido.equipos', []);

            // Borramos los equipos previos de este módulo y acta
            EquipoComputo::where('cabecera_monitoreo_id', $idActa)
                ->where('modulo', 'atencion_prenatal')
                ->delete();

            // Insertamos los nuevos
            foreach ($datosEquipos as $item) {
                EquipoComputo::create([
                    'cabecera_monitoreo_id' => $idActa,
                    'modulo'      => 'atencion_prenatal',
                    'descripcion' => $item['nombre'] ?? 'Desconocido',
                    'cantidad'    => 1, // Por defecto 1 según tu lógica de tabla
                    'estado'      => $item['estado'] ?? 'Regular',
                    'nro_serie'   => $item['serie'] ?? null,      // Capturado del input nuevo
                    'propio'      => $item['propiedad'] ?? null,  // ESTABLECIMIENTO, PROPIO, etc.
                    'observacion' => $item['observaciones'] ?? null,
                ]);
            }

            // =========================================================================
            // PASO 5: Guardar en RespuestaEntrevistado
            // =========================================================================
            RespuestaEntrevistado::updateOrCreate(
                [
                    'cabecera_monitoreo_id' => $idActa,
                    'modulo'                => 'atencion_prenatal'
                ],
                [
                    'doc_profesional'       => $datosPrenatal['personal_dni'],
                    'recibio_capacitacion'  => $datosPrenatal['capacitacion_recibida'],
                    // Si es radio button, se guarda directo. Si fuese array, usar json_encode.
                    'inst_que_lo_capacito'  => $datosPrenatal['capacitacion_entes'],

                    // Prenatal no tiene sección de "Dificultades" en el blade actual,
                    // así que enviamos null o lo omitimos si la tabla lo permite.
                    'inst_a_quien_comunica' => $datosPrenatal['dificultad_comunica_a'],
                    'medio_que_utiliza'     => $datosPrenatal['dificultad_medio_uso'],
                ]
            );

            return redirect()->route('usuario.monitoreo.modulos', $idActa)
                ->with('success', 'Atención Prenatal guardada con éxito.');
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

    // BUSCADOR DE PROFESIONAL (Igual que en Citas, necesario si usas el JS de búsqueda)
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
        set_time_limit(120); // Aumentar tiempo límite por si acaso

        $acta = CabeceraMonitoreo::findOrFail($idActa);
        $registro = ModuloPrenatal::where('monitoreo_id', $idActa)->firstOrFail();

        // Convertir fotos a Base64 para el PDF
        if (!empty($registro->fotos_evidencia)) {
            $base64 = [];
            foreach ($registro->fotos_evidencia as $url) {
                // Limpieza de URL para obtener path local
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

        // Convertir firma a Base64 si existe
        if ($registro->firma_grafica && str_contains($registro->firma_grafica, 'http')) {
            $rutaRelativa = str_replace(url('/'), '', $registro->firma_grafica);
            $path = public_path($rutaRelativa);
            if (file_exists($path)) {
                $type = pathinfo($path, PATHINFO_EXTENSION);
                $data = file_get_contents($path);
                $registro->firma_grafica = 'data:image/' . $type . ';base64,' . base64_encode($data);
            }
        }

        $pdf = Pdf::loadView('usuario.monitoreo.pdf.prenatal', compact('acta', 'registro'));
        return $pdf->stream('Prenatal_' . $idActa . '.pdf');
    }
}
