@extends('layouts.usuario')

@section('title', 'Módulo: Puerperio')

@section('content')
<div class="py-12 bg-slate-50 min-h-screen">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        
        {{-- ENCABEZADO SUPERIOR --}}
        <div class="mb-8 flex flex-col md:flex-row items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-3 mb-1">
                    <span class="px-3 py-1 bg-rose-600 text-white text-[10px] font-black rounded-lg uppercase tracking-widest">Módulo Materno</span>
                    <span class="text-slate-400 font-bold text-[10px] uppercase">ID Acta: #{{ str_pad($acta->id, 5, '0', STR_PAD_LEFT) }}</span>
                </div>
                <h2 class="text-3xl font-black text-slate-900 uppercase tracking-tight">13. Puerperio</h2>
                <p class="text-slate-500 font-bold uppercase text-xs mt-1">
                    <i data-lucide="baby" class="inline-block w-4 h-4 mr-1 text-rose-500"></i> {{ $acta->establecimiento->nombre }}
                </p>
            </div>
            <a href="{{ route('usuario.monitoreo.modulos', $acta->id) }}" class="flex items-center gap-2 px-6 py-3 bg-white border-2 border-slate-200 rounded-2xl text-slate-600 font-black text-xs hover:bg-slate-50 transition-all uppercase shadow-sm">
                <i data-lucide="arrow-left" class="w-4 h-4"></i> Volver al Panel
            </a>
        </div>

        <form action="{{ route('usuario.monitoreo.puerperio.store', $acta->id) }}" 
              method="POST" 
              enctype="multipart/form-data" 
              class="space-y-6" 
              id="form-monitoreo-puerperio">
            @csrf
            
            {{-- 1.- DETALLES DEL AMBIENTE --}}
            <div class="bg-white rounded-[2rem] p-8 shadow-lg border border-slate-100">
                <div class="flex items-center gap-3 mb-6 border-b border-slate-100 pb-4">
                    <span class="bg-rose-600 text-white w-8 h-8 flex items-center justify-center rounded-full font-black text-sm">1</span>
                    <h3 class="text-rose-900 font-black text-lg uppercase tracking-tight">DETALLES DEL CONSULTORIO</h3>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
                    {{-- FECHA --}}
                    <div>
                        <label class="block text-slate-500 text-[10px] font-black uppercase tracking-widest mb-2">Fecha de Monitoreo</label>
                        <input type="date" name="contenido[fecha]" value="{{ $detalle->contenido['fecha'] ?? date('Y-m-d') }}" class="w-full bg-slate-50 border-2 border-slate-200 rounded-xl px-4 py-3 text-slate-800 font-bold outline-none focus:border-rose-500 transition-all">
                    </div>
                    
                    {{-- TURNO --}}
                    <div>
                    <x-turno :selected="$detalle->contenido['turno'] ?? ''" />
                    </div>

                    {{-- NRO DE AMBIENTES --}}
                    <div>
                        <label class="block text-slate-500 text-[10px] font-black uppercase tracking-widest mb-2">Nro. de Consultorios</label>
                        <input type="number" 
                               name="contenido[num_ambientes]" 
                               value="{{ $detalle->contenido['num_ambientes'] ?? '' }}" 
                               min="0"
                               class="w-full bg-slate-50 border-2 border-slate-200 rounded-xl px-4 py-3 text-slate-800 font-bold outline-none focus:border-rose-500 transition-all text-center"
                               placeholder="EJ: 1">
                    </div>

                    {{-- DENOMINACIÓN --}}
                    <div>
                        <label class="block text-slate-500 text-[10px] font-black uppercase tracking-widest mb-2">Consultorio entrevistado</label>
                        <input type="text" 
                               name="contenido[denominacion_ambiente]" 
                               value="{{ $detalle->contenido['denominacion_ambiente'] ?? '' }}" 
                               class="w-full bg-slate-50 border-2 border-slate-200 rounded-xl px-4 py-3 text-slate-800 font-bold outline-none focus:border-rose-500 transition-all uppercase"
                               placeholder="EJ: OBSTETRICIA 01">
                    </div>
                </div>
            </div>

            {{-- 2.- DATOS DEL PROFESIONAL --}}
            <div class="bg-white rounded-[2rem] p-8 shadow-lg border border-slate-100">
                <div class="flex items-center gap-3 mb-6 border-b border-slate-100 pb-4">
                    <span class="bg-rose-600 text-white w-8 h-8 flex items-center justify-center rounded-full font-black text-sm">2</span>
                    <h3 class="text-rose-900 font-black text-lg uppercase tracking-tight">DATOS DEL PROFESIONAL</h3>
                </div>

                {{-- BUSQUEDA DE PROFESIONAL (COMPONENTE) --}}
                <div class="mb-6">
                    <x-busqueda-profesional prefix="rrhh" :detalle="$detalle" />
                </div>

                {{-- SIHCE / DDJJ / CONFIDENCIALIDAD --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-4 border-t border-slate-100">
                    {{-- 1. Utiliza SIHCE --}}
                    <div>
                        <label class="block text-rose-600 text-[10px] font-black uppercase tracking-widest mb-2">¿Utiliza SIHCE?</label>
                        <select name="contenido[cuenta_sihce]" id="cuenta_sihce" onchange="toggleSihceAndDocs(this.value)" class="w-full px-4 py-3 bg-rose-50 border-2 border-rose-100 rounded-xl font-bold text-sm uppercase outline-none text-rose-700 cursor-pointer hover:bg-rose-100 transition-colors">
                            <option value="SI" {{ ($detalle->contenido['cuenta_sihce'] ?? '') == 'SI' ? 'selected' : '' }}>SI</option>
                            <option value="NO" {{ ($detalle->contenido['cuenta_sihce'] ?? '') == 'NO' ? 'selected' : '' }}>NO</option>
                        </select>
                    </div>

                    {{-- 2. Declaración Jurada --}}
                    <div id="div_firmo_dj">
                        <label class="block text-slate-500 text-[10px] font-black uppercase tracking-widest mb-2">¿Firmó Declaración Jurada?</label>
                        <select name="contenido[firmo_dj]" id="firmo_dj" class="w-full px-4 py-3 bg-slate-50 border-2 border-slate-200 rounded-xl font-bold text-sm uppercase outline-none focus:border-rose-500">
                            <option value="SI" {{ ($detalle->contenido['firmo_dj'] ?? '') == 'SI' ? 'selected' : '' }}>SI</option>
                            <option value="NO" {{ ($detalle->contenido['firmo_dj'] ?? '') == 'NO' ? 'selected' : '' }}>NO</option>
                        </select>
                    </div>

                    {{-- 3. Confidencialidad --}}
                    <div id="div_firmo_confidencialidad">
                        <label class="block text-slate-500 text-[10px] font-black uppercase tracking-widest mb-2">¿Firmó Confidencialidad?</label>
                        <select name="contenido[firmo_confidencialidad]" id="firmo_confidencialidad" class="w-full px-4 py-3 bg-slate-50 border-2 border-slate-200 rounded-xl font-bold text-sm uppercase outline-none focus:border-rose-500">
                            <option value="SI" {{ ($detalle->contenido['firmo_confidencialidad'] ?? '') == 'SI' ? 'selected' : '' }}>SI</option>
                            <option value="NO" {{ ($detalle->contenido['firmo_confidencialidad'] ?? '') == 'NO' ? 'selected' : '' }}>NO</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- 3.- TIPO DE DNI Y FIRMA DIGITAL --}}
            {{-- MODIFICACIÓN: Agregado ID para ocultar la sección completa --}}
            <div id="seccion_detalle_dni" class="bg-white rounded-[2rem] p-8 shadow-lg border border-slate-100 transition-all duration-300">
                <div class="flex items-center gap-3 mb-6 border-b border-slate-100 pb-4">
                    <span class="bg-rose-600 text-white w-8 h-8 flex items-center justify-center rounded-full font-black text-sm">3</span>
                    <h3 class="text-rose-900 font-black text-lg uppercase tracking-tight">DETALLE DE DNI Y FIRMA DIGITAL</h3>
                </div>
                
                <div class="mb-8">
                    <label class="block text-slate-400 text-[10px] font-black uppercase tracking-widest mb-4">SELECCIONE EL TIPO DE DOCUMENTO FÍSICO</label>
                    <input type="hidden" name="contenido[tipo_dni]" id="tipo_dni_input" value="{{ $detalle->contenido['tipo_dni'] ?? '' }}">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- DNI ELECTRÓNICO --}}
                        <div id="card_electronico" onclick="selectDniType('ELECTRONICO')" 
                             class="cursor-pointer border-2 rounded-2xl p-6 flex items-center gap-4 transition-all hover:shadow-md {{ ($detalle->contenido['tipo_dni'] ?? '') == 'ELECTRONICO' ? 'border-rose-600 bg-rose-50' : 'border-slate-200 bg-white' }}">
                            <div class="h-12 w-12 rounded-xl bg-rose-100 flex items-center justify-center text-rose-600">
                                <i data-lucide="credit-card" class="w-6 h-6"></i>
                            </div>
                            <div>
                                <h4 class="text-sm font-black text-slate-800 uppercase">DNI ELECTRÓNICO</h4>
                                <span class="text-[10px] font-bold text-rose-500 bg-rose-100 px-2 py-0.5 rounded uppercase">Con Chip</span>
                            </div>
                        </div>

                        {{-- DNI AZUL --}}
                        <div id="card_azul" onclick="selectDniType('AZUL')" 
                             class="cursor-pointer border-2 rounded-2xl p-6 flex items-center gap-4 transition-all hover:shadow-md {{ ($detalle->contenido['tipo_dni'] ?? '') == 'AZUL' ? 'border-rose-600 bg-rose-50' : 'border-slate-200 bg-white' }}">
                            <div class="h-12 w-12 rounded-xl bg-slate-100 flex items-center justify-center text-slate-500">
                                <i data-lucide="user-square" class="w-6 h-6"></i>
                            </div>
                            <div>
                                <h4 class="text-sm font-black text-slate-800 uppercase">DNI AZUL</h4>
                                <span class="text-[10px] font-bold text-slate-400 bg-slate-100 px-2 py-0.5 rounded uppercase">Sin Chip</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- BLOQUE INFERIOR DNI --}}
                <div id="bloque_opciones_dni" class="bg-slate-50 rounded-2xl p-6 border border-slate-200 {{ empty($detalle->contenido['tipo_dni']) ? 'hidden' : '' }}">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        {{-- Versión DNIe --}}
                        <div id="bloque_version_dnie" class="{{ ($detalle->contenido['tipo_dni'] ?? '') == 'ELECTRONICO' ? '' : 'hidden' }}">
                            <label class="block text-rose-600 text-[10px] font-black uppercase tracking-widest mb-2">Versión del DNIe</label>
                            <select name="contenido[version_dnie]" class="w-full px-4 py-3 bg-white border-2 border-rose-100 rounded-xl font-bold text-sm uppercase outline-none focus:border-rose-500 text-rose-700">
                                <option value="" selected disabled>-- SELECCIONE --</option>
                                <option value="1.0" {{ ($detalle->contenido['version_dnie'] ?? '') == '1.0' ? 'selected' : '' }}>VERSIÓN 1.0</option>
                                <option value="2.0" {{ ($detalle->contenido['version_dnie'] ?? '') == '2.0' ? 'selected' : '' }}>VERSIÓN 2.0</option>
                                <option value="3.0" {{ ($detalle->contenido['version_dnie'] ?? '') == '3.0' ? 'selected' : '' }}>VERSIÓN 3.0</option>
                            </select>
                        </div>

                        {{-- Firma Digital --}}
                        <div id="bloque_firma_digital" class="{{ ($detalle->contenido['tipo_dni'] ?? '') == 'ELECTRONICO' ? '' : 'hidden' }}">
                            <label class="block text-rose-600 text-[10px] font-black uppercase tracking-widest mb-3">¿Firma Digitalmente en SIHCE?</label>
                            <div class="flex items-center gap-6">
                                <label class="flex items-center gap-2 cursor-pointer group">
                                    <div class="relative flex items-center">
                                        <input type="radio" name="contenido[firma_digital_sihce]" value="SI" class="peer h-5 w-5 cursor-pointer appearance-none rounded-full border-2 border-slate-300 checked:border-rose-600 transition-all" {{ ($detalle->contenido['firma_digital_sihce'] ?? '') == 'SI' ? 'checked' : '' }}>
                                        <span class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-2.5 h-2.5 bg-rose-600 rounded-full opacity-0 peer-checked:opacity-100 transition-opacity"></span>
                                    </div>
                                    <span class="text-sm font-bold text-slate-600 group-hover:text-rose-600 transition-colors">SÍ</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer group">
                                    <div class="relative flex items-center">
                                        <input type="radio" name="contenido[firma_digital_sihce]" value="NO" class="peer h-5 w-5 cursor-pointer appearance-none rounded-full border-2 border-slate-300 checked:border-red-500 transition-all" {{ ($detalle->contenido['firma_digital_sihce'] ?? '') == 'NO' ? 'checked' : '' }}>
                                        <span class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-2.5 h-2.5 bg-red-500 rounded-full opacity-0 peer-checked:opacity-100 transition-opacity"></span>
                                    </div>
                                    <span class="text-sm font-bold text-slate-600 group-hover:text-red-500 transition-colors">NO</span>
                                </label>
                            </div>
                        </div>

                        {{-- Observaciones --}}
                        <div class="md:col-span-2 mt-2">
                            <label class="block text-slate-400 text-[10px] font-black uppercase tracking-widest mb-2">Observaciones</label>
                            <textarea name="contenido[observaciones_dni]" rows="2" class="w-full px-4 py-3 bg-white border-2 border-slate-200 rounded-xl font-bold text-sm uppercase outline-none focus:border-rose-500 placeholder-slate-300" placeholder="Escriba aquí si presenta dificultades...">{{ $detalle->contenido['observaciones_dni'] ?? '' }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 4.- DETALLES DE CAPACITACIÓN --}}
            <div class="bg-white rounded-[2rem] p-8 shadow-lg border border-slate-100">
                <div class="flex items-center gap-3 mb-6 border-b border-slate-100 pb-4">
                    <span class="bg-rose-600 text-white w-8 h-8 flex items-center justify-center rounded-full font-black text-sm">4</span>
                    <h3 class="text-rose-900 font-black text-lg uppercase tracking-tight">DETALLES DE CAPACITACIÓN</h3>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- USUARIO Y ACCESO --}}
                    <div>
                        <label class="block text-slate-500 text-[10px] font-black uppercase tracking-widest mb-2">¿Cuenta con Usuario y Acceso?</label>
                        <select name="contenido[acceso_sistema]" class="w-full px-4 py-3 bg-slate-50 border-2 border-slate-200 rounded-xl font-bold text-sm outline-none focus:border-rose-500 transition-all uppercase cursor-pointer">
                            <option value="SI" {{ ($detalle->contenido['acceso_sistema'] ?? '') == 'SI' ? 'selected' : '' }}>SI</option>
                            <option value="NO" {{ ($detalle->contenido['acceso_sistema'] ?? '') == 'NO' ? 'selected' : '' }}>NO</option>
                        </select>
                    </div>

                    {{-- CAPACITACIÓN --}}
                    <div>
                        <label class="block text-slate-500 text-[10px] font-black uppercase tracking-widest mb-2">¿Recibió Capacitación?</label>
                        <select name="contenido[recibio_capacitacion]" id="recibio_capacitacion" onchange="toggleEntidadCapacitadora(this.value)" class="w-full px-4 py-3 bg-slate-50 border-2 border-slate-200 rounded-xl font-bold text-sm outline-none focus:border-rose-500 transition-all uppercase cursor-pointer">
                            <option value="SI" {{ ($detalle->contenido['recibio_capacitacion'] ?? '') == 'SI' ? 'selected' : '' }}>SI</option>
                            <option value="NO" {{ ($detalle->contenido['recibio_capacitacion'] ?? '') == 'NO' ? 'selected' : '' }}>NO</option>
                        </select>
                    </div>
                    
                    {{-- ENTIDAD CAPACITADORA --}}
                    <div id="wrapper_entidad_capacitadora" class="hidden md:col-span-2">
                        <label class="block text-slate-500 text-[10px] font-black uppercase tracking-widest mb-2">¿De parte de quién?</label>
                        <select name="contenido[inst_que_lo_capacito]" class="w-full px-4 py-3 bg-slate-50 border-2 border-slate-200 rounded-xl font-bold text-sm outline-none focus:border-rose-500 transition-all uppercase cursor-pointer">
                            <option value="UNIDAD EJECUTORA" {{ ($detalle->contenido['inst_que_lo_capacito'] ?? '') == 'UNIDAD EJECUTORA' ? 'selected' : '' }}>UNIDAD EJECUTORA</option>
                            <option value="DIRESA" {{ ($detalle->contenido['inst_que_lo_capacito'] ?? '') == 'DIRESA' ? 'selected' : '' }}>DIRESA</option>
                            <option value="MINSA" {{ ($detalle->contenido['inst_que_lo_capacito'] ?? '') == 'MINSA' ? 'selected' : '' }}>MINSA</option>
                            <option value="OTROS" {{ ($detalle->contenido['inst_que_lo_capacito'] ?? '') == 'OTROS' ? 'selected' : '' }}>OTROS</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- 5.- EQUIPAMIENTO --}}
            <div class="bg-white rounded-[2rem] p-8 shadow-lg border border-slate-100">
                <div class="flex items-center gap-3 mb-6 border-b border-slate-100 pb-4">
                    <span class="bg-rose-600 text-white w-8 h-8 flex items-center justify-center rounded-full font-black text-sm">5</span>
                    <h3 class="text-rose-900 font-black text-lg uppercase tracking-tight">EQUIPAMIENTO DEL CONSULTORIO</h3>
                </div>
                <x-tabla-equipos :equipos="$equipos" modulo="puerperio" />
            </div>

            {{-- 6.- SOPORTE Y COMUNICACIÓN --}}
            <div class="bg-white rounded-[2rem] p-8 shadow-lg border border-slate-100">
                <div class="flex items-center gap-3 mb-6 border-b border-slate-100 pb-4">
                    <span class="bg-rose-600 text-white w-8 h-8 flex items-center justify-center rounded-full font-black text-sm">6</span>
                    <h3 class="text-rose-900 font-black text-lg uppercase tracking-tight">SOPORTE</h3>
                </div>
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
            <div class="bg-slate-900 rounded-[3rem] p-10 shadow-2xl text-white">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
                    <div>
                        <div class="flex items-center gap-3 mb-6">
                            <span class="bg-rose-500 text-white w-8 h-8 flex items-center justify-center rounded-full font-black text-sm">7</span>
                            <h3 class="text-white font-black text-lg uppercase tracking-tight">COMENTARIOS</h3>
                        </div>
                        <textarea name="contenido[comentarios]" rows="6" class="w-full bg-white/10 border-2 border-white/20 rounded-2xl p-4 text-white font-bold outline-none focus:border-rose-500 transition-all uppercase placeholder-white/30">{{ $detalle->contenido['comentarios'] ?? '' }}</textarea>
                    </div>
                    <div>
                        <div class="flex items-center gap-3 mb-6">
                            <span class="bg-rose-500 text-white w-8 h-8 flex items-center justify-center rounded-full font-black text-sm">8</span>
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
                                <i data-lucide="upload-cloud" id="upload-icon" class="w-8 h-8 text-rose-400 mb-2"></i>
                                <span id="file-name-display" class="text-[10px] font-bold uppercase tracking-widest text-slate-300 text-center">{{ isset($detalle->contenido['foto_evidencia']) ? 'CLICK PARA CAMBIAR' : 'SUBIR FOTO' }}</span>
                                <img id="img-preview" src="#" class="hidden mt-2 w-20 h-20 object-cover rounded-lg border-2 border-rose-500">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- BOTÓN GRANDE DE GUARDADO --}}
            <div class="pt-10 pb-5 mt-6">
                <button type="submit" id="btn-submit-action" 
                        class="w-full group bg-rose-600 text-white p-8 rounded-[3rem] font-black shadow-2xl shadow-rose-200 flex items-center justify-between hover:bg-rose-700 transition-all duration-500 active:scale-[0.98] cursor-pointer">
                    <div class="flex items-center gap-8 pointer-events-none">
                        <div class="h-16 w-16 bg-white/20 rounded-3xl flex items-center justify-center group-hover:rotate-12 transition-all shadow-lg border border-white/30">
                            <i data-lucide="save" id="icon-save-loader" class="w-8 h-8 text-white"></i>
                        </div>
                        <div class="text-left">
                            <p class="text-xl uppercase tracking-[0.3em] leading-none">Confirmar Registro</p>
                            <p class="text-[10px] text-rose-200 font-bold uppercase mt-3 tracking-widest">Sincronizar Módulo Puerperio</p>
                        </div>
                    </div>
                    <div class="h-14 w-14 bg-white/10 rounded-full flex items-center justify-center group-hover:bg-white group-hover:text-rose-600 transition-all duration-500">
                        <i data-lucide="chevron-right" class="w-7 h-7"></i>
                    </div>
                </button>
            </div>

        </form>
    </div>
