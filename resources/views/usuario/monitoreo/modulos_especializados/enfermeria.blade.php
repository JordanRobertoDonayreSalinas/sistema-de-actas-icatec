@extends('layouts.usuario')
@section('title', 'Módulo 02: Enfermeria')

@section('content')

{{-- SCRIPTS DE ALPINE (Lógica exclusiva para esta vista) --}}
<script>
    // CAMBIO REALIZADO: Nombre de función actualizado a enfermeriaForm
    function enfermeriaForm() {
        return {
            saving: false,
            
            // 1. VARIABLES DE ESTADO (Inicializadas con datos del backend)
            // Si no hay datos (registro nuevo), asumimos valores por defecto: NO para SIHCE, DNI para documento.
            sihce: '{{ $dataMap->contenido['profesional']['cuenta_sihce'] ?? '' }}',
            tipoDoc: '{{ $dataMap->contenido['profesional']['tipo_doc'] ?? 'DNI' }}',

            // Datos para el componente de capacitación
            form: {
                capacitacion: @json($valCapacitacion)
            },

            // 2. DETECTOR DE CAMBIOS
            // Esta función escucha cualquier cambio en el formulario y actualiza las variables si coincide el ID
            updateVisibility(event) {
                // ID del select en el componente esp_2_1_docAdmin: 'sihce_' + prefix
                if (event.target.id === 'sihce_profesional') {
                    this.sihce = event.target.value;
                }
                // ID del select en el componente esp_2_datosProfesional: 'tipo_' + prefix
                if (event.target.id === 'tipo_profesional') {
                    this.tipoDoc = event.target.value;
                }
            },

            async guardarTodo() {
                this.saving = true;
                this.$refs.formHtml.submit();
            }
        }
    }
</script>

{{-- CAMBIO REALIZADO: x-data inicializado con enfermeriaForm() --}}
<div class="py-12 bg-[#f8fafc] min-h-screen" x-data="enfermeriaForm()">
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
                    <h2 class="text-3xl font-black text-slate-900 uppercase tracking-tight italic">Módulo Enfermería</h2>
                </div>
            </div>
            
            <a href="{{ route('usuario.monitoreo.modulos', $acta->id) }}" class="px-6 py-3 bg-white border border-slate-200 rounded-2xl text-slate-500 font-black text-xs uppercase tracking-widest shadow-sm hover:bg-slate-50 transition-colors">
                Volver
            </a>
        </div>

        {{-- FORMULARIO --}}
        {{-- Agregamos @change para ejecutar updateVisibility cada vez que se modifique un input --}}
        <form 
            action="{{ route('usuario.monitoreo.sm_enfermeria.store', $acta->id) }}" 
            method="POST" 
            enctype="multipart/form-data" 
            x-ref="formHtml"
            @submit.prevent="guardarTodo" 
            @change="updateVisibility($event)"
            class="space-y-8"
        >
            @csrf

            {{-- 1. DETALLE CONSULTORIO --}}
            <x-esp_1_detalleDeConsultorio :detalle="$dataMap" />
            
            {{-- 2. DATOS PROFESIONAL --}}
            {{-- Aquí está el select de TIPO DOC (id="tipo_profesional") --}}
            <x-esp_2_datosProfesional prefix="profesional" :detalle="$dataMap" />
            
            {{-- 2.1 DOCUMENTACIÓN ADMIN --}}
            {{-- Aquí está el select de SIHCE (id="sihce_profesional") --}}
            <x-esp_2_1_docAdmin prefix="profesional" :detalle="$dataMap" />

            {{-- 3. DNI Y FIRMA --}}
            {{-- CONDICIÓN: Se oculta si Tipo Doc NO es DNI (ej. es C.E.) --}}
            <div x-show="tipoDoc === 'DNI'" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform -translate-y-2"
                 x-transition:enter-end="opacity-100 transform translate-y-0">
                <x-esp_3_detalleDni :detalle="$dataMap" color="indigo" />
            </div>

            {{-- 4. CAPACITACIÓN --}}
            {{-- CONDICIÓN: Se oculta si SIHCE es NO --}}
            <div x-show="sihce === 'SI'"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform -translate-y-2"
                 x-transition:enter-end="opacity-100 transform translate-y-0">
                
                <input type="hidden" name="capacitacion[recibieron_cap]" :value="form.capacitacion.recibieron_cap">
                <input type="hidden" name="capacitacion[institucion_cap]" :value="form.capacitacion.institucion_cap">

                <x-esp_4_detalleCap model="form.capacitacion" />
            </div>

            {{-- 5. INVENTARIO (EQUIPOS) --}}
            {{-- Este siempre es visible --}}
            @php
                $equiposComoObjetos = collect($valInventario)->map(function ($item) {
                    return (object) $item;
                });
            @endphp
            <x-esp_5_equipos :equipos="$equiposComoObjetos" modulo="enfermeria_esp" />
            
            {{-- 6. SOPORTE --}}
            {{-- CONDICIÓN: Se oculta si SIHCE es NO (misma regla que capacitación) --}}
            <div x-show="sihce === 'SI'"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform -translate-y-2"
                 x-transition:enter-end="opacity-100 transform translate-y-0">
                <x-esp_6_soporte :detalle="$dataMap" />
            </div>

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