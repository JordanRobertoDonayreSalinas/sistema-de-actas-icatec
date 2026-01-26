<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Compromiso de Confidencialidad</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 8.5pt; line-height: 1.2; color: #000; margin: 0.8cm; }
        .header { text-align: left; margin-bottom: 10px; }
        .logo-img { height: 60px; }
        .titulo { text-align: center; font-weight: bold; text-transform: uppercase; margin: 10px 0; font-size: 10pt; }
        .fecha-derecha { text-align: right; margin-bottom: 8px; font-size: 8pt; }
        .contenido { text-align: justify; margin-bottom: 6px; font-size: 8.5pt; }
        .datos-tabla { width: 100%; margin: 8px 0; font-size: 8pt; line-height: 1.2; }
        .datos-tabla tr { margin-bottom: 2px; }
        .datos-label { font-weight: bold; }
        .separador { border-bottom: 1px solid #000; margin: 8px 0; }
        .parrafo { text-align: justify; margin-bottom: 6px; line-height: 1.2; font-size: 8.5pt; }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ public_path('images/logo_minsa_cc.png') }}" alt="Logo Ministerio de Salud" class="logo-img">
    </div>

    <div class="titulo">COMPROMISO DE CONFIDENCIALIDAD</div>

    <div class="fecha-derecha">
        @php
            $meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
            $fecha = \Carbon\Carbon::parse($doc->fecha);
            $mes = $meses[$fecha->month - 1];
        @endphp
        <span class="fecha-lugar">Ica, {{ $fecha->format('d') }} de {{ $mes }} del {{ $fecha->year }}</span>
    </div>

    <table class="datos-tabla">
        <tr>
            <td><strong>El (LA) SUSCRITO (A):</strong></td>
        </tr>
        <tr>
            <td><span style="color: #0066cc;">{{ $doc->profesional_apellido_paterno }} {{ $doc->profesional_apellido_materno }}, {{ $doc->profesional_nombre }}</span></td>
        </tr>
        <tr>
            <td><strong>{{ $doc->profesional_tipo_doc }}:</strong></td>
        </tr>
        <tr>
            <td><span style="color: #0066cc;">{{ $doc->profesional_doc }}</span></td>
        </tr>
        <tr>
            <td><strong>ESTABLECIMIENTO:</strong></td>
        </tr>
        <tr>
            <td><span style="color: #0066cc;">{{ $doc->establecimiento ? strtoupper($doc->establecimiento->codigo . ' - ' . $doc->establecimiento->nombre) : '' }}</span></td>
        </tr>
        <tr>
            <td><strong>ÁREA/CARGO, ROL O FUNCIONES:</strong></td>
        </tr>
        <tr>
            <td><span style="color: #0066cc;">{{ $doc->cargo_rol }} / {{ $doc->area_oficina }}</span></td>
        </tr>
        <tr>
            <td><strong>CORREO ELECTRÓNICO:</strong></td>
        </tr>
        <tr>
            <td><span style="color: #0066cc;">{{ $doc->correo_electronico ?? 'No registrado' }}</span></td>
        </tr>
    </table>

    <div class="separador"></div>

    <div class="parrafo">
        En virtud del cumplimiento de la Constitución Política del Perú, la Ley N° 29733 - Ley de Protección de Datos Personales y de lo señalado en la Ley N° 26842, Ley General de Salud, acepto y reconozco que el Ministerio de Salud a través de la Oficina de Gestión de Tecnologías de la Información, tendré acceso a la información Nominal del sistema de información de historia clínica electrónica SIHCE del MINSA en su(s) módulo(s) de <strong><u>{{ strtoupper($doc->sistemas_acceso) }}</u></strong>, el cual contiene datos personales incluyendo datos personales de salud, con la finalidad de validar, gestionar y hacer seguimiento a los indicadores sanitarios para fortalecerlos procesos de salud, mejorar las coberturas y el cierre de brechas de las prestaciones de los servicios de salud, por lo cual se garantizará obligatoriamente que se hará uso correcto, proporcionado y responsable de los datos referidos. 
    </div>

    <div class="parrafo">
        En ese sentido, por este medio me obligo a no divulgar, revelar, comunicar, distribuir, transmitir, grabar, modificar sin que guarde relación con la prestación de salud y de acuerdo a mis funciones asignadas estrictamente aplicable al ámbito laboral, duplicar, copiar o de cualquier otra forma de reproducir o tratamiento de datos personales, sin la autorización expresa y por escrito del titular de dicha información, la información y documentación a la que tengo acceso, bajo responsabilidad, para fines que no sean relacionados a la gestión sanitaria de la jurisdicción territorial sanitaria a la que represento según mis funciones.
    </div>

    <div class="parrafo">
        Asimismo, debo señalar que la información a la que accedo, a través de la autorización del Ministerio de Salud, solo será usada únicamente, para los fines y plazos que están señalados en el segundo párrafo del presente compromiso, de incumplir el mismo o realizar una acción que no ha sido permitida u autorizada o que no guarde lógica razonable con lo descrito en el presente documento, me someto a las responsabilidades de índole administrativa, penal y civil conforme a Ley.
    </div>

    <div class="parrafo">
        Las obligaciones y derechos inmersos en el presente compromiso de confidencialidad estarán vigentes a partir de la fecha de firma del vínculo con la Institución a la que represento, durante el tiempo que dure esta relación y después de la fecha en que se haya dado por terminada la relación laboral o las funciones asignadas, sin importar la razón de esta, de conformidad con la normatividad vigente.
    </div>

    <div class="parrafo">
        Por tanto, expreso mi compromiso de respetar el derecho fundamental a la protección de los datos personales, la intimidad personal y familiar de las personas y a guardar la reserva debida sobre la información a la que tuviera acceso por razón de mi actividad, prolongándose esta reserva incluso después que finalice el ejercicio de mi relación contractual o laboral con mi Institución.
    </div>

    <div style="margin-top: 25px; text-align: center;">
        <p style="font-size: 9pt; font-weight: bold; margin-bottom: 10px; text-align: left;">Firma:</p>
        <div style="border: 1px solid #000; width: 300px; padding: 15px; min-height: 100px; position: relative; margin: 0 auto;">
            <!-- Espacio para firma -->
            <div style="min-height: 100px;"></div>
            
            <!-- Línea separadora y datos -->
            <div style="border-top: 1px solid #000; padding-top: 10px; text-align: center;">
                <p style="font-weight: bold; font-size: 9pt; margin: 0; text-transform: uppercase;">
                    {{ $doc->profesional_apellido_paterno }} {{ $doc->profesional_apellido_materno }}, {{ $doc->profesional_nombre }}
                </p>
                <p style="font-size: 8.5pt; margin: 3px 0 0 0; color: #555;">
                    {{ $doc->profesional_tipo_doc }}: {{ $doc->profesional_doc }}
                </p>
            </div>
        </div>
    </div>

</body>
</html>