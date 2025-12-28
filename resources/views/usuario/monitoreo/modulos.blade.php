@extends('layouts.usuario')

@section('title', 'Panel de Gestión Modular - ' . $acta->establecimiento->nombre)

@section('content')
<div class="py-12 bg-[#f8fafc] min-h-screen">
    <div class="max-w-5xl mx-auto px-6">
        
        {{-- ENCABEZADO: DISEÑO CORPORATIVO DE ALTO IMPACTO --}}
        <div class="bg-white border border-slate-200 rounded-[3rem] p-10 shadow-2xl shadow-slate-200/60 mb-10 relative overflow-hidden">
            {{-- Decoración sutil de fondo --}}
            <div class="absolute top-0 right-0 w-40 h-40 bg-indigo-50 rounded-full -mr-20 -mt-20 opacity-60"></div>
            <div class="absolute bottom-0 left-0 w-24 h-24 bg-slate-50 rounded-full -ml-12 -mb-12 opacity-80"></div>
            
            <div class="flex flex-col md:flex-row justify-between items-center gap-8 relative z-10">
                <div class="flex items-center gap-8">
                    <div class="h-20 w-20 rounded-3xl bg-indigo-600 flex items-center justify-center shadow-2xl shadow-indigo-200 transform -rotate-3 hover:rotate-0 transition-transform duration-500">
                        <i data-lucide="layout-dashboard" class="text-white w-10 h-10"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-3">
                            <span class="px-4 py-1.5 rounded-full bg-indigo-100 text-indigo-700 text-[10px] font-black uppercase tracking-[0.15em]">Sistema de Monitoreo Modular</span>
                            <span class="text-slate-300">/</span>
                            <span class="text-slate-500 text-[10px] font-bold uppercase">ID: {{ str_pad($acta->id, 5, '0', STR_PAD_LEFT) }}</span>
                        </div>
                        <h2 class="text-3xl font-black text-slate-900 mt-2 italic tracking-tight">{{ $acta->establecimiento->nombre }}</h2>
                        <p class="text-slate-400 text-[11px] font-bold mt-2 uppercase tracking-widest flex items-center gap-2">
                            <i data-lucide="map-pin" class="w-4 h-4 text-indigo-400"></i> {{ $acta->establecimiento->distrito }}, {{ $acta->establecimiento->provincia }}
                        </p>
                    </div>
                </div>
                <div>
                    <a href="{{ route('usuario.monitoreo.index') }}" 
                       class="group flex items-center gap-3 px-8 py-4 rounded-2xl border-2 border-slate-100 text-slate-400 font-black text-xs hover:bg-slate-50 hover:text-slate-600 transition-all uppercase tracking-widest">
                       <i data-lucide="chevron-left" class="w-4 h-4 group-hover:-translate-x-1 transition-transform"></i>
                       Volver
                    </a>
                </div>
            </div>
        </div>

        {{-- CUERPO DEL PANEL --}}
        <div class="space-y-8">
            <div class="flex items-center justify-between px-6">
                <div class="space-y-1">
                    <h3 class="text-[12px] font-black text-slate-900 uppercase tracking-[0.4em]">Módulos de Evaluación Técnica</h3>
                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-tight">Seleccione un componente para registrar o revisar información</p>
                </div>
                <div class="flex flex-col items-end">
                    <span class="text-[11px] font-black text-indigo-600 bg-indigo-50 px-5 py-2 rounded-full uppercase tracking-widest border border-indigo-100 shadow-sm">
                        {{ count($modulosGuardados) }} / 8 Completados
                    </span>
                    <div class="w-full h-1 bg-slate-100 rounded-full mt-3 overflow-hidden">
                        <div class="h-full bg-indigo-500 transition-all duration-1000" style="width: {{ (count($modulosGuardados)/8)*100 }}%"></div>
                    </div>
                </div>
            </div>

            {{-- GRID DE MÓDULOS --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
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
                @php $isCompleted = in_array($slug, $modulosGuardados); @endphp
                
                <div class="relative bg-white border {{ $isCompleted ? 'border-emerald-200' : 'border-slate-200' }} p-2 rounded-[2.5rem] flex items-center shadow-sm hover:shadow-2xl transition-all duration-500 group">
                    
                    {{-- Parte Izquierda: Acceso al Formulario --}}
                    <a href="{{ route('usuario.monitoreo.seccion', ['id' => $acta->id, 'seccion' => $slug]) }}" 
                       class="flex items-center gap-6 p-4 flex-1 rounded-[2rem] hover:bg-slate-50 transition-colors">
                        <div class="h-16 w-16 rounded-2xl {{ $isCompleted ? 'bg-emerald-50 text-emerald-600' : 'bg-slate-50 text-slate-400' }} flex items-center justify-center group-hover:scale-105 transition-transform duration-300">
                            <i data-lucide="{{ $data['icon'] }}" class="w-8 h-8"></i>
                        </div>
                        <div>
                            <p class="text-[13px] font-black text-slate-800 uppercase tracking-tight">{{ $data['nombre'] }}</p>
                            <p class="text-[9px] font-bold {{ $isCompleted ? 'text-emerald-500' : 'text-slate-400' }} uppercase mt-1.5 flex items-center gap-2">
                                @if($isCompleted)
                                    <i data-lucide="check-circle-2" class="w-3 h-3"></i> Registro verificado
                                @else
                                    <i data-lucide="circle-dashed" class="w-3 h-3"></i> Pendiente de evaluación
                                @endif
                            </p>
                        </div>
                    </a>

                    {{-- Parte Derecha: Acciones de PDF y Estado --}}
                    <div class="flex items-center gap-3 px-4">
                        @if($isCompleted)
                            <a href="{{ route('usuario.monitoreo.pdf.modulo', ['id' => $acta->id, 'modulo' => $slug]) }}" 
                               target="_blank"
                               title="Ver Reporte Técnico"
                               class="h-12 w-12 rounded-2xl bg-red-50 text-red-500 flex items-center justify-center hover:bg-red-600 hover:text-white transition-all shadow-sm border border-red-100">
                                <i data-lucide="file-text" class="w-6 h-6"></i>
                            </a>
                            <div class="h-12 w-12 bg-emerald-500 text-white rounded-2xl flex items-center justify-center shadow-lg shadow-emerald-100 border-2 border-white">
                                <i data-lucide="check" class="w-7 h-7"></i>
                            </div>
                        @else
                            <div class="h-10 w-10 rounded-full flex items-center justify-center text-slate-200">
                                <i data-lucide="chevron-right" class="w-6 h-6"></i>
                            </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>

            {{-- ACCIÓN FINAL: BANNER DE CIERRE --}}
            <div class="pt-10">
                <a href="{{ route('usuario.monitoreo.pdf', $acta->id) }}" target="_blank" 
                   class="w-full group bg-slate-900 text-white p-10 rounded-[3.5rem] font-black shadow-2xl flex items-center justify-between hover:bg-black hover:-translate-y-2 transition-all duration-500 overflow-hidden relative">
                    
                    {{-- Brillo animado de fondo --}}
                    <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/5 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000"></div>

                    <div class="flex items-center gap-8 relative z-10">
                        <div class="h-16 w-16 bg-indigo-500 rounded-3xl flex items-center justify-center shadow-xl shadow-indigo-500/20 group-hover:rotate-6 transition-all duration-500">
                            <i data-lucide="file-check" class="w-8 h-8 text-white"></i>
                        </div>
                        <div class="text-left">
                            <p class="text-lg uppercase tracking-[0.3em] leading-none">Generar Acta Consolidada</p>
                            <p class="text-[11px] text-slate-400 font-bold uppercase mt-3 italic tracking-widest">Descargar documento final unificado (PDF)</p>
                        </div>
                    </div>
                    <div class="h-14 w-14 bg-white/10 rounded-full flex items-center justify-center group-hover:bg-indigo-600 transition-colors relative z-10">
                        <i data-lucide="arrow-right" class="w-7 h-7"></i>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    });
</script>
@endpush