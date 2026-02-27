@extends('layouts.usuario')
@php
    use App\Helpers\ModuloHelper;
@endphp

@section('title', 'Reporte de Equipos de Cómputo')

@section('header-content')
    <div>
        <h1 class="text-2xl font-bold text-slate-800">📊 Reporte de Equipos de Cómputo</h1>
        <p class="text-sm text-slate-500 mt-1">Filtra y genera reportes de equipos registrados</p>
    </div>
@endsection

@section('content')
    <div class="max-w-7xl mx-auto space-y-6">

        {{-- Tarjeta de Filtros --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
            <div class="flex items-center gap-3 mb-6">
                <div
                    class="w-10 h-10 rounded-xl bg-gradient-to-br from-purple-500 to-indigo-600 flex items-center justify-center">
                    <i data-lucide="filter" class="w-5 h-5 text-white"></i>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-slate-800">Filtros de Búsqueda</h2>
                    <p class="text-xs text-slate-500">Selecciona los criterios para filtrar los equipos</p>
                </div>
            </div>

            <form method="GET" action="{{ route('usuario.reportes.equipos') }}" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4">
                    {{-- Fecha Inicio --}}
                    <div>
                        <label for="fecha_inicio" class="block text-sm font-semibold text-slate-700 mb-2">
                            <i data-lucide="calendar" class="w-4 h-4 inline mr-1"></i>
                            Fecha Inicio
                        </label>
                        <input type="date" id="fecha_inicio" name="fecha_inicio" value="{{ $fechaInicio }}"
                            class="w-full px-4 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all">
                    </div>

                    {{-- Fecha Fin --}}
                    <div>
                        <label for="fecha_fin" class="block text-sm font-semibold text-slate-700 mb-2">
                            <i data-lucide="calendar" class="w-4 h-4 inline mr-1"></i>
                            Fecha Fin
                        </label>
                        <input type="date" id="fecha_fin" name="fecha_fin" value="{{ $fechaFin }}"
                            class="w-full px-4 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all">
                    </div>

                    {{-- Establecimiento --}}
                    <div>
                        <label for="establecimiento_id" class="block text-sm font-semibold text-slate-700 mb-2">
                            <i data-lucide="building-2" class="w-4 h-4 inline mr-1"></i>
                            Establecimiento
                        </label>
                        <select id="establecimiento_id" name="establecimiento_id"
                            class="w-full px-4 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all">
                            <option value="">Todos</option>
                            @foreach($establecimientos as $est)
                                <option value="{{ $est->id }}" {{ request('establecimiento_id') == $est->id ? 'selected' : '' }}>
                                    {{ $est->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Provincia --}}
                    <div>
                        <label for="provincia" class="block text-sm font-semibold text-slate-700 mb-2">
                            <i data-lucide="map-pin" class="w-4 h-4 inline mr-1"></i>
                            Provincia
                        </label>
                        <select id="provincia" name="provincia"
                            class="w-full px-4 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all">
                            <option value="">Todas</option>
                            @foreach($provincias as $prov)
                                <option value="{{ $prov }}" {{ request('provincia') == $prov ? 'selected' : '' }}>
                                    {{ $prov }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Módulo --}}
                    <div>
                        <label for="modulo" class="block text-sm font-semibold text-slate-700 mb-2">
                            <i data-lucide="layers" class="w-4 h-4 inline mr-1"></i>
                            Módulo
                        </label>
                        <select id="modulo" name="modulo"
                            class="w-full px-4 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all">
                            <option value="">Todos</option>
                            @foreach($modulos as $key => $nombre)
                                <option value="{{ $key }}" {{ request('modulo') == $key ? 'selected' : '' }}>
                                    {{ $nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Filtro por Descripción --}}
                    <div>
                        <label for="descripcion" class="block text-sm font-semibold text-slate-700 mb-2">
                            <i data-lucide="file-text" class="w-4 h-4 inline-block mr-1"></i>
                            Descripción
                        </label>
                        <select id="descripcion" name="descripcion"
                            class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">
                            <option value="">Todas</option>
                            @foreach($descripciones as $descripcion)
                                <option value="{{ $descripcion }}" {{ request('descripcion') == $descripcion ? 'selected' : '' }}>
                                    {{ $descripcion }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Tipo --}}
                    <div>
                        <label for="tipo" class="block text-sm font-semibold text-slate-700 mb-2">
                            <i data-lucide="building-2" class="w-4 h-4 inline mr-1"></i>
                            Tipo
                        </label>
                        <select id="tipo" name="tipo"
                            class="w-full px-4 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all">
                            <option value="">Todos</option>
                            <option value="ESPECIALIZADO" {{ request('tipo') == 'ESPECIALIZADO' ? 'selected' : '' }}>
                                ESPECIALIZADO</option>
                            <option value="NO ESPECIALIZADO" {{ request('tipo') == 'NO ESPECIALIZADO' ? 'selected' : '' }}>NO
                                ESPECIALIZADO</option>
                        </select>
                    </div>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="submit"
                        class="flex items-center gap-2 px-6 py-2.5 bg-gradient-to-r from-purple-600 to-indigo-600 text-white font-semibold rounded-xl hover:shadow-lg hover:scale-105 transition-all">
                        <i data-lucide="search" class="w-4 h-4"></i>
                        Filtrar
                    </button>

                    <a href="{{ route('usuario.reportes.equipos') }}"
                        class="flex items-center gap-2 px-6 py-2.5 bg-slate-200 text-slate-700 font-semibold rounded-xl hover:bg-slate-300 transition-all">
                        <i data-lucide="x" class="w-4 h-4"></i>
                        Limpiar
                    </a>

                    @if($equipos->count() > 0)
                        <button type="button" onclick="generarPDF()"
                            class="flex items-center gap-2 px-6 py-2.5 bg-gradient-to-r from-red-600 to-pink-600 text-white font-semibold rounded-xl hover:shadow-lg hover:scale-105 transition-all ml-auto">
                            <i data-lucide="file-text" class="w-4 h-4"></i>
                            Generar PDF
                        </button>

                        <button type="button" onclick="exportarExcel()"
                            class="flex items-center gap-2 px-6 py-2.5 bg-gradient-to-r from-green-600 to-emerald-600 text-white font-semibold rounded-xl hover:shadow-lg hover:scale-105 transition-all">
                            <i data-lucide="file-spreadsheet" class="w-4 h-4"></i>
                            Exportar Excel
                        </button>
                    @endif
                </div>
            </form>
        </div>

        {{-- Resultados --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-6 border-b border-slate-200 bg-gradient-to-r from-purple-50 to-indigo-50">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-10 h-10 rounded-xl bg-gradient-to-br from-purple-500 to-indigo-600 flex items-center justify-center">
                            <i data-lucide="monitor" class="w-5 h-5 text-white"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-slate-800">Equipos Encontrados</h3>
                            <p class="text-xs text-slate-500">Total: {{ $equipos->total() }} equipos</p>
                        </div>
                    </div>
                </div>
            </div>

            @if($equipos->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-slate-50 border-b border-slate-200">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-bold text-slate-700 uppercase tracking-wider">Fecha</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-slate-700 uppercase tracking-wider">IPRESS</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-slate-700 uppercase tracking-wider">Establecimiento</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-slate-700 uppercase tracking-wider">Categoría</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-slate-700 uppercase tracking-wider">Tipo</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-slate-700 uppercase tracking-wider">Módulo</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-slate-700 uppercase tracking-wider">Conectividad</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-slate-700 uppercase tracking-wider">Fuente WiFi</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-slate-700 uppercase tracking-wider">Proveedor</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-slate-700 uppercase tracking-wider">Cant.</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-slate-700 uppercase tracking-wider">Descripción</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-slate-700 uppercase tracking-wider">Propio</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-slate-700 uppercase tracking-wider">Estado</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($equipos as $equipo)
                                <tr class="hover:bg-purple-50/50 transition-colors">
                                    <td class="px-4 py-3 text-sm text-slate-500">
                                        {{ $equipo->cabecera->fecha ? \Carbon\Carbon::parse($equipo->cabecera->fecha)->format('d/m/Y') : 'N/A' }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-slate-600 font-mono">
                                        {{ $equipo->cabecera->establecimiento->codigo ?? 'N/A' }}</td>
                                    <td class="px-4 py-3 text-sm text-slate-600">
                                        {{ $equipo->cabecera->establecimiento->nombre ?? 'N/A' }}</td>
                                    <td class="px-4 py-3 text-sm text-slate-600">
                                        {{ $equipo->cabecera->establecimiento->categoria ?? 'N/A' }}</td>
                                    <td class="px-4 py-3 text-sm text-slate-600">
                                        <span
                                            class="px-2 py-1 text-xs font-semibold rounded-full {{ ModuloHelper::getTipoEstablecimiento($equipo->cabecera->establecimiento) == 'ESPECIALIZADO' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-700' }}">
                                            {{ ModuloHelper::getTipoEstablecimiento($equipo->cabecera->establecimiento) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-slate-700 font-medium">{{ ModuloHelper::getNombreAmigable($equipo->modulo) ?? 'N/A' }}</td>
                                    @php
                                        $conectividad = ModuloHelper::getConectividadActa($equipo->cabecera);
                                    @endphp
                                    <td class="px-4 py-3 text-sm">
                                        <span class="px-2 py-0.5 text-xs font-medium rounded bg-slate-100 text-slate-600">
                                            {{ $conectividad['tipo'] }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-sm">
                                        <span class="px-2 py-0.5 text-xs font-medium rounded bg-slate-100 text-slate-600">
                                            {{ $conectividad['fuente'] }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-sm">
                                        <span class="px-2 py-0.5 text-xs font-medium rounded bg-slate-100 text-slate-600">
                                            {{ $conectividad['operador'] }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-slate-700 font-semibold">{{ $equipo->cantidad ?? 0 }}</td>
                                    <td class="px-4 py-3 text-sm text-slate-600">{{ $equipo->descripcion ?? 'N/A' }}</td>
                                    <td class="px-4 py-3 text-sm text-slate-600">{{ $equipo->propio ?? 'N/A' }}</td>
                                    <td class="px-4 py-3">
                                        <span
                                            class="px-2 py-1 text-xs font-semibold rounded-full {{ $equipo->estado == 'Operativo' ? 'bg-green-100 text-green-700' : ($equipo->estado == 'Inoperativo' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                                            {{ $equipo->estado ?? 'N/A' }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Paginación --}}
                <div class="p-4 border-t border-slate-200">
                    {{ $equipos->appends(request()->query())->links() }}
                </div>
            @else
                <div class="p-12 text-center">
                    <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-slate-100 flex items-center justify-center">
                        <i data-lucide="inbox" class="w-10 h-10 text-slate-400"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-slate-700 mb-2">No se encontraron equipos</h3>
                    <p class="text-sm text-slate-500">Intenta ajustar los filtros de búsqueda</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Formulario oculto para generar PDF --}}
    <form id="pdfForm" method="POST" action="{{ route('usuario.reportes.equipos.pdf') }}" style="display: none;">
        @csrf
        <input type="hidden" name="fecha_inicio" value="{{ $fechaInicio }}">
        <input type="hidden" name="fecha_fin" value="{{ $fechaFin }}">
        <input type="hidden" name="establecimiento_id" value="{{ request('establecimiento_id') }}">
        <input type="hidden" name="provincia" value="{{ request('provincia') }}">
        <input type="hidden" name="modulo" value="{{ request('modulo') }}">
        <input type="hidden" name="tipo" value="{{ request('tipo') }}">
    </form>

    {{-- Formulario oculto para exportar Excel --}}
    <form id="excelForm" method="POST" action="{{ route('usuario.reportes.equipos.excel') }}" style="display: none;">
        @csrf
        <input type="hidden" name="fecha_inicio" value="{{ $fechaInicio }}">
        <input type="hidden" name="fecha_fin" value="{{ $fechaFin }}">
        <input type="hidden" name="establecimiento_id" value="{{ request('establecimiento_id') }}">
        <input type="hidden" name="provincia" value="{{ request('provincia') }}">
        <input type="hidden" name="modulo" value="{{ request('modulo') }}">
        <input type="hidden" name="tipo" value="{{ request('tipo') }}">
    </form>
@endsection

@push('scripts')
    <script>
        // Inicializar Lucide icons
        lucide.createIcons();

        // Función para generar PDF
        function generarPDF() {
            document.getElementById('pdfForm').submit();
        }

        // Función para exportar a Excel
        function exportarExcel() {
            document.getElementById('excelForm').submit();
        }

        // ===== FILTROS DINÁMICOS =====
        const tipoSelect = document.getElementById('tipo');
        const provinciaSelect = document.getElementById('provincia');
        const establecimientoSelect = document.getElementById('establecimiento_id');
        const moduloSelect = document.getElementById('modulo');
        const descripcionSelect = document.getElementById('descripcion');

        // Guardar valores originales
        const valoresOriginales = {
            tipo: '{{ request('tipo') }}',
            provincia: '{{ request('provincia') }}',
            establecimiento: '{{ request('establecimiento_id') }}',
            modulo: '{{ request('modulo') }}',
            descripcion: '{{ request('descripcion') }}'
        };

        // Cuando cambia el tipo
        tipoSelect.addEventListener('change', function() {
            const tipo = this.value;
            
            // Resetear valores dependientes
            provinciaSelect.value = '';
            establecimientoSelect.value = '';
            moduloSelect.value = '';
            descripcionSelect.value = '';
            
            // Actualizar provincias
            actualizarProvincias(tipo);
            
            // Actualizar establecimientos
            actualizarEstablecimientos(tipo, '');
            
            // Actualizar módulos
            actualizarModulos(tipo, '', '');
            
            // Actualizar descripciones
            actualizarDescripciones(tipo, '', '', '');
        });

        // Cuando cambia la provincia
        provinciaSelect.addEventListener('change', function() {
            const tipo = tipoSelect.value;
            const provincia = this.value;

            // Resetear valores dependientes
            establecimientoSelect.value = '';
            moduloSelect.value = '';
            descripcionSelect.value = '';
            
            // Actualizar establecimientos
            actualizarEstablecimientos(tipo, provincia);
            
            // Actualizar módulos
            actualizarModulos(tipo, provincia, '');
            
            // Actualizar descripciones
            actualizarDescripciones(tipo, provincia, '', '');
        });

        // Cuando cambia el establecimiento
        establecimientoSelect.addEventListener('change', function () {
            const tipo = tipoSelect.value;
            const provincia = provinciaSelect.value;
            const establecimiento = this.value;

            // Resetear módulo y descripción
            moduloSelect.value = '';
            descripcionSelect.value = '';
            
            // Actualizar módulos
            actualizarModulos(tipo, provincia, establecimiento);
            
            // Actualizar descripciones
            actualizarDescripciones(tipo, provincia, establecimiento, '');
        });

        // Cuando cambia el módulo
        moduloSelect.addEventListener('change', function() {
            const tipo = tipoSelect.value;
            const provincia = provinciaSelect.value;
            const establecimiento = establecimientoSelect.value;
            const modulo = this.value;
            
            // Resetear descripción
            descripcionSelect.value = '';
            
            // Actualizar descripciones
            actualizarDescripciones(tipo, provincia, establecimiento, modulo);
        });

        // Función para actualizar provincias
        function actualizarProvincias(tipo) {
            const url = '{{ route('usuario.reportes.equipos.ajax.provincias') }}';
            const params = new URLSearchParams();
            if (tipo) params.append('tipo', tipo);

            fetch(`${url}?${params}`)
                .then(response => response.json())
                .then(data => {
                    const valorActual = provinciaSelect.value;
                    provinciaSelect.innerHTML = '<option value="">Todas</option>';

                    data.forEach(provincia => {
                        const option = document.createElement('option');
                        option.value = provincia;
                        option.textContent = provincia;
                        if (provincia === valorActual) option.selected = true;
                        provinciaSelect.appendChild(option);
                    });
                });
        }

        // Función para actualizar establecimientos
        function actualizarEstablecimientos(tipo, provincia) {
            const url = '{{ route('usuario.reportes.equipos.ajax.establecimientos') }}';
            const params = new URLSearchParams();
            if (tipo) params.append('tipo', tipo);
            if (provincia) params.append('provincia', provincia);

            fetch(`${url}?${params}`)
                .then(response => response.json())
                .then(data => {
                    const valorActual = establecimientoSelect.value;
                    establecimientoSelect.innerHTML = '<option value="">Todos</option>';

                    data.forEach(est => {
                        const option = document.createElement('option');
                        option.value = est.id;
                        option.textContent = est.nombre;
                        if (est.id == valorActual) option.selected = true;
                        establecimientoSelect.appendChild(option);
                    });
                });
        }

        // Función para actualizar módulos
        function actualizarModulos(tipo, provincia, establecimiento) {
            const url = '{{ route('usuario.reportes.equipos.ajax.modulos') }}';
            const params = new URLSearchParams();
            if (tipo) params.append('tipo', tipo);
            if (provincia) params.append('provincia', provincia);
            if (establecimiento) params.append('establecimiento_id', establecimiento);

            fetch(`${url}?${params}`)
                .then(response => response.json())
                .then(data => {
                    const valorActual = moduloSelect.value;
                    moduloSelect.innerHTML = '<option value="">Todos</option>';

                    Object.entries(data).forEach(([key, nombre]) => {
                        const option = document.createElement('option');
                        option.value = key;
                        option.textContent = nombre;
                        if (key === valorActual) option.selected = true;
                        moduloSelect.appendChild(option);
                    });
                });
        }

        // Función para actualizar descripciones
        function actualizarDescripciones(tipo, provincia, establecimiento, modulo) {
            const url = '{{ route('usuario.reportes.equipos.ajax.descripciones') }}';
            const params = new URLSearchParams();
            if (tipo) params.append('tipo', tipo);
            if (provincia) params.append('provincia', provincia);
            if (establecimiento) params.append('establecimiento_id', establecimiento);
            if (modulo) params.append('modulo', modulo);

            fetch(`${url}?${params}`)
                .then(response => response.json())
                .then(data => {
                    const valorActual = descripcionSelect.value;
                    descripcionSelect.innerHTML = '<option value="">Todas</option>';
                    
                    data.forEach(descripcion => {
                        const option = document.createElement('option');
                        option.value = descripcion;
                        option.textContent = descripcion;
                        if (descripcion === valorActual) option.selected = true;
                        descripcionSelect.appendChild(option);
                    });
                });
        }

        // Inicializar filtros al cargar la página si hay valores seleccionados
        document.addEventListener('DOMContentLoaded', function() {
            const tipoInicial = tipoSelect.value;
            const provinciaInicial = provinciaSelect.value;
            const establecimientoInicial = establecimientoSelect.value;
            const moduloInicial = moduloSelect.value;

            // Si hay un tipo seleccionado, actualizar todos los filtros
            if (tipoInicial) {
                actualizarProvincias(tipoInicial);
                actualizarEstablecimientos(tipoInicial, provinciaInicial);
                actualizarModulos(tipoInicial, provinciaInicial, establecimientoInicial);
                actualizarDescripciones(tipoInicial, provinciaInicial, establecimientoInicial, moduloInicial);
            } else if (provinciaInicial) {
                // Si solo hay provincia, actualizar establecimientos, módulos y descripciones
                actualizarEstablecimientos('', provinciaInicial);
                actualizarModulos('', provinciaInicial, establecimientoInicial);
                actualizarDescripciones('', provinciaInicial, establecimientoInicial, moduloInicial);
            } else if (establecimientoInicial) {
                // Si solo hay establecimiento, actualizar módulos y descripciones
                actualizarModulos('', '', establecimientoInicial);
                actualizarDescripciones('', '', establecimientoInicial, moduloInicial);
            } else if (moduloInicial) {
                // Si solo hay módulo, actualizar descripciones
                actualizarDescripciones('', '', '', moduloInicial);
            }
        });
    </script>
@endpush