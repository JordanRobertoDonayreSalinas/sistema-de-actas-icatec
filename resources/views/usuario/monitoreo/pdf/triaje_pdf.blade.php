<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Triaje</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; color: #333; }
        
        /* Encabezado */
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #4f46e5; padding-bottom: 10px; }
        .header h1 { color: #4f46e5; margin: 0; text-transform: uppercase; font-size: 18px; }
        .header p { margin: 2px 0; color: #666; }
        
        /* Títulos de Sección */
        .section-title { 
            background-color: #f3f4f6; 
            color: #1f2937; 
            padding: 8px; 
            font-weight: bold; 
            text-transform: uppercase; 
            margin-top: 20px; 
            border-left: 4px solid #4f46e5;
            font-size: 12px;
        }

        /* Grillas de Información */
        .info-grid { width: 100%; margin-top: 10px; border-collapse: collapse; }
        .info-grid td { padding: 5px; vertical-align: top; }
        .label { font-weight: bold; color: #6b7280; display: block; font-size: 9px; text-transform: uppercase; }
        .value { font-weight: bold; color: #111; }

        /* Tablas de datos (Inventario) */
        .table { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 10px; }
        .table th { background-color: #4f46e5; color: white; padding: 6px; text-align: left; text-transform: uppercase; }
        .table td { border-bottom: 1px solid #e5e7eb; padding: 6px; }
        .table tr:nth-child(even) { background-color: #f9fafb; }

        /* Galería de Fotos (Mejorada: Grandes y Centradas) */
        .gallery { 
            margin-top: 15px;
            width: 100%;
            text-align: center;
        }
        .photo-container { 
            display: inline-block; 
            width: 45%;           /* 2 fotos por fila */
            margin: 1%; 
            vertical-align: top; 
            background-color: #fff;
            padding: 5px;
            border: 1px solid #ddd;
            border-radius: 6px;
        }
        .photo { 
            width: 100%; 
            height: 250px;        /* Altura fija para uniformidad */
            object-fit: contain;  /* Muestra toda la foto sin recortar */
            display: block;
        }
    </style>
</head>
<body>

    {{-- ENCABEZADO --}}
    <div class="header">
        <h1>Ficha de Monitoreo - Triaje</h1>
        <p>
            Acta N° {{ str_pad($acta->id, 5, '0', STR_PAD_LEFT) }} | 
            {{-- CORRECCIÓN AQUÍ: Usamos el campo 'fecha' de la tabla --}}
            Fecha: {{ \Carbon\Carbon::parse($acta->fecha)->format('d/m/Y') }}
        </p>
        <p>
            {{ $acta->establecimiento->codigo ?? 'S/C' }} - {{ $acta->establecimiento->nombre ?? 'Establecimiento Desconocido' }}
        </p>
    </div>

    {{-- 1. DATOS DEL PROFESIONAL --}}
    <div class="section-title">1. Datos del Profesional Responsable</div>
    @if($dbCapacitacion && $dbCapacitacion->profesional)
        <table class="info-grid">
            <tr>
                <td><span class="label">Nombre Completo</span> <span class="value">{{ $dbCapacitacion->profesional->apellido_paterno }} {{ $dbCapacitacion->profesional->apellido_materno }}, {{ $dbCapacitacion->profesional->nombres }}</span></td>
                <td><span class="label">Documento</span> <span class="value">{{ $dbCapacitacion->profesional->tipo_doc }}: {{ $dbCapacitacion->profesional->doc }}</span></td>
                <td><span class="label">Contacto</span> <span class="value">{{ $dbCapacitacion->profesional->telefono }}</span></td>
            </tr>
            <tr>
                <td colspan="3"><span class="label">Email</span> <span class="value">{{ $dbCapacitacion->profesional->email }}</span></td>
            </tr>
        </table>
    @else
        <p style="padding:10px; color:#999;">No se registró información del profesional.</p>
    @endif

    {{-- 2. CAPACITACIÓN (ACTUALIZADA) --}}
    <div class="section-title">2. Capacitación</div>
    <table class="info-grid">
        <tr>
            <td width="50%">
                <span class="label">¿Recibió Capacitación?</span> 
                <span class="value">{{ $dbCapacitacion->recibieron_cap ?? '-' }}</span>
            </td>
            <td>
                <span class="label">Entidad</span> 
                <span class="value">{{ $dbCapacitacion->institucion_cap ?? 'N/A' }}</span>
            </td>
        </tr>
        {{-- NUEVA FILA: Declaración y Compromiso --}}
        <tr>
            <td>
                <span class="label">Declaración Jurada</span> 
                <span class="value">{{ $dbCapacitacion->decl_jurada ?? '-' }}</span>
            </td>
            <td>
                <span class="label">Compromiso Confidencialidad</span> 
                <span class="value">{{ $dbCapacitacion->comp_confidencialidad ?? '-' }}</span>
            </td>
        </tr>
    </table>

    {{-- 3. INVENTARIO (Ahora leyendo de EquipoComputo) --}}
    <div class="section-title">3. Inventario de Equipamiento</div>
    <table class="table">
        <thead>
            <tr>
                <th>Descripción</th>
                <th>Propiedad</th>
                <th>Estado</th>
                <th>Nro. Serie</th>
                <th>Observación</th>
            </tr>
        </thead>
        <tbody>
            @forelse($dbInventario as $item)
                <tr>
                    <td>{{ $item->descripcion }}</td>
                    {{-- Cambiado a 'propio' --}}
                    <td>{{ $item->propio }}</td>
                    <td>{{ $item->estado }}</td>
                    {{-- Cambiado a 'nro_serie' --}}
                    <td>{{ $item->nro_serie ?? '-' }}</td>
                    {{-- Cambiado a 'observacion' --}}
                    <td>{{ $item->observacion }}</td>
                </tr>
            @empty
                <tr><td colspan="5" style="text-align:center; padding:10px;">Sin equipamiento registrado</td></tr>
            @endforelse
        </tbody>
    </table>
    

    {{-- 4. DIFICULTADES --}}
    <div class="section-title">4. Dificultades con el Sistema</div>
    <table class="info-grid">
        <tr>
            <td><span class="label">Institución Coordina</span> <span class="value">{{ $dbDificultad->insti_comunica ?? '-' }}</span></td>
            <td><span class="label">Medio Comunicación</span> <span class="value">{{ $dbDificultad->medio_comunica ?? '-' }}</span></td>
        </tr>
    </table>

    {{-- 5. FOTOS --}}
    <div class="section-title">5. Evidencia Fotográfica</div>
    <div class="gallery">
        @forelse($dbFotos as $foto)
            <div class="photo-container">
                <img src="{{ public_path('storage/' . $foto->url_foto) }}" class="photo">
            </div>
        @empty
            <p style="padding:10px; color:#999; text-align: center;">No hay evidencia fotográfica adjunta.</p>
        @endforelse
    </div>

    {{-- 6. FIRMAS --}}
    <div class="section-title">6. Firmas</div>
    
    {{-- Tabla de firma centrada con línea --}}
    <table style="width: 100%; margin-top: 80px;">
        <tr>
            <td style="text-align: center;">
                <div style="width: 250px; margin: 0 auto; border-top: 1px solid #333; padding-top: 5px;">
                    
                    @if($dbCapacitacion && $dbCapacitacion->profesional)
                        <div class="value" style="text-transform: uppercase; font-size: 10px;">
                            {{ $dbCapacitacion->profesional->apellido_paterno }} 
                            {{ $dbCapacitacion->profesional->apellido_materno }}, 
                            {{ $dbCapacitacion->profesional->nombres }}
                        </div>
                        <div style="font-size: 9px; color: #666; margin-top: 2px;">
                            {{ $dbCapacitacion->profesional->tipo_doc }}: {{ $dbCapacitacion->profesional->doc }}
                        </div>
                    @else
                        <div class="value">PROFESIONAL NO REGISTRADO</div>
                    @endif

                    <div style="font-weight: bold; font-size: 10px; margin-top: 4px;">
                        RESPONSABLE DE TRIAJE
                    </div>

                </div>
            </td>
        </tr>
    </table>

</body>
</html>