<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Gestión Administrativa - Acta {{ $acta->id }}</title>
    <style>
        /* Configuraciones de página */
        @page {
            margin: 1.5cm;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 11px;
            color: #1e293b;
            line-height: 1.4;
        }
        
        /* Encabezado */
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #4f46e5;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 16px;
            text-transform: uppercase;
            color: #4f46e5;
        }
        .header p {
            margin: 2px 0;
            font-weight: bold;
            color: #64748b;
        }

        /* Secciones */
        .section-title {
            background-color: #f1f5f9;
            padding: 6px 10px;
            font-weight: bold;
            text-transform: uppercase;
            border-left: 4px solid #4f46e5;
            margin-top: 15px;
            font-size: 12px;
        }

        /* Tablas */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }
        th, td {
            border: 1px solid #e2e8f0;
            padding: 7px 10px;
            text-align: left;
        }
        th {
            background-color: #f8fafc;
            color: #475569;
            font-size: 10px;
            text-transform: uppercase;
        }

        /* Utilidades */
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .mt-4 { margin-top: 1rem; }
        
        /* Footer */
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

        /* Evidencia fotográfica */
        .photo-container {
            margin-top: 20px;
            text-align: center;
        }
        .photo-frame {
            border: 1px solid #e2e8f0;
            padding: 10px;
            display: inline-block;
            background: #fff;
        }
        .photo {
            max-width: 400px;
            max-height: 300px;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>Módulo 01: Gestión Administrativa</h1>
        <p>Acta de Monitoreo N° {{ str_pad($acta->id, 5, '0', STR_PAD_LEFT) }}</p>
        <p>Establecimiento: {{ $acta->establecimiento->nombre }}</p>
    </div>

    <div class="section-title">1. Personal Responsable de RR.HH.</div>
    <table>
        <tr>
            <th width="25%">Nombre Completo:</th>
            <td width="40%">{{ $detalle->contenido['rrhh']['nombres'] ?? '' }} {{ $detalle->contenido['rrhh']['apellido_paterno'] ?? '' }} {{ $detalle->contenido['rrhh']['apellido_materno'] ?? '' }}</td>
            <th width="15%">DNI / DOC:</th>
            <td width="20%">{{ $detalle->contenido['rrhh']['doc'] ?? 'No registrado' }}</td>
        </tr>
        <tr>
            <th>Email Corporativo:</th>
            <td>{{ $detalle->contenido['rrhh']['email'] ?? 'N/A' }}</td>
            <th>Teléfono:</th>
            <td>{{ $detalle->contenido['rrhh']['telefono'] ?? 'N/A' }}</td>
        </tr>
    </table>

    <div class="section-title">2. Acceso y Programación SIHCE</div>
    <table>
        <tr>
            <th>Cuenta con Usuario SIHCE:</th>
            <td>{{ $detalle->contenido['cuenta_sihce'] ?? 'NO' }}</td>
            <th>Programación actual hasta:</th>
            <td>{{ $detalle->contenido['programacion_mes'] ?? '' }} - {{ $detalle->contenido['programacion_anio'] ?? '' }}</td>
        </tr>
    </table>

    @if(($detalle->contenido['cuenta_sihce'] ?? '') == 'NO')
    <p class="font-bold" style="margin-top: 10px; color: #b45309;">* Personal encargado de programar turnos:</p>
    <table>
        <tr>
            <th width="25%">Responsable:</th>
            <td>{{ $detalle->contenido['programador']['nombres'] ?? '' }} {{ $detalle->contenido['programador']['apellido_paterno'] ?? '' }}</td>
            <th width="15%">DNI:</th>
            <td>{{ $detalle->contenido['programador']['doc'] ?? '' }}</td>
        </tr>
    </table>
    @endif

    <div class="section-title">3. Capacitación y Comunicación</div>
    <table>
        <tr>
            <th width="25%">¿Recibió Capacitación?</th>
            <td>{{ $detalle->contenido['recibio_capacitacion'] ?? 'NO' }}</td>
            <th width="25%">Entidad Capacitadora:</th>
            <td>{{ $detalle->contenido['inst_que_lo_capacito'] ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th>Informa dificultades a:</th>
            <td>{{ $detalle->contenido['inst_a_quien_comunica'] ?? 'N/A' }}</td>
            <th>Medio utilizado:</th>
            <td>{{ $detalle->contenido['medio_que_utiliza'] ?? 'N/A' }}</td>
        </tr>
    </table>

    <div class="section-title">4. Inventario de Equipamiento</div>
    <table>
        <thead>
            <tr>
                <th>Descripción del Hardware</th>
                <th class="text-center">Cant.</th>
                <th>Estado</th>
                <th>Propiedad</th>
            </tr>
        </thead>
        <tbody>
            @forelse($equipos as $eq)
            <tr>
                <td>{{ $eq->descripcion }}</td>
                <td class="text-center">{{ $eq->cantidad }}</td>
                <td>{{ $eq->estado }}</td>
                <td>{{ $eq->propio == 1 ? 'INSTITUCIONAL' : 'PERSONAL' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="text-center">No se registraron equipos en este módulo.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="section-title">5. Observaciones y Comentarios</div>
    <div style="border: 1px solid #e2e8f0; padding: 10px; min-height: 60px; margin-top: 8px;">
        {{ $detalle->contenido['comentarios'] ?? 'Sin observaciones adicionales.' }}
    </div>

    @if(isset($detalle->contenido['foto_evidencia']))
    <div class="photo-container">
        <div class="section-title">6. Evidencia Fotográfica</div>
        <div class="photo-frame mt-4">
            {{-- Usamos path absoluto para DomPDF --}}
            <img src="{{ public_path('storage/' . $detalle->contenido['foto_evidencia']) }}" class="photo">
        </div>
    </div>
    @endif

    <div class="footer">
        Generado por: {{ Auth::user()->name }} | Fecha y Hora: {{ date('d/m/Y H:i:s') }}
    </div>

</body>
</html>