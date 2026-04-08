<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Incidencia #{{ $incidencia->id }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #333; line-height: 1.4; margin: 0; padding: 0; font-size: 11px; }
        .header { width: 100%; margin-bottom: 20px; border-bottom: 2px solid #f97316; padding-bottom: 10px; }
        .header table { width: 100%; border-collapse: collapse; }
        .header .logos { text-align: left; }
        .header .logos img { height: 45px; margin-right: 15px; }
        .header .titulo { text-align: right; }
        .header h1 { margin: 0; color: #f97316; font-size: 18px; text-transform: uppercase; }
        .header p { margin: 2px 0 0; color: #64748b; font-size: 10px; }

        .section { margin-bottom: 15px; }
        .section-title { background: #f8fafc; border-left: 4px solid #f97316; padding: 6px 10px; font-weight: bold; color: #1e293b; text-transform: uppercase; margin-bottom: 8px; font-size: 10px; }
        
        .grid { width: 100%; border-collapse: collapse; }
        .grid td { padding: 5px; vertical-align: top; border: 1px solid #e2e8f0; }
        .label { font-weight: bold; color: #64748b; width: 30%; }
        .value { color: #1e293b; }

        .badge { display: inline-block; padding: 2px 8px; border-radius: 4px; font-size: 9px; font-weight: bold; text-transform: uppercase; }
        .badge-pendiente { background: #fef3c7; color: #92400e; }
        .badge-proceso { background: #dbeafe; color: #1e40af; }
        .badge-resuelto { background: #d1fae5; color: #065f46; }

        .observacion { background: #fff; border: 1px solid #e2e8f0; padding: 10px; border-radius: 4px; min-height: 50px; }

        .respuesta-item { margin-bottom: 10px; padding: 10px; border: 1px solid #e2e8f0; border-radius: 6px; }
        .respuesta-header { font-weight: bold; color: #475569; border-bottom: 1px solid #f1f5f9; padding-bottom: 4px; margin-bottom: 6px; display: flex; justify-content: space-between; }
        
        .footer { position: fixed; bottom: -30px; left: 0; right: 0; text-align: center; color: #94a3b8; font-size: 9px; border-top: 1px solid #f1f5f9; padding-top: 5px; }
        
        .watermark { position: fixed; top: 40%; left: 15%; font-size: 80px; color: rgba(249, 115, 22, 0.05); transform: rotate(-45deg); z-index: -1; font-weight: bold; }
    </style>
</head>
<body>
    <div class="watermark">ICATEC MESA AYUDA</div>

    <div class="header">
        <table>
            <tr>
                <td class="logos">
                    {{-- Usando rutas absolutas para DomPDF --}}
                    <img src="{{ public_path('images/logoreferencias.png') }}" alt="GORE ICA">
                    <img src="{{ public_path('img/diresa.png') }}" alt="DIRESA">
                </td>
                <td class="titulo">
                    <h1>Ticket #{{ $incidencia->id }}</h1>
                    <p>Fecha de reporte: {{ $incidencia->created_at->format('d/m/Y H:i') }}</p>
                    <p>Estado actual: 
                        <span class="badge badge-{{ $incidencia->estado == 'Pendiente' ? 'pendiente' : ($incidencia->estado == 'En proceso' ? 'proceso' : 'resuelto') }}">
                            {{ $incidencia->estado }}
                        </span>
                    </p>
                </td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Datos del Reportante</div>
        <table class="grid">
            <tr>
                <td class="label">DNI:</td>
                <td class="value">{{ $incidencia->dni }}</td>
                <td class="label">Profesional:</td>
                <td class="value">{{ $incidencia->apellidos }}, {{ $incidencia->nombres }}</td>
            </tr>
            <tr>
                <td class="label">Celular:</td>
                <td class="value">{{ $incidencia->celular }}</td>
                <td class="label">Correo:</td>
                <td class="value">{{ $incidencia->correo }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Ubicación del Incidente</div>
        <table class="grid">
            <tr>
                <td class="label">IPRESS:</td>
                <td class="value">{{ $incidencia->codigo_ipress }}</td>
                <td class="label">Establecimiento:</td>
                <td class="value">{{ $incidencia->nombre_establecimiento }}</td>
            </tr>
            <tr>
                <td class="label">Provincia / Distrito:</td>
                <td class="value">{{ $incidencia->provincia_establecimiento }} / {{ $incidencia->distrito_establecimiento }}</td>
                <td class="label">Red / Microred:</td>
                <td class="value">{{ $incidencia->red }} / {{ $incidencia->microred }}</td>
            </tr>
            <tr>
                <td class="label">Módulos Afectados:</td>
                <td class="value" colspan="3">{{ $incidencia->modulos }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Detalle del Incidente / Observación</div>
        <div class="observacion">
            {!! nl2br(e($incidencia->observacion)) !!}
        </div>
    </div>

    {{-- EVIDENCIAS DE LA INCIDENCIA --}}
    @php
        $fotosIncidencia = [];
        if($incidencia->imagen1) $fotosIncidencia[] = $incidencia->imagen1;
        if($incidencia->imagen2) $fotosIncidencia[] = $incidencia->imagen2;
        if($incidencia->imagen3) $fotosIncidencia[] = $incidencia->imagen3;
    @endphp

    @if(count($fotosIncidencia) > 0)
    <div class="section">
        <div class="section-title">Evidencias del Profesional</div>
        <table style="width: 100%;">
            <tr>
                @foreach($fotosIncidencia as $foto)
                <td style="width: 33%; text-align: center; padding: 5px;">
                    @php
                        $path = storage_path('app/public/' . $foto);
                    @endphp
                    @if(file_exists($path))
                        <img src="{{ $path }}" style="max-width: 100%; max-height: 150px; border: 1px solid #ddd; border-radius: 4px;">
                    @else
                        <div style="font-size: 8px; color: #999;">Archivo no encontrado</div>
                    @endif
                </td>
                @endforeach
            </tr>
        </table>
    </div>
    @endif

    @if($respuestas->count() > 0)
    <div class="section">
        <div class="section-title">Historial de Seguimiento y Respuesta</div>
        @foreach($respuestas as $resp)
        <div class="respuesta-item">
            <div class="respuesta-header">
                Atendido por: {{ $resp->usuario->name ?? 'Sistema' }} | {{ $resp->created_at->format('d/m/Y H:i') }}
            </div>
            <div style="margin-bottom: 5px;">
                <strong>Estado:</strong> {{ $resp->estado }}
            </div>
            <div style="margin-bottom: 8px;">
                {!! nl2br(e($resp->respuesta)) !!}
            </div>

            {{-- FOTOS DE LA RESPUESTA --}}
            @php
                $fotosResp = [];
                if($resp->imagen1) $fotosResp[] = $resp->imagen1;
                if($resp->imagen2) $fotosResp[] = $resp->imagen2;
                if($resp->imagen3) $fotosResp[] = $resp->imagen3;
            @endphp
            @if(count($fotosResp) > 0)
                <table style="width: 100%; margin-top: 10px;">
                    <tr>
                        @foreach($fotosResp as $fr)
                        <td style="width: 33%; text-align: center; padding: 2px;">
                            @php $pResp = storage_path('app/public/' . $fr); @endphp
                            @if(file_exists($pResp))
                                <img src="{{ $pResp }}" style="max-width: 100%; max-height: 100px; border-radius: 2px;">
                            @endif
                        </td>
                        @endforeach
                    </tr>
                </table>
            @endif
        </div>
        @endforeach
    </div>
    @endif

    <div class="footer">
        Este documento es un reporte generado automáticamente por la Plataforma ICATEC - Sistema de Gestión de Incidencias SIHCE.
        <br>Generado el {{ date('d/m/Y H:i:s') }}
    </div>
</body>
</html>
