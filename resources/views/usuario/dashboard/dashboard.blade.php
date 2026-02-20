@extends('layouts.usuario')

@section('title', 'Dashboard')

{{-- HEADER SUPERIOR --}}
@section('header-content')
    <h1 class="text-xl font-bold text-slate-800 tracking-tight">
        Bienvenido de nuevo, {{ Auth::user()->apellido_paterno }} {{ Auth::user()->name }}
    </h1>
    <div class="flex items-center gap-2 text-xs text-slate-500 mt-0.5">
        <span>Plataforma</span>
        <span class="text-slate-300">•</span>
        <span>Dashboard</span>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        /* Fix Tailwind CSS reset que rompe los tiles de Leaflet */
        .leaflet-container img.leaflet-tile {
            max-width: none !important;
            display: inline !important;
        }

        .leaflet-container img {
            max-width: none !important;
        }
    </style>
@endpush

{{-- CONTENIDO DEL DASHBOARD --}}
@section('content')

    <div class="max-w-7xl mx-auto space-y-8">

        {{-- 1. SECCIÓN MÉTRICAS --}}
        <div>
            <h3 class="text-sm font-bold text-slate-500 uppercase tracking-widest mb-4">Estadística</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                {{-- POSICIÓN 1: TOTAL ACTAS (VERDE) --}}
                <div
                    class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 p-8 text-white shadow-xl shadow-emerald-500/10 group transition-transform hover:scale-[1.01]">
                    <div
                        class="absolute -right-10 -top-10 h-40 w-40 rounded-full bg-white/10 blur-3xl transition-all group-hover:bg-white/20">
                    </div>

                    <div class="relative z-10">
                        <div class="flex items-start justify-between mb-4">
                            <div>
                                <p class="text-emerald-100 font-medium tracking-wide text-sm uppercase">Total Actas</p>
                                <h3 class="mt-1 text-4xl font-extrabold tracking-tight">{{ $totalActas }}</h3>
                            </div>
                            <div class="rounded-xl bg-white/20 p-3 backdrop-blur-sm">
                                <i data-lucide="file-text" class="h-8 w-8 text-white"></i>
                            </div>
                        </div>
                        <div class="h-px w-full bg-white/20 mb-4"></div>

                        {{-- LISTA MENSUAL --}}
                        <div>
                            <p class="text-[10px] font-bold text-emerald-100 uppercase tracking-widest mb-3">Historial por
                                Mes</p>
                            <div class="h-40 overflow-y-auto custom-scroll pr-2 space-y-2">
                                @forelse($actasPorMes as $registro)
                                    <div
                                        class="flex justify-between items-center bg-black/10 rounded px-3 py-2 text-sm hover:bg-black/20 transition-colors">
                                        <span class="font-medium">{{ $registro['nombre_mes'] }}</span>
                                        <span
                                            class="bg-white text-teal-700 font-bold px-2 py-0.5 rounded text-xs min-w-[30px] text-center">
                                            {{ $registro['total'] }}
                                        </span>
                                    </div>
                                @empty
                                    <p class="text-sm text-emerald-100 italic">No hay registros aún.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                {{-- POSICIÓN 2: RANKING ESTABLECIMIENTOS (VIOLETA/INDIGO) --}}
                <div
                    class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-indigo-600 to-purple-600 p-8 text-white shadow-xl shadow-indigo-500/10 group transition-transform hover:scale-[1.01]">
                    <div
                        class="absolute -right-10 -top-10 h-40 w-40 rounded-full bg-white/10 blur-3xl transition-all group-hover:bg-white/20">
                    </div>

                    <div class="relative z-10 flex flex-col justify-between h-full">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <p class="text-indigo-100 font-medium tracking-wide text-sm uppercase">Ranking
                                    Establecimientos</p>
                                <h3 class="mt-1 text-2xl font-bold tracking-tight">Top Actividad</h3>
                            </div>
                            <div class="rounded-xl bg-white/20 p-3 backdrop-blur-sm">
                                <i data-lucide="building-2" class="h-8 w-8 text-white"></i>
                            </div>
                        </div>

                        <div class="h-px w-full bg-white/20 mb-4"></div>

                        {{-- LISTA TOP ESTABLECIMIENTOS --}}
                        <div>
                            <p class="text-[10px] font-bold text-indigo-100 uppercase tracking-widest mb-3">Mayor generación
                                de actas</p>
                            <div class="h-40 overflow-y-auto custom-scroll pr-2 space-y-2">
                                @if(isset($topEstablecimientos) && count($topEstablecimientos) > 0)
                                    @foreach($topEstablecimientos as $est)
                                        <div
                                            class="flex justify-between items-center bg-black/10 rounded px-3 py-2 text-sm hover:bg-black/20 transition-colors">
                                            <span class="font-medium truncate w-2/3" title="{{ $est->nombre }}">
                                                {{ $est->nombre }}
                                            </span>
                                            <div class="flex items-center gap-2">
                                                <span
                                                    class="bg-white text-purple-700 font-bold px-2 py-0.5 rounded text-xs min-w-[30px] text-center">
                                                    {{ $est->actas_count }}
                                                </span>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="flex flex-col items-center justify-center h-32 text-indigo-100/60">
                                        <i data-lucide="hospital" class="w-8 h-8 mb-2"></i>
                                        <p class="text-xs italic">Sin datos de establecimientos.</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 2. MAPA DE ESTABLECIMIENTOS --}}
        <div>
            {{-- Encabezado con título y contadores --}}
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-4">
                <h3 class="text-sm font-bold text-slate-500 uppercase tracking-widest">Mapa de Establecimientos — Región Ica
                </h3>
                <div class="flex items-center gap-3 flex-wrap">
                    {{-- Contador total --}}
                    <span class="bg-slate-100 text-slate-600 text-xs font-bold px-3 py-1.5 rounded-full">
                        Total: {{ $establecimientosMap->count() }}
                    </span>
                    {{-- Con monitoreo --}}
                    <span
                        class="bg-emerald-100 text-emerald-700 text-xs font-bold px-3 py-1.5 rounded-full flex items-center gap-1.5">
                        <span class="inline-block w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                        Con monitoreo: {{ $establecimientosMap->where('has_monitoreo', true)->count() }}
                    </span>
                    {{-- Sin monitoreo --}}
                    <span
                        class="bg-red-100 text-red-600 text-xs font-bold px-3 py-1.5 rounded-full flex items-center gap-1.5">
                        <span class="inline-block w-2.5 h-2.5 rounded-full bg-red-500"></span>
                        Sin monitoreo: {{ $establecimientosMap->where('has_monitoreo', false)->count() }}
                    </span>
                </div>
            </div>

            {{-- Filtro por provincia --}}
            <div class="flex items-center gap-3 mb-3">
                <label class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Filtrar por provincia:</label>
                <select id="filtro-provincia"
                    class="text-sm font-medium text-slate-700 border border-slate-200 rounded-xl px-4 py-2 bg-white shadow-sm focus:outline-none focus:ring-2 focus:ring-emerald-400 transition">
                    <option value="">Todas las provincias</option>
                    @foreach($provincias as $prov)
                        <option value="{{ $prov }}">{{ $prov }}</option>
                    @endforeach
                </select>
                {{-- Contador dinámico visible al filtrar --}}
                <span id="contador-filtro" class="text-xs text-slate-400 italic hidden"></span>
            </div>

            {{-- Mapa --}}
            <div class="rounded-2xl overflow-hidden shadow-xl border border-slate-200" style="height: 500px;">
                <div id="mapa-establecimientos" style="height: 100%; width: 100%;"></div>
            </div>
        </div>

    </div>

