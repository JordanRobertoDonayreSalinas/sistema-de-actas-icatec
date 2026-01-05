<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Acta de Monitoreo N° {{ ltrim($acta->id, '0') }}</title>
    <style>
        /* Configuración de Página */
        @page { margin: 1.5cm 2cm 2.5cm 2cm; }
        body { 
            font-family: 'Helvetica', 'Arial', sans-serif; 
            font-size: 10px; 
            color: #334155; 
            line-height: 1.5; 
            margin: 0;
        }

        /* Encabezado */
        .header { 
            text-align: center; 
            margin-bottom: 20px; 
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 10px;
        }
        .header h1 { 
            margin: 0; 
            font-size: 16px; 
            color: #0f172a; 
            text-transform: uppercase; 
        }
        .header .establishment {
            margin-top: 5px;
            font-size: 12px;
            color: #1e293b;
            font-weight: bold;
            text-transform: uppercase;
        }

        /* Títulos de Sección */
        .section-header { 
            background-color: #f8fafc; 
            padding: 6px 12px; 
            font-weight: bold; 
            font-size: 10px;
            color: #1e293b;
            border-left: 4px solid #0f172a; 
            margin: 15px 0 10px 0; 
            text-transform: uppercase;
        }

        /* Tablas */
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-bottom: 10px; 
        }
        th, td { 
            border: 1px solid #e2e8f0; 
            padding: 6px 10px; 
        }
        th {
            background-color: #f1f5f9;
            text-align: left;
            font-size: 8.5px;
            color: #475569;
        }
        .bg-label { 
            background-color: #f8fafc; 
            font-weight: bold; 
            width: 40%; 
        }
        .uppercase { text-transform: uppercase; }

        /* Estilo Estandarizado para Fotos (MAXIMIZADO) */
        .foto-grid {
            width: 100%;
            margin: 20px 0;
            text-align: center;
        }
        .foto-wrapper {
            width: 350px; /* Tamaño máximo para 2 fotos horizontales */
            height: 260px; 
            display: inline-block;
            margin: 8px;
            border: 1px solid #cbd5e1;
            background-color: #f8fafc;
            border-radius: 10px;
            overflow: hidden;
            vertical-align: top;
        }
        .foto-wrapper img {
            width: 100%;
            height: 100%;
            display: block;
        }
        .foto-caption {
            font-size: 8.5px;
            font-weight: 900;
            color: #1e293b;
            margin-top: 8px;
            text-transform: uppercase;
            letter-spacing: 0.8px;
        }

        /* Estilo de Firmas */
        .firmas-grid {
            width: 100%;
            margin-top: 20px;
            text-align: center;
        }
        .firma-card { 
            width: 30%; 
            display: inline-block; 
            vertical-align: top; 
            margin: 5px;
            border: 1px solid #cbd5e1; 
            background-color: #ffffff;
            border-radius: 4px;
            padding: 15px 5px 10px 5px;
            text-align: center;
        }
        .linea-firma { 
            border-top: 1px solid #94a3b8; 
            margin: 40px auto 8px auto; 
            width: 85%; 
        }
        .nombre-firma { 
            font-size: 8px; 
            font-weight: bold; 
            color: #0f172a;
            display: block;
            text-transform: uppercase;
        }
        .cargo-firma { 
            font-size: 7px; 
            color: #64748b; 
            display: block;
            text-transform: uppercase;
        }

        /* Pie de Página */
        #footer {
            position: fixed;
            bottom: -1cm;
            left: 0;
            right: 0;
            height: 50px;
            text-align: center;
            border-top: 1px solid #e2e8f0;
            padding-top: 10px;
        }
        .footer-text {
            font-size: 8px;
            color: #94a3b8;
        }
        .page-number:before {
            content: "Página " counter(page);
        }

        .no-break { page-break-inside: avoid; }
    </style>
