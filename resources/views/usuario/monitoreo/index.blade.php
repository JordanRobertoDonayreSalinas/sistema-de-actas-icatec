@extends('layouts.usuario')

@section('title', 'Actas de Monitoreo')

@push('styles')
    <style>
        input[type="date"] {
            position: relative;
            color: #4b5563;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='20' height='20' viewBox='0 0 24 24' fill='none' stroke='%233b82f6' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Crect x='3' y='4' width='18' height='18' rx='2' ry='2'/%3E%3Cline x1='16' y1='2' x2='16' y2='6'/%3E%3Cline x1='8' y1='2' x2='8' y2='6'/%3E%3Cline x1='3' y1='10' x2='21' y2='10'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 0.5rem center;
            background-size: 1.2em;
        }
        input[type="date"]::-webkit-calendar-picker-indicator {
            opacity: 0;
            cursor: pointer;
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
        }
        [x-cloak] { display: none !important; }
    </style>
@endpush

@section('header-content')
    <h1 class="text-xl font-bold text-slate-800 tracking-tight">Actas de Monitoreo</h1>
    <div class="flex items-center gap-2 text-xs text-slate-500 mt-0.5">
        <span>Operaciones</span>
        <span class="text-slate-300">•</span>
        <span>Actas de Monitoreo</span>
    </div>
@endsection

