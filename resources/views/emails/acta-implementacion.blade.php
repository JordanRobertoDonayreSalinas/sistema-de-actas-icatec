<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acta de Implementación Firmada</title>
    <style>
        /* Fonts - Using system fonts first for perf, then Google Fonts fallback */
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

        body { margin: 0; padding: 0; width: 100% !important; height: 100% !important; background-color: #f3f4f6; -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; }
        div[style*="margin: 16px 0"] { margin: 0 !important; }
        table { border-collapse: collapse !important; mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
        img { border: 0; height: auto; line-height: 100%; outline: none; text-decoration: none; -ms-interpolation-mode: bicubic; }
        p { display: block; margin: 13px 0; }

        .container { filter: drop-shadow(0 10px 15px rgba(0, 0, 0, 0.05)); }
        
        @media only screen and (max-width: 620px) {
            .wrapper { width: 100% !important; border-radius: 0 !important; margin-top: 0 !important; margin-bottom: 0 !important; }
            .header { padding: 40px 24px !important; border-radius: 0 !important; }
            .body { padding: 32px 24px !important; }
            .card { padding: 24px !important; }
        }
    </style>
</head>
<body style="margin: 0; padding: 0; background-color: #f8fafc;">
    <center>
        <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f8fafc;">
            <tr>
                <td align="center" style="padding: 40px 10px;">
                    <!-- Wrapper Container -->
                    <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="600" class="wrapper" style="background-color: #ffffff; border-radius: 24px; overflow: hidden; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);">
                        <!-- Header Section -->
                        <tr>
                            <td align="center" style="background: linear-gradient(135deg, #7c3aed 0%, #4338ca 100%); padding: 56px 40px;" class="header">
                                <div style="width: 48px; height: 48px; background: rgba(255,255,255,0.15); border-radius: 12px; margin-bottom: 24px; display: inline-block; vertical-align: middle; line-height: 48px; font-size: 24px;">📄</div>
                                <h1 style="margin: 0; color: #ffffff; font-size: 26px; font-weight: 800; letter-spacing: -0.02em; line-height: 1.2;">Acta de Implementación<br>Firmada</h1>
                                <p style="margin: 12px 0 0; color: rgba(255, 255, 255, 0.9); font-size: 15px; font-weight: 500; letter-spacing: 0.03em; text-transform: uppercase;">MÓDULO: {{ $moduloNombre }}</p>
                            </td>
                        </tr>

                        <!-- Body Content -->
                        <tr>
                            <td style="padding: 48px; background-color: #ffffff;" class="body">
                                <h2 style="margin: 0 0 12px; color: #1e293b; font-size: 18px; font-weight: 700;">Hola,</h2>
                                <p style="margin: 0 0 32px; color: #64748b; font-size: 15px; line-height: 1.7; font-weight: 400;">
                                    Se ha completado el proceso de registro y firma del acta correspondiente a la implementación realizada. El documento oficial ha sido validado satisfactoriamente.
                                </p>

                                <!-- Summary Card -->
                                <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" class="card" style="background-color: #f8fafc; border-radius: 20px; border: 1px solid #f1f5f9; padding: 32px;">
                                    <tr>
                                        <td>
                                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                                                <!-- ID -->
                                                <tr>
                                                    <td style="padding-bottom: 20px; border-bottom: 1px solid rgba(0,0,0,0.04);">
                                                        <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                                                            <tr>
                                                                <td style="font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.1em;">🆔 Identificador</td>
                                                                <td align="right" style="font-size: 15px; font-weight: 700; color: #1e293b;">#{{ $acta->id }}</td>
                                                            </tr>
                                                        </table>
                                                    </td>
                                                </tr>
                                                <!-- Fecha -->
                                                <tr>
                                                    <td style="padding: 20px 0; border-bottom: 1px solid rgba(0,0,0,0.04);">
                                                        <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                                                            <tr>
                                                                <td style="font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.1em;">📅 Fecha de Emisión</td>
                                                                <td align="right" style="font-size: 15px; font-weight: 600; color: #475569;">{{ \Carbon\Carbon::parse($acta->fecha)->format('d/m/Y') }}</td>
                                                            </tr>
                                                        </table>
                                                    </td>
                                                </tr>
                                                <!-- Establecimiento -->
                                                <tr>
                                                    <td style="padding: 20px 0; border-bottom: 1px solid rgba(0,0,0,0.04);">
                                                        <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                                                            <tr>
                                                                <td style="font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.1em;">🏥 Establecimiento</td>
                                                                <td align="right" style="font-size: 15px; font-weight: 600; color: #475569;">{{ $acta->nombre_establecimiento }}</td>
                                                            </tr>
                                                        </table>
                                                    </td>
                                                </tr>
                                                <!-- Ubicación -->
                                                <tr>
                                                    <td style="padding-top: 20px;">
                                                        <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                                                            <tr>
                                                                <td style="font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.1em;">📍 Ubicación</td>
                                                                <td align="right" style="font-size: 15px; font-weight: 600; color: #475569;">{{ $acta->distrito }} – {{ $acta->provincia }}</td>
                                                            </tr>
                                                        </table>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>

                                <!-- Attachment Notice -->
                                <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-top: 32px;">
                                    <tr>
                                        <td align="center" style="background-color: #f1f5f9; border-radius: 12px; padding: 16px;">
                                            <p style="margin: 0; color: #64748b; font-size: 13px; font-weight: 500;">
                                                <span style="font-size: 18px; vertical-align: middle; margin-right: 8px;">🖇️</span>
                                                El acta firmada se encuentra adjunta para su revisión y archivo oficial.
                                            </p>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>

                        <!-- Footer -->
                        <tr>
                            <td style="padding: 32px 48px; background-color: #f8fafc; border-top: 1px solid #f1f5f9; text-align: center;">
                                <p style="margin: 0 0 12px; font-size: 12px; color: #94a3b8; line-height: 1.6;">
                                    Este es un mensaje automático del sistema <strong>HERRAMIENTAS DE IMPLEMENTACION SIHCE</strong>.<br>
                                    Cualquier modificación al documento adjunto invalidará la firma digital.
                                </p>
                                <p style="margin: 0; font-size: 11px; font-weight: 600; color: #7c3aed;">
                                    © {{ date('Y') }} ICATEC • Todas las firmas son válidas digitalmente.
                                </p>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </center>
</body>
</html>
