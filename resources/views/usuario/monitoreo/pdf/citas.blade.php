<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title>Reporte de Monitoreo - Citas</title>
  <style>
    body {
      font-family: 'Helvetica', Arial, sans-serif;
      font-size: 11px;
      color: #333;
      line-height: 1.4;
      margin: 20px 30px;
    }

    /* CABECERA PRINCIPAL */
    .header {
      text-align: center;
      border-bottom: 2px solid #2563eb;
      padding-bottom: 10px;
      margin-bottom: 20px;
    }

    .header h1 {
      margin: 0;
      font-size: 16px;
      text-transform: uppercase;
      color: #1e293b;
    }

    .header p {
      margin: 3px 0;
      font-size: 10px;
      color: #64748b;
    }

    /* ESTILOS DE SECCIÓN */
    .section {
      margin-bottom: 20px;
      width: 100%;
    }

    .section-title {
      background-color: #f1f5f9;
      color: #0f172a;
      padding: 6px 10px;
      font-weight: bold;
      font-size: 12px;
      border-left: 5px solid #2563eb;
      text-transform: uppercase;
      margin-bottom: 10px;
    }

    /* TABLAS GENERALES */
    table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 5px;
    }

    th,
    td {
      border: 1px solid #cbd5e1;
      padding: 6px 8px;
      text-align: left;
      vertical-align: middle;
    }

    th {
      background-color: #e2e8f0;
      font-weight: bold;
      font-size: 10px;
      text-transform: uppercase;
      color: #334155;
    }

    .text-center {
      text-align: center;
    }

    .text-right {
      text-align: right;
    }

    /* DATA GRID (Para datos clave valor) */
    .data-grid {
      width: 100%;
      margin-bottom: 10px;
    }

    .data-grid td {
      border: none;
      border-bottom: 1px solid #f1f5f9;
      padding: 5px 0;
    }

    .label {
      font-weight: bold;
      color: #475569;
      width: 140px;
      display: inline-block;
    }

    .value {
      color: #000;
      font-weight: normal;
    }

    /* ESTADO BADGES (Texto coloreado) */
    .status-ok {
      color: #166534;
      font-weight: bold;
    }

    /* Verde */
    .status-warn {
      color: #ca8a04;
      font-weight: bold;
    }

    /* Amarillo oscuro */
    .status-err {
      color: #991b1b;
      font-weight: bold;
    }

    /* Rojo */

    /* ALERTAS / NOTAS */
    .alert-box {
      background-color: #fffbeb;
      border: 1px solid #fcd34d;
      color: #92400e;
      padding: 8px;
      font-size: 10px;
      border-radius: 4px;
      margin-top: 5px;
    }

    /* FOTOS */
    .photos-container {
      text-align: center;
      margin-top: 10px;
    }

    .photo-frame {
      display: inline-block;
      width: 45%;
      margin: 0 5px;
      border: 1px solid #e2e8f0;
      padding: 4px;
      background: #fff;
    }

    .photo-img {
      width: 100%;
      height: 180px;
      object-fit: cover;
    }

    /* FIRMA */
    .firma-section {
      margin-top: 40px;
      text-align: center;
      page-break-inside: avoid;
    }

    .firma-img {
      height: 70px;
      object-fit: contain;
      display: block;
      margin: 0 auto;
    }

    .linea-firma {
      border-top: 1px solid #000;
      width: 250px;
      margin: 5px auto;
    }
  </style>
</head>

