@extends('layouts.usuario')
@section('title', 'Módulo 02: Citas')

@section('content')

{{-- SCRIPTS DE ALPINE --}}
<script>
    function triajeForm() {
        return {
            saving: false,
            // Inicializamos con los datos del servidor
            form: {
                capacitacion: @json($valCapacitacion)
            },
            async guardarTodo() {
                this.saving = true;
                this.$refs.formHtml.submit();
            }
        }
    }
</script>

<div class="py-12 bg-[#f8fafc] min-h-screen" x-data="triajeForm()">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        
        {{-- ENCABEZADO --}}
        <div class="mb-10 flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="flex items-center gap-6">
                <div class="h-16 w-16 bg-white rounded-2xl flex items-center justify-center shadow-sm border border-slate-100">
                    <span class="text-2xl font-black text-indigo-600">03</span>
                </div>
                <div>
                    <div class="flex items-center gap-3 mb-1">
                        <span class="px-3 py-1 bg-indigo-100 text-indigo-700 text-[10px] font-black rounded-full uppercase tracking-widest">Módulo Técnico</span>
                        <span class="text-slate-400 font-bold text-[10px] uppercase tracking-wider">ID Acta: #{{ str_pad($acta->id, 5, '0', STR_PAD_LEFT) }}</span>
                    </div>
                    <h2 class="text-3xl font-black text-slate-900 uppercase tracking-tight italic">Módulo Citas</h2>
                </div>
            </div>
            
            <a href="{{ route('usuario.monitoreo.modulos', $acta->id) }}" class="px-6 py-3 bg-white border border-slate-200 rounded-2xl text-slate-500 font-black text-xs uppercase tracking-widest shadow-sm hover:bg-slate-50 transition-colors">
                Volver
            </a>
        </div>

        {{-- FORMULARIO --}}
        <form 
            action="{{ route('usuario.monitoreo.citas_esp.store', $acta->id) }}" 
            method="POST" 
            enctype="multipart/form-data" 
            x-ref="formHtml"
            @submit.prevent="guardarTodo" 
            class="space-y-8"
        >
            @csrf

            {{-- 1. DETALLE CONSULTORIO --}}
            <x-esp_1_detalleDeConsultorio :detalle="$dataMap" />
            
            {{-- 2. DATOS PROFESIONAL --}}
            {{-- Usamos el prefijo 'profesional' para que se guarde en contenido[profesional] --}}
            <x-esp_2_datosProfesional prefix="profesional" :detalle="$dataMap" />

            {{-- 3. DNI Y FIRMA --}}
            <x-esp_3_detalleDni :detalle="$dataMap" color="indigo" />

            {{-- 4. CAPACITACIÓN --}}
            {{-- Truco: Inputs Hidden para enviar datos de Alpine al servidor --}}
            <input type="hidden" name="capacitacion[recibieron_cap]" :value="form.capacitacion.recibieron_cap">
            <input type="hidden" name="capacitacion[institucion_cap]" :value="form.capacitacion.institucion_cap">
            
            <x-esp_4_detalleCap model="form.capacitacion" />

            {{-- 5. INVENTARIO (EQUIPOS) --}}
            {{-- Pasamos la lista de equipos ($valInventario) y un identificador de módulo --}}
            <x-esp_5_equipos :equipos="$valInventario" modulo="citas_esp" />
            
            {{-- 6. SOPORTE --}}
            <x-esp_6_soporte :detalle="$dataMap" />

            {{-- 7. COMENTARIOS Y FOTOS --}}
            <x-esp_7_comentariosEvid :comentario="$dataMap" />

            {{-- BOTÓN GUARDAR --}}
            <div class="fixed bottom-6 right-6 z-50 md:static md:flex md:justify-end mt-10">
                <button type="submit" :disabled="saving" class="bg-indigo-600 hover:bg-indigo-700 text-white px-10 py-4 rounded-2xl font-black uppercase tracking-widest shadow-xl flex items-center gap-3 transition-all transform hover:scale-105 disabled:opacity-70 disabled:scale-100">
                    <i x-show="!saving" data-lucide="save" class="w-5 h-5"></i>
                    <i x-show="saving" data-lucide="loader-2" class="w-5 h-5 animate-spin"></i>
                    <span x-text="saving ? 'Guardando...' : 'Guardar Cambios'"></span>
                </button>
            </div>

        </form>
    </div>
</div>
@endsection