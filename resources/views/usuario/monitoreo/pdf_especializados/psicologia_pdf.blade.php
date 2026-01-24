<!DOCTYPE html>
<html>
<head>
    <title>Reporte de Monitoreo - Psicología</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 11px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #0d9488; pb: 10px; }
        .section-title { background: #f0fdfa; color: #0f766e; padding: 8px; font-weight: bold; text-transform: uppercase; margin-top: 15px; border-left: 4px solid #0d9488; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #e2e8f0; padding: 6px; text-left; }
        th { background: #f8fafc; font-weight: bold; color: #64748b; }
        .evidence-img { width: 300px; height: auto; border-radius: 10px; margin-top: 10px; }
        .status-badge { padding: 2px 6px; border-radius: 4px; font-size: 9px; font-weight: bold; }
    </style>
</head>
<body>

    <div class="header">
        <h2 style="margin:0;">ACTA DE MONITOREO ESPECIALIZADO - PSICOLOGÍA</h2>
        <p style="margin:5px 0;">Establecimiento: {{ $monitoreo->establecimiento->nombre }} | Fecha: {{ $monitoreo->fecha_monitoreo }}</p>
    </div>

    {{-- I. DATOS DEL PROFESIONAL --}}
    <div class="section-title">I. Datos del Profesional Evaluado</div>
    <table>
        <tr>
            <th>N° Documento</th>
            <td>{{ $data['profesional']['doc'] ?? '---' }}</td>
            <th>Nombres y Apellidos</th>
            <td>{{ ($data['profesional']['nombres'] ?? '') . ' ' . ($data['profesional']['apellido_paterno'] ?? '') }}</td>
        </tr>
        <tr>
            <th>Cargo/Profesión</th>
            <td>{{ $data['profesional']['cargo'] ?? '---' }}</td>
            <th>Correo/Teléfono</th>
            <td>{{ ($data['profesional']['email'] ?? '---') . ' / ' . ($data['profesional']['telefono'] ?? '---') }}</td>
        </tr>
    </table>

    {{-- II. DETALLE DNI Y FIRMA --}}
    <div class="section-title">II. Identidad Digital y Firma</div>
    <table>
        <tr>
            <th>Tipo DNI</th>
            <td>{{ $data['tipo_dni_fisico'] ?? 'DNI AZUL' }}</td>
            <th>Versión DNIe</th>
            <td>{{ $data['dnie_version'] ?? 'N/A' }}</td>
            <th>Firma en SIHCE</th>
            <td style="font-weight: bold; color: {{ ($data['dnie_firma_sihce'] ?? '') == 'SI' ? 'green' : 'red' }}">
                {{ $data['dnie_firma_sihce'] ?? 'NO' }}
            </td>
        </tr>
    </table>

    {{-- III. EQUIPAMIENTO TÉCNICO --}}
    <div class="section-title">III. Equipamiento del Consultorio</div>
    <table>
        <thead>
            <tr>
                <th>Descripción</th>
                <th>Estado</th>
                <th>Serie/Código</th>
                <th>Propiedad</th>
            </tr>
        </thead>
        <tbody>
            @foreach($equipos as $equipo)
            <tr>
                <td>{{ $equipo->tipo_equipo }}</td>
                <td>{{ $equipo->estado }}</td>
                <td>{{ $equipo->serie_cod }}</td>
                <td>{{ $equipo->propiedad }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- IV. COMENTARIOS Y EVIDENCIA --}}
    <div class="section-title">IV. Observaciones y Evidencia Fotográfica</div>
    <div style="margin-top: 10px;">
        <strong>Comentarios del Especialista:</strong>
        <p style="background: #fdfdfd; padding: 10px; border: 1px solid #eee; min-height: 50px;">
            {{ $data['comentario_especialista'] ?? 'Sin observaciones registradas.' }}
        </p>
    </div>

    @if(isset($data['foto_final_path']))
    <div style="text-align: center; margin-top: 20px;">
        <p><strong>Evidencia Visual:</strong></p>
        <img src="{{ public_path('storage/' . $data['foto_final_path']) }}" class="evidence-img">
    </div>
    @endif

</body>
</html>