@section('content')

    @php
        $filtersAreActive = request()->anyFilled(['implementador', 'provincia', 'fecha_inicio', 'fecha_fin']);
    @endphp

    <div x-data="{ open: {{ $filtersAreActive ? 'true' : 'false' }} }" class="w-full">

        {{-- ==================== TARJETA AZUL SUPERIOR (Monitoreo) ====================== --}}
        <div class="bg-gradient-to-r from-blue-600 to-indigo-500 p-5 rounded-2xl shadow-xl mb-6 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full blur-3xl -mr-16 -mt-16 pointer-events-none"></div>

            <div class="relative z-10 flex flex-col lg:flex-row items-center justify-between gap-6">
                <div class="flex flex-col gap-4 w-full">
                    <div class="flex flex-wrap items-center gap-3">
                        <div class="bg-slate-900 text-white rounded-xl px-5 py-2.5 shadow-lg border border-slate-700 flex flex-col items-center min-w-[100px]">
                            <span class="text-2xl font-bold leading-none">{{ $monitoreos->total() }}</span>
                            <span class="text-[0.65rem] uppercase tracking-widest text-slate-400 font-semibold mt-1">Total</span>
                        </div>
                        <div class="bg-white/20 backdrop-blur-md text-white rounded-xl px-5 py-2.5 border border-white/30 flex flex-col items-center min-w-[100px]">
                            <span class="text-2xl font-bold leading-none">{{ $countCompletados ?? 0 }}</span>
                            <span class="text-[0.65rem] uppercase tracking-widest text-blue-100 font-semibold mt-1">Finalizados</span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-3 w-full lg:w-auto justify-center lg:justify-end mt-2 lg:mt-0">
                    <button @click="open = !open" type="button"
                        class="flex items-center gap-2 px-5 py-3 rounded-xl font-bold text-sm transition-all shadow-lg border border-white/20 text-white bg-white/10 hover:bg-white/20 backdrop-blur-sm">
                        <i data-lucide="filter" class="w-4 h-4"></i>
                        <span x-text="open ? 'Ocultar Filtros' : 'Mostrar Filtros'"></span>
                    </button>

                    {{-- Ruta para crear monitoreo --}}
                    <a href="{{ route('usuario.monitoreo.create') }}"
                        class="flex items-center gap-2 px-5 py-3 rounded-xl font-bold text-sm transition-all shadow-lg bg-white text-blue-700 hover:bg-blue-50 border border-transparent">
                        <i data-lucide="activity" class="w-5 h-5"></i>
                        <span>Nueva Acta</span>
                    </a>
                </div>
            </div>
        </div>

        {{-- ========================== FILTROS ============================== --}}
        <form x-show="open" x-cloak 
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
            method="GET" action="{{ route('usuario.monitoreo.index') }}"
            class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200 mb-6">
            
            <div class="flex flex-wrap lg:flex-nowrap items-end gap-4">
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 flex-grow w-full">
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1.5 tracking-wider">Implementador</label>
                        <select name="implementador" class="w-full text-xs font-medium text-slate-700 border-slate-200 bg-slate-50 rounded-xl focus:ring-2 focus:ring-blue-500 py-2.5">
                            <option value="">Todos</option>
                            @foreach ($implementadores as $impl)
                                <option value="{{ $impl }}" {{ request('implementador') == $impl ? 'selected' : '' }}>{{ $impl }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1.5 tracking-wider">Provincia</label>
                        <select name="provincia" class="w-full text-xs font-medium text-slate-700 border-slate-200 bg-slate-50 rounded-xl focus:ring-2 focus:ring-blue-500 py-2.5">
                            <option value="">Todas</option>
                            @foreach ($provincias as $prov)
                                <option value="{{ $prov }}" {{ request('provincia') == $prov ? 'selected' : '' }}>{{ $prov }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1.5 tracking-wider">Desde</label>
                        <input type="date" name="fecha_inicio" value="{{ request('fecha_inicio') }}" 
                            class="w-full text-xs font-medium text-slate-700 border-slate-200 bg-slate-50 rounded-xl py-2.5">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1.5 tracking-wider">Hasta</label>
                        <input type="date" name="fecha_fin" value="{{ request('fecha_fin') }}" 
                            class="w-full text-xs font-medium text-slate-700 border-slate-200 bg-slate-50 rounded-xl py-2.5">
                    </div>
                </div>

                <div class="flex items-center gap-3 shrink-0">
                    <button type="submit" class="w-11 h-11 flex items-center justify-center rounded-xl bg-blue-600 hover:bg-blue-700 text-white shadow-lg shadow-blue-500/30 transition-all hover:scale-105">
                        <i data-lucide="search" class="w-5 h-5"></i>
                    </button>
                    <a href="{{ route('usuario.monitoreo.index') }}" 
                        class="w-11 h-11 flex items-center justify-center rounded-xl bg-slate-400 hover:bg-slate-500 text-white shadow-lg shadow-slate-400/30 transition-all hover:scale-105">
                        <i data-lucide="rotate-cw" class="w-5 h-5"></i>
                    </a>
                </div>
            </div>
        </form>

        {{-- ======================== TABLA ========================= --}}
        <div class="bg-white rounded-2xl shadow-xl shadow-slate-200/50 border border-slate-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-slate-800">
                        <tr>
                            <th class="px-3 py-3 text-[10px] font-bold text-white uppercase tracking-wider">#</th>
                            <th class="px-3 py-3 text-[10px] font-bold text-white uppercase tracking-wider">Fecha</th>
                            <th class="px-3 py-3 text-[10px] font-bold text-white uppercase tracking-wider">Establecimiento</th>
                            <th class="px-3 py-3 text-[10px] font-bold text-white uppercase tracking-wider">Tipo</th>
                            <th class="px-3 py-3 text-[10px] font-bold text-white uppercase tracking-wider">Implementador</th>
                            <th class="px-3 py-3 text-[10px] font-bold text-white uppercase tracking-wider text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs">
                        @forelse($monitoreos as $monitoreo)
                            <tr class="hover:bg-blue-50/30 transition-colors group">
                                <td class="px-3 py-3 font-mono font-bold text-slate-700">{{ $monitoreo->id }}</td>
                                <td class="px-3 py-3 text-slate-600">
                                    {{ \Carbon\Carbon::parse($monitoreo->fecha)->format('d/m/Y') }}
                                </td>
                                <td class="px-3 py-3 font-semibold text-slate-800">{{ $monitoreo->establecimiento->nombre ?? '—' }}</td>
                                <td class="px-3 py-3">
                                    <span class="px-2 py-0.5 rounded-full bg-blue-100 text-blue-700 font-bold text-[9px] uppercase tracking-tighter">Monitoreo</span>
                                </td>
                                <td class="px-3 py-3 text-slate-500">{{ $monitoreo->implementador }}</td>
                                <td class="px-3 py-3 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="#" class="p-1.5 rounded-lg text-slate-400 hover:text-red-600 hover:bg-red-50 transition-all" title="PDF">
                                            <i data-lucide="file-text" class="w-4 h-4"></i>
                                        </a>
                                        
                                        <a href="#" class="p-1.5 rounded-lg text-slate-400 hover:text-blue-600 hover:bg-blue-50 transition-all" title="Editar">
                                            <i data-lucide="pencil" class="w-4 h-4"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-6 py-12 text-center text-slate-400">No se encontraron registros de monitoreo</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if ($monitoreos->hasPages())
            <div class="mt-4">{{ $monitoreos->appends(request()->query())->links() }}</div>
        @endif
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        @if (session('success'))
            Swal.fire({ icon: 'success', title: '¡Éxito!', text: @json(session('success')), confirmButtonColor: '#3b82f6', timer: 3000, toast: true, position: 'top-end', showConfirmButton: false });
        @endif
    </script>
@endpush