@extends('layouts.usuario')

@section('title', 'Programación por Sectores — ICATEC')

@section('header-content')
    <h1 class="text-xl font-bold text-slate-800 tracking-tight">Mapa de Programación por Sectores</h1>
    <div class="flex items-center gap-2 text-xs text-slate-500 mt-0.5">
        <span>Plataforma</span>
        <span class="text-slate-300">•</span>
        <span>Cronograma General · 07/04/2026 → 23/03/2027</span>
        <span class="text-slate-300">•</span>
        <span class="font-semibold text-indigo-500">PDF vs3 — 23 Sectores · {{ $programacion->count() }} EESS</span>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        .leaflet-container img.leaflet-tile { max-width: none !important; display: inline !important; }
        .leaflet-container img { max-width: none !important; }
        .custom-popup .leaflet-popup-content-wrapper {
            border-radius: 16px; padding: 0; overflow: hidden; box-shadow: 0 20px 60px rgba(0,0,0,0.15);
        }
        .custom-popup .leaflet-popup-content { margin: 0; }
        .custom-popup .leaflet-popup-tip-container { display: none; }

        /* Gantt */
        .gantt-bar { position: absolute; top: 4px; bottom: 4px; border-radius: 4px; min-width: 3px; transition: opacity .15s; cursor: pointer; }
        .gantt-bar:hover { opacity: 0.8; filter: brightness(1.1); }
        .gantt-row { position: relative; height: 28px; }

        /* Modal */
        #modal-edit { display: none; }
        #modal-edit.active { display: flex; }

        .etapa-badge-0 { background:#f1f5f9; color:#64748b; }
        .etapa-badge-1 { background:#dbeafe; color:#1d4ed8; }
        .etapa-badge-2 { background:#fef3c7; color:#b45309; }
        .etapa-badge-3 { background:#ede9fe; color:#6d28d9; }
        .etapa-badge-4 { background:#d1fae5; color:#065f46; font-weight:900; }

        #leyenda-sectores::-webkit-scrollbar { width: 4px; }
        #leyenda-sectores::-webkit-scrollbar-thumb { background:#e2e8f0; border-radius:4px; }

        /* ── MODO FOCO ── */
        #seccion-kpis,
        #seccion-gantt,
        #seccion-tabla { transition: opacity .25s, max-height .35s; overflow: hidden; max-height: 9999px; opacity: 1; }

        body.modo-foco #seccion-kpis,
        body.modo-foco #seccion-gantt,
        body.modo-foco #seccion-tabla { max-height: 0 !important; opacity: 0; pointer-events: none; margin: 0 !important; }

        body.modo-foco #mapa-wrapper { border-radius: 0; position: fixed;
            inset: 0; z-index: 800; margin: 0 !important; height: 100dvh !important;
            box-shadow: none; border: none; }
        body.modo-foco #mapa-wrapper #mapa-sectores { height: 100% !important; }

        body.modo-foco #seccion-filtros { position: fixed; top: 16px; left: 50%; transform: translateX(-50%);
            z-index: 900; background: rgba(255,255,255,0.96); backdrop-filter: blur(12px);
            box-shadow: 0 8px 40px rgba(0,0,0,0.18); border-radius: 20px;
            border: 1px solid rgba(255,255,255,0.9); padding: 14px 20px; max-width: 94vw; width: auto; }

        body.modo-foco #btn-salir-foco { display: flex !important; }
        #btn-salir-foco { display: none; position: fixed; bottom: 24px; right: 24px;
            z-index: 901; background: #1e293b; color: #fff; border: none;
            border-radius: 50px; padding: 10px 20px; font-size: 11px; font-weight: 900;
            cursor: pointer; box-shadow: 0 8px 32px rgba(0,0,0,0.35);
            letter-spacing: .06em; align-items: center; gap: 8px;
            transition: background .15s; }
        #btn-salir-foco:hover { background: #334155; }

        body.modo-foco #page-wrapper { overflow: hidden; }
    </style>
@endpush

@section('content')
<div id="page-wrapper" class="max-w-7xl mx-auto space-y-5">

    {{-- ══ KPIs ══ --}}
    {{-- id para ocultar en modo foco --}}
    <div id="seccion-kpis" class="grid grid-cols-2 md:grid-cols-6 gap-4">
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm col-span-2 md:col-span-1">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Total</span>
                <span class="w-3 h-3 rounded-full bg-indigo-500"></span>
            </div>
            <div id="kpi-total" class="text-3xl font-black text-indigo-700">{{ $programacion->count() }}</div>
            <div class="text-[10px] font-bold text-slate-400 uppercase">EESS Programados</div>
            <div class="mt-2 text-[9px] text-slate-400">
                <span id="kpi-con-est" class="font-bold text-slate-600">—</span> con establecimiento vinculado
            </div>
        </div>
        @php
            $provs = ['ICA'=>['color'=>'indigo','sec'=>'1-10'], 'PISCO'=>['color'=>'violet','sec'=>'11-14'], 'CHINCHA'=>['color'=>'rose','sec'=>'15-18'], 'PALPA'=>['color'=>'amber','sec'=>'19-20'], 'NAZCA'=>['color'=>'emerald','sec'=>'21-23']];
        @endphp
        @foreach($provs as $prov => $cfg)
        <div class="bg-white rounded-2xl border border-{{ $cfg['color'] }}-100 p-5 shadow-sm">
            <div class="text-[9px] font-black text-{{ $cfg['color'] }}-400 uppercase tracking-widest mb-1">{{ ucfirst(strtolower($prov)) }}</div>
            <div id="kpi-{{ strtolower($prov) }}" class="text-2xl font-black text-{{ $cfg['color'] }}-600">{{ $programacion->where('provincia', $prov)->count() }}</div>
            <div class="text-[9px] text-{{ $cfg['color'] }}-300 mt-1">Secs {{ $cfg['sec'] }}</div>
        </div>
        @endforeach
    </div>

    {{-- ══ FILTROS ══ --}}
    <div id="seccion-filtros" class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5">
        <div class="flex flex-wrap items-end gap-3">
            <div class="flex flex-col gap-1">
                <label class="text-[9px] font-black text-slate-400 uppercase">Provincia</label>
                <select id="filtro-provincia" class="text-xs border-slate-200 rounded-xl px-3 py-2 font-bold text-slate-700 bg-slate-50 min-w-[130px]">
                    <option value="">Todas</option>
                    {{-- Poblado por JS desde datos reales de BD --}}
                </select>
            </div>
            <div class="flex flex-col gap-1">
                <label class="text-[9px] font-black text-slate-400 uppercase">Distrito</label>
                <select id="filtro-distrito" class="text-xs border-slate-200 rounded-xl px-3 py-2 font-bold text-slate-700 bg-slate-50 min-w-[160px]">
                    <option value="">Todos</option>
                </select>
            </div>
            <div class="flex flex-col gap-1">
                <label class="text-[9px] font-black text-slate-400 uppercase">Sector</label>
                <select id="filtro-sector" class="text-xs border-slate-200 rounded-xl px-3 py-2 font-bold text-slate-700 bg-slate-50 min-w-[170px]">
                    <option value="">Todos</option>
                </select>
            </div>
            <div class="flex flex-col gap-1">
                <label class="text-[9px] font-black text-slate-400 uppercase">Categoría</label>
                <select id="filtro-categoria" class="text-xs border-slate-200 rounded-xl px-3 py-2 font-bold text-slate-700 bg-slate-50 min-w-[140px]">
                    <option value="">Todas</option>
                </select>
            </div>
            <div class="flex flex-col gap-1">
                <label class="text-[9px] font-black text-slate-400 uppercase">Etapa de Progresión</label>
                <select id="filtro-etapa" class="text-xs border-slate-200 rounded-xl px-3 py-2 font-bold text-slate-700 bg-slate-50">
                    <option value="">Todas</option>
                    <option value="0">Sin Inicio</option>
                    <option value="1">Implementado</option>
                    <option value="2">Con Asistencia</option>
                    <option value="3">Con Monitoreo</option>
                    <option value="4">Ciclo Completo</option>
                </select>
            </div>
            <div class="flex items-center gap-3 ml-auto">
                <div class="bg-indigo-50 border border-indigo-100 rounded-xl px-4 py-2.5 flex items-center gap-2">
                    <span class="text-[9px] font-black text-indigo-400 uppercase">Mostrando</span>
                    <span id="badge-visible" class="text-sm font-black text-indigo-700">{{ $programacion->count() }}</span>
                    <span class="text-[9px] font-black text-indigo-400 uppercase">EESS</span>
                </div>
                {{-- BOTÓN MODO FOCO --}}
                <button id="btn-foco" title="Ver solo mapa"
                    class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-wide bg-slate-900 text-white hover:bg-indigo-600 transition-all shadow-sm">
                    <svg id="icon-expand" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/></svg>
                    <span id="label-foco">Ver solo mapa</span>
                </button>
            </div>
        </div>
    </div>

    {{-- ══ MAPA ══ --}}
    <div id="mapa-wrapper" class="relative rounded-2xl overflow-hidden shadow-xl border border-slate-200" style="height: 560px;">
        <div id="mapa-sectores" class="h-full w-full"></div>

        {{-- LEYENDA SECTORES --}}
        <div class="absolute bottom-5 right-5 z-[1000] bg-white/95 backdrop-blur-md p-4 rounded-2xl shadow-2xl border border-slate-100 min-w-[210px]">
            <h4 class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-3 flex items-center gap-1.5">
                <i data-lucide="map-pin" class="w-3 h-3"></i> Sectores
            </h4>
            <div id="leyenda-sectores" class="space-y-1 max-h-60 overflow-y-auto pr-1"></div>
        </div>

        {{-- LEYENDA ETAPAS --}}
        <div class="absolute bottom-5 left-5 z-[1000] bg-white/95 backdrop-blur-md p-3 rounded-2xl shadow-2xl border border-slate-100">
            <h4 class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-2 flex items-center gap-1">
                <i data-lucide="layers" class="w-3 h-3"></i> Progresión SIHCE
            </h4>
            <div class="space-y-1.5">
                <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-slate-400 flex-shrink-0 border border-white shadow-sm"></span><span class="text-[9px] text-slate-500">Sin Inicio</span></div>
                <div class="flex items-center gap-2"><span class="w-3.5 h-3.5 rounded-full bg-blue-500 flex-shrink-0 border border-white shadow-sm"></span><span class="text-[9px] text-slate-500">Implementado</span></div>
                <div class="flex items-center gap-2"><span class="w-4 h-4 rounded-full bg-amber-500 flex-shrink-0 border border-white shadow-sm"></span><span class="text-[9px] text-slate-500">Con Asistencia</span></div>
                <div class="flex items-center gap-2"><span class="w-5 h-5 rounded-full bg-violet-500 flex-shrink-0 border border-white shadow-sm"></span><span class="text-[9px] text-slate-500">Con Monitoreo</span></div>
                <div class="flex items-center gap-2"><span class="w-5 h-5 rounded-full bg-emerald-500 flex-shrink-0 border border-white shadow-sm animate-pulse"></span><span class="text-[9px] text-slate-500">Ciclo Completo</span></div>
            </div>
        </div>

        {{-- Badge top --}}
        <div class="absolute top-4 left-1/2 -translate-x-1/2 z-[1000] bg-white/95 backdrop-blur-md px-4 py-2.5 rounded-xl shadow-lg border border-slate-100">
            <span class="text-[9px] font-black text-slate-400 uppercase">Programación de Visitas — vs3 · 07/04/2026 → 23/03/2027</span>
        </div>
    </div>

    {{-- ══ CRONOGRAMA GANTT ══ --}}
    <div id="seccion-gantt" class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center gap-2">
            <i data-lucide="calendar-range" class="w-4 h-4 text-indigo-400"></i>
            <h3 class="text-[11px] font-black text-slate-600 uppercase tracking-widest">Cronograma por Sector</h3>
            <span class="text-[9px] text-slate-400 ml-auto font-semibold">07/04/2026 → 23/03/2027</span>
        </div>
        <div class="px-6 py-4 overflow-x-auto">
            {{-- Cabecera de meses --}}
            <div class="mb-1 relative" style="padding-left: 120px;">
                <div id="gantt-months" class="flex text-[8px] font-black text-slate-400 uppercase tracking-wider"></div>
            </div>
            {{-- Filas de sectores --}}
            <div id="gantt-rows" class="space-y-0.5"></div>
        </div>
    </div>

    {{-- ══ TABLA DE DETALLE ══ --}}
    <div id="seccion-tabla" class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i data-lucide="table-2" class="w-4 h-4 text-slate-400"></i>
                <h3 class="text-[11px] font-black text-slate-600 uppercase tracking-widest">Detalle por EESS</h3>
            </div>
            <span id="tabla-info" class="text-[9px] text-slate-400 font-semibold bg-slate-50 px-3 py-1.5 rounded-lg border border-slate-100"></span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100">
                        <th class="text-left px-3 py-3 text-[9px] font-black text-slate-400 uppercase tracking-widest w-6">#</th>
                        <th class="text-left px-3 py-3 text-[9px] font-black text-slate-400 uppercase tracking-widest">Establecimiento</th>
                        <th class="text-left px-3 py-3 text-[9px] font-black text-slate-400 uppercase tracking-widest">Provincia</th>
                        <th class="text-left px-3 py-3 text-[9px] font-black text-slate-400 uppercase tracking-widest">Sector</th>
                        <th class="text-left px-3 py-3 text-[9px] font-black text-slate-400 uppercase tracking-widest">Cuad.</th>
                        <th class="text-left px-3 py-3 text-[9px] font-black text-slate-400 uppercase tracking-widest">Comienzo</th>
                        <th class="text-left px-3 py-3 text-[9px] font-black text-slate-400 uppercase tracking-widest">Fin</th>
                        <th class="text-right px-3 py-3 text-[9px] font-black text-slate-400 uppercase tracking-widest">Días</th>
                        <th class="text-center px-3 py-3 text-[9px] font-black text-slate-400 uppercase tracking-widest">Progresión</th>
                        <th class="text-center px-3 py-3 text-[9px] font-black text-slate-400 uppercase tracking-widest">Acción</th>
                    </tr>
                </thead>
                <tbody id="tabla-body" class="divide-y divide-slate-50"></tbody>
            </table>
        </div>
    </div>

</div>

{{-- Botón flotante para salir del modo foco --}}
<button id="btn-salir-foco" aria-label="Salir del modo foco">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
    Salir del modo mapa
</button>

{{-- ══ MODAL EDICIÓN DE SECTOR ══ --}}
<div id="modal-edit" class="fixed inset-0 z-[9999] items-center justify-center bg-slate-900/50 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl border border-slate-100 w-full max-w-sm mx-4 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="font-black text-slate-800 text-sm">Editar Sector</h3>
                <p id="modal-nombre" class="text-[10px] text-slate-400 mt-0.5"></p>
            </div>
            <button id="modal-close" class="text-slate-400 hover:text-slate-600 transition-colors">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <form id="modal-form" class="px-6 py-5 space-y-4">
            @csrf
            <div>
                <label class="text-[10px] font-black text-slate-500 uppercase tracking-wider block mb-1">Sector (1 — 23)</label>
                <select id="modal-sector" name="sector" class="w-full text-sm border border-slate-200 rounded-xl px-3 py-2.5 font-bold text-slate-700 focus:ring-indigo-500 focus:border-indigo-400">
                    @for($s = 1; $s <= 23; $s++)
                        <option value="{{ $s }}">Sector {{ $s }}</option>
                    @endfor
                </select>
            </div>
            <div>
                <label class="text-[10px] font-black text-slate-500 uppercase tracking-wider block mb-1">Cuadrilla</label>
                <input id="modal-cuadril" name="cuadril" type="text" maxlength="15"
                    class="w-full text-sm border border-slate-200 rounded-xl px-3 py-2.5 font-bold text-slate-700 focus:ring-indigo-500 focus:border-indigo-400"
                    placeholder="Ej. C1, C2...">
            </div>
            <div class="flex gap-3 pt-1">
                <button type="button" id="modal-cancel" class="flex-1 px-4 py-2.5 text-sm font-bold text-slate-600 bg-slate-100 rounded-xl hover:bg-slate-200 transition-colors">
                    Cancelar
                </button>
                <button type="submit" class="flex-1 px-4 py-2.5 text-sm font-black text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 transition-colors">
                    Guardar
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
(function () {

    /* ══════════════════════════════════════════════════════
       DATOS DESDE SERVIDOR
    ══════════════════════════════════════════════════════ */
    var programacion = @json($programacion);
    var updateUrl    = "{{ route('usuario.dashboard.programacion.sectores.update', ['id' => '__ID__']) }}";
    var csrfToken    = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    /* ── Paleta de colores por sector ── */
    var sectorColors = {
        1:'#818cf8', 2:'#6366f1', 3:'#4f46e5', 4:'#4338ca', 5:'#3730a3',
        6:'#312e81', 7:'#6366f1', 8:'#4f46e5', 9:'#818cf8', 10:'#6366f1',
        11:'#a78bfa',12:'#8b5cf6',13:'#7c3aed',14:'#6d28d9',
        15:'#f472b6',16:'#ec4899',17:'#db2777',18:'#be185d',
        19:'#fbbf24',20:'#f59e0b',
        21:'#34d399',22:'#10b981',23:'#059669'
    };

    /* ── Configuración etapas (borde del marcador) ── */
    var etapaConfig = {
        0: { color:'#94a3b8', size:7,  label:'Sin Inicio' },
        1: { color:'#3b82f6', size:10, label:'Implementado' },
        2: { color:'#f59e0b', size:13, label:'Con Asistencia' },
        3: { color:'#8b5cf6', size:16, label:'Con Monitoreo' },
        4: { color:'#22c55e', size:19, label:'Ciclo Completo' },
    };

    /* ── KPI con establecimiento vinculado ── */
    var conEst = programacion.filter(function(p){ return p.tiene_est; }).length;
    document.getElementById('kpi-con-est').textContent = conEst;

    /* ══════════════════════════════════════════════════════
       POPUP
    ══════════════════════════════════════════════════════ */
    function check(ok, label) {
        return '<div class="flex items-center gap-2"><span class="w-4 h-4 rounded-full flex items-center justify-center flex-shrink-0 '
            + (ok ? 'bg-emerald-100 text-emerald-600' : 'bg-slate-100 text-slate-300')
            + '">' + (ok ? '✓' : '○') + '</span><span class="text-[10px] font-semibold '
            + (ok ? 'text-slate-700' : 'text-slate-400') + '">' + label + '</span></div>';
    }

    function buildPopup(p) {
        var secColor = sectorColors[p.sector] || '#6366f1';
        var etCfg    = etapaConfig[p.etapa];
        var etLabels = {0:'bg-slate-100 text-slate-600',1:'bg-blue-100 text-blue-700',2:'bg-amber-100 text-amber-700',3:'bg-violet-100 text-violet-700',4:'bg-emerald-100 text-emerald-700'};

        var modulosHtml = '';
        if (p.modulos_impl && p.modulos_impl.length > 0) {
            modulosHtml = '<div class="mt-1 flex flex-wrap gap-1">'
                + p.modulos_impl.map(function(m){ return '<span class="text-[8px] font-bold bg-blue-50 text-blue-600 px-1.5 py-0.5 rounded-full border border-blue-100">' + m + '</span>'; }).join('')
                + '</div>';
        }

        var sinEst = !p.tiene_est
            ? '<div class="px-4 pb-2"><p class="text-[9px] text-amber-500 bg-amber-50 px-2 py-1 rounded-lg">⚠ Sin coincidencia exacta en BD</p></div>'
            : '';

        // Aviso si la provincia de la BD difiere de la del PDF
        var provMismatch = '';
        if (p.tiene_est && p.provincia_pdf && p.provincia !== p.provincia_pdf) {
            provMismatch = '<div class="px-4 pb-2"><p class="text-[9px] text-indigo-500 bg-indigo-50 px-2 py-1 rounded-lg">🗘 PDF: ' + p.provincia_pdf + ' → BD: ' + p.provincia + '</p></div>';
        }

        return '<div class="bg-white min-w-[260px] max-w-[300px]">'
            + '<div class="px-4 pt-4 pb-3 border-b border-slate-100">'
            +   '<div class="flex items-center gap-2 mb-1">'
            +     '<span class="w-6 h-6 rounded-full text-white text-[9px] font-black flex items-center justify-center flex-shrink-0" style="background:' + secColor + '">S' + p.sector + '</span>'
            +     '<h4 class="font-black text-slate-800 text-[12px] leading-tight">' + p.nombre + '</h4>'
            +   '</div>'
            +   '<p class="text-[10px] text-slate-400 uppercase tracking-wider">' + (p.distrito||'') + ' — ' + p.provincia + '</p>'
            +   '<div class="flex gap-1.5 mt-1.5 flex-wrap">'
            +     '<span class="text-[9px] font-black px-2 py-0.5 rounded-full text-white" style="background:' + secColor + '">' + p.cuadril + '</span>'
            +     '<span class="text-[9px] font-black px-2 py-0.5 rounded-full ' + etLabels[p.etapa] + '">' + etCfg.label + '</span>'
            +   '</div>'
            + '</div>'
            + '<div class="px-4 py-3 space-y-1.5">'
            +   check(p.tiene_impl, 'Implementación' + (p.total_impl > 0 ? ' (' + p.total_impl + ' mód.)' : ''))
            +   modulosHtml
            +   check(p.tiene_asist, 'Asistencia Técnica' + (p.total_asistencias > 0 ? ' (' + p.total_asistencias + ' actas)' : ''))
            +   check(p.tiene_monitoreo, 'Monitoreo' + (p.total_monitoreos > 0 ? ' (' + p.total_monitoreos + ' actas)' : ''))
            + '</div>'
            + '<div class="px-4 pb-3 space-y-1 border-t border-slate-100 pt-2">'
            +   '<div class="flex justify-between text-[10px]"><span class="text-slate-500 font-semibold">Comienzo</span><span class="font-black text-slate-700">' + (p.comienzo||'—') + '</span></div>'
            +   '<div class="flex justify-between text-[10px]"><span class="text-slate-500 font-semibold">Fin</span><span class="font-black text-slate-700">' + (p.fin||'—') + '</span></div>'
            +   '<div class="flex justify-between text-[10px]"><span class="text-slate-500 font-semibold">Duración</span><span class="font-black text-slate-700">' + (p.dias||'—') + ' días</span></div>'
            + '</div>'
            + sinEst
            + provMismatch
            + '</div>';
    }

    /* ══════════════════════════════════════════════════════
       MAPA LEAFLET
    ══════════════════════════════════════════════════════ */
    var map = L.map('mapa-sectores').setView([-14.07, -75.73], 8);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>', maxZoom: 19
    }).addTo(map);

    var markersList = [];
    programacion.forEach(function(p) {
        if (!p.lat || !p.lon || isNaN(p.lat)) return;
        var sc  = sectorColors[p.sector] || '#6366f1';
        var etc = etapaConfig[p.etapa];
        var m = L.circleMarker([p.lat, p.lon], {
            radius:      etc.size / 2 + 4,
            fillColor:   sc,
            color:       etc.color,
            weight:      p.etapa === 4 ? 3 : 2,
            opacity:     1,
            fillOpacity: 0.85,
        }).addTo(map).bindPopup(buildPopup(p), { className: 'custom-popup', maxWidth: 320 });

        markersList.push({ marker: m, item: p });
    });

    /* ══════════════════════════════════════════════════════
       LEYENDA SECTORES
    ══════════════════════════════════════════════════════ */
    var sectoresUnicos = [...new Set(programacion.map(function(p){ return p.sector; }))].sort(function(a,b){return a-b;});

    function buildLeyenda(visibles) {
        var cont = document.getElementById('leyenda-sectores');
        cont.innerHTML = '';
        var secs = visibles || sectoresUnicos;
        secs.forEach(function(s) {
            var color = sectorColors[s] || '#6366f1';
            var prov  = (programacion.find(function(p){ return p.sector === s; }) || {}).provincia || '';
            var cnt   = markersList.filter(function(m){ return m.item.sector === s; }).length;
            var d = document.createElement('div');
            d.className = 'flex items-center gap-2 cursor-pointer hover:opacity-70 transition-opacity py-0.5';
            d.innerHTML = '<span class="w-3 h-3 rounded-full flex-shrink-0 border-2 border-white shadow-sm" style="background:' + color + '"></span>'
                + '<div><p class="text-[10px] font-bold text-slate-700 leading-none">Sector ' + s + '</p><p class="text-[8px] text-slate-400">' + prov + ' · ' + cnt + ' EESS</p></div>';
            d.addEventListener('click', function() {
                document.getElementById('filtro-sector').value = s;
                applyFilters();
            });
            cont.appendChild(d);
        });
    }

    /* Poblar provincias desde datos reales de la BD */
    function poblarProvincias() {
        var sel = document.getElementById('filtro-provincia');
        sel.innerHTML = '<option value="">Todas</option>';
        var provs = [...new Set(
            programacion.filter(function(p){ return p.provincia; }).map(function(p){ return p.provincia; })
        )].sort();
        provs.forEach(function(pv) {
            var cnt = programacion.filter(function(p){ return p.provincia === pv; }).length;
            var opt = document.createElement('option');
            opt.value = pv;
            opt.textContent = pv.charAt(0) + pv.slice(1).toLowerCase() + ' (' + cnt + ')';
            sel.appendChild(opt);
        });
    }
    poblarProvincias();

    function poblarSelectSectores(filtProv) {
        var sel = document.getElementById('filtro-sector');
        sel.innerHTML = '<option value="">Todos</option>';
        var secs = filtProv
            ? [...new Set(programacion.filter(function(p){return p.provincia===filtProv;}).map(function(p){return p.sector;}))]
            : sectoresUnicos;
        secs.sort(function(a,b){return a-b;}).forEach(function(s) {
            var item = programacion.find(function(p){return p.sector===s;});
            var opt  = document.createElement('option');
            opt.value = s;
            opt.textContent = 'Sector ' + s + (item ? ' — ' + item.provincia : '');
            sel.appendChild(opt);
        });
    }
    poblarSelectSectores(null);

    /* Poblar distritos (en cascada con provincia) */
    function poblarDistritos(filtProv) {
        var sel = document.getElementById('filtro-distrito');
        var prevVal = sel.value;
        sel.innerHTML = '<option value="">Todos</option>';
        var source = filtProv
            ? programacion.filter(function(p){ return p.provincia === filtProv; })
            : programacion;
        var distritos = [...new Set(
            source.filter(function(p){ return p.distrito; }).map(function(p){ return p.distrito; })
        )].sort();
        distritos.forEach(function(d) {
            var opt = document.createElement('option');
            opt.value = d; opt.textContent = d;
            sel.appendChild(opt);
        });
        sel.value = distritos.includes(prevVal) ? prevVal : '';
    }
    poblarDistritos(null);

    /* Poblar categorías */
    function poblarCategorias() {
        var sel = document.getElementById('filtro-categoria');
        var prevVal = sel.value;
        sel.innerHTML = '<option value="">Todas</option>';
        var categorias = [...new Set(
            programacion.filter(function(p){ return p.categoria; }).map(function(p){ return p.categoria; })
        )].sort();
        categorias.forEach(function(c) {
            var opt = document.createElement('option');
            opt.value = c; opt.textContent = c;
            sel.appendChild(opt);
        });
        sel.value = categorias.includes(prevVal) ? prevVal : '';
    }
    poblarCategorias();

    /* ══════════════════════════════════════════════════════
       TABLA
    ══════════════════════════════════════════════════════ */
    var etapaLabels = {0:'Sin Inicio',1:'Implementado',2:'Con Asistencia',3:'Con Monitoreo',4:'Ciclo Completo'};
    var etapaBadge  = {0:'etapa-badge-0',1:'etapa-badge-1',2:'etapa-badge-2',3:'etapa-badge-3',4:'etapa-badge-4'};

    function updateTabla(filtered) {
        var tbody = document.getElementById('tabla-body');
        tbody.innerHTML = '';
        document.getElementById('tabla-info').textContent = filtered.length + ' registros';

        filtered.forEach(function(mObj, idx) {
            var p     = mObj.item;
            var color = sectorColors[p.sector] || '#6366f1';
            var tr    = document.createElement('tr');
            tr.className = 'hover:bg-slate-50 transition-colors';
            tr.innerHTML =
                '<td class="px-3 py-2.5 text-slate-400 font-bold">' + (idx+1) + '</td>'
                + '<td class="px-3 py-2.5">'
                +   '<span class="font-semibold text-slate-700">' + p.nombre + '</span>'
                +   (p.nombre !== p.nombre_pdf ? '<br><span class="text-[8px] text-slate-400">PDF: ' + p.nombre_pdf + '</span>' : '')
                +   (!p.tiene_est ? '<span class="ml-1 text-[8px] text-amber-500 font-bold bg-amber-50 px-1.5 py-0.5 rounded">Sin coord.</span>' : '')
                + '</td>'
                + '<td class="px-3 py-2.5 text-slate-500">' + p.provincia + '</td>'
                + '<td class="px-3 py-2.5" data-sector-cell="' + p.id + '">'
                +   '<span class="inline-flex items-center text-[10px] font-black px-2 py-0.5 rounded-full text-white" style="background:' + color + '">Sec ' + p.sector + '</span>'
                + '</td>'
                + '<td class="px-3 py-2.5 font-mono text-slate-500" data-cuadril-cell="' + p.id + '">' + (p.cuadril||'—') + '</td>'
                + '<td class="px-3 py-2.5 text-slate-600">' + (p.comienzo||'—') + '</td>'
                + '<td class="px-3 py-2.5 text-slate-600">' + (p.fin||'—') + '</td>'
                + '<td class="px-3 py-2.5 text-right text-slate-500">' + (p.dias||'—') + '</td>'
                + '<td class="px-3 py-2.5 text-center"><span class="text-[9px] font-black px-2 py-0.5 rounded-full ' + etapaBadge[p.etapa] + '">' + etapaLabels[p.etapa] + '</span></td>'
                + '<td class="px-3 py-2.5 text-center">'
                +   '<button class="btn-editar p-1.5 rounded-lg hover:bg-indigo-50 text-slate-400 hover:text-indigo-600 transition-colors" data-id="' + p.id + '" data-nombre="' + p.nombre + '" data-sector="' + p.sector + '" data-cuadril="' + (p.cuadril||'') + '" title="Editar sector">'
                +     '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>'
                +   '</button>'
                + '</td>';

            // Click en fila → zoom al marcador
            tr.addEventListener('click', function(e) {
                if (e.target.closest('.btn-editar')) return;
                if (p.lat && !isNaN(p.lat)) {
                    map.setView([p.lat, p.lon], 14);
                    mObj.marker.openPopup();
                }
            });
            tbody.appendChild(tr);
        });

        // Asignar eventos al botón editar
        tbody.querySelectorAll('.btn-editar').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                openModal(btn.dataset.id, btn.dataset.nombre, btn.dataset.sector, btn.dataset.cuadril);
            });
        });
    }

    /* ══════════════════════════════════════════════════════
       FILTROS
    ══════════════════════════════════════════════════════ */
    var filtroProv = '', filtroSec = '', filtroEtapa = '', filtroDistrito = '', filtroCategoria = '';

    function applyFilters() {
        var group    = L.featureGroup();
        var filtered = [];

        markersList.forEach(function(mObj) {
            var p = mObj.item;
            var ok = (filtroProv      === '' || p.provincia === filtroProv)
                  && (filtroSec       === '' || String(p.sector) === String(filtroSec))
                  && (filtroEtapa     === '' || String(p.etapa)  === String(filtroEtapa))
                  && (filtroDistrito  === '' || (p.distrito  || '') === filtroDistrito)
                  && (filtroCategoria === '' || (p.categoria || '') === filtroCategoria);
            if (ok) {
                if (!map.hasLayer(mObj.marker)) map.addLayer(mObj.marker);
                group.addLayer(mObj.marker);
                filtered.push(mObj);
            } else {
                if (map.hasLayer(mObj.marker)) map.removeLayer(mObj.marker);
            }
        });

        document.getElementById('badge-visible').textContent = filtered.length;
        updateTabla(filtered);

        var secsVis = [...new Set(filtered.map(function(m){ return m.item.sector; }))].sort(function(a,b){return a-b;});
        buildLeyenda(secsVis.length ? secsVis : null);

        if (group.getLayers().length > 0 && (filtroProv || filtroSec || filtroEtapa || filtroDistrito || filtroCategoria)) {
            map.fitBounds(group.getBounds(), { padding:[50,50], maxZoom:13 });
        }
    }

    document.getElementById('filtro-provincia').addEventListener('change', function() {
        filtroProv = this.value; filtroSec = ''; filtroDistrito = '';
        poblarSelectSectores(filtroProv || null);
        poblarDistritos(filtroProv || null);
        applyFilters();
    });
    document.getElementById('filtro-distrito').addEventListener('change', function() {
        filtroDistrito = this.value; applyFilters();
    });
    document.getElementById('filtro-sector').addEventListener('change', function() {
        filtroSec = this.value; applyFilters();
    });
    document.getElementById('filtro-categoria').addEventListener('change', function() {
        filtroCategoria = this.value; applyFilters();
    });
    document.getElementById('filtro-etapa').addEventListener('change', function() {
        filtroEtapa = this.value; applyFilters();
    });

    /* ══════════════════════════════════════════════════════
       GANTT
    ══════════════════════════════════════════════════════ */
    (function buildGantt() {
        var REF_START = new Date('2026-04-07');
        var REF_END   = new Date('2027-03-23');
        var TOTAL_MS  = REF_END - REF_START;

        // Cabecera de meses
        var monthsCont = document.getElementById('gantt-months');
        var months = ['Abr 26','May 26','Jun 26','Jul 26','Ago 26','Sep 26','Oct 26','Nov 26','Dic 26','Ene 27','Feb 27','Mar 27'];
        var monthDates = [
            new Date('2026-04-01'), new Date('2026-05-01'), new Date('2026-06-01'),
            new Date('2026-07-01'), new Date('2026-08-01'), new Date('2026-09-01'),
            new Date('2026-10-01'), new Date('2026-11-01'), new Date('2026-12-01'),
            new Date('2027-01-01'), new Date('2027-02-01'), new Date('2027-03-01'),
        ];
        var barW = 900;
        monthDates.forEach(function(d, i) {
            var left = Math.max(0, (d - REF_START) / TOTAL_MS * 100);
            var span = document.createElement('span');
            span.style.position    = 'absolute';
            span.style.left        = left + '%';
            span.style.whiteSpace  = 'nowrap';
            span.textContent       = months[i];
            monthsCont.appendChild(span);
        });
        monthsCont.style.position = 'relative';
        monthsCont.style.height   = '16px';

        // Agrupar por sector
        var sectoresProg = {};
        programacion.forEach(function(p) {
            if (!p.comienzo_iso || !p.fin_iso) return;
            if (!sectoresProg[p.sector]) {
                sectoresProg[p.sector] = { sector:p.sector, provincia:p.provincia, items:[] };
            }
            sectoresProg[p.sector].items.push(p);
        });

        var rowsCont = document.getElementById('gantt-rows');
        Object.keys(sectoresProg).sort(function(a,b){return a-b;}).forEach(function(sKey) {
            var sg    = sectoresProg[sKey];
            var color = sectorColors[sg.sector] || '#6366f1';

            // Calcular rango del sector
            var comMin = sg.items.reduce(function(mn,p){ return !mn||p.comienzo_iso<mn?p.comienzo_iso:mn;},null);
            var finMax = sg.items.reduce(function(mx,p){ return !mx||p.fin_iso>mx?p.fin_iso:mx;},null);

            var row = document.createElement('div');
            row.className = 'flex items-center gap-2 mb-0.5';

            var label = document.createElement('div');
            label.style.width     = '116px';
            label.style.flexShrink = '0';
            label.innerHTML = '<span class="text-[9px] font-black text-slate-600">S' + sg.sector + '</span>'
                            + '<span class="text-[8px] text-slate-400 ml-1">' + sg.provincia.slice(0,3) + '</span>'
                            + '<span class="text-[8px] text-slate-300 ml-1">(' + sg.items.length + ')</span>';

            var track = document.createElement('div');
            track.style.flex        = '1';
            track.style.position    = 'relative';
            track.style.height      = '20px';
            track.style.background  = '#f8fafc';
            track.style.borderRadius = '4px';
            track.style.overflow    = 'hidden';

            // Barra del rango del sector
            if (comMin && finMax) {
                var leftPct  = (new Date(comMin) - REF_START) / TOTAL_MS * 100;
                var rightPct = (new Date(finMax)  - REF_START) / TOTAL_MS * 100;
                var bar = document.createElement('div');
                bar.className = 'gantt-bar';
                bar.style.left       = Math.max(0, leftPct) + '%';
                bar.style.width      = Math.min(100, rightPct - leftPct) + '%';
                bar.style.background = color;
                bar.style.opacity    = '0.75';
                bar.title            = 'Sector ' + sg.sector + ': ' + comMin + ' → ' + finMax;
                track.appendChild(bar);
            }

            row.appendChild(label);
            row.appendChild(track);
            rowsCont.appendChild(row);
        });
    })();

    /* ══════════════════════════════════════════════════════
       MODAL DE EDICIÓN
    ══════════════════════════════════════════════════════ */
    var editingId = null;

    function openModal(id, nombre, sector, cuadril) {
        editingId = id;
        document.getElementById('modal-nombre').textContent  = nombre;
        document.getElementById('modal-sector').value        = sector;
        document.getElementById('modal-cuadril').value       = cuadril || '';
        document.getElementById('modal-edit').classList.add('active');
    }

    function closeModal() {
        document.getElementById('modal-edit').classList.remove('active');
        editingId = null;
    }

    document.getElementById('modal-close').addEventListener('click',  closeModal);
    document.getElementById('modal-cancel').addEventListener('click', closeModal);
    document.getElementById('modal-edit').addEventListener('click', function(e) {
        if (e.target === this) closeModal();
    });

    document.getElementById('modal-form').addEventListener('submit', function(e) {
        e.preventDefault();
        if (!editingId) return;

        var sector  = document.getElementById('modal-sector').value;
        var cuadril = document.getElementById('modal-cuadril').value;
        var url     = updateUrl.replace('__ID__', editingId);

        fetch(url, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ sector: parseInt(sector), cuadril: cuadril })
        })
        .then(function(r){ return r.json(); })
        .then(function(data) {
            if (!data.success) throw new Error('Error al guardar');

            // Actualizar en memoria
            var prog = programacion.find(function(p){ return String(p.id) === String(editingId); });
            if (prog) {
                prog.sector  = data.sector;
                prog.cuadril = data.cuadril;
            }

            // Actualizar celda de la tabla sin recargar
            var secCell = document.querySelector('[data-sector-cell="' + editingId + '"]');
            var cuaCell = document.querySelector('[data-cuadril-cell="' + editingId + '"]');
            if (secCell) {
                var color = sectorColors[data.sector] || '#6366f1';
                secCell.innerHTML = '<span class="inline-flex items-center text-[10px] font-black px-2 py-0.5 rounded-full text-white" style="background:' + color + '">Sec ' + data.sector + '</span>';
            }
            if (cuaCell) cuaCell.textContent = data.cuadril || '—';

            closeModal();
            lucide.createIcons();
        })
        .catch(function() {
            alert('No se pudo guardar. Intente de nuevo.');
        });
    });

    /* ══════════════════════════════════════════════════════
       MODO FOCO
    ══════════════════════════════════════════════════════ */
    var enModoFoco = false;

    function toggleFoco() {
        enModoFoco = !enModoFoco;
        document.body.classList.toggle('modo-foco', enModoFoco);

        var labelBtn  = document.getElementById('label-foco');
        var iconExp   = document.getElementById('icon-expand');
        var btnFoco   = document.getElementById('btn-foco');

        if (enModoFoco) {
            labelBtn.textContent = 'Vista normal';
            btnFoco.style.background = '#4f46e5';
            iconExp.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 9L4 4m0 0h4m-4 0v4m15-4l-5 5m5-5h-4m4 0v4M9 15l-5 5m5-5H5m4 0v4m11-4l-5-5m5 5h-4m4 0v-4"/>';
        } else {
            labelBtn.textContent = 'Ver solo mapa';
            btnFoco.style.background = '';
            iconExp.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/>';
        }

        // Forzar que Leaflet recalcule el tamaño después de la animación
        setTimeout(function() { map.invalidateSize(); }, 400);
    }

    document.getElementById('btn-foco').addEventListener('click', toggleFoco);
    document.getElementById('btn-salir-foco').addEventListener('click', toggleFoco);

    // Salir con ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && enModoFoco) toggleFoco();
    });

    /* ══════════════════════════════════════════════════════
       INICIALIZACIÓN
    ══════════════════════════════════════════════════════ */
    applyFilters();

    // Zoom automático a los marcadores al cargar
    var allGroup = L.featureGroup(markersList.map(function(m){ return m.marker; }));
    if (allGroup.getLayers().length > 0) {
        map.fitBounds(allGroup.getBounds(), { padding: [40, 40], maxZoom: 10 });
    }

})();
</script>
@endpush
