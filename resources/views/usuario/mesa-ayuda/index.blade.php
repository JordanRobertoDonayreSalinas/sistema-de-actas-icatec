@extends('layouts.usuario')

@section('title', 'Mesa de Ayuda – Incidencias')

@section('header-content')
    <h1 class="text-xl font-bold text-slate-800 tracking-tight">Mesa de Ayuda</h1>
    <div class="flex items-center gap-2 text-xs text-slate-500 mt-0.5">
        <span>Plataforma</span>
        <span class="text-slate-300">•</span>
        <span>Gestión de Incidencias SIHCE</span>
    </div>
@endsection

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    {{-- HEADER + Acciones --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-lg font-bold text-slate-800">Incidencias Reportadas</h2>
            <p class="text-sm text-slate-500 mt-0.5">Gestiona y responde los reportes técnicos de los establecimientos.</p>
        </div>
        <a href="{{ route('mesa-ayuda.form') }}" target="_blank"
            class="inline-flex items-center gap-2 bg-orange-600 hover:bg-orange-700 text-white text-sm font-bold px-5 py-2.5 rounded-xl transition-all shadow-md shadow-orange-100 hover:scale-105">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Ver formulario público
        </a>
    </div>

    {{-- ESTADÍSTICAS --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl border border-slate-200 p-5 flex items-center gap-4 shadow-sm">
            <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center">
                <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <div>
                <p class="text-2xl font-extrabold text-slate-800">{{ $stats['total'] }}</p>
                <p class="text-xs text-slate-500 font-medium">Total</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-amber-200 p-5 flex items-center gap-4 shadow-sm">
            <div class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center">
                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-2xl font-extrabold text-amber-700">{{ $stats['pendientes'] }}</p>
                <p class="text-xs text-amber-600 font-medium">Pendientes</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-blue-200 p-5 flex items-center gap-4 shadow-sm">
            <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
            </div>
            <div>
                <p class="text-2xl font-extrabold text-blue-700">{{ $stats['en_proceso'] }}</p>
                <p class="text-xs text-blue-600 font-medium">En proceso</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-emerald-200 p-5 flex items-center gap-4 shadow-sm">
            <div class="w-10 h-10 rounded-xl bg-emerald-100 flex items-center justify-center">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <div>
                <p class="text-2xl font-extrabold text-emerald-700">{{ $stats['resueltos'] }}</p>
                <p class="text-xs text-emerald-600 font-medium">Resueltos</p>
            </div>
        </div>
    </div>

    {{-- ALERTAS --}}
    @if(session('success'))
        <div class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-3 rounded-xl text-sm">
            <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-700 px-5 py-3 rounded-xl text-sm">
            <svg class="w-5 h-5 text-red-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
            {{ session('error') }}
        </div>
    @endif

    {{-- FILTROS --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
        <form method="GET" action="{{ route('usuario.mesa-ayuda.index') }}"
            class="flex flex-wrap items-end gap-4">
            <div class="flex flex-col gap-1.5 flex-1 min-w-[140px]">
                <label class="text-xs font-bold text-slate-500 uppercase tracking-wide">N° Ticket</label>
                <input type="number" name="ticket" value="{{ request('ticket') }}" placeholder="Ej: 15"
                    class="border border-slate-200 rounded-xl px-4 py-2 text-sm text-slate-700 bg-white focus:outline-none focus:ring-2 focus:ring-orange-400 transition">
            </div>
            <div class="flex flex-col gap-1.5 flex-1 min-w-[140px]">
                <label class="text-xs font-bold text-slate-500 uppercase tracking-wide">Estado</label>
                <select name="estado"
                    class="border border-slate-200 rounded-xl px-4 py-2 text-sm text-slate-700 bg-white focus:outline-none focus:ring-2 focus:ring-orange-400 transition">
                    <option value="">Todos</option>
                    <option value="Pendiente"  {{ request('estado') === 'Pendiente'  ? 'selected' : '' }}>🟡 Pendiente</option>
                    <option value="En proceso" {{ request('estado') === 'En proceso' ? 'selected' : '' }}>🔵 En proceso</option>
                    <option value="Resuelto"   {{ request('estado') === 'Resuelto'   ? 'selected' : '' }}>🟢 Resuelto</option>
                </select>
            </div>
            <div class="flex flex-col gap-1.5 flex-1 min-w-[160px]">
                <label class="text-xs font-bold text-slate-500 uppercase tracking-wide">Provincia</label>
                <select name="provincia"
                    class="border border-slate-200 rounded-xl px-4 py-2 text-sm text-slate-700 bg-white focus:outline-none focus:ring-2 focus:ring-orange-400 transition">
                    <option value="">Todas</option>
                    @foreach($provincias as $prov)
                        <option value="{{ $prov }}" {{ request('provincia') === $prov ? 'selected' : '' }}>{{ $prov }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit"
                class="flex items-center gap-2 bg-orange-600 hover:bg-orange-700 text-white text-sm font-bold px-5 py-2 rounded-xl transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
                </svg>
                Filtrar
            </button>
            @if(request()->hasAny(['ticket', 'estado', 'provincia']))
                <a href="{{ route('usuario.mesa-ayuda.index') }}"
                    class="flex items-center gap-2 bg-slate-100 hover:bg-slate-200 text-slate-600 text-sm font-bold px-5 py-2 rounded-xl transition">
                    Limpiar
                </a>
            @endif
        </form>
    </div>

    {{-- TABLA --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        @if($incidencias->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-widest">Ticket</th>
                        <th class="px-5 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-widest">Profesional</th>
                        <th class="px-5 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-widest">Establecimiento</th>
                        <th class="px-5 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-widest">Módulo</th>
                        <th class="px-5 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-widest">Estado</th>
                        <th class="px-5 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-widest">Fecha</th>
                        <th class="px-5 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-widest">Acción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($incidencias as $inc)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-5 py-4">
                            <span class="font-bold text-orange-700 bg-orange-50 px-2.5 py-1 rounded-lg border border-orange-200 text-xs">
                                #{{ $inc->id }}
                            </span>
                        </td>
                        <td class="px-5 py-4">
                            <p class="font-semibold text-slate-800">{{ $inc->apellidos }}, {{ $inc->nombres }}</p>
                            <p class="text-xs text-slate-400 mt-0.5">DNI: {{ $inc->dni }}</p>
                        </td>
                        <td class="px-5 py-4">
                            <p class="font-medium text-slate-700 leading-tight">{{ Str::limit($inc->nombre_establecimiento, 35) }}</p>
                            <p class="text-xs text-slate-400 mt-0.5">{{ $inc->distrito_establecimiento }} – {{ $inc->provincia_establecimiento }}</p>
                        </td>
                        <td class="px-5 py-4">
                            <span class="text-xs font-semibold text-slate-600 bg-slate-100 px-2.5 py-1 rounded-lg">
                                {{ str_replace('_', ' ', ucwords($inc->modulos, '_')) }}
                            </span>
                        </td>
                        <td class="px-5 py-4">
                            @if($inc->estado === 'Pendiente')
                                <span class="inline-flex items-center gap-1.5 text-xs font-bold text-amber-700 bg-amber-100 px-2.5 py-1 rounded-full">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Pendiente
                                </span>
                            @elseif($inc->estado === 'En proceso')
                                <span class="inline-flex items-center gap-1.5 text-xs font-bold text-blue-700 bg-blue-100 px-2.5 py-1 rounded-full">
                                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span> En proceso
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 text-xs font-bold text-emerald-700 bg-emerald-100 px-2.5 py-1 rounded-full">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Resuelto
                                </span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-xs text-slate-500 whitespace-nowrap">
                            {{ $inc->created_at->format('d/m/Y') }}<br>
                            <span class="text-slate-400">{{ $inc->created_at->format('H:i') }} h</span>
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-2">
                                @if(Auth::user()->role === 'admin')
                                <a href="{{ route('usuario.mesa-ayuda.responder', $inc->id) }}"
                                    class="flex items-center justify-center w-8 h-8 rounded-lg bg-orange-50 text-orange-600 hover:bg-orange-600 hover:text-white transition-colors"
                                    title="Responder / Atender">
                                    <i data-lucide="message-square-reply" class="w-4 h-4"></i>
                                </a>
                                @else
                                <span class="text-xs text-slate-400 italic">Solo lectura</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Paginación --}}
        @if($incidencias->hasPages())
        <div class="px-5 py-4 border-t border-slate-100">
            {{ $incidencias->links() }}
        </div>
        @endif

        @else
        <div class="flex flex-col items-center justify-center py-20 text-center">
            <div class="w-16 h-16 rounded-2xl bg-slate-100 flex items-center justify-center mb-4">
                <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <h3 class="font-bold text-slate-600 mb-1">Sin incidencias registradas</h3>
            <p class="text-sm text-slate-400">No hay reportes que coincidan con los filtros aplicados.</p>
        </div>
        @endif
    </div>
</div>
@endsection
