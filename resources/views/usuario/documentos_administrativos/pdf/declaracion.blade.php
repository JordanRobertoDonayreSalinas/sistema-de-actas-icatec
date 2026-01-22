<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Declaración Jurada</title>
    <style>
        body { font-family: 'Times New Roman', Times, serif; font-size: 11pt; line-height: 1.4; color: #000; margin: 2cm 2.5cm; }
        .titulo { text-align: center; font-weight: bold; text-transform: uppercase; margin-bottom: 30px; font-size: 14pt; text-decoration: underline; }
        .contenido { text-align: justify; margin-bottom: 15px; }
        .datos-personales { margin: 20px 0; padding-left: 20px; }
        .datos-personales p { margin: 5px 0; font-weight: bold; }
        .modulos-box { border: 1px solid #000; padding: 10px; margin: 15px 0; font-size: 9pt; }
        .modulos-lista { display: table; width: 100%; }
        .modulo-item { display: table-cell; width: 33%; padding: 2px; }
        .firmas { margin-top: 100px; text-align: center; }
        .firma-linea { border-top: 1px solid #000; width: 250px; margin: 0 auto 5px auto; }
    </style>
</head>
<body>

    <div class="titulo">DECLARACIÓN JURADA DE USUARIO</div>

    <div class="contenido">
        <p>Yo,</p>
        <div class="datos-personales">
            <p>NOMBRE COMPLETO: {{ $doc->profesional_apellido_paterno }} {{ $doc->profesional_apellido_materno }} {{ $doc->profesional_nombre }}</p>
            <p>DOCUMENTO DE IDENTIDAD (DNI/CEX): {{ $doc->profesional_doc }}</p>
            <p>CARGO / FUNCIÓN: {{ $doc->cargo_rol }}</p>
            <p>ESTABLECIMIENTO: {{ $doc->establecimiento->nombre_establecimiento }}</p>
        </div>
        <p>Por medio del presente documento, DECLARO BAJO JURAMENTO:</p>
        
        <ol>
            <li>Que he recibido capacitación y/o inducción básica para el manejo de los sistemas informáticos solicitados.</li>
            <li>Que los datos consignados en este formulario son verdaderos y actualizados.</li>
            <li>Que asumo total responsabilidad por las acciones realizadas con mi código de usuario en los siguientes módulos o sistemas para los cuales solicito acceso:</li>
        </ol>
    </div>

    <div class="modulos-box">
        <strong>SISTEMAS / MÓDULOS SOLICITADOS:</strong>
        <p style="margin-top: 5px; text-transform: uppercase;">
            {{ $doc->sistemas_acceso }}
        </p>
    </div>

    <div class="contenido">
        <p>Asimismo, autorizo a la DIRESA ICA y a la Oficina de TI a auditar mis accesos y transacciones realizadas en el sistema para fines de control y seguridad.</p>
        <p>Firmo en señal de conformidad, en la ciudad de Ica, a los {{ \Carbon\Carbon::parse($doc->fecha)->day }} días del mes de {{ \Carbon\Carbon::parse($doc->fecha)->translatedFormat('F') }} del año {{ \Carbon\Carbon::parse($doc->fecha)->year }}.</p>
    </div>

    <div class="firmas">
        <div class="firma-linea"></div>
        <strong>{{ $doc->profesional_nombre }} {{ $doc->profesional_apellido_paterno }}</strong><br>
        DNI: {{ $doc->profesional_doc }}<br>
        <span style="font-size: 8pt;">(Firma y Huella Digital)</span>
    </div>

</body>
</html>