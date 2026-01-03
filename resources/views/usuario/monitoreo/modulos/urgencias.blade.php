@extends('layouts.usuario')

@section('title', 'Módulo 18: Urgencias y Emergencias')

@section('content')
<div class="py-12 bg-slate-50 min-h-screen">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        
        {{-- ENCABEZADO --}}
        <div class="mb-8 flex flex-col md:flex-row items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-3 mb-1">
                    <span class="px-3 py-1 bg-red-600 text-white text-[10px] font-black rounded-lg uppercase tracking-widest">Módulo Crítico</span>
                    <span class="text-slate-400 font-bold text-[10px] uppercase">ID Acta: #{{ str_pad($acta->id, 5, '0', STR_PAD_LEFT) }}</span>
                </div>
                <h2 class="text-3xl font-black text-slate-900 uppercase tracking-tight">18. Urgencias y Emergencias</h2>
                <p class="text-slate-500 font-bold uppercase text-xs mt-1">
                    <i data-lucide="hospital" class="inline-block w-4 h-4 mr-1 text-red-500"></i> {{ $acta->establecimiento->nombre }}
                </p>
            </div>
            <a href="{{ route('usuario.monitoreo.modulos', $acta->id) }}" class="flex items-center gap-2 px-6 py-3 bg-white border-2 border-slate-200 rounded-2xl text-slate-600 font-black text-xs hover:bg-slate-50 transition-all uppercase shadow-sm">
                <i data-lucide="arrow-left" class="w-4 h-4"></i> Volver al Panel
            </a>
        </div>

        {{-- FORMULARIO --}}
        <form action="{{ route('usuario.monitoreo.urgencias.store', $acta->id) }}" 
              method="POST" 
              enctype="multipart/form-data" 
              class="space-y-8" 
              id="form-urgencias">
            @csrf

            {{-- SECCIÓN 1: RESPONSABLE --}}
            <div class="bg-white rounded-[3rem] p-10 shadow-xl shadow-slate-200/50 border border-slate-100">
                <div class="flex items-center gap-4 mb-8">
                    <div class="h-12 w-12 bg-red-50 text-red-600 rounded-2xl flex items-center justify-center font-black text-xl shadow-inner">1</div>
                    <h3 class="text-lg font-black text-slate-800 uppercase tracking-tight">Responsable de Urgencias</h3>
                </div>
                <x-busqueda-profesional prefix="responsable" :detalle="$detalle" />
            </div>

            {{-- SECCIÓN 2: PROCESOS DE EMERGENCIA --}}
            <div class="bg-white rounded-[3rem] p-10 shadow-xl shadow-slate-200/50 border border-slate-100">
                <div class="flex items-center gap-4 mb-8">
                    <div class="h-12 w-12 bg-red-50 text-red-600 rounded-2xl flex items-center justify-center font-black text-xl shadow-inner">2</div>
                    <h3 class="text-lg font-black text-slate-800 uppercase tracking-tight">Evaluación de Procesos</h3>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">1. Prioridad de Triaje Predominante</label>
                        <select name="contenido[triaje_prioridad]" class="w-full px-6 py-4 bg-slate-50 border-2 border-slate-100 rounded-2xl font-bold text-sm outline-none focus:border-red-500 transition-all uppercase">
                            <option value="PRIORIDAD I" {{ ($detalle->contenido['triaje_prioridad'] ?? '') == 'PRIORIDAD I' ? 'selected' : '' }}>PRIORIDAD I (GRAVE)</option>
                            <option value="PRIORIDAD II" {{ ($detalle->contenido['triaje_prioridad'] ?? '') == 'PRIORIDAD II' ? 'selected' : '' }}>PRIORIDAD II (URGENCIA)</option>
                            <option value="PRIORIDAD III" {{ ($detalle->contenido['triaje_prioridad'] ?? '') == 'PRIORIDAD III' ? 'selected' : '' }}>PRIORIDAD III (MENOR)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">2. Tiempo Promedio de Espera</label>
                        <select name="contenido[tiempo_espera]" class="w-full px-6 py-4 bg-slate-50 border-2 border-slate-100 rounded-2xl font-bold text-sm outline-none focus:border-red-500 transition-all uppercase">
                            <option value="0-15 MIN" {{ ($detalle->contenido['tiempo_espera'] ?? '') == '0-15 MIN' ? 'selected' : '' }}>0 - 15 MINUTOS</option>
                            <option value="15-30 MIN" {{ ($detalle->contenido['tiempo_espera'] ?? '') == '15-30 MIN' ? 'selected' : '' }}>15 - 30 MINUTOS</option>
                            <option value="+30 MIN" {{ ($detalle->contenido['tiempo_espera'] ?? '') == '+30 MIN' ? 'selected' : '' }}>MÁS DE 30 MINUTOS</option>
                        </select>
                    </div>
                </div>

                <div class="mt-8">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">3. Disponibilidad de Insumos Críticos</label>
                    <select name="contenido[insumos_emergencia]" class="w-full px-6 py-4 bg-slate-50 border-2 border-slate-100 rounded-2xl font-bold text-sm outline-none focus:border-red-500 transition-all uppercase">
                        <option value="COMPLETO" {{ ($detalle->contenido['insumos_emergencia'] ?? '') == 'COMPLETO' ? 'selected' : '' }}>STOCK COMPLETO</option>
                        <option value="PARCIAL" {{ ($detalle->contenido['insumos_emergencia'] ?? '') == 'PARCIAL' ? 'selected' : '' }}>STOCK PARCIAL / CRÍTICO</option>
                        <option value="DESABASTECIDO" {{ ($detalle->contenido['insumos_emergencia'] ?? '') == 'DESABASTECIDO' ? 'selected' : '' }}>DESABASTECIDO</option>
                    </select>
                </div>

                {{-- INVENTARIO --}}
                <div class="mt-10 pt-10 border-t border-slate-100">
                    <x-tabla-equipos :equipos="$equipos ?? []" modulo="urgencias_emergencias" :esHistorico="$esHistorico ?? false" />
                </div>
            </div>

            {{-- SECCIÓN FINAL: COMENTARIOS Y FOTO --}}
            <div class="bg-slate-900 rounded-[3.5rem] p-12 shadow-2xl text-white relative overflow-hidden">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 relative z-10">
                    <div>
                        <h3 class="text-sm font-black uppercase tracking-[0.3em] text-red-400 mb-6 flex items-center gap-2">
                            <i data-lucide="message-square" class="w-5 h-5"></i> 3. Observaciones de Emergencia
                        </h3>
                        <textarea name="contenido[observaciones]" rows="5" class="w-full bg-white/5 border-2 border-white/10 rounded-3xl p-6 text-white font-bold outline-none focus:border-red-500 transition-all uppercase placeholder-white/20 shadow-inner">{{ $detalle->contenido['observaciones'] ?? '' }}</textarea>
                    </div>
                    
                    <div>
                        <h3 class="text-sm font-black uppercase tracking-[0.3em] text-orange-400 mb-6 flex items-center gap-2">
                            <i data-lucide="camera" class="w-5 h-5"></i> 4. Evidencia Fotográfica
                        </h3>
                        
                        @if(isset($detalle->contenido['foto_evidencia']))
                            <div class="mb-6 relative group w-full max-w-xs">
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Imagen Actual:</p>
                                <div class="rounded-3xl overflow-hidden border-4 border-red-500/30 shadow-2xl">
                                    <img src="{{ asset('storage/' . $detalle->contenido['foto_evidencia']) }}" 
                                         class="w-full h-48 object-cover group-hover:scale-110 transition-transform duration-700">
                                </div>
                            </div>
                        @endif

                        <div class="relative group">
                            <input type="file" name="foto_evidencia" id="foto_evidencia" onchange="previewImage(event)" accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-20">
                            <div id="dropzone" class="bg-white/5 border-2 border-dashed border-white/20 rounded-[2.5rem] p-10 flex flex-col items-center justify-center group-hover:bg-white/10 transition-all duration-500 shadow-inner">
                                <i data-lucide="upload-cloud" id="upload-icon" class="w-10 h-10 text-red-400 mb-4 transition-transform group-hover:-translate-y-2"></i>
                                <span id="file-name-display" class="text-[10px] font-black uppercase tracking-widest text-slate-300">SUBIR FOTO DE ÁREA</span>
                                <img id="img-preview" src="#" class="hidden mt-4 w-32 h-32 object-cover rounded-2xl border-2 border-red-500">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- BOTÓN DE GUARDADO --}}
            <div class="pt-10 pb-20">
                <button type="submit" id="btn-submit-action" class="w-full group bg-red-600 text-white p-10 rounded-[3rem] font-black shadow-2xl flex items-center justify-between hover:bg-red-700 transition-all duration-500 active:scale-[0.98]">
                    <div class="flex items-center gap-8 pointer-events-none">
                        <div class="h-16 w-16 bg-white/20 rounded-3xl flex items-center justify-center group-hover:rotate-12 transition-all border border-white/30 shadow-lg">
                            <i data-lucide="save" id="icon-save-loader" class="w-8 h-8 text-white"></i>
                        </div>
                        <div class="text-left">
                            <p class="text-xl uppercase tracking-[0.3em] leading-none">Guardar Urgencias</p>
                            <p class="text-[10px] text-red-200 font-bold uppercase mt-3 tracking-widest">Registrar Módulo 18 en el Sistema</p>
                        </div>
                    </div>
                    <div class="h-14 w-14 bg-white/10 rounded-full flex items-center justify-center group-hover:bg-white group-hover:text-red-600 transition-all duration-500">
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
                fileName.innerText = "EVIDENCIA: " + input.files[0].name.toUpperCase();
                dropzone.classList.add('bg-red-500/10', 'border-red-500');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    document.getElementById('form-urgencias').onsubmit = function() {
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