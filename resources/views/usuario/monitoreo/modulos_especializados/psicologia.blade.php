@extends('layouts.usuario')

@section('title', 'Psicología - CSMC')

@section('content')
<div class="min-h-screen bg-[#f4f7fa] pb-20" x-data="{ unsavedChanges: false }">
    
    {{-- ENCABEZADO TEAL --}}
    <div class="bg-teal-900 pt-10 pb-24 rounded-b-[3rem] shadow-xl relative overflow-hidden">
        <div class="max-w-6xl mx-auto px-6 relative z-10 flex justify-between items-center text-white">
            <div class="flex items-center gap-6">
                <a href="{{ route('usuario.monitoreo.salud_mental_group.index', $monitoreo->id) }}" class="h-12 w-12 rounded-xl bg-white/10 flex items-center justify-center hover:bg-white/20 transition-all">
                    <i data-lucide="arrow-left" class="w-6 h-6"></i>
                </a>
                <div>
                    <h1 class="text-3xl font-black uppercase italic tracking-tight">04. Psicología</h1>
                    <p class="text-teal-200 text-[11px] font-bold uppercase tracking-widest">CSMC: {{ $monitoreo->establecimiento->nombre }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-5xl mx-auto px-6 -mt-16 relative z-20">
        @if ($errors->any())
            <div class="bg-red-500 text-white p-4 rounded-2xl mb-4 font-bold text-xs uppercase">
                Hay errores en el formulario. Por favor revisa los campos.
            </div>
        @endif

        <form action="{{ route('usuario.monitoreo.sm_psicologia.store', $monitoreo->id) }}" 
              method="POST" 
              enctype="multipart/form-data">
            @csrf
            
            {{-- BLOQUES DE COMPONENTES --}}
            <x-esp_1_detalleDeConsultorio :detalle="$registro" />
            <x-esp_2_datosProfesional prefix="profesional" :detalle="$registro" />
            <x-esp_3_detalleDni :detalle="$registro" color="teal" />

            {{-- 5. Equipamiento del Consultorio (CON SINCRONIZACIÓN ALPINE) --}}
            <div x-data="equipamientoComponent({{ json_encode($data['inventario'] ?? []) }})">
                <x-esp_5_equipos :model="json_encode($data['inventario'] ?? [])" />
                
                <div class="hidden">
                    <template x-for="(item, index) in items" :key="item.id">
                        <div>
                            <input type="hidden" :name="'contenido[inventario]['+index+'][descripcion]'" :value="item.descripcion">
                            <input type="hidden" :name="'contenido[inventario]['+index+'][propiedad]'" :value="item.propiedad">
                            <input type="hidden" :name="'contenido[inventario]['+index+'][estado]'" :value="item.estado">
                            <input type="hidden" :name="'contenido[inventario]['+index+'][tipo_codigo]'" :value="item.tipo_codigo">
                            <input type="hidden" :name="'contenido[inventario]['+index+'][codigo]'" :value="item.codigo">
                            <input type="hidden" :name="'contenido[inventario]['+index+'][observacion]'" :value="item.observacion">
                        </div>
                    </template>
                </div>
            </div>

            <x-esp_4_detalleCap :model="json_encode($data['capacitacion'] ?? [])" />
            <x-esp_6_soporte :detalle="$registro" />
            <x-esp_7_comentariosEvid :comentario="$registro" />

            {{-- BARRA FLOTANTE --}}
            <div class="fixed bottom-6 left-0 right-0 px-6 z-50">
                <div class="max-w-5xl mx-auto bg-slate-900/90 backdrop-blur-md p-4 rounded-2xl flex justify-between items-center border border-white/10">
                    <span class="text-[10px] font-black uppercase text-emerald-400">SIHCE - Región Ica</span>
                    <div class="flex gap-3">
                        <button type="submit" class="px-8 py-3 bg-teal-500 text-white rounded-xl text-[10px] font-black uppercase shadow-lg hover:bg-teal-400 transition-all">
                            Guardar Psicología CSMC
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        if(typeof lucide !== 'undefined') lucide.createIcons();
    });
</script>
@endsection