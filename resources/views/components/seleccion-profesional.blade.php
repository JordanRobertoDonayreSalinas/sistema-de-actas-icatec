@props([
    'titulo' => 'Datos del Profesional',
    'subtitulo' => 'Responsable',
    'model' => 'form.profesional' // Valor por defecto del scope de Alpine
])

<div class="bg-white border border-slate-200 rounded-[2.5rem] p-8 shadow-xl shadow-slate-200/50 relative overflow-hidden">
    {{-- Decoración --}}
    <div class="absolute top-0 right-0 w-32 h-32 bg-indigo-50 rounded-full -mr-16 -mt-16 opacity-60 pointer-events-none"></div>
    
    <div class="relative z-10">
        <div class="flex items-center gap-4 mb-8">
            <div class="h-12 w-12 rounded-2xl bg-indigo-600 flex items-center justify-center shadow-lg shadow-indigo-200">
                <i data-lucide="user-cog" class="text-white w-6 h-6"></i>
            </div>
            <div>
                <h3 class="text-lg font-black text-slate-900 uppercase tracking-tight">{{ $titulo }}</h3>
                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">{{ $subtitulo }}</p>
            </div>
        </div>

        <div class="space-y-6">
            {{-- Buscador DNI --}}
            <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
                <div class="md:col-span-4">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Tipo Doc.</label>
                    <select x-model="{{ $model }}.tipo_doc" class="w-full bg-slate-50 border border-slate-200 text-slate-700 text-sm rounded-xl font-bold uppercase p-3">
                        <option value="DNI">DNI</option>
                        <option value="CE">C.E.</option>
                    </select>
                </div>
                <div class="md:col-span-8 relative">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Número Documento</label>
                    <div class="flex gap-2">
                        {{-- Nota: La función buscarProfesional() debe existir en el scope padre o pasarse como evento --}}
                        <input type="text" x-model="{{ $model }}.doc" @keydown.enter.prevent="buscarProfesional()" placeholder="Ingrese DNI..." 
                               class="flex-1 bg-slate-50 border border-slate-200 text-slate-900 text-sm rounded-xl p-3 font-bold tracking-wider">
                        <button type="button" @click="buscarProfesional()" class="bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl px-4 py-2 transition-colors">
                            <i data-lucide="search" class="w-5 h-5"></i>
                        </button>
                    </div>
                    <span x-show="msgProfesional" x-text="msgProfesional" class="absolute -bottom-5 left-0 text-[10px] font-bold text-emerald-500"></span>
                </div>
            </div>

            {{-- Datos Personales --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Apellido Paterno</label>
                    <input type="text" x-model="{{ $model }}.apellido_paterno" class="w-full bg-white border border-slate-200 rounded-xl p-3 font-bold uppercase">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Apellido Materno</label>
                    <input type="text" x-model="{{ $model }}.apellido_materno" class="w-full bg-white border border-slate-200 rounded-xl p-3 font-bold uppercase">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Nombres</label>
                    <input type="text" x-model="{{ $model }}.nombres" class="w-full bg-white border border-slate-200 rounded-xl p-3 font-bold uppercase">
                </div>
            </div>
             {{-- Contacto --}}
             <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Email</label>
                    <input type="email" x-model="{{ $model }}.email" class="w-full bg-white border border-slate-200 rounded-xl p-3 font-medium lowercase">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Teléfono</label>
                    <input type="text" x-model="{{ $model }}.telefono" class="w-full bg-white border border-slate-200 rounded-xl p-3 font-bold">
                </div>
            </div>
        </div>
    </div>
</div>