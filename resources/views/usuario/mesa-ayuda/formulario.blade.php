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
    .input-field {
        display: block;
        width: 100%;
        border: 1px solid #e2e8f0;
        border-radius: 0.75rem;
        padding: 0.625rem 1rem;
        font-size: 0.875rem;
        color: #334155;
        background: #fff;
        transition: box-shadow .15s, border-color .15s;
    }
    .input-field:focus {
        outline: none;
        box-shadow: 0 0 0 2px #fb923c55;
        border-color: #f97316;
    }
    .label-field {
        display: block;
        font-size: 0.625rem;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: .07em;
        margin-bottom: 0.375rem;
    }
</style>
@endpush

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    {{-- HERO --}}
    <div class="text-center mb-2">
        <div class="inline-flex items-center gap-2 bg-orange-50 text-orange-700 text-xs font-bold px-4 py-1.5 rounded-full border border-orange-200 mb-3">
            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
            </svg>
            Formulario de Reporte de Incidencia Técnica
        </div>
        <p class="text-sm text-slate-500 max-w-xl mx-auto">
            Complete el siguiente formulario con los datos del problema. El equipo técnico de implementación
            revisará su reporte y le brindará soporte a la brevedad.
        </p>
    </div>

    {{-- FORM --}}
    <form id="formIncidencia" class="space-y-5" enctype="multipart/form-data">
        @csrf

        {{-- SECCIÓN 1: Datos del profesional --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/60 flex items-center gap-3">
                <div class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center text-xs font-bold">1</div>
                <h2 class="text-sm font-bold text-slate-700">Datos del Profesional</h2>
            </div>
            <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="label-field">DNI *</label>
                    <input type="text" name="dni" maxlength="8" placeholder="8 dígitos" class="input-field" required>
                </div>
                <div>
                    <label class="label-field">Celular *</label>
                    <input type="text" name="celular" maxlength="9" placeholder="9 dígitos" class="input-field" required>
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
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/60 flex items-center gap-3">
                <div class="w-7 h-7 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center text-xs font-bold">2</div>
                <h2 class="text-sm font-bold text-slate-700">Datos del Establecimiento</h2>
            </div>
            <div class="p-6 space-y-5">
                <div>
                    <label class="label-field">Código IPRESS *</label>
                    <div class="flex gap-3">
                        <input type="text" id="codigoIpress" placeholder="Ej: 3372"
                            class="input-field flex-1">
                        <button type="button" id="btnBuscar"
                            class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-xl transition flex items-center gap-2 whitespace-nowrap">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            Buscar
                        </button>
                    </div>
                    <p id="msgBusqueda" class="text-xs mt-1 hidden"></p>
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
                        <div><span class="text-[10px] font-bold text-blue-500 uppercase">Establecimiento</span><p id="txtNombre" class="font-semibold text-slate-800 mt-0.5 text-xs"></p></div>
                        <div><span class="text-[10px] font-bold text-blue-500 uppercase">Categoría</span><p id="txtCategoria" class="font-semibold text-slate-800 mt-0.5 text-xs"></p></div>
                        <div><span class="text-[10px] font-bold text-blue-500 uppercase">Distrito</span><p id="txtDistrito" class="font-semibold text-slate-800 mt-0.5 text-xs"></p></div>
                        <div><span class="text-[10px] font-bold text-blue-500 uppercase">Provincia</span><p id="txtProvincia" class="font-semibold text-slate-800 mt-0.5 text-xs"></p></div>
                        <div><span class="text-[10px] font-bold text-blue-500 uppercase">Red</span><p id="txtRed" class="font-semibold text-slate-800 mt-0.5 text-xs"></p></div>
                        <div><span class="text-[10px] font-bold text-blue-500 uppercase">Microred</span><p id="txtMicrored" class="font-semibold text-slate-800 mt-0.5 text-xs"></p></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- SECCIÓN 3: Incidencia --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/60 flex items-center gap-3">
                <div class="w-7 h-7 rounded-lg bg-orange-100 text-orange-700 flex items-center justify-center text-xs font-bold">3</div>
                <h2 class="text-sm font-bold text-slate-700">Descripción de la Incidencia</h2>
            </div>
            <div class="p-6 space-y-5">
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
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/60 flex items-center gap-3">
                <div class="w-7 h-7 rounded-lg bg-purple-100 text-purple-700 flex items-center justify-center text-xs font-bold">4</div>
                <h2 class="text-sm font-bold text-slate-700">Evidencia Fotográfica</h2>
            </div>
            <div class="p-6">
                <p class="text-xs text-slate-400 mb-3">Adjunte capturas de pantalla o fotos del problema (hasta 3). Formatos: JPG, PNG – Máx 5 MB c/u.</p>
                <label for="imagenes"
                    class="flex flex-col items-center justify-center gap-3 border-2 border-dashed border-slate-300 hover:border-orange-400 rounded-xl p-8 cursor-pointer transition-all group">
                    <div class="w-10 h-10 rounded-full bg-slate-100 group-hover:bg-orange-50 flex items-center justify-center transition-all">
                        <svg class="w-5 h-5 text-slate-400 group-hover:text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <span class="text-sm font-semibold text-slate-500 group-hover:text-orange-600 transition-colors">Haz clic para seleccionar imágenes</span>
                    <input id="imagenes" type="file" name="imagenes[]" multiple accept=".jpg,.jpeg,.png" class="hidden">
                </label>
                <div id="previewImagenes" class="mt-4 grid grid-cols-3 gap-3 hidden"></div>
            </div>
        </div>

        {{-- BOTÓN ENVIAR --}}
        <div class="flex justify-end">
            <button type="submit" id="btnEnviar"
                class="flex items-center gap-2 bg-orange-600 hover:bg-orange-700 text-white font-bold px-8 py-3 rounded-xl shadow-lg shadow-orange-200/60 transition-all hover:scale-105 active:scale-100">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                </svg>
                Enviar Reporte
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
        const msg    = document.getElementById('msgBusqueda');
        const result = document.getElementById('resultadoEstablecimiento');

        if (!codigo) {
            msg.textContent = 'Ingrese un código IPRESS válido.';
            msg.className   = 'text-xs mt-1 text-red-500';
            msg.classList.remove('hidden');
            return;
        }

        this.disabled    = true;
        this.textContent = 'Buscando...';

        try {
            const res  = await fetch(`{{ route('mesa-ayuda.buscar') }}?codigo=${encodeURIComponent(codigo)}`);
            const data = await res.json();

            if (data.found) {
                document.getElementById('hiddenCodigo').value    = codigo;
                document.getElementById('hiddenNombre').value    = data.nombre;
                document.getElementById('hiddenDistrito').value  = data.distrito;
                document.getElementById('hiddenProvincia').value = data.provincia;
                document.getElementById('hiddenCategoria').value = data.categoria;
                document.getElementById('hiddenRed').value       = data.red;
                document.getElementById('hiddenMicrored').value  = data.microred;

                document.getElementById('txtNombre').textContent    = data.nombre;
                document.getElementById('txtCategoria').textContent = data.categoria;
                document.getElementById('txtDistrito').textContent  = data.distrito;
                document.getElementById('txtProvincia').textContent = data.provincia;
                document.getElementById('txtRed').textContent       = data.red;
                document.getElementById('txtMicrored').textContent  = data.microred;

                result.classList.remove('hidden');
                msg.textContent = '✔ Establecimiento encontrado.';
                msg.className   = 'text-xs mt-1 text-emerald-600 font-semibold';
            } else {
                result.classList.add('hidden');
                msg.textContent = '✗ Código IPRESS no encontrado en el sistema.';
                msg.className   = 'text-xs mt-1 text-red-500';
            }
            msg.classList.remove('hidden');
        } catch (e) {
            msg.textContent = 'Error de conexión. Intente nuevamente.';
            msg.className   = 'text-xs mt-1 text-red-500';
            msg.classList.remove('hidden');
        }

        this.disabled = false;
        this.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg> Buscar';
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
            const res  = await fetch('{{ route("mesa-ayuda.store") }}', {
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
