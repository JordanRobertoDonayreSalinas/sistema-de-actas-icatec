@extends('layouts.usuario')

@section('title', 'Mapa de Monitoreo')

{{-- HEADER SUPERIOR --}}
@section('header-content')
    <h1 class="text-xl font-bold text-slate-800 tracking-tight">
        Bienvenido de nuevo, {{ Auth::user()->apellido_paterno }} {{ Auth::user()->name }}
    </h1>
    <div class="flex items-center gap-2 text-xs text-slate-500 mt-0.5">
        <span>Plataforma</span>
        <span class="text-slate-300">•</span>
        <span>Mapa de Monitoreo</span>
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



        {{-- 2. MAPA DE ESTABLECIMIENTOS --}}
        <div>
            {{-- Encabezado con título y contadores --}}
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-4">
                <h3 class="text-sm font-bold text-slate-500 uppercase tracking-widest">Mapa de Monitoreo — Región Ica
                </h3>
                <div class="flex items-center gap-3 flex-wrap">
                    {{-- Contador total --}}
                    <span class="bg-slate-100 text-slate-600 text-xs font-bold px-3 py-1.5 rounded-full">
                        Total: <span id="main-count-total">{{ $establecimientosMap->count() }}</span>
                    </span>
                    {{-- Con monitoreo --}}
                    <span
                        class="bg-emerald-100 text-emerald-700 text-xs font-bold px-3 py-1.5 rounded-full flex items-center gap-1.5">
                        <span class="inline-block w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                        Con monitoreo: <span
                            id="main-count-con">{{ $establecimientosMap->where('has_monitoreo', true)->count() }}</span>
                    </span>
                    {{-- Sin monitoreo --}}
                    <span
                        class="bg-red-100 text-red-600 text-xs font-bold px-3 py-1.5 rounded-full flex items-center gap-1.5">
                        <span class="inline-block w-2.5 h-2.5 rounded-full bg-red-500"></span>
                        Sin monitoreo: <span
                            id="main-count-sin">{{ $establecimientosMap->where('has_monitoreo', false)->count() }}</span>
                    </span>
                </div>
            </div>

            {{-- Filtros combinados --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-3">
                {{-- Filtro por provincia --}}
                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Provincia:</label>
                    <select id="filtro-provincia"
                        class="text-sm font-medium text-slate-700 border border-slate-200 rounded-xl px-4 py-2 bg-white shadow-sm focus:outline-none focus:ring-2 focus:ring-emerald-400 transition w-full">
                        <option value="">Todas las provincias</option>
                        @foreach($provincias as $prov)
                            <option value="{{ $prov }}">{{ $prov }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Filtro por distrito --}}
                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Distrito:</label>
                    <select id="filtro-distrito"
                        class="text-sm font-medium text-slate-700 border border-slate-200 rounded-xl px-4 py-2 bg-white shadow-sm focus:outline-none focus:ring-2 focus:ring-emerald-400 transition w-full">
                        <option value="">Todos los distritos</option>
                        @foreach($distritos as $dist)
                            <option value="{{ $dist }}">{{ $dist }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Filtro por categoría --}}
                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Categoría:</label>
                    <select id="filtro-categoria"
                        class="text-sm font-medium text-slate-700 border border-slate-200 rounded-xl px-4 py-2 bg-white shadow-sm focus:outline-none focus:ring-2 focus:ring-emerald-400 transition w-full">
                        <option value="">Todas las categorías</option>
                        @foreach($categorias as $cat)
                            <option value="{{ $cat }}">{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Filtro por establecimiento --}}
                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Establecimiento:</label>
                    <select id="filtro-establecimiento"
                        class="text-sm font-medium text-slate-700 border border-slate-200 rounded-xl px-4 py-2 bg-white shadow-sm focus:outline-none focus:ring-2 focus:ring-emerald-400 transition w-full">
                        <option value="">Todos los establecimientos</option>
                        @foreach($establecimientosMap as $est)
                            <option value="{{ $est->id }}">{{ $est->nombre }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Contador dinámico visible al filtrar --}}
            <div class="mb-4">
                <span id="contador-filtro"
                    class="text-xs text-slate-500 font-medium bg-slate-50 px-3 py-1 rounded-lg border border-slate-100 hidden"></span>
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
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                maxZoom: 19
            }).addTo(map);

            var iconSin = {
                radius: 6,
                fillColor: "#ef4444",
                color: "#fff",
                weight: 2,
                opacity: 1,
                fillOpacity: 0.9
            };

            var iconCon = {
                radius: 8,
                fillColor: "#10b981",
                color: "#fff",
                weight: 2,
                opacity: 1,
                fillOpacity: 0.9
            };

            var establecimientos = @json($establecimientosMap);

            // Guardar marcadores con metadata para poder filtrar
            var markers = [];

            establecimientos.forEach(function (e) {
                var lat = parseFloat(e.latitud);
                var lon = parseFloat(e.longitud);

                // Normalización si los datos vienen escalados por 10^8 (ej: -1415234688)
                if (Math.abs(lat) > 180) lat = lat / 100000000;
                if (Math.abs(lon) > 180) lon = lon / 100000000;

                if (!isNaN(lat) && !isNaN(lon)) {
                    var tieneMonitoreo = e.has_monitoreo;
                    var options = tieneMonitoreo ? iconCon : iconSin;
                    var badgeMonitoreo = tieneMonitoreo
                        ? '<span style="background:#d1fae5;color:#065f46;font-size:10px;padding:1px 7px;border-radius:9999px;display:inline-block;margin-top:5px;font-weight:600;">✔ Con monitoreo</span>'
                        : '<span style="background:#fee2e2;color:#991b1b;font-size:10px;padding:1px 7px;border-radius:9999px;display:inline-block;margin-top:5px;">Sin monitoreo</span>';

                    var marker = L.circleMarker([lat, lon], options)
                        .addTo(map)
                        .bindPopup(
                            '<div style="font-family:sans-serif;min-width:180px;padding:5px;">' +
                            '<div style="margin-bottom:8px;border-bottom:1px solid #f1f5f9;padding-bottom:8px;">' +
                            '<b style="font-size:14px;color:#1e293b;display:block;">' + e.nombre + '</b>' +
                            '<span style="color:#64748b;font-size:11px;font-weight:500;">' + (e.distrito || '') + (e.provincia ? ' — ' + e.provincia : '') + '</span>' +
                            '</div>' +
                            (e.categoria ? '<div style="margin-bottom:8px;"><span style="background:#f0f9ff;color:#0369a1;font-size:10px;padding:2px 8px;border-radius:6px;font-weight:700;border:1px solid #bae6fd;">' + e.categoria + '</span></div>' : '') +
                            '<div>' + badgeMonitoreo + '</div>' +
                            '</div>'
                        );

                    markers.push({
                        id: e.id,
                        marker: marker,
                        provincia: (e.provincia || ''),
                        distrito: (e.distrito || ''),
                        categoria: (e.categoria || ''),
                        has_monitoreo: tieneMonitoreo
                    });
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

            // Filtros
            var selectProvincia = document.getElementById('filtro-provincia');
            var selectDistrito = document.getElementById('filtro-distrito');
            var selectCategoria = document.getElementById('filtro-categoria');
            var selectEstablecimiento = document.getElementById('filtro-establecimiento');
            var contadorFiltro = document.getElementById('contador-filtro');

            // Contadores principales
            var mainCountTotal = document.getElementById('main-count-total');
            var mainCountCon = document.getElementById('main-count-con');
            var mainCountSin = document.getElementById('main-count-sin');

            // Actualizar opciones de filtros (Distrito, Categoría y Establecimiento) de forma dinámica
            function updateOptions() {
                var prov = selectProvincia.value;
                var currentDist = selectDistrito.value;
                var currentCat = selectCategoria.value;
                var currentEst = selectEstablecimiento.value;

                // 1. Actualizar DISTRITOS (según Provincia)
                selectDistrito.innerHTML = '<option value="">Todos los distritos</option>';
                var distritosVisibles = new Set();
                establecimientos.forEach(function (e) {
                    if (prov === '' || e.provincia === prov) {
                        if (e.distrito) distritosVisibles.add(e.distrito);
                    }
                });
                Array.from(distritosVisibles).sort().forEach(function (d) {
                    var option = document.createElement('option');
                    option.value = d;
                    option.textContent = d;
                    if (d === currentDist) option.selected = true;
                    selectDistrito.appendChild(option);
                });

                // 2. Actualizar CATEGORÍAS (según Provincia y Distrito)
                var newDist = selectDistrito.value;
                selectCategoria.innerHTML = '<option value="">Todas las categorías</option>';
                var categoriasVisibles = new Set();
                establecimientos.forEach(function (e) {
                    var matchProv = (prov === '' || e.provincia === prov);
                    var matchDist = (newDist === '' || e.distrito === newDist);
                    if (matchProv && matchDist) {
                        if (e.categoria) categoriasVisibles.add(e.categoria);
                    }
                });
                Array.from(categoriasVisibles).sort().forEach(function (c) {
                    var option = document.createElement('option');
                    option.value = c;
                    option.textContent = c;
                    if (c === currentCat) option.selected = true;
                    selectCategoria.appendChild(option);
                });

                // 3. Actualizar ESTABLECIMIENTOS (según los otros 3 filtros)
                var newCat = selectCategoria.value;
                selectEstablecimiento.innerHTML = '<option value="">Todos los establecimientos</option>';
                establecimientos.forEach(function (e) {
                    var matchProv = (prov === '' || e.provincia === prov);
                    var matchDist = (newDist === '' || e.distrito === newDist);
                    var matchCat = (newCat === '' || e.categoria === newCat);
                    if (matchProv && matchDist && matchCat) {
                        var option = document.createElement('option');
                        option.value = e.id;
                        option.textContent = e.nombre;
                        if (String(e.id) === String(currentEst)) option.selected = true;
                        selectEstablecimiento.appendChild(option);
                    }
                });
            }

            function applyFilters() {
                var prov = selectProvincia.value;
                var dist = selectDistrito.value;
                var cat = selectCategoria.value;
                var estId = selectEstablecimiento.value;

                var visible = 0, conMon = 0, sinMon = 0;
                var group = L.featureGroup();

                markers.forEach(function (m) {
                    var matchProv = (prov === '' || m.provincia === prov);
                    var matchDist = (dist === '' || m.distrito === dist);
                    var matchCat = (cat === '' || m.categoria === cat);
                    var matchEst = (estId === '' || String(m.id) === String(estId));

                    var mostrar = matchProv && matchDist && matchCat && matchEst;

                    if (mostrar) {
                        if (!map.hasLayer(m.marker)) map.addLayer(m.marker);
                        group.addLayer(m.marker);
                        visible++;
                        m.has_monitoreo ? conMon++ : sinMon++;
                    } else {
                        if (map.hasLayer(m.marker)) map.removeLayer(m.marker);
                    }
                });

                // Actualizar contadores principales
                mainCountTotal.textContent = visible;
                mainCountCon.textContent = conMon;
                mainCountSin.textContent = sinMon;

                // Auto-enfocar si hay marcadores visibles y algún filtro activo
                if (visible > 0 && (prov !== '' || dist !== '' || cat !== '' || estId !== '')) {
                    if (estId !== '') {
                        // Si es un solo establecimiento, hacemos más zoom
                        markers.forEach(function (m) {
                            if (String(m.id) === String(estId)) {
                                map.setView(m.marker.getLatLng(), 15);
                                m.marker.openPopup();
                            }
                        });
                    } else {
                        map.fitBounds(group.getBounds(), { padding: [50, 50], maxZoom: 13 });
                    }
                } else if (prov === '' && dist === '' && cat === '' && estId === '') {
                    map.setView([-14.07, -75.73], 9);
                }

                if (prov !== '' || dist !== '' || cat !== '' || estId !== '') {
                    contadorFiltro.innerHTML = `<b>${visible}</b> establecimientos encontrados con los filtros aplicados`;
                    contadorFiltro.classList.remove('hidden');
                } else {
                    contadorFiltro.classList.add('hidden');
                }
            }

            selectProvincia.addEventListener('change', function () {
                updateOptions();
                applyFilters();
            });

            selectDistrito.addEventListener('change', function () {
                updateOptions();
                applyFilters();
            });

            selectCategoria.addEventListener('change', function () {
                updateOptions();
                applyFilters();
            });

            selectEstablecimiento.addEventListener('change', applyFilters);

        })();
    </script>
@endpush