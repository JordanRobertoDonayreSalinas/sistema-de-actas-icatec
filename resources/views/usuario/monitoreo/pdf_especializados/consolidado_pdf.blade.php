<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte Consolidado CSMC</title>
    <style>
        @page { margin: 1.5cm; }
        body { font-family: sans-serif; font-size: 10px; color: #333; line-height: 1.4; }
        
        /* HEADER */
        .header { text-align: center; margin-bottom: 25px; border-bottom: 2px solid #0f766e; padding-bottom: 10px; }
        .header h1 { margin: 0; font-size: 16px; color: #115e59; text-transform: uppercase; }
        .header p { margin: 2px 0; font-size: 10px; color: #555; }

        /* NUEVO: ESTILOS SECCIÓN DE CONTROL */
        .control-section { margin-bottom: 30px; }
        .control-title {
            font-size: 11px;
            font-weight: bold;
            color: #1e293b;
            text-transform: uppercase;
            border-left: 5px solid #0f172a; /* Barra lateral oscura */
            padding-left: 10px;
            margin-bottom: 10px;
        }
        .control-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        .control-table th { background-color: #f1f5f9; color: #334155; font-weight: bold; text-align: left; padding: 6px; border: 1px solid #e2e8f0; width: 30%; }
        .control-table td { border: 1px solid #e2e8f0; padding: 6px; color: #0f172a; }

        .team-header {
            font-size: 10px; font-weight: bold; color: #334155; margin-bottom: 5px; text-transform: uppercase;
        }
        
        /* MÓDULOS */
        .module-container { margin-bottom: 20px; page-break-inside: avoid; border: 1px solid #ddd; padding: 10px; border-radius: 5px; }
        .module-header { 
            background-color: #0f766e; color: white; padding: 6px 10px; 
            font-size: 12px; font-weight: bold; text-transform: uppercase; 
            border-radius: 3px; margin-bottom: 10px;
        }
        .section-title {
            font-size: 10px; font-weight: bold; color: #0f766e;
            border-bottom: 1px solid #ccfbf1; margin-top: 8px; margin-bottom: 4px; text-transform: uppercase;
        }

        /* TABLAS GENERALES */
        table.data-table { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
        .data-table th, .data-table td { border: 1px solid #e2e8f0; padding: 4px 6px; text-align: left; vertical-align: top; }
        .data-table th { background-color: #f0fdfa; color: #0f766e; font-weight: bold; font-size: 9px; white-space: nowrap; width: 30%; }
        
        .table-equipos { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
        .table-equipos th, .table-equipos td { border: 1px solid #e2e8f0; padding: 4px 6px; }
        .table-equipos th { background-color: #1e293b; color: #fff; text-align: center; font-size: 9px; }
        .table-equipos td { text-align: center; font-size: 9px; }
        .text-left { text-align: left !important; }
        .text-center { text-align: center !important; }
        .badge { background-color: #e2e8f0; padding: 2px 4px; border-radius: 3px; font-weight: bold; font-size: 8px; }

        /* FOTOS */
        .photo-section { margin-top: 20px; page-break-inside: avoid; }
        .photo-grid { width: 100%; text-align: center; margin-top: 10px; }
        .photo-container {
            display: inline-block; width: 45%; margin: 0 5px 10px 5px;
            border: 4px solid #fff; box-shadow: 0 0 5px rgba(0,0,0,0.1);
            border-radius: 8px; overflow: hidden; vertical-align: top;
        }
        .photo-img { width: 100%; height: 200px; object-fit: cover; border-radius: 5px; }

        /* FIRMAS (AJUSTADO) */
        .signatures-section { margin-top: 30px; page-break-inside: avoid; }
        .signatures-table { width: 100%; border: none; margin-top: 10px; }
        .signatures-table td { border: none; padding: 8px; vertical-align: top; }
        
        .signature-box {
            border: 1px solid #94a3b8;
            border-radius: 8px;
            /* AQUÍ ESTÁ EL CAMBIO: Mayor padding-top para bajar el texto y dejar espacio para firmar */
            padding: 70px 10px 10px 10px; 
            text-align: center;
            /* Altura fija ajustada para mantener proporción */
            height: 60px; 
            background-color: #fff;
        }
        .signature-line {
            border-bottom: 1px solid #64748b;
            width: 85%;
            margin: 0 auto 5px auto;
        }
        .signature-name { 
            font-weight: bold; font-size: 9px; color: #0f172a; text-transform: uppercase; margin-bottom: 2px;
        }
        .signature-role { 
            font-size: 8px; color: #64748b; font-weight: bold; text-transform: uppercase; 
        }

    </style>
</head>
<body>

    <div class="header">
        <h1>Reporte Consolidado de Módulos Especializados</h1>
        <p><strong>Establecimiento:</strong> {{ $acta->establecimiento->nombre ?? 'NO DEFINIDO' }}</p>
        <p><strong>Fecha de Generación:</strong> {{ date('d/m/Y H:i') }} | <strong>Acta N°:</strong> {{ str_pad($acta->id, 5, '0', STR_PAD_LEFT) }}</p>
    </div>

    {{-- ================================================================= --}}
    {{-- NUEVA SECCIÓN: INFORMACIÓN DE CONTROL (Al inicio)                 --}}
    {{-- ================================================================= --}}
    <div class="control-section">
        <div class="control-title">INFORMACIÓN DE CONTROL</div>
        
        {{-- Tabla de Control Principal --}}
        <table class="control-table">
            <tr>
                <th>Fecha de Monitoreo:</th>
                {{-- Usamos created_at como fecha base, o la fecha actual si no existe --}}
                <td>{{ $acta->created_at ? $acta->created_at->format('d/m/Y') : date('d/m/Y') }}</td>
            </tr>
            <tr>
                <th>Monitor / Implementador:</th>
                <td>{{ $monitor['nombre'] }}</td>
            </tr>
            <tr>
                <th>Jefe del Establecimiento:</th>
                <td>{{ $jefe['nombre'] }}</td>
            </tr>
        </table>

        {{-- Tabla Equipo Acompañamiento --}}
        @if(isset($equipoMonitoreo) && count($equipoMonitoreo) > 0)
            <div class="team-header">EQUIPO DE ACOMPAÑAMIENTO:</div>
            <table class="control-table" style="font-size: 9px;">
                <thead>
                    <tr>
                        <th style="background-color: #f8fafc; color: #64748b; width: 50%;">NOMBRE COMPLETO</th>
                        <th style="background-color: #f8fafc; color: #64748b; width: 25%;">DNI</th>
                        <th style="background-color: #f8fafc; color: #64748b; width: 25%;">CARGO</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($equipoMonitoreo as $miembro)
                    <tr>
                        <td style="text-transform: uppercase;">{{ $miembro->nombre_completo ?? '-' }}</td>
                        <td>{{ $miembro->dni ?? '-' }}</td>
                        <td style="text-transform: uppercase;">{{ $miembro->cargo ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
    {{-- ================================================================= --}}


    @php $modulosOrdenados = $modulos->sortBy('modulo_nombre'); @endphp

    @forelse($modulosOrdenados as $modulo)
        @php
            $contenido = is_string($modulo->contenido) ? json_decode($modulo->contenido, true) : $modulo->contenido;
            $consultorio = $contenido['detalle_del_consultorio'] ?? [];
            $profesional = $contenido['datos_del_profesional'] ?? [];
            $admin       = $contenido['documentacion_administrativa'] ?? [];
            $dni         = $contenido['detalle_de_dni_y_firma_digital'] ?? [];
            $capacitacion= $contenido['detalles_de_capacitacion'] ?? [];
            $soporte     = $contenido['soporte'] ?? [];
            $equipos     = $contenido['equipos_de_computo'] ?? [];
            $evidencias  = $contenido['comentarios_y_evidencias'] ?? [];
        @endphp

        <div class="module-container">
            <div class="module-header">{{ $modulo->modulo_nombre }}</div>

            <table class="data-table" style="margin-bottom: 0;">
                <tr>
                    <td width="50%" style="border:none; padding: 0 5px 0 0;">
                        <div class="section-title">Detalle del Consultorio</div>
                        <table class="data-table">
                            <tr><th>Fecha Monitoreo</th><td>{{ $consultorio['fecha_monitoreo'] ?? '-' }}</td></tr>
                            <tr><th>Turno</th><td>{{ $consultorio['turno'] ?? '-' }}</td></tr>
                            <tr><th>N° Consultorios</th><td>{{ $consultorio['num_consultorios'] ?? '-' }}</td></tr>
                            <tr><th>Denominación</th><td>{{ $consultorio['denominacion'] ?? '-' }}</td></tr>
                        </table>
                    </td>
                    <td width="50%" style="border:none; padding: 0 0 0 5px;">
                        <div class="section-title">Datos del Profesional</div>
                        <table class="data-table">
                            <tr><th>Profesional</th><td>{{ $profesional['apellido_paterno'] ?? '' }} {{ $profesional['apellido_materno'] ?? '' }}, {{ $profesional['nombres'] ?? '' }}</td></tr>
                            <tr><th>Documento</th><td><span class="badge">{{ $profesional['tipo_doc'] ?? 'DNI' }}</span> {{ $profesional['doc'] ?? '-' }}</td></tr>
                            <tr><th>Cargo</th><td>{{ $profesional['cargo'] ?? '-' }}</td></tr>
                            <tr><th>Contacto</th><td>{{ $profesional['email'] ?? '-' }} <br> {{ $profesional['telefono'] ?? '' }}</td></tr>
                        </table>
                    </td>
                </tr>
            </table>

            <div class="section-title">Documentación y Firma Digital</div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Utiliza SIHCE</th>
                        <th>Firmó DJ</th>
                        <th>Firmó Confidencialidad</th>
                        <th>Firma Digital Activa</th>
                        <th>Tipo DNI</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-center">{{ $admin['utiliza_sihce'] ?? '-' }}</td>
                        <td class="text-center">{{ $admin['firmo_dj'] ?? '-' }}</td>
                        <td class="text-center">{{ $admin['firmo_confidencialidad'] ?? '-' }}</td>
                        <td class="text-center">{{ $dni['firma_digital_sihce'] ?? '-' }}</td>
                        <td class="text-center">{{ $dni['tipo_dni'] ?? '-' }}</td>
                    </tr>
                </tbody>
            </table>
            @if(!empty($dni['observaciones_dni']))
            <div style="background: #f8fafc; padding: 4px; border: 1px dashed #cbd5e1; font-size: 9px; margin-bottom: 5px;">
                <strong>Obs. DNI:</strong> {{ $dni['observaciones_dni'] }}
            </div>
            @endif
            
            @if(count($equipos) > 0)
                <div class="section-title">Equipos de Cómputo Reportados</div>
                <table class="table-equipos">
                    <thead><tr><th>Descripción</th><th>Cant.</th><th>Estado</th><th>Propiedad</th><th>Serie</th></tr></thead>
                    <tbody>
                        @foreach($equipos as $eq)
                            <tr>
                                <td class="text-left">{{ $eq['descripcion'] ?? '-' }}</td>
                                <td>{{ $eq['cantidad'] ?? '1' }}</td>
                                <td>{{ $eq['estado'] ?? '-' }}</td>
                                <td>{{ $eq['propio'] ?? ($eq['propiedad'] ?? '-') }}</td>
                                <td style="font-family: monospace;">{{ $eq['nro_serie'] ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif

            @if(!empty($evidencias['comentarios']))
                <div class="section-title">Observaciones / Comentarios</div>
                <div style="background: #fff; padding: 5px; border: 1px solid #ddd; font-style: italic;">
                    {{ $evidencias['comentarios'] }}
                </div>
            @endif
        </div>
    @empty
        <div style="padding: 20px; text-align: center; border: 1px dashed #999; color: #777;">
            No se han registrado módulos especializados para esta acta.
        </div>
    @endforelse

    {{-- PANEL FOTOGRÁFICO --}}
    @if($acta->foto1 || $acta->foto2)
        <div class="module-header" style="margin-top: 25px;">PANEL FOTOGRÁFICO DE EVIDENCIAS</div>
        <div class="photo-section">
            <div class="photo-grid">
                @if($acta->foto1)
                    @php 
                        $path1 = storage_path('app/public/' . $acta->foto1);
                        if(!file_exists($path1)) $path1 = public_path('storage/' . $acta->foto1);
                    @endphp
                    @if(file_exists($path1))
                        <div class="photo-container"><img src="{{ $path1 }}" class="photo-img"></div>
                    @endif
                @endif

                @if($acta->foto2)
                    @php 
                        $path2 = storage_path('app/public/' . $acta->foto2);
                        if(!file_exists($path2)) $path2 = public_path('storage/' . $acta->foto2);
                    @endphp
                    @if(file_exists($path2))
                        <div class="photo-container"><img src="{{ $path2 }}" class="photo-img"></div>
                    @endif
                @endif
            </div>
        </div>
    @endif

    {{-- FIRMAS (Espaciado Ajustado) --}}
    <div class="module-header" style="margin-top: 25px;">FIRMAS DE CONFORMIDAD</div>
    
    <div class="signatures-section">
        <table class="signatures-table">
            <tr>
                <td width="48%">
                    <div class="signature-box">
                        <div class="signature-line"></div>
                        <div class="signature-name">{{ $monitor['nombre'] }}</div>
                        <div class="signature-role">IMPLEMENTADOR</div>
                    </div>
                </td>
                <td width="4%"></td>
                <td width="48%">
                    <div class="signature-box">
                        <div class="signature-line"></div>
                        <div class="signature-name">{{ $jefe['nombre'] }}</div>
                        <div class="signature-role">{{ $jefe['cargo'] }}</div>
                    </div>
                </td>
            </tr>
        </table>

        <table class="signatures-table">
            <tr>
                <td width="25%"></td>
                <td width="50%">
                    <div class="signature-box">
                        <div class="signature-line"></div>
                        <div class="signature-name">{{ $monitor['nombre'] }}</div> 
                        <div class="signature-role">DIRESA</div>
                    </div>
                </td>
                <td width="25%"></td>
            </tr>
        </table>
    </div>

</body>
</html>