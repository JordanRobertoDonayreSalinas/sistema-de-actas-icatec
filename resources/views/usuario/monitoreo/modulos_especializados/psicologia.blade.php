@extends('layouts.usuario')

@section('title', 'Psicología - CSMC')

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
                            04. Psicología
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
        
        {{-- Asegúrate de usar enctype para la subida de fotos --}}
        <form action="{{ route('usuario.monitoreo.sm_psicologia.store', $monitoreo->id) }}" 
      method="POST" 
      enctype="multipart/form-data"
      @submit="unsavedChanges = false">
    @csrf

            {{-- Componentes principales --}}
            <x-esp_1_detalleDeConsultorio :detalle="$registro" />
            <x-esp_2_datosProfesional prefix="profesional" :detalle="$registro" />
            <x-esp_3_detalleDni :detalle="$registro" color="teal" />

            
            <x-esp_4_detalleCap :model="json_encode($registro->contenido['capacitacion'] ?? [])" />
            {{-- 5. Equipamiento (CORRECCIÓN DE VARIABLE $equipos) --}}
<x-esp_5_equipos :model="json_encode($data['inventario'] ?? [])" />
            <x-esp_6_soporte :detalle="$registro" />
            <x-esp_7_comentariosEvid :comentario="$registro" />

            {{-- Botón de envío --}}
            <div class="fixed bottom-6 left-0 right-0 px-6 z-50">
                <div class="max-w-5xl mx-auto bg-slate-900/90 backdrop-blur-md p-4 rounded-2xl flex justify-between items-center border border-white/10">
                    <button type="submit" class="px-8 py-3 bg-teal-500 text-white rounded-xl text-[10px] font-black uppercase shadow-lg hover:bg-teal-400 transition-all">
                        Guardar Psicología CSMC
                    </button>
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