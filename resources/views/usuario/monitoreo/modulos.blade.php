@extends('layouts.usuario')

@section('title', 'Panel de Módulos - ' . $acta->establecimiento->nombre)

@section('content')
<div class="py-10 bg-slate-50 min-h-screen">
    <div class="max-w-5xl mx-auto px-4">
        
        {{-- ENCABEZADO DEL ACTA --}}
        <div class="bg-white border border-slate-200 rounded-[2.5rem] p-8 shadow-sm mb-8 flex flex-col md:flex-row justify-between items-center gap-6">
            <div class="flex items-center gap-6">
                <div class="h-16 w-16 rounded-2xl bg-indigo-600 flex items-center justify-center shadow-lg shadow-indigo-200">
                    <i data-lucide="layout-grid" class="text-white w-8 h-8"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="px-3 py-1 rounded-full bg-indigo-100 text-indigo-700 text-[10px] font-black uppercase">Monitoreo Modular</span>
                        <span class="text-slate-300">/</span>
                        <span class="text-slate-500 text-[10px] font-bold uppercase">ID: {{ $acta->id }}</span>
                    </div>
                    <h2 class="text-2xl font-black text-slate-800 mt-1 italic">{{ $acta->establecimiento->nombre }}</h2>
                    <p class="text-slate-400 text-xs font-bold mt-1 uppercase tracking-tight flex items-center gap-2">
                        <i data-lucide="map-pin" class="w-3 h-3"></i> {{ $acta->establecimiento->distrito }}, {{ $acta->establecimiento->provincia }}
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('usuario.monitoreo.index') }}" class="px-6 py-3 rounded-xl border border-slate-200 text-slate-400 font-black text-xs hover:bg-slate-50 transition-all uppercase">Cancelar</a>
            </div>
        </div>

        {{-- GRID DE MÓDULOS --}}
        <div class="space-y-6">
            <div class="flex items-center justify-between px-4">
                <h3 class="text-[11px] font-black text-slate-400 uppercase tracking-[0.3em]">Seleccione un componente para evaluar</h3>
                <span class="text-[10px] font-bold text-indigo-500 bg-indigo-50 px-3 py-1 rounded-full uppercase">8 Módulos Disponibles</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @php
                    $modulosLinks = [
                        'programacion'   => ['nombre' => 'Programación de consultorios y turnos', 'icon' => 'calendar-range'],
                        'ventanilla'     => ['nombre' => 'Ventanilla única', 'icon' => 'monitor'],
                        'caja'           => ['nombre' => 'Caja', 'icon' => 'banknote'],
                        'triaje'         => ['nombre' => 'Triaje', 'icon' => 'stethoscope'],
                        'medicina'       => ['nombre' => 'Consulta Externa: Medicina', 'icon' => 'user-plus'],
                        'cred'           => ['nombre' => 'Control de Crecimiento y Desarrollo', 'icon' => 'baby'],
                        'inmunizaciones' => ['nombre' => 'Inmunizaciones', 'icon' => 'syringe'],
                        'prenatal'       => ['nombre' => 'Atención Prenatal', 'icon' => 'heart-pulse'],
                    ];
                @endphp

                @foreach($modulosLinks as $slug => $data)
                <a href="{{ route('usuario.monitoreo.seccion', ['id' => $acta->id, 'seccion' => $slug]) }}" 
                   class="group relative bg-white border border-slate-200 p-6 rounded-[2rem] flex items-center justify-between hover:border-indigo-500 hover:shadow-xl hover:shadow-indigo-100 transition-all">
                    
                    <div class="flex items-center gap-5">
                        <div class="h-12 w-12 rounded-2xl bg-slate-50 flex items-center justify-center text-slate-400 group-hover:bg-indigo-50 group-hover:text-indigo-600 transition-colors">
                            <i data-lucide="{{ $data['icon'] }}" class="w-6 h-6"></i>
                        </div>
                        <div>
                            <p class="text-[11px] font-black text-slate-800 uppercase tracking-tighter leading-tight">{{ $data['nombre'] }}</p>
                            <p class="text-[9px] font-bold text-slate-400 uppercase mt-1 group-hover:text-indigo-400 transition-colors">Click para abrir formulario</p>
                        </div>
                    </div>

                    <div class="h-8 w-8 rounded-full border border-slate-100 flex items-center justify-center text-slate-300 group-hover:bg-indigo-600 group-hover:text-white group-hover:border-indigo-600 transition-all">
                        <i data-lucide="chevron-right" class="w-4 h-4"></i>
                    </div>

                    {{-- Indicador de completado --}}
                    @if(isset($modulosGuardados) && in_array($slug, $modulosGuardados))
                        <div class="absolute -top-2 -right-2 bg-emerald-500 text-white p-1 rounded-full shadow-lg border-2 border-white">
                            <i data-lucide="check" class="w-3 h-3"></i>
                        </div>
                    @endif
                </a>
                @endforeach
            </div>

            {{-- ACCIÓN FINAL --}}
            <div class="pt-10">
                <a href="{{ route('usuario.monitoreo.pdf', $acta->id) }}" target="_blank" 
                   class="w-full bg-slate-900 text-white p-6 rounded-[2rem] font-black shadow-2xl flex items-center justify-center gap-4 hover:bg-black hover:-translate-y-1 transition-all">
                    <i data-lucide="file-check" class="w-6 h-6 text-indigo-400"></i>
                    <div class="text-left">
                        <p class="text-xs uppercase tracking-widest leading-none">Finalizar Evaluación</p>
                        <p class="text-[10px] text-slate-400 font-bold uppercase mt-1">Generar PDF Consolidado del Monitoreo</p>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection