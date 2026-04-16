<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Panel de Usuario')</title>

    {{-- Favicon --}}
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('favicon.png') }}">

    {{-- Fuentes --}}
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    {{-- Vite (Tailwind + JS principal) --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Alpine.js con Plugins --}}
    <script defer src="https://unpkg.com/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    {{-- SweetAlert2 --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- Librería para Escáner de Código de Barras (Html5-QRCode) --}}
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .bg-grid-slate {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='40' height='40' viewBox='0 0 40 40'%3E%3Cg fill-rule='evenodd'%3E%3Cg fill='%23cbd5e1' fill-opacity='0.4'%3E%3Cpath opacity='0.5' d='M0 38.59l2.83-2.829-1.414-1.415L0 35.758v2.832zM38.59 40l-2.83-2.828 1.414-1.414L40 38.586V40h-1.41zM0 1.414L1.414 0l1.415 1.414L0 4.242V1.414zM38.586 0l1.414 1.414-2.828 2.828L35.758 0h2.828z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }

        .custom-scroll::-webkit-scrollbar {
            width: 4px;
            height: 4px;
        }

        .custom-scroll::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
        }

        .custom-scroll::-webkit-scrollbar-thumb {
            background: rgba(203, 213, 225, 0.6);
            border-radius: 4px;
        }

        [x-cloak] {
            display: none !important;
        }
    </style>

    @stack('styles')
</head>

