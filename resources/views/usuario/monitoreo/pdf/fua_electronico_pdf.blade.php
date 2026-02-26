<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Módulo 14: FUA Electrónico - Acta {{ $acta->numero_acta }}</title>
    <style>
        /* AJUSTES EXACTOS DEL ARCHIVO DE MEDICINA */
        @page { margin: 1.2cm 1.5cm 2cm 1.5cm; }
        body { font-family: 'Helvetica', sans-serif; font-size: 10px; color: #1e293b; line-height: 1.4; }
        
        /* Encabezado */
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #4f46e5; padding-bottom: 10px; }
        .header h1 { margin: 0; font-size: 16px; text-transform: uppercase; color: #4f46e5; font-weight: bold; }
        
        /* Títulos de Sección */
        .section-title { background-color: #f1f5f9; padding: 6px 10px; font-weight: bold; text-transform: uppercase; border-left: 4px solid #4f46e5; margin-top: 15px; margin-bottom: 5px; font-size: 10px; }

        /* Tablas */
        table { width: 100%; border-collapse: collapse; table-layout: fixed; margin-bottom: 5px; }
        th, td { border: 1px solid #e2e8f0; padding: 6px 8px; text-align: left; vertical-align: middle; word-wrap: break-word; }
        th { background-color: #f8fafc; color: #475569; font-size: 8.5px; text-transform: uppercase; }
        
        /* Utilidades */
        .bg-label { background-color: #f8fafc; font-weight: bold; width: 35%; text-transform: uppercase;} /* Ancho ajustado para FUA */
        .uppercase { text-transform: uppercase; }
        .text-center { text-align: center; }

        /* Evidencia fotográfica: Contenedor AJUSTABLE */
        .foto-container { 
            margin: 15px auto; 
            padding: 10px; 
            border: 1px solid #e2e8f0; 
            background-color: #ffffff; 
            text-align: center; 
            display: table; 
            width: auto; }
        .foto { 
            display: block; 
            margin: 0 auto; 
            max-width: 100%; 
            max-height: 300px; 
            width: auto; height: auto; 
            object-fit: contain; 
            background-color: #ffffff; 
            border: 1px solid #e2e8f0; 
            box-shadow: 0 2px 4px rgba(0,0,0,0.05); }

        /* Estilo para items (Materiales/Recursos) */
        .materiales-list { padding: 8px; }
        .materiales-item { display: inline-block; padding: 3px 8px; background-color: #e0e7ff; border-radius: 4px; margin: 2px; font-size: 9px; }
        .badge-gray { display: inline-block; padding: 3px 8px; background-color: #f1f5f9; color: #475569; border-radius: 4px; margin: 2px; font-size: 9px; border: 1px solid #e2e8f0; font-weight: bold; text-transform: uppercase; }

        /* Firmas */
        .firma-section { margin-top: 15px; }
        .firma-container { width: 50%; display: table; table-layout: fixed; margin: 0 25%;}
        .firma-box { display: table-cell; width: 50%; text-align: center; padding: 0 28px; vertical-align: top; border: 1px solid #e2e8f0; border-radius: 14px; }
        .firma-linea { border-bottom: 1px solid #000; height: 150px; margin-bottom: 8px; }
        .firma-label { font-size: 10px; margin: 5px 0; }
        .firma-nombre { font-weight: bold; text-transform: uppercase; font-size: 12px; }

        /* Recuadro Sin Evidencia */
        .no-evidence-box { border: 2px dashed #cbd5e1; border-radius: 20px; padding: 30px; text-align: center; color: #64748b; font-style: italic; background-color: #f8fafc; margin: 15px 0; }
    </style>
</head>
<body>
    {{-- BLOQUE DE CONFIGURACIÓN GLOBAL --}}
    @php 
        $n = 1; // Contador de secciones

        // --------------------------------------------------------
        // PREPARAMOS LOS DATOS DEL PROFESIONAL (Lógica Unificada)
        // --------------------------------------------------------
        $rawTipoDoc = $detalle->contenido['profesional']['tipo_doc'] ?? '---';
        $rawNumDoc  = $detalle->contenido['profesional']['doc'] ?? '---';
        
        // B. Aplicar lógica de recorte para C.E. (Quitar los 2 primeros caracteres)
        $docFinal = $rawNumDoc; // Valor por defecto

        // Nombre Completo
        $pNom = $detalle->contenido['profesional']['nombres'] ?? '';
        $pPat = $detalle->contenido['profesional']['apellido_paterno'] ?? '';
        $pMat = $detalle->contenido['profesional']['apellido_materno'] ?? '';
        $profNombreCompleto = trim($pPat . ' ' . $pMat . ' ' . $pNom);
        
        if(empty($profNombreCompleto)) {
            $profNombreCompleto = $detalle->contenido['profesional']['apellidos_nombres'] ?? '---';
        }

        // 2. Variable maestra para control de visibilidad
        // Si tiene_sistema_fua es 'NO', ocultamos varias secciones
        $tieneFua = $detalle->contenido['tiene_sistema_fua'] ?? '---';
        $utilizaSihce = $detalle->contenido['utiliza_sihce'] ?? '---';
    @endphp

    {{-- ENCABEZADO --}}
    <div class="header">
        <h1>Módulo 14: FUA Electrónico</h1>
        <div style="font-weight: bold; color: #64748b; font-size: 10px; margin-top: 5px;">
            ACTA N° {{ str_pad($acta->numero_acta, 3, '0', STR_PAD_LEFT) }} | 
            ESTABLECIMIENTO: {{ $acta->establecimiento->codigo }} - {{ strtoupper($acta->establecimiento->nombre) }} | 
            FECHA: 
            @php
                // CAMBIO: Buscamos fecha_monitoreo_fua
                $fechaRaw = $detalle->contenido['fecha_monitoreo_fua'] ?? null;
                if ($fechaRaw) {
                    echo \Carbon\Carbon::parse($fechaRaw)->format('d/m/Y');
                } else {
                    echo \Carbon\Carbon::parse($acta->fecha)->format('d/m/Y');
                }
            @endphp
        </div>
    </div>

    {{-- SECCIÓN 1: DATOS DEL SISTEMA --}}
    <div class="section-title">{{ $n++ }}. Detalles del Sistema</div>
    <table>
        <tr>
            <td class="bg-label">¿Cuenta con módulo FUA del SIHCE?</td>
            <td class="uppercase">{{ $tieneFua }}</td>
        </tr>
        <tr>
            <td class="bg-label">Nro. Personas que Digitan</td>
            <td class="uppercase">{{ $detalle->contenido['n_digitadores'] ?? '0' }}</td>
        </tr>
        <tr>
            <td class="bg-label">Turno Evaluado</td>
            <td class="uppercase">{{ $detalle->contenido['turno'] ?? '---' }}</td>
        </tr>
    </table>

    {{-- SECCIÓN 2: DATOS DEL PROFESIONAL --}}
    <div class="section-title">{{ $n++ }}. Datos del profesional</div>
    <table>
        <tr>
            <td class="bg-label">Apellidos y Nombres</td>
            <td class="uppercase">{{ strtoupper($profNombreCompleto) }}</td>
        </tr>
        <tr>
            <td class="bg-label">Tipo Doc.</td>
            <td>{{ $rawTipoDoc }}</td>
        </tr>
        <tr>
            <td class="bg-label">Documento</td>
            <td>{{ $docFinal }}</td>
        </tr>
        <tr>
            <td class="bg-label">Correo</td>
            <td>{{ $detalle->contenido['profesional']['email'] ?? '---' }}</td>
        </tr>
        <tr>
          <td class="bg-label">Celular</td>
            <td>{{ $detalle->contenido['profesional']['telefono'] ?? '---' }}</td>
        </tr>
        @if($tieneFua != 'NO')
        <tr>
            <td class="bg-label">¿Utiliza SIHCE?</td>
            <td class="uppercase">{{ $utilizaSihce }}</td>
        </tr>
        @endif
        <tr>
            <td class="bg-label">Profesion</td>
            <td class="uppercase">{{ $detalle->contenido['profesional']['cargo'] ?? '---' }}</td>
        </tr>
        {{-- DOC ADMIN: Condicional --}}
        @if($tieneFua != 'NO' && $utilizaSihce != 'NO')
        <tr>
            <td class="bg-label">¿Firmó Declaración Jurada?</td>
            <td class="uppercase">{{ $detalle->contenido['firmo_dj'] ?? '---' }}</td>
        </tr>
        <tr>
            <td class="bg-label">¿Firmó Compromiso de Confidencialidad?</td>
            <td class="uppercase">{{ $detalle->contenido['firmo_confidencialidad'] ?? '---' }}</td>
        </tr>
        @endif
    </table>

    {{-- SECCIÓN 3: DNI Y FIRMA (CONDICIONAL) --}}
    @if(($detalle->contenido['profesional']['tipo_doc'] ?? '') == 'DNI')
    <div class="section-title">{{ $n++ }}. Tipo de DNI y Firma Digital</div>
    <table>
        <tr>
            <td class="bg-label">Tipo de DNI</td>
            <td class="uppercase">{{ $detalle->contenido['tipo_dni_fisico'] ?? '---' }}</td>
        </tr>
        @if(($detalle->contenido['tipo_dni_fisico'] ?? '') != 'AZUL')
        <tr>
            <td class="bg-label">Versión DNIe</td>
            <td class="uppercase">{{ $detalle->contenido['dnie_version'] ?? '---' }}</td>
        </tr>
        <tr>
            <td class="bg-label">¿Firma digitalmente en SIHCE?</td>
            <td class="uppercase">{{ $detalle->contenido['dnie_firma_sihce'] ?? '---' }}</td>
        </tr>
        @endif
        <tr>
            <td class="bg-label">Observaciones</td>
            <td class="uppercase">{{ $detalle->contenido['dni_observacion'] ?? '---' }}</td>
        </tr>
    </table>
    @endif

    {{-- SECCIÓN 4: CAPACITACIÓN (CONDICIONAL SIHCE) --}}
    @if($tieneFua != 'NO' && $utilizaSihce != 'NO')
    <div class="section-title">{{ $n++ }}. Detalles de Capacitación</div>
    <table>
        <tr>
            <td class="bg-label">¿Recibió Capacitación?</td>
            <td>{{ $detalle->contenido['recibio_capacitacion'] ?? '---' }}</td>
        </tr>
        @if(($detalle->contenido['recibio_capacitacion'] ?? '') != 'NO')
        <tr>
            <td class="bg-label">¿De parte de quién?</td>
            <td>{{ $detalle->contenido['inst_capacitacion'] ?? '---' }}</td>
        </tr>
        @endif
    </table>
    @endif

    {{-- SECCIÓN 5: SOFTWARE Y FLUJO --}}
    <div class="section-title">{{ $n++ }}. Software y Flujo de Atención</div>
    <table>
        <tr>
            <td class="bg-label">Software utilizado</td>
            <td class="uppercase">{{ $detalle->contenido['nombre_software'] ?? '---' }}</td>
        </tr>
        <tr>
            <td class="bg-label">Modalidad de Registro</td>
            <td class="uppercase">
                @php
                    $mod = $detalle->contenido['modalidad_registro'] ?? '';
                    $modLabel = match($mod) {
                        'EN TIEMPO REAL' => 'EN TIEMPO REAL (Punto de Atención)',
                        'DIGITACION POSTERIOR' => 'DIGITACIÓN POSTERIOR',
                        'AMBOS' => 'AMBOS',
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

    {{-- SECCIÓN 6: INTEROPERABILIDAD --}}
    <div class="section-title">{{ $n++ }}. Envío de Información (Tramas)</div>
    <table>
        <thead>
            <tr>
                <th>Frecuencia de Envío</th>
                <th>Conectividad (Servidor)</th>
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
            </tr>
        </tbody>
    </table>

    {{-- SECCIÓN 7: RECURSOS DISPONIBLES --}}
    <div class="section-title">{{ $n++ }}. Recursos Específicos Disponibles</div>
    <div class="materiales-list" style="border: 1px solid #e2e8f0; background: #fff;">
        @php
            $recursos = $detalle->contenido['recursos'] ?? [];
            $labels = [
                'lector_barras' => 'Lector de Código de Barras',
                'impresora_fua' => 'Impresora FUA',
                'puntos_red' => 'Puntos de Red',
                'wifi_personal' => 'WiFi Personal',
                'servidor_local' => 'Servidor Local'
            ];
            $hayRecursos = false;
        @endphp
        
        @foreach($labels as $key => $label)
            @if(isset($recursos[$key]) && $recursos[$key])
                <span class="badge-gray">{{ $label }}</span>
                @php $hayRecursos = true; @endphp
            @endif
        @endforeach

        @if(!$hayRecursos)
            <span style="color: #94a3b8; font-style: italic; font-size: 9px;">SIN RECURSOS ESPECÍFICOS REGISTRADOS</span>
        @endif
    </div>
   
    {{-- SECCIÓN 8: EQUIPAMIENTO DEL ÁREA --}}
    <div class="section-title">{{ $n++ }}. Equipamiento del Área</div>
    @php
        $equipos = \App\Models\EquipoComputo::where('cabecera_monitoreo_id', $acta->id)
                    ->where('modulo', 'fua_electronico')
                    ->get();
    @endphp
    @if($equipos->count() > 0)
        <table>
            <thead>
                <tr>
                    <th width="25%">Descripción</th>
                    <th width="12%">Cantidad</th>
                    <th width="15%">Estado</th>
                    <th width="18%">Propiedad</th>
                    <th width="15%">N.SERIE/C.PAT</th>
                    <th width="15%">Observación</th>
                </tr>
            </thead>
            <tbody>
                @foreach($equipos as $eq)
                <tr>
                    <td class="uppercase">{{ $eq->descripcion }}</td>
                    <td class="text-center">{{ $eq->cantidad }}</td>
                    <td>{{ $eq->estado }}</td>
                    <td>{{ $eq->propio }}</td>
                    <td>{{ $eq->nro_serie ?? '---' }}</td>
                    <td class="uppercase">{{ $eq->observacion ?? '---' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div style="color: #94a3b8; font-style: italic; padding: 8px;">SIN EQUIPAMIENTO REGISTRADO</div>
    @endif

    {{-- SECCIÓN: CONECTIVIDAD --}}
    <div class="section-title">{{ $n++ }}. CONECTIVIDAD</div>
    @php
        $tipoConectividad  = $detalle->contenido['tipo_conectividad'] ?? null;
        $wifiFuente        = $detalle->contenido['wifi_fuente'] ?? null;
        $operadorServicio  = $detalle->contenido['operador_servicio'] ?? null;
    @endphp
    <table>
        <tr>
            <td class="bg-label">Tipo de Conectividad</td>
            <td class="uppercase">{{ $tipoConectividad ?? '---' }}</td>
        </tr>
        @if($tipoConectividad == 'WIFI')
        <tr>
            <td class="bg-label">Fuente de WiFi</td>
            <td class="uppercase">{{ $wifiFuente ?? '---' }}</td>
        </tr>
        @endif
        @if($tipoConectividad != 'SIN CONECTIVIDAD')
        <tr>
            <td class="bg-label">Operador de Servicio</td>
            <td class="uppercase">{{ $operadorServicio ?? '---' }}</td>
        </tr>
        @endif
    </table>

    {{-- SECCIÓN 9: SOPORTE (CONDICIONAL SIHCE) --}}
    @if($tieneFua != 'NO' && $utilizaSihce != 'NO')
    <div class="section-title">{{ $n++ }}. Soporte</div>
    <table>
        <tr>
            <td class="bg-label">¿A quién le comunica?</td>
            <td class="uppercase">{{ $detalle->contenido['comunica_a'] ?? '---' }}</td>
        </tr>
        <tr>
            <td class="bg-label">¿Qué medio utiliza?</td>
            <td class="uppercase">{{ $detalle->contenido['medio_soporte'] ?? '---' }}</td>
        </tr>
    </table>
    @endif

    {{-- SECCIÓN 10: COMENTARIOS --}}
    <div class="section-title">{{ $n++ }}. Observaciones / Comentarios</div>
    <div style="border: 1px solid #e2e8f0; padding: 10px; min-height: 40px; font-size: 9px;" class="uppercase">
        {{ $detalle->contenido['comentarios'] ?? 'SIN OBSERVACIONES.' }}
    </div>

    {{-- 11. EVIDENCIA FOTOGRÁFICA --}}
    <div class="section-title">{{ $n++ }}. Evidencia Fotográfica</div>

    @if(!empty($imagenesData) && is_array($imagenesData) && count($imagenesData) > 0)
        @if(count($imagenesData) === 1)
            <div class="foto-container">
                <img src="{{ $imagenesData[0] }}" class="foto" alt="Evidencia">
            </div>
        @else
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
        <div class="no-evidence-box">
            No se adjuntó evidencia fotográfica.
        </div>
    @endif

    {{-- 12. FIRMAS --}}
    <div class="firma-section">
        <div class="section-title">{{ $n++ }}. Firma del entrevistado</div>
        <div class="firma-container">
            <div class="firma-box">
                <div class="firma-linea"></div>
                <div class="firma-nombre">{{ strtoupper($profNombreCompleto) }}</div>
                <div class="firma-label">{{ $rawTipoDoc }}: {{ $docFinal }}</div>
                <div class="firma-label">FIRMA DEL PROFESIONAL ENTREVISTADO</div>
            </div>
        </div>
    </div>
</body>
</html>
