<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title>Reporte de Monitoreo - Citas</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      font-size: 11px;
      color: #333;
      line-height: 1.4;
    }

    .header {
      width: 100%;
      border-bottom: 2px solid #2563eb;
      /* Azul */
      padding-bottom: 10px;
      margin-bottom: 20px;
      text-align: center;
    }

    .header h1 {
      margin: 0;
      font-size: 16px;
      text-transform: uppercase;
      color: #1e293b;
    }

    .header p {
      margin: 2px 0;
      color: #64748b;
      font-size: 10px;
    }

    /* Modifica tu clase .section existente */
    .section {
      margin-bottom: 15px;
      clear: both;
      /* <--- AGREGA ESTO */
      display: block;
      /* <--- AGREGA ESTO */
      width: 100%;
      /* <--- AGREGA ESTO */
    }

    .section-title {
      background-color: #f1f5f9;
      color: #0f172a;
      padding: 5px 10px;
      font-weight: bold;
      font-size: 12px;
      border-left: 4px solid #2563eb;
      margin-bottom: 8px;
      text-transform: uppercase;
    }

    /* Tablas */
    table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 10px;
    }

    th,
    td {
      border: 1px solid #cbd5e1;
      padding: 6px;
      text-align: left;
    }

    th {
      background-color: #e2e8f0;
      font-weight: bold;
      font-size: 10px;
      text-transform: uppercase;
    }

    .text-center {
      text-align: center;
    }

    .text-right {
      text-align: right;
    }

    /* Etiquetas y valores */
    .row {
      margin-bottom: 4px;
    }

    .label {
      font-weight: bold;
      color: #475569;
      width: 130px;
      display: inline-block;
    }

    .value {
      color: #000;
    }

    /* Firma */
    .firma-container {
      margin-top: 50px;
      text-align: center;
      page-break-inside: avoid;
      /* Evita que la firma quede sola en otra hoja */
    }

    .firma-img {
      max-width: 200px;
      height: 80px;
      object-fit: contain;
      border-bottom: 1px solid #000;
      margin-bottom: 5px;
    }

    /* Evidencias */
    .photos-grid {
      text-align: center;
    }

    .photo-wrapper {
      display: inline-block;
      width: 45%;
      margin: 5px;
      border: 1px solid #ddd;
      padding: 2px;
      vertical-align: top;
      /* <--- ESTO SOLUCIONA QUE SE MUEVAN DE LUGAR */
    }

    .photo-img {
      width: 100%;
      height: auto;
      max-height: 200px;
    }
  </style>
</head>

