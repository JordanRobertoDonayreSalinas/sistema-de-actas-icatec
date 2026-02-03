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

    {{-- Alpine.js --}}
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

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
            <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-emerald-500 via-teal-500 to-blue-500">
            </div>

            <div class="h-20 flex items-center px-8 bg-slate-900/50 backdrop-blur-sm border-b border-white/5 shrink-0">
                <div class="flex items-center gap-3">
                    <div
                        class="w-8 h-8 rounded-lg bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center font-bold text-white text-lg shadow-lg shadow-emerald-500/40">
                        U</div>
                    <div><span class="font-bold text-lg tracking-wide block leading-tight">Panel de Usuario</span></div>
                </div>
            </div>

            <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto custom-scroll">
                <p class="px-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-3 mt-2">Plataforma</p>

                <a href="{{ route('usuario.dashboard') }}"
                    class="group relative flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ request()->routeIs('usuario.dashboard') ? 'bg-emerald-600/10 text-emerald-400' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                    <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
                    <span class="font-medium">Dashboard</span>
                </a>

                <a href="{{ route('usuario.perfil') }}"
                    class="group relative flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ request()->routeIs('usuario.perfil') ? 'bg-emerald-600/10 text-emerald-400' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                    <i data-lucide="user-circle" class="w-5 h-5"></i>
                    <span class="font-semibold">Mi Perfil</span>
                </a>

                <p class="px-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-3 mt-6">Operaciones</p>

                {{-- ASISTENCIA TÉCNICA --}}
                <a href="{{ route('usuario.actas.index') }}"
                    class="group relative flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ request()->routeIs('usuario.actas.*') ? 'bg-emerald-600/10 text-emerald-400' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                    <i data-lucide="file-text" class="w-5 h-5"></i>
                    <span class="font-medium">Actas de Asistencia Técnica</span>
                </a>

                {{-- MONITOREO --}}
                <a href="{{ route('usuario.monitoreo.index') }}"
                    class="group relative flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ request()->routeIs('usuario.monitoreo.*') ? 'bg-blue-600/10 text-blue-400' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                    <i data-lucide="activity" class="w-5 h-5"></i>
                    <span class="font-medium">Actas de Monitoreo</span>
                </a>

                {{-- DOCUMENTOS ADMINISTRATIVOS --}}
                <a href="{{ route('usuario.documentos.index') }}"
                    class="group relative flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ request()->routeIs('usuario.documentos.*') ? 'bg-indigo-600/10 text-indigo-400' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                    <i data-lucide="folder-open" class="w-5 h-5"></i>
                    <span class="font-medium">Documentos Administrativos</span>
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
                                <p class="text-[10px] text-emerald-600 uppercase tracking-wider font-black">Usuario</p>
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
                            class="absolute right-0 mt-2 w-64 bg-white rounded-2xl shadow-2xl border border-slate-100 py-2 z-50"
                            x-cloak>

                            <div class="border-t border-slate-50 mt-1 pt-1">
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

    @stack('scripts')
</body>

</html>