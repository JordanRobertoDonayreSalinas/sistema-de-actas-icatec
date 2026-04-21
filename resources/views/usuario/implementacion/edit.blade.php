@extends('layouts.usuario')

@section('title', 'Editar Acta ' . $moduloConfig['nombre'])

@push('styles')
<style>
    @keyframes slide-up {
        from { opacity: 0; transform: translateY(18px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .slide-up { animation: slide-up 0.45s ease-out forwards; }
    .slide-up-d1 { animation: slide-up 0.45s 0.05s ease-out both; }
    .slide-up-d2 { animation: slide-up 0.45s 0.10s ease-out both; }
    .slide-up-d3 { animation: slide-up 0.45s 0.15s ease-out both; }
    .slide-up-d4 { animation: slide-up 0.45s 0.20s ease-out both; }
    .slide-up-d5 { animation: slide-up 0.45s 0.25s ease-out both; }
    .slide-up-d6 { animation: slide-up 0.45s 0.30s ease-out both; }
    .slide-up-d7 { animation: slide-up 0.45s 0.35s ease-out both; }
    .slide-up-d8 { animation: slide-up 0.45s 0.40s ease-out both; }

    /* Radio pill para Firma Digital */
    .radio-pill input[type="radio"] { display:none; }
    .radio-pill label { cursor:pointer; display:flex; align-items:center; gap:0.5rem; padding:0.6rem 1.2rem; border-radius:9999px; border:2px solid #e2e8f0; font-size:0.8rem; font-weight:700; color:#64748b; transition:all 0.2s; background:#fff; }
    .radio-pill input[type="radio"]:checked + label { border-color:#6366f1; background:#eef2ff; color:#4338ca; }

    /* Radio pill para Modalidad */
    .radio-pill-blue input[type="radio"] { display:none; }
    .radio-pill-blue label { cursor:pointer; display:flex; align-items:center; gap:0.5rem; padding:0.6rem 1.2rem; border-radius:9999px; border:2px solid #e2e8f0; font-size:0.8rem; font-weight:700; color:#64748b; transition:all 0.2s; background:#fff; }
    .radio-pill-blue input[type="radio"]:checked + label { border-color:#3b82f6; background:#eff6ff; color:#1d4ed8; }
</style>
@endpush

@section('header-content')
<div class="flex items-center gap-3 w-full">
    <div class="flex items-center justify-center w-9 h-9 rounded-xl bg-purple-600 text-white flex-shrink-0">
        <i data-lucide="file-pen" class="w-5 h-5"></i>
    </div>
    <div>
        <h1 class="text-xl font-bold text-slate-800 tracking-tight">Editar Acta #{{ $acta->id }}: {{ $moduloConfig['nombre'] }}</h1>
        <div class="flex items-center gap-1.5 text-xs text-slate-400 mt-0.5">
            <a href="{{ route('usuario.implementacion.index') }}" class="hover:text-indigo-600 font-semibold">Actas de Implementación</a>
            <span>›</span>
            <span>Editar #{{ $acta->id }}</span>
        </div>
    </div>
</div>
@endsection

@section('content')
<div class="max-w-5xl mx-auto">
    <form action="{{ route('usuario.implementacion.update', ['modulo' => $moduloKey, 'id' => $acta->id]) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')
        <input type="hidden" name="renipress_data" id="renipress_data_input" value="{{ json_encode($acta->renipress_data) }}">

        {{-- === TARJETA 1: MÓDULO ================================================== --}}
        <div class="bg-white rounded-3xl p-8 shadow-xl shadow-slate-200/60 border border-slate-100 slide-up-d1">
            <div class="flex items-center justify-between mb-6 pb-4 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="bg-indigo-600 p-2.5 rounded-xl text-white">
                        <i data-lucide="layers" class="w-5 h-5"></i>
                    </div>
                    <h2 class="text-base font-bold text-slate-800 uppercase tracking-wide">1. Módulo a Implementar</h2>
                </div>
                <a href="{{ route('usuario.implementacion.index') }}" class="flex items-center gap-1.5 px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-bold transition-colors">
                    <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
                    Volver
                </a>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                {{-- Selector de módulo --}}
                <div class="bg-indigo-50 rounded-2xl p-4 border border-indigo-100 cursor-pointer hover:bg-indigo-100 transition-colors">
                    <label class="text-[10px] font-black text-indigo-400 uppercase tracking-widest block mb-2">Módulo</label>
                    <div class="flex items-center gap-2">
                        <i data-lucide="grid-2x2" class="w-5 h-5 text-indigo-600 flex-shrink-0"></i>
                        <select name="modulo_key" id="modulo_key_select" class="bg-transparent border-0 p-0 text-sm font-black text-indigo-900 focus:ring-0 w-full cursor-pointer outline-none">
                            @foreach($modulos as $k => $cfg)
                                <option value="{{ $k }}" {{ $k == $moduloKey ? 'selected' : '' }}>{{ $cfg['nombre'] }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                {{-- Fecha --}}
                <div class="bg-slate-50 rounded-2xl p-4 border border-slate-200">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2">Fecha de Implementación</label>
                    <div class="flex items-center gap-2">
                        <i data-lucide="calendar" class="w-5 h-5 text-slate-500 flex-shrink-0"></i>
                        <input type="date" name="fecha" required value="{{ date('Y-m-d', strtotime($acta->fecha)) }}" class="bg-transparent border-0 p-0 text-sm font-black text-slate-800 focus:ring-0 w-full cursor-pointer outline-none">
                    </div>
                </div>
            </div>
        </div>
        {{-- Campo oculto para persistir los datos de RENIPRESS --}}
        <input type="hidden" name="renipress_data" id="renipress_data_input" value="{{ json_encode($acta->renipress_data) }}">
        
        {{-- === TARJETA 2: DATOS DEL ESTABLECIMIENTO ============================= --}}
        <div class="bg-white rounded-3xl p-8 shadow-xl shadow-slate-200/60 border border-slate-100 slide-up-d2">
            <div class="flex items-center gap-3 mb-6 pb-4 border-b border-slate-100">
                <div class="bg-teal-600 p-2.5 rounded-xl text-white">
                    <i data-lucide="hospital" class="w-5 h-5"></i>
                </div>
                <h2 class="text-base font-bold text-slate-800 uppercase tracking-wide">2. Datos del Establecimiento</h2>
            </div>

            {{-- Buscador (opcional en edición) --}}
            <div class="relative mb-5">
                <label class="text-xs font-bold text-slate-500 uppercase mb-2 block">Buscar Establecimiento (Opcional — para actualizar datos)</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i data-lucide="search" class="h-5 w-5 text-slate-400"></i>
                    </div>
                    <input type="text" id="busqueda_establecimiento" placeholder="Opcional: Volver a buscar para actualizar campos..." autocomplete="off"
                        class="block w-full pl-11 pr-4 py-4 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-bold text-slate-800 placeholder-slate-400 focus:ring-2 focus:ring-teal-400 focus:bg-white transition-all uppercase">
                </div>
                <ul id="sugerencias_establecimiento" class="absolute z-50 bg-white border border-slate-200 w-full rounded-2xl shadow-xl mt-1 hidden max-h-60 overflow-y-auto divide-y divide-slate-50"></ul>
            </div>

            {{-- Campos con datos precargados --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                <div class="col-span-2 md:col-span-3 p-4 bg-slate-50 rounded-2xl border border-slate-100">
                    <label class="text-[9px] font-black text-slate-400 uppercase block mb-1">Nombre Establecimiento</label>
                    <input type="text" id="nombre_establecimiento" name="nombre_establecimiento" readonly required value="{{ $acta->nombre_establecimiento }}"
                        class="w-full bg-transparent border-0 p-0 text-sm font-bold text-slate-700 focus:ring-0">
                </div>
                <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100">
                    <label class="text-[9px] font-black text-slate-400 uppercase block mb-1">RENIPRESS</label>
                    <input type="text" id="codigo_establecimiento" name="codigo_establecimiento" readonly required value="{{ $acta->codigo_establecimiento }}"
                        class="w-full bg-transparent border-0 p-0 text-xs font-bold text-slate-700 focus:ring-0 font-mono">
                </div>
                <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100">
                    <label class="text-[9px] font-black text-slate-400 uppercase block mb-1">Provincia</label>
                    <input type="text" id="provincia" name="provincia" readonly value="{{ $acta->provincia }}" class="w-full bg-transparent border-0 p-0 text-xs font-bold text-slate-700 focus:ring-0">
                </div>
                <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100">
                    <label class="text-[9px] font-black text-slate-400 uppercase block mb-1">Distrito</label>
                    <input type="text" id="distrito" name="distrito" readonly value="{{ $acta->distrito }}" class="w-full bg-transparent border-0 p-0 text-xs font-bold text-slate-700 focus:ring-0">
                </div>
                <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100">
                    <label class="text-[9px] font-black text-slate-400 uppercase block mb-1">Categoría</label>
                    <input type="text" id="categoria" name="categoria" readonly value="{{ $acta->categoria }}" class="w-full bg-transparent border-0 p-0 text-xs font-bold text-slate-700 focus:ring-0">
                </div>
                <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100">
                    <label class="text-[9px] font-black text-slate-400 uppercase block mb-1">Red</label>
                    <input type="text" id="red" name="red" readonly value="{{ $acta->red }}" class="w-full bg-transparent border-0 p-0 text-xs font-bold text-slate-700 focus:ring-0">
                </div>
                <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100">
                    <label class="text-[9px] font-black text-slate-400 uppercase block mb-1">Microred</label>
                    <input type="text" id="microred" name="microred" readonly value="{{ $acta->microred }}" class="w-full bg-transparent border-0 p-0 text-xs font-bold text-slate-700 focus:ring-0">
                </div>
                <div class="col-span-2 md:col-span-4">
                    <label class="text-xs font-bold text-slate-500 uppercase mb-2 block">Responsable del Establecimiento</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i data-lucide="user-cog" class="h-5 w-5 text-slate-400"></i>
                        </div>
                        <input type="text" id="responsable" name="responsable" required value="{{ $acta->responsable }}" placeholder="Nombre del médico jefe o responsable"
                            class="block w-full pl-11 pr-4 py-3 bg-white border border-slate-200 rounded-xl text-sm font-bold text-slate-700 focus:border-teal-400 uppercase">
                    </div>
                </div>
            </div>
        </div>

        @if($moduloKey === 'ges_adm')
        {{-- === TARJETA 2.1: SERVICIOS RENIPRESS (AUTOMÁTICO) ===================== --}}
        <div id="section_renipress" class="bg-white rounded-3xl p-8 shadow-xl shadow-slate-200/60 border border-slate-100 slide-up-d2 {{ empty($acta->renipress_data) ? 'hidden' : '' }}">
            <div class="flex items-center justify-between mb-6 pb-4 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="bg-blue-600 p-2.5 rounded-xl text-white">
                        <i data-lucide="info" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-slate-800 uppercase tracking-wide">Servicios Autorizados (RENIPRESS)</h2>
                        <p class="text-[10px] text-slate-400 font-bold uppercase mt-1">Información sincronizada desde SUSALUD - No editable</p>
                    </div>
                </div>
                <div id="sync_status_container" class="flex items-center gap-3">
                    <button type="button" onclick="toggleRenipressManual()" class="text-[10px] font-bold text-amber-600 bg-amber-50 hover:bg-amber-100 px-3 py-1 rounded-lg border border-amber-200 transition-colors">
                        <i data-lucide="zap" class="w-3 h-3 inline pb-0.5"></i> MODO MANUAL
                    </button>
                    <div id="sync_status" class="flex items-center gap-2 px-3 py-1 bg-emerald-50 text-emerald-600 rounded-lg text-xs font-black">
                        <i data-lucide="check" class="w-3.5 h-3.5"></i>
                        SINCRONIZADO
                    </div>
                </div>
            </div>
            
            {{-- SECCIÓN DE CONTINGENCIA: PROCESADOR DE PEGADO INTELIGENTE (MEJORADO) --}}
            <div id="renipress_fallback" class="mt-8 pt-6 border-t border-slate-100 hidden">
                <div class="bg-amber-50 rounded-2xl p-6 border border-amber-100">
                    <div class="flex items-start gap-3 mb-4">
                        <div class="bg-amber-500 p-1.5 rounded-lg text-white mt-0.5">
                            <i data-lucide="alert-triangle" class="w-4 h-4"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-amber-800 uppercase">Procesador de Pegado Inteligente (Contingencia)</h3>
                            <p class="text-xs text-amber-600 mt-1">
                                SUSALUD ha bloqueado la consulta automática. Por favor, abre el 
                                <a href="http://renipress.susalud.gob.pe:8080/wb-renipress/inicio.htm" target="_blank" class="font-bold underline hover:text-amber-700">Portal de SUSALUD</a>, 
                                busca el establecimiento, copia la tabla de servicios y pégala aquí abajo.
                            </p>
                        </div>
                    </div>
                    
                    <textarea id="renipress_paste_area" rows="3" 
                        class="w-full bg-white border border-amber-200 rounded-xl p-3 text-xs outline-none focus:ring-2 focus:ring-amber-500/20 transition-all font-mono"
                        placeholder="Pega aquí la tabla de servicios (UPSS o Servicios Autorizados) para procesarla automáticamente..."></textarea>
                    
                    <button type="button" onclick="smartParseRenipress()" 
                        class="mt-3 w-full bg-amber-600 hover:bg-amber-700 text-white font-bold py-2.5 rounded-xl text-xs transition-all shadow-lg shadow-amber-200 flex items-center justify-center gap-2">
                        <i data-lucide="zap" class="w-4 h-4"></i> PROCESAR TEXTO PEGADO
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- UPSS --}}
                <div class="space-y-3">
                    <p class="text-xs font-black text-slate-500 uppercase tracking-widest flex items-center gap-2">
                        <span class="w-1.5 h-1.5 bg-blue-500 rounded-full"></span> UPSS
                    </p>
                    <div class="overflow-hidden rounded-2xl border border-slate-100 bg-slate-50/50">
                        <table class="w-full text-left text-[10px]">
                            <thead class="bg-slate-100 text-slate-600 font-bold uppercase">
                                <tr>
                                    <th class="px-3 py-2">Cód.</th>
                                    <th class="px-3 py-2">Nombre</th>
                                </tr>
                            </thead>
                            <tbody id="renipress_upss_body" class="divide-y divide-slate-100"></tbody>
                        </table>
                    </div>
                </div>

                {{-- UPS --}}
                <div class="space-y-3">
                    <p class="text-xs font-black text-slate-500 uppercase tracking-widest flex items-center gap-2">
                        <span class="w-1.5 h-1.5 bg-indigo-500 rounded-full"></span> UPS (Servicios)
                    </p>
                    <div class="overflow-hidden rounded-2xl border border-slate-100 bg-slate-50/50">
                        <table class="w-full text-left text-[10px]">
                            <thead class="bg-slate-100 text-slate-600 font-bold uppercase">
                                <tr>
                                    <th class="px-3 py-2">Cód.</th>
                                    <th class="px-3 py-2">Servicio</th>
                                </tr>
                            </thead>
                            <tbody id="renipress_ups_body" class="divide-y divide-slate-100"></tbody>
                        </table>
                    </div>
                </div>

                {{-- Especialidades --}}
                <div class="space-y-3">
                    <p class="text-xs font-black text-slate-500 uppercase tracking-widest flex items-center gap-2">
                        <span class="w-1.5 h-1.5 bg-violet-500 rounded-full"></span> Especialidades
                    </p>
                    <div class="overflow-hidden rounded-2xl border border-slate-100 bg-slate-50/50">
                        <table class="w-full text-left text-[10px]">
                            <thead class="bg-slate-100 text-slate-600 font-bold uppercase">
                                <tr>
                                    <th class="px-3 py-2">Cód.</th>
                                    <th class="px-3 py-2">Especialidad</th>
                                </tr>
                            </thead>
                            <tbody id="renipress_especialidades_body" class="divide-y divide-slate-100"></tbody>
                        </table>
                    </div>
                </div>

                {{-- Cartera --}}
                <div class="space-y-3">
                    <p class="text-xs font-black text-slate-500 uppercase tracking-widest flex items-center gap-2">
                        <span class="w-1.5 h-1.5 bg-purple-500 rounded-full"></span> Cartera de Servicios
                    </p>
                    <div class="overflow-hidden rounded-2xl border border-slate-100 bg-slate-50/50">
                        <table class="w-full text-left text-[10px]">
                            <thead class="bg-slate-100 text-slate-600 font-bold uppercase">
                                <tr>
                                    <th class="px-3 py-2">Cód.</th>
                                    <th class="px-3 py-2">Servicio</th>
                                </tr>
                            </thead>
                            <tbody id="renipress_cartera_body" class="divide-y divide-slate-100"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- === TARJETA 3: MODALIDAD (solo Citas) ================================= --}}
        @if($moduloKey === 'citas')
        <div class="bg-white rounded-3xl p-8 shadow-xl shadow-slate-200/60 border border-slate-100 slide-up-d3">
            <div class="flex items-center gap-3 mb-6 pb-4 border-b border-slate-100">
                <div class="bg-blue-600 p-2.5 rounded-xl text-white">
                    <i data-lucide="calendar-check" class="w-5 h-5"></i>
                </div>
                <h2 class="text-base font-bold text-slate-800 uppercase tracking-wide">3. Modalidad de Implementación</h2>
            </div>
            <div class="flex flex-wrap gap-3">
                <div class="radio-pill-blue">
                    <input type="radio" name="modalidad" id="modal_horario" value="POR HORARIO" {{ $acta->modalidad == 'POR HORARIO' ? 'checked' : '' }}>
                    <label for="modal_horario">
                        <i data-lucide="clock" class="w-4 h-4"></i> Por Horario
                    </label>
                </div>
                <div class="radio-pill-blue">
                    <input type="radio" name="modalidad" id="modal_seleccion" value="POR SELECCION" {{ $acta->modalidad == 'POR SELECCION' ? 'checked' : '' }}>
                    <label for="modal_seleccion">
                        <i data-lucide="mouse-pointer-click" class="w-4 h-4"></i> Por Selección (Exclusividad)
                    </label>
                </div>
            </div>
        </div>
        @endif

        {{-- === TARJETA 5: FIRMA DIGITAL ========================================= --}}
        @if(in_array($moduloKey, ['medicina', 'odontologia', 'nutricion', 'psicologia', 'mental', 'emergencia', 'referencias', 'laboratorio', 'farmacia', 'fua']))
        <div class="bg-white rounded-3xl p-8 shadow-xl shadow-slate-200/60 border border-slate-100 slide-up-d4">
            <div class="flex items-center gap-3 mb-6 pb-4 border-b border-slate-100">
                <div class="bg-violet-600 p-2.5 rounded-xl text-white">
                    <i data-lucide="pen-tool" class="w-5 h-5"></i>
                </div>
                <h2 class="text-base font-bold text-slate-800 uppercase tracking-wide">Firma Digital</h2>
            </div>
            <p class="text-xs text-slate-500 mb-4">¿El módulo cuenta con firma digital habilitada?</p>
            <div class="flex flex-wrap gap-3">
                <div class="radio-pill">
                    <input type="radio" name="firma_digital" id="firma_si" value="SI" {{ $acta->firma_digital == 'SI' ? 'checked' : '' }} required>
                    <label for="firma_si">
                        <i data-lucide="check-circle" class="w-4 h-4 text-emerald-500"></i> SÍ, cuenta con firma digital
                    </label>
                </div>
                <div class="radio-pill">
                    <input type="radio" name="firma_digital" id="firma_no" value="NO" {{ $acta->firma_digital == 'NO' ? 'checked' : '' }} required>
                    <label for="firma_no">
                        <i data-lucide="x-circle" class="w-4 h-4 text-red-400"></i> NO cuenta con firma digital
                    </label>
                </div>
            </div>
        </div>
        @endif

        {{-- === TARJETA 6: USUARIOS PARTICIPANTES ================================ --}}
        <div class="bg-white rounded-3xl p-8 shadow-xl shadow-slate-200/60 border border-slate-100 slide-up-d5">
            <div class="flex items-center justify-between mb-6 pb-4 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="bg-emerald-500 p-2.5 rounded-xl text-white">
                        <i data-lucide="users" class="w-5 h-5"></i>
                    </div>
                    <h2 class="text-base font-bold text-slate-800 uppercase tracking-wide">Usuarios Participantes</h2>
                </div>
                <button type="button" onclick="agregarParticipante()" class="flex items-center gap-1.5 text-xs font-bold text-emerald-600 bg-emerald-50 hover:bg-emerald-100 px-4 py-2 rounded-xl transition-colors border border-emerald-200">
                    <i data-lucide="plus" class="w-3.5 h-3.5"></i> Agregar
                </button>
            </div>
            <div id="usuarios-container" class="space-y-3">
                @forelse($acta->usuarios as $index => $usu)
                <div class="participante-row bg-emerald-50/40 border border-emerald-100 rounded-2xl p-4 relative pr-12">
                    <button type="button" onclick="this.closest('.participante-row').remove()" class="absolute top-3 right-3 w-7 h-7 flex items-center justify-center rounded-full text-slate-300 hover:text-red-500 hover:bg-red-50 transition-colors" title="Quitar">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 6L6 18M6 6l12 12"/></svg>
                    </button>
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 text-sm">
                        <div>
                            @php
                                $profesionalLocal = \App\Models\Profesional::where('doc', $usu->dni)->first();
                                $localTipoDoc = $profesionalLocal ? $profesionalLocal->tipo_doc : 'DNI';
                            @endphp
                            <label class="block text-[10px] uppercase font-black text-emerald-600 mb-1">Tipo Doc</label>
                            <select id="participante_tipodoc_{{$index}}" name="usuarios[{{$index}}][tipo_doc]" class="w-full border border-slate-200 rounded-xl py-2 px-3 text-sm outline-none focus:border-emerald-500 bg-white">
                                <option value="DNI" {{ $localTipoDoc == 'DNI' ? 'selected' : '' }}>DNI</option>
                                <option value="CE" {{ $localTipoDoc == 'CE' ? 'selected' : '' }}>CE</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] uppercase font-black text-emerald-600 mb-1">NÚm. Documento</label>
                            <div class="flex items-center gap-1.5">
                                <div class="relative flex-1">
                                    <input type="text" id="participante_dni_{{$index}}" name="usuarios[{{$index}}][dni]" value="{{$usu->dni}}" maxlength="15" required
                                        class="w-full border border-slate-200 rounded-xl py-2 px-3 text-sm outline-none focus:border-emerald-500 bg-white font-bold"
                                        onkeydown="if(event.key === 'Enter'){event.preventDefault(); buscarPersona('participante', {{$index}});}">
                                </div>
                                <button type="button" onclick="buscarPersona('participante', {{$index}})" class="bg-emerald-600 hover:bg-emerald-700 text-white p-2 rounded-xl shadow-sm transition-colors flex-shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                                </button>
                            </div>
                        </div>
                        <div><label class="block text-[10px] uppercase font-black text-emerald-600 mb-1">Ap. Paterno</label><input type="text" id="participante_ap_{{$index}}" name="usuarios[{{$index}}][apellido_paterno]" value="{{$usu->apellido_paterno}}" required class="w-full border border-slate-200 rounded-xl py-2 px-3 text-sm outline-none focus:border-emerald-500 bg-white"></div>
                        <div><label class="block text-[10px] uppercase font-black text-emerald-600 mb-1">Ap. Materno</label><input type="text" id="participante_am_{{$index}}" name="usuarios[{{$index}}][apellido_materno]" value="{{$usu->apellido_materno}}" required class="w-full border border-slate-200 rounded-xl py-2 px-3 text-sm outline-none focus:border-emerald-500 bg-white"></div>
                        <div><label class="block text-[10px] uppercase font-black text-emerald-600 mb-1">Nombres</label><input type="text" id="participante_nom_{{$index}}" name="usuarios[{{$index}}][nombres]" value="{{$usu->nombres}}" required class="w-full border border-slate-200 rounded-xl py-2 px-3 text-sm outline-none focus:border-emerald-500 bg-white"></div>
                        <div><label class="block text-[10px] uppercase font-black text-emerald-600 mb-1">Celular</label><input type="text" pattern="[0-9]*" inputmode="numeric" minlength="9" maxlength="9" oninput="this.value = this.value.replace(/[^0-9]/g, '')" placeholder="Ej. 999999999" name="usuarios[{{$index}}][celular]" value="{{$usu->celular}}" class="w-full border border-slate-200 rounded-xl py-2 px-3 text-sm outline-none focus:border-emerald-500 bg-white"></div>
                        <div><label class="block text-[10px] uppercase font-black text-emerald-600 mb-1">Correo</label><input type="email" name="usuarios[{{$index}}][correo]" value="{{$usu->correo}}" class="w-full border border-slate-200 rounded-xl py-2 px-3 text-sm outline-none focus:border-emerald-500 bg-white"></div>
                        <div class="col-span-2">
                            <label class="block text-[10px] uppercase font-black text-emerald-600 mb-1">Estado Credencial</label>
                            <select name="usuarios[{{$index}}][permisos]" required class="w-full border border-slate-200 rounded-xl py-2 px-3 text-sm outline-none focus:border-emerald-500 bg-white">
                                <option value="">Seleccione...</option>
                                <option value="C.C. Y D.J." {{ $usu->permisos == 'C.C. Y D.J.' ? 'selected' : '' }}>Entregado (C.C. y D.J.)</option>
                                <option value="POR REGULARIZAR" {{ $usu->permisos == 'POR REGULARIZAR' ? 'selected' : '' }}>Por regularizar</option>
                            </select>
                        </div>
                    </div>
                </div>
                @empty
                @endforelse
            </div>
        </div>

        {{-- === TARJETA 7: PERSONAL IMPLEMENTADOR ================================ --}}
        <div class="bg-white rounded-3xl p-8 shadow-xl shadow-slate-200/60 border border-slate-100 slide-up-d6">
            <div class="flex items-center justify-between mb-6 pb-4 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="bg-purple-500 p-2.5 rounded-xl text-white">
                        <i data-lucide="user-check" class="w-5 h-5"></i>
                    </div>
                    <h2 class="text-base font-bold text-slate-800 uppercase tracking-wide">Personal Implementador</h2>
                </div>
                <button type="button" id="btn-add-implem" onclick="agregarImplementador()" class="flex items-center gap-1.5 text-xs font-bold text-purple-600 bg-purple-50 hover:bg-purple-100 px-4 py-2 rounded-xl transition-colors border border-purple-200">
                    <i data-lucide="plus" class="w-3.5 h-3.5"></i> Agregar
                </button>
            </div>
            <div id="implementadores-container" class="space-y-3">
                @forelse($acta->implementadores as $index => $imp)
                <div class="implem-row bg-purple-50/40 border border-purple-100 rounded-2xl p-4 relative pr-12">
                    <button type="button" onclick="this.closest('.implem-row').remove();" class="absolute top-3 right-3 w-7 h-7 flex items-center justify-center rounded-full text-purple-200 hover:text-red-500 hover:bg-red-50 transition-colors" title="Quitar">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 6L6 18M6 6l12 12"/></svg>
                    </button>
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 text-sm">
                        <div>
                            <label class="block text-[10px] uppercase font-black text-purple-600 mb-1">DNI</label>
                            <div class="flex items-center gap-1.5">
                                <div class="relative flex-1">
                                    <input type="text" id="implementador_dni_{{$index}}" name="implementadores[{{$index}}][dni]" value="{{$imp->dni}}" maxlength="15" required
                                        class="w-full border border-slate-200 rounded-xl py-2 px-3 text-sm outline-none focus:border-purple-500 bg-white font-bold"
                                        onkeydown="if(event.key === 'Enter'){event.preventDefault(); buscarPersona('implementador', {{$index}});}">
                                </div>
                                <button type="button" onclick="buscarPersona('implementador', {{$index}})" class="bg-purple-600 hover:bg-purple-700 text-white p-2 rounded-xl shadow-sm transition-colors flex-shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                                </button>
                            </div>
                        </div>
                        <div><label class="block text-[10px] uppercase font-black text-purple-600 mb-1">Ap. Paterno</label><input type="text" id="implementador_ap_{{$index}}" name="implementadores[{{$index}}][apellido_paterno]" value="{{$imp->apellido_paterno}}" required class="w-full border border-slate-200 rounded-xl py-2 px-3 text-sm outline-none focus:border-purple-500 bg-white"></div>
                        <div><label class="block text-[10px] uppercase font-black text-purple-600 mb-1">Ap. Materno</label><input type="text" id="implementador_am_{{$index}}" name="implementadores[{{$index}}][apellido_materno]" value="{{$imp->apellido_materno}}" required class="w-full border border-slate-200 rounded-xl py-2 px-3 text-sm outline-none focus:border-purple-500 bg-white"></div>
                        <div><label class="block text-[10px] uppercase font-black text-purple-600 mb-1">Nombres</label><input type="text" id="implementador_nom_{{$index}}" name="implementadores[{{$index}}][nombres]" value="{{$imp->nombres}}" required class="w-full border border-slate-200 rounded-xl py-2 px-3 text-sm outline-none focus:border-purple-500 bg-white"></div>
                        <div class="col-span-2"><label class="block text-[10px] uppercase font-black text-purple-600 mb-1">Cargo / Equipo</label><input type="text" name="implementadores[{{$index}}][cargo]" value="{{$imp->cargo}}" class="w-full border border-slate-200 rounded-xl py-2 px-3 text-sm outline-none focus:border-purple-500 bg-white" placeholder="Ej. Equipo de Implementación MINSA"></div>
                    </div>
                </div>
                @empty
                @endforelse
            </div>
        </div>

        {{-- === TARJETA 8: OBSERVACIONES ========================================= --}}
        <div class="bg-white rounded-3xl p-8 shadow-xl shadow-slate-200/60 border border-slate-100 slide-up-d7">
            <div class="flex items-center gap-3 mb-6 pb-4 border-b border-slate-100">
                <div class="bg-amber-500 p-2.5 rounded-xl text-white">
                    <i data-lucide="message-square" class="w-5 h-5"></i>
                </div>
                <h2 class="text-base font-bold text-slate-800 uppercase tracking-wide">Observaciones Finales</h2>
            </div>
            <textarea name="observaciones" rows="4" class="w-full bg-slate-50 border border-slate-200 rounded-2xl text-sm p-4 outline-none focus:border-amber-400 focus:ring-1 focus:ring-amber-300 transition-all resize-none" placeholder="Ingrese anotaciones u observaciones sobre la implementación...">{{ $acta->observaciones }}</textarea>
        </div>

        {{-- === TARJETA 10: GESTIÓN DE FIRMAS DIGITALES ========================== --}}
        <div class="bg-white rounded-3xl p-8 shadow-xl shadow-slate-200/60 border border-slate-100 slide-up-d8">
            <div class="flex items-center justify-between mb-6 pb-4 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="bg-indigo-600 p-2.5 rounded-xl text-white">
                        <i data-lucide="signature" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-slate-800 uppercase tracking-wide">Gestión de Firmas Digitales</h2>
                        <p class="text-[10px] text-slate-400 font-bold uppercase mt-1">Sincroniza rúbricas desde el banco centralizado</p>
                    </div>
                </div>
                <div class="flex gap-2">
                    <button type="button" onclick="detectarFirmas()" class="flex items-center gap-1.5 text-xs font-bold text-indigo-600 bg-indigo-50 hover:bg-indigo-100 px-4 py-2 rounded-xl transition-all border border-indigo-100">
                        <i data-lucide="scan-face" class="w-3.5 h-3.5"></i>
                        Detectar en Banco
                    </button>
                    <a href="{{ route('usuario.implementacion.pdf', ['modulo' => $moduloKey, 'id' => $acta->id, 'digital' => 1]) }}" target="_blank" id="btn-generar-digital" class="hidden flex items-center gap-1.5 text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 px-4 py-2 rounded-xl transition-all shadow-lg shadow-indigo-100">
                        <i data-lucide="file-check" class="w-3.5 h-3.5"></i>
                        Ver PDF con Firmas
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Participantes --}}
                <div class="space-y-3">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Estado: Participantes</p>
                    <div id="status-firmas-usuarios" class="space-y-2">
                        @foreach($acta->usuarios as $usu)
                            <div class="flex items-center justify-between p-3 bg-slate-50 rounded-2xl border border-slate-100 dni-status-row" data-dni="{{ $usu->dni }}">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-white flex items-center justify-center border border-slate-100 shadow-sm">
                                        <i data-lucide="user" class="w-4 h-4 text-slate-400"></i>
                                    </div>
                                    <div>
                                        <p class="text-xs font-bold text-slate-700">{{ $usu->apellido_paterno }} {{ $usu->nombres }}</p>
                                        <p class="text-[10px] font-mono text-slate-400">{{ $usu->dni }}</p>
                                    </div>
                                </div>
                                <div class="status-indicator">
                                    <div class="animate-pulse w-4 h-4 bg-slate-200 rounded-full"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Implementadores --}}
                <div class="space-y-3">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Estado: Implementadores</p>
                    <div id="status-firmas-implementadores" class="space-y-2">
                        @foreach($acta->implementadores as $imp)
                            <div class="flex items-center justify-between p-3 bg-slate-50 rounded-2xl border border-slate-100 dni-status-row" data-dni="{{ $imp->dni }}">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-white flex items-center justify-center border border-slate-100 shadow-sm">
                                        <i data-lucide="shield-check" class="w-4 h-4 text-indigo-400"></i>
                                    </div>
                                    <div>
                                        <p class="text-xs font-bold text-indigo-700">{{ $imp->apellido_paterno }} {{ $imp->nombres }}</p>
                                        <p class="text-[10px] font-mono text-slate-400">{{ $imp->dni }}</p>
                                    </div>
                                </div>
                                <div class="status-indicator">
                                    <div class="animate-pulse w-4 h-4 bg-slate-200 rounded-full"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="mt-6 bg-indigo-50/50 rounded-2xl p-4 border border-indigo-100/50">
                <div class="flex gap-3 items-start">
                    <i data-lucide="info" class="w-5 h-5 text-indigo-500 mt-0.5"></i>
                    <div>
                        <p class="text-xs font-bold text-indigo-800">Sobre la Firma Digital</p>
                        <p class="text-[11px] text-indigo-600 mt-1 leading-relaxed">
                            Si todos los involucrados tienen su rúbrica cargada en el <a href="{{ route('admin.firmas.index') }}" target="_blank" class="font-black underline">Banco de Firmas</a>, podrá generar un PDF consolidado automáticamente. Caso contrario, deberá imprimir el documento y cargarlo escaneado en la sección de "Acta Firmada".
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- === BOTÓN GUARDAR ===================================================== --}}
        <div class="slide-up-d8">
            <div class="flex gap-3">
                <a href="{{ route('usuario.implementacion.index') }}" class="flex-1 flex items-center justify-center py-4 bg-white border-2 border-slate-200 rounded-[1.5rem] text-slate-500 font-black text-sm uppercase tracking-widest hover:bg-slate-50 transition-all shadow-sm">
                    Cancelar
                </a>
                <button type="submit" class="flex-[3] py-4 bg-indigo-600 rounded-[1.5rem] text-white font-black text-sm uppercase tracking-widest shadow-xl shadow-indigo-200 hover:bg-indigo-700 hover:scale-[1.01] transition-all flex items-center justify-center gap-3">
                    <i data-lucide="save" class="w-5 h-5"></i>
                    Guardar Cambios
                </button>
            </div>
        </div>

    </form>
