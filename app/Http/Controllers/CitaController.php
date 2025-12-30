<?php

namespace App\Http\Controllers;

use App\Models\CabeceraMonitoreo;
use App\Models\EquipoComputo;
use App\Models\ModuloCita;
use App\Models\MonitoreoModulos;
use App\Models\Profesional;
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
        // 1. Procesar Fotos
        $rutasFotos = [];

        // A. Fotos antiguas (vienen del input hidden del JS, si es edición)
        if ($request->has('rutas_servidor') && !empty($request->rutas_servidor)) {
            // Decodificar JSON de rutas que envía el JS
            $rutasFotos = json_decode($request->rutas_servidor, true) ?? [];
        }

        // B. Fotos nuevas subidas
        if ($request->hasFile('fotos')) {
            foreach ($request->file('fotos') as $foto) {
                $path = $foto->store('evidencias', 'public');
                $rutasFotos[] = asset('storage/' . $path);
            }
        }

        // Limitar a 2 fotos
        $rutasFotos = array_slice($rutasFotos, 0, 2);

        // 2. Extraer Inputs del Request (tu formulario envía todo en el array 'contenido')
        $input = $request->input('contenido');

        // 3. Guardar en Base de Datos (Mapeo Manual)
        ModuloCita::updateOrCreate(
            ['monitoreo_id' => $idActa], // Clave de búsqueda
            [
                // Personal
                'personal_nombre' => $input['personal_nombre'] ?? null,
                'personal_dni'    => $input['personal_dni'] ?? null,
                'personal_turno'  => $input['personal_turno'] ?? null,
                'personal_roles'  => $input['personal_rol'] ?? [], // Array

                'capacitacion_recibida'      => $input['capacitacion'] ?? null,
                'capacitacion_entes'         => $input['capacitacion_ente'] ?? [], // Array
                'capacitacion_otros_detalle' => $input['capacitacion_otros_detalle'] ?? null,

                // Logística
                'insumos_disponibles'   => $input['insumos'] ?? [], // Array
                // Para las tablas dinámicas, usamos array_values para reindexar (0,1,2...)
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
                'firma_grafica'   => $request->input('firma_grafica_data'),

            ]
        );

        MonitoreoModulos::updateOrCreate(
            [
                'cabecera_monitoreo_id' => $idActa, // Relación con el ID del acta
                'modulo_nombre'         => 'citas'   // Identificador de este formulario
            ],
            [
                'contenido' => 'FINALIZADO' // Texto fijo que solicitaste
            ]
        );

        //INSERTA EN TABLA NORMALIZADA
        $datosEquipos = $request->input('contenido.equipos', []);

        // 2. IMPORTANTE: Primero limpiamos los registros anteriores de este monitoreo
        // Esto sirve para que si borraste una fila en el HTML, también se borre en la BD al guardar.
        EquipoComputo::where('cabecera_monitoreo_id', $request->id) // O el ID que uses como FK
            ->where('modulo', 'citas') // Opcional: Para asegurar que borras solo de este módulo
            ->delete();

        // 3. Recorremos y creamos los nuevos registros
        foreach ($datosEquipos as $item) {
            EquipoComputo::create([
                'cabecera_monitoreo_id' => $request->id, // Tu ID foráneo
                'modulo'      => 'citas', // O el nombre del módulo en el que estés (ej. 'gestion_administrativa')
                'descripcion' => $item['nombre'] ?? 'Desconocido',
                'cantidad'    => 1, // Como ahora es registro individual, la cantidad siempre es 1
                'estado'      => $item['estado'] ?? 'Regular',
                'nro_serie'   => $item['serie'] ?? null,

                // Convertimos el select (ESTABLECIMIENTO/PROPIO) a booleano (0 o 1)
                'propio'      => ($item['propiedad'] ?? '') === 'PROPIO' ? 1 : 0,

                'observacion' => $item['observaciones'] ?? null,
            ]);
        }



        return redirect()->route('usuario.monitoreo.modulos', $idActa) // O redirigir al index
            ->with('success', 'Módulo de Citas finalizado y guardado correctamente.');
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


    // IMPRIMIR PDF
    public function generar($idActa)
    {
        // 1. Aumentar el tiempo límite por si acaso (opcional, pero recomendado)
        set_time_limit(120);

        $acta = CabeceraMonitoreo::findOrFail($idActa);
        $registro = ModuloCita::where('monitoreo_id', $idActa)->firstOrFail();

        // 2. CONVERTIR FOTOS A BASE64 (Para evitar el bloqueo de imágenes)
        $fotosBase64 = [];
        if (!empty($registro->fotos_evidencia)) {
            foreach ($registro->fotos_evidencia as $url) {
                // Convertimos la URL pública a una ruta de archivo en tu disco duro
                // Ejemplo: http://127.0.0.1:8000/storage/fotos/img.png -> C:\...\public\storage\fotos\img.png
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

        // Sobrescribimos la variable para la vista (ahora son códigos largos, no links)
        $registro->fotos_evidencia = $fotosBase64;

        // 3. HACER LO MISMO CON LA FIRMA (Si existe)
        if ($registro->firma_grafica && str_contains($registro->firma_grafica, 'http')) {
            $rutaRelativa = str_replace(url('/'), '', $registro->firma_grafica);
            $path = public_path($rutaRelativa);
            if (file_exists($path)) {
                $type = pathinfo($path, PATHINFO_EXTENSION);
                $data = file_get_contents($path);
                $registro->firma_grafica = 'data:image/' . $type . ';base64,' . base64_encode($data);
            }
        }

        // 4. Generar PDF (Ya no necesitas isRemoteEnabled porque las imágenes van incrustadas)
        $pdf = Pdf::loadView('usuario.monitoreo.pdf.citas', compact('acta', 'registro'));

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