<body>

  <div class="header">
    <h1>Acta de Monitoreo - Ventanilla y Caja</h1>
    <p>
      <strong>Establecimiento:</strong> {{ $acta->establecimiento->nombre ?? 'No especificado' }} |
      <strong>Fecha:</strong> {{ $acta->created_at->format('d/m/Y H:i') }} |
      <strong>ID Acta:</strong> {{ $acta->id }}
    </p>
  </div>

  <div class="section">
    <div class="section-title">1. Datos del Responsable</div>
    <div class="row">
      <span class="label">Responsable:</span>
      <span class="value">{{ $registro->personal_nombre ?? '-' }}</span>
    </div>
    <div class="row">
      <span class="label">DNI / Turno:</span>
      <span class="value">{{ $registro->personal_dni ?? '-' }} / {{ $registro->personal_turno ?? '-' }}</span>
    </div>
    <div class="row">
      <span class="label">Roles Asignados:</span>
      <span class="value">
        {{ !empty($registro->personal_roles) ? implode(', ', $registro->personal_roles) : 'Ninguno' }}
      </span>
    </div>
    <div class="row">
      <span class="label">Capacitación:</span>
      <span class="value">
        {{ $registro->capacitacion_recibida ?? 'NO' }}

        @if (!empty($registro->capacitacion_entes))
          {{-- CORRECCIÓN: Verificamos si es array antes de usar implode --}}
          (Por:
          {{ is_array($registro->capacitacion_entes) ? implode(', ', $registro->capacitacion_entes) : $registro->capacitacion_entes }})
        @endif
      </span>
    </div>
    @if ($registro->capacitacion_otros_detalle)
      <div class="row">
        <span class="label">Detalle Capacitación:</span>
        <span class="value">{{ $registro->capacitacion_otros_detalle }}</span>
      </div>
    @endif
  </div>

  <div class="section">
    <div class="section-title">2. Equipamiento e Insumos</div>
    <div class="row" style="margin-bottom: 8px;">
      <span class="label">Insumos Disponibles:</span>
      <span
        class="value">{{ !empty($registro->insumos_disponibles) ? implode(', ', $registro->insumos_disponibles) : 'Sin insumos registrados' }}</span>
    </div>

    <table>
      <thead>
        <tr>
          <th width="40%">Equipo</th>
          <th width="20%">Propiedad</th>
          <th width="15%" class="text-center">Cantidad</th>
          <th width="25%">Estado</th>
        </tr>
      </thead>
      <tbody>
        @forelse($registro->equipos_listado ?? [] as $eq)
          <tr>
            <td>{{ $eq['nombre'] ?? '-' }}</td>
            <td>{{ $eq['propiedad'] ?? '-' }}</td>
            <td class="text-center">{{ $eq['cantidad'] ?? 0 }}</td>
            <td>{{ $eq['estado'] ?? '-' }}</td>
          </tr>
        @empty
          <tr>
            <td colspan="4" class="text-center">No hay equipos registrados</td>
          </tr>
        @endforelse
      </tbody>
    </table>

    @if ($registro->equipos_observaciones)
      <p><strong>Observaciones:</strong> {{ $registro->equipos_observaciones }}</p>
    @endif
  </div>

  <div class="section">
    <div class="section-title">3. Gestión y Calidad</div>
    <div class="row">
      <span class="label">Nro. Ventanillas:</span>
      <span class="value">{{ $registro->nro_ventanillas }}</span>
    </div>

    <h4 style="margin: 5px 0; font-size: 10px; color: #555;">PRODUCCIÓN DE CITAS</h4>
    <table>
      <thead>
        <tr>
          <th>Servicio</th>
          <th class="text-center">Total Citas</th>
        </tr>
      </thead>
      <tbody>
        @forelse($registro->produccion_listado ?? [] as $prod)
          <tr>
            <td>{{ $prod['nombre'] ?? '-' }}</td>
            <td class="text-center">{{ $prod['cantidad'] ?? 0 }}</td>
          </tr>
        @empty
          <tr>
            <td colspan="2" class="text-center">Sin datos de producción</td>
          </tr>
        @endforelse
      </tbody>
    </table>

    <table style="margin-top: 10px;">
      <thead>
        <tr>
          <th>Indicador de Calidad</th>
          <th width="15%" class="text-center">Respuesta</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>¿Disminuye el tiempo de espera?</td>
          <td class="text-center">{{ $registro->calidad_tiempo_espera ?? '-' }}</td>
        </tr>
        <tr>
          <td>¿El paciente se muestra satisfecho?</td>
          <td class="text-center">{{ $registro->calidad_paciente_satisfecho ?? '-' }}</td>
        </tr>
        <tr>
          <td>¿Utiliza reportes del sistema?</td>
          <td class="text-center">{{ $registro->calidad_usa_reportes ?? '-' }}</td>
        </tr>
      </tbody>
    </table>

    @if ($registro->calidad_socializa_con)
      <p style="font-size:10px;">* Socializa reportes con: {{ $registro->calidad_socializa_con }}</p>
    @endif

    @if ($registro->dificultad_comunica_a)
      <div style="background: #fffbeb; border: 1px solid #fcd34d; padding: 5px; font-size: 10px; margin-top:5px;">
        <strong>Reporte de Dificultades:</strong> Se comunica con {{ $registro->dificultad_comunica_a }} a través de
        {{ $registro->dificultad_medio_uso ?? 'medio no especificado' }}.
      </div>
    @endif
  </div>

  <div class="section">
    <div class="section-title">4. Evidencias</div>

    @if (!empty($registro->fotos_evidencia))
      <div class="photos-grid">
        @foreach ($registro->fotos_evidencia as $fotoUrl)
          <div class="photo-wrapper">
            {{-- NOTA: Si usas dompdf, a veces falla con https://.
                             Si tus imagenes no cargan, hay que habilitar 'isRemoteEnabled' en config/dompdf.php --}}
            <img src="{{ $fotoUrl }}" class="photo-img">
          </div>
        @endforeach
      </div>
    @else
      <p class="text-center" style="color: #999;">No se adjuntaron fotografías.</p>
    @endif
  </div>

  <div class="firma-container">
    @if ($registro->firma_grafica)
      <img src="{{ $registro->firma_grafica }}" class="firma-img">
    @else
      <div style="height: 60px;"></div>
    @endif
    <p><strong>{{ $registro->personal_nombre ?? 'Responsable' }}</strong><br>
      Firma de Conformidad</p>
  </div>

</body>

</html>
