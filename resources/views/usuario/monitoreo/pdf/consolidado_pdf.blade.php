<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Acta de Monitoreo N° {{ str_pad($acta->id, 5, '0', STR_PAD_LEFT) }}</title>
    <style>
        @page { margin: 1.2cm 1.5cm; }
        body { font-family: 'Helvetica', sans-serif; font-size: 9px; color: #1e293b; line-height: 1.3; }
        .header { text-align: center; border-bottom: 2px solid #0f172a; padding-bottom: 10px; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 15px; color: #0f172a; text-transform: uppercase; }
        .section-header { background-color: #f1f5f9; padding: 5px 10px; font-weight: bold; border-left: 4px solid #4f46e5; margin-top: 15px; margin-bottom: 8px; text-transform: uppercase; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; table-layout: fixed; }
        th, td { border: 1px solid #e2e8f0; padding: 5px 8px; text-align: left; vertical-align: middle; word-wrap: break-word; }
        th { background-color: #f8fafc; font-size: 8px; text-transform: uppercase; }
        .bg-label { background-color: #f8fafc; font-weight: bold; width: 35%; }
        .uppercase { text-transform: uppercase; }
        .no-break { page-break-inside: avoid; }
        .firmas-container { margin-top: 30px; width: 100%; text-align: center; }
        .firma-box { width: 45%; display: inline-block; text-align: center; vertical-align: top; margin-bottom: 40px; }
        .linea { border-top: 1px solid #000; margin-top: 50px; margin-bottom: 5px; width: 80%; margin-left: auto; margin-right: auto; }
        .cargo { font-size: 7.5px; font-weight: bold; text-transform: uppercase; display: block; }
    </style>
</head>
<body>

    <div class="header">
        <h1>Acta de Monitoreo N° {{ str_pad($acta->id, 5, '0', STR_PAD_LEFT) }}</h1>
        <div style="font-weight: bold;">ESTABLECIMIENTO: {{ strtoupper($acta->establecimiento->nombre) }}</div>
    </div>

    <div class="section-header">1. Información General</div>
    <table>
        <tr>
            <td class="bg-label">Fecha de Visita:</td>
            <td>{{ \Carbon\Carbon::parse($acta->fecha_monitoreo)->format('d/m/Y') }}</td>
        </tr>
        <tr>
            <td class="bg-label">Monitor Responsable:</td>
            <td class="uppercase">{{ $monitor['nombre'] }}</td>
        </tr>
        <tr>
            <td class="bg-label">Jefe del Establecimiento:</td>
            <td class="uppercase">{{ $jefe['nombre'] }}</td>
        </tr>
    </table>

    @foreach($modulos as $mod)
        <div class="no-break">
            <div class="section-header">Módulo: {{ strtoupper(str_replace('_', ' ', $mod->modulo_nombre)) }}</div>
            
            @php
                $cont = is_string($mod->contenido) ? json_decode($mod->contenido, true) : $mod->contenido;
            @endphp

            @if(is_array($cont))
                <table>
                    @foreach($cont as $key => $value)
                        {{-- Filtramos campos internos, fotos y arrays para la tabla general --}}
                        @if(!is_array($value) && !in_array($key, ['foto_evidencia', 'comentarios', 'observaciones', 'password']))
                            <tr>
                                <td class="bg-label">{{ strtoupper(str_replace(['_', 'inst'], [' ', 'entidad'], $key)) }}:</td>
                                <td class="uppercase">{{ $value }}</td>
                            </tr>
                        @endif
                    @endforeach
                </table>
                
                {{-- Manejo de Observaciones u Observaciones según el módulo --}}
                @php $obs = $cont['observaciones'] ?? ($cont['comentarios'] ?? null); @endphp
                @if($obs)
                <div style="font-size: 8px; font-style: italic; margin-bottom: 10px; color: #475569; padding: 0 10px;">
                    <strong>Observaciones del Módulo:</strong> {{ strtoupper($obs) }}
                </div>
                @endif
            @else
                <p style="color: red; padding: 10px;">Error: Los datos de este módulo no tienen el formato correcto.</p>
            @endif
        </div>
    @endforeach

    @if($equipos->count() > 0)
        <div class="no-break">
            <div class="section-header">2. Inventario de Equipos Tecnológicos</div>
            <table>
                <thead>
                    <tr>
                        <th width="35%">Descripción</th>
                        <th width="20%">Módulo</th>
                        <th width="25%">N° Serie</th>
                        <th width="20%">Propiedad</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($equipos as $eq)
                        <tr class="uppercase">
                            <td>{{ $eq->descripcion }}</td>
                            <td>{{ str_replace('_', ' ', $eq->modulo) }}</td>
                            <td>{{ $eq->nro_serie ?? 'S/N' }}</td>
                            <td>{{ $eq->propio }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <div class="no-break">
        <div class="section-header">Firmas de Conformidad</div>
        <div class="firmas-container">
            {{-- Firma del Monitor --}}
            <div class="firma-box">
                <div class="linea"></div>
                <span class="cargo">{{ $monitor['nombre'] }}</span>
                <span class="cargo">Monitor Responsable</span>
                <div style="font-size: 7px;">DNI: {{ $monitor['dni'] }}</div>
            </div>

            {{-- Firma del Jefe --}}
            <div class="firma-box">
                <div class="linea"></div>
                <span class="cargo">{{ $jefe['nombre'] }}</span>
                <span class="cargo">{{ $jefe['cargo'] }}</span>
                <div style="font-size: 7px;">DNI: {{ $jefe['dni'] }}</div>
            </div>

            {{-- Firmas del Equipo de Monitoreo (Acompañantes) --}}
            @foreach($equipoMonitoreo as $miembro)
                <div class="firma-box">
                    <div class="linea"></div>
                    <span class="cargo">{{ strtoupper($miembro->nombres_apellidos ?? ($miembro->nombre ?? 'MIEMBRO TÉCNICO')) }}</span>
                    <span class="cargo">{{ strtoupper($miembro->cargo ?? 'Equipo de Monitoreo') }}</span>
                    <div style="font-size: 7px;">DNI: {{ $miembro->dni ?? '____________' }}</div>
                </div>
            @endforeach
        </div>
    </div>

</body>
</html>