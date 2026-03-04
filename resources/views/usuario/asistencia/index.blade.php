@extends('layouts.usuario')

@section('title', 'Actas de Asistencia Técnica')

@push('styles')
    <style>
        [x-cloak] { display: none !important; }
        .input-modern {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 0.75rem;
            color: #334155;
            font-size: 0.75rem;
            font-weight: 600;
            padding: 0.6rem 0.75rem;
            transition: all 0.2s;
        }
        .input-modern:focus {
            border-color: #10b981;
            ring: 2px;
            ring-color: #10b981;
            outline: none;
        }
    </style>
@endpush

@section('header-content')
    <h1 class="text-xl font-bold text-slate-800 tracking-tight">Actas de Asistencia Técnica</h1>
    <div class="flex items-center gap-2 text-xs text-slate-500 mt-0.5">
        <span>Operaciones</span>
        <span class="text-slate-300">•</span>
        <span>Actas de Asistencia Técnica</span>
    </div>
@endsection

@section('content')
    <div x-data="{ open: {{ request()->anyFilled(['implementador', 'provincia', 'distrito', 'establecimiento_id', 'firmado']) ? 'true' : 'false' }} }" class="w-full">

        {{-- ETFs --}}
        <div class="bg-gradient-to-r from-emerald-600 to-teal-500 p-5 rounded-2xl shadow-xl mb-6 relative overflow-hidden text-white">
            <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full blur-3xl -mr-16 -mt-16 pointer-events-none"></div>
            <div class="relative z-10 flex flex-col lg:flex-row items-center justify-between gap-6">
                <div class="flex flex-wrap items-center gap-3">
                    <div class="bg-slate-900 text-white rounded-xl px-5 py-2.5 shadow-lg border border-slate-700 flex flex-col items-center min-w-[100px]">
                        <span class="text-2xl font-bold leading-none">{{ $actas->total() }}</span>
                        <span class="text-[0.65rem] uppercase tracking-widest text-slate-400 font-semibold mt-1">Total</span>
                    </div>
                    <div class="bg-white/20 backdrop-blur-md text-white rounded-xl px-5 py-2.5 border border-white/30 flex flex-col items-center min-w-[100px]">
                        <span class="text-2xl font-bold leading-none">{{ $countFirmadas }}</span>
                        <span class="text-[0.65rem] uppercase tracking-widest text-emerald-100 font-semibold mt-1">Firmadas</span>
                    </div>
                    <div class="bg-amber-500 text-white rounded-xl px-5 py-2.5 shadow-lg border border-amber-400 flex flex-col items-center min-w-[100px]">
                        <span class="text-2xl font-bold leading-none">{{ $countPendientes }}</span>
                        <span class="text-[0.65rem] uppercase tracking-widest text-amber-100 font-semibold mt-1">Pendientes</span>
                    </div>
                </div>
                <div class="flex items-center gap-3 w-full lg:w-auto justify-center lg:justify-end mt-2 lg:mt-0">
                    <button @click="open = !open" type="button" class="flex items-center gap-2 px-5 py-3 rounded-xl font-bold text-sm transition-all shadow-lg border border-white/20 text-white bg-white/10 hover:bg-white/20 backdrop-blur-sm">
                        <i data-lucide="filter" class="w-4 h-4" x-show="!open"></i>
                        <i data-lucide="filter-x" class="w-4 h-4" x-show="open" x-cloak></i>
                        <span x-text="open ? 'Ocultar Filtros' : 'Mostrar Filtros'"></span>
                    </button>
                    <a href="{{ route('usuario.actas.create') }}" class="flex items-center gap-2 px-5 py-3 rounded-xl font-bold text-sm transition-all shadow-lg bg-white text-emerald-700 hover:bg-emerald-50 border border-transparent">
                        <i data-lucide="plus-circle" class="w-5 h-5"></i>
                        <span>Nueva Acta</span>
                    </a>
                </div>
            </div>
        </div>

        {{-- FILTROS --}}
        <form x-show="open" x-cloak x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
            method="GET" action="{{ route('usuario.actas.index') }}"
            class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200 mb-6 space-y-4">
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-7 gap-3">
                {{-- Implementador --}}
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1 ml-1">Implementador</label>
                    <select name="implementador" class="input-modern w-full uppercase">
                        <option value="">Todos</option>
                        @foreach ($implementadores as $impl)
                            <option value="{{ $impl }}" {{ request('implementador') == $impl ? 'selected' : '' }}>{{ $impl }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Provincia --}}
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1 ml-1">Provincia</label>
                    <select id="provincia" name="provincia" class="input-modern w-full uppercase">
                        <option value="">Todas</option>
                        @foreach ($provincias as $prov)
                            <option value="{{ $prov }}" {{ request('provincia') == $prov ? 'selected' : '' }}>{{ $prov }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Distrito --}}
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1 ml-1">Distrito</label>
                    <select id="distrito" name="distrito" class="input-modern w-full uppercase">
                        <option value="">Todos</option>
                        @isset($distritos)
                            @foreach ($distritos as $dist)
                                <option value="{{ $dist }}" {{ request('distrito') == $dist ? 'selected' : '' }}>{{ $dist }}</option>
                            @endforeach
                        @endisset
                    </select>
                </div>

                {{-- Establecimiento --}}
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1 ml-1">Establ.</label>
                    <select id="establecimiento_id" name="establecimiento_id" class="input-modern w-full uppercase">
                        <option value="">Todos</option>
                        @isset($establecimientos)
                            @foreach ($establecimientos as $est)
                                <option value="{{ $est->id }}" {{ request('establecimiento_id') == $est->id ? 'selected' : '' }}>{{ $est->nombre }}</option>
                            @endforeach
                        @endisset
                    </select>
                </div>

                {{-- Estado --}}
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1 ml-1">Estado</label>
                    <select name="firmado" class="input-modern w-full uppercase">
                        <option value="">Todos</option>
                        <option value="1" {{ request('firmado') === '1' ? 'selected' : '' }}>Firmado</option>
                        <option value="0" {{ request('firmado') === '0' ? 'selected' : '' }}>Pendiente</option>
                    </select>
                </div>

                {{-- Fechas --}}
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1 ml-1">Desde</label>
                    <input type="date" name="fecha_inicio" value="{{ $valInicio }}" class="input-modern w-full">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1 ml-1">Hasta</label>
                    <input type="date" name="fecha_fin" value="{{ $valFin }}" class="input-modern w-full">
                </div>
            </div>

            <div class="flex items-center gap-3 pt-2 border-t border-slate-50">
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs flex items-center gap-2 transition-all">
                    <i data-lucide="search" class="w-4 h-4"></i> FILTRAR
                </button>
                <a href="{{ route('usuario.actas.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs flex items-center gap-2 transition-all">
                    <i data-lucide="rotate-ccw" class="w-4 h-4"></i> LIMPIAR
                </a>
            </div>
        </form>

        {{-- TABLA --}}
        <div class="bg-white rounded-2xl shadow-xl shadow-slate-200/50 border border-slate-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-slate-800 text-white">
                        <tr>
                            <th class="px-3 py-3 text-[10px] font-bold uppercase text-center">#</th>
                            <th class="px-3 py-3 text-[10px] font-bold uppercase text-center">Fecha</th>
                            <th class="px-3 py-3 text-[10px] font-bold uppercase">Establecimiento</th>
                            <th class="px-3 py-3 text-[10px] font-bold uppercase">Modalidad</th>
                            <th class="px-3 py-3 text-[10px] font-bold uppercase">Implementador</th>
                            <th class="px-3 py-3 text-[10px] font-bold uppercase text-center">Estado</th>
                            <th class="px-3 py-3 text-[10px] font-bold uppercase text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs text-slate-600">
                        @forelse($actas as $acta)
                            <tr class="hover:bg-emerald-50/30 transition-colors group">
                                <td class="px-3 py-3 font-mono font-bold text-center text-slate-400">{{ $acta->id }}</td>
                                <td class="px-3 py-3 text-center font-bold">{{ \Carbon\Carbon::parse($acta->fecha)->format('d/m/Y') }}</td>
                                <td class="px-3 py-3 font-semibold text-slate-800">{{ $acta->establecimiento->nombre ?? '—' }}</td>
                                <td class="px-3 py-3">{{ $acta->modalidad }}</td>
                                <td class="px-3 py-3 text-slate-500">{{ $acta->implementador }}</td>
                                <td class="px-3 py-3 text-center whitespace-nowrap">
                                    <div class="flex items-center justify-center gap-1">
                                        @if ($acta->firmado)
                                            <span class="inline-flex px-2 py-0.5 rounded-md text-[9px] font-black bg-emerald-100 text-emerald-700 border border-emerald-200 uppercase">Firmado</span>
                                            @if (!empty($acta->firmado_pdf))
                                                <a href="{{ asset('storage/' . $acta->firmado_pdf) }}" target="_blank" class="text-slate-400 hover:text-emerald-600 p-1"><i data-lucide="eye" class="w-3.5 h-3.5"></i></a>
                                            @endif
                                            <form action="{{ route('usuario.actas.subirPDF', $acta->id) }}" method="POST" enctype="multipart/form-data" class="inline-block m-0">
                                                @csrf
                                                <input type="file" name="pdf_firmado" accept="application/pdf" onchange="this.form.submit()" hidden id="pdf-{{ $acta->id }}">
                                                <label for="pdf-{{ $acta->id }}" class="cursor-pointer text-slate-300 hover:text-emerald-500 p-1" title="Reemplazar"><i data-lucide="refresh-cw" class="w-3.5 h-3.5"></i></label>
                                            </form>
                                        @else
                                            <span class="inline-flex px-2 py-0.5 rounded-md text-[9px] font-black bg-amber-100 text-amber-700 border border-amber-200 uppercase">Pendiente</span>
                                            <form action="{{ route('usuario.actas.subirPDF', $acta->id) }}" method="POST" enctype="multipart/form-data" class="inline-block m-0 ml-1">
                                                @csrf
                                                <input type="file" name="pdf_firmado" accept="application/pdf" onchange="this.form.submit()" hidden id="pdf-u-{{ $acta->id }}">
                                                <label for="pdf-u-{{ $acta->id }}" class="cursor-pointer text-slate-400 hover:text-emerald-600 p-1" title="Subir"><i data-lucide="upload-cloud" class="w-3.5 h-3.5"></i></label>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-3 py-3 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ route('usuario.actas.generarPDF', $acta->id) }}" target="_blank" class="p-1.5 rounded-lg text-slate-400 hover:text-red-600 hover:bg-red-50 transition-all" title="PDF"><i data-lucide="file-text" class="w-4 h-4"></i></a>
                                        <a href="{{ route('usuario.actas.edit', $acta->id) }}" class="p-1.5 rounded-lg text-slate-400 hover:text-amber-600 hover:bg-amber-50 transition-all" title="Editar"><i data-lucide="pencil" class="w-4 h-4"></i></a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="px-6 py-12 text-center text-slate-400 text-xs italic">No se encontraron actas registradas</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($actas->hasPages()) <div class="p-4 border-t border-slate-50 text-xs">{{ $actas->appends(request()->query())->links() }}</div> @endif
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => { if (window.lucide) window.lucide.createIcons(); });

        const provinciaSelect = document.getElementById('provincia');
        const distritoSelect = document.getElementById('distrito');
        const establecimientoSelect = document.getElementById('establecimiento_id');

        provinciaSelect.addEventListener('change', () => {
            fetch(`{{ route('usuario.actas.ajax.distritos') }}?provincia=${provinciaSelect.value}`)
                .then(r => r.json())
                .then(data => {
                    distritoSelect.innerHTML = '<option value="">Todos</option>';
                    establecimientoSelect.innerHTML = '<option value="">Todos</option>';
                    data.forEach(d => {
                        distritoSelect.innerHTML += `<option value="${d}">${d}</option>`;
                    });
                });
        });

        distritoSelect.addEventListener('change', () => {
            fetch(`{{ route('usuario.actas.ajax.establecimientos') }}?provincia=${provinciaSelect.value}&distrito=${distritoSelect.value}`)
                .then(r => r.json())
                .then(data => {
                    establecimientoSelect.innerHTML = '<option value="">Todos</option>';
                    data.forEach(e => {
                        establecimientoSelect.innerHTML += `<option value="${e.id}">${e.nombre}</option>`;
                    });
                });
        });

        @if (session('success'))
            Swal.fire({ icon: 'success', title: '¡Éxito!', text: @json(session('success')), toast: true, position: 'top-end', showConfirmButton: false, timer: 3000 });
        @endif
    </script>
@endpush