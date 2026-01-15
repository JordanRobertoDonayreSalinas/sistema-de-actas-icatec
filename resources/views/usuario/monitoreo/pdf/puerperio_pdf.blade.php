<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>REPORTE MÓDULO 13 - ATENCIÓN DEL PUERPERIO</title>
    <style>
        /* Configuración de Página */
        @page { margin: 0.8cm 0.8cm 2cm 0.8cm; }
        body { font-family: 'Helvetica', Arial, sans-serif; font-size: 10px; color: #333; line-height: 1.4; }
        
        /* Estilos Generales */
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-uppercase { text-transform: uppercase; }
        .font-bold { font-weight: bold; }
        
        /* --- ENCABEZADO (Estilo Azul - Igual a Gestión Administrativa) --- */
        .main-header { 
            text-align: center; 
            margin-bottom: 20px; 
        }
        .module-title { 
            font-size: 16px; 
            font-weight: bold; 
            color: #4f46e5; /* Azul Índigo */
            text-transform: uppercase; 
            margin: 0 0 5px 0;
        }
        .acta-info { 
            font-size: 10px; 
            color: #64748b; 
            font-weight: bold; 
            text-transform: uppercase; 
            margin: 0;
        }
        .header-bar { 
            margin-top: 10px;
            border-bottom: 3px solid #4f46e5; /* Línea Azul */
            width: 100%;
        }

        /* Tablas */
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; table-layout: fixed; }
        
        /* Cabeceras de Sección (Estilo Barra Gris con Borde Azul) */
        .section-header { 
            background-color: #f1f5f9; 
            color: #1e293b;
            padding: 8px 10px; 
            font-weight: bold; 
            text-transform: uppercase; 
            font-size: 11px; 
            text-align: left;
            border: 1px solid #e2e8f0;
            border-left: 4px solid #4f46e5; /* Detalle Azul */
        }

        /* Celdas */
        th.table-head { background-color: #f8fafc; color: #475569; font-size: 9px; text-transform: uppercase; padding: 6px; border: 1px solid #e2e8f0; font-weight: bold; }
        td { padding: 6px 8px; border: 1px solid #e2e8f0; text-align: left; vertical-align: middle; word-wrap: break-word; }
        
        /* Columnas de Etiqueta vs Dato */
        .label-col { background-color: #ffffff; color: #64748b; width: 35%; font-weight: bold; font-size: 9px; text-transform: uppercase; }
        .data-col { background-color: #ffffff; color: #0f172a; width: 65%; font-weight: bold; font-size: 9px; text-transform: uppercase; }

        /* Evidencia */
        .evidence-box { text-align: center; padding: 10px; border: 1px dashed #cbd5e1; border-radius: 4px; background: #fafafa; }
        .evidence-img { max-height: 250px; max-width: 100%; border-radius: 2px; }
        
        /* Caja de Firma */
        .signature-section { margin-top: 30px; page-break-inside: avoid; }
        .signature-box { 
            width: 300px; 
            margin: 0 auto; 
            text-align: center; 
            border: 1px solid #333; 
            border-radius: 10px;    
            padding: 30px 20px 10px 20px;
            background-color: #fff;
        }
        .signature-line { border-top: 1px solid #000; margin: 10px 15px 5px 15px; }
        .signature-name { font-weight: bold; font-size: 9px; text-transform: uppercase; }
        .signature-role { font-size: 8px; color: #64748b; text-transform: uppercase; }

        /* Pie de Página */
        .footer-line { 
            position: fixed; 
            bottom: 35px; 
            left: 0; 
            right: 0; 
            height: 1px; 
            background-color: #cbd5e1; 
        }
    </style>
</head>
<body>

    {{-- LÍNEA DECORATIVA DEL PIE DE PÁGINA --}}
    <div class="footer-line"></div>

    {{-- ENCABEZADO --}}
    <div class="main-header">
        <h1 class="module-title">MÓDULO 13: ATENCIÓN DEL PUERPERIO</h1>
        <p class="acta-info">
            ACTA Nº {{ str_pad($acta->id, 5, '0', STR_PAD_LEFT) }} | 
            ESTABLECIMIENTO: {{ $acta->establecimiento->codigo ?? 'S/C' }} - {{ $acta->establecimiento->nombre }} | 
            FECHA: {{ isset($detalle->contenido['fecha']) ? date('d/m/Y', strtotime($detalle->contenido['fecha'])) : date('d/m/Y') }}
        </p>
        <div class="header-bar"></div>
    </div>

    {{-- 1. DETALLES DEL CONSULTORIO --}}
    <table>
        <thead><tr><th colspan="2" class="section-header">1. DETALLES DEL CONSULTORIO</th></tr></thead>
        <tbody>
            <tr>
                <td class="label-col">Turno</td>
                <td class="data-col">{{ $detalle->contenido['turno'] ?? '---' }}</td>
            </tr>
            <tr>
                <td class="label-col">Nro. de Consultorios</td>
                <td class="data-col">{{ $detalle->contenido['num_consultorios'] ?? '---' }}</td>
            </tr>
            <tr>
                <td class="label-col">Denominación</td>
                <td class="data-col">{{ $detalle->contenido['denominacion_consultorio'] ?? '---' }}</td>
            </tr>
        </tbody>
    </table>

    {{-- 2. DATOS DEL PROFESIONAL --}}
    <table>
        <thead><tr><th colspan="2" class="section-header">2. DATOS DEL PROFESIONAL</th></tr></thead>
        <tbody>
            <tr>
                <td class="label-col">Apellidos y Nombres</td>
                <td class="data-col">
                    {{ $detalle->contenido['rrhh']['apellido_paterno'] ?? '' }} 
                    {{ $detalle->contenido['rrhh']['apellido_materno'] ?? '' }} 
                    {{ $detalle->contenido['rrhh']['nombres'] ?? '' }}
                </td>
            </tr>
            <tr>
                <td class="label-col">{{ $detalle->contenido['rrhh']['tipo_doc'] ?? 'DOCUMENTO DE IDENTIDAD' }}</td>
                <td class="data-col">{{ $detalle->contenido['rrhh']['doc'] ?? '---' }}</td>
            </tr>
            <tr>
                <td class="label-col">PROFESION</td>
                <td class="data-col">
                    {{ $detalle->contenido['rrhh']['cargo'] ?? '---' }}
                </td>
            </tr>
            <tr>
                <td class="label-col">¿Utiliza SIHCE?</td>
                <td class="data-col">{{ $detalle->contenido['cuenta_sihce'] ?? '---' }}</td>
            </tr>
            
            @if(($detalle->contenido['cuenta_sihce'] ?? '') == 'SI')
            <tr>
                <td class="label-col">¿Firmó Declaración Jurada?</td>
                <td class="data-col">{{ $detalle->contenido['firmo_dj'] ?? '---' }}</td>
            </tr>
            <tr>
                <td class="label-col">¿Firmó Confidencialidad?</td>
                <td class="data-col">{{ $detalle->contenido['firmo_confidencialidad'] ?? '---' }}</td>
            </tr>
            @else
            <tr>
                <td class="label-col">Documentación SIHCE</td>
                <td class="data-col" style="color: #94a3b8;">NO APLICA (NO UTILIZA SIHCE)</td>
            </tr>
            @endif
        </tbody>
    </table>

    {{-- 3. TIPO DE DNI Y FIRMA DIGITAL --}}
    @if(isset($detalle->contenido['tipo_dni']) && $detalle->contenido['tipo_dni'] != '')
    <table>
        <thead><tr><th colspan="2" class="section-header">3. TIPO DE DNI Y FIRMA DIGITAL</th></tr></thead>
        <tbody>
            <tr>
                <td class="label-col">Tipo de DNI</td>
                <td class="data-col">{{ $detalle->contenido['tipo_dni'] ?? '---' }}</td>
            </tr>
            @if(($detalle->contenido['tipo_dni'] ?? '') == 'ELECTRONICO')
            <tr>
                <td class="label-col">Versión DNIe</td>
                <td class="data-col">{{ $detalle->contenido['version_dnie'] ?? '---' }}</td>
            </tr>
            <tr>
                <td class="label-col">¿Firma Digitalmente en SIHCE?</td>
                <td class="data-col">{{ $detalle->contenido['firma_digital_sihce'] ?? '---' }}</td>
            </tr>
            @endif
            <tr>
                <td class="label-col">Observaciones</td>
                <td class="data-col">{{ $detalle->contenido['observaciones_dni'] ?? 'Sin observaciones' }}</td>
            </tr>
        </tbody>
    </table>
    @endif

    {{-- 4. ACCESO Y CAPACITACIÓN --}}
    <table>
        <thead><tr><th colspan="2" class="section-header">4. ACCESO Y CAPACITACIÓN</th></tr></thead>
        <tbody>
            <tr>
                <td class="label-col">¿Cuenta con Usuario y Acceso?</td>
                <td class="data-col">{{ $detalle->contenido['acceso_sistema'] ?? '---' }}</td>
            </tr>
            <tr>
                <td class="label-col">¿Recibió Capacitación?</td>
                <td class="data-col">{{ $detalle->contenido['recibio_capacitacion'] ?? '---' }}</td>
            </tr>
            @if(($detalle->contenido['recibio_capacitacion'] ?? '') == 'SI')
            <tr>
                <td class="label-col">Entidad Capacitadora</td>
                <td class="data-col">{{ $detalle->contenido['inst_que_lo_capacito'] ?? '---' }}</td>
            </tr>
            @endif
        </tbody>
    </table>

    {{-- 5. EQUIPAMIENTO REGISTRADO --}}
    <div style="margin-bottom: 15px;">
        <div class="section-header" style="margin-bottom: 5px;">5. EQUIPAMIENTO REGISTRADO</div>
        <table>
            <thead>
                <tr>
                    <th class="table-head" style="width: 25%;">DESCRIPCIÓN</th>
                    <th class="table-head" style="width: 7%; text-align: center;">CANT.</th>
                    <th class="table-head" style="width: 12%;">ESTADO</th>
                    <th class="table-head" style="width: 12%;">PROPIEDAD</th>
                    <th class="table-head" style="width: 16%;">N. SERIE / CP</th>
                    <th class="table-head" style="width: 28%;">OBSERVACIÓN</th>
                </tr>
            </thead>
            <tbody>
                @forelse($equipos as $equipo)
                <tr>
                    <td style="font-size: 9px;">{{ $equipo->descripcion }}</td>
                    <td class="text-center" style="font-weight: bold;">{{ $equipo->cantidad ?? 1 }}</td>
                    <td class="text-center" style="font-size: 9px;">{{ $equipo->estado }}</td>
                    <td class="text-center" style="font-size: 9px;">{{ $equipo->propio }}</td>
                    <td class="text-center" style="font-size: 9px;">{{ $equipo->nro_serie ?? '---' }}</td>
                    <td style="font-size: 9px;">{{ $equipo->observacion ?? '-' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center" style="color: #94a3b8; padding: 15px;">No se registraron equipos.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- 6. SOPORTE Y COMUNICACIÓN --}}
    <table>
        <thead><tr><th colspan="2" class="section-header">6. SOPORTE Y COMUNICACIÓN</th></tr></thead>
        <tbody>
            <tr>
                <td class="label-col">Ante dificultades comunica a</td>
                <td class="data-col">{{ $detalle->contenido['inst_a_quien_comunica'] ?? '---' }}</td>
            </tr>
            <tr>
                <td class="label-col">Medio utilizado</td>
                <td class="data-col">{{ $detalle->contenido['medio_que_utiliza'] ?? '---' }}</td>
            </tr>
        </tbody>
    </table>

    {{-- 7. COMENTARIOS --}}
    <table>
        <thead><tr><th class="section-header">7. COMENTARIOS</th></tr></thead>
        <tbody>
            <tr>
                <td class="data-col" style="width: 100%; font-weight: normal; padding: 10px;">
                    {{ $detalle->contenido['comentarios'] ?? 'Sin comentarios registrados.' }}
                </td>
            </tr>
        </tbody>
    </table>

    {{-- 8. EVIDENCIA FOTOGRÁFICA (UNA SOLA FOTO) --}}
    <div style="page-break-inside: avoid;">
        <div class="section-header" style="margin-bottom: 5px;">8. EVIDENCIA FOTOGRÁFICA</div>
        
        @php
            $fotoPath = $detalle->contenido['foto_evidencia'] ?? null;
            // Si por error antiguo es un array, tomar el primero
            if(is_array($fotoPath)) $fotoPath = $fotoPath[0] ?? null;
        @endphp

        @if($fotoPath && file_exists(public_path('storage/' . $fotoPath)))
            <div class="evidence-box">
                <img src="{{ public_path('storage/' . $fotoPath) }}" class="evidence-img">
            </div>
        @else
            <div class="evidence-box" style="color: #94a3b8;">No se adjuntó evidencia fotográfica.</div>
        @endif
    </div>

    {{-- 9. FIRMA --}}
    <div class="signature-section">
        <div class="section-header" style="margin-bottom: 40px;">9. FIRMA DE CONFORMIDAD</div>
        
        <div class="signature-box">
            <div style="height: 30px;"></div> 
            <div class="signature-line"></div>
            <div class="signature-name">
                
                {{ $detalle->contenido['rrhh']['apellido_paterno'] ?? '' }} 
                {{ $detalle->contenido['rrhh']['apellido_materno'] ?? '' }}
                {{ $detalle->contenido['rrhh']['nombres'] ?? '' }} 
            </div>
            <div class="signature-role">
                {{ $detalle->contenido['rrhh']['cargo'] ?? 'PROFESIONAL DE SALUD' }}
            </div>
            <div class="signature-role">
                {{ $detalle->contenido['rrhh']['tipo_doc'] ?? 'DNI' }}: {{ $detalle->contenido['rrhh']['doc'] ?? '________' }}
            </div>
        </div>
    </div>

    {{-- SCRIPT PARA PIE DE PÁGINA --}}
    <script type="text/php">
        if (isset($pdf)) {
            $font = $fontMetrics->get_font("Helvetica", "bold");
            $size = 8;
            $color = array(0.2, 0.2, 0.2);
            $y = $pdf->get_height() - 28;

            $textLeft = "SISTEMA DE ACTAS - DIRESA ICA";
            $pdf->page_text(30, $y, $textLeft, $font, $size, $color);

            $textRight = "PÁGINA {PAGE_NUM} DE {PAGE_COUNT}";
            $pdf->page_text($pdf->get_width() - 80, $y, $textRight, $font, $size, $color);
        }
    </script>

</body>
</html>