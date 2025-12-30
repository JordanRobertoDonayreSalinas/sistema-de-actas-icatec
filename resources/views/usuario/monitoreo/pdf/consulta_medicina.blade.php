<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Consulta Externa - Medicina - Acta {{ $acta->id }}</title>
    <style>
        @page { margin: 1.2cm 1.5cm; }
        body { font-family: 'Helvetica', sans-serif; font-size: 10px; color: #1e293b; line-height: 1.4; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #4f46e5; padding-bottom: 10px; }
        .header h1 { margin: 0; font-size: 15px; text-transform: uppercase; color: #4f46e5; }
        .section-title { background-color: #f1f5f9; padding: 6px 10px; font-weight: bold; text-transform: uppercase; border-left: 4px solid #4f46e5; margin-top: 15px; margin-bottom: 5px; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; table-layout: fixed; margin-bottom: 5px; }
        th, td { border: 1px solid #e2e8f0; padding: 6px 8px; text-align: left; vertical-align: middle; word-wrap: break-word; }
        th { background-color: #f8fafc; color: #475569; font-size: 8.5px; text-transform: uppercase; }
        .bg-label { background-color: #f8fafc; font-weight: bold; width: 30%; }
        .uppercase { text-transform: uppercase; }
        .text-center { text-align: center; }
        /* Evidencia fotográfica: imagen única centrada en recuadro */
        .foto-container { margin: 15px 0; padding: 15px; border: 2px solid #4f46e5; background-color: #f9fafc; text-align: center; }
        .foto { display: block; margin: 0 auto; width: 100%; height: 280px; object-fit: contain; background-color: #ffffff; border: 1px solid #e2e8f0; }
        /* Evidencia fotográfica: múltiples imágenes en grid con recuadro uniforme */
        .foto-grid { display: flex; flex-wrap: wrap; justify-content: space-between; gap: 6px; margin: 10px 0; padding: 10px; border: 2px solid #4f46e5; background-color: #f9fafc; }
        .foto-grid-item { width: calc(50% - 6px); }
        .foto-grid-item img { width: 100%; height: 150px; object-fit: contain; background-color: #ffffff; border: 1px solid #e2e8f0; display: block; }
        .materiales-list { padding: 8px; }
        .materiales-item { display: inline-block; padding: 3px 8px; background-color: #e0e7ff; border-radius: 4px; margin: 2px; font-size: 9px; }
    </style>
</head>
<body>

    <div class="header">
        <h1>Módulo 04: Consulta Externa - Medicina</h1>
        <div style="font-weight: bold; color: #64748b; font-size: 10px;">
            ACTA N° {{ str_pad($acta->id, 5, '0', STR_PAD_LEFT) }} | E.E.S.S.: {{ strtoupper($acta->establecimiento->nombre) }}
        </div>
    </div>

    <div class="section-title">1. Detalles</div>
    <table>
        <tr>
            <td class="bg-label">Cantidad</td>
            <td>{{ $detalle->contenido['num_consultorios'] ?? '---' }}</td>
        </tr>
        <tr>
            <td class="bg-label">Denominación</td>
            <td class="uppercase">{{ $detalle->contenido['denominacion_consultorio'] ?? '---' }}</td>
        </tr>
    </table>

    <div class="section-title">2. Datos del profesional</div>
    <table>
        <tr>
            <td class="bg-label">Nombres y Apellidos</td>
            <td class="uppercase">{{ $detalle->contenido['profesional']['apellidos_nombres'] ?? ($detalle->contenido['profesional']['nombres'] ?? '---') }}</td>
        </tr>
        <tr>
            <td class="bg-label">Documento</td>
            <td>{{ $detalle->contenido['profesional']['doc'] ?? '---' }}</td>
        </tr>
        <tr>
            <td class="bg-label">Cargo</td>
            <td class="uppercase">{{ $detalle->contenido['profesional']['cargo'] ?? '---' }}</td>
        </tr>
    </table>

    <div class="section-title">3. Detalles de Capacitación</div>
    <table>
        <tr>
            <td class="bg-label">¿Recibió Capacitación?</td>
            <td>{{ $detalle->contenido['recibio_capacitacion'] ?? '---' }}</td>
        </tr>
        <tr>
            <td class="bg-label">¿De parte de quién?</td>
            <td>{{ $detalle->contenido['inst_capacitacion'] ?? '---' }}</td>
        </tr>
    </table>

    <div class="section-title">4. Materiales</div>
    <div class="materiales-list">
        @php
            $materiales = $detalle->contenido['materiales'] ?? [];
            $materialesMapping = [
                'historia_clinica' => 'Historia Clínica',
                'fua' => 'FUA',
                'receta' => 'Receta',
                'orden_laboratorio' => 'Orden de Laboratorio',
                'hoja_referencia' => 'Hoja de Referencia',
                'otros' => 'Otros'
            ];
            $materialesSeleccionados = [];
            foreach ($materialesMapping as $key => $label) {
                if (isset($materiales[$key]) && $materiales[$key]) {
                    $materialesSeleccionados[] = $label;
                }
            }
        @endphp
        @if(count($materialesSeleccionados) > 0)
            @foreach($materialesSeleccionados as $material)
                <span class="materiales-item">- {{ $material }}</span>
            @endforeach
        @else
            <span style="color: #94a3b8; font-style: italic;">SIN MATERIALES REGISTRADOS</span>
        @endif
    </div>

    <div class="section-title">5. Equipamiento del Área</div>
    @php
        $equipos = \App\Models\EquipoComputo::where('cabecera_monitoreo_id', $acta->id)
                    ->where('modulo', 'consulta_medicina')
                    ->get();
    @endphp
    @if($equipos->count() > 0)
        <table>
            <thead>
                <tr>
                    <th width="35%">Descripción</th>
                    <th width="15%">Cantidad</th>
                    <th width="15%">Estado</th>
                    <th width="20%">N° Serie</th>
                    <th width="15%">Observación</th>
                </tr>
            </thead>
            <tbody>
                @foreach($equipos as $eq)
                <tr>
                    <td class="uppercase">{{ $eq->descripcion }}</td>
                    <td class="text-center">{{ $eq->cantidad }}</td>
                    <td>{{ $eq->estado }}</td>
                    <td>{{ $eq->nro_serie ?? '---' }}</td>
                    <td class="uppercase">{{ $eq->observacion ?? '---' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div style="color: #94a3b8; font-style: italic; padding: 8px;">SIN EQUIPAMIENTO REGISTRADO</div>
    @endif

    <div class="section-title">6. Soporte Técnico</div>
    <table>
        <tr>
            <td class="bg-label">¿A quién le comunica?</td>
            <td class="uppercase">{{ $detalle->contenido['comunica_a'] ?? '---' }}</td>
        </tr>
        <tr>
            <td class="bg-label">¿Qué medio utiliza?</td>
            <td>{{ $detalle->contenido['medio_soporte'] ?? '---' }}</td>
        </tr>
    </table>

    <div class="section-title">7. Comentarios</div>
    <div style="border: 1px solid #e2e8f0; padding: 10px; min-height: 40px;" class="uppercase">
        {{ $detalle->contenido['comentarios'] ?? 'SIN COMENTARIOS.' }}
    </div>

    @if(!empty($imagenesData) && is_array($imagenesData) && count($imagenesData) > 0)
        <div class="section-title">8. Evidencia Fotográfica</div>
        @if(count($imagenesData) === 1)
            <div class="foto-container">
                <img src="{{ $imagenesData[0] }}" class="foto" alt="Evidencia">
            </div>
        @else
            <div class="foto-grid">
                @foreach($imagenesData as $img)
                    <div class="foto-grid-item">
                        <img src="{{ $img }}" alt="Evidencia">
                    </div>
                @endforeach
            </div>
        @endif
    @endif

    <div style="position: fixed; bottom: -10px; width: 100%; text-align: right; font-size: 8px; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 5px;">
        Generado por Sistema de Monitoreo | Fecha: {{ date('d/m/Y H:i:s') }}
    </div>

</body>
</html>

