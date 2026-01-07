<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte Referencias - {{ $acta->establecimiento->nombre }}</title>
    <style>
        /* Configuraciones críticas para DomPDF */
        @page { margin: 1.5cm; }
        * { box-sizing: border-box; }
        body { font-family: 'Helvetica', sans-serif; font-size: 10px; line-height: 1.4; color: #1e293b; }
        
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #6366f1; padding-bottom: 10px; }
        .header h1 { margin: 0; font-size: 16px; text-transform: uppercase; color: #6366f1; }
        .header p { margin: 2px 0; font-weight: bold; color: #64748b; }
        
        .section-header { background: #f1f5f9; padding: 6px 10px; margin-top: 15px; border-left: 4px solid #4f46e5; font-weight: bold; text-transform: uppercase; font-size: 10px; }
        
        /* Tablas de Datos */
        table { width: 100%; border-collapse: collapse; margin-top: 8px; table-layout: fixed; }
        .table-data, .table-data th, .table-data td { border: 0.5px solid #cbd5e1; }
        th { background: #f8fafc; padding: 6px; text-align: left; font-size: 9px; color: #475569; text-transform: uppercase; }
        td { padding: 6px; vertical-align: top; }
        
        .text-center { text-align: center; }
        .uppercase { text-transform: uppercase; }

        /* SECCIÓN FOTOS: Ajuste para evitar deformación */
        .photo-table { width: 100%; border: none !important; margin-top: 10px; border-collapse: separate; border-spacing: 10px; }
        .photo-table td { border: none !important; text-align: center; vertical-align: top; }
        
        .img-box { 
            width: 100%; 
            max-height: 300px; /* Altura máxima controlada */
            border: 1px solid #e2e8f0; 
            border-radius: 8px; 
            background-color: #f8fafc; 
            overflow: hidden;
            display: block;
            padding: 5px;
        }
        .img-box img { 
            max-width: 100%; 
            max-height: 290px; 
            width: auto; 
            height: auto; 
            object-fit: contain; /* Mantiene la proporción original */
        }
        .photo-label { margin-top: 5px; font-size: 8px; font-weight: bold; color: #475569; text-transform: uppercase; }
        
        /* BLOQUE DE FIRMA */
        .signature-card { 
            border: 1px solid #1e293b !important; 
            background-color: #f8fafc !important; 
            border-radius: 15px !important; 
            padding: 20px; 
        }
        .signature-line { border-top: 1px solid #000000; width: 80%; margin: 0 auto 10px auto; height: 1px; }
    </style>
</head>
<body>

    <div class="header">
        <h1>Módulo 16: Refcon</h1>
        <p>Acta de Monitoreo N° {{ str_pad($acta->id, 5, '0', STR_PAD_LEFT) }}</p>
        <p>Establecimiento: {{ $acta->establecimiento->nombre ?? 'N/A' }}</p>
    </div>

    <div class="section-header">01. Responsable del Servicio</div>
    <table class="table-data">
        <tr>
            <th width="20%">Nombre Completo:</th>
            <td width="40%" class="uppercase">
                @if(isset($datos['personal']['nombre']))
                    {{ $datos['personal']['nombre'] }} 
                    {{ $datos['personal']['apellido_paterno'] ?? '' }} 
                    {{ $datos['personal']['apellido_materno'] ?? '' }}
                @else
                    {{ $detalle->personal_nombre ?? 'N/A' }}
                @endif
            </td>
            <th width="15%">DNI:</th>
            <td width="25%">{{ $datos['personal']['dni'] ?? ($detalle->personal_dni ?? 'N/A') }}</td>
        </tr>
        <tr>
            <th>Turno:</th>
            <td class="uppercase">{{ $datos['personal']['turno'] ?? ($detalle->personal_turno ?? 'N/A') }}</td>
            <th>Rol / Cargo:</th>
            <td class="uppercase">{{ $datos['personal']['rol'] ?? ($detalle->personal_roles ?? 'Responsable') }}</td>
        </tr>
        <tr>
            <th>Email:</th>
            <td>{{ $datos['personal']['email'] ?? 'N/A' }}</td>
            <th>Contacto:</th>
            <td>{{ $datos['personal']['contacto'] ?? 'N/A' }}</td>
        </tr>
    </table>

    <div class="section-header">02. Capacitación</div>
    <table class="table-data">
        <tr>
            <th width="30%">¿Recibió Capacitación?</th>
            <td>{{ $datos['capacitacion']['recibio'] ?? 'NO' }}</td>
            <th width="30%">Entidades:</th>
            <td>
                @if(isset($datos['capacitacion']['ente']))
                    {{ is_array($datos['capacitacion']['ente']) ? implode(', ', $datos['capacitacion']['ente']) : $datos['capacitacion']['ente'] }}
                @else N/A @endif
            </td>
        </tr>
    </table>

    <div class="section-header">03. Insumos y Equipamiento</div>
    <table class="table-data">
        <thead>
            <tr>
                <th>Descripción</th>
                <th class="text-center" width="50">Cant.</th>
                <th class="text-center" width="80">Estado</th>
                <th class="text-center" width="100">Propiedad</th>
                <th>Número de Serie</th>
            </tr>
        </thead>
        <tbody>
            @forelse($equipos as $eq)
            <tr class="uppercase">
                <td>{{ $eq->descripcion }}</td>
                <td class="text-center">{{ $eq->cantidad }}</td>
                <td class="text-center">{{ $eq->estado }}</td>
                <td class="text-center">{{ $eq->propio }}</td>
                <td>{{ $eq->nro_serie }}</td>
            </tr>
            @empty
            <tr><td colspan="5" class="text-center">No se registraron equipos.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="section-header">04. Gestión de Referencias</div>
    <table class="table-data">
        @php
            $preguntas = [
                'hojas_referencia' => '¿Cuenta con stock suficiente de formatos/hojas de referencia?',
                'libro_registro' => '¿El libro de registro de referencias se encuentra actualizado?',
                'contrareferencias' => '¿Se realiza el seguimiento y archivo de las contrareferencias?',
                'flujo_paciente' => '¿Existe un flujo definido para la referencia del paciente?',
                'digitacion_his' => '¿Se realiza la digitación oportuna en el sistema HIS?',
                'criterios_medicos' => '¿Las hojas de referencia cumplen criterios técnicos?',
                'comunicacion_destino' => '¿Se comunica con el destino antes de enviar al paciente?'
            ];
        @endphp
        @foreach($preguntas as $key => $texto)
        <tr>
            <td width="85%">{{ $texto }}</td>
            <td width="15%" class="text-center uppercase">{{ $datos['preguntas'][$key] ?? 'N/A' }}</td>
        </tr>
        @endforeach
    </table>

    <div class="section-header">05. Evidencias Fotográficas</div>
    <table class="photo-table">
        <tr>
            @if($detalle && !empty($detalle->foto_1))
            <td style="width: {{ empty($detalle->foto_2) ? '100%' : '50%' }};">
                <div class="img-box">
                    <img src="{{ public_path('storage/' . $detalle->foto_1) }}">
                </div>
                <div class="photo-label">EVIDENCIA FOTOGRÁFICA 01</div>
            </td>
            @endif

            @if($detalle && !empty($detalle->foto_2))
            <td style="width: {{ empty($detalle->foto_1) ? '100%' : '50%' }};">
                <div class="img-box">
                    <img src="{{ public_path('storage/' . $detalle->foto_2) }}">
                </div>
                <div class="photo-label">EVIDENCIA FOTOGRÁFICA 02</div>
            </td>
            @endif
        </tr>
    </table>

    <div style="margin-top: 60px;">
        <table style="width: 100%; border: none;">
            <tr>
                <td style="width: 20%; border: none;"></td>
                <td class="signature-card" style="width: 60%; text-align: center;">
                    <div style="height: 50px;"></div>
                    <div class="signature-line"></div>
                    <div class="uppercase" style="font-size: 11px; font-weight: bold;">
                        @if(isset($datos['personal']['nombre']))
                            {{ $datos['personal']['nombre'] }} 
                            {{ $datos['personal']['apellido_paterno'] ?? '' }} 
                            {{ $datos['personal']['apellido_materno'] ?? '' }}
                        @else
                            {{ $detalle->personal_nombre ?? 'SIN NOMBRE' }}
                        @endif
                    </div>
                    <div style="font-size: 10px; color: #334155;">
                        DNI: {{ $datos['personal']['dni'] ?? ($detalle->personal_dni ?? '________') }} <br>
                        <span>Profesional de Referencias</span>
                    </div>
                </td>
                <td style="width: 20%; border: none;"></td>
            </tr>
        </table>
    </div>
</body>
</html>