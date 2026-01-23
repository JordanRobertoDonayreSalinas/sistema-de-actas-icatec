@props(['detalle', 'color' => 'indigo'])

{{-- 1. DEFINICIÓN DE COLORES Y TEMA --}}
@php
    // Definimos primero todos los estilos disponibles
    $allThemes = [
        'indigo' => [
            'bg-soft' => 'bg-indigo-50', 
            'border' => 'border-indigo-200', 
            'border-focus' => 'focus:border-indigo-500', 
            'ring-focus' => 'focus:ring-indigo-100', 
            'text' => 'text-indigo-600', 
            'text-dark' => 'text-indigo-900', 
            'checked-bg' => 'peer-checked:bg-gradient-to-br peer-checked:from-indigo-50 peer-checked:to-indigo-100/50', 
            'checked-border' => 'peer-checked:border-indigo-500', 
            'icon-checked' => 'peer-checked:bg-indigo-500 peer-checked:text-white'
        ],
        'teal' => [
            'bg-soft' => 'bg-teal-50', 
            'border' => 'border-teal-200', 
            'border-focus' => 'focus:border-teal-500', 
            'ring-focus' => 'focus:ring-teal-100', 
            'text' => 'text-teal-600', 
            'text-dark' => 'text-teal-900', 
            'checked-bg' => 'peer-checked:bg-gradient-to-br peer-checked:from-teal-50 peer-checked:to-teal-100/50', 
            'checked-border' => 'peer-checked:border-teal-500', 
            'icon-checked' => 'peer-checked:bg-teal-500 peer-checked:text-white'
        ],
        'blue' => [
            'bg-soft' => 'bg-blue-50', 
            'border' => 'border-blue-200', 
            'border-focus' => 'focus:border-blue-500', 
            'ring-focus' => 'focus:ring-blue-100', 
            'text' => 'text-blue-600', 
            'text-dark' => 'text-blue-900', 
            'checked-bg' => 'peer-checked:bg-gradient-to-br peer-checked:from-blue-50 peer-checked:to-blue-100/50', 
            'checked-border' => 'peer-checked:border-blue-500', 
            'icon-checked' => 'peer-checked:bg-blue-500 peer-checked:text-white'
        ],
        'purple' => [
            'bg-soft' => 'bg-purple-50', 
            'border' => 'border-purple-200', 
            'border-focus' => 'focus:border-purple-500', 
            'ring-focus' => 'focus:ring-purple-100', 
            'text' => 'text-purple-600', 
            'text-dark' => 'text-purple-900', 
            'checked-bg' => 'peer-checked:bg-gradient-to-br peer-checked:from-purple-50 peer-checked:to-purple-100/50', 
            'checked-border' => 'peer-checked:border-purple-500', 
            'icon-checked' => 'peer-checked:bg-purple-500 peer-checked:text-white'
        ]
    ];

    // Seleccionamos el tema actual o usamos 'indigo' por defecto
    $theme = $allThemes[$color] ?? $allThemes['indigo'];
@endphp

