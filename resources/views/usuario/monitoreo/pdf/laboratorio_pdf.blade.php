<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>REPORTE MÓDULO 05 - LABORATORIO</title>
    <style>
        /* Configuración de márgenes para permitir el pie de página fijo */
        @page { margin: 0.8cm 0.8cm 2cm 0.8cm; }
        body { font-family: 'Helvetica', Arial, sans-serif; font-size: 10px; color: #333; line-height: 1.5; }
        
        /* Encabezado Principal */
        .main-header { text-align: center; margin-bottom: 20px; }
        .module-title { font-size: 16px; font-weight: bold; color: #059669; text-transform: uppercase; margin: 0; }
        .acta-info { font-size: 10px; font-weight: bold; color: #666; margin-top: 5px; text-transform: uppercase; }
        .emerald-line { border-bottom: 2px solid #059669; margin-top: 10px; margin-bottom: 20px; }

        /* Estilo de Tablas */
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; table-layout: fixed; }
        
        .section-header { 
            background-color: #ecfdf5; 
            border-left: 5px solid #059669; 
            padding: 8px 12px; 
            font-weight: bold; 
            text-transform: uppercase; 
            color: #064e3b;
            font-size: 11px;
            text-align: left;
        }

        th.table-head { background-color: #f8fafc; color: #64748b; font-size: 8px; text-transform: uppercase; padding: 5px; border: 1px solid #e2e8f0; }
        td { padding: 7px 10px; border: 1px solid #e2e8f0; text-align: left; word-wrap: break-word; }
        
        .label-col { background-color: #ffffff; font-weight: bold; width: 35%; color: #334155; text-transform: uppercase; font-size: 8.5px; }
        .data-col { background-color: #ffffff; width: 65%; text-transform: uppercase; color: #000; font-weight: bold; }

        /* Estilos para Evidencia y Firma */
        .evidence-container { text-align: center; margin-top: 10px; padding: 10px; }
        .evidence-img { max-width: 350px; border: 1px solid #e2e8f0; padding: 5px; border-radius: 4px; }
        
        .signature-box { 
            margin: 30px auto; 
            width: 350px; 
            text-align: center; 
            border: 1px solid #e2e8f0; 
            border-radius: 20px; 
            padding: 25px;
            page-break-inside: avoid;
        }
        .signature-line { border-top: 1.5px solid #333; margin: 15px 30px; }
        .signature-name { font-weight: bold; text-transform: uppercase; font-size: 10px; }
        .signature-detail { color: #475569; font-size: 9px; text-transform: uppercase; margin-top: 3px; font-weight: bold; }

        /* PIE DE PÁGINA SEGÚN REFERENCIA */
        .footer { 
            position: fixed; 
            bottom: -1cm; 
            left: 0; 
            right: 0; 
            height: 50px; 
            text-align: center; 
            font-size: 9px; 
            color: #94a3b8; 
            border-top: 1px solid #e2e8f0; 
            padding-top: 10px; 
        }
        .pagenum:before { content: counter(page); }
    </style>
</head>
<body>

    <div class="footer">
        Acta de Monitoreo IPRESS NO ESPECIALIZADAS N° {{ str_pad($acta->id, 1, '0', STR_PAD_LEFT) }}<br>
        Página <span class="pagenum"></span> 
    </div>

    <div class="main-header">
        <h1 class="module-title">Módulo 05: Laboratorio Clínico</h1>
        <p class="acta-info">ACTA N° {{ str_pad($acta->id, 5, '0', STR_PAD_LEFT) }} | E.E.S.S.: {{ $acta->establecimiento->nombre }}</p>
        <div class="emerald-line"></div>
    </div>

    {{-- 1. DETALLES DE MONITOREO --}}
    <table>
        <thead><tr><th colspan="2" class="section-header">1. DETALLES DE MONITOREO</th></tr></thead>
        <tbody>
            <tr>
                <td class="label-col">Turno de Evaluación</td>
                <td class="data-col">{{ $detalle->contenido['turno'] ?? '---' }}</td>
            </tr>
        </tbody>
    </table>

    {{-- 2. DATOS DEL PROFESIONAL --}}
    <table>
        <thead><tr><th colspan="2" class="section-header">2. PERSONAL DE LABORATORIO</th></tr></thead>
        <tbody>
            <tr>
                <td class="label-col">Nombres y Apellidos</td>
                <td class="data-col">
                    {{ $detalle->contenido['rrhh']['apellido_paterno'] ?? '' }} 
                    {{ $detalle->contenido['rrhh']['apellido_materno'] ?? '' }} 
                    {{ $detalle->contenido['rrhh']['nombres'] ?? '' }}
                </td>
            </tr>
            <tr>
                <td class="label-col">Documento de Identidad</td>
                <td class="data-col">{{ $detalle->contenido['rrhh']['doc'] ?? '---' }}</td>
            </tr>
        </tbody>
    </table>

    {{-- 3. DOCUMENTACIÓN ADMINISTRATIVA --}}
    <table>
        <thead><tr><th colspan="2" class="section-header">3. DOCUMENTACIÓN ADMINISTRATIVA</th></tr></thead>
        <tbody>
            <tr>
                <td class="label-col">¿Firmó Declaración Jurada?</td>
                <td class="data-col">{{ $detalle->contenido['firmo_dj'] ?? '---' }}</td>
            </tr>
            <tr>
                <td class="label-col">¿Firmó Compromiso de Confidencialidad?</td>
                <td class="data-col">{{ $detalle->contenido['firmo_confidencialidad'] ?? '---' }}</td>
            </tr>
        </tbody>
    </table>

    {{-- 4. TIPO DE DNI Y FIRMA DIGITAL --}}
    <table>
        <thead><tr><th colspan="2" class="section-header">4. TIPO DE DNI Y FIRMA DIGITAL</th></tr></thead>
        <tbody>
            <tr>
                <td class="label-col">Tipo de Documento</td>
                <td class="data-col">{{ (isset($detalle->contenido['version_dni']) && $detalle->contenido['version_dni'] !== 'NO APLICA') ? 'ELECTRONICO' : 'AZUL' }}</td>
            </tr>
            @if(isset($detalle->contenido['version_dni']) && $detalle->contenido['version_dni'] !== 'NO APLICA')
            <tr>
                <td class="label-col">Versión del DNIe</td>
                <td class="data-col">{{ $detalle->contenido['version_dni'] ?? '---' }}</td>
            </tr>
            <tr>
                <td class="label-col">¿Firma Digitalmente en SIHCE?</td>
                <td class="data-col">{{ $detalle->contenido['firma_digital'] ?? '---' }}</td>
            </tr>
            @endif
            <tr>
                <td class="label-col">Observaciones / Motivo de Uso</td>
                <td class="data-col">{{ $detalle->contenido['observaciones_dni'] ?? '---' }}</td>
            </tr>
        </tbody>
    </table>

    {{-- 5. PROCESOS Y CALIDAD --}}
    <table>
        <thead><tr><th colspan="2" class="section-header">5. PROCESOS Y CALIDAD</th></tr></thead>
        <tbody>
            <tr>
                <td class="label-col">¿Cuenta con Manual de Procedimientos?</td>
                <td class="data-col">{{ $detalle->contenido['manual_procedimientos'] ?? '---' }}</td>
            </tr>
            <tr>
                <td class="label-col">¿Realiza Control Interno?</td>
                <td class="data-col">{{ $detalle->contenido['control_interno'] ?? '---' }}</td>
            </tr>
        </tbody>
    </table>

    {{-- 6. CAPACITACIÓN Y COMUNICACIÓN --}}
    <table>
        <thead><tr><th colspan="2" class="section-header">6. CAPACITACIÓN Y COMUNICACIÓN</th></tr></thead>
        <tbody>
            <tr>
                <td class="label-col">¿Recibió capacitación técnica?</td>
                <td class="data-col">{{ $detalle->contenido['recibio_capacitacion'] ?? '---' }}</td>
            </tr>
            @if(($detalle->contenido['recibio_capacitacion'] ?? '') === 'SI')
            <tr>
                <td class="label-col">Entidad Capacitadora</td>
                <td class="data-col">{{ $detalle->contenido['inst_que_lo_capacito'] ?? '---' }}</td>
            </tr>
            @endif
            <tr>
                <td class="label-col">Comunica dificultades a</td>
                <td class="data-col">{{ $detalle->contenido['inst_a_quien_comunica'] ?? '---' }}</td>
            </tr>
            <tr>
                <td class="label-col">Medio de comunicación</td>
                <td class="data-col">{{ $detalle->contenido['medio_que_utiliza'] ?? '---' }}</td>
            </tr>
        </tbody>
    </table>

    {{-- 7. EQUIPAMIENTO DEL ÁREA --}}
    <table>
        <thead><tr><th colspan="5" class="section-header">7. EQUIPAMIENTO DEL ÁREA</th></tr></thead>
        <thead>
            <tr>
                <th class="table-head" style="width: 25%;">Número de Serie</th>
                <th class="table-head" style="width: 8%;">Cant.</th>
                <th class="table-head" style="width: 37%;">Descripción del Equipo</th>
                <th class="table-head" style="width: 15%;">Estado</th>
                <th class="table-head" style="width: 15%;">Propiedad</th>
            </tr>
        </thead>
        <tbody>
            @forelse($equipos as $equipo)
            <tr>
                <td style="text-align: center;">{{ $equipo->nro_serie ?? 'S/N' }}</td>
                <td style="text-align: center;">{{ $equipo->cantidad ?? 1 }}</td>
                <td>{{ $equipo->descripcion }}</td>
                <td style="text-align: center;">{{ $equipo->estado }}</td>
                <td style="text-align: center;">{{ $equipo->propio }}</td>
            </tr>
            @empty
            <tr><td colspan="5" style="text-align: center; color: #94a3b8;">No se registraron equipos.</td></tr>
            @endforelse
        </tbody>
    </table>

    {{-- 8. COMENTARIOS --}}
    <table>
        <thead><tr><th class="section-header">8. COMENTARIOS Y OBSERVACIONES</th></tr></thead>
        <tbody>
            <tr>
                <td class="data-col" style="text-transform: uppercase; font-weight: normal; min-height: 40px;">
                    {{ $detalle->contenido['comentarios'] ?? 'SIN COMENTARIOS REGISTRADOS.' }}
                </td>
            </tr>
        </tbody>
    </table>

    {{-- 9. EVIDENCIA FOTOGRÁFICA --}}
    <table><thead><tr><th class="section-header">9. EVIDENCIA FOTOGRÁFICA</th></tr></thead></table>
    @if(isset($detalle->contenido['foto_evidencia']))
        @php $path = public_path('storage/' . $detalle->contenido['foto_evidencia']); @endphp
        <div class="evidence-container">
            @if(file_exists($path))
                <img src="{{ $path }}" class="evidence-img">
            @else
                <div style="color: red; font-style: italic;">Imagen no encontrada en el servidor.</div>
            @endif
        </div>
    @endif

    {{-- 10. FIRMA DE CONFORMIDAD --}}
    <table style="margin-top: 20px;"><thead><tr><th class="section-header">10. FIRMA DE CONFORMIDAD</th></tr></thead></table>
    <div class="signature-box">
        <div style="height: 50px;"></div>
        <div class="signature-line"></div>
        <div class="signature-name">
            {{ $detalle->contenido['rrhh']['apellido_paterno'] ?? '' }} 
            {{ $detalle->contenido['rrhh']['apellido_materno'] ?? '' }}, 
            {{ $detalle->contenido['rrhh']['nombres'] ?? '' }}
        </div>
        <div class="signature-detail">PROFESIONAL DE LABORATORIO</div>
        <div class="signature-detail">DNI: {{ $detalle->contenido['rrhh']['doc'] ?? '________' }}</div>
    </div>

</body>
</html>