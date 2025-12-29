@extends('layouts.usuario')

@section('title', 'Panel de Gestión Modular - ' . $acta->establecimiento->nombre)

@section('content')
<div class="py-12 bg-[#f8fafc] min-h-screen" 
     x-data="{ 
        activos: {{ json_encode($modulosActivos) }},
        modulosFirmados: {{ json_encode($modulosFirmados ?? []) }},
        modulosGuardados: {{ json_encode($modulosGuardados ?? []) }},
        showModal: false,
        currentModule: '',
        currentModuleName: '',
        async toggle(slug) {
            if(this.activos.includes(slug)) {
                this.activos = this.activos.filter(i => i !== slug);
            } else {
                this.activos.push(slug);
            }
            try {
                await fetch('{{ route('usuario.monitoreo.toggle', $acta->id) }}', {
                    method: 'POST',
                    headers: { 
                        'Content-Type': 'application/json', 
                        'X-CSRF-TOKEN': '{{ csrf_token() }}' 
                    },
                    body: JSON.stringify({ modulos_activos: this.activos })
                });
            } catch (error) {
                console.error('Error al guardar configuración:', error);
            }
        },
        openUpload(slug, name) {
            this.currentModule = slug;
            this.currentModuleName = name;
            this.showModal = true;
        }
     }">
    
    <div class="max-w-5xl mx-auto px-6">
        
        {{-- ENCABEZADO --}}
        <div class="bg-white border border-slate-200 rounded-[3rem] p-10 shadow-2xl shadow-slate-200/60 mb-10 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-40 h-40 bg-indigo-50 rounded-full -mr-20 -mt-20 opacity-60"></div>
            
            <div class="flex flex-col md:flex-row justify-between items-center gap-8 relative z-10">
                <div class="flex items-center gap-8">
                    <div class="h-20 w-20 rounded-3xl bg-indigo-600 flex items-center justify-center shadow-2xl shadow-indigo-200">
                        <i data-lucide="layout-dashboard" class="text-white w-10 h-10"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-3">
                            <span class="px-4 py-1.5 rounded-full bg-indigo-100 text-indigo-700 text-[10px] font-black uppercase tracking-[0.15em]">SISTEMA MODULAR</span>
                            <span class="text-slate-500 text-[10px] font-bold uppercase tracking-widest">ID #{{ str_pad($acta->id, 5, '0', STR_PAD_LEFT) }}</span>
                        </div>
                        <h2 class="text-3xl font-black text-slate-900 mt-2 italic tracking-tight">{{ $acta->establecimiento->nombre }}</h2>
                    </div>
                </div>
                <a href="{{ route('usuario.monitoreo.index') }}" class="group flex items-center gap-3 px-8 py-4 rounded-2xl border-2 border-slate-100 text-slate-400 font-black text-xs hover:bg-slate-50 transition-all uppercase tracking-widest">
                    <i data-lucide="chevron-left" class="w-4 h-4"></i> Volver
                </a>
            </div>
        </div>

        {{-- CUERPO DEL PANEL --}}
        <div class="space-y-8">
            <div class="flex flex-col md:flex-row md:items-center justify-between px-6 gap-4">
                <div class="space-y-1">
                    <h3 class="text-[12px] font-black text-slate-900 uppercase tracking-[0.4em]">Módulos de Evaluación Técnica</h3>
                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-tight">Active o desactive módulos según la cartera de servicios de la IPRESS</p>
                </div>
                
                {{-- CONTADOR DINÁMICO --}}
                <div class="flex flex-col items-end">
                    <span class="text-[11px] font-black text-indigo-600 bg-indigo-50 px-5 py-2 rounded-full uppercase tracking-widest border border-indigo-100 shadow-sm">
                        <span x-text="activos.filter(a => modulosGuardados.includes(a)).length"></span> / <span x-text="activos.length"></span> Completados
                    </span>
                    <div class="w-48 h-1.5 bg-slate-100 rounded-full mt-3 overflow-hidden">
                        <div class="h-full bg-indigo-500 transition-all duration-1000" 
                             :style="`width: ${(activos.filter(a => modulosGuardados.includes(a)).length / (activos.length || 1)) * 100}%` "></div>
                    </div>
                </div>
            </div>

            {{-- GRID DE MÓDULOS --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach($modulosMaster as $slug => $data)
                @php 
                    $isCompleted = in_array($slug, $modulosGuardados); 
                    $isSigned = in_array($slug, $modulosFirmados ?? []); 
                    $routeSlug = str_replace('_', '-', $slug);
                    $routeName = "usuario.monitoreo.{$routeSlug}.index";
                    $pdfRouteName = "usuario.monitoreo.{$routeSlug}.pdf";
                    
                    $hasRoute = Route::has($routeName);
                    $hasPdfRoute = Route::has($pdfRouteName);
                    $viewSignedRoute = route('usuario.monitoreo.ver-pdf-firmado', [$acta->id, $slug]);
                @endphp
                
                <div class="relative bg-white border rounded-[2.5rem] flex items-center shadow-sm transition-all duration-500 group"
                     :class="activos.includes('{{ $slug }}') ? '{{ $isCompleted ? 'border-emerald-200' : 'border-slate-200' }}' : 'border-slate-100 bg-slate-50/50 grayscale'">
                    
                    {{-- TOGGLE --}}
                    <div class="absolute -top-3 -right-2 z-20">
                        <button @click="toggle('{{ $slug }}')" 
                                :class="activos.includes('{{ $slug }}') ? 'bg-indigo-600' : 'bg-slate-300'"
                                class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none shadow-sm">
                            <span :class="activos.includes('{{ $slug }}') ? 'translate-x-6' : 'translate-x-1'"
                                  class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform"></span>
                        </button>
                    </div>

                    <div class="flex flex-1 items-center overflow-hidden">
                        <template x-if="activos.includes('{{ $slug }}')">
                            @if($hasRoute)
                                <a href="{{ route($routeName, $acta->id) }}" class="flex items-center gap-6 p-4 flex-1 rounded-[2rem] hover:bg-slate-50 transition-colors">
                                    <div class="h-16 w-16 rounded-2xl {{ $isCompleted ? 'bg-emerald-50 text-emerald-600' : 'bg-indigo-50 text-indigo-500' }} flex items-center justify-center group-hover:scale-105 transition-all">
                                        <i data-lucide="{{ $data['icon'] }}" class="w-8 h-8"></i>
                                    </div>
                                    <div>
                                        <p class="text-[13px] font-black text-slate-800 uppercase tracking-tight">{{ $data['nombre'] }}</p>
                                        <div class="flex items-center gap-2 mt-1">
                                            <p class="text-[9px] font-bold {{ $isCompleted ? 'text-emerald-500' : 'text-slate-400' }} uppercase">
                                                {{ $isCompleted ? 'Completado' : 'Pendiente' }}
                                            </p>
                                            @if($isSigned)
                                                <span class="px-2 py-0.5 bg-emerald-500 text-white text-[8px] font-black rounded-md uppercase tracking-tighter flex items-center gap-1 animate-pulse">
                                                    <i data-lucide="check" class="w-2 h-2"></i> Firmado
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </a>
                            @else
                                <div class="flex items-center gap-6 p-4 flex-1 rounded-[2rem]">
                                    <div class="h-16 w-16 rounded-2xl bg-slate-50 text-slate-400 flex items-center justify-center">
                                        <i data-lucide="{{ $data['icon'] }}" class="w-8 h-8"></i>
                                    </div>
                                    <div>
                                        <p class="text-[13px] font-black text-slate-400 uppercase tracking-tight">{{ $data['nombre'] }}</p>
                                        <p class="text-[9px] font-bold text-slate-300 uppercase mt-1.5 flex items-center gap-2 italic">
                                            Módulo en configuración
                                        </p>
                                    </div>
                                </div>
                            @endif
                        </template>

                        <template x-if="!activos.includes('{{ $slug }}')">
                            <div class="flex items-center gap-6 p-4 flex-1 opacity-40 select-none">
                                <div class="h-16 w-16 rounded-2xl bg-slate-100 flex items-center justify-center">
                                    <i data-lucide="slash" class="w-8 h-8 text-slate-300"></i>
                                </div>
                                <div>
                                    <p class="text-[13px] font-black text-slate-300 uppercase tracking-tight">{{ $data['nombre'] }}</p>
                                    <p class="text-[9px] font-bold text-slate-200 uppercase mt-1.5 italic tracking-widest text-wrap">No aplica a esta categoría</p>
                                </div>
                            </div>
                        </template>
                    </div>

                    {{-- ACCIONES DE PDF --}}
                    <div class="flex items-center gap-2 px-4" x-show="activos.includes('{{ $slug }}') && {{ $isCompleted ? 'true' : 'false' }}">
                        @if($hasPdfRoute)
                        <a href="{{ route($pdfRouteName, $acta->id) }}" target="_blank" class="h-10 w-10 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center hover:bg-indigo-600 hover:text-white transition-all shadow-sm" title="Ver Reporte">
                            <i data-lucide="file-text" class="w-5 h-5"></i>
                        </a>
                        @endif
                        
                        <button @click="openUpload('{{ $slug }}', '{{ $data['nombre'] }}')" class="h-10 w-10 rounded-xl {{ $isSigned ? 'bg-emerald-50 text-emerald-600' : 'bg-orange-50 text-orange-600' }} flex items-center justify-center hover:bg-slate-900 hover:text-white transition-all shadow-sm" title="Subir Firma">
                            <i data-lucide="{{ $isSigned ? 'refresh-cw' : 'upload' }}" class="w-5 h-5"></i>
                        </button>

                        @if($isSigned)
                        <a href="{{ $viewSignedRoute }}" target="_blank" class="h-10 w-10 rounded-xl bg-emerald-500 text-white flex items-center justify-center hover:bg-emerald-700 transition-all shadow-sm" title="Ver Firma">
                            <i data-lucide="eye" class="w-5 h-5"></i>
                        </a>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>

            {{-- BOTÓN CONSOLIDADO --}}
            <div class="pt-12">
                <a href="{{ route('usuario.monitoreo.pdf', $acta->id) }}" target="_blank" 
                   class="w-full group bg-slate-900 text-white p-10 rounded-[3.5rem] font-black shadow-2xl flex items-center justify-between hover:bg-black hover:-translate-y-2 transition-all duration-500 relative overflow-hidden">
                    <div class="flex items-center gap-8 relative z-10">
                        <div class="h-16 w-16 bg-indigo-500 rounded-3xl flex items-center justify-center group-hover:rotate-6 transition-all duration-500">
                            <i data-lucide="file-check" class="w-8 h-8 text-white"></i>
                        </div>
                        <div class="text-left">
                            <p class="text-lg uppercase tracking-[0.3em] leading-none">Generar Acta Consolidada</p>
                            <p class="text-[11px] text-slate-400 font-bold uppercase mt-3 italic">Documento final unificado con todos los módulos</p>
                        </div>
                    </div>
                    <div class="h-14 w-14 bg-white/10 rounded-full flex items-center justify-center group-hover:bg-indigo-600 transition-colors">
                        <i data-lucide="arrow-right" class="w-7 h-7"></i>
                    </div>
                </a>
            </div>
        </div>
    </div>

    {{-- MODAL DE SUBIDA --}}
    <div x-show="showModal" 
         class="fixed inset-0 z-[100] flex items-center justify-center p-6 bg-slate-900/60 backdrop-blur-sm"
         x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-90"
         x-transition:enter-end="opacity-100 scale-100">
        
        <div class="bg-white rounded-[2.5rem] shadow-2xl max-w-md w-full overflow-hidden" @click.away="showModal = false">
            <div class="bg-indigo-600 p-8 text-white">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="text-xl font-black uppercase tracking-tight" x-text="currentModuleName"></h3>
                        <p class="text-indigo-200 text-xs font-bold uppercase mt-2">Documento Escaneado / Firmado</p>
                    </div>
                    <button @click="showModal = false" class="text-white/50 hover:text-white transition-colors">
                        <i data-lucide="x" class="w-6 h-6"></i>
                    </button>
                </div>
            </div>
            
            <form action="{{ route('usuario.monitoreo.subir-pdf-firmado', $acta->id) }}" method="POST" enctype="multipart/form-data" class="p-8 space-y-6">
                @csrf
                <input type="hidden" name="modulo" :value="currentModule">
                
                <div class="border-2 border-dashed border-slate-200 rounded-3xl p-10 flex flex-col items-center justify-center group hover:border-indigo-400 transition-all cursor-pointer relative">
                    <input type="file" name="pdf_firmado" accept="application/pdf" required class="absolute inset-0 opacity-0 cursor-pointer">
                    <i data-lucide="file-up" class="w-12 h-12 text-slate-300 group-hover:text-indigo-500 mb-4 transition-colors"></i>
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Seleccionar archivo PDF firmado</span>
                </div>

                <div class="flex gap-4 mt-8">
                    <button type="button" @click="showModal = false" class="flex-1 px-6 py-4 rounded-2xl bg-slate-100 text-slate-500 font-black text-xs uppercase hover:bg-slate-200 transition-all">Cancelar</button>
                    <button type="submit" class="flex-1 px-6 py-4 rounded-2xl bg-indigo-600 text-white font-black text-xs uppercase shadow-lg hover:bg-indigo-700 transition-all">Subir Firma</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        if (typeof lucide !== 'undefined') lucide.createIcons();
    });
</script>

<style>
    [x-cloak] { display: none !important; }
    .grayscale { filter: grayscale(1); opacity: 0.6; }
</style>
@endsection