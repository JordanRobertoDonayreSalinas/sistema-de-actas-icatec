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
        {{-- PASOS REDUCIDOS A 4 (SIN FIRMA) --}}
        @foreach ([1 => ['user', 'Responsable'], 2 => ['package', 'Equipamiento'], 3 => ['database', 'Datos'], 4 => ['camera', 'Evidencias']] as $i => $data)
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

            @php
              $rawEnte = $registro->capacitacion_entes ?? '';
              // Asegurar valor único si antes era array
              $valorGuardado = is_array($rawEnte) ? $rawEnte[0] ?? '' : $rawEnte;
            @endphp

            <div id="div-capacitacion-detalles"
              class="{{ ($registro->capacitacion_recibida ?? '') == 'SI' ? '' : 'hidden' }} mt-4 pt-4 border-t border-slate-100">
              <p class="input-label mb-2">Entidad que capacitó (Seleccione una):</p>
              <div class="flex flex-wrap gap-4 mb-3">
                @foreach (['MINSA', 'DIRESA', 'UNIDAD EJECUTORA'] as $ente)
                  <label class="flex items-center gap-2 cursor-pointer">
                    <input type="radio" name="contenido[capacitacion_ente]" value="{{ $ente }}"
                      class="text-indigo-600 focus:ring-0" {{ $valorGuardado == $ente ? 'checked' : '' }}>
                    <span class="text-xs font-bold text-slate-600">{{ $ente }}</span>
                  </label>
                @endforeach
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

          {{-- EQUIPOS CON LOGICA CORREGIDA --}}
          <div class="bg-white rounded-lg border border-slate-200 shadow-sm overflow-hidden">

            <div
              class="bg-slate-50 border-b border-slate-100 px-4 py-3 flex flex-wrap gap-3 justify-between items-center">
              <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider">Detalle de Equipos</h3>

              <div class="flex items-center gap-2">
                {{-- SELECTOR DE EQUIPOS --}}
                <select id="select-equipo-agregar"
                  class="text-xs border-slate-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 py-1.5 pl-2 pr-8">
                  <option value="" disabled selected>-- Seleccione equipo --</option>
                  @foreach (['Tablet', 'Laptop', 'CPU', 'Monitor', 'Teclado', 'Mouse', 'Impresora', 'Escaner', 'Ticketera', 'Lector de DNIe', 'Lector de Codigo de Barras', 'OTRO'] as $eq)
                    <option value="{{ $eq }}">{{ $eq }}</option>
                  @endforeach
                </select>

                <button type="button" onclick="agregarEquipoDesdeSelect()"
                  class="text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 px-3 py-2 rounded-lg flex items-center gap-2 shadow-sm transition-all">
                  <i data-lucide="plus" class="w-3 h-3"></i> AGREGAR
                </button>
              </div>
            </div>

            <table class="w-full text-left border-collapse" id="tabla-equipos">
              <thead>
                <tr
                  class="bg-slate-50/50 border-b border-slate-200 text-[11px] uppercase text-slate-500 font-bold tracking-wider">
                  <th class="px-3 py-2 w-[25%]">Descripción</th>
                  <th class="px-3 py-2 w-[20%]">N° Serie / Cod.</th>
                  <th class="px-3 py-2 w-[15%]">Propiedad</th>
                  <th class="px-3 py-2 w-[15%]">Estado</th>
                  <th class="px-3 py-2 w-[20%]">Observaciones</th>
                  <th class="px-3 py-2 w-[5%] text-center"></th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100" id="tbody-equipos">
                @php
                  $equiposGuardados = $registro->equipos_listado ?? [];
                @endphp

                @foreach ($equiposGuardados as $idx => $item)
                  <tr class="group hover:bg-slate-50 transition-colors">
                    {{-- 1. Nombre del Equipo --}}
                    <td class="p-2 align-middle">
                      <input type="text" name="contenido[equipos][{{ $idx }}][nombre]"
                        value="{{ $item['nombre'] ?? '' }}"
                        class="w-full bg-transparent border-0 border-b border-transparent focus:border-indigo-500 focus:ring-0 font-bold text-slate-700 text-xs px-2 py-1 placeholder-slate-300"
                        placeholder="Nombre">
                    </td>

                    {{-- 2. Serie / Código --}}
                    <td class="p-2 align-middle">
                      <div class="relative flex items-center">
                        <input type="text" id="serie-input-{{ $idx }}"
                          name="contenido[equipos][{{ $idx }}][serie]" value="{{ $item['serie'] ?? '' }}"
                          class="w-full bg-slate-50 border border-slate-200 text-slate-600 text-[11px] font-mono uppercase rounded pl-2 pr-8 py-1 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 placeholder-slate-400"
                          placeholder="----">

                        <button type="button" onclick="iniciarEscaneo('serie-input-{{ $idx }}')"
                          class="absolute right-0.5 p-1 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded transition-colors cursor-pointer z-10">
                          <i data-lucide="scan-barcode" class="w-3 h-3"></i>
                        </button>
                      </div>
                    </td>

                    {{-- 3. Propiedad --}}
                    <td class="p-2 align-middle">
                      <select name="contenido[equipos][{{ $idx }}][propiedad]"
                        class="w-full bg-white border border-slate-200 text-[11px] text-slate-600 rounded px-1 py-1 focus:border-indigo-500 focus:ring-0 cursor-pointer">
                        <option value="ESTABLECIMIENTO"
                          {{ ($item['propiedad'] ?? '') == 'ESTABLECIMIENTO' ? 'selected' : '' }}>Establecimiento
                        </option>
                        <option value="SERVICIO" {{ ($item['propiedad'] ?? '') == 'SERVICIO' ? 'selected' : '' }}>
                          Servicio</option>
                        <option value="PERSONAL" {{ ($item['propiedad'] ?? '') == 'PERSONAL' ? 'selected' : '' }}>
                          Personal</option>
                      </select>
                    </td>

                    {{-- 4. Estado --}}
                    <td class="p-2 align-middle">
                      <select name="contenido[equipos][{{ $idx }}][estado]"
                        class="w-full bg-white border border-slate-200 text-[11px] rounded px-1 py-1 focus:border-indigo-500 focus:ring-0 cursor-pointer text-slate-600">
                        <option value="Operativo" {{ ($item['estado'] ?? '') == 'Operativo' ? 'selected' : '' }}>
                          Operativo
                        </option>
                        <option value="Regular" {{ ($item['estado'] ?? '') == 'Regular' ? 'selected' : '' }}>Regular
                        </option>
                        <option value="Inoperativo" {{ ($item['estado'] ?? '') == 'Inoperativo' ? 'selected' : '' }}>
                          Inoperativo</option>
                      </select>
                    </td>

                    {{-- 5. Observaciones --}}
                    <td class="p-2 align-middle">
                      <input type="text" name="contenido[equipos][{{ $idx }}][observaciones]"
                        value="{{ $item['observaciones'] ?? '' }}"
                        class="w-full bg-transparent text-[11px] text-slate-500 border-0 border-b border-transparent focus:border-indigo-500 focus:ring-0 placeholder-slate-300 italic px-2 py-1"
                        placeholder="Observaciones...">
                    </td>

                    {{-- 6. Botón Eliminar --}}
                    <td class="p-2 text-center align-middle">
                      <button type="button" onclick="this.closest('tr').remove()"
                        class="text-slate-300 hover:text-red-500 hover:bg-red-50 p-1 rounded transition-all">
                        <i data-lucide="trash-2" class="w-3 h-3"></i>
                      </button>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
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

        {{-- MODAL SCANNER (NECESARIO PARA PASO 2) --}}
        <div id="scanner-modal"
          class="fixed inset-0 z-50 hidden bg-black/90 backdrop-blur-sm flex items-center justify-center p-4 transition-all">
          <div class="bg-white rounded-2xl w-full max-w-md overflow-hidden relative shadow-2xl">
            <div class="p-4 bg-white border-b flex justify-between items-center z-10 relative">
              <h3 class="font-bold text-slate-800 flex items-center gap-2">
                <i data-lucide="scan" class="text-indigo-600"></i> Escáner
              </h3>
              <button type="button" onclick="detenerEscaneo()"
                class="text-slate-400 hover:text-red-500 bg-slate-50 hover:bg-red-50 p-1 rounded-full transition-colors">
                <i data-lucide="x" class="w-5 h-5"></i>
              </button>
            </div>
            <div id="reader" class="w-full bg-black min-h-[250px] relative"></div>
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
                <input type="number" min="0" name="contenido[nro_consultorios]"
                  class="w-20 border border-indigo-200 rounded p-2 text-center font-bold text-indigo-700 bg-white"
                  value="{{ $registro->nro_consultorios ?? 0 }}">
              </div>

              <div class="flex justify-between items-center mb-4">
                <span class="text-xs font-bold text-slate-700 uppercase">Gestantes Registradas (Mes):</span>
                <input type="number" min="0" name="contenido[nro_gestantes_mes]"
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

            {{-- SECCIÓN INFERIOR COMPLETA: Dificultades --}}
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden md:col-span-2">
              <div class="bg-slate-50 px-4 py-3 border-b border-slate-100 flex items-center gap-2">
                <h3 class="text-xs font-bold text-slate-700 uppercase tracking-wide">Reporte de Dificultades</h3>
              </div>

              <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-8 relative">

                {{-- Pregunta 1 --}}
                <div>
                  <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-3">1. ¿A quién comunica?
                  </p>
                  <div class="grid grid-cols-3 gap-3">
                    @foreach (['MINSA', 'DIRESA', 'Establecimiento'] as $opcion)
                      <label class="cursor-pointer group relative">
                        <input type="radio" name="contenido[dificultades][comunica]" value="{{ $opcion }}"
                          class="peer sr-only"
                          {{ ($registro->dificultad_comunica_a ?? '') == $opcion ? 'checked' : '' }}>
                        <div
                          class="text-center py-3 px-1 rounded-lg border border-slate-200 bg-white transition-all peer-checked:border-indigo-500 peer-checked:bg-indigo-50/50 peer-checked:shadow-sm group-hover:border-indigo-300 h-full flex items-center justify-center">
                          <span
                            class="block text-[10px] font-bold text-slate-500 peer-checked:text-indigo-700">{{ $opcion }}</span>
                        </div>
                        <div
                          class="absolute -top-2 -right-2 bg-indigo-600 text-white rounded-full p-0.5 opacity-0 peer-checked:opacity-100 transition-opacity shadow-sm">
                          <i data-lucide="check" class="w-2 h-2"></i>
                        </div>
                      </label>
                    @endforeach
                  </div>
                </div>

                {{-- Línea divisoria --}}
                <div class="hidden md:block absolute top-4 bottom-4 left-1/2 w-px bg-slate-100 -translate-x-1/2">
                </div>

                {{-- Pregunta 2 --}}
                <div>
                  <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-3">2. ¿Qué medio utiliza?
                  </p>
                  <div class="grid grid-cols-3 gap-3">
                    @foreach (['WhatsApp', 'Teléfono', 'Email'] as $opcion)
                      <label class="cursor-pointer group relative">
                        <input type="radio" name="contenido[dificultades][medio]" value="{{ $opcion }}"
                          class="peer sr-only"
                          {{ ($registro->dificultad_medio_uso ?? '') == $opcion ? 'checked' : '' }}>
                        <div
                          class="text-center py-3 px-1 rounded-lg border border-slate-200 bg-white transition-all peer-checked:border-indigo-500 peer-checked:bg-indigo-50/50 peer-checked:shadow-sm group-hover:border-indigo-300 h-full flex items-center justify-center">
                          <span
                            class="block text-[10px] font-bold text-slate-500 peer-checked:text-indigo-700">{{ $opcion }}</span>
                        </div>
                        <div
                          class="absolute -top-2 -right-2 bg-indigo-600 text-white rounded-full p-0.5 opacity-0 peer-checked:opacity-100 transition-opacity shadow-sm">
                          <i data-lucide="check" class="w-2 h-2"></i>
                        </div>
                      </label>
                    @endforeach
                  </div>
                </div>

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
            <h3 class="input-label mb-3 flex justify-between"><span>Archivos Seleccionados </span><span
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
      </div>

      {{-- NAVEGACIÓN --}}
      <div class="mt-6 flex justify-between items-center">
        <button type="button" class="btn-nav btn-prev" id="btn-prev" onclick="changeStep(-1)"
          style="visibility: hidden;"><i data-lucide="arrow-left" class="w-4 h-4"></i> Anterior</button>
        <div>
          <button type="button" class="btn-nav btn-next" id="btn-next" onclick="changeStep(1)">Siguiente <i
              data-lucide="arrow-right" class="w-4 h-4"></i></button>
          {{-- BOTON GUARDAR EN STEP 4 --}}
          <button type="submit" class="btn-nav btn-finish" id="btn-submit" style="display: none;"><i
              data-lucide="check-circle" class="w-4 h-4"></i> Finalizar y Guardar</button>
        </div>
      </div>
    </form>
  </div>