<div id="seccion_dni_firma" class="bg-white rounded-[2.5rem] p-8 shadow-xl shadow-slate-200/40 border border-slate-100 mb-8 transition-all duration-300 hover:shadow-2xl hover:shadow-slate-200/60 {{ data_get($detalle->contenido, 'profesional.tipo_doc', 'DNI') !== 'DNI' && data_get($detalle->contenido, 'profesional.tipo_doc') !== null ? 'hidden' : '' }} seccion-numerada group/dniblock">
    
    {{-- HEADER --}}
    <div class="flex items-center gap-5 mb-10 border-b border-slate-50 pb-6">
        <div class="h-14 w-14 {{ $theme['bg-soft'] }} {{ $theme['text'] }} rounded-2xl flex items-center justify-center shadow-sm border {{ $theme['border'] }} transition-transform duration-300 group-hover/dniblock:scale-105">
            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M16 10h2"/><path d="M16 14h2"/><path d="M6.17 15a3 3 0 0 1 5.66 0"/><circle cx="9" cy="11" r="2"/><rect x="2" y="5" width="20" height="14" rx="2"/>
            </svg>
        </div>
        <div>
            <h3 class="text-xl font-black text-slate-800 uppercase tracking-tight leading-tight">Detalle DNI y Firma Digital</h3>
            <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mt-1">Validación de identidad digital</p>
        </div>
    </div>
    
    {{-- TIPO DE DOCUMENTO --}}
    <div class="mb-10">
        <label class="block text-[11px] font-black text-slate-400 uppercase tracking-widest mb-5 ml-2 flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m12 19 7-7 3 3-7 7-3-3z"/><path d="m18 13-1.5-7.5L2 2l3.5 14.5L13 18l5-5z"/><path d="m2 2 7.5 8.6L13 18l5-5 3 3"/></svg>
            Seleccione el Tipo de Documento
        </label>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            {{-- OPCIÓN A: DNI ELECTRÓNICO --}}
            <label class="relative cursor-pointer group perspective-1000">
                <input type="radio" name="contenido[tipo_dni_fisico]" value="ELECTRONICO" class="peer sr-only" onchange="toggleDniFields()" {{ data_get($detalle->contenido, "tipo_dni_fisico") == 'ELECTRONICO' ? 'checked' : '' }}>
                <div class="p-5 rounded-[1.5rem] border-2 bg-white transition-all duration-300 relative overflow-hidden border-slate-200 hover:{{ $theme['border'] }} hover:shadow-md hover:-translate-y-1 {{ $theme['checked-border'] }} {{ $theme['checked-bg'] }} peer-checked:shadow-lg peer-checked:ring-2 peer-checked:ring-offset-2 {{ str_replace('focus:', 'peer-checked:', $theme['ring-focus']) }}">
                    <div class="flex items-center gap-5 relative z-10">
                        <div class="w-14 h-14 rounded-2xl flex items-center justify-center transition-all duration-300 bg-slate-100 text-slate-400 border border-slate-200 group-hover:{{ $theme['text'] }} {{ $theme['icon-checked'] }} peer-checked:border-transparent peer-checked:shadow-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2"/><path d="M8.5 7v10"/><path d="M15.5 7v10"/><path d="M3 8.5h5"/><path d="M3 15.5h5"/><path d="M16 8.5h5"/><path d="M16 15.5h5"/></svg>
                        </div>
                        <div class="flex-1">
                            <span class="block text-sm font-black text-slate-700 uppercase transition-colors peer-checked:{{ $theme['text-dark'] }}">DNI Electrónico</span>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mt-1 block peer-checked:{{ $theme['text'] }}">Con Chip Digital</span>
                        </div>
                        <div class="opacity-0 scale-50 transition-all duration-300 ease-out peer-checked:opacity-100 peer-checked:scale-100 {{ $theme['text'] }}">
                            <div class="bg-white rounded-full p-1.5 shadow-sm border {{ $theme['border'] }}">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                            </div>
                        </div>
                    </div>
                </div>
            </label>

            {{-- OPCIÓN B: DNI AZUL --}}
            <label class="relative cursor-pointer group perspective-1000">
                <input type="radio" name="contenido[tipo_dni_fisico]" value="AZUL" class="peer sr-only" onchange="toggleDniFields()" {{ data_get($detalle->contenido, "tipo_dni_fisico") == 'AZUL' ? 'checked' : '' }}>
                <div class="p-5 rounded-[1.5rem] border-2 bg-white transition-all duration-300 relative overflow-hidden border-slate-200 hover:border-blue-300 hover:shadow-md hover:-translate-y-1 peer-checked:border-blue-500 peer-checked:bg-gradient-to-br peer-checked:from-blue-50 peer-checked:to-blue-100/50 peer-checked:shadow-lg peer-checked:ring-2 peer-checked:ring-offset-2 peer-checked:ring-blue-100">
                    <div class="flex items-center gap-5 relative z-10">
                        <div class="w-14 h-14 rounded-2xl flex items-center justify-center transition-all duration-300 bg-slate-100 text-slate-400 border border-slate-200 group-hover:text-blue-500 peer-checked:bg-blue-500 peer-checked:text-white peer-checked:border-transparent peer-checked:shadow-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="12" x="2" y="6" rx="2"/></svg>
                        </div>
                        <div class="flex-1">
                            <span class="block text-sm font-black text-slate-700 uppercase transition-colors peer-checked:text-blue-900">DNI Azul</span>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mt-1 block peer-checked:text-blue-700">Sin Chip</span>
                        </div>
                        <div class="opacity-0 scale-50 transition-all duration-300 ease-out peer-checked:opacity-100 peer-checked:scale-100 text-blue-600">
                            <div class="bg-white rounded-full p-1.5 shadow-sm border border-blue-200">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                            </div>
                        </div>
                    </div>
                </div>
            </label>
        </div>
    </div>

    {{-- BLOQUE CONDICIONAL DNIe --}}
    <div id="block-info-dnie" class="hidden animate-fade-in-down mb-10">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 p-8 bg-slate-50/80 backdrop-blur-sm rounded-[2rem] border border-slate-200/80 relative overflow-hidden">
            
            {{-- SELECT VERSIÓN --}}
            <div class="relative z-10">
                <label class="block text-[11px] font-black text-slate-400 uppercase tracking-widest mb-3 ml-1">Versión del DNIe</label>
                <div class="relative group/select">
                    <select name="contenido[dnie_version]" class="appearance-none w-full pl-5 pr-12 py-4 bg-white border-2 border-slate-200 rounded-2xl font-bold text-xs outline-none transition-all shadow-sm cursor-pointer {{ $theme['border-focus'] }} focus:ring-4 {{ $theme['ring-focus'] }} text-slate-700 uppercase relative z-10">
                        <option value="">-- SELECCIONE --</option>
                        <option value="1.0" {{ data_get($detalle->contenido, "dnie_version") == '1.0' ? 'selected' : '' }}>VERSIÓN 1.0</option>
                        <option value="2.0" {{ data_get($detalle->contenido, "dnie_version") == '2.0' ? 'selected' : '' }}>VERSIÓN 2.0</option>
                        <option value="3.0" {{ data_get($detalle->contenido, "dnie_version") == '3.0' ? 'selected' : '' }}>VERSIÓN 3.0</option>
                    </select>
                    <div class="absolute right-5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none z-0 transition-colors group-hover/select:{{ $theme['text'] }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
                    </div>
                </div>
            </div>

            {{-- TOGGLE SÍ/NO --}}
            <div class="relative z-10">
                <label class="block text-[11px] font-black text-slate-400 uppercase tracking-widest mb-3 ml-1">¿Firma Digitalmente en SIHCE?</label>
                <div class="flex gap-4 h-[54px]">
                    <label class="flex-1 relative cursor-pointer group">
                        <input type="radio" name="contenido[dnie_firma_sihce]" value="SI" {{ data_get($detalle->contenido, "dnie_firma_sihce") == 'SI' ? 'checked' : '' }} class="peer sr-only">
                        <div class="h-full w-full flex items-center justify-center gap-2 rounded-2xl border-2 border-slate-200 bg-white transition-all hover:border-emerald-300 hover:bg-emerald-50/20 peer-checked:bg-emerald-500 peer-checked:border-emerald-500 peer-checked:text-white peer-checked:shadow-lg peer-checked:shadow-emerald-200/50">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-emerald-500 peer-checked:text-white transition-colors"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="M22 4 12 14.01l-3-3"/></svg>
                            <span class="text-xs font-black text-slate-600 peer-checked:text-white transition-colors uppercase tracking-wider">SÍ, FIRMA</span>
                        </div>
                    </label>
                    <label class="flex-1 relative cursor-pointer group">
                        <input type="radio" name="contenido[dnie_firma_sihce]" value="NO" {{ data_get($detalle->contenido, "dnie_firma_sihce") == 'NO' ? 'checked' : '' }} class="peer sr-only">
                        <div class="h-full w-full flex items-center justify-center gap-2 rounded-2xl border-2 border-slate-200 bg-white transition-all hover:border-red-300 hover:bg-red-50/20 peer-checked:bg-red-500 peer-checked:border-red-500 peer-checked:text-white peer-checked:shadow-lg peer-checked:shadow-red-200/50">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-red-500 peer-checked:text-white transition-colors"><circle cx="12" cy="12" r="10"/><path d="m15 9-6 6"/><path d="m9 9 6 6"/></svg>
                            <span class="text-xs font-black text-slate-600 peer-checked:text-white transition-colors uppercase tracking-wider">NO FIRMA</span>
                        </div>
                    </label>
                </div>
            </div>
        </div>
    </div>

    {{-- OBSERVACIONES --}}
    <div class="relative">
        <label class="block text-[11px] font-black text-slate-400 uppercase tracking-widest mb-3 ml-2 flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/><line x1="9" x2="15" y1="10" y2="10"/><line x1="12" x2="12" y1="7" y2="13"/></svg>
            Observaciones
        </label>
        <div class="relative group/textarea">
            <textarea name="contenido[dni_observacion]" 
                      rows="2"
                      class="w-full px-6 py-4 bg-slate-50 border-2 border-slate-200 rounded-3xl font-bold text-xs outline-none {{ $theme['border-focus'] }} focus:bg-white focus:ring-4 {{ $theme['ring-focus'] }} text-slate-700 uppercase placeholder:text-slate-400 transition-all resize-none shadow-inner"
                      placeholder="... descripción">{{ data_get($detalle->contenido, "dni_observacion") }}</textarea>
            <div class="absolute bottom-4 right-5 text-slate-300 pointer-events-none transition-colors group-focus-within/textarea:{{ $theme['text'] }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
            </div>
        </div>
    </div>
