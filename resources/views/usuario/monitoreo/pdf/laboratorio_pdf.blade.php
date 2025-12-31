<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Laboratorio - Acta {{ $acta->id }}</title>
    <style>
        @page { margin: 1.2cm 1.5cm; }
        body { font-family: 'Helvetica', sans-serif; font-size: 10px; color: #1e293b; line-height: 1.4; }
        .header { text-align: center; margin-bottom: 15px; border-bottom: 2px solid #4f46e5; padding-bottom: 10px; }
        .header h1 { margin: 0; font-size: 16px; text-transform: uppercase; color: #4f46e5; }
        .section-title { background-color: #f1f5f9; padding: 6px 10px; font-weight: bold; text-transform: uppercase; border-left: 4px solid #4f46e5; margin-top: 15px; margin-bottom: 5px; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; table-layout: fixed; margin-bottom: 5px; }
        th, td { border: 1px solid #e2e8f0; padding: 6px 8px; text-align: left; vertical-align: middle; word-wrap: break-word; }
        th { background-color: #f8fafc; color: #475569; font-size: 8.5px; text-transform: uppercase; }
        .bg-label { background-color: #f8fafc; font-weight: bold; width: 35%; }
        .uppercase { text-transform: uppercase; }
        .text-center { text-align: center; }
        
        /* Imagen y Evidencia */
        .container-evidencia { text-align: center; margin-top: 10px; padding: 10px; border: 1px solid #e2e8f0; border-radius: 12px; background-color: #fcfcfc; }
        .evidencia-img { max-width: 100%; max-height: 280px; border-radius: 6px; }
        .no-evidencia { padding: 20px; border: 2px dashed #cbd5e1; color: #94a3b8; border-radius: 12px; font-style: italic; }

        /* SECCIÓN DE FIRMAS */
        .firmas-wrapper { margin-top: 10px; width: 100%; text-align: center; }
        .firma-recuadro { 
            width: 50%; 
            border: 1px solid #94a3b8; 
            border-radius: 10px; 
            display: inline-block; 
            padding: 15px 10px;
            text-align: center;
            background-color: #ffffff;
        }
        .firma-espacio { height: 75px; margin-bottom: 10px; }
        .firma-linea-firmar { border-top: 1px solid #1e293b; margin: 0 20px; padding-top: 5px; }
        .firma-nombre { font-weight: bold; font-size: 9px; text-transform: uppercase; margin-top: 5px; line-height: 1.1; }
        .firma-info { font-size: 8px; color: #475569; text-transform: uppercase; margin-top: 2px; }
    </style>
</head>
<body>

    <div class="header">
        <h1>Módulo 17: Laboratorio Clínico</h1>
        <div style="font-weight: bold; color: #64748b; font-size: 10px;">
            ACTA N° {{ str_pad($acta->id, 5, '0', STR_PAD_LEFT) }} | 
            E.E.S.S.: {{ strtoupper($acta->establecimiento->nombre) }}
        </div>
    </div>

    <div class="section-title">1. Profesional de Laboratorio</div>
    <table>
        <tr>
            <td class="bg-label">Apellidos y Nombres:</td>
            <td class="uppercase">
                {{ ($detalle->contenido['responsable']['apellido_paterno'] ?? '') }} 
                {{ ($detalle->contenido['responsable']['apellido_materno'] ?? '') }},
                {{ ($detalle->contenido['responsable']['nombres'] ?? '') }}
            </td>
        </tr>
        <tr>
            <td class="bg-label">{{ ($detalle->contenido['responsable']['tipo_doc'] ?? 'DNI') }}:</td>
            <td>{{ $detalle->contenido['responsable']['doc'] ?? '---' }}</td>
        </tr>
    </table>

    <div class="section-title">2. Acceso y Uso de Sistema</div>
    <table>
        <tr>
            <td class="bg-label">¿Cuenta con Usuario?</td>
            <td>{{ strtoupper($detalle->contenido['acceso_sistema'] ?? 'NO') }}</td>
        </tr>
    </table>

    <div class="section-title">3. Capacitación y Comunicación del Personal</div>
    <table>
        <tr>
            <td class="bg-label">¿Recibió capacitación técnica?</td>
            <td>{{ strtoupper($detalle->contenido['recibio_capacitacion'] ?? 'NO') }}</td>
        </tr>
        <tr>
            <td class="bg-label">Entidad que lo capacitó:</td>
            <td class="uppercase">{{ $detalle->contenido['inst_que_lo_capacito'] ?? '---' }}</td>
        </tr>
        <tr>
            <td class="bg-label">¿A quién comunica en caso de tener alguna dificultad?</td>
            <td class="uppercase">{{ $detalle->contenido['inst_a_quien_comunica'] ?? '---' }}</td>
        </tr>
        <tr>
            <td class="bg-label">¿Medio que utiliza para comunicarse?</td>
            <td class="uppercase">{{ $detalle->contenido['medio_que_utiliza'] ?? '---' }}</td>
        </tr>
    </table>

    <div class="section-title">4. Inventario de Equipamiento Tecnológico</div>
    <table>
        <thead>
            <tr>
                <th width="30%">Descripción</th>
                <th width="20%">N° Serie</th>
                <th width="10%" class="text-center">Cant.</th>
                <th width="15%">Estado</th>
                <th width="25%">Propiedad</th>
            </tr>
        </thead>
        <tbody>
            @forelse($equipos as $eq)
            <tr class="uppercase" style="font-size: 8.5px;">
                <td>{{ $eq->descripcion }}</td>
                <td>{{ $eq->nro_serie ?? 'S/N' }}</td>
                <td class="text-center">{{ $eq->cantidad }}</td>
                <td>{{ $eq->estado }}</td>
                <td>{{ $eq->propio ? 'INSTITUCIONAL' : 'PERSONAL' }}</td>
            </tr>
            @empty
            <tr><td colspan="5" class="text-center" style="color: #94a3b8; padding: 15px;">No se registraron equipos en este módulo.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="section-title">5. Evidencia Fotográfica</div>
    <div class="container-evidencia">
        @if(!empty($detalle->contenido['foto_evidencia']))
            @php $path = public_path('storage/' . $detalle->contenido['foto_evidencia']); @endphp
            @if(file_exists($path))
                <img src="{{ $path }}" class="evidencia-img">
            @else
                <div class="no-evidencia">Archivo de imagen no encontrado.</div>
            @endif
        @else
            <div class="no-evidencia">No se adjuntó evidencia fotográfica.</div>
        @endif
    </div>

    <div class="section-title">6. Observaciones Generales</div>
    <div style="border: 1px solid #e2e8f0; padding: 10px; min-height: 40px; font-size: 9px;" class="uppercase">
        {{ $detalle->contenido['comentarios'] ?? 'SIN OBSERVACIONES ADICIONALES.' }}
    </div>

    <div class="section-title">7. Firma de Conformidad</div>
    <div class="firmas-wrapper">
        <div class="firma-recuadro">
            <div class="firma-espacio"></div>
            <div class="firma-linea-firmar"></div>
            <div class="firma-nombre">
                {{ ($detalle->contenido['responsable']['apellido_paterno'] ?? '') }} 
                {{ ($detalle->contenido['responsable']['apellido_materno'] ?? '') }},
                {{ ($detalle->contenido['responsable']['nombres'] ?? '') }}
            </div>
            <div class="firma-info">PROFESIONAL DE LABORATORIO</div>
            <div class="firma-info">
                {{ ($detalle->contenido['responsable']['tipo_doc'] ?? 'DNI') }}: {{ $detalle->contenido['responsable']['doc'] ?? '________' }}
            </div>
        </div>
    </div>

</body>
</html>