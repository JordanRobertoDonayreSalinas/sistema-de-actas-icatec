<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte Farmacia - {{ $acta->establecimiento->nombre }}</title>
    <style>
        @page { margin: 1.5cm; }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 10px;
            color: #1e293b;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #6366f1;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 16px;
            text-transform: uppercase;
            color: #6366f1;
        }
        .header p {
            margin: 2px 0;
            font-weight: bold;
            color: #64748b;
        }
        .section-header { 
            background: #f1f5f9; 
            padding: 6px 10px; 
            margin-top: 15px; 
            border-left: 4px solid #4f46e5; 
            font-weight: bold; 
            text-transform: uppercase; 
            font-size: 10px; 
        }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; table-layout: fixed; }
        .table-data, .table-data th, .table-data td { border: 0.5px solid #cbd5e1; }
        th { background: #f8fafc; padding: 6px; text-align: left; font-size: 9px; color: #475569; text-transform: uppercase; }
        td { padding: 6px; font-size: 10px; vertical-align: top; }
        
        .text-center { text-align: center; }
        .uppercase { text-transform: uppercase; }
        
        /* SECCIÓN FOTOS: Lógica de visualización grande */
        .photo-section { width: 100%; margin-top: 15px; text-align: center; }
        .photo-box {
            display: block;
            width: 95%;
            margin: 15px auto;
            text-align: center;
        }
        .photo-box img {
            width: 500px;
            height: 350px;
            object-fit: contain;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            background-color: #f8fafc;
        }

        /* BLOQUE DE FIRMA */
        .signature-card { 
            border: 1px solid #1e293b !important; 
            background-color: #f8fafc !important; 
            border-radius: 15px !important; 
            padding: 25px; 
        }
        .signature-line { border-top: 1px solid #000000; width: 80%; margin: 0 auto 10px auto; height: 1px; }
    </style>
</head>
<body>

    <div class="header">
        <h1>Módulo 15: Farmacia</h1>
        <p>Acta de Monitoreo N° {{ str_pad($acta->id, 5, '0', STR_PAD_LEFT) }}</p>
        <p>Establecimiento: {{ $acta->establecimiento->nombre ?? 'N/A' }}</p>
    </div>

    <div class="section-header">01. Responsable del Servicio</div>
    <table class="table-data">
        <tr>
            <th width="20%">Nombre:</th>
            <td width="40%" class="uppercase">{{ $detalle->personal_nombre ?? 'N/A' }}</td>
            <th width="15%">DNI:</th>
            <td width="25%">{{ $detalle->personal_dni ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th>Turno</th>
            <td class="uppercase">{{ $detalle->personal_turno ?? 'N/A' }} </td>
            <th>Rol / Cargo</th>
            <td class="uppercase">{{ $detalle->personal_roles ?? ' ' }}</td>
        </tr>
        <tr>
            <th>Email:</th>
            <td>{{ $datos['personal']['email'] ?? 'N/A' }}</td>
            <th>Teléfono:</th>
            <td>{{ $datos['personal']['contacto'] ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th>Declaración Jurada</th>
            <td>{{ $datos['documentacion']['firma_dj'] ?? 'NO' }}</td>
            <th>Compromiso Confidencialidad</th>
            <td>{{ $datos['documentacion']['firma_confidencialidad'] ?? 'NO' }}</td>
        </tr>
        <tr>
            <th>Tipo de DNI Físico</th>
            <td @if(($datos['dni_firma']['tipo_dni_fisico'] ?? 'AZUL') != 'ELECTRONICO') colspan="3" @endif>
                <strong>DNI {{ $datos['dni_firma']['tipo_dni_fisico'] ?? 'AZUL' }}</strong>
            </td>
            @if(($datos['dni_firma']['tipo_dni_fisico'] ?? '') == 'ELECTRONICO')
                <th>DETALLE DNIe</th>
                <td>
                    V: {{ $datos['dni_firma']['dnie_version'] ?? '---' }} | Firma: {{ $datos['dni_firma']['firma_sihce'] ?? 'NO' }}
                </td>
            @endif
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
                <td>{{ $eq->nro_serie }} {{ $eq->observaciones }}</td>
            </tr>
            @empty
            <tr><td colspan="5" class="text-center">No se registraron equipos.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="section-header">04. Gestión de Stock y Almacenamiento</div>
    <table class="table-data">
        @php
            $preguntas = [
                'sis_gestion' => '¿Cuenta con sistema de gestión para el control de inventario?',
                'stock_actual' => '¿El stock físico coincide con el reporte del sistema?',
                'fua_sismed' => '¿Realiza la digitación oportuna en el SISMED?',
                'inventario_anual' => '¿Ha realizado el inventario anual de medicamentos e insumos?'
            ];
        @endphp
        @foreach($preguntas as $key => $texto)
        <tr>
            <td width="85%">{{ $texto }}</td>
            <td width="15%" class="text-center uppercase">{{ $datos['preguntas'][$key] ?? 'N/A' }}</td>
        </tr>
        @endforeach
    </table>

    <div class="section-header">05. Soporte y Comunicación</div>
    <table class="table-data">
        <tr class="uppercase">
            <th class="text-center">¿A quién comunica dificultades?</th>
            <td class="text-center">{{ $datos['soporte']['comunica'] ?? 'N/A' }}</td>
            <th class="text-center">Medio:</th>
            <td class="text-center">{{ $datos['soporte']['medio'] ?? 'N/A' }}</td>
        </tr>
    </table>

    <div class="section-header">06. Evidencias Fotográficas</div>
    <div class="photo-section">
        @if(!empty($detalle->foto_1))
            @php $path1 = storage_path('app/public/' . $detalle->foto_1); @endphp
            @if(file_exists($path1))
                <div class="photo-box"><img src="{{ $path1 }}"></div>
            @endif
        @endif
        @if(!empty($detalle->foto_2))
            @php $path2 = storage_path('app/public/' . $detalle->foto_2); @endphp
            @if(file_exists($path2))
                <div class="photo-box"><img src="{{ $path2 }}"></div>
            @endif
        @endif
    </div>

    <div class="section-header">Firma del responsable</div>
    <div style="margin-top: 60px;">
        <table style="width: 100%; border: none;">
            <tr>
                <td style="width: 20%; border: none;"></td>
                <td class="signature-card" style="width: 60%; text-align: center;">
                    <div style="height: 60px;"></div>
                    <div class="signature-line"></div>
                    <div class="uppercase" style="font-size: 11px;">{{ $detalle->personal_nombre ?? 'SIN NOMBRE' }}</div>
                    <div style="font-size: 10px; color: #334155;">
                        DNI: {{ $detalle->personal_dni ?? '________' }} <br>
                        <span style="font-style: italic;">{{ $detalle->personal_roles ?? 'Responsable de Farmacia' }}</span>
                    </div>
                </td>
                <td style="width: 20%; border: none;"></td>
            </tr>
        </table>
    </div>
</body>
</html>