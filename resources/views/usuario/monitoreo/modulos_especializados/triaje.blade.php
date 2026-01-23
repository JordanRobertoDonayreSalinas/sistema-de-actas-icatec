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
            {{-- 1.- DETALLES DEL AMBIENTE (USANDO TU COMPONENTE PULIDO) --}}
            {{-- Pasamos 'detalle' y opcionalmente 'titulo' si quisieras cambiar el h2 del componente --}}
            <x-esp_1_detalleDeConsultorio :detalle="$detalle" />

            {{-- 2. DATOS DEL PROFESIONAL --}}
            {{-- CORRECCIÓN AQUI: Se agrega prefix="rrhh" para que el componente funcione --}}
            <x-esp_2_datosProfesional prefix="rrhh" :detalle="$detalle" />
            {{-- 2.- DATOS DEL PROFESIONAL --}}
            <div class="bg-white rounded-[2rem] p-8 shadow-lg border border-slate-100">
                <div class="flex items-center gap-3 mb-6 border-b border-slate-100 pb-4">
                    <span class="bg-teal-600 text-white w-8 h-8 flex items-center justify-center rounded-full font-black text-sm">2</span>
                    <h3 class="text-teal-900 font-black text-lg uppercase tracking-tight">DATOS DEL PROFESIONAL</h3>
                </div>

                {{-- BUSQUEDA DE PROFESIONAL --}}
                <div class="mb-6">
                    <x-busqueda-profesional prefix="rrhh" :detalle="$detalle" />
                </div>

                {{-- SIHCE / DDJJ / CONFIDENCIALIDAD --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-4 border-t border-slate-100">
                    <div>
                        <label class="block text-teal-600 text-[10px] font-black uppercase tracking-widest mb-2">¿Utiliza SIHCE?</label>
                        <select name="contenido[cuenta_sihce]" id="cuenta_sihce" onchange="toggleSihceAndDocs(this.value)" class="w-full px-4 py-3 bg-teal-50 border-2 border-teal-100 rounded-xl font-bold text-sm uppercase outline-none text-teal-700 cursor-pointer hover:bg-teal-100 transition-colors">
                            <option value="SI" {{ ($detalle->contenido['cuenta_sihce'] ?? '') == 'SI' ? 'selected' : '' }}>SI</option>
                            <option value="NO" {{ ($detalle->contenido['cuenta_sihce'] ?? '') == 'NO' ? 'selected' : '' }}>NO</option>
                        </select>
                    </div>
                    <div id="div_firmo_dj">
                        <label class="block text-slate-500 text-[10px] font-black uppercase tracking-widest mb-2">¿Firmó Declaración Jurada?</label>
                        <select name="contenido[firmo_dj]" id="firmo_dj" class="w-full px-4 py-3 bg-slate-50 border-2 border-slate-200 rounded-xl font-bold text-sm uppercase outline-none focus:border-teal-500">
                            <option value="SI" {{ ($detalle->contenido['firmo_dj'] ?? '') == 'SI' ? 'selected' : '' }}>SI</option>
                            <option value="NO" {{ ($detalle->contenido['firmo_dj'] ?? '') == 'NO' ? 'selected' : '' }}>NO</option>
                        </select>
                    </div>
                    <div id="div_firmo_confidencialidad">
                        <label class="block text-slate-500 text-[10px] font-black uppercase tracking-widest mb-2">¿Firmó Confidencialidad?</label>
                        <select name="contenido[firmo_confidencialidad]" id="firmo_confidencialidad" class="w-full px-4 py-3 bg-slate-50 border-2 border-slate-200 rounded-xl font-bold text-sm uppercase outline-none focus:border-teal-500">
                            <option value="SI" {{ ($detalle->contenido['firmo_confidencialidad'] ?? '') == 'SI' ? 'selected' : '' }}>SI</option>
                            <option value="NO" {{ ($detalle->contenido['firmo_confidencialidad'] ?? '') == 'NO' ? 'selected' : '' }}>NO</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- 3. DETALLE DE DNI Y FIRMA DIGITAL --}}
            {{-- Asegúrate que este componente tenga el ID 'seccion_detalle_dni' internamente --}}
            <x-esp_3_detalleDni :detalle="$detalle" />

            {{-- 4. DETALLES DE CAPACITACIÓN --}}
            <x-esp_4_detalleCap :detalle="$detalle" />

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