</div>
@endsection

@push('scripts')
<script>
    lucide.createIcons();

    // --- CAMBIAR MÓDULO ---
    document.getElementById('modulo_key_select').addEventListener('change', function() {
        const nuevoModulo = this.value;
        Swal.fire({
            title: '¿Cambiar módulo?',
            text: "Esta acta se moverá a un nuevo módulo. Se guardará la información básica y se recargará la página. ¿Desea continuar?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, cambiar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route("usuario.implementacion.cambiar_modulo", ["modulo" => $moduloKey, "id" => $acta->id]) }}';
                
                const csrf = document.createElement('input');
                csrf.type = 'hidden';
                csrf.name = '_token';
                csrf.value = '{{ csrf_token() }}';
                form.appendChild(csrf);

                const nuevoModInput = document.createElement('input');
                nuevoModInput.type = 'hidden';
                nuevoModInput.name = 'nuevo_modulo';
                nuevoModInput.value = nuevoModulo;
                form.appendChild(nuevoModInput);

                document.body.appendChild(form);
                form.submit();
            } else {
                this.value = '{{ $moduloKey }}';
            }
        });
    });

    // --- ESTABLECIMIENTO AUTOCOMPLETE ---
    const inputEst = document.getElementById('busqueda_establecimiento');
    const listaEst = document.getElementById('sugerencias_establecimiento');

    inputEst.addEventListener('input', function () {
        const val = this.value.trim();
        if (val.length >= 3) {
            fetch(`{{ route('usuario.implementacion.ajax.establecimiento') }}?q=${encodeURIComponent(val)}`)
                .then(r => r.json())
                .then(data => {
                    listaEst.innerHTML = '';
                    if (data.length > 0) {
                        listaEst.classList.remove('hidden');
                        data.forEach(est => {
                            const li = document.createElement('li');
                            li.className = 'px-4 py-3 hover:bg-teal-50 cursor-pointer flex flex-col gap-0.5';
                            li.innerHTML = `<span class="font-bold text-slate-700 text-sm">${est.codigo_establecimiento} - ${est.nombre_establecimiento}</span>
                                            <span class="text-[10px] text-slate-500">${est.distrito}, ${est.provincia} (${est.categoria})</span>`;
                            li.addEventListener('click', () => {
                                document.getElementById('codigo_establecimiento').value = est.codigo_establecimiento;
                                document.getElementById('nombre_establecimiento').value = est.nombre_establecimiento;
                                document.getElementById('provincia').value = est.provincia;
                                document.getElementById('distrito').value = est.distrito;
                                document.getElementById('categoria').value = est.categoria;
                                document.getElementById('red').value = est.red ?? '';
                                document.getElementById('microred').value = est.microred ?? '';
                                document.getElementById('responsable').value = est.responsable ?? '';
                                inputEst.value = `${est.codigo_establecimiento} - ${est.nombre_establecimiento}`;
                                listaEst.classList.add('hidden');

                                // Sincronizar RENIPRESS
                                if ("{{ $moduloKey }}" === 'ges_adm') {
                                    syncRenipressData(est.codigo_establecimiento);
                                }
                            });
                            listaEst.appendChild(li);
                        });
                    } else {
                        listaEst.classList.add('hidden');
                    }
                });
        } else {
            listaEst.classList.add('hidden');
        }
    });

    document.addEventListener('click', (e) => {
        if (!inputEst.contains(e.target) && !listaEst.contains(e.target)) listaEst.classList.add('hidden');
    });

    // --- SINCRONIZACIÓN RENIPRESS ---
    async function syncRenipressData(codigo) {
        const section = document.getElementById('section_renipress');
        const status = document.getElementById('sync_status');
        const hiddenInput = document.getElementById('renipress_data_input');
        
        section.classList.remove('hidden');
        status.innerHTML = '<i data-lucide="refresh-cw" class="w-3.5 h-3.5 animate-spin"></i> SINCRONIZANDO...';
        lucide.createIcons();

        try {
            const response = await fetch(`/usuario/implementacion/ajax/renipress-sync?codigo=${codigo}`);
            const result = await response.json();
            
            if (result.success) {
                const data = result.data;
                hiddenInput.value = JSON.stringify(data);
                
                // Llenar tablas
                fillRenipressTable('renipress_upss_body', data.upss);
                fillRenipressTable('renipress_ups_body', data.servicios);
                fillRenipressTable('renipress_especialidades_body', data.especialidades);
                fillRenipressTable('renipress_cartera_body', data.cartera);
                
                status.innerHTML = '<i data-lucide="check" class="w-3.5 h-3.5 text-emerald-500"></i> SINCRONIZADO';
                status.classList.replace('bg-red-50', 'bg-emerald-50');
                status.classList.replace('bg-blue-50', 'bg-emerald-50');
                status.classList.replace('text-red-600', 'text-emerald-600');
                status.classList.replace('text-blue-600', 'text-emerald-600');
                document.getElementById('renipress_fallback').classList.add('hidden');
            } else {
                status.innerHTML = '<i data-lucide="alert-circle" class="w-3.5 h-3.5 text-red-500"></i> PROTECCIÓN ACTIVA';
                status.classList.replace('bg-blue-50', 'bg-red-50');
                status.classList.replace('bg-emerald-50', 'bg-red-50');
                status.classList.replace('text-blue-600', 'text-red-600');
                status.classList.replace('text-emerald-600', 'text-red-600');
                document.getElementById('renipress_fallback').classList.remove('hidden');
            }
        } catch (error) {
            console.error('Error syncing Renipress:', error);
            status.innerHTML = '<i data-lucide="x" class="w-3.5 h-3.5 text-red-500"></i> ERROR DE CONEXIÓN';
            // Mostrar fallback incluso en error de red/servidor
            document.getElementById('renipress_fallback').classList.remove('hidden');
        } finally {
            lucide.createIcons();
        }
    }

    function smartParseRenipress() {
        const text = document.getElementById('renipress_paste_area').value;
        if(!text.trim()) return;

        const lines = text.split('\n');
        
        const data = {
            upss: [],
            servicios: [],
            especialidades: [],
            cartera: []
        };

        let currentCategory = 'upss'; 
        
        const headerCategorizer = (line) => {
            const up = line.toUpperCase().trim();
            // UPSS es un encabezado exacto
            if (up === 'UPSS') return 'upss';
            
            // Unidades Productoras de Servicios - UPS
            // IMPORTANTE: Evitar que la palabra 'UPSS' dentro de una línea active la categoría 'UPS'
            if (up.includes('UNIDADES PRODUCTORAS') || (up.includes('UPS') && !up.includes('UPSS'))) return 'servicios';
            
            if (up.includes('ESPECIALIDADES')) return 'especialidades';
            if (up.includes('CARTERA')) return 'cartera';
            return null;
        };

        const itemRegex = /^([\d\-]{1,15})\s+(.+)$/i;
        const ignoreTerms = ['CÓDIGO', 'NOMBRE', 'ESTADO', 'SERVICIO', 'ESPECIALIDAD', 'REGISTROS:', 'ANTERIOR', 'SIGUIENTE', 'BUSCAR'];

        lines.forEach(line => {
            const cleanLine = line.trim();
            if (!cleanLine) return;

            const newCat = headerCategorizer(cleanLine);
            if (newCat) {
                currentCategory = newCat;
                return;
            }

            if (ignoreTerms.some(term => cleanLine.toUpperCase().startsWith(term))) return;

            const match = cleanLine.match(itemRegex);
            if(match) {
                let codigo = match[1].trim();
                let nombre = match[2].trim();
                nombre = nombre.replace(/\s+(ACTIVO|INACTIVO)$/i, '').trim();
                data[currentCategory].push({ codigo, nombre });
            }
        });

        const totalFound = data.upss.length + data.servicios.length + data.especialidades.length + data.cartera.length;

        if(totalFound > 0) {
            document.getElementById('renipress_data_input').value = JSON.stringify(data);
            
            fillRenipressTable('renipress_upss_body', data.upss);
            fillRenipressTable('renipress_ups_body', data.servicios);
            fillRenipressTable('renipress_especialidades_body', data.especialidades);
            fillRenipressTable('renipress_cartera_body', data.cartera);
            
            Swal.fire({ 
                icon: 'success', 
                title: '¡Datos Procesados!', 
                html: `<div class="text-left text-xs space-y-1">
                        <p><b>UPSS:</b> ${data.upss.length}</p>
                        <p><b>UPS:</b> ${data.servicios.length}</p>
                        <p><b>Especialidades:</b> ${data.especialidades.length}</p>
                        <p><b>Cartera:</b> ${data.cartera.length}</p>
                       </div>`,
                timer: 4000
            });
        } else {
            Swal.fire({ 
                icon: 'warning', 
                title: 'No se detectaron datos', 
                text: 'Asegúrate de incluir los encabezados y copiar las tablas completas.' 
            });
        }
    }

    function fillRenipressTable(id, list) {
        const body = document.getElementById(id);
        body.innerHTML = '';
        if (!list || list.length === 0) {
            body.innerHTML = '<tr><td colspan="2" class="p-3 text-center text-slate-300 italic">No registra</td></tr>';
            return;
        }
        list.forEach(item => {
            const row = document.createElement('tr');
            const estadoHtml = item.estado ? `<span class="ml-2 px-1.5 py-0.5 text-[9px] rounded-md ${item.estado.includes('ACTIVO') ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500'}">${item.estado}</span>` : '';
            row.innerHTML = `<td class="px-3 py-1.5 font-mono text-slate-400 font-bold">${item.codigo}</td>
                             <td class="px-3 py-1.5 text-slate-600 font-black">${item.nombre} ${estadoHtml}</td>`;
            body.appendChild(row);
        });
    }

    // Cargar datos guardados al iniciar
    document.addEventListener('DOMContentLoaded', () => {
        const stored = document.getElementById('renipress_data_input').value;
        if (stored) {
            try {
                const data = JSON.parse(stored);
                if (data) {
                    fillRenipressTable('renipress_upss_body', data.upss);
                    fillRenipressTable('renipress_ups_body', data.ups);
                    fillRenipressTable('renipress_especialidades_body', data.especialidades);
                    fillRenipressTable('renipress_cartera_body', data.cartera);
                }
            } catch(e) {}
        }
    });

    // --- PARTICIPANTES (nuevos) ---
    let idxUsu = {{ $acta->usuarios->count() > 0 ? $acta->usuarios->keys()->last() + 1 : 0 }};
    function agregarParticipante() {
        const tpl = `
        <div class="participante-row bg-emerald-50/40 border border-emerald-100 rounded-2xl p-4 relative pr-12">
            <button type="button" onclick="this.closest('.participante-row').remove()" class="absolute top-3 right-3 w-7 h-7 flex items-center justify-center rounded-full text-slate-300 hover:text-red-500 hover:bg-red-50 transition-colors" title="Quitar">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 6L6 18M6 6l12 12"/></svg>
            </button>
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 text-sm">
                <div>
                    <label class="block text-[10px] uppercase font-black text-emerald-600 mb-1">Tipo Doc</label>
                    <select id="participante_tipodoc_${idxUsu}" name="usuarios[${idxUsu}][tipo_doc]" class="w-full border border-slate-200 rounded-xl py-2 px-3 text-sm outline-none focus:border-emerald-500 bg-white">
                        <option value="DNI">DNI</option>
                        <option value="CE">CE</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] uppercase font-black text-emerald-600 mb-1">NÚM. DOCUMENTO</label>
                    <div class="flex items-center gap-1.5">
                        <div class="relative flex-1">
                            <input type="text" id="participante_dni_${idxUsu}" name="usuarios[${idxUsu}][dni]" maxlength="15" required
                                class="w-full border border-slate-200 rounded-xl py-2 px-3 text-sm outline-none focus:border-emerald-500 bg-white font-bold"
                                onkeydown="if(event.key === 'Enter'){event.preventDefault(); buscarPersona('participante', ${idxUsu});}">
                            <div id="loading_participante_${idxUsu}" class="hidden absolute right-2 top-2">
                                <svg class="w-4 h-4 animate-spin text-emerald-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path></svg>
                            </div>
                        </div>
                        <button type="button" onclick="buscarPersona('participante', ${idxUsu})" class="bg-emerald-600 hover:bg-emerald-700 text-white p-2 rounded-xl shadow-sm transition-colors flex-shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                        </button>
                    </div>
                    <p id="msg_participante_${idxUsu}" class="text-[10px] text-red-500 mt-1 hidden"></p>
                </div>
                <div><label class="block text-[10px] uppercase font-black text-emerald-600 mb-1">Ap. Paterno</label><input type="text" id="participante_ap_${idxUsu}" name="usuarios[${idxUsu}][apellido_paterno]" required class="w-full border border-slate-200 rounded-xl py-2 px-3 text-sm outline-none focus:border-emerald-500 bg-white"></div>
                <div><label class="block text-[10px] uppercase font-black text-emerald-600 mb-1">Ap. Materno</label><input type="text" id="participante_am_${idxUsu}" name="usuarios[${idxUsu}][apellido_materno]" required class="w-full border border-slate-200 rounded-xl py-2 px-3 text-sm outline-none focus:border-emerald-500 bg-white"></div>
                <div><label class="block text-[10px] uppercase font-black text-emerald-600 mb-1">Nombres</label><input type="text" id="participante_nom_${idxUsu}" name="usuarios[${idxUsu}][nombres]" required class="w-full border border-slate-200 rounded-xl py-2 px-3 text-sm outline-none focus:border-emerald-500 bg-white"></div>
                <div><label class="block text-[10px] uppercase font-black text-emerald-600 mb-1">Celular</label><input type="text" pattern="[0-9]*" inputmode="numeric" minlength="9" maxlength="9" oninput="this.value = this.value.replace(/[^0-9]/g, '')" placeholder="Ej. 999999999" name="usuarios[${idxUsu}][celular]" class="w-full border border-slate-200 rounded-xl py-2 px-3 text-sm outline-none focus:border-emerald-500 bg-white"></div>
                <div><label class="block text-[10px] uppercase font-black text-emerald-600 mb-1">Correo</label><input type="email" name="usuarios[${idxUsu}][correo]" class="w-full border border-slate-200 rounded-xl py-2 px-3 text-sm outline-none focus:border-emerald-500 bg-white"></div>
                <div class="col-span-2"><label class="block text-[10px] uppercase font-black text-emerald-600 mb-1">Estado Credencial</label>
                    <select name="usuarios[${idxUsu}][permisos]" required class="w-full border border-slate-200 rounded-xl py-2 px-3 text-sm outline-none focus:border-emerald-500 bg-white">
                        <option value="">Seleccione...</option>
                        <option value="C.C. Y D.J.">Entregado (C.C. y D.J.)</option>
                        <option value="POR REGULARIZAR">Por regularizar</option>
                    </select>
                </div>
            </div>
        </div>`;
        document.getElementById('usuarios-container').insertAdjacentHTML('beforeend', tpl);
        idxUsu++;
    }


    // --- IMPLEMENTADORES (nuevos) ---
    let idxImp = {{ $acta->implementadores->count() }};
    function agregarImplementador() {
        const defaultCargo = 'IMPLEMENTADOR(A)';
        const tpl = `
        <div class="implem-row bg-purple-50/40 border border-purple-100 rounded-2xl p-4 relative pr-12">
            <button type="button" onclick="this.closest('.implem-row').remove();" class="absolute top-3 right-3 w-7 h-7 flex items-center justify-center rounded-full text-purple-200 hover:text-red-500 hover:bg-red-50 transition-colors" title="Quitar">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 6L6 18M6 6l12 12"/></svg>
            </button>
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 text-sm">
                <div>
                    <label class="block text-[10px] uppercase font-black text-purple-600 mb-1">DNI</label>
                    <div class="flex items-center gap-1.5">
                        <div class="relative flex-1">
                            <input type="text" id="implementador_dni_${idxImp}" name="implementadores[${idxImp}][dni]" maxlength="15" required
                                class="w-full border border-slate-200 rounded-xl py-2 px-3 text-sm outline-none focus:border-purple-500 bg-white font-bold"
                                onkeydown="if(event.key === 'Enter'){event.preventDefault(); buscarPersona('implementador', ${idxImp});}">
                            <div id="loading_implementador_${idxImp}" class="hidden absolute right-2 top-2">
                                <svg class="w-4 h-4 animate-spin text-purple-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path></svg>
                            </div>
                        </div>
                        <button type="button" onclick="buscarPersona('implementador', ${idxImp})" class="bg-purple-600 hover:bg-purple-700 text-white p-2 rounded-xl shadow-sm transition-colors flex-shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                        </button>
                    </div>
                    <p id="msg_implementador_${idxImp}" class="text-[10px] text-red-500 mt-1 hidden"></p>
                </div>
                <div><label class="block text-[10px] uppercase font-black text-purple-600 mb-1">Ap. Paterno</label><input type="text" id="implementador_ap_${idxImp}" name="implementadores[${idxImp}][apellido_paterno]" required class="w-full border border-slate-200 rounded-xl py-2 px-3 text-sm outline-none focus:border-purple-500 bg-white"></div>
                <div><label class="block text-[10px] uppercase font-black text-purple-600 mb-1">Ap. Materno</label><input type="text" id="implementador_am_${idxImp}" name="implementadores[${idxImp}][apellido_materno]" required class="w-full border border-slate-200 rounded-xl py-2 px-3 text-sm outline-none focus:border-purple-500 bg-white"></div>
                <div><label class="block text-[10px] uppercase font-black text-purple-600 mb-1">Nombres</label><input type="text" id="implementador_nom_${idxImp}" name="implementadores[${idxImp}][nombres]" required class="w-full border border-slate-200 rounded-xl py-2 px-3 text-sm outline-none focus:border-purple-500 bg-white"></div>
                <div class="col-span-2"><label class="block text-[10px] uppercase font-black text-purple-600 mb-1">Cargo / Equipo</label><input type="text" name="implementadores[${idxImp}][cargo]" class="w-full border border-slate-200 rounded-xl py-2 px-3 text-sm outline-none focus:border-purple-500 bg-white" placeholder="Ej. Equipo de Implementación MINSA" value="${defaultCargo}"></div>
            </div>
        </div>`;
        document.getElementById('implementadores-container').insertAdjacentHTML('beforeend', tpl);
        idxImp++;
    }

    // --- BÚSQUEDA PERSONA (RENIEC / LOCAL) ---
    async function buscarPersona(tipo, index) {
        const docInput = document.getElementById(`${tipo}_dni_${index}`);
        const tipoDocInput = document.getElementById(`${tipo}_tipodoc_${index}`);
        
        const doc = docInput ? docInput.value.trim() : '';
        const tipoDoc = tipoDocInput ? tipoDocInput.value : 'DNI';
        
        const loader = document.getElementById(`loading_${tipo}_${index}`);
        const msg = document.getElementById(`msg_${tipo}_${index}`);
        
        if (doc.length < 5) return;
        if (loader) loader.classList.remove('hidden');
        if (msg) msg.classList.add('hidden');
        
        const baseUrl = `{{ route('usuario.monitoreo.citas.buscar.profesional') }}`;
        try {
            const response = await fetch(`${baseUrl}?type=doc&q=${doc}&tipo_doc=${tipoDoc}&local_only=1`);
            const data = await response.json();
            if (data.length > 0) {
                rellenarPersona(tipo, index, data[0]);
                if (msg) {
                    msg.textContent = 'Persona encontrada.';
                    msg.className = `text-[10px] ${tipo == 'participante' ? 'text-emerald-600' : 'text-purple-600'} mt-1 font-bold`;
                    msg.classList.remove('hidden');
                }
            } else {
                if (tipoDoc === 'DNI' && doc.length === 8) {
                    Swal.fire({
                        html: `<div class="p-4 flex flex-col items-center">
                            <div class="h-14 w-14 bg-gradient-to-br from-blue-500 to-blue-700 rounded-full flex items-center justify-center shadow-xl mb-4">
                                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="text-white"><path d="M2 12h20"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                            </div>
                            <h3 class="text-xl font-black text-blue-900 mb-2">Consultando RENIEC</h3>
                            <p class="text-xs text-slate-400 text-center">Extrayendo nombres oficiales de la plataforma nacional.</p>
                        </div>`,
                        allowOutsideClick: false, showConfirmButton: false,
                        customClass: { popup: 'rounded-[2rem]' }
                    });
                    const responseExt = await fetch(`${baseUrl}?type=doc&q=${doc}&tipo_doc=DNI`);
                    const dataExt = await responseExt.json();
                    Swal.close();
                    if (dataExt.length > 0 && dataExt[0].exists_external) {
                        rellenarPersona(tipo, index, dataExt[0]);
                        if (msg) {
                            msg.textContent = 'Extraído de RENIEC.';
                            msg.className = 'text-[10px] text-blue-600 mt-1 font-bold';
                            msg.classList.remove('hidden');
                        }
                    } else {
                        mostrarMsgNuevoPersona(tipo, index, msg);
                    }
                } else {
                    mostrarMsgNuevoPersona(tipo, index, msg);
                }
            }
        } catch (error) {
            console.error('Error:', error);
            if (Swal.isVisible()) Swal.close();
        } finally {
            if (loader) loader.classList.add('hidden');
        }
    }

    function mostrarMsgNuevoPersona(tipo, index, msg) {
        msg.textContent = 'Persona nueva. Complete los datos.';
        msg.className = 'text-[10px] text-blue-600 mt-1 font-bold';
        msg.classList.remove('hidden');
        const ap = document.getElementById(`${tipo}_ap_${index}`);
        if (ap) ap.focus();
    }

    function rellenarPersona(tipo, index, prof) {
        const ap = document.getElementById(`${tipo}_ap_${index}`);
        const am = document.getElementById(`${tipo}_am_${index}`);
        const nom = document.getElementById(`${tipo}_nom_${index}`);
        if (ap && prof.apellido_paterno) ap.value = prof.apellido_paterno;
        if (am && prof.apellido_materno) am.value = prof.apellido_materno;
        if (nom && prof.nombres) nom.value = prof.nombres;
        if (tipo === 'participante') {
            const cel = document.querySelector(`input[name="usuarios[${index}][celular]"]`);
            if (cel && (prof.celular || prof.telefono)) cel.value = prof.celular || prof.telefono;
            const correo = document.querySelector(`input[name="usuarios[${index}][correo]"]`);
            if (correo && (prof.email || prof.correo)) correo.value = prof.email || prof.correo;
        }
    }


    // ====== EVIDENCIA FOTOGRÁFICA (EDIT) ======
    function previewFotoEdit(slot, input) {
        if (!input.files || !input.files[0]) return;
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById(slot + '_preview').src = e.target.result;
            document.getElementById(slot + '_preview').classList.remove('hidden');
            document.getElementById(slot + '_placeholder').classList.add('hidden');
            document.getElementById(slot + '_actions').classList.remove('hidden');
        };
        reader.readAsDataURL(input.files[0]);
    }

    function clearFotoEdit(slot) {
        document.getElementById(slot + '_input').value = '';
        document.getElementById(slot + '_preview').src = '';
        document.getElementById(slot + '_preview').classList.add('hidden');
        document.getElementById(slot + '_placeholder').classList.remove('hidden');
        document.getElementById(slot + '_actions').classList.add('hidden');
    }

    // --- LÓGICA RENIPRESS (EDIT) ---
    function toggleRenipressManual() {
        const fallback = document.getElementById('renipress_fallback');
        if (fallback.classList.contains('hidden')) {
            fallback.classList.remove('hidden');
            document.getElementById('renipress_paste_area').focus();
        } else {
            fallback.classList.add('hidden');
        }
    }

    function fillRenipressTable(tableId, data) {
        const body = document.getElementById(tableId);
        if (!body) return;
        body.innerHTML = '';
        if (!data || data.length === 0) {
            body.innerHTML = '<tr><td colspan="2" class="px-3 py-4 text-center text-slate-400 italic">No se encontraron registros</td></tr>';
            return;
        }
        data.forEach(item => {
            const row = document.createElement('tr');
            const estadoHtml = item.estado ? `<span class="ml-2 px-1.5 py-0.5 text-[9px] rounded-md ${item.estado.includes('ACTIVO') ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500'}">${item.estado}</span>` : '';
            row.innerHTML = `<td class="px-3 py-2 font-mono text-slate-500 font-bold">${item.codigo}</td>
                             <td class="px-3 py-2 text-slate-700 font-black">${item.nombre}${estadoHtml}</td>`;
            body.appendChild(row);
        });
    }

    async function syncRenipressData(codigo) {
        const section = document.getElementById('section_renipress');
        const status = document.getElementById('sync_status');
        const hiddenInput = document.getElementById('renipress_data_input');
        
        if (section) section.classList.remove('hidden');
        if (status) {
            status.innerHTML = '<i data-lucide="refresh-cw" class="w-3.5 h-3.5 animate-spin"></i> SINCRONIZANDO...';
            lucide.createIcons();
        }

        try {
            const response = await fetch(`/usuario/implementacion/ajax/renipress-sync?codigo=${codigo}`);
            const result = await response.json();
            
            if (result.success) {
                const data = result.data;
                hiddenInput.value = JSON.stringify(data);
                
                fillRenipressTable('renipress_upss_body', data.upss);
                fillRenipressTable('renipress_ups_body', data.servicios); 
                fillRenipressTable('renipress_especialidades_body', data.especialidades);
                fillRenipressTable('renipress_cartera_body', data.cartera);
                
                if (status) {
                    status.innerHTML = '<i data-lucide="check" class="w-3.5 h-3.5 text-emerald-500"></i> SINCRONIZADO';
                    status.className = 'flex items-center gap-1.5 px-3 py-1 bg-emerald-50 text-emerald-600 rounded-full text-[10px] font-black border border-emerald-100';
                }
                document.getElementById('renipress_fallback').classList.add('hidden');
            } else {
                if (status) {
                    status.innerHTML = '<i data-lucide="alert-circle" class="w-3.5 h-3.5 text-amber-500"></i> MODO MANUAL ACTIVO';
                    status.className = 'flex items-center gap-1.5 px-3 py-1 bg-amber-50 text-amber-600 rounded-full text-[10px] font-black border border-amber-100';
                }
                document.getElementById('renipress_fallback').classList.remove('hidden');
            }
        } catch (error) {
            console.error('Error syncing Renipress:', error);
            if (status) status.innerHTML = '<i data-lucide="x" class="w-3.5 h-3.5 text-red-500"></i> ERROR DE CONEXIÓN';
            document.getElementById('renipress_fallback').classList.remove('hidden');
        } finally {
            if (typeof lucide !== 'undefined') lucide.createIcons();
        }
    }

    function smartParseRenipress() {
        const text = document.getElementById('renipress_paste_area').value;
        if(!text.trim()) return;

        const lines = text.split('\n');
        const data = { upss: [], servicios: [], especialidades: [], cartera: [] };
        let currentCategory = 'upss'; 
        
        const headerCategorizer = (line) => {
            const up = line.toUpperCase().trim();
            if (up === 'UPSS') return 'upss';
            if (up.includes('UNIDADES PRODUCTORAS') || (up.includes('UPS') && !up.includes('UPSS'))) return 'servicios';
            if (up.includes('ESPECIALIDADES')) return 'especialidades';
            if (up.includes('CARTERA')) return 'cartera';
            return null;
        };

        const itemRegex = /^([\d\-]{1,15})\s+(.+)$/i;
        const ignoreTerms = ['CÓDIGO', 'NOMBRE', 'ESTADO', 'SERVICIO', 'ESPECIALIDAD', 'REGISTROS:', 'ANTERIOR', 'SIGUIENTE', 'BUSCAR'];

        lines.forEach(line => {
            const cleanLine = line.trim();
            if (!cleanLine) return;
            const newCat = headerCategorizer(cleanLine);
            if (newCat) {
                currentCategory = newCat;
                return;
            }
            if (ignoreTerms.some(term => cleanLine.toUpperCase().startsWith(term))) return;
            const match = cleanLine.match(itemRegex);
            if(match) {
                let codigo = match[1].trim();
                let nombre = match[2].trim();
                nombre = nombre.replace(/\s+(ACTIVO|INACTIVO)$/i, '').trim();
                data[currentCategory].push({ codigo, nombre });
            }
        });

        const totalFound = data.upss.length + data.servicios.length + data.especialidades.length + data.cartera.length;
        if(totalFound > 0) {
            document.getElementById('renipress_data_input').value = JSON.stringify(data);
            fillRenipressTable('renipress_upss_body', data.upss);
            fillRenipressTable('renipress_ups_body', data.servicios);
            fillRenipressTable('renipress_especialidades_body', data.especialidades);
            fillRenipressTable('renipress_cartera_body', data.cartera);
            
            Swal.fire({ 
                icon: 'success', 
                title: '¡Datos Procesados!', 
                html: `<div class="text-left text-xs space-y-1">
                        <p><b>UPSS:</b> ${data.upss.length}</p>
                        <p><b>UPS:</b> ${data.servicios.length}</p>
                        <p><b>Especialidades:</b> ${data.especialidades.length}</p>
                        <p><b>Cartera:</b> ${data.cartera.length}</p>
                       </div>`,
                timer: 4000
            });
        }
    }

    // Cargar datos guardados al iniciar
    document.addEventListener('DOMContentLoaded', () => {
        const stored = document.getElementById('renipress_data_input').value;
        if (stored) {
            try {
                const data = JSON.parse(stored);
                if (data) {
                    fillRenipressTable('renipress_upss_body', data.upss);
                    fillRenipressTable('renipress_ups_body', data.servicios || data.ups);
                    fillRenipressTable('renipress_especialidades_body', data.especialidades);
                    fillRenipressTable('renipress_cartera_body', data.cartera);
                }
            } catch (e) { console.error('Error loading RENIPRESS data:', e); }
        }
    });
    // --- DETECTAR FIRMAS DIGITALES ---
    async function detectarFirmas() {
        const rows = document.querySelectorAll('.dni-status-row');
        const btnGenerar = document.getElementById('btn-generar-digital');
        let totalFirmas = 0;

        for (const row of rows) {
            const dni = row.getAttribute('data-dni');
            const indicator = row.querySelector('.status-indicator');
            indicator.innerHTML = '<div class="animate-spin w-4 h-4 border-2 border-indigo-500 border-t-transparent rounded-full font-bold"></div>';
            
            try {
                const response = await fetch(`/admin/banco-firmas/search-ajax?term=${dni}`);
                const results = await response.json();
                
                // Buscar resultado exacto
                const match = results.find(r => r.text.includes(dni));
                
                if (match && match.has_firma) {
                    indicator.innerHTML = '<div class="flex items-center gap-1 px-2 py-0.5 bg-emerald-100 text-emerald-600 rounded-lg text-[10px] font-black uppercase tracking-wider animate-in zoom-in"><i data-lucide="check" class="w-3 h-3"></i></div>';
                    totalFirmas++;
                } else {
                    indicator.innerHTML = '<div class="flex items-center gap-1 px-2 py-0.5 bg-red-50 text-red-500 rounded-lg text-[10px] font-black uppercase tracking-wider animate-in zoom-in"><i data-lucide="x" class="w-3 h-3"></i></div>';
                }
            } catch (error) {
                indicator.innerHTML = '<div class="w-4 h-4 bg-slate-200 rounded-full"></div>';
            }
            refreshLucide();
        }

        if (totalFirmas > 0) {
            btnGenerar.classList.remove('hidden');
            btnGenerar.classList.add('animate-in', 'slide-in-from-right-10', 'duration-500');
            
            if (totalFirmas === rows.length) {
                Swal.fire({
                    icon: 'success',
                    title: '¡Firmas Completas!',
                    text: 'Se han detectado las firmas de todos los participantes. Ya puede generar el PDF consolidado.',
                    timer: 3000,
                    showConfirmButton: false,
                    customClass: { popup: 'rounded-3xl' }
                });
            } else {
                Swal.fire({
                    icon: 'info',
                    title: 'Firmas Parciales',
                    text: `Se detectaron ${totalFirmas} de ${rows.length} firmas. El PDF solo incluirá las firmas encontradas.`,
                    customClass: { popup: 'rounded-3xl' }
                });
            }
        } else {
            Swal.fire({
                icon: 'warning',
                title: 'Sin Firmas',
                text: 'No se encontraron firmas en el banco para los participantes de esta acta.',
                customClass: { popup: 'rounded-3xl' }
            });
        }
    }

    // Auto-ejecutar detección al cargar
    document.addEventListener('DOMContentLoaded', () => {
        setTimeout(detectarFirmas, 1000);
    });

</script>
@endpush
