<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title>Reporte Atención Prenatal</title>
  <style>
    /* CONFIGURACIÓN GENERAL */
    body {
      font-family: Arial, sans-serif;
      font-size: 11px;
      color: #333;
      line-height: 1.4;
      margin: 0;
      padding: 0;
    }

    /* ENCABEZADO */
    .header-table {
      width: 100%;
      border-bottom: 2px solid #0f172a;
      padding-bottom: 10px;
      margin-bottom: 20px;
    }

    .header-title {
      font-size: 16px;
      font-weight: bold;
      color: #0f172a;
      text-transform: uppercase;
      text-align: center;
    }

    .header-sub {
      font-size: 10px;
      color: #666;
      text-align: center;
    }

    /* TÍTULOS DE SECCIÓN */
    .section-title {
      background-color: #0f172a;
      /* Azul oscuro */
      color: white;
      padding: 5px 10px;
      font-size: 12px;
      font-weight: bold;
      text-transform: uppercase;
      margin-top: 15px;
      margin-bottom: 10px;
      border-radius: 4px;
    }

    /* TABLAS DE DATOS */
    .data-table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 10px;
    }

    .data-table th,
    .data-table td {
      border: 1px solid #cbd5e1;
      padding: 6px;
      text-align: left;
      vertical-align: top;
    }

    .data-table th {
      background-color: #f1f5f9;
      font-weight: bold;
      color: #334155;
      width: 30%;
      /* Ancho fijo para etiquetas */
    }

    /* TABLA DE LISTADOS (Equipos) */
    .list-table {
      width: 100%;
      border-collapse: collapse;
      font-size: 10px;
    }

    .list-table th {
      background-color: #e2e8f0;
      padding: 5px;
      border: 1px solid #94a3b8;
      text-align: center;
    }

    .list-table td {
      border: 1px solid #cbd5e1;
      padding: 5px;
      text-align: center;
    }

    .text-left {
      text-align: left !important;
    }

    /* UTILIDADES */
    .badge {
      background-color: #e2e8f0;
      padding: 2px 6px;
      border-radius: 4px;
      font-size: 10px;
      margin-right: 5px;
      display: inline-block;
    }

    .page-break {
      page-break-after: always;
    }

    .no-break {
      page-break-inside: avoid;
    }
  </style>
</head>

