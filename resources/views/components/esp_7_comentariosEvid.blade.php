@props(['comentario' => null])

<div class="bg-teal-900 rounded-[3rem] p-10 shadow-2xl text-white mb-8">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
        
        {{-- COLUMNA 1: COMENTARIOS --}}
        <div>
            <div class="flex items-center gap-3 mb-6">
                <span class="bg-teal-500 text-white w-8 h-8 flex items-center justify-center rounded-full font-black text-sm">7</span>
                <h3 class="text-white font-black text-lg uppercase tracking-tight">COMENTARIOS</h3>
            </div>
            
            {{-- Name coincide con tu $fillable: comentario_esp --}}
            <textarea 
                name="comentario_esp" 
                rows="8" 
                class="w-full bg-white/10 border-2 border-white/20 rounded-2xl p-4 text-white font-bold outline-none focus:border-teal-500 transition-all uppercase placeholder-white/30 resize-none"
                placeholder="ESCRIBA SU COMENTARIO AQUÍ..."
            >{{ old('comentario_esp', $comentario->comentario_esp ?? '') }}</textarea>
        </div>

        {{-- COLUMNA 2: EVIDENCIA FOTOGRÁFICA --}}
        <div>
            <div class="flex items-center gap-3 mb-6">
                <span class="bg-teal-500 text-white w-8 h-8 flex items-center justify-center rounded-full font-black text-sm">8</span>
                <h3 class="text-white font-black text-lg uppercase tracking-tight">EVIDENCIA FOTOGRÁFICA</h3>
            </div>

            <div class="relative group w-full h-64">
                {{-- Input File Oculto --}}
                <input 
                    type="file" 
                    name="foto_esp_file" 
                    id="foto_esp_input" 
                    accept="image/*" 
                    class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-20"
                    onchange="previewEspImage(event)"
                >
                
                {{-- Contenedor Visual (Dropzone) --}}
                <div id="dropzone_esp" class="bg-white/5 border-2 border-dashed border-white/20 rounded-[2rem] w-full h-full flex flex-col items-center justify-center group-hover:bg-white/10 transition-all shadow-inner relative overflow-hidden">
                    
                    {{-- Estado Inicial: Mostrar Icono o Imagen Existente --}}
                    <div id="placeholder_content" class="flex flex-col items-center justify-center {{ isset($comentario->foto_url_esp) ? 'hidden' : '' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-10 h-10 text-teal-400 mb-3"><path d="M4 14.899A7 7 0 1 1 15.71 8h1.79a4.5 4.5 0 0 1 2.5 8.242"/><path d="M12 12v9"/><path d="m16 16-4-4-4 4"/></svg>
                        <span class="text-[10px] font-bold uppercase tracking-widest text-slate-300 text-center">
                            SUBIR FOTO
                        </span>
                    </div>

                    {{-- Previsualización de Imagen (DB o Nueva) --}}
                    <img 
                        id="img_preview_esp" 
                        src="{{ isset($comentario->foto_url_esp) ? asset('storage/' . $comentario->foto_url_esp) : '#' }}" 
                        class="{{ isset($comentario->foto_url_esp) ? '' : 'hidden' }} absolute inset-0 w-full h-full object-cover p-2 rounded-[2.5rem]"
                    >
                    
                    {{-- Overlay al hacer hover con imagen cargada --}}
                    <div class="absolute inset-0 bg-black/50 hidden group-hover:flex items-center justify-center z-10 pointer-events-none">
                        <span class="text-white font-bold text-xs uppercase tracking-widest">Cambiar Imagen</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Script simple para manejar la previsualización localmente --}}
<script>
    function previewEspImage(event) {
        const input = event.target;
        const preview = document.getElementById('img_preview_esp');
        const placeholder = document.getElementById('placeholder_content');

        if (input.files && input.files[0]) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.classList.remove('hidden');
                placeholder.classList.add('hidden');
            }
            
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>