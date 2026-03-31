<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>AAT Nº {{ str_pad($acta->id, 5, '0', STR_PAD_LEFT) }} - {{ mb_strtoupper($acta->establecimiento->nombre ?? 'SIN ESTABLECIMIENTO') }}</title>
    <style>
        @page { margin: 1.5cm 1.5cm 2cm 1.5cm; }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11px;
            color: #1e293b;
            line-height: 1.4;
            background-color: #ffffff;
            margin: 0; padding: 0;
        }

        /* Header */
        .text-center { text-align: center; }
        .header-title {
            font-size: 18px; font-weight: bold; color: #065f46; 
            text-transform: uppercase; margin: 0; letter-spacing: 0.5px;
            padding-bottom: 12px;
            border-bottom: 3px solid #065f46;
            margin-bottom: 25px;
        }

        /* Sections */
        h3 {
            font-size: 14px;
            color: #065f46;
            margin-top: 25px;
            margin-bottom: 12px;
            padding-left: 8px;
            border-left: 4px solid #065f46;
            font-weight: bold;
            line-height: 14px;
        }
        .icon {
            display: inline-block;
            border: 1.5px solid #065f46;
            width: 11px; height: 13px;
            margin-left: 3px;
            margin-right: 8px;
            position: relative;
            top: 2px;
        }
        .icon-line {
            position: absolute;
            left: 4px; top: 0;
            height: 13px; width: 1.5px;
            background-color: #065f46;
        }

        /* Info Grid */
        table.info-grid {
            width: 100%; border-collapse: collapse; margin-bottom: 15px;
            border: 1px solid #cbd5e1; border-radius: 4px;
        }
        table.info-grid td { padding: 10px 12px; border-bottom: 1px solid #e2e8f0; }
        table.info-grid tr:last-child td { border-bottom: none; }
        table.info-grid td.label {
            width: 30%; font-weight: bold; color: #065f46;
            font-size: 11px; background-color: #f8fafc; border-right: 1px solid #e2e8f0;
        }
        table.info-grid td.value { color: #334155; font-size: 11px; text-transform: uppercase; }

        /* Data Tables */
        table.data-table {
            width: 100%; border-collapse: collapse; margin-bottom: 20px;
            border: 1px solid #cbd5e1;
            text-align: center;
        }
        table.data-table th {
            padding: 10px; font-size: 11px;
            background-color: #059669; color: #ffffff;
            font-weight: bold; border-left: 1px solid #10b981; border-right: 1px solid #10b981;
        }
        table.data-table th:first-child { border-left: none; }
        table.data-table th:last-child { border-right: none; }
        
        table.data-table td {
            padding: 10px; border: 1px solid #cbd5e1;
            color: #334155; vertical-align: middle;
        }

        /* Lists Box */
        .box-container {
            border: 1px solid #cbd5e1; border-radius: 4px; padding: 12px 15px;
            background-color: #f8fafc; margin-bottom: 15px;
            color: #334155;
            min-height: 25px;
        }
        .list-item { margin-bottom: 8px; }
        .list-item:last-child { margin-bottom: 0; }

        /* Images */
        .evidencias-table { width: 100%; border-collapse: separate; border-spacing: 15px; margin-top: 5px; margin-left: -15px; width: calc(100% + 30px); }
        .evidencias-table td { width: 50%; vertical-align: top; }
        .foto-item {
            border: 1px solid #cbd5e1; padding: 10px;
            text-align: center; background-color: #ffffff; border-radius: 4px;
        }
        .foto-item img { width: 100%; max-height: 240px; object-fit: contain; border: 1px solid #e2e8f0; }
        .foto-caption {
            font-size: 12px; color: #1e293b; margin-top: 10px;
            font-weight: bold; letter-spacing: 0.5px;
        }

        /* Signatures */
        table.firmas { width: 100%; border-collapse: collapse; margin-top: 20px; border: 1px solid #e2e8f0; }
        table.firmas td { width: 50%; text-align: center; vertical-align: bottom; padding: 35px 15px 15px 15px; border: 1px solid #e2e8f0; page-break-inside: avoid; }
        .linea-firma { border-top: 1px solid #1e293b; margin: 30px auto 8px auto; width: 80%; }
        .nombre-firma { font-weight: bold; color: #065f46; font-size: 10px; text-transform: uppercase; display: block; }
        .detalles-firma { font-size: 9px; color: #64748b; text-transform: uppercase; display: block; margin-top: 4px; }

        /* Footer */
        footer {
            position: fixed; bottom: -1cm; left: 0cm; right: 0cm; height: 1cm;
            font-size: 9px; color: #94a3b8;
        }
        .footer-table { width: 100%; border-collapse: collapse; }
        .footer-table td { padding: 0; }
        .txt-left { text-align: left; }
        .txt-right { text-align: right; }
        .page-info:after { content: "Página " counter(page); }
    </style>
</head>
<body>
    <footer>
        <table class="footer-table">
            <tr>
                <td class="txt-left">SISTEMA DE ACTAS DE ASISTENCIAS TÉCNICAS</td>
                <td class="txt-right"><span class="page-info"></span></td>
            </tr>
        </table>
    </footer>

    <main>
        <div class="text-center">
            <h1 class="header-title">ACTA DE ASISTENCIA TÉCNICA #{{ str_pad($acta->id, 5, '0', STR_PAD_LEFT) }}</h1>
        </div>

        <h3>
            <span class="icon"><span class="icon-line"></span></span>Información General
        </h3>
        <table class="info-grid">
            <tbody>
                <tr><td class="label">Fecha</td><td class="value">{{ \Carbon\Carbon::parse($acta->fecha)->format('d/m/Y') }}</td></tr>
                <tr><td class="label">Establecimiento</td><td class="value">{{ $acta->establecimiento->nombre ?? '---' }}</td></tr>
                <tr><td class="label">Responsable del EESS</td><td class="value">{{ $acta->responsable }}</td></tr>
                <tr><td class="label">Tema / Motivo</td><td class="value">{{ $acta->tema }}</td></tr>
                <tr><td class="label">Modalidad</td><td class="value">{{ $acta->modalidad }}</td></tr>
                <tr><td class="label">Implementador</td><td class="value">{{ $acta->implementador }}</td></tr>
            </tbody>
        </table>

        <h3>
            <span class="icon"><span class="icon-line"></span></span>Participantes
        </h3>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width:12%">DNI</th>
                    <th style="width:22%">Apellidos</th>
                    <th style="width:23%">Nombres</th>
                    <th style="width:15%">Cargo</th>
                    <th style="width:28%">Módulo</th>
                </tr>
            </thead>
            <tbody>
                @forelse($acta->participantes as $p)
                    <tr>
                        <td style="font-family: monospace;">{{ $p->dni }}</td>
                        <td style="text-transform: uppercase;">{{ $p->apellidos }}</td>
                        <td style="text-transform: uppercase;">{{ $p->nombres }}</td>
                        <td style="text-transform: uppercase;">{{ $p->cargo }}</td>
                        <td style="text-transform: uppercase;">{{ $p->modulo }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" style="padding: 15px; color:#94a3b8; font-style: italic;">No se registraron participantes.</td></tr>
                @endforelse
            </tbody>
        </table>

        <h3>
            <span class="icon"><span class="icon-line"></span></span>Actividades Desarrolladas
        </h3>
        <div class="box-container">
            @forelse($acta->actividades as $index => $a)
                <div class="list-item">{{ $index + 1 }}. {{ $a->descripcion }}</div>
            @empty
                <div class="list-item" style="color: #94a3b8; font-style: italic;">Sin actividades.</div>
            @endforelse
        </div>

        <h3>
            <span class="icon"><span class="icon-line"></span></span>Acuerdos y Compromisos
        </h3>
        <div class="box-container">
            @forelse($acta->acuerdos as $index => $a)
                <div class="list-item">{{ $index + 1 }}. {{ $a->descripcion }}</div>
            @empty
                <div class="list-item" style="color: #94a3b8; font-style: italic;">Sin acuerdos registrados.</div>
            @endforelse
        </div>

        @if($acta->observaciones->count() > 0)
            <h3>
                <span class="icon"><span class="icon-line"></span></span>Observaciones
            </h3>
            <div class="box-container">
                @foreach($acta->observaciones as $index => $o)
                    <div class="list-item">{{ $index + 1 }}. {{ $o->descripcion }}</div>
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
            <div style="page-break-inside: avoid;">
                <h3>
                    <span class="icon"><span class="icon-line"></span></span>Evidencias Fotográficas
                </h3>
                <table class="evidencias-table">
                    @foreach(array_chunk($imagenesSrc, 2) as $fila)
                        <tr>
                            @foreach($fila as $idx_interno => $src)
                                <td>
                                    <div class="foto-item">
                                        <img src="{{ $src }}">
                                        <div class="foto-caption">EVIDENCIA {{ $loop->parent->index * 2 + $loop->iteration }}</div>
                                    </div>
                                </td>
                            @endforeach
                            @if(count($fila) < 2)
                                <td></td>
                            @endif
                        </tr>
                    @endforeach
                </table>
            </div>
        @endif

        {{-- Panel de Firmas --}}
        <div style="page-break-inside: avoid;">
            <h3>
                <span class="icon"><span class="icon-line"></span></span>Panel de Firmas
            </h3>
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
                            <span class="nombre-firma">{{ $participantes[$i]->nombres }} {{ $participantes[$i]->apellidos }}</span>
                            <span class="detalles-firma">{{ $participantes[$i]->modulo ?? 'PARTICIPANTE' }}</span>
                        </td>
                        @if(isset($participantes[$i+1]))
                            <td>
                                <div class="linea-firma"></div>
                                <span class="nombre-firma">{{ $participantes[$i+1]->nombres }} {{ $participantes[$i+1]->apellidos }}</span>
                                <span class="detalles-firma">{{ $participantes[$i+1]->modulo ?? 'PARTICIPANTE' }}</span>
                            </td>
                        @else
                            <td style="border: none;"></td>
                        @endif
                    </tr>
                @endfor
            </table>
        </div>
    </main>
</body>
</html>