<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title>Reporte de Monitoreo - Atención Pre Natal</title>
  <style>
    body {
      font-family: 'Helvetica', Arial, sans-serif;
      font-size: 11px;
      color: #333;
      line-height: 1.4;
      margin: 20px 30px;
    }

    /* CABECERA */
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

    /* SECCIONES */
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

    /* TABLAS */
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

    /* DATA GRID (Datos clave valor sin bordes verticales) */
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

    /* ESTADOS */
    .status-ok {
      color: #166534;
      font-weight: bold;
    }

    .status-warn {
      color: #ca8a04;
      font-weight: bold;
    }

    .status-err {
      color: #991b1b;
      font-weight: bold;
    }

    /* ALERTAS */
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
    <h1>Acta de Monitoreo - Atención Pre Natal</h1>
    <p>
      <strong>Establecimiento:</strong> {{ $acta->establecimiento->nombre ?? 'No especificado' }} &nbsp;|&nbsp;
      <strong>Fecha:</strong> {{ $acta->created_at->format('d/m/Y H:i A') }} &nbsp;|&nbsp;
      <strong>ID Acta:</strong> {{ $acta->id }}
    </p>
  </div>

  <div class="section">
    <div class="section-title">1. Datos del Responsable</div>
    <table class="data-grid">
      {{-- FILA 1: Responsable y DNI --}}
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

      {{-- FILA 2: Especialidad y Consultorio --}}
      <tr>
        <td>
          <span class="label">Especialidad:</span>
          <span class="value">{{ $registro->personal_especialidad ?? '-' }}</span>
        </td>
        <td>
          <span class="label">Consultorio:</span>
          <span class="value">{{ $registro->nombre_consultorio ?? '-' }}</span>
        </td>
      </tr>

      {{-- FILA 3: Capacitación --}}
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

      {{-- NUEVA FILA 4: Documentación Administrativa --}}
      <tr>
        <td>
          <span class="label">Declaración Jurada:</span>
          @if (($registro->firma_dj ?? '') == 'SI')
            <span class="status-ok">SÍ FIRMÓ</span>
          @else
            <span class="status-err">{{ $registro->firma_dj ?? 'NO' }}</span>
          @endif
        </td>
        <td>
          <span class="label">Confidencialidad:</span>
          @if (($registro->firma_confidencialidad ?? '') == 'SI')
            <span class="status-ok">SÍ FIRMÓ</span>
          @else
            <span class="status-err">{{ $registro->firma_confidencialidad ?? 'NO' }}</span>
          @endif
        </td>
      </tr>

      {{-- NUEVA FILA 5: Tipo de DNI y Detalle --}}
      <tr>
        <td>
          <span class="label">Tipo DNI Físico:</span>
          <span class="value">{{ $registro->tipo_dni_fisico ?? '-' }}</span>
        </td>
        <td>
          @if (($registro->tipo_dni_fisico ?? '') == 'ELECTRONICO')
            <span class="label">Detalle DNIe:</span>
            <span class="value">
              Versión {{ $registro->dnie_version ?? '-' }} | Firma SIHCE:
              <span class="{{ ($registro->firma_sihce ?? '') == 'SI' ? 'status-ok' : '' }}">
                {{ $registro->firma_sihce ?? 'NO' }}
              </span>
            </span>
          @endif
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
        <strong>Observaciones Generales:</strong> {{ $registro->equipos_observaciones }}
      </div>
    @endif
  </div>

  <div class="section">
    <div class="section-title">3. Gestión y Dificultades</div>

    <table style="width: 100%; margin-bottom: 10px;">
      <tr>
        <td width="50%" style="border:none; padding: 0 10px 0 0; vertical-align: top;">
          <div style="background: #f8fafc; border: 1px solid #e2e8f0; padding: 10px; border-radius: 4px;">
            <div style="margin-bottom: 5px;">
              <strong>Nro. Consultorios:</strong> {{ $registro->nro_consultorios }}
            </div>
            <div style="margin-bottom: 5px;">
              <strong>Gestantes Registradas (Mes):</strong> {{ $registro->nro_gestantes_mes }}
            </div>
            <div>
              <strong>Descarga HISMINSA:</strong> {{ $registro->gestion_hisminsa ?? '-' }}
            </div>
          </div>
        </td>
        <td width="50%" style="border:none; padding: 0 0 0 10px; vertical-align: top;">
          <div style="background: #f8fafc; border: 1px solid #e2e8f0; padding: 10px; border-radius: 4px;">
            <div style="margin-bottom: 5px;">
              <strong>¿Utiliza Reportes?:</strong> {{ $registro->gestion_reportes ?? '-' }}
            </div>
            @if ($registro->gestion_reportes_socializa)
              <div>
                <strong>Socializa con:</strong> {{ $registro->gestion_reportes_socializa }}
              </div>
            @endif
          </div>
        </td>
      </tr>
    </table>

    {{-- SECCIÓN DIFICULTADES (NUEVA) --}}
    @if ($registro->dificultad_comunica_a || $registro->dificultad_medio_uso)
      <div class="alert-box">
        <strong>REPORTE DE DIFICULTADES:</strong><br>
        Se comunica con: <u>{{ $registro->dificultad_comunica_a ?? 'No especificado' }}</u>
        a través del medio: <u>{{ $registro->dificultad_medio_uso ?? 'No especificado' }}</u>.
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
