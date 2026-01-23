<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Citas - CSMC</title>
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
            margin: 1cm 1.5cm 1.5cm 1.5cm; 
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
            background-color: #f3f4f6; 
            border-left: 5px solid #4f46e5; 
            padding: 6px 10px;
            font-weight: bold;
            font-size: 11px;
            text-transform: uppercase;
            color: #1f2937;
            margin-top: 15px;
            margin-bottom: 5px; 
        }

        /* --- TABLAS --- */
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5px;
        }
        .details-table td, .details-table th {
            border: 1px solid #e5e7eb; 
            padding: 6px 8px;
            vertical-align: middle;
        }
        .label-cell {
            background-color: #ffffff;
            font-weight: bold;
            color: #374151; 
            width: 25%; 
            font-size: 9px;
        }
        .value-cell {
            color: #000;
            font-size: 10px;
        }

        /* --- TABLA INVENTARIO --- */
        .inventory-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
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

        /* --- FOTOS --- */
        .photo-container { 
            width: 100%; 
            margin-top: 10px; 
            text-align: center; 
            border: 1px solid #e5e7eb;
            padding: 10px;
            background: #fff;
        }
        .photo-container img {
            max-width: 100%;
            max-height: 350px;
            object-fit: contain;
        }

        /* --- FIRMAS --- */
        .signature-section {
            margin-top: 40px;
            width: 100%;
            text-align: center;
        }
        .signature-frame {
            width: 300px;
            height: 100px;
            margin: 0 auto;
            border-bottom: 1px solid #000;
            position: relative;
        }
        .signature-text {
            margin-top: 5px;
            font-size: 10px;
            font-weight: bold;
        }
    </style>