<body>

  {{-- ENCABEZADO --}}
  <table class="header-table">
    <tr>
      <td style="border: none; width: 20%;">
        {{-- Aquí puedes poner tu logo --}}
        {{-- <img src="{{ public_path('img/logo.png') }}" width="80"> --}}
      </td>
      <td style="border: none; width: 60%; text-align: center;">
        <div class="header-title">Ficha de Monitoreo</div>
        <div class="header-title" style="font-size: 14px; color: #2563eb;">Atención Pre-Natal</div>
      </td>
      <td style="border: none; width: 20%; text-align: right; font-size: 9px;">
        Fecha: {{ date('d/m/Y') }}<br>
        Hora: {{ date('H:i') }}
      </td>
    </tr>
  </table>

  {{-- 1. DATOS DEL RESPONSABLE --}}
  <div class="section-title">1. Datos Generales y Responsable</div>
  <table class="data-table">
    <tr>
      <th>Establecimiento / Consultorio:</th>
      <td>{{ $registro->nombre_consultorio ?? 'No registrado' }}</td>
    </tr>
    <tr>
      <th>Profesional Responsable:</th>
      <td>{{ $registro->personal_nombre ?? '-' }}</td>
    </tr>
    <tr>
      <th>Documento Identidad:</th>
      <td>{{ $registro->personal_tipo_doc }} - {{ $registro->personal_dni }}</td>
    </tr>
    <tr>
      <th>Especialidad:</th>
      <td>{{ $registro->personal_especialidad ?? '-' }}</td>
    </tr>
    <tr>
      <th>Capacitación Recibida:</th>
      <td>
        <strong>{{ $registro->capacitacion_recibida }}</strong>
        @if (($registro->capacitacion_recibida ?? '') == 'SI')
          <br><span style="font-size: 10px; color: #666;">
            Entes: {{ implode(', ', $registro->capacitacion_entes ?? []) }}
            @if ($registro->capacitacion_otros_detalle)
              ({{ $registro->capacitacion_otros_detalle }})
            @endif
          </span>
        @endif
      </td>
    </tr>
  </table>

  {{-- 2. RECURSOS (Materiales y Equipos) --}}
  <div class="section-title">2. Recursos Disponibles</div>

  <div class="no-break">
    <h4 style="margin: 5px 0; font-size: 11px; text-transform: uppercase;">Materiales e Insumos</h4>
    <div style="margin-bottom: 10px; padding: 10px; border: 1px solid #cbd5e1; background: #f8fafc;">
      @forelse($registro->insumos_disponibles ?? [] as $insumo)
        <span class="badge">☑ {{ $insumo }}</span>
      @empty
        <span style="color: #999;">No se registraron insumos.</span>
      @endforelse
      @if ($registro->materiales_otros)
        <br><small>Otros: {{ $registro->materiales_otros }}</small>
      @endif
    </div>
  </div>

  <div class="no-break">
    <h4 style="margin: 10px 0 5px; font-size: 11px; text-transform: uppercase;">Equipamiento</h4>
    <table class="list-table">
      <thead>
        <tr>
          <th class="text-left">Descripción</th>
          <th>Propiedad</th>
          <th>Cant.</th>
          <th>Estado</th>
        </tr>
      </thead>
      <tbody>
        @forelse($registro->equipos_listado ?? [] as $equipo)
          <tr>
            <td class="text-left">{{ $equipo['nombre'] ?? '-' }}</td>
            <td>{{ $equipo['propiedad'] ?? '-' }}</td>
            <td>{{ $equipo['cantidad'] ?? 0 }}</td>
            <td>{{ $equipo['estado'] ?? '-' }}</td>
          </tr>
        @empty
          <tr>
            <td colspan="4" style="color: #999; padding: 10px;">No se registraron equipos.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
    @if ($registro->equipos_observaciones)
      <div style="margin-top: 5px; font-size: 10px; font-style: italic; color: #555;">
        <strong>Observaciones:</strong> {{ $registro->equipos_observaciones }}
      </div>
    @endif
  </div>

  {{-- 3. GESTIÓN --}}
  <div class="section-title no-break">3. Indicadores de Gestión</div>
  <table class="data-table no-break">
    <tr>
      <th>Nro. Consultorios:</th>
      <td>{{ $registro->nro_consultorios }}</td>
      <th>Gestantes (Mes):</th>
      <td>{{ $registro->nro_gestantes_mes }}</td>
    </tr>
    <tr>
      <th>Uso HISMINSA:</th>
      <td>{{ $registro->gestion_hisminsa }}</td>
      <th>Emite Reportes:</th>
      <td>
        {{ $registro->gestion_reportes }}
        @if ($registro->gestion_reportes_socializa)
          <br><small>(Socializa con: {{ $registro->gestion_reportes_socializa }})</small>
        @endif
      </td>
    </tr>
  </table>

  {{-- SALTO DE PÁGINA PARA EVIDENCIAS --}}
  <div class="page-break"></div>

  {{-- 4. EVIDENCIAS FOTOGRÁFICAS --}}
  <div class="section-title">4. Evidencias Fotográficas</div>
  <table style="width: 100%; margin-top: 10px;">
    <tr>
      @forelse($registro->fotos_evidencia ?? [] as $foto)
        <td style="width: 50%; text-align: center; border: 1px solid #ddd; padding: 10px;">
          {{--
                       NOTA: Como tu controlador ya convierte a Base64,
                       aquí usamos directamente la variable $foto
                    --}}
          <img src="{{ $foto }}" style="max-width: 100%; max-height: 300px; border-radius: 4px;">
        </td>
        {{-- Salto de fila cada 2 fotos (opcional si usas tablas anidadas) --}}
        @if (($loop->index + 1) % 2 == 0)
    </tr>
    <tr>
      @endif
    @empty
      <td style="text-align: center; color: #999; padding: 20px;">No se adjuntaron fotografías.</td>
      @endforelse
    </tr>
  </table>

  {{-- 5. FIRMA DE CONFORMIDAD --}}
  <div class="no-break" style="margin-top: 50px;">
    <table style="width: 100%;">
      <tr>
        <td style="width: 30%;"></td> {{-- Espacio vacío izq --}}
        <td style="width: 40%; text-align: center; border: none;">
          @if ($registro->firma_grafica)
            <img src="{{ $registro->firma_grafica }}" style="width: 150px; height: auto;">
          @else
            <div style="height: 50px;"></div>
          @endif
          <div style="border-top: 1px solid #000; margin-top: 5px; padding-top: 5px;">
            <strong>{{ $registro->personal_nombre ?? 'Firma del Responsable' }}</strong><br>
            <span style="font-size: 9px;">DNI: {{ $registro->personal_dni }}
