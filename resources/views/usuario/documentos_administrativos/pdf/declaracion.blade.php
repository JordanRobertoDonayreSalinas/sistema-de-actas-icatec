<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Declaración Jurada</title>
    <style>
        body { 
            font-family: Arial, Helvetica, sans-serif; 
            font-size: 10pt; 
            line-height: 1.4; 
            color: #000; 
            margin: 1.5cm 2cm; 
        }
        .titulo { 
            text-align: center; 
            font-weight: bold; 
            text-transform: uppercase; 
            margin-bottom: 20px; 
            font-size: 12pt; 
            letter-spacing: 0.5px;
            color: #000;
        }
        .contenido { 
            text-align: justify; 
            margin-bottom: 8px; 
            line-height: 1.3;
            color: #000;
        }
        .underline {
            text-decoration: underline;
            font-weight: bold;
            color: #000;
            text-transform: uppercase;
        }
        .fecha-lugar {
            text-decoration: underline;
            font-weight: bold;
            color: #000;
        }
    </style>
</head>
<body>

    <div class="titulo">DECLARACIÓN JURADA</div>

    <div class="contenido">
        <p style="margin-bottom: 20px;">
            Yo, <span class="underline">{{ strtoupper($doc->profesional_apellido_paterno . ' ' . $doc->profesional_apellido_materno . ' ' . $doc->profesional_nombre) }}</span> identificado con {{ $doc->profesional_tipo_doc == 'DNI' ? 'Documento Nacional de Identidad' : 'Carnet de Extranjería' }} N° 
            <span class="underline">{{ $doc->profesional_doc }}</span> en calidad de responsable del banco de datos personales del Establecimiento 
            <span class="underline">{{ $doc->establecimiento ? strtoupper($doc->establecimiento->codigo . ' - ' . $doc->establecimiento->nombre) : '' }}</span>
        
            en el marco de la Ley N° 29733, Ley de Protección de Datos Personales, su reglamento, 
            directiva de seguridad, así como la Resolución Ministerial Nº 004-2016-PCM, que aprueba el uso 
            obligatorio de la Norma Técnica Peruana "NTP ISO/IEC 27001:2014 Tecnología de la 
            Información. Técnicas de Seguridad. Sistemas de Gestión de Seguridad de la Información. 
            Requisitos. 2da Edición", en todas las entidades integrantes del sistema nacional de informática 
            y la Resolución Ministerial N° 68a-2020/MINSA, que aprueba la Directiva Administrativa N° 294-MINSA/2020/OGTI, "Directiva Administrativa que establece el tratamiento de los datos 
            personales relacionados con la salud o datos personales en salud".
        </p>
        
        <p style="margin-bottom: 12px;">
            Declaro que como responsable del banco de datos personales a mi cargo, al acceso a los sistemas 
            de información asistenciales que el Ministerio de Salud brinda para el cumplimiento de nuestras 
            funciones, que he recibido los lineamientos de seguridad de la información para la gestión de 
            accesos del sistema de información administrativo <span class="underline">{{ strtoupper($doc->sistemas_acceso) }}</span>, que se 
            deben cumplir y que son de mi entera responsabilidad su cumplimiento, así como de su difusión, 
            para que el personal tenga conocimiento del mismo, bajo responsabilidad.
        </p>
        
        <p style="margin-bottom: 12px;">
            Asimismo, declaro conocer que la presente declaración se encuentra sujeta al principio de 
            presunción de veracidad y al principio de privilegio de controles posteriores, establecidos en el 
            TUO de la Ley de Procedimiento Administrativo General, aprobado mediante Decreto Supremo 
            N° 004-2019-JUS.
        </p>
    </div>

    <p style="margin-top: 20px; margin-bottom: 20px;">
        @php
            $meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
            $fecha = \Carbon\Carbon::parse($doc->fecha);
            $mes = $meses[$fecha->month - 1];
        @endphp
        <span class="fecha-lugar">Ica, {{ $fecha->format('d') }} de {{ $mes }} del {{ $fecha->year }}</span>
    </p>

    <div style="margin-top: 30px; text-align: center;">
        <p style="margin-bottom: 8px; color: #000; text-align: left;">Firma:</p>
        
        <div style="border: 1px solid #000; width: 300px; height: 150px; margin: 0 auto; padding: 15px; position: relative;">
            <!-- Línea para la firma -->
            <div style="position: absolute; bottom: 50px; left: 50%; transform: translateX(-50%); width: 80%; border-top: 1px solid #666;"></div>
            
            <!-- Nombre completo -->
            <p style="position: absolute; bottom: 30px; left: 0; right: 0; text-align: center; font-weight: bold; font-size: 10pt; margin: 0; color: #000;">
                {{ strtoupper($doc->profesional_apellido_paterno . ' ' . $doc->profesional_apellido_materno . ' ' . $doc->profesional_nombre) }}
            </p>
            
            <!-- Tipo y número de documento -->
            <p style="position: absolute; bottom: 12px; left: 0; right: 0; text-align: center; font-size: 9pt; margin: 0; color: #666;">
                {{ $doc->profesional_tipo_doc }}: {{ $doc->profesional_doc }}
            </p>
        </div>
    </div>

</body>
</html>