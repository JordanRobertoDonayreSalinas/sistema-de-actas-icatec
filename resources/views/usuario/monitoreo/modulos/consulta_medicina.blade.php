@extends('layouts.usuario')

@section('title', 'Módulo 04: Consulta Externa - Medicina')

@section('content')
<div class="py-12 bg-slate-50 min-h-screen">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        
        {{-- ENCABEZADO --}}
        <div class="mb-8 flex flex-col md:flex-row items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-3 mb-1">
                    <span class="px-3 py-1 bg-indigo-600 text-white text-[10px] font-black rounded-lg uppercase tracking-widest">Módulo Técnico</span>
                    <span class="text-slate-400 font-bold text-[10px] uppercase">ID Acta: #{{ str_pad($acta->id, 5, '0', STR_PAD_LEFT) }}</span>
                </div>
                <h2 class="text-3xl font-black text-slate-900 uppercase tracking-tight">04. Consulta Externa: Medicina</h2>
                <p class="text-slate-500 font-bold uppercase text-xs mt-1">
                    <i data-lucide="hospital" class="inline-block w-4 h-4 mr-1 text-indigo-500"></i> {{ $acta->establecimiento->nombre }}
                </p>
            </div>
            <a href="{{ route('usuario.monitoreo.modulos', $acta->id) }}" class="flex items-center gap-2 px-6 py-3 bg-white border-2 border-slate-200 rounded-2xl text-slate-600 font-black text-xs hover:bg-slate-50 transition-all uppercase shadow-sm">
                <i data-lucide="arrow-left" class="w-4 h-4"></i> Volver al Panel
            </a>
        </div>

        {{-- FORMULARIO --}}
        <form action="{{ route('usuario.monitoreo.consulta-medicina.store', $acta->id) }}" 
              method="POST" 
              enctype="multipart/form-data" 
              class="space-y-8" 
              id="form-consulta-medicina">
            @csrf

            {{-- SECCIÓN 1: DETALLES DEL CONSULTORIO --}}
            <div class="bg-white rounded-[3rem] p-10 shadow-xl shadow-slate-200/50 border border-slate-100 mb-8">
                <div class="flex items-center gap-4 mb-8">
                    <div class="h-12 w-12 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center font-black text-xl shadow-inner">1</div>
                    <h3 class="text-lg font-black text-slate-800 uppercase tracking-tight">Detalles del Consultorio</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    {{-- Campo: Cantidad de Consultorios --}}
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Nro. de Consultorios</label>
                        <input type="number" 
                               name="contenido[num_consultorios]"
                               min="0"
                                onkeydown="return event.keyCode !== 69 && event.keyCode !== 189"
                                oninput="this.value = Math.abs(this.value)" 
                               value="{{ $detalle->contenido['num_consultorios'] ?? '' }}"
                               class="w-full px-6 py-4 bg-slate-50 border-2 border-slate-100 rounded-2xl font-black text-slate-600 outline-none focus:border-indigo-500 transition-all text-center"
                               placeholder="EJ: 1">
                    </div>

                    {{-- Campo: Denominación --}}
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Denominación del Consultorio</label>
                        <input type="text" 
                               name="contenido[denominacion_consultorio]" 
                               value="{{ $detalle->contenido['denominacion_consultorio'] ?? '' }}"
                               class="w-full px-6 py-4 bg-slate-50 border-2 border-slate-100 rounded-2xl font-bold text-slate-600 outline-none focus:border-indigo-500 transition-all uppercase"
                               placeholder="EJ: CONSULTORIO MEDICINA 01">
                    </div>

                    {{-- TURNO (MAÑANA / TARDE) --}}
                    <div class="md:col-span-2">
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Turno</label>
                        <div class="flex gap-4">
                            {{-- OPCIÓN MAÑANA --}}
                            <label class="flex-1 relative cursor-pointer group">
                                <input type="radio" name="contenido[turno]" value="MAÑANA" 
                                    {{ ($detalle->contenido['turno'] ?? '') == 'MAÑANA' ? 'checked' : '' }} 
                                    class="peer sr-only">
                                <div class="p-4 rounded-2xl border-2 border-slate-100 bg-slate-50 text-center transition-all peer-checked:border-amber-400 peer-checked:bg-amber-50 peer-checked:text-amber-700 peer-checked:shadow-sm hover:border-amber-200">
                                    <div class="flex items-center justify-center gap-2">
                                        <i data-lucide="sun" class="w-4 h-4"></i>
                                        <span class="text-xs font-black uppercase tracking-wider">MAÑANA</span>
                                    </div>
                                </div>
                            </label>
                            
                            {{-- OPCIÓN TARDE --}}
                            <label class="flex-1 relative cursor-pointer group">
                                <input type="radio" name="contenido[turno]" value="TARDE" 
                                    {{ ($detalle->contenido['turno'] ?? '') == 'TARDE' ? 'checked' : '' }} 
                                    class="peer sr-only">
                                <div class="p-4 rounded-2xl border-2 border-slate-100 bg-slate-50 text-center transition-all peer-checked:border-indigo-500 peer-checked:bg-indigo-50 peer-checked:text-indigo-700 peer-checked:shadow-sm hover:border-indigo-200">
                                    <div class="flex items-center justify-center gap-2">
                                        <i data-lucide="sunset" class="w-4 h-4"></i>
                                        <span class="text-xs font-black uppercase tracking-wider">TARDE</span>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            {{-- SECCIÓN 2: PROFESIONAL --}}
            <div class="bg-white rounded-[3rem] p-10 shadow-xl shadow-slate-200/50 border border-slate-100">
                <div class="flex items-center gap-4 mb-8">
                    <div class="h-12 w-12 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center font-black text-xl shadow-inner">2</div>
                    <h3 class="text-lg font-black text-slate-800 uppercase tracking-tight">Datos del Profesional</h3>
                </div>
                <x-busqueda-profesional prefix="profesional" :detalle="$detalle" />
                {{-- NUEVA SECCIÓN: DOCUMENTACIÓN ADMINISTRATIVA --}}
                <div class="bg-white rounded-[3rem] p-10 shadow-xl shadow-slate-200/50 border border-slate-100 mb-8">
                    <div class="flex items-center gap-4 mb-8">
                        {{-- Icono representativo --}}
                        <div class="h-10 w-10 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center font-black text-xl shadow-inner">
                            <i data-lucide="file-signature" class="w-6 h-6"></i>
                        </div>
                        <h3 class="text-lg font-black text-slate-600 uppercase tracking-tight">Documentación Administrativa</h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        
                        {{-- PREGUNTA 1: DECLARACIÓN JURADA --}}
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">¿Firmó Declaración Jurada?</label>
                            <div class="flex gap-4">
                                {{-- OPCIÓN SÍ --}}
                                <label class="flex-1 relative cursor-pointer group">
                                    <input type="radio" name="contenido[firmo_dj]" value="SI" 
                                        {{ ($detalle->contenido['firmo_dj'] ?? '') == 'SI' ? 'checked' : '' }} 
                                        class="peer sr-only">
                                    <div class="p-4 rounded-2xl border-2 border-slate-100 bg-slate-50 text-center transition-all peer-checked:border-emerald-500 peer-checked:bg-emerald-50 peer-checked:text-emerald-700 peer-checked:shadow-sm hover:border-emerald-200">
                                        <span class="text-xs font-black uppercase tracking-wider">SÍ</span>
                                    </div>
                                </label>
                                
                                {{-- OPCIÓN NO --}}
                                <label class="flex-1 relative cursor-pointer group">
                                    <input type="radio" name="contenido[firmo_dj]" value="NO" 
                                        {{ ($detalle->contenido['firmo_dj'] ?? '') == 'NO' ? 'checked' : '' }} 
                                        class="peer sr-only">
                                    <div class="p-4 rounded-2xl border-2 border-slate-100 bg-slate-50 text-center transition-all peer-checked:border-red-500 peer-checked:bg-red-50 peer-checked:text-red-700 peer-checked:shadow-sm hover:border-red-200">
                                        <span class="text-xs font-black uppercase tracking-wider">NO</span>
                                    </div>
                                </label>
                            </div>
                        </div>

                        {{-- PREGUNTA 2: COMPROMISO DE CONFIDENCIALIDAD --}}
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">¿Firmó Compromiso de Confidencialidad?</label>
                            <div class="flex gap-4">
                                {{-- OPCIÓN SÍ --}}
                                <label class="flex-1 relative cursor-pointer group">
                                    <input type="radio" name="contenido[firmo_confidencialidad]" value="SI" 
                                        {{ ($detalle->contenido['firmo_confidencialidad'] ?? '') == 'SI' ? 'checked' : '' }} 
                                        class="peer sr-only">
                                    <div class="p-4 rounded-2xl border-2 border-slate-100 bg-slate-50 text-center transition-all peer-checked:border-emerald-500 peer-checked:bg-emerald-50 peer-checked:text-emerald-700 peer-checked:shadow-sm hover:border-emerald-200">
                                        <span class="text-xs font-black uppercase tracking-wider">SÍ</span>
                                    </div>
                                </label>
                                
                                {{-- OPCIÓN NO --}}
                                <label class="flex-1 relative cursor-pointer group">
                                    <input type="radio" name="contenido[firmo_confidencialidad]" value="NO" 
                                        {{ ($detalle->contenido['firmo_confidencialidad'] ?? '') == 'NO' ? 'checked' : '' }} 
                                        class="peer sr-only">
                                    <div class="p-4 rounded-2xl border-2 border-slate-100 bg-slate-50 text-center transition-all peer-checked:border-red-500 peer-checked:bg-red-50 peer-checked:text-red-700 peer-checked:shadow-sm hover:border-red-200">
                                        <span class="text-xs font-black uppercase tracking-wider">NO</span>
                                    </div>
                                </label>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            {{-- SECCIÓN 3: DATOS DEL DNI Y FIRMA DIGITAL --}}
            <div class="bg-white rounded-[3rem] p-10 shadow-xl shadow-slate-200/50 border border-slate-100 mb-8">
                <div class="flex items-center gap-4 mb-8">
                    <div class="h-12 w-12 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center font-black text-xl shadow-inner">3</div>
                    <h3 class="text-lg font-black text-slate-800 uppercase tracking-tight">Tipo de DNI y Firma Digital</h3>
                </div>
                
                {{-- SELECCIÓN DEL TIPO DE DOCUMENTO --}}
                <div class="mb-6">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Seleccione el Tipo de Documento Físico</label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- OPCIÓN A: DNI ELECTRÓNICO --}}
                        <label class="relative cursor-pointer group">
                            <input type="radio" 
                                   name="contenido[tipo_dni_fisico]" 
                                   value="ELECTRONICO" 
                                   class="peer sr-only"
                                   onchange="toggleDniFields()"
                                   {{ ($detalle->contenido['tipo_dni_fisico'] ?? '') == 'ELECTRONICO' ? 'checked' : '' }}>
                            <div class="p-5 rounded-2xl border-2 border-slate-100 bg-slate-50 peer-checked:border-indigo-500 peer-checked:bg-indigo-50/50 transition-all hover:border-indigo-200">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-white flex items-center justify-center text-indigo-600 shadow-sm">
                                        <i data-lucide="credit-card" class="w-5 h-5"></i>
                                    </div>
                                    <div>
                                        <span class="block text-xs font-black text-slate-700 uppercase">DNI Electrónico</span>
                                        <span class="text-[9px] text-slate-400 font-bold uppercase">Con Chip</span>
                                    </div>
                                    <div class="ml-auto opacity-0 peer-checked:opacity-100 text-indigo-600">
                                        <i data-lucide="check-circle-2" class="w-6 h-6 fill-indigo-100"></i>
                                    </div>
                                </div>
                            </div>
                        </label>

                        {{-- OPCIÓN B: DNI AZUL / AMARILLO --}}
                        <label class="relative cursor-pointer group">
                            <input type="radio" 
                                   name="contenido[tipo_dni_fisico]" 
                                   value="AZUL" 
                                   class="peer sr-only"
                                   onchange="toggleDniFields()"
                                   {{ ($detalle->contenido['tipo_dni_fisico'] ?? '') == 'AZUL' ? 'checked' : '' }}>
                            <div class="p-5 rounded-2xl border-2 border-slate-100 bg-slate-50 peer-checked:border-blue-500 peer-checked:bg-blue-50/50 transition-all hover:border-blue-200">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-white flex items-center justify-center text-blue-500 shadow-sm">
                                        <i data-lucide="user-square" class="w-5 h-5"></i>
                                    </div>
                                    <div>
                                        <span class="block text-xs font-black text-slate-700 uppercase">DNI Azul</span>
                                        <span class="text-[9px] text-slate-400 font-bold uppercase">Sin Chip</span>
                                    </div>
                                    <div class="ml-auto opacity-0 peer-checked:opacity-100 text-blue-500">
                                        <i data-lucide="check-circle-2" class="w-6 h-6 fill-blue-100"></i>
                                    </div>
                                </div>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- BLOQUE CONDICIONAL (SOLO SI ES DNIe) --}}
                <div id="block-info-dnie" class="hidden animate-fade-in-down mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-6 bg-indigo-50/40 rounded-3xl border border-indigo-100">
                        {{-- Versión del DNIe --}}
                        <div>
                            <label class="block text-[10px] font-black text-indigo-400 uppercase tracking-widest mb-3">Versión del DNIe</label>
                            <select name="contenido[dnie_version]" class="w-full px-5 py-3 bg-white border-2 border-indigo-100 rounded-xl font-bold text-xs outline-none focus:border-indigo-500 text-slate-600 uppercase transition-all">
                                <option value="">-- SELECCIONE --</option>
                                <option value="1.0" {{ ($detalle->contenido['dnie_version'] ?? '') == '1.0' ? 'selected' : '' }}>VERSIÓN 1.0</option>
                                <option value="2.0" {{ ($detalle->contenido['dnie_version'] ?? '') == '2.0' ? 'selected' : '' }}>VERSIÓN 2.0</option>
                                <option value="3.0" {{ ($detalle->contenido['dnie_version'] ?? '') == '3.0' ? 'selected' : '' }}>VERSIÓN 3.0</option>
                            </select>
                        </div>

                        {{-- Realiza Firma SIHCE --}}
                        <div>
                            <label class="block text-[10px] font-black text-indigo-400 uppercase tracking-widest mb-3">¿Firma Digitalmente en SIHCE?</label>
                            <div class="flex gap-4 mt-1">
                                <label class="flex items-center gap-2 cursor-pointer bg-white px-4 py-2 rounded-xl border border-indigo-100 shadow-sm hover:border-indigo-400 transition-colors">
                                    <input type="radio" name="contenido[dnie_firma_sihce]" value="SI" {{ ($detalle->contenido['dnie_firma_sihce'] ?? '') == 'SI' ? 'checked' : '' }} class="w-4 h-4 accent-indigo-600">
                                    <span class="text-xs font-bold text-slate-700">SÍ</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer bg-white px-4 py-2 rounded-xl border border-indigo-100 shadow-sm hover:border-red-400 transition-colors">
                                    <input type="radio" name="contenido[dnie_firma_sihce]" value="NO" {{ ($detalle->contenido['dnie_firma_sihce'] ?? '') == 'NO' ? 'checked' : '' }} class="w-4 h-4 accent-red-500">
                                    <span class="text-xs font-bold text-slate-700">NO</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                {{--BLOQUE FIJO (SIEMPRE VISIBLE) --}}
                <div class="p-6 bg-slate-50 rounded-3xl border border-slate-100">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Observaciones / Motivo de uso</label>
                    <textarea name="contenido[dni_observacion]" 
                              rows="2"
                              class="w-full px-5 py-4 bg-white border-2 border-slate-200 rounded-2xl font-bold text-xs outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-50/50 text-slate-600 uppercase placeholder:text-slate-300 transition-all"
                              placeholder="INGRESE AQUÍ CUALQUIER OBSERVACIÓN ...">{{ $detalle->contenido['dni_observacion'] ?? '' }}</textarea>
                </div>
            </div>

            {{-- SECCIÓN 4: DETALLES DE CAPACITACIÓN --}}
            <div class="bg-white rounded-[3rem] p-10 shadow-xl shadow-slate-200/50 border border-slate-100">
                <div class="flex items-center gap-4 mb-8">
                    <div class="h-12 w-12 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center font-black text-xl shadow-inner">4</div>
                    <h3 class="text-lg font-black text-slate-800 uppercase tracking-tight">Detalles de Capacitación</h3>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-10 mb-10">
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">¿Recibió capacitación?</label>
                        <div class="flex gap-8">
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="radio" name="contenido[recibio_capacitacion]" value="SI" {{ ($detalle->contenido['recibio_capacitacion'] ?? '') == 'SI' ? 'checked' : '' }} class="w-5 h-5" onchange="toggleInstCapacitacion(this.value)">
                                <span class="text-sm font-bold">SÍ</span>
                            </label>
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="radio" name="contenido[recibio_capacitacion]" value="NO" {{ ($detalle->contenido['recibio_capacitacion'] ?? '') == 'NO' ? 'checked' : '' }} class="w-5 h-5" onchange="toggleInstCapacitacion(this.value)">
                                <span class="text-sm font-bold">NO</span>
                            </label>
                        </div>
                    </div>
                    <div id="section_inst_capacitacion" class="{{ ($detalle->contenido['recibio_capacitacion'] ?? '') === 'NO' ? 'hidden' : '' }}">
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">¿De parte de quién?</label>
                        <select name="contenido[inst_capacitacion]" class="w-full px-6 py-4 bg-slate-50 border-2 border-slate-100 rounded-2xl font-bold text-sm outline-none focus:border-indigo-500 transition-all">
                            @foreach(['MINSA','DIRESA','UNIDAD EJECUTORA'] as $op)
                                <option value="{{$op}}" {{ ($detalle->contenido['inst_capacitacion'] ?? '') == $op ? 'selected' : '' }}>{{$op}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- SECCIÓN 5: MATERIALES --}}
            <div class="bg-white rounded-[3rem] p-10 shadow-xl shadow-slate-200/50 border border-slate-100">
                <div class="flex items-center gap-4 mb-8">
                    <div class="h-12 w-12 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center font-black text-xl shadow-inner">5</div>
                    <h3 class="text-lg font-black text-slate-800 uppercase tracking-tight">Materiales</h3>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @foreach(['Historia Clínica' => 'historia_clinica', 'FUA' => 'fua', 'Receta' => 'receta', 'Orden de Laboratorio' => 'orden_laboratorio', 'Hoja de Referencia' => 'hoja_referencia', 'Otros' => 'otros'] as $label => $key)
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" name="contenido[materiales][{{$key}}]" value="1" {{ (isset($detalle->contenido['materiales'][$key]) && $detalle->contenido['materiales'][$key]) ? 'checked' : '' }} class="w-5 h-5 rounded border-slate-300">
                            <span class="text-sm font-bold text-slate-700">{{ $label }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- SECCIÓN 6: EQUIPAMIENTO DEL ÁREA --}}
            <div class="bg-white rounded-[3rem] p-10 shadow-xl shadow-slate-200/50 border border-slate-100">
                <div class="flex items-center gap-4 mb-8">
                    <div class="h-12 w-12 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center font-black text-xl shadow-inner">6</div>
                    <h3 class="text-lg font-black text-slate-800 uppercase tracking-tight">Equipamiento del Área</h3>
                </div>
                <x-tabla-equipos :equipos="$equipos" modulo="consulta_medicina" />
            </div>

            {{-- SECCIÓN 7: SOPORTE TÉCNICO --}}
            <div class="bg-white rounded-[3rem] p-10 shadow-xl shadow-slate-200/50 border border-slate-100">
                <div class="flex items-center gap-4 mb-8">
                    <div class="h-12 w-12 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center font-black text-xl shadow-inner">7</div>
                    <h3 class="text-lg font-black text-slate-800 uppercase tracking-tight">Soporte Técnico</h3>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">¿A quién le comunica?</label>
                        <select name="contenido[comunica_a]" class="w-full px-6 py-4 bg-slate-50 border-2 border-slate-100 rounded-2xl font-bold text-sm outline-none">
                            @foreach(['MINSA','DIRESA','JEFE DE ESTABLECIMIENTO','OTRO'] as $op)
                                <option value="{{$op}}" {{ ($detalle->contenido['comunica_a'] ?? '') == $op ? 'selected' : '' }}>{{$op}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">¿Qué medio utiliza?</label>
                        <div class="flex gap-8 mt-3">
                            @foreach(['WHATSAPP' => 'whatsapp', 'TELEFONO' => 'telefono', 'EMAIL' => 'email'] as $label => $key)
                                <label class="flex items-center gap-3 cursor-pointer">
                                    <input type="radio" name="contenido[medio_soporte]" value="{{$label}}" {{ ($detalle->contenido['medio_soporte'] ?? '') == $label ? 'checked' : '' }} class="w-5 h-5">
                                    <span class="text-sm font-bold">{{$label}}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- SECCIÓN 8: COMENTARIOS Y FOTOS --}}
            <div class="bg-slate-900 rounded-[3.5rem] p-12 shadow-2xl text-white relative overflow-hidden">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 relative z-10">
                    <div>
                        <h3 class="text-sm font-black uppercase tracking-[0.3em] text-indigo-400 mb-6 flex items-center gap-2">
                            <i data-lucide="message-square" class="w-5 h-5"></i> Comentarios
                        </h3>
                        <textarea name="contenido[comentarios]" rows="5" class="w-full bg-white/5 border-2 border-white/10 rounded-3xl p-6 text-white font-bold outline-none focus:border-indigo-500 transition-all uppercase placeholder-white/20 shadow-inner" placeholder="OBSERVACIONES...">{{ $detalle->contenido['comentarios'] ?? '' }}</textarea>
                    </div>
                    
                    <div>
                        @php
                            $fotosActuales = [];
                            if (isset($detalle->contenido['foto_evidencia'])) {
                                $val = $detalle->contenido['foto_evidencia'];
                                $fotosActuales = is_array($val) ? $val : [$val];
                            }
                        @endphp
                        <h3 class="text-sm font-black uppercase tracking-[0.3em] text-red-400 mb-6 flex items-center gap-2">
                            <i data-lucide="camera" class="w-5 h-5"></i> Evidencia Fotográfica
                        </h3>
                        <div class="relative group">
                            <input type="file" 
                            name="foto_evidencia[]" 
                            id="foto_evidencia" 
                            accept="image/*" multiple 
                            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-20" 
                            onchange="simplePreview(event)">

                            <div id="dropzone" class="bg-white/5 border-2 border-dashed border-white/20 rounded-[2.5rem] p-10 flex flex-col items-center justify-center group-hover:bg-white/10 transition-all duration-500 shadow-inner">
                                {{-- Icono y Texto --}}
                                <div id="placeholder-content" class="flex flex-col items-center">
                                    <i data-lucide="upload-cloud" class="w-10 h-10 text-indigo-400 mb-4"></i>
                                    <span class="text-[10px] font-black uppercase tracking-widest text-slate-300 text-center">
                                        Click para seleccionar (Reemplaza las anteriores)
                                    </span>
                                </div>

                                {{-- Contenedor PREVIEW (Nuevas) --}}
                                <div id="new-previews" class="hidden mt-6 grid grid-cols-3 gap-3 w-full"></div>
                            </div>
                        </div>
                        
                        {{-- BLOQUE DE FOTOS GUARDADAS (Se ocultará si subes nuevas) --}}
                        @if(count($fotosActuales) > 0)
                            <div id="saved-images-block" class="mt-6">
                                <div class="flex items-center gap-2 mb-3 opacity-70">
                                    <i data-lucide="check-circle" class="w-4 h-4 text-emerald-400"></i>
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-400">Imágenes actuales en sistema:</span>
                                </div>
                                <div class="grid grid-cols-4 gap-2">
                                    @foreach($fotosActuales as $foto)
                                        <a href="{{ asset('storage/'.$foto) }}" target="_blank" class="block aspect-square rounded-lg overflow-hidden border border-white/10 hover:opacity-80 transition">
                                            <img src="{{ asset('storage/'.$foto) }}" class="w-full h-full object-cover">
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- BOTÓN DE GUARDADO FINAL --}}
            <div class="pt-10 pb-20">
                <button type="submit" id="btn-submit-action" 
                        class="w-full group bg-indigo-600 text-white p-10 rounded-[3rem] font-black shadow-2xl shadow-indigo-200 flex items-center justify-between hover:bg-indigo-700 transition-all duration-500 active:scale-[0.98] cursor-pointer">
                    <div class="flex items-center gap-8 pointer-events-none">
                        <div class="h-16 w-16 bg-white/20 rounded-3xl flex items-center justify-center group-hover:rotate-12 transition-all shadow-lg border border-white/30">
                            <i data-lucide="save" id="icon-save-loader" class="w-8 h-8 text-white"></i>
                        </div>
                        <div class="text-left">
                            <p class="text-xl uppercase tracking-[0.3em] leading-none">Confirmar Registro</p>
                            <p class="text-[10px] text-indigo-200 font-bold uppercase mt-3 tracking-widest">Sincronizar Módulo 04 con el Maestro</p>
                        </div>
                    </div>
                    <div class="h-14 w-14 bg-white/10 rounded-full flex items-center justify-center group-hover:bg-white group-hover:text-indigo-600 transition-all duration-500">
                        <i data-lucide="chevron-right" class="w-7 h-7"></i>
                    </div>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Función mejorada para ocultar y LIMPIAR campos
    function toggleDniFields(isInitialLoad = false) {
        // Obtenemos el valor seleccionado (puede ser "DNI ELECTRONICO" o "DNI AZUL")
        const tipoDni = document.querySelector('input[name="contenido[tipo_dni_fisico]"]:checked')?.value;
        
        // Referencias a los bloques
        const blockDnie = document.getElementById('block-info-dnie');
        
        // Referencias a los inputs para limpiarlos
        const selectVersion = document.querySelector('select[name="contenido[dnie_version]"]');
        const radiosFirma = document.querySelectorAll('input[name="contenido[dnie_firma_sihce]"]');
        const txtObservacion = document.querySelector('textarea[name="contenido[dni_observacion]"]');

        if (tipoDni === 'ELECTRONICO') {
            // Mostrar campos de DNIe
            blockDnie.classList.remove('hidden');
            
            // Limpiar observación de DNI Azul (si no es carga inicial)
            if (!isInitialLoad && txtObservacion) {
                txtObservacion.value = '';
            }

        } else if (tipoDni === 'AZUL') {
            // Ocultar campos de DNIe
            blockDnie.classList.add('hidden');
            
            // Limpiar datos de DNIe (si no es carga inicial)
            if (!isInitialLoad) {
                if (selectVersion) selectVersion.value = ''; // Reset select
                radiosFirma.forEach(r => r.checked = false); // Reset radios
            }
        } else {
            // Ninguno seleccionado
            blockDnie.classList.add('hidden');
        }
    }

    // Ejecutar al cargar: pasamos 'true' para que NO borre los datos que vienen de la BD
    document.addEventListener('DOMContentLoaded', () => toggleDniFields(true));

    function toggleInstCapacitacion(value) {
        const section = document.getElementById('section_inst_capacitacion');
        value === 'NO' ? section.classList.add('hidden') : section.classList.remove('hidden');
    }
    
    function simplePreview(event) {
        const input = event.target;
        const previewContainer = document.getElementById('new-previews');
        const placeholder = document.getElementById('placeholder-content');
        const savedBlock = document.getElementById('saved-images-block');
        
        // Limpiamos previsualizaciones anteriores para evitar duplicados visuales
        previewContainer.innerHTML = '';
        
        // VALIDACIÓN: Verificamos los archivos ANTES de procesarlos
        if (input.files && input.files.length > 0) {
            
            // Recorremos los archivos para buscar intrusos (PDFs, Excel, etc.)
            for (let i = 0; i < input.files.length; i++) {
                const file = input.files[i];
                
                // Si el archivo NO es una imagen...
                if (!file.type.startsWith('image/')) {
                    // 1. Mostrar Alerta
                    alert(`⚠️ ERROR DE FORMATO:\n\nEl archivo "${file.name}" NO es una imagen.\nSolo se permiten archivos JPG, PNG o JPEG.`);
                    
                    // 2. Limpiar el input (Resetea la selección para que no se suba nada incorrecto)
                    input.value = ""; 
                    
                    // 3. Restaurar la vista al estado inicial
                    previewContainer.classList.add('hidden');
                    placeholder.classList.remove('hidden');
                    if(savedBlock) savedBlock.style.display = 'block'; // Volver a mostrar las fotos guardadas
                    
                    return; // ¡IMPORTANTE! Detiene la función aquí.
                }
            }

            // --- Si llegamos aquí, todos los archivos son imágenes válidas ---

            // 1. Ocultar placeholder y mostrar contenedor de nuevas
            previewContainer.classList.remove('hidden');
            placeholder.classList.add('hidden');
            
            // 2. Ocultar fotos antiguas visualmente (Lógica de reemplazo)
            if(savedBlock) savedBlock.style.display = 'none';

            // 3. Generar miniaturas (Máximo 5)
            Array.from(input.files).slice(0, 5).forEach(file => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = 'w-full h-24 object-cover rounded-xl border border-indigo-500 shadow-sm animate-fade-in';
                    previewContainer.appendChild(img);
                }
                reader.readAsDataURL(file);
            });

        } else {
            // Si el usuario cancela la selección (input vacío)
            previewContainer.classList.add('hidden');
            placeholder.classList.remove('hidden');
            if(savedBlock) savedBlock.style.display = 'block';
        }
    }
    document.getElementById('form-consulta-medicina').onsubmit = function() {
        const btn = document.getElementById('btn-submit-action');
        const icon = document.getElementById('icon-save-loader');
        
        btn.disabled = true;
        btn.classList.add('opacity-50', 'cursor-not-allowed');
        
        icon.innerHTML = '<i data-lucide="loader-2" class="w-8 h-8 text-white animate-spin"></i>';
        if (typeof lucide !== 'undefined') lucide.createIcons();
        
        return true;
    };
</script>
@endsection
