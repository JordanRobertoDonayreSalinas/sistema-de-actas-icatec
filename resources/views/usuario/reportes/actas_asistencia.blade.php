@extends('layouts.usuario')

@section('title', 'Reporte de Actas de Asistencia Técnica')

@push('styles')
<style>
    [x-cloak] { display: none !important; }
    .input-filter {
        background-color: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 0.75rem;
        color: #334155;
        font-size: 0.75rem;
        font-weight: 600;
        padding: 0.5rem 0.75rem;
        width: 100%;
        transition: all 0.2s;
    }
    .input-filter:focus {
        outline: none;
        border-color: #10b981;
        box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.15);
    }
</style>
@endpush

@section('header-content')
    <div>
        <h1 class="text-xl font-bold text-slate-800 tracking-tight">📋 Reporte de Asistencia Técnica</h1>
        <div class="flex items-center gap-2 text-xs text-slate-500 mt-0.5">
            <span>Reportes</span>
            <span class="text-slate-300">•</span>
            <span>Actas de Asistencia Técnica</span>
        </div>
    </div>
@endsection

@section('content')
<div class="max-w-[1400px] mx-auto space-y-6">

    {{-- ========== KPIs ========== --}}
    <div class="bg-gradient-to-r from-emerald-600 to-teal-500 p-5 rounded-2xl shadow-xl relative overflow-hidden text-white">
        <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full blur-3xl -mr-16 -mt-16 pointer-events-none"></div>
        <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex flex-wrap items-center gap-3">
                {{-- Total --}}
                <div class="bg-slate-900 text-white rounded-xl px-5 py-2.5 shadow-lg border border-slate-700 flex flex-col items-center min-w-[110px]">
                    <span class="text-2xl font-bold leading-none">{{ $actas->total() }}</span>
                    <span class="text-[0.65rem] uppercase tracking-widest text-slate-400 font-semibold mt-1">Total (Filtro)</span>
                </div>
                {{-- Firmadas --}}
                <div class="bg-white/20 backdrop-blur-md rounded-xl px-5 py-2.5 border border-white/30 flex flex-col items-center min-w-[110px]">
                    <span class="text-2xl font-bold leading-none">{{ $totalFirmadas }}</span>
                    <span class="text-[0.65rem] uppercase tracking-widest text-emerald-100 font-semibold mt-1">Firmadas</span>
                </div>
                {{-- Pendientes --}}
                <div class="bg-amber-500 text-white rounded-xl px-5 py-2.5 shadow-lg border border-amber-400 flex flex-col items-center min-w-[110px]">
                    <span class="text-2xl font-bold leading-none">{{ $totalPendientes }}</span>
                    <span class="text-[0.65rem] uppercase tracking-widest text-amber-100 font-semibold mt-1">Pendientes</span>
                </div>
            </div>
            <div class="flex items-center gap-2 text-sm font-semibold text-white/80">
                <i data-lucide="calendar-range" class="w-4 h-4"></i>
                {{ \Carbon\Carbon::parse($fechaInicio)->format('d/m/Y') }} — {{ \Carbon\Carbon::parse($fechaFin)->format('d/m/Y') }}
            </div>
        </div>
    </div>

    {{-- ========== FILTROS ========== --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5">
        <form method="GET" action="{{ route('usuario.reportes.actas.asistencia') }}" id="filtroForm" class="space-y-4">

            <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-8 gap-3">
                {{-- Fecha Inicio --}}
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1 ml-1">Desde</label>
                    <input type="date" name="fecha_inicio" value="{{ $fechaInicio }}" class="input-filter">
                </div>
                {{-- Fecha Fin --}}
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1 ml-1">Hasta</label>
                    <input type="date" name="fecha_fin" value="{{ $fechaFin }}" class="input-filter">
                </div>
                {{-- Implementador --}}
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1 ml-1">Implementador</label>
                    <select name="implementador" class="input-filter uppercase">
                        <option value="">Todos</option>
                        @foreach($implementadores as $impl)
                            <option value="{{ $impl }}" {{ request('implementador') == $impl ? 'selected' : '' }}>{{ $impl }}</option>
                        @endforeach
                    </select>
                </div>
                {{-- Tema / Motivo --}}
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1 ml-1">Tema / Motivo</label>
                    <select name="tema" class="input-filter">
                        <option value="">Todos</option>
                        @foreach($temas as $t)
                            <option value="{{ $t }}" {{ request('tema') == $t ? 'selected' : '' }}>{{ $t }}</option>
                        @endforeach
                    </select>
                </div>
                {{-- Módulo (de participantes) --}}
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1 ml-1">Módulo</label>
                    <select name="modulo" class="input-filter">
                        <option value="">Todos</option>
                        @foreach($modulos as $mod)
                            <option value="{{ $mod }}" {{ request('modulo') == $mod ? 'selected' : '' }}>{{ $mod }}</option>
                        @endforeach
                    </select>
                </div>
                {{-- Provincia --}}
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1 ml-1">Provincia</label>
                    <select id="provinciaSelect" name="provincia" class="input-filter uppercase">
                        <option value="">Todas</option>
                        @foreach($provincias as $prov)
                            <option value="{{ $prov }}" {{ request('provincia') == $prov ? 'selected' : '' }}>{{ $prov }}</option>
                        @endforeach
                    </select>
                </div>
                {{-- Distrito --}}
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1 ml-1">Distrito</label>
                    <select id="distritoSelect" name="distrito" class="input-filter uppercase">
                        <option value="">Todos</option>
                    </select>
                </div>
                {{-- Estado --}}
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1 ml-1">Estado</label>
                    <select name="firmado" class="input-filter uppercase">
                        <option value="">Todos</option>
                        <option value="1" {{ request('firmado') === '1' ? 'selected' : '' }}>Firmado</option>
                        <option value="0" {{ request('firmado') === '0' ? 'selected' : '' }}>Pendiente</option>
                    </select>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2 pt-2 border-t border-slate-100">
                <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl flex items-center gap-2 transition-all">
                    <i data-lucide="search" class="w-4 h-4"></i> FILTRAR
                </button>
                <a href="{{ route('usuario.reportes.actas.asistencia') }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs rounded-xl flex items-center gap-2 transition-all">
                    <i data-lucide="rotate-ccw" class="w-4 h-4"></i> LIMPIAR
                </a>
                <div class="ml-auto">
                    @if($actas->count() > 0)
                    <button type="button" onclick="exportarExcel()" class="px-5 py-2.5 bg-green-50 text-green-700 hover:bg-green-100 font-bold text-xs rounded-xl flex items-center gap-2 transition-all border border-green-200">
                        <i data-lucide="file-spreadsheet" class="w-4 h-4"></i> EXPORTAR EXCEL
                    </button>
                    @endif
                </div>
            </div>
        </form>
    </div>

    {{-- ========== TABLA ========== --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="px-5 py-3 bg-slate-50/60 border-b border-slate-100 flex items-center gap-2">
            <i data-lucide="clipboard-list" class="w-4 h-4 text-emerald-600"></i>
            <h3 class="text-sm font-bold text-slate-800">Actas Registradas ({{ $actas->total() }})</h3>
        </div>

        @if($actas->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead class="bg-slate-800 text-white">
                    <tr>
                        <th class="px-3 py-3 text-center font-bold uppercase text-[10px]">#</th>
                        <th class="px-3 py-3 text-center font-bold uppercase text-[10px]">Fecha</th>
                        <th class="px-3 py-3 font-bold uppercase text-[10px]">Establecimiento</th>
                        <th class="px-3 py-3 font-bold uppercase text-[10px]">Provincia</th>
                        <th class="px-3 py-3 font-bold uppercase text-[10px]">Implementador</th>
                        <th class="px-3 py-3 font-bold uppercase text-[10px]">Tema / Motivo</th>
                        <th class="px-3 py-3 font-bold uppercase text-[10px]">Modalidad</th>
                        <th class="px-3 py-3 text-center font-bold uppercase text-[10px]">Partic.</th>
                        <th class="px-3 py-3 text-center font-bold uppercase text-[10px]">Estado</th>
                        <th class="px-3 py-3 text-right font-bold uppercase text-[10px]">PDF</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-600">
                    @foreach($actas as $acta)
                    <tr class="hover:bg-emerald-50/30 transition-colors">
                        <td class="px-3 py-3 text-center font-mono font-bold text-slate-400">{{ $acta->id }}</td>
                        <td class="px-3 py-3 text-center font-bold whitespace-nowrap">
                            {{ \Carbon\Carbon::parse($acta->fecha)->format('d/m/Y') }}
                        </td>
                        <td class="px-3 py-3 font-semibold text-slate-800 max-w-[220px]">
                            <div class="truncate" title="{{ $acta->establecimiento->nombre ?? '—' }}">
                                {{ $acta->establecimiento->nombre ?? '—' }}
                            </div>
                            <div class="text-[10px] text-slate-400 font-normal">{{ $acta->establecimiento->distrito ?? '' }}</div>
                        </td>
                        <td class="px-3 py-3 whitespace-nowrap">{{ $acta->establecimiento->provincia ?? '—' }}</td>
                        <td class="px-3 py-3 text-slate-500">{{ $acta->implementador ?? '—' }}</td>
                        <td class="px-3 py-3">
                            <span class="px-2 py-0.5 rounded text-[9px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-100">
                                {{ $acta->tema ?? '—' }}
                            </span>
                        </td>
                        <td class="px-3 py-3">
                            <span class="px-2 py-0.5 rounded text-[9px] font-bold bg-slate-100 text-slate-600 uppercase">
                                {{ $acta->modalidad ?? '—' }}
                            </span>
                        </td>
                        <td class="px-3 py-3 text-center font-bold text-slate-700">
                            {{ $acta->participantes->count() }}
                        </td>
                        <td class="px-3 py-3 text-center">
                            @if($acta->firmado)
                                <span class="inline-flex px-2 py-0.5 rounded-md text-[9px] font-black bg-emerald-100 text-emerald-700 border border-emerald-200 uppercase">Firmado</span>
                            @else
                                <span class="inline-flex px-2 py-0.5 rounded-md text-[9px] font-black bg-amber-100 text-amber-700 border border-amber-200 uppercase">Pendiente</span>
                            @endif
                        </td>
                        <td class="px-3 py-3 text-right">
                            <a href="{{ route('usuario.actas.generarPDF', $acta->id) }}" target="_blank"
                               class="p-1.5 inline-flex rounded-lg text-slate-400 hover:text-red-600 hover:bg-red-50 transition-all"
                               title="Ver PDF">
                                <i data-lucide="file-text" class="w-4 h-4"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-50 bg-slate-50/30">
            {{ $actas->links() }}
        </div>
        @else
        <div class="py-16 text-center">
            <div class="w-16 h-16 mx-auto mb-3 bg-slate-50 rounded-full flex items-center justify-center">
                <i data-lucide="file-x" class="w-8 h-8 text-slate-300"></i>
            </div>
            <p class="text-slate-400 font-medium">No se encontraron actas con los filtros seleccionados</p>
        </div>
        @endif
    </div>

</div>

{{-- Formulario oculto para exportar Excel --}}
<form id="excelForm" method="POST" action="{{ route('usuario.reportes.actas.asistencia.excel') }}" style="display:none;">
    @csrf
    <input type="hidden" name="fecha_inicio"       value="{{ $fechaInicio }}">
    <input type="hidden" name="fecha_fin"           value="{{ $fechaFin }}">
    <input type="hidden" name="implementador"       value="{{ request('implementador') }}">
    <input type="hidden" name="tema"                value="{{ request('tema') }}">
    <input type="hidden" name="modulo"              value="{{ request('modulo') }}">
    <input type="hidden" name="provincia"           value="{{ request('provincia') }}">
    <input type="hidden" name="distrito"            value="{{ request('distrito') }}">
    <input type="hidden" name="establecimiento_id"  value="{{ request('establecimiento_id') }}">
    <input type="hidden" name="firmado"             value="{{ request('firmado') }}">
</form>
@endsection

@push('scripts')
<script>
    lucide.createIcons();

    function exportarExcel() {
        document.getElementById('excelForm').submit();
    }

    const provinciaSelect  = document.getElementById('provinciaSelect');
    const distritoSelect   = document.getElementById('distritoSelect');

    // Inicializar distritos si hay provincia seleccionada al cargar
    if (provinciaSelect.value) {
        cargarDistritos(provinciaSelect.value, '{{ request('distrito') }}');
    }

    provinciaSelect.addEventListener('change', () => {
        distritoSelect.innerHTML = '<option value="">Todos</option>';
        if (provinciaSelect.value) cargarDistritos(provinciaSelect.value, '');
    });

    function cargarDistritos(provincia, selectedDistrito) {
        fetch(`{{ route('usuario.reportes.actas.asistencia.ajax.distritos') }}?provincia=${encodeURIComponent(provincia)}`)
            .then(r => r.json())
            .then(data => {
                distritoSelect.innerHTML = '<option value="">Todos</option>';
                data.forEach(d => {
                    const opt = document.createElement('option');
                    opt.value = d;
                    opt.textContent = d;
                    if (d === selectedDistrito) opt.selected = true;
                    distritoSelect.appendChild(opt);
                });
            });
    }
</script>
@endpush
