@extends('layouts.usuario')

@section('title', 'Nueva Acta ' . $moduloConfig['nombre'])

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

    /* Campo readonly info */
    .field-info { background:#f8fafc; border:1px solid #e2e8f0; border-radius:0.75rem; padding:0.6rem 0.85rem; font-size:0.8rem; color:#475569; font-weight:600; }

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
        <i data-lucide="file-plus-2" class="w-5 h-5"></i>
    </div>
    <div>
        <h1 class="text-xl font-bold text-slate-800 tracking-tight">Nueva Acta: {{ $moduloConfig['nombre'] }}</h1>
        <div class="flex items-center gap-1.5 text-xs text-slate-400 mt-0.5">
            <a href="{{ route('usuario.implementacion.index') }}" class="hover:text-indigo-600 font-semibold">Actas de Implementación</a>
            <span>›</span>
            <span>Nuevo</span>
        </div>
    </div>
</div>
@endsection

@section('content')
<div class="max-w-5xl mx-auto">
    <form action="{{ route('usuario.implementacion.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        <input type="hidden" name="modulo_key" value="{{ $moduloKey }}">

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
                <div class="bg-indigo-50 rounded-2xl p-4 border border-indigo-100">
                    <label class="text-[10px] font-black text-indigo-400 uppercase tracking-widest block mb-2">Módulo</label>
                    <div class="flex items-center gap-2">
                        <i data-lucide="grid-2x2" class="w-5 h-5 text-indigo-600 flex-shrink-0"></i>
                        <select onchange="window.location.href='?modulo='+this.value" class="bg-transparent border-0 p-0 text-sm font-black text-indigo-900 focus:ring-0 w-full cursor-pointer outline-none">
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
                        <input type="date" name="fecha" required value="{{ date('Y-m-d') }}" class="bg-transparent border-0 p-0 text-sm font-black text-slate-800 focus:ring-0 w-full cursor-pointer outline-none">
                    </div>
                </div>

            </div>
        </div>

        {{-- === TARJETA 2: ESTABLECIMIENTO ======================================== --}}
        <div class="bg-white rounded-3xl p-8 shadow-xl shadow-slate-200/60 border border-slate-100 slide-up-d2">
            <div class="flex items-center gap-3 mb-6 pb-4 border-b border-slate-100">
                <div class="bg-teal-600 p-2.5 rounded-xl text-white">
                    <i data-lucide="hospital" class="w-5 h-5"></i>
                </div>
                <h2 class="text-base font-bold text-slate-800 uppercase tracking-wide">2. Datos del Establecimiento</h2>
            </div>

            {{-- Buscador --}}
            <div class="relative mb-5">
                <label class="text-xs font-bold text-slate-500 uppercase mb-2 block">Buscar Establecimiento (Renipress / Nombre)</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i data-lucide="search" class="h-5 w-5 text-slate-400"></i>
                    </div>
                    <input type="text" id="busqueda_establecimiento" placeholder="Ej: HOSPITAL REGIONAL, 120001..." autocomplete="off"
                        class="block w-full pl-11 pr-4 py-4 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-bold text-slate-800 placeholder-slate-400 focus:ring-2 focus:ring-teal-400 focus:bg-white transition-all uppercase">
                </div>
                <ul id="sugerencias_establecimiento" class="absolute z-50 bg-white border border-slate-200 w-full rounded-2xl shadow-xl mt-1 hidden max-h-60 overflow-y-auto divide-y divide-slate-50"></ul>
            </div>

            {{-- Campos automáticos --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                <div class="col-span-2 md:col-span-3 p-4 bg-slate-50 rounded-2xl border border-slate-100">
                    <label class="text-[9px] font-black text-slate-400 uppercase block mb-1">Nombre Establecimiento</label>
                    <input type="text" id="nombre_establecimiento" name="nombre_establecimiento" readonly required
                        class="w-full bg-transparent border-0 p-0 text-sm font-bold text-slate-700 focus:ring-0 placeholder-slate-300"
                        placeholder="— Seleccione un establecimiento —">
                </div>
                <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100">
                    <label class="text-[9px] font-black text-slate-400 uppercase block mb-1">RENIPRESS</label>
                    <input type="text" id="codigo_establecimiento" name="codigo_establecimiento" readonly required
                        class="w-full bg-transparent border-0 p-0 text-xs font-bold text-slate-700 focus:ring-0 font-mono"
                        placeholder="———">
                </div>
                <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100">
                    <label class="text-[9px] font-black text-slate-400 uppercase block mb-1">Provincia</label>
                    <input type="text" id="provincia" name="provincia" readonly class="w-full bg-transparent border-0 p-0 text-xs font-bold text-slate-700 focus:ring-0" value="---">
                </div>
                <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100">
                    <label class="text-[9px] font-black text-slate-400 uppercase block mb-1">Distrito</label>
                    <input type="text" id="distrito" name="distrito" readonly class="w-full bg-transparent border-0 p-0 text-xs font-bold text-slate-700 focus:ring-0" value="---">
                </div>
                <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100">
                    <label class="text-[9px] font-black text-slate-400 uppercase block mb-1">Categoría</label>
                    <input type="text" id="categoria" name="categoria" readonly class="w-full bg-transparent border-0 p-0 text-xs font-bold text-slate-700 focus:ring-0" value="---">
                </div>
                <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100">
                    <label class="text-[9px] font-black text-slate-400 uppercase block mb-1">Red</label>
                    <input type="text" id="red" name="red" readonly class="w-full bg-transparent border-0 p-0 text-xs font-bold text-slate-700 focus:ring-0" value="---">
                </div>
                <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100">
                    <label class="text-[9px] font-black text-slate-400 uppercase block mb-1">Microred</label>
                    <input type="text" id="microred" name="microred" readonly class="w-full bg-transparent border-0 p-0 text-xs font-bold text-slate-700 focus:ring-0" value="---">
                </div>
                <div class="col-span-2 md:col-span-4">
                    <label class="text-xs font-bold text-slate-500 uppercase mb-2 block">Responsable del Establecimiento</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i data-lucide="user-cog" class="h-5 w-5 text-slate-400"></i>
                        </div>
                        <input type="text" id="responsable" name="responsable" required placeholder="Nombre del médico jefe o responsable"
                            class="block w-full pl-11 pr-4 py-3 bg-white border border-slate-200 rounded-xl text-sm font-bold text-slate-700 focus:border-teal-400 focus:ring-teal-400 uppercase">
                    </div>
                </div>
            </div>
        </div>

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
                    <input type="radio" name="modalidad" id="modal_horario" value="POR HORARIO">
                    <label for="modal_horario">
                        <i data-lucide="clock" class="w-4 h-4"></i> Por Horario
                    </label>
                </div>
                <div class="radio-pill-blue">
                    <input type="radio" name="modalidad" id="modal_seleccion" value="POR SELECCION">
                    <label for="modal_seleccion">
                        <i data-lucide="mouse-pointer-click" class="w-4 h-4"></i> Por Selección (Exclusividad)
                    </label>
                </div>
            </div>
        </div>
        @endif

        {{-- === TARJETA 4: UPSS/UPS (solo Ges. Adm.) ============================= --}}
        @if($moduloKey === 'ges_adm')
        <div class="bg-white rounded-3xl p-8 shadow-xl shadow-slate-200/60 border border-slate-100 slide-up-d3">
            <div class="flex items-center gap-3 mb-6 pb-4 border-b border-slate-100">
                <div class="bg-cyan-600 p-2.5 rounded-xl text-white">
                    <i data-lucide="building-2" class="w-5 h-5"></i>
                </div>
                <h2 class="text-base font-bold text-slate-800 uppercase tracking-wide">3. UPSS / UPS</h2>
            </div>
            <div id="seccion-upss" class="space-y-5">
                {{-- Tabla 1: Renipress SUSALUD --}}
                <div>
                    <p class="text-xs font-black text-slate-500 uppercase tracking-widest mb-3">Renipress SUSALUD (UPS / UPSS)</p>
                    <div class="overflow-hidden rounded-2xl border border-slate-200 shadow-sm">
                        <table class="w-full text-left text-xs text-slate-600">
                            <thead class="bg-cyan-600 text-white font-semibold">
                                <tr>
                                    <th class="p-3">UPSS</th>
                                    <th class="p-3 border-l border-cyan-500">Estado UPSS</th>
                                    <th class="p-3 border-l border-cyan-500">UPS</th>
                                    <th class="p-3 border-l border-cyan-500">Estado UPS</th>
                                </tr>
                            </thead>
                            <tbody id="upss-establecimiento-body">
                                <tr><td colspan="4" class="p-4 text-center text-slate-400 italic">Seleccione un establecimiento para ver sus UPSS</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Tabla 2: Regularizar --}}
                <div>
                    <p class="text-xs font-black text-slate-500 uppercase tracking-widest mb-3">(UPSS/UPS) Regularizar en Renipress SUSALUD</p>
                    <div class="bg-slate-50 rounded-2xl border border-slate-200 p-4">
                        <div class="relative mb-3">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i data-lucide="search" class="h-4 w-4 text-slate-400"></i>
                            </div>
                            <input type="text" id="upss-search-input" class="w-full pl-10 pr-4 py-2.5 bg-white border border-slate-200 rounded-xl text-sm outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-300 transition-all" placeholder="Escriba código o descripción de UPSS/UPS..." onkeyup="buscarUpsGlobal(this)">
                            <div id="upss_global_results" class="absolute z-50 w-full bg-white border border-slate-200 shadow-xl mt-1 rounded-xl hidden max-h-60 overflow-y-auto"></div>
                        </div>
                        <button type="button" onclick="agregarUpssManual()" class="flex items-center gap-1.5 text-xs font-bold text-white bg-teal-600 hover:bg-teal-700 px-4 py-2 rounded-xl transition-colors mb-3">
                            <i data-lucide="plus" class="w-3.5 h-3.5"></i> Agregar UPSS/UPS Manual
                        </button>
                        <div id="upss-container" class="space-y-2"></div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- === TARJETA 5: FIRMA DIGITAL (Módulos específicos) =================== --}}
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
                    <input type="radio" name="firma_digital" id="firma_si" value="SI" required>
                    <label for="firma_si">
                        <i data-lucide="check-circle" class="w-4 h-4 text-emerald-500"></i> SÍ, cuenta con firma digital
                    </label>
                </div>
                <div class="radio-pill">
                    <input type="radio" name="firma_digital" id="firma_no" value="NO" required>
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
            <div id="usuarios-container" class="space-y-3"></div>
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
            <div id="implementadores-container" class="space-y-3"></div>
        </div>

        {{-- === TARJETA 8: OBSERVACIONES ========================================= --}}
        <div class="bg-white rounded-3xl p-8 shadow-xl shadow-slate-200/60 border border-slate-100 slide-up-d7">
            <div class="flex items-center gap-3 mb-6 pb-4 border-b border-slate-100">
                <div class="bg-amber-500 p-2.5 rounded-xl text-white">
                    <i data-lucide="message-square" class="w-5 h-5"></i>
                </div>
                <h2 class="text-base font-bold text-slate-800 uppercase tracking-wide">Observaciones Finales</h2>
            </div>
            <textarea name="observaciones" rows="4" class="w-full bg-slate-50 border border-slate-200 rounded-2xl text-sm p-4 outline-none focus:border-amber-400 focus:ring-1 focus:ring-amber-300 transition-all resize-none" placeholder="Ingrese anotaciones u observaciones sobre la implementación..."></textarea>
        </div>

        {{-- === TARJETA 9: EVIDENCIA FOTOGRAFICA ================================== --}}
        <div class="bg-white rounded-3xl p-8 shadow-xl shadow-slate-200/60 border border-slate-100 slide-up-d8">
            <div class="flex items-center gap-3 mb-6 pb-4 border-b border-slate-100">
                <div class="bg-orange-500 p-2.5 rounded-xl text-white">
                    <i data-lucide="camera" class="w-5 h-5"></i>
                </div>
                <div>
                    <h2 class="text-base font-bold text-slate-800 uppercase tracking-wide">Evidencia Fotográfica</h2>
                    <p class="text-xs text-slate-400 mt-0.5">Adjunte hasta 2 fotografías como evidencia de la implementación</p>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                {{-- FOTO 1 --}}
                <div>
                    <p class="text-[10px] font-black text-orange-500 uppercase tracking-widest mb-2">Foto 1</p>
                    <label for="foto1_input" class="group relative flex flex-col items-center justify-center gap-3 min-h-[180px] bg-orange-50/60 border-2 border-dashed border-orange-200 rounded-2xl cursor-pointer hover:bg-orange-50 hover:border-orange-400 transition-all overflow-hidden" id="foto1_label">
                        <img id="foto1_preview" class="absolute inset-0 w-full h-full object-cover rounded-2xl hidden" alt="Foto 1">
                        <div id="foto1_placeholder" class="flex flex-col items-center gap-2 p-4 text-center z-10">
                            <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center shadow-sm group-hover:scale-110 transition-transform">
                                <i data-lucide="image-plus" class="w-6 h-6 text-orange-400"></i>
                            </div>
                            <p class="text-sm font-bold text-slate-600">Haz clic para subir foto 1</p>
                            <p class="text-[10px] text-slate-400">JPG, PNG, WEBP — máx. 5 MB</p>
                        </div>
                        <div id="foto1_overlay" class="absolute inset-0 bg-black/0 group-hover:bg-black/10 rounded-2xl transition-all hidden"></div>
                    </label>
                    <input type="file" id="foto1_input" name="foto1" accept="image/*" class="hidden"
                        onchange="previewFotoCreate('foto1', this)">
                    <div id="foto1_actions" class="hidden mt-2 flex justify-end">
                        <button type="button" onclick="clearFotoCreate('foto1')" class="text-xs font-bold text-red-500 hover:text-red-600 flex items-center gap-1">
                            <i data-lucide="x" class="w-3 h-3"></i> Quitar foto
                        </button>
                    </div>
                </div>

                {{-- FOTO 2 --}}
                <div>
                    <p class="text-[10px] font-black text-orange-500 uppercase tracking-widest mb-2">Foto 2</p>
                    <label for="foto2_input" class="group relative flex flex-col items-center justify-center gap-3 min-h-[180px] bg-orange-50/60 border-2 border-dashed border-orange-200 rounded-2xl cursor-pointer hover:bg-orange-50 hover:border-orange-400 transition-all overflow-hidden" id="foto2_label">
                        <img id="foto2_preview" class="absolute inset-0 w-full h-full object-cover rounded-2xl hidden" alt="Foto 2">
                        <div id="foto2_placeholder" class="flex flex-col items-center gap-2 p-4 text-center z-10">
                            <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center shadow-sm group-hover:scale-110 transition-transform">
                                <i data-lucide="image-plus" class="w-6 h-6 text-orange-400"></i>
                            </div>
                            <p class="text-sm font-bold text-slate-600">Haz clic para subir foto 2</p>
                            <p class="text-[10px] text-slate-400">JPG, PNG, WEBP — máx. 5 MB</p>
                        </div>
                        <div id="foto2_overlay" class="absolute inset-0 bg-black/0 group-hover:bg-black/10 rounded-2xl transition-all hidden"></div>
                    </label>
                    <input type="file" id="foto2_input" name="foto2" accept="image/*" class="hidden"
                        onchange="previewFotoCreate('foto2', this)">
                    <div id="foto2_actions" class="hidden mt-2 flex justify-end">
                        <button type="button" onclick="clearFotoCreate('foto2')" class="text-xs font-bold text-red-500 hover:text-red-600 flex items-center gap-1">
                            <i data-lucide="x" class="w-3 h-3"></i> Quitar foto
                        </button>
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
                    Guardar Acta de Implementación
                </button>
            </div>
        </div>

    </form>
