<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Compromiso de Confidencialidad</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11pt; line-height: 1.5; color: #000; margin: 2cm 2cm; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 1px solid #000; padding-bottom: 10px; }
        .header h2 { margin: 0; font-size: 14pt; text-transform: uppercase; }
        .header p { margin: 0; font-size: 10pt; }
        .titulo { text-align: center; font-weight: bold; text-transform: uppercase; margin: 20px 0; font-size: 12pt; text-decoration: underline; }
        .contenido { text-align: justify; margin-bottom: 15px; }
        .datos-tabla { width: 100%; border-collapse: collapse; margin: 20px 0; font-size: 10pt; }
        .datos-tabla td { padding: 5px; vertical-align: top; }
        .datos-label { font-weight: bold; width: 180px; }
        .firmas { margin-top: 80px; width: 100%; }
        .firma-box { width: 45%; float: left; text-align: center; border-top: 1px solid #000; padding-top: 5px; margin-right: 5%; }
        .footer { position: fixed; bottom: 0; left: 0; right: 0; font-size: 8pt; text-align: center; color: #555; border-top: 1px solid #ccc; padding-top: 5px; }
        ul { margin-top: 5px; margin-bottom: 5px; }
        li { margin-bottom: 5px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Ministerio de Salud</h2>
        <p>Dirección Regional de Salud Ica</p>
        <p>Oficina de Tecnologías de la Información</p>
    </div>

    <div class="titulo">COMPROMISO DE CONFIDENCIALIDAD SIHCE</div>

    <div class="contenido">
        <p>Yo, identificado(a) con los datos consignados a continuación, en mi calidad de usuario autorizado para acceder a los sistemas de información de la institución:</p>
    </div>

    <table class="datos-tabla">
        <tr>
            <td class="datos-label">Apellidos y Nombres:</td>
            <td>{{ $doc->profesional_apellido_paterno }} {{ $doc->profesional_apellido_materno }}, {{ $doc->profesional_nombre }}</td>
        </tr>
        <tr>
            <td class="datos-label">Tipo y N° Documento:</td>
            <td>{{ $doc->profesional_tipo_doc }} - {{ $doc->profesional_doc }}</td>
        </tr>
        <tr>
            <td class="datos-label">Establecimiento (IPRESS):</td>
            <td>{{ $doc->establecimiento->nombre_establecimiento }}</td>
        </tr>
        <tr>
            <td class="datos-label">Área / Oficina:</td>
            <td>{{ $doc->area_oficina }}</td>
        </tr>
        <tr>
            <td class="datos-label">Cargo / Función:</td>
            <td>{{ $doc->cargo_rol }}</td>
        </tr>
        <tr>
            <td class="datos-label">Correo Electrónico:</td>
            <td>{{ $doc->correo_electronico ?? 'No registrado' }}</td>
        </tr>
    </table>

    <div class="contenido">
        <p><strong>ME COMPROMETO A:</strong></p>
        <ul>
            <li>Mantener la estricta confidencialidad de la información a la que tenga acceso (datos de pacientes, historias clínicas, diagnósticos, etc.), no divulgándola a terceros no autorizados.</li>
            <li>Utilizar los accesos (usuario y contraseña) asignados única y exclusivamente para el cumplimiento de mis funciones laborales.</li>
            <li>No compartir, ceder ni prestar mi cuenta de usuario y contraseña a ninguna otra persona bajo ninguna circunstancia.</li>
            <li>Informar inmediatamente a la Oficina de TI sobre cualquier sospecha de vulneración de seguridad o uso indebido de mis credenciales.</li>
            <li>Cumplir con las normas y políticas de seguridad de la información vigentes en la institución y en el marco de la Ley de Protección de Datos Personales.</li>
        </ul>
        <p>Reconozco que el incumplimiento de este compromiso acarreará las sanciones administrativas, civiles y penales que correspondan según la normativa vigente.</p>
    </div>

    <div class="contenido" style="margin-top: 30px;">
        <p>Ica, {{ \Carbon\Carbon::parse($doc->fecha)->translatedFormat('d \d\e F \d\e Y') }}</p>
    </div>

    <div class="firmas">
        <div class="firma-box">
            <strong>Firma del Usuario</strong><br>
            DNI: {{ $doc->profesional_doc }}
        </div>
        <div class="firma-box" style="float: right; margin-right: 0;">
            <strong>V°B° Jefe / Responsable</strong><br>
            Establecimiento / Área
        </div>
    </div>

    <div class="footer">
        Generado por el Sistema de Actas ICATEC - Fecha de impresión: {{ date('d/m/Y H:i:s') }}
    </div>
</body>
</html>