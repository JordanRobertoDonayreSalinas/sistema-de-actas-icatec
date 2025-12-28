<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Evaluación de Programación - {{ $acta->establecimiento->nombre }}</title>
    <style>
        @page { margin: 1.5cm; }
        body { font-family: 'Helvetica', sans-serif; font-size: 10px; color: #1e293b; line-height: 1.5; }
        .header { width: 100%; border-bottom: 2px solid #0f172a; padding-bottom: 10px; margin-bottom: 20px; }
        .title { font-size: 14px; font-weight: bold; text-transform: uppercase; color: #0f172a; }
        .section-title { background: #f1f5f9; padding: 6px 10px; font-weight: bold; border-left: 4px solid #4f46e5; margin: 15px 0 10px; text-transform: uppercase; font-size: 11px; }
        .table-info { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        .table-info td { border: 1px solid #e2e8f0; padding: 8px; vertical-align: top; }
        .label { font-weight: bold; font-size: 8px; color: #64748b; text-transform: uppercase; display: block; margin-bottom: 2px; }
        .value { font-size: 10px; font-weight: bold; color: #0f172a; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 4px; font-size: 9px; font-weight: bold; color: white; }
        .badge-si { background-color: #10b981; }
        .badge-no { background-color: #ef4444; }
        .footer { margin-top: 50px; width: 100%; }
        .box-firma { text-align: center; width: 50%; }
        .linea { border-top: 1px solid #0f172a; width: 200px; margin: 0 auto 5px; }
    </style>
</head>
<body>

    <table class="header">
        <tr>
            <td>
                <div class="title">Evaluación de Programación de Consultorios</div>
                <div style="color: #6366f1; font-weight: bold; font-size: 9px;">Módulo de Gestión Administrativa - Auditoría Técnica</div>
            </td>
            <td style="text-align: right;">
                <span class="label">Fecha de Monitoreo:</span>
                <span class="value">{{ \Carbon\Carbon::parse($acta->fecha)->format('d/m/Y') }}</span>
            </td>
        </tr>
    </table>

    <div class="section-title">I. Datos del Establecimiento</div>
    <table class="table-info">
        <tr>
            <td width="50%"><span class="label">Establecimiento:</span><span class="value">{{ $acta->establecimiento->nombre }}</span></td>
            <td width="50%"><span class="label">Ubicación:</span><span class="value">{{ $acta->establecimiento->distrito }} - {{ $acta->establecimiento->provincia }}</span></td>
        </tr>
        <tr>
            <td><span class="label">Implementador Responsable:</span><span class="value">{{ $acta->implementador }}</span></td>
            <td><span class="label">Responsable del EESS:</span><span class="value">{{ $acta->responsable }}</span></td>
        </tr>
    </table>

    @if($acta->programacion)
        <div class="section-title">II. Información del Personal Responsable (RRHH)</div>
        <table class="table-info">
            <tr>
                <td colspan="2"><span class="label">Apellidos y Nombres:</span><span class="value">{{ $acta->programacion->rrhh_nombre }}</span></td>
                <td><span class="label">DNI:</span><span class="value">{{ $acta->programacion->rrhh_dni }}</span></td>
            </tr>
            <tr>
                <td width="33%"><span class="label">Teléfono:</span><span class="value">{{ $acta->programacion->rrhh_telefono ?? '---' }}</span></td>
                <td width="34%"><span class="label">Correo Electrónico:</span><span class="value">{{ $acta->programacion->rrhh_correo ?? '---' }}</span></td>
                <td width="33%">
                    <span class="label">¿Cuenta con Usuario ODOO?</span>
                    <span class="badge {{ $acta->programacion->odoo == 'SI' ? 'badge-si' : 'badge-no' }}">
                        {{ $acta->programacion->odoo }}
                    </span>
                </td>
            </tr>
        </table>

        @if($acta->programacion->odoo == 'NO')
            <div style="background-color: #fef2f2; border: 1px solid #fee2e2; padding: 10px; margin-top: 10px; border-radius: 8px;">
                <p style="margin: 0 0 10px 0; font-weight: bold; color: #991b1b; font-size: 9px; text-transform: uppercase;">Detalle: Quién realiza la programación ante falta de acceso</p>
                <table class="table-info" style="margin-bottom: 0;">
                    <tr>
                        <td width="50%"><span class="label">Nombres:</span><span class="value">{{ $acta->programacion->quien_programa_nombre }}</span></td>
                        <td width="25%"><span class="label">DNI:</span><span class="value">{{ $acta->programacion->quien_programa_dni }}</span></td>
                        <td width="25%"><span class="label">Teléfono:</span><span class="value">{{ $acta->programacion->quien_programa_telefono }}</span></td>
                    </tr>
                </table>
            </div>
        @endif

        <div class="section-title">III. Capacitación y Estado del Sistema</div>
        <table class="table-info">
            <tr>
                <td width="50%">
                    <span class="label">¿Capacitación en Gestión Administrativa?</span>
                    <span class="value">{{ $acta->programacion->capacitacion }}</span>
                </td>
                <td width="50%">
                    <span class="label">Mes de Programación en el Sistema:</span>
                    <span class="value">{{ $acta->programacion->mes_sistema ? \Carbon\Carbon::parse($acta->programacion->mes_sistema)->format('F Y') : 'NO REGISTRA' }}</span>
                </td>
            </tr>
        </table>

        <div class="section-title">IV. Hallazgos y Comentarios</div>
        <div style="border: 1px solid #e2e8f0; padding: 15px; min-height: 80px; border-radius: 8px; background: #fafafa;">
            <span class="label" style="margin-bottom: 10px;">Observaciones del entrevistado:</span>
            <div style="font-size: 10px; color: #334155;">{{ $acta->programacion->comentarios ?? 'Sin comentarios registrados.' }}</div>
        </div>

        <div class="footer">
            <table width="100%">
                <tr>
                    <td class="box-firma">
                        <div class="linea"></div>
                        <div class="value">{{ $acta->implementador }}</div>
                        <div class="label">Firma del Monitor / Implementador</div>
                    </td>
                    <td class="box-firma">
                        <div class="linea"></div>
                        <div class="value">{{ $acta->programacion->rrhh_nombre }}</div>
                        <div class="label">Firma del Responsable Entrevistado</div>
                    </td>
                </tr>
            </table>
        </div>
    @else
        <div style="text-align: center; padding: 50px; color: #ef4444; font-weight: bold;">
            ADVERTENCIA: NO EXISTEN DATOS REGISTRADOS PARA ESTE MÓDULO.
        </div>
    @endif

</body>
</html>