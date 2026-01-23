<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>REPORTE MÓDULO 4.1 - TRIAJE</title>
    <style>
        /* MÁRGENES: 2.5cm abajo para reservar espacio al pie de página */
        @page { margin: 1.2cm 1.5cm 2.5cm 1.5cm; }
        body { font-family: 'Helvetica', Arial, sans-serif; font-size: 10px; color: #1e293b; line-height: 1.4; }
        
        /* ENCABEZADO */
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #009688; padding-bottom: 10px; }
        .header h1 { margin: 0; font-size: 16px; text-transform: uppercase; color: #00796b; font-weight: bold; }
        .acta-info { font-size: 10px; color: #64748b; font-weight: bold; text-transform: uppercase; margin-top: 5px; }

        /* TÍTULOS DE SECCIÓN */
        .section-title { 
            background-color: #e0f2f1; 
            padding: 6px 10px; 
            font-weight: bold; 
            text-transform: uppercase; 
            border-left: 4px solid #009688; 
            margin-top: 15px; 
            margin-bottom: 5px; 
            font-size: 10px; 
            color: #004d40;
        }

        /* TABLAS */
        table { width: 100%; border-collapse: collapse; table-layout: fixed; margin-bottom: 5px; }
        th, td { border: 1px solid #cfd8dc; padding: 6px 8px; text-align: left; vertical-align: middle; word-wrap: break-word; }
        th { background-color: #f1f8e9; color: #37474f; font-size: 8.5px; text-transform: uppercase; font-weight: bold; }
        
        /* COLUMNA ETIQUETA */
        .bg-label { background-color: #f5f5f5; font-weight: bold; width: 35%; text-transform: uppercase; color: #455a64; font-size: 9px; }
        .uppercase { text-transform: uppercase; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }

        /* TABLA EQUIPOS */
        .equipos-table th { background-color: #009688; color: white; text-align: center; border: 1px solid #00796b; }
        .equipos-table td { font-size: 9px; }

        /* EVIDENCIA */
        .foto-container { 
            margin: 15px auto; padding: 10px; border: 1px solid #e2e8f0; 
            background-color: #ffffff; text-align: center; display: table; width: auto; 
        }
        .foto { display: block; margin: 0 auto; max-width: 100%; max-height: 250px; width: auto; height: auto; object-fit: contain; }
        .no-evidence { border: 2px dashed #cbd5e1; border-radius: 10px; padding: 20px; text-align: center; color: #64748b; font-style: italic; background-color: #f8fafc; margin: 10px 0; }

        /* FIRMA */
        .firma-section { margin-top: 40px; page-break-inside: avoid; }
        .firma-container { width: 100%; margin-top: 10px; }
        .firma-box { width: 45%; text-align: center; display: inline-block; vertical-align: top; margin: 0 2%; }
        .firma-linea { border-bottom: 1px solid #000; margin-bottom: 5px; width: 80%; margin-left: 10%; }
        .firma-nombre { font-weight: bold; font-size: 10px; text-transform: uppercase; margin-top: 5px; }
        .firma-label { font-size: 9px; color: #64748b; }
        
        /* BADGES */
        .badge { padding: 2px 5px; border-radius: 3px; font-size: 8px; color: white; font-weight: bold; display: inline-block; }
        .bg-operativo { background-color: #2e7d32; }
        .bg-inoperativo { background-color: #c62828; }
        .bg-neutral { background-color: #78909c; }
    </style>
</head>
<body>

    {{-- ENCABEZADO --}}
    <div class="header">
        <h1>MÓDULO: TRIAJE (CSMC)</h1>
        <div class="acta-info">
            ACTA N° {{ str_pad($monitoreo->numero_acta ?? $monitoreo->id, 5, '0', STR_PAD_LEFT) }} | 
            ESTABLECIMIENTO: {{ $monitoreo->establecimiento->codigo }} - {{ strtoupper($monitoreo->establecimiento->nombre) }} | 
            FECHA: {{ \Carbon\Carbon::parse($monitoreo->fecha)->format('d/m/Y') }}
        </div>
    </div>

    {{-- 1. DETALLES DEL CONSULTORIO --}}
    <div class="section-title">1. DETALLES DEL CONSULTORIO</div>
    <table>
        <tbody>
            <tr>
                <td class="bg-label">Turno Evaluado</td>
                <td class="uppercase">{{ $data['turno'] ?? '---' }}</td>
            </tr>
            <tr>
                <td class="bg-label">Nro. de Consultorios</td>
                <td class="uppercase">{{ $data['num_ambientes'] ?? '---' }}</td>
            </tr>
            <tr>
                <td class="bg-label">Denominación del Ambiente</td>
                <td class="uppercase">{{ $data['denominacion_ambiente'] ?? '---' }}</td>
            </tr>
        </tbody>
    </table>

    {{-- 2. DATOS DEL PROFESIONAL --}}
    <div class="section-title">2. DATOS DEL PROFESIONAL</div>
    <table>
        <tbody>
            <tr>
                <td class="bg-label">Apellidos y Nombres</td>
                <td class="uppercase">
                    {{ $data['rrhh']['apellido_paterno'] ?? '' }} 
                    {{ $data['rrhh']['apellido_materno'] ?? '' }} 
                    {{ $data['rrhh']['nombres'] ?? '' }}
                </td>
            </tr>
            <tr>
                <td class="bg-label">{{ $data['rrhh']['tipo_doc'] ?? 'DOCUMENTO DE IDENTIDAD' }}</td>
                <td>{{ $data['rrhh']['doc'] ?? '---' }}</td>
            </tr>
            <tr>
                <td class="bg-label">Cargo / Función</td>
                <td class="uppercase">{{ $data['rrhh']['cargo'] ?? '---' }}</td>
            </tr>
            <tr>
                <td class="bg-label">Contacto</td>
                <td class="uppercase">
                    @if(!empty($data['rrhh']['email'])) ✉ {{ $data['rrhh']['email'] }} <br> @endif
                    @if(!empty($data['rrhh']['telefono'])) 📞 {{ $data['rrhh']['telefono'] }} @endif
                </td>
            </tr>
        </tbody>
    </table>

    {{-- 3. GESTIÓN ADMINISTRATIVA --}}
    <div class="section-title">3. GESTIÓN ADMINISTRATIVA</div>
    <table>
        <tbody>
            <tr>
                <td class="bg-label">¿Utiliza SIHCE?</td>
                <td class="uppercase">{{ $data['cuenta_sihce'] ?? '---' }}</td>
            </tr>
            @if(($data['cuenta_sihce'] ?? '') == 'SI')
            <tr>
                <td class="bg-label">¿Firmó Declaración Jurada?</td>
                <td class="uppercase">{{ $data['firmo_dj'] ?? '---' }}</td>
            </tr>
            <tr>
                <td class="bg-label">¿Firmó Confidencialidad?</td>
                <td class="uppercase">{{ $data['firmo_confidencialidad'] ?? '---' }}</td>
            </tr>
            @else
            <tr>
                <td class="bg-label">Documentación</td>
                <td style="color: #64748b;">NO APLICA (NO USA SIHCE)</td>
            </tr>
            @endif
        </tbody>
    </table>

    {{-- 4. DETALLE DNI ELECTRÓNICO --}}
    @if(isset($data['tipo_dni']) && $data['tipo_dni'] != '')
    <div class="section-title">4. DETALLE DNI Y FIRMA DIGITAL</div>
    <table>
        <tbody>
            <tr>
                <td class="bg-label">Tipo de Documento</td>
                <td class="uppercase">{{ $data['tipo_dni'] }}</td>
            </tr>
            @if($data['tipo_dni'] == 'ELECTRONICO')
            <tr>
                <td class="bg-label">Versión DNIe</td>
                <td class="uppercase">{{ $data['version_dnie'] ?? '---' }}</td>
            </tr>
            <tr>
                <td class="bg-label">¿Firma Digitalmente?</td>
                <td class="uppercase">{{ $data['firma_digital_sihce'] ?? '---' }}</td>
            </tr>
            @endif
            <tr>
                <td class="bg-label">Observaciones DNI</td>
                <td class="uppercase">{{ $data['observaciones_dni'] ?? 'NINGUNA' }}</td>
            </tr>
        </tbody>
    </table>
    @endif

    {{-- 5. CAPACITACIÓN --}}
    <div class="section-title">5. CAPACITACIÓN</div>
    <table>
        <tbody>
            <tr>
                <td class="bg-label">¿Recibió Capacitación?</td>
                <td class="uppercase">{{ $data['recibio_capacitacion'] ?? '---' }}</td>
            </tr>
            @if(($data['recibio_capacitacion'] ?? '') == 'SI')
            <tr>
                <td class="bg-label">Entidad Capacitadora</td>
                <td class="uppercase">{{ $data['inst_que_lo_capacito'] ?? '---' }}</td>
            </tr>
            @endif
        </tbody>
    </table>

    {{-- 6. EQUIPAMIENTO BIOMÉDICO Y MOBILIARIO --}}
    <div class="section-title">6. EQUIPAMIENTO BIOMÉDICO Y MOBILIARIO</div>
    @if(isset($data['equipos']) && count($data['equipos']) > 0)
        <table class="equipos-table">
            <thead>
                <tr>
                    <th width="5%">#</th>
                    <th width="45%">DESCRIPCIÓN</th>
                    <th width="15%">ESTADO</th>
                    <th width="10%">CANT.</th>
                    <th width="25%">OBSERVACIÓN</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data['equipos'] as $item)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td class="uppercase">{{ $item['descripcion'] ?? '-' }}</td>
                    <td class="text-center">
                        @php $estado = strtoupper($item['estado'] ?? ''); @endphp
                        @if($estado == 'OPERATIVO') <span class="badge bg-operativo">OPERATIVO</span>
                        @elseif($estado == 'INOPERATIVO') <span class="badge bg-inoperativo">INOPERATIVO</span>
                        @else <span class="badge bg-neutral">{{ $estado ?: '-' }}</span>
                        @endif
                    </td>
                    <td class="text-center font-bold">{{ $item['cantidad'] ?? '1' }}</td>
                    <td class="uppercase">{{ $item['observacion'] ?? '' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div style="color: #94a3b8; font-style: italic; padding: 10px; border: 1px solid #e2e8f0; text-align: center;">NO SE REGISTRARON EQUIPOS</div>
    @endif

    {{-- 7. SOPORTE --}}
    <div class="section-title">7. SOPORTE Y COMUNICACIÓN</div>
    <table>
        <tbody>
            <tr>
                <td class="bg-label">Comunica dificultades a</td>
                <td class="uppercase">{{ $data['inst_a_quien_comunica'] ?? '---' }}</td>
            </tr>
            <tr>
                <td class="bg-label">Medio utilizado</td>
                <td class="uppercase">{{ $data['medio_que_utiliza'] ?? '---' }}</td>
            </tr>
        </tbody>
    </table>

    {{-- 8. COMENTARIOS --}}
    <div class="section-title">8. COMENTARIOS / OBSERVACIONES</div>
    <div style="border: 1px solid #cfd8dc; padding: 10px; min-height: 40px; text-transform: uppercase; font-size: 10px; background-color: #fff;">
        {{ $data['comentarios'] ?? 'SIN COMENTARIOS REGISTRADOS.' }}
    </div>

    {{-- 9. EVIDENCIA FOTOGRÁFICA --}}
    <div class="section-title">9. EVIDENCIA FOTOGRÁFICA</div>
    @php
        $fotoPath = $data['foto_evidencia'] ?? null;
        if(is_array($fotoPath)) $fotoPath = $fotoPath[0] ?? null;
    @endphp

    @if($fotoPath && file_exists(public_path('storage/' . $fotoPath)))
        <div class="foto-container">
            <img src="{{ public_path('storage/' . $fotoPath) }}" class="foto">
        </div>
    @else
        <div class="no-evidence">
            No se adjuntó evidencia fotográfica.
        </div>
    @endif

    {{-- 10. FIRMAS --}}
    <div class="firma-section">
        <div class="firma-container">
            {{-- FIRMA MONITOR --}}
            <div class="firma-box">
                <div style="height: 40px;"></div> {{-- Espacio para firma --}}
                <div class="firma-linea"></div>
                <div class="firma-nombre">{{ $monitoreo->user->name ?? 'MONITOR' }}</div>
                <div class="firma-label">MONITOR / SUPERVISOR</div>
            </div>

            {{-- FIRMA RESPONSABLE TRIAJE --}}
            <div class="firma-box">
                <div style="height: 40px;"></div> {{-- Espacio para firma --}}
                <div class="firma-linea"></div>
                <div class="firma-nombre">
                    {{ $data['rrhh']['nombres'] ?? '' }} 
                    {{ $data['rrhh']['apellido_paterno'] ?? '' }}
                </div>
                <div class="firma-label">RESPONSABLE DE TRIAJE</div>
                <div class="firma-label">{{ $data['rrhh']['tipo_doc'] ?? 'DNI' }}: {{ $data['rrhh']['doc'] ?? '________' }}</div>
            </div>
        </div>
    </div>

</body>
</html>