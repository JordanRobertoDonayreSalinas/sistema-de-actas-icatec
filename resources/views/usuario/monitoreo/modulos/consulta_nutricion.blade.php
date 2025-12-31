@extends('layouts.usuario')

@section('title', 'Módulo 05: Consulta Externa - Nutrición')

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
                <h2 class="text-3xl font-black text-slate-900 uppercase tracking-tight">06. Consulta Externa: Nutrición</h2>
                <p class="text-slate-500 font-bold uppercase text-xs mt-1">
                    <i data-lucide="hospital" class="inline-block w-4 h-4 mr-1 text-indigo-500"></i> {{ $acta->establecimiento->nombre }}
                </p>
            </div>
            <a href="{{ route('usuario.monitoreo.modulos', $acta->id) }}" class="flex items-center gap-2 px-6 py-3 bg-white border-2 border-slate-200 rounded-2xl text-slate-600 font-black text-xs hover:bg-slate-50 transition-all uppercase shadow-sm">
                <i data-lucide="arrow-left" class="w-4 h-4"></i> Volver al Panel
            </a>
        </div>

        {{-- FORMULARIO --}}
        <form action="{{ route('usuario.monitoreo.consulta-nutricion.store', $acta->id) }}" 
              method="POST" 
              enctype="multipart/form-data" 
              class="space-y-8" 
              id="form-consulta-nutricion">
            @csrf

            {{-- SECCIÓN 1: CONSULTORIOS --}}
            <div class="bg-white rounded-[3rem] p-10 shadow-xl shadow-slate-200/50 border border-slate-100">
                <div class="flex items-center gap-4 mb-8">
                    <div class="h-12 w-12 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center font-black text-xl shadow-inner">1</div>
                    <h3 class="text-lg font-black text-slate-800 uppercase tracking-tight">Detalles</h3>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Cantidad de Consultorios</label>
                        <input type="number" name="contenido[num_consultorios]" value="{{ $detalle->contenido['num_consultorios'] ?? 0 }}" class="w-full px-6 py-4 bg-slate-50 border-2 border-slate-100 rounded-2xl font-bold text-sm outline-none" />
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Denominación del Consultorio</label>
                        <input type="text" name="contenido[denominacion_consultorio]" value="{{ $detalle->contenido['denominacion_consultorio'] ?? '' }}" class="w-full px-6 py-4 bg-slate-50 border-2 border-slate-100 rounded-2xl font-bold text-sm outline-none" />
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
            </div>

            {{-- SECCIÓN 3: DETALLES DE CAPACITACIÓN --}}
            <div class="bg-white rounded-[3rem] p-10 shadow-xl shadow-slate-200/50 border border-slate-100">
                <div class="flex items-center gap-4 mb-8">
                    <div class="h-12 w-12 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center font-black text-xl shadow-inner">3</div>
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

            {{-- SECCIÓN 4: MATERIALES --}}
            <div class="bg-white rounded-[3rem] p-10 shadow-xl shadow-slate-200/50 border border-slate-100">
                <div class="flex items-center gap-4 mb-8">
                    <div class="h-12 w-12 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center font-black text-xl shadow-inner">4</div>
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

            {{-- SECCIÓN 5: EQUIPAMIENTO DEL ÁREA --}}
            <div class="bg-white rounded-[3rem] p-10 shadow-xl shadow-slate-200/50 border border-slate-100">
                <div class="flex items-center gap-4 mb-8">
                    <div class="h-12 w-12 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center font-black text-xl shadow-inner">5</div>
                    <h3 class="text-lg font-black text-slate-800 uppercase tracking-tight">Equipamiento del Área</h3>
                </div>
                <x-tabla-equipos :equipos="$equipos" modulo="consulta_nutricion" />
            </div>

            {{-- SECCIÓN 6: SOPORTE TÉCNICO --}}
            <div class="bg-white rounded-[3rem] p-10 shadow-xl shadow-slate-200/50 border border-slate-100">
                <div class="flex items-center gap-4 mb-8">
                    <div class="h-12 w-12 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center font-black text-xl shadow-inner">6</div>
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

            {{-- SECCIÓN 7: COMENTARIOS Y FOTOS --}}
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
                            // Normalize foto_evidencia to array format
                            $fotosActuales = [];
                            if (isset($detalle->contenido['foto_evidencia'])) {
                                $fotoVal = $detalle->contenido['foto_evidencia'];
                                if (is_array($fotoVal)) {
                                    $fotosActuales = $fotoVal;
                                } elseif (!empty($fotoVal)) {
                                    $fotosActuales = [$fotoVal];
                                }
                            }
                        @endphp
                        <h3 class="text-sm font-black uppercase tracking-[0.3em] text-red-400 mb-6 flex items-center gap-2">
                            <i data-lucide="camera" class="w-5 h-5"></i> Evidencia Fotográfica
                        </h3>
                        <div class="relative group">
                            <input type="file" name="foto_evidencia[]" id="foto_evidencia" {{ (count($fotosActuales) > 0) ? '' : 'required' }} accept="image/*" multiple class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-20" onchange="previewImage(event)">
                            <div id="dropzone" class="bg-white/5 border-2 border-dashed border-white/20 rounded-[2.5rem] p-10 flex flex-col items-center justify-center group-hover:bg-white/10 transition-all duration-500 shadow-inner">
                                <i data-lucide="upload-cloud" id="upload-icon" class="w-10 h-10 text-indigo-400 mb-4"></i>
                                <span id="file-name-display" class="text-[10px] font-black uppercase tracking-widest text-slate-300 text-center leading-relaxed">
                                    {{ count($fotosActuales) > 0 ? 'CLICK PARA AGREGAR/REEMPLAZAR IMÁGENES' : 'SELECCIONAR HASTA 5 IMÁGENES' }}
                                </span>
                                <div id="img-previews" class="hidden mt-4 grid grid-cols-3 gap-3"></div>
                            </div>
                        </div>
                        @if(count($fotosActuales) > 0)
                            <div class="mt-4 flex items-center gap-3 bg-emerald-500/10 p-4 rounded-2xl border border-emerald-500/20">
                                <i data-lucide="image-check" class="text-emerald-400 w-6 h-6"></i>
                                <div>
                                    <span class="block text-[10px] font-black text-emerald-400 uppercase tracking-widest">{{ count($fotosActuales) }} Imagen(es) Verificada(s)</span>
                                    <p class="text-[9px] text-emerald-500/60 font-bold uppercase italic tracking-tighter">Archivo(s) almacenado(s) correctamente</p>
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
                            <p class="text-[10px] text-indigo-200 font-bold uppercase mt-3 tracking-widest">Sincronizar Módulo 05 con el Maestro</p>
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
    function toggleInstCapacitacion(value) {
        const section = document.getElementById('section_inst_capacitacion');
        value === 'NO' ? section.classList.add('hidden') : section.classList.remove('hidden');
    }

    function previewImage(event) {
        const input = event.target;
        const icon = document.getElementById('upload-icon');
        const fileName = document.getElementById('file-name-display');
        const dropzone = document.getElementById('dropzone');
        const previews = document.getElementById('img-previews');

        previews.innerHTML = '';
        if (input.files && input.files.length > 0) {
            const maxFiles = 5;
            const files = Array.from(input.files).slice(0, maxFiles);
            files.forEach(file => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = 'w-24 h-24 object-cover rounded-2xl border-2 border-indigo-500 shadow-2xl';
                    previews.appendChild(img);
                }
                reader.readAsDataURL(file);
            });
            previews.classList.remove('hidden');
            icon.classList.add('hidden');
            fileName.innerText = `${files.length} / 5 IMÁGENES SELECCIONADAS`;
            dropzone.classList.add('bg-indigo-500/10', 'border-indigo-500');
        }
    }

    document.getElementById('form-consulta-nutricion').onsubmit = function() {
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
