@extends('layouts.usuario')

@section('title', 'Mesa de Ayuda SIHCE – Reportar Incidencia')

@section('header-content')
    <h1 class="text-xl font-bold text-slate-800 tracking-tight">Mesa de Ayuda</h1>
    <div class="flex items-center gap-2 text-xs text-slate-500 mt-0.5">
        <span>Plataforma</span>
        <span class="text-slate-300">•</span>
        <span>Formulario de Reporte</span>
    </div>
@endsection

@push('styles')
    <style>
        body {
            background-color: #f8fafc;
        }

        .input-field {
            display: block;
            width: 100%;
            border: 1px solid #cbd5e1;
            border-radius: 0.75rem;
            padding: 0.75rem 1rem;
            font-size: 0.875rem;
            font-weight: 500;
            color: #334155;
            background: #f8fafc;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .input-field:focus {
            outline: none;
            box-shadow: 0 4px 14px 0 rgba(249, 115, 22, 0.15), 0 0 0 3px rgba(249, 115, 22, 0.1);
            border-color: #f97316;
            background: #ffffff;
        }

        .input-field::placeholder {
            color: #94a3b8;
            font-weight: 400;
        }

        .label-field {
            display: block;
            font-size: 0.65rem;
            font-weight: 800;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: .05em;
            margin-bottom: 0.5rem;
        }

        /* Estilos Premium para las Cards */
        .premium-card {
            background: white;
            border-radius: 1.25rem;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.05), 0 2px 4px -2px rgb(0 0 0 / 0.05);
            transition: all 0.3s ease;
            overflow: hidden;
        }

        .premium-card:hover {
            box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.08), 0 4px 6px -4px rgb(0 0 0 / 0.05);
            border-color: #cbd5e1;
        }
    </style>
@endpush

