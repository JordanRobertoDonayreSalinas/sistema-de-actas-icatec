<?php

namespace App\Http\Controllers;

use App\Models\CabeceraMonitoreo;
use App\Models\ModuloParto;
use App\Models\MonitoreoModulos;
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
            $input = $request->input('contenido');

            // Procesar Fotos
            $fotosUrls = [];
            if ($request->hasFile('fotos')) {
                foreach ($request->file('fotos') as $foto) {
                    $path = $foto->store('evidencias_parto', 'public');
                    $fotosUrls[] = asset('storage/' . $path);
                }
            }
            if ($request->filled('rutas_servidor')) {
                $antiguas = json_decode($request->input('rutas_servidor'), true);
                if (is_array($antiguas)) $fotosUrls = array_merge($fotosUrls, $antiguas);
            }

            // GUARDAR DATOS - Asegúrate que los nombres de la izquierda existan en tu Migración
            ModuloParto::updateOrCreate(
                ['monitoreo_id' => $idActa],
                [
                    'nombre_consultorio'         => $input['nombre_consultorio'] ?? null,
                    'personal_tipo_doc'          => $input['personal_tipo_doc'] ?? null,
                    'personal_dni'               => $input['personal_dni'] ?? null,
                    'personal_especialidad'      => $input['personal_especialidad'] ?? null,
                    'personal_nombre'            => $input['personal_nombre'] ?? null,
                    'capacitacion_recibida'      => $input['capacitacion'] ?? null,
                    'capacitacion_entes'         => $input['capacitacion_ente'] ?? [],
                    'capacitacion_otros_detalle' => $input['capacitacion_otros_detalle'] ?? null,

                    // Mapeo corregido de Arrays
                    'insumos_disponibles'        => $input['insumos'] ?? [],
                    'materiales_otros'           => $input['materiales_otros'] ?? null,
                    'equipos_listado'            => $input['equipos'] ?? [],
                    'equipos_observaciones'      => $input['equipos_observaciones'] ?? null,

                    'nro_consultorios'           => $input['nro_consultorios'] ?? 0,
                    'nro_gestantes_mes'          => $input['nro_gestantes_mes'] ?? 0,
                    'gestion_hisminsa'           => $input['gestion_hisminsa'] ?? null,
                    'gestion_reportes'           => $input['gestion_reportes'] ?? null,
                    'gestion_reportes_socializa' => $input['gestion_reportes_socializa'] ?? null,

                    'fotos_evidencia'            => $fotosUrls,
                    'firma_grafica'              => $request->input('firma_grafica_data'),
                ]
            );

            // Actualizar el estado a FINALIZADO (Asegúrate de usar el modelo correcto)
            MonitoreoModulos::updateOrCreate(
                ['cabecera_monitoreo_id' => $idActa, 'modulo_nombre' => 'parto'],
                ['contenido' => 'FINALIZADO']
            );

            return redirect()->route('usuario.monitoreo.modulos', $idActa)
                ->with('success', 'Modulo Parto guardado con éxito.');
        } catch (\Exception $e) {
            // Esto te mostrará el error real en pantalla si algo falla en la BD
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

    public function generar($idActa)
    {
        $acta = CabeceraMonitoreo::findOrFail($idActa);
        $registro = ModuloParto::where('monitoreo_id', $idActa)->firstOrFail();

        // Convertir fotos a Base64 para el PDF
        if (!empty($registro->fotos_evidencia)) {
            $base64 = [];
            foreach ($registro->fotos_evidencia as $url) {
                $path = public_path(str_replace(url('/'), '', $url));
                if (file_exists($path)) {
                    $data = base64_encode(file_get_contents($path));
                    $base64[] = 'data:image/' . pathinfo($path, PATHINFO_EXTENSION) . ';base64,' . $data;
                }
            }
            $registro->fotos_evidencia = $base64;
        }

        $pdf = Pdf::loadView('usuario.monitoreo.pdf.parto', compact('acta', 'registro'));
        return $pdf->stream('Parto_' . $idActa . '.pdf');
    }
}
