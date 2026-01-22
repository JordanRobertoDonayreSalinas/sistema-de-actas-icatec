@extends('layouts.usuario')

@section('title', 'Admisión y Citas - CSMC')

@section('content')
<div class="min-h-screen bg-[#f4f7fa] pb-20" x-data="{ 
    unsavedChanges: false,
    updateProgress() {
        // Lógica simple para barra de progreso visual
        let total = document.querySelectorAll('.criterion-toggle').length;
        let checked = document.querySelectorAll('.criterion-toggle:checked').length;
        return (total > 0) ? Math.round((checked / total) * 100) : 0;
    }
}">
    
    {{-- ENCABEZADO ESPECÍFICO PARA CSMC (Color TEAL) --}}
    <div class="bg-teal-900 pt-10 pb-24 rounded-b-[3rem] shadow-xl relative overflow-hidden">
        <div class="absolute top-0 right-0 w-96 h-96 bg-white/5 rounded-full -mr-20 -mt-20 blur-3xl"></div>
        <div class="absolute bottom-0 left-0 w-64 h-64 bg-emerald-500/10 rounded-full -ml-10 -mb-10 blur-2xl"></div>
        
        <div class="max-w-6xl mx-auto px-6 relative z-10">
            <div class="flex flex-col md:flex-row justify-between items-center gap-6">
                <div class="flex items-center gap-6">
                    <a href="{{ route('usuario.monitoreo.modulos', $monitoreo->id) }}" class="h-12 w-12 rounded-xl bg-white/10 flex items-center justify-center text-white hover:bg-white/20 transition-all">
                        <i data-lucide="arrow-left" class="w-6 h-6"></i>
                    </a>
                    <div>
                        <div class="flex items-center gap-3 mb-2">
                            <span class="px-3 py-1 bg-emerald-400 text-teal-900 text-[10px] font-black rounded-lg uppercase tracking-widest">
                                Módulo Especializado
                            </span>
                            <span class="text-teal-200 text-[11px] font-bold uppercase tracking-widest">
                                CSMC: {{ $monitoreo->establecimiento->nombre }}
                            </span>
                        </div>
                        <h1 class="text-3xl font-black text-white tracking-tight uppercase italic">
                            01. Admisión y Citas
                        </h1>
                    </div>
                </div>
                
                {{-- KPI DE CUMPLIMIENTO --}}
                <div class="bg-white/10 backdrop-blur-md border border-white/20 px-6 py-3 rounded-2xl flex items-center gap-4">
                    <div class="text-right">
                        <p class="text-[10px] text-teal-200 font-bold uppercase tracking-widest">Cumplimiento</p>
                        <p class="text-2xl font-black text-white" x-text="updateProgress() + '%'"></p>
                    </div>
                    <div class="h-12 w-12 rounded-full border-4 border-emerald-400 border-t-transparent animate-spin-slow"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-5xl mx-auto px-6 -mt-16 relative z-20">
        
        <form action="{{ route('usuario.monitoreo.guardar_modulo', ['id' => $monitoreo->id, 'modulo' => 'citas_csmc']) }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            {{-- BLOQUE 1: GESTIÓN DE CITAS Y ADMISIÓN --}}
            <div class="bg-white rounded-[2.5rem] shadow-xl border border-slate-200 overflow-hidden mb-8">
                <div class="bg-slate-50 px-8 py-6 border-b border-slate-100 flex items-center gap-3">
                    <div class="h-10 w-10 rounded-xl bg-teal-100 flex items-center justify-center text-teal-600">
                        <i data-lucide="calendar-check" class="w-5 h-5"></i>
                    </div>
                    <h3 class="text-slate-800 font-black text-sm uppercase tracking-wider">Criterios de Evaluación CSMC</h3>
                </div>

                <div class="p-8 space-y-8">
                    {{-- CRITERIO 1 --}}
                    <div class="group">
                        <div class="flex items-start gap-4">
                            <div class="mt-1">
                                <span class="h-6 w-6 rounded-full bg-slate-200 text-slate-500 flex items-center justify-center text-[10px] font-black">01</span>
                            </div>
                            <div class="flex-1">
                                <p class="text-slate-700 font-bold text-xs uppercase leading-relaxed">
                                    ¿El CSMC cuenta con un sistema de admisión diferenciado que garantice la confidencialidad y el trato humanizado al usuario?
                                </p>
                                <div class="mt-3 flex items-center gap-6">
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="radio" name="criterio_1" value="1" class="criterion-toggle w-4 h-4 text-teal-600 focus:ring-teal-500 border-slate-300" @change="unsavedChanges = true">
                                        <span class="text-[11px] font-bold text-slate-600 uppercase">Cumple</span>
                                    </label>
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="radio" name="criterio_1" value="0" class="w-4 h-4 text-red-600 focus:ring-red-500 border-slate-300" @change="unsavedChanges = true">
                                        <span class="text-[11px] font-bold text-slate-600 uppercase">No Cumple</span>
                                    </label>
                                </div>
                                <textarea name="obs_1" rows="2" class="mt-3 w-full bg-slate-50 border-slate-200 rounded-xl text-xs focus:border-teal-500 focus:ring-0 placeholder:text-slate-400" placeholder="Observaciones / Hallazgos..."></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="w-full h-px bg-slate-100"></div>

                    {{-- CRITERIO 2 --}}
                    <div class="group">
                        <div class="flex items-start gap-4">
                            <div class="mt-1">
                                <span class="h-6 w-6 rounded-full bg-slate-200 text-slate-500 flex items-center justify-center text-[10px] font-black">02</span>
                            </div>
                            <div class="flex-1">
                                <p class="text-slate-700 font-bold text-xs uppercase leading-relaxed">
                                    ¿Se ofertan turnos para psiquiatría, psicología y terapia ocupacional en horarios que cubren la demanda (Turnos Tarde/Mañana)?
                                </p>
                                <div class="mt-3 flex items-center gap-6">
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="radio" name="criterio_2" value="1" class="criterion-toggle w-4 h-4 text-teal-600 focus:ring-teal-500 border-slate-300" @change="unsavedChanges = true">
                                        <span class="text-[11px] font-bold text-slate-600 uppercase">Cumple</span>
                                    </label>
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="radio" name="criterio_2" value="0" class="w-4 h-4 text-red-600 focus:ring-red-500 border-slate-300" @change="unsavedChanges = true">
                                        <span class="text-[11px] font-bold text-slate-600 uppercase">No Cumple</span>
                                    </label>
                                </div>
                                <textarea name="obs_2" rows="2" class="mt-3 w-full bg-slate-50 border-slate-200 rounded-xl text-xs focus:border-teal-500 focus:ring-0 placeholder:text-slate-400" placeholder="Observaciones..."></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="w-full h-px bg-slate-100"></div>

                    {{-- CRITERIO 3: CONTINUIDAD DE CUIDADOS (Clave en CSMC) --}}
                    <div class="group">
                        <div class="flex items-start gap-4">
                            <div class="mt-1">
                                <span class="h-6 w-6 rounded-full bg-slate-200 text-slate-500 flex items-center justify-center text-[10px] font-black">03</span>
                            </div>
                            <div class="flex-1">
                                <p class="text-slate-700 font-bold text-xs uppercase leading-relaxed">
                                    ¿Existe un mecanismo activo de rescate (llamadas/visitas) para pacientes con trastornos mentales graves que faltan a sus citas?
                                </p>
                                <div class="mt-3 flex items-center gap-6">
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="radio" name="criterio_3" value="1" class="criterion-toggle w-4 h-4 text-teal-600 focus:ring-teal-500 border-slate-300" @change="unsavedChanges = true">
                                        <span class="text-[11px] font-bold text-slate-600 uppercase">Cumple</span>
                                    </label>
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="radio" name="criterio_3" value="0" class="w-4 h-4 text-red-600 focus:ring-red-500 border-slate-300" @change="unsavedChanges = true">
                                        <span class="text-[11px] font-bold text-slate-600 uppercase">No Cumple</span>
                                    </label>
                                </div>
                                <textarea name="obs_3" rows="2" class="mt-3 w-full bg-slate-50 border-slate-200 rounded-xl text-xs focus:border-teal-500 focus:ring-0 placeholder:text-slate-400" placeholder="Detallar mecanismo usado..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- BLOQUE 2: EVIDENCIA FOTOGRÁFICA --}}
            <div class="bg-white rounded-[2.5rem] shadow-xl border border-slate-200 overflow-hidden mb-24">
                <div class="bg-slate-50 px-8 py-6 border-b border-slate-100 flex items-center gap-3">
                    <div class="h-10 w-10 rounded-xl bg-purple-100 flex items-center justify-center text-purple-600">
                        <i data-lucide="image-plus" class="w-5 h-5"></i>
                    </div>
                    <h3 class="text-slate-800 font-black text-sm uppercase tracking-wider">Evidencias del Módulo</h3>
                </div>
                <div class="p-8">
                    <div class="border-2 border-dashed border-slate-200 rounded-2xl p-8 flex flex-col items-center justify-center text-center hover:bg-slate-50 hover:border-teal-400 transition-all cursor-pointer relative group">
                        <input type="file" name="foto_evidencia" class="absolute inset-0 opacity-0 cursor-pointer w-full h-full z-10" onchange="previewImage(this)">
                        <div id="upload-placeholder" class="group-hover:scale-105 transition-transform duration-300">
                            <div class="h-16 w-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:bg-teal-50">
                                <i data-lucide="upload-cloud" class="w-8 h-8 text-slate-400 group-hover:text-teal-500"></i>
                            </div>
                            <p class="text-xs font-bold text-slate-500 uppercase">Arrastra una imagen o haz clic aquí</p>
                            <p class="text-[10px] text-slate-400 mt-1">Formatos: JPG, PNG (Max 5MB)</p>
                        </div>
                        <img id="image-preview" src="#" class="hidden max-h-64 rounded-xl shadow-lg mt-4 object-cover" />
                    </div>
                </div>
            </div>

            {{-- BARRA DE ACCIONES FLOTANTE --}}
            <div class="fixed bottom-6 left-0 right-0 px-6 z-50">
                <div class="max-w-5xl mx-auto bg-slate-900/90 backdrop-blur-md text-white p-4 rounded-2xl shadow-2xl flex items-center justify-between border border-white/10">
                    <div class="flex items-center gap-3 px-2">
                        <div class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></div>
                        <span class="text-[10px] font-bold uppercase tracking-widest text-slate-300" x-show="unsavedChanges">Cambios sin guardar</span>
                        <span class="text-[10px] font-bold uppercase tracking-widest text-emerald-400" x-show="!unsavedChanges">Todo al día</span>
                    </div>
                    <div class="flex gap-3">
                        <a href="{{ route('usuario.monitoreo.modulos', $monitoreo->id) }}" class="px-6 py-3 rounded-xl border border-white/20 text-[10px] font-black uppercase tracking-widest hover:bg-white/10 transition-colors">
                            Cancelar
                        </a>
                        <button type="submit" class="px-8 py-3 rounded-xl bg-teal-500 text-white text-[10px] font-black uppercase tracking-widest shadow-lg shadow-teal-500/30 hover:bg-teal-400 hover:scale-105 transition-all flex items-center gap-2">
                            <i data-lucide="save" class="w-4 h-4"></i> Guardar Citas CSMC
                        </button>
                    </div>
                </div>
            </div>

        </form>
    </div>
</div>

<script>
    function previewImage(input) {
        const preview = document.getElementById('image-preview');
        const placeholder = document.getElementById('upload-placeholder');
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.classList.remove('hidden');
                placeholder.classList.add('hidden');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
    document.addEventListener('DOMContentLoaded', () => {
        lucide.createIcons();
    });
</script>
@endsection