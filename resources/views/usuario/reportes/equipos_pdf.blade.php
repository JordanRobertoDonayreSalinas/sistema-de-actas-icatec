<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Equipos de Cómputo</title>
    @php
        use App\Helpers\ModuloHelper;
    @endphp
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 9pt;
            color: #1e293b;
            line-height: 1.4;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 3px solid #6366f1;
        }

        .header h1 {
            font-size: 18pt;
            color: #4f46e5;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .header p {
            font-size: 9pt;
            color: #64748b;
        }

        .filtros-aplicados {
            background: #f1f5f9;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #8b5cf6;
        }

        .filtros-aplicados h3 {
            font-size: 10pt;
            color: #6366f1;
            margin-bottom: 8px;
            font-weight: bold;
        }

        .filtros-aplicados p {
            font-size: 8pt;
            color: #475569;
            margin-bottom: 3px;
        }

        .filtros-aplicados strong {
            color: #1e293b;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        thead {
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            color: white;
        }

        thead th {
            padding: 10px 6px;
            text-align: left;
            font-size: 8pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        tbody tr {
            border-bottom: 1px solid #e2e8f0;
        }

        tbody tr:nth-child(even) {
            background: #f8fafc;
        }

        tbody tr:hover {
            background: #f1f5f9;
        }

        tbody td {
            padding: 8px 6px;
            font-size: 8pt;
            color: #334155;
        }

        .estado-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 7pt;
            font-weight: bold;
            text-transform: uppercase;
        }

        .estado-operativo {
            background: #dcfce7;
            color: #166534;
        }

        .estado-inoperativo {
            background: #fee2e2;
            color: #991b1b;
        }

        .estado-otro {
            background: #fef3c7;
            color: #92400e;
        }

        .footer-info {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 2px solid #e2e8f0;
            font-size: 8pt;
            color: #64748b;
        }

        .footer-info p {
            margin-bottom: 3px;
        }

        .total-equipos {
            background: #ede9fe;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 15px;
            text-align: center;
            border: 2px solid #8b5cf6;
        }

        .total-equipos h2 {
            font-size: 11pt;
            color: #6366f1;
            font-weight: bold;
        }

        .no-data {
            text-align: center;
            padding: 40px;
            color: #94a3b8;
            font-style: italic;
        }
    </style>
</head>

