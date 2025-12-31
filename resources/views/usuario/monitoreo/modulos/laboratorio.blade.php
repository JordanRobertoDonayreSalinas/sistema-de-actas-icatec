@extends('layouts.usuario')

@section('title', 'Módulo 17: Laboratorio')

@section('content')
<div class="py-12 bg-slate-50 min-h-screen">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        
        {{-- ENCABEZADO DIRECTO --}}
        <div class="mb-8 flex flex-col md:flex-row items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-3 mb-1">
                    <span class="px-3 py-1 bg-indigo-600 text-white text-[10px] font-black rounded-lg uppercase tracking-widest">Módulo Técnico</span>
                    <span class="text-slate-400 font-bold text-[10px] uppercase">ID Acta: #{{ str_pad($acta->id, 5, '0', STR_PAD_LEFT) }}</span>
                </div>
                <h2 class="text-3xl font-black text-slate-900 uppercase tracking-tight">17. Laboratorio</h2>
                <p class="text-slate-500 font-bold uppercase text-xs mt-1">
                    <i data-lucide="hospital" class="inline-block w-4 h-4 mr-1 text-indigo-500"></i> {{ $acta->establecimiento->nombre }}
                </p>
            </div>
            <a href="{{ route('usuario.monitoreo.modulos', $acta->id) }}" class="flex items-center gap-2 px-6 py-3 bg-white border-2 border-slate-200 rounded-2xl text-slate-600 font-black text-xs hover:bg-slate-50 transition-all uppercase shadow-sm">
                <i data-lucide="arrow-left" class="w-4 h-4"></i> Volver al Panel
            </a>
        </div>

        {{-- FORMULARIO --}}
        <form action="{{ route('usuario.monitoreo.laboratorio.store', $acta->id) }}" 
              method="POST" 
              enctype="multipart/form-data" 
              class="space-y-8" 
              id="form-monitoreo-final">
            @csrf

            {{-- SECCIÓN 1: RESPONSABLE --}}
            <div class="bg-white rounded-[3rem] p-10 shadow-xl shadow-slate-200/50 border border-slate-100">
                <div class="flex items-center gap-4 mb-8">
                    <div class="h-12 w-12 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center font-black text-xl shadow-inner">1</div>
                    <h3 class="text-lg font-black text-slate-800 uppercase tracking-tight">Profesional de Laboratorio</h3>
                </div>
                {{-- CORRECCIÓN: El prefijo debe ser "responsable" para que el controlador lo detecte --}}
                <x-busqueda-profesional prefix="responsable" :detalle="$detalle" />
            </div>

            {{-- SECCIÓN 2: ACCESO Y CAPACITACIÓN --}}
            <div class="bg-white rounded-[3rem] p-10 shadow-xl shadow-slate-200/50 border border-slate-100">
                <div class="flex items-center gap-4 mb-8">
                    <div class="h-12 w-12 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center font-black text-xl shadow-inner">2</div>
                    <h3 class="text-lg font-black text-slate-800 uppercase tracking-tight">Acceso y Capacitación</h3>
                </div>
                
                <div class="max-w-md mb-8">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">1. ¿Cuenta con acceso al sistema?</label>
                    <select name="contenido[acceso_sistema]" class="w-full px-6 py-4 bg-slate-50 border-2 border-slate-100 rounded-2xl focus:border-indigo-500 font-bold text-sm outline-none transition-all cursor-pointer shadow-sm uppercase">
                        <option value="SI" {{ (isset($detalle->contenido['acceso_sistema']) && $detalle->contenido['acceso_sistema'] == 'SI') ? 'selected' : '' }}>SI, POSEE CREDENCIALES ACTIVAS</option>
                        <option value="NO" {{ (isset($detalle->contenido['acceso_sistema']) && $detalle->contenido['acceso_sistema'] == 'NO') ? 'selected' : '' }}>NO POSEE CREDENCIALES</option>
                    </select>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-10 mt-10 border-t border-slate-100 pt-10">
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">2. ¿Recibió capacitación?</label>
                        <select name="contenido[recibio_capacitacion]" class="w-full px-6 py-4 bg-slate-50 border-2 border-slate-100 rounded-2xl font-bold text-sm outline-none focus:border-indigo-500 transition-all uppercase">
                            <option value="SI" {{ ($detalle->contenido['recibio_capacitacion'] ?? '') == 'SI' ? 'selected' : '' }}>SI, FUE CAPACITADO</option>
                            <option value="NO" {{ ($detalle->contenido['recibio_capacitacion'] ?? '') == 'NO' ? 'selected' : '' }}>NO FUE CAPACITADO</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">3. Entidad Capacitadora</label>
                        <select name="contenido[inst_que_lo_capacito]" class="w-full px-6 py-4 bg-slate-50 border-2 border-slate-100 rounded-2xl font-bold text-sm outline-none focus:border-indigo-500 transition-all uppercase">
                            <option value="MINSA" {{ ($detalle->contenido['inst_que_lo_capacito'] ?? '') == 'MINSA' ? 'selected' : '' }}>MINSA</option>
                            <option value="DIRESA" {{ ($detalle->contenido['inst_que_lo_capacito'] ?? '') == 'DIRESA' ? 'selected' : '' }}>DIRESA</option>
                            <option value="OTROS" {{ ($detalle->contenido['inst_que_lo_capacito'] ?? '') == 'OTROS' ? 'selected' : '' }}>OTROS</option>
                        </select>
                    </div>
                </div>

                {{-- SECCIÓN DE EQUIPOS --}}
                <div class="mt-10 pt-10 border-t border-slate-100">
                    {{-- CORRECCIÓN: Se agrega el parámetro modulo="laboratorio" para independizar el inventario --}}
                    <x-tabla-equipos :equipos="$equipos ?? []" modulo="laboratorio" :esHistorico="$esHistorico ?? false" />
                </div>
            </div>

            {{-- SECCIÓN 3: COMUNICACIÓN --}}
            <div class="bg-white rounded-[3rem] p-10 shadow-xl shadow-slate-200/50 border border-slate-100">
                <div class="flex items-center gap-4 mb-8">
                    <div class="h-12 w-12 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center font-black text-xl shadow-inner">3</div>
                    <h3 class="text-lg font-black text-slate-800 uppercase tracking-tight">Comunicación de Dificultades</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">5. ¿A quién comunica dificultades?</label>
                        <select name="contenido[inst_a_quien_comunica]" class="w-full px-6 py-4 bg-slate-50 border-2 border-slate-100 rounded-2xl font-bold text-sm outline-none focus:border-indigo-500 uppercase">
                            @foreach(['MINSA','DIRESA','JEFE DE ESTABLECIMIENTO','OTROS'] as $op)
                                <option value="{{$op}}" {{ ($detalle->contenido['inst_a_quien_comunica'] ?? '') == $op ? 'selected' : '' }}>{{$op}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">6. ¿Qué medio utiliza?</label>
                        <select name="contenido[medio_que_utiliza]" class="w-full px-6 py-4 bg-slate-50 border-2 border-slate-100 rounded-2xl font-bold text-sm outline-none focus:border-indigo-500 uppercase">
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
                        <h3 class="text-sm font-black uppercase tracking-[0.3em] text-indigo-400 mb-6 flex items-center gap-2">
                            <i data-lucide="message-square" class="w-5 h-5"></i> 7. Comentarios
                        </h3>
                        <textarea name="contenido[comentarios]" rows="5" class="w-full bg-white/5 border-2 border-white/10 rounded-3xl p-6 text-white font-bold outline-none focus:border-indigo-500 transition-all uppercase placeholder-white/20 shadow-inner">{{ $detalle->contenido['comentarios'] ?? '' }}</textarea>
                    </div>
                    
                    <div>
                        <h3 class="text-sm font-black uppercase tracking-[0.3em] text-red-400 mb-6 flex items-center gap-2">
                            <i data-lucide="camera" class="w-5 h-5"></i> 8. Evidencia Fotográfica
                        </h3>
                        
                        @if(isset($detalle->contenido['foto_evidencia']))
                            <div class="mb-6 relative group w-full max-w-xs">
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Imagen Actual:</p>
                                <div class="rounded-3xl overflow-hidden border-4 border-indigo-500/30 shadow-2xl">
                                    <img src="{{ asset('storage/' . $detalle->contenido['foto_evidencia']) }}" 
                                         class="w-full h-48 object-cover group-hover:scale-110 transition-transform duration-700">
                                </div>
                            </div>
                        @endif

                        <div class="relative group">
                            <input type="file" name="foto_evidencia" id="foto_evidencia" onchange="previewImage(event)" accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-20">
                            <div id="dropzone" class="bg-white/5 border-2 border-dashed border-white/20 rounded-[2.5rem] p-10 flex flex-col items-center justify-center group-hover:bg-white/10 transition-all duration-500 shadow-inner">
                                <i data-lucide="upload-cloud" id="upload-icon" class="w-10 h-10 text-indigo-400 mb-4 transition-transform group-hover:-translate-y-2"></i>
                                <span id="file-name-display" class="text-[10px] font-black uppercase tracking-widest text-slate-300">SUBIR FOTO DE EVIDENCIA</span>
                                <img id="img-preview" src="#" class="hidden mt-4 w-32 h-32 object-cover rounded-2xl border-2 border-indigo-500">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- BOTÓN DE GUARDADO FINAL --}}
            <div class="pt-10 pb-20">
                <button type="submit" id="btn-submit-action" class="w-full group bg-indigo-600 text-white p-10 rounded-[3rem] font-black shadow-2xl flex items-center justify-between hover:bg-indigo-700 transition-all duration-500 active:scale-[0.98]">
                    <div class="flex items-center gap-8 pointer-events-none">
                        <div class="h-16 w-16 bg-white/20 rounded-3xl flex items-center justify-center group-hover:rotate-12 transition-all border border-white/30 shadow-lg">
                            <i data-lucide="save" id="icon-save-loader" class="w-8 h-8 text-white"></i>
                        </div>
                        <div class="text-left">
                            <p class="text-xl uppercase tracking-[0.3em] leading-none">Confirmar Registro</p>
                            <p class="text-[10px] text-indigo-200 font-bold uppercase mt-3 tracking-widest">Sincronizar Módulo 17 con el Maestro</p>
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
                dropzone.classList.add('bg-indigo-500/10', 'border-indigo-500');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

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