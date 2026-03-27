<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>AAT Nº {{ str_pad($acta->id, 3, '0', STR_PAD_LEFT) }} - {{ mb_strtoupper($acta->establecimiento->nombre ?? 'SIN ESTABLECIMIENTO') }}</title>
    <style>
        @page {
            margin: 2cm 1.5cm 2.5cm 1.5cm;
        }
        
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 10.5px;
            color: #334155;
            line-height: 1.5;
            background-color: #ffffff;
            margin: 0;
            padding: 0;
        }

        /* Top Accent Bar (Material App Bar) */
        .top-accent {
            background-color: #059669; /* Emerald 600 */
            color: white;
            padding: 15px 20px;
            margin-bottom: 25px;
            border-radius: 6px;
        }
        .top-accent table {
            width: 100%;
        }
        .top-accent .header-title {
            font-size: 18px;
            font-weight: bold;
            color: #ffffff;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .top-accent .header-subtitle {
            font-size: 10px;
            color: #a7f3d0; /* Emerald 200 */
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 2px;
        }
        .top-accent .doc-number {
            font-size: 16px;
            font-weight: bold;
            color: #ffffff;
            text-align: right;
        }
        .top-accent .doc-date {
            font-size: 10px;
            color: #ecfdf5; /* Emerald 50 */
            margin-top: 2px;
            text-align: right;
        }

        /* Material Sections */
        h3 {
            font-size: 11px;
            background-color: #059669; /* Fondo sólido Emerald */
            color: #ffffff;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 8px 12px;
            border-radius: 4px;
            margin-top: 35px;
            margin-bottom: 15px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        /* Info Grid */
        table.info-grid {
            width: 100%;
            border-collapse: collapse;
            background-color: #f8fafc;
            border-radius: 6px;
            overflow: hidden;
            border: 1px solid #e2e8f0;
        }
        table.info-grid td {
            padding: 10px 12px;
            vertical-align: top;
            border-bottom: 1px solid #e2e8f0;
        }
        table.info-grid td.label {
            width: 35%;
            font-weight: bold;
            color: #047857; /* Emerald 700 */
            text-transform: uppercase;
            font-size: 9px;
            letter-spacing: 0.5px;
            background-color: #ecfdf5; /* Fondo claro esmeralda */
        }
        table.info-grid td.value {
            color: #0f172a;
            font-size: 11px;
        }

        /* Data Tables */
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            overflow: hidden;
        }
        table.data-table th {
            text-align: left;
            padding: 10px 8px;
            font-size: 9px;
            background-color: #047857; /* Emerald Darker */
            color: #ffffff;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        table.data-table td {
            padding: 10px 8px;
            border-bottom: 1px solid #f1f5f9;
            color: #0f172a;
            vertical-align: top;
        }

        /* Lists */
        .list-box {
            background-color: #f8fafc;
            border-radius: 6px;
            padding: 15px;
            border: 1px solid #e2e8f0;
            border-left: 4px solid #10b981; /* Acento verde izquierdo Material */
        }
        .list-item {
            margin-bottom: 8px;
            padding-left: 15px;
            position: relative;
            color: #1e293b;
        }
        .list-item:last-child { margin-bottom: 0; }
        .list-item:before {
            content: "•";
            position: absolute;
            left: 0;
            color: #059669; /* Emerald pulido */
            font-weight: bold;
        }

        /* Images */
        .evidencias-table { width: 100%; border-collapse: separate; border-spacing: 15px 0; margin-top: 5px; }
        .evidencias-table td { width: 50%; vertical-align: top; padding-bottom: 20px; }
        .foto-item {
            border: 1px solid #e2e8f0;
            border-bottom: 4px solid #34d399; /* Acento colorido abajo */
            border-radius: 6px;
            padding: 8px;
            text-align: center;
            page-break-inside: avoid;
            background-color: #ffffff;
        }
        .foto-item img {
            width: 100%; max-height: 200px; object-fit: contain; border-radius: 4px;
        }
        .foto-caption {
            font-size: 9px; color: #047857; margin-top: 8px; text-transform: uppercase; letter-spacing: 0.5px; font-weight: bold;
        }

        /* Signatures */
        table.firmas { width: 100%; border-collapse: separate; border-spacing: 30px 0; margin-top: 40px; table-layout: fixed; }
        table.firmas td { width: 50%; text-align: center; vertical-align: bottom; padding-bottom: 30px; page-break-inside: avoid; }
        .linea-firma { border-top: 1px solid #1e293b; margin: 70px auto 8px auto; width: 85%; }
        .nombre-firma { font-weight: bold; color: #0f172a; font-size: 10px; text-transform: uppercase; display: block; }
        .detalles-firma { font-size: 8.5px; color: #059669; font-transform: uppercase; letter-spacing: 0.5px; display: block; margin-top: 3px; font-weight: bold; }

        /* Footer */
        footer {
            position: fixed;
            bottom: -1.7cm;
            left: 0cm;
            right: 0cm;
            height: 1cm;
            border-top: 2px solid #10b981;
            padding-top: 8px;
            font-size: 9px;
            color: #64748b;
        }
        .footer-table { width: 100%; border-collapse: collapse; }
        .footer-table td { padding: 0; }
        .txt-left { text-align: left; }
        .txt-right { text-align: right; }
        
        /* Solución paginador DomPdf segura */
        .page-info:after { content: "PÁG. " counter(page); }

    </style>
</head>
<body>
    <!-- FOOTER GLOBALES -->
    <footer>
        <table class="footer-table">
            <tr>
                <td class="txt-left" style="letter-spacing: 0.5px; text-transform: uppercase; font-weight: bold; color: #047857;">Sistema Actas de Asistencias Técnicas</td>
                <td class="txt-right"><span class="page-info"></span></td>
            </tr>
        </table>
    </footer>

    <!-- CONTENIDO PRINCIPAL -->
    <main>
        
        <div class="top-accent">
            <table>
                <tr>
                    <td>
                        <h1 class="header-title">Asistencia Técnica</h1>
                        <div class="header-subtitle">Registro de Información</div>
                    </td>
                    <td style="vertical-align: top;">
                        <div class="doc-number">AAT Nº {{ str_pad($acta->id, 3, '0', STR_PAD_LEFT) }}</div>
                        <div class="doc-date">FECHA: {{ \Carbon\Carbon::parse($acta->fecha)->format('d/m/Y') }}</div>
                    </td>
                </tr>
            </table>
        </div>

        <h3>Información General</h3>
        <table class="info-grid">
            <tbody>
                <tr><td class="label">Establecimiento</td><td class="value" style="font-weight: bold;">{{ $acta->establecimiento->nombre ?? '---' }}</td></tr>
                <tr><td class="label">Responsable del EESS</td><td class="value">{{ $acta->responsable }}</td></tr>
                <tr><td class="label">Tema / Motivo</td><td class="value">{{ $acta->tema }}</td></tr>
                <tr><td class="label">Modalidad</td><td class="value">{{ $acta->modalidad }}</td></tr>
                <tr><td class="label">Implementador</td><td class="value">{{ $acta->implementador }}</td></tr>
            </tbody>
        </table>

        <h3>Participantes</h3>
        <table class="data-table">
            <thead>
                <tr><th style="width:15%">DNI</th><th style="width:40%">Nombres y Apellidos</th><th style="width:25%">Cargo</th><th style="width:20%">Módulo</th></tr>
            </thead>
            <tbody>
                @forelse($acta->participantes as $p)
                    <tr>
                        <td style="font-family: monospace; font-size: 11.5px;">{{ $p->dni }}</td>
                        <td style="text-transform: uppercase; font-weight: bold;">{{ $p->nombres }} {{ $p->apellidos }}</td>
                        <td>{{ $p->cargo }}</td>
                        <td>{{ $p->modulo }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" style="text-align:center; padding: 25px 0; color:#94a3b8; font-style: italic;">No se registraron participantes.</td></tr>
                @endforelse
            </tbody>
        </table>

        <h3>Actividades Desarrolladas</h3>
        <div class="list-box">
            @foreach($acta->actividades as $a)
                <div class="list-item">{{ $a->descripcion }}</div>
            @endforeach
        </div>

        <h3>Acuerdos y Compromisos</h3>
        <div class="list-box">
            @foreach($acta->acuerdos as $a)
                <div class="list-item">{{ $a->descripcion }}</div>
            @endforeach
        </div>

        @if($acta->observaciones->count() > 0)
            <h3>Observaciones</h3>
            <div class="list-box">
                @foreach($acta->observaciones as $o)
                    <div class="list-item">{{ $o->descripcion }}</div>
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
            <h3>Evidencias Fotográficas</h3>
            <table class="evidencias-table">
                @foreach(array_chunk($imagenesSrc, 2) as $fila)
                    <tr>
                        @foreach($fila as $idx_interno => $src)
                            <td>
                                <div class="foto-item">
                                    <img src="{{ $src }}">
                                    <div class="foto-caption">Evidencia Fotográfica {{ $loop->parent->index * 2 + $loop->iteration }}</div>
                                </div>
                            </td>
                        @endforeach
                        @if(count($fila) < 2)
                            <td></td>
                        @endif
                    </tr>
                @endforeach
            </table>
        @endif

        {{-- Panel de Firmas --}}
        <h3>Panel de Firmas</h3>
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
    </main>
</body>
</html>