@endsection

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        (function () {
            var map = L.map('mapa-establecimientos').setView([-14.07, -75.73], 9);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
                maxZoom: 18,
            }).addTo(map);

            // Marcador rojo — sin actas de monitoreo
            var iconSin = L.divIcon({
                html: '<div style="background:#ef4444;width:13px;height:13px;border-radius:50%;border:2px solid white;box-shadow:0 1px 5px rgba(239,68,68,0.6);"></div>',
                className: '',
                iconSize: [13, 13],
                iconAnchor: [6, 6],
                popupAnchor: [0, -8]
            });

            // Marcador verde — con actas de monitoreo
            var iconCon = L.divIcon({
                html: '<div style="background:#10b981;width:16px;height:16px;border-radius:50%;border:2px solid white;box-shadow:0 1px 6px rgba(16,185,129,0.7);"></div>',
                className: '',
                iconSize: [16, 16],
                iconAnchor: [8, 8],
                popupAnchor: [0, -10]
            });

            var establecimientos = @json($establecimientosMap);

            // Guardar marcadores con metadata para poder filtrar
            var markers = [];

            establecimientos.forEach(function (e) {
                var lat = parseFloat(e.latitud);
                var lon = parseFloat(e.longitud);
                if (!isNaN(lat) && !isNaN(lon)) {
                    var tieneMonitoreo = e.has_monitoreo;
                    var icon = tieneMonitoreo ? iconCon : iconSin;
                    var badgeMonitoreo = tieneMonitoreo
                        ? '<span style="background:#d1fae5;color:#065f46;font-size:10px;padding:1px 7px;border-radius:9999px;display:inline-block;margin-top:5px;font-weight:600;">✔ Con monitoreo</span>'
                        : '<span style="background:#fee2e2;color:#991b1b;font-size:10px;padding:1px 7px;border-radius:9999px;display:inline-block;margin-top:5px;">Sin monitoreo</span>';

                    var marker = L.marker([lat, lon], { icon: icon })
                        .addTo(map)
                        .bindPopup(
                            '<div style="font-family:sans-serif;min-width:170px;">' +
                            '<b style="font-size:13px;">' + e.nombre + '</b><br>' +
                            '<span style="color:#6b7280;font-size:11px;">' + (e.distrito || '') + (e.provincia ? ' — ' + e.provincia : '') + '</span><br>' +
                            (e.categoria ? '<span style="background:#e0f2fe;color:#0369a1;font-size:10px;padding:1px 6px;border-radius:9999px;display:inline-block;margin-top:4px;">' + e.categoria + '</span><br>' : '') +
                            badgeMonitoreo +
                            '</div>'
                        );

                    markers.push({ marker: marker, provincia: (e.provincia || ''), has_monitoreo: tieneMonitoreo });
                }
            });

            // Leyenda del mapa
            var legend = L.control({ position: 'bottomright' });
            legend.onAdd = function () {
                var div = L.DomUtil.create('div');
                div.style.cssText = 'background:white;padding:10px 14px;border-radius:10px;box-shadow:0 2px 10px rgba(0,0,0,0.15);font-family:sans-serif;font-size:12px;line-height:1.8;';
                div.innerHTML =
                    '<div style="font-weight:700;margin-bottom:6px;color:#334155;font-size:11px;text-transform:uppercase;letter-spacing:.05em;">Leyenda</div>' +
                    '<div style="display:flex;align-items:center;gap:8px;"><span style="display:inline-block;width:14px;height:14px;border-radius:50%;background:#10b981;border:2px solid white;box-shadow:0 0 4px rgba(16,185,129,0.5);flex-shrink:0;"></span> Con monitoreo</div>' +
                    '<div style="display:flex;align-items:center;gap:8px;"><span style="display:inline-block;width:13px;height:13px;border-radius:50%;background:#ef4444;border:2px solid white;box-shadow:0 0 3px rgba(239,68,68,0.4);flex-shrink:0;"></span> Sin monitoreo</div>';
                return div;
            };
            legend.addTo(map);

            // Filtro por provincia
            var selectProvincia = document.getElementById('filtro-provincia');
            var contadorFiltro = document.getElementById('contador-filtro');

            selectProvincia.addEventListener('change', function () {
                var prov = this.value;
                var visible = 0, conMon = 0, sinMon = 0;

                markers.forEach(function (m) {
                    var mostrar = (prov === '' || m.provincia === prov);
                    if (mostrar) {
                        if (!map.hasLayer(m.marker)) map.addLayer(m.marker);
                        visible++;
                        m.has_monitoreo ? conMon++ : sinMon++;
                    } else {
                        if (map.hasLayer(m.marker)) map.removeLayer(m.marker);
                    }
                });

                if (prov !== '') {
                    contadorFiltro.textContent = visible + ' establecimiento(s) — ' + conMon + ' con monitoreo, ' + sinMon + ' sin monitoreo';
                    contadorFiltro.classList.remove('hidden');
                } else {
                    contadorFiltro.classList.add('hidden');
                }
            });
        })();
    </script>
@endpush