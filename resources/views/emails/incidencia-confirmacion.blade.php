<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmación de Incidencia</title>
    <style>
        body { margin: 0; padding: 0; background: #f1f5f9; font-family: 'Segoe UI', Arial, sans-serif; }
        .wrapper { max-width: 600px; margin: 32px auto; background: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
        .header { background: linear-gradient(135deg, #ea580c 0%, #dc2626 100%); padding: 36px 40px; text-align: center; }
        .header h1 { margin: 0; color: #fff; font-size: 22px; font-weight: 700; letter-spacing: -0.3px; }
        .header p { margin: 6px 0 0; color: rgba(255,255,255,0.85); font-size: 13px; }
        .badge { display: inline-block; background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.35); color: #fff; font-size: 12px; font-weight: 700; padding: 4px 14px; border-radius: 99px; margin-top: 12px; letter-spacing: .05em; }
        .body { padding: 36px 40px; }
        .ticket-box { background: #fff7ed; border: 2px solid #fed7aa; border-radius: 12px; padding: 20px 24px; margin-bottom: 28px; text-align: center; }
        .ticket-box .label { font-size: 11px; font-weight: 700; color: #92400e; text-transform: uppercase; letter-spacing: .08em; margin-bottom: 6px; }
        .ticket-box .number { font-size: 36px; font-weight: 800; color: #ea580c; line-height: 1; }
        .ticket-box .sub { font-size: 11px; color: #b45309; margin-top: 4px; }
        .section-title { font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: .07em; margin-bottom: 12px; }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 24px; }
        .info-item { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 12px 16px; }
        .info-item .key { font-size: 10px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: .06em; display: block; margin-bottom: 3px; }
        .info-item .val { font-size: 13px; font-weight: 600; color: #334155; }
        .observacion { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 16px; font-size: 13px; color: #475569; line-height: 1.65; margin-bottom: 24px; }
        .estado-badge { display: inline-flex; align-items: center; gap: 6px; background: #fef9c3; border: 1px solid #fde047; color: #854d0e; font-size: 12px; font-weight: 700; padding: 4px 12px; border-radius: 99px; }
        .notice { background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 10px; padding: 14px 18px; font-size: 12px; color: #166534; margin-top: 24px; line-height: 1.6; }
        .footer { background: #f8fafc; border-top: 1px solid #e2e8f0; padding: 20px 40px; text-align: center; }
        .footer p { margin: 0; font-size: 11px; color: #94a3b8; line-height: 1.7; }
        .footer a { color: #ea580c; text-decoration: none; font-weight: 600; }
        @media (max-width: 520px) {
            .body { padding: 24px 20px; }
            .info-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="header">
        <h1>Mesa de Ayuda – SIHCE</h1>
        <p>Sistema de Implementación ICATEc</p>
        <div class="badge">✅ Reporte recibido correctamente</div>
    </div>

    <div class="body">
        <p style="font-size:15px;font-weight:700;color:#1e293b;margin:0 0 6px;">
            Hola, {{ $incidencia->nombres }} {{ $incidencia->apellidos }}
        </p>
        <p style="font-size:13px;color:#64748b;margin:0 0 24px;">
            Tu reporte de incidencia técnica ha sido registrado con éxito. El equipo de implementación lo atenderá a la brevedad.
        </p>

        <div class="ticket-box">
            <div class="label">N° de Ticket asignado</div>
            <div class="number">#{{ $incidencia->id }}</div>
            <div class="sub">Fecha: {{ $incidencia->created_at->format('d/m/Y H:i') }} h</div>
        </div>

        <p class="section-title">Datos del establecimiento</p>
        <div class="info-grid">
            <div class="info-item" style="grid-column: span 2;">
                <span class="key">Establecimiento</span>
                <span class="val">{{ $incidencia->nombre_establecimiento }}</span>
            </div>
            <div class="info-item">
                <span class="key">Código IPRESS</span>
                <span class="val">{{ $incidencia->codigo_ipress }}</span>
            </div>
            <div class="info-item">
                <span class="key">Categoría</span>
                <span class="val">{{ $incidencia->categoria }}</span>
            </div>
            <div class="info-item">
                <span class="key">Distrito</span>
                <span class="val">{{ $incidencia->distrito_establecimiento }}</span>
            </div>
            <div class="info-item">
                <span class="key">Provincia</span>
                <span class="val">{{ $incidencia->provincia_establecimiento }}</span>
            </div>
        </div>

        <p class="section-title">Detalle de la incidencia</p>
        <div class="info-grid">
            <div class="info-item" style="grid-column: span 2;">
                <span class="key">Módulo SIHCE afectado</span>
                <span class="val">{{ str_replace('_', ' ', ucwords($incidencia->modulos, '_')) }}</span>
            </div>
        </div>
        <div class="observacion">
            <strong style="color:#334155;">Descripción:</strong><br>
            {{ $incidencia->observacion }}
        </div>

        <div style="margin-bottom: 16px;">
            <span class="estado-badge">
                <span style="width:8px;height:8px;border-radius:50%;background:#eab308;display:inline-block;"></span>
                Estado: Pendiente
            </span>
        </div>

        <div class="notice">
            📌 <strong>Guarda tu N° de ticket:</strong> puedes usarlo para hacer seguimiento de tu reporte con el equipo técnico.<br>
            Si tienes alguna consulta adicional, comunícate con el implementador responsable de tu zona.
        </div>
    </div>

    <div class="footer">
        <p>Correo enviado automáticamente por el sistema.<br>
        <a href="mailto:{{ config('mail.from.address') }}">{{ config('mail.from.address') }}</a><br>
        © {{ date('Y') }} ICATEc – Sistema de Implementación SIHCE
        </p>
    </div>
</div>
</body>
</html>
