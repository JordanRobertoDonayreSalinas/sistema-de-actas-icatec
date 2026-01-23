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
            <div class="bg-white rounded-[2rem] p-8 shadow-lg border border-slate-100">
                <div class="flex items-center gap-3 mb-6 border-b border-slate-100 pb-4">
                    <span class="bg-teal-600 text-white w-8 h-8 flex items-center justify-center rounded-full font-black text-sm">1</span>
                    <h3 class="text-teal-900 font-black text-lg uppercase tracking-tight">DETALLES DEL ÁREA</h3>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
                    {{-- FECHA --}}
                    <div>
                        <label class="block text-slate-500 text-[10px] font-black uppercase tracking-widest mb-2">Fecha de Monitoreo</label>
                        <input type="date" name="contenido[fecha]" value="{{ $monitoreo->contenido['fecha'] ?? date('Y-m-d') }}" class="w-full bg-slate-50 border-2 border-slate-200 rounded-xl px-4 py-3 text-slate-800 font-bold outline-none focus:border-teal-500 transition-all">
                    </div>
                    
                    {{-- TURNO --}}
                    <div>
                        <label class="block text-slate-500 text-[10px] font-black uppercase tracking-widest mb-2">Turno</label>
                        <select name="contenido[turno]" class="w-full bg-slate-50 border-2 border-slate-200 rounded-xl px-4 py-3 text-slate-800 font-bold outline-none focus:border-teal-500 transition-all uppercase">
                            <option value="MAÑANA" {{ ($detalle->contenido['turno'] ?? '') == 'MAÑANA' ? 'selected' : '' }}>MAÑANA</option>
                            <option value="TARDE" {{ ($detalle->contenido['turno'] ?? '') == 'TARDE' ? 'selected' : '' }}>TARDE</option>
                        </select>
                    </div>

                    {{-- NRO DE AMBIENTES --}}
                    <div>
                        <label class="block text-slate-500 text-[10px] font-black uppercase tracking-widest mb-2">Nro. de Ambientes</label>
                        <input type="number" 
                               name="contenido[num_ambientes]" 
                               value="{{ $detalle->contenido['num_ambientes'] ?? '' }}" 
                               min="0"
                               class="w-full bg-slate-50 border-2 border-slate-200 rounded-xl px-4 py-3 text-slate-800 font-bold outline-none focus:border-teal-500 transition-all text-center"
                               placeholder="EJ: 1">
                    </div>

                    {{-- DENOMINACIÓN --}}
                    <div>
                        <label class="block text-slate-500 text-[10px] font-black uppercase tracking-widest mb-2">Denominación del Ambiente</label>
                        <input type="text" 
                               name="contenido[denominacion_ambiente]" 
                               value="{{ $detalle->contenido['denominacion_ambiente'] ?? '' }}" 
                               class="w-full bg-slate-50 border-2 border-slate-200 rounded-xl px-4 py-3 text-slate-800 font-bold outline-none focus:border-teal-500 transition-all uppercase"
                               placeholder="EJ: TRIAJE PRINCIPAL">
                    </div>
                </div>
                <h2 class="text-3xl font-black text-slate-900 uppercase tracking-tight">06. Farmacia</h2>
                <p class="text-slate-500 font-bold uppercase text-xs mt-1">
                    <i data-lucide="clipboard-pulse" class="inline-block w-4 h-4 mr-1 text-teal-500"></i> {{ $monitoreo->establecimiento->nombre }}
                </p>
            </div>
<<<<<<< HEAD

=======
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

>>>>>>> main
            {{-- DATOS DEL PROFESIONAL --}}
            <div class="bg-white rounded-[2rem] p-8 shadow-lg border border-slate-100">
                <div class="flex items-center gap-3 mb-6 border-b border-slate-100 pb-4">
                    <span class="bg-teal-600 text-white w-8 h-8 flex items-center justify-center rounded-full font-black text-sm">2</span>
