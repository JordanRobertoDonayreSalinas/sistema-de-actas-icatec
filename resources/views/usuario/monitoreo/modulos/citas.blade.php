@extends('layouts.usuario')
@section('title', 'Monitoreo - Citas')

@push('styles')
  <style>
    /* ... (TUS ESTILOS CSS ORIGINALES SE MANTIENEN IGUAL) ... */
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
      /* Antes era 2rem */
      padding: 0 0.5rem;
      /* Reducimos el padding lateral */
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

    /* 2. Compactar la tarjeta blanca del formulario */
    .form-card {
      background: white;
      border-radius: 1rem;
      box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
      border: 1px solid #f1f5f9;
      padding: 1.25rem;
      /* IMPORTANTE: Antes era 2rem. Esto gana mucho espacio en los bordes */
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
        @foreach ([1 => ['user', 'Responsable'], 2 => ['package', 'Equipamiento'], 3 => ['bar-chart-2', 'Gestión'], 4 => ['camera', 'Evidencias'], 5 => ['pen-tool', 'Firma']] as $i => $data)
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


    <form action="{{ route('usuario.monitoreo.citas.create', $acta->id) }}" method="POST" enctype="multipart/form-data"
      id="mainForm">
      @csrf
      {{-- No necesitamos input hidden 'modulo_nombre' aquí si lo manejas en controlador o migracion, pero lo dejo por si acaso --}}

      <div class="form-card">

        {{-- PASO 1: PERSONAL --}}
        <div id="step-1" class="step-content active">
          <div class="mb-6 border-b border-slate-100 pb-4">
            <h2 class="text-2xl font-bold text-slate-800">Responsable Único</h2>
            <p class="text-slate-500 text-sm">Información del encargado.</p>
          </div>

          {{-- FILA 1: Identificación --}}
          <div class="grid grid-cols-1 md:grid-cols-12 gap-4 mb-4">
            {{-- 1. Tipo de Documento --}}
            <div class="md:col-span-3">
              <label class="input-label">Tipo Documento</label>
              <select name="contenido[personal_tipo_doc]" id="personal_tipo_doc" class="input-blue">
                <option value="DNI" {{ ($registro->personal_tipo_doc ?? '') == 'DNI' ? 'selected' : '' }}>DNI</option>
                <option value="CE" {{ ($registro->personal_tipo_doc ?? '') == 'CE' ? 'selected' : '' }}>C.E.</option>
                <option value="OTRO" {{ ($registro->personal_tipo_doc ?? '') == 'OTRO' ? 'selected' : '' }}>OTRO
                </option>
              </select>
            </div>

            {{-- 2. Número de Documento --}}
            <div class="md:col-span-3">
              <label class="input-label">Nro. Documento</label>
              <div class="relative">
                <input type="text" name="contenido[personal_dni]" id="personal_dni" maxlength="15"
                  class="input-blue font-bold" placeholder="Ingrese y presione Enter"
                  value="{{ $registro->personal_dni ?? '' }}" onblur="buscarPorDoc()"
                  onkeydown="if(event.key === 'Enter'){event.preventDefault(); buscarPorDoc();}">
                <div id="loading-doc" class="hidden absolute right-3 top-2.5">
                  <i data-lucide="loader-2" class="w-4 h-4 animate-spin text-indigo-600"></i>
                </div>
              </div>
              <p id="msg-doc" class="text-[10px] text-red-500 mt-1 hidden"></p>
            </div>

            {{-- 3. Turno --}}
            <div class="md:col-span-3">
              <label class="input-label text-center">Turno</label>
              <select name="contenido[personal_turno]" class="input-blue text-center">
                <option value="MAÑANA" {{ ($registro->personal_turno ?? '') == 'MAÑANA' ? 'selected' : '' }}>MAÑANA
                </option>
                <option value="TARDE" {{ ($registro->personal_turno ?? '') == 'TARDE' ? 'selected' : '' }}>TARDE
                </option>
                <option value="NOCHE" {{ ($registro->personal_turno ?? '') == 'NOCHE' ? 'selected' : '' }}>NOCHE
                </option>
              </select>
            </div>

            {{-- 4. Roles --}}
            <div class="md:col-span-3 relative">
              <label class="input-label text-center">Roles Asignados</label>
              <button type="button" onclick="toggleRolDropdown()"
                class="input-blue flex justify-between items-center bg-white w-full text-left">
                <span id="rol-selected-text" class="text-xs truncate block pr-2">-- Seleccionar --</span>
                <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400"></i>
              </button>
              <div id="rol-dropdown-list"
                class="hidden absolute z-50 top-full mt-1 w-full bg-white border border-slate-200 rounded-lg shadow-xl p-2 max-h-60 overflow-y-auto">
                <div class="space-y-1">
                  @foreach (['ADMISIONISTA', 'CAJERO'] as $rol)
                    <label
                      class="flex items-center gap-2 p-2 hover:bg-slate-50 rounded cursor-pointer border-b border-slate-50 last:border-0">
                      <input type="checkbox" name="contenido[personal_rol][]" value="{{ $rol }}"
                        class="rounded text-indigo-600 focus:ring-0 border-slate-300" onchange="updateRolText()"
                        {{ in_array($rol, $registro->personal_roles ?? []) ? 'checked' : '' }}>
                      <span class="text-xs font-bold text-slate-600">{{ $rol }}</span>
                    </label>
                  @endforeach
                </div>
              </div>
            </div>
          </div>

          {{-- FILA 2: Nombre Completo --}}
          <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
            <div class="md:col-span-12 relative">
              <label class="input-label">Apellidos y Nombres</label>
              <input type="text" name="contenido[personal_nombre]" id="personal_nombre" class="input-blue"
                placeholder="Escriba para buscar coincidencias..." autocomplete="off"
                value="{{ $registro->personal_nombre ?? '' }}" oninput="buscarPorNombre()">
              <div id="lista-sugerencias"
                class="hidden absolute z-50 w-full bg-white border border-slate-200 rounded-lg shadow-lg mt-1 max-h-60 overflow-y-auto">
              </div>
            </div>
          </div>

          {{-- SECCIÓN CAPACITACIÓN --}}
          <div class="md:col-span-12 bg-slate-50 p-4 rounded-lg border border-slate-100 mt-4">

            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
              <div>
                <p class="text-sm font-bold text-slate-700">¿El personal recibió capacitación?</p>
              </div>
              <div class="toggle-group">
                <label>
                  <input type="radio" name="contenido[capacitacion]" value="SI" class="toggle-radio"
                    onchange="toggleCapacitacion(true)"
                    {{ ($registro->capacitacion_recibida ?? '') == 'SI' ? 'checked' : '' }}>
                  <span class="toggle-btn"><i data-lucide="check" class="w-4 h-4"></i> SÍ</span>
                </label>
                <label>
                  <input type="radio" name="contenido[capacitacion]" value="NO" class="toggle-radio"
                    onchange="toggleCapacitacion(false)"
                    {{ ($registro->capacitacion_recibida ?? '') == 'NO' ? 'checked' : '' }}>
                  <span class="toggle-btn"><i data-lucide="x" class="w-4 h-4"></i> NO</span>
                </label>
              </div>
            </div>

            {{-- Lógica PHP para evitar errores de Array vs String --}}
            @php
              $rawEnte = $registro->capacitacion_entes ?? '';
              // Si por error viene un array, tomamos el primer valor. Si es texto, lo usamos directo.
              $valorGuardado = is_array($rawEnte) ? $rawEnte[0] ?? '' : $rawEnte;
            @endphp

            <div id="div-capacitacion-detalles"
              class="{{ ($registro->capacitacion_recibida ?? '') == 'SI' ? '' : 'hidden' }} mt-4 pt-4 border-t border-slate-200">

              <p class="input-label mb-2">Entidad que capacitó (Seleccione una):</p>

              <div class="flex flex-wrap gap-4">

                {{-- NUEVA LISTA FIJA --}}
                @foreach (['MINSA', 'DIRESA', 'UNIDAD EJECUTORA'] as $ente)
                  <label class="flex items-center gap-2 cursor-pointer">
                    {{-- Radio Button Simple: name="...[capacitacion_ente]" sin corchetes --}}
                    <input type="radio" name="contenido[capacitacion_ente]" value="{{ $ente }}"
                      class="text-indigo-600 focus:ring-0" {{ $valorGuardado == $ente ? 'checked' : '' }}>

                    <span class="text-xs font-bold text-slate-600">{{ $ente }}</span>
                  </label>
                @endforeach

              </div>
              {{-- Ya no hay campo de texto ni opción "OTROS" --}}
            </div>
          </div>
        </div>

        {{-- PASO 2: LOGÍSTICA --}}
        <div id="step-2" class="step-content">

          <div class="mb-6 border-b border-slate-100 pb-4">
            <h2 class="text-2xl font-bold text-slate-800">Equipamiento e Insumos</h2>
          </div>

          <div class="bg-slate-50 p-6 rounded-xl border border-slate-100 mb-8">
            <div class="flex items-center gap-2 mb-4">
              <h3 class="input-label mb-0 text-slate-600">Al iniciar sus labores diarias cuenta con:</h3>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
              @foreach (['TICKET', 'FUA', 'HOJA DE FILIACIÓN', 'PAPEL BOND', 'TONER / TINTA', 'LAPICEROS'] as $insumo)
                <label
                  class="flex items-center gap-3 cursor-pointer bg-white p-3 rounded-lg border border-slate-200 hover:border-indigo-400 group transition-colors">
                  <input type="checkbox" name="contenido[insumos][]" value="{{ $insumo }}"
                    class="rounded text-indigo-600 focus:ring-0 border-slate-300"
                    {{ in_array($insumo, $registro->insumos_disponibles ?? []) ? 'checked' : '' }}>
                  <span
                    class="text-[11px] font-bold text-slate-600 group-hover:text-indigo-700 uppercase">{{ $insumo }}</span>
                </label>
              @endforeach
            </div>
          </div>

          <div class="bg-white rounded-lg border border-slate-200 shadow-sm overflow-hidden">

            <div class="bg-slate-50 border-b border-slate-100 px-4 py-3 flex justify-between items-center">
              <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider">Detalle de Equipos</h3>
              <button type="button" onclick="agregarFilaEquipo()"
                class="text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 px-3 py-2 rounded-lg flex items-center gap-2 shadow-sm transition-all">
                <i data-lucide="plus" class="w-3 h-3"></i> AGREGAR
              </button>
            </div>

            <table class="w-full text-left border-collapse" id="tabla-equipos">
              <thead>
                <tr
                  class="bg-slate-50/50 border-b border-slate-200 text-[11px] uppercase text-slate-500 font-bold tracking-wider">
                  <th class="px-3 py-2 w-[15%]">Descripción</th>
                  <th class="px-3 py-2 w-[20%]">N° Serie / Cod.</th>
                  <th class="px-3 py-2 w-[15%]">Propiedad</th>
                  <th class="px-3 py-2 w-[15%]">Estado</th>
                  <th class="px-3 py-2 w-[30%]">Observaciones</th>
                  <th class="px-3 py-2 w-[5%] text-center"></th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100" id="tbody-equipos">
                @php
                  $equipos = $registro->equipos_listado ?? [];
                  // Si no hay equipos guardados, mostramos los por defecto
                  $defaultItems = [
                      ['nombre' => 'Monitor', 'propiedad' => 'ESTABLECIMIENTO', 'estado' => 'Bueno'],
                      ['nombre' => 'CPU', 'propiedad' => 'ESTABLECIMIENTO', 'estado' => 'Bueno'],
                      ['nombre' => 'Teclado', 'propiedad' => 'ESTABLECIMIENTO', 'estado' => 'Bueno'],
                      ['nombre' => 'Mouse', 'propiedad' => 'ESTABLECIMIENTO', 'estado' => 'Bueno'],
                      ['nombre' => 'Impresora', 'propiedad' => 'ESTABLECIMIENTO', 'estado' => 'Bueno'],
                      ['nombre' => 'Ticketera', 'propiedad' => 'ESTABLECIMIENTO', 'estado' => 'Bueno'],
                  ];
                  $items = count($equipos) > 0 ? $equipos : $defaultItems;
                @endphp

                @foreach ($items as $idx => $item)
                  <tr class="group hover:bg-slate-50 transition-colors">
                    <td class="p-2 align-middle">
                      <input type="text" name="contenido[equipos][{{ $idx }}][nombre]"
                        value="{{ $item['nombre'] ?? '' }}"
                        class="w-full bg-transparent border-0 border-b border-transparent focus:border-indigo-500 focus:ring-0 font-bold text-slate-700 text-xs px-2 py-1 placeholder-slate-300"
                        placeholder="Nombre">
                    </td>

                    <td class="p-2 align-middle">
                      <div class="relative flex items-center">
                        <input type="text" id="serie-input-{{ $idx }}"
                          name="contenido[equipos][{{ $idx }}][serie]" value="{{ $item['serie'] ?? '' }}"
                          class="w-full bg-slate-50 border border-slate-200 text-slate-600 text-[11px] font-mono uppercase rounded pl-2 pr-8 py-1 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 placeholder-slate-400"
                          placeholder="----">

                        <button type="button" onclick="iniciarEscaneo('serie-input-{{ $idx }}')"
                          class="absolute right-0.5 p-1 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded transition-colors cursor-pointer z-10"
                          title="Escanear">
                          <i data-lucide="scan-barcode" class="w-3 h-3"></i>
                        </button>
                      </div>
                    </td>

                    <td class="p-2 align-middle">
                      <select name="contenido[equipos][{{ $idx }}][propiedad]"
                        class="w-full bg-white border border-slate-200 text-[11px] text-slate-600 rounded px-1 py-1 focus:border-indigo-500 focus:ring-0 cursor-pointer">
                        <option value="ESTABLECIMIENTO"
                          {{ ($item['propiedad'] ?? '') == 'ESTABLECIMIENTO' ? 'selected' : '' }}>Establecimiento
                        </option>
                        <option value="PROPIO" {{ ($item['propiedad'] ?? '') == 'PROPIO' ? 'selected' : '' }}>Propio
                        </option>
                      </select>
                    </td>

                    <td class="p-2 align-middle">
                      <select name="contenido[equipos][{{ $idx }}][estado]"
                        class="w-full bg-white border border-slate-200 text-[11px] rounded px-1 py-1 focus:border-indigo-500 focus:ring-0 cursor-pointer
                                {{ ($item['estado'] ?? '') == 'Malo' || ($item['estado'] ?? '') == 'Inoperativo' ? 'text-red-600 font-bold bg-red-50' : 'text-slate-600' }}">
                        @foreach (['Bueno', 'Regular', 'Malo', 'Inoperativo'] as $est)
                          <option value="{{ $est }}" {{ ($item['estado'] ?? '') == $est ? 'selected' : '' }}>
                            {{ $est }}</option>
                        @endforeach
                      </select>
                    </td>

                    <td class="p-2 align-middle">
                      <input type="text" name="contenido[equipos][{{ $idx }}][observaciones]"
                        value="{{ $item['observaciones'] ?? '' }}"
                        class="w-full bg-transparent text-[11px] text-slate-500 border-0 border-b border-transparent focus:border-indigo-500 focus:ring-0 placeholder-slate-300 italic px-2 py-1"
                        placeholder="Observaciones...">
                    </td>

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

          <div class="mt-4 bg-slate-50 p-4 rounded-xl border border-slate-200">
            <label class="flex items-center gap-2 text-xs font-bold text-slate-500 uppercase mb-2">
              <i data-lucide="message-square" class="w-4 h-4"></i> Observaciones Adicionales
            </label>
            <textarea name="contenido[equipos_observaciones]" rows="3"
              placeholder="Describa aquí algun comentario y/o dificultad adicional..."
              class="w-full bg-white border border-slate-300 rounded-lg p-3 text-sm resize-none focus:ring-indigo-500 focus:border-indigo-500">{{ $registro->equipos_observaciones ?? '' }}</textarea>
          </div>

        </div>

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

        {{-- PASO 3: GESTIÓN --}}
        <div id="step-3" class="step-content">
          <div class="mb-6 border-b border-slate-100 pb-4">
            <h2 class="text-2xl font-bold text-slate-800">Gestión y Calidad</h2>
          </div>
          <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
            <div>
              <div class="flex flex-col md:flex-row md:justify-between md:items-end mb-5 gap-4">
                <div>
                  <h2 class="text-l font-black text-slate-700 uppercase">Citas Otorgadas</h2>
                </div>
                <div class="bg-indigo-50 px-3 py-2 rounded-lg border border-indigo-100 flex items-center gap-3">
                  <label class="text-[10px] font-bold text-indigo-600 uppercase">Nro. Ventanillas:</label>
                  <input type="number" name="contenido[nro_ventanillas]"
                    class="w-14 bg-white border border-indigo-200 text-center font-bold text-lg rounded-md text-indigo-700"
                    placeholder="0" min="0" value="{{ $registro->nro_ventanillas ?? 0 }}">
                </div>
              </div>
              <div class="overflow-hidden rounded-lg border border-slate-200 shadow-sm mb-4">
                <table class="blue-table mb-0" id="tabla-produccion">
                  <thead>
                    <tr>
                      <th>Servicio</th>
                      <th class="text-center w-32">Total Citas</th>
                      <th class="w-10"></th>
                    </tr>
                  </thead>
                  <tbody>
                    @php
                      $produccion = $registro->produccion_listado ?? [];
                      $prodItems =
                          count($produccion) > 0
                              ? $produccion
                              : [
                                  ['nombre' => 'MEDICINA', 'cantidad' => 0],
                                  ['nombre' => 'ODONTOLOGÍA', 'cantidad' => 0],
                                  ['nombre' => 'NUTRICION', 'cantidad' => 0],
                                  ['nombre' => 'PSICOLOGIA', 'cantidad' => 0],
                                  ['nombre' => 'CRED', 'cantidad' => 0],
                              ];
                    @endphp
                    @foreach ($prodItems as $i => $item)
                      <tr>
                        <td><input type="text" name="contenido[produccion][{{ $i }}][nombre]"
                            value="{{ $item['nombre'] ?? '' }}" class="table-input font-bold text-slate-600"></td>
                        <td><input type="number" name="contenido[produccion][{{ $i }}][cantidad]"
                            value="{{ $item['cantidad'] ?? 0 }}" min="0"
                            class="table-input text-center font-bold text-indigo-600 bg-indigo-50/50"></td>
                        <td class="text-center"><button type="button" onclick="this.closest('tr').remove()"
                            class="p-1 rounded text-slate-300 hover:text-red-500"><i data-lucide="trash-2"
                              class="w-4 h-4"></i></button></td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
              <button type="button" onclick="agregarFilaProduccion('tabla-produccion')"
                class="w-full text-xs font-bold text-indigo-600 bg-indigo-50 hover:bg-indigo-100 border border-indigo-200 px-4 py-3 rounded-lg flex items-center justify-center gap-2"><i
                  data-lucide="plus" class="w-3 h-3"></i> AGREGAR OTRO SERVICIO</button>
            </div>

            <div class="space-y-8">
              <div>
                <label class="input-label border-b border-slate-100 pb-2 mb-4 block">Evaluación de Calidad</label>
                <div class="space-y-3">
                  {{-- Pregunta 1 --}}
                  <div class="flex items-center justify-between p-3 bg-slate-50 rounded-lg border border-slate-100">
                    <p class="text-xs font-bold text-slate-600 pr-4">¿Disminuye el tiempo de espera?</p>
                    <div class="toggle-group">
                      <label><input type="radio" name="contenido[calidad][espera]" value="SI"
                          class="toggle-radio"
                          {{ ($registro->calidad_tiempo_espera ?? '') == 'SI' ? 'checked' : '' }}><span
                          class="toggle-btn"><i data-lucide="check" class="w-3 h-3"></i> SÍ</span></label>
                      <label><input type="radio" name="contenido[calidad][espera]" value="NO"
                          class="toggle-radio"
                          {{ ($registro->calidad_tiempo_espera ?? '') == 'NO' ? 'checked' : '' }}><span
                          class="toggle-btn"><i data-lucide="x" class="w-3 h-3"></i> NO</span></label>
                    </div>
                  </div>
                  {{-- Pregunta 2 --}}
                  <div class="flex items-center justify-between p-3 bg-slate-50 rounded-lg border border-slate-100">
                    <p class="text-xs font-bold text-slate-600 pr-4">¿El paciente se muestra satisfecho?</p>
                    <div class="toggle-group">
                      <label><input type="radio" name="contenido[calidad][satisfaccion]" value="SI"
                          class="toggle-radio"
                          {{ ($registro->calidad_paciente_satisfecho ?? '') == 'SI' ? 'checked' : '' }}><span
                          class="toggle-btn"><i data-lucide="check" class="w-3 h-3"></i> SÍ</span></label>
                      <label><input type="radio" name="contenido[calidad][satisfaccion]" value="NO"
                          class="toggle-radio"
                          {{ ($registro->calidad_paciente_satisfecho ?? '') == 'NO' ? 'checked' : '' }}><span
                          class="toggle-btn"><i data-lucide="x" class="w-3 h-3"></i> NO</span></label>
                    </div>
                  </div>
                  {{-- Pregunta 3 --}}
                  <div class="bg-slate-50 rounded-lg border border-slate-100 p-3">
                    <div class="flex items-center justify-between">
                      <p class="text-xs font-bold text-slate-600 pr-4">¿Se utilizan reportes del sistema?</p>
                      <div class="toggle-group">
                        <label><input type="radio" name="contenido[calidad][reportes]" value="SI"
                            class="toggle-radio" onchange="toggleReportes(true)"
                            {{ ($registro->calidad_usa_reportes ?? '') == 'SI' ? 'checked' : '' }}><span
                            class="toggle-btn"><i data-lucide="check" class="w-3 h-3"></i> SÍ</span></label>
                        <label><input type="radio" name="contenido[calidad][reportes]" value="NO"
                            class="toggle-radio" onchange="toggleReportes(false)"
                            {{ ($registro->calidad_usa_reportes ?? '') == 'NO' ? 'checked' : '' }}><span
                            class="toggle-btn"><i data-lucide="x" class="w-3 h-3"></i> NO</span></label>
                      </div>
                    </div>
                    <div id="div-reportes-detalle"
                      class="{{ ($registro->calidad_usa_reportes ?? '') == 'SI' ? '' : 'hidden' }} mt-3 pt-3 border-t border-slate-200/50">
                      <input type="text" name="contenido[calidad][reportes_socializa]" class="input-blue text-xs"
                        placeholder="¿Con quién lo socializa?" value="{{ $registro->calidad_socializa_con ?? '' }}">
                    </div>
                  </div>
                </div>
              </div>

              {{-- Dificultades --}}
              <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="bg-slate-50 px-4 py-3 border-b border-slate-100 flex items-center gap-2">
                  <h3 class="text-xs font-bold text-slate-700 uppercase tracking-wide">Reporte de Dificultades</h3>
                </div>
                <div class="p-5 space-y-6">
                  <div>
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-3">1. ¿A quién comunica?
                    </p>
                    <div class="grid grid-cols-3 gap-3">
                      @foreach (['MINSA', 'DIRIS/DIRESA', 'EESS'] as $opcion)
                        <label class="cursor-pointer group relative">
                          <input type="radio" name="contenido[dificultades][comunica]" value="{{ $opcion }}"
                            class="peer sr-only"
                            {{ ($registro->dificultad_comunica_a ?? '') == $opcion ? 'checked' : '' }}>
                          <div
                            class="text-center py-3 px-2 rounded-lg border border-slate-200 bg-white transition-all peer-checked:border-indigo-500 peer-checked:bg-indigo-50/50 peer-checked:shadow-sm group-hover:border-indigo-300">
                            <span
                              class="block text-xs font-bold text-slate-500 peer-checked:text-indigo-700">{{ $opcion }}</span>
                          </div>
                          <div
                            class="absolute -top-2 -right-2 bg-indigo-600 text-white rounded-full p-0.5 opacity-0 peer-checked:opacity-100 transition-opacity shadow-sm">
                            <i data-lucide="check" class="w-2 h-2"></i>
                          </div>
                        </label>
                      @endforeach
                    </div>
                  </div>
                  <div class="h-px bg-slate-100"></div>
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
                            class="text-center py-3 px-2 rounded-lg border border-slate-200 bg-white transition-all peer-checked:border-indigo-500 peer-checked:bg-indigo-50/50 peer-checked:shadow-sm group-hover:border-indigo-300">
                            <span
                              class="block text-xs font-bold text-slate-500 peer-checked:text-indigo-700">{{ $opcion }}</span>
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
          <div class="max-w-xl mx-auto py-4">
            <div class="flex justify-between items-end mb-2 px-1">
              <label class="text-xs font-bold text-slate-500 uppercase">Dibuje su firma aquí:</label>
              <button type="button" onclick="clearSignature()"
                class="text-[10px] font-bold text-red-500 hover:text-red-700 flex items-center gap-1 bg-red-50 px-2 py-1 rounded transition hover:bg-red-100"><i
                  data-lucide="eraser" class="w-3 h-3"></i> LIMPIAR</button>
            </div>
            <div class="bg-white border-2 border-dashed border-slate-300 rounded-xl shadow-sm p-1 relative">
              <canvas id="signature-pad"
                class="w-full h-64 bg-slate-50 rounded-lg cursor-crosshair touch-none block"></canvas>
            </div>
            <p class="text-[10px] text-center text-slate-400 mt-3 italic"><i data-lucide="info"
                class="w-3 h-3 inline-block mr-1"></i> Al guardar, se generará el acta con esta firma gráfica.</p>
            <input type="hidden" name="firma_grafica_data" id="firma_input"
              value="{{ $registro->firma_grafica ?? '' }}">
          </div>
        </div>

      </div>

      {{-- NAV --}}
      <div class="mt-8 flex justify-between items-center">
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
  <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
  <script>
    let canvas, ctx;
    let isDrawing = false;
    let evidenceList = [];
    const MAX_PHOTOS = 2;
    let timeoutNombre = null;

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
          msg.textContent = "No se encontró el documento en la base de datos de profesionales.";
          msg.className = "text-[10px] text-red-500 mt-1";
          msg.classList.remove('hidden');
        }
      } catch (error) {
        console.error('Error:', error);
      } finally {
        loader.classList.add('hidden');
      }
    }

    // 2. BUSCAR POR NOMBRE (Mientras escribe)
    function buscarPorNombre() {
      const query = document.getElementById('personal_nombre').value;
      const lista = document.getElementById('lista-sugerencias');

      // Limpiar timeout anterior
      clearTimeout(timeoutNombre);

      if (query.length < 3) {
        lista.classList.add('hidden');
        lista.innerHTML = '';
        return;
      }

      // Esperar 300ms antes de buscar (Debounce)
      timeoutNombre = setTimeout(async () => {
        try {
          const response = await fetch(
            `{{ route('usuario.monitoreo.citas.buscar.profesional') }}?type=name&q=${query}`);
          const data = await response.json();

          lista.innerHTML = '';

          if (data.length > 0) {
            lista.classList.remove('hidden');
            data.forEach(prof => {
              // Construir nombre completo
              const nombreCompleto = `${prof.apellido_paterno} ${prof.apellido_materno} ${prof.nombres}`;

              // Crear elemento de lista
              const item = document.createElement('div');
              item.className = "p-2 hover:bg-slate-100 cursor-pointer border-b border-slate-100 text-xs";
              item.innerHTML =
                `<strong>${nombreCompleto}</strong> <span class="text-slate-400">(${prof.tipo_doc}: ${prof.doc})</span>`;

              // Al hacer clic, rellenar y cerrar lista
              item.onclick = () => {
                rellenarDatos(prof);
                lista.classList.add('hidden');
              };

              lista.appendChild(item);
            });
          } else {
            lista.classList.add('hidden');
          }
        } catch (error) {
          console.error(error);
        }
      }, 300);
    }

    // Cerrar lista de sugerencias si hago clic fuera
    document.addEventListener('click', function(e) {
      const lista = document.getElementById('lista-sugerencias');
      const input = document.getElementById('personal_nombre');
      if (!lista.contains(e.target) && e.target !== input) {
        lista.classList.add('hidden');
      }
    });

    // FUNCION AUXILIAR PARA RELLENAR LOS INPUTS
    function rellenarDatos(prof) {
      // Concatenar nombres según tus columnas de BD
      const nombreCompleto = `${prof.apellido_paterno} ${prof.apellido_materno} ${prof.nombres}`.trim();

      document.getElementById('personal_nombre').value = nombreCompleto;
      document.getElementById('personal_dni').value = prof.doc;

      // Intentar seleccionar el tipo de doc si coincide con las opciones
      const selectTipo = document.getElementById('personal_tipo_doc');
      if (['DNI', 'CE'].includes(prof.tipo_doc)) {
        selectTipo.value = prof.tipo_doc;
      } else {
        selectTipo.value = 'OTRO';
      }
    }

    document.addEventListener('DOMContentLoaded', () => {
      lucide.createIcons();
      initSignaturePad();

      if (typeof updateRolText === 'function') updateRolText();

      // --- CORRECCIÓN AQUÍ: Evitar error si $registro es null ---
      const fotosGuardadas = @json($registro->fotos_evidencia ?? []);

      if (fotosGuardadas.length > 0) {
        fotosGuardadas.forEach((url, i) => {
          evidenceList.push({
            type: 'server',
            file: null,
            url: url,
            name: 'Foto Guardada',
            id: Date.now() + i
          });
        });
        renderGallery();
        syncInputs();
      }

      // --- CORRECCIÓN AQUÍ: Cargar firma ---
      const firmaInput = document.getElementById('firma_input');
      if (firmaInput && firmaInput.value && canvas) {
        const img = new Image();
        img.src = firmaInput.value;
        img.onload = function() {
          ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
        };
      }
    });

    // 1. UI LOGIC
    window.toggleRolDropdown = function() {
      document.getElementById('rol-dropdown-list').classList.toggle('hidden');
    }
    window.updateRolText = function() {
      const checkboxes = document.querySelectorAll('#rol-dropdown-list input[type="checkbox"]:checked');
      const textSpan = document.getElementById('rol-selected-text');
      if (!textSpan) return;
      if (checkboxes.length === 0) {
        textSpan.textContent = '-- Seleccionar --';
        textSpan.classList.remove('text-indigo-600', 'font-bold');
      } else {
        const values = Array.from(checkboxes).map(cb => cb.value);
        textSpan.textContent = values.length <= 2 ? values.join(', ') : `${values.length} Seleccionados`;
        textSpan.classList.add('text-indigo-600', 'font-bold');
      }
    }
    document.addEventListener('click', function(e) {
      const dropdown = document.getElementById('rol-dropdown-list');
      const button = e.target.closest('button[onclick="toggleRolDropdown()"]');
      if (dropdown && !button && !dropdown.contains(e.target) && !dropdown.classList.contains('hidden')) {
        dropdown.classList.add('hidden');
      }
    });

    window.switchTab = function(tab) {
      const panelLocal = document.getElementById('panel-local');
      const panelServer = document.getElementById('panel-server');
      const btnLocal = document.getElementById('tab-local');
      const btnServer = document.getElementById('tab-server');
      const activeClass = ['text-indigo-600', 'border-b-2', 'border-indigo-600'];
      const inactiveClass = ['text-slate-400', 'hover:text-indigo-500'];

      if (tab === 'local') {
        panelLocal.classList.remove('hidden');
        panelServer.classList.add('hidden');
        btnLocal.classList.add(...activeClass);
        btnLocal.classList.remove(...inactiveClass);
        btnServer.classList.remove(...activeClass);
      } else {
        panelLocal.classList.add('hidden');
        panelServer.classList.remove('hidden');
        btnServer.classList.add(...activeClass);
        btnServer.classList.remove(...inactiveClass);
        btnLocal.classList.remove(...activeClass);
      }
    }
    window.openServerModal = function() {
      alert("🚧 MANTENIMIENTO 🚧\nExplorador habilitado en producción.");
    }
    window.closeServerModal = function() {
      document.getElementById('server-modal').classList.add('hidden');
    }

    window.toggleCapacitacion = function(show) {
      const div = document.getElementById('div-capacitacion-detalles');
      if (div) show ? div.classList.remove('hidden') : div.classList.add('hidden');
    }
    window.toggleOtrosCapacitacion = function(checkbox) {
      const div = document.getElementById('div-capacitacion-otros');
      if (div) checkbox.checked ? div.classList.remove('hidden') : div.classList.add('hidden');
    }
    window.toggleReportes = function(show) {
      const div = document.getElementById('div-reportes-detalle');
      if (div) show ? div.classList.remove('hidden') : div.classList.add('hidden');
    }

    // 2. WIZARD
    let currentStep = 1;
    const totalSteps = 5;
    window.showStep = function(step) {
      document.querySelectorAll('.step-content').forEach(el => el.classList.remove('active'));
      const target = document.getElementById(`step-${step}`);
      if (target) target.classList.add('active');
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
        setTimeout(resizeCanvas, 100);
      } else {
        btnNext.style.display = 'flex';
        btnSubmit.style.display = 'none';
      }
      window.scrollTo({
        top: 0,
        behavior: 'smooth'
      });
    }

    function getIconForStep(step) {
      switch (step) {
        case 1:
          return '<i data-lucide="user" class="w-5 h-5"></i>';
        case 2:
          return '<i data-lucide="package" class="w-5 h-5"></i>';
        case 3:
          return '<i data-lucide="bar-chart-2" class="w-5 h-5"></i>';
        case 4:
          return '<i data-lucide="camera" class="w-5 h-5"></i>';
        case 5:
          return '<i data-lucide="pen-tool" class="w-5 h-5"></i>';
        default:
          return step;
      }
    }
    window.changeStep = function(dir) {
      const newStep = currentStep + dir;
      if (newStep >= 1 && newStep <= totalSteps) {
        currentStep = newStep;
        showStep(currentStep);
      }
    }
    window.goToStep = function(step) {
      currentStep = step;
      showStep(currentStep);
    }

    // 3. TABLAS
    function generateId() {
      return Date.now() + Math.floor(Math.random() * 1000);
    }
    let equipoIndex = {{ count($items) }};

    window.agregarFilaEquipo = function(tableId, baseName) {
      equipoIndex++;
      const tbody = document.getElementById('tbody-equipos');

      const row = document.createElement('tr');
      row.className = "group hover:bg-slate-50 transition-colors";

      row.innerHTML = `
        <td class="p-2 align-middle">
            <input type="text" name="contenido[equipos][${equipoIndex}][nombre]"
                class="w-full bg-transparent border-0 border-b border-transparent focus:border-indigo-500 focus:ring-0 font-bold text-slate-700 text-xs px-2 py-1 placeholder-slate-300"
                placeholder="Nuevo Equipo">
        </td>

        <td class="p-2 align-middle">
            <div class="relative flex items-center">
                <input type="text" id="serie-input-${equipoIndex}"
                    name="contenido[equipos][${equipoIndex}][serie]"
                    class="w-full bg-slate-50 border border-slate-200 text-slate-600 text-[11px] font-mono uppercase rounded pl-2 pr-8 py-1 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 placeholder-slate-400"
                    placeholder="----">

                <button type="button" onclick="iniciarEscaneo('serie-input-${equipoIndex}')"
                    class="absolute right-0.5 p-1 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded transition-colors cursor-pointer z-10"
                    title="Escanear">
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 7V5a2 2 0 0 1 2-2h2"/><path d="M17 3h2a2 2 0 0 1 2 2v2"/><path d="M21 17v2a2 2 0 0 1-2 2h-2"/><path d="M7 21H5a2 2 0 0 1-2-2v-2"/><rect width="5" height="5" x="7" y="7" rx="1"/><path d="M7 17h10"/><path d="M17 7v10"/></svg>
                </button>
            </div>
        </td>

        <td class="p-2 align-middle">
            <select name="contenido[equipos][${equipoIndex}][propiedad]"
                class="w-full bg-white border border-slate-200 text-[11px] text-slate-600 rounded px-1 py-1 focus:border-indigo-500 focus:ring-0 cursor-pointer">
                <option value="ESTABLECIMIENTO" selected>Establecimiento</option>
                <option value="PROPIO">Propio</option>
            </select>
        </td>

        <td class="p-2 align-middle">
            <select name="contenido[equipos][${equipoIndex}][estado]"
                class="w-full bg-white border border-slate-200 text-[11px] rounded px-1 py-1 focus:border-indigo-500 focus:ring-0 cursor-pointer text-slate-600">
                <option value="Bueno" selected>Bueno</option>
                <option value="Regular">Regular</option>
                <option value="Malo">Malo</option>
                <option value="Inoperativo">Inoperativo</option>
            </select>
        </td>

        <td class="p-2 align-middle">
            <input type="text" name="contenido[equipos][${equipoIndex}][observaciones]"
                class="w-full bg-transparent text-[11px] text-slate-500 border-0 border-b border-transparent focus:border-indigo-500 focus:ring-0 placeholder-slate-300 italic px-2 py-1"
                placeholder="Observaciones...">
        </td>

        <td class="p-2 text-center align-middle">
            <button type="button" onclick="this.closest('tr').remove()"
                class="text-slate-300 hover:text-red-500 hover:bg-red-50 p-1 rounded transition-all">
                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" x2="10" y1="11" y2="17"/><line x1="14" x2="14" y1="11" y2="17"/></svg>
            </button>
        </td>
    `;

      tbody.appendChild(row);

      // Si usas librerías que necesitan reiniciar iconos (como Lucide), descomenta esto:
      if (window.lucide) lucide.createIcons();
    }
    window.agregarFilaProduccion = function(tableId) {
      const tbody = document.querySelector(`#${tableId} tbody`);
      if (!tbody) return;
      const id = generateId();
      const tr = document.createElement('tr');
      tr.innerHTML =
        `<td><input type="text" name="contenido[produccion][${id}][nombre]" class="table-input font-bold text-indigo-600" placeholder="Nuevo Servicio..."></td><td><input type="number" name="contenido[produccion][${id}][cantidad]" placeholder="0" class="table-input text-center font-bold text-indigo-600 bg-indigo-50/50"></td><td class="text-center"><button type="button" onclick="this.closest('tr').remove()" class="p-1 rounded text-slate-300 hover:text-red-500"><i data-lucide="trash-2" class="w-4 h-4"></i></button></td>`;
      tbody.appendChild(tr);
      lucide.createIcons();
    }

    // 4. EVIDENCIAS
    window.handleFiles = function(files) {
      if (!files.length) return;
      Array.from(files).forEach(file => {
        if (evidenceList.length >= MAX_PHOTOS) evidenceList.shift();
        evidenceList.push({
          type: 'local',
          file: file,
          url: URL.createObjectURL(file),
          name: file.name,
          id: generateId()
        });
      });
      renderGallery();
      syncInputs();
    }
    window.removeImage = function(id) {
      evidenceList = evidenceList.filter(item => item.id !== id);
      renderGallery();
      syncInputs();
    }

    function renderGallery() {
      const container = document.getElementById('gallery-container');
      const countDisplay = document.getElementById('count-display');
      const emptyState = document.getElementById('empty-state');
      if (!container) return;
      Array.from(container.children).forEach(c => {
        if (c.id !== 'empty-state') container.removeChild(c);
      });
      if (evidenceList.length === 0) {
        if (emptyState) emptyState.style.display = 'block';
        if (countDisplay) countDisplay.innerText = `0 / ${MAX_PHOTOS}`;
        return;
      }
      if (emptyState) emptyState.style.display = 'none';
      evidenceList.forEach(item => {
        const div = document.createElement('div');
        div.className =
          'relative group aspect-square rounded-xl overflow-hidden border border-slate-200 shadow-sm bg-white animate-fade-in';
        const badgeColor = item.type === 'local' ? 'bg-indigo-500' : 'bg-emerald-500';
        div.innerHTML =
          `<img src="${item.url}" class="w-full h-full object-cover"><button type="button" onclick="removeImage(${item.id})" class="absolute top-2 right-2 bg-red-500 text-white p-1 rounded-full shadow hover:bg-red-600 z-10"><i data-lucide="x" class="w-3 h-3"></i></button><div class="absolute top-2 left-2 ${badgeColor} text-white text-[9px] font-bold px-2 py-0.5 rounded-full shadow-sm"><span>${item.type.toUpperCase()}</span></div>`;
        container.appendChild(div);
      });
      if (countDisplay) countDisplay.innerText = `${evidenceList.length} / ${MAX_PHOTOS}`;
      lucide.createIcons();
    }

    function syncInputs() {
      const dt = new DataTransfer();
      evidenceList.filter(i => i.type === 'local').forEach(i => dt.items.add(i.file));
      const input = document.getElementById('final-input-files');
      if (input) input.files = dt.files;

      // Sincronizar rutas antiguas (server) para que no se pierdan
      const serverFiles = evidenceList.filter(i => i.type === 'server').map(i => i.url);
      document.getElementById('final-input-server').value = JSON.stringify(serverFiles);
    }

    // 5. SIGNATURE
    function initSignaturePad() {
      canvas = document.getElementById('signature-pad');
      if (!canvas) return;
      ctx = canvas.getContext('2d');
      ctx.lineWidth = 2;
      ctx.lineCap = 'round';
      ctx.lineJoin = 'round';
      ctx.strokeStyle = '#0f172a';
      canvas.addEventListener('mousedown', startDrawing);
      canvas.addEventListener('mousemove', draw);
      canvas.addEventListener('mouseup', stopDrawing);
      canvas.addEventListener('mouseleave', stopDrawing);
      canvas.addEventListener('touchstart', startDrawing, {
        passive: false
      });
      canvas.addEventListener('touchmove', draw, {
        passive: false
      });
      canvas.addEventListener('touchend', stopDrawing);
      setTimeout(resizeCanvas, 500);
      window.addEventListener("resize", resizeCanvas);
    }

    function startDrawing(e) {
      isDrawing = true;
      const pos = getPos(e);
      ctx.beginPath();
      ctx.moveTo(pos.x, pos.y);
      if (e.type === 'touchstart') e.preventDefault();
    }

    function draw(e) {
      if (!isDrawing) return;
      const pos = getPos(e);
      ctx.lineTo(pos.x, pos.y);
      ctx.stroke();
      if (e.type === 'touchmove') e.preventDefault();
    }

    function stopDrawing() {
      if (isDrawing) {
        isDrawing = false;
        ctx.beginPath();
      }
    }

    function getPos(e) {
      const rect = canvas.getBoundingClientRect();
      const clientX = e.touches ? e.touches[0].clientX : e.clientX;
      const clientY = e.touches ? e.touches[0].clientY : e.clientY;
      return {
        x: clientX - rect.left,
        y: clientY - rect.top
      };
    }
    window.resizeCanvas = function() {
      if (!canvas || !ctx) return;
      const stepContainer = document.getElementById('step-5');
      if (canvas.offsetWidth === 0) return;
      const ratio = Math.max(window.devicePixelRatio || 1, 1);
      const width = canvas.offsetWidth;
      const height = canvas.offsetHeight;

      // Intentar preservar imagen al redimensionar
      const tempImg = new Image();
      tempImg.src = canvas.toDataURL();

      canvas.width = width * ratio;
      canvas.height = height * ratio;
      ctx.scale(ratio, ratio);
      ctx.lineWidth = 2;
      ctx.lineCap = 'round';
      ctx.lineJoin = 'round';
      ctx.strokeStyle = '#0f172a';

      tempImg.onload = function() {
        ctx.drawImage(tempImg, 0, 0, width, height);
      }
    }
    window.clearSignature = function() {
      if (canvas && ctx) {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        isDrawing = false;
        ctx.beginPath();
        document.getElementById('firma_input').value = '';
      }
    }
    window.saveSignature = function() {
      const input = document.getElementById('firma_input');
      if (!input) return;
      if (canvas) input.value = canvas.toDataURL('image/png');
    }

    let html5QrcodeScanner = null;
    let currentInputId = null;

    function iniciarEscaneo(inputId) {
      currentInputId = inputId;
      const modal = document.getElementById('scanner-modal');
      modal.classList.remove('hidden'); // Mostrar modal

      // Iniciar libreria
      html5QrcodeScanner = new Html5Qrcode("reader");

      const config = {
        fps: 10,
        qrbox: {
          width: 250,
          height: 250
        }
      };

      // Preferir cámara trasera (environment)
      html5QrcodeScanner.start({
          facingMode: "environment"
        }, config, onScanSuccess, onScanFailure)
        .catch(err => {
          console.error("Error iniciando cámara", err);
          alert("No se pudo acceder a la cámara. Asegúrate de dar permisos.");
          modal.classList.add('hidden');
        });
    }

    function onScanSuccess(decodedText, decodedResult) {
      // Cuando detecta un código:
      if (currentInputId) {
        document.getElementById(currentInputId).value = decodedText;
      }
      detenerEscaneo();
    }

    function onScanFailure(error) {
      // Pasa constantemente si no detecta nada, puedes dejarlo vacío para no saturar la consola
      // console.warn(`Code scan error = ${error}`);
    }

    function detenerEscaneo() {
      if (html5QrcodeScanner) {
        html5QrcodeScanner.stop().then(() => {
          html5QrcodeScanner.clear();
          document.getElementById('scanner-modal').classList.add('hidden');
          currentInputId = null;
        }).catch(err => {
          console.error("Error deteniendo el scanner", err);
        });
      } else {
        document.getElementById('scanner-modal').classList.add('hidden');
      }
    }
  </script>
@endpush