</head>
<body>

    <div id="footer">
        <div class="footer-text">
            Acta de Monitoreo IPRESS NO ESPECIALIZADAS N° {{ ltrim($acta->id, '0') }}<br>
            <span class="page-number"></span> 
        </div>
    </div>

    <div class="header">
        <h1>Acta de Monitoreo IPRESS NO ESPECIALIZADAS N° {{ ltrim($acta->id, '0') }}</h1>
        <div class="establishment">{{ strtoupper($acta->establecimiento->nombre) }}</div>
    </div>

    <div class="section-header">1. Información de Control</div>
    <table>
        <tr>
            <td class="bg-label">Fecha:</td>
            <td>{{ \Carbon\Carbon::parse($acta->fecha)->format('d/m/Y') }}</td>
        </tr>
        <tr>
            <td class="bg-label">Implementador:</td>
            <td class="uppercase">{{ $monitor['nombre'] }}</td>
        </tr>
        <tr>
            <td class="bg-label">Jefe del Establecimiento:</td>
            <td class="uppercase">{{ $jefe['nombre'] }}</td>
        </tr>
    </table>

    <div class="section-header">2. RESUMEN DE HALLAZGOS POR MÓDULOS</div>

    @php
        $ordenEstricto = [
            'gestion_administrativa' => 'GESTION ADMINISTRATIVA',
            'citas'                  => 'CITAS',
            'triaje'                 => 'TRIAJE',
            'consulta_medicina'      => 'CONSULTA EXTERNA: MEDICINA',
            'consulta_odontologia'   => 'CONSULTA EXTERNA: ODONTOLOGIA',
            'consulta_nutricion'     => 'CONSULTA EXTERNA: NUTRICION',
            'consulta_psicologia'    => 'CONSULTA EXTERNA: PSICOLOGIA',
            'cred'                   => 'CRED',
            'inmunizaciones'         => 'INMUNIZACIONES',
            'atencion_prenatal'      => 'ATENCION PRENATAL',
            'planificacion_familiar' => 'PLANIFICACION FAMILIAR',
            'parto'                  => 'PARTO',
            'puerperio'              => 'PUERPERIO',
            'fua_electronico'        => 'FUA ELECTRONICO',
            'farmacia'               => 'FARMACIA',
            'refcon'                 => 'REFCON',
            'laboratorio'            => 'LABORATORIO',
            'urgencias_emergencias'  => 'URGENCIAS Y EMERGENCIAS'
        ];
        $yaImpresos = [];
    @endphp

    @foreach($ordenEstricto as $nombreTecnico => $tituloPublico)
        @php $mod = $modulos->where('modulo_nombre', $nombreTecnico)->first(); @endphp
        @if($mod && !in_array($mod->id, $yaImpresos))
            @php 
                $yaImpresos[] = $mod->id; 
                $cont = is_string($mod->contenido) ? json_decode($mod->contenido, true) : $mod->contenido; 
            @endphp
            @if(is_array($cont) && isset($cont[0]) && is_string($cont[0]) && str_contains($cont[0], '_')) @continue @endif

            <div class="no-break">
                <div style="background-color: #f1f5f9; padding: 4px 10px; font-weight: bold; font-size: 9px; margin-bottom: 2px; border: 1px solid #e2e8f0;">
                    MÓDULO: {{ $tituloPublico }}
                </div>
                @if(is_array($cont))
                    <table>
                        @foreach($cont as $key => $value)
                            @if(!is_numeric($key) && !is_array($value) && !in_array($key, ['foto_evidencia', 'comentarios', 'observaciones', 'password', 'token']) && !str_contains(strtoupper($key), 'DOC'))
                                <tr>
                                    <td class="bg-label">{{ strtoupper(str_replace(['_', 'inst'], [' ', 'entidad'], $key)) }}:</td>
                                    <td class="uppercase">{{ $value }}</td>
                                </tr>
                            @endif
                        @endforeach
                    </table>
                @endif
            </div>
        @endif
    @endforeach

    <div class="section-header">3. DETALLE DE EQUIPAMIENTO POR MÓDULO</div>
    @php $equiposPorModulo = $equipos->groupBy('modulo'); @endphp

    @if($equiposPorModulo->count() > 0)
        @foreach($ordenEstricto as $nombreTecnico => $tituloPublico)
            @php $equiposModulo = $equiposPorModulo->get($nombreTecnico); @endphp
            @if($equiposModulo && $equiposModulo->count() > 0)
                <div class="no-break">
                    <div style="padding: 4px 10px; font-weight: bold; font-size: 8.5px; color: #1e293b; border-bottom: 1px solid #cbd5e1; margin-bottom: 5px;">
                        EQUIPOS EN {{ $tituloPublico }}
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th width="40%">DESCRIPCIÓN</th>
                                <th width="30%">N° SERIE / CÓDIGO</th>
                                <th width="30%">PROPIEDAD</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($equiposModulo as $eq)
                                <tr class="uppercase">
                                    <td>{{ $eq->descripcion }}</td>
                                    <td style="font-family: monospace;">{{ $eq->nro_serie ?? '---' }}</td>
                                    <td>{{ $eq->propio }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        @endforeach
    @else
        <p style="padding-left: 10px; font-style: italic; color: #64748b;">No se registró equipamiento tecnológico en este monitoreo.</p>
    @endif

    {{-- SECCIÓN: PANEL FOTOGRÁFICO MAXIMIZADO --}}
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
            <p style="padding-left: 10px; font-style: italic; color: #64748b;">No se adjuntaron fotografías de evidencia en esta acta.</p>
        @endif
    </div>

    <div class="no-break">
        <div class="section-header">5. Firmas de Conformidad</div>
        <div class="firmas-grid">
            <div class="firma-card">
                <div class="linea-firma"></div>
                <span class="nombre-firma">{{ $monitor['nombre'] }}</span>
                <span class="cargo-firma">Implementador</span>
            </div>
            <div class="firma-card">
                <div class="linea-firma"></div>
                <span class="nombre-firma">{{ $jefe['nombre'] }}</span>
                <span class="cargo-firma">Jefe de Establecimiento</span>
            </div>
            @foreach($equipoMonitoreo as $miembro)
                <div class="firma-card">
                    <div class="linea-firma"></div>
                    <span class="nombre-firma">{{ strtoupper(($miembro->apellido_paterno ?? '') . ' ' . ($miembro->apellido_materno ?? ''). ' ' . ($miembro->nombres ?? '')) }}</span>
                    <span class="cargo-firma">{{ strtoupper($miembro->institucion ?? 'Acompañante Técnico') }}</span>
                </div>
            @endforeach
        </div>
    </div>
</body>
</html>