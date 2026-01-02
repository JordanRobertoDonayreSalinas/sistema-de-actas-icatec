<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Odontología</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #4f46e5; padding-bottom: 10px; }
        .header h1 { color: #4f46e5; margin: 0; text-transform: uppercase; font-size: 18px; }
        .header p { margin: 2px 0; color: #666; }
        
        .section-title { 
            background-color: #f3f4f6; 
            color: #1f2937; 
            padding: 8px; 
            font-weight: bold; 
            text-transform: uppercase; 
            margin-top: 15px; 
            border-left: 4px solid #4f46e5;
            font-size: 12px;
        }

        .info-grid { width: 100%; margin-top: 10px; border-collapse: collapse; }
        .info-grid td { padding: 5px; vertical-align: top; }
        .label { font-weight: bold; color: #6b7280; display: block; font-size: 9px; text-transform: uppercase; }
        .value { font-weight: bold; color: #111; }

        /* Tablas de datos */
        .table { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 10px; }
        .table th { background-color: #4f46e5; color: white; padding: 6px; text-align: left; text-transform: uppercase; }
        .table td { border-bottom: 1px solid #e5e7eb; padding: 6px; }
        .table tr:nth-child(even) { background-color: #f9fafb; }

        /* Fotos */
        .gallery { 
            margin-top: 15px;
            width: 100%;
            text-align: center; /* Esto centra las fotos si hay una sola */
        }

        .photo-container { 
            display: inline-block; 
            width: 45%;           /* Casi la mitad del ancho para que entren dos */
            margin: 1%;           /* Espacio entre fotos */
            vertical-align: top; 
            background-color: #fff;
            padding: 5px;
            border: 1px solid #ddd;
            border-radius: 6px;
        }

        .photo { 
            width: 100%;          /* Ocupa todo el contenedor */
            height: 250px;        /* Altura más grande (antes era 120px) */
            object-fit: contain;  /* Muestra toda la foto sin recortarla */
            display: block;
        }
    </style>
</head>
<body>

    {{-- ENCABEZADO --}}
    <div class="header">
        <h1>Ficha de Monitoreo - Odontología</h1>
        <p>Acta N° {{ str_pad($acta->id, 5, '0', STR_PAD_LEFT) }} | Fecha: {{ $acta->created_at->format('d/m/Y') }}</p>
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

    {{-- 2. CAPACITACIÓN --}}
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
    </table>

    {{-- 3. INICIO DE LABORES (NUEVO) --}}
    <div class="section-title">3. Inicio de Labores</div>
    <table class="info-grid">
        <tr>
            <td><span class="label">N° Consultorios</span> <span class="value">{{ $dbInicioLabores->cant_consultorios ?? '-' }}</span></td>
            <td><span class="label">Tipo FUA</span> <span class="value">{{ str_replace('_', ' ', $dbInicioLabores->fua ?? '-') }}</span></td>
            <td><span class="label">Tipo Referencia</span> <span class="value">{{ $dbInicioLabores->referencia ?? '-' }}</span></td>
        </tr>
        <tr>
            <td><span class="label">Emisión Receta</span> <span class="value">{{ $dbInicioLabores->receta ?? '-' }}</span></td>
            <td colspan="2"><span class="label">Orden Laboratorio</span> <span class="value">{{ $dbInicioLabores->orden_laboratorio ?? '-' }}</span></td>
        </tr>
    </table>

    {{-- 4. SECCIÓN DNI (NUEVO) --}}
    <div class="section-title">4. Identidad Digital (DNI)</div>
    <table class="info-grid">
        <tr>
            <td><span class="label">Tipo de Documento</span> <span class="value">{{ str_replace('_', ' ', $dbDni->tip_dni ?? '-') }}</span></td>
            <td><span class="label">Versión DNIe</span> <span class="value">{{ $dbDni->version_dni ?? 'N/A' }}</span></td>
            <td><span class="label">Firma en SIHCE</span> <span class="value">{{ $dbDni->firma_sihce ?? 'N/A' }}</span></td>
        </tr>
        @if(!empty($dbDni->comentarios))
        <tr>
            <td colspan="3">
                <span class="label">Observaciones DNI</span>
                <span class="value">{{ $dbDni->comentarios }}</span>
            </td>
        </tr>
        @endif
    </table>

    {{-- 5. INVENTARIO --}}
    <div class="section-title">5. Inventario de Equipamiento</div>
    <table class="table">
        <thead>
            <tr>
                <th>Descripción</th>
                <th>Propiedad</th>
                <th>Estado</th>
                <th>Cod. Barras</th>
                <th>Observación</th>
            </tr>
        </thead>
        <tbody>
            @forelse($dbInventario as $item)
                <tr>
                    <td>{{ $item->descripcion }}</td>
                    <td>{{ $item->propiedad }}</td>
                    <td>{{ $item->estado }}</td>
                    <td>{{ $item->cod_barras ?? '-' }}</td>
                    <td>{{ $item->observaciones }}</td>
                </tr>
            @empty
                <tr><td colspan="5" style="text-align:center; padding:10px;">Sin equipamiento registrado</td></tr>
            @endforelse
        </tbody>
    </table>
    @if(count($dbInventario) > 0 && !empty($dbInventario[0]->comentarios))
        <div style="margin-top:5px; font-style:italic; font-size:10px; color:#666;">
            <strong>Comentarios Generales:</strong> {{ $dbInventario[0]->comentarios }}
        </div>
    @endif

    {{-- 6. DIFICULTADES --}}
    <div class="section-title">6. Dificultades con el Sistema</div>
    <table class="info-grid">
        <tr>
            <td><span class="label">Institución Coordina</span> <span class="value">{{ $dbDificultad->insti_comunica ?? '-' }}</span></td>
            <td><span class="label">Medio Comunicación</span> <span class="value">{{ $dbDificultad->medio_comunica ?? '-' }}</span></td>
        </tr>
    </table>

    {{-- 7. FOTOS --}}
    <div class="section-title">7. Evidencia Fotográfica</div>
    <div class="gallery">
        @forelse($dbFotos as $foto)
            <div class="photo-container">
                <img src="{{ public_path('storage/' . $foto->url_foto) }}" class="photo">
            </div>
        @empty
            <p style="padding:10px; color:#999;">No hay evidencia fotográfica adjunta.</p>
        @endforelse
    </div>

    {{-- 8. FIRMAS --}}
    <div class="section-title">8. Firmas</div>
    
    {{-- Usamos una tabla para centrar todo perfectamente en el PDF --}}
    <table style="width: 100%; margin-top: 80px;"> {{-- margin-top da espacio para el garabato de la firma --}}
        <tr>
            <td style="text-align: center;">
                {{-- Esta caja div crea la línea de la firma --}}
                <div style="width: 250px; margin: 0 auto; border-top: 1px solid #333; padding-top: 5px;">
                    
                    {{-- Nombre del Profesional --}}
                    <div class="value" style="text-transform: uppercase; font-size: 10px;">
                        {{ $dbCapacitacion->profesional->apellido_paterno }} 
                        {{ $dbCapacitacion->profesional->apellido_materno }}, 
                        {{ $dbCapacitacion->profesional->nombres }}
                    </div>

                    {{-- DNI / Documento --}}
                    <div style="font-size: 9px; color: #666; margin-top: 2px;">
                        {{ $dbCapacitacion->profesional->tipo_doc }}: {{ $dbCapacitacion->profesional->doc }}
                    </div>

                    {{-- Cargo --}}
                    <div style="font-weight: bold; font-size: 10px; margin-top: 4px;">
                        ODONTOLOGO
                    </div>

                </div>
            </td>
        </tr>
    </table>

</body>
</html>