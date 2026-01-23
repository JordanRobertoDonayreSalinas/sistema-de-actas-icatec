@props(['model'])

<div x-data="{ 
        entidad: {{ $model }},
    }" 
    class="bg-white border border-slate-200 rounded-[2.5rem] shadow-xl overflow-hidden mb-8">
    
    {{-- Encabezado estilizado igual a Criterios --}}
    <div class="bg-slate-50 px-8 py-6 border-b border-slate-100 flex items-center gap-3">
        <div class="h-10 w-10 rounded-xl bg-indigo-100 flex items-center justify-center text-indigo-600">
            <i data-lucide="graduation-cap" class="w-5 h-5"></i>
        </div>
        <h3 class="text-slate-800 font-black text-sm uppercase tracking-wider">Detalles de Capacitación</h3>
    </div>

    {{-- Inputs ocultos para envío de datos --}}
    <input type="hidden" name="capacitacion[recibieron_cap]" :value="entidad.recibieron_cap">
    <input type="hidden" name="capacitacion[institucion_cap]" :value="entidad.institucion_cap">

    <div class="p-8 space-y-6">
        <div class="group">
            <div class="flex items-start gap-4">
                <div class="mt-1">
                    <span class="h-6 w-6 rounded-full bg-slate-200 text-slate-500 flex items-center justify-center text-[10px] font-black italic">04</span>
                </div>
                <div class="flex-1">
                    <p class="text-slate-700 font-bold text-xs uppercase leading-relaxed mb-4">
                        ¿El personal ha recibido capacitación relacionada a sus funciones en el CSMC?
                    </p>
                    
                    <div class="flex gap-4">
                        <label class="cursor-pointer flex-1">
                            <input type="radio" value="SI" x-model="entidad.recibieron_cap" class="peer sr-only" @change="unsavedChanges = true">
                            <div class="text-center py-3 rounded-xl border-2 border-slate-100 bg-slate-50 text-slate-400 font-bold text-xs uppercase transition-all peer-checked:bg-teal-50 peer-checked:text-teal-600 peer-checked:border-teal-500 hover:bg-white shadow-sm">SI</div>
                        </label>
                        <label class="cursor-pointer flex-1">
                            <input type="radio" value="NO" x-model="entidad.recibieron_cap" class="peer sr-only" @change="unsavedChanges = true; entidad.institucion_cap = ''">
                            <div class="text-center py-3 rounded-xl border-2 border-slate-100 bg-slate-50 text-slate-400 font-bold text-xs uppercase transition-all peer-checked:bg-slate-100 peer-checked:text-slate-600 peer-checked:border-slate-300 hover:bg-white shadow-sm">NO</div>
                        </label>
                    </div>

                    {{-- Selector Condicional --}}
                    <div x-show="entidad.recibieron_cap === 'SI'" 
                         x-cloak
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 -translate-y-2"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         class="mt-4 p-4 bg-teal-50/30 border border-teal-100 rounded-2xl">
                        <label class="block text-[10px] font-black text-teal-600 uppercase tracking-widest mb-2 pl-1">Entidad que capacitó</label>
                        <select x-model="entidad.institucion_cap" 
                                @change="unsavedChanges = true"
                                class="w-full bg-white border border-teal-200 rounded-xl p-3 font-bold uppercase text-sm focus:ring-teal-500 cursor-pointer">
                            <option value="" disabled>Seleccione entidad...</option>
                            <option value="JEFE DE ESTABLECIMIENTO">JEFE DE ESTABLECIMIENTO</option>
                            <option value="UNIDAD EJECUTORA">UNIDAD EJECUTORA</option>
                            <option value="MINSA">MINSA</option>
                            <option value="DIRESA">DIRESA</option>
                            <option value="OTROS">OTROS</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>