<body>
    {{-- Encabezado --}}
    <div class="header">
        <h1>📊 REPORTE DE EQUIPOS DE CÓMPUTO</h1>
        <p>Sistema de Actas - Documentación Administrativa</p>
    </div>

    {{-- Filtros Aplicados --}}
    <div class="filtros-aplicados">
        <h3>🔍 Filtros Aplicados</h3>
        @if($filtros['fecha_inicio'])
            <p><strong>Fecha Inicio:</strong> {{ \Carbon\Carbon::parse($filtros['fecha_inicio'])->format('d/m/Y') }}</p>
        @endif
        @if($filtros['fecha_fin'])
            <p><strong>Fecha Fin:</strong> {{ \Carbon\Carbon::parse($filtros['fecha_fin'])->format('d/m/Y') }}</p>
        @endif
        @if($filtros['establecimiento'])
            <p><strong>Establecimiento:</strong> {{ $filtros['establecimiento']->nombre }}</p>
        @endif
        @if($filtros['provincia'])
            <p><strong>Provincia:</strong> {{ $filtros['provincia'] }}</p>
        @endif
        @if($filtros['modulo'])
            <p><strong>Módulo:</strong> {{ $filtros['modulo'] }}</p>
        @endif
        @if($filtros['tipo'])
            <p><strong>Tipo:</strong> {{ $filtros['tipo'] }}</p>
        @endif
        <p><strong>Generado por:</strong> {{ $usuarioLogeado->name }}</p>
        <p><strong>Fecha de generación:</strong> {{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}</p>
    </div>

    {{-- Total de Equipos --}}
    <div class="total-equipos">
        <h2>Total de Equipos: {{ $equipos->count() }}</h2>
    </div>

    {{-- Tabla de Equipos --}}
    @if($equipos->count() > 0)
        <table>
            <thead>
                <tr>
                    <th style="width: 8%;">Fecha</th>
                    <th style="width: 8%;">IPRESS</th>
                    <th style="width: 18%;">Establecimiento</th>
                    <th style="width: 7%;">Categoría</th>
                    <th style="width: 10%;">Tipo</th>
                    <th style="width: 18%;">Módulo</th>
                    <th style="width: 6%;">Cant.</th>
                    <th style="width: 12%;">Descripción</th>
                    <th style="width: 6%;">Propio</th>
                    <th style="width: 7%;">Estado</th>
                </tr>
            </thead>
            <tbody>
                @foreach($equipos as $equipo)
                    <tr>
                        <td>{{ $equipo->cabecera->fecha ? \Carbon\Carbon::parse($equipo->cabecera->fecha)->format('d/m/Y') : 'N/A' }}
                        </td>
                        <td style="font-family: 'Courier New', monospace; text-align: center;">
                            {{ $equipo->cabecera->establecimiento->codigo ?? 'N/A' }}
                        </td>
                        <td>{{ $equipo->cabecera->establecimiento->nombre ?? 'N/A' }}</td>
                        <td style="text-align: center;">{{ $equipo->cabecera->establecimiento->categoria ?? 'N/A' }}</td>
                        <td style="font-weight: bold; text-align: center;">
                            {{ ModuloHelper::getTipoEstablecimiento($equipo->cabecera->establecimiento) }}
                        </td>
                        <td>{{ ModuloHelper::getNombreAmigable($equipo->modulo) ?? 'N/A' }}</td>
                        <td style="text-align: center; font-weight: bold;">{{ $equipo->cantidad ?? 0 }}</td>
                        <td>{{ $equipo->descripcion ?? 'N/A' }}</td>
                        <td>{{ $equipo->propio ?? 'N/A' }}</td>
                        <td>
                            <span
                                class="estado-badge {{ $equipo->estado == 'Operativo' ? 'estado-operativo' : ($equipo->estado == 'Inoperativo' ? 'estado-inoperativo' : 'estado-otro') }}">
                                {{ $equipo->estado ?? 'N/A' }}
                            </span>
                        </td>
                        <td style="font-family: 'Courier New', monospace;">{{ $equipo->nro_serie ?? 'N/A' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Información adicional si hay observaciones --}}
        @php
            $equiposConObservacion = $equipos->filter(function ($e) {
                return !empty($e->observacion);
            });
        @endphp

        @if($equiposConObservacion->count() > 0)
            <div style="margin-top: 20px; page-break-inside: avoid;">
                <h3 style="font-size: 10pt; color: #6366f1; margin-bottom: 10px; font-weight: bold;">📝 Observaciones</h3>
                @foreach($equiposConObservacion as $equipo)
                    <div
                        style="background: #fef3c7; padding: 8px; margin-bottom: 8px; border-radius: 6px; border-left: 3px solid #f59e0b;">
                        <p style="font-size: 8pt; margin-bottom: 2px;"><strong>{{ $equipo->descripcion }}</strong>
                            ({{ $equipo->modulo }})</p>
                        <p style="font-size: 7pt; color: #78350f;">{{ $equipo->observacion }}</p>
                    </div>
                @endforeach
            </div>
        @endif
    @else
        <div class="no-data">
            <p>No se encontraron equipos con los filtros aplicados</p>
        </div>
    @endif

    {{-- Footer con información del sistema --}}
    <div class="footer-info">
        <p><strong>Sistema de Actas</strong> - Reporte generado automáticamente</p>
        <p>Este documento contiene información oficial del sistema de gestión de actas</p>
    </div>
</body>

</html>