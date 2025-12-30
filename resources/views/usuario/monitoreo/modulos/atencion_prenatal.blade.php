@extends('layouts.usuario')
@section('title', 'Monitoreo - Atención Pre Natal')

@push('styles')
  <style>
    :root {
      --primary-blue: #0f172a;
      --accent-blue: #2563eb;
      --light-blue: #eff6ff;
      --success: #10b981;
      --danger: #ef4444;
    }

    body {
      background-color: #f8fafc;
    }

    .step-header {
      margin-bottom: 1rem;
      padding: 0 0.5rem;
    }

    .step-connector {
      flex: 1;
      height: 2px;
      background-color: #e2e8f0;
      margin: 0 10px;
      position: relative;
      top: 10px;
      z-index: 0;
    }

    .step-connector.active {
      background-color: var(--accent-blue);
    }

    .step-circle {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      background-color: white;
      border: 2px solid #e2e8f0;
      color: #94a3b8;
      font-weight: 700;
      position: relative;
      z-index: 10;
      transition: all 0.3s ease;
    }

    .step-circle.active {
      border-color: var(--accent-blue);
      color: var(--accent-blue);
      box-shadow: 0 0 0 4px var(--light-blue);
    }

    .step-circle.completed {
      background-color: var(--accent-blue);
      border-color: var(--accent-blue);
      color: white;
    }

    .step-label {
      font-size: 0.7rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.05em;
      margin-top: 0.5rem;
      color: #94a3b8;
    }

    .step-label.active {
      color: var(--primary-blue);
    }

    .form-card {
      background: white;
      border-radius: 1rem;
      box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
      border: 1px solid #f1f5f9;
      padding: 1.25rem;
    }

    .input-label {
      display: block;
      font-size: 0.75rem;
      font-weight: 700;
      color: var(--primary-blue);
      text-transform: uppercase;
      margin-bottom: 0.4rem;
      letter-spacing: 0.05em;
    }

    .input-blue {
      width: 100%;
      border: 1px solid #cbd5e1;
      border-radius: 0.5rem;
      padding: 0.6rem 1rem;
      font-size: 0.875rem;
      color: #334155;
      background-color: #fff;
      transition: all 0.2s;
    }

    .input-blue:focus {
      outline: none;
      border-color: var(--accent-blue);
      box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    }

    .toggle-group {
      display: flex;
      gap: 0.5rem;
    }

    .toggle-radio {
      display: none;
    }

    .toggle-btn {
      padding: 0.5rem 1.2rem;
      border: 1px solid #cbd5e1;
      border-radius: 0.5rem;
      cursor: pointer;
      font-weight: 700;
      font-size: 0.75rem;
      color: #64748b;
      background: white;
      transition: all 0.2s;
      display: flex;
      align-items: center;
      gap: 0.4rem;
    }

    .toggle-btn:hover {
      background: #f1f5f9;
    }

    .toggle-radio[value="SI"]:checked+.toggle-btn {
      background-color: var(--success);
      border-color: var(--success);
      color: white;
      box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.4);
    }

    .toggle-radio[value="NO"]:checked+.toggle-btn {
      background-color: var(--danger);
      border-color: var(--danger);
      color: white;
      box-shadow: 0 4px 6px -1px rgba(239, 68, 68, 0.4);
    }

    .blue-table {
      width: 100%;
      border-collapse: separate;
      border-spacing: 0;
      border: 1px solid #e2e8f0;
      border-radius: 0.5rem;
      overflow: hidden;
    }

    .blue-table th {
      background-color: var(--light-blue);
      color: var(--primary-blue);
      font-size: 0.7rem;
      text-transform: uppercase;
      font-weight: 800;
      padding: 0.75rem 1rem;
      text-align: left;
    }

    .blue-table td {
      border-top: 1px solid #f1f5f9;
      padding: 0.5rem;
    }

    .table-input {
      width: 100%;
      background: transparent;
      border: 1px solid transparent;
      padding: 0.4rem;
      border-radius: 0.375rem;
      font-size: 0.85rem;
    }

    .table-input:focus {
      border-color: var(--accent-blue);
      background: white;
      outline: none;
    }

    .btn-nav {
      padding: 0.8rem 2rem;
      border-radius: 0.5rem;
      font-weight: 700;
      font-size: 0.875rem;
      display: flex;
      align-items: center;
      gap: 0.5rem;
      transition: all 0.2s;
      cursor: pointer;
    }

    .btn-prev {
      background: white;
      border: 1px solid #cbd5e1;
      color: #64748b;
    }

    .btn-prev:hover {
      background: #f1f5f9;
      color: #334155;
    }

    .btn-next {
      background: var(--primary-blue);
      color: white;
      border: 1px solid var(--primary-blue);
      box-shadow: 0 4px 6px -1px rgba(15, 23, 42, 0.4);
    }

    .btn-next:hover {
      background: #1e293b;
      transform: translateY(-1px);
    }

    .btn-finish {
      background: var(--success);
      color: white;
      border: 1px solid var(--success);
      box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.4);
    }

    .btn-finish:hover {
      background: #059669;
      transform: translateY(-1px);
    }

    .upload-area {
      border: 2px dashed #cbd5e1;
      border-radius: 1rem;
      padding: 2rem;
      text-align: center;
      background-color: #f8fafc;
      cursor: pointer;
      transition: 0.2s;
    }

    .upload-area:hover {
      border-color: var(--accent-blue);
      background-color: var(--light-blue);
    }

    canvas#signature-pad {
      border: 2px solid #e2e8f0;
      border-radius: 0.5rem;
      background: #fff;
      width: 100%;
      cursor: crosshair;
    }

    .step-content {
      display: none;
      animation: fadeIn 0.4s ease;
    }

    .step-content.active {
      display: block;
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: translateY(5px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
  </style>
@endpush

@section('content')

  <div class="max-w-7xl mx-auto py-4 px-3">

    {{-- HEADER --}}
    <div class="step-header">
      <div class="flex mb-6">
        <a href="{{ route('usuario.monitoreo.modulos', $acta->id) }}"
          class="group flex items-center gap-2 text-slate-400 hover:text-slate-600 font-bold text-[10px] uppercase tracking-widest bg-white px-4 py-2 rounded-xl border border-slate-200 shadow-sm transition-all hover:border-slate-300">
          <i data-lucide="chevron-left" class="w-3 h-3 transition-transform group-hover:-translate-x-1"></i>
          Volver al Panel de Módulos
        </a>
      </div>

      <div class="flex justify-between items-start">
        @foreach ([1 => ['user', 'Responsable'], 2 => ['package', 'Equipamiento'], 3 => ['database', 'Datos'], 4 => ['camera', 'Evidencias'], 5 => ['pen-tool', 'Firma']] as $i => $data)
          <div class="flex flex-col items-center flex-1 cursor-pointer" onclick="goToStep({{ $i }})">
            <div class="step-circle {{ $i == 1 ? 'active' : '' }}" id="circle-{{ $i }}">
              <i data-lucide="{{ $data[0] }}" class="w-5 h-5"></i>
            </div>
            <span class="step-label {{ $i == 1 ? 'active' : '' }}"
              id="label-{{ $i }}">{{ $data[1] }}</span>
          </div>
          @if (!$loop->last)
            <div class="step-connector" id="line-{{ $i }}"></div>
          @endif
        @endforeach
      </div>
    </div>



    {{-- FORMULARIO --}}
    <form action="{{ route('usuario.monitoreo.atencion-prenatal.create', $acta->id) }}" method="POST"
      enctype="multipart/form-data" id="mainForm">
      @csrf
      <input type="hidden" name="modulo_nombre" value="atencion_prenatal">

      <div class="form-card">

        {{-- PASO 1: RESPONSABLE --}}
        <div id="step-1" class="step-content active">
          <div class="mb-3 border-b border-slate-100 pb-2">
            <h2 class="text-xl font-bold text-slate-800">Datos Generales del Responsable</h2>
            <p class="text-slate-500 text-xs">Información de Atención Prenatal</p>
          </div>

          <div class="mb-4">
            <label class="input-label">Nombre del Consultorio</label>
            <input type="text" name="contenido[nombre_consultorio]" class="input-blue"
              placeholder="Escriba el nombre del consultorio (ej. Consultorio 01)"
              value="{{ $registro->nombre_consultorio ?? '' }}">
          </div>

          <div class="bg-slate-50 p-4 rounded-lg border border-slate-100 mb-4">
            <h3 class="text-xs font-bold text-slate-700 mb-3 uppercase border-b border-slate-200 pb-2">Datos del
              Profesional</h3>

            <div class="grid grid-cols-1 md:grid-cols-12 gap-4 mb-4">
              <div class="md:col-span-3">
                <label class="input-label">Tipo Doc.</label>
                <select name="contenido[personal_tipo_doc]" id="personal_tipo_doc" class="input-blue text-xs">
                  <option value="DNI" {{ ($registro->personal_tipo_doc ?? '') == 'DNI' ? 'selected' : '' }}>DNI
                  </option>
                  <option value="CE" {{ ($registro->personal_tipo_doc ?? '') == 'CE' ? 'selected' : '' }}>C.E.
                  </option>
                </select>
              </div>

              <div class="md:col-span-4">
                <label class="input-label">Nro. Documento</label>
                <div class="relative">
                  <input type="text" name="contenido[personal_dni]" id="personal_dni" maxlength="15"
                    class="input-blue font-bold text-center" placeholder="Ingrese y Enter"
                    value="{{ $registro->personal_dni ?? '' }}" onblur="buscarPorDoc()"
                    onkeydown="if(event.key === 'Enter'){event.preventDefault(); buscarPorDoc();}">
                  <div id="loading-doc" class="hidden absolute right-3 top-2.5">
                    <i data-lucide="loader-2" class="w-4 h-4 animate-spin text-indigo-600"></i>
                  </div>
                </div>
                <p id="msg-doc" class="text-[10px] text-red-500 mt-1 hidden"></p>
              </div>

              <div class="md:col-span-5">
                <label class="input-label">Especialidad</label>
                <select name="contenido[personal_especialidad]" id="personal_especialidad" class="input-blue text-xs">
                  <option value="">-- Seleccionar --</option>
                  @foreach (['OBSTETRA', 'MEDICO GINECOLOGO', 'MEDICO GENERAL', 'LIC. ENFERMERIA', 'TECNICO ENFERMERIA'] as $esp)
                    <option value="{{ $esp }}"
                      {{ ($registro->personal_especialidad ?? '') == $esp ? 'selected' : '' }}>{{ $esp }}
                    </option>
                  @endforeach
                </select>
              </div>
            </div>

            <div class="relative">
              <label class="input-label">Apellidos y Nombres</label>
              <input type="text" name="contenido[personal_nombre]" id="personal_nombre" class="input-blue"
                placeholder="Escriba apellidos para buscar..." autocomplete="off"
                value="{{ $registro->personal_nombre ?? '' }}" oninput="buscarPorNombre()">
              <div id="lista-sugerencias"
                class="hidden absolute z-50 w-full bg-white border border-slate-200 rounded-lg shadow-lg mt-1 max-h-60 overflow-y-auto">
              </div>
            </div>
          </div>

          <div class="bg-white p-4 rounded-lg border border-slate-200 shadow-sm">
            <div class="flex justify-between items-center gap-4">
              <p class="text-sm font-bold text-slate-700">¿El personal recibió capacitación?</p>
              <div class="toggle-group">
                <label><input type="radio" name="contenido[capacitacion]" value="SI" class="toggle-radio"
                    onchange="toggleCapacitacion(true)"
                    {{ ($registro->capacitacion_recibida ?? '') == 'SI' ? 'checked' : '' }}><span class="toggle-btn"><i
                      data-lucide="check" class="w-4 h-4"></i> SÍ</span></label>
                <label><input type="radio" name="contenido[capacitacion]" value="NO" class="toggle-radio"
                    onchange="toggleCapacitacion(false)"
                    {{ ($registro->capacitacion_recibida ?? '') == 'NO' ? 'checked' : '' }}><span class="toggle-btn"><i
                      data-lucide="x" class="w-4 h-4"></i> NO</span></label>
              </div>
            </div>

            <div id="div-capacitacion-detalles"
              class="{{ ($registro->capacitacion_recibida ?? '') == 'SI' ? '' : 'hidden' }} mt-4 pt-4 border-t border-slate-100">
              <p class="input-label mb-2">Entidad que capacitó:</p>
              <div class="flex flex-wrap gap-4 mb-3">
                @foreach (['MINSA', 'DIRIS/DIRESA', 'OTROS'] as $ente)
                  <label
                    class="flex items-center gap-2 cursor-pointer bg-slate-50 px-3 py-2 rounded border border-slate-100 hover:border-indigo-300 transition">
                    <input type="checkbox" name="contenido[capacitacion_ente][]" value="{{ $ente }}"
                      class="rounded text-indigo-600 focus:ring-0 border-slate-300"
                      {{ in_array($ente, $registro->capacitacion_entes ?? []) ? 'checked' : '' }}
                      {{ $ente == 'OTROS' ? 'onchange=toggleOtrosCapacitacion(this)' : '' }}>
                    <span class="text-xs font-bold text-slate-600">{{ $ente }}</span>
                  </label>
                @endforeach
              </div>
              <div id="div-capacitacion-otros"
                class="{{ in_array('OTROS', $registro->capacitacion_entes ?? []) ? '' : 'hidden' }}">
                <input type="text" name="contenido[capacitacion_otros_detalle]" class="input-blue"
                  placeholder="Especifique..." value="{{ $registro->capacitacion_otros_detalle ?? '' }}">
              </div>
            </div>
          </div>
        </div>

        {{-- PASO 2: MATERIALES Y EQUIPOS --}}
        <div id="step-2" class="step-content">
          <div class="mb-6 border-b border-slate-100 pb-4">
            <h2 class="text-2xl font-bold text-slate-800">Recursos: Materiales y Equipamiento</h2>
          </div>

          {{-- Materiales --}}
          <div class="bg-slate-50 p-6 rounded-xl border border-slate-100 mb-6">
            <p class="input-label mb-4 text-slate-600">Al iniciar sus labores diarias cuenta con:</p>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
              @foreach (['REGISTRO DE HISTORIA CLINICA', 'PLAN DE PARTO', 'CARNET DE ATENCION', 'TAMIZAJE DE VIOLENCIA', 'CONTROL PRE-NATAL', 'RECETA', 'ORDENES DE LABORATORIO', 'FUA'] as $insumo)
                <label
                  class="flex items-center gap-3 cursor-pointer bg-white p-3 rounded-lg border border-slate-200 hover:border-indigo-400 group">
                  <input type="checkbox" name="contenido[insumos][]" value="{{ $insumo }}"
                    class="rounded text-indigo-600 focus:ring-0 border-slate-300"
                    {{ in_array($insumo, $registro->insumos_disponibles ?? []) ? 'checked' : '' }}>
                  <span
                    class="text-[11px] font-bold text-slate-600 group-hover:text-indigo-700">{{ $insumo }}</span>
                </label>
              @endforeach
            </div>

          </div>

          {{-- Equipos --}}
          <div class="mb-6">
            <div class="flex justify-between items-end mb-3">
              <h3 class="input-label text-slate-600">Listado de Equipos</h3>
              <button type="button" onclick="agregarFila('tabla-equipos', 'contenido[equipos]')"
                class="text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 px-4 py-2 rounded-lg flex items-center gap-2">
                <i data-lucide="plus" class="w-3 h-3"></i> AGREGAR
              </button>
            </div>
            <div class="overflow-hidden rounded-lg border border-slate-200 shadow-sm">
              <table class="blue-table mb-0" id="tabla-equipos">
                <thead>
                  <tr>
                    <th style="width: 35%">Descripción</th>
                    <th style="width: 20%">Propiedad</th>
                    <th style="width: 10%" class="text-center">Cant.</th>
                    <th style="width: 25%">Estado</th>
                    <th style="width: 10%" class="text-center">Acción</th>
                  </tr>
                </thead>
                <tbody>
                  @php
                    $equipos = $registro->equipos_listado ?? [
                        ['nombre' => 'Monitor', 'propiedad' => 'ESTABLECIMIENTO', 'cantidad' => 1, 'estado' => 'Bueno'],
                        ['nombre' => 'CPU', 'propiedad' => 'ESTABLECIMIENTO', 'cantidad' => 1, 'estado' => 'Bueno'],
                        ['nombre' => 'Teclado', 'propiedad' => 'ESTABLECIMIENTO', 'cantidad' => 1, 'estado' => 'Bueno'],
                        ['nombre' => 'Mouse', 'propiedad' => 'ESTABLECIMIENTO', 'cantidad' => 1, 'estado' => 'Bueno'],
                        [
                            'nombre' => 'Impresora',
                            'propiedad' => 'ESTABLECIMIENTO',
                            'cantidad' => 1,
                            'estado' => 'Bueno',
                        ],
                    ];
                  @endphp
                  @foreach ($equipos as $idx => $item)
                    <tr>
                      <td><input type="text" name="contenido[equipos][{{ $idx }}][nombre]"
                          value="{{ $item['nombre'] }}" class="table-input font-bold"></td>
                      <td><select name="contenido[equipos][{{ $idx }}][propiedad]"
                          class="table-input text-xs">
                          <option value="ESTABLECIMIENTO"
                            {{ $item['propiedad'] == 'ESTABLECIMIENTO' ? 'selected' : '' }}>Establecimiento</option>
                          <option value="PROPIO" {{ $item['propiedad'] == 'PROPIO' ? 'selected' : '' }}>Propio</option>
                        </select></td>
                      <td><input type="number" name="contenido[equipos][{{ $idx }}][cantidad]"
                          value="{{ $item['cantidad'] }}" class="table-input text-center"></td>
                      <td><select name="contenido[equipos][{{ $idx }}][estado]" class="table-input text-xs">
                          @foreach (['Bueno', 'Regular', 'Malo', 'Inoperativo'] as $est)
                            <option value="{{ $est }}" {{ $item['estado'] == $est ? 'selected' : '' }}>
                              {{ $est }}</option>
                          @endforeach
                        </select></td>
                      <td class="text-center"><button type="button" onclick="this.closest('tr').remove()"
                          class="p-1 rounded text-slate-300 hover:text-red-500"><i data-lucide="trash-2"
                            class="w-4 h-4"></i></button></td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>

          {{-- Observaciones unificadas --}}
          <div class="mt-4 bg-slate-50 p-4 rounded-xl border border-slate-200">
            <label class="flex items-center gap-2 text-xs font-bold text-slate-500 uppercase mb-2"><i
                data-lucide="message-square" class="w-4 h-4"></i> Observaciones Adicionales</label>
            <textarea name="contenido[equipos_observaciones]" rows="3"
              placeholder="Comentarios sobre materiales o equipos..."
              class="w-full bg-white border border-slate-300 rounded-lg p-3 text-sm resize-none">{{ $registro->equipos_observaciones ?? '' }}</textarea>
          </div>
        </div>

        {{-- PASO 3: DATOS --}}
        <div id="step-3" class="step-content">
          <div class="mb-6 border-b border-slate-100 pb-4">
            <h2 class="text-2xl font-bold text-slate-800">Datos de Gestión</h2>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Indicadores --}}
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
              <label class="input-label mb-4 border-b pb-2">Información del Consultorio</label>

              <div class="flex justify-between items-center mb-4">
                <span class="text-xs font-bold text-slate-700 uppercase">Número de Consultorios:</span>
                <input type="number" name="contenido[nro_consultorios]"
                  class="w-20 border border-indigo-200 rounded p-2 text-center font-bold text-indigo-700 bg-white"
                  value="{{ $registro->nro_consultorios ?? 0 }}">
              </div>

              <div class="flex justify-between items-center mb-4">
                <span class="text-xs font-bold text-slate-700 uppercase">Gestantes Registradas (Mes):</span>
                <input type="number" name="contenido[nro_gestantes_mes]"
                  class="w-20 border border-indigo-200 rounded p-2 text-center font-bold text-indigo-700 bg-white"
                  value="{{ $registro->nro_gestantes_mes ?? 0 }}">
              </div>

              <div class="flex justify-between items-center">
                <span class="text-xs font-bold text-slate-600 uppercase">¿Descarga en HISMINSA?</span>
                <div class="toggle-group">
                  <label><input type="radio" name="contenido[gestion_hisminsa]" value="SI" class="toggle-radio"
                      {{ ($registro->gestion_hisminsa ?? '') == 'SI' ? 'checked' : '' }}><span
                      class="toggle-btn">SÍ</span></label>
                  <label><input type="radio" name="contenido[gestion_hisminsa]" value="NO" class="toggle-radio"
                      {{ ($registro->gestion_hisminsa ?? '') == 'NO' ? 'checked' : '' }}><span
                      class="toggle-btn">NO</span></label>
                </div>
              </div>
            </div>

            {{-- Reportes --}}
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
              <label class="input-label mb-4 border-b pb-2">Reportes del Sistema</label>
              <div class="flex justify-between items-center mb-4">
                <span class="text-xs font-bold text-slate-600 uppercase">¿Utiliza Reportes?</span>
                <div class="toggle-group">
                  <label><input type="radio" name="contenido[gestion_reportes]" value="SI" class="toggle-radio"
                      onchange="toggleReportesPrenatal(true)"
                      {{ ($registro->gestion_reportes ?? '') == 'SI' ? 'checked' : '' }}><span
                      class="toggle-btn">SÍ</span></label>
                  <label><input type="radio" name="contenido[gestion_reportes]" value="NO" class="toggle-radio"
                      onchange="toggleReportesPrenatal(false)"
                      {{ ($registro->gestion_reportes ?? '') == 'NO' ? 'checked' : '' }}><span
                      class="toggle-btn">NO</span></label>
                </div>
              </div>
              <div id="div-reportes-prenatal-detalle"
                class="{{ ($registro->gestion_reportes ?? '') == 'SI' ? '' : 'hidden' }}">
                <label class="input-label mb-1">Si es "SI" ¿con quién lo socializa?</label>
                <input type="text" name="contenido[gestion_reportes_socializa]" class="input-blue"
                  placeholder="Especifique..." value="{{ $registro->gestion_reportes_socializa ?? '' }}">
              </div>
            </div>
          </div>
        </div>

        {{-- PASO 4: EVIDENCIAS --}}
        <div id="step-4" class="step-content">
          <div class="mb-6 border-b border-slate-100 pb-4">
            <h2 class="text-2xl font-bold text-slate-800">Evidencias Fotográficas</h2>
          </div>
          <div class="mb-6">
            <div class="flex gap-4 mb-4 border-b border-slate-200">
              <button type="button" onclick="switchTab('local')" id="tab-local"
                class="pb-2 text-sm font-bold text-indigo-600 border-b-2 border-indigo-600 transition-all"><i
                  data-lucide="upload-cloud" class="w-4 h-4 inline mr-1"></i> Subir desde PC</button>
              <button type="button" onclick="switchTab('server')" id="tab-server"
                class="pb-2 text-sm font-bold text-slate-400 hover:text-indigo-500 transition-all"><i
                  data-lucide="server" class="w-4 h-4 inline mr-1"></i> Explorar Servidor</button>
            </div>
            <div id="panel-local" class="block">
              <div
                class="upload-area relative group bg-slate-50 border-2 border-dashed border-slate-300 rounded-xl p-8 text-center hover:bg-indigo-50 hover:border-indigo-400 transition-all cursor-pointer">
                <input type="file" id="input-fotos-local" multiple accept="image/*"
                  class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                  onchange="handleFiles(this.files)">
                <div class="flex flex-col items-center gap-3">
                  <div
                    class="w-12 h-12 bg-white rounded-full shadow-sm flex items-center justify-center text-indigo-500 group-hover:scale-110 transition-transform">
                    <i data-lucide="image-plus" class="w-6 h-6"></i>
                  </div>
                  <p class="text-slate-700 font-bold text-sm">Haga clic o arrastre fotos aquí</p>
                  <p class="text-slate-400 text-[10px] uppercase font-bold">Máximo 2 imágenes</p>
                </div>
              </div>
            </div>
            <div id="panel-server" class="hidden">
              <div class="bg-slate-50 border border-slate-200 rounded-xl p-8 text-center">
                <div class="mb-4">
                  <i data-lucide="hard-drive" class="w-10 h-10 text-slate-400 mx-auto mb-2"></i>
                  <p class="text-sm font-bold text-slate-600">Seleccionar archivos alojados en el Hosting</p>
                </div>
                <button type="button" onclick="openServerModal()"
                  class="bg-slate-800 text-white px-5 py-2.5 rounded-lg text-xs font-bold hover:bg-slate-900 transition flex items-center justify-center gap-2 mx-auto">
                  <i data-lucide="search" class="w-4 h-4"></i> ABRIR EXPLORADOR DE ARCHIVOS
                </button>
              </div>
            </div>
          </div>
          <div>
            <h3 class="input-label mb-3 flex justify-between"><span>Archivos Seleccionados</span><span
                class="text-indigo-600" id="count-display">0 / 2</span></h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4" id="gallery-container">
              <div id="empty-state"
                class="col-span-full py-6 text-center text-slate-400 text-xs italic bg-slate-50 rounded-lg border border-dashed border-slate-200">
                No hay imágenes seleccionadas</div>
            </div>
          </div>
          <input type="file" name="fotos[]" id="final-input-files" multiple class="hidden">
          <input type="hidden" name="rutas_servidor" id="final-input-server">
        </div>

        {{-- PASO 5: FIRMA --}}
        <div id="step-5" class="step-content">
          <div class="mb-6 border-b border-slate-100 pb-4">
            <h2 class="text-2xl font-bold text-slate-800">Conformidad</h2>
          </div>
          <div class="max-w-xl mx-auto py-8">
            <div class="flex justify-between items-end mb-2">
              <label class="text-xs font-bold text-slate-500 uppercase">Firma del Profesional Encargado:</label>
              <button type="button" onclick="clearSignature()"
                class="text-[10px] font-bold text-red-500 flex items-center gap-1 bg-red-50 px-2 py-1 rounded transition hover:bg-red-100">LIMPIAR
                FIRMA</button>
            </div>
            <div class="bg-white border-2 border-dashed border-slate-300 rounded-xl p-1 shadow-sm">
              <canvas id="signature-pad" class="w-full h-64 bg-slate-50 rounded-lg"></canvas>
            </div>
            <input type="hidden" name="firma_grafica_data" id="firma_input"
              value="{{ $registro->firma_grafica ?? '' }}">
            <p class="text-[10px] text-center text-slate-400 mt-4 italic"><i data-lucide="info"
                class="w-3 h-3 inline-block mr-1"></i> La firma gráfica se adjuntará al reporte final del acta.</p>
          </div>
        </div>
      </div>

      {{-- NAVEGACIÓN --}}
      <div class="mt-6 flex justify-between items-center">
        <button type="button" class="btn-nav btn-prev" id="btn-prev" onclick="changeStep(-1)"
          style="visibility: hidden;"><i data-lucide="arrow-left" class="w-4 h-4"></i> Anterior</button>
        <div>
          <button type="button" class="btn-nav btn-next" id="btn-next" onclick="changeStep(1)">Siguiente <i
              data-lucide="arrow-right" class="w-4 h-4"></i></button>
          <button type="submit" class="btn-nav btn-finish" id="btn-submit" style="display: none;"
            onclick="saveSignature()"><i data-lucide="check-circle" class="w-4 h-4"></i> Finalizar y Guardar</button>
        </div>
      </div>
    </form>
  </div>

