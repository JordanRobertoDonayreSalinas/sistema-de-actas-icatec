<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Acta de Monitoreo N° {{ ltrim($acta->numero_acta, '0') }}</title>
    <style>
        /* Configuración de Página */
        @page { margin: 1.5cm 1.5cm 2cm 1.5cm; }
        body { 
            font-family: 'Helvetica', 'Arial', sans-serif; 
            font-size: 9px; 
            color: #334155; 
            line-height: 1.3; 
            margin: 0;
        }

        /* Encabezado Principal */
        .header { 
            text-align: center; 
            margin-bottom: 15px; 
            border-bottom: 2px solid #0f172a;
            padding-bottom: 8px;
        }
        .header h1 { 
            margin: 0; 
            font-size: 14px; 
            color: #0f172a; 
            text-transform: uppercase; 
        }
        .header-sub {
            font-size: 10px;
            margin-top: 4px;
            color: #1e293b;
        }

        /* Títulos de Sección Principales */
        .section-header { 
            background-color: #e2e8f0; 
            padding: 5px 10px; 
            font-weight: bold; 
            font-size: 10px;
            color: #0f172a;
            margin: 15px 0 8px 0; 
            text-transform: uppercase;
        }

        /* Contenedor de cada Módulo */
        .modulo-container {
            border: 1px solid #cbd5e1;
            margin-bottom: 12px;
            border-radius: 2px;
        }
        .modulo-title {
            background-color: #f1f5f9;
            padding: 4px 8px;
            font-weight: bold;
            font-size: 10px;
            color: #0f172a;
            border-bottom: 1px solid #cbd5e1;
            text-transform: uppercase;
        }
        .sub-section {
            background-color: #f8fafc;
            font-size: 8px;
            font-weight: bold;
            color: #475569;
            padding: 3px 8px;
            border-bottom: 1px solid #e2e8f0;
            border-top: 1px solid #e2e8f0;
        }

        /* Tablas de Datos (Compactas) */
        table.data-table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-bottom: 0;
        }
        table.data-table th, table.data-table td { 
            border: 1px solid #e2e8f0; 
            padding: 4px 6px; 
            word-wrap: break-word;
            vertical-align: middle;
        }
        table.data-table th {
            background-color: #f1f5f9;
            text-align: left;
            font-size: 8px;
            color: #475569;
            text-transform: uppercase;
        }
        .bg-label { 
            background-color: #f8fafc; 
            font-weight: bold; 
            color: #475569;
            font-size: 8px;
        }
        .uppercase { text-transform: uppercase; font-size: 8px; }

        /* Fotos */
        .foto-grid {
            width: 100%;
            margin: 10px 0;
            text-align: center;
        }
        .foto-wrapper {
            width: 48%; 
            height: 200px; 
            display: inline-block;
            margin: 4px 1%;
            border: 1px solid #cbd5e1;
            background-color: #f8fafc;
            border-radius: 4px;
            overflow: hidden;
            vertical-align: top;
        }
        .foto-wrapper img {
            width: 100%;
            height: 100%;
            display: block;
            object-fit: cover;
        }
        .foto-caption {
            font-size: 7px;
            font-weight: bold;
            color: #1e293b;
            margin-top: 4px;
            text-transform: uppercase;
        }

        /* Firmas - DISEÑO ACTUALIZADO SEGÚN IMAGEN */
        .firmas-grid {
            width: 100%;
            margin-top: 15px;
            text-align: center;
            page-break-inside: avoid;
        }
        .firma-card { 
            width: 31%; 
            display: inline-block; 
            vertical-align: top; 
            margin: 5px 1%;
            background-color: #ffffff;
            border: 1px solid #cbd5e1; /* Borde gris/celeste claro del marco */
            border-radius: 6px; /* Esquinas redondeadas como en la imagen */
            box-sizing: border-box;
        }
        .firma-espacio {
            height: 50px; /* Espacio en blanco para firmar encima de la línea */
            width: 100%;
        }
        .firma-bottom {
            padding: 0 10px 12px 10px; /* Espaciado interno inferior */
        }
        .linea-firma { 
            border-top: 1px solid #94a3b8; /* Línea recta sólida, no punteada */
            margin: 0 auto 6px auto; 
            width: 95%; /* La línea no toca los bordes de la caja */
        }
        .nombre-firma { 
            font-size: 8px; 
            font-weight: bold; 
            color: #0f172a; /* Azul muy oscuro/negro */
            display: block;
            text-transform: uppercase;
            margin-bottom: 2px;
        }
        .cargo-firma { 
            font-size: 7px; 
            color: #64748b; /* Gris azulado más claro */
            display: block;
            text-transform: uppercase;
        }

        /* Footer */
        #footer {
            position: fixed;
            bottom: -1.5cm;
            left: 0;
            right: 0;
            height: 40px;
            text-align: center;
            border-top: 1px solid #e2e8f0;
            padding-top: 5px;
        }
        .footer-text { font-size: 8px; color: #94a3b8; }
        .page-number:before { content: "Página " counter(page); }
        .no-break { page-break-inside: avoid; }
    </style>
</head>
<body>

    <div id="footer">
        <div class="footer-text">
            Acta de Monitoreo IPRESS NO ESPECIALIZADAS N° {{ ltrim($acta->numero_acta, '0') }} | <span class="page-number"></span> 
        </div>
    </div>

    <div class="header">
        <h1>REPORTE CONSOLIDADO DE MONITOREO IPRESS</h1>
        <div class="header-sub">
            <strong>Establecimiento:</strong> {{ strtoupper($acta->establecimiento->nombre ?? 'ESTABLECIMIENTO NO REGISTRADO') }} 
            &nbsp;|&nbsp; 
            <strong>Acta N°:</strong> {{ str_pad($acta->numero_acta, 5, '0', STR_PAD_LEFT) }}
        </div>
    </div>

    <div class="section-header" style="margin-top: 0;">1. INFORMACIÓN DE CONTROL</div>
    <table class="data-table">
        <tr>
            <td class="bg-label" style="width: 20%;">FECHA DE MONITOREO:</td>
            <td style="width: 30%;">{{ \Carbon\Carbon::parse($acta->fecha)->format('d/m/Y') }}</td>
            <td class="bg-label" style="width: 20%;">MONITOR / IMPLEMENTADOR:</td>
            <td class="uppercase" style="width: 30%;">{{ $monitor['nombre'] ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="bg-label">JEFE DEL ESTABLECIMIENTO:</td>
            <td class="uppercase" colspan="3">{{ $jefe['nombre'] ?? 'N/A' }}</td>
        </tr>
    </table>

    @if(isset($equipoMonitoreo) && $equipoMonitoreo->count() > 0)
        <div class="sub-section" style="border-top: none; margin-top: 5px;">EQUIPO DE ACOMPAÑAMIENTO</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 45%">NOMBRE COMPLETO</th>
                    <th style="width: 20%">DNI</th>
                    <th style="width: 35%">CARGO</th>
                </tr>
            </thead>
            <tbody>
                @foreach($equipoMonitoreo as $acom)
                <tr>
                    <td class="uppercase">{{ trim(($acom->nombres ?? '') . ' ' . ($acom->apellido_paterno ?? '') . ' ' . ($acom->apellido_materno ?? '')) }}</td>
                    <td class="uppercase">{{ $acom->dni ?? $acom->doc ?? '-' }}</td>
                    <td class="uppercase">{{ $acom->cargo ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <div class="section-header">2. RESUMEN DE HALLAZGOS POR MÓDULOS</div>

    @php
        $ordenEstricto = [
            'gestion_administrativa' => 'GESTION ADMINISTRATIVA',
            'citas'                  => 'CITAS',
            'triaje'                 => 'TRIAJE',
            'consulta_medicina'      => 'CONSULTA EXTERNA: MEDICINA',
            'consulta_odontologia'   => 'CONSULTA EXTERNA: ODONTOLOGIA',
            'odontologia'            => 'CONSULTA EXTERNA: ODONTOLOGIA', 
            'consulta_nutricion'     => 'CONSULTA EXTERNA: NUTRICION',
            'nutricion'              => 'CONSULTA EXTERNA: NUTRICION', 
            'consulta_psicologia'    => 'CONSULTA EXTERNA: PSICOLOGIA',
            'psicologia'             => 'CONSULTA EXTERNA: PSICOLOGIA', 
            'cred'                   => 'CRED',
            'inmunizaciones'         => 'INMUNIZACIONES',
            'atencion_prenatal'      => 'ATENCION PRENATAL',
            'prenatal'               => 'ATENCION PRENATAL', 
            'planificacion_familiar' => 'PLANIFICACION FAMILIAR',
            'planificacion'          => 'PLANIFICACION FAMILIAR', 
            'parto'                  => 'PARTO',
            'puerperio'              => 'PUERPERIO',
            'fua_electronico'        => 'FUA ELECTRONICO',
            'farmacia'               => 'FARMACIA',
            'referencias'            => 'REFERENCIAS Y CONTRAREFERENCIAS',
            'refcon'                 => 'REFERENCIAS Y CONTRAREFERENCIAS', 
            'laboratorio'            => 'LABORATORIO',
            'urgencias'              => 'URGENCIAS Y EMERGENCIAS',
            'infraestructura_2d'     => 'INFRAESTRUCTURA Y CROQUIS 2D'
        ];
        
        $impresos = []; 
        $contadorModulo = 1;
        $hiddenKeys = ['id', 'acta_id', 'foto_evidencia', 'fotos_evidencia', 'comentarios', 'observaciones', 'password', 'token', 'created_at', 'updated_at', '_token'];
    @endphp

    @foreach($ordenEstricto as $nombreTecnico => $tituloPublico)
        @php 
            $mod = $modulos->first(function($item) use ($nombreTecnico) {
                return strtolower($item->modulo_nombre) === strtolower($nombreTecnico);
            });
        @endphp

        @if($mod && !in_array($mod->id, $impresos))
            @php 
                $impresos[] = $mod->id; 
                $cont = is_string($mod->contenido) ? json_decode($mod->contenido, true) : $mod->contenido; 
            @endphp
            
            <div class="modulo-container">
                <div class="modulo-title">MÓDULO {{ $contadorModulo }}: {{ $tituloPublico }}</div>
                
                @if(is_array($cont))
                    @php
                        $filasUnificadas = [];
                        foreach($cont as $k => $v) {
                            if(in_array($k, $hiddenKeys)) continue;
                            if(is_array($v)) {
                                if(!isset($v[0])) {
                                    foreach($v as $subK => $subV) {
                                        if(!is_array($subV) && !in_array($subK, $hiddenKeys) && $subV !== null && trim($subV) !== '') {
                                            $filasUnificadas[$k . ' ' . $subK] = $subV;
                                        }
                                    }
                                }
                            } else {
                                if($v !== null && trim($v) !== '') {
                                    $filasUnificadas[$k] = $v;
                                }
                            }
                        }

                        // Categorizador Dinámico
                        $grupoConsultorio = [];
                        $grupoProfesional = [];
                        $grupoDoc = [];
                        $grupoSoporte = [];
                        $grupoOtros = [];

                        foreach($filasUnificadas as $label => $val) {
                            $lblLower = strtolower($label);
                            
                            if(str_contains($lblLower, 'sihce') || str_contains($lblLower, 'dj') || str_contains($lblLower, 'confidencialidad') || str_contains($lblLower, 'dni fisico') || str_contains($lblLower, 'tipo dni') || str_contains($lblLower, 'dnie') || str_contains($lblLower, 'digital')) {
                                $grupoDoc[$label] = $val;
                            } elseif(str_contains($lblLower, 'fecha') || str_contains($lblLower, 'turno') || str_contains($lblLower, 'consultorio') || str_contains($lblLower, 'ventanilla') || str_contains($lblLower, 'horario')) {
                                $grupoConsultorio[$label] = $val;
                            } elseif(str_contains($lblLower, 'profesional') || str_contains($lblLower, 'personal') || str_contains($lblLower, 'rrhh') || str_contains($lblLower, 'cargo') || str_contains($lblLower, 'email') || str_contains($lblLower, 'telefono') || str_contains($lblLower, 'celular') || str_contains($lblLower, 'contacto') || str_contains($lblLower, 'rol') || str_contains($lblLower, 'especialidad')) {
                                $grupoProfesional[$label] = $val;
                            } elseif(str_contains($lblLower, 'capacitacion') || str_contains($lblLower, 'comunica') || str_contains($lblLower, 'soporte') || str_contains($lblLower, 'conectividad') || str_contains($lblLower, 'operador') || str_contains($lblLower, 'wifi')) {
                                $grupoSoporte[$label] = $val;
                            } else {
                                $grupoOtros[$label] = $val;
                            }
                        }

                        // Helper para imprimir chunks de 2 columnas
                        $renderChunkTable = function($grupoArray) {
                            if(count($grupoArray) == 0) return '';
                            $html = '<table class="data-table">';
                            foreach(array_chunk($grupoArray, 2, true) as $chunk) {
                                $html .= '<tr>';
                                $i = 0;
                                foreach($chunk as $l => $v) {
                                    $valStr = is_bool($v) ? ($v ? 'SI' : 'NO') : $v;
                                    $cleanLabel = strtoupper(str_replace(['_', 'inst'], [' ', 'entidad'], $l));
                                    $html .= '<td class="bg-label" style="width: 25%;">'.$cleanLabel.':</td>';
                                    $html .= '<td class="uppercase" style="width: 25%;">'.$valStr.'</td>';
                                    $i++;
                                }
                                if($i == 1) { // Rellenar espacio vacío si es impar
                                    $html .= '<td class="bg-label" style="width: 25%;"></td><td style="width: 25%;"></td>';
                                }
                                $html .= '</tr>';
                            }
                            $html .= '</table>';
                            return $html;
                        };
                    @endphp

                    {{-- Bloque Consultorio y Profesional --}}
                    @if(count($grupoConsultorio) > 0 || count($grupoProfesional) > 0)
                        <table style="width: 100%; border-collapse: collapse; border: none; padding: 0;">
                            <tr>
                                @if(count($grupoConsultorio) > 0)
                                <td style="width: 50%; padding: 0; vertical-align: top; border-right: 1px solid #e2e8f0;">
                                    <div class="sub-section" style="border-top: none;">DETALLE DEL CONSULTORIO</div>
                                    <table class="data-table" style="border: none;">
                                        @foreach($grupoConsultorio as $l => $v)
                                        <tr>
                                            <td class="bg-label" style="border-left:none;">{{ strtoupper(str_replace('_', ' ', $l)) }}:</td>
                                            <td class="uppercase" style="border-right:none;">{{ is_bool($v) ? ($v ? 'SI' : 'NO') : $v }}</td>
                                        </tr>
                                        @endforeach
                                    </table>
                                </td>
                                @endif
                                
                                @if(count($grupoProfesional) > 0)
                                <td style="{{ count($grupoConsultorio) > 0 ? 'width: 50%;' : 'width: 100%;' }} padding: 0; vertical-align: top;">
                                    <div class="sub-section" style="border-top: none;">DATOS DEL PROFESIONAL</div>
                                    <table class="data-table" style="border: none;">
                                        @foreach($grupoProfesional as $l => $v)
                                        <tr>
                                            <td class="bg-label" style="border-left:none;">{{ strtoupper(str_replace('_', ' ', $l)) }}:</td>
                                            <td class="uppercase" style="border-right:none;">{{ is_bool($v) ? ($v ? 'SI' : 'NO') : $v }}</td>
                                        </tr>
                                        @endforeach
                                    </table>
                                </td>
                                @endif
                            </tr>
                        </table>
                    @endif

                    {{-- Documentación y Firma --}}
                    @if(count($grupoDoc) > 0)
                        <div class="sub-section">DOCUMENTACIÓN Y FIRMA DIGITAL</div>
                        {!! $renderChunkTable($grupoDoc) !!}
                    @endif

                    {{-- Capacitación y Soporte --}}
                    @if(count($grupoSoporte) > 0)
                        <div class="sub-section">CAPACITACIÓN Y SOPORTE</div>
                        {!! $renderChunkTable($grupoSoporte) !!}
                    @endif

                    {{-- Otros Datos --}}
                    @if(count($grupoOtros) > 0)
                        <div class="sub-section">DATOS ADICIONALES DEL MÓDULO</div>
                        {!! $renderChunkTable($grupoOtros) !!}
                    @endif

                    {{-- Fotos Dinámicas del Módulo --}}
                    @php $fotosEncontradas = false; @endphp
                    @if(!empty($cont['fotos_evidencia']) && is_array($cont['fotos_evidencia']))
                        @foreach($cont['fotos_evidencia'] as $ruta)
                            @php
                                $isFullUrl = !empty($ruta) && str_starts_with($ruta, 'http');
                                $realPath = !empty($ruta) ? ($isFullUrl ? $ruta : public_path('storage/' . $ruta)) : null;
                            @endphp
                            @if($realPath && ($isFullUrl || file_exists($realPath)))
                                @if(!$fotosEncontradas) 
                                    <div class="sub-section">EVIDENCIAS FOTOGRÁFICAS</div><div class="foto-grid"> 
                                    @php $fotosEncontradas = true; @endphp
                                @endif
                                <div class="foto-wrapper"><img src="{{ $realPath }}"></div>
                            @endif
                        @endforeach
                        @if($fotosEncontradas) </div> @endif
                    @endif
                    
                    @if(!$fotosEncontradas && (!empty($cont['foto_1']) || !empty($cont['foto_2'])))
                        <div class="sub-section">EVIDENCIAS FOTOGRÁFICAS</div>
                        <div class="foto-grid">
                            @foreach(['foto_1', 'foto_2'] as $fKey)
                                @php
                                    $fPath = $cont[$fKey] ?? null;
                                    $fIsFull = $fPath && str_starts_with($fPath, 'http');
                                    $fRealPath = $fPath ? ($fIsFull ? $fPath : public_path('storage/' . $fPath)) : null;
                                @endphp
                                @if($fRealPath && ($fIsFull || file_exists($fRealPath)))
                                    <div class="foto-wrapper"><img src="{{ $fRealPath }}"></div>
                                @endif
                            @endforeach
                        </div>
                    @endif

                @else
                    <div style="padding: 10px; font-style: italic; font-size: 8px;">Sin información detallada registrada.</div>
                @endif
            </div>
            @php $contadorModulo++; @endphp
        @endif
    @endforeach

    <div class="section-header">3. DETALLE DE EQUIPAMIENTO POR MÓDULO</div>
    
    @if($equipos && $equipos->count() > 0)
        <table class="data-table" style="margin-bottom: 15px;">
            <thead>
                <tr>
                    <th width="5%" style="text-align: center;">N°</th>
                    <th width="15%">MÓDULO</th>
                    <th width="15%">SERIE/CÓDIGO</th>
                    <th width="5%" style="text-align: center;">CANT.</th>
                    <th width="25%">DESCRIPCIÓN DEL EQUIPO</th>
                    <th width="10%" style="text-align: center;">ESTADO</th>
                    <th width="10%">PROPIEDAD</th>
                    <th width="15%">OBSERVACIÓN</th>
                </tr>
            </thead>
            <tbody>
                @foreach($equipos as $index => $eq)
                    <tr class="uppercase">
                        <td style="text-align: center;">{{ $index + 1 }}</td>
                        <td>{{ strtoupper(str_replace('_', ' ', $eq->modulo)) }}</td>
                        <td style="font-family: monospace;">{{ $eq->nro_serie ?? '---' }}</td>
                        <td style="text-align: center;">{{ $eq->cantidad ?? '1' }}</td>
                        <td>{{ $eq->descripcion }}</td>
                        <td style="text-align: center;">{{ $eq->estado ?? 'N/A' }}</td>
                        <td>{{ $eq->propio ?? '---' }}</td>
                        <td>{{ $eq->observacion ?? '---' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p style="padding-left: 10px; font-style: italic; color: #64748b; font-size: 9px;">No se registró equipamiento tecnológico en este monitoreo.</p>
    @endif

    <div class="no-break">
        <div class="section-header">4. PANEL FOTOGRÁFICO DE EVIDENCIAS</div>
        @if($acta->foto1 || $acta->foto2)
            <div class="foto-grid">
                @if($acta->foto1 && file_exists(public_path('storage/' . $acta->foto1)))
                    <div class="foto-wrapper">
                        <img src="{{ public_path('storage/' . $acta->foto1) }}">
                        <div class="foto-caption">EVIDENCIA 01 - REGISTRO DE MONITOREO</div>
                    </div>
                @endif

                @if($acta->foto2 && file_exists(public_path('storage/' . $acta->foto2)))
                    <div class="foto-wrapper">
                        <img src="{{ public_path('storage/' . $acta->foto2) }}">
                        <div class="foto-caption">EVIDENCIA 02 - REGISTRO DE MONITOREO</div>
                    </div>
                @endif
            </div>
        @else
            <p style="padding-left: 10px; font-style: italic; color: #64748b; font-size: 9px;">No se adjuntaron fotografías de evidencia en esta acta.</p>
        @endif
    </div>

    <div class="no-break">
        <div class="section-header">5. FIRMAS DE CONFORMIDAD</div>
        <div class="firmas-grid">
            
            <div class="firma-card">
                <div class="firma-espacio"></div>
                <div class="firma-bottom">
                    <div class="linea-firma"></div>
                    <span class="nombre-firma">{{ $monitor['nombre'] ?? 'MONITOR' }}</span>
                    <span class="cargo-firma">IMPLEMENTADOR</span>
                </div>
            </div>

            <div class="firma-card">
                <div class="firma-espacio"></div>
                <div class="firma-bottom">
                    <div class="linea-firma"></div>
                    <span class="nombre-firma">{{ $jefe['nombre'] ?? 'JEFE DE ESTABLECIMIENTO' }}</span>
                    <span class="cargo-firma">JEFE DEL ESTABLECIMIENTO</span>
                </div>
            </div>

            @foreach($equipoMonitoreo as $miembro)
                <div class="firma-card">
                    <div class="firma-espacio"></div>
                    <div class="firma-bottom">
                        <div class="linea-firma"></div>
                        <span class="nombre-firma">{{ strtoupper(trim(($miembro->apellido_paterno ?? '') . ' ' . ($miembro->apellido_materno ?? ''). ' ' . ($miembro->nombres ?? ''))) }}</span>
                        <span class="cargo-firma">{{ strtoupper($miembro->institucion ?? 'ACOMPAÑANTE TÉCNICO') }}</span>
                    </div>
                </div>
            @endforeach

        </div>
    </div>
</body>
</html>