</div>

<script>
    if (typeof window.toggleDniFields === 'undefined') {
        window.toggleDniFields = function(isInitialLoad = false) {
            const tipoDniInput = document.querySelector(`input[name="contenido[tipo_dni_fisico]"]:checked`);
            const tipoDni = tipoDniInput ? tipoDniInput.value : null;
            const blockDnie = document.getElementById('block-info-dnie');
            const selectVersion = document.querySelector(`select[name="contenido[dnie_version]"]`);
            const radiosFirma = document.querySelectorAll(`input[name="contenido[dnie_firma_sihce]"]`);
            const txtObservacion = document.querySelector(`textarea[name="contenido[dni_observacion]"]`);

            if (!blockDnie) return;

            if (tipoDni === 'ELECTRONICO') {
                blockDnie.classList.remove('hidden');
                if (!isInitialLoad && txtObservacion) txtObservacion.value = '';
            } else if (tipoDni === 'AZUL') {
                blockDnie.classList.add('hidden');
                if (!isInitialLoad) {
                    if (selectVersion) selectVersion.value = ''; 
                    if(radiosFirma) radiosFirma.forEach(r => r.checked = false);
                }
            } else {
                blockDnie.classList.add('hidden');
            }
        };
        window.initDniFirmaVisibility = function() {
            const sectionDni = document.getElementById('seccion_dni_firma');
            const inputTipoDoc = document.getElementById('profesional_tipo_doc') || document.querySelector('[name="contenido[profesional][tipo_doc]"]') || document.querySelector('select[name*="tipo_doc"]');
            function checkTipoDoc() {
                if (!inputTipoDoc || !sectionDni) return;
                const valor = inputTipoDoc.value.toUpperCase().trim();
                sectionDni.classList.toggle('hidden', valor !== 'DNI');
                if (typeof actualizarCorrelativo === 'function') actualizarCorrelativo();
            }
            if (inputTipoDoc) {
                checkTipoDoc();
                inputTipoDoc.addEventListener('change', checkTipoDoc);
                setInterval(checkTipoDoc, 1000); 
            }
        };
    }
    document.addEventListener('DOMContentLoaded', () => {
        if(typeof toggleDniFields === 'function') toggleDniFields(true);
        if(typeof initDniFirmaVisibility === 'function') initDniFirmaVisibility();
    });
</script>