</div>

<script>
    // --- LÓGICA DE NEGOCIO ---

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

    // --- NUEVA LÓGICA: OCULTAR SECCIÓN DNI SI NO ES DNI ---
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
            cardElectronico.classList.add('border-rose-600', 'bg-rose-50');
            cardElectronico.classList.remove('border-slate-200', 'bg-white');
            cardAzul.classList.remove('border-rose-600', 'bg-rose-50');
            cardAzul.classList.add('border-slate-200', 'bg-white');
            bloqueVersion.classList.remove('hidden');
            bloqueFirma.classList.remove('hidden');
        } else {
            cardAzul.classList.add('border-rose-600', 'bg-rose-50');
            cardAzul.classList.remove('border-slate-200', 'bg-white');
            cardElectronico.classList.remove('border-rose-600', 'bg-rose-50');
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
        
        // --- DETECTAR CAMBIO EN TIPO DOCUMENTO ---
        // Nombre generado por el componente x-busqueda-profesional
        const selectTipoDoc = document.querySelector('select[name="contenido[rrhh][tipo_doc]"]');
        if (selectTipoDoc) {
            // Ejecutar al inicio
            toggleSeccionDni(selectTipoDoc.value);
            // Escuchar cambios
            selectTipoDoc.addEventListener('change', function() {
                toggleSeccionDni(this.value);
            });
        }
        
        if (typeof lucide !== 'undefined') lucide.createIcons();
    });

    document.getElementById('form-monitoreo-puerperio').onsubmit = function() {
        const btn = document.getElementById('btn-submit-action');
        const icon = document.getElementById('icon-save-loader');
        
        btn.disabled = true;
        btn.classList.add('opacity-50', 'cursor-not-allowed');
        
        icon.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="animate-spin"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>';
        
        return true;
    };
</script>
@endsection