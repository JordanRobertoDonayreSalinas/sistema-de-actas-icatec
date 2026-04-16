ñ@extends('layouts.usuario')

@section('title', 'Sectorización Propuesta — ICATEC')

@section('header-content')
    <h1 class="text-xl font-bold text-slate-800 tracking-tight">Mapa de Programación por Sectorización Propuesta</h1>
    <div class="flex items-center gap-2 text-xs text-slate-500 mt-0.5">
        <span>Dashboard</span>
        <span class="text-slate-300">•</span>
        <span class="font-semibold text-amber-600 bg-amber-50 px-2 py-0.5 rounded-md border border-amber-100">Borrador
            Independiente</span>
        <span class="text-slate-300">•</span>
        <span class="font-semibold text-indigo-500">{{ $programacion->count() }} EESS vinculados</span>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        .leaflet-container img.leaflet-tile {
            max-width: none !important;
            display: inline !important;
        }

        .leaflet-container img {
            max-width: none !important;
        }

        .custom-popup .leaflet-popup-content-wrapper {
            border-radius: 16px;
            padding: 0;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
        }

        .custom-popup .leaflet-popup-content {
            margin: 0;
        }

        .custom-popup .leaflet-popup-tip-container {
            display: none;
        }

        /* Gantt */
        .gantt-bar {
            position: absolute;
            top: 4px;
            bottom: 4px;
            border-radius: 4px;
            min-width: 3px;
            transition: opacity .15s;
            cursor: pointer;
        }

        .gantt-bar:hover {
            opacity: 0.8;
            filter: brightness(1.1);
        }

        .gantt-row {
            position: relative;
            height: 28px;
        }

        /* Modal */
        #modal-edit {
            display: none;
        }

        #modal-edit.active {
            display: flex;
        }

        .etapa-badge-0 {
            background: #f1f5f9;
            color: #64748b;
        }

        .etapa-badge-1 {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .etapa-badge-2 {
            background: #fef3c7;
            color: #b45309;
        }

        .etapa-badge-3 {
            background: #ede9fe;
            color: #6d28d9;
        }

        .etapa-badge-4 {
            background: #d1fae5;
            color: #065f46;
            font-weight: 900;
        }

        #leyenda-sectores::-webkit-scrollbar {
            width: 4px;
        }

        #leyenda-sectores::-webkit-scrollbar-thumb {
            background: #e2e8f0;
            border-radius: 4px;
        }

        /* ── MODO FOCO ── */
        #seccion-kpis,
        #seccion-gantt,
        #seccion-tabla {
            transition: opacity .25s, max-height .35s;
            overflow: hidden;
            max-height: 9999px;
            opacity: 1;
        }

        body.modo-foco #seccion-kpis,
        body.modo-foco #seccion-gantt,
        body.modo-foco #seccion-tabla {
            max-height: 0 !important;
            opacity: 0;
            pointer-events: none;
            margin: 0 !important;
        }

        body.modo-foco #mapa-wrapper {
            border-radius: 0;
            position: fixed;
            inset: 0;
            z-index: 800;
            margin: 0 !important;
            height: 100dvh !important;
            box-shadow: none;
            border: none;
        }

        body.modo-foco #mapa-wrapper #mapa-sectores {
            height: 100% !important;
        }

        body.modo-foco #seccion-filtros {
            position: fixed;
            top: 16px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 900;
            background: rgba(255, 255, 255, 0.96);
            backdrop-filter: blur(12px);
            box-shadow: 0 8px 40px rgba(0, 0, 0, 0.18);
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.9);
            padding: 14px 20px;
            max-width: 94vw;
            width: auto;
        }

        body.modo-foco #btn-salir-foco {
            display: flex !important;
        }

        #btn-salir-foco {
            display: none;
            position: fixed;
            top: 28px;
            right: 24px;
            z-index: 901;
            background: #1e293b;
            color: #fff;
            border: none;
            border-radius: 50px;
            padding: 10px 20px;
            font-size: 11px;
            font-weight: 900;
            cursor: pointer;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.35);
            letter-spacing: .06em;
            align-items: center;
            gap: 8px;
            transition: background .15s;
        }

        #btn-salir-foco:hover {
            background: #334155;
        }

        body.modo-foco #page-wrapper {
            overflow: hidden;
        }
    </style>
@endpush