{{-- 
    SCRIPTS DE INTERACCIÓN 
    Estos scripts controlan la lógica entre componentes.
    IMPORTANTE: Los IDs utilizados aquí deben coincidir con los que están dentro de tus componentes blade (esp_*).
--}}
<script>
    // Controla visibilidad de campos SIHCE/DJ (Componente 2 y 4)
    function toggleSihceAndDocs(val) {
        const divDj = document.getElementById('div_firmo_dj');
        const divConf = document.getElementById('div_firmo_confidencialidad');
        const djSelect = document.getElementById('firmo_dj');
        const confSelect = document.getElementById('firmo_confidencialidad');

        if (divDj && divConf) {
            if (val === 'SI') {
                divDj.classList.remove('hidden');
                divConf.classList.remove('hidden');
            } else {
                divDj.classList.add('hidden');
                divConf.classList.add('hidden');
                if(djSelect) djSelect.value = 'NO';
                if(confSelect) confSelect.value = 'NO';
            }
        }
    }

    // Controla visibilidad de la sección de DNI (Componente 3)
    function toggleSeccionDni(tipoDoc) {
        const seccion = document.getElementById('seccion_detalle_dni');
        if (!seccion) return;
        
        if (tipoDoc === 'DNI') {
            seccion.classList.remove('hidden');
            const dniVal = document.getElementById('tipo_dni_input').value;
            if(dniVal) selectDniType(dniVal);
        } else {
            seccion.classList.add('hidden');
        }
    }

    // Controla selección visual de tipo de DNI (Componente 3)
    function selectDniType(tipo) {
        const input = document.getElementById('tipo_dni_input');
        const cardElectronico = document.getElementById('card_electronico');
        const cardAzul = document.getElementById('card_azul');
        const bloqueOpciones = document.getElementById('bloque_opciones_dni');
        const bloqueVersion = document.getElementById('bloque_version_dnie');
        const bloqueFirma = document.getElementById('bloque_firma_digital');

        if(input) input.value = tipo;
        if(bloqueOpciones) bloqueOpciones.classList.remove('hidden');

        if (tipo === 'ELECTRONICO') {
            if(cardElectronico) {
                cardElectronico.classList.add('border-teal-600', 'bg-teal-50');
                cardElectronico.classList.remove('border-slate-200', 'bg-white');
            }
            if(cardAzul) {
                cardAzul.classList.remove('border-teal-600', 'bg-teal-50');
                cardAzul.classList.add('border-slate-200', 'bg-white');
            }
            if(bloqueVersion) bloqueVersion.classList.remove('hidden');
            if(bloqueFirma) bloqueFirma.classList.remove('hidden');
        } else {
            if(cardAzul) {
                cardAzul.classList.add('border-teal-600', 'bg-teal-50');
                cardAzul.classList.remove('border-slate-200', 'bg-white');
            }
            if(cardElectronico) {
                cardElectronico.classList.remove('border-teal-600', 'bg-teal-50');
                cardElectronico.classList.add('border-slate-200', 'bg-white');
            }
            if(bloqueVersion) bloqueVersion.classList.add('hidden');
            if(bloqueFirma) bloqueFirma.classList.add('hidden');
        }
    }

    // Controla visibilidad de entidad capacitadora (Componente 4)
    function toggleEntidadCapacitadora(val) {
        const wrapper = document.getElementById('wrapper_entidad_capacitadora');
        if(wrapper) {
            val === 'SI' ? wrapper.classList.remove('hidden') : wrapper.classList.add('hidden');
        }
    }

    // Preview de imagen de evidencia (Componente 7)
    function previewImage(event) {
        const input = event.target;
        const preview = document.getElementById('img-preview');
        const icon = document.getElementById('upload-icon');
        const fileName = document.getElementById('file-name-display');
        
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                if(preview) {
                    preview.src = e.target.result;
                    preview.classList.remove('hidden');
                }
                if(icon) icon.classList.add('hidden');
                if(fileName) fileName.innerText = "NUEVA: " + input.files[0].name.substring(0, 15).toUpperCase() + "...";
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    // Inicialización
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof lucide !== 'undefined') lucide.createIcons();

        // Inicializar estados según valores guardados
        const selectCapacitacion = document.getElementById('recibio_capacitacion');
        if (selectCapacitacion) toggleEntidadCapacitadora(selectCapacitacion.value);

        const selectSihce = document.getElementById('cuenta_sihce');
        if (selectSihce) toggleSihceAndDocs(selectSihce.value);

        const dniInput = document.getElementById('tipo_dni_input');
        if(dniInput && dniInput.value) selectDniType(dniInput.value);
        
        // Listener para el tipo de documento del profesional en Componente 2
        // IMPORTANTE: Como usas prefix='rrhh', el name es contenido[rrhh][tipo_doc]
        const selectTipoDoc = document.querySelector('select[name="contenido[rrhh][tipo_doc]"]'); 
        
        if (selectTipoDoc) {
            toggleSeccionDni(selectTipoDoc.value);
            selectTipoDoc.addEventListener('change', function() {
                toggleSeccionDni(this.value);
            });
        }
    });

    // Animación Submit
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
                icon.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="animate-spin"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>';
            }
            return true;
        };
    }
</script>
@endsection