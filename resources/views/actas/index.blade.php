@extends('layouts.app')

@section('title', 'Listado de Actas')

@push('styles')
    <style>
        /* Estilo personalizado para input type="date" */
        input[type="date"] {
            position: relative;
            color: #4b5563;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='20' height='20' viewBox='0 0 24 24' fill='none' stroke='%236b7280' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Crect x='3' y='4' width='18' height='18' rx='2' ry='2'/%3E%3Cline x1='16' y1='2' x2='16' y2='6'/%3E%3Cline x1='8' y1='2' x2='8' y2='6'/%3E%3Cline x1='3' y1='10' x2='21' y2='10'/%3E%3C/svg%3E");
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
    <h1 class="text-xl font-bold text-slate-800 tracking-tight">Listado de Actas</h1>
    <div class="flex items-center gap-2 text-xs text-slate-500 mt-0.5">
        <span>Panel del Administrador</span>
        <span class="text-slate-300">•</span>
        <span>Listado de actas</span>
    </div>
@endsection

@section('content')

    {{-- Lógica para determinar si los filtros deben mostrarse abiertos --}}
    @php
        $filtersAreActive =
            request()->filled('implementador') ||
            request()->filled('provincia') ||
            request()->filled('firmado') ||
            request()->filled('fecha_inicio') ||
            request()->filled('fecha_fin');
    @endphp

    <div x-data="{ open: {{ $filtersAreActive ? 'true' : 'false' }} }" class="w-full">

        {{-- ==================== TARJETA AZUL SUPERIOR ====================== --}}
        <div class="bg-gradient-to-r from-[#4338ca] to-[#3b82f6] p-5 rounded-2xl shadow-xl mb-6 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full blur-3xl -mr-16 -mt-16 pointer-events-none"></div>

            <div class="relative z-10 flex flex-col lg:flex-row items-center justify-between gap-6">
                <div class="flex flex-col gap-4 w-full">
                    <h2 class="text-3xl font-extrabold text-white tracking-tight flex items-center gap-3">
                        Listado de Actas
                    </h2>
                    
                    <div class="flex flex-wrap items-center gap-3">
                        <div class="bg-gray-900 text-white rounded-xl px-5 py-2.5 shadow-lg border border-gray-700 flex flex-col items-center min-w-[100px]">
                            <span class="text-2xl font-bold leading-none">{{ $actas->total() }}</span>
                            <span class="text-[0.65rem] uppercase tracking-widest text-gray-400 font-semibold mt-1">Total</span>
                        </div>
                        <div class="bg-green-600 text-white rounded-xl px-5 py-2.5 shadow-lg border border-green-500 flex flex-col items-center min-w-[100px]">
                            <span class="text-2xl font-bold leading-none">{{ $countFirmadas }}</span>
                            <span class="text-[0.65rem] uppercase tracking-widest text-green-100 font-semibold mt-1">Firmadas</span>
                        </div>
                        <div class="bg-red-600 text-white rounded-xl px-5 py-2.5 shadow-lg border border-red-500 flex flex-col items-center min-w-[100px]">
                            <span class="text-2xl font-bold leading-none">{{ $countPendientes }}</span>
                            <span class="text-[0.65rem] uppercase tracking-widest text-red-100 font-semibold mt-1">Pendientes</span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-3 w-full lg:w-auto justify-center lg:justify-end mt-2 lg:mt-0">
                    <button @click="open = !open" type="button"
                        class="flex items-center gap-2 px-5 py-3 rounded-xl font-bold text-sm transition-all shadow-lg border border-white/20 text-white bg-indigo-500/40 hover:bg-indigo-500/60 backdrop-blur-sm">
                        <i data-lucide="filter" class="w-4 h-4" x-show="!open"></i>
                        <i data-lucide="filter-x" class="w-4 h-4" x-show="open" x-cloak></i>
                        <span x-text="open ? 'Ocultar Filtros' : 'Mostrar Filtros'"></span>
                    </button>

                    {{-- CORRECCIÓN RUTA: admin.actas.create --}}
                    <a href="{{ route('admin.actas.create') }}"
                        class="flex items-center gap-2 px-5 py-3 rounded-xl font-bold text-sm transition-all shadow-lg bg-white text-indigo-700 hover:bg-indigo-50 border border-transparent">
                        <i data-lucide="plus-circle" class="w-5 h-5"></i>
                        <span>Nueva Acta</span>
                    </a>
                </div>
            </div>
        </div>

        {{-- ========================== FILTROS (CORRECCIÓN RUTA admin.actas.index) ============================== --}}
        <form x-show="open" x-cloak 
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
            method="GET" action="{{ route('admin.actas.index') }}"
            class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200 mb-6">
            
            <div class="flex flex-wrap lg:flex-nowrap items-end gap-4">
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 flex-grow w-full">
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1.5 tracking-wider">Implementador</label>
                        <select name="implementador" class="w-full text-xs font-medium text-slate-700 border-slate-200 bg-slate-50 rounded-xl focus:ring-2 focus:ring-indigo-500 py-2.5">
                            <option value="">Todos</option>
                            @foreach ($implementadores as $impl)
                                <option value="{{ $impl }}" {{ request('implementador') == $impl ? 'selected' : '' }}>{{ $impl }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1.5 tracking-wider">Provincia</label>
                        <select name="provincia" class="w-full text-xs font-medium text-slate-700 border-slate-200 bg-slate-50 rounded-xl focus:ring-2 focus:ring-indigo-500 py-2.5">
                            <option value="">Todas</option>
                            @foreach ($provincias as $prov)
                                <option value="{{ $prov }}" {{ request('provincia') == $prov ? 'selected' : '' }}>{{ $prov }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1.5 tracking-wider">Estado</label>
                        <select name="firmado" class="w-full text-xs font-medium text-slate-700 border-slate-200 bg-slate-50 rounded-xl focus:ring-2 focus:ring-indigo-500 py-2.5">
                            <option value="">Todos</option>
                            <option value="1" {{ request('firmado') === '1' ? 'selected' : '' }}>Firmado</option>
                            <option value="0" {{ request('firmado') === '0' ? 'selected' : '' }}>Pendiente</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1.5 tracking-wider">Desde</label>
                        <input type="date" name="fecha_inicio" value="{{ request('fecha_inicio', now()->startOfMonth()->format('Y-m-d')) }}" 
                            class="w-full text-xs font-medium text-slate-700 border-slate-200 bg-slate-50 rounded-xl focus:ring-2 focus:ring-indigo-500 py-2.5">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1.5 tracking-wider">Hasta</label>
                        <input type="date" name="fecha_fin" value="{{ request('fecha_fin', now()->format('Y-m-d')) }}" 
                            class="w-full text-xs font-medium text-slate-700 border-slate-200 bg-slate-50 rounded-xl focus:ring-2 focus:ring-indigo-500 py-2.5">
                    </div>
                </div>

                <div class="flex items-center gap-3 shrink-0">
                    <button type="submit" class="w-11 h-11 flex items-center justify-center rounded-xl bg-[#6366f1] hover:bg-[#4f46e5] text-white shadow-lg shadow-indigo-500/30 transition-all hover:scale-105" title="Aplicar Filtros">
                        <i data-lucide="filter" class="w-5 h-5"></i>
                    </button>

                    {{-- CORRECCIÓN RUTA: admin.actas.index --}}
                    <a href="{{ route('admin.actas.index') }}" 
                        class="w-11 h-11 flex items-center justify-center rounded-xl bg-slate-400 hover:bg-slate-500 text-white shadow-lg shadow-slate-400/30 transition-all hover:scale-105" 
                        title="Limpiar Filtros">
                        <i data-lucide="rotate-cw" class="w-5 h-5"></i>
                    </a>
                </div>
            </div>
        </form>

        {{-- ======================== TABLA COMPACTA ========================= --}}
        <div class="bg-white rounded-2xl shadow-xl shadow-slate-200/50 border border-slate-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-[#4f46e5]">
                        <tr>
                            <th class="px-3 py-2.5 text-[10px] font-bold text-white uppercase tracking-wider">#</th>
                            <th class="px-3 py-2.5 text-[10px] font-bold text-white uppercase tracking-wider whitespace-nowrap">Fecha</th>
                            <th class="px-3 py-2.5 text-[10px] font-bold text-white uppercase tracking-wider">Establecimiento</th>
                            <th class="px-3 py-2.5 text-[10px] font-bold text-white uppercase tracking-wider whitespace-nowrap">Modalidad</th>
                            <th class="px-3 py-2.5 text-[10px] font-bold text-white uppercase tracking-wider">Implementador</th>
                            <th class="px-3 py-2.5 text-[10px] font-bold text-white uppercase tracking-wider text-center">Estado</th>
                            <th class="px-3 py-2.5 text-[10px] font-bold text-white uppercase tracking-wider text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs">
                        @forelse($actas as $acta)
                            <tr class="hover:bg-slate-50 transition-colors group">
                                <td class="px-3 py-2 font-mono font-bold text-slate-700">{{ $acta->id }}</td>
                                <td class="px-3 py-2 whitespace-nowrap text-slate-600 font-medium">
                                    {{ \Carbon\Carbon::parse($acta->fecha)->format('d/m/Y') }}
                                </td>
                                <td class="px-3 py-2 font-semibold text-slate-800 max-w-[200px] truncate" title="{{ $acta->establecimiento->nombre ?? '' }}">
                                    {{ $acta->establecimiento->nombre ?? '—' }}
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap"><span class="text-slate-600">{{ $acta->modalidad }}</span></td>
                                <td class="px-3 py-2 text-slate-500 max-w-[150px] truncate" title="{{ $acta->implementador }}">{{ $acta->implementador }}</td>
                                <td class="px-3 py-2 text-center whitespace-nowrap">
                                    <div class="flex items-center justify-center gap-1">
                                        @if ($acta->firmado)
                                            <span class="inline-flex px-2 py-0.5 rounded-md text-[10px] font-bold bg-green-100 text-green-700 border border-green-200">Firmado</span>
                                            @if (!empty($acta->firmado_pdf))
                                                <a href="{{ asset('storage/' . $acta->firmado_pdf) }}" target="_blank" class="text-slate-400 hover:text-green-600 p-1"><i data-lucide="eye" class="w-3.5 h-3.5"></i></a>
                                            @endif
                                            <form action="{{ route('actas.subirPDF', $acta->id) }}" method="POST" enctype="multipart/form-data" class="inline-block m-0">
                                                @csrf
                                                <input type="file" name="pdf_firmado" accept="application/pdf" onchange="this.form.submit()" hidden id="pdf-{{ $acta->id }}">
                                                <label for="pdf-{{ $acta->id }}" class="cursor-pointer text-slate-300 hover:text-indigo-500 p-1" title="Reemplazar"><i data-lucide="refresh-cw" class="w-3.5 h-3.5"></i></label>
                                            </form>
                                        @else
                                            <span class="inline-flex px-2 py-0.5 rounded-md text-[10px] font-bold bg-yellow-100 text-yellow-700 border border-yellow-200">Pendiente</span>
                                            <form action="{{ route('actas.subirPDF', $acta->id) }}" method="POST" enctype="multipart/form-data" class="inline-block m-0 ml-1">
                                                @csrf
                                                <input type="file" name="pdf_firmado" accept="application/pdf" onchange="this.form.submit()" hidden id="pdf-u-{{ $acta->id }}">
                                                <label for="pdf-u-{{ $acta->id }}" class="cursor-pointer text-slate-400 hover:text-indigo-600 p-1" title="Subir"><i data-lucide="upload-cloud" class="w-3.5 h-3.5"></i></label>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-3 py-2 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ route('actas.generarPDF', $acta->id) }}" target="_blank" class="p-1.5 rounded-lg text-slate-400 hover:text-red-600 hover:bg-red-50 transition-all" title="PDF"><i data-lucide="file-text" class="w-3.5 h-3.5"></i></a>
                                        
                                        {{-- CORRECCIÓN RUTA: admin.actas.edit --}}
                                        <a href="{{ route('admin.actas.edit', $acta->id) }}" class="p-1.5 rounded-lg text-slate-400 hover:text-amber-600 hover:bg-amber-50 transition-all" title="Editar"><i data-lucide="pencil" class="w-3.5 h-3.5"></i></a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="px-6 py-12 text-center text-slate-400">No se encontraron resultados</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if ($actas->hasPages())
            <div class="mt-4">{{ $actas->appends(request()->query())->links('pagination::tailwind') }}</div>
        @endif
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        @if (session('success'))
            Swal.fire({ icon: 'success', title: '¡Éxito!', text: @json(session('success')), confirmButtonColor: '#4f46e5', timer: 3000, toast: true, position: 'top-end', showConfirmButton: false });
        @endif
        @if (session('error'))
            Swal.fire({ icon: 'error', title: 'Error', text: @json(session('error')), confirmButtonColor: '#ef4444' });
        @endif
    </script>
@endpush