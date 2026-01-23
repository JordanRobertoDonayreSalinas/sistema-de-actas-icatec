
@props(['detalle'])

@php
    // Recuperamos el valor guardado para evitar errores en la vista
    $turno = $detalle->contenido['turno'] ?? '';
@endphp

{{-- ESTRUCTURA ORIGINAL (Estilo Teal / CSMC) --}}
<div class="bg-white rounded-[2rem] p-8 shadow-lg border border-slate-100 mb-8">
    
    {{-- ENCABEZADO CON CÍRCULO NÚMERO 1 --}}
    <div class="flex items-center gap-3 mb-6 border-b border-slate-100 pb-4">
        <span class="bg-teal-600 text-white w-8 h-8 flex items-center justify-center rounded-full font-black text-sm">1</span>
        <h3 class="text-teal-900 font-black text-lg uppercase tracking-tight">DETALLES DEL CONSULTORIO</h3>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-start">
        
        {{-- FECHA --}}
        <div>
            <label class="block text-slate-500 text-[10px] font-black uppercase tracking-widest mb-2">Fecha de Monitoreo</label>
            <input type="date" 
                   name="contenido[fecha]" 
                   value="{{ $detalle->contenido['fecha'] ?? date('Y-m-d') }}" 
                   class="w-full bg-slate-50 border-2 border-slate-200 rounded-xl px-4 py-3 text-slate-800 font-bold outline-none focus:border-teal-500 transition-all">
        </div>
        
        {{-- TURNO (CORREGIDO) --}}
        <div>
            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">
                Turno
            </label>
            <div class="flex gap-4">
                {{-- OPCIÓN MAÑANA --}}
                <label class="flex-1 relative cursor-pointer group">
                    <input type="radio" name="contenido[turno]" value="MAÑANA" 
                        {{ $turno == 'MAÑANA' ? 'checked' : '' }} 
                        class="peer sr-only">
                    <div class="p-3 rounded-xl border-2 border-slate-200 bg-slate-50 text-center transition-all 
                        peer-checked:border-amber-400 peer-checked:bg-amber-50 peer-checked:text-amber-700 
                        peer-checked:shadow-sm hover:border-amber-200">
                        <div class="flex items-center justify-center gap-2">
                            <i data-lucide="sun" class="w-4 h-4"></i>
                            <span class="text-[10px] font-black uppercase tracking-wider">MAÑANA</span>
                        </div>
                    </div>
                </label>
                
                {{-- OPCIÓN TARDE --}}
                <label class="flex-1 relative cursor-pointer group">
                    <input type="radio" name="contenido[turno]" value="TARDE" 
                        {{ $turno == 'TARDE' ? 'checked' : '' }} 
                        class="peer sr-only">
                    <div class="p-3 rounded-xl border-2 border-slate-200 bg-slate-50 text-center transition-all 
                        peer-checked:border-indigo-500 peer-checked:bg-indigo-50 peer-checked:text-indigo-700 
                        peer-checked:shadow-sm hover:border-indigo-200">
                        <div class="flex items-center justify-center gap-2">
                            <i data-lucide="sunset" class="w-4 h-4"></i>
                            <span class="text-[10px] font-black uppercase tracking-wider">TARDE</span>
                        </div>
                    </div>
                </label>
            </div>
        </div>

        {{-- NRO DE AMBIENTES --}}
        <div>
            <label class="block text-slate-500 text-[10px] font-black uppercase tracking-widest mb-2">Nro. de Consultorios</label>
            <input type="number" 
                   name="contenido[num_ambientes]" 
                   value="{{ $detalle->contenido['num_ambientes'] ?? '' }}" 
                   min="0"
                   class="w-full bg-slate-50 border-2 border-slate-200 rounded-xl px-4 py-3 text-slate-800 font-bold outline-none focus:border-teal-500 transition-all text-center"
                   placeholder="EJ: 1">
        </div>

        {{-- DENOMINACIÓN --}}
        <div>
            <label class="block text-slate-500 text-[10px] font-black uppercase tracking-widest mb-2">Consultorio Entrevistado</label>
            <input type="text" 
                   name="contenido[denominacion_ambiente]" 
                   value="{{ $detalle->contenido['denominacion_ambiente'] ?? '' }}" 
                   class="w-full bg-slate-50 border-2 border-slate-200 rounded-xl px-4 py-3 text-slate-800 font-bold outline-none focus:border-teal-500 transition-all uppercase"
                   placeholder="EJ: C. EXT. MEDICINA">
        </div>
    </div>
</div>