</head>
<body>

    {{-- INICIALIZAMOS CONTADOR DE SECCIONES --}}
    @php $i = 1; @endphp

    <div class="main-header">
        <h1>MODULO 02 - Citas</h1>
        <p>
            ACTA N° {{ str_pad($acta->id, 5, '0', STR_PAD_LEFT) }} | 
            ESTABLECIMIENTO: {{ $acta->establecimiento->codigo ?? 'S/C' }} - {{ $acta->establecimiento->nombre ?? '-' }} |
            FECHA: {{ $dbInicioLabores->fecha_registro ? \Carbon\Carbon::parse($dbInicioLabores->fecha_registro)->format('d/m/Y') : '-' }}
        </p>
    </div>

    {{-- SECCIÓN 1: DETALLES --}}
    <div class="section-header">{{ $i++ }}. DETALLES DEL CONSULTORIO</div>
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
    </table>

    {{-- SECCIÓN 2: PROFESIONAL --}}
    <div class="section-header">{{ $i++ }}. DATOS DEL PROFESIONAL</div>
    @php 
        $prof = $dbCapacitacion->profesional ?? null; 
    @endphp
    <table class="details-table">
        <tr>
            <td class="label-cell">APELLIDOS Y NOMBRES:</td>
            <td class="value-cell" colspan="3">
                {{ $prof ? "$prof->apellido_paterno $prof->apellido_materno, $prof->nombres" : 'NO REGISTRADO' }}
            </td>
        </tr>
        <tr>
            <td class="label-cell">CARGO:</td>
            <td class="value-cell">{{ $prof->cargo ?? '-' }}</td>
            <td class="label-cell">DOCUMENTO:</td>
            <td class="value-cell">{{ $prof->tipo_doc ?? '' }}: {{ $prof->doc ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label-cell">CORREO:</td>
            <td class="value-cell">{{ $prof->email ?? '-' }}</td>
            <td class="label-cell">CELULAR:</td>
            <td class="value-cell">{{ $prof->telefono ?? '-' }}</td>
        </tr>
    </table>

    {{-- SECCIÓN CONDICIONAL: DNI (Si hay tipo de DNI seleccionado) --}}
    @if(!empty($dbDni->tip_dni))
        <div class="section-header">{{ $i++ }}. DETALLE DE DNI Y FIRMA DIGITAL</div>
        <table class="details-table">
            <tr>
                <td class="label-cell">TIPO DNI:</td>
                <td class="value-cell">{{ str_replace('_', ' ', $dbDni->tip_dni) }}</td>
                <td class="label-cell">VERSIÓN DNIe:</td>
                <td class="value-cell">{{ $dbDni->version_dni ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label-cell">¿REALIZA FIRMA EN SIHCE?</td>
                <td class="value-cell" colspan="3">{{ $dbDni->firma_sihce ?? 'NO' }}</td>
            </tr>
            @if(!empty($dbDni->comentarios))
            <tr>
                <td class="label-cell">OBSERVACIONES DNI:</td>
                <td class="value-cell" colspan="3">{{ $dbDni->comentarios }}</td>
            </tr>
            @endif
        </table>
    @endif

    {{-- SECCIÓN CONDICIONAL: CAPACITACIÓN (Si hay respuesta en 'recibieron_cap') --}}
    @if(!empty($dbCapacitacion->recibieron_cap))
        <div class="section-header">{{ $i++ }}. DETALLES DE CAPACITACIÓN</div>
        <table class="details-table">
            <tr>
                <td class="label-cell">¿RECIBIÓ CAPACITACIÓN?</td>
                <td class="value-cell">{{ $dbCapacitacion->recibieron_cap }}</td>
                <td class="label-cell">ENTIDAD CAPACITADORA:</td>
                <td class="value-cell">{{ $dbCapacitacion->institucion_cap ?? '-' }}</td>
            </tr>
        </table>
    @endif

    {{-- SECCIÓN EQUIPAMIENTO (Siempre visible) --}}
    <div class="section-header">{{ $i++ }}. EQUIPAMIENTO DEL CONSULTORIO</div>
    <table class="inventory-table">
        <thead>
            <tr>
                <th width="30%">DESCRIPCIÓN</th>
                <th width="10%">CANT.</th>
                <th width="15%">ESTADO</th>
                <th width="15%">PROPIEDAD</th>
                <th width="15%">SERIE/COD</th>
                <th width="15%">OBSERVACIÓN</th>
            </tr>
        </thead>
        <tbody>
            @forelse($dbInventario as $item)
            <tr>
                <td>{{ $item->descripcion }}</td>
                <td style="text-align: center;">{{ $item->cantidad }}</td>
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

    {{-- SECCIÓN CONDICIONAL: SOPORTE (Si hay datos de soporte) --}}
    @if(!empty($dbDificultad->insti_comunica) || !empty($dbDificultad->medio_comunica))
        <div class="section-header">{{ $i++ }}. SOPORTE Y DIFICULTADES</div>
        <table class="details-table">
            <tr>
                <td class="label-cell">INSTITUCIÓN QUE COORDINA:</td>
                <td class="value-cell">{{ $dbDificultad->insti_comunica ?? '-' }}</td>
                <td class="label-cell">MEDIO DE COMUNICACIÓN:</td>
                <td class="value-cell">{{ $dbDificultad->medio_comunica ?? '-' }}</td>
            </tr>
        </table>
    @endif

    {{-- SECCIÓN COMENTARIOS --}}
    <div class="section-header">{{ $i++ }}. COMENTARIOS GENERALES</div>
    <table class="details-table">
        <tr>
            <td style="padding: 10px; min-height: 40px; vertical-align: top; font-size: 10px;">
                {{ $dbInicioLabores->comentarios ?? 'Sin comentarios registrados.' }}
            </td>
        </tr>
    </table>

    {{-- SECCIÓN EVIDENCIA --}}
    <div class="section-header">{{ $i++ }}. EVIDENCIA FOTOGRÁFICA</div>
    <div class="photo-container">
        @if(!empty($fotoUrl))
            {{-- Usamos public_path para que DomPDF encuentre el archivo localmente --}}
            <img src="{{ public_path('storage/' . $fotoUrl) }}">
        @else
            <p style="padding: 20px; color: #999;">No se adjuntó evidencia fotográfica.</p>
        @endif
    </div>

    {{-- FIRMA --}}
    <div class="signature-section">
        <div class="signature-frame"></div>
        <div class="signature-text">
            @if($prof)
                {{ $prof->apellido_paterno }} {{ $prof->apellido_materno }} {{ $prof->nombres }}<br>
                {{ $prof->tipo_doc }}: {{ $prof->doc }}
            @else
                FIRMA PROFESIONAL
            @endif
        </div>
        <div style="font-size: 9px; color: #666; margin-top: 2px;">FIRMA DEL PROFESIONAL ENTREVISTADO</div>
    </div>

    {{-- PIE DE PÁGINA --}}
    <script type="text/php">
        if (isset($pdf)) {
            $y = $pdf->get_height() - 30;
            $font = $fontMetrics->get_font("helvetica", "normal");
            $size = 8;
            $color = array(0.3, 0.3, 0.3);

            $pdf->page_text(40, $y, "SISTEMA DE ACTAS - MODULO CITAS", $font, $size, $color);

            $text = "PAG: {PAGE_NUM} / {PAGE_COUNT}";
            $width = $fontMetrics->get_text_width($text, $font, $size);
            $x = $pdf->get_width() - $width - 30;
            
            $pdf->page_text($x, $y, $text, $font, $size, $color);
        }
    </script>

</body>
</html>