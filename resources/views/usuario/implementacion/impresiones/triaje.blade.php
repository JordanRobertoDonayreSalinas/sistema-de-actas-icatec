<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>AI Nº {{$acta->id}} - {{$acta->modulo}} - {{$acta->nombre_establecimiento}}</title>
            <style>
        /* Configuración de Página */
        @page { margin: 1.5cm 2cm 2cm 2cm; }
        body { 
            font-family: 'DejaVu Sans', 'Helvetica', 'Arial', sans-serif; 
            font-size: 10px; 
            color: #334155; 
            line-height: 1.5; 
            margin: 0;
        }

        /* Encabezado */
        .header { 
            text-align: center; 
            margin-bottom: 20px; 
            border-bottom: 2px solid #e9d5ff;
            padding-bottom: 10px;
        }
        .header h1 { 
            margin: 0; 
            font-size: 16px; 
            color: #4c1d95; 
            text-transform: uppercase; 
        }
        .header .establishment {
            margin-top: 5px;
            font-size: 11px;
            color: #4c1d95;
            font-weight: bold;
            text-transform: uppercase;
        }

        /* Títulos de Sección */
        .section-header { 
            background-color: #f8fafc; 
            padding: 6px 12px; 
            font-weight: bold; 
            font-size: 10px;
            color: #6b21a8; 
            border-left: 4px solid #6b21a8; 
            margin: 15px 0 10px 0; 
            text-transform: uppercase;
        }

        /* Tablas */
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-bottom: 10px; 
        }
        th, td { 
            border: 1px solid #e2e8f0; 
            padding: 6px 10px; 
            word-wrap: break-word;
        }
                th {
            background-color: #f3e8ff;
            text-align: left;
            font-size: 8px;
            color: #6b21a8;
            text-transform: uppercase;
        }
        td.bg-label { 
            background-color: #faf5ff; 
            font-weight: bold; 
            width: 30%; 
            color: #6b21a8;
        }
        .uppercase { text-transform: uppercase; }

        /* Fotos wrapper modificado para modo de tabla DomPDF */
        table.foto-table {
            border: none;
            width: 100%;
            margin: 10px 0;
        }
        table.foto-table td {
            border: none;
            padding: 5px;
            width: 50%;
            vertical-align: top;
            text-align: center;
        }
        .foto-wrapper {
            border: 1px solid #cbd5e1;
            background-color: #f8fafc;
            border-radius: 6px;
            padding: 10px;
            margin: 0;
        }
        .foto-wrapper img {
            max-width: 100%;
            max-height: 180px;
            height: auto;
            object-fit: contain;
        }
        .foto-caption {
            font-size: 8px;
            font-weight: bold;
            color: #1e293b;
            margin-top: 10px;
            text-transform: uppercase;
        }

        /* Firmas */
        .firmas-grid {
            width: 100%;
            margin-top: 10px;
            text-align: center;
        }
        .firma-card { 
            width: 31%; 
            display: inline-block; 
            vertical-align: top; 
            margin: 5px 1%;
            border: 1px solid #cbd5e1; 
            background-color: #ffffff;
            border-radius: 4px;
            padding: 15px 5px 10px 5px;
        }
        .linea-firma { 
            border-top: 1px solid #94a3b8; 
            margin: 40px auto 8px auto; 
            width: 85%; 
        }
        .nombre-firma { 
            font-size: 8px; 
            font-weight: bold; 
            color: #0f172a;
            display: block;
            text-transform: uppercase;
            min-height: 20px;
        }
        .cargo-firma { 
            font-size: 7px; 
            color: #64748b; 
            display: block;
            text-transform: uppercase;
        }

        /* Footer */
        .no-break { page-break-inside: avoid; }
    </style>
