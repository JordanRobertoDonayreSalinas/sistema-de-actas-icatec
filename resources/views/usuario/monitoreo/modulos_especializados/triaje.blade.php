@extends('layouts.usuario')

@section('title', 'Módulo: Triaje CSMC')

@section('content')
<div class="py-12 bg-slate-50 min-h-screen">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        
        {{-- ENCABEZADO SUPERIOR --}}
        <div class="mb-8 flex flex-col md:flex-row items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-3 mb-1">
                    <span class="px-3 py-1 bg-teal-600 text-white text-[10px] font-black rounded-lg uppercase tracking-widest">Módulo Especializado</span>
                    <span class="text-slate-400 font-bold text-[10px] uppercase">ID Acta: #{{ str_pad($acta->numero_acta ?? $acta->id, 5, '0', STR_PAD_LEFT) }}</span>
                </div>
                <h2 class="text-3xl font-black text-slate-900 uppercase tracking-tight">02. Triaje</h2>
                <p class="text-slate-500 font-bold uppercase text-xs mt-1">
                    <i data-lucide="clipboard-pulse" class="inline-block w-4 h-4 mr-1 text-teal-500"></i> {{ $acta->establecimiento->nombre }}
                </p>
            </div>
            <a href="{{ route('usuario.monitoreo.modulos', $acta->id) }}" class="flex items-center gap-2 px-6 py-3 bg-white border-2 border-slate-200 rounded-2xl text-slate-600 font-black text-xs hover:bg-slate-50 transition-all uppercase shadow-sm">
                <i data-lucide="arrow-left" class="w-4 h-4"></i> Volver al Panel
            </a>
        </div>

        {{-- FORMULARIO PRINCIPAL --}}
        <form action="{{ route('usuario.monitoreo.triaje_esp.store', $acta->id) }}" 
              method="POST" 
              enctype="multipart/form-data" 
              class="space-y-6" 
              id="form-monitoreo-triaje">
            @csrf
            
            {{-- 1. DETALLES DEL CONSULTORIO --}}
            <x-esp_1_detalleDeConsultorio :detalle="$detalle" />

            {{-- 2. DATOS DEL PROFESIONAL (RRHH - Identidad y Cargo) --}}
            <x-esp_2_datosProfesional prefix="rrhh" :detalle="$detalle" />

            {{-- 2.1 DOCUMENTACIÓN ADMINISTRATIVA (SIHCE, DDJJ, Confidencialidad) --}}
            <x-esp_2_1_docAdmin prefix="rrhh" :detalle="$detalle" />

            {{-- 3. DETALLE DE DNI Y FIRMA DIGITAL --}}
            {{-- Se muestra condicionalmente si el tipo de doc en el componente 2 es DNI --}}
            <x-esp_3_detalleDni :detalle="$detalle" parentKey="rrhh" color='teal' />

            {{-- ================================================================================= --}}
            {{-- 4. DETALLES DE CAPACITACIÓN (PARCHE DE COMPATIBILIDAD)                           --}}
            {{-- ================================================================================= --}}
            @php
                // Preparamos los datos en formato JSON/Objeto como espera el componente antiguo
                $datosCapacitacion = [
                    'recibieron_cap' => data_get($detalle->contenido, 'capacitacion.recibieron_cap', ''),
                    'institucion_cap' => data_get($detalle->contenido, 'capacitacion.institucion_cap', '')
                ];
                // Convertimos a string JSON para pasarlo a la variable $model
                $modelJson = json_encode($datosCapacitacion);
            @endphp

            {{-- Llamamos al componente pasando 'model' en lugar de 'detalle' --}}
            <x-esp_4_detalleCap :model="$modelJson" />

            {{-- Script IN-LINE para forzar los atributos 'name' en este componente específico --}}
            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    // Esperamos un momento para asegurar que Alpine haya renderizado
                    setTimeout(() => {
                        // Inyectar name a los Radio Buttons de Capacitación
                        const radiosCap = document.querySelectorAll('[x-model="entidad.recibieron_cap"]');
                        radiosCap.forEach(r => r.setAttribute('name', 'contenido[capacitacion][recibieron_cap]'));

                        // Inyectar name al Select de Institución
                        const selectCap = document.querySelector('[x-model="entidad.institucion_cap"]');
                        if(selectCap) selectCap.setAttribute('name', 'contenido[capacitacion][institucion_cap]');
                    }, 500);
                });
            </script>
            {{-- ================================================================================= --}}

            {{-- 5. EQUIPAMIENTO DE TRIAJE --}}
            <x-esp_5_equipos :equipos="$equipos" modulo="triaje_esp" />

            {{-- 6. SOPORTE --}}
            <x-esp_6_soporte :detalle="$detalle" />

            {{-- 7. COMENTARIOS Y EVIDENCIA --}}
            <x-esp_7_comentariosEvid :detalle="$detalle" />

            {{-- BOTÓN DE GUARDADO --}}
            <div class="pt-10 pb-5 mt-6">
                <button type="submit" id="btn-submit-action" 
                        class="w-full group bg-teal-600 text-white p-8 rounded-[3rem] font-black shadow-2xl shadow-teal-200 flex items-center justify-between hover:bg-teal-700 transition-all duration-500 active:scale-[0.98] cursor-pointer">
                    <div class="flex items-center gap-8 pointer-events-none">
                        <div class="h-16 w-16 bg-white/20 rounded-3xl flex items-center justify-center group-hover:rotate-12 transition-all shadow-lg border border-white/30">
                            <i data-lucide="save" id="icon-save-loader" class="w-8 h-8 text-white"></i>
                        </div>
                        <div class="text-left">
                            <p class="text-xl uppercase tracking-[0.3em] leading-none">Confirmar Registro</p>
                            <p class="text-[10px] text-teal-200 font-bold uppercase mt-3 tracking-widest">Sincronizar Módulo Triaje</p>
                        </div>
                    </div>
                    <div class="h-14 w-14 bg-white/10 rounded-full flex items-center justify-center group-hover:bg-white group-hover:text-teal-600 transition-all duration-500">
                        <i data-lucide="chevron-right" class="w-7 h-7"></i>
                    </div>
                </button>
            </div>

        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof lucide !== 'undefined') lucide.createIcons();

        // 1. Lógica para mostrar/ocultar sección DNI detalles (Componente 3)
        // Se basa en el select "Tipo Doc" del Componente 2 (RRHH)
        const selectTipoDoc = document.querySelector('select[name="contenido[rrhh][tipo_doc]"]');
        
        function toggleSeccionDni(val) {
            const seccion = document.getElementById('seccion_dni_firma'); // ID del div principal en esp_3_detalleDni
            if (!seccion) return;
            
            // Mostramos si es DNI, ocultamos si es CE u otro
            if (val === 'DNI') {
                seccion.classList.remove('hidden');
            } else {
                seccion.classList.add('hidden');
            }
        }

        if (selectTipoDoc) {
            // Estado inicial al cargar
            toggleSeccionDni(selectTipoDoc.value);
            
            // Al cambiar el select
            selectTipoDoc.addEventListener('change', function() {
                toggleSeccionDni(this.value);
            });
        }

        // 2. Animación Submit y bloqueo de botón
        const form = document.getElementById('form-monitoreo-triaje');
        if(form){
            form.onsubmit = function() {
                const btn = document.getElementById('btn-submit-action');
                const icon = document.getElementById('icon-save-loader');
                
                if(btn) {
                    btn.disabled = true;
                    btn.classList.add('opacity-50', 'cursor-not-allowed');
                }
                if(icon) {
                    icon.innerHTML = '<svg class="animate-spin -ml-1 mr-3 h-8 w-8 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';
                }
                return true;
            };
        }
    });
</script>
@endsection