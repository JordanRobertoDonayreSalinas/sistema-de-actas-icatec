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
                <div
                    class="px-7 py-5 border-b border-slate-100 bg-gradient-to-r from-slate-50 to-white flex items-center gap-4">
                    <div
                        class="w-9 h-9 rounded-xl bg-gradient-to-br from-emerald-400 to-teal-500 shadow-md shadow-emerald-200 text-white flex items-center justify-center text-sm font-black tracking-tighter">
                        1</div>
                    <h2 class="text-base font-extrabold text-slate-800 tracking-tight">Datos del Profesional</h2>
                </div>
                <div class="p-7 grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label class="label-field">DNI *</label>
                        <div class="flex gap-3 items-stretch">
                            <div class="relative flex-1">
                                <i data-lucide="user"
                                    class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                <input type="text" id="dniSearch" name="dni" maxlength="8" placeholder="8 dígitos"
                                    class="input-field h-full" style="padding-left: 2.5rem;" required>
                            </div>
                            <button type="button" id="btnBuscarDni"
                                class="px-5 py-2.5 bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white text-sm font-bold rounded-xl shadow-lg shadow-emerald-500/30 transition-all hover:scale-105 active:scale-95 flex items-center gap-2 whitespace-nowrap">
                                <i data-lucide="search" class="w-4 h-4"></i>
                                Buscar
                            </button>
                        </div>
                        <p id="msgBusquedaDni" class="text-xs mt-2 hidden"></p>
                    </div>
                    <div>
                        <label class="label-field">Celular *</label>
                        <input type="text" name="celular" maxlength="9" placeholder="9 dígitos" class="input-field"
                            required>
                    </div>
                    <div>
                        <label class="label-field">Apellidos *</label>
                        <input type="text" name="apellidos" placeholder="Apellidos completos" class="input-field" required>
                    </div>
                    <div>
                        <label class="label-field">Nombres *</label>
                        <input type="text" name="nombres" placeholder="Nombres completos" class="input-field" required>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="label-field">Correo Electrónico *</label>
                        <input type="email" name="correo" placeholder="correo@ejemplo.com" class="input-field" required>
                    </div>
                </div>
            </div>

            {{-- SECCIÓN 2: Establecimiento --}}
            <div class="premium-card">
                <div
                    class="px-7 py-5 border-b border-slate-100 bg-gradient-to-r from-slate-50 to-white flex items-center gap-4">
                    <div
                        class="w-9 h-9 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 shadow-md shadow-blue-200 text-white flex items-center justify-center text-sm font-black tracking-tighter">
                        2</div>
                    <h2 class="text-base font-extrabold text-slate-800 tracking-tight">Datos del Establecimiento</h2>
                </div>
                <div class="p-7 space-y-6">
                    <div>
                        <label class="label-field">Código IPRESS *</label>
                        <div class="flex gap-3 items-stretch">
                            <div class="relative flex-1">
                                <i data-lucide="hash"
                                    class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                <input type="text" id="codigoIpress" placeholder="Ej: 3372 o 'Santa Rosa'"
                                    autocomplete="off" class="input-field h-full" style="padding-left: 2.5rem;">
                                <!-- Dropdown de sugerencias -->
                                <ul id="ipressSuggestions"
                                    class="absolute z-50 w-full bg-white border border-slate-200 shadow-2xl rounded-xl mt-2 hidden max-h-64 overflow-y-auto custom-scroll overflow-hidden">
                                </ul>
                            </div>
                            <button type="button" id="btnBuscar"
                                class="px-6 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white text-sm font-bold rounded-xl shadow-lg shadow-blue-500/30 transition-all hover:scale-105 active:scale-95 flex items-center gap-2 whitespace-nowrap">
                                <i data-lucide="search" class="w-4 h-4"></i>
                                Buscar
                            </button>
                        </div>
                        <p id="msgBusqueda" class="text-xs mt-2 hidden"></p>
                    </div>

                    <input type="hidden" name="codigo_ipress" id="hiddenCodigo">
                    <input type="hidden" name="nombre_establecimiento" id="hiddenNombre">
                    <input type="hidden" name="distrito_establecimiento" id="hiddenDistrito">
                    <input type="hidden" name="provincia_establecimiento" id="hiddenProvincia">
                    <input type="hidden" name="categoria" id="hiddenCategoria">
                    <input type="hidden" name="red" id="hiddenRed">
                    <input type="hidden" name="microred" id="hiddenMicrored">

                    <div id="resultadoEstablecimiento" class="hidden bg-blue-50 border border-blue-200 rounded-xl p-4">
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 text-sm">
                            <div><span class="text-[10px] font-bold text-blue-500 uppercase">Establecimiento</span>
                                <p id="txtNombre" class="font-semibold text-slate-800 mt-0.5 text-xs"></p>
                            </div>
                            <div><span class="text-[10px] font-bold text-blue-500 uppercase">Categoría</span>
                                <p id="txtCategoria" class="font-semibold text-slate-800 mt-0.5 text-xs"></p>
                            </div>
                            <div><span class="text-[10px] font-bold text-blue-500 uppercase">Distrito</span>
                                <p id="txtDistrito" class="font-semibold text-slate-800 mt-0.5 text-xs"></p>
                            </div>
                            <div><span class="text-[10px] font-bold text-blue-500 uppercase">Provincia</span>
                                <p id="txtProvincia" class="font-semibold text-slate-800 mt-0.5 text-xs"></p>
                            </div>
                            <div><span class="text-[10px] font-bold text-blue-500 uppercase">Red</span>
                                <p id="txtRed" class="font-semibold text-slate-800 mt-0.5 text-xs"></p>
                            </div>
                            <div><span class="text-[10px] font-bold text-blue-500 uppercase">Microred</span>
                                <p id="txtMicrored" class="font-semibold text-slate-800 mt-0.5 text-xs"></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- SECCIÓN 3: Incidencia --}}
            <div class="premium-card">
                <div
                    class="px-7 py-5 border-b border-slate-100 bg-gradient-to-r from-slate-50 to-white flex items-center gap-4">
                    <div
                        class="w-9 h-9 rounded-xl bg-gradient-to-br from-orange-400 to-red-500 shadow-md shadow-orange-200 text-white flex items-center justify-center text-sm font-black tracking-tighter">
                        3</div>
                    <h2 class="text-base font-extrabold text-slate-800 tracking-tight">Descripción de la Incidencia</h2>
                </div>
                <div class="p-7 space-y-6">
                    <div>
                        <label class="label-field">Módulo SIHCE con problema *</label>
                        <select name="modulos" class="input-field" required>
                            <option value="">— Seleccione un módulo —</option>
                            @foreach($modulos as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="label-field">Descripción del problema *</label>
                        <textarea name="observacion" rows="5" maxlength="2000"
                            placeholder="Describa el problema con detalle: pasos para reproducirlo, mensajes de error, etc."
                            class="input-field resize-none" required></textarea>
                        <p class="text-xs text-slate-400 mt-1">Máximo 2000 caracteres.</p>
                    </div>
                </div>
            </div>

            {{-- SECCIÓN 4: Imágenes --}}
            <div class="premium-card">
                <div
                    class="px-7 py-5 border-b border-slate-100 bg-gradient-to-r from-slate-50 to-white flex items-center gap-4">
                    <div
                        class="w-9 h-9 rounded-xl bg-gradient-to-br from-purple-500 to-indigo-500 shadow-md shadow-purple-200 text-white flex items-center justify-center text-sm font-black tracking-tighter">
                        4</div>
                    <h2 class="text-base font-extrabold text-slate-800 tracking-tight">Evidencia Fotográfica</h2>
                </div>
                <div class="p-7">
                    <p
                        class="text-xs font-medium text-slate-500 mb-4 bg-slate-50 p-3 rounded-lg border border-slate-100/50">
                        <i data-lucide="info" class="w-4 h-4 inline-block text-slate-400 mr-1 -mt-0.5"></i>
                        Adjunte capturas de pantalla o fotos del problema (hasta 3). Formatos: JPG, PNG – Máx 5 MB c/u.
                    </p>
                    <label for="imagenes"
                        class="relative overflow-hidden group flex flex-col items-center justify-center gap-4 border-2 border-dashed border-slate-300 hover:border-orange-500 bg-slate-50 hover:bg-orange-50/50 rounded-2xl p-10 cursor-pointer transition-all duration-300">
                        <div
                            class="absolute inset-0 bg-gradient-to-br from-orange-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity">
                        </div>
                        <div
                            class="w-14 h-14 rounded-full bg-white shadow-sm flex items-center justify-center group-hover:scale-110 group-hover:shadow-md transition-all duration-300 z-10 text-orange-500 border border-slate-100">
                            <i data-lucide="upload-cloud"
                                class="w-6 h-6 text-slate-400 group-hover:text-orange-500 transition-colors"></i>
                        </div>
                        <div class="text-center z-10">
                            <span
                                class="block text-sm font-extrabold text-slate-600 group-hover:text-orange-600 transition-colors">Haz
                                clic aquí para seleccionar imágenes</span>
                            <span class="block text-xs font-medium text-slate-400 mt-1">O arrastra y suelta tus archivos
                                aquí</span>
                        </div>
                        <input id="imagenes" type="file" name="imagenes[]" multiple accept=".jpg,.jpeg,.png" class="hidden">
                    </label>
                    <div id="previewImagenes" class="mt-4 grid grid-cols-3 gap-3 hidden"></div>
                </div>
            </div>

            {{-- BOTÓN ENVIAR --}}
            <div class="flex justify-end pt-4 pb-8">
                <button type="submit" id="btnEnviar"
                    class="flex items-center gap-3 bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white font-extrabold px-10 py-4 rounded-xl shadow-xl shadow-orange-500/30 border border-orange-400/50 transition-all hover:-translate-y-1 hover:shadow-2xl hover:shadow-orange-500/40 active:translate-y-0 active:shadow-md text-base">
                    <i data-lucide="send" class="w-5 h-5"></i>
                    Enviar Reporte Técnico
                </button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            // ─── BÚSQUEDA DE ESTABLECIMIENTO ───────────────────────────
            document.getElementById('btnBuscar').addEventListener('click', async function () {
                const codigo = document.getElementById('codigoIpress').value.trim();
                const msg = document.getElementById('msgBusqueda');
                const result = document.getElementById('resultadoEstablecimiento');

                if (!codigo) {
                    msg.textContent = 'Ingrese un código IPRESS válido.';
                    msg.className = 'text-xs mt-1 text-red-500';
                    msg.classList.remove('hidden');
                    return;
                }

                this.disabled = true;
                this.textContent = 'Buscando...';

                try {
                    const res = await fetch(`{{ route('mesa-ayuda.buscar') }}?codigo=${encodeURIComponent(codigo)}`);
                    const data = await res.json();

                    if (data.found) {
                        document.getElementById('hiddenCodigo').value = codigo;
                        document.getElementById('hiddenNombre').value = data.nombre;
                        document.getElementById('hiddenDistrito').value = data.distrito;
                        document.getElementById('hiddenProvincia').value = data.provincia;
                        document.getElementById('hiddenCategoria').value = data.categoria;
                        document.getElementById('hiddenRed').value = data.red;
                        document.getElementById('hiddenMicrored').value = data.microred;

                        document.getElementById('txtNombre').textContent = data.nombre;
                        document.getElementById('txtCategoria').textContent = data.categoria;
                        document.getElementById('txtDistrito').textContent = data.distrito;
                        document.getElementById('txtProvincia').textContent = data.provincia;
                        document.getElementById('txtRed').textContent = data.red;
                        document.getElementById('txtMicrored').textContent = data.microred;

                        result.classList.remove('hidden');
                        msg.textContent = '✔ Establecimiento encontrado.';
                        msg.className = 'text-xs mt-1 text-emerald-600 font-semibold';
                    } else {
                        result.classList.add('hidden');
                        msg.textContent = '✗ Código IPRESS no encontrado en el sistema.';
                        msg.className = 'text-xs mt-1 text-red-500';
                    }
                    msg.classList.remove('hidden');
                } catch (e) {
                    msg.textContent = 'Error de conexión. Intente nuevamente.';
                    msg.className = 'text-xs mt-1 text-red-500';
                    msg.classList.remove('hidden');
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
                    const res = await fetch(`{{ route('mesa-ayuda.buscar-dni') }}?dni=${encodeURIComponent(dni)}`);
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
                    const res = await fetch('{{ route("mesa-ayuda.store") }}', {
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
                            document.getElementById('formIncidencia').reset();
                            document.getElementById('resultadoEstablecimiento').classList.add('hidden');
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