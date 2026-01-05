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
        .section-title {
            background-color: #f1f5f9;
            padding: 6px 10px;
            font-weight: bold;
            text-transform: uppercase;
            border-left: 4px solid #6366f1;
            margin-top: 15px;
            font-size: 12px;
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
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: right;
            font-size: 9px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 5px;
        }
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
        .photo-section {
        width: 100%;
        margin-top: 15px;
        text-align: center;
        }
        
        .photo-box {
            display: block;        /* Cambia a block para que ocupe su propia línea */
            width: 95%;           /* Casi todo el ancho de la página */
            margin: 15px auto;    /* Centra el contenedor */
            text-align: center;
        }

        .photo-box img {
            width: 500px;         /* Aumentamos de 250px a 500px (o usa 100% si quieres total) */
            height: 350px;        /* Aumentamos de 180px a 350px */
            object-fit: contain;  /* CAMBIO CLAVE: contain para que la foto se vea COMPLETA sin recortes */
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            background-color: #f8fafc; /* Fondo suave por si la foto es muy delgada */
        }
        
        .photo-caption {
            font-size: 9px;
            color: #64748b;
            text-transform: uppercase;
            margin-top: 5px;
            font-weight: bold;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>Módulo 08: Control de Crecimiento y Desarrollo (CRED)</h1>
        <p>Acta de Monitoreo N° {{ str_pad($acta->id, 5, '0', STR_PAD_LEFT) }}</p>
        <p>Establecimiento: {{ $acta->establecimiento->nombre ?? 'N/A' }}</p>
    </div>

    <div class="section-title">1. Responsable de la Atención</div>
        <table class="table">
        <tr>
            <th>Nombre del Responsable</th>
            <td>{{ $detalle->personal_nombre ?? 'No registrado' }}</td>
            <th>DNI</th>
            <td>{{ $detalle->personal_dni ?? 'No registrado' }}</td>
        </tr>
        <tr>
            <th>Turno</th>
            <td>{{ $detalle->personal_turno ?? 'N/A' }}</td>
            <th>Rol / Cargo</th>
            <td>{{ $detalle->personal_roles ?? 'N/A' }}</td>
        </tr>
        {{-- NUEVA FILA: EMAIL Y TELÉFONO --}}
        <tr>
            <th>Email</th>
            <td>{{ $datos['personal']['email'] ?? 'No registrado' }}</td>
            <th>Teléfono</th>
            <td>{{ $datos['personal']['contacto'] ?? 'No registrado' }}</td>
        </tr>
    </table>
    <div class="section-title">2. Capacitación</div>
    <table>
        <tr>
            <th width="30%">¿Recibió Capacitación?</th>
            {{-- Usamos $datos en lugar de $detalle->contenido --}}
            <td>{{ $datos['capacitacion']['recibio'] ?? 'NO' }}</td>
            
            <th width="30%">Entidades:</th>
            <td>
                @if(isset($datos['capacitacion']['ente']) && is_array($datos['capacitacion']['ente']))
                    {{ implode(', ', $datos['capacitacion']['ente']) }}
                @elseif(isset($datos['capacitacion']['ente']) && is_string($datos['capacitacion']['ente']))
                    {{ $datos['capacitacion']['ente'] }}
                @else
                    N/A
                @endif
            </td>
        </tr>
    </table>
    <div class="section-title">3. Inventario de Equipamiento Computarizado</div>
    <table>
        <thead>
            <tr>
                <th>Descripción Hardware</th>
                <th class="text-center">Cant.</th>
                <th class="text-center">Estado</th>
                <th class="text-center">Propiedad</th>
                <th class="text-center">Número de Serie</th>
                
            </tr>
        </thead>
        <tbody>
            @forelse($equipos as $eq)
            <tr class="uppercase">
                <td>{{ $eq->descripcion }}</td>
                <td class="text-center">{{ $eq->cantidad }}</td>
                <td class="text-center">{{ $eq->estado }}</td>
                <td class="text-center">{{ $eq->propio }}</td>
                <td>{{ $eq->nro_serie ? ''.$eq->nro_serie : '' }} {{ $eq->observaciones }}</td>
            </tr>
            @empty
            <tr><td colspan="5" class="text-center">No se registraron equipos.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="section-title">4. Métricas de Atención Mes Actual</div>
    <table class="table">
        <tr>
            <th>Atenciones CRED del mes</th>
            <td>{{ $datos['nro_atenciones_mes'] ?? 0 }}</td>
            <th>Descargas en HIS</th>
            <td>{{ $datos['descargas_his'] ?? 0 }}</td>
        </tr>
    </table>

    <div class="section-title">5. Soporte y Comunicación</div>
    <table class="table-data" style="width: 100%;">
    <tr>
        <th style="text-align: center;">¿A quién comunica dificultades?</th>
        <th style="text-align: center;">¿Qué medio utiliza?</th>
    </tr>
    <tr class="uppercase">
        <td style="text-align: center;">{{ $datos['soporte']['comunica'] ?? 'N/A' }}</td>
        <td style="text-align: center;">{{ $datos['soporte']['medio'] ?? 'N/A' }}</td>
    </tr>
</table>

    <div class="section-title">6. Evidencia Fotográfica</div>
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

    <div class="section-title">Firma del Responsable</div>

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
                    <span style="font-style: italic; color: #64748b;">CARGO:
                        {{ $detalle->personal_roles ?? 'Responsable de CRED' }}
                    </span>
                </div> 
            </td>

            <td style="width: 20%; border: none;"></td>
        </tr>
    </table>
</div>

    <div class="footer">
        Generado por: {{ Auth::user()->name }} | Fecha: {{ date('d/m/Y H:i:s') }} | Sistema de Actas ICATEC
    </div>

</body>
</html>