<body>

  <div class="header">
    <h1>Acta de Monitoreo - Citas (Ventanilla y Caja)</h1>
    <p>
      <strong>Establecimiento:</strong> {{ $acta->establecimiento->nombre ?? 'No especificado' }} &nbsp;|&nbsp;
      <strong>Fecha de Monitoreo:</strong> {{ $acta->created_at->format('d/m/Y H:i A') }} &nbsp;|&nbsp;
      <strong>ID:</strong> {{ $acta->id }}
    </p>
  </div>

  <div class="section">
    <div class="section-title">1. Datos del Responsable</div>
    <table class="data-grid">
      <tr>
        <td width="60%">
          <span class="label">Responsable:</span>
          <span class="value">{{ $registro->personal_nombre ?? '-' }}</span>
        </td>
        <td width="40%">
          <span class="label">DNI / Documento:</span>
          <span class="value">{{ $registro->personal_dni ?? '-' }}</span>
        </td>
      </tr>
      <tr>
        <td>
          <span class="label">Roles Asignados:</span>
          <span
            class="value">{{ !empty($registro->personal_roles) ? implode(', ', $registro->personal_roles) : 'Ninguno' }}</span>
        </td>
        <td>
          <span class="label">Turno:</span>
          <span class="value">{{ $registro->personal_turno ?? '-' }}</span>
        </td>
      </tr>
      <tr>
        <td colspan="2">
          <span class="label">Capacitación:</span>
          <span class="value">
            {{ $registro->capacitacion_recibida ?? 'NO' }}
            @if ($registro->capacitacion_entes)
              (Entidad:
              {{ is_array($registro->capacitacion_entes) ? implode(', ', $registro->capacitacion_entes) : $registro->capacitacion_entes }})
            @endif
          </span>
        </td>
      </tr>
    </table>
  </div>

  <div class="section">
    <div class="section-title">2. Equipamiento e Insumos</div>

    <div style="margin-bottom: 8px; font-size: 10px;">
      <strong>Insumos Disponibles:</strong>
      <span style="color: #444;">
        {{ !empty($registro->insumos_disponibles) ? implode(', ', $registro->insumos_disponibles) : 'Ninguno registrado' }}
      </span>
    </div>

    <table>
      <thead>
        <tr>
          <th width="35%">Descripción del Equipo</th>
          <th width="15%">Serie/Cod.</th>
          <th width="15%">Propiedad</th>
          <th width="15%" class="text-center">Estado</th>
          <th width="20%">Observaciones</th>
        </tr>
      </thead>
      <tbody>
        @forelse($registro->equipos_listado ?? [] as $eq)
          <tr>
            <td>{{ $eq['nombre'] ?? '-' }}</td>
            <td style="font-family: monospace; font-size: 10px;">{{ $eq['serie'] ?? '-' }}</td>
            <td>{{ $eq['propiedad'] ?? '-' }}</td>
            <td class="text-center">
              @php
                $est = $eq['estado'] ?? '-';
                $clase = match (strtoupper($est)) {
                    'OPERATIVO', 'BUENO' => 'status-ok',
                    'REGULAR' => 'status-warn',
                    default => 'status-err',
                };
              @endphp
              <span class="{{ $clase }}">{{ $est }}</span>
            </td>
            <td>{{ $eq['observaciones'] ?? '' }}</td>
          </tr>
        @empty
          <tr>
            <td colspan="5" class="text-center" style="padding: 10px; color: #777;">
              No se registraron equipos.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>

    @if ($registro->equipos_observaciones)
      <div class="alert-box" style="background: #f8fafc; border-color: #cbd5e1; color: #334155;">
        <strong>Observaciones Generales de Logística:</strong> {{ $registro->equipos_observaciones }}
      </div>
    @endif
  </div>

  <div class="section">
    <div class="section-title">3. Gestión y Calidad</div>

    <div style="margin-bottom: 10px;">
      <span class="label">Nro. Ventanillas:</span>
      <span class="value" style="font-size: 12px; font-weight: bold;">{{ $registro->nro_ventanillas }}</span>
    </div>

    <h4 style="margin: 5px 0; font-size: 10px; color: #555; border-bottom: 1px solid #ccc;">Producción de Citas</h4>
    <table>
      <thead>
        <tr>
          <th>Servicio / Área</th>
          <th width="100" class="text-center">Total Citas</th>
        </tr>
      </thead>
      <tbody>
        @forelse($registro->produccion_listado ?? [] as $prod)
          <tr>
            <td>{{ $prod['nombre'] ?? '-' }}</td>
            <td class="text-center"><strong>{{ $prod['cantidad'] ?? 0 }}</strong></td>
          </tr>
        @empty
          <tr>
            <td colspan="2" class="text-center">Sin información</td>
          </tr>
        @endforelse
      </tbody>
    </table>

    <h4 style="margin: 15px 0 5px 0; font-size: 10px; color: #555; border-bottom: 1px solid #ccc;">Indicadores de
      Calidad</h4>
    <table>
      <thead>
        <tr>
          <th>Pregunta / Indicador</th>
          <th width="100" class="text-center">Respuesta</th>
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
          <td>
            ¿Utiliza reportes del sistema?
            @if ($registro->calidad_socializa_con)
              <div style="font-size: 9px; color: #666; margin-top: 2px;">(Socializa con:
                {{ $registro->calidad_socializa_con }})</div>
            @endif
          </td>
          <td class="text-center">{{ $registro->calidad_usa_reportes ?? '-' }}</td>
        </tr>
      </tbody>
    </table>

    @if ($registro->dificultad_comunica_a)
      <div class="alert-box">
        <strong>REPORTE DE DIFICULTADES:</strong><br>
        Se comunica con <u>{{ $registro->dificultad_comunica_a }}</u> a través del medio:
        <u>{{ $registro->dificultad_medio_uso ?? 'No especificado' }}</u>.
      </div>
    @endif
  </div>

  <div class="section">
    <div class="section-title">4. Evidencias Fotográficas</div>

    @if (!empty($registro->fotos_evidencia))
      <div class="photos-container">
        @foreach ($registro->fotos_evidencia as $fotoUrl)
          <div class="photo-frame">
            <img src="{{ $fotoUrl }}" class="photo-img">
          </div>
        @endforeach
      </div>
    @else
      <div style="text-align: center; padding: 20px; border: 1px dashed #ccc; color: #999;">
        No se adjuntaron fotografías para este reporte.
      </div>
    @endif
  </div>

  <div class="firma-section">
    @if ($registro->firma_grafica)
      <img src="{{ $registro->firma_grafica }}" class="firma-img">
    @else
      <div style="height: 60px;"></div>
    @endif

    <div class="linea-firma"></div>

    <p style="margin: 0; font-weight: bold;">{{ $registro->personal_nombre ?? 'NOMBRE DEL RESPONSABLE' }}</p>
    <p style="margin: 2px 0; font-size: 10px; color: #666;">Firma de Conformidad</p>
  </div>

</body>

</html>
