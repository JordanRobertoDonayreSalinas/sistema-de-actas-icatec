@extends('layouts.usuario')

@section('title', 'Mapa de Progresión ICATEC')

@section('header-content')
    <h1 class="text-xl font-bold text-slate-800 tracking-tight">
        Mapa de Progresión
    </h1>
    <div class="flex items-center gap-2 text-xs text-slate-500 mt-0.5">
        <span>Plataforma</span>
        <span class="text-slate-300">•</span>
        <span>Mapa de Progresión — Implementación → Asistencia → Monitoreo</span>
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

        /* Marcador pulsante para etapa 3 */
        @keyframes glow-pulse {
            0%   { box-shadow: 0 0 0 0 rgba(34,197,94,0.5); }
            70%  { box-shadow: 0 0 0 14px rgba(34,197,94,0); }
            100% { box-shadow: 0 0 0 0 rgba(34,197,94,0); }
        }
        .marker-completo { animation: glow-pulse 2s infinite; border-radius: 50%; }

        /* Filtro etapa activo */
        .btn-etapa.activo { ring: 2px; transform: scale(1.05); }

        /* Barra de progresión etapas */
        .stage-bar { transition: width 0.6s ease; }
    </style>
@endpush

@section('content')
    <div class="max-w-7xl mx-auto space-y-5">

        {{-- ══ PANEL SUPERIOR: KPIs de Progresión ══ --}}
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4">

            {{-- Etapa 0 --}}
            <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Sin Inicio</span>
                    <span class="w-3 h-3 rounded-full bg-slate-400 shadow-sm"></span>
                </div>
                <div class="text-3xl font-black text-slate-700">{{ $contadores['etapa0'] }}</div>
                <div class="text-[10px] text-slate-400 mt-1">establecimientos</div>
                <div class="mt-3 bg-slate-100 rounded-full h-1.5 overflow-hidden">
                    <div class="stage-bar h-full bg-slate-400 rounded-full"
                         style="width: {{ $contadores['total'] > 0 ? round($contadores['etapa0'] / $contadores['total'] * 100) : 0 }}%"></div>
                </div>
            </div>

            {{-- Etapa 1 --}}
            <div class="bg-white rounded-2xl border border-blue-100 p-5 shadow-sm">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-[9px] font-black text-blue-400 uppercase tracking-widest">Implementados</span>
                    <span class="w-3 h-3 rounded-full bg-blue-500 shadow-sm"></span>
                </div>
                <div class="text-3xl font-black text-blue-700">{{ $contadores['etapa1'] }}</div>
                <div class="text-[10px] text-blue-400 mt-1">listos para asistencia</div>
                <div class="mt-3 bg-blue-50 rounded-full h-1.5 overflow-hidden">
                    <div class="stage-bar h-full bg-blue-500 rounded-full"
                         style="width: {{ $contadores['total'] > 0 ? round($contadores['etapa1'] / $contadores['total'] * 100) : 0 }}%"></div>
                </div>
            </div>

            {{-- Etapa 2 --}}
            <div class="bg-white rounded-2xl border border-amber-100 p-5 shadow-sm">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-[9px] font-black text-amber-500 uppercase tracking-widest">Con Asistencia</span>
                    <span class="w-3 h-3 rounded-full bg-amber-500 shadow-sm"></span>
                </div>
                <div class="text-3xl font-black text-amber-700">{{ $contadores['etapa2'] }}</div>
                <div class="text-[10px] text-amber-400 mt-1">listos para monitoreo</div>
                <div class="mt-3 bg-amber-50 rounded-full h-1.5 overflow-hidden">
                    <div class="stage-bar h-full bg-amber-500 rounded-full"
                         style="width: {{ $contadores['total'] > 0 ? round($contadores['etapa2'] / $contadores['total'] * 100) : 0 }}%"></div>
                </div>
            </div>

            {{-- Etapa 3 --}}
            <div class="bg-white rounded-2xl border border-violet-100 p-5 shadow-sm">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-[9px] font-black text-violet-500 uppercase tracking-widest">Con Monitoreo</span>
                    <span class="w-3 h-3 rounded-full bg-violet-500 shadow-sm"></span>
                </div>
                <div class="text-3xl font-black text-violet-700">{{ $contadores['etapa3'] }}</div>
                <div class="text-[10px] text-violet-400 mt-1">monitoreo activo</div>
                <div class="mt-3 bg-violet-50 rounded-full h-1.5 overflow-hidden">
                    <div class="stage-bar h-full bg-violet-500 rounded-full"
                         style="width: {{ $contadores['total'] > 0 ? round($contadores['etapa3'] / $contadores['total'] * 100) : 0 }}%"></div>
                </div>
            </div>

            {{-- Etapa 4 --}}
            <div class="bg-white rounded-2xl border border-emerald-100 p-5 shadow-sm">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-[9px] font-black text-emerald-500 uppercase tracking-widest">Ciclo Completo</span>
                    <span class="w-3 h-3 rounded-full bg-emerald-500 shadow-sm animate-pulse"></span>
                </div>
                <div class="text-3xl font-black text-emerald-700">{{ $contadores['etapa4'] }}</div>
                <div class="text-[10px] text-emerald-400 mt-1">de {{ $contadores['total'] }} totales</div>
                <div class="mt-3 bg-emerald-50 rounded-full h-1.5 overflow-hidden">
                    <div class="stage-bar h-full bg-emerald-500 rounded-full"
                         style="width: {{ $contadores['total'] > 0 ? round($contadores['etapa4'] / $contadores['total'] * 100) : 0 }}%"></div>
                </div>
            </div>
        </div>

        {{-- ══ PANEL DE CONTROL + FILTROS ══ --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
            <div class="flex flex-col lg:flex-row lg:items-start justify-between gap-6">

                {{-- Filtros por etapa --}}
                <div class="space-y-3">
                    <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-2">
                        <i data-lucide="filter" class="w-3 h-3"></i> Filtrar por Etapa
                    </h3>
                    <div class="flex flex-wrap gap-2" id="filtros-etapa">
                        <button data-etapa="" class="btn-etapa activo px-3 py-1.5 rounded-xl text-[10px] font-black uppercase tracking-wider bg-slate-800 text-white transition-all">
                            Todas
                        </button>
                        <button data-etapa="0" class="btn-etapa px-3 py-1.5 rounded-xl text-[10px] font-black uppercase tracking-wider bg-slate-100 text-slate-600 hover:bg-slate-200 transition-all flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-slate-400"></span> Sin Inicio
                        </button>
                        <button data-etapa="1" class="btn-etapa px-3 py-1.5 rounded-xl text-[10px] font-black uppercase tracking-wider bg-blue-50 text-blue-700 hover:bg-blue-100 transition-all flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-blue-500"></span> Implementados
                        </button>
                        <button data-etapa="2" class="btn-etapa px-3 py-1.5 rounded-xl text-[10px] font-black uppercase tracking-wider bg-amber-50 text-amber-700 hover:bg-amber-100 transition-all flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-amber-500"></span> Con Asistencia
                        </button>
                        <button data-etapa="3" class="btn-etapa px-3 py-1.5 rounded-xl text-[10px] font-black uppercase tracking-wider bg-violet-50 text-violet-700 hover:bg-violet-100 transition-all flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-violet-500"></span> Con Monitoreo
                        </button>
                        <button data-etapa="4" class="btn-etapa px-3 py-1.5 rounded-xl text-[10px] font-black uppercase tracking-wider bg-emerald-50 text-emerald-700 hover:bg-emerald-100 transition-all flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span> Ciclo Completo
                        </button>
                    </div>
                </div>

                {{-- Filtros geográficos y de año --}}
                <div class="flex flex-wrap items-end gap-3">
                    <div class="flex flex-col gap-1 border-r border-slate-200 pr-3 mr-1">
                        <label class="text-[9px] font-black text-slate-400 uppercase">Año</label>
                        <select id="filtro-anio" class="text-xs border-slate-200 rounded-xl px-3 py-2 focus:ring-violet-500 transition font-bold text-slate-700 bg-slate-50">
                            <option value="todos" {{ $anioFiltro == 'todos' ? 'selected' : '' }}>Todos los años</option>
                            @foreach($aniosDisponibles as $anio)
                                <option value="{{ $anio }}" {{ $anioFiltro == $anio ? 'selected' : '' }}>{{ $anio }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="flex flex-col gap-1">
                        <label class="text-[9px] font-black text-slate-400 uppercase">Provincia</label>
                        <select id="filtro-provincia" class="text-xs border-slate-200 rounded-xl px-3 py-2 focus:ring-indigo-500 transition">
                            <option value="">Todas</option>
                            @foreach($provincias as $prov)
                                <option value="{{ $prov }}">{{ $prov }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex flex-col gap-1">
                        <label class="text-[9px] font-black text-slate-400 uppercase">Distrito</label>
                        <select id="filtro-distrito" class="text-xs border-slate-200 rounded-xl px-3 py-2 focus:ring-indigo-500 transition">
                            <option value="">Todos</option>
                            @foreach($distritos as $dist)
                                <option value="{{ $dist }}">{{ $dist }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex flex-col gap-1">
                        <label class="text-[9px] font-black text-slate-400 uppercase">Categoría</label>
                        <select id="filtro-categoria" class="text-xs border-slate-200 rounded-xl px-3 py-2 focus:ring-indigo-500 transition">
                            <option value="">Todas</option>
                            @foreach($categorias as $cat)
                                <option value="{{ $cat }}">{{ $cat }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex flex-col gap-1">
                        <label class="text-[9px] font-black text-slate-400 uppercase">Establecimiento</label>
                        <select id="filtro-establecimiento" class="text-xs border-slate-200 rounded-xl px-3 py-2 focus:ring-indigo-500 transition max-w-[200px]">
                            <option value="">Todos</option>
                            @foreach($establecimientosMap as $e)
                                <option value="{{ $e->id }}">{{ $e->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- Contador dinámico --}}
            <div id="contador-filtro" class="mt-4 hidden">
                <span class="text-[10px] text-slate-500 font-semibold bg-slate-50 px-3 py-1.5 rounded-lg border border-slate-100"></span>
            </div>
        </div>

        {{-- ══ MAPA ══ --}}
        <div class="relative rounded-2xl overflow-hidden shadow-xl border border-slate-200" style="height: 560px;">
            <div id="mapa-progresion" class="h-full w-full"></div>

            {{-- LEYENDA FLOTANTE --}}
            <div class="absolute bottom-5 right-5 z-[1000] bg-white/95 backdrop-blur-md p-4 rounded-2xl shadow-2xl border border-slate-100 min-w-[190px]">
                <h4 class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-3 flex items-center gap-1.5">
                    <i data-lucide="layers" class="w-3 h-3"></i> Etapa de Progresión
                </h4>
                <div class="space-y-2.5">
                    <div class="flex items-center gap-3">
                        <span class="w-3.5 h-3.5 rounded-full bg-slate-400 border-2 border-white shadow-sm flex-shrink-0"></span>
                        <div>
                            <p class="text-[10px] font-bold text-slate-600 leading-none">Sin inicio</p>
                            <p class="text-[9px] text-slate-400">Sin ningún acta</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="w-4 h-4 rounded-full bg-blue-500 border-2 border-white shadow-sm flex-shrink-0"></span>
                        <div>
                            <p class="text-[10px] font-bold text-blue-700 leading-none">Implementado</p>
                            <p class="text-[9px] text-blue-400">Listo para Asistencia</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="w-5 h-5 rounded-full bg-amber-500 border-2 border-white shadow-sm flex-shrink-0"></span>
                        <div>
                            <p class="text-[10px] font-bold text-amber-700 leading-none">Con Asistencia</p>
                            <p class="text-[9px] text-amber-400">Listo para Monitoreo</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="w-6 h-6 rounded-full bg-violet-500 border-2 border-white shadow-sm flex-shrink-0"></span>
                        <div>
                            <p class="text-[10px] font-bold text-violet-700 leading-none">Con Monitoreo</p>
                            <p class="text-[9px] text-violet-400">Monitoreo activo</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="w-7 h-7 rounded-full bg-emerald-500 border-2 border-white shadow-sm flex-shrink-0 animate-pulse"></span>
                        <div>
                            <p class="text-[10px] font-bold text-emerald-700 leading-none">⭐ Ciclo Completo</p>
                            <p class="text-[9px] text-emerald-400">Las 3 etapas cubiertas</p>
                        </div>
                    </div>
                </div>

            </div>

            {{-- Conteo flotante superior izquierdo --}}
            <div id="badge-visible" class="absolute top-4 left-4 z-[1000] bg-white/95 backdrop-blur-md px-4 py-2.5 rounded-xl shadow-lg border border-slate-100">
                <span class="text-[9px] font-black text-slate-400 uppercase">Mostrando</span>
                <span id="badge-count" class="text-sm font-black text-slate-800 ml-1">{{ $establecimientosMap->count() }}</span>
                <span class="text-[9px] font-black text-slate-400 uppercase ml-1">EESS</span>
            </div>
        </div>

    </div>
@endsection

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
    (function () {

        var map = L.map('mapa-progresion').setView([-14.07, -75.73], 9);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
            maxZoom: 19
        }).addTo(map);

        var establecimientos = @json($establecimientosMap);
        var markers = [];

        /* ── Configuración visual por etapa ── */
        var etapaConfig = {
            0: { color: '#94a3b8', size: 9,  label: 'Sin Inicio' },
            1: { color: '#3b82f6', size: 13, label: 'Implementado' },
            2: { color: '#f59e0b', size: 17, label: 'Con Asistencia' },
            3: { color: '#8b5cf6', size: 21, label: 'Con Monitoreo' },
            4: { color: '#22c55e', size: 26, label: 'Ciclo Completo' },
        };

        /* ── Popup HTML por establecimiento ── */
        function buildPopup(e) {
            var cfg = etapaConfig[e.etapa];

            /* Checkboxes de etapas */
            var check = function(ok, label) {
                return `<div class="flex items-center gap-2">
                    <span class="w-4 h-4 rounded-full flex items-center justify-center flex-shrink-0 ${ok ? 'bg-emerald-100 text-emerald-600' : 'bg-slate-100 text-slate-300'}">
                        ${ok ? '✓' : '○'}
                    </span>
                    <span class="text-[10px] font-semibold ${ok ? 'text-slate-700' : 'text-slate-400'}">${label}</span>
                </div>`;
            };

            /* Módulos implementados */
            var modulosHtml = '';
            if (e.modulos_impl && e.modulos_impl.length > 0) {
                modulosHtml = `<div class="mt-2 flex flex-wrap gap-1">` +
                    e.modulos_impl.map(function(m) {
                        return `<span class="text-[8px] font-bold bg-blue-50 text-blue-600 px-2 py-0.5 rounded-full border border-blue-100">${m}</span>`;
                    }).join('') +
                `</div>`;
            }

            /* Etiqueta de etapa */
            var colorClass = { 0: 'bg-slate-100 text-slate-600', 1: 'bg-blue-100 text-blue-700', 2: 'bg-amber-100 text-amber-700', 3: 'bg-violet-100 text-violet-700', 4: 'bg-emerald-100 text-emerald-700 font-black' };

            return `
            <div class="bg-white min-w-[240px] max-w-[280px]">
                <div class="px-4 pt-4 pb-3 border-b border-slate-100">
                    <h4 class="font-black text-slate-800 text-[13px] leading-tight">${e.nombre}</h4>
                    <p class="text-[10px] text-slate-400 uppercase tracking-wider mt-0.5">${e.distrito || ''} — ${e.provincia || ''}</p>
                    <span class="mt-2 inline-flex text-[9px] font-black px-2.5 py-1 rounded-full uppercase tracking-wider ${colorClass[e.etapa]}">
                        ${cfg.label}
                    </span>
                </div>
                <div class="px-4 py-3 space-y-1.5">
                    ${check(e.tiene_impl, 'Implementación' + (e.total_impl > 0 ? ' (' + e.total_impl + ' mód.)' : ''))}
                    ${modulosHtml}
                    ${check(e.tiene_asist, 'Asistencia Técnica' + (e.total_asistencias > 0 ? ' (' + e.total_asistencias + ' actas)' : ''))}
                    ${check(e.tiene_monitoreo, 'Monitoreo' + (e.total_monitoreos > 0 ? ' (' + e.total_monitoreos + ' actas)' : ''))}
                </div>
                ${e.etapa < 4 ? `<div class="px-4 pb-3"><p class="text-[9px] text-slate-400 bg-slate-50 px-2.5 py-1.5 rounded-lg">Siguiente: ${e.etapa === 0 ? '→ Necesita Implementación' : e.etapa === 1 ? '→ Programar Asistencia Técnica' : e.etapa === 2 ? '→ Programar Monitoreo' : '→ Monitoreo / Continuar'}</p></div>` : `<div class="px-4 pb-3"><p class="text-[9px] font-bold text-emerald-600 bg-emerald-50 px-2.5 py-1.5 rounded-lg">⭐ Ciclo completo alcanzado</p></div>`}
            </div>`;
        }

        /* ── Crear marcadores ── */
        establecimientos.forEach(function (e) {
            var lat = parseFloat(e.latitud);
            var lon = parseFloat(e.longitud);
            if (Math.abs(lat) > 180) lat /= 100000000;
            if (Math.abs(lon) > 180) lon /= 100000000;
            if (isNaN(lat) || isNaN(lon)) return;

            var cfg = etapaConfig[e.etapa];

            var m = L.circleMarker([lat, lon], {
                radius:      cfg.size / 2,
                fillColor:   cfg.color,
                color:       '#fff',
                weight:      e.etapa === 4 ? 3 : 2,
                opacity:     1,
                fillOpacity: 0.9,
                className:   e.etapa === 4 ? 'marker-completo' : ''
            }).addTo(map).bindPopup(buildPopup(e), { className: 'custom-popup', maxWidth: 300 });

            markers.push({
                marker:    m,
                id:        e.id,
                etapa:     e.etapa,
                provincia: e.provincia  || '',
                distrito:  e.distrito   || '',
                categoria: e.categoria  || '',
            });
        });

        /* ── FILTROS ── */
        var filtroEtapa     = '';
        var filtroProvincia = '';
        var filtroDistrito  = '';
        var filtroCategoria = '';
        var filtroEstId     = '';

        var badgeCount = document.getElementById('badge-count');

        function applyFilters() {
            var group      = L.featureGroup();
            var visibleCnt = 0;

            markers.forEach(function (m) {
                var matchEtapa = (filtroEtapa === '' || String(m.etapa) === filtroEtapa);
                var matchProv  = (filtroProvincia === '' || m.provincia === filtroProvincia);
                var matchDist  = (filtroDistrito === ''  || m.distrito  === filtroDistrito);
                var matchCat   = (filtroCategoria === '' || m.categoria  === filtroCategoria);
                var matchEst   = (filtroEstId === ''     || String(m.id) === filtroEstId);

                if (matchEtapa && matchProv && matchDist && matchCat && matchEst) {
                    if (!map.hasLayer(m.marker)) map.addLayer(m.marker);
                    group.addLayer(m.marker);
                    visibleCnt++;
                } else {
                    if (map.hasLayer(m.marker)) map.removeLayer(m.marker);
                }
            });

            badgeCount.textContent = visibleCnt;

            if (visibleCnt > 0) {
                if (filtroEstId !== '') {
                    markers.forEach(function (m) {
                        if (String(m.id) === filtroEstId) { map.setView(m.marker.getLatLng(), 15); m.marker.openPopup(); }
                    });
                } else if (group.getLayers().length > 0) {
                    var anyFilter = filtroEtapa || filtroProvincia || filtroDistrito || filtroCategoria;
                    if (anyFilter) map.fitBounds(group.getBounds(), { padding: [50, 50], maxZoom: 13 });
                }
            }
        }

        /* Botones de etapa */
        document.querySelectorAll('.btn-etapa').forEach(function (btn) {
            btn.addEventListener('click', function () {
                document.querySelectorAll('.btn-etapa').forEach(function(b) {
                    b.classList.remove('activo', 'bg-slate-800', 'text-white');
                });
                btn.classList.add('activo');
                filtroEtapa = btn.dataset.etapa;
                applyFilters();
            });
        });

        /* Selects geográficos */
        function updateDistritosyCategorias() {
            var selectDist = document.getElementById('filtro-distrito');
            var selectCat  = document.getElementById('filtro-categoria');
            var selectEst  = document.getElementById('filtro-establecimiento');

            var distSet = new Set(), catSet = new Set();
            establecimientos.forEach(function (e) {
                if (filtroProvincia === '' || e.provincia === filtroProvincia) {
                    if (e.distrito) distSet.add(e.distrito);
                    if (e.categoria) catSet.add(e.categoria);
                }
            });

            selectDist.innerHTML = '<option value="">Todos</option>';
            Array.from(distSet).sort().forEach(function (d) {
                selectDist.innerHTML += `<option value="${d}">${d}</option>`;
            });

            selectCat.innerHTML = '<option value="">Todas</option>';
            Array.from(catSet).sort().forEach(function (c) {
                selectCat.innerHTML += `<option value="${c}">${c}</option>`;
            });

            selectEst.innerHTML = '<option value="">Todos</option>';
            establecimientos.forEach(function (e) {
                var matchProv = (filtroProvincia === '' || e.provincia === filtroProvincia);
                var matchDist = (filtroDistrito === ''  || e.distrito  === filtroDistrito);
                var matchCat  = (filtroCategoria === '' || e.categoria  === filtroCategoria);
                if (matchProv && matchDist && matchCat) {
                    selectEst.innerHTML += `<option value="${e.id}">${e.nombre}</option>`;
                }
            });
        }

        document.getElementById('filtro-anio').addEventListener('change', function () {
            // Este filtro recarga la página porque afecta la consulta a base de datos
            window.location.href = "{{ route('usuario.dashboard.general') }}?anio=" + this.value;
        });

        document.getElementById('filtro-provincia').addEventListener('change', function () {
            filtroProvincia = this.value; filtroDistrito = ''; filtroCategoria = ''; filtroEstId = '';
            updateDistritosyCategorias(); applyFilters();
        });
        document.getElementById('filtro-distrito').addEventListener('change', function () {
            filtroDistrito = this.value; filtroEstId = '';
            updateDistritosyCategorias(); applyFilters();
        });
        document.getElementById('filtro-categoria').addEventListener('change', function () {
            filtroCategoria = this.value; filtroEstId = '';
            updateDistritosyCategorias(); applyFilters();
        });
        document.getElementById('filtro-establecimiento').addEventListener('change', function () {
            filtroEstId = this.value; applyFilters();
        });

    })();
    </script>
@endpush
