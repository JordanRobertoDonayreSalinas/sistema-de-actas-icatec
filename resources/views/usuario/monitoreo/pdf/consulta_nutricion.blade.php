<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Consulta Externa - Nutrición - Acta {{ $acta->id }}</title>
    <style>
        /* AJUSTAMOS EL MARGEN INFERIOR A 2CM PARA QUE QUEPA EL PIE DE PÁGINA */
        @page { margin: 1.2cm 1.5cm 2cm 1.5cm; }
        body { font-family: 'Helvetica', sans-serif; font-size: 10px; color: #1e293b; line-height: 1.4; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #4f46e5; padding-bottom: 10px; }
        .header h1 { margin: 0; font-size: 15px; text-transform: uppercase; color: #4f46e5; }
        .section-title { background-color: #f1f5f9; padding: 6px 10px; font-weight: bold; text-transform: uppercase; border-left: 4px solid #4f46e5; margin-top: 15px; margin-bottom: 5px; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; table-layout: fixed; margin-bottom: 5px; }
        th, td { border: 1px solid #e2e8f0; padding: 6px 8px; text-align: left; vertical-align: middle; word-wrap: break-word; }
        th { background-color: #f8fafc; color: #475569; font-size: 8.5px; text-transform: uppercase; }
        .bg-label { background-color: #f8fafc; font-weight: bold; width: 30%; }
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
        .firma-container { width: 50%; display: table; table-layout: fixed; margin: 0 25%; }
        .firma-box { display: table-cell; width: 50%; text-align: center; padding: 0 28px; vertical-align: top; border: 1px solid #e2e8f0; border-radius: 14px;}
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

    <div class="header">
        <h1>Módulo 6: Consulta Externa - Nutrición</h1>
        <div style="font-weight: bold; color: #64748b; font-size: 10px;">
            ACTA N° {{ str_pad($acta->id, 5, '0', STR_PAD_LEFT) }} | E.E.S.S.: {{ strtoupper($acta->establecimiento->nombre) }}
        </div>
    </div>

    <div class="section-title">1. Detalles de consultorio</div>
    <table>
        <tr>
            <td class="bg-label">Cantidad</td>
            <td>{{ $detalle->contenido['num_consultorios'] ?? '---' }}</td>
        </tr>
        <tr>
            <td class="bg-label">Denominación</td>
            <td class="uppercase">{{ $detalle->contenido['denominacion_consultorio'] ?? '---' }}</td>
        </tr>
        <tr>
            <td class="bg-label">Turno</td>
            <td class="uppercase">{{ $detalle->contenido['turno'] ?? '---' }}</td>
        </tr>
    </table>

    <div class="section-title">2. Datos del profesional</div>
    <table>
        <tr>
            <td class="bg-label">Nombres y Apellidos</td>
            <td class="uppercase">
                @php
                    $profNombre = $detalle->contenido['profesional']['nombres'] ?? '';
                    $profApellidoPaterno = $detalle->contenido['profesional']['apellido_paterno'] ?? '';
                    $profApellidoMaterno = $detalle->contenido['profesional']['apellido_materno'] ?? '';
                    $profCompleto = trim($profApellidoPaterno . ' ' . $profApellidoMaterno . ' ' . $profNombre);
                    if(empty($profCompleto)) {
                        $profCompleto = $detalle->contenido['profesional']['apellidos_nombres'] ?? '---';
                    }
                @endphp
                {{ strtoupper($profCompleto) }}
            </td>
        </tr>
        <tr>
            <td class="bg-label">Documento</td>
            <td>{{ $detalle->contenido['profesional']['doc'] ?? '---' }}</td>
        </tr>
        <tr>
            <td class="bg-label">Correo</td>
            <td>{{ $detalle->contenido['profesional']['email'] ?? '---' }}</td>
        </tr>
        <tr>
            <td class="bg-label">Cargo</td>
            <td class="uppercase">NUTRICIONISTA</td>
        </tr>
        <tr>
            <td class="bg-label">¿Firmó Declaración Jurada?</td>
            <td class="uppercase">{{ $detalle->contenido['firmo_dj'] ?? '---' }}</td>
        </tr>
        <tr>
            <td class="bg-label">¿Firmó Compromiso de Confidencialidad?</td>
            <td class="uppercase">{{ $detalle->contenido['firmo_confidencialidad'] ?? '---' }}</td>
        </tr>
    </table>
    
    <div class="section-title">3. Tipo de DNI y Firma Digital</div>
    <table>
        <tr>
            <td class="bg-label">Tipo de DNI</td>
            <td class="uppercase">{{ $detalle->contenido['tipo_dni_fisico'] ?? '---' }}</td>
        </tr>
        <tr>
            <td class="bg-label">Versión DNIe</td>
            <td class="uppercase">{{ $detalle->contenido['dnie_version'] ?? '---' }}</td>
        </tr>
        <tr>
            <td class="bg-label">¿Firma digitalmente en SIHCE?</td>
            <td class="uppercase">{{ $detalle->contenido['dnie_firma_sihce'] ?? '---' }}</td>
        </tr>
        <tr>
            <td class="bg-label">Observaciones/Motivo de Uso</td>
            <td class="uppercase">{{ $detalle->contenido['dni_observacion'] ?? '---' }}</td>
        </tr>
    </table>

    <div class="section-title">4. Detalles de Capacitación</div>
    <table>
        <tr>
            <td class="bg-label">¿Recibió Capacitación?</td>
            <td>{{ $detalle->contenido['recibio_capacitacion'] ?? '---' }}</td>
        </tr>
        <tr>
            <td class="bg-label">¿De parte de quién?</td>
            <td>{{ $detalle->contenido['inst_capacitacion'] ?? '---' }}</td>
        </tr>
    </table>

    <div class="section-title">5. Materiales</div>
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

    <div class="section-title">6. Equipamiento del Área</div>
    @php
        $equipos = \App\Models\EquipoComputo::where('cabecera_monitoreo_id', $acta->id)
                    ->where('modulo', 'consulta_nutricion')
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
                    <th width="15%">N° Serie</th>
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

    <div class="section-title">7. Soporte Técnico</div>
    <table>
        <tr>
            <td class="bg-label">¿A quién le comunica?</td>
            <td class="uppercase">{{ $detalle->contenido['comunica_a'] ?? '---' }}</td>
        </tr>
        <tr>
            <td class="bg-label">¿Qué medio utiliza?</td>
            <td>{{ $detalle->contenido['medio_soporte'] ?? '---' }}</td>
        </tr>
    </table>

    <div class="section-title">8. Comentarios</div>
    <div style="border: 1px solid #e2e8f0; padding: 10px; min-height: 40px;" class="uppercase">
        {{ $detalle->contenido['comentarios'] ?? 'SIN COMENTARIOS.' }}
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
                
                <div class="firma-label">NUTRICIONISTA</div>
                <div class="firma-label">DNI: {{ $detalle->contenido['profesional']['doc'] ?? '___________________' }}</div>
            </div>
        </div>
    </div>
</body>
</html>
