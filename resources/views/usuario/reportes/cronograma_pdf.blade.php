<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Cronograma de Actividades</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; page-break-inside: auto; }
        tr { page-break-inside: avoid; page-break-after: auto; }
        th { background-color: #1E3A5F; color: #FFFFFF; font-weight: bold; text-align: center; border: 1px solid #AAAAAA; padding: 5px; }
        td { background-color: #FFFFFF; border: 1px solid #AAAAAA; padding: 5px; vertical-align: top; }
        .text-center { text-align: center; }
        .evidencia-img { max-width: 100px; max-height: 80px; margin-right: 5px; margin-bottom: 5px; }
        .evidencia-container { margin-top: 5px; }
    </style>
</head>
<body>
    @php
        $inicioFmt = \Carbon\Carbon::parse($fechaInicio)->format('d/m/Y');
        $finFmt = \Carbon\Carbon::parse($fechaFin)->format('d/m/Y');
    @endphp
    <h2 style="text-align: center; color: #1E3A5F; margin-bottom: 20px;">CRONOGRAMA DE ACTIVIDADES DEL {{ $inicioFmt }} AL {{ $finFmt }}</h2>
    
    <table>
        <thead>
            <tr>
                <th style="width: 10%;">FECHA DE ACTIVIDAD</th>
                <th style="width: 25%;">DESCRIPCIÓN DE ACTIVIDAD</th>
                <th style="width: 20%;">ESTABLECIMIENTO EN DONDE SE REALIZA LA ACTIVIDAD</th>
                <th style="width: 20%;">PARTICIPANTES EN LA ACTIVIDAD</th>
                <th style="width: 25%;">ACTA O EVIDENCIA DE LA ACTIVIDAD REALIZADA</th>
            </tr>
        </thead>
        <tbody>
            @foreach($actividades as $fila)
                @php
                    $fecha = \Carbon\Carbon::parse($fila['fecha'])->format('d/m/Y');
                    
                    if ($fila['tipo_key'] === 'asistencia') {
                        $actividadTxt = $fila['actividad'] !== '—' ? $fila['actividad'] : '';
                        $actividadTxt = mb_strtolower($actividadTxt, 'UTF-8');
                        $actividadTxt = mb_strtoupper(mb_substr($actividadTxt, 0, 1, 'UTF-8'), 'UTF-8') . mb_substr($actividadTxt, 1, null, 'UTF-8');
                        $descripcion = 'Asistencia Técnica: ' . $actividadTxt;
                        if (!empty($fila['modalidad']) && $fila['modalidad'] !== '—') {
                            $descripcion .= "<br>Modalidad: " . $fila['modalidad'];
                        }
                    } elseif ($fila['tipo_key'] === 'monitoreo') {
                        $descripcion = 'Monitoreo de Uso del SIHCE MINSA - Presencial';
                    } else {
                        $descripcion = 'Implementación Módulo de ' . $fila['actividad'];
                    }

                    $categoria = strtoupper(trim($fila['categoria_establecimiento'] ?? ''));
                    if (in_array($categoria, ['I-1', 'I-2'])) {
                        $prefijo = 'P.S.';
                    } elseif (in_array($categoria, ['I-3', 'I-4'])) {
                        $prefijo = 'C.S.';
                    } else {
                        $prefijo = '';
                    }
                    $nombreEstab = $fila['establecimiento'];
                    if (mb_strtoupper($nombreEstab, 'UTF-8') === $nombreEstab) {
                        $nombreEstab = mb_convert_case($nombreEstab, MB_CASE_TITLE, 'UTF-8');
                    }
                    $estabTxt = ($prefijo ? $prefijo . ' ' : '') . $nombreEstab . ' - ' . $fila['provincia'];

                    $participantesTxt = $fila['participantes_txt'] ?? $fila['responsable'];
                    $actaTxt = $fila['nombre_acta'] ?? ('Acta de ' . $fila['tipo']);
                @endphp
                <tr>
                    <td class="text-center">{{ $fecha }}</td>
                    <td>{!! $descripcion !!}</td>
                    <td class="text-center">{{ $estabTxt }}</td>
                    <td>{!! nl2br(e($participantesTxt)) !!}</td>
                    <td>
                        <div style="margin-bottom: 15px; display: block;">{{ $actaTxt }}</div>
                        @if(!empty($fila['imagenes_paths']) && is_array($fila['imagenes_paths']))
                            <div class="evidencia-container">
                                @foreach($fila['imagenes_paths'] as $imgPath)
                                    @if(file_exists($imgPath))
                                        @php
                                            $type = pathinfo($imgPath, PATHINFO_EXTENSION);
                                            $data = file_get_contents($imgPath);
                                            $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
                                        @endphp
                                        <img src="{{ $base64 }}" class="evidencia-img">
                                    @endif
                                @endforeach
                            </div>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
