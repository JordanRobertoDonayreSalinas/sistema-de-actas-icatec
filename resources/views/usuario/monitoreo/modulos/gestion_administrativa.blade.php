@extends('layouts.usuario')

@section('title', 'Módulo 01: Gestión Administrativa')

@section('content')
<div class="py-12 bg-slate-50 min-h-screen">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        
        {{-- ENCABEZADO SUPERIOR --}}
        <div class="mb-8 flex flex-col md:flex-row items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-3 mb-1">
                    <span class="px-3 py-1 bg-indigo-600 text-white text-[10px] font-black rounded-lg uppercase tracking-widest">Módulo Técnico</span>
                    <span class="text-slate-400 font-bold text-[10px] uppercase">ID Acta: #{{ str_pad($acta->id, 5, '0', STR_PAD_LEFT) }}</span>
                </div>
                <h2 class="text-3xl font-black text-slate-900 uppercase tracking-tight">01. Gestión Administrativa</h2>
                <p class="text-slate-500 font-bold uppercase text-xs mt-1">
                    <i data-lucide="hospital" class="inline-block w-4 h-4 mr-1 text-indigo-500"></i> {{ $acta->establecimiento->nombre }}
                </p>
            </div>
            <a href="{{ route('usuario.monitoreo.modulos', $acta->id) }}" class="flex items-center gap-2 px-6 py-3 bg-white border-2 border-slate-200 rounded-2xl text-slate-600 font-black text-xs hover:bg-slate-50 transition-all uppercase shadow-sm">
                <i data-lucide="arrow-left" class="w-4 h-4"></i> Volver al Panel
            </a>
        </div>

        <form action="{{ route('usuario.monitoreo.gestion-administrativa.store', $acta->id) }}" 
              method="POST" 
              enctype="multipart/form-data" 
              class="space-y-6" 
              id="form-monitoreo-final">
            @csrf
            
            {{-- 1.- DATOS GENERALES --}}
            <div class="bg-white rounded-[2rem] p-8 shadow-lg border border-slate-100">
                <div class="flex items-center gap-3 mb-6 border-b border-slate-100 pb-4">
                    <span class="bg-indigo-600 text-white w-8 h-8 flex items-center justify-center rounded-full font-black text-sm">1</span>
                    <h3 class="text-indigo-900 font-black text-lg uppercase tracking-tight">DATOS GENERALES</h3>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
                    {{-- FECHA --}}
                    <div>
                        <label class="block text-slate-500 text-[10px] font-black uppercase tracking-widest mb-2">Fecha de Monitoreo</label>
                        <input type="date" 
                               name="contenido[fecha]" 
                               value="{{ $detalle->contenido['fecha'] ?? date('Y-m-d') }}" 
                               class="w-full bg-slate-50 border-2 border-slate-200 rounded-xl px-4 py-3 text-slate-800 font-bold outline-none focus:border-indigo-500 transition-all">
                    </div>

                    {{-- TURNO --}}
                    <div>
                       <x-turno :selected="$detalle->contenido['turno'] ?? ''" />
                    </div>
                </div>
            </div>

            {{-- 2.- DATOS DEL PROFESIONAL --}}
            <div class="bg-white rounded-[2rem] p-8 shadow-lg border border-slate-100">
                <div class="flex items-center gap-3 mb-6 border-b border-slate-100 pb-4">
                    <span class="bg-indigo-600 text-white w-8 h-8 flex items-center justify-center rounded-full font-black text-sm">2</span>
                    <h3 class="text-indigo-900 font-black text-lg uppercase tracking-tight">DATOS DEL PROFESIONAL</h3>
                </div>

                {{-- Componente de Búsqueda --}}
                <div class="mb-6">
                <x-busqueda-profesional prefix="rrhh" :detalle="$detalle" />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    {{-- CARGO --}}
                    <div>
                        <label class="block text-slate-500 text-[10px] font-black uppercase tracking-widest mb-2">Cargo</label>
                        <select name="contenido[cargo_profesional]" id="cargo_profesional" onchange="toggleCargoManual(this.value)" class="w-full px-4 py-3 bg-slate-50 border-2 border-slate-200 rounded-xl font-bold text-sm uppercase outline-none focus:border-indigo-500 transition-all cursor-pointer">
                            <option value="MEDICO" {{ ($detalle->contenido['cargo_profesional'] ?? '') == 'MEDICO' ? 'selected' : '' }}>MEDICO</option>
                            <option value="ENFERMERA" {{ ($detalle->contenido['cargo_profesional'] ?? '') == 'ENFERMERA' ? 'selected' : '' }}>ENFERMERA</option>
                            <option value="OBSTETRA" {{ ($detalle->contenido['cargo_profesional'] ?? '') == 'OBSTETRA' ? 'selected' : '' }}>OBSTETRA</option>
                            <option value="TECNICO" {{ ($detalle->contenido['cargo_profesional'] ?? '') == 'TECNICO' ? 'selected' : '' }}>TECNICO</option>
                            <option value="OTROS" {{ ($detalle->contenido['cargo_profesional'] ?? '') == 'OTROS' ? 'selected' : '' }}>OTROS (ESPECIFICAR)</option>
                        </select>
                        <div id="div_cargo_manual" class="mt-2 {{ ($detalle->contenido['cargo_profesional'] ?? '') == 'OTROS' ? '' : 'hidden' }}">
                            <input type="text" name="contenido[cargo_profesional_manual]" value="{{ $detalle->contenido['cargo_profesional_manual'] ?? '' }}" class="w-full px-4 py-3 bg-indigo-50 border-2 border-indigo-200 rounded-xl font-bold text-sm uppercase outline-none text-indigo-700 placeholder-indigo-300" placeholder="Escriba el cargo aquí...">
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-4 border-t border-slate-100">
                    {{-- 1. SIHCE (PRIMERO) --}}
                    <div>
                        <label class="block text-indigo-600 text-[10px] font-black uppercase tracking-widest mb-2">¿Utiliza SIHCE?</label>
                        <select name="contenido[cuenta_sihce]" id="cuenta_sihce" onchange="toggleSihceAndDocs(this.value)" class="w-full px-4 py-3 bg-indigo-50 border-2 border-indigo-100 rounded-xl font-bold text-sm uppercase outline-none text-indigo-700 cursor-pointer hover:bg-indigo-100 transition-colors">
                            <option value="SI" {{ ($detalle->contenido['cuenta_sihce'] ?? '') == 'SI' ? 'selected' : '' }}>SI</option>
                            <option value="NO" {{ ($detalle->contenido['cuenta_sihce'] ?? '') == 'NO' ? 'selected' : '' }}>NO</option>
                        </select>
                    </div>

                    {{-- 2. DDJJ --}}
                    <div>
                        <label class="block text-slate-500 text-[10px] font-black uppercase tracking-widest mb-2">¿Firmó Declaración Jurada?</label>
                        <select name="contenido[firmo_dj]" id="firmo_dj" class="w-full px-4 py-3 bg-slate-50 border-2 border-slate-200 rounded-xl font-bold text-sm uppercase outline-none focus:border-indigo-500">
                            <option value="SI" {{ ($detalle->contenido['firmo_dj'] ?? '') == 'SI' ? 'selected' : '' }}>SI</option>
                            <option value="NO" {{ ($detalle->contenido['firmo_dj'] ?? '') == 'NO' ? 'selected' : '' }}>NO</option>
                        </select>
                    </div>

                    {{-- 3. CONFIDENCIALIDAD --}}
                    <div>
                        <label class="block text-slate-500 text-[10px] font-black uppercase tracking-widest mb-2">¿Firmó Compromiso Confidencialidad?</label>
                        <select name="contenido[firmo_confidencialidad]" id="firmo_confidencialidad" class="w-full px-4 py-3 bg-slate-50 border-2 border-slate-200 rounded-xl font-bold text-sm uppercase outline-none focus:border-indigo-500">
                            <option value="SI" {{ ($detalle->contenido['firmo_confidencialidad'] ?? '') == 'SI' ? 'selected' : '' }}>SI</option>
                            <option value="NO" {{ ($detalle->contenido['firmo_confidencialidad'] ?? '') == 'NO' ? 'selected' : '' }}>NO</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- 3.- DETALLE DE DNI Y FIRMA DIGITAL --}}
            <div id="section_dni_detalle" class="bg-white rounded-[2rem] p-8 shadow-lg border border-slate-100 hidden">
                <div class="flex items-center gap-3 mb-6 border-b border-slate-100 pb-4">
                    <span class="bg-indigo-600 text-white w-8 h-8 flex items-center justify-center rounded-full font-black text-sm">3</span>
                    <h3 class="text-indigo-900 font-black text-lg uppercase tracking-tight">DETALLE DE DNI Y FIRMA DIGITAL</h3>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- TIPO DE DNI --}}
                    <div>
                        <label class="block text-slate-500 text-[10px] font-black uppercase tracking-widest mb-2">Tipo de DNI</label>
                        <select name="contenido[tipo_dni]" id="tipo_dni" onchange="toggleDniElectronico(this.value)" class="w-full px-4 py-3 bg-slate-50 border-2 border-slate-200 rounded-xl font-bold text-sm uppercase outline-none focus:border-indigo-500 cursor-pointer">
                            <option value="">SELECCIONE...</option>
                            <option value="ELECTRONICO" {{ ($detalle->contenido['tipo_dni'] ?? '') == 'ELECTRONICO' ? 'selected' : '' }}>ELECTRÓNICO</option>
                            <option value="AZUL" {{ ($detalle->contenido['tipo_dni'] ?? '') == 'AZUL' ? 'selected' : '' }}>AZUL</option>
                        </select>
                    </div>

                    {{-- OBSERVACIONES --}}
                    <div class="md:row-span-2">
                        <label class="block text-slate-500 text-[10px] font-black uppercase tracking-widest mb-2">Observaciones</label>
                        <textarea name="contenido[observaciones_dni]" rows="4" class="w-full px-4 py-3 bg-slate-50 border-2 border-slate-200 rounded-xl font-bold text-sm uppercase outline-none focus:border-indigo-500" >{{ $detalle->contenido['observaciones_dni'] ?? '' }}</textarea>
                    </div>

                    {{-- SUB-SECCIÓN DNIe (Condicional) --}}
                    <div id="bloque_dnie" class="col-span-1 grid grid-cols-1 gap-6 hidden">
                        <div>
                            <label class="block text-slate-500 text-[10px] font-black uppercase tracking-widest mb-2">Versión DNIe</label>
                            <select name="contenido[version_dnie]" class="w-full px-4 py-3 bg-slate-50 border-2 border-slate-200 rounded-xl font-bold text-sm uppercase outline-none focus:border-indigo-500">
                                <option value="" selected disabled>SELECCIONAR...</option>
                                <option value="1.0" {{ ($detalle->contenido['version_dnie'] ?? '') == '1.0' ? 'selected' : '' }}>1.0</option>
                                <option value="2.0" {{ ($detalle->contenido['version_dnie'] ?? '') == '2.0' ? 'selected' : '' }}>2.0</option>
                                <option value="3.0" {{ ($detalle->contenido['version_dnie'] ?? '') == '3.0' ? 'selected' : '' }}>3.0</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-slate-500 text-[10px] font-black uppercase tracking-widest mb-2">¿Firma Digitalmente en SIHCE?</label>
                            <select name="contenido[firma_digital_sihce]" class="w-full px-4 py-3 bg-slate-50 border-2 border-slate-200 rounded-xl font-bold text-sm uppercase outline-none focus:border-indigo-500">
                                <option value="SI" {{ ($detalle->contenido['firma_digital_sihce'] ?? '') == 'SI' ? 'selected' : '' }}>SI</option>
                                <option value="NO" {{ ($detalle->contenido['firma_digital_sihce'] ?? '') == 'NO' ? 'selected' : '' }}>NO</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 4.- DETALLES DE CAPACITACIÓN --}}
            <div class="bg-white rounded-[2rem] p-8 shadow-lg border border-slate-100">
                <div class="flex items-center gap-3 mb-6 border-b border-slate-100 pb-4">
                    <span class="bg-indigo-600 text-white w-8 h-8 flex items-center justify-center rounded-full font-black text-sm">4</span>
                    <h3 class="text-indigo-900 font-black text-lg uppercase tracking-tight">DETALLES DE CAPACITACIÓN</h3>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-slate-500 text-[10px] font-black uppercase tracking-widest mb-2">¿Recibió Capacitación?</label>
                        <select name="contenido[recibio_capacitacion]" id="recibio_capacitacion" onchange="toggleEntidadCapacitadora(this.value)" class="w-full px-4 py-3 bg-slate-50 border-2 border-slate-200 rounded-xl font-bold text-sm outline-none focus:border-indigo-500 transition-all uppercase cursor-pointer">
                            <option value="SI" {{ ($detalle->contenido['recibio_capacitacion'] ?? '') == 'SI' ? 'selected' : '' }}>SI</option>
                            <option value="NO" {{ ($detalle->contenido['recibio_capacitacion'] ?? '') == 'NO' ? 'selected' : '' }}>NO</option>
                        </select>
                    </div>
                    <div id="wrapper_entidad_capacitadora" class="hidden">
                        <label class="block text-slate-500 text-[10px] font-black uppercase tracking-widest mb-2">¿De parte de quién?</label>
                        <select name="contenido[inst_que_lo_capacito]" class="w-full px-4 py-3 bg-slate-50 border-2 border-slate-200 rounded-xl font-bold text-sm outline-none focus:border-indigo-500 transition-all uppercase cursor-pointer">
                            <option value="UNIDAD EJECUTORA" {{ ($detalle->contenido['inst_que_lo_capacito'] ?? '') == 'UNIDAD EJECUTORA' ? 'selected' : '' }}>UNIDAD EJECUTORA</option>
                            <option value="DIRESA" {{ ($detalle->contenido['inst_que_lo_capacito'] ?? '') == 'DIRESA' ? 'selected' : '' }}>DIRESA</option>
                            <option value="MINSA" {{ ($detalle->contenido['inst_que_lo_capacito'] ?? '') == 'MINSA' ? 'selected' : '' }}>MINSA</option>
                            <option value="OTROS" {{ ($detalle->contenido['inst_que_lo_capacito'] ?? '') == 'OTROS' ? 'selected' : '' }}>OTROS</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- 5.- EQUIPAMIENTO DEL CONSULTORIO --}}
            <div class="bg-white rounded-[2rem] p-8 shadow-lg border border-slate-100">
                <div class="flex items-center gap-3 mb-6 border-b border-slate-100 pb-4">
                    <span class="bg-indigo-600 text-white w-8 h-8 flex items-center justify-center rounded-full font-black text-sm">5</span>
                    <h3 class="text-indigo-900 font-black text-lg uppercase tracking-tight">EQUIPAMIENTO DEL CONSULTORIO</h3>
                </div>
                <x-tabla-equipos :equipos="$equipos" modulo="gestion_administrativa" />
            </div>

            {{-- 7.- SOPORTE (Oculto si SIHCE es NO) --}}
            <div id="section_soporte" class="bg-white rounded-[2rem] p-8 shadow-lg border border-slate-100 {{ ($detalle->contenido['cuenta_sihce'] ?? '') == 'NO' ? 'hidden' : '' }}">
                <div class="flex items-center gap-3 mb-6 border-b border-slate-100 pb-4">
                    <span class="bg-indigo-600 text-white w-8 h-8 flex items-center justify-center rounded-full font-black text-sm">7</span>
                    <h3 class="text-indigo-900 font-black text-lg uppercase tracking-tight">SOPORTE TÉCNICO</h3>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-slate-500 text-[10px] font-black uppercase tracking-widest mb-2">Ante dificultades se comunica con:</label>
                        <select name="contenido[inst_a_quien_comunica]" class="w-full px-4 py-3 bg-slate-50 border-2 border-slate-200 rounded-xl font-bold text-sm outline-none uppercase cursor-pointer">
                            @foreach(['DIRESA','UNIDAD EJECUTORA','JEFE DE ESTABLECIMIENTO','MINSA','OTROS'] as $op)
                                <option value="{{$op}}" {{ ($detalle->contenido['inst_a_quien_comunica'] ?? '') == $op ? 'selected' : '' }}>{{$op}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-slate-500 text-[10px] font-black uppercase tracking-widest mb-2">Medio que utiliza:</label>
                        <select name="contenido[medio_que_utiliza]" class="w-full px-4 py-3 bg-slate-50 border-2 border-slate-200 rounded-xl font-bold text-sm outline-none uppercase cursor-pointer">
                            @foreach(['CELULAR','EMAIL','WHATSAPP','OTROS'] as $me)
                                <option value="{{$me}}" {{ ($detalle->contenido['medio_que_utiliza'] ?? '') == $me ? 'selected' : '' }}>{{$me}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- 8.- PROGRAMACIÓN ACTUAL EN EL SIHCE HASTA --}}
            <div id="section_programacion" class="bg-white rounded-[2rem] p-8 shadow-lg border border-slate-100 {{ ($detalle->contenido['cuenta_sihce'] ?? '') == 'NO' ? 'hidden' : '' }}">
                <div class="flex items-center gap-3 mb-6 border-b border-slate-100 pb-4">
                    <span class="bg-indigo-600 text-white w-8 h-8 flex items-center justify-center rounded-full font-black text-sm">8</span>
                    <h3 class="text-indigo-900 font-black text-lg uppercase tracking-tight">PROGRAMACIÓN ACTUAL EN EL SIHCE HASTA:</h3>
                </div>
                <div>
                    <label class="block text-slate-500 text-[10px] font-black uppercase tracking-widest mb-2">SELECCIONE FECHA (MES Y AÑO)</label>
                    <input type="month" 
                           name="contenido[fecha_programacion]" 
                           value="{{ $detalle->contenido['fecha_programacion'] ?? '' }}" 
                           class="w-full px-4 py-3 bg-slate-50 border-2 border-slate-200 rounded-xl font-bold text-sm uppercase outline-none focus:border-indigo-500 transition-all text-slate-700 cursor-pointer">
                </div>
            </div>

            {{-- 9.- COMENTARIOS y 10.- EVIDENCIA --}}
            <div class="bg-slate-900 rounded-[3rem] p-10 shadow-2xl text-white">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
                    {{-- 9.- COMENTARIOS --}}
                    <div>
                        <div class="flex items-center gap-3 mb-6">
                            <span class="bg-indigo-500 text-white w-8 h-8 flex items-center justify-center rounded-full font-black text-sm">9</span>
                            <h3 class="text-white font-black text-lg uppercase tracking-tight">COMENTARIOS</h3>
                        </div>
                        <textarea name="contenido[comentarios]" rows="6" class="w-full bg-white/10 border-2 border-white/20 rounded-2xl p-4 text-white font-bold outline-none focus:border-indigo-500 transition-all uppercase placeholder-white/30">{{ $detalle->contenido['comentarios'] ?? '' }}</textarea>
                    </div>
                    
                    {{-- 10.- EVIDENCIA --}}
                    <div>
                        <div class="flex items-center gap-3 mb-6">
                            <span class="bg-red-500 text-white w-8 h-8 flex items-center justify-center rounded-full font-black text-sm">10</span>
                            <h3 class="text-white font-black text-lg uppercase tracking-tight">EVIDENCIA FOTOGRÁFICA</h3>
                        </div>
                        @if(isset($detalle->contenido['foto_evidencia']))
                            <div class="mb-4 relative group w-full">
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Imagen Actual:</p>
                                <div class="rounded-2xl overflow-hidden border-2 border-white/20 shadow-lg h-32 w-32 bg-black/50">
                                    <img src="{{ asset('storage/' . $detalle->contenido['foto_evidencia']) }}" class="w-full h-full object-cover">
                                </div>
                            </div>
                        @endif
                        <div class="relative group">
                            <input type="file" name="foto_evidencia" id="foto_evidencia" accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-20" onchange="previewImage(event)">
                            <div id="dropzone" class="bg-white/5 border-2 border-dashed border-white/20 rounded-[2rem] p-8 flex flex-col items-center justify-center group-hover:bg-white/10 transition-all shadow-inner h-48 border-spacing-4">
                                <i data-lucide="upload-cloud" id="upload-icon" class="w-8 h-8 text-indigo-400 mb-2"></i>
                                <span id="file-name-display" class="text-[10px] font-bold uppercase tracking-widest text-slate-300 text-center">
                                    {{ isset($detalle->contenido['foto_evidencia']) ? 'CLICK PARA CAMBIAR' : 'SUBIR FOTO' }}
                                </span>
                                <img id="img-preview" src="#" class="hidden mt-2 w-20 h-20 object-cover rounded-lg border-2 border-indigo-500">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 11.- FIRMA --}}
                <div class="mt-12 pt-10 border-t border-white/10 text-center">
                    
                    <button type="submit" id="btn-submit-action" class="w-full md:w-auto px-12 py-4 bg-emerald-500 hover:bg-emerald-600 text-white font-black rounded-xl uppercase tracking-widest shadow-lg shadow-emerald-500/30 transition-all transform hover:scale-105 flex items-center justify-center gap-2 mx-auto">
                        <span id="btn-text">GUARDAR</span>
                        <i data-lucide="save" class="w-5 h-5"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    function toggleCargoManual(val) {
        const div = document.getElementById('div_cargo_manual');
        if (val === 'OTROS') {
            div.classList.remove('hidden');
        } else {
            div.classList.add('hidden');
        }
    }

    function toggleSihceAndDocs(val) {
        const sectionSoporte = document.getElementById('section_soporte');
        const sectionProgramacion = document.getElementById('section_programacion');
        const djSelect = document.getElementById('firmo_dj');
        const confSelect = document.getElementById('firmo_confidencialidad');

        if (val === 'SI') {
            sectionSoporte.classList.remove('hidden');
            sectionProgramacion.classList.remove('hidden');
        } else {
            sectionSoporte.classList.add('hidden');
            sectionProgramacion.classList.add('hidden');
            if(djSelect) djSelect.value = 'NO';
            if(confSelect) confSelect.value = 'NO';
        }
    }

    function toggleSihce(val) {
        toggleSihceAndDocs(val);
    }

    function toggleDniElectronico(val) {
        const bloque = document.getElementById('bloque_dnie');
        if (val === 'ELECTRONICO') {
            bloque.classList.remove('hidden');
        } else {
            bloque.classList.add('hidden');
        }
    }

    function toggleEntidadCapacitadora(val) {
        const wrapper = document.getElementById('wrapper_entidad_capacitadora');
        if (val === 'SI') {
            wrapper.classList.remove('hidden');
        } else {
            wrapper.classList.add('hidden');
        }
    }

    function checkNationality(tipoDoc) {
        const sectionDni = document.getElementById('section_dni_detalle');
        if (tipoDoc === 'DNI') {
            sectionDni.classList.remove('hidden');
        } else {
            sectionDni.classList.add('hidden');
            const dniSelect = document.getElementById('tipo_dni');
            if (dniSelect) {
                dniSelect.value = "";
                toggleDniElectronico(""); 
            }
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

        const selectDni = document.getElementById('tipo_dni');
        if (selectDni) toggleDniElectronico(selectDni.value);

        const selectCargo = document.getElementById('cargo_profesional');
        if (selectCargo) toggleCargoManual(selectCargo.value);

        const docTypeSelect = document.getElementById('tipo_rrhh');
        if (docTypeSelect) {
            checkNationality(docTypeSelect.value);
            docTypeSelect.addEventListener('change', function() {
                checkNationality(this.value);
            });
        }
        
        if (typeof lucide !== 'undefined') lucide.createIcons();
    });

    document.getElementById('form-monitoreo-final').onsubmit = function() {
        const btn = document.getElementById('btn-submit-action');
        const text = document.getElementById('btn-text');
        btn.disabled = true;
        btn.classList.add('opacity-75', 'cursor-wait');
        text.innerText = "GUARDANDO...";
        if (typeof lucide !== 'undefined') lucide.createIcons();
        return true;
    };
</script>
@endsection