<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Psicología</title>
    <style>
        /* --- CONFIGURACIÓN GENERAL --- */
        body { 
            font-family: 'Helvetica', sans-serif; 
            font-size: 10px; 
            color: #333; 
            margin: 0; 
            padding-top: 0;
        }

        @page {
            margin: 1cm 1.5cm 1.5cm 1.5cm; /* Márgenes para no cortar contenido */
        }

        /* --- ENCABEZADO SUPERIOR --- */
        .main-header { 
            text-align: center; 
            margin-bottom: 20px; 
            border-bottom: 2px solid #4f46e5; 
            padding-bottom: 10px; 
        }
        .main-header h1 { 
            color: #4f46e5; 
            margin: 0; 
            font-size: 16px; 
            text-transform: uppercase; 
        }
        .main-header p { 
            margin: 3px 0; 
            font-size: 10px; 
            color: #555; 
        }

        /* --- TÍTULOS DE SECCIÓN --- */
        .section-header {
            background-color: #f3f4f6; /* Fondo gris claro */
            border-left: 5px solid #4f46e5; /* Barra azul a la izquierda */
            padding: 6px 10px;
            font-weight: bold;
            font-size: 11px;
            text-transform: uppercase;
            color: #1f2937;
            margin-top: 15px;
            margin-bottom: 0; /* Pegado a la tabla */
        }

        /* --- TABLAS DE DETALLE (GRILLA) --- */
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5px;
        }
        .details-table td, .details-table th {
            border: 1px solid #e5e7eb; /* Bordes grises suaves */
            padding: 6px 8px;
            vertical-align: middle;
        }
        
        /* Celdas de "Etiqueta" (Izquierda) */
        .label-cell {
            background-color: #ffffff;
            font-weight: bold;
            color: #374151; /* Gris oscuro */
            width: 25%; /* Ancho fijo para etiquetas */
        }

        /* Celdas de "Valor" (Derecha) */
        .value-cell {
            color: #000;
        }

        /* --- TABLA DE INVENTARIO (LISTA) --- */
        .inventory-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 0;
        }
        .inventory-table th {
            background-color: #4f46e5;
            color: white;
            padding: 5px;
            font-size: 9px;
            text-align: left;
            text-transform: uppercase;
        }
        .inventory-table td {
            border: 1px solid #e5e7eb;
            padding: 5px;
            font-size: 9px;
        }

        /* --- GALERÍA DE FOTOS --- */
        .gallery { 
            width: 100%; 
            margin-top: 10px; 
            text-align: center; 
        }
        .photo-box {
            display: inline-block;
            width: 48%;
            margin: 2px;
            border: 1px solid #ddd;
            padding: 4px;
            background: #fff;
        }
        .photo-box img {
            width: 100%;
            height: 200px;
            object-fit: contain;
        }

        /* --- FIRMAS --- */
        .signature-section {
            margin-top: 40px;
            width: 100%;
            text-align: center;
        }

        .signature-frame {
            width: 350px;
            height: 160px;
            margin: 0 auto;
            border: 1px solid #cbd5e1;
            border-radius: 12px;
            position: relative;
            background-color: #fff;
        }

        .signature-box {
            position: absolute;
            bottom: 15px;
            left: 0;
            right: 0;
            width: 85%;
            margin: 0 auto;
            border-top: 1px solid #000;
            padding-top: 8px;
            text-align: center;
        }
    </style>
