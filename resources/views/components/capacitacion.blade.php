@props(['model'])

{{-- 
    Uso: <x-capacitacion model="form.capacitacion" />
    Encapsula la sección de preguntas sobre formación y documentos firmados.
--}}

<div x-data="capacitacionComponent({{ $model }})" 
     class="bg-white border border-slate-200 rounded-[2.5rem] p-8 shadow-xl shadow-slate-200/50 relative overflow-hidden">
    
    {{-- Decoración de fondo --}}
    <div class="absolute top-0 right-0 w-24 h-24 bg-indigo-50 rounded-full -mr-12 -mt-12 opacity-60 pointer-events-none"></div>
    
    <div class="relative z-10">
        {{-- Encabezado --}}
        <div class="flex items-center gap-4 mb-6">
            <div class="h-12 w-12 rounded-2xl bg-indigo-600 flex items-center justify-center shadow-lg shadow-indigo-200">
                <i data-lucide="graduation-cap" class="text-white w-6 h-6"></i>
            </div>
            <div>
                <h3 class="text-lg font-black text-slate-900 uppercase tracking-tight">Capacitación</h3>
                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Formación Profesional</p>
            </div>
        </div>

        {{-- Pregunta Principal --}}
        <div class="space-y-4">
            <label class="block text-sm font-bold text-slate-700">¿El personal ha recibido capacitación?</label>
            <div class="flex gap-4">
                <label class="cursor-pointer flex-1">
                    <input type="radio" value="SI" x-model="entidad.recibieron_cap" class="peer sr-only">
                    <div class="text-center py-3 rounded-xl border-2 border-slate-100 bg-slate-50 text-slate-400 font-bold text-xs uppercase transition-all peer-checked:bg-indigo-50 peer-checked:text-indigo-600 peer-checked:border-indigo-500 hover:bg-white shadow-sm">SI</div>
                </label>
                <label class="cursor-pointer flex-1">
                    <input type="radio" value="NO" x-model="entidad.recibieron_cap" class="peer sr-only">
                    <div class="text-center py-3 rounded-xl border-2 border-slate-100 bg-slate-50 text-slate-400 font-bold text-xs uppercase transition-all peer-checked:bg-slate-100 peer-checked:text-slate-600 peer-checked:border-slate-300 hover:bg-white shadow-sm">NO</div>
                </label>
            </div>

            {{-- Selector Condicional (Aparece si responden SI) --}}
            <div x-show="entidad.recibieron_cap === 'SI'" 
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 -translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="mt-4">
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 pl-1">Entidad que capacitó</label>
                <select x-model="entidad.institucion_cap" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 font-bold uppercase text-sm focus:ring-indigo-500 cursor-pointer">
                    <option value="" disabled>Seleccione entidad...</option>
                    <option value="MINSA">MINSA</option>
                    <option value="DIRESA">DIRESA</option>
                    <option value="UNIDAD EJECUTORA">UNIDAD EJECUTORA</option>
                </select>
            </div>
        </div>
        
        {{-- Grid de Documentos Firmados --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-6 mt-6 border-t border-slate-100">
            
            {{-- Declaración Jurada --}}
            <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">¿Firmó Declaración Jurada?</label>
                <div class="flex gap-4">
                    <label class="cursor-pointer flex-1">
                        <input type="radio" value="SI" x-model="entidad.decl_jurada" class="peer sr-only">
                        <div class="px-4 py-2 text-center rounded-lg border-2 border-slate-200 text-slate-400 font-bold text-xs peer-checked:bg-indigo-50 peer-checked:text-indigo-600 peer-checked:border-indigo-500 transition-all hover:bg-slate-50">SÍ</div>
                    </label>
                    <label class="cursor-pointer flex-1">
                        <input type="radio" value="NO" x-model="entidad.decl_jurada" class="peer sr-only">
                        <div class="px-4 py-2 text-center rounded-lg border-2 border-slate-200 text-slate-400 font-bold text-xs peer-checked:bg-slate-100 peer-checked:text-slate-600 peer-checked:border-slate-400 transition-all hover:bg-slate-50">NO</div>
                    </label>
                </div>
            </div>

            {{-- Compromiso Confidencialidad --}}
            <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">¿Firmó Confidencialidad?</label>
                <div class="flex gap-4">
                    <label class="cursor-pointer flex-1">
                        <input type="radio" value="SI" x-model="entidad.comp_confidencialidad" class="peer sr-only">
                        <div class="px-4 py-2 text-center rounded-lg border-2 border-slate-200 text-slate-400 font-bold text-xs peer-checked:bg-indigo-50 peer-checked:text-indigo-600 peer-checked:border-indigo-500 transition-all hover:bg-slate-50">SÍ</div>
                    </label>
                    <label class="cursor-pointer flex-1">
                        <input type="radio" value="NO" x-model="entidad.comp_confidencialidad" class="peer sr-only">
                        <div class="px-4 py-2 text-center rounded-lg border-2 border-slate-200 text-slate-400 font-bold text-xs peer-checked:bg-slate-100 peer-checked:text-slate-600 peer-checked:border-slate-400 transition-all hover:bg-slate-50">NO</div>
                    </label>
                </div>
            </div>
        </div>
    </div>

    {{-- Script del Componente --}}
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('capacitacionComponent', (entidadVinculada) => ({
                // Vinculamos 'form.capacitacion' a 'entidad'
                entidad: entidadVinculada,
            }));
        });
    </script>
</div>