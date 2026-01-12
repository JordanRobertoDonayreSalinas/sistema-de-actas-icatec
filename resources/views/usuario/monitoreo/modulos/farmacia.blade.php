@extends('layouts.usuario')

@section('title', 'Monitoreo Farmacia - ' . ($acta->establecimiento->nombre ?? 'Sin Establecimiento'))

@push('styles')
    <style>
        [x-cloak] { display: none !important; }
        .input-standard {
            width: 100%;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 1rem;
            padding: 0.75rem 1rem;
            font-size: 0.875rem;
            font-weight: 600;
            color: #1e293b;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .input-standard:focus {
            outline: none;
            border-color: #6366f1;
            background-color: #fff;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
            transform: translateY(-1px);
        }
        .spinner { 
            border: 2px solid #f3f3f3; 
            border-top: 2px solid #6366f1; 
            border-radius: 50%; 
            width: 16px; 
            height: 16px; 
            animation: spin 1s linear infinite; 
            display: inline-block; 
        }
        /* Estilo para los botones de eliminar imagen */
        .btn-remove-image {
            position: absolute;
            top: 0.5rem;
            right: 0.5rem;
            background-color: #ef4444;
            color: white;
            padding: 0.4rem;
            border-radius: 9999px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            transition: all 0.2s;
            z-index: 20;
        }
        .btn-remove-image:hover {
            background-color: #dc2626;
            transform: scale(1.1);
        }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    </style>
@endpush