</head>
<body>

    {{-- ENCABEZADO GENERAL --}}
    <div class="main-header">
        <h1>MODULO 07 - CONSULTA EXTERNA: Psicología</h1>
        <p>
            ACTA N° {{ str_pad($acta->id, 5, '0', STR_PAD_LEFT) }} | 
            ESTABLECIMIENTO: {{ $acta->establecimiento->codigo ?? 'S/C' }} - {{ $acta->establecimiento->nombre ?? '-' }} |
            FECHA: {{ $dbInicioLabores->fecha_registro ? \Carbon\Carbon::parse($dbInicioLabores->fecha_registro)->format('d/m/Y') : '-' }}
        </p>
    </div>

    {{-- 1. DETALLES DEL CONSULTORIO --}}
    <div class="section-header">1. DETALLES DEL CONSULTORIO</div>
    <table class="details-table">
        <tr>
            <td class="label-cell">CANTIDAD CONSULTORIOS:</td>
            <td class="value-cell" colspan="3">{{ $dbInicioLabores->cant_consultorios ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label-cell">CONSULTORIO ENTREVISTADO:</td>
            <td class="value-cell" colspan="3">{{ $dbInicioLabores->nombre_consultorio ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label-cell">TURNO:</td> 
            <td class="value-cell" colspan="3">{{ $dbInicioLabores->turno ?? '-' }}</td> 
        </tr>
        {{-- <tr>
            <td class="label-cell">FECHA DE MONITOREO:</td> 
            <td class="value-cell" colspan="3">
                {{ $dbInicioLabores->fecha_registro ? \Carbon\Carbon::parse($dbInicioLabores->fecha_registro)->format('d/m/Y') : '-' }}
            </td> 
        </tr> --}}
    </table>

    {{-- 2. DATOS DEL PROFESIONAL --}}
    <div class="section-header">2. DATOS DEL PROFESIONAL</div>
    @php 
        $prof = $dbCapacitacion->profesional ?? null; 
    @endphp
    <table class="details-table">
        <tr>
            <td class="label-cell">APELLIDOS Y NOMBRES:</td>
            <td class="value-cell">
                {{ $prof ? "$prof->apellido_paterno $prof->apellido_materno, $prof->nombres" : 'NO REGISTRADO' }}
            </td>
            <td class="label-cell">CARGO:</td>
            <td class="value-cell">{{ $prof->cargo ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label-cell">TIPO DOC:</td>
            <td class="value-cell">{{ $prof->tipo_doc ?? '-' }}</td>
            <td class="label-cell">¿FIRMÓ DECLARACIÓN JURADA?</td>
            <td class="value-cell">{{ $dbCapacitacion->decl_jurada ?? 'NO' }}</td>
        </tr>
        <tr>
            <td class="label-cell">DOCUMENTO:</td>
            <td class="value-cell">{{ $prof->doc ?? '-' }}</td>
            <td class="label-cell">¿FIRMÓ COMP. CONFIDENCIALIDAD?</td>
            <td class="value-cell">{{ $dbCapacitacion->comp_confidencialidad ?? 'NO' }}</td>
        </tr>
        <tr>
            <td class="label-cell">CORREO:</td>
            <td class="value-cell">{{ $prof->email ?? '-' }}</td>
            <td class="label-cell">CELULAR:</td>
            <td class="value-cell">{{ $prof->telefono ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label-cell" colspan="2"></td>
            <td class="label-cell">¿UTILIZA SIHCE?</td>
            <td class="value-cell">{{ $dbInicioLabores->utiliza_sihce ?? '-' }}</td>
        </tr>
    </table>

    {{-- 3. DETALLE DE DNI Y FIRMA DIGITAL --}}
    <div class="section-header">3. DETALLE DE DNI Y FIRMA DIGITAL</div>
    <table class="details-table">
        <tr>
            <td class="label-cell">TIPO DNI:</td>
            <td class="value-cell">{{ str_replace('_', ' ', $dbDni->tip_dni ?? '-') }}</td>
            <td class="label-cell">VERSIÓN DNIe:</td>
            <td class="value-cell">{{ $dbDni->version_dni ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label-cell">¿REALIZA FIRMA EN SIHCE?</td>
            <td class="value-cell" colspan="3">{{ $dbDni->firma_sihce ?? 'NO' }}</td>
        </tr>
        <tr>
            <td class="label-cell">OBSERVACIONES DNI:</td>
            <td class="value-cell" colspan="3">{{ $dbDni->comentarios ?? '-' }}</td>
        </tr>
    </table>

    {{-- 4. DETALLES DE CAPACITACIÓN --}}
    <div class="section-header">4. DETALLES DE CAPACITACIÓN</div>
    <table class="details-table">
        <tr>
            <td class="label-cell">¿RECIBIÓ CAPACITACIÓN?</td>
            <td class="value-cell">{{ $dbCapacitacion->recibieron_cap ?? '-' }}</td>
            <td class="label-cell">ENTIDAD CAPACITADORA:</td>
            <td class="value-cell">{{ $dbCapacitacion->institucion_cap ?? 'N/A' }}</td>
        </tr>
    </table>

    {{-- 5. MATERIALES (INICIO DE LABORES) --}}
    {{-- CAMBIO: Solo mostramos TIPO FUA y REFERENCIA, eliminando Receta y Orden Lab --}}
    <div class="section-header">5. MATERIALES (INICIO DE LABORES)</div>
    <table class="details-table">
        <tr>
            <td class="label-cell">TIPO FUA:</td>
            <td class="value-cell">{{ str_replace('_', ' ', $dbInicioLabores->fua ?? '-') }}</td>
            <td class="label-cell">TIPO REFERENCIA:</td>
            <td class="value-cell">{{ $dbInicioLabores->referencia ?? '-' }}</td>
        </tr>
    </table>

    {{-- 6. EQUIPAMIENTO DEL CONSULTORIO --}}
    <div class="section-header">6. EQUIPAMIENTO DEL CONSULTORIO</div>
    <table class="inventory-table">
        <thead>
            <tr>
                <th width="30%">DESCRIPCIÓN</th>
                <th width="10%">CANTIDAD</th>
                <th width="15%">ESTADO</th>
                <th width="15%">PROPIEDAD</th>
                <th width="15%">N.SERIE / C.PAT</th>
                <th width="15%">OBSERVACIÓN</th>
            </tr>
        </thead>
        <tbody>
            @forelse($dbInventario as $item)
            <tr>
                <td>{{ $item->descripcion }}</td>
                <td style="text-align: center;">{{ $item->cantidad ?? '1' }}</td>
                <td>{{ $item->estado }}</td>
                <td>{{ $item->propio }}</td>
                <td>{{ $item->nro_serie ?? '-' }}</td>
                <td>{{ $item->observacion }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align: center; padding: 10px; color: #777;">Sin equipamiento registrado</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- 7. SOPORTE --}}
    <div class="section-header">7. SOPORTE</div>
    <table class="details-table">
        <tr>
            <td class="label-cell">INSTITUCIÓN QUE COORDINA:</td>
            <td class="value-cell">{{ $dbDificultad->insti_comunica ?? '-' }}</td>
            <td class="label-cell">MEDIO DE COMUNICACIÓN:</td>
            <td class="value-cell">{{ $dbDificultad->medio_comunica ?? '-' }}</td>
        </tr>
    </table>

    {{-- 8. COMENTARIOS GENERALES --}}
    <div class="section-header">8. COMENTARIOS GENERALES</div>
    <table class="details-table">
        <tr>
            <td style="padding: 10px; height: 40px; vertical-align: top;">
                {{ $dbInicioLabores->comentarios ?? 'Sin comentarios generales registrados.' }}
            </td>
        </tr>
    </table>

    {{-- 9. EVIDENCIA FOTOGRÁFICA --}}
    <div class="section-header">9. EVIDENCIA FOTOGRÁFICA</div>
    <div class="gallery">
        @forelse($dbFotos as $foto)
            <div class="photo-box">
                <img src="{{ public_path('storage/' . $foto->url_foto) }}">
            </div>
        @empty
            <p style="padding: 20px; color: #999;">No se adjuntaron fotografías.</p>
        @endforelse
    </div>

    {{-- 10. FIRMA --}}
    <div class="section-header">10. FIRMA</div>
    
    <div class="signature-section">
        <div class="signature-frame">
            <div class="signature-box">
                @if($prof)
                    <div style="font-weight: bold; font-size: 11px; color: #1e293b;">
                        {{ $prof->apellido_paterno }} {{ $prof->apellido_materno }}, {{ $prof->nombres }}
                    </div>                
                    <div style="font-size: 10px; color: #64748b; margin-top: 1px;">
                        {{ $prof->tipo_doc }}: {{ $prof->doc }}
                    </div>
                    <div style="font-weight: bold; font-size: 9px; margin-top: 4px;">FIRMA DEL PROFESIONAL ENTREVISTADO</div>
                @else
                    <div style="padding: 10px;">FIRMA PENDIENTE</div>
                @endif
            </div>
        </div>
    </div>

    {{-- SCRIPT PIE DE PÁGINA --}}
    <script type="text/php">
        if (isset($pdf)) {
            $y = $pdf->get_height() - 30;
            $font = $fontMetrics->get_font("helvetica", "normal");
            $size = 8;
            $color = array(0.3, 0.3, 0.3);

            $pdf->page_text(40, $y, "SISTEMA DE ACTAS", $font, $size, $color);

            $text = "PAG: {PAGE_NUM} / {PAGE_COUNT}";
            $dummyText = "PAG: 10 / 10"; 
            $width = $fontMetrics->get_text_width($dummyText, $font, $size);
            $x = $pdf->get_width() - $width - 30;
            
            $pdf->page_text($x, $y, $text, $font, $size, $color);
        }
    </script>

</body>
</html>