@endsection

@push('scripts')
  <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
  <script>
    let evidenceList = [],
      currentStep = 1;
    const totalSteps = 4,
      MAX_PHOTOS = 2;
    let timeoutNombre = null;

    document.addEventListener('DOMContentLoaded', () => {
      lucide.createIcons();

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
    });

    // ... (El resto de funciones de navegación y búsqueda siguen igual) ...
    window.showStep = function(step) {
      document.querySelectorAll('.step-content').forEach(el => el.classList.remove('active'));
      document.getElementById(`step-${step}`).classList.add('active');
      for (let i = 1; i <= totalSteps; i++) {
        const circle = document.getElementById(`circle-${i}`);
        const label = document.getElementById(`label-${i}`);
        const line = document.getElementById(`line-${i}`);
        if (circle) {
          circle.classList.remove('active', 'completed');
          if (label) label.classList.remove('active');
          if (line) line.classList.remove('active');
          if (i < step) {
            circle.classList.add('completed');
            circle.innerHTML = '<i data-lucide="check" class="w-5 h-5"></i>';
            if (line) line.classList.add('active');
          } else if (i === step) {
            circle.classList.add('active');
            circle.innerHTML = getIconForStep(i);
            if (label) label.classList.add('active');
          } else {
            circle.innerHTML = getIconForStep(i);
          }
        }
      }
      lucide.createIcons();
      const btnPrev = document.getElementById('btn-prev');
      const btnNext = document.getElementById('btn-next');
      const btnSubmit = document.getElementById('btn-submit');
      if (btnPrev) btnPrev.style.visibility = step === 1 ? 'hidden' : 'visible';
      if (step === totalSteps) {
        btnNext.style.display = 'none';
        btnSubmit.style.display = 'flex';
      } else {
        btnNext.style.display = 'flex';
        btnSubmit.style.display = 'none';
      }
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

    // 1. BUSCAR POR NUMERO DE DOCUMENTO (Al salir del input o dar Enter)
    async function buscarPorDoc() {
      const doc = document.getElementById('personal_dni').value.trim();
      const tipo = document.getElementById('personal_tipo_doc').value;
      const loader = document.getElementById('loading-doc');
      const msg = document.getElementById('msg-doc');

      if (doc.length < 5) return; // Validación básica

      loader.classList.remove('hidden');
      msg.classList.add('hidden');

      try {
        // Llamada al backend
        const response = await fetch(`{{ route('usuario.monitoreo.citas.buscar.profesional') }}?type=doc&q=${doc}`);
        const data = await response.json();

        if (data.length > 0) {
          // Encontrado: Rellenar datos
          rellenarDatos(data[0]);
          msg.textContent = "Personal encontrado.";
          msg.className = "text-[10px] text-green-600 mt-1";
          msg.classList.remove('hidden');
        } else {
          // CAMBIO AQUÍ: Mensaje amigable indicando que se creará nuevo
          msg.textContent = "Personal nuevo. Complete los nombres y se guardará automáticamente.";
          msg.className = "text-[10px] text-blue-600 mt-1 font-bold"; // Color azul para indicar info, no error fatal
          msg.classList.remove('hidden');

          // Opcional: Limpiar el campo nombre para que escriban el nuevo
          document.getElementById('personal_nombre').value = '';
          document.getElementById('personal_nombre').focus();
        }
      } catch (error) {
        console.error('Error:', error);
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

    // LOGICA TABLA EQUIPOS
    let equipoIndex = {{ count($registro->equipos_listado ?? []) }};

    function agregarEquipoDesdeSelect() {
      const select = document.getElementById('select-equipo-agregar');
      const tipoEquipo = select.value;
      if (!tipoEquipo) {
        alert("Por favor seleccione un equipo de la lista.");
        return;
      }
      const tbody = document.getElementById('tbody-equipos');
      const esOtro = tipoEquipo === 'OTRO';
      const valorNombre = esOtro ? '' : tipoEquipo;
      const inputNombre =
        `<input type="text" name="contenido[equipos][${equipoIndex}][nombre]" value="${valorNombre}" class="w-full bg-transparent border-0 border-b border-transparent focus:border-indigo-500 focus:ring-0 font-bold text-slate-700 text-xs px-2 py-1 placeholder-slate-300" placeholder="Escriba nombre..." ${esOtro ? 'autofocus' : ''}>`;

      const fila = `
          <tr class="group hover:bg-slate-50 transition-colors">
              <td class="p-2 align-middle">${inputNombre}</td>
              <td class="p-2 align-middle">
                  <div class="relative flex items-center">
                      <input type="text" id="serie-input-${equipoIndex}" name="contenido[equipos][${equipoIndex}][serie]" class="w-full bg-slate-50 border border-slate-200 text-slate-600 text-[11px] font-mono uppercase rounded pl-2 pr-8 py-1 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 placeholder-slate-400" placeholder="----">
                      <button type="button" onclick="iniciarEscaneo('serie-input-${equipoIndex}')" class="absolute right-0.5 p-1 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded transition-colors cursor-pointer z-10"><i data-lucide="scan-barcode" class="w-3 h-3"></i></button>
                  </div>
              </td>
              <td class="p-2 align-middle">
                  <select name="contenido[equipos][${equipoIndex}][propiedad]" class="w-full bg-white border border-slate-200 text-[11px] text-slate-600 rounded px-1 py-1 focus:border-indigo-500 focus:ring-0 cursor-pointer">
                      <option value="ESTABLECIMIENTO" selected>Establecimiento</option>
                      <option value="SERVICIO">Servicio</option>
                      <option value="PERSONAL">Personal</option>
                  </select>
              </td>
              <td class="p-2 align-middle">
                  <select name="contenido[equipos][${equipoIndex}][estado]" class="w-full bg-white border border-slate-200 text-[11px] rounded px-1 py-1 focus:border-indigo-500 focus:ring-0 cursor-pointer text-slate-600">
                      <option value="Operativo" selected>Operativo</option>
                      <option value="Regular">Regular</option>
                      <option value="Inoperativo">Inoperativo</option>
                  </select>
              </td>
              <td class="p-2 align-middle">
                  <input type="text" name="contenido[equipos][${equipoIndex}][observaciones]" class="w-full bg-transparent text-[11px] text-slate-500 border-0 border-b border-transparent focus:border-indigo-500 focus:ring-0 placeholder-slate-300 italic px-2 py-1" placeholder="Observaciones...">
              </td>
              <td class="p-2 text-center align-middle">
                  <button type="button" onclick="this.closest('tr').remove()" class="text-slate-300 hover:text-red-500 hover:bg-red-50 p-1 rounded transition-all"><i data-lucide="trash-2" class="w-3 h-3"></i></button>
              </td>
          </tr>`;
      tbody.insertAdjacentHTML('beforeend', fila);
      if (typeof lucide !== 'undefined') lucide.createIcons();
      select.value = "";
      equipoIndex++;
    }

    // --- EVIDENCIAS (AQUÍ ESTÁ LA CORRECCIÓN) ---
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
    window.openServerModal = () => alert("🚧 MANTENIMIENTO 🚧\nExplorador habilitado en producción.");

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
      const countDisplay = document.getElementById('count-display'); // <--- 1. SELECCIONAMOS EL CONTADOR

      cont.querySelectorAll('.group').forEach(e => e.remove());
      document.getElementById('empty-state').style.display = evidenceList.length ? 'none' : 'block';

      evidenceList.forEach(i => {
        const d = document.createElement('div');
        d.className = "relative group aspect-square rounded-xl overflow-hidden border bg-white shadow-sm";
        const badgeColor = i.type === 'local' ? 'bg-indigo-500' : 'bg-emerald-500';
        const badgeText = i.type.toUpperCase();
        d.innerHTML = `
            <img src="${i.url}" class="w-full h-full object-cover">
            <button type="button" onclick="removeImage(${i.id})" class="absolute top-1 right-1 bg-red-500 text-white p-1 rounded-full shadow-lg"><i data-lucide="x" class="w-3 h-3"></i></button>
            <div class="absolute top-2 left-2 ${badgeColor} text-white text-[9px] font-bold px-2 py-0.5 rounded-full shadow-sm"><span>${badgeText}</span></div>
        `;
        cont.appendChild(d);
      });

      // <--- 2. ACTUALIZAMOS EL TEXTO DEL CONTADOR
      if (countDisplay) {
        countDisplay.innerText = `${evidenceList.length} / ${MAX_PHOTOS}`;
      }

      lucide.createIcons();
    }

    function syncInputs() {
      const dt = new DataTransfer();
      evidenceList.filter(i => i.type === 'local').forEach(i => dt.items.add(i.file));
      document.getElementById('final-input-files').files = dt.files;
      document.getElementById('final-input-server').value = JSON.stringify(evidenceList.filter(i => i.type === 'server')
        .map(i => i.url));
    }

    // LOGICA SCANNER
    let html5QrcodeScanner = null;
    let currentInputId = null;

    function iniciarEscaneo(inputId) {
      currentInputId = inputId;
      document.getElementById('scanner-modal').classList.remove('hidden');
      html5QrcodeScanner = new Html5Qrcode("reader");
      html5QrcodeScanner.start({
          facingMode: "environment"
        }, {
          fps: 10,
          qrbox: {
            width: 250,
            height: 250
          }
        }, onScanSuccess, onScanFailure)
        .catch(err => {
          console.error(err);
          alert("Error de cámara");
          document.getElementById('scanner-modal').classList.add('hidden');
        });
    }

    function onScanSuccess(decodedText) {
      if (currentInputId) document.getElementById(currentInputId).value = decodedText;
      detenerEscaneo();
    }

    function onScanFailure(error) {}

    function detenerEscaneo() {
      if (html5QrcodeScanner) {
        html5QrcodeScanner.stop().then(() => {
          html5QrcodeScanner.clear();
          document.getElementById('scanner-modal').classList.add('hidden');
          currentInputId = null;
        });
      } else {
        document.getElementById('scanner-modal').classList.add('hidden');
      }
    }
  </script>
@endpush