@section('content')
    <div id="page-wrapper" class="max-w-7xl mx-auto space-y-5">

        {{-- ALERT DE INFORMACIÓN --}}
        <div class="bg-indigo-600 rounded-2xl p-4 shadow-lg flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="bg-white/20 p-2.5 rounded-xl text-white">
                    <i data-lucide="info" class="w-6 h-6"></i>
                </div>
                <div>
                    <h4 class="text-white font-black text-sm">Modo de Simulación Activo</h4>
                    <p class="text-indigo-100 text-[11px]">Cualquier cambio de sector aquí <b>no afecta</b> al cronograma
                        oficial principal.</p>
                </div>
            </div>
            <div class="hidden md:block">
                <span
                    class="text-[10px] bg-white/10 text-white px-3 py-1.5 rounded-lg border border-white/20 font-bold uppercase tracking-widest">Entorno
                    Seguro</span>
            </div>
        </div>

        {{-- ══ KPIs ══ --}}
        <div id="seccion-kpis" class="grid grid-cols-2 md:grid-cols-6 gap-4">
            <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm col-span-2 md:col-span-1">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Total</span>
                    <span class="w-3 h-3 rounded-full bg-indigo-500"></span>
                </div>
                <div id="kpi-total" class="text-3xl font-black text-indigo-700">{{ $programacion->count() }}</div>
                <div class="text-[10px] font-bold text-slate-400 uppercase">EESS en Borrador</div>
                <div class="mt-2 text-[9px] text-slate-400">
                    <span id="kpi-con-est" class="font-bold text-slate-600">—</span> vinculados
                </div>
            </div>
            @php
                $provs = ['ICA' => ['color' => 'indigo', 'sec' => '1-10'], 'PISCO' => ['color' => 'violet', 'sec' => '11-14'], 'CHINCHA' => ['color' => 'rose', 'sec' => '15-18'], 'PALPA' => ['color' => 'amber', 'sec' => '19-20'], 'NAZCA' => ['color' => 'emerald', 'sec' => '21-23']];
            @endphp
            @foreach($provs as $prov => $cfg)
                <div class="bg-white rounded-2xl border border-{{ $cfg['color'] }}-100 p-5 shadow-sm">
                    <div class="text-[9px] font-black text-{{ $cfg['color'] }}-400 uppercase tracking-widest mb-1">
                        {{ ucfirst(strtolower($prov)) }}</div>
                    <div id="kpi-{{ strtolower($prov) }}" class="text-2xl font-black text-{{ $cfg['color'] }}-600">
                        {{ $programacion->where('provincia', $prov)->count() }}</div>
                    <div class="text-[9px] text-{{ $cfg['color'] }}-300 mt-1">Secs {{ $cfg['sec'] }}</div>
                </div>
            @endforeach
        </div>

        {{-- ══ FILTROS ══ --}}
        <div id="seccion-filtros"
            class="bg-white rounded-2xl shadow-sm border border-slate-200 transition-all duration-300">
            <div class="flex items-center justify-between px-5 py-3 border-b border-slate-100 cursor-pointer hover:bg-slate-50 transition-colors"
                onclick="document.getElementById('filtros-content').classList.toggle('hidden'); document.getElementById('icon-filtros-toggle').classList.toggle('rotate-180');">
                <div class="flex items-center gap-2">
                    <i data-lucide="filter" class="w-4 h-4 text-indigo-500"></i>
                    <h3 class="text-[11px] font-black text-slate-600 uppercase tracking-widest">Filtrar para Re-Sectorizar
                    </h3>
                </div>
                <button type="button" class="text-slate-400 hover:text-slate-600 transition-colors">
                    <i data-lucide="chevron-up" id="icon-filtros-toggle"
                        class="w-4 h-4 transition-transform duration-300 rotate-180"></i>
                </button>
            </div>

            <div id="filtros-content" class="p-5 hidden">
                <div class="flex flex-wrap items-end gap-3">
                    <div class="flex flex-col gap-1">
                        <label class="text-[9px] font-black text-slate-400 uppercase">Provincia</label>
                        <select id="filtro-provincia"
                            class="text-xs border-slate-200 rounded-xl px-3 py-2.5 font-bold text-slate-700 bg-slate-50 min-w-[130px]">
                            <option value="">Todas</option>
                        </select>
                    </div>
                    <div class="flex flex-col gap-1">
                        <label class="text-[9px] font-black text-slate-400 uppercase">Distrito</label>
                        <select id="filtro-distrito"
                            class="text-xs border-slate-200 rounded-xl px-3 py-2.5 font-bold text-slate-700 bg-slate-50 min-w-[150px]">
                            <option value="">Todos</option>
                        </select>
                    </div>
                    <div class="flex flex-col gap-1 flex-1 min-w-[200px]">
                        <label class="text-[9px] font-black text-slate-400 uppercase">Establecimiento</label>
                        <select id="filtro-establecimiento"
                            class="text-xs border-slate-200 rounded-xl px-3 py-2.5 font-bold text-slate-700 bg-slate-50 w-full">
                            <option value="">Todos</option>
                        </select>
                    </div>
                    <div class="flex flex-col gap-1">
                        <label class="text-[9px] font-black text-slate-400 uppercase">Sector</label>
                        <select id="filtro-sector"
                            class="text-xs border-slate-200 rounded-xl px-3 py-2.5 font-bold text-slate-700 bg-slate-50 min-w-[130px]">
                            <option value="">Todos</option>
                        </select>
                    </div>
                    <div class="flex flex-col gap-1">
                        <label class="text-[9px] font-black text-slate-400 uppercase">Categoría</label>
                        <select id="filtro-categoria"
                            class="text-xs border-slate-200 rounded-xl px-3 py-2.5 font-bold text-slate-700 bg-slate-50 min-w-[100px]">
                            <option value="">Todas</option>
                        </select>
                    </div>
                    <div class="flex flex-col gap-1">
                        <label class="text-[9px] font-black text-slate-400 uppercase">Progresión</label>
                        <select id="filtro-etapa"
                            class="text-xs border-slate-200 rounded-xl px-3 py-2.5 font-bold text-slate-700 bg-slate-50">
                            <option value="">Todas</option>
                            <option value="0">Sin Inicio</option>
                            <option value="1">Implementado</option>
                            <option value="2">Con Asistencia</option>
                            <option value="3">Con Monitoreo</option>
                            <option value="4">Ciclo Completo</option>
                        </select>
                    </div>
                    <div class="flex items-center gap-3 w-full justify-end mt-2">
                        <div class="bg-indigo-50 border border-indigo-100 rounded-xl px-4 py-2.5 flex items-center gap-2">
                            <span class="text-[9px] font-black text-indigo-400 uppercase tracking-widest">Encontrados</span>
                            <span id="badge-visible"
                                class="text-sm font-black text-indigo-700">{{ $programacion->count() }}</span>
                            <span class="text-[9px] font-black text-indigo-400 uppercase tracking-widest">EESS</span>
                        </div>
                        <button id="btn-foco" title="Ver solo mapa"
                            class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-wide bg-slate-900 text-white hover:bg-indigo-600 transition-all shadow-sm">
                            <svg id="icon-expand" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l5-5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" />
                            </svg>
                            <span id="label-foco">Ver solo mapa</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- ══ MAPA ══ --}}
        <div id="mapa-wrapper" class="relative rounded-2xl overflow-hidden shadow-xl border border-slate-200"
            style="height: 560px;">
            <div id="mapa-sectores" class="h-full w-full"></div>

            {{-- LEYENDA SECTORES --}}
            <div
                class="absolute bottom-5 right-5 z-[1000] bg-white/95 backdrop-blur-md p-4 rounded-2xl shadow-2xl border border-slate-100 min-w-[210px]">
                <h4 class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-3 flex items-center gap-1.5">
                    <i data-lucide="map-pin" class="w-3 h-3"></i> Sectores
                </h4>
                <div id="leyenda-sectores" class="space-y-1 max-h-[450px] overflow-y-auto pr-1"></div>
            </div>

            {{-- LEYENDA ETAPAS --}}
            <div
                class="absolute bottom-5 left-5 z-[1000] bg-white/95 backdrop-blur-md p-3 rounded-2xl shadow-2xl border border-slate-100">
                <h4 class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-2 flex items-center gap-1">
                    <i data-lucide="layers" class="w-3 h-3"></i> Progresión SIHCE
                </h4>
                <div class="space-y-1.5">
                    <div class="flex items-center gap-2"><span
                            class="w-3 h-3 rounded-full bg-slate-400 flex-shrink-0 border border-white shadow-sm"></span><span
                            class="text-[9px] text-slate-500">Sin Inicio</span></div>
                    <div class="flex items-center gap-2"><span
                            class="w-3.5 h-3.5 rounded-full bg-blue-500 flex-shrink-0 border border-white shadow-sm"></span><span
                            class="text-[9px] text-slate-500">Implementado</span></div>
                    <div class="flex items-center gap-2"><span
                            class="w-4 h-4 rounded-full bg-amber-500 flex-shrink-0 border border-white shadow-sm"></span><span
                            class="text-[9px] text-slate-500">Con Asistencia</span></div>
                    <div class="flex items-center gap-2"><span
                            class="w-5 h-5 rounded-full bg-violet-500 flex-shrink-0 border border-white shadow-sm"></span><span
                            class="text-[9px] text-slate-500">Con Monitoreo</span></div>
                    <div class="flex items-center gap-2"><span
                            class="w-5 h-5 rounded-full bg-emerald-500 flex-shrink-0 border border-white shadow-sm animate-pulse"></span><span
                            class="text-[9px] text-slate-500">Ciclo Completo</span></div>
                </div>
            </div>
        </div>

        {{-- ══ CRONOGRAMA GANTT ══ --}}
        <div id="seccion-gantt" class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center gap-2">
                <i data-lucide="calendar-range" class="w-4 h-4 text-indigo-400"></i>
                <h3 class="text-[11px] font-black text-slate-600 uppercase tracking-widest">Previsualización de Tiempos</h3>
                <span class="text-[9px] text-slate-400 ml-auto font-semibold uppercase tracking-tighter">Borrador de
                    Trabajo</span>
            </div>
            <div class="px-6 py-4 overflow-x-auto">
                <div class="mb-1 relative" style="padding-left: 120px;">
                    <div id="gantt-months" class="flex text-[8px] font-black text-slate-400 uppercase tracking-wider"></div>
                </div>
                <div id="gantt-rows" class="space-y-0.5"></div>
            </div>
        </div>

        {{-- ══ TABLA DE DETALLE ══ --}}
        <div id="seccion-tabla" class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i data-lucide="table-2" class="w-4 h-4 text-indigo-400"></i>
                    <h3 class="text-[11px] font-black text-slate-600 uppercase tracking-widest">Lista de Establecimientos —
                        Edición</h3>
                </div>
                <span id="tabla-info"
                    class="text-[9px] text-slate-400 font-semibold bg-slate-50 px-3 py-1.5 rounded-lg border border-slate-100"></span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-xs">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100">
                            <th
                                class="text-left px-3 py-3 text-[9px] font-black text-slate-400 uppercase tracking-widest w-6">
                                #</th>
                            <th class="text-left px-3 py-3 text-[9px] font-black text-slate-400 uppercase tracking-widest">
                                EESS</th>
                            <th class="text-left px-3 py-3 text-[9px] font-black text-slate-400 uppercase tracking-widest">
                                Provincia</th>
                            <th class="text-left px-3 py-3 text-[9px] font-black text-slate-400 uppercase tracking-widest">
                                Sector</th>
                            <th class="text-left px-3 py-3 text-[9px] font-black text-slate-400 uppercase tracking-widest">
                                Cuad.</th>
                            <th class="text-left px-3 py-3 text-[9px] font-black text-slate-400 uppercase tracking-widest">
                                Comienzo</th>
                            <th class="text-left px-3 py-3 text-[9px] font-black text-slate-400 uppercase tracking-widest">
                                Fin</th>
                            <th class="text-right px-3 py-3 text-[9px] font-black text-slate-400 uppercase tracking-widest">
                                Días</th>
                            <th
                                class="text-center px-3 py-3 text-[9px] font-black text-slate-400 uppercase tracking-widest">
                                Progresión</th>
                            <th
                                class="text-center px-3 py-3 text-[9px] font-black text-slate-400 uppercase tracking-widest">
                                Acción</th>
                        </tr>
                    </thead>
                    <tbody id="tabla-body" class="divide-y divide-slate-50"></tbody>
                </table>
            </div>
        </div>

    </div>

    {{-- Botón flotante para salir del modo foco --}}
    <button id="btn-salir-foco" aria-label="Salir del modo foco">
        <i data-lucide="minimize-2" class="w-4 h-4"></i>
        Salir del modo mapa
    </button>

    {{-- ══ MODAL EDICIÓN DE SECTOR ══ --}}
    <div id="modal-edit" class="fixed inset-0 z-[9999] items-center justify-center bg-slate-900/60 backdrop-blur-sm">
        <div class="bg-white rounded-2xl shadow-2xl border border-slate-100 w-full max-w-sm mx-4 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="font-black text-slate-800 text-sm">Cambiar Sectorización</h3>
                    <p id="modal-nombre" class="text-[10px] text-slate-400 mt-0.5 font-bold uppercase"></p>
                </div>
                <button id="modal-close" class="text-slate-400 hover:text-slate-600 transition-colors">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            <form id="modal-form" class="px-6 py-5 space-y-4">
                @csrf
                <div>
                    <label class="text-[10px] font-black text-slate-500 uppercase tracking-wider block mb-1">Nuevo Sector
                        Asignado</label>
                    <select id="modal-sector" name="sector"
                        class="w-full text-sm border border-slate-200 rounded-xl px-3 py-2.5 font-bold text-slate-700 bg-slate-50 focus:ring-indigo-500 focus:border-indigo-400">
                        @for($s = 1; $s <= 23; $s++)
                            <option value="{{ $s }}">Sector {{ $s }}</option>
                        @endfor
                    </select>
                    <p class="text-[9px] text-slate-400 mt-1 italic">El color del punto en el mapa cambiará automáticamente.
                    </p>
                </div>
                <div>
                    <label class="text-[10px] font-black text-slate-500 uppercase tracking-wider block mb-1">Cuadrilla
                        (Borrador)</label>
                    <input id="modal-cuadril" name="cuadril" type="text" maxlength="15"
                        class="w-full text-sm border border-slate-200 rounded-xl px-3 py-2.5 font-bold text-slate-700 bg-slate-50 focus:ring-indigo-500 focus:border-indigo-400"
                        placeholder="Ej. C1A">
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" id="modal-cancel"
                        class="flex-1 px-4 py-2.5 text-sm font-bold text-slate-600 bg-slate-100 rounded-xl hover:bg-slate-200 transition-colors">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="flex-1 px-4 py-2.5 text-sm font-black text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 transition-all shadow-md shadow-indigo-200">
                        Guardar Propuesta
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
            // IMPORTANTE: URL DE LA RUTA DE PROPUESTA
            var updateUrl = "{{ route('usuario.dashboard.programacion.propuesta.update', ['id' => '__ID__']) }}";
            var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            var sectorColors = {
                1: '#ef4444', 2: '#3b82f6', 3: '#22c55e', 4: '#a855f7', 5: '#f97316',
                6: '#06b6d4', 7: '#eab308', 8: '#ec4899', 9: '#14b8a6', 10: '#6366f1',
                11: '#84cc16', 12: '#f43f5e', 13: '#0ea5e9', 14: '#d946ef', 15: '#10b981',
                16: '#f59e0b', 17: '#4338ca', 18: '#be185d', 19: '#166534', 20: '#c2410c',
                21: '#1e40af', 22: '#86198f', 23: '#4d7c0f', 24: '#9f1239', 25: '#0e7490',
                26: '#b45309', 27: '#1d4ed8', 28: '#047857', 29: '#6b21a8', 30: '#8b5cf6'
            };

            var etapaConfig = {
                0: { color: '#94a3b8', size: 7, label: 'Sin Inicio' },
                1: { color: '#3b82f6', size: 10, label: 'Implementado' },
                2: { color: '#f59e0b', size: 13, label: 'Con Asistencia' },
                3: { color: '#8b5cf6', size: 16, label: 'Con Monitoreo' },
                4: { color: '#22c55e', size: 19, label: 'Ciclo Completo' },
            };

            var conEst = programacion.filter(function (p) { return p.tiene_est; }).length;
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
                var etCfg = etapaConfig[p.etapa];
                var etLabels = { 0: 'bg-slate-100 text-slate-600', 1: 'bg-blue-100 text-blue-700', 2: 'bg-amber-100 text-amber-700', 3: 'bg-violet-100 text-violet-700', 4: 'bg-emerald-100 text-emerald-700' };

                return '<div class="bg-white min-w-[260px] max-w-[300px]">'
                    + '<div class="px-4 pt-4 pb-3 border-b border-slate-100">'
                    + '<div class="flex items-center gap-2 mb-1">'
                    + '<span class="w-6 h-6 rounded-full text-white text-[9px] font-black flex items-center justify-center flex-shrink-0" style="background:' + secColor + '">S' + p.sector + '</span>'
                    + '<h4 class="font-black text-slate-800 text-[12px] leading-tight">' + p.nombre + '</h4>'
                    + '</div>'
                    + '<p class="text-[10px] text-slate-400 uppercase tracking-wider font-bold">' + (p.distrito || '') + ' — ' + p.provincia + '</p>'
                    + '<div class="flex gap-1.5 mt-1.5">'
                    + '<span class="text-[9px] font-black px-2 py-0.5 rounded-full text-white" style="background:' + secColor + '">' + (p.cuadril || 'S/C') + '</span>'
                    + '<span class="text-[9px] font-black px-2 py-0.5 rounded-full ' + etLabels[p.etapa] + '">' + etCfg.label + '</span>'
                    + '</div>'
                    + '</div>'
                    + '<div class="px-4 py-3 space-y-1.5">'
                    + check(p.etapa >= 1, 'Implementación')
                    + check(p.etapa >= 2 || p.etapa == 4, 'Asistencia Técnica')
                    + check(p.etapa >= 3 || p.etapa == 4, 'Monitoreo')
                    + '</div>'
                    + '<div class="px-4 py-3 border-t border-slate-50 pt-2">'
                    + '<div class="flex justify-between text-[10px] mb-1"><span class="text-slate-500 font-semibold italic">Programado:</span><span class="font-black text-slate-700 underline">' + (p.comienzo || '—') + ' al ' + (p.fin || '—') + '</span></div>'
                    + '<button type="button" class="mt-2 w-full py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-600 font-black text-[10px] uppercase rounded-xl transition-all border border-indigo-100 shadow-sm" onclick="openModal(' + p.id + ', \'' + p.nombre.replace(/'/g, "\\'") + '\', ' + p.sector + ', \'' + (p.cuadril || '') + '\')">'
                    + 'Modificar Sector (Propuesta)'
                    + '</button>'
                    + '</div>'
                    + '</div>';
            }

            /* ══════════════════════════════════════════════════════
               MAPA LEAFLET
            ══════════════════════════════════════════════════════ */
            var map = L.map('mapa-sectores').setView([-14.07, -75.73], 8);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }).addTo(map);

            var markersList = [];
            programacion.forEach(function (p) {
                if (!p.lat || !p.lon || isNaN(p.lat)) return;
                var sc = sectorColors[p.sector] || '#6366f1';
                var etc = etapaConfig[p.etapa];
                var m = L.circleMarker([p.lat, p.lon], {
                    radius: etc.size / 2 + 4,
                    fillColor: sc,
                    color: etc.color,
                    weight: p.etapa === 4 ? 3 : 2,
                    opacity: 1,
                    fillOpacity: 0.85,
                }).addTo(map).bindPopup(buildPopup(p), { className: 'custom-popup', maxWidth: 320 });

                markersList.push({ marker: m, item: p });
            });

            /* ══════════════════════════════════════════════════════
               LEYENDA SECTORES SECTORES DINÁMICA
            ══════════════════════════════════════════════════════ */
            var sectoresUnicos = [];
            function updateSectoresUnicos() {
                sectoresUnicos = [...new Set(programacion.map(function (p) { return p.sector; }))].sort(function (a, b) { return a - b; });
            }
            updateSectoresUnicos();

            function buildLeyenda(filteredMarkers) {
                var cont = document.getElementById('leyenda-sectores');
                cont.innerHTML = '';
                var markersToCount = filteredMarkers || markersList;

                updateSectoresUnicos();

                sectoresUnicos.forEach(function (s) {
                    var color = sectorColors[s] || '#6366f1';
                    var prov = (programacion.find(function (p) { return p.sector === s; }) || {}).provincia || '';
                    var cnt = markersToCount.filter(function (m) { return parseInt(m.item.sector) === parseInt(s); }).length;

                    var d = document.createElement('div');
                    d.className = 'flex items-center gap-2 cursor-pointer py-0.5 ' + (cnt === 0 ? 'opacity-30 grayscale' : 'opacity-100');
                    d.innerHTML = '<span class="w-3 h-3 rounded-full border-2 border-white shadow-sm" style="background:' + color + '"></span>'
                        + '<div><p class="text-[10px] font-bold text-slate-700 leading-none">Sector ' + s + '</p><p class="text-[8px] text-slate-400">' + prov + ' · ' + cnt + ' EESS</p></div>';
                    d.addEventListener('click', function () {
                        document.getElementById('filtro-sector').value = s;
                        document.getElementById('filtro-sector').dispatchEvent(new Event('change'));
                    });
                    cont.appendChild(d);
                });
            }

            /* ══════════════════════════════════════════════════════
               FILTROS Y UI
            ══════════════════════════════════════════════════════ */
            var filtroProv = '', filtroSec = '', filtroEtapa = '', filtroDistrito = '', filtroCategoria = '', filtroEst = '';

            function applyFilters(skipFit) {
                var group = L.featureGroup();
                var filtered = [];

                markersList.forEach(function (mObj) {
                    var p = mObj.item;
                    var ok = (filtroProv === '' || p.provincia === filtroProv)
                        && (filtroSec === '' || String(p.sector) === String(filtroSec))
                        && (filtroEtapa === '' || String(p.etapa) === String(filtroEtapa))
                        && (filtroDistrito === '' || (p.distrito || '') === filtroDistrito)
                        && (filtroCategoria === '' || (p.categoria || '') === filtroCategoria)
                        && (filtroEst === '' || (p.nombre || '') === filtroEst);
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
                buildLeyenda(filtered);
                updateKPIs();
                buildGantt();

                if (!skipFit && group.getLayers().length > 0 && (filtroProv || filtroSec || filtroEtapa || filtroDistrito || filtroCategoria || filtroEst)) {
                    map.fitBounds(group.getBounds(), { padding: [50, 50], maxZoom: 13 });
                }
            }

            function updateKPIs() {
                var provs = ['ICA', 'PISCO', 'CHINCHA', 'PALPA', 'NAZCA'];
                provs.forEach(function (prov) {
                    var el = document.getElementById('kpi-' + prov.toLowerCase());
                    if (el) {
                        var cnt = programacion.filter(function (p) { return p.provincia === prov; }).length;
                        el.textContent = cnt;
                    }
                });
                document.getElementById('kpi-total').textContent = programacion.length;
            }

            function updateTabla(filtered) {
                var tbody = document.getElementById('tabla-body');
                tbody.innerHTML = '';
                filtered.forEach(function (mObj, idx) {
                    var p = mObj.item;
                    var color = sectorColors[p.sector] || '#6366f1';
                    var tr = document.createElement('tr');
                    tr.className = 'hover:bg-slate-50 transition-colors cursor-pointer';
                    tr.innerHTML = '<td class="px-3 py-2.5 text-slate-400 font-bold">' + (idx + 1) + '</td>'
                        + '<td class="px-3 py-2.5 font-semibold text-slate-700">' + p.nombre + '</td>'
                        + '<td class="px-3 py-2.5 text-slate-500">' + p.provincia + '</td>'
                        + '<td class="px-3 py-2.5" data-sector-cell="' + p.id + '"><span class="inline-flex items-center text-[10px] font-black px-2 py-0.5 rounded-full text-white" style="background:' + color + '">Sec ' + p.sector + '</span></td>'
                        + '<td class="px-3 py-2.5 font-mono text-slate-500">' + (p.cuadril || '—') + '</td>'
                        + '<td class="px-3 py-2.5 text-slate-500">' + (p.comienzo || '—') + '</td>'
                        + '<td class="px-3 py-2.5 text-slate-500">' + (p.fin || '—') + '</td>'
                        + '<td class="px-3 py-2.5 text-right">' + (p.dias || '—') + '</td>'
                        + '<td class="px-3 py-2.5 text-center"><span class="text-[9px] font-black px-2 py-0.5 rounded-full ' + (etapaBadge[p.etapa] || '') + '">' + (etapaLabels[p.etapa] || '') + '</span></td>'
                        + '<td class="px-3 py-2.5 text-center"><button class="btn-editar font-black text-indigo-600 text-[10px] px-2 py-1 bg-indigo-50 rounded-lg" data-id="' + p.id + '" data-nombre="' + p.nombre.replace(/'/g, "") + '" data-sector="' + p.sector + '" data-cuadril="' + (p.cuadril || '') + '">EDITAR</button></td>';
                    tr.addEventListener('click', function (e) {
                        if (e.target.closest('.btn-editar')) return;
                        map.setView([p.lat, p.lon], 15); mObj.marker.openPopup();
                    });
                    tbody.appendChild(tr);
                });
                tbody.querySelectorAll('.btn-editar').forEach(function (b) { b.onclick = function (e) { e.stopPropagation(); openModal(b.dataset.id, b.dataset.nombre, b.dataset.sector, b.dataset.cuadril); }; });
            }
            var etapaLabels = { 0: 'Sin Inicio', 1: 'Implementado', 2: 'Con Asistencia', 3: 'Con Monitoreo', 4: 'Ciclo Completo' };
            var etapaBadge = { 0: 'etapa-badge-0', 1: 'etapa-badge-1', 2: 'etapa-badge-2', 3: 'etapa-badge-3', 4: 'etapa-badge-4' };

            function buildGantt() {
                var REF_START = new Date('2026-04-07');
                var REF_END = new Date('2027-03-23');
                var TOTAL_MS = REF_END - REF_START;
                var monthsCont = document.getElementById('gantt-months');
                monthsCont.innerHTML = '';
                var monthsNames = ['Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic', 'Ene', 'Feb', 'Mar'];
                for (var i = 0; i < 12; i++) {
                    var span = document.createElement('span');
                    span.style.flex = '1'; span.textContent = monthsNames[i]; monthsCont.appendChild(span);
                }
                var sectoresProg = {};
                programacion.forEach(function (p) {
                    if (!p.comienzo_iso || !p.fin_iso) return;
                    if (!sectoresProg[p.sector]) sectoresProg[p.sector] = { sector: p.sector, items: [] };
                    sectoresProg[p.sector].items.push(p);
                });
                var rowsCont = document.getElementById('gantt-rows');
                rowsCont.innerHTML = '';
                Object.keys(sectoresProg).sort(function (a, b) { return a - b; }).forEach(function (sKey) {
                    var sg = sectoresProg[sKey];
                    var comMin = sg.items.reduce(function (mn, p) { return !mn || p.comienzo_iso < mn ? p.comienzo_iso : mn; }, null);
                    var finMax = sg.items.reduce(function (mx, p) { return !mx || p.fin_iso > mx ? p.fin_iso : mx; }, null);
                    var row = document.createElement('div');
                    row.className = 'flex items-center gap-2 mb-0.5';
                    row.innerHTML = '<div style="width:116px; flex-shrink:0;" class="text-[9px] font-black text-slate-500">S' + sg.sector + ' (' + sg.items.length + ')</div>'
                        + '<div style="flex:1; position:relative; height:18px; background:#f8fafc; border-radius:4px; overflow:hidden;">'
                        + '<div class="gantt-bar" style="left:' + ((new Date(comMin) - REF_START) / TOTAL_MS * 100) + '%; width:' + ((new Date(finMax) - new Date(comMin)) / TOTAL_MS * 100) + '%; background:' + (sectorColors[sg.sector] || '#6366f1') + '; opacity:0.7;"></div>'
                        + '</div>';
                    rowsCont.appendChild(row);
                });
            }

            /* ── POBLACIÓN DE SELECTS DINÁMICOS ── */
            function getFilteredSource(excludeFilter) {
                return programacion.filter(function (p) {
                    var ok = true;
                    if (excludeFilter !== 'prov' && filtroProv !== '') ok = ok && (p.provincia === filtroProv);
                    if (excludeFilter !== 'dist' && filtroDistrito !== '') ok = ok && (p.distrito === filtroDistrito);
                    if (excludeFilter !== 'sec' && filtroSec !== '') ok = ok && (String(p.sector) === String(filtroSec));
                    if (excludeFilter !== 'etapa' && filtroEtapa !== '') ok = ok && (String(p.etapa) === String(filtroEtapa));
                    if (excludeFilter !== 'cat' && filtroCategoria !== '') ok = ok && (p.categoria === filtroCategoria);
                    if (excludeFilter !== 'est' && filtroEst !== '') ok = ok && (p.nombre === filtroEst);
                    return ok;
                });
            }

            function poblarProvincias() {
                var sel = document.getElementById('filtro-provincia');
                var prevVal = sel.value;
                sel.innerHTML = '<option value="">Todas</option>';
                var source = getFilteredSource('prov');
                var provs = [...new Set(source.map(function (p) { return p.provincia; }))].sort();
                provs.forEach(function (pr) { var opt = document.createElement('option'); opt.value = pr; opt.textContent = pr; sel.appendChild(opt); });
                if ([...sel.options].some(o => o.value === prevVal)) sel.value = prevVal;
            }

            function poblarDistritos() {
                var sel = document.getElementById('filtro-distrito');
                var prevVal = sel.value;
                sel.innerHTML = '<option value="">Todos</option>';
                var source = getFilteredSource('dist');
                var distritos = [...new Set(source.filter(function (p) { return p.distrito; }).map(function (p) { return p.distrito; }))].sort();
                distritos.forEach(function (d) { var opt = document.createElement('option'); opt.value = d; opt.textContent = d; sel.appendChild(opt); });
                if ([...sel.options].some(o => o.value === prevVal)) sel.value = prevVal;
            }

            function poblarEstablecimientos() {
                var sel = document.getElementById('filtro-establecimiento');
                var prevVal = sel.value;
                sel.innerHTML = '<option value="">Todos</option>';
                var source = getFilteredSource('est');
                var eess = [...new Set(source.map(function (p) { return p.nombre; }))].sort();
                eess.forEach(function (e) { var opt = document.createElement('option'); opt.value = e; opt.textContent = e; sel.appendChild(opt); });
                if ([...sel.options].some(o => o.value === prevVal)) sel.value = prevVal;
            }

            function poblarCategorias() {
                var sel = document.getElementById('filtro-categoria');
                var prevVal = sel.value;
                sel.innerHTML = '<option value="">Todas</option>';
                var source = getFilteredSource('cat');
                var cats = [...new Set(source.filter(function (p) { return p.categoria; }).map(function (p) { return p.categoria; }))].sort();
                cats.forEach(function (c) { var opt = document.createElement('option'); opt.value = c; opt.textContent = c; sel.appendChild(opt); });
                if ([...sel.options].some(o => o.value === prevVal)) sel.value = prevVal;
            }

            function poblarSectores() {
                var sel = document.getElementById('filtro-sector');
                var prevVal = sel.value;
                sel.innerHTML = '<option value="">Todos</option>';
                var source = getFilteredSource('sec');
                var secs = [...new Set(source.map(function (p) { return p.sector; }))].sort(function (a, b) { return a - b; });
                secs.forEach(function (s) { var opt = document.createElement('option'); opt.value = s; opt.textContent = 'Sector ' + s; sel.appendChild(opt); });
                if ([...sel.options].some(o => o.value === prevVal)) sel.value = prevVal;
            }

            function syncAllSelects() {
                poblarProvincias();
                poblarDistritos();
                poblarEstablecimientos();
                poblarCategorias();
                poblarSectores();
            }

            /* ── LISTENERS ── */
            document.getElementById('filtro-provincia').addEventListener('change', function () {
                filtroProv = this.value; syncAllSelects(); applyFilters();
            });

            document.getElementById('filtro-distrito').addEventListener('change', function () {
                filtroDistrito = this.value; syncAllSelects(); applyFilters();
            });

            document.getElementById('filtro-establecimiento').addEventListener('change', function () {
                filtroEst = this.value; syncAllSelects(); applyFilters();
            });

            document.getElementById('filtro-sector').addEventListener('change', function () {
                filtroSec = this.value; syncAllSelects(); applyFilters();
            });

            document.getElementById('filtro-categoria').addEventListener('change', function () {
                filtroCategoria = this.value; syncAllSelects(); applyFilters();
            });

            document.getElementById('filtro-etapa').addEventListener('change', function () {
                filtroEtapa = this.value; syncAllSelects(); applyFilters();
            });

            function initFilters() {
                syncAllSelects();
            }

            /* INICIALIZAR */
            initFilters();
            applyFilters();
            var allG = L.featureGroup(markersList.map(function (m) { return m.marker }));
            if (allG.getLayers().length > 0) map.fitBounds(allG.getBounds(), { padding: [40, 40] });

            /* MODO FOCO */
            var f = false; document.getElementById('btn-foco').onclick = function () { f = !f; document.body.classList.toggle('modo-foco', f); setTimeout(function () { map.invalidateSize() }, 400); };
            document.getElementById('btn-salir-foco').onclick = function () { f = false; document.body.classList.remove('modo-foco'); map.invalidateSize(); };

        })();
    </script>
@endpush