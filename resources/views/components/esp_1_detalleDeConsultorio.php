{{-- Archivo: resources/views/components/detalle-consultorio.blade.php --}}
@props(['detalle'])

{{-- 
    ESTRUCTURA ADAPTADA:
    Usamos el mismo contenedor "card" que el componente de Soporte.
    Header integrado con fondo suave y cuerpo con grid.
--}}
<section
    {{ $attributes->merge(['class' => 'bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden mb-8']) }}>

    {{-- 1. ENCABEZADO INTEGRADO --}}
    <div class="px-6 py-4 border-b border-slate-100 flex items-center gap-3 bg-slate-50/50">
        {{-- Icono (Indigo para Consultorio) --}}
        <div class="p-2 bg-indigo-50 text-indigo-600 rounded-lg border border-indigo-100 shadow-sm">
            <i data-lucide="stethoscope" class="w-5 h-5"></i>
        </div>
        <div>
            <h2 class="text-lg font-bold text-slate-800 leading-tight">Detalles del Consultorio</h2>
            <p class="text-xs text-slate-500 font-medium">Registro del ambiente</p>
        </div>
    </div>

    {{-- 2. CUERPO DEL CONTENIDO --}}
    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-8">

        {{-- COLUMNA 1 --}}
        <div class="space-y-6">

            {{-- FECHA --}}
            <div>
                <label
                    class="block text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-3 flex items-center gap-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-slate-300"></span>
                    Fecha de Monitoreo
                </label>
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i data-lucide="calendar"
                            class="h-4 w-4 text-slate-400 group-focus-within:text-indigo-500 transition-colors"></i>
                    </div>
                    <input type="date" name="contenido[fecha_monitoreo_medicina]"
                        value="{{ $detalle->contenido['fecha_monitoreo_medicina'] ?? date('Y-m-d') }}"
                        class="w-full pl-10 pr-4 py-2.5 bg-white border border-slate-200 rounded-lg text-sm font-bold text-slate-600 outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all uppercase cursor-pointer shadow-sm">
                </div>
            </div>

            {{-- NRO CONSULTORIOS --}}
            <div>
                <label
                    class="block text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-3 flex items-center gap-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-slate-300"></span>
                    Nro. de Consultorios
                </label>
                <input type="number" name="contenido[num_consultorios]" min="0"
                    onkeydown="return event.keyCode !== 69 && event.keyCode !== 189"
                    oninput="this.value = Math.abs(this.value)"
                    value="{{ $detalle->contenido['num_consultorios'] ?? '' }}"
                    class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-lg text-sm font-bold text-slate-600 outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all text-center shadow-sm placeholder:text-slate-300 placeholder:font-normal"
                    placeholder="EJ: 1">
            </div>

        </div>

        {{-- COLUMNA 2 --}}
        <div class="space-y-6">

            {{-- TURNO --}}
            <div>
                <label
                    class="block text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-3 flex items-center gap-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-slate-300"></span>
                    Turno
                </label>
                <div class="flex gap-3">
                    {{-- MAÑANA --}}
                    <label class="flex-1 relative cursor-pointer group">
                        <input type="radio" name="contenido[turno]" value="MAÑANA"
                            {{ ($detalle->contenido['turno'] ?? '') == 'MAÑANA' ? 'checked' : '' }}
                            class="peer sr-only">
                        <div
                            class="py-2.5 px-2 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 transition-all peer-checked:border-amber-400 peer-checked:bg-amber-50 peer-checked:text-amber-700 peer-checked:shadow-sm flex items-center justify-center gap-2">
                            <i data-lucide="sun" class="w-4 h-4 text-slate-400 peer-checked:text-amber-600"></i>
                            <span
                                class="text-[10px] font-bold text-slate-600 peer-checked:text-amber-800 uppercase">MAÑANA</span>
                        </div>
                        {{-- Icono Check Flotante (Estilo Soporte) --}}
                        <div
                            class="absolute -top-1.5 -right-1.5 bg-amber-500 text-white rounded-full p-0.5 opacity-0 peer-checked:opacity-100 transition-all transform scale-50 peer-checked:scale-100 shadow-sm z-10">
                            <i data-lucide="check" class="w-2 h-2"></i>
                        </div>
                    </label>

                    {{-- TARDE --}}
                    <label class="flex-1 relative cursor-pointer group">
                        <input type="radio" name="contenido[turno]" value="TARDE"
                            {{ ($detalle->contenido['turno'] ?? '') == 'TARDE' ? 'checked' : '' }} class="peer sr-only">
                        <div
                            class="py-2.5 px-2 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 transition-all peer-checked:border-indigo-500 peer-checked:bg-indigo-50 peer-checked:text-indigo-700 peer-checked:shadow-sm flex items-center justify-center gap-2">
                            <i data-lucide="sunset" class="w-4 h-4 text-slate-400 peer-checked:text-indigo-600"></i>
                            <span
                                class="text-[10px] font-bold text-slate-600 peer-checked:text-indigo-800 uppercase">TARDE</span>
                        </div>
                        {{-- Icono Check Flotante --}}
                        <div
                            class="absolute -top-1.5 -right-1.5 bg-indigo-600 text-white rounded-full p-0.5 opacity-0 peer-checked:opacity-100 transition-all transform scale-50 peer-checked:scale-100 shadow-sm z-10">
                            <i data-lucide="check" class="w-2 h-2"></i>
                        </div>
                    </label>
                </div>
            </div>

            {{-- DENOMINACIÓN --}}
            <div>
                <label
                    class="block text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-3 flex items-center gap-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-slate-300"></span>
                    Denominación
                </label>
                <input type="text" name="contenido[denominacion_consultorio]"
                    value="{{ $detalle->contenido['denominacion_consultorio'] ?? '' }}"
                    class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-lg text-sm font-bold text-slate-600 outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all uppercase shadow-sm placeholder:text-slate-300 placeholder:font-normal"
                    placeholder="EJ: Consultorio 01">
            </div>

        </div>

    </div>
</section>