@endsection

@push('scripts')
  <script>
    let canvas, ctx, isDrawing = false,
      evidenceList = [],
      currentStep = 1;
    const totalSteps = 5,
      MAX_PHOTOS = 2;
    let timeoutNombre = null;

    document.addEventListener('DOMContentLoaded', () => {
      lucide.createIcons();
      initSignaturePad();

      const fotosGuardadas = @json($registro->fotos_evidencia ?? []);
      if (fotosGuardadas.length > 0) {
        fotosGuardadas.forEach((url, i) => evidenceList.push({
          type: 'server',
          file: null,
          url: url,
          name: 'Foto',
          id: Date.now() + i
        }));
        renderGallery();
        syncInputs();
      }

      const firmaInput = document.getElementById('firma_input');
      if (firmaInput && firmaInput.value) {
        const img = new Image();
        img.src = firmaInput.value;
        img.onload = () => ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
      }
    });

    window.showStep = function(step) {
      document.querySelectorAll('.step-content').forEach(el => el.classList.remove('active'));
      document.getElementById(`step-${step}`).classList.add('active');

      for (let i = 1; i <= totalSteps; i++) {
        const circle = document.getElementById(`circle-${i}`);
        const label = document.getElementById(`label-${i}`);
        const line = document.getElementById(`line-${i}`);

        circle.classList.remove('active', 'completed');
        if (label) label.classList.remove('active');
        if (line) line.classList.remove('active');

        if (i < step) {
          // Paso completado: Fondo azul y icono de check
          circle.classList.add('completed');
          circle.innerHTML = '<i data-lucide="check" class="w-5 h-5"></i>';
          if (line) line.classList.add('active');
        } else if (i === step) {
          // Paso actual: Borde azul y icono original
          circle.classList.add('active');
          circle.innerHTML = getIconForStep(i);
          if (label) label.classList.add('active');
        } else {
          // Paso futuro: Gris y icono original
          circle.innerHTML = getIconForStep(i);
        }
      }



      // IMPORTANTE: Actualizar botones y re-dibujar iconos
      lucide.createIcons();
      document.getElementById('btn-prev').style.visibility = step === 1 ? 'hidden' : 'visible';
      document.getElementById('btn-next').style.display = step === totalSteps ? 'none' : 'flex';
      document.getElementById('btn-submit').style.display = step === totalSteps ? 'flex' : 'none';

      if (step === 5) setTimeout(resizeCanvas, 100);
      window.scrollTo({
        top: 0,
        behavior: 'smooth'
      });
    }

    window.changeStep = (dir) => {
      currentStep += dir;
      showStep(currentStep);
    }
    window.goToStep = (s) => {
      currentStep = s;
      showStep(s);
    }

    async function buscarPorDoc() {
      const doc = document.getElementById('personal_dni').value.trim();
      const loader = document.getElementById('loading-doc');
      if (doc.length < 5) return;
      loader.classList.remove('hidden');
      try {
        const res = await fetch(
          `{{ route('usuario.monitoreo.atencion-prenatal.buscar.profesional') }}?type=doc&q=${doc}`);
        const data = await res.json();
        if (data.length > 0) rellenarDatos(data[0]);
      } finally {
        loader.classList.add('hidden');
      }
    }

    function getIconForStep(step) {
      switch (step) {
        case 1:
          return '<i data-lucide="user" class="w-5 h-5"></i>';
        case 2:
          return '<i data-lucide="package" class="w-5 h-5"></i>';
        case 3:
          return '<i data-lucide="database" class="w-5 h-5"></i>';
        case 4:
          return '<i data-lucide="camera" class="w-5 h-5"></i>';
        case 5:
          return '<i data-lucide="pen-tool" class="w-5 h-5"></i>';
        default:
          return step;
      }
    }

    function buscarPorNombre() {
      const query = document.getElementById('personal_nombre').value;
      const lista = document.getElementById('lista-sugerencias');
      clearTimeout(timeoutNombre);
      if (query.length < 3) return lista.classList.add('hidden');
      timeoutNombre = setTimeout(async () => {
        const res = await fetch(
          `{{ route('usuario.monitoreo.atencion-prenatal.buscar.profesional') }}?type=name&q=${query}`);
        const data = await res.json();
        lista.innerHTML = '';
        if (data.length > 0) {
          lista.classList.remove('hidden');
          data.forEach(p => {
            const div = document.createElement('div');
            div.className = "p-2 hover:bg-slate-100 cursor-pointer text-xs border-b last:border-0";
            div.innerHTML =
              `<strong>${p.apellido_paterno} ${p.apellido_materno} ${p.nombres}</strong> <span class='text-slate-400 text-[10px]'>(${p.doc})</span>`;
            div.onclick = () => {
              rellenarDatos(p);
              lista.classList.add('hidden');
            };
            lista.appendChild(div);
          });
        }
      }, 300);
    }

    function rellenarDatos(p) {
      document.getElementById('personal_nombre').value = `${p.apellido_paterno} ${p.apellido_materno} ${p.nombres}`;
      document.getElementById('personal_dni').value = p.doc;
      document.getElementById('personal_tipo_doc').value = (p.tipo_doc == 'DNI' || p.tipo_doc == 'CE') ? p.tipo_doc :
        'DNI';
    }

    window.toggleCapacitacion = (s) => document.getElementById('div-capacitacion-detalles').classList.toggle('hidden', !
      s);
    window.toggleReportesPrenatal = (s) => document.getElementById('div-reportes-prenatal-detalle').classList.toggle(
      'hidden', !s);
    window.toggleOtrosCapacitacion = (c) => document.getElementById('div-capacitacion-otros').classList.toggle('hidden', !
      c.checked);

    window.agregarFila = (tableId, baseName) => {
      const tbody = document.querySelector(`#${tableId} tbody`);
      const id = Date.now();
      const tr = document.createElement('tr');
      tr.innerHTML =
        `<td><input type="text" name="${baseName}[${id}][nombre]" class="table-input font-bold"></td><td><select name="${baseName}[${id}][propiedad]" class="table-input text-xs"><option value="ESTABLECIMIENTO">EESS</option><option value="PROPIO">Propio</option></select></td><td><input type="number" name="${baseName}[${id}][cantidad]" value="1" class="table-input text-center"></td><td><select name="${baseName}[${id}][estado]" class="table-input text-xs"><option value="Bueno">Bueno</option><option value="Regular">Regular</option><option value="Malo">Malo</option></select></td><td class="text-center"><button type="button" onclick="this.closest('tr').remove()" class="text-slate-300 hover:text-red-500"><i data-lucide="trash-2" class="w-4 h-4"></i></button></td>`;
      tbody.appendChild(tr);
      lucide.createIcons();
    }

    window.switchTab = (t) => {
      document.getElementById('tab-local').className = t == 'local' ?
        'pb-2 text-sm font-bold text-indigo-600 border-b-2 border-indigo-600' :
        'pb-2 text-sm font-bold text-slate-400 hover:text-indigo-500';
      document.getElementById('tab-server').className = t == 'server' ?
        'pb-2 text-sm font-bold text-indigo-600 border-b-2 border-indigo-600' :
        'pb-2 text-sm font-bold text-slate-400 hover:text-indigo-500';
      document.getElementById('panel-local').style.display = t == 'local' ? 'block' : 'none';
      document.getElementById('panel-server').style.display = t == 'server' ? 'block' : 'none';
    }

    window.handleFiles = (files) => {
      Array.from(files).forEach(f => {
        if (evidenceList.length >= MAX_PHOTOS) evidenceList.shift();
        evidenceList.push({
          type: 'local',
          file: f,
          url: URL.createObjectURL(f),
          id: Date.now() + Math.random()
        });
      });
      renderGallery();
      syncInputs();
    }
    window.removeImage = (id) => {
      evidenceList = evidenceList.filter(i => i.id !== id);
      renderGallery();
      syncInputs();
    }

    function renderGallery() {
      const cont = document.getElementById('gallery-container');
      cont.querySelectorAll('.group').forEach(e => e.remove()); // Limpiar galería previa

      document.getElementById('empty-state').style.display = evidenceList.length ? 'none' : 'block';

      evidenceList.forEach(i => {
        const d = document.createElement('div');
        d.className = "relative group aspect-square rounded-xl overflow-hidden border bg-white shadow-sm";

        // Determinar el color y texto del badge según el origen
        const badgeColor = i.type === 'local' ? 'bg-indigo-500' : 'bg-emerald-500';
        const badgeText = i.type.toUpperCase();

        d.innerHTML = `
            <img src="${i.url}" class="w-full h-full object-cover">
            <button type="button" onclick="removeImage(${i.id})" class="absolute top-1 right-1 bg-red-500 text-white p-1 rounded-full shadow-lg">
                <i data-lucide="x" class="w-3 h-3"></i>
            </button>
            <div class="absolute top-2 left-2 ${badgeColor} text-white text-[9px] font-bold px-2 py-0.5 rounded-full shadow-sm">
                <span>${badgeText}</span>
            </div>
        `;
        cont.appendChild(d);
      });
      lucide.createIcons(); // Re-inicializar iconos de Lucide
    }

    function syncInputs() {
      const dt = new DataTransfer();
      evidenceList.filter(i => i.type === 'local').forEach(i => dt.items.add(i.file));
      document.getElementById('final-input-files').files = dt.files;
      document.getElementById('final-input-server').value = JSON.stringify(evidenceList.filter(i => i.type === 'server')
        .map(i => i.url));
    }

    function initSignaturePad() {
      canvas = document.getElementById('signature-pad');
      ctx = canvas.getContext('2d');
      ctx.lineWidth = 2;
      ctx.lineCap = 'round';
      ctx.strokeStyle = '#0f172a';
      const getP = (e) => {
        const r = canvas.getBoundingClientRect();
        return {
          x: (e.clientX || e.touches[0].clientX) - r.left,
          y: (e.clientY || e.touches[0].clientY) - r.top
        };
      };
      const start = (e) => {
        isDrawing = true;
        const p = getP(e);
        ctx.beginPath();
        ctx.moveTo(p.x, p.y);
      };
      const move = (e) => {
        if (!isDrawing) return;
        const p = getP(e);
        ctx.lineTo(p.x, p.y);
        ctx.stroke();
        e.preventDefault();
      };
      canvas.addEventListener('mousedown', start);
      canvas.addEventListener('mousemove', move);
      window.addEventListener('mouseup', () => isDrawing = false);
      canvas.addEventListener('touchstart', start);
      canvas.addEventListener('touchmove', move);
    }

    function resizeCanvas() {
      if (!canvas) return;
      const ratio = Math.max(window.devicePixelRatio || 1, 1);
      const w = canvas.offsetWidth,
        h = canvas.offsetHeight;
      const data = canvas.toDataURL();
      canvas.width = w * ratio;
      canvas.height = h * ratio;
      ctx.scale(ratio, ratio);
      ctx.lineWidth = 2;
      ctx.lineCap = 'round';
      const img = new Image();
      img.src = data;
      img.onload = () => ctx.drawImage(img, 0, 0, w, h);
    }
    window.clearSignature = () => {
      ctx.clearRect(0, 0, canvas.width, canvas.height);
      document.getElementById('firma_input').value = '';
    }
    window.saveSignature = () => {
      document.getElementById('firma_input').value = canvas.toDataURL();
    }
  </script>
@endpush
