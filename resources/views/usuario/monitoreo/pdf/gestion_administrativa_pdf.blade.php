<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Gestión Administrativa - Acta {{ $acta->id }}</title>
    <style>
        @page { margin: 1.2cm 1.5cm; }
        body { font-family: 'Helvetica', sans-serif; font-size: 10px; color: #1e293b; line-height: 1.4; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #4f46e5; padding-bottom: 10px; }
        .header h1 { margin: 0; font-size: 15px; text-transform: uppercase; color: #4f46e5; }
        .section-title { background-color: #f1f5f9; padding: 6px 10px; font-weight: bold; text-transform: uppercase; border-left: 4px solid #4f46e5; margin-top: 15px; margin-bottom: 5px; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; table-layout: fixed; margin-bottom: 5px; }
        th, td { border: 1px solid #e2e8f0; padding: 6px 8px; text-align: left; vertical-align: middle; word-wrap: break-word; }
        th { background-color: #f8fafc; color: #475569; font-size: 8.5px; text-transform: uppercase; }
        .bg-label { background-color: #f8fafc; font-weight: bold; width: 30%; }
        .uppercase { text-transform: uppercase; }
        .text-center { text-align: center; }
    </style>
</head>
<body>

    <div class="header">
        <h1>Módulo 01: Gestión Administrativa</h1>
        <div style="font-weight: bold; color: #64748b; font-size: 10px;">
            ACTA N° {{ str_pad($acta->id, 5, '0', STR_PAD_LEFT) }} | 
            E.E.S.S.: {{ strtoupper($acta->establecimiento->nombre) }}
        </div>
    </div>

    <div class="section-title">1. Responsable de Recursos Humanos</div>
    <table>
        <tr>
            <td class="bg-label">Apellidos y Nombres:</td>
            <td class="uppercase">
                {{ $detalle->contenido['rrhh']['apellido_paterno'] ?? '' }} 
                {{ $detalle->contenido['rrhh']['apellido_materno'] ?? '' }} 
                {{ $detalle->contenido['rrhh']['nombres'] ?? '' }}
            </td>
        </tr>
        <tr>
            <td class="bg-label">DNI / Documento:</td>
            <td>{{ $detalle->contenido['rrhh']['doc'] ?? '---' }}</td>
        </tr>
    </table>

    <div class="section-title">2. Acceso y Programación SIHCE</div>
    <table>
        <tr>
            <td class="bg-label">¿Cuenta con Usuario SIHCE?</td>
            <td>{{ $detalle->contenido['cuenta_sihce'] ?? 'NO' }}</td>
        </tr>
        <tr>
            <td class="bg-label">Programación Actual hasta:</td>
            <td class="uppercase">{{ $detalle->contenido['programacion_mes'] ?? '---' }} {{ $detalle->contenido['programacion_anio'] ?? '' }}</td>
        </tr>
    </table>

    <div class="section-title">4. Inventario de Equipamiento Tecnológico</div>
    <table>
        <thead>
            <tr>
                <th width="30%">Descripción</th>
                <th width="20%">N° Serie</th>
                <th width="10%" class="text-center">Cant.</th>
                <th width="15%">Estado</th>
                <th width="25%">Observaciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($equipos as $eq)
            <tr class="uppercase">
                <td>{{ $eq->descripcion }}</td>
                <td>{{ $eq->nro_serie ?? '---' }}</td>
                <td class="text-center">{{ $eq->cantidad }}</td>
                <td>{{ $eq->estado }}</td>
                <td style="font-size: 8px;">{{ $eq->observacion ?? '---' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center" style="color: #94a3b8; font-style: italic;">No se registraron equipos en este módulo.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="section-title">5. Observaciones Generales</div>
    <div style="border: 1px solid #e2e8f0; padding: 10px; min-height: 40px;" class="uppercase">
        {{ $detalle->contenido['comentarios'] ?? 'SIN OBSERVACIONES ADICIONALES.' }}
    </div>

    <div style="position: fixed; bottom: -10px; width: 100%; text-align: right; font-size: 8px; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 5px;">
        Generado por Sistema de Monitoreo | Fecha: {{ date('d/m/Y H:i:s') }}
    </div>

</body>
</html>