</head>
<body>
        <div class="header">
        <h1>ACTA DE IMPLEMENTACIÓN DEL SIHCE - REGIÓN ICA</h1>
        <div class="establishment">ESTABLECIMIENTO: {{ $acta->codigo_establecimiento }} - {{ strtoupper($acta->nombre_establecimiento) }}</div>
    </div>

    <div class="section">
        <div class="section-header"><span style="color:#6b21a8;">&#9638;</span> DEFINICIÓN</div>
        <table>
            <tr>
                <td class="bg-label"><strong>Documento</strong></td>
                <td>{{ $acta->modulo . ' #' . $acta->id }}</td>
            </tr>
            <tr>
                <td class="bg-label"><strong>Fecha</strong></td>
                <td>{{ \Carbon\Carbon::parse($acta->fecha)->format('d/m/Y') }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-header"><span style="color:#6b21a8;">&#8962;</span> DATOS DEL ESTABLECIMIENTO</div>
        <table>
            <tr>
                <td class="bg-label"><strong>Establecimiento</strong></td>
                <td>{{ $acta->codigo_establecimiento . ' - ' . $acta->nombre_establecimiento }}</td>
            </tr>
            <tr>
                <td class="bg-label"><strong>Provincia / Distrito</strong></td>
                <td>{{ $acta->provincia }}, {{ $acta->distrito }}</td>
            </tr>
            <tr>
                <td class="bg-label"><strong>Red / Microred</strong></td>
                <td>{{ $acta->red }} / {{ $acta->microred }}</td>
            </tr>
            <tr>
                <td class="bg-label"><strong>Responsable</strong></td>
                <td>{{ $acta->responsable }}</td>
            </tr>
        </table>
    </div>

    


    <div class="section">
        <div class="section-header"><span style="color:#6b21a8;">&#9881;</span> MÓDULO IMPLEMENTADO</div>
        <table>
            <tr>
                <td class="bg-label"><strong>Módulo</strong></td>
                <td>{{ $acta->modulo }}</td>
            </tr>
        </table>
    </div>


    
    <div class="section">
        <div class="section-header"><span style="color:#6b21a8;">&#9679;</span> USUARIO(S) DEL MÓDULO</div>
        <table>
            <thead>
                <tr>
                    <th class="bg-label">DNI</th>
                    <th class="bg-label">Apellidos y Nombres</th>
                    <th class="bg-label">Celular</th>
                    <th class="bg-label">Permisos</th>
                </tr>
            </thead>
            <tbody>
                @foreach($acta->usuarios as $p)
                <tr>
                    <td>{{ $p->dni }}</td>
                    <td>{{ $p->apellido_paterno }} {{ $p->apellido_materno }} {{ $p->nombres }}</td>
                    <td>{{ $p->celular }}</td>
                    <td>{{ $p->permisos }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    

    
    {{-- === SECCIÓN RENIPRESS (AUTOMÁTICO) === --}}
    @if(!empty($acta->renipress_data))
    <div class="section">
        <div class="section-header"><span style="color:#6b21a8;">&#9432;</span> SERVICIOS AUTORIZADOS (RENIPRESS)</div>
        <table style="table-layout: fixed;">
            <tr>
                <td style="vertical-align: top; width: 50%; padding: 0;">
                    <table style="margin-bottom: 0; border: none;">
                        <thead><tr><th colspan="2" style="background-color: #f1f5f9; color: #475569;">UPSS</th></tr></thead>
                        <tbody>
                            @forelse($acta->renipress_data['upss'] ?? [] as $u)
                                <tr><td style="width: 40px; font-family: monospace; color: #94a3b8; font-size: 7px;">{{ $u['codigo'] }}</td><td style="font-size: 7px;">{{ $u['nombre'] }}</td></tr>
                            @empty
                                <tr><td colspan="2" style="text-align: center; color: #cbd5e1; font-style: italic; font-size: 7px;">No registra</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </td>
                <td style="vertical-align: top; width: 50%; padding: 0;">
                    <table style="margin-bottom: 0; border: none;">
                        <thead><tr><th colspan="2" style="background-color: #f1f5f9; color: #475569;">Servicios Autorizados</th></tr></thead>
                        <tbody>
                            @forelse($acta->renipress_data['servicios'] ?? [] as $u)
                                <tr><td style="width: 40px; font-family: monospace; color: #94a3b8; font-size: 7px;">{{ $u['codigo'] }}</td><td style="font-size: 7px;">{{ $u['nombre'] }}</td></tr>
                            @empty
                                <tr><td colspan="2" style="text-align: center; color: #cbd5e1; font-style: italic; font-size: 7px;">No registra</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </td>
            </tr>
            <tr>
                <td style="vertical-align: top; width: 50%; padding: 0;">
                    <table style="margin-bottom: 0; border: none;">
                        <thead><tr><th colspan="2" style="background-color: #f1f5f9; color: #475569;">Especialidades</th></tr></thead>
                        <tbody>
                            @forelse($acta->renipress_data['especialidades'] ?? [] as $e)
                                <tr><td style="width: 40px; font-family: monospace; color: #94a3b8; font-size: 7px;">{{ $e['codigo'] }}</td><td style="font-size: 7px;">{{ $e['nombre'] }}</td></tr>
                            @empty
                                <tr><td colspan="2" style="text-align: center; color: #cbd5e1; font-style: italic; font-size: 7px;">No registra</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </td>
                <td style="vertical-align: top; width: 50%; padding: 0;">
                    <table style="margin-bottom: 0; border: none;">
                        <thead><tr><th colspan="2" style="background-color: #f1f5f9; color: #475569;">Cartera de Servicios</th></tr></thead>
                        <tbody>
                            @forelse($acta->renipress_data['cartera'] ?? [] as $c)
                                <tr><td style="width: 40px; font-family: monospace; color: #94a3b8; font-size: 7px;">{{ $c['codigo'] }}</td><td style="font-size: 7px;">{{ $c['nombre'] }}</td></tr>
                            @empty
                                <tr><td colspan="2" style="text-align: center; color: #cbd5e1; font-style: italic; font-size: 7px;">No registra</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </td>
            </tr>
        </table>
    </div>
    @endif
<div style="margin-top: 15px;">
        <div class="section-header"><span style="color:#6b21a8;">&#10004;</span> COMPROMISO</div>
        <table style="border-collapse: collapse; width: 100%; margin-top: 5px;">
            <tr>
                <td style="border: 1px solid #000; padding: 10px; min-height: 60px;">
                    <b>El jefe del establecimiento se compromete:</b>   <br>

                    1. ACTUALIZAR en Renipress SUSALUD las UPPS y UPS añadidas para el funcionamiento del modulo en virtud a contar con profesionales que realizan la actividad. <br>

                    2. Garantizará la continuidad de lo implementado, en caso de presentarse inconvenientes comunicará al equipo implementador de la Unidad Ejecutora. <br>

                    3. Brindar las facilidades que se requieran que garanticen la carga de la programación de turnos y consultorios con 03 meses de anticipación de acuerdo a ley. <br><br>

                    

                    <b>La Unidad Ejecutora se compromete:</b> <br>

                    1. Brindar asistencia técnica permanente a los USUARIOS del Sihce. <br>

                    2. Crear y actualizar USUARIOS según necesidad del Establecimiento.<br>
                </td>
            </tr>
        </table>
    </div>
    
    <div style="margin-top: 15px;">
        <div class="section-header"><span style="color:#6b21a8;">&#9998;</span> OBSERVACIONES</div>
        <table style="border-collapse: collapse; width: 100%; margin-top: 5px;">
            <tr>
                <td style="border: 1px solid #000; padding: 10px; min-height: 60px;">
                    {!! nl2br(e($acta->observaciones)) !!}
                </td>
            </tr>
        </table>
    </div>

        @if($acta->foto1 || $acta->foto2)
    <div style="margin-top: 20px;">
        <div class="section-header"><span style="color:#6b21a8;">&#9635;</span> EVIDENCIA FOTOGRÁFICA</div>
        <table class="foto-table">
            <tr>
                @if($acta->foto1)
                <td>
                    <div class="foto-wrapper">
                        <img src="{{ storage_path('app/public/' . $acta->foto1) }}">
                        <div class="foto-caption">EVIDENCIA 01</div>
                    </div>
                </td>
                @endif
                @if($acta->foto2)
                <td>
                    <div class="foto-wrapper">
                        <img src="{{ storage_path('app/public/' . $acta->foto2) }}">
                        <div class="foto-caption">EVIDENCIA 02</div>
                    </div>
                </td>
                @endif
            </tr>
        </table>
    </div>
    @endif

    <div style="margin-top: 15px;"><div class="section-header"><span style="color:#6b21a8;">&#9997;</span> FIRMAS</div></div>



            <div style="margin: 10px 0;">
        <p>Dan fe de la veracidad de los datos consignados:</p>

        @php
            $firmantes = [];
            
            // 1. Jefe del Establecimiento
            $firmantes[] = [
                'cargo' => 'Jefe del establecimiento',
                'nombre' => !empty($acta->responsable) ? mb_strtoupper($acta->responsable) : '_________________________________',
                'dni' => '____________________',
                'tipo_doc' => 'DNI'
            ];

            // 2. Implementadores (dinámicos)
            foreach ($acta->implementadores as $i) {
                $profI = \App\Models\Profesional::where('doc', $i->dni)->first();
                $tipoDocI = $profI && !empty($profI->tipo_doc) ? mb_strtoupper($profI->tipo_doc) : 'DNI';
                $nombreCompleto = trim($i->apellido_paterno . ' ' . $i->apellido_materno . ', ' . $i->nombres);
                $cargo = trim($i->cargo);
                if (empty($cargo)) {
                    $cargo = 'Implementador DIRESA_ICATEC';
                }
                if (!empty($nombreCompleto) && $nombreCompleto != ', ') {
                    $firmantes[] = [
                        'cargo' => mb_strtoupper($cargo),
                        'nombre' => mb_strtoupper($nombreCompleto),
                        'dni' => $i->dni,
                        'tipo_doc' => $tipoDocI
                    ];
                }
            }

            

            // 4. Usuarios Participantes (dinámicos)
            foreach ($acta->usuarios as $u) {
                $profU = \App\Models\Profesional::where('doc', $u->dni)->first();
                $tipoDocU = $profU && !empty($profU->tipo_doc) ? mb_strtoupper($profU->tipo_doc) : (isset($u->tipo_doc) && !empty($u->tipo_doc) ? mb_strtoupper($u->tipo_doc) : 'DNI');
                $nombreCompleto = trim($u->apellido_paterno . ' ' . $u->apellido_materno . ', ' . $u->nombres);
                if (!empty($nombreCompleto) && $nombreCompleto != ', ') {
                    $firmantes[] = [
                        'cargo' => 'Participante de Implementación',
                        'nombre' => mb_strtoupper($nombreCompleto),
                        'dni' => $u->dni,
                        'tipo_doc' => $tipoDocU
                    ];
                }
            }

            // 5. Espacio en blanco si no se llenaron usuarios participantes
            if ($acta->usuarios->count() === 0) {
                $firmantes[] = [
                    'cargo' => 'Participante de Implementación',
                    'nombre' => '____________________________________',
                    'dni' => '____________________',
                    'tipo_doc' => 'DNI'
                ];
            }
        @endphp

                <div class="firmas-grid">
            @foreach ($firmantes as $f)
                <div class="firma-card no-break">
                    <div class="linea-firma"></div>
                    <span class="nombre-firma">{{ $f['nombre'] }}</span>
                    <span class="cargo-firma">{{ $f['cargo'] }}</span>
                </div>
            @endforeach
        </div>
    </div>

    <div style="margin-top: 15px;">
        <h4 style="margin-bottom: 5px;"><strong>Glosario</strong></h4>
        <p style="margin: 2px 0;">D.J. : Declaración Jurada </p>
        <p style="margin: 2px 0;">C.C. : Compromiso de Confidencialidad </p>
    </div>



    <script type="text/php">
        if (isset($pdf)) {
            $y = $pdf->get_height() - 30;
            $font = $fontMetrics->get_font("helvetica", "normal");
            $size = 8;
            $color = array(0.5, 0.5, 0.5);
            $pdf->page_script('$pdf->line(40, $pdf->get_height() - 40, $pdf->get_width() - 40, $pdf->get_height() - 40, array(0.7, 0.7, 0.7), 1);');
            
            $pdf->page_text(40, $y, "HERRAMIENTAS DE IMPLEMENTACION SIHCE", $font, $size, $color);
            $text = "PAG: {PAGE_NUM} / {PAGE_COUNT}";
            $dummyText = "PAG: 10 / 10";
            $width = $fontMetrics->get_text_width($dummyText, $font, $size);
            $x = $pdf->get_width() - $width - 40;
            $pdf->page_text($x, $y, $text, $font, $size, $color);
        }
    </script>
</body>
</html>