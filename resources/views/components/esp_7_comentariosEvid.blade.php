@props(['comentario' => null])

{{-- 
    CAMBIO VISUAL PRINCIPAL:
    - De fondo oscuro (bg-teal-900) a una tarjeta blanca limpia con sombra suave.
    - Bordes redondeados un poco más sutiles (3xl en lugar de 3rem).
--}}
<div class="bg-white p-8 md:p-10 rounded-3xl shadow-[0_20px_50px_rgba(8,_112,_184,_0.07)] border border-slate-100 mb-8 relative overflow-hidden">
    
    {{-- Un detalle visual opcional: una barra superior verde elegante --}}
    <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-emerald-500 to-teal-500"></div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 pt-4">
        
        {{-- COLUMNA 1: COMENTARIOS --}}
        <div>
            {{-- Encabezado más limpio con colores esmeralda --}}
            <div class="flex items-center gap-4 mb-6">
                <span class="bg-emerald-100 text-emerald-700 w-10 h-10 flex items-center justify-center rounded-full font-black text-sm shadow-sm">7</span>
                <h3 class="text-slate-800 font-bold text-lg uppercase tracking-tight">COMENTARIOS DEL ESPECIALISTA</h3>
            </div>
            
            {{-- Textarea moderno: fondo claro, borde que se ilumina en verde al enfocar --}}
            <div class="relative">
                <textarea 
                    name="comentario_esp" 
                    rows="8" 
                    class="w-full bg-slate-50 border border-slate-200 rounded-2xl p-5 text-slate-700 font-medium outline-none focus:bg-white focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/20 transition-all placeholder-slate-400 resize-none leading-relaxed shadow-sm peer"
                    placeholder="Escriba sus observaciones detalladas aquí..."
                >{{ old('comentario_esp', $comentario->comentario_esp ?? '') }}</textarea>
                {{-- Icono decorativo en esquina --}}
                <div class="absolute top-4 right-4 text-slate-300 peer-focus:text-emerald-500 transition-colors pointer-events-none">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                </div>
            </div>
        </div>

        {{-- COLUMNA 2: EVIDENCIA FOTOGRÁFICA --}}
        <div>
            <div class="flex items-center gap-4 mb-6">
                <span class="bg-emerald-100 text-emerald-700 w-10 h-10 flex items-center justify-center rounded-full font-black text-sm shadow-sm">8</span>
                <h3 class="text-slate-800 font-bold text-lg uppercase tracking-tight">EVIDENCIA FOTOGRÁFICA</h3>
            </div>

            <div class="relative group w-full h-[17rem]">
                {{-- Input File Oculto (Misma lógica) --}}
                <input 
                    type="file" 
                    name="foto_esp_file" 
                    id="foto_esp_input" 
                    accept="image/*" 
                    class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-20"
                    onchange="previewEspImage(event)"
                >
                
                {{-- Dropzone Visualmente Mejorado --}}
                <div id="dropzone_esp" class="bg-slate-50 border-2 border-dashed border-slate-300 rounded-3xl w-full h-full flex flex-col items-center justify-center group-hover:border-emerald-500 group-hover:bg-emerald-50/50 transition-all relative overflow-hidden shadow-sm">
                    
                    {{-- Estado Inicial --}}
                    <div id="placeholder_content" class="flex flex-col items-center justify-center {{ isset($comentario->foto_url_esp) ? 'hidden' : '' }} p-6 text-center transition-all group-hover:scale-105">
                        <div class="bg-white p-4 rounded-full shadow-md mb-4 group-hover:shadow-emerald-200/50 transition-shadow">
                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="text-emerald-500"><path d="M4 14.899A7 7 0 1 1 15.71 8h1.79a4.5 4.5 0 0 1 2.5 8.242"/><path d="M12 12v9"/><path d="m16 16-4-4-4 4"/></svg>
                        </div>
                        <span class="text-sm font-bold uppercase tracking-wider text-slate-600 group-hover:text-emerald-700 mb-1">
                            Haga clic o arrastre una imagen
                        </span>
                        <span class="text-xs text-slate-400">Soporta: JPG, PNG (Máx 5MB)</span>
                    </div>

                    {{-- Previsualización de Imagen --}}
                    <img 
                        id="img_preview_esp" 
                        src="{{ isset($comentario->foto_url_esp) ? asset('storage/' . $comentario->foto_url_esp) : '#' }}" 
                        class="{{ isset($comentario->foto_url_esp) ? '' : 'hidden' }} absolute inset-0 w-full h-full object-cover rounded-3xl z-10 border-4 border-white shadow-inner"
                    >
                    
                    {{-- Overlay al hacer hover con imagen cargada --}}
                    <div class="{{ isset($comentario->foto_url_esp) ? '' : 'hidden' }} absolute inset-0 bg-black/60 flex flex-col items-center justify-center z-10 opacity-0 group-hover:opacity-100 transition-opacity rounded-3xl backdrop-blur-sm pointer-events-none">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-white mb-2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" x2="12" y1="3" y2="15"/></svg>
                        <span class="text-white font-bold text-sm uppercase tracking-widest">Cambiar Imagen</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- El script de JS se mantiene exactamente igual --}}
<script>
    function previewEspImage(event) {
        const input = event.target;
        const preview = document.getElementById('img_preview_esp');
        const placeholder = document.getElementById('placeholder_content');
        const overlay = document.getElementById('dropzone_esp').querySelector('.bg-black\\/60');

        if (input.files && input.files[0]) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.classList.remove('hidden');
                placeholder.classList.add('hidden');
                // Asegurar que el overlay se muestre al hacer hover
                if(overlay) overlay.classList.remove('hidden');
            }
            
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>