@section('content')
    <div class="max-w-4xl mx-auto space-y-8 py-6">

        {{-- HERO --}}
        <div class="text-center mb-2">
            <div
                class="inline-flex items-center gap-2 bg-orange-50 text-orange-700 text-xs font-bold px-4 py-1.5 rounded-full border border-orange-200 mb-3">
                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                        clip-rule="evenodd" />
                </svg>
                Formulario de Reporte de Incidencia Técnica
            </div>
            <p class="text-sm text-slate-500 max-w-xl mx-auto">
                Complete el siguiente formulario con los datos del problema. El equipo técnico de implementación
                revisará su reporte y le brindará soporte a la brevedad.
            </p>
        </div>

        {{-- FORM --}}
        <form id="formIncidencia" class="space-y-8" enctype="multipart/form-data">
            @csrf

            {{-- SECCIÓN 1: Datos del profesional --}}
            <div class="premium-card">
                <div class="px-7 py-5 border-b border-slate-100 bg-gradient-to-r from-slate-50 to-white flex items-center gap-4">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 shadow-md shadow-emerald-200 text-white flex items-center justify-center">
                        <i data-lucide="user-check" class="w-5 h-5"></i>
                    </div>
                    <h2 class="text-base font-extrabold text-slate-800 tracking-tight uppercase">1. Datos del Profesional</h2>
                </div>
                <div class="p-7 space-y-6">
                    {{-- DNI SEARCH ROW --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-end">
                        <div class="space-y-2">
                            <label class="label-field">Documento de Identidad (DNI) *</label>
                            <div class="flex gap-3 items-stretch">
                                <div class="relative flex-1 group">
                                    <i data-lucide="id-card"
                                        class="w-5 h-5 absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-emerald-500 transition-colors"></i>
                                    <input type="text" id="dniSearch" name="dni" maxlength="8" placeholder="8 dígitos"
                                        class="input-field h-full py-3 text-base font-black" style="padding-left: 3.5rem;" required>
                                </div>
                                <button type="button" id="btnBuscarDni"
                                    class="px-8 py-4 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-black rounded-2xl shadow-lg shadow-emerald-500/20 transition-all hover:scale-105 active:scale-95 flex items-center gap-2 whitespace-nowrap uppercase tracking-wider">
                                    <i data-lucide="search" class="w-4 h-4"></i>
                                    Validar
                                </button>
                            </div>
                            <p id="msgBusquedaDni" class="text-[10px] mt-2 hidden uppercase font-black"></p>
                        </div>
                        <div class="hidden md:block">
                            <div class="bg-emerald-50/50 border border-emerald-100 rounded-xl p-3 flex items-start gap-3">
                                <i data-lucide="shield-check" class="w-5 h-5 text-emerald-500 mt-0.5"></i>
                                <p class="text-[10px] text-emerald-700 leading-tight">Sus datos se validarán contra la base de datos de RENIEC y MPI-Engineers para garantizar la autenticidad del reporte.</p>
                            </div>
                        </div>
                    </div>

                    {{-- NAMES ROW --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="label-field">Apellidos Completos *</label>
                            <div class="relative group">
                                <i data-lucide="user" class="w-5 h-5 absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-emerald-500 transition-colors"></i>
                                <input type="text" name="apellidos" placeholder="Ej: Perez Garcia" class="input-field py-3 font-bold" style="padding-left: 3.5rem;" required>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <label class="label-field">Nombres Completos *</label>
                            <div class="relative group">
                                <i data-lucide="user" class="w-5 h-5 absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-emerald-500 transition-colors"></i>
                                <input type="text" name="nombres" placeholder="Ej: Juan Pedro" class="input-field py-3 font-bold" style="padding-left: 3.5rem;" required>
                            </div>
                        </div>
                    </div>

                    {{-- CONTACT ROW --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="label-field">Número de Celular *</label>
                            <div class="relative group">
                                <i data-lucide="phone" class="w-5 h-5 absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-emerald-500 transition-colors"></i>
                                <input type="text" name="celular" maxlength="9" placeholder="999 999 999" class="input-field py-3 font-bold" style="padding-left: 3.5rem;" required>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <label class="label-field">Correo Institucional / Personal *</label>
                            <div class="relative group">
                                <i data-lucide="mail" class="w-5 h-5 absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-emerald-500 transition-colors"></i>
                                <input type="email" name="correo" placeholder="ejemplo@dominio.com" class="input-field py-3 font-bold" style="padding-left: 3.5rem;" required>
                            </div>
                        </div>
                    </div>
                </div>

            {{-- SECCIÓN 2: Establecimiento --}}
            <div class="premium-card">
                <div class="px-7 py-5 border-b border-slate-100 bg-gradient-to-r from-slate-50 to-white flex items-center gap-4">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-indigo-600 to-purple-600 shadow-md shadow-indigo-200 text-white flex items-center justify-center">
                        <i data-lucide="building-2" class="w-5 h-5"></i>
                    </div>
                    <h2 class="text-base font-extrabold text-slate-800 tracking-tight uppercase">2. Datos del Establecimiento</h2>
                </div>
                <div class="p-7 space-y-8">
                    {{-- BUSCADOR PRINCIPAL --}}
                    <div>
                        <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-3 block">Buscar IPRESS (Escriba nombre o código)</label>
                        <div class="flex gap-3 items-stretch">
                            <div class="relative flex-1">
                                <i data-lucide="search" class="w-5 h-5 absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                <input type="text" id="codigoIpress" placeholder="EJ: HOSPITAL REGIONAL..."
                                    autocomplete="off" class="input-field h-full py-4 text-lg font-bold placeholder:font-normal placeholder:text-slate-300" style="padding-left: 3.5rem;">
                                <!-- Dropdown de sugerencias -->
                                <ul id="ipressSuggestions"
                                    class="absolute z-50 w-full bg-white border border-slate-200 shadow-2xl rounded-2xl mt-3 hidden max-h-80 overflow-y-auto custom-scroll overflow-hidden transition-all duration-200 border-t-4 border-t-indigo-500">
                                </ul>
                            </div>
                            <button type="button" id="btnBuscar"
                                class="px-8 py-4 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-black rounded-2xl shadow-xl shadow-indigo-500/20 transition-all hover:scale-[1.02] active:scale-95 flex items-center gap-2 whitespace-nowrap uppercase tracking-wider">
                                <i data-lucide="search" class="w-5 h-5"></i>
                                Buscar
                            </button>
                        </div>
                        <p id="msgBusqueda" class="text-xs mt-3 hidden"></p>
                    </div>

                    {{-- GRID DE RESULTADOS (CARDS) --}}
                    <div id="resultadoEstablecimiento" class="animate-in fade-in slide-in-from-top-4 duration-500">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                            {{-- CATEGORÍA --}}
                            <div class="bg-indigo-50/30 border-2 border-dashed border-indigo-200 rounded-2xl p-5 transition-all hover:bg-indigo-50/50">
                                <p class="text-[9px] font-black text-indigo-600 uppercase tracking-widest mb-1">Categoría</p>
                                <p id="txtCategoria" class="text-lg font-black text-slate-800">---</p>
                            </div>
                            {{-- PROVINCIA --}}
                            <div class="bg-slate-50/50 border border-slate-100 rounded-2xl p-5">
                                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Provincia</p>
                                <p id="txtProvincia" class="text-lg font-black text-slate-800">---</p>
                            </div>
                            {{-- DISTRITO --}}
                            <div class="bg-slate-50/50 border border-slate-100 rounded-2xl p-5">
                                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Distrito</p>
                                <p id="txtDistrito" class="text-lg font-black text-slate-800">---</p>
                            </div>
                            {{-- RED --}}
                            <div class="bg-slate-50/50 border border-slate-100 rounded-2xl p-5">
                                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Red</p>
                                <p id="txtRed" class="text-lg font-black text-slate-800">---</p>
                            </div>
                        </div>

                        {{-- MICRORED (FULL WIDTH) --}}
                        <div class="bg-slate-50/50 border border-slate-100 rounded-2xl p-5 mb-8">
                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Microred</p>
                            <p id="txtMicrored" class="text-lg font-black text-slate-800">---</p>
                        </div>

                        {{-- JEFE DEL ESTABLECIMIENTO (EDITABLE) --}}
                        <div>
                            <label class="text-[10px] font-black text-slate-600 uppercase tracking-widest mb-3 block">Jefe del Establecimiento (Editable)</label>
                            <div class="relative">
                                <i data-lucide="user-cog" class="w-5 h-5 absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                <input type="text" name="jefe_establecimiento" id="jefeEstablecimiento" placeholder="Ingrese nombre del jefe o responsable..."
                                    class="input-field py-4 font-bold text-slate-700 bg-white border-2 border-slate-100 hover:border-slate-200 transition-colors" style="padding-left: 3.5rem;">
                            </div>
                        </div>
                    </div>

                    <input type="hidden" name="codigo_ipress" id="hiddenCodigo">
                    <input type="hidden" name="nombre_establecimiento" id="hiddenNombre">
                    <input type="hidden" name="distrito_establecimiento" id="hiddenDistrito">
                    <input type="hidden" name="provincia_establecimiento" id="hiddenProvincia">
                    <input type="hidden" name="categoria" id="hiddenCategoria">
                    <input type="hidden" name="red" id="hiddenRed">
                    <input type="hidden" name="microred" id="hiddenMicrored">
                </div>
                </div>
            </div>

            {{-- SECCIÓN 3: Incidencia --}}
            <div class="premium-card">
                <div class="px-7 py-5 border-b border-slate-100 bg-gradient-to-r from-slate-50 to-white flex items-center gap-4">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-orange-500 to-red-600 shadow-md shadow-orange-200 text-white flex items-center justify-center">
                        <i data-lucide="alert-circle" class="w-5 h-5"></i>
                    </div>
                    <h2 class="text-base font-extrabold text-slate-800 tracking-tight uppercase">3. Descripción de la Incidencia</h2>
                </div>
                <div class="p-7 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="label-field">Módulo SIHCE con problema *</label>
                            <div class="relative group">
                                <i data-lucide="layers" class="w-5 h-5 absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-orange-500 transition-colors"></i>
                                <select name="modulos" class="input-field py-3 font-bold appearance-none cursor-pointer" style="padding-left: 3.5rem;" required>
                                    <option value="">— SELECCIONE UN MÓDULO —</option>
                                    @foreach($modulos as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                <i data-lucide="chevron-down" class="w-4 h-4 absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"></i>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <div class="bg-orange-50 border border-orange-100 rounded-xl p-3 flex items-start gap-3 w-full">
                                <i data-lucide="info" class="w-5 h-5 text-orange-500 mt-0.5"></i>
                                <p class="text-[10px] text-orange-700 leading-tight">Seleccione el módulo donde se presenta el error para que el especialista correspondiente pueda atenderlo rápidamente.</p>
                            </div>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <label class="label-field">Descripción Detallada del Problema *</label>
                        <div class="relative group">
                            <i data-lucide="message-square" class="w-5 h-5 absolute left-4 top-5 text-slate-400 group-focus-within:text-orange-500 transition-colors"></i>
                            <textarea name="observacion" rows="5" maxlength="2000"
                                placeholder="Describa el problema con detalle: pasos para reproducirlo, mensajes de error, etc."
                                class="input-field py-4 font-medium resize-none" style="padding-left: 3.5rem;" required></textarea>
                        </div>
                        <div class="flex justify-between items-center px-1">
                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Máximo 2000 caracteres</p>
                            <p id="charCount" class="text-[9px] font-black text-slate-400 uppercase tracking-widest">0 / 2000</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- SECCIÓN 4: Imágenes --}}
            <div class="premium-card">
                <div class="px-7 py-5 border-b border-slate-100 bg-gradient-to-r from-slate-50 to-white flex items-center gap-4">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-purple-500 to-indigo-600 shadow-md shadow-purple-200 text-white flex items-center justify-center">
                        <i data-lucide="image-plus" class="w-5 h-5"></i>
                    </div>
                    <h2 class="text-base font-extrabold text-slate-800 tracking-tight uppercase">4. Soporte Visual (Opcional)</h2>
                </div>
                <div class="p-7">
                    <div class="mb-6 bg-indigo-50/50 border border-indigo-100 rounded-2xl p-4 flex items-center gap-4">
                        <div class="w-12 h-12 rounded-full bg-white flex items-center justify-center text-indigo-500 shadow-sm">
                            <i data-lucide="info" class="w-6 h-6"></i>
                        </div>
                        <div>
                            <p class="text-[11px] font-black text-indigo-700 uppercase tracking-widest leading-none mb-1">Recomendación</p>
                            <p class="text-xs text-indigo-600/80 font-medium">Adjunte capturas de pantalla (máximo 3) para facilitar la resolución. Formatos permitidos: JPG, PNG (máx. 5MB).</p>
                        </div>
                    </div>
                    
                    <label for="imagenes"
                        class="relative overflow-hidden group flex flex-col items-center justify-center gap-4 border-2 border-dashed border-slate-200 hover:border-indigo-400 bg-slate-50/50 hover:bg-white rounded-3xl p-12 cursor-pointer transition-all duration-500">
                        <div class="absolute inset-0 bg-gradient-to-br from-indigo-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                        
                        <div class="w-16 h-16 rounded-2xl bg-white shadow-xl flex items-center justify-center group-hover:scale-110 group-hover:rotate-3 transition-all duration-500 z-10 border border-slate-100">
                            <i data-lucide="upload-cloud" class="w-8 h-8 text-indigo-500"></i>
                        </div>
                        
                        <div class="text-center z-10">
                            <span class="block text-base font-black text-slate-700 group-hover:text-indigo-600 transition-colors uppercase tracking-tight">Seleccionar Archivos</span>
                            <span class="block text-[10px] font-bold text-slate-400 mt-2 uppercase tracking-widest">O arrastre y suelte aquí</span>
                        </div>
                        <input id="imagenes" type="file" name="imagenes[]" multiple accept=".jpg,.jpeg,.png" class="hidden">
                    </label>
                    <div id="previewImagenes" class="mt-6 grid grid-cols-3 gap-4 hidden"></div>
                </div>
            </div>

            {{-- BOTÓN ENVIAR --}}
            <div class="flex justify-center pt-8 pb-12">
                <button type="submit" id="btnEnviar"
                    class="group relative flex items-center gap-4 bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 text-white font-black px-16 py-5 rounded-2xl shadow-2xl shadow-indigo-500/30 border border-indigo-400/30 transition-all hover:-translate-y-1.5 active:translate-y-0 uppercase tracking-widest text-sm overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-r from-white/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    <i data-lucide="send" class="w-5 h-5 group-hover:translate-x-1 group-hover:-translate-y-0.5 transition-transform"></i>
                    <span>Enviar Reporte Técnico</span>
                </button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            const inputIpress = document.getElementById('codigoIpress');
            const listIpress = document.getElementById('ipressSuggestions');
            const msgBusqueda = document.getElementById('msgBusqueda');
            const resultDiv = document.getElementById('resultadoEstablecimiento');

            function fillEstablecimiento(data) {
                document.getElementById('hiddenCodigo').value = data.id || data.codigo;
                document.getElementById('hiddenNombre').value = data.nombre;
                document.getElementById('hiddenDistrito').value = data.distrito;
                document.getElementById('hiddenProvincia').value = data.provincia;
                document.getElementById('hiddenCategoria').value = data.categoria;
                document.getElementById('hiddenRed').value = data.red;
                document.getElementById('hiddenMicrored').value = data.microred;

                // Actualizar CARDS visuales
                document.getElementById('txtCategoria').textContent = data.categoria || '---';
                document.getElementById('txtDistrito').textContent = data.distrito || '---';
                document.getElementById('txtProvincia').textContent = data.provincia || '---';
                document.getElementById('txtRed').textContent = data.red || '---';
                document.getElementById('txtMicrored').textContent = data.microred || '---';
                
                // Jefe del establecimiento
                const jefeInput = document.getElementById('jefeEstablecimiento');
                jefeInput.value = data.jefe || '';

                resultDiv.classList.remove('hidden');
                msgBusqueda.textContent = '✔ Establecimiento seleccionado: ' + data.nombre;
                msgBusqueda.className = 'text-xs mt-3 text-emerald-600 font-black uppercase tracking-wider';
                listIpress.classList.add('hidden');
                inputIpress.value = data.id || data.codigo;
            }

            // ─── AUTOCOMPLETE IPRESS ──────────────────────────────────
            let debounceTimer;
            inputIpress.addEventListener('input', function() {
                clearTimeout(debounceTimer);
                const term = this.value.trim();
                
                if (term.length < 3) {
                    listIpress.classList.add('hidden');
                    return;
                }

                debounceTimer = setTimeout(async () => {
                    try {
                        const res = await fetch(`{{ route('usuario.mesa-ayuda.buscar') }}?term=${encodeURIComponent(term)}`);
                        const data = await res.json();
                        
                        listIpress.innerHTML = '';
                        if (data.length > 0) {
                            data.forEach(item => {
                                const li = document.createElement('li');
                                li.className = 'px-5 py-4 hover:bg-indigo-50/50 cursor-pointer border-b border-slate-50 last:border-none transition-all group';
                                li.innerHTML = `
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 rounded-xl bg-slate-50 text-slate-400 flex items-center justify-center group-hover:bg-white group-hover:text-indigo-600 transition-colors shadow-sm">
                                            <i data-lucide="building-2" class="w-5 h-5"></i>
                                        </div>
                                        <div class="flex flex-col">
                                            <span class="text-sm font-black text-slate-800 group-hover:text-indigo-700 transition-colors">${item.label}</span>
                                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-tight">${item.red} / ${item.microred}</span>
                                        </div>
                                    </div>
                                `;
                                li.addEventListener('click', () => fillEstablecimiento(item));
                                listIpress.appendChild(li);
                            });
                            if (typeof lucide !== 'undefined') lucide.createIcons({attrs: { class: 'w-5 h-5' }, node: listIpress});
                            listIpress.classList.remove('hidden');
                        } else {
                            listIpress.classList.add('hidden');
                        }
                    } catch (e) {
                        console.error('Error fetching establishments', e);
                    }
                }, 300);
            });

            // ─── CHARACTER COUNTER ─────────────────────────────────────
            const observacion = document.querySelector('textarea[name="observacion"]');
            const charCount = document.getElementById('charCount');
            if (observacion && charCount) {
                observacion.addEventListener('input', function() {
                    const count = this.value.length;
                    charCount.textContent = `${count} / 2000`;
                    if (count > 1800) charCount.className = 'text-[9px] font-black text-orange-500 uppercase tracking-widest';
                    else charCount.className = 'text-[9px] font-black text-slate-400 uppercase tracking-widest';
                });
            }

            // Cerrar lista al hacer click fuera
            document.addEventListener('click', (e) => {
                if (!inputIpress.contains(e.target) && !listIpress.contains(e.target)) {
                    listIpress.classList.add('hidden');
                }
            });

            // ─── BOTÓN BUSCAR (Acción Directa) ──────────────────────────
            document.getElementById('btnBuscar').addEventListener('click', async function () {
                const term = inputIpress.value.trim();

                if (!term) {
                    msgBusqueda.textContent = 'Ingrese un nombre o código IPRESS.';
                    msgBusqueda.className = 'text-xs mt-1 text-red-500';
                    msgBusqueda.classList.remove('hidden');
                    return;
                }

                this.disabled = true;
                this.textContent = 'Buscando...';

                try {
                    const res = await fetch(`{{ route('usuario.mesa-ayuda.buscar') }}?term=${encodeURIComponent(term)}`);
                    const data = await res.json();

                    if (data.length > 0) {
                        // Si hay varios, mostramos la lista. Si hay uno solo, lo seleccionamos.
                        if (data.length === 1) {
                            fillEstablecimiento(data[0]);
                        } else {
                            listIpress.classList.remove('hidden');
                            msgBusqueda.textContent = 'Seleccione una de las opciones encontradas.';
                            msgBusqueda.className = 'text-xs mt-3 text-blue-500 font-black uppercase tracking-wider';
                        }
                    } else {
                        // Resetear etiquetas pero no ocultar
                        document.getElementById('txtCategoria').textContent = '---';
                        document.getElementById('txtDistrito').textContent = '---';
                        document.getElementById('txtProvincia').textContent = '---';
                        document.getElementById('txtRed').textContent = '---';
                        document.getElementById('txtMicrored').textContent = '---';
                        document.getElementById('jefeEstablecimiento').value = '';
                        
                        msgBusqueda.textContent = '✗ No se encontraron resultados.';
                        msgBusqueda.className = 'text-xs mt-3 text-red-500 font-bold';
                    }
                    msgBusqueda.classList.remove('hidden');
                } catch (e) {
                    msgBusqueda.textContent = 'Error de conexión. Intente nuevamente.';
                    msgBusqueda.className = 'text-xs mt-1 text-red-500';
                    msgBusqueda.classList.remove('hidden');
                }

                this.innerHTML = '<i data-lucide="search" class="w-4 h-4"></i> Buscar';
                if (typeof lucide !== 'undefined') lucide.createIcons();
            });

            // ─── BÚSQUEDA DE DNI (RENIEC / LOCAL) ──────────────────────
            document.getElementById('btnBuscarDni').addEventListener('click', async function () {
                const dniInput = document.getElementById('dniSearch');
                const dni = dniInput.value.trim();
                const msg = document.getElementById('msgBusquedaDni');

                if (!/^\d{8}$/.test(dni)) {
                    msg.textContent = 'Ingrese un DNI válido de 8 dígitos.';
                    msg.className = 'text-xs mt-1 text-red-500 font-semibold';
                    msg.classList.remove('hidden');
                    dniInput.focus();
                    return;
                }

                this.disabled = true;
                this.innerHTML = '<div class="w-4 h-4 rounded-full border-2 border-white border-t-transparent animate-spin"></div>';

                try {
                    const res = await fetch(`{{ route('usuario.mesa-ayuda.buscar-dni') }}?dni=${encodeURIComponent(dni)}`);
                    const data = await res.json();

                    if (data.found) {
                        document.querySelector('input[name="apellidos"]').value = `${data.apellido_paterno} ${data.apellido_materno}`;
                        document.querySelector('input[name="nombres"]').value = data.nombres;
                        if (data.correo) document.querySelector('input[name="correo"]').value = data.correo;
                        if (data.celular) document.querySelector('input[name="celular"]').value = data.celular;

                        msg.textContent = '✔ Datos validados correctamente.';
                        msg.className = 'text-xs mt-1 text-emerald-600 font-semibold';

                        if (data.source === 'local') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Datos encontrados en MPI-Engineers',
                                text: 'El profesional ya se encuentra registrado',
                                confirmButtonColor: '#10b981',
                                confirmButtonText: 'Entendido'
                            });
                        } else if (data.source === 'reniec') {
                            Swal.fire({
                                icon: 'info',
                                title: 'Datos de API externa',
                                text: 'El profesional fue consultado exitosamente.',
                                confirmButtonColor: '#3b82f6',
                                confirmButtonText: 'Entendido'
                            });
                        }
                    } else {
                        msg.textContent = data.message || '✗ DNI no encontrado. Ingrese sus datos manualmente.';
                        msg.className = 'text-xs mt-1 text-amber-600 font-semibold';
                        document.querySelector('input[name="apellidos"]').focus();
                    }
                    msg.classList.remove('hidden');
                } catch (e) {
                    msg.textContent = 'Error de conexión. Puede ingresar sus datos manualmente.';
                    msg.className = 'text-xs mt-1 text-red-500 font-semibold';
                    msg.classList.remove('hidden');
                }

                this.disabled = false;
                this.innerHTML = '<i data-lucide="search" class="w-4 h-4"></i> Buscar';
                if (typeof lucide !== 'undefined') lucide.createIcons();
            });

            // ─── PREVIEW IMÁGENES ───────────────────────────────────────
            document.getElementById('imagenes').addEventListener('change', function () {
                const preview = document.getElementById('previewImagenes');
                preview.innerHTML = '';
                const files = Array.from(this.files).slice(0, 3);

                if (files.length > 0) {
                    preview.classList.remove('hidden');
                    files.forEach(file => {
                        const reader = new FileReader();
                        reader.onload = e => {
                            const div = document.createElement('div');
                            div.className = 'relative rounded-xl overflow-hidden border border-slate-200 bg-slate-100 aspect-square';
                            div.innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover">`;
                            preview.appendChild(div);
                        };
                        reader.readAsDataURL(file);
                    });
                } else {
                    preview.classList.add('hidden');
                }
            });

            // ─── ENVÍO DEL FORMULARIO ───────────────────────────────────
            document.getElementById('formIncidencia').addEventListener('submit', async function (e) {
                e.preventDefault();

                if (!document.getElementById('hiddenCodigo').value) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Establecimiento requerido',
                        text: 'Debe buscar y verificar su establecimiento por código IPRESS antes de enviar.',
                        confirmButtonColor: '#f97316'
                    });
                    return;
                }

                const btn = document.getElementById('btnEnviar');
                btn.disabled = true;
                btn.innerHTML = '<svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg> Enviando...';

                const formData = new FormData(this);
                const fileInput = document.getElementById('imagenes');
                formData.delete('imagenes[]');
                Array.from(fileInput.files).forEach(f => formData.append('imagenes[]', f));

                try {
                    const res = await fetch('{{ route("usuario.mesa-ayuda.store") }}', {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                        body: formData
                    });
                    const data = await res.json();

                    if (res.ok) {
                        Swal.fire({
                            icon: 'success',
                            title: '¡Reporte enviado!',
                            html: `${data.message}<br><br><b>N° de Ticket: #${data.ticket}</b><br><small class="text-slate-500">Guarde este número para hacer seguimiento.</small>`,
                            confirmButtonColor: '#f97316',
                            confirmButtonText: 'Aceptar'
                        }).then(() => {
                            this.reset();
                            document.getElementById('txtCategoria').textContent = '---';
                            document.getElementById('txtDistrito').textContent = '---';
                            document.getElementById('txtProvincia').textContent = '---';
                            document.getElementById('txtRed').textContent = '---';
                            document.getElementById('txtMicrored').textContent = '---';
                            document.getElementById('jefeEstablecimiento').value = '';
                            document.getElementById('msgBusqueda').classList.add('hidden');
                            document.getElementById('previewImagenes').classList.add('hidden');
                            document.getElementById('hiddenCodigo').value = '';
                        });
                    } else {
                        Swal.fire({ icon: 'error', title: 'Error de validación', text: data.message, confirmButtonColor: '#f97316' });
                    }
                } catch (err) {
                    Swal.fire({ icon: 'error', title: 'Error', text: 'Error de conexión. Intente nuevamente.', confirmButtonColor: '#f97316' });
                }

                btn.disabled = false;
                btn.innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg> Enviar Reporte';
            });
        });
    </script>
@endpush