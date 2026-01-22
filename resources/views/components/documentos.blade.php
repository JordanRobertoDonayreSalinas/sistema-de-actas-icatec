@props(['model', 'tipo' => 'triaje'])

{{-- 
    Uso: <x-documentos model="form.inicio_labores" tipo="odontologia" />
    Tipos soportados: 'triaje', 'odontologia', 'psicologia'
--}}

@php
    // Definir el placeholder según el tipo de módulo
    $placeholder = match($tipo) {
        'odontologia' => 'EJ: ODONTOLOGÍA 01',
        'psicologia'  => 'EJ: PSICOLOGÍA 01',
        default       => 'EJ: CONSULTORIO 01'
    };
@endphp

<div x-data="documentosComponent({{ $model }})" 
     class="bg-white border border-slate-200 rounded-[2.5rem] p-8 shadow-xl shadow-slate-200/50 relative overflow-hidden">
    
    <div class="absolute top-0 right-0 w-24 h-24 bg-indigo-50 rounded-full -mr-12 -mt-12 opacity-60 pointer-events-none"></div>
    
    <div class="flex items-center gap-4 mb-8 relative z-10">
        <div class="h-12 w-12 rounded-2xl bg-indigo-600 flex items-center justify-center shadow-lg shadow-indigo-200">
            <i data-lucide="clipboard-list" class="text-white w-6 h-6"></i>
        </div>
        <div>
            <h3 class="text-lg font-black text-slate-900 uppercase tracking-tight">Inicio Labores</h3>
            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Configuración Inicial</p>
        </div>
    </div>

    <div class="space-y-8 relative z-10">
        {{-- SECCIÓN SUPERIOR: DATOS GENERALES --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            {{-- 1. Fecha de Monitoreo --}}
            <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Fecha de Monitoreo</label>
                <div class="relative">
                    <input type="date" 
                           x-model="entidad.fecha_registro" 
                           class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 pl-4 font-bold text-sm text-slate-600 focus:ring-indigo-500 uppercase">
                </div>
            </div>

            {{-- 2. Cantidad Consultorios --}}
            <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Cantidad de Consultorios</label>
                <div class="relative">
                    <input type="number" 
                           min="0" 
                           x-model="entidad.consultorios" 
                           class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 pl-4 font-bold text-sm focus:ring-indigo-500">
                </div>
            </div>

            {{-- 3. Nombre del Consultorio (Dinámico) --}}
            <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Nombre del Consultorio</label>
                <div class="relative">
                    <input type="text" 
                           placeholder="{{ $placeholder }}"
                           x-model="entidad.nombre_consultorio" 
                           class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 pl-4 font-bold text-sm focus:ring-indigo-500 uppercase">
                </div>
            </div>

            {{-- 4. Turno --}}
            <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Turno</label>
                <div class="relative">
                    <select x-model="entidad.turno" 
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 pl-4 font-bold text-sm focus:ring-indigo-500 uppercase cursor-pointer">
                        <option value="" disabled>Seleccione...</option>
                        <option value="MAÑANA">MAÑANA</option>
                        <option value="TARDE">TARDE</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- SECCIÓN INFERIOR: ESPECÍFICOS (Solo Odontología y Psicología) --}}
        @if($tipo !== 'triaje')
        <div class="pt-6 border-t border-slate-100 animate-in fade-in zoom-in duration-300">
            <label class="block text-sm font-bold text-slate-800 mb-4 uppercase tracking-tight">Al iniciar cuenta con:</label>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                {{-- FUA (Común para Odonto y Psico) --}}
                <div class="bg-slate-50 p-5 rounded-2xl border border-slate-100 hover:shadow-md transition-shadow">
                    <span class="block text-[10px] font-black text-slate-400 uppercase mb-3 tracking-widest">FUA</span>
                    <div class="space-y-3">
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <input type="radio" value="ELECTRONICA" x-model="entidad.fua" class="w-4 h-4 text-indigo-600 focus:ring-indigo-500 border-slate-300">
                            <span class="text-xs font-bold text-slate-600 group-hover:text-indigo-600 transition-colors uppercase">FUA Electrónica</span>
                        </label>
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <input type="radio" value="MANUAL" x-model="entidad.fua" class="w-4 h-4 text-indigo-600 focus:ring-indigo-500 border-slate-300">
                            <span class="text-xs font-bold text-slate-600 group-hover:text-indigo-600 transition-colors uppercase">FUA Manual</span>
                        </label>
                    </div>
                </div>

                {{-- REFERENCIA (Común para Odonto y Psico) --}}
                <div class="bg-slate-50 p-5 rounded-2xl border border-slate-100 hover:shadow-md transition-shadow">
                    <span class="block text-[10px] font-black text-slate-400 uppercase mb-3 tracking-widest">Referencia</span>
                    <div class="space-y-3">
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <input type="radio" value="SIHCE" x-model="entidad.referencia" class="w-4 h-4 text-indigo-600 focus:ring-indigo-500 border-slate-300">
                            <span class="text-xs font-bold text-slate-600 group-hover:text-indigo-600 transition-colors uppercase">Por SIHCE</span>
                        </label>
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <input type="radio" value="DIRECTO REFCON" x-model="entidad.referencia" class="w-4 h-4 text-indigo-600 focus:ring-indigo-500 border-slate-300">
                            <span class="text-xs font-bold text-slate-600 group-hover:text-indigo-600 transition-colors uppercase">Directo a REFCON</span>
                        </label>
                    </div>
                </div>

                {{-- CAMPOS EXCLUSIVOS DE ODONTOLOGÍA --}}
                @if($tipo === 'odontologia')
                    {{-- Receta --}}
                    <div class="bg-slate-50 p-5 rounded-2xl border border-slate-100 hover:shadow-md transition-shadow">
                        <span class="block text-[10px] font-black text-slate-400 uppercase mb-3 tracking-widest">Receta</span>
                        <div class="space-y-3">
                            <label class="flex items-center gap-3 cursor-pointer group">
                                <input type="radio" value="SIHCE" x-model="entidad.receta" class="w-4 h-4 text-indigo-600 focus:ring-indigo-500 border-slate-300">
                                <span class="text-xs font-bold text-slate-600 group-hover:text-indigo-600 transition-colors uppercase">Por SIHCE</span>
                            </label>
                            <label class="flex items-center gap-3 cursor-pointer group">
                                <input type="radio" value="MANUAL" x-model="entidad.receta" class="w-4 h-4 text-indigo-600 focus:ring-indigo-500 border-slate-300">
                                <span class="text-xs font-bold text-slate-600 group-hover:text-indigo-600 transition-colors uppercase">Manual</span>
                            </label>
                        </div>
                    </div>

                    {{-- Orden Laboratorio --}}
                    <div class="bg-slate-50 p-5 rounded-2xl border border-slate-100 hover:shadow-md transition-shadow">
                        <span class="block text-[10px] font-black text-slate-400 uppercase mb-3 tracking-widest">Orden Laboratorio</span>
                        <div class="space-y-3">
                            <label class="flex items-center gap-3 cursor-pointer group">
                                <input type="radio" value="SIHCE" x-model="entidad.orden_lab" class="w-4 h-4 text-indigo-600 focus:ring-indigo-500 border-slate-300">
                                <span class="text-xs font-bold text-slate-600 group-hover:text-indigo-600 transition-colors uppercase">Por SIHCE</span>
                            </label>
                            <label class="flex items-center gap-3 cursor-pointer group">
                                <input type="radio" value="MANUAL" x-model="entidad.orden_lab" class="w-4 h-4 text-indigo-600 focus:ring-indigo-500 border-slate-300">
                                <span class="text-xs font-bold text-slate-600 group-hover:text-indigo-600 transition-colors uppercase">Manual</span>
                            </label>
                        </div>
                    </div>
                @endif
            </div>
        </div>
        @endif
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('documentosComponent', (entidadVinculada) => ({
                entidad: entidadVinculada
            }));
        });
    </script>
</div>