<<<<<<< HEAD
                    <h3 class="text-teal-900 font-black text-lg uppercase tracking-tight">DATOS DEL PROFESIONAL (Triaje)</h3>
                </div>

                {{-- BUSQUEDA DE PROFESIONAL (Componente reutilizable) --}}
                <div class="mb-6">
                    <x-busqueda-profesional prefix="profesional" :detalle="$detalle" color="teal" />
                </div>

=======
                    <h3 class="text-teal-900 font-black text-lg uppercase tracking-tight">DATOS DEL PROFESIONAL</h3>
                </div>

                {{-- BUSQUEDA DE PROFESIONAL (Componente reutilizable) --}}
                <div class="mb-6">
                    <x-esp_2_datosProfesional prefix="profesional" :detalle="$detalle" color="teal" />
                </div>

>>>>>>> main
                {{-- SIHCE / DDJJ / CONFIDENCIALIDAD --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-4 border-t border-slate-100">
                    <div>
                        <label class="block text-teal-600 text-[10px] font-black uppercase tracking-widest mb-2">¿Utiliza SIHCE?</label>
                        <select name="contenido[cuenta_sihce]" id="cuenta_sihce" onchange="toggleSihceAndDocs(this.value)" class="w-full px-4 py-3 bg-teal-50 border-2 border-teal-100 rounded-xl font-bold text-sm uppercase outline-none text-teal-700 cursor-pointer hover:bg-teal-100 transition-colors">
                            <option value="SI" {{ ($detalle->contenido['cuenta_sihce'] ?? '') == 'SI' ? 'selected' : '' }}>SI</option>
                            <option value="NO" {{ ($detalle->contenido['cuenta_sihce'] ?? '') == 'NO' ? 'selected' : '' }}>NO</option>
                        </select>
<<<<<<< HEAD
                    </div>
                    <div id="div_firmo_dj">
                        <label class="block text-slate-500 text-[10px] font-black uppercase tracking-widest mb-2">¿Firmó Declaración Jurada?</label>
                        <select name="contenido[firmo_dj]" id="firmo_dj" class="w-full px-4 py-3 bg-slate-50 border-2 border-slate-200 rounded-xl font-bold text-sm uppercase outline-none focus:border-teal-500">
                            <option value="SI" {{ ($detalle->contenido['firmo_dj'] ?? '') == 'SI' ? 'selected' : '' }}>SI</option>
                            <option value="NO" {{ ($detalle->contenido['firmo_dj'] ?? '') == 'NO' ? 'selected' : '' }}>NO</option>
                        </select>
                    </div>
=======
                    </div>
                    <div id="div_firmo_dj">
                        <label class="block text-slate-500 text-[10px] font-black uppercase tracking-widest mb-2">¿Firmó Declaración Jurada?</label>
                        <select name="contenido[firmo_dj]" id="firmo_dj" class="w-full px-4 py-3 bg-slate-50 border-2 border-slate-200 rounded-xl font-bold text-sm uppercase outline-none focus:border-teal-500">
                            <option value="SI" {{ ($detalle->contenido['firmo_dj'] ?? '') == 'SI' ? 'selected' : '' }}>SI</option>
                            <option value="NO" {{ ($detalle->contenido['firmo_dj'] ?? '') == 'NO' ? 'selected' : '' }}>NO</option>
                        </select>
                    </div>
>>>>>>> main
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
<<<<<<< HEAD
            <div class="bg-white rounded-[2rem] p-8 shadow-lg border border-slate-100">
                <div class="flex items-center gap-3 mb-6 border-b border-slate-100 pb-4">
                    <span class="bg-teal-600 text-white w-8 h-8 flex items-center justify-center rounded-full font-black text-sm">4</span>
                    <h3 class="text-teal-900 font-black text-lg uppercase tracking-tight">DETALLES DE CAPACITACIÓN</h3>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-slate-500 text-[10px] font-black uppercase tracking-widest mb-2">¿Cuenta con Usuario y Acceso?</label>
                        <select name="contenido[acceso_sistema]" class="w-full px-4 py-3 bg-slate-50 border-2 border-slate-200 rounded-xl font-bold text-sm outline-none focus:border-teal-500 transition-all uppercase cursor-pointer">
                            <option value="SI" {{ ($detalle->contenido['acceso_sistema'] ?? '') == 'SI' ? 'selected' : '' }}>SI</option>
                            <option value="NO" {{ ($detalle->contenido['acceso_sistema'] ?? '') == 'NO' ? 'selected' : '' }}>NO</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-slate-500 text-[10px] font-black uppercase tracking-widest mb-2">¿Recibió Capacitación?</label>
                        <select name="contenido[recibio_capacitacion]" id="recibio_capacitacion" onchange="toggleEntidadCapacitadora(this.value)" class="w-full px-4 py-3 bg-slate-50 border-2 border-slate-200 rounded-xl font-bold text-sm outline-none focus:border-teal-500 transition-all uppercase cursor-pointer">
                            <option value="SI" {{ ($detalle->contenido['recibio_capacitacion'] ?? '') == 'SI' ? 'selected' : '' }}>SI</option>
                            <option value="NO" {{ ($detalle->contenido['recibio_capacitacion'] ?? '') == 'NO' ? 'selected' : '' }}>NO</option>
                        </select>
                    </div>
                    <div id="wrapper_entidad_capacitadora" class="hidden md:col-span-2">
                        <label class="block text-slate-500 text-[10px] font-black uppercase tracking-widest mb-2">¿De parte de quién?</label>
                        <select name="contenido[inst_que_lo_capacito]" class="w-full px-4 py-3 bg-slate-50 border-2 border-slate-200 rounded-xl font-bold text-sm outline-none focus:border-teal-500 transition-all uppercase cursor-pointer">
                            <option value="UNIDAD EJECUTORA" {{ ($detalle->contenido['inst_que_lo_capacito'] ?? '') == 'UNIDAD EJECUTORA' ? 'selected' : '' }}>UNIDAD EJECUTORA</option>
                            <option value="DIRESA" {{ ($detalle->contenido['inst_que_lo_capacito'] ?? '') == 'DIRESA' ? 'selected' : '' }}>DIRESA</option>
                            <option value="MINSA" {{ ($detalle->contenido['inst_que_lo_capacito'] ?? '') == 'MINSA' ? 'selected' : '' }}>MINSA</option>
                            <option value="OTROS" {{ ($detalle->contenido['inst_que_lo_capacito'] ?? '') == 'OTROS' ? 'selected' : '' }}>OTROS</option>
                        </select>
                    </div>
                </div>
            </div>

=======
            <x-esp_4_detalleCap :model="json_encode($detalle->contenido ?? [])" />
            
>>>>>>> main
            {{-- EQUIPAMIENTO --}}
            <div class="bg-white rounded-[2rem] p-8 shadow-lg border border-slate-100">
                <div class="flex items-center gap-3 mb-6 border-b border-slate-100 pb-4">
                    <span class="bg-teal-600 text-white w-8 h-8 flex items-center justify-center rounded-full font-black text-sm">5</span>
<<<<<<< HEAD
                    <h3 class="text-teal-900 font-black text-lg uppercase tracking-tight">EQUIPAMIENTO DE TRIAJE</h3>
                </div>
                {{-- COMPONENTE TABLA EQUIPOS --}}
                <x-tabla-equipos :equipos="$equipos" modulo="farmacia_esp" />
            </div>

            {{-- 6.- SOPORTE Y COMUNICACIÓN --}}
=======
                    <h3 class="text-teal-900 font-black text-lg uppercase tracking-tight">EQUIPAMIENTO</h3>
                </div>
                <x-esp_5_equipos :equipos="$equipos" modulo="farmacia_esp" />
            </div>

            {{-- SOPORTE--}}
>>>>>>> main
            <div class="bg-white rounded-[2rem] p-8 shadow-lg border border-slate-100">
                <div class="flex items-center gap-3 mb-6 border-b border-slate-100 pb-4">
                    <span class="bg-teal-600 text-white w-8 h-8 flex items-center justify-center rounded-full font-black text-sm">6</span>
                    <h3 class="text-teal-900 font-black text-lg uppercase tracking-tight">SOPORTE</h3>
                </div>
<<<<<<< HEAD
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-slate-500 text-[10px] font-black uppercase tracking-widest mb-2">Comunica dificultades a:</label>
                        <select name="contenido[inst_a_quien_comunica]" class="w-full px-4 py-3 bg-slate-50 border-2 border-slate-200 rounded-xl font-bold text-sm outline-none uppercase cursor-pointer">
                            @foreach(['DIRESA','UNIDAD EJECUTORA','JEFE DE ESTABLECIMIENTO','MINSA','OTROS'] as $op)
                                <option value="{{$op}}" {{ ($detalle->contenido['inst_a_quien_comunica'] ?? '') == $op ? 'selected' : '' }}>{{$op}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-slate-500 text-[10px] font-black uppercase tracking-widest mb-2">Medio utilizado:</label>
                        <select name="contenido[medio_que_utiliza]" class="w-full px-4 py-3 bg-slate-50 border-2 border-slate-200 rounded-xl font-bold text-sm outline-none uppercase cursor-pointer">
                            @foreach(['CELULAR','EMAIL','WHATSAPP','OTROS'] as $me)
                                <option value="{{$me}}" {{ ($detalle->contenido['medio_que_utiliza'] ?? '') == $me ? 'selected' : '' }}>{{$me}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- 7.- COMENTARIOS y 8.- EVIDENCIA --}}
            <div class="bg-teal-900 rounded-[3rem] p-10 shadow-2xl text-white">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
                    <div>
                        <div class="flex items-center gap-3 mb-6">
                            <span class="bg-teal-500 text-white w-8 h-8 flex items-center justify-center rounded-full font-black text-sm">7</span>
                            <h3 class="text-white font-black text-lg uppercase tracking-tight">COMENTARIOS</h3>
                        </div>
                        <textarea name="contenido[comentarios]" rows="6" class="w-full bg-white/10 border-2 border-white/20 rounded-2xl p-4 text-white font-bold outline-none focus:border-teal-500 transition-all uppercase placeholder-white/30">{{ $detalle->contenido['comentarios'] ?? '' }}</textarea>
                    </div>
                    <div>
                        <div class="flex items-center gap-3 mb-6">
                            <span class="bg-teal-500 text-white w-8 h-8 flex items-center justify-center rounded-full font-black text-sm">8</span>
                            <h3 class="text-white font-black text-lg uppercase tracking-tight">EVIDENCIA FOTOGRÁFICA</h3>
                        </div>
                        @if(isset($monitoreo->contenido['foto_evidencia']))
                            <div class="mb-4 relative group w-full">
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Imagen Actual:</p>
                                <div class="rounded-2xl overflow-hidden border-2 border-white/20 shadow-lg h-32 w-32 bg-black/50">
                                    <img src="{{ asset('storage/' . $monitoreo->contenido['foto_evidencia']) }}" class="w-full h-full object-cover">
                                </div>
                            </div>
                        @endif
                        <div class="relative group">
                            <input type="file" name="foto_evidencia" id="foto_evidencia" accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-20" onchange="previewImage(event)">
                            <div id="dropzone" class="bg-white/5 border-2 border-dashed border-white/20 rounded-[2rem] p-8 flex flex-col items-center justify-center group-hover:bg-white/10 transition-all shadow-inner h-48 border-spacing-4">
                                <i data-lucide="upload-cloud" id="upload-icon" class="w-8 h-8 text-teal-400 mb-2"></i>
                                <span id="file-name-display" class="text-[10px] font-bold uppercase tracking-widest text-slate-300 text-center">{{ isset($detalle->contenido['foto_evidencia']) ? 'CLICK PARA CAMBIAR' : 'SUBIR FOTO' }}</span>
                                <img id="img-preview" src="#" class="hidden mt-2 w-20 h-20 object-cover rounded-lg border-2 border-teal-500">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
=======
                <x-esp_6_soporte :detalle="$detalle" />
            </div>

            {{-- COMENTARIOS Y EVIDENCIA FOTOGRAFICA --}}
            <x-esp_7_comentariosEvid :comentario="(object) ($detalle->contenido ?? [])" />
>>>>>>> main

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