<body class="bg-slate-50 text-slate-800 font-sans antialiased bg-grid-slate">

    <div class="flex h-screen overflow-hidden">

        {{-- ================= SIDEBAR USUARIO ================= --}}
        <aside class="w-72 shrink-0 bg-slate-900 text-white hidden md:flex flex-col shadow-2xl relative z-30">
        <div class="absolute top-0 left-0 w-full h-[2px] bg-cyan-500"></div>

            <div class="h-20 flex items-center px-5 border-b border-white/[0.07] shrink-0">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-md border border-cyan-500/20 bg-cyan-500/10 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-cyan-400" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M19 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V5a2 2 0 0 0-2-2zm-6 14h-2v-4H7v-2h4V7h2v4h4v2h-4v4z"/>
                        </svg>
                    </div>
                    <div>
                        <span class="font-black text-[11px] tracking-[0.08em] text-white uppercase block leading-tight">Herramientas de Implementación SIHCE</span>
                    </div>
                </div>
            </div>

            <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto custom-scroll">
                @if(Auth::user()->role === 'admin')
                <p class="px-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-3 mt-2">Plataforma</p>

                {{-- GESTIONAR USUARIOS --}}
                <a href="{{ route('admin.users.index') }}"
                    class="group relative flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ request()->routeIs('admin.users.*') ? 'bg-indigo-600/10 text-indigo-400' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                    <i data-lucide="users" class="w-5 h-5"></i>
                    <span class="font-medium">Gestionar Usuarios</span>
                </a>

                {{-- Dashboard Dropdown --}}
                <div x-data="{ open: {{ request()->routeIs('usuario.dashboard*') ? 'true' : 'false' }} }">
                    <button @click="open = !open"
                        class="w-full group relative flex items-center justify-between gap-3 px-4 py-3 rounded-xl transition-all {{ request()->routeIs('usuario.dashboard*') ? 'bg-emerald-600/10 text-emerald-400' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                        <div class="flex items-center gap-3">
                            <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
                            <span class="font-medium">Dashboard</span>
                        </div>
                        <i data-lucide="chevron-down" class="w-4 h-4 transition-transform"
                            :class="open ? 'rotate-180' : ''"></i>
                    </button>

                    <div x-show="open" x-collapse class="ml-4 mt-1 space-y-1">
                        <a href="{{ route('usuario.dashboard.general') }}"
                            class="group relative flex items-center gap-3 px-4 py-2 rounded-lg transition-all {{ request()->routeIs('usuario.dashboard.general') ? 'bg-teal-600/10 text-teal-400' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                            <i data-lucide="git-merge" class="w-4 h-4"></i>
                            <span class="text-sm font-medium">Mapa de Progresión</span>
                        </a>
                        <a href="{{ route('usuario.dashboard.equipos') }}"
                            class="group relative flex items-center gap-3 px-4 py-2 rounded-lg transition-all {{ request()->routeIs('usuario.dashboard.equipos') ? 'bg-teal-600/10 text-emerald-400' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                            <i data-lucide="monitor" class="w-4 h-4"></i>
                            <span class="text-sm font-medium">Equipos de Cómputo</span>
                        </a>
                        <a href="{{ route('usuario.dashboard.programacion.sectores') }}"
                            class="group relative flex items-center gap-3 px-4 py-2 rounded-lg transition-all {{ request()->routeIs('usuario.dashboard.programacion.sectores') ? 'bg-indigo-600/10 text-indigo-400' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                            <i data-lucide="map" class="w-4 h-4"></i>
                            <span class="text-sm font-medium">Programación por Sectores</span>
                        </a>
                        <a href="{{ route('usuario.dashboard.programacion.propuesta') }}"
                            class="group relative flex items-center gap-3 px-4 py-2 rounded-lg transition-all {{ request()->routeIs('usuario.dashboard.programacion.propuesta') ? 'bg-amber-600/10 text-amber-400' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                            <i data-lucide="map-pin" class="w-4 h-4"></i>
                            <span class="text-sm font-medium">Sectorización Propuesta</span>
                        </a>
                    </div>
                </div>
                @endif

                <a href="{{ route('usuario.perfil') }}"
                    class="group relative flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ request()->routeIs('usuario.perfil') ? 'bg-emerald-600/10 text-emerald-400' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                    <i data-lucide="user-circle" class="w-5 h-5"></i>
                    <span class="font-semibold">Mi Perfil</span>
                </a>

                @if(Auth::user()->role === 'admin' || Auth::user()->role === 'operador')
                <p class="px-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-3 mt-6">Operaciones</p>
                @endif

                @if(Auth::user()->role === 'admin')
                {{-- IMPLEMENTACIÓN --}}
                <a href="{{ route('usuario.implementacion.index') }}"
                    class="group relative flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ request()->routeIs('usuario.implementacion.*') ? 'bg-purple-600/10 text-purple-400' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                    <i data-lucide="pen-tool" class="w-5 h-5"></i>
                    <span class="font-medium">Actas de Implementación</span>
                </a>

                {{-- ASISTENCIA TÉCNICA --}}
                <a href="{{ route('usuario.actas.index') }}"
                    class="group relative flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ request()->routeIs('usuario.actas.*') ? 'bg-emerald-600/10 text-emerald-400' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                    <i data-lucide="file-text" class="w-5 h-5"></i>
                    <span class="font-medium">Actas de Asistencia Técnica</span>
                </a>
                @endif

                @if(Auth::user()->role === 'admin' || Auth::user()->role === 'operador')
                {{-- MONITOREO --}}
                <a href="{{ route('usuario.monitoreo.index') }}"
                    class="group relative flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ request()->routeIs('usuario.monitoreo.*') ? 'bg-blue-600/10 text-blue-400' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                    <i data-lucide="activity" class="w-5 h-5"></i>
                    <span class="font-medium">Actas de Monitoreo</span>
                </a>
                @endif

                @if(Auth::user()->role === 'admin')

                {{-- DOCUMENTOS ADMINISTRATIVOS --}}
                <a href="{{ route('usuario.documentos.index') }}"
                    class="group relative flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ request()->routeIs('usuario.documentos.*') ? 'bg-indigo-600/10 text-indigo-400' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                    <i data-lucide="folder-open" class="w-5 h-5"></i>
                    <span class="font-medium">Documentos Administrativos</span>
                </a>

                {{-- BANCO DE FIRMAS --}}
                <a href="{{ route('admin.firmas.index') }}"
                    class="group relative flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ request()->routeIs('admin.firmas.*') ? 'bg-indigo-600/10 text-indigo-400' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                    <i data-lucide="signature" class="w-5 h-5"></i>
                    <span class="font-medium">Banco de Firmas</span>
                </a>

                {{-- REPORTES (Desplegable) --}}
                <div x-data="{ open: {{ request()->routeIs('usuario.reportes.*', 'usuario.auditoria.*') ? 'true' : 'false' }} }">
                    {{-- Botón Principal --}}
                    <button @click="open = !open" type="button"
                        class="w-full group relative flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ request()->routeIs('usuario.reportes.*', 'usuario.auditoria.*') ? 'bg-purple-600/10 text-purple-400' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                        <i data-lucide="bar-chart-3" class="w-5 h-5"></i>
                        <span class="font-medium flex-1 text-left">Reportes</span>
                        <i data-lucide="chevron-down" class="w-4 h-4 transition-transform duration-200"
                            :class="open ? 'rotate-180' : ''"></i>
                    </button>

                    {{-- Submenú de Reportes --}}
                    <div x-show="open" x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 -translate-y-2"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-2"
                        class="ml-4 mt-1 space-y-1 border-l-2 border-slate-700/50 pl-2" x-cloak>

                        {{-- Equipos de Cómputo --}}
                        <a href="{{ route('usuario.reportes.equipos') }}"
                            class="group relative flex items-center gap-3 px-4 py-2.5 rounded-lg transition-all {{ request()->routeIs('usuario.reportes.equipos') ? 'bg-purple-600/10 text-purple-300' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                            <i data-lucide="monitor" class="w-4 h-4"></i>
                            <span class="font-medium text-sm">Equipos de Cómputo</span>
                        </a>



                        {{-- Auditoría de Consistencia SIHCE --}}
                        <a href="{{ route('usuario.auditoria.index') }}"
                            class="group relative flex items-center gap-3 px-4 py-2.5 rounded-lg transition-all {{ request()->routeIs('usuario.auditoria.index') ? 'bg-purple-600/10 text-purple-300' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                            <i data-lucide="check-square" class="w-4 h-4"></i>
                            <span class="font-medium text-sm">Auditoría SIHCE</span>
                        </a>

                        {{-- Auditoría de Equipos y Conectividad --}}
                        <a href="{{ route('usuario.auditoria.equipos') }}"
                            class="group relative flex items-center gap-3 px-4 py-2.5 rounded-lg transition-all {{ request()->routeIs('usuario.auditoria.equipos') ? 'bg-purple-600/10 text-purple-300' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                            <i data-lucide="monitor" class="w-4 h-4"></i>
                            <span class="font-medium text-sm">Auditoría Equipos</span>
                        </a>
                    </div>
                </div>
                @endif

                <p class="px-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-3 mt-6">Mesa de Ayuda</p>

                {{-- INCIDENCIAS --}}
                <a href="{{ route('usuario.mesa-ayuda.index') }}"
                    class="group relative flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ request()->routeIs('usuario.mesa-ayuda.index', 'usuario.mesa-ayuda.responder') ? 'bg-orange-600/10 text-orange-400' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                    <i data-lucide="headphones" class="w-5 h-5"></i>
                    <span class="font-medium flex-1">Gestión de Incidencias</span>
                    @php
                        if(Auth::user()->role === 'admin') {
                            $pendientes = \App\Models\Incidencia::where('estado', 'Pendiente')->count();
                        } else {
                            $pendientes = \App\Models\Incidencia::where('estado', 'Pendiente')->where('dni', Auth::user()->username)->count();
                        }
                    @endphp
                    @if($pendientes > 0)
                        <span class="text-[10px] font-bold bg-orange-500 text-white px-2 py-0.5 rounded-full">
                            {{ $pendientes }}
                        </span>
                    @endif
                </a>

                {{-- FORMULARIO PÚBLICO --}}
                <a href="{{ route('usuario.mesa-ayuda.form') }}"
                    class="group relative flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ request()->routeIs('usuario.mesa-ayuda.form') ? 'bg-orange-600/10 text-orange-400' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                    <i data-lucide="external-link" class="w-5 h-5"></i>
                    <span class="font-medium">Registro de Incidencias</span>
                </a>

            </nav>
        </aside>

        {{-- ================= CONTENIDO PRINCIPAL ================= --}}
        <main class="flex-1 flex flex-col h-screen overflow-hidden relative">

            <header
                class="h-20 shrink-0 bg-white border-b border-slate-200 flex items-center justify-between px-8 sticky top-0 z-20 shadow-sm">
                <div>
                    @yield('header-content')
                </div>

                <div class="flex items-center gap-4">
                    <div class="h-8 w-px bg-slate-200 mx-2 hidden sm:block"></div>

                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" @click.away="open = false" type="button"
                            class="flex items-center gap-3 p-1 rounded-xl hover:bg-slate-50 transition-all focus:outline-none group">

                            <div class="text-right hidden sm:block">
                                <p class="text-sm font-bold text-slate-700 leading-tight">{{ Auth::user()->name }}</p>
                                <p class="text-[11px] text-cyan-600 font-semibold">
                                    {{ Auth::user()->role === 'admin' ? 'Administrador' : (Auth::user()->role === 'operador' ? 'Operador' : 'Usuario') }}
                                </p>
                            </div>

                            <div
                                class="h-10 w-10 rounded-full bg-gradient-to-br from-emerald-500 to-teal-600 text-white flex items-center justify-center font-bold shadow-md border-2 border-white uppercase transition-transform group-hover:scale-105">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </div>

                            <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400 transition-transform"
                                :class="open ? 'rotate-180' : ''"></i>
                        </button>

                        <div x-show="open" x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                            x-transition:leave-end="opacity-0 scale-95 translate-y-2"
                            class="absolute right-0 mt-2 w-56 bg-white rounded-2xl shadow-2xl border border-slate-100 py-2 z-50"
                            x-cloak>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit"
                                    class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-red-500 hover:bg-red-50 transition-colors font-bold text-left">
                                    <i data-lucide="log-out" class="w-4 h-4"></i>
                                    <span>Cerrar Sesión</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <div class="flex-1 overflow-y-auto p-8 custom-scroll">
                @yield('content')
            </div>

        </main>
    </div>

    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            lucide.createIcons();
        });

        window.refreshLucide = () => {
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        }
    </script>

    {{-- Chart.js para gráficos estadísticos --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

    {{-- SweetAlert2 --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @stack('scripts')
</body>

</html>