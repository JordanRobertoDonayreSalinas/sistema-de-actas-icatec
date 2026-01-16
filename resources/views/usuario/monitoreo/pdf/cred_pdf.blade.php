<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte CRED - Acta {{ $acta->id }}</title>
    <style>
        @page { margin: 1.5cm; }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 11px;
            color: #1e293b;
            line-height: 1.4;
            counter-reset: section-counter;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #4f46e5;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 16px;
            text-transform: uppercase;
            color: #4f46e5;
        }
        .header p {
            margin: 2px 0;
            font-weight: bold;
            color: #64748b;
        }
        .section-header {
            background-color: #f1f5f9;
            padding: 6px 10px;
            font-weight: bold;
            text-transform: uppercase;
            border-left: 4px solid #4f46e5;
            margin-top: 15px;
            font-size: 12px;
        }
        .section-header:before {
            /* Incrementamos el contador cada vez que aparece una clase section-header */
            counter-increment: section-counter;
            /* Mostramos el número seguido de un punto y un espacio */
            content: counter(section-counter) ". ";
        }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; table-layout: fixed; }
        th, td {
            border: 1px solid #e2e8f0;
            padding: 5px;
            text-align: left;
            font-size: 9px;
        }
        th {
            background-color: #f8fafc;
            color: #475569;
            font-size: 10px;
            text-transform: uppercase;
        }
        .text-center { text-align: center; }
        /* --- CSS CORREGIDO PARA EL FOOTER --- */
        .footer {
            position: fixed;
            bottom: -1cm;
            left: 0px;
            right: 0px;
            width: 100%;
            height: 30px;
            border-top: 0.5pt solid #e2e8f0;
        }

        .tab-footer {
            width: 100%;
            border-collapse: collapse;
            border: none;
        }

        .tab-footer td {
            border: none !important;
            padding: 5px 0;
            font-size: 9px;
            color: #94a3b8;
            vertical-align: middle; /* Cambiado a middle para mejor alineación */
        }

        .col-izquierda { text-align: left; width: 50%; }
        .col-derecha { text-align: right; width: 50%; }
        .photo-container {
            margin-top: 20px;
            text-align: center;
        }
        .photo-frame {
            border: 1px solid #e2e8f0;
            padding: 10px;
            display: inline-block;
            background: #fff;
            margin: 10px;
            vertical-align: top;
        }
        .photo {
            max-width: 300px;
            max-height: 250px;
            object-fit: contain;
        }
        /* Contenedor principal de la sección de fotos */
        .photo-section {
            width: 100%;
            margin-top: 10px;
            text-align: center;
        }

        /* Caja individual para cada foto */
        .photo-box {
            display: inline-block; /* Permite que se pongan una al lado de la otra si caben */
            width: 45%;            /* Ajustado para que quepan 2 por fila */
            margin: 10px 1%;
            text-align: center;
            vertical-align: top;
        }

        .photo-box img {
            width: 100%;           /* Se adapta al 45% del ancho disponible */
            height: 180px;         /* Altura fija controlada */
            object-fit: contain;   /* Muestra la foto completa sin recortar */
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            background-color: #f8fafc;
        }

    </style>
