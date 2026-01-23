@extends('layouts.usuario')

@section('title', 'Módulo: Farmacia CSMC')

@section('content')
<div class="py-12 bg-slate-50 min-h-screen">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        
        {{-- ENCABEZADO SUPERIOR --}}
        <div class="mb-8 flex flex-col md:flex-row items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-3 mb-1">
                    <span class="px-3 py-1 bg-teal-600 text-white text-[10px] font-black rounded-lg uppercase tracking-widest">Módulo Especializado</span>
                    <span class="text-slate-400 font-bold text-[10px] uppercase">ID Acta: #{{ str_pad($monitoreo->numero_acta ?? $monitoreo->id, 5, '0', STR_PAD_LEFT) }}</span>
                </div>
                <h2 class="text-3xl font-black text-slate-900 uppercase tracking-tight">06. Farmacia</h2>
                <p class="text-slate-500 font-bold uppercase text-xs mt-1">
                    <i data-lucide="clipboard-pulse" class="inline-block w-4 h-4 mr-1 text-teal-500"></i> {{ $monitoreo->establecimiento->nombre }}
                </p>
            </div>
            <a href="{{ route('usuario.monitoreo.modulos', $monitoreo->id) }}" class="flex items-center gap-2 px-6 py-3 bg-white border-2 border-slate-200 rounded-2xl text-slate-600 font-black text-xs hover:bg-slate-50 transition-all uppercase shadow-sm">
                <i data-lucide="arrow-left" class="w-4 h-4"></i> Volver al Panel
            </a>
        </div>

        {{-- FORMULARIO --}}
        <form action="{{ route('usuario.monitoreo.farmacia_esp.store', $monitoreo->id) }}" 
              method="POST" 
              enctype="multipart/form-data" 
              class="space-y-6" 
              id="form-monitoreo-triaje">
            @csrf
            
            {{-- 1.- DETALLES DEL AMBIENTE --}}
            <x-esp_1_detalleDeConsultorio :detalle="$detalle" />

            {{-- DATOS DEL PROFESIONAL --}}
            <div class="bg-white rounded-[2rem] p-8 shadow-lg border border-slate-100">
                <div class="flex items-center gap-3 mb-6 border-b border-slate-100 pb-4">
                    <span class="bg-teal-600 text-white w-8 h-8 flex items-center justify-center rounded-full font-black text-sm">2</span>
                    <h3 class="text-teal-900 font-black text-lg uppercase tracking-tight">DATOS DEL PROFESIONAL</h3>
                </div>

                {{-- BUSQUEDA DE PROFESIONAL (Componente reutilizable) --}}
                <div class="mb-6">
                    <x-esp_2_datosProfesional prefix="profesional" :detalle="$detalle" color="teal" />
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

            {{-- DETALLE DNI Y FIRMA DIGITAL --}}
            <x-esp_3_detalleDni :detalle="$detalle" color="teal" />

            {{-- 4.- DETALLES DE CAPACITACIÓN --}}
            <x-esp_4_detalleCap :model="json_encode($detalle->contenido ?? [])" />
            
            {{-- EQUIPAMIENTO --}}
            <div class="bg-white rounded-[2rem] p-8 shadow-lg border border-slate-100">
                <div class="flex items-center gap-3 mb-6 border-b border-slate-100 pb-4">
                    <span class="bg-teal-600 text-white w-8 h-8 flex items-center justify-center rounded-full font-black text-sm">5</span>
                    <h3 class="text-teal-900 font-black text-lg uppercase tracking-tight">EQUIPAMIENTO</h3>
                </div>
                <x-esp_5_equipos :equipos="$equipos" modulo="farmacia_esp" />
            </div>

            {{-- SOPORTE--}}
            <div class="bg-white rounded-[2rem] p-8 shadow-lg border border-slate-100">
                <div class="flex items-center gap-3 mb-6 border-b border-slate-100 pb-4">
                    <span class="bg-teal-600 text-white w-8 h-8 flex items-center justify-center rounded-full font-black text-sm">6</span>
                    <h3 class="text-teal-900 font-black text-lg uppercase tracking-tight">SOPORTE</h3>
                </div>
                <x-esp_6_soporte :detalle="$detalle" />
            </div>

            {{-- COMENTARIOS Y EVIDENCIA FOTOGRAFICA --}}
            <x-esp_7_comentariosEvid :comentario="(object) ($detalle->contenido ?? [])" />

            {{-- BOTÓN GRANDE DE GUARDADO --}}
            <div class="pt-10 pb-5 mt-6">
                <button type="submit" id="btn-submit-action" 
                        class="w-full group bg-teal-600 text-white p-8 rounded-[3rem] font-black shadow-2xl shadow-teal-200 flex items-center justify-between hover:bg-teal-700 transition-all duration-500 active:scale-[0.98] cursor-pointer">
                    <div class="flex items-center gap-8 pointer-events-none">
                        <div class="h-16 w-16 bg-white/20 rounded-3xl flex items-center justify-center group-hover:rotate-12 transition-all shadow-lg border border-white/30">
                            <i data-lucide="save" id="icon-save-loader" class="w-8 h-8 text-white"></i>
                        </div>
                        <div class="text-left">
                            <p class="text-xl uppercase tracking-[0.3em] leading-none">Confirmar Registro</p>
                            <p class="text-[10px] text-teal-200 font-bold uppercase mt-3 tracking-widest">Sincronizar Módulo Farmacia Especializada</p>
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
    function toggleSihceAndDocs(val) {
        const divDj = document.getElementById('div_firmo_dj');
        const divConf = document.getElementById('div_firmo_confidencialidad');
        const djSelect = document.getElementById('firmo_dj');
        const confSelect = document.getElementById('firmo_confidencialidad');

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

    function toggleSeccionDni(tipoDoc) {
        const seccion = document.getElementById('seccion_detalle_dni');
        if (!seccion) return;
        if (tipoDoc === 'DNI') {
            seccion.classList.remove('hidden');
        } else {
            seccion.classList.add('hidden');
        }
    }

    function selectDniType(tipo) {
        const input = document.getElementById('tipo_dni_input');
        const cardElectronico = document.getElementById('card_electronico');
        const cardAzul = document.getElementById('card_azul');
        const bloqueOpciones = document.getElementById('bloque_opciones_dni');
        const bloqueVersion = document.getElementById('bloque_version_dnie');
        const bloqueFirma = document.getElementById('bloque_firma_digital');

        input.value = tipo;
        bloqueOpciones.classList.remove('hidden');

        if (tipo === 'ELECTRONICO') {
            cardElectronico.classList.add('border-teal-600', 'bg-teal-50');
            cardElectronico.classList.remove('border-slate-200', 'bg-white');
            cardAzul.classList.remove('border-teal-600', 'bg-teal-50');
            cardAzul.classList.add('border-slate-200', 'bg-white');
            bloqueVersion.classList.remove('hidden');
            bloqueFirma.classList.remove('hidden');
        } else {
            cardAzul.classList.add('border-teal-600', 'bg-teal-50');
            cardAzul.classList.remove('border-slate-200', 'bg-white');
            cardElectronico.classList.remove('border-teal-600', 'bg-teal-50');
            cardElectronico.classList.add('border-slate-200', 'bg-white');
            bloqueVersion.classList.add('hidden');
            bloqueFirma.classList.add('hidden');
        }
    }

    function toggleEntidadCapacitadora(val) {
        const wrapper = document.getElementById('wrapper_entidad_capacitadora');
        val === 'SI' ? wrapper.classList.remove('hidden') : wrapper.classList.add('hidden');
    }

    function checkNationality(tipoDoc) {
        const dniInput = document.getElementById('tipo_dni_input');
        if (dniInput && dniInput.value) {
            selectDniType(dniInput.value);
        }
    }

    function previewImage(event) {
        const input = event.target;
        const preview = document.getElementById('img-preview');
        const icon = document.getElementById('upload-icon');
        const fileName = document.getElementById('file-name-display');
        
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.classList.remove('hidden');
                icon.classList.add('hidden');
                fileName.innerText = "NUEVA: " + input.files[0].name.substring(0, 15).toUpperCase() + "...";
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const selectCapacitacion = document.getElementById('recibio_capacitacion');
        if (selectCapacitacion) toggleEntidadCapacitadora(selectCapacitacion.value);

        const selectSihce = document.getElementById('cuenta_sihce');
        if (selectSihce) toggleSihceAndDocs(selectSihce.value);

        const dniVal = document.getElementById('tipo_dni_input').value;
        if(dniVal) selectDniType(dniVal);
        
        const selectTipoDoc = document.querySelector('select[name="contenido[rrhh][tipo_doc]"]');
        if (selectTipoDoc) {
            toggleSeccionDni(selectTipoDoc.value);
            selectTipoDoc.addEventListener('change', function() {
                toggleSeccionDni(this.value);
            });
        }
        
        if (typeof lucide !== 'undefined') lucide.createIcons();
    });

    document.getElementById('form-monitoreo-triaje').onsubmit = function() {
        const btn = document.getElementById('btn-submit-action');
        const icon = document.getElementById('icon-save-loader');
        
        btn.disabled = true;
        btn.classList.add('opacity-50', 'cursor-not-allowed');
        
        icon.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="animate-spin"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>';
        
        return true;
    };
</script>
@endsection