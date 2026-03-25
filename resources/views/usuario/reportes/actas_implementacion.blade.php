@extends('layouts.usuario')

@section('title', 'Reporte de Actas de Implementación')

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
        border-color: #8b5cf6;
        box-shadow: 0 0 0 2px rgba(139, 92, 246, 0.15);
    }
</style>
@endpush

@section('header-content')
    <div>
        <h1 class="text-xl font-bold text-slate-800 tracking-tight">📊 Reporte de Actas de Implementación</h1>
        <div class="flex items-center gap-2 text-xs text-slate-500 mt-0.5">
            <span>Reportes</span>
            <span class="text-slate-300">•</span>
            <span>Actas de Implementación</span>
        </div>
    </div>
@endsection

@section('content')
<div class="max-w-[1400px] mx-auto space-y-6">

    {{-- ========== KPIs ========== --}}
    <div class="bg-gradient-to-r from-purple-600 to-indigo-600 p-5 rounded-2xl shadow-xl relative overflow-hidden text-white">
        <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full blur-3xl -mr-16 -mt-16 pointer-events-none"></div>
        <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex flex-wrap items-center gap-4">
                {{-- Total --}}
                <div class="bg-slate-900 text-white rounded-xl px-5 py-2.5 shadow-lg border border-slate-700 flex flex-col items-center min-w-[120px]">
                    <span class="text-2xl font-bold leading-none">{{ $totalGeneral }}</span>
                    <span class="text-[0.65rem] uppercase tracking-widest text-slate-400 font-semibold mt-1">Actas Registradas</span>
                </div>
                {{-- Personas Asistentes --}}
                <div class="bg-white/20 backdrop-blur-md rounded-xl px-5 py-2.5 border border-white/30 flex flex-col items-center min-w-[120px]">
                    <span class="text-2xl font-bold leading-none">{{ $totalPersonasAsistentes }}</span>
                    <span class="text-[0.65rem] uppercase tracking-widest text-purple-100 font-semibold mt-1">Personas Implementadas</span>
                </div>
                {{-- Módulo Principal --}}
                <div class="bg-emerald-500 text-white rounded-xl px-4 py-2.5 shadow-lg border border-emerald-400 flex flex-col items-center min-w-[120px]">
                    <span class="text-sm font-bold leading-tight truncate max-w-[160px]" title="{{ $moduloMasImplementado }}">{{ $moduloMasImplementado }}</span>
                    <span class="text-[0.6rem] uppercase tracking-widest text-emerald-100 font-semibold mt-1">Módulo más frecuente</span>
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
        <form method="GET" action="{{ route('usuario.reportes.actas.implementacion') }}" id="filtroForm" class="space-y-4">

            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
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
                {{-- Módulo --}}
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1 ml-1">Módulo</label>
                    <select name="modulo_key" class="input-filter uppercase">
                        <option value="">Todos</option>
                        @foreach($modulos as $k => $cfg)
                            <option value="{{ $k }}" {{ request('modulo_key') == $k ? 'selected' : '' }}>{{ $cfg['nombre'] }}</option>
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
                {{-- Responsable --}}
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1 ml-1">Responsable</label>
                    <input type="text" name="responsable" value="{{ request('responsable') }}" placeholder="Nombre..." class="input-filter">
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2 pt-2 border-t border-slate-100">
                <button type="submit" class="px-5 py-2.5 bg-purple-600 hover:bg-purple-700 text-white font-bold text-xs rounded-xl flex items-center gap-2 transition-all shadow-md shadow-purple-500/20">
                    <i data-lucide="search" class="w-4 h-4"></i> FILTRAR
                </button>
                <a href="{{ route('usuario.reportes.actas.implementacion') }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs rounded-xl flex items-center gap-2 transition-all">
                    <i data-lucide="rotate-ccw" class="w-4 h-4"></i> LIMPIAR
                </a>
                <div class="ml-auto">
                    @if($actasPaginadas->count() > 0)
                    <button type="button" onclick="exportarExcel()" class="px-5 py-2.5 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 font-bold text-xs rounded-xl flex items-center gap-2 transition-all border border-emerald-200">
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
            <i data-lucide="pen-tool" class="w-4 h-4 text-purple-600"></i>
            <h3 class="text-sm font-bold text-slate-800">Resultados del Filtro ({{ $actasPaginadas->total() }})</h3>
        </div>

        @if($actasPaginadas->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead class="bg-slate-800 text-white">
                    <tr>
                        <th class="px-3 py-3 text-center font-bold uppercase text-[10px]">N° Acta</th>
                        <th class="px-3 py-3 font-bold uppercase text-[10px]">Módulo</th>
                        <th class="px-3 py-3 text-center font-bold uppercase text-[10px]">Fecha</th>
                        <th class="px-3 py-3 font-bold uppercase text-[10px]">Establecimiento / IPRESS</th>
                        <th class="px-3 py-3 font-bold uppercase text-[10px]">Implementadores</th>
                        <th class="px-3 py-3 text-center font-bold uppercase text-[10px]">Asistentes</th>
                        <th class="px-3 py-3 text-right font-bold uppercase text-[10px]">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-600">
                    @foreach($actasPaginadas as $acta)
                    <tr class="hover:bg-purple-50/30 transition-colors">
                        <td class="px-3 py-3 text-center font-mono font-bold text-slate-500">
                            {{ $acta->id }}
                        </td>
                        <td class="px-3 py-3 font-bold text-purple-700 uppercase whitespace-nowrap">
                            {{ $acta->tipo_nombre }}
                        </td>
                        <td class="px-3 py-3 text-center font-bold whitespace-nowrap">
                            {{ \Carbon\Carbon::parse($acta->fecha)->format('d/m/Y') }}
                        </td>
                        <td class="px-3 py-3 font-semibold text-slate-800 max-w-[240px]">
                            <div class="truncate" title="{{ $acta->nombre_establecimiento }}">
                                {{ $acta->nombre_establecimiento }}
                            </div>
                            <div class="text-[10px] font-normal text-slate-400">
                                IPRESS: {{ $acta->codigo_establecimiento }} • {{ $acta->distrito }}
                            </div>
                        </td>
                        <td class="px-3 py-3 max-w-[200px] truncate text-slate-500" title="{{ $acta->implementadores->map(fn($i) => $i->apellido_paterno)->implode(', ') }}">
                            @if($acta->implementadores->count())
                                {{ $acta->implementadores->first()->apellido_paterno }} {{ $acta->implementadores->first()->nombres }}
                                @if($acta->implementadores->count() > 1) <span class="text-[10px] text-blue-500 font-bold bg-blue-50 px-1 rounded">(+{{$acta->implementadores->count()-1}})</span> @endif
                            @else
                                —
                            @endif
                        </td>
                        <td class="px-3 py-3 text-center">
                            <span class="inline-flex px-2 py-0.5 rounded-md text-[10px] font-black bg-slate-100 text-slate-700 border border-slate-200">
                                <i data-lucide="users" class="w-3 h-3 mr-1"></i> {{ $acta->usuarios->count() }}
                            </span>
                        </td>
                        <td class="px-3 py-3 text-right space-x-1">
                            <a href="{{ route('usuario.implementacion.pdf', ['modulo' => $acta->tipo_key, 'id' => $acta->id]) }}"
                               target="_blank"
                               class="p-1.5 inline-flex rounded-lg text-slate-400 hover:text-red-500 hover:bg-red-50 transition-all"
                               title="Ver PDF">
                                <i data-lucide="file-text" class="w-4 h-4"></i>
                            </a>
                            <a href="{{ route('usuario.implementacion.edit', ['modulo' => $acta->tipo_key, 'id' => $acta->id]) }}"
                               class="p-1.5 inline-flex rounded-lg text-slate-400 hover:text-blue-600 hover:bg-blue-50 transition-all"
                               title="Editar">
                                <i data-lucide="edit" class="w-4 h-4"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-50 bg-slate-50/30">
            {{ $actasPaginadas->withQueryString()->links() }}
        </div>
        @else
        <div class="py-16 text-center">
            <div class="w-16 h-16 mx-auto mb-3 bg-slate-50 rounded-full flex items-center justify-center">
                <i data-lucide="folder-search" class="w-8 h-8 text-slate-300"></i>
            </div>
            <p class="text-slate-400 font-medium">No se encontraron actas con los filtros seleccionados</p>
        </div>
        @endif
    </div>

</div>

{{-- Formulario oculto para exportar Excel --}}
<form id="excelForm" method="POST" action="{{ route('usuario.reportes.actas.implementacion.excel') }}" style="display:none;">
    @csrf
    <input type="hidden" name="fecha_inicio" value="{{ $fechaInicio }}">
    <input type="hidden" name="fecha_fin" value="{{ $fechaFin }}">
    <input type="hidden" name="modulo_key" value="{{ request('modulo_key') }}">
    <input type="hidden" name="provincia" value="{{ request('provincia') }}">
    <input type="hidden" name="distrito" value="{{ request('distrito') }}">
    <input type="hidden" name="responsable" value="{{ request('responsable') }}">
</form>
@endsection

@push('scripts')
<script>
    lucide.createIcons();

    function exportarExcel() {
        document.getElementById('excelForm').submit();
    }

    const provinciaSelect = document.getElementById('provinciaSelect');
    const distritoSelect  = document.getElementById('distritoSelect');

    if (provinciaSelect.value) {
        cargarDistritos(provinciaSelect.value, '{{ request('distrito') }}');
    }

    provinciaSelect.addEventListener('change', () => {
        distritoSelect.innerHTML = '<option value="">Todos</option>';
        if (provinciaSelect.value) cargarDistritos(provinciaSelect.value, '');
    });

    function cargarDistritos(provincia, selectedDistrito) {
        fetch(`{{ route('usuario.reportes.actas.implementacion.ajax.distritos') }}?provincia=${encodeURIComponent(provincia)}`)
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
