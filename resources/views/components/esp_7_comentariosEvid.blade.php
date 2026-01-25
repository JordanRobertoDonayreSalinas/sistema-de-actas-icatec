@props(['comentario' => null])

{{-- Contenedor Principal: Más compacto (p-6 en lugar de p-10) --}}
<div class="bg-white p-6 md:p-8 rounded-[2rem] shadow-xl shadow-slate-200/50 border border-slate-100 mb-6 relative overflow-hidden group/card transition-all hover:shadow-2xl hover:shadow-emerald-100/40">
    
    {{-- Detalle decorativo superior (más fino) --}}
    <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-emerald-400 via-teal-500 to-emerald-400"></div>

    {{-- SECCIÓN 1: COMENTARIOS --}}
    <div class="mb-6">
        {{-- Encabezado Compacto --}}
        <div class="flex items-center gap-3 mb-3">
            {{-- Icono más pequeño (w-10 h-10) --}}
            <div class="w-10 h-10 rounded-xl bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-600 shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 9a2 2 0 0 1-2 2H6l-4 4V4c0-1.1.9-2 2-2h8a2 2 0 0 1 2 2v5Z"/><path d="M18 9h2a2 2 0 0 1 2 2v11l-4-4h-6a2 2 0 0 1-2-2v-1"/></svg>
            </div>
            <div>
                <h3 class="text-slate-800 font-black text-sm uppercase tracking-tight leading-none">Comentarios</h3>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-0.5">Observaciones Técnicas</p>
            </div>
        </div>
        
        {{-- Textarea Compacto (rows="3", padding reducido) --}}
        <div class="relative group/input">
            <textarea 
                name="comentario_esp" 
                rows="3" 
                class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl p-4 text-slate-700 font-bold text-xs outline-none focus:bg-white focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all placeholder-slate-400 resize-none leading-relaxed shadow-inner"
                placeholder="ESCRIBA SUS OBSERVACIONES AQUÍ..."
            >{{ old('comentario_esp', $comentario->comentario_esp ?? '') }}</textarea>
            
            <div class="absolute bottom-3 right-4 text-slate-300 pointer-events-none group-focus-within/input:text-emerald-500 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
            </div>
        </div>
    </div>

    {{-- Separador fino --}}
    <div class="h-px w-full bg-slate-100 mb-6"></div>

    {{-- SECCIÓN 2: EVIDENCIA FOTOGRÁFICA --}}
    <div>
        <div class="flex items-center gap-3 mb-3">
            <div class="w-10 h-10 rounded-xl bg-teal-50 border border-teal-100 flex items-center justify-center text-teal-600 shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 4h-5L7 7H4a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-3l-2.5-3z"/><circle cx="12" cy="13" r="3"/></svg>
            </div>
            <div>
                <h3 class="text-slate-800 font-black text-sm uppercase tracking-tight leading-none">Evidencia Fotográfica</h3>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-0.5">Respaldo Visual</p>
            </div>
        </div>

        {{-- Dropzone Compacto (h-52 en lugar de h-80) --}}
        <div class="relative group w-full h-52">
            <input 
                type="file" 
                name="foto_esp_file" 
                id="foto_esp_input" 
                accept="image/*" 
                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-20"
                onchange="previewEspImage(event)"
            >
            
            <div id="dropzone_esp" class="bg-slate-50 border-2 border-dashed border-slate-200 rounded-2xl w-full h-full flex flex-col items-center justify-center group-hover:border-emerald-400 group-hover:bg-emerald-50/30 transition-all duration-300 relative overflow-hidden">
                
                {{-- Contenido Placeholder Compacto --}}
                <div id="placeholder_content" class="flex flex-row items-center gap-4 {{ isset($comentario->foto_url_esp) ? 'hidden' : '' }} p-4 transition-all group-hover:scale-105">
                    <div class="bg-white p-3 rounded-2xl shadow-md border border-slate-100">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="text-emerald-500"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>
                    </div>
                    <div class="text-left">
                        <span class="block text-xs font-black uppercase tracking-widest text-slate-600 group-hover:text-emerald-700">
                            Subir Foto
                        </span>
                        <span class="text-[10px] font-bold text-slate-400">JPG, PNG (Máx 5MB)</span>
                    </div>
                </div>

                {{-- Preview Imagen (Contain para que se vea completa en el espacio reducido) --}}
                <img 
                    id="img_preview_esp" 
                    src="{{ isset($comentario->foto_url_esp) ? asset('storage/' . $comentario->foto_url_esp) : '#' }}" 
                    class="{{ isset($comentario->foto_url_esp) ? '' : 'hidden' }} absolute inset-0 w-full h-full object-contain p-2 z-10"
                >
                
                {{-- Overlay Hover --}}
                <div class="{{ isset($comentario->foto_url_esp) ? '' : 'hidden' }} absolute inset-0 bg-slate-900/40 backdrop-blur-[1px] flex flex-col items-center justify-center z-10 opacity-0 group-hover:opacity-100 transition-all duration-300 pointer-events-none">
                    <div class="bg-white px-4 py-2 rounded-xl shadow-lg transform translate-y-2 group-hover:translate-y-0 transition-transform">
                        <span class="text-emerald-600 font-black text-[10px] uppercase tracking-widest flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" x2="12" y1="3" y2="15"/></svg>
                            Cambiar
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function previewEspImage(event) {
        const input = event.target;
        const preview = document.getElementById('img_preview_esp');
        const placeholder = document.getElementById('placeholder_content');
        const overlay = document.getElementById('dropzone_esp').querySelector('.bg-slate-900\\/40');

        if (input.files && input.files[0]) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.classList.remove('hidden');
                placeholder.classList.add('hidden');
                if(overlay) overlay.classList.remove('hidden');
            }
            
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>