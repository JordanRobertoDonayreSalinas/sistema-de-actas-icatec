@extends('layouts.usuario')

@section('title', 'Dashboard')

{{-- HEADER SUPERIOR --}}
@section('header-content')
    <h1 class="text-xl font-bold text-slate-800 tracking-tight">
        Bienvenido de nuevo, {{ Auth::user()->name }} 
    </h1>
    <div class="flex items-center gap-2 text-xs text-slate-500 mt-0.5">
        <span>Plataforma</span>
        <span class="text-slate-300">•</span>
        <span>Dashboard</span>
    </div>
@endsection

{{-- CONTENIDO DEL DASHBOARD --}}
@section('content')
    <div class="max-w-7xl mx-auto space-y-8">

        {{-- 1. SECCIÓN MÉTRICAS --}}
        <div>
            <h3 class="text-sm font-bold text-slate-500 uppercase tracking-widest mb-4">Estadística</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                {{-- POSICIÓN 1: TOTAL ACTAS (VERDE) --}}
                <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 p-8 text-white shadow-xl shadow-emerald-500/10 group transition-transform hover:scale-[1.01]">
                    <div class="absolute -right-10 -top-10 h-40 w-40 rounded-full bg-white/10 blur-3xl transition-all group-hover:bg-white/20"></div>
                    
                    <div class="relative z-10">
                        <div class="flex items-start justify-between mb-4">
                            <div>
                                <p class="text-emerald-100 font-medium tracking-wide text-sm uppercase">Total Actas</p>
                                <h3 class="mt-1 text-4xl font-extrabold tracking-tight">{{ $totalActas }}</h3>
                            </div>
                            <div class="rounded-xl bg-white/20 p-3 backdrop-blur-sm">
                                <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            </div>
                        </div>
                        <div class="h-px w-full bg-white/20 mb-4"></div>
                        
                        {{-- LISTA MENSUAL --}}
                        <div>
                            <p class="text-[10px] font-bold text-emerald-100 uppercase tracking-widest mb-3">Historial por Mes</p>
                            <div class="h-40 overflow-y-auto custom-scroll pr-2 space-y-2">
                                @forelse($actasPorMes as $registro)
                                    <div class="flex justify-between items-center bg-black/10 rounded px-3 py-2 text-sm hover:bg-black/20 transition-colors">
                                        <span class="font-medium">{{ $registro['nombre_mes'] }}</span>
                                        <span class="bg-white text-teal-700 font-bold px-2 py-0.5 rounded text-xs min-w-[30px] text-center">
                                            {{ $registro['total'] }}
                                        </span>
                                    </div>
                                @empty
                                    <p class="text-sm text-emerald-100 italic">No hay registros aún.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                {{-- POSICIÓN 2: RANKING ESTABLECIMIENTOS (VIOLETA/INDIGO) --}}
                <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-indigo-600 to-purple-600 p-8 text-white shadow-xl shadow-indigo-500/10 group transition-transform hover:scale-[1.01]">
                    <div class="absolute -right-10 -top-10 h-40 w-40 rounded-full bg-white/10 blur-3xl transition-all group-hover:bg-white/20"></div>
                    
                    <div class="relative z-10 flex flex-col justify-between h-full">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <p class="text-indigo-100 font-medium tracking-wide text-sm uppercase">Ranking Establecimientos</p>
                                <h3 class="mt-1 text-2xl font-bold tracking-tight">Top Actividad</h3>
                            </div>
                            <div class="rounded-xl bg-white/20 p-3 backdrop-blur-sm">
                                <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                            </div>
                        </div>

                        <div class="h-px w-full bg-white/20 mb-4"></div>

                        {{-- LISTA TOP ESTABLECIMIENTOS --}}
                        <div>
                            <p class="text-[10px] font-bold text-indigo-100 uppercase tracking-widest mb-3">Mayor generación de actas</p>
                            <div class="h-40 overflow-y-auto custom-scroll pr-2 space-y-2">
                                @if(isset($topEstablecimientos) && count($topEstablecimientos) > 0)
                                    @foreach($topEstablecimientos as $est)
                                        <div class="flex justify-between items-center bg-black/10 rounded px-3 py-2 text-sm hover:bg-black/20 transition-colors">
                                            <span class="font-medium truncate w-2/3" title="{{ $est->nombre }}">
                                                {{ $est->nombre }}
                                            </span>
                                            <div class="flex items-center gap-2">
                                                <span class="bg-white text-purple-700 font-bold px-2 py-0.5 rounded text-xs min-w-[30px] text-center">
                                                    {{ $est->actas_count }}
                                                </span>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="flex flex-col items-center justify-center h-32 text-indigo-100/60">
                                        <i data-lucide="hospital" class="w-8 h-8 mb-2"></i>
                                        <p class="text-xs italic">Sin datos de establecimientos.</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        
        </div>

    </div>
@endsection