@section('content')
<div class="py-10 bg-slate-50 min-h-screen" 
     x-data="{ 
        openModal: false, 
        docNuevo: '', 
        images: {
            img1: '{{ !empty($detalle->foto_1) ? asset('storage/'.$detalle->foto_1) : null }}',
            img2: '{{ !empty($detalle->foto_2) ? asset('storage/'.$detalle->foto_2) : null }}'
        },
        previewImage(event, key) {
            const file = event.target.files[0];
            if (file) {
                this.images[key] = URL.createObjectURL(file);
            }
        },
        removeImage(key, inputId, actualId) {
            this.images[key] = null;
            document.getElementById(inputId).value = '';
            document.getElementById(actualId).value = '';
        }
     }" 
     @abrir-modal-nuevo.window="openModal = true; docNuevo = $event.detail.doc">
    
    <div class="max-w-5xl mx-auto px-4">
        @if(session('success'))
            <div class="mb-6 bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-xl shadow-sm text-emerald-800 font-bold uppercase text-xs">
                {{ session('success') }}
            </div>
        @endif

        <a href="{{ route('usuario.monitoreo.modulos', $acta->id) }}" class="inline-flex items-center gap-2 text-slate-400 font-bold text-[11px] uppercase tracking-widest mb-6 hover:text-indigo-600 transition-all group">
            <i data-lucide="arrow-left" class="w-4 h-4 transition-transform group-hover:-translate-x-1"></i>
            Panel de Módulos
        </a>

        <div class="bg-white border border-slate-200 rounded-[3rem] shadow-xl overflow-hidden">
            <div class="bg-slate-900 p-10 text-white relative overflow-hidden">
                <div class="absolute top-0 right-0 p-10 opacity-10 rotate-12">
                    <i data-lucide="baby" class="w-48 h-48"></i>
                </div>
                <div class="relative z-10 flex flex-col md:flex-row md:items-end justify-between gap-6">
                    <div>
                        <span class="px-3 py-1 bg-indigo-500/20 border border-indigo-400/30 rounded-full text-indigo-300 text-[10px] font-black uppercase tracking-widest">
                            Módulo 15
                        </span>
                        <h3 class="text-3xl font-black uppercase italic tracking-tight mt-2">Farmacia</h3>
                    </div>
                    
                    <div class="flex items-center gap-4 bg-white/5 backdrop-blur-md px-4 py-2 rounded-2xl border border-white/10 shadow-lg">
                        <div class="p-2 bg-indigo-600 rounded-lg">
                            <i data-lucide="calendar" class="w-5 h-5 text-white"></i>
                        </div>
                        <div class="flex flex-col">
                            <label for="fecha_monitoreo" class="text-[9px] font-black text-indigo-300 uppercase tracking-widest leading-none mb-1">
                                Fecha del Monitoreo
                            </label>
                            <input type="date" 
                                name="fecha_monitoreo" 
                                id="fecha_monitoreo"
                                value="{{ old('fecha_monitoreo', $acta->fecha) }}"
                                class="bg-transparent text-white border-none p-0 focus:ring-0 font-bold text-lg cursor-pointer [color-scheme:dark]">
                        </div>
                    </div>
                </div>
            </div>

            <form action="{{ route('usuario.monitoreo.farmacia.store', $acta->id) }}" method="POST" enctype="multipart/form-data" class="p-8 md:p-12 space-y-12">
                @csrf
                {{-- Inputs ocultos de fotos con IDs para Alpine --}}
                <input type="hidden" name="foto_1_actual" id="foto_1_actual" value="{{ $detalle->foto_1 ?? '' }}">
                <input type="hidden" name="foto_2_actual" id="foto_2_actual" value="{{ $detalle->foto_2 ?? '' }}">

                {{-- 01. PERSONAL --}}
                <div class="space-y-8">
                    <div class="flex items-center gap-4">
                        <span class="h-12 w-12 rounded-2xl bg-indigo-600 text-white flex items-center justify-center text-lg font-black shadow-lg shadow-indigo-200">01</span>
                        <h4 class="text-sm font-black text-slate-800 uppercase tracking-wider">Responsable de Atención</h4>
                    </div>

                    <div class="bg-white p-8 rounded-[2.5rem] border border-slate-200 shadow-sm space-y-8">
                        {{-- Fila 1: Documento y Nombres --}}
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
                            <div class="md:col-span-3 space-y-2">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-2">Tipo de Doc.</label>
                                <select name="contenido[personal][tipo_doc]" id="tipo_doc" class="input-standard">
                                    <option value="DNI" {{ ($detalle->contenido['personal']['tipo_doc'] ?? '') == 'DNI' ? 'selected' : '' }}>DNI</option>
                                    <option value="C.E." {{ ($detalle->contenido['personal']['tipo_doc'] ?? '') == 'C.E.' ? 'selected' : '' }}>C.E.</option>
                                </select>
                            </div>
                            <div class="md:col-span-3 space-y-2">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-2 flex items-center justify-between">
                                    DNI / Carnet <span id="loading_profesional" class="hidden"><span class="spinner"></span></span>
                                </label>
                                <input type="text" name="contenido[personal][dni]" id="doc" maxlength="12" value="{{ $detalle->contenido['personal']['dni'] ?? '' }}" class="input-standard shadow-sm">
                            </div>
                            <div class="md:col-span-6 space-y-2">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-2">Nombres Completos</label>
                                <input type="text" name="contenido[personal][nombre]" id="nombres" value="{{ $detalle->contenido['personal']['nombre'] ?? '' }}" class="input-standard uppercase">
                            </div>
                        </div>

                        {{-- Fila 2: Apellidos --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-2">Apellido Paterno</label>
                                <input type="text" name="contenido[personal][apellido_paterno]" id="apellido_paterno" value="{{ $detalle->contenido['personal']['apellido_paterno'] ?? '' }}" class="input-standard uppercase">
                            </div>
                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-2">Apellido Materno</label>
                                <input type="text" name="contenido[personal][apellido_materno]" id="apellido_materno" value="{{ $detalle->contenido['personal']['apellido_materno'] ?? '' }}" class="input-standard uppercase">
                            </div>
                        </div>

                        {{-- Fila 3: Contacto y Turno --}}
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                            <div class="md:col-span-1 space-y-2">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-2">Celular</label>
                                <input type="text" name="contenido[personal][contacto]" id="telefono" value="{{ $detalle->contenido['personal']['contacto'] ?? '' }}" class="input-standard">
                            </div>
                            <div class="md:col-span-2 space-y-2">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-2">Correo</label>
                                <input type="email" name="contenido[personal][email]" id="email" value="{{ $detalle->contenido['personal']['email'] ?? '' }}" class="input-standard">
                            </div>
                            <div class="md:col-span-1 space-y-2">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-2 text-center block">Turno</label>
                                <input type="text" name="contenido[personal][turno]" value="{{ $detalle->contenido['personal']['turno'] ?? '' }}" class="input-standard text-center uppercase" placeholder="Mañana / Tarde">
                            </div>
                        </div>

                        {{-- SUB-SECCIÓN: USO DE SISTEMA Y CAPACITACIÓN EN UNA SOLA FILA --}}
                        <div class="mt-10 pt-8 border-t border-slate-100">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 items-start">
                                
                                {{-- 1. PREGUNTA: USO DE SIHCE --}}
                                <div class="space-y-4">
                                    <label class="text-[11px] font-black text-indigo-600 uppercase tracking-widest flex items-center gap-2">
                                        <i data-lucide="monitor" class="w-4 h-4"></i>
                                        ¿Utiliza el sistema SIHCE?
                                    </label>
                                    <div class="grid grid-cols-2 gap-3">
                                        <label class="relative cursor-pointer group">
                                            <input type="radio" name="contenido[personal][utiliza_sihce]" value="SI" 
                                                {{ ($detalle->contenido['personal']['utiliza_sihce'] ?? '') == 'SI' ? 'checked' : '' }} 
                                                class="peer sr-only">
                                            <div class="py-3 rounded-2xl border-2 border-slate-100 bg-slate-50/50 text-center transition-all peer-checked:border-indigo-600 peer-checked:bg-indigo-50 peer-checked:text-indigo-700 hover:border-slate-200">
                                                <span class="text-xs font-bold uppercase">SÍ</span>
                                            </div>
                                        </label>
                                        <label class="relative cursor-pointer group">
                                            <input type="radio" name="contenido[personal][utiliza_sihce]" value="NO" 
                                                {{ ($detalle->contenido['personal']['utiliza_sihce'] ?? '') == 'NO' ? 'checked' : '' }} 
                                                class="peer sr-only">
                                            <div class="py-3 rounded-2xl border-2 border-slate-100 bg-slate-50/50 text-center transition-all peer-checked:border-red-500 peer-checked:bg-red-50 peer-checked:text-red-700 hover:border-slate-200">
                                                <span class="text-xs font-bold uppercase">NO</span>
                                            </div>
                                        </label>
                                    </div>
                                </div>

                                {{-- 2. PREGUNTA: CAPACITACIÓN --}}
                                <div class="space-y-4">
                                    <label class="text-[11px] font-black text-indigo-600 uppercase tracking-widest flex items-center gap-2">
                                        <i data-lucide="graduation-cap" class="w-4 h-4"></i>
                                        ¿Recibió capacitación?
                                    </label>
                                    <div class="grid grid-cols-2 gap-3">
                                        <label class="relative cursor-pointer group">
                                            <input type="radio" name="contenido[capacitacion][recibio]" value="SI" 
                                                {{ ($detalle->contenido['capacitacion']['recibio'] ?? '') == 'SI' ? 'checked' : '' }} 
                                                class="peer sr-only radio-capacitacion">
                                            <div class="py-3 rounded-2xl border-2 border-slate-100 bg-slate-50/50 text-center transition-all peer-checked:border-indigo-600 peer-checked:bg-indigo-50 peer-checked:text-indigo-700 hover:border-slate-200">
                                                <span class="text-xs font-bold uppercase">SÍ</span>
                                            </div>
                                        </label>
                                        <label class="relative cursor-pointer group">
                                            <input type="radio" name="contenido[capacitacion][recibio]" value="NO" 
                                                {{ ($detalle->contenido['capacitacion']['recibio'] ?? '') == 'NO' ? 'checked' : '' }} 
                                                class="peer sr-only radio-capacitacion">
                                            <div class="py-3 rounded-2xl border-2 border-slate-100 bg-slate-50/50 text-center transition-all peer-checked:border-slate-400 peer-checked:bg-slate-100 peer-checked:text-slate-600 hover:border-slate-200">
                                                <span class="text-xs font-bold uppercase">NO</span>
                                            </div>
                                        </label>
                                    </div>
                                </div>

                                {{-- 3. ENTIDAD QUE CAPACITÓ (Condicional) --}}
                                <div id="seccion_capacitacion_entes" class="space-y-4 transition-all duration-300 {{ ($detalle->contenido['capacitacion']['recibio'] ?? '') != 'SI' ? 'hidden' : '' }}">
                                    <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-2">
                                        <i data-lucide="building-2" class="w-4 h-4 text-indigo-400"></i>
                                        Entidad que capacitó
                                    </label>
                                    <div class="flex flex-wrap gap-2">
                                        @php $entes_guardados = (array)($detalle->contenido['capacitacion']['ente'] ?? []); @endphp
                                        @foreach(['MINSA', 'DIRESA', 'UE'] as $ente)
                                        <label class="relative cursor-pointer group">
                                            <input type="checkbox" name="contenido[capacitacion][ente][]" value="{{ $ente }}" 
                                                {{ in_array($ente, $entes_guardados) ? 'checked' : '' }} 
                                                class="peer sr-only">
                                            <div class="px-4 py-2 rounded-xl border-2 border-slate-100 bg-white transition-all peer-checked:border-indigo-500 peer-checked:bg-indigo-600 peer-checked:text-white group-hover:border-indigo-200">
                                                <span class="text-[10px] font-black tracking-widest">{{ $ente }}</span>
                                            </div>
                                        </label>
                                        @endforeach
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

                {{-- DOCUMENTACIÓN ADMINISTRATIVA --}}
                <div class="mt-6 mb-6 border-t border-slate-100 pt-6">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="p-2 bg-indigo-50 rounded-lg text-indigo-600">
                            <i data-lucide="file-signature" class="w-5 h-5"></i>
                        </div>
                        <h3 class="text-lg font-bold text-slate-700 uppercase tracking-tight">Documentación Administrativa</h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Declaración Jurada --}}
                        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
                            <p class="text-[10px] font-black text-slate-400 uppercase mb-3">¿Firmó declaración jurada?</p>
                            <div class="flex gap-4">
                                <label class="flex items-center gap-2 cursor-pointer font-bold text-sm text-slate-600">
                                    <input type="radio" name="contenido[documentacion][firma_dj]" value="SI" 
                                        {{ ($detalle->contenido['documentacion']['firma_dj'] ?? '') == 'SI' ? 'checked' : '' }}> SÍ
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer font-bold text-sm text-slate-600">
                                    <input type="radio" name="contenido[documentacion][firma_dj]" value="NO" 
                                        {{ ($detalle->contenido['documentacion']['firma_dj'] ?? '') == 'NO' ? 'checked' : '' }}> NO
                                </label>
                            </div>
                        </div>

                        {{-- Compromiso de Confidencialidad --}}
                        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
                            <p class="text-[10px] font-black text-slate-400 uppercase mb-3">¿Firmó compromiso de confidencialidad?</p>
                            <div class="flex gap-4">
                                <label class="flex items-center gap-2 cursor-pointer font-bold text-sm text-slate-600">
                                    <input type="radio" name="contenido[documentacion][firma_confidencialidad]" value="SI" 
                                        {{ ($detalle->contenido['documentacion']['firma_confidencialidad'] ?? '') == 'SI' ? 'checked' : '' }}> SÍ
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer font-bold text-sm text-slate-600">
                                    <input type="radio" name="contenido[documentacion][firma_confidencialidad]" value="NO" 
                                        {{ ($detalle->contenido['documentacion']['firma_confidencialidad'] ?? '') == 'NO' ? 'checked' : '' }}> NO
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- SECCIÓN 02: TIPO DE DNI Y FIRMA DIGITAL --}}
                <div class="mt-6 border-t border-slate-100 pt-6" x-data="{ tipoDni: '{{ $detalle->contenido['dni_firma']['tipo_dni_fisico'] ?? ($registro->tipo_dni_fisico ?? 'AZUL') }}' }">
                    <div class="flex items-center gap-4 mb-6">
                        <span class="h-12 w-12 rounded-2xl bg-indigo-600 text-white flex items-center justify-center text-lg font-black shadow-lg shadow-indigo-200">02</span>
                        <h4 class="text-sm font-black text-slate-800 uppercase tracking-wider">Tipo de DNI y Firma Digital</h4>
                    </div>

                    <div class="bg-white p-8 rounded-[2.5rem] border border-slate-200 shadow-sm space-y-8">
                        
                        <div>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-2 mb-4 italic">Seleccione el tipo de documento físico</p>

                            {{-- Grid de Tarjetas de Selección --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                                {{-- TARJETA: DNI ELECTRÓNICO --}}
                                <label class="cursor-pointer relative group">
                                    <input type="radio" name="contenido[dni_firma][tipo_dni_fisico]" value="ELECTRONICO" class="peer sr-only"
                                        x-model="tipoDni" @change="toggleDniOptions('ELECTRONICO')"
                                        {{ ($detalle->contenido['dni_firma']['tipo_dni_fisico'] ?? '') == 'ELECTRONICO' ? 'checked' : '' }}>

                                    <div class="p-6 rounded-[2rem] border-2 transition-all duration-200 border-slate-100 bg-slate-50/50 hover:border-indigo-300 peer-checked:border-indigo-600 peer-checked:bg-indigo-50/30">
                                        <div class="flex items-center gap-4">
                                            <div class="bg-indigo-100 p-3 rounded-2xl text-indigo-600">
                                                <i data-lucide="credit-card" class="w-6 h-6"></i>
                                            </div>
                                            <div>
                                                <h4 class="font-black text-slate-800 text-sm uppercase">DNI ELECTRÓNICO</h4>
                                                <span class="text-[9px] font-black text-indigo-500 bg-indigo-100/50 px-2 py-0.5 rounded-full uppercase">Con Chip</span>
                                            </div>
                                            <div class="ml-auto hidden peer-checked:block text-indigo-600">
                                                <i data-lucide="check-circle-2" class="w-6 h-6 fill-indigo-600 text-white"></i>
                                            </div>
                                        </div>
                                    </div>
                                </label>

                                {{-- TARJETA: DNI AZUL --}}
                                <label class="cursor-pointer relative group">
                                    <input type="radio" name="contenido[dni_firma][tipo_dni_fisico]" value="AZUL" class="peer sr-only"
                                        x-model="tipoDni" @change="toggleDniOptions('AZUL')"
                                        {{ ($detalle->contenido['dni_firma']['tipo_dni_fisico'] ?? 'AZUL') == 'AZUL' ? 'checked' : '' }}>

                                    <div class="p-6 rounded-[2rem] border-2 transition-all duration-200 border-slate-100 bg-slate-50/50 hover:border-sky-300 peer-checked:border-sky-600 peer-checked:bg-sky-50/30">
                                        <div class="flex items-center gap-4">
                                            <div class="bg-sky-100 p-3 rounded-2xl text-sky-600">
                                                <i data-lucide="user-square" class="w-6 h-6"></i>
                                            </div>
                                            <div>
                                                <h4 class="font-black text-slate-800 text-sm uppercase">DNI AZUL</h4>
                                                <span class="text-[9px] font-black text-slate-400 bg-slate-100 px-2 py-0.5 rounded-full uppercase">Tradicional</span>
                                            </div>
                                            <div class="ml-auto hidden peer-checked:block text-sky-600">
                                                <i data-lucide="check-circle-2" class="w-6 h-6 fill-sky-600 text-white"></i>
                                            </div>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        {{-- CONTENEDOR CONDICIONAL --}}
                        <div id="dnie-options-container" 
                            x-show="tipoDni === 'ELECTRONICO'" 
                            x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 transform scale-95"
                            x-transition:enter-end="opacity-100 transform scale-100"
                            class="bg-indigo-50/50 p-8 rounded-[2.5rem] border border-indigo-100 space-y-8">
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                {{-- Versión del DNIe --}}
                                <div class="space-y-2">
                                    <label class="text-[10px] font-black text-indigo-700 uppercase tracking-widest ml-2">Versión del DNIe</label>
                                    <select name="contenido[dni_firma][dnie_version]" class="input-standard">
                                        <option value="">-- SELECCIONE --</option>
                                        <option value="1.0" {{ ($detalle->contenido['dni_firma']['dnie_version'] ?? '') == '1.0' ? 'selected' : '' }}>VERSIÓN 1.0</option>
                                        <option value="2.0" {{ ($detalle->contenido['dni_firma']['dnie_version'] ?? '') == '2.0' ? 'selected' : '' }}>VERSIÓN 2.0</option>
                                        <option value="3.0" {{ ($detalle->contenido['dni_firma']['dnie_version'] ?? '') == '3.0' ? 'selected' : '' }}>VERSIÓN 3.0</option>
                                    </select>
                                </div>

                                {{-- Firma Digital en SIHCE --}}
                                <div class="space-y-2">
                                    <label class="text-[10px] font-black text-indigo-700 uppercase tracking-widest ml-2 block">¿Firma digitalmente en SIHCE?</label>
                                    <div class="flex items-center gap-8 mt-4">
                                        <label class="flex items-center gap-2 cursor-pointer font-black text-xs text-slate-500 group">
                                            <input type="radio" name="contenido[dni_firma][firma_sihce]" value="SI" 
                                                class="w-5 h-5 text-indigo-600 border-slate-300 focus:ring-indigo-500"
                                                {{ ($detalle->contenido['dni_firma']['firma_sihce'] ?? '') == 'SI' ? 'checked' : '' }}> 
                                            <span class="group-hover:text-indigo-600 transition-colors">SÍ</span>
                                        </label>
                                        <label class="flex items-center gap-2 cursor-pointer font-black text-xs text-slate-500 group">
                                            <input type="radio" name="contenido[dni_firma][firma_sihce]" value="NO" 
                                                class="w-5 h-5 text-indigo-600 border-slate-300 focus:ring-indigo-500"
                                                {{ ($detalle->contenido['dni_firma']['firma_sihce'] ?? '') == 'NO' ? 'checked' : '' }}> 
                                            <span class="group-hover:text-red-500 transition-colors">NO</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- OBSERVACIONES --}}
                        <div class="space-y-3">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-2 flex items-center gap-2">
                                <i data-lucide="message-square" class="w-3 h-3 text-indigo-500"></i>
                                Observaciones o motivos de uso
                            </label>
                            <textarea name="contenido[dni_firma][observaciones]" rows="3" class="input-standard w-full resize-none" placeholder="Escriba aquí los detalles adicionales si el profesional no cuenta con el documento actualizado o presenta dificultades...">{{ $detalle->contenido['dni_firma']['observaciones'] ?? '' }}</textarea>
                        </div>

                    </div> {{-- FIN DEL CONTENEDOR CON BORDE --}}
                </div>

                {{-- 03. EQUIPOS --}}
                <div class="space-y-8 mt-12">
                    <div class="flex items-center gap-4">
                        <span class="h-12 w-12 rounded-2xl bg-indigo-600 text-white flex items-center justify-center text-lg font-black shadow-lg shadow-indigo-200">03</span>
                        <h4 class="text-sm font-black text-slate-800 uppercase tracking-wider">Insumos y Equipamiento</h4>
                    </div>

                    {{-- CONTENEDOR CON BORDE Y SOMBRA (Igual a Secciones 01 y 02) --}}
                    <div class="bg-white p-8 rounded-[2.5rem] border border-slate-200 shadow-sm">
                        
                        {{-- Encabezado interno opcional para dar contexto --}}
                        <div class="mb-6 flex items-center gap-2">
                            <i data-lucide="monitor-speaker" class="w-4 h-4 text-indigo-500"></i>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest italic">
                                Inventario de equipos informáticos y médicos en el consultorio
                            </p>
                        </div>

                        {{-- Componente de la Tabla --}}
                        <div class="overflow-hidden rounded-3xl border border-slate-100">
                            <x-tabla-equipos :equipos="$equipos" modulo="cred" />
                        </div>

                        {{-- Nota informativa al pie del contenedor --}}
                        <div class="mt-4 flex items-center gap-2 px-2">
                            <i data-lucide="info" class="w-3 h-3 text-slate-400"></i>
                            <p class="text-[9px] text-slate-400 font-medium">
                                * Asegúrese de registrar el estado actual y el número de serie de cada equipo para el reporte patrimonial.
                            </p>
                        </div>
                    </div> 
                </div>

                {{-- 04. GESTIÓN --}}
                <div class="space-y-6">
                    <div class="flex items-center gap-4 border-b border-slate-100 pb-4">
                        <span class="h-10 w-10 rounded-2xl bg-indigo-600 text-white flex items-center justify-center text-sm font-black shadow-lg shadow-indigo-200">03</span>
                        <h4 class="text-sm font-black text-slate-800 uppercase tracking-wider">Gestión de Stock y Almacenamiento</h4>
                    </div>

                    <div class="space-y-4 bg-slate-50/50 p-6 rounded-[2rem] border border-slate-100">
                        <div class="grid grid-cols-1 gap-3">
                            @foreach([
                                'sis_gestion' => '¿Cuenta con sistema de gestión para el control de inventario?',
                                'stock_actual' => '¿El stock físico coincide con el reporte del sistema?',
                                'fua_sismed' => '¿Realiza la digitación oportuna en el SISMED?',
                                'inventario_anual' => '¿Ha realizado el inventario anual de medicamentos e insumos?'
                            ] as $key => $pregunta)
                            <div class="flex items-center justify-between p-4 bg-white rounded-xl border border-slate-100 hover:shadow-md transition-shadow">
                                <span class="text-[11px] font-bold text-slate-700 uppercase">{{ $pregunta }}</span>
                                <div class="flex gap-4">
                                    <label class="flex items-center gap-2 cursor-pointer font-black text-[10px] text-slate-400">
                                        <input type="radio" name="contenido[preguntas][{{ $key }}]" value="SI" {{ ($detalle->contenido['preguntas'][$key] ?? '') == 'SI' ? 'checked' : '' }} class="text-indigo-600"> SÍ
                                    </label>
                                    <label class="flex items-center gap-2 cursor-pointer font-black text-[10px] text-slate-400">
                                        <input type="radio" name="contenido[preguntas][{{ $key }}]" value="NO" {{ ($detalle->contenido['preguntas'][$key] ?? '') == 'NO' ? 'checked' : '' }} class="text-indigo-600"> NO
                                    </label>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- 05. SOPORTE --}}
                <div class="space-y-6">
                    <div class="flex items-center gap-4">
                        <span class="h-12 w-12 rounded-2xl bg-indigo-600 text-white flex items-center justify-center text-lg font-black shadow-lg shadow-indigo-200">05</span>
                        <h4 class="text-sm font-black text-slate-800 uppercase tracking-wider">Dificultades y Soporte</h4>
                    </div>

                    <div class="p-8 bg-white rounded-[2.5rem] border border-slate-200 shadow-sm space-y-10">
                        {{-- Pregunta 1 --}}
                        <div class="space-y-5">
                            <label class="text-[11px] font-black text-indigo-600 uppercase tracking-widest flex items-center gap-2">
                                <i data-lucide="user-cog" class="w-4 h-4"></i>
                                ¿A quién comunica dificultades?
                            </label>
                            <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
                                @foreach(['MINSA', 'DIRESA', 'UE', 'JEFE DE EESS', 'OTROS'] as $com)
                                <label class="relative cursor-pointer group">
                                    <input type="radio" name="contenido[soporte][comunica]" value="{{$com}}" 
                                        {{ ($detalle->contenido['soporte']['comunica'] ?? '') == $com ? 'checked' : '' }} 
                                        class="peer sr-only">
                                    <div class="px-4 py-3 rounded-2xl border-2 border-slate-100 bg-slate-50/50 text-center transition-all 
                                        peer-checked:border-indigo-600 peer-checked:bg-indigo-50 peer-checked:text-indigo-700
                                        group-hover:border-slate-300">
                                        <span class="text-[10px] font-bold uppercase tracking-tight">{{ $com }}</span>
                                    </div>
                                </label>
                                @endforeach
                            </div>
                        </div>

                        {{-- Pregunta 2 --}}
                        <div class="space-y-5">
                            <label class="text-[11px] font-black text-indigo-600 uppercase tracking-widest flex items-center gap-2">
                                <i data-lucide="message-circle" class="w-4 h-4"></i>
                                ¿Qué medio utiliza?
                            </label>
                            <div class="grid grid-cols-3 gap-4">
                                @foreach(['WhatsApp', 'Teléfono', 'Email'] as $medio)
                                @php
                                    $icon = ['WhatsApp' => 'message-square', 'Teléfono' => 'phone', 'Email' => 'mail'][$medio];
                                @endphp
                                <label class="relative cursor-pointer group">
                                    <input type="radio" name="contenido[soporte][medio]" value="{{$medio}}" 
                                        {{ ($detalle->contenido['soporte']['medio'] ?? '') == $medio ? 'checked' : '' }} 
                                        class="peer sr-only">
                                    <div class="flex flex-col items-center gap-2 p-4 rounded-3xl border-2 border-slate-100 bg-slate-50/50 transition-all
                                        peer-checked:border-emerald-500 peer-checked:bg-emerald-50 peer-checked:text-emerald-700
                                        group-hover:border-slate-300">
                                        <i data-lucide="{{ $icon }}" class="w-5 h-5"></i>
                                        <span class="text-[10px] font-black uppercase">{{ $medio }}</span>
                                    </div>
                                </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 06. COMENTARIOS Y OBSERVACIONES GENERALES --}}
                <div class="space-y-6">
                    <div class="flex items-center gap-4">
                        <span class="h-12 w-12 rounded-2xl bg-indigo-600 text-white flex items-center justify-center text-lg font-black shadow-lg shadow-indigo-200">06</span>
                        <h4 class="text-xs font-black text-slate-700 uppercase tracking-widest">Observaciones y/o Comentarios</h4>
                    </div>
                    <div class="bg-white p-8 rounded-[2.5rem] border border-slate-200 shadow-sm space-y-4">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-2 flex items-center gap-2">
                            <i data-lucide="message-square" class="w-4 h-4 text-indigo-500"></i>
                            Notas adicionales del monitoreo
                        </label>
                        <textarea 
                            name="contenido[observaciones_generales]" 
                            rows="4" 
                            class="input-standard w-full resize-none" 
                            placeholder="Describa hallazgos relevantes, dificultades adicionales o recomendaciones para este establecimiento...">{{ $detalle->contenido['observaciones_generales'] ?? '' }}</textarea>
                    </div>
                </div>

                {{-- 07. EVIDENCIAS --}}
                <div class="space-y-8">
                    <div class="flex items-center gap-4">
                        <span class="h-12 w-12 rounded-2xl bg-indigo-600 text-white flex items-center justify-center text-lg font-black shadow-lg shadow-indigo-200">07</span>
                        <h4 class="text-sm font-black text-slate-800 uppercase tracking-wider">Evidencias Fotográficas</h4>
                    </div>

                    {{-- CONTENEDOR UNIFICADO --}}
                    <div class="bg-white p-8 rounded-[2.5rem] border border-slate-200 shadow-sm space-y-6">
                        
                        <div class="flex items-center gap-2 mb-2">
                            <i data-lucide="camera" class="w-4 h-4 text-indigo-500"></i>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest italic">
                                Capture o suba fotos del consultorio o documentación relevante
                            </p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            {{-- Espacio para Evidencia 01 --}}
                            <div class="space-y-4">
                                <label class="text-[10px] font-black uppercase text-slate-400 ml-2 italic flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-indigo-400"></span> Evidencia Principal
                                </label>
                                <div class="relative h-56 rounded-[2rem] border-2 border-dashed border-slate-200 overflow-hidden bg-slate-50 flex items-center justify-center transition-all hover:bg-slate-100 group">
                                    <template x-if="images.img1">
                                        <div class="relative w-full h-full">
                                            <img :src="images.img1" class="h-full w-full object-cover shadow-inner">
                                            <button type="button" @click="removeImage('img1', 'input_foto_1', 'foto_1_actual')" class="btn-remove-image hover:scale-110 active:scale-95 transition-transform">
                                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                            </button>
                                            <div class="absolute bottom-0 inset-x-0 bg-gradient-to-t from-black/50 to-transparent p-4">
                                                <p class="text-white text-[9px] font-bold uppercase tracking-widest">Imagen cargada</p>
                                            </div>
                                        </div>
                                    </template>
                                    <template x-if="!images.img1">
                                        <div class="flex flex-col items-center gap-3 text-slate-300 group-hover:text-indigo-400 transition-colors">
                                            <div class="p-4 rounded-full bg-white shadow-sm transition-transform group-hover:scale-110">
                                                <i data-lucide="image-plus" class="w-8 h-8"></i>
                                            </div>
                                            <span class="text-[10px] font-black uppercase tracking-tighter">Seleccionar Archivo</span>
                                        </div>
                                    </template>
                                    <input type="file" name="foto_evidencia_1" id="input_foto_1" @change="previewImage($event, 'img1')" class="absolute inset-0 opacity-0 cursor-pointer z-10">
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 flex items-start gap-3 bg-indigo-50/50 p-4 rounded-2xl border border-indigo-100">
                            <i data-lucide="shield-check" class="w-4 h-4 text-indigo-500 mt-0.5"></i>
                            <p class="text-[9px] text-indigo-700 leading-relaxed font-medium">
                                <span class="font-bold">Privacidad de datos:</span> Las imágenes subidas serán utilizadas exclusivamente para fines de auditoría y respaldo del presente monitoreo CRED, cumpliendo con la normativa vigente de protección de datos personales.
                            </p>
                        </div>

                    </div> {{-- FIN DEL CONTENEDOR --}}
                </div>

                <div class="pt-10">
                    <button type="submit" class="w-full bg-indigo-600 text-white py-8 rounded-[2.5rem] font-black text-lg shadow-2xl hover:bg-indigo-700 transition-all flex items-center justify-center gap-6 group">
                        <i data-lucide="save" class="w-8 h-8 transition-transform group-hover:rotate-12"></i>
                        FINALIZAR Y GUARDAR MÓDULO FARMACIA
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL NUEVO PROFESIONAL --}}
    <div x-show="openModal" x-cloak class="fixed inset-0 z-[200] flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-slate-900/80 backdrop-blur-sm" @click="openModal = false"></div>
        <div class="relative bg-white rounded-[3.5rem] p-12 text-center max-w-lg w-full border border-slate-200 shadow-2xl">
            <h3 class="text-2xl font-black text-slate-800 uppercase italic">Nuevo Profesional</h3>
            <p class="my-8 text-slate-600 font-medium leading-relaxed">El documento <strong x-text="docNuevo"></strong> no existe en el sistema. Los campos han sido limpiados para su ingreso manual.</p>
            <button @click="openModal = false" class="w-full bg-slate-900 text-white py-5 rounded-2xl font-black uppercase tracking-widest hover:bg-black transition-all shadow-lg">ENTENDIDO</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        lucide.createIcons();

        // 1. Visibilidad Capacitación
        const radiosCap = document.querySelectorAll('.radio-capacitacion');
        const seccionEntes = document.getElementById('seccion_capacitacion_entes');
        radiosCap.forEach(radio => {
            radio.addEventListener('change', function() {
                if (this.value === 'SI') {
                    seccionEntes.classList.remove('hidden');
                } else {
                    seccionEntes.classList.add('hidden');
                    seccionEntes.querySelectorAll('input[type="checkbox"]').forEach(cb => cb.checked = false);
                }
            });
        });

        // 2. Búsqueda y Limpieza de Profesional
        const inputDoc = document.getElementById('doc');
        const loader = document.getElementById('loading_profesional');
        const limpiarInputs = () => {
            ['nombres', 'apellido_paterno', 'apellido_materno', 'telefono', 'email'].forEach(id => {
                const el = document.getElementById(id);
                if (el) el.value = '';
            });
        };

        if (inputDoc) {
            inputDoc.addEventListener('input', function() {
                const docValue = this.value.trim();
                const tipo = document.getElementById('tipo_doc').value;
                if ((tipo === 'DNI' && docValue.length === 8) || (tipo !== 'DNI' && docValue.length >= 6)) {
                    loader.classList.remove('hidden');
                    fetch(`{{ url('usuario/monitoreo/profesional/buscar') }}/${docValue}`)
                        .then(r => r.json())
                        .then(data => {
                            loader.classList.add('hidden');
                            if (data.exists) {
                                document.getElementById('nombres').value = data.nombres || '';
                                document.getElementById('apellido_paterno').value = data.apellido_paterno || '';
                                document.getElementById('apellido_materno').value = data.apellido_materno || '';
                                if(document.getElementById('telefono')) document.getElementById('telefono').value = data.telefono || '';
                                if(document.getElementById('email')) document.getElementById('email').value = data.email || '';
                            } else {
                                limpiarInputs();
                                window.dispatchEvent(new CustomEvent('abrir-modal-nuevo', { detail: { doc: docValue } }));
                            }
                        })
                        .catch(() => loader.classList.add('hidden'));
                }
            });
        }
    });
</script>
@endpush
