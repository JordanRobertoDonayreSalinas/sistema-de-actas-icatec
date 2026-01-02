<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>FUA Electrónico - Acta {{ $acta->id }}</title>
    <style>
        @page { margin: 1.2cm 1.5cm; }
        body { font-family: 'Helvetica', sans-serif; font-size: 10px; color: #1e293b; line-height: 1.4; }
        
        /* Encabezado */
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #4f46e5; padding-bottom: 10px; }
        .header h1 { margin: 0; font-size: 15px; text-transform: uppercase; color: #4f46e5; }
        
        /* Títulos de Sección */
        .section-title { 
            background-color: #f1f5f9; 
            padding: 6px 10px; 
            font-weight: bold; 
            text-transform: uppercase; 
            border-left: 4px solid #4f46e5; 
            margin-top: 15px; 
            margin-bottom: 5px; 
            font-size: 10px; 
        }

        /* Tablas */
        table { width: 100%; border-collapse: collapse; table-layout: fixed; margin-bottom: 5px; }
        th, td { border: 1px solid #e2e8f0; padding: 6px 8px; text-align: left; vertical-align: middle; word-wrap: break-word; }
        th { background-color: #f8fafc; color: #475569; font-size: 8.5px; text-transform: uppercase; font-weight: bold; }
        
        /* Utilidades */
        .bg-label { background-color: #f8fafc; font-weight: bold; width: 35%; color: #334155; }
        .uppercase { text-transform: uppercase; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        
        /* Etiquetas (Badges) simulados en PDF */
        .badge { 
            display: inline-block; 
            padding: 2px 6px; 
            border-radius: 4px; 
            font-size: 9px; 
            font-weight: bold; 
            text-transform: uppercase;
        }
        .badge-gray { background-color: #e2e8f0; color: #475569; }

        /* Evidencia fotográfica: Contenedor AJUSTABLE */
        .foto-container { 
            margin: 15px auto;  /* 'auto' a los lados CENTRA el cuadro en la página */
            padding: 10px; 
            border: 1px solid #e2e8f0; 
            background-color: #ffffff; 
            text-align: center; 
            display: table; /* Hace que el div se comporte como una tabla (se encoge al contenido) */
            width: auto;    /* Le dice que no use el 100%, sino solo lo necesario */
        }
        /* Estilo para la IMAGEN ÚNICA (Ajustado) */
        .foto { 
            display: block; 
            margin: 0 auto; 
            max-width: 100%;      /* No desbordar el ancho */
            max-height: 300px;    /* <--- HE REDUCIDO ESTO (Antes 500px, ahora 350px) */
            width: auto;          /* Mantiene la proporción correcta */
            height: auto;         /* Mantiene la proporción correcta */
            object-fit: contain;  /* Asegura que se vea completa dentro del recuadro */
            background-color: #ffffff; 
            border: 1px solid #e2e8f0; 
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        /* Evidencia fotográfica: múltiples imágenes en grid con recuadro uniforme */
        .foto-grid { display: flex; flex-wrap: wrap; justify-content: space-between; gap: 6px; margin: 10px 0; padding: 10px; border: 2px solid #4f46e5; background-color: #f9fafc; }
        .foto-grid-item { width: calc(50% - 6px); }
        .foto-grid-item img { width: 100%; height: 150px; object-fit: contain; background-color: #ffffff; border: 1px solid #e2e8f0; display: block; }
        .materiales-list { padding: 8px; }
        .materiales-item { display: inline-block; padding: 3px 8px; background-color: #e0e7ff; border-radius: 4px; margin: 2px; font-size: 9px; }
        /* Estilos para recuadros de firma */
        .firma-section { margin-top: 15px; }
        .firma-container { width: 50%; display: table; table-layout: fixed; margin: 0 25%;}
        .firma-box { display: table-cell; width: 50%; text-align: center; padding: 0 28px; vertical-align: top; border: 1px solid #e2e8f0; border-radius: 14px; }
        .firma-linea { border-bottom: 1px solid #000; height: 150px; margin-bottom: 8px; }
        .firma-label { font-size: 10px; margin: 5px 0; }
        .firma-nombre { font-weight: bold; text-transform: uppercase; font-size: 12px; }
        .firma-fecha { font-size: 8px; margin-top: 3px; }

        /* Recuadro para indicar ausencia de evidencia fotográfica */
        .no-evidence-box {
            border: 2px dashed #cbd5e1; /* Borde gris discontinuo */
            border-radius: 20px;        /* Bordes redondeados */
            padding: 30px;              /* Espacio interno */
            text-align: center;
            color: #64748b;             /* Color de texto gris suave */
            font-style: italic;
            background-color: #f8fafc;  /* Fondo muy claro */
            margin: 15px 0;
        }
    </style>
</head>
<body>

    {{-- ENCABEZADO --}}
    <div class="header">
        <h1>Módulo 14: FUA Electrónico</h1>
        <div style="font-weight: bold; color: #64748b; font-size: 10px;">
            ACTA N° {{ str_pad($acta->id, 5, '0', STR_PAD_LEFT) }} | E.E.S.S.: {{ strtoupper($acta->establecimiento->nombre) }}
        </div>
    </div>

    {{-- SECCIÓN 1: DATOS DEL SISTEMA --}}
    <div class="section-title">1. Características del Sistema FUA</div>
    <table>
        <tr>
            <td class="bg-label">¿Cuenta con Sistema SIHCE/FUA?</td>
            <td class="uppercase">{{ $detalle->contenido['tiene_sistema_fua'] ?? '---' }}</td>
        </tr>
        <tr>
            <td class="bg-label">Software Utilizado</td>
            <td class="uppercase">{{ $detalle->contenido['nombre_software'] ?? '---' }}</td>
        </tr>
        <tr>
            <td class="bg-label">Nro. Personas que Digitan</td>
            <td class="uppercase">{{ $detalle->contenido['version_sistema'] ?? '---' }}</td>
        </tr>
        <tr>
            <td class="bg-label">Modalidad de Registro</td>
            <td class="uppercase">
                @php
                    $mod = $detalle->contenido['modalidad_registro'] ?? '';
                    $modLabel = match($mod) {
                        'PUNTO_ATENCION' => 'EN TIEMPO REAL (Punto de Atención)',
                        'DIGITACION' => 'DIGITACIÓN POSTERIOR',
                        default => '---'
                    };
                @endphp
                {{ $modLabel }}
            </td>
        </tr>
        <tr>
            <td class="bg-label">Gestión de Firma</td>
            <td class="uppercase">
                @php
                    $firma = $detalle->contenido['gestion_firma'] ?? '';
                    $firmaLabel = match($firma) {
                        'IMPRESION_FISICA' => 'SE IMPRIME Y FIRMA MANUALMENTE',
                        'FIRMA_DIGITAL' => 'FIRMA DIGITAL / ELECTRÓNICA',
                        'SIN_FIRMA' => 'PENDIENTE / NO SE IMPRIME',
                        default => '---'
                    };
                @endphp
                {{ $firmaLabel }}
            </td>
        </tr>
    </table>

    {{-- SECCIÓN 2: INTEROPERABILIDAD Y TRAMAS --}}
    <div class="section-title">2. Interoperabilidad y Tramas</div>
    <table>
        <thead>
            <tr>
                <th>Frecuencia de Envío</th>
                <th>Conectividad (Servidor)</th>
                <th>Realiza Backup Local</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center uppercase">{{ $detalle->contenido['frecuencia_envio'] ?? '---' }}</td>
                <td class="text-center uppercase">
                    @if(($detalle->contenido['conectividad_server'] ?? '') == 'ESTABLE')
                        <span style="color: #059669; font-weight: bold;">ESTABLE</span>
                    @elseif(($detalle->contenido['conectividad_server'] ?? '') == 'INESTABLE')
                        <span style="color: #dc2626; font-weight: bold;">INESTABLE / FALLAS</span>
                    @else
                        ---
                    @endif
                </td>
                <td class="text-center uppercase">{{ $detalle->contenido['realiza_backup'] ?? '---' }}</td>
            </tr>
        </tbody>
    </table>

    {{-- SECCIÓN 3: RECURSOS DISPONIBLES --}}
    <div class="section-title">3. Recursos Específicos Disponibles</div>
    <div style="border: 1px solid #e2e8f0; padding: 10px; background-color: #fff;">
        @php
            $recursos = $detalle->contenido['recursos'] ?? [];
            $labels = [
                'lector_barras' => 'Lector de Código de Barras (DNI)',
                'impresora_tickets' => 'Impresora de Tickets',
                'impresora_fua' => 'Impresora FUA (A4/A5)',
                'puntos_red' => 'Puntos de Red',
                'wifi_personal' => 'WiFi Personal',
                'servidor_local' => 'Servidor Local'
            ];
            $hayRecursos = false;
        @endphp
        
        <table style="border: none;">
            <tr>
            @foreach($labels as $key => $label)
                @if(isset($recursos[$key]) && $recursos[$key])
                    <td style="border: none; padding: 4px; width: 33%;">
                        <span class="badge badge-gray">{{ $label }}</span>
                    </td>
                    @php $hayRecursos = true; @endphp
                    @if($loop->iteration % 3 == 0) </tr><tr> @endif
                @endif
            @endforeach
            </tr>
        </table>

        @if(!$hayRecursos)
            <div style="text-align: center; color: #94a3b8; font-style: italic;">No se registraron recursos específicos.</div>
        @endif
    </div>

    {{-- SECCIÓN 4: DATOS DEL RESPONSABLE --}}
    <div class="section-title">4. Responsable FUA / Admisión</div>
    <table>
        <tr>
            <td class="bg-label">Nombre Completo</td>
            <td class="uppercase">
                @php
                    $prof = $detalle->contenido['profesional'] ?? [];
                    $nombreCompleto = trim(($prof['apellido_paterno'] ?? '') . ' ' . ($prof['apellido_materno'] ?? '') . ' ' . ($prof['nombres'] ?? ''));
                    if(empty($nombreCompleto)) $nombreCompleto = $prof['apellidos_nombres'] ?? '---';
                @endphp
                {{ $nombreCompleto }}
            </td>
        </tr>
        <tr>
            <td class="bg-label">DNI</td>
            <td>{{ $prof['doc'] ?? '---' }}</td>
        </tr>
        <tr>
            <td class="bg-label">Correo / Teléfono</td>
            <td>{{ strtolower($prof['email'] ?? '') }} {{ isset($prof['telefono']) ? ' / '.$prof['telefono'] : '' }}</td>
        </tr>
    </table>

    {{-- SECCIÓN 5: CAPACITACIÓN --}}
    <div class="section-title">5. Capacitación</div>
    <table>
        <tr>
            <td class="bg-label">¿Recibió Capacitación?</td>
            <td width="15%" class="text-center">{{ $detalle->contenido['recibio_capacitacion'] ?? '---' }}</td>
            <td class="bg-label" width="20%">Entidad:</td>
            <td class="uppercase">{{ $detalle->contenido['inst_capacitacion'] ?? '---' }}</td>
        </tr>
    </table>

    {{-- SECCIÓN 6: EQUIPAMIENTO INFORMÁTICO --}}
    <div class="section-title">6. Equipamiento del Área</div>
    @php
        $equipos = \App\Models\EquipoComputo::where('cabecera_monitoreo_id', $acta->id)
                    ->where('modulo', 'fua_electronico')
                    ->get();
    @endphp
    @if($equipos->count() > 0)
        <table>
            <thead>
                <tr>
                    <th width="30%">Descripción</th>
                    <th width="10%">Cant.</th>
                    <th width="15%">Estado</th>
                    <th width="15%">Propiedad</th>
                    <th width="30%">Observación</th>
                </tr>
            </thead>
            <tbody>
                @foreach($equipos as $eq)
                <tr>
                    <td class="uppercase">{{ $eq->descripcion }}</td>
                    <td class="text-center">{{ $eq->cantidad }}</td>
                    <td class="text-center">{{ $eq->estado }}</td>
                    <td class="text-center">{{ $eq->propio }}</td>
                    <td class="uppercase" style="font-size: 9px;">{{ $eq->observacion ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="no-evidence-box" style="padding: 10px; font-size: 9px;">SIN EQUIPAMIENTO REGISTRADO</div>
    @endif

    {{-- SECCIÓN 7: SOPORTE TÉCNICO --}}
    <div class="section-title">7. Soporte Técnico</div>
    <table>
        <tr>
            <td class="bg-label">Reporta fallas a:</td>
            <td class="uppercase">{{ $detalle->contenido['comunica_a'] ?? '---' }}</td>
            <td class="bg-label">Medio utilizado:</td>
            <td class="uppercase">{{ $detalle->contenido['medio_soporte'] ?? '---' }}</td>
        </tr>
    </table>

    {{-- SECCIÓN 8: COMENTARIOS --}}
    <div class="section-title">8. Observaciones / Comentarios</div>
    <div style="border: 1px solid #e2e8f0; padding: 10px; min-height: 40px; font-size: 9px;" class="uppercase">
        {{ $detalle->contenido['comentarios'] ?? 'SIN OBSERVACIONES.' }}
    </div>

    {{-- 9. EVIDENCIA FOTOGRÁFICA --}}
    <div class="section-title">9. Evidencia Fotográfica</div>

    @if(!empty($imagenesData) && is_array($imagenesData) && count($imagenesData) > 0)
        
        @if(count($imagenesData) === 1)
            {{-- Caso 1 Foto --}}
            <div class="foto-container">
                <img src="{{ $imagenesData[0] }}" class="foto" alt="Evidencia">
            </div>
        @else
            {{-- Caso Múltiples Fotos --}}
            <div style="text-align: center; padding: 10px; border: 1px solid #ffffff; background-color: #f9fafc;">
                <table style="width: 100%; border: none;">
                    <tr>
                        @foreach($imagenesData as $index => $img)
                            @if($index > 0 && $index % 2 == 0) 
                                </tr><tr> 
                            @endif
                            <td style="border: none; padding: 5px; text-align: center; width: 50%;">
                                <div style="border: 1px solid #cbd5e1; padding: 4px; background: #fff;">
                                    <img src="{{ $img }}" style="max-width: 100%; height: 160px; object-fit: contain;">
                                </div>
                            </td>
                        @endforeach
                        @if(count($imagenesData) % 2 != 0)
                            <td style="border: none;"></td>
                        @endif
                    </tr>
                </table>
            </div>
        @endif

    @else
        {{-- ESTA ES LA PARTE QUE AGREGA EL RECUADRO "SIN EVIDENCIA" --}}
        <div class="no-evidence-box">
            No se adjuntó evidencia fotográfica.
        </div>
    @endif

    {{-- 10. FIRMAS (Ahora están fuera del IF para que siempre salgan) --}}
    <div class="firma-section">
        <div class="section-title">10. Firma del entrevistado</div>
        <div class="firma-container">
            <div class="firma-box">
                <div class="firma-linea"></div>
                <div class="firma-nombre">
                    @php
                        $profesionalNombre = $detalle->contenido['profesional']['nombres'] ?? '';
                        $profesionalApellidoPaterno = $detalle->contenido['profesional']['apellido_paterno'] ?? '';
                        $profesionalApellidoMaterno = $detalle->contenido['profesional']['apellido_materno'] ?? '';
                        $profesional = trim($profesionalApellidoPaterno . ' ' . $profesionalApellidoMaterno . ', ' . $profesionalNombre);
                        if(empty($profesional)) {
                            $profesional = $detalle->contenido['profesional']['apellidos_nombres'] ?? '___________________';
                        }
                    @endphp
                    {{ strtoupper($profesional) }}
                </div>
                
                <div class="firma-label">RESPONSABLE DEL SIS</div>
                <div class="firma-label">DNI: {{ $detalle->contenido['profesional']['doc'] ?? '___________________' }}</div>
            </div>
        </div>
    </div>

    <div style="position: fixed; bottom: -10px; width: 100%; text-align: right; font-size: 8px; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 5px;">
        Generado por Sistema de Monitoreo | Fecha: {{ date('d/m/Y H:i:s') }}
    </div>

</body>
</html>

    
