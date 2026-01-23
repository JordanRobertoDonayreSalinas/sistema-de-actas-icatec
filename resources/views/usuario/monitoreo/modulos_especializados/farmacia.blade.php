@extends('layouts.usuario')

@section('title', 'Farmacia- CSMC')

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
                            <span class="text-slate-400 font-bold text-[10px] uppercase tracking-widest">ID Acta: #{{ str_pad($monitoreo->numero_acta, 5, '0', STR_PAD_LEFT) }}</span>
                            
                        </div>
                        <h1 class="text-3xl font-black text-white tracking-tight uppercase italic">
                            Farmacia
                        </h1>
                        <span class="text-teal-200 text-[11px] font-bold uppercase tracking-widest">
                            <i data-lucide="hospital" class="inline-block w-4 h-4 mr-1 text-white-500"></i>{{ $monitoreo->establecimiento->nombre }}
                        </span>
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
        
        {{-- FORMULARIO CON RUTA CORRECTA --}}
        <form action="{{ route('usuario.monitoreo.farmacia_esp.store', $monitoreo->id) }}" method="POST" enctype="multipart/form-data">
            @csrf

            {{-- CONTENEDOR PRINCIPAL --}}
            <div class="space-y-16 mb-24">

                {{-- 1. SECCIÓN: DATOS DEL PROFESIONAL (SOBRE FONDO VERDE) --}}
                <div class="relative">
                    {{-- TÍTULO: Diseño "High Contrast" para fondo oscuro --}}
                    <div class="flex items-center gap-5 mb-8 pl-2">
                        {{-- Ícono: Fondo BLANCO para resaltar --}}
                        <div class="h-14 w-14 bg-white rounded-2xl flex items-center justify-center text-teal-600 shadow-xl shadow-teal-900/20">
                            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        </div>
                        <div>
                            {{-- Texto: BLANCO con sombra suave --}}
                            <h3 class="text-xl font-black text-white uppercase tracking-tight drop-shadow-md">Profesional Responsable</h3>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="h-1 w-8 bg-teal-400 rounded-full shadow-sm"></span>
                                <p class="text-[10px] font-bold text-teal-100 uppercase tracking-widest shadow-sm">Identificación y Cargo</p>
                            </div>
                        </div>
                    </div>

                    <x-busqueda-profesional prefix="profesional" :detalle="$monitoreo" color="teal" />
                </div>


                {{-- 2. SECCIÓN: DNI Y FIRMA --}}
                <x-dni_firma :detalle="$monitoreo" color="teal" />


                {{-- 3. SECCIÓN: EQUIPAMIENTO (SOBRE FONDO CLARO) --}}
                <div class="relative">
                    {{-- TÍTULO: Diseño oscuro normal --}}
                    <div class="flex items-center gap-5 mb-8 pl-2">
                        {{-- Ícono: Gradiente Teal --}}
                        <div class="h-14 w-14 bg-gradient-to-br from-teal-400 to-teal-600 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-teal-200/50">
                            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="14" x="2" y="3" rx="2"/><line x1="8" x2="16" y1="21" y2="21"/><line x1="12" x2="12" y1="17" y2="21"/></svg>
                        </div>
                        <div>
                            {{-- Texto: OSCURO (Slate-800) --}}
                            <h3 class="text-xl font-black text-slate-800 uppercase tracking-tight">Equipamiento Tecnológico</h3>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="h-1 w-8 bg-teal-500 rounded-full"></span>
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Inventario de Hardware</p>
                            </div>
                        </div>
                    </div>
                    
                    <x-tabla-equipos :equipos="$equipos" modulo="farmacia_esp" />
                </div>

            </div>

            {{-- BLOQUE 2: EVIDENCIA FOTOGRÁFICA --}}
            {{-- Lógica para mostrar imagen guardada o el placeholder de subida --}}
            @php
                $hasImage = !empty($data['foto_evidencia']);
                $imageUrl = $hasImage ? asset('storage/' . $data['foto_evidencia']) : '#';
            @endphp

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
                        
                        {{-- PLACEHOLDER: Solo visible si NO hay imagen guardada y NO se ha seleccionado una nueva en JS --}}
                        <div id="upload-placeholder" class="{{ $hasImage ? 'hidden' : '' }} group-hover:scale-105 transition-transform duration-300">
                            <div class="h-16 w-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:bg-teal-50">
                                <i data-lucide="upload-cloud" class="w-8 h-8 text-slate-400 group-hover:text-teal-500"></i>
                            </div>
                            <p class="text-xs font-bold text-slate-500 uppercase">Arrastra una imagen o haz clic aquí</p>
                            <p class="text-[10px] text-slate-400 mt-1">Formatos: JPG, PNG (Max 10MB)</p>
                        </div>

                        {{-- PREVIEW: Visible si HAY imagen guardada --}}
                        <img id="image-preview" src="{{ $imageUrl }}" class="{{ $hasImage ? '' : 'hidden' }} max-h-96 rounded-xl shadow-lg mt-4 object-cover w-full" />
                    </div>
                </div>
            </div>

            {{-- BOTÓN DE GUARDADO FINAL --}}
            <div class="pt-10 pb-20">
                <button type="submit" id="btn-submit-action" 
                        class="w-full group bg-indigo-600 text-white p-10 rounded-[3rem] font-black shadow-2xl shadow-indigo-200 flex items-center justify-between hover:bg-indigo-700 transition-all duration-500 active:scale-[0.98] cursor-pointer">
                    <div class="flex items-center gap-8 pointer-events-none">
                        <div class="h-16 w-16 bg-white/20 rounded-3xl flex items-center justify-center group-hover:rotate-12 transition-all shadow-lg border border-white/30">
                            <i data-lucide="save" id="icon-save-loader" class="w-8 h-8 text-white"></i>
                        </div>
                        <div class="text-left">
                            <p class="text-xl uppercase tracking-[0.3em] leading-none">Confirmar Registro</p>
                            <p class="text-[10px] text-indigo-200 font-bold uppercase mt-3 tracking-widest">Sincronizar Farmacia con el Maestro</p>
                        </div>
                    </div>
                    <div class="h-14 w-14 bg-white/10 rounded-full flex items-center justify-center group-hover:bg-white group-hover:text-indigo-600 transition-all duration-500">
                        <i data-lucide="chevron-right" class="w-7 h-7"></i>
                    </div>
                </button>
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