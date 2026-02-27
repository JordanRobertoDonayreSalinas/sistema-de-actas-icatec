@extends('layouts.usuario')

@section('title', 'Mapa de Asistencias Técnicas')

@section('header-content')
    <h1 class="text-xl font-bold text-slate-800 tracking-tight">
        Mapa de Asistencias Técnicas
    </h1>
    <div class="flex items-center gap-2 text-xs text-slate-500 mt-0.5">
        <span>Plataforma</span>
        <span class="text-slate-300">•</span>
        <span>Mapa de Asistencias Técnicas</span>
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

        /* Animación de pulsación para intensidad alta */
        @keyframes custom-pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(126, 34, 206, 0.4);
            }

            70% {
                box-shadow: 0 0 0 15px rgba(126, 34, 206, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(126, 34, 206, 0);
            }
        }

        .high-intensity-pulse {
            animation: custom-pulse 2s infinite;
            border-radius: 50%;
        }

        .custom-popup .leaflet-popup-content-wrapper {
            border-radius: 12px;
            padding: 0;
            overflow: hidden;
        }

        .custom-popup .leaflet-popup-content {
            margin: 0;
        }
    </style>
@endpush

@section('content')
    <div class="max-w-7xl mx-auto space-y-6">
        {{-- PANEL DE CONTROL --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
            <div
                class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 bg-white p-6 rounded-3xl shadow-sm border border-slate-100 mb-8 mt-4">
                <div class="space-y-1">
                    <h3 class="text-sm font-black text-slate-600 uppercase tracking-widest flex items-center gap-2">
                        <i data-lucide="map-pin" class="w-4 h-4 text-indigo-500"></i>
                        Intensidad de Asistencia Técnica
                    </h3>
                    <div class="flex items-center gap-3 flex-wrap pt-1">
                        <span
                            class="bg-slate-100 text-slate-600 text-[10px] font-black px-3 py-1.5 rounded-full uppercase tracking-tight">
                            EESS: <span id="main-count-eess">{{ $establecimientosMap->count() }}</span>
                        </span>
                        <span
                            class="bg-indigo-100 text-indigo-700 text-[10px] font-black px-3 py-1.5 rounded-full uppercase tracking-tight">
                            Total Asistencias: <span
                                id="main-count-total">{{ $establecimientosMap->sum('total_asistencias') }}</span>
                        </span>
                    </div>
                </div>

                <div class="flex items-center gap-4 flex-wrap w-full md:w-auto">
                    <div class="flex flex-col sm:flex-row sm:items-center gap-2">
                        <label class="text-xs font-bold text-slate-500 uppercase">Provincia:</label>
                        <select id="filtro-provincia"
                            class="text-sm border-slate-200 rounded-xl px-4 py-2 focus:ring-indigo-500 transition">
                            <option value="">Todas</option>
                            @foreach($provincias as $prov)
                                <option value="{{ $prov }}">{{ $prov }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex flex-col sm:flex-row sm:items-center gap-2">
                        <label class="text-xs font-bold text-slate-500 uppercase">Distrito:</label>
                        <select id="filtro-distrito"
                            class="text-sm border-slate-200 rounded-xl px-4 py-2 focus:ring-indigo-500 transition">
                            <option value="">Todos</option>
                            @foreach($distritos as $dist)
                                <option value="{{ $dist }}">{{ $dist }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex flex-col sm:flex-row sm:items-center gap-2">
                        <label class="text-xs font-bold text-slate-500 uppercase">Categoría:</label>
                        <select id="filtro-categoria"
                            class="text-sm border-slate-200 rounded-xl px-4 py-2 focus:ring-indigo-500 transition">
                            <option value="">Todas</option>
                            @foreach($categorias as $cat)
                                <option value="{{ $cat }}">{{ $cat }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex flex-col sm:flex-row sm:items-center gap-2">
                        <label class="text-xs font-bold text-slate-500 uppercase">Establecimiento:</label>
                        <select id="filtro-establecimiento"
                            class="text-sm border-slate-200 rounded-xl px-4 py-2 focus:ring-indigo-500 transition max-w-[200px]">
                            <option value="">Todos</option>
                            @foreach($establecimientosMap as $e)
                                <option value="{{ $e->id }}">{{ $e->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- Contador dinámico visible al filtrar --}}
            <div class="mt-4">
                <span id="contador-filtro"
                    class="text-xs text-slate-500 font-medium bg-slate-50 px-3 py-1 rounded-lg border border-slate-100 hidden"></span>
            </div>
        </div>

        {{-- MAPA --}}
        <div class="relative rounded-2xl overflow-hidden shadow-xl border border-slate-200 bg-slate-100"
            style="height: 600px;">
            <div id="mapa-asistencias" class="h-full w-full"></div>

            {{-- LEYENDA FLOTANTE --}}
            <div
                class="absolute bottom-6 right-6 z-[1000] bg-white/90 backdrop-blur-md p-4 rounded-2xl shadow-2xl border border-white/20">
                <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Escala de Asistencias</h4>
                <div class="space-y-2">
                    <div class="flex items-center gap-3">
                        <span class="w-4 h-4 rounded-full bg-slate-400 border-2 border-white shadow-sm"></span>
                        <span class="text-xs font-semibold text-slate-600">0 asistencias</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="w-4 h-4 rounded-full bg-blue-500 border-2 border-white shadow-sm"></span>
                        <span class="text-xs font-semibold text-slate-600">1 - 5 asistencias</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="w-5 h-5 rounded-full bg-indigo-600 border-2 border-white shadow-sm"></span>
                        <span class="text-xs font-semibold text-slate-600">6 - 15 asistencias</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <span
                            class="w-6 h-6 rounded-full bg-purple-700 border-2 border-white shadow-sm animate-pulse"></span>
                        <span class="text-xs font-bold text-slate-700">+15 asistencias</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        (function () {
            var map = L.map('mapa-asistencias').setView([-14.07, -75.73], 9);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                maxZoom: 19
            }).addTo(map);

            var establecimientos = @json($establecimientosMap);
            var markers = [];

            // Función para determinar estilo según cantidad
            function getMarkerStyle(total) {
                if (total === 0) return { color: '#94a3b8', size: 10, pulse: false };
                if (total <= 5) return { color: '#3b82f6', size: 14, pulse: false };
                if (total <= 15) return { color: '#4f46e5', size: 18, pulse: false };
                return { color: '#7e22ce', size: 24, pulse: true };
            }

            establecimientos.forEach(function (e) {
                var lat = parseFloat(e.latitud);
                var lon = parseFloat(e.longitud);

                // Normalización si los datos vienen escalados por 10^8
                if (Math.abs(lat) > 180) lat = lat / 100000000;
                if (Math.abs(lon) > 180) lon = lon / 100000000;

                if (isNaN(lat) || isNaN(lon)) return;

                var style = getMarkerStyle(e.total_asistencias);

                var className = style.pulse ? 'high-intensity-pulse' : '';

                var marker = L.circleMarker([lat, lon], {
                    radius: style.size / 2,
                    fillColor: style.color,
                    color: '#fff',
                    weight: 2,
                    opacity: 1,
                    fillOpacity: 0.9,
                    className: className
                })
                    .addTo(map)
                    .bindPopup(`
                                                    <div class="p-4 min-w-[210px] bg-white">
                                                        <h4 class="font-black text-slate-800 text-sm mb-1 leading-tight">${e.nombre}</h4>
                                                        <p class="text-[11px] font-medium text-slate-500 mb-3 uppercase tracking-wider">${e.distrito} — ${e.provincia}</p>
                                                        <div class="flex items-center justify-between bg-indigo-50 px-3 py-2.5 rounded-xl border border-indigo-100/50">
                                                            <span class="text-[9px] font-black text-indigo-400 uppercase tracking-widest">Total Asistencias</span>
                                                            <span class="text-base font-black text-indigo-600">${e.total_asistencias}</span>
                                                        </div>
                                                    </div>
                                                `, { className: 'custom-popup' });

                markers.push({
                    id: e.id,
                    marker: marker,
                    provincia: e.provincia,
                    distrito: (e.distrito || ''),
                    categoria: (e.categoria || '')
                });
            });

            // Filtros
            var selectProv = document.getElementById('filtro-provincia');
            var selectDist = document.getElementById('filtro-distrito');
            var selectCat = document.getElementById('filtro-categoria');
            var selectEst = document.getElementById('filtro-establecimiento');
            var contadorFiltro = document.getElementById('contador-filtro');

            // Contadores principales
            var mainCountEess = document.getElementById('main-count-eess');
            var mainCountTotal = document.getElementById('main-count-total');

            // Actualizar opciones de filtros (Distrito, Categoría y Establecimiento) de forma dinámica
            function updateOptions() {
                var prov = selectProv.value;
                var currentDist = selectDist.value;
                var currentCat = selectCat.value;
                var currentEst = selectEst.value;

                // 1. Actualizar DISTRITOS (según Provincia)
                selectDist.innerHTML = '<option value="">Todos</option>';
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
                    selectDist.appendChild(option);
                });

                // 2. Actualizar CATEGORÍAS (según Provincia y Distrito)
                var newDist = selectDist.value;
                selectCat.innerHTML = '<option value="">Todas</option>';
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
                    selectCat.appendChild(option);
                });

                // 3. Actualizar ESTABLECIMIENTOS (según los otros 3 filtros)
                var newCat = selectCat.value;
                selectEst.innerHTML = '<option value="">Todos</option>';
                establecimientos.forEach(function (e) {
                    var matchProv = (prov === '' || e.provincia === prov);
                    var matchDist = (newDist === '' || e.distrito === newDist);
                    var matchCat = (newCat === '' || e.categoria === newCat);
                    if (matchProv && matchDist && matchCat) {
                        var option = document.createElement('option');
                        option.value = e.id;
                        option.textContent = e.nombre;
                        if (String(e.id) === String(currentEst)) option.selected = true;
                        selectEst.appendChild(option);
                    }
                });
            }

            function applyFilters() {
                var provVal = selectProv.value;
                var distVal = selectDist.value;
                var catVal = selectCat.value;
                var estId = selectEst.value;

                var group = L.featureGroup();
                var countEess = 0;
                var countTotal = 0;

                markers.forEach(function (m) {
                    var matchProv = (provVal === '' || m.provincia === provVal);
                    var matchDist = (distVal === '' || m.distrito === distVal);
                    var matchCat = (catVal === '' || m.categoria === catVal);
                    var matchEst = (estId === '' || String(m.id) === String(estId));

                    if (matchProv && matchDist && matchCat && matchEst) {
                        if (!map.hasLayer(m.marker)) map.addLayer(m.marker);
                        group.addLayer(m.marker);
                        countEess++;

                        // Necesitamos el total_asistencias que está en el objeto original e
                        var estData = establecimientos.find(e => String(e.id) === String(m.id));
                        if (estData) countTotal += (estData.total_asistencias || 0);

                    } else {
                        if (map.hasLayer(m.marker)) map.removeLayer(m.marker);
                    }
                });

                // Actualizar contadores principales
                mainCountEess.textContent = countEess;
                mainCountTotal.textContent = countTotal;

                if (countEess > 0 && (provVal !== '' || distVal !== '' || catVal !== '' || estId !== '')) {
                    if (estId !== '') {
                        markers.forEach(function (m) {
                            if (String(m.id) === String(estId)) {
                                map.setView(m.marker.getLatLng(), 15);
                                m.marker.openPopup();
                            }
                        });
                    } else {
                        map.fitBounds(group.getBounds(), { padding: [50, 50], maxZoom: 13 });
                    }
                } else if (provVal === '' && distVal === '' && catVal === '' && estId === '') {
                    map.setView([-14.07, -75.73], 9);
                }

                if (provVal !== '' || distVal !== '' || catVal !== '' || estId !== '') {
                    contadorFiltro.innerHTML = `<b>${countEess}</b> establecimientos encontrados con los filtros aplicados`;
                    contadorFiltro.classList.remove('hidden');
                } else {
                    contadorFiltro.classList.add('hidden');
                }
            }

            selectProv.addEventListener('change', function () {
                updateOptions();
                applyFilters();
            });

            selectDist.addEventListener('change', function () {
                updateOptions();
                applyFilters();
            });

            selectCat.addEventListener('change', function () {
                updateOptions();
                applyFilters();
            });

            selectEst.addEventListener('change', applyFilters);
        })();
    </script>
@endpush