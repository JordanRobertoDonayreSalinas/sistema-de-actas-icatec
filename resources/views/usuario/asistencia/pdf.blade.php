<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Acta de Asistencia Técnica - {{ $acta->id }}</title>
    <style>
        @page { margin: 1.5cm; }
        body { background-color: white !important; margin: 0; padding: 0; }
        
        .acta-documento {
            font-family: "DejaVu Sans", Arial, sans-serif;
            font-size: 11px;
            color: #2e3a46;
            line-height: 1.4;
        }

        .acta-documento h1 {
            text-align: center;
            color: #0d47a1;
            font-size: 18px;
            margin-bottom: 10px;
            text-transform: uppercase;
            border-bottom: 3px solid #1976d2;
            padding-bottom: 5px;
        }

        .acta-documento h3 {
            margin-top: 15px;
            margin-bottom: 8px;
            color: #0d47a1;
            font-size: 13px;
            font-weight: bold;
            border-left: 4px solid #1976d2;
            padding-left: 8px;
        }

        .info-card {
            border: 1px solid #e0e6ed;
            border-radius: 6px;
            padding: 10px;
            background: #f9fbfd;
            margin-bottom: 10px;
        }

        .info-general { width: 100%; border-collapse: collapse; }
        .info-general td { padding: 4px 8px; border-bottom: 1px solid #eef3f7; }
        .info-general td.label { width: 30%; font-weight: bold; color: #0d47a1; }

        table.participantes { width: 100%; border-collapse: collapse; margin-top: 5px; }
        table.participantes thead th {
            background: #1976d2; color: #ffffff; padding: 6px; font-size: 10px;
        }
        table.participantes tbody td {
            border: 1px solid #e6ecf3; padding: 5px; font-size: 9px; text-align: center;
        }

        /* Estilo para Evidencias Fotográficas */
        .foto-item {
            border: 1px solid #cfd8dc;
            padding: 5px;
            background: #fff;
            text-align: center;
        }
        .foto-item img {
            width: 100%;
            height: auto;
            max-height: 250px; /* Evita que una foto desborde la página */
            object-fit: contain; /* Muestra la imagen completa sin recortes */
            background-color: #f5f5f5;
        }
        .foto-caption {
            font-size: 9px;
            font-weight: bold;
            margin-top: 5px;
            color: #333;
            text-transform: uppercase;
        }

        /* Panel de Firmas */
        table.firmas { width: 100%; border-collapse: collapse; margin-top: 15px; table-layout: fixed; }
        table.firmas td {
            width: 50%; text-align: center; vertical-align: bottom;
            padding: 20px 5px 10px 5px; border: 1px solid #e0e6ed;
        }
        .linea-firma {
            border-top: 1px solid #333;
            margin: 45px auto 5px auto;
            width: 80%;
        }
        .nombre-firma { font-weight: bold; color: #0d47a1; font-size: 9px; display: block; text-transform: uppercase; }
        .detalles-firma { font-size: 8px; color: #444; display: block; margin-top: 2px; }
        
        .page-break { page-break-after: always; }
    </style>
</head>
<body>

    <div class="acta-documento">
        
        <h1>ACTA DE ASISTENCIA TÉCNICA #{{ str_pad($acta->id, 5, '0', STR_PAD_LEFT) }}</h1>

        <h3>📑 Información General</h3>
        <div class="info-card">
            <table class="info-general">
                <tbody>
                    <tr><td class="label">Fecha</td><td>{{ \Carbon\Carbon::parse($acta->fecha)->format('d/m/Y') }}</td></tr>
                    <tr><td class="label">Establecimiento</td><td>{{ $acta->establecimiento->nombre ?? '---' }}</td></tr>
                    <tr><td class="label">Responsable del EESS</td><td>{{ $acta->responsable }}</td></tr>
                    <tr><td class="label">Tema / Motivo</td><td>{{ $acta->tema }}</td></tr>
                    <tr><td class="label">Modalidad</td><td>{{ $acta->modalidad }}</td></tr>
                    <tr><td class="label">Implementador</td><td>{{ $acta->implementador }}</td></tr>
                </tbody>
            </table>
        </div>

        <h3>👥 Participantes</h3>
        <table class="participantes">
            <thead>
                <tr><th>DNI</th><th>Apellidos</th><th>Nombres</th><th>Cargo</th><th>Módulo</th></tr>
            </thead>
            <tbody>
                @forelse($acta->participantes as $p)
                    <tr>
                        <td>{{ $p->dni }}</td>
                        <td>{{ $p->apellidos }}</td>
                        <td>{{ $p->nombres }}</td>
                        <td>{{ $p->cargo }}</td>
                        <td>{{ $p->modulo }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5">Sin participantes registrados</td></tr>
                @endforelse
            </tbody>
        </table>

        <h3>📌 Actividades Desarrolladas</h3>
        <div class="info-card">
            @foreach($acta->actividades as $idx => $a)
                <div style="margin-bottom: 3px;">{{ $idx + 1 }}. {{ $a->descripcion }}</div>
            @endforeach
        </div>

        <h3>🤝 Acuerdos y Compromisos</h3>
        <div class="info-card">
            @foreach($acta->acuerdos as $idx => $a)
                <div style="margin-bottom: 3px;">{{ $idx + 1 }}. {{ $a->descripcion }}</div>
            @endforeach
        </div>

        @if($acta->observaciones->count() > 0)
            <h3>📝 Observaciones</h3>
            <div class="info-card">
                @foreach($acta->observaciones as $idx => $o)
                    <div style="margin-bottom: 3px;">{{ $idx + 1 }}. {{ $o->descripcion }}</div>
                @endforeach
            </div>
        @endif

        {{-- Procesamiento de Imágenes a Base64 --}}
        @php
            $imageFields = ['imagen1','imagen2','imagen3','imagen4','imagen5'];
            $imagenesSrc = [];
            foreach ($imageFields as $field) {
                if ($acta->$field && file_exists(storage_path('app/public/' . $acta->$field))) {
                    $path = storage_path('app/public/' . $acta->$field);
                    $type = pathinfo($path, PATHINFO_EXTENSION);
                    $data = file_get_contents($path);
                    $imagenesSrc[] = 'data:image/' . $type . ';base64,' . base64_encode($data);
                }
            }
        @endphp

        @if(count($imagenesSrc) > 0)
            <div class="page-break"></div>
            <h3>📷 Evidencias Fotográficas</h3>
            <table style="width: 100%; border-collapse: collapse;">
                @foreach(array_chunk($imagenesSrc, 2) as $fila)
                    <tr>
                        @foreach($fila as $idx_interno => $src)
                            <td style="width: 50%; padding: 5px; vertical-align: top;">
                                <div class="foto-item">
                                    <img src="{{ $src }}">
                                    <div class="foto-caption">Evidencia {{ $loop->parent->index * 2 + $loop->iteration }}</div>
                                </div>
                            </td>
                        @endforeach
                        @if(count($fila) < 2)
                            <td style="width: 50%;"></td>
                        @endif
                    </tr>
                @endforeach
            </table>
        @endif

        {{-- Panel de Firmas --}}
        <div class="page-break"></div>
        <h3>✍ Panel de Firmas</h3>
        <table class="firmas">
            <tr>
                <td>
                    <div class="linea-firma"></div>
                    <span class="nombre-firma">{{ $acta->implementador }}</span>
                    <span class="detalles-firma">IMPLEMENTADOR(A)</span>
                </td>
                <td>
                    <div class="linea-firma"></div>
                    <span class="nombre-firma">{{ $acta->responsable }}</span>
                    <span class="detalles-firma">JEFE DEL ESTABLECIMIENTO</span>
                </td>
            </tr>

            @php $participantes = $acta->participantes; @endphp
            @for ($i = 0; $i < count($participantes); $i += 2)
                <tr>
                    <td>
                        <div class="linea-firma"></div>
                        <span class="nombre-firma">{{ $participantes[$i]->apellidos }} {{ $participantes[$i]->nombres }}</span>
                        <span class="detalles-firma">{{ $participantes[$i]->modulo ?? 'PARTICIPANTE' }}</span>
                    </td>
                    @if(isset($participantes[$i+1]))
                        <td>
                            <div class="linea-firma"></div>
                            <span class="nombre-firma">{{ $participantes[$i+1]->apellidos }} {{ $participantes[$i+1]->nombres }}</span>
                            <span class="detalles-firma">{{ $participantes[$i+1]->modulo ?? 'PARTICIPANTE' }}</span>
                        </td>
                    @else
                        <td style="border: none;"></td>
                    @endif
                </tr>
            @endfor
        </table>
    </div>
</body>
</html>