<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nueva Incidencia</title>
    <style>
        body { margin: 0; padding: 0; background: #f1f5f9; font-family: 'Segoe UI', Arial, sans-serif; }
        .wrapper { max-width: 600px; margin: 32px auto; background: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
        .header { background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); padding: 32px 40px; text-align: center; }
        .header h1 { margin: 0; color: #fff; font-size: 20px; font-weight: 700; }
        .header p { margin: 5px 0 0; color: rgba(255,255,255,0.6); font-size: 12px; }
        .alert-banner { background: #fef2f2; border-bottom: 3px solid #ef4444; padding: 14px 40px; display: flex; align-items: center; gap: 10px; }
        .alert-banner .icon { font-size: 20px; }
        .alert-banner p { margin: 0; font-size: 13px; font-weight: 700; color: #991b1b; }
        .body { padding: 32px 40px; }
        .ticket-ref { font-size: 13px; color: #64748b; margin-bottom: 24px; }
        .ticket-ref strong { color: #ea580c; font-size: 18px; }
        .section-title { font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: .07em; margin: 20px 0 10px; padding-bottom: 6px; border-bottom: 1px solid #f1f5f9; }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 8px; }
        .info-item { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px 14px; }
        .info-item .key { font-size: 10px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: .06em; display: block; margin-bottom: 2px; }
        .info-item .val { font-size: 13px; font-weight: 600; color: #334155; }
        .observacion { background: #fffbeb; border: 1px solid #fde68a; border-radius: 8px; padding: 14px 16px; font-size: 13px; color: #78350f; line-height: 1.65; margin-top: 8px; }
        .cta { margin-top: 28px; text-align: center; }
        .cta a { display: inline-block; background: #ea580c; color: #fff; font-size: 14px; font-weight: 700; padding: 12px 32px; border-radius: 10px; text-decoration: none; }
        .footer { background: #f8fafc; border-top: 1px solid #e2e8f0; padding: 18px 40px; text-align: center; }
        .footer p { margin: 0; font-size: 11px; color: #94a3b8; line-height: 1.7; }
        @media (max-width: 520px) {
            .body { padding: 20px 18px; }
            .info-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="header">
        <h1>🚨 Nueva Incidencia Pendiente</h1>
        <p>Mesa de Ayuda – SIHCE · ICATEc</p>
    </div>

    <div class="alert-banner">
        <span class="icon">⚠️</span>
        <p>Se ha registrado una nueva incidencia que requiere atención.</p>
    </div>

    <div class="body">
        <div class="ticket-ref">
            Ticket: <strong>#{{ $incidencia->id }}</strong>
            &nbsp;·&nbsp; {{ $incidencia->created_at->format('d/m/Y H:i') }} h
        </div>

        <p class="section-title">Datos del profesional</p>
        <div class="info-grid">
            <div class="info-item">
                <span class="key">Apellidos y Nombres</span>
                <span class="val">{{ $incidencia->apellidos }}, {{ $incidencia->nombres }}</span>
            </div>
            <div class="info-item">
                <span class="key">DNI</span>
                <span class="val">{{ $incidencia->dni }}</span>
            </div>
            <div class="info-item">
                <span class="key">Celular</span>
                <span class="val">{{ $incidencia->celular }}</span>
            </div>
            <div class="info-item">
                <span class="key">Correo</span>
                <span class="val">{{ $incidencia->correo }}</span>
            </div>
        </div>

        <p class="section-title">Establecimiento</p>
        <div class="info-grid">
            <div class="info-item" style="grid-column: span 2;">
                <span class="key">Nombre del establecimiento</span>
                <span class="val">{{ $incidencia->nombre_establecimiento }}</span>
            </div>
            <div class="info-item">
                <span class="key">Código IPRESS / Categoría</span>
                <span class="val">{{ $incidencia->codigo_ipress }} · {{ $incidencia->categoria }}</span>
            </div>
            <div class="info-item">
                <span class="key">Provincia / Distrito</span>
                <span class="val">{{ $incidencia->provincia_establecimiento }} / {{ $incidencia->distrito_establecimiento }}</span>
            </div>
            <div class="info-item">
                <span class="key">Red</span>
                <span class="val">{{ $incidencia->red }}</span>
            </div>
            <div class="info-item">
                <span class="key">Microred</span>
                <span class="val">{{ $incidencia->microred }}</span>
            </div>
        </div>

        <p class="section-title">Problema reportado</p>
        <div class="info-grid">
            <div class="info-item" style="grid-column: span 2;">
                <span class="key">Módulo SIHCE afectado</span>
                <span class="val">{{ str_replace('_', ' ', ucwords($incidencia->modulos, '_')) }}</span>
            </div>
        </div>
        <div class="observacion">
            <strong>Descripción del problema:</strong><br><br>
            {{ $incidencia->observacion }}
        </div>

        <div class="cta">
            <a href="{{ url('/usuario/incidencias/' . $incidencia->id . '/responder') }}">
                Abrir incidencia en el sistema →
            </a>
        </div>
    </div>

    <div class="footer">
        <p>Notificación automática del sistema · No responder a este correo<br>
        © {{ date('Y') }} ICATEc – Mesa de Ayuda SIHCE
        </p>
    </div>
</div>
</body>
</html>
