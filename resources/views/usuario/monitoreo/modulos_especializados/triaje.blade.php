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
              id="form-monitoreo-triaje"
              onsubmit="sincronizarDatos(event)">
            @csrf

            {{-- CONTENEDOR PARA LOS INPUTS OCULTOS GENERADOS POR JS --}}
            <div id="inputs_ocultos_container"></div>
            
            {{-- 1. DETALLES DEL CONSULTORIO --}}
            <x-esp_1_detalleDeConsultorio :detalle="$detalle" />

            {{-- 2. DATOS DEL PROFESIONAL --}}
            <x-esp_2_datosProfesional prefix="rrhh" :detalle="$detalle" />

            {{-- 2.1 DOCUMENTACIÓN ADMINISTRATIVA (Contiene el Select de SIHCE) --}}
            <x-esp_2_1_docAdmin prefix="rrhh" :detalle="$detalle" />

            {{-- 3. DETALLE DE DNI Y FIRMA DIGITAL --}}
            {{-- Nota: El JS buscará el ID 'seccion_dni_firma' dentro de este componente para ocultarlo/mostrarlo --}}
            <x-esp_3_detalleDni :detalle="$detalle" parentKey="rrhh" color='teal' />

            {{-- 4. DETALLES DE CAPACITACIÓN (CON WRAPPER DE VISIBILIDAD) --}}
            <div id="wrapper_capacitacion">
                @php
                    // Preparamos los datos para que el componente de Alpine los inicialice
                    $datosCapacitacion = [
                        'recibieron_cap' => data_get($detalle->contenido, 'capacitacion.recibieron_cap', ''),
                        'institucion_cap' => data_get($detalle->contenido, 'capacitacion.institucion_cap', '')
                    ];
                @endphp
                <x-esp_4_detalleCap :model="json_encode($datosCapacitacion)" />
            </div>

            {{-- 5. EQUIPAMIENTO DE TRIAJE --}}
            <x-esp_5_equipos :equipos="$equipos" modulo="triaje_esp" />

            {{-- 6. SOPORTE (CON WRAPPER DE VISIBILIDAD) --}}
            <div id="wrapper_soporte">
                <x-esp_6_soporte :detalle="$detalle" />
            </div>

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
    /**
     * 1. Lógica de Sincronización de datos (Alpine -> Hidden Inputs)
     * Extrae los valores de los componentes dinámicos y los prepara para el Controlador.
     */
    function sincronizarDatos(e) {
        const createHidden = (name, value) => {
            if (!value) return;
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = name;
            input.value = value;
            document.getElementById('inputs_ocultos_container').appendChild(input);
        };

        // Limpiar contenedor previo
        document.getElementById('inputs_ocultos_container').innerHTML = '';

        // Sincronizar Capacitación (Solo si es visible)
        const capWrap = document.getElementById('wrapper_capacitacion');
        if (capWrap && capWrap.style.display !== 'none') {
            const radioCap = capWrap.querySelector('input[type="radio"]:checked');
            if (radioCap) createHidden('contenido[capacitacion][recibieron_cap]', radioCap.value);
            
            const selectCap = capWrap.querySelector('select');
            if (selectCap && selectCap.value) createHidden('contenido[capacitacion][institucion_cap]', selectCap.value);
        }

        // Animación de carga en el botón
        const btn = document.getElementById('btn-submit-action');
        const icon = document.getElementById('icon-save-loader');
        if(btn) {
            btn.disabled = true;
            btn.classList.add('opacity-50', 'cursor-not-allowed');
        }
        if(icon) {
            icon.innerHTML = '<svg class="animate-spin h-8 w-8 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';
        }
    }

    /**
     * 2. Lógica de Visibilidad y DNI
     */
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof lucide !== 'undefined') lucide.createIcons();

        // --- Lógica SIHCE (Ocultar secciones Capacitación y Soporte si NO usa SIHCE) ---
        // Nombre del input generado por x-esp_2_1_docAdmin con prefix="rrhh"
        const sihceInputName = 'contenido[rrhh][cuenta_sihce]';
        const sectionWrappers = ['wrapper_capacitacion', 'wrapper_soporte'];

        function toggleSihceSections() {
            const select = document.querySelector(`select[name="${sihceInputName}"]`);
            if (!select) return;

            const isNo = select.value === 'NO';
            sectionWrappers.forEach(id => {
                const el = document.getElementById(id);
                if (el) el.style.display = isNo ? 'none' : 'block';
            });
        }

        // --- Lógica DNI (Mostrar/Ocultar detalles de firma si es DNI) ---
        // Nombre del input generado por x-esp_2_datosProfesional con prefix="rrhh"
        const selectTipoDoc = document.querySelector('select[name="contenido[rrhh][tipo_doc]"]');
        function toggleSeccionDni(val) {
            // Este ID debe existir dentro del componente x-esp_3_detalleDni
            const seccion = document.getElementById('seccion_dni_firma');
            if (!seccion) return;
            
            if (val === 'DNI') {
                seccion.classList.remove('hidden');
            } else {
                seccion.classList.add('hidden');
            }
        }

        // Listeners para cambios en tiempo real
        document.body.addEventListener('change', (e) => {
            if (e.target.name === sihceInputName) toggleSihceSections();
            if (e.target.name === 'contenido[rrhh][tipo_doc]') toggleSeccionDni(e.target.value);
        });

        // Ejecutar al cargar la página para establecer el estado inicial correcto
        toggleSihceSections();
        if (selectTipoDoc) toggleSeccionDni(selectTipoDoc.value);
    });
</script>
@endsection