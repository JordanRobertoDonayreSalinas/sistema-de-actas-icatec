@extends('layouts.usuario')

@section('title', 'Módulo 05: Laboratorio')

@section('content')
<div class="py-12 bg-slate-50 min-h-screen">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        
        {{-- ENCABEZADO PRINCIPAL --}}
        <div class="mb-8 flex flex-col md:flex-row items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-3 mb-1">
                    <span class="px-3 py-1 bg-emerald-600 text-white text-[10px] font-black rounded-lg uppercase tracking-widest">Módulo Técnico</span>
                    <span class="text-slate-400 font-bold text-[10px] uppercase">ID Acta: #{{ str_pad($acta->id, 5, '0', STR_PAD_LEFT) }}</span>
                </div>
                <h2 class="text-3xl font-black text-slate-900 uppercase tracking-tight">05. Laboratorio</h2>
                <p class="text-slate-500 font-bold uppercase text-xs mt-1">
                    <i data-lucide="hospital" class="inline-block w-4 h-4 mr-1 text-emerald-500"></i> {{ $acta->establecimiento->nombre }}
                </p>
            </div>
            <a href="{{ route('usuario.monitoreo.modulos', $acta->id) }}" class="flex items-center gap-2 px-6 py-3 bg-white border-2 border-slate-200 rounded-2xl text-slate-600 font-black text-xs hover:bg-slate-50 transition-all uppercase shadow-sm">
                <i data-lucide="arrow-left" class="w-4 h-4"></i> Volver al Panel
            </a>
        </div>

        <form action="{{ route('usuario.monitoreo.laboratorio.store', $acta->id) }}" 
              method="POST" 
              enctype="multipart/form-data" 
              class="space-y-8" 
              id="form-monitoreo-final">
            @csrf

            {{-- SECCIÓN 0: TURNO --}}
            <div class="bg-emerald-600 rounded-[3rem] p-10 shadow-xl shadow-emerald-200/50 border border-emerald-500 relative overflow-hidden">
                <div class="absolute top-0 right-0 -mt-10 -mr-10 w-40 h-40 bg-white/10 rounded-full blur-3xl"></div>
                <div class="relative z-10 flex flex-col md:flex-row items-center gap-8">
                    <div class="flex items-center gap-4">
                        <div class="h-14 w-14 bg-white/20 text-white rounded-2xl flex items-center justify-center font-black text-2xl shadow-lg border border-white/30">
                            <i data-lucide="clock" class="w-8 h-8"></i>
                        </div>
                        <div>
                            <h3 class="text-white font-black text-xl uppercase tracking-tight">Turno:</h3>
                            <p class="text-emerald-100 text-[10px] font-bold uppercase tracking-widest">Horario de evaluación</p>
                        </div>
                    </div>
                    <div class="flex-1 w-full max-w-2xl">
                        <x-turno :selected="$detalle->contenido['turno'] ?? ''" />
                    </div>
                </div>
            </div>

            {{-- SECCIÓN 1: DATOS DEL PROFESIONAL (RESPONSABLE) --}}
            <div class="bg-white rounded-[3rem] p-10 shadow-xl shadow-slate-200/50 border border-slate-100">
                <div class="flex items-center gap-4 mb-8">
                    <div class="h-12 w-12 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center font-black text-xl shadow-inner">1</div>
                    <h3 class="text-lg font-black text-slate-800 uppercase tracking-tight">Responsable de Laboratorio</h3>
                </div>
                <x-busqueda-profesional prefix="rrhh" :detalle="$detalle" />
            </div>

            {{-- SECCIÓN 2: PROCESOS Y CALIDAD --}}
            <div class="bg-white rounded-[3rem] p-10 shadow-xl shadow-slate-200/50 border border-slate-100">
                <div class="flex items-center gap-4 mb-8">
                    <div class="h-12 w-12 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center font-black text-xl shadow-inner">2</div>
                    <h3 class="text-lg font-black text-slate-800 uppercase tracking-tight">Procesos y Calidad</h3>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-10 mb-8">
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">¿Cuenta con Manual de Procedimientos?</label>
                        <select name="contenido[manual_procedimientos]" class="w-full px-6 py-4 bg-slate-50 border-2 border-slate-100 rounded-2xl focus:border-emerald-500 font-bold text-sm outline-none transition-all cursor-pointer shadow-sm uppercase">
                            <option value="SI" {{ (isset($detalle->contenido['manual_procedimientos']) && $detalle->contenido['manual_procedimientos'] == 'SI') ? 'selected' : '' }}>SI</option>
                            <option value="NO" {{ (isset($detalle->contenido['manual_procedimientos']) && $detalle->contenido['manual_procedimientos'] == 'NO') ? 'selected' : '' }}>NO</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">¿Realiza Control Interno?</label>
                        <select name="contenido[control_interno]" class="w-full px-6 py-4 bg-slate-50 border-2 border-slate-100 rounded-2xl focus:border-emerald-500 font-bold text-sm outline-none transition-all cursor-pointer shadow-sm uppercase">
                            <option value="SI" {{ (isset($detalle->contenido['control_interno']) && $detalle->contenido['control_interno'] == 'SI') ? 'selected' : '' }}>SI</option>
                            <option value="NO" {{ (isset($detalle->contenido['control_interno']) && $detalle->contenido['control_interno'] == 'NO') ? 'selected' : '' }}>NO</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- SECCIÓN 3: DOCUMENTACIÓN, DNI Y FIRMA --}}
            <x-documentacion_administrativa :detalle="$detalle" />

            {{-- SECCIÓN 4 Y 5: CAPACITACIÓN --}}
            <div class="bg-white rounded-[3rem] p-10 shadow-xl shadow-slate-200/50 border border-slate-100">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-10 mb-10 border-b border-slate-100 pb-10">
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">4. ¿Recibió capacitación?</label>
                        <select name="contenido[recibio_capacitacion]" id="recibio_capacitacion" onchange="toggleEntidadCapacitadora(this.value)" class="w-full px-6 py-4 bg-slate-50 border-2 border-slate-100 rounded-2xl font-bold text-sm outline-none focus:border-emerald-500 transition-all uppercase">
                            <option value="SI" {{ ($detalle->contenido['recibio_capacitacion'] ?? '') == 'SI' ? 'selected' : '' }}>SI</option>
                            <option value="NO" {{ ($detalle->contenido['recibio_capacitacion'] ?? '') == 'NO' ? 'selected' : '' }}>NO</option>
                        </select>
                    </div>
                    <div id="wrapper_entidad_capacitadora" class="transition-all duration-300">
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">5. Entidad Capacitadora</label>
                        <select name="contenido[inst_que_lo_capacito]" class="w-full px-6 py-4 bg-slate-50 border-2 border-slate-100 rounded-2xl font-bold text-sm outline-none focus:border-emerald-500 transition-all uppercase">
                            <option value="MINSA" {{ ($detalle->contenido['inst_que_lo_capacito'] ?? '') == 'MINSA' ? 'selected' : '' }}>MINSA</option>
                            <option value="DIRESA" {{ ($detalle->contenido['inst_que_lo_capacito'] ?? '') == 'DIRESA' ? 'selected' : '' }}>DIRESA</option>
                            <option value="UNIDAD EJECUTORA" {{ ($detalle->contenido['inst_que_lo_capacito'] ?? '') == 'UNIDAD EJECUTORA' ? 'selected' : '' }}>UNIDAD EJECUTORA</option>
                            <option value="OTROS" {{ ($detalle->contenido['inst_que_lo_capacito'] ?? '') == 'OTROS' ? 'selected' : '' }}>OTROS</option>
                        </select>
                    </div>
                </div>

                <div class="mt-6">
                    <x-tabla-equipos :equipos="$equipos" modulo="laboratorio" />
                </div>
            </div>

            {{-- SECCIÓN 6 Y 7: COMUNICACIÓN --}}
            <div class="bg-white rounded-[3rem] p-10 shadow-xl shadow-slate-200/50 border border-slate-100">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">6. ¿A quién comunica dificultades?</label>
                        <select name="contenido[inst_a_quien_comunica]" class="w-full px-6 py-4 bg-slate-50 border-2 border-slate-100 rounded-2xl font-bold text-sm outline-none uppercase focus:border-emerald-500">
                            @foreach(['MINSA','DIRESA','JEFE DE ESTABLECIMIENTO','OTRO'] as $op)
                                <option value="{{$op}}" {{ ($detalle->contenido['inst_a_quien_comunica'] ?? '') == $op ? 'selected' : '' }}>{{$op}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">7. ¿Qué medio utiliza?</label>
                        <select name="contenido[medio_que_utiliza]" class="w-full px-6 py-4 bg-slate-50 border-2 border-slate-100 rounded-2xl font-bold text-sm outline-none uppercase focus:border-emerald-500">
                            @foreach(['WHATSAPP','TELEFONO','EMAIL'] as $me)
                                <option value="{{$me}}" {{ ($detalle->contenido['medio_que_utiliza'] ?? '') == $me ? 'selected' : '' }}>{{$me}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- SECCIÓN FINAL: COMENTARIOS Y FOTO --}}
            <div class="bg-slate-900 rounded-[3.5rem] p-12 shadow-2xl text-white relative overflow-hidden">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 relative z-10">
                    <div>
                        <h3 class="text-sm font-black uppercase tracking-[0.3em] text-emerald-400 mb-6 flex items-center gap-2">
                            <i data-lucide="message-square" class="w-5 h-5"></i> 8. Comentarios
                        </h3>
                        <textarea name="contenido[comentarios]" rows="5" class="w-full bg-white/5 border-2 border-white/10 rounded-3xl p-6 text-white font-bold outline-none focus:border-emerald-500 transition-all uppercase placeholder-white/20 shadow-inner">{{ $detalle->contenido['comentarios'] ?? '' }}</textarea>
                    </div>
                    
                    <div>
                        <h3 class="text-sm font-black uppercase tracking-[0.3em] text-red-400 mb-6 flex items-center gap-2">
                            <i data-lucide="camera" class="w-5 h-5"></i> 9. Evidencia Fotográfica
                        </h3>
                        
                        @if(isset($detalle->contenido['foto_evidencia']))
                            <div class="mb-6 relative group w-full max-w-xs">
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Imagen Actual:</p>
                                <div class="rounded-3xl overflow-hidden border-4 border-emerald-500/30 shadow-2xl">
                                    <img src="{{ asset('storage/' . $detalle->contenido['foto_evidencia']) }}" 
                                         class="w-full h-48 object-cover group-hover:scale-110 transition-transform duration-700">
                                </div>
                            </div>
                        @endif

                        <div class="relative group">
                            <input type="file" name="foto_evidencia" id="foto_evidencia" accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-20" onchange="previewImage(event)">
                            <div id="dropzone" class="bg-white/5 border-2 border-dashed border-white/20 rounded-[2.5rem] p-10 flex flex-col items-center justify-center group-hover:bg-white/10 transition-all duration-500 shadow-inner">
                                <i data-lucide="upload-cloud" id="upload-icon" class="w-10 h-10 text-emerald-400 mb-4"></i>
                                <span id="file-name-display" class="text-[10px] font-black uppercase tracking-widest text-slate-300 text-center leading-relaxed">
                                    {{ isset($detalle->contenido['foto_evidencia']) ? 'CLICK PARA REEMPLAZAR IMAGEN' : 'SUBIR FOTO DE EVIDENCIA' }}
                                </span>
                                <img id="img-preview" src="#" alt="Vista previa" class="hidden mt-4 w-32 h-32 object-cover rounded-2xl border-2 border-emerald-500 shadow-2xl">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="pt-10 pb-20">
                <button type="submit" id="btn-submit-action" class="w-full group bg-emerald-600 text-white p-10 rounded-[3rem] font-black shadow-2xl flex items-center justify-between hover:bg-emerald-700 transition-all duration-500 active:scale-[0.98]">
                    <div class="flex items-center gap-8 pointer-events-none">
                        <div class="h-16 w-16 bg-white/20 rounded-3xl flex items-center justify-center group-hover:rotate-12 transition-all shadow-lg border border-white/30">
                            <i data-lucide="save" id="icon-save-loader" class="w-8 h-8 text-white"></i>
                        </div>
                        <div class="text-left">
                            <p class="text-xl uppercase tracking-[0.3em] leading-none">Confirmar Registro</p>
                            <p class="text-[10px] text-emerald-200 font-bold uppercase mt-3 tracking-widest">Sincronizar Módulo Laboratorio</p>
                        </div>
                    </div>
                    <div class="h-14 w-14 bg-white/10 rounded-full flex items-center justify-center group-hover:bg-white group-hover:text-emerald-600 transition-all duration-500">
                        <i data-lucide="chevron-right" class="w-7 h-7"></i>
                    </div>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function toggleEntidadCapacitadora(value) {
        const wrapper = document.getElementById('wrapper_entidad_capacitadora');
        wrapper.style.display = (value === 'SI') ? 'block' : 'none';
    }

    function previewImage(event) {
        const input = event.target;
        const preview = document.getElementById('img-preview');
        const icon = document.getElementById('upload-icon');
        const fileName = document.getElementById('file-name-display');
        const dropzone = document.getElementById('dropzone');

        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.classList.remove('hidden');
                icon.classList.add('hidden');
                fileName.innerText = "NUEVA IMAGEN: " + input.files[0].name.toUpperCase();
                dropzone.classList.add('bg-emerald-500/10', 'border-emerald-500');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const selectCapacitacion = document.getElementById('recibio_capacitacion');
        if (selectCapacitacion) toggleEntidadCapacitadora(selectCapacitacion.value);
    });

    document.getElementById('form-monitoreo-final').onsubmit = function() {
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