</div>
@endsection

@push('scripts')
<script>
    lucide.createIcons();

    // --- ESTABLECIMIENTO AUTOCOMPLETE ---
    const inputEst = document.getElementById('busqueda_establecimiento');
    const listaEst = document.getElementById('sugerencias_establecimiento');

    inputEst.addEventListener('input', function () {
        const val = this.value.trim();
        if (val.length >= 3) {
            fetch(`/usuario/implementacion/ajax/establecimiento?q=${encodeURIComponent(val)}`)
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

    // --- PARTICIPANTES ---
    let idxUsu = 0;
    function agregarParticipante() {
        const tpl = `
        <div class="participante-row bg-emerald-50/40 border border-emerald-100 rounded-2xl p-4 relative pr-12">
            <button type="button" onclick="this.closest('.participante-row').remove()" class="absolute top-3 right-3 w-7 h-7 flex items-center justify-center rounded-full text-slate-300 hover:text-red-500 hover:bg-red-50 transition-colors" title="Quitar">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 6L6 18M6 6l12 12"/></svg>
            </button>
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 text-sm">
                <div>
                    <label class="block text-[10px] uppercase font-black text-emerald-600 mb-1">DNI</label>
                    <div class="flex items-center gap-1.5">
                        <div class="relative flex-1">
                            <input type="text" id="participante_dni_${idxUsu}" name="usuarios[${idxUsu}][dni]" maxlength="15" required
                                class="w-full border border-slate-200 rounded-xl py-2 px-3 text-sm outline-none focus:border-emerald-500 bg-white font-bold"
                                onkeydown="if(event.key === 'Enter'){event.preventDefault(); buscarPersona('participante', ${idxUsu});}">
                            <div id="loading_participante_${idxUsu}" class="hidden absolute right-2 top-2">
                                <i data-lucide="loader-2" class="w-4 h-4 animate-spin text-emerald-600"></i>
                            </div>
                        </div>
                        <button type="button" onclick="buscarPersona('participante', ${idxUsu})" class="bg-emerald-600 hover:bg-emerald-700 text-white p-2 rounded-xl shadow-sm transition-colors flex-shrink-0">
                            <i data-lucide="search" class="w-4 h-4"></i>
                        </button>
                    </div>
                    <p id="msg_participante_${idxUsu}" class="text-[10px] text-red-500 mt-1 hidden"></p>
                </div>
                <div><label class="block text-[10px] uppercase font-black text-emerald-600 mb-1">Ap. Paterno</label><input type="text" id="participante_ap_${idxUsu}" name="usuarios[${idxUsu}][apellido_paterno]" required class="w-full border border-slate-200 rounded-xl py-2 px-3 text-sm outline-none focus:border-emerald-500 bg-white"></div>
                <div><label class="block text-[10px] uppercase font-black text-emerald-600 mb-1">Ap. Materno</label><input type="text" id="participante_am_${idxUsu}" name="usuarios[${idxUsu}][apellido_materno]" required class="w-full border border-slate-200 rounded-xl py-2 px-3 text-sm outline-none focus:border-emerald-500 bg-white"></div>
                <div><label class="block text-[10px] uppercase font-black text-emerald-600 mb-1">Nombres</label><input type="text" id="participante_nom_${idxUsu}" name="usuarios[${idxUsu}][nombres]" required class="w-full border border-slate-200 rounded-xl py-2 px-3 text-sm outline-none focus:border-emerald-500 bg-white"></div>
                <div><label class="block text-[10px] uppercase font-black text-emerald-600 mb-1">Celular</label><input type="text" name="usuarios[${idxUsu}][celular]" class="w-full border border-slate-200 rounded-xl py-2 px-3 text-sm outline-none focus:border-emerald-500 bg-white"></div>
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
        setTimeout(() => lucide.createIcons(), 10);
    }

    // --- IMPLEMENTADORES ---
    let idxImp = 0;
    function agregarImplementador() {
        let defaultDni = '', defaultApPat = '', defaultApMat = '', defaultNom = '', defaultCargo = 'IMPLEMENTADOR(A)';
        if (idxImp === 0) {
            defaultDni = '{{ Auth::user()->documento ?? Auth::user()->username ?? '' }}';
            defaultApPat = '{{ Auth::user()->apellido_paterno ?? '' }}';
            defaultApMat = '{{ Auth::user()->apellido_materno ?? '' }}';
            defaultNom = '{{ Auth::user()->name ?? '' }}';
        }
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
                                onkeydown="if(event.key === 'Enter'){event.preventDefault(); buscarPersona('implementador', ${idxImp});}" value="${defaultDni}">
                            <div id="loading_implementador_${idxImp}" class="hidden absolute right-2 top-2">
                                <i data-lucide="loader-2" class="w-4 h-4 animate-spin text-purple-600"></i>
                            </div>
                        </div>
                        <button type="button" onclick="buscarPersona('implementador', ${idxImp})" class="bg-purple-600 hover:bg-purple-700 text-white p-2 rounded-xl shadow-sm transition-colors flex-shrink-0">
                            <i data-lucide="search" class="w-4 h-4"></i>
                        </button>
                    </div>
                    <p id="msg_implementador_${idxImp}" class="text-[10px] text-red-500 mt-1 hidden"></p>
                </div>
                <div><label class="block text-[10px] uppercase font-black text-purple-600 mb-1">Ap. Paterno</label><input type="text" id="implementador_ap_${idxImp}" name="implementadores[${idxImp}][apellido_paterno]" required class="w-full border border-slate-200 rounded-xl py-2 px-3 text-sm outline-none focus:border-purple-500 bg-white" value="${defaultApPat}"></div>
                <div><label class="block text-[10px] uppercase font-black text-purple-600 mb-1">Ap. Materno</label><input type="text" id="implementador_am_${idxImp}" name="implementadores[${idxImp}][apellido_materno]" required class="w-full border border-slate-200 rounded-xl py-2 px-3 text-sm outline-none focus:border-purple-500 bg-white" value="${defaultApMat}"></div>
                <div><label class="block text-[10px] uppercase font-black text-purple-600 mb-1">Nombres</label><input type="text" id="implementador_nom_${idxImp}" name="implementadores[${idxImp}][nombres]" required class="w-full border border-slate-200 rounded-xl py-2 px-3 text-sm outline-none focus:border-purple-500 bg-white" value="${defaultNom}"></div>
                <div class="col-span-2"><label class="block text-[10px] uppercase font-black text-purple-600 mb-1">Cargo / Equipo</label><input type="text" name="implementadores[${idxImp}][cargo]" class="w-full border border-slate-200 rounded-xl py-2 px-3 text-sm outline-none focus:border-purple-500 bg-white" placeholder="Ej. Equipo de Implementación MINSA" value="${defaultCargo}"></div>
            </div>
        </div>`;
        document.getElementById('implementadores-container').insertAdjacentHTML('beforeend', tpl);
        idxImp++;
        setTimeout(() => lucide.createIcons(), 10);
    }

    // --- BÚSQUEDA PERSONA (RENIEC / LOCAL) ---
    async function buscarPersona(tipo, index) {
        const docInput = document.getElementById(`${tipo}_dni_${index}`);
        const doc = docInput.value.trim();
        const loader = document.getElementById(`loading_${tipo}_${index}`);
        const msg = document.getElementById(`msg_${tipo}_${index}`);
        if (doc.length < 5) return;
        loader.classList.remove('hidden');
        msg.classList.add('hidden');
        const baseUrl = `{{ route('usuario.monitoreo.citas.buscar.profesional') }}`;
        try {
            const response = await fetch(`${baseUrl}?type=doc&q=${doc}&local_only=1`);
            const data = await response.json();
            if (data.length > 0) {
                rellenarPersona(tipo, index, data[0]);
                msg.textContent = "Persona encontrada.";
                msg.className = `text-[10px] ${tipo == 'participante' ? 'text-emerald-600' : 'text-purple-600'} mt-1 font-bold`;
                msg.classList.remove('hidden');
            } else {
                if (doc.length === 8) {
                    Swal.fire({
                        html: `<div class="p-4 flex flex-col items-center">
                            <div class="relative w-24 h-24 flex items-center justify-center mb-6">
                                <div class="absolute inset-0 border-[6px] border-blue-50 rounded-full animate-ping opacity-75"></div>
                                <div class="absolute inset-3 border-4 border-blue-100 rounded-full animate-pulse"></div>
                                <div class="h-14 w-14 bg-gradient-to-br from-blue-500 to-blue-700 rounded-full flex items-center justify-center shadow-xl shadow-blue-500/50 z-10 relative">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="text-white animate-bounce"><path d="M2 12h20"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                                </div>
                            </div>
                            <h3 class="text-2xl font-black text-blue-900 uppercase tracking-tight mb-2">Conectando RENIEC</h3>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] text-center mb-6 leading-relaxed">Extrayendo nombres oficiales<br>de la plataforma nacional.</p>
                            <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden flex gap-1">
                                <div class="bg-blue-300 h-full w-1/3 rounded-full animate-pulse"></div>
                                <div class="bg-blue-500 h-full w-1/3 rounded-full animate-pulse delay-75"></div>
                                <div class="bg-blue-700 h-full w-1/3 rounded-full animate-pulse delay-150"></div>
                            </div>
                        </div>`,
                        allowOutsideClick: false, showConfirmButton: false,
                        customClass: { popup: 'rounded-[2rem] border-2 border-blue-50 shadow-2xl p-0 bg-white' }
                    });
                    const responseExt = await fetch(`${baseUrl}?type=doc&q=${doc}`);
                    const dataExt = await responseExt.json();
                    Swal.close();
                    if (dataExt.length > 0 && dataExt[0].exists_external) {
                        rellenarPersona(tipo, index, dataExt[0]);
                        msg.textContent = "Extraído de RENIEC.";
                        msg.className = "text-[10px] text-blue-600 mt-1 font-bold";
                        msg.classList.remove('hidden');
                        const Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, timerProgressBar: true });
                        Toast.fire({ icon: 'info', title: 'Nombres encontrados en RENIEC.' });
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
            loader.classList.add('hidden');
        }
    }

    function mostrarMsgNuevoPersona(tipo, index, msg) {
        msg.textContent = "Persona nueva. Complete los datos.";
        msg.className = "text-[10px] text-blue-600 mt-1 font-bold";
        msg.classList.remove('hidden');
        document.getElementById(`${tipo}_ap_${index}`).focus();
    }

    function rellenarPersona(tipo, index, prof) {
        if(prof.apellido_paterno) document.getElementById(`${tipo}_ap_${index}`).value = prof.apellido_paterno;
        if(prof.apellido_materno) document.getElementById(`${tipo}_am_${index}`).value = prof.apellido_materno;
        if(prof.nombres) document.getElementById(`${tipo}_nom_${index}`).value = prof.nombres;
        if (tipo === 'participante') {
            const celularInput = document.querySelector(`input[name="usuarios[${index}][celular]"]`);
            if(celularInput && (prof.celular || prof.telefono)) celularInput.value = prof.celular || prof.telefono;
            const correoInput = document.querySelector(`input[name="usuarios[${index}][correo]"]`);
            if(correoInput && (prof.email || prof.correo)) correoInput.value = prof.email || prof.correo;
        }
    }

    window.onload = () => { agregarParticipante(); agregarImplementador(); }

    // ====== EVIDENCIA FOTOGRÁFICA (CREATE) ======
    function previewFotoCreate(slot, input) {
        if (!input.files || !input.files[0]) return;
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById(slot + '_preview').src = e.target.result;
            document.getElementById(slot + '_preview').classList.remove('hidden');
            document.getElementById(slot + '_placeholder').classList.add('hidden');
            document.getElementById(slot + '_overlay').classList.remove('hidden');
            document.getElementById(slot + '_actions').classList.remove('hidden');
        };
        reader.readAsDataURL(input.files[0]);
    }

    function clearFotoCreate(slot) {
        document.getElementById(slot + '_input').value = '';
        document.getElementById(slot + '_preview').src = '';
        document.getElementById(slot + '_preview').classList.add('hidden');
        document.getElementById(slot + '_placeholder').classList.remove('hidden');
        document.getElementById(slot + '_overlay').classList.add('hidden');
        document.getElementById(slot + '_actions').classList.add('hidden');
    }

    // ====== LÓGICA VISUAL UPSS ======
    let upssIndex = 0;

    async function buscarUpsGlobal(input) {
        const val = input.value.toLowerCase();
        const resultsDiv = document.getElementById('upss_global_results');
        if (val.length < 3) { resultsDiv.classList.add('hidden'); return; }
        try {
            const response = await fetch(`{{ route('usuario.implementacion.ajax.upss') }}?q=${val}`);
            const data = await response.json();
            let html = '';
            data.forEach(item => {
                const codUpss = item.codigo_upss || item.codigo_ups.substring(0,2) + '0000';
                const nombreUpss = item.descripcion_upss || 'UPSS PREDETERMINADA';
                html += `<div class="p-3 border-b border-slate-100 hover:bg-teal-50 cursor-pointer transition-colors"
                     onclick="agregarFilaUpss('${codUpss}', '${nombreUpss}', '${item.codigo_ups}', '${item.descripcion_ups}')">
                    <div class="text-xs font-semibold text-slate-700">${item.codigo_ups} - ${item.descripcion_ups}</div>
                    <div class="text-[10px] text-slate-400 font-bold">${codUpss} - ${nombreUpss}</div>
                </div>`;
            });
            if(html) {
                resultsDiv.innerHTML = html;
                resultsDiv.classList.remove('hidden');
            } else {
                resultsDiv.innerHTML = '<div class="p-3 text-xs text-slate-500 text-center">No se encontraron resultados</div>';
                resultsDiv.classList.remove('hidden');
            }
        } catch(error) { console.error('Error buscando UPSS', error); }
    }

    function agregarFilaUpss(c_upss, n_upss, c_ups, n_ups) {
        const container = document.getElementById('upss-container');
        const div = document.createElement('div');
        div.className = "flex flex-wrap lg:flex-nowrap gap-2 p-3 bg-white border border-slate-200 rounded-xl upss-row items-center";
        div.innerHTML = `
            <input type="text" name="upss_regularizar[${upssIndex}][codigo_upss]" value="${c_upss}" class="w-full lg:w-32 border border-slate-200 bg-slate-50 rounded-lg text-xs p-2 outline-none" placeholder="UPSS" readonly>
            <input type="text" name="upss_regularizar[${upssIndex}][nombre_ups]" value="${c_ups} - ${n_ups}" class="flex-1 border border-slate-200 bg-slate-50 rounded-lg text-xs p-2 outline-none" placeholder="UPS Nombre" readonly>
            <input type="hidden" name="upss_regularizar[${upssIndex}][codigo_ups]" value="${c_ups}">
            <button type="button" onclick="this.closest('.upss-row').remove()" class="bg-red-500 text-white px-3 py-1.5 rounded-lg text-xs font-bold hover:bg-red-600 transition-colors whitespace-nowrap">
                × Eliminar
            </button>`;
        container.appendChild(div);
        upssIndex++;
        document.getElementById('upss-search-input').value = '';
        document.getElementById('upss_global_results').classList.add('hidden');
    }

    function agregarUpssManual() {
        agregarFilaUpss('', '', '', '');
        const container = document.getElementById('upss-container');
        const rows = container.querySelectorAll('.upss-row');
        const lastRow = rows[rows.length - 1];
        lastRow.querySelectorAll('input').forEach(input => input.removeAttribute('readonly'));
    }
</script>
@endpush
