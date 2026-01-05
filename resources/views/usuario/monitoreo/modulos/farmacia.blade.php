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
            {{-- ENCABEZADO --}}
            <div class="bg-slate-900 p-10 text-white relative overflow-hidden">
                <div class="absolute top-0 right-0 p-10 opacity-10 rotate-12">
                    <i data-lucide="pill" class="w-48 h-48"></i>
                </div>
                <div class="relative z-10">
                    <span class="px-3 py-1 bg-indigo-500/20 border border-indigo-400/30 rounded-full text-indigo-300 text-[10px] font-black uppercase tracking-widest">Módulo 15</span>
                    <h3 class="text-3xl font-black uppercase italic tracking-tight mt-2">Farmacia</h3>
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
                        <h4 class="text-sm font-black text-slate-800 uppercase tracking-wider">Responsable del Servicio</h4>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-12 gap-6 bg-slate-50/50 p-8 rounded-[2.5rem] border border-slate-100">
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
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-2">Nombres</label>
                            <input type="text" name="contenido[personal][nombre]" id="nombres" value="{{ $detalle->contenido['personal']['nombre'] ?? '' }}" class="input-standard uppercase">
                        </div>
                        <div class="md:col-span-4 space-y-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-2">Apellido Paterno</label>
                            <input type="text" name="contenido[personal][apellido_paterno]" id="apellido_paterno" value="{{ $detalle->contenido['personal']['apellido_paterno'] ?? '' }}" class="input-standard uppercase">
                        </div>
                        <div class="md:col-span-4 space-y-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-2">Apellido Materno</label>
                            <input type="text" name="contenido[personal][apellido_materno]" id="apellido_materno" value="{{ $detalle->contenido['personal']['apellido_materno'] ?? '' }}" class="input-standard uppercase">
                        </div>
                        <div class="md:col-span-4 space-y-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-2">Contacto</label>
                            <input type="text" name="contenido[personal][contacto]" id="telefono" value="{{ $detalle->contenido['personal']['contacto'] ?? '' }}" class="input-standard">
                        </div>
                        <div class="md:col-span-6 space-y-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-2">Email</label>
                            <input type="email" name="contenido[personal][email]" id="email" value="{{ $detalle->contenido['personal']['email'] ?? '' }}" class="input-standard">
                        </div>
                        <div class="md:col-span-3 space-y-2 text-center">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Turno</label>
                            <input type="text" name="contenido[personal][turno]" value="{{ $detalle->contenido['personal']['turno'] ?? '' }}" class="input-standard text-center uppercase">
                        </div>
                        <div class="md:col-span-3 space-y-2 text-center">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Rol</label>
                            <input type="text" name="contenido[personal][rol]" value="{{ $detalle->contenido['personal']['rol'] ?? '' }}" class="input-standard text-center uppercase">
                        </div>

                        {{-- CAPACITACIÓN --}}
                        <div class="md:col-span-12 mt-4 bg-white p-6 rounded-2xl border border-slate-100 flex flex-col md:flex-row items-center gap-8">
                            <div class="flex items-center gap-4">
                                <p class="text-[11px] font-black text-slate-500 uppercase italic">¿Recibió capacitación?</p>
                                <div class="flex gap-4">
                                    <label class="flex items-center gap-2 cursor-pointer font-black text-xs text-slate-400">
                                        <input type="radio" name="contenido[capacitacion][recibio]" value="SI" {{ ($detalle->contenido['capacitacion']['recibio'] ?? '') == 'SI' ? 'checked' : '' }} class="radio-capacitacion text-indigo-600"> SÍ
                                    </label>
                                    <label class="flex items-center gap-2 cursor-pointer font-black text-xs text-slate-400">
                                        <input type="radio" name="contenido[capacitacion][recibio]" value="NO" {{ ($detalle->contenido['capacitacion']['recibio'] ?? '') == 'NO' ? 'checked' : '' }} class="radio-capacitacion text-indigo-600"> NO
                                    </label>
                                </div>
                            </div>

                            <div id="seccion_capacitacion_entes" class="flex-1 md:border-l border-slate-200 md:pl-8 {{ ($detalle->contenido['capacitacion']['recibio'] ?? '') != 'SI' ? 'hidden' : '' }}">
                                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-3">De parte de quién:</p>
                                <div class="flex flex-wrap gap-6">
                                    @php $entes_guardados = (array)($detalle->contenido['capacitacion']['ente'] ?? []); @endphp
                                    @foreach(['MINSA', 'DIRIS / DIRESA', 'UE'] as $ente)
                                    <label class="flex items-center gap-2 text-[10px] font-bold text-slate-600 cursor-pointer">
                                        <input type="checkbox" name="contenido[capacitacion][ente][]" value="{{ $ente }}" {{ in_array($ente, $entes_guardados) ? 'checked' : '' }} class="rounded text-indigo-600"> {{ $ente }}
                                    </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 02. EQUIPOS --}}
                <div class="space-y-8">
                    <div class="flex items-center gap-4 border-b border-slate-100 pb-4">
                        <span class="h-12 w-12 rounded-2xl bg-indigo-600 text-white flex items-center justify-center text-lg font-black shadow-lg shadow-indigo-200">02</span>
                        <h4 class="text-sm font-black text-slate-800 uppercase tracking-wider">Insumos y Equipamiento</h4>
                    </div>
                    <x-tabla-equipos :equipos="$equipos" modulo="farmacia" />
                </div>

                {{-- 03. GESTIÓN --}}
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

                {{-- 04. SOPORTE --}}
                <div class="space-y-6">
                    <div class="flex items-center gap-4">
                        <span class="h-12 w-12 rounded-2xl bg-indigo-600 text-white flex items-center justify-center text-lg font-black shadow-lg shadow-indigo-200">04</span>
                        <h4 class="text-xs font-black text-slate-700 uppercase tracking-widest">Dificultades y Soporte</h4>
                    </div>
                    <div class="p-8 bg-indigo-50/30 rounded-[2.5rem] border border-indigo-100 grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="space-y-3">
                            <label class="text-[10px] font-black text-indigo-700 uppercase italic">¿A quién comunica dificultades?</label>
                            <div class="flex gap-4">
                                @foreach(['MINSA', 'DIRIS/DIRESA', 'UE'] as $com)
                                <label class="flex items-center gap-2 text-[10px] font-bold text-slate-500 uppercase">
                                    <input type="radio" name="contenido[soporte][comunica]" value="{{$com}}" {{ ($detalle->contenido['soporte']['comunica'] ?? '') == $com ? 'checked' : '' }}> {{$com}}
                                </label>
                                @endforeach
                            </div>
                        </div>
                        <div class="space-y-3">
                            <label class="text-[10px] font-black text-indigo-700 uppercase italic">¿Qué medio utiliza?</label>
                            <div class="flex gap-4">
                                @foreach(['WhatsApp', 'Teléfono', 'Email'] as $medio)
                                <label class="flex items-center gap-2 text-[10px] font-bold text-slate-500 uppercase">
                                    <input type="radio" name="contenido[soporte][medio]" value="{{$medio}}" {{ ($detalle->contenido['soporte']['medio'] ?? '') == $medio ? 'checked' : '' }}> {{$medio}}
                                </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 05. FOTOS CON ELIMINACIÓN --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm">
                    <div class="space-y-4">
                        <label class="text-[10px] font-black uppercase text-slate-400 ml-2 italic">Foto de Almacén / Stock</label>
                        <div class="relative h-48 rounded-3xl border-2 border-dashed border-slate-200 overflow-hidden bg-slate-50 flex items-center justify-center">
                            <template x-if="images.img1">
                                <div class="relative w-full h-full group">
                                    <img :src="images.img1" class="h-full w-full object-cover">
                                    <button type="button" @click="removeImage('img1', 'input_foto_1', 'foto_1_actual')" class="btn-remove-image">
                                        <i data-lucide="x" class="w-4 h-4"></i>
                                    </button>
                                </div>
                            </template>
                            <template x-if="!images.img1">
                                <div class="flex flex-col items-center gap-2 text-slate-300">
                                    <i data-lucide="image-plus" class="w-10 h-10"></i>
                                    <span class="text-[10px] font-black uppercase">Subir Foto</span>
                                </div>
                            </template>
                            <input type="file" name="foto_evidencia_1" id="input_foto_1" @change="previewImage($event, 'img1')" class="absolute inset-0 opacity-0 cursor-pointer">
                        </div>
                    </div>

                    <div class="space-y-4">
                        <label class="text-[10px] font-black uppercase text-slate-400 ml-2 italic">Foto de Área de Atención</label>
                        <div class="relative h-48 rounded-3xl border-2 border-dashed border-slate-200 overflow-hidden bg-slate-50 flex items-center justify-center">
                            <template x-if="images.img2">
                                <div class="relative w-full h-full group">
                                    <img :src="images.img2" class="h-full w-full object-cover">
                                    <button type="button" @click="removeImage('img2', 'input_foto_2', 'foto_2_actual')" class="btn-remove-image">
                                        <i data-lucide="x" class="w-4 h-4"></i>
                                    </button>
                                </div>
                            </template>
                            <template x-if="!images.img2">
                                <div class="flex flex-col items-center gap-2 text-slate-300">
                                    <i data-lucide="image-plus" class="w-10 h-10"></i>
                                    <span class="text-[10px] font-black uppercase">Subir Foto</span>
                                </div>
                            </template>
                            <input type="file" name="foto_evidencia_2" id="input_foto_2" @change="previewImage($event, 'img2')" class="absolute inset-0 opacity-0 cursor-pointer">
                        </div>
                    </div>
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
