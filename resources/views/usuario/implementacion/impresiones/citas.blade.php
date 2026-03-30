<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>AI Nº {{$acta->id}} - {{$acta->modulo}} - {{$acta->nombre_establecimiento}}</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        h1 { font-size: 18px;  text-align: center;}
        .section { margin-top: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 4px; font-size: 11px; }
        @page { margin-bottom: 1.5cm; }
    </style>
</head>
<body>
    <h1>ACTA DE IMPLEMENTACIÓN DEL SIHCE - REGIÓN ICA <br> SEGUN MODULOS</h1>

    <div class="section">
        <h3>Definicion</h3>
        <table>
            <tr>
                <td style="background-color: #0066cc; color: white; width: 150px;"><strong>Documento</strong></td>
                <td>{{ $acta->modulo . ' #' . $acta->id }}</td>
            </tr>
            <tr>
                <td style="background-color: #0066cc; color: white;"><strong>Fecha</strong></td>
                <td>{{ \Carbon\Carbon::parse($acta->fecha)->format('d/m/Y') }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <h3>Datos del Establecimiento</h3>
        <table>
            <tr>
                <td style="background-color: #0066cc; color: white; width: 150px;"><strong>Establecimiento</strong></td>
                <td>{{ $acta->codigo_establecimiento . ' - ' . $acta->nombre_establecimiento }}</td>
            </tr>
            <tr>
                <td style="background-color: #0066cc; color: white;"><strong>Provincia / Distrito</strong></td>
                <td>{{ $acta->provincia }}, {{ $acta->distrito }}</td>
            </tr>
            <tr>
                <td style="background-color: #0066cc; color: white;"><strong>Red / Microred</strong></td>
                <td>{{ $acta->red }} / {{ $acta->microred }}</td>
            </tr>
            <tr>
                <td style="background-color: #0066cc; color: white;"><strong>Responsable</strong></td>
                <td>{{ $acta->responsable }}</td>
            </tr>
        </table>
    </div>

    


    <div class="section">
        <h3>Módulo Implementado</h3>
        <table>
            <tr>
                <td style="background-color: #0066cc; color: white; width: 150px;"><strong>Módulo</strong></td>
                <td>{{ $acta->modulo }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <h3>Modalidad de Modulo Citas</h3>
        <table>
            <tr>
                <td style="background-color: #0066cc; color: white; width: 150px;"><strong>Módulo</strong></td>
                <td>{{ $acta->modalidad }}</td>
            </tr>
        </table>
    </div>


    
    <div class="section">
        <h3>Usuario(s) del Modulo</h3>
        <table>
            <thead>
                <tr>
                    <th style="background-color: #0066cc; color: white; width: 150px;">DNI</th>
                    <th style="background-color: #0066cc; color: white; width: 150px;">Apellidos y Nombres</th>
                    <th style="background-color: #0066cc; color: white; width: 150px;">Celular</th>
                    <th style="background-color: #0066cc; color: white; width: 150px;">Permisos</th>
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

    

    <div style="margin-top: 15px;">
        <h3 style="margin-bottom: 5px;">Compromiso</h3>
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
        <h3 style="margin-bottom: 5px;">Observaciones</h3>
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
        <h3>Evidencia Fotográfica</h3>
        <table style="border-collapse: collapse; width: 100%; margin-top: 5px;">
            <tr>
                @if($acta->foto1)
                <td style="border: 1px solid #000; padding: 5px; width: 50%; text-align: center;">
                    <img src="{{ storage_path('app/public/' . $acta->foto1) }}" style="max-width: 100%; max-height: 160px;">
                </td>
                @endif
                @if($acta->foto2)
                <td style="border: 1px solid #000; padding: 5px; width: 50%; text-align: center;">
                    <img src="{{ storage_path('app/public/' . $acta->foto2) }}" style="max-width: 100%; max-height: 160px;">
                </td>
                @endif
            </tr>
        </table>
    </div>
    @endif

    <div style="margin-top: 15px;"><h3 style="margin-bottom: 5px;">Firmas.</h3></div>



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

            // 3. Implementador OITE Unidad Ejecutora (SIEMPRE FIJO SEGÚN REQUERIMIENTO)
            $firmantes[] = [
                'cargo' => 'Implementador OITE Unidad Ejecutora',
                'nombre' => '____________________________________',
                'dni' => '____________________',
                'tipo_doc' => 'DNI'
            ];

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

        <table width="100%" cellspacing="0" cellpadding="0" style="border: none; margin-top: 10px;">
            @foreach (array_chunk($firmantes, 2) as $row)
            <tr>
                @foreach ($row as $f)
                <td width="50%" valign="top" style="border: none; padding: 5px;">
                    <div style="border: 1px solid #000; border-radius: 6px; padding: 8px 12px; text-align: left;">
                        <div style="height: 85px;"></div>
                        <p style="margin: 0 0 8px 0; font-weight: bold; font-size: 11px; color: #0f172a;">
                            {{ $f['cargo'] }}
                        </p>
                        <p style="margin: 0 0 5px 0; font-size: 10px; color: #0f172a;">
                            Apellidos y Nombres: {{ $f['nombre'] }}
                        </p>
                        <p style="margin: 0; font-size: 10px; color: #0f172a;">
                            {{ $f['tipo_doc'] }}: {{ $f['dni'] }}
                        </p>
                    </div>
                </td>
                @endforeach
                @if(count($row) == 1)
                <td width="50%" style="border: none; padding: 10px;"></td>
                @endif
            </tr>
            @endforeach
        </table>
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
            $color = array(0, 0, 0);
            
            $pdf->page_script('$pdf->line(40, $pdf->get_height() - 40, $pdf->get_width() - 40, $pdf->get_height() - 40, array(0,0,0), 1);');
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