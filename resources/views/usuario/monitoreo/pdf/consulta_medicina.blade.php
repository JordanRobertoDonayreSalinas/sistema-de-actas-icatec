<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Módulo 04: Consulta Externa - Medicina - Acta {{ $acta->numero_acta }}</title>
    <style>
        /* AJUSTAMOS EL MARGEN INFERIOR A 2CM PARA QUE QUEPA EL PIE DE PÁGINA */
        @page { margin: 1.2cm 1.5cm 2cm 1.5cm; }
        body { font-family: 'Helvetica', sans-serif; font-size: 10px; color: #1e293b; line-height: 1.4; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #4f46e5; padding-bottom: 10px; }
        .header h1 { margin: 0; font-size: 16px; text-transform: uppercase; color: #4f46e5; font-weight: bold; }
        .section-title { background-color: #f1f5f9; padding: 6px 10px; font-weight: bold; text-transform: uppercase; border-left: 4px solid #4f46e5; margin-top: 15px; margin-bottom: 5px; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; table-layout: fixed; margin-bottom: 5px; }
        th, td { border: 1px solid #e2e8f0; padding: 6px 8px; text-align: left; vertical-align: middle; word-wrap: break-word; }
        th { background-color: #f8fafc; color: #475569; font-size: 8.5px; text-transform: uppercase; }
        .bg-label { background-color: #f8fafc; font-weight: bold; width: 30%;  text-transform: uppercase;}
        .uppercase { text-transform: uppercase; }
        .text-center { text-align: center; }

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
    {{-- BLOQUE DE CONFIGURACIÓN GLOBAL --}}
    @php 
        $n = 1; // Contador de secciones

        // --------------------------------------------------------
        // PREPARAMOS LOS DATOS DEL PROFESIONAL UNA SOLA VEZ
        // --------------------------------------------------------
        
        // A. Obtener datos crudos
        $rawTipoDoc = $detalle->contenido['profesional']['tipo_doc'] ?? '---';
        $rawNumDoc  = $detalle->contenido['profesional']['doc'] ?? '---';
        
        // B. Aplicar lógica de recorte para C.E. (Quitar los 2 primeros caracteres)
        $docFinal = $rawNumDoc; // Valor por defecto
               
        // C. Preparar Nombre Completo (También lo reutilizaremos)
        $pNom = $detalle->contenido['profesional']['nombres'] ?? '';
        $pPat = $detalle->contenido['profesional']['apellido_paterno'] ?? '';
        $pMat = $detalle->contenido['profesional']['apellido_materno'] ?? '';
        $profNombreCompleto = trim($pPat . ' ' . $pMat . ' ' . $pNom);
        
        if(empty($profNombreCompleto)) {
            $profNombreCompleto = $detalle->contenido['profesional']['apellidos_nombres'] ?? '---';
        }
    @endphp

    <div class="header">
        <h1>Módulo 04: Consulta Externa - Medicina</h1>
        <div style="font-weight: bold; color: #64748b; font-size: 10px; margin-top: 5px;">
            ACTA N° {{ str_pad($acta->numero_acta, 3, '0', STR_PAD_LEFT) }} | 
            ESTABLECIMIENTO: {{ $acta->establecimiento->codigo }} - {{ strtoupper($acta->establecimiento->nombre) }} | 
            FECHA: 
            @php
                // 1. Buscamos la fecha específica del módulo
                $fechaRaw = $detalle->contenido['fecha_monitoreo_medicina'] ?? null;
                
                // 2. Si existe, la formateamos. Si no, usamos la fecha general del acta
                if ($fechaRaw) {
                    echo \Carbon\Carbon::parse($fechaRaw)->format('d/m/Y');
                } else {
                    echo \Carbon\Carbon::parse($acta->fecha)->format('d/m/Y');
                }
            @endphp
        </div>
    </div>

    <div class="section-title">{{ $n++ }}. DETALLES DEL CONSULTORIO</div>
    <table>
        <tr>
            <td class="bg-label">Cantidad</td>
            <td>{{ $detalle->contenido['num_consultorios'] ?? '0' }}</td>
        </tr>
        <tr>
            <td class="bg-label">Consultorio Entrevistado</td>
            <td class="uppercase">{{ $detalle->contenido['denominacion_consultorio'] ?? '---' }}</td>
        </tr>
        <tr>
            <td class="bg-label">Turno</td>
            <td class="uppercase">{{ $detalle->contenido['turno'] ?? '---' }}</td>
        </tr>
        
    </table>

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
        <tr>
            <td class="bg-label">¿Utiliza SIHCE?</td>
            <td class="uppercase">{{ $detalle->contenido['utiliza_sihce'] ?? '---' }}</td>
        </tr>
        <tr>
            <td class="bg-label">Profesion</td>
            <td class="uppercase">{{ $detalle->contenido['profesional']['cargo'] ?? '---' }}</td>
        </tr>
        {{-- DOC ADMIN: Se muestra si SIHCE NO es 'NO' (o sea SI o vacío) --}}
        @if(($detalle->contenido['utiliza_sihce'] ?? '') != 'NO')
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
    {{-- Solo se muestra si Tipo Doc ES "DNI" --}}
    @if(($detalle->contenido['profesional']['tipo_doc'] ?? '') == 'DNI')
    <div class="section-title">{{ $n++ }}. DETALLE DE DNI Y FIRMA DIGITAL</div>
    <table>
        <tr>
            <td class="bg-label">Tipo de DNI</td>
            <td class="uppercase">{{ $detalle->contenido['tipo_dni_fisico'] ?? '---' }}</td>
        </tr>
        {{-- Si es AZUL, ocultamos estos campos --}}
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
    @if(($detalle->contenido['utiliza_sihce'] ?? '') != 'NO')
    <div class="section-title">{{ $n++ }}. Detalles de Capacitación</div>
    <table>
        <tr>
            <td class="bg-label">¿Recibió Capacitación?</td>
            <td>{{ $detalle->contenido['recibio_capacitacion'] ?? '---' }}</td>
        </tr>
        {{-- Sub-condición: Solo mostrar institución si SÍ recibió capacitación --}}
        @if(($detalle->contenido['recibio_capacitacion'] ?? '') != 'NO')
            <tr>
                <td class="bg-label">¿De parte de quién?</td>
                <td>{{ $detalle->contenido['inst_capacitacion'] ?? '---' }}</td>
            </tr>
        @endif
    </table>
    @endif

    <div class="section-title">{{ $n++ }}. Materiales</div>
    <div class="materiales-list">
        @php
            $materiales = $detalle->contenido['materiales'] ?? [];
            $materialesMapping = [
                'historia_clinica' => 'Historia Clínica',
                'fua' => 'FUA',
                'receta' => 'Receta',
                'orden_laboratorio' => 'Orden de Laboratorio',
                'hoja_referencia' => 'Hoja de Referencia',
                'otros' => 'Otros'
            ];
            $materialesSeleccionados = [];
            foreach ($materialesMapping as $key => $label) {
                if (isset($materiales[$key]) && $materiales[$key]) {
                    $materialesSeleccionados[] = $label;
                }
            }
        @endphp
        @if(count($materialesSeleccionados) > 0)
            @foreach($materialesSeleccionados as $material)
                <span class="materiales-item">{{ $material }}</span>
            @endforeach
        @else
            <span style="color: #94a3b8; font-style: italic;">SIN MATERIALES REGISTRADOS</span>
        @endif
    </div>

    <div class="section-title">{{ $n++ }}. Equipamiento del Consultorio</div>
    @php
        $equipos = \App\Models\EquipoComputo::where('cabecera_monitoreo_id', $acta->id)
                    ->where('modulo', 'consulta_medicina')
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

    {{-- SECCIÓN 7: SOPORTE (CONDICIONAL SIHCE) --}}
    @if(($detalle->contenido['utiliza_sihce'] ?? '') != 'NO')
    <div class="section-title">{{ $n++ }}. Soporte</div>
    <table>
        <tr>
            <td class="bg-label">ANTE DIFICULTADES SE COMUNICA CON</td>
            <td class="uppercase">{{ $detalle->contenido['comunica_a'] ?? '---' }}</td>
        </tr>
        <tr>
            <td class="bg-label">MEDIO QUE UTILIZA</td>
            <td>{{ $detalle->contenido['medio_soporte'] ?? '---' }}</td>
        </tr>
    </table>
    @endif

    <div class="section-title">{{ $n++ }}. Comentarios</div>
    <div style="border: 1px solid #e2e8f0; padding: 10px; min-height: 40px;" class="uppercase">
        {{ $detalle->contenido['comentarios'] ?? 'SIN COMENTARIOS.' }}
    </div>

    {{-- 8. EVIDENCIA FOTOGRÁFICA --}}
    <div class="section-title">{{ $n++ }}. Evidencia Fotográfica</div>

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
        <div class="section-title">{{ $n++ }}. Firma</div>
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