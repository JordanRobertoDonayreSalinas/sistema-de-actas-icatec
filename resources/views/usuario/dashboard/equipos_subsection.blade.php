{{-- SUBSECCIÓN: EQUIPOS DE CÓMPUTO --}}
<div>
    <h3 class="text-sm font-bold text-slate-500 uppercase tracking-widest mb-4">Equipos de Cómputo</h3>

    {{-- FILTROS --}}
    <div class="bg-white rounded-2xl shadow-lg shadow-slate-200/50 p-6 border border-slate-100 mb-6">
        <div class="flex items-center gap-3 mb-4">
            <div class="rounded-lg bg-indigo-100 p-2">
                <i data-lucide="filter" class="h-5 w-5 text-indigo-600"></i>
            </div>
            <h4 class="text-lg font-bold text-slate-800">Filtros</h4>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4">
            {{-- Filtro de Mes --}}
            <div>
                <label for="eq_mes" class="block text-sm font-semibold text-slate-700 mb-2">
                    <i data-lucide="calendar" class="w-4 h-4 inline-block mr-1"></i>
                    Mes
                </label>
                <select id="eq_mes"
                    class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">
                    <option value="" selected>Todos</option>
                    <option value="1">Enero</option>
                    <option value="2">Febrero</option>
                    <option value="3">Marzo</option>
                    <option value="4">Abril</option>
                    <option value="5">Mayo</option>
                    <option value="6">Junio</option>
                    <option value="7">Julio</option>
                    <option value="8">Agosto</option>
                    <option value="9">Septiembre</option>
                    <option value="10">Octubre</option>
                    <option value="11">Noviembre</option>
                    <option value="12">Diciembre</option>
                </select>
            </div>

            {{-- Filtro de Año --}}
            <div>
                <label for="eq_anio" class="block text-sm font-semibold text-slate-700 mb-2">
                    <i data-lucide="calendar-days" class="w-4 h-4 inline-block mr-1"></i>
                    Año
                </label>
                <select id="eq_anio"
                    class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">
                    <option value="">Todos</option>
                    @foreach($aniosDisponibles as $anio)
                        <option value="{{ $anio }}" {{ $anio == now()->year ? 'selected' : '' }}>{{ $anio }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Filtro de Tipo --}}
            <div>
                <label for="eq_tipo" class="block text-sm font-semibold text-slate-700 mb-2">
                    <i data-lucide="building-2" class="w-4 h-4 inline-block mr-1"></i>
                    Tipo
                </label>
                <select id="eq_tipo"
                    class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">
                    <option value="">Todos</option>
                    @foreach($tipos as $tipo)
                        <option value="{{ $tipo }}">{{ $tipo }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Filtro de Provincia --}}
            <div>
                <label for="eq_provincia" class="block text-sm font-semibold text-slate-700 mb-2">
                    <i data-lucide="map-pin" class="w-4 h-4 inline-block mr-1"></i>
                    Provincia
                </label>
                <select id="eq_provincia"
                    class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">
                    <option value="">Todas</option>
                    @foreach($provincias as $provincia)
                        <option value="{{ $provincia }}">{{ $provincia }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Filtro de Establecimiento --}}
            <div>
                <label for="eq_establecimiento" class="block text-sm font-semibold text-slate-700 mb-2">
                    <i data-lucide="home" class="w-4 h-4 inline-block mr-1"></i>
                    Establecimiento
                </label>
                <select id="eq_establecimiento"
                    class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">
                    <option value="">Todos</option>
                    @foreach($establecimientos as $est)
                        <option value="{{ $est->id }}">{{ $est->nombre }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Filtro de Módulo (Selección Visual) --}}
            <div class="md:col-span-2 lg:col-span-3">
                <label class="block text-sm font-semibold text-slate-700 mb-3">
                    <i data-lucide="layers" class="w-4 h-4 inline-block mr-1"></i>
                    Módulos <span class="text-xs text-slate-500">(Selecciona uno o varios)</span>
                </label>

                {{-- Contenedor con scroll para los módulos --}}
                <div class="border border-slate-300 rounded-lg p-4 bg-slate-50 max-h-48 overflow-y-auto custom-scroll">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
                        @foreach($modulos as $mod)
                            <label
                                class="flex items-center gap-2 p-2 rounded-lg hover:bg-white transition-all cursor-pointer group">
                                <input type="checkbox" name="modulos[]" value="{{ $mod['valor'] }}"
                                    class="eq_modulo_checkbox w-4 h-4 text-indigo-600 border-slate-300 rounded focus:ring-2 focus:ring-indigo-500 cursor-pointer">
                                <span class="text-sm text-slate-700 group-hover:text-indigo-600 transition-colors">
                                    {{ $mod['nombre'] }}
                                </span>
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- Botones de acción rápida --}}
                <div class="flex gap-2 mt-2">
                    <button type="button" id="btnSeleccionarTodos"
                        class="text-xs px-3 py-1 bg-indigo-100 text-indigo-700 rounded-lg hover:bg-indigo-200 transition-all">
                        Seleccionar Todos
                    </button>
                    <button type="button" id="btnLimpiarModulos"
                        class="text-xs px-3 py-1 bg-slate-100 text-slate-700 rounded-lg hover:bg-slate-200 transition-all">
                        Limpiar
                    </button>
                    <span id="contadorModulos" class="text-xs text-slate-500 self-center ml-auto">
                        0 seleccionados
                    </span>
                </div>
            </div>

            {{-- Filtro de Descripción --}}
            <div>
                <label for="eq_descripcion" class="block text-sm font-semibold text-slate-700 mb-2">
                    <i data-lucide="file-text" class="w-4 h-4 inline-block mr-1"></i>
                    Descripción
                </label>
                <select id="eq_descripcion"
                    class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">
                    <option value="">Todas</option>
                    @foreach($descripciones as $desc)
                        <option value="{{ $desc }}">{{ $desc }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Botón Aplicar --}}
            <div class="flex items-end">
                <button id="btnAplicarFiltrosEquipos"
                    class="w-full px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg transition-all shadow-lg shadow-indigo-500/30 hover:shadow-xl hover:shadow-indigo-500/40">
                    <i data-lucide="search" class="w-4 h-4 inline-block mr-2"></i>
                    Aplicar Filtros
                </button>
            </div>
        </div>
    </div>

    {{-- Tarjeta de Total de Equipos --}}
    <div
        class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-cyan-500 to-blue-600 p-8 text-white shadow-xl shadow-cyan-500/10 mb-6 group transition-transform hover:scale-[1.01]">
        <div
            class="absolute -right-10 -top-10 h-40 w-40 rounded-full bg-white/10 blur-3xl transition-all group-hover:bg-white/20">
        </div>

        <div class="relative z-10 flex items-center justify-between">
            <div>
                <p class="text-cyan-100 font-medium tracking-wide text-sm uppercase">Total Equipos</p>
                <h3 id="eq_totalEquipos" class="mt-1 text-5xl font-extrabold tracking-tight">{{ $totalEquipos }}</h3>
                <p id="eq_periodoTexto" class="mt-2 text-cyan-100/80 text-sm">
                    {{ now()->locale('es')->translatedFormat('F Y') }}
                </p>
            </div>
            <div class="rounded-xl bg-white/20 p-4 backdrop-blur-sm">
                <i data-lucide="monitor" class="h-12 w-12 text-white"></i>
            </div>
        </div>
    </div>

    <!-- SECCIÓN 1: ESTADO DEL PARQUE INFORMÁTICO -->
    <div class="mb-10">
        <div class="flex items-center gap-2 mb-6 border-l-4 border-indigo-600 pl-4">
            <h2 class="text-xl font-extrabold text-slate-800 uppercase tracking-tight">I. Estado del Parque Informático
            </h2>
            <div class="h-px flex-1 bg-gradient-to-r from-slate-200 to-transparent"></div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Gráfico: Por Estado -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 transition-all hover:shadow-md">
                <h3 class="text-sm font-bold text-slate-500 mb-6 flex items-center gap-2 uppercase tracking-widest">
                    <i data-lucide="activity" class="w-4 h-4 text-emerald-500"></i>
                    Distribución por Estado
                </h3>
                <div class="h-[300px] relative">
                    <canvas id="chartEstado"></canvas>
                </div>
            </div>

            <!-- Gráfico: Por Tipo -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 transition-all hover:shadow-md">
                <h3 class="text-sm font-bold text-slate-500 mb-6 flex items-center gap-2 uppercase tracking-widest">
                    <i data-lucide="monitor" class="w-4 h-4 text-indigo-500"></i>
                    Distribución por Tipo
                </h3>
                <div class="h-[300px] relative">
                    <canvas id="chartTipo"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- SECCIÓN 2: DISTRIBUCIÓN OPERATIVA -->
    <div class="mb-10">
        <div class="flex items-center gap-2 mb-6 border-l-4 border-purple-600 pl-4">
            <h2 class="text-xl font-extrabold text-slate-800 uppercase tracking-tight">II. Distribución Operativa</h2>
            <div class="h-px flex-1 bg-gradient-to-r from-slate-200 to-transparent"></div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Gráfico: Por Módulo -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 transition-all hover:shadow-md">
                <h3 class="text-sm font-bold text-slate-500 mb-6 flex items-center gap-2 uppercase tracking-widest">
                    <i data-lucide="layers" class="w-4 h-4 text-purple-500"></i>
                    Uso por Módulo
                </h3>
                <div class="h-[300px] relative">
                    <canvas id="chartModulo"></canvas>
                </div>
            </div>

            <!-- Gráfico: Por Descripción -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 transition-all hover:shadow-md">
                <h3 class="text-sm font-bold text-slate-500 mb-6 flex items-center gap-2 uppercase tracking-widest">
                    <i data-lucide="package" class="w-4 h-4 text-amber-500"></i>
                    Top Equipos (Descripción)
                </h3>
                <div class="h-[300px] relative">
                    <canvas id="chartDescripcion"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- SECCIÓN 3: INFRAESTRUCTURA DE CONECTIVIDAD -->
    <div class="mb-10">
        <div class="flex items-center gap-2 mb-6 border-l-4 border-blue-600 pl-4">
            <h2 class="text-xl font-extrabold text-slate-800 uppercase tracking-tight">III. Infraestructura de
                Conectividad</h2>
            <div class="h-px flex-1 bg-gradient-to-r from-slate-200 to-transparent"></div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Gráfico: Tipo de Conectividad -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 transition-all hover:shadow-md">
                <h3
                    class="text-[11px] font-black text-slate-400 mb-6 flex items-center gap-2 uppercase tracking-widest">
                    <i data-lucide="wifi" class="w-4 h-4 text-blue-500"></i>
                    Tipo de Conexión
                </h3>
                <div class="h-[250px] relative">
                    <canvas id="chartConectividad"></canvas>
                </div>
            </div>

            <!-- Gráfico: Fuente WiFi -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 transition-all hover:shadow-md">
                <h3
                    class="text-[11px] font-black text-slate-400 mb-6 flex items-center gap-2 uppercase tracking-widest">
                    <i data-lucide="router" class="w-4 h-4 text-fuchsia-500"></i>
                    Fuente de Datos
                </h3>
                <div class="h-[250px] relative">
                    <canvas id="chartFuenteWifi"></canvas>
                </div>
            </div>

            <!-- Gráfico: Proveedor -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 transition-all hover:shadow-md">
                <h3
                    class="text-[11px] font-black text-slate-400 mb-6 flex items-center gap-2 uppercase tracking-widest">
                    <i data-lucide="globe" class="w-4 h-4 text-rose-500"></i>
                    Operador de Servicio
                </h3>
                <div class="h-[250px] relative">
                    <canvas id="chartProveedor"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- SECCIÓN 4: ANÁLISIS POR SEDE / ESTABLECIMIENTO -->
    <div class="mb-10">
        <div class="flex items-center gap-2 mb-6 border-l-4 border-cyan-600 pl-4">
            <h2 class="text-xl font-extrabold text-slate-800 uppercase tracking-tight">IV. Distribución por
                Establecimiento</h2>
            <div class="h-px flex-1 bg-gradient-to-r from-slate-200 to-transparent"></div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-8 transition-all hover:shadow-md">
            <h3 class="text-sm font-bold text-slate-500 mb-8 flex items-center gap-2 uppercase tracking-widest">
                <i data-lucide="building-2" class="w-5 h-5 text-cyan-500"></i>
                Total de Equipos por Sede
            </h3>
            <div class="h-96 w-full">
                <canvas id="chartEstablecimiento"></canvas>
            </div>
        </div>
    </div>
</div>
</div>