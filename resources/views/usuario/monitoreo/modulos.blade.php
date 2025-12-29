@extends('layouts.usuario')

@section('title', 'Panel de Gestión Modular - ' . $acta->establecimiento->nombre)

@section('content')
<div class="py-12 bg-[#f8fafc] min-h-screen" 
     x-data="{ 
        activos: {{ json_encode($modulosActivos) }},
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
        init() {
            this.$watch('activos', () => {
                this.$nextTick(() => {
                    if (typeof lucide !== 'undefined') {
                        lucide.createIcons();
                    }
                });
            });
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
                            <span class="px-4 py-1.5 rounded-full bg-indigo-100 text-indigo-700 text-[10px] font-black uppercase tracking-[0.15em]">Sistema de Monitoreo Modular</span>
                            <span class="text-slate-500 text-[10px] font-bold uppercase">ID: {{ str_pad($acta->id, 5, '0', STR_PAD_LEFT) }}</span>
                        </div>
                        <h2 class="text-3xl font-black text-slate-900 mt-2 italic tracking-tight">{{ $acta->establecimiento->nombre }}</h2>
                        <p class="text-slate-400 text-[11px] font-bold mt-2 uppercase tracking-widest flex items-center gap-2">
                            <i data-lucide="map-pin" class="w-4 h-4 text-indigo-400"></i> {{ $acta->establecimiento->distrito }}, {{ $acta->establecimiento->provincia }} 
                            <span class="text-indigo-600 ml-2 bg-indigo-50 px-2 py-0.5 rounded">CAT: {{ $acta->categoria_congelada ?? $acta->establecimiento->categoria }}</span>
                        </p>
                    </div>
                </div>
                <div>
                    <a href="{{ route('usuario.monitoreo.index') }}" 
                       class="group flex items-center gap-3 px-8 py-4 rounded-2xl border-2 border-slate-100 text-slate-400 font-black text-xs hover:bg-slate-50 hover:text-slate-600 transition-all uppercase tracking-widest">
                       <i data-lucide="chevron-left" class="w-4 h-4"></i> Volver
                    </a>
                </div>
            </div>
        </div>

        {{-- CUERPO DEL PANEL --}}
        <div class="space-y-8">
            <div class="flex flex-col md:flex-row md:items-center justify-between px-6 gap-4">
                <div class="space-y-1">
                    <h3 class="text-[12px] font-black text-slate-900 uppercase tracking-[0.4em]">Módulos de Evaluación Técnica</h3>
                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-tight">Active o desactive módulos según la cartera de servicios de la IPRESS</p>
                </div>
                <div class="flex flex-col items-end">
                    @php
                        $completadosCount = count(array_intersect($modulosActivos, $modulosGuardados));
                        $totalActivos = count($modulosActivos);
                        $porcentaje = $totalActivos > 0 ? ($completadosCount / $totalActivos) * 100 : 0;
                    @endphp
                    <span class="text-[11px] font-black text-indigo-600 bg-indigo-50 px-5 py-2 rounded-full uppercase tracking-widest border border-indigo-100 shadow-sm">
                        <span x-text="activos.filter(a => {{ json_encode($modulosGuardados) }}.includes(a)).length"></span> / <span x-text="activos.length"></span> Completados
                    </span>
                    <div class="w-48 h-1.5 bg-slate-100 rounded-full mt-3 overflow-hidden">
                        <div class="h-full bg-indigo-500 transition-all duration-1000" 
                             :style="`width: ${(activos.filter(a => {{ json_encode($modulosGuardados) }}.includes(a)).length / (activos.length || 1)) * 100}%` "></div>
                    </div>
                </div>
            </div>

            {{-- GRID DE MÓDULOS --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @php
                    $modulosLinks = [
                        'gestion_administrativa' => ['nombre' => '01. Gestión Administrativa', 'icon' => 'folder-kanban'],
                        'citas'                  => ['nombre' => '02. Citas', 'icon' => 'calendar-clock'],
                        'triaje'                 => ['nombre' => '03. Triaje', 'icon' => 'stethoscope'],
                        'consulta_medicina'      => ['nombre' => '04. Consulta Externa: Medicina', 'icon' => 'user-cog'],
                        'consulta_odontologia'   => ['nombre' => '05. Consulta Externa: Odontología', 'icon' => 'smile'],
                        'consulta_nutricion'     => ['nombre' => '06. Consulta Externa: Nutrición', 'icon' => 'apple'],
                        'consulta_psicologia'    => ['nombre' => '07. Consulta Externa: Psicología', 'icon' => 'brain'],
                        'cred'                   => ['nombre' => '08. CRED', 'icon' => 'baby'],
                        'inmunizaciones'         => ['nombre' => '09. Inmunizaciones', 'icon' => 'syringe'],
                        'atencion_prenatal'      => ['nombre' => '10. Atención Prenatal', 'icon' => 'heart-pulse'],
                        'planificacion_familiar' => ['nombre' => '11. Planificación Familiar', 'icon' => 'users'],
                        'parto'                  => ['nombre' => '12. Parto', 'icon' => 'bed'],
                        'puerperio'              => ['nombre' => '13. Puerperio', 'icon' => 'home'],
                        'fua_electronico'        => ['nombre' => '14. FUA Electrónico', 'icon' => 'file-digit'],
                        'farmacia'               => ['nombre' => '15. Farmacia', 'icon' => 'pill'],
                        'referencias'            => ['nombre' => '16. Referencias y Contrareferencias', 'icon' => 'map-pinned'],
                        'laboratorio'            => ['nombre' => '17. Laboratorio', 'icon' => 'test-tube-2'],
                        'urgencias'              => ['nombre' => '18. Urgencias y Emergencias', 'icon' => 'ambulance'],
                    ];
                @endphp

                @foreach($modulosLinks as $slug => $data)
                @php 
                    $isCompleted = in_array($slug, $modulosGuardados); 
                    // Construimos el nombre de la ruta dinámicamente
                    $routeName = "usuario.monitoreo.{$slug}.index";
                @endphp
                
                <div class="relative bg-white border rounded-[2.5rem] flex items-center shadow-sm transition-all duration-500 group"
                     :class="activos.includes('{{ $slug }}') ? '{{ $isCompleted ? 'border-emerald-200 bg-white' : 'border-slate-200 bg-white' }}' : 'border-slate-100 bg-slate-50/50 grayscale'">
                    
                    {{-- INTERRUPTOR (TOGGLE) --}}
                    <div class="absolute -top-3 -right-2 z-20">
                        <button @click="toggle('{{ $slug }}')" 
                                :class="activos.includes('{{ $slug }}') ? 'bg-indigo-600' : 'bg-slate-300'"
                                class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none shadow-sm">
                            <span :class="activos.includes('{{ $slug }}') ? 'translate-x-6' : 'translate-x-1'"
                                  class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform"></span>
                        </button>
                    </div>

                    {{-- CONTENIDO DINÁMICO --}}
                    <div class="flex flex-1 items-center overflow-hidden">
                        {{-- Módulo Activo --}}
                        <template x-if="activos.includes('{{ $slug }}')">
                            @if(Route::has($routeName))
                                <a href="{{ route($routeName, $acta->id) }}" 
                                   class="flex items-center gap-6 p-4 flex-1 rounded-[2rem] hover:bg-slate-50 transition-colors">
                                    <div class="h-16 w-16 rounded-2xl {{ $isCompleted ? 'bg-emerald-50 text-emerald-600' : 'bg-indigo-50 text-indigo-500' }} flex items-center justify-center group-hover:scale-105 transition-transform duration-300">
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
                            @else
                                {{-- Fallback por si la ruta no existe aún --}}
                                <div class="flex items-center gap-6 p-4 flex-1 opacity-50 cursor-help" title="Módulo en desarrollo (Ruta no definida)">
                                    <div class="h-16 w-16 rounded-2xl bg-orange-50 text-orange-400 flex items-center justify-center">
                                        <i data-lucide="construction" class="w-8 h-8"></i>
                                    </div>
                                    <div>
                                        <p class="text-[13px] font-black text-slate-500 uppercase tracking-tight">{{ $data['nombre'] }}</p>
                                        <p class="text-[9px] font-bold text-orange-400 uppercase mt-1.5 italic">Próximamente</p>
                                    </div>
                                </div>
                            @endif
                        </template>

                        {{-- Módulo Desactivado --}}
                        <template x-if="!activos.includes('{{ $slug }}')">
                            <div class="flex items-center gap-6 p-4 flex-1 cursor-not-allowed select-none">
                                <div class="h-16 w-16 rounded-2xl bg-slate-100 text-slate-300 flex items-center justify-center">
                                    <i data-lucide="slash" class="w-8 h-8"></i>
                                </div>
                                <div>
                                    <p class="text-[13px] font-black text-slate-300 uppercase tracking-tight">{{ $data['nombre'] }}</p>
                                    <p class="text-[9px] font-bold text-slate-200 uppercase mt-1.5 italic tracking-widest text-wrap">No aplica a esta categoría</p>
                                </div>
                            </div>
                        </template>
                    </div>

                    {{-- ACCIONES DE PDF --}}
                    <div class="flex items-center gap-3 px-4" x-show="activos.includes('{{ $slug }}')">
                        @if($isCompleted)
                            <a href="{{ route('usuario.monitoreo.pdf.modulo', ['id' => $acta->id, 'modulo' => $slug]) }}" 
                               target="_blank"
                               class="h-12 w-12 rounded-2xl bg-red-50 text-red-500 flex items-center justify-center hover:bg-red-600 hover:text-white transition-all shadow-sm">
                                <i data-lucide="file-text" class="w-6 h-6"></i>
                            </a>
                        @else
                            <div class="h-10 w-10 rounded-full flex items-center justify-center text-slate-200">
                                <i data-lucide="lock-keyhole" class="w-5 h-5"></i>
                            </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>

            {{-- ACCIÓN FINAL --}}
            <div class="pt-10">
                <a href="{{ route('usuario.monitoreo.pdf', $acta->id) }}" target="_blank" 
                   class="w-full group bg-slate-900 text-white p-10 rounded-[3.5rem] font-black shadow-2xl flex items-center justify-between hover:bg-black hover:-translate-y-2 transition-all duration-500 overflow-hidden relative">
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