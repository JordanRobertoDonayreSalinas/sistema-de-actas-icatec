<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Acta de Reunión Nº {{ str_pad($reunion->id, 4, '0', STR_PAD_LEFT) }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10.5px;
            color: #000;
            margin: 0;
            padding: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            border: 1px solid #000;
        }
        td, th {
            border: 1px solid #000;
            padding: 4px 6px;
            vertical-align: middle;
        }
        .bg-blue {
            background-color: #17365d;
            color: #ffffff;
            font-weight: bold;
            text-align: center;
        }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .title {
            font-size: 12px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 8px;
        }
        ol {
            margin-top: 5px;
            margin-bottom: 5px;
            padding-left: 20px;
        }
        li {
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

    <div class="title">ACTA DE REUNIÓN</div>

    <!-- CABECERA -->
    <table>
        <tr>
            <td colspan="2" class="text-center font-bold" style="width: 70%; font-size: 11px;">
                {{ mb_strtoupper($reunion->titulo_reunion, 'UTF-8') }}
            </td>
            <td class="bg-blue" style="width: 10%;">FECHA</td>
            <td class="text-center font-bold" style="width: 20%;">
                {{ \Carbon\Carbon::parse($reunion->fecha_reunion)->format('d/m/Y') }}
            </td>
        </tr>
        <tr>
            <td class="bg-blue" style="width: 15%;">INSTITUCIÓN</td>
            <td class="text-center font-bold" style="width: 55%;">
                {{ mb_strtoupper($reunion->nombre_institucion, 'UTF-8') }}
            </td>
            <td class="bg-blue" style="width: 10%;">HORA</td>
            <td class="text-center font-bold" style="width: 20%;">
                {{ \Carbon\Carbon::parse($reunion->hora_reunion)->format('h:i A') }}
            </td>
        </tr>
    </table>

    <!-- INFORMACION GENERAL -->
    <table>
        <tr>
            <td class="bg-blue">INFORMACIÓN GENERAL</td>
        </tr>
        <tr>
            <td style="text-align: justify; padding: 10px; font-size: 11.5px;"> <!-- 1 punto más -->
                {{ $reunion->descripcion_general }}
            </td>
        </tr>
    </table>

    <!-- PARTICIPANTES -->
    <table>
        <tr>
            <td colspan="4" class="bg-blue">PARTICIPANTES</td>
        </tr>
        <tr>
            <td class="text-center font-bold" style="width: 35%;">PARTICIPANTE</td>
            <td class="text-center font-bold" style="width: 25%;">CARGO</td>
            <td class="text-center font-bold" style="width: 25%;">ENTIDAD</td>
            <td class="text-center font-bold" style="width: 15%;">FIRMA</td>
        </tr>
        @if(empty($reunion->participantes))
            <tr><td colspan="4" class="text-center">SIN PARTICIPANTES REGISTRADOS</td></tr>
        @else
            @foreach($reunion->participantes as $p)
            <tr>
                <td>{{ mb_strtoupper(($p['apellidos'] ?? '') . ' ' . ($p['nombres'] ?? ''), 'UTF-8') }}</td>
                <td class="text-center">{{ mb_strtoupper($p['cargo'] ?? '', 'UTF-8') }}</td>
                <td class="text-center">{{ mb_strtoupper($p['institucion'] ?? '', 'UTF-8') }}</td>
                <td style="padding: 12px;"></td>
            </tr>
            @endforeach
        @endif
    </table>

    <!-- ACUERDOS -->
    <table>
        <tr>
            <td class="bg-blue">ACUERDOS</td>
        </tr>
        <tr>
            <td style="text-align: justify; padding: 10px;">
                @if(!empty($reunion->acuerdos))
                    <ol>
                        @foreach($reunion->acuerdos as $ac)
                            <li>{{ mb_strtoupper($ac['descripcion'] ?? '', 'UTF-8') }}</li>
                        @endforeach
                    </ol>
                @else
                    <div class="text-center">SIN ACUERDOS RELEVANTES.</div>
                @endif
            </td>
        </tr>
    </table>

    <!-- OBSERVACIONES -->
    <table>
        <tr>
            <td class="bg-blue">OBSERVACIONES</td>
        </tr>
        <tr>
            <td style="text-align: justify; padding: 10px;">
                @if(!empty($reunion->comentarios_observaciones))
                    <ol>
                        @foreach($reunion->comentarios_observaciones as $ob)
                            <li>{{ mb_strtoupper($ob['descripcion'] ?? '', 'UTF-8') }}</li>
                        @endforeach
                    </ol>
                @else
                    <div class="text-center">SIN OBSERVACIONES RELEVANTES.</div>
                @endif
            </td>
        </tr>
    </table>

    <!-- PROCESAMIENTO DE IMÁGENES -->
    @php
        $imageFields = ['foto_1', 'foto_2'];
        $imagenesSrc = [];
        foreach ($imageFields as $field) {
            if (!empty($reunion->$field)) {
                $posiblesRutas = [
                    public_path($reunion->$field), 
                    storage_path('app/public/' . str_replace('storage/', '', $reunion->$field))
                ];
                
                $rutaEncontrada = null;
                foreach ($posiblesRutas as $ruta) {
                    if (file_exists($ruta) && is_file($ruta)) {
                        $rutaEncontrada = $ruta;
                        break;
                    }
                }
                
                if($rutaEncontrada){
                    $type = pathinfo($rutaEncontrada, PATHINFO_EXTENSION);
                    $data = file_get_contents($rutaEncontrada);
                    $imagenesSrc[] = 'data:image/' . $type . ';base64,' . base64_encode($data);
                }
            }
        }
    @endphp

    @if(count($imagenesSrc) > 0)
    <div style="page-break-inside: avoid;">
        <table>
            <tr>
                <td class="bg-blue">EVIDENCIA FOTOGRÁFICA</td>
            </tr>
            <tr>
                <td class="text-center" style="padding: 15px;">
                    @foreach($imagenesSrc as $src)
                        <img src="{{ $src }}" style="max-width: 45%; max-height: 250px; margin: 0 10px; display: inline-block;">
                    @endforeach
                </td>
            </tr>
            @if($reunion->hora_finalizada_reunion)
            <tr>
                <td style="border-top: 1px solid #000; padding: 5px; font-size: 10px;">
                    Siendo las {{ \Carbon\Carbon::parse($reunion->hora_finalizada_reunion)->format('h:i A') }}, se da por concluida la reunión, firmando los presentes.
                </td>
            </tr>
            @endif
        </table>
    </div>
    @endif

</body>
</html>
