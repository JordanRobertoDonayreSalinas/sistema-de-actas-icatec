@extends('layouts.usuario')

@section('title', 'Auditoría de Consistencia')

@section('header-content')
    <h1 class="text-xl font-bold text-slate-800 tracking-tight">Auditoría de Consistencia</h1>
    <div class="flex items-center gap-2 text-xs text-slate-500 mt-0.5">
        <span>Reportes</span>
        <span class="text-slate-300">•</span>
        <span>SIHCE vs Documentos Administrativos</span>
    </div>
@endsection

@section('content')
    <div class="w-full">
        {{-- Tarjeta de Alerta / Info --}}
        <div class="bg-amber-50 border-l-4 border-amber-400 p-4 mb-6 rounded-r-xl shadow-sm">
            <div class="flex items-center gap-3">
                <i data-lucide="alert-triangle" class="w-5 h-5 text-amber-500"></i>
                <div>
                    <h3 class="text-sm font-bold text-amber-800 uppercase tracking-tight">Reporte de Omisiones</h3>
                    <p class="text-xs text-amber-700">Este reporte identifica profesionales que, según los monitoreos,
                        <b>utilizan el sistema SIHCE</b> pero que aún <b>no han sido registrados</b> en el módulo de
                        Documentos Administrativos.</p>
                </div>
            </div>
        </div>

        {{-- Filtros --}}
        <form method="GET" action="{{ route('usuario.auditoria.index') }}"
            class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                <div>
                    <label
                        class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1 block">Provincia</label>
                    <select name="provincia"
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-xs font-bold text-slate-700 focus:ring-2 focus:ring-indigo-500 outline-none transition-all">
                        <option value="">TODAS</option>
                        @foreach($provincias as $prov)
                            <option value="{{ $prov }}" {{ request('provincia') == $prov ? 'selected' : '' }}>{{ $prov }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1 block">Desde</label>
                    <input type="date" name="fecha_inicio" value="{{ $fecha_inicio }}"
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-xs font-bold text-slate-700 focus:ring-2 focus:ring-indigo-500 outline-none transition-all">
                </div>
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1 block">Hasta</label>
                    <input type="date" name="fecha_fin" value="{{ $fecha_fin }}"
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-xs font-bold text-slate-700 focus:ring-2 focus:ring-indigo-500 outline-none transition-all">
                </div>
                <div class="flex gap-2">
                    <button type="submit"
                        class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2.5 px-4 rounded-xl text-xs transition-all shadow-lg shadow-indigo-200 flex items-center justify-center gap-2">
                        <i data-lucide="search" class="w-4 h-4"></i>
                        <span>Filtrar</span>
                    </button>
                    <a href="{{ route('usuario.auditoria.index') }}"
                        class="bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold py-2.5 px-4 rounded-xl text-xs transition-all flex items-center justify-center">
                        <i data-lucide="rotate-cw" class="w-4 h-4"></i>
                    </a>
                </div>
            </div>
        </form>

        {{-- Tabla de Resultados --}}
        <div class="bg-white rounded-2xl shadow-xl shadow-slate-200/50 border border-slate-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-slate-800">
                        <tr>
                            <th class="px-6 py-4 text-[10px] font-bold text-white uppercase tracking-wider">Profesional /
                                DNI</th>
                            <th class="px-6 py-4 text-[10px] font-bold text-white uppercase tracking-wider">Ubicación /
                                IPRESS</th>
                            <th class="px-6 py-4 text-[10px] font-bold text-white uppercase tracking-wider text-center">
                                Módulo Origen</th>
                            <th class="px-6 py-4 text-[10px] font-bold text-white uppercase tracking-wider text-center">
                                Compromiso</th>
                            <th class="px-6 py-4 text-[10px] font-bold text-white uppercase tracking-wider text-center">D.
                                Jurada</th>
                            <th class="px-6 py-4 text-[10px] font-bold text-white uppercase tracking-wider text-center">
                                Ación</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($inconsistencias as $item)
                            <tr class="hover:bg-indigo-50/30 transition-all group">
                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <span
                                            class="font-bold text-slate-800 uppercase text-xs truncate max-w-[200px]">{{ $item['nombre'] }}</span>
                                        <span
                                            class="text-[10px] font-medium text-slate-400 font-mono mt-0.5 tracking-widest">{{ $item['doc'] }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <span
                                            class="font-bold text-slate-600 uppercase text-[10px]">{{ $item['ipress'] }}</span>
                                        <span class="text-[9px] text-slate-400 uppercase mt-0.5">{{ $item['provincia'] }} -
                                            {{ $item['distrito'] }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span
                                        class="bg-slate-100 text-slate-500 px-2 py-1 rounded-lg text-[9px] font-black border border-slate-200 uppercase tracking-tighter">
                                        {{ $item['modulo_origen'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span
                                        class="px-2 py-1 rounded-full font-black text-[9px] bg-red-100 text-red-600 animate-pulse">
                                        {{ $item['estado_compromiso'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span
                                        class="px-2 py-1 rounded-full font-black text-[9px] bg-red-100 text-red-600 animate-pulse">
                                        {{ $item['estado_dj'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <a href="{{ route('usuario.documentos.create', ['dni' => $item['doc']]) }}"
                                        class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-600 hover:text-white transition-all shadow-sm"
                                        title="Crear Documento">
                                        <i data-lucide="plus-circle" class="w-4 h-4"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-20 text-center">
                                    <div class="flex flex-col items-center opacity-20">
                                        <i data-lucide="check-circle-2" class="w-12 h-12 mb-2 text-emerald-500"></i>
                                        <span class="text-xs font-black uppercase tracking-widest">¡Felicidades! No se
                                            detectaron inconsistencias</span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof lucide !== 'undefined') lucide.createIcons();
        });
    </script>
@endpush