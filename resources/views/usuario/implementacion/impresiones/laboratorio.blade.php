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
        <h3>Firma Digital</h3>
        <table>
            <tr>
                <td style="background-color: #0066cc; color: white; width: 150px;"><strong>Firma Digital</strong></td>
                <td>{{ $acta->firma_digital }}</td>
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

    

    <div style="margin-top: 20px;">
        <h3>Compromiso</h3>
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
    
    <div style="margin-top: 20px;">
        <h3>Observaciones</h3>
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
                    <img src="{{ storage_path('app/public/' . $acta->foto1) }}" style="max-width: 100%; max-height: 200px;">
                </td>
                @endif
                @if($acta->foto2)
                <td style="border: 1px solid #000; padding: 5px; width: 50%; text-align: center;">
                    <img src="{{ storage_path('app/public/' . $acta->foto2) }}" style="max-width: 100%; max-height: 200px;">
                </td>
                @endif
            </tr>
        </table>
    </div>
    @endif

    <div class="section">
        <h3>Firmas.</h3>
    </div>



    <div style="margin: 20px;">
        <p>Dan fe de la veracidad de los datos consignados:</p>
        
        @php
            $firmas = [];
            // 1. Jefe del establecimiento
            $firmas[] = [
                'titulo' => 'Jefe del establecimiento',
                'nombre' => $acta->responsable ?: '_________________________________',
                'dni'    => '____________________'
            ];

            // 2. Implementadores (1 o más)
            if ($acta->implementadores && $acta->implementadores->count() > 0) {
                foreach($acta->implementadores as $imp) {
                    $firmas[] = [
                        'titulo' => 'Implementador DIRESA_ICATEC',
                        'nombre' => $imp->apellido_paterno . ' ' . $imp->apellido_materno . ', ' . $imp->nombres,
                        'dni'    => $imp->dni
                    ];
                }
            } else {
                $firmas[] = [
                    'titulo' => 'Implementador DIRESA_ICATEC',
                    'nombre' => '_________________________________',
                    'dni'    => '____________________'
                ];
            }

            // 3. Usuarios/Participantes (0 o más)
            if ($acta->usuarios && $acta->usuarios->count() > 0) {
                foreach($acta->usuarios as $user) {
                    $firmas[] = [
                        'titulo' => 'Usuario / Participante',
                        'nombre' => $user->apellidos_nombres ?? '_________________________________',
                        'dni'    => $user->dni ?? '____________________'
                    ];
                }
            }

            // 4. Implementador OITE
            $firmas[] = [
                'titulo' => 'Implementador OITE Unidad Ejecutora',
                'nombre' => '_________________________________',
                'dni'    => '____________________'
            ];

            // 5. Responsable del Modulo
            $firmas[] = [
                'titulo' => 'Responsable del Modulo del EE.SS',
                'nombre' => '_________________________________',
                'dni'    => '____________________'
            ];
            
            // Agrupar de a 2 para la tabla
            $filas = array_chunk($firmas, 2);
        @endphp

        <table width="100%" cellspacing="0" cellpadding="10" style="border: none;">
            @foreach($filas as $fila)
            <tr>
                @foreach($fila as $firma)
                <td width="50%" valign="top" style="border: none; padding-bottom: 20px;">
                    <br><br><br><br>
                    <p style="margin: 0; font-size: 11px;">_________________________________________</p>
                    <p style="margin: 2px 0 0 0; font-size: 11px;"><strong>{{ $firma['titulo'] }}</strong></p>
                    <p style="margin: 2px 0 0 0; font-size: 10px;">{{ $firma['nombre'] }}</p>
                    <p style="margin: 2px 0 0 0; font-size: 10px;">DNI: {{ $firma['dni'] }}</p>
                </td>
                @endforeach
                @if(count($fila) == 1)
                <td width="50%" style="border: none;"></td>
                @endif
            </tr>
            @endforeach
        </table>
    </div>

    <div style="margin-top: 80px;">
        <h4><strong>Glosario</strong></h4>
        <p>D.J. : Declaración Jurada </p>
        <p>C.C. : Compromiso de Confidencialidad </p>
    </div>



    <script type="text/php">
        if (isset($pdf)) {
            $y = $pdf->get_height() - 30;
            $font = $fontMetrics->get_font("helvetica", "normal");
            $size = 8;
            $color = array(0.3, 0.3, 0.3);
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