</head>
<body>

    <div class="footer">
        <table class="tab-footer">
            <tr>
                <td class="col-izquierda">SISTEMA DE ACTAS</td>
                <td class="col-derecha">
                    {{-- Se deja vacío para que el script PHP posicione el número aquí --}}
                </td>
            </tr>
        </table>
    </div>

    <div class="header">
        <h1>Módulo 08: Crecimiento y Desarrollo (CRED)</h1>
        <div style="font-weight: bold; color: #64748b; font-size: 10px;">
            ACTA N° {{ str_pad($acta->id, 3, '0', STR_PAD_LEFT) }} | ESTABLECIMIENTO.: {{ strtoupper($acta->establecimiento->codigo) }} - {{ strtoupper($acta->establecimiento->nombre) }} | FECHA: {{ !empty($detalle->fecha_registro) ? \Carbon\Carbon::parse($detalle->fecha_registro)->format('d/m/Y') : 'Sin Registro' }}
        </div>
    </div>
    <div class="section-header">Datos del Profesional</div>
    <table class="table-data">
        <tr>
            <th width="35%">Apellidos y nombres:</th>
            <td class="uppercase">{{ $detalle->personal_nombre ?? 'N/A' }}</td>
        </tr>
        
        <tr>
            <th>Tipo de documento:</th>
            <td>{{ $datos['personal']['tipo_doc'] ?? 'N/A' }}</td>
        </tr>

        <tr>
            <th>Documento:</th>
            <td>{{ $detalle->personal_dni ?? 'N/A' }}</td>
        </tr>

        <tr>
            <th>Correo:</th>
            <td>{{ $datos['personal']['email'] ?? 'N/A' }}</td>
        </tr>

        <tr>
            <th>Celular:</th>
            <td>{{ $datos['personal']['contacto'] ?? 'N/A' }}</td>
        </tr>
        
        <tr>
            <th>Profesión:</th>
            <td>
                @php
                    $profesion = $datos['personal']['profesion'] ?? 'N/A';
                    $especifique = $datos['personal']['profesion_otro'] ?? '';
                    
                    // Si la profesión es 'OTROS' y hay algo escrito en especifique, mostrar eso.
                    if ($profesion === 'OTROS' && !empty($especifique)) {
                        $profesion = $especifique;
                    }
                @endphp
                {{ mb_strtoupper($profesion, 'UTF-8') }}
            </td>
        </tr>

        <tr>
            <th>Turno:</th>
            <td class="uppercase">{{ $detalle->personal_turno ?? 'N/A' }}</td>
        </tr>

        <tr>
            <th>¿Firmó Declaración Jurada?:</th>
            <td>{{ $datos['documentacion']['firma_dj'] ?? 'NO' }}</td>
        </tr>

        <tr>
            <th>¿Firmó Compromiso de Confidencialidad?:</th>
            <td>{{ $datos['documentacion']['firma_confidencialidad'] ?? 'NO' }}</td>
        </tr>

        <tr>
            <th>¿Utiliza SIHCE?:</th>
            <td>{{ $datos['personal']['utiliza_sihce'] ?? 'NO' }}</td>
        </tr>

    </table>

    @if(($datos['personal']['tipo_doc'] ?? '') == 'DNI')
        <div class="section-header">DETALLE DE DNI Y FIRMA DIGITAL</div>
        <table class="table-data" style="text-transform: uppercase;">
            <tr>
                <th>Tipo de DNI:</th>
                <td><strong>DNI {{ $datos['dni_firma']['tipo_dni_fisico'] ?? 'AZUL' }}</strong></td>
            </tr>

            @if(($datos['dni_firma']['tipo_dni_fisico'] ?? '') == 'ELECTRONICO')
            <tr>
                <th>Versión DNIe:</th>
                <td>{{ $datos['dni_firma']['dnie_version'] ?? '---' }}</td>
            </tr>

            <tr>
                <th>¿Firma digitalmente en SIHCE?:</th>
                <td>{{ $datos['dni_firma']['firma_sihce'] ?? 'NO' }}</td>
            </tr>
            @endif
            <tr>
                <th>Observaciones:</th>
                <td> {{ $datos['dni_firma']['observaciones'] ?? 'SIN OBSERVACIONES' }}</td>
            </tr>
        </table>
    @endif
    <div class="section-header">Detalles de Capacitación</div>
    <table>
        <tr>
            <th width="30%">¿Recibió Capacitación?</th>
            {{-- Si no recibió, el valor ocupa el resto de la fila --}}
            <td @if(($datos['capacitacion']['recibio'] ?? 'NO') == 'NO') colspan="3" @endif>
                {{ $datos['capacitacion']['recibio'] ?? 'NO' }}
            </td>
            
            {{-- Solo mostramos el ente si la respuesta es SI --}}
            @if(($datos['capacitacion']['recibio'] ?? 'NO') == 'SI')
                <th width="30%">¿De parte de quién?</th>
                <td>
                    @if(isset($datos['capacitacion']['ente']) && is_array($datos['capacitacion']['ente']))
                        {{ implode(', ', $datos['capacitacion']['ente']) }}
                    @elseif(isset($datos['capacitacion']['ente']) && is_string($datos['capacitacion']['ente']))
                        {{ $datos['capacitacion']['ente'] }}
                    @else
                        -
                    @endif
                </td>
            @endif
        </tr>
    </table>

    <div class="section-header">Equipamiento del Consultorio</div>
    <table>
        <thead>
            <tr>
                <th class="text-center">Descripción</th>
                <th class="text-center">Cantidad</th>
                <th class="text-center">Estado</th>
                <th class="text-center">Propiedad</th>
                <th class="text-center">N. Serie/C.Pat</th>
                <th class="text-center">Observaciones</th>
                
            </tr>
        </thead>
        <tbody>
            @forelse($equipos as $eq)
            <tr class="uppercase">
                <td class="text-center">{{ $eq->descripcion }}</td>
                <td class="text-center">{{ $eq->cantidad }}</td>
                <td class="text-center">{{ $eq->estado }}</td>
                <td class="text-center">{{ $eq->propio }}</td>
                <td class="text-center">{{ $eq->nro_serie ? ''.$eq->nro_serie : '' }} {{ $eq->observaciones }}</td>
                <td class="text-center">{{ $eq->observacion }}</td>
            </tr>
            @empty
            <tr><td colspan="5" class="text-center">No se registraron equipos.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="section-header">Métricas de Atención Mes Actual</div>
    <table class="table">
        <tr>
            <th>Atenciones CRED del mes</th>
            <td>{{ $datos['nro_atenciones_mes'] ?? 0 }}</td>
            <th>Descargas en HIS</th>
            <td>{{ $datos['descargas_his'] ?? 0 }}</td>
        </tr>
    </table>

    @if(($datos['personal']['utiliza_sihce'] ?? '') === 'SI')
        <div class="section-header">Soporte</div>
        <table class="table-data" style="width: 100%; margin-top: 5px; text-transform: uppercase;" >
            <tr>
                <th style="width: 25%; text-align: left; background-color: #f8fafc;">Ante dificultades se comunica con:</th>
                <td style="width: 25%; text-align: left;" class="uppercase">
                    {{ $datos['soporte']['comunica'] ?? '' }}
                </td>

                <th style="width: 20%; text-align: left; background-color: #f8fafc;">Medio que utiliza:</th>
                <td style="width: 30%; text-align: left;" class="uppercase">
                    {{ $datos['soporte']['medio'] ?? '' }}
                </td>
            </tr>
        </table>
    @endif
    
    <div class="section-header">Comentarios</div>
    
    <div style="
        width: 96%; 
        margin: 10px auto; 
        padding: 15px; 
        border: 1px solid #e2e8f0; 
        background-color: #f8fafc; 
        border-radius: 8px;
        min-height: 60px;
    ">
        <div style="font-size: 10px; color: #1e293b; line-height: 1.6; text-align: justify;">
            @if(!empty($datos['observaciones_generales']))
                {{ $datos['observaciones_generales'] }}
            @else
                <span style="color: #94a3b8; font-style: italic;">No se registraron comentarios.</span>
            @endif
        </div>
    </div>
    <div class="section-header">Evidencia Fotográfica</div>
    <div class="photo-section">
        {{-- Foto 1 --}}
        @if(!empty($detalle->foto_1))
            @php $path1 = storage_path('app/public/' . $detalle->foto_1); @endphp
            @if(file_exists($path1))
                <div class="photo-box">
                    <img src="{{ $path1 }}">
                    
                </div>
            @endif
        @endif

        {{-- Foto 2 --}}
        @if(!empty($detalle->foto_2))
            @php $path2 = storage_path('app/public/' . $detalle->foto_2); @endphp
            @if(file_exists($path2))
                <div class="photo-box">
                    <img src="{{ $path2 }}">
                    
                </div>
            @endif
        @endif
    </div>

    <div class="section-header">Firma</div>

        <div style="margin-top: 80px;"> {{-- Espacio de 3-4 líneas adicionales --}}
            <table style="width: 100%; border: none;">
                <tr>
                    <td style="width: 20%; border: none;"></td>
                    
                    <td style="width: 60%; border: 1px solid #1e293b; padding: 30px 20px; text-align: center; border-radius: 15px; background-color: #f8fafc;">
                        
                        {{-- Espacio para la firma física --}}
                        <div style="height: 70px;"></div>

                        {{-- Línea de firma --}}
                        <div style="width: 80%; border-top: 1.0pt solid #000000; margin: 0 auto 10px auto;"></div>

                        {{-- Datos del Responsable --}}
                        <div style="text-transform: uppercase; font-size: 11px; color: #000; margin-top: 5px; line-height: 1.2;">
                            {{ $detalle->personal_nombre ?? 'SIN NOMBRE REGISTRADO' }}
                        </div>
                        
                        <div style="font-size: 10px; color: #334155; margin-top: 6px; line-height: 1.4;">
                            DNI: {{ $detalle->personal_dni ?? '________' }} <br>
                        </div> 
                        <div style="font-size: 10px; color: #334155; margin-top: 6px; line-height: 1.4;">
                            {{' FIRMA DEL PROFESIONAL ENTREVISTADO ' }} <br>
                        </div> 
                    </td>

                    <td style="width: 20%; border: none;"></td>
                </tr>
            </table>
        </div>

<script type="text/php">
    if (isset($pdf)) {
        // Usamos la misma fuente definida en tu body CSS
        $font = $fontMetrics->get_font("helvetica", "normal");
        
        // Tamaño 9 para coincidir con .tab-footer td
        $size = 7;
        
        // Color #94a3b8 exacto
        $color = array(0.58, 0.64, 0.72);
        
        $text = "PÁG {PAGE_NUM} / {PAGE_COUNT}";
        
        // Calculamos el ancho dinámico del texto
        $width = $fontMetrics->get_text_width($text, $font, $size);
        
        // AJUSTES FINALES:
        // X: 565 es el límite derecho para hojas A4 con margen de 1.5cm
        // Y: 813 suele ser el eje exacto para alinearse con el texto de la izquierda
        $pdf->page_text(565 - $width, 813, $text, $font, $size, $color);
    }
</script>
</body>

</html>
