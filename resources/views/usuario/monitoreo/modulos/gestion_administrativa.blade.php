@extends('layouts.usuario')

@section('title', '01. Gestión Administrativa')

@push('styles')
<style>
    body { background-color: #fcfcfd; }
    .glass-panel { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 2rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02); }
    .input-field { @apply w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm font-bold text-slate-700 transition-all duration-300 outline-none; }
    .input-field:focus { @apply bg-white border-blue-600 ring-4 ring-blue-600/5; }
    .label-field { @apply block text-[10px] font-black text-slate-400 uppercase tracking-[0.15em] mb-2 ml-1; }
    .table-custom { @apply w-full text-left border-separate border-spacing-y-2; }
    .table-custom thead th { @apply text-[9px] font-black text-slate-400 uppercase tracking-widest px-4 pb-2; }
    .table-custom tbody td { @apply bg-slate-50/50 py-3 px-4 first:rounded-l-2xl last:rounded-r-2xl border-y border-slate-100; }
    [x-cloak] { display: none !important; }
    /* Resaltado de datos jalados de mon_profesionales */
    .text-fetched { border-color: #3b82f6 !important; background-color: #eff6ff !important; color: #1d4ed8 !important; font-weight: 800 !important; }
</style>
@endpush

@section('content')
<div class="max-w-6xl mx-auto py-12 px-6">
    
    {{-- CABECERA --}}
    <div class="flex flex-col md:flex-row justify-between items-end mb-12 gap-6 border-b border-slate-100 pb-10">
        <div class="space-y-3">
            <div class="flex items-center gap-3">
                <span class="bg-blue-600 h-2 w-10 rounded-full"></span>
                <span class="text-[10px] font-black text-blue-600 uppercase tracking-[0.3em]">Auditoría de Gestión</span>
            </div>
            <h1 class="text-4xl font-black text-slate-900 italic">01. Gestión Administrativa</h1>
        </div>
        <a href="{{ route('usuario.monitoreo.modulos', $acta->id) }}" class="h-12 w-12 bg-white border border-slate-200 rounded-2xl flex items-center justify-center text-slate-400 hover:text-blue-600 transition-all shadow-sm">
            <i data-lucide="layout-grid"></i>
        </a>
    </div>

    <form id="form-modulo" action="{{ route('usuario.monitoreo.gestion_administrativa.store', $acta->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="modulo_nombre" value="gestion_administrativa">

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            <div class="lg:col-span-8 space-y-8">
                
                {{-- CARD: RESPONSABLE RRHH (FILTRO MAESTRO) --}}
                <div class="glass-panel p-10">
                    <div class="flex items-center gap-4 mb-10">
                        <div class="h-12 w-12 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center">
                            <i data-lucide="user-cog" class="w-6 h-6"></i>
                        </div>
                        <h3 class="text-xl font-bold text-slate-800 uppercase tracking-tight">Responsable RRHH</h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
                        <div class="md:col-span-4">
                            <label class="label-field">Tipo Doc.</label>
                            <select name="contenido[rrhh][tipo_doc]" class="input-field bg-white">
                                <option value="DNI" {{ (isset($detalle->contenido['rrhh']['tipo_doc']) && $detalle->contenido['rrhh']['tipo_doc'] == 'DNI') ? 'selected' : '' }}>DNI</option>
                                <option value="C.E" {{ (isset($detalle->contenido['rrhh']['tipo_doc']) && $detalle->contenido['rrhh']['tipo_doc'] == 'C.E') ? 'selected' : '' }}>C.E</option>
                            </select>
                        </div>
                        <div class="md:col-span-8">
                            <label class="label-field">DNI / Documento (Escriba 8 dígitos para filtrar)</label>
                            <div class="relative">
                                <input type="text" id="doc_rrhh" name="contenido[rrhh][doc]" 
                                    class="input-field font-mono text-lg" placeholder="00000000" maxlength="15" autocomplete="off" 
                                    value="{{ $detalle->contenido['rrhh']['doc'] ?? '' }}">
                                <div id="rrhh-loading" class="absolute right-4 top-3.5 hidden">
                                    <div class="animate-spin h-5 w-5 border-2 border-blue-600 border-t-transparent rounded-full"></div>
                                </div>
                            </div>
                        </div>

                        <div id="rrhh_results" class="md:col-span-12 grid grid-cols-1 md:grid-cols-2 gap-6 pt-8 mt-4 border-t border-slate-50">
                            <div>
                                <label class="label-field">Apellido Paterno</label>
                                <input type="text" name="contenido[rrhh][apellido_paterno]" class="input-field uppercase search-target" required 
                                    value="{{ $detalle->contenido['rrhh']['apellido_paterno'] ?? '' }}">
                            </div>
                            <div>
                                <label class="label-field">Apellido Materno</label>
                                <input type="text" name="contenido[rrhh][apellido_materno]" class="input-field uppercase search-target" required 
                                    value="{{ $detalle->contenido['rrhh']['apellido_materno'] ?? '' }}">
                            </div>
                            <div class="md:col-span-2">
                                <label class="label-field">Nombres Completos</label>
                                <input type="text" name="contenido[rrhh][nombres]" class="input-field uppercase search-target" required 
                                    value="{{ $detalle->contenido['rrhh']['nombres'] ?? '' }}">
                            </div>
                            <div>
                                <label class="label-field">Email</label>
                                <input type="email" name="contenido[rrhh][email]" class="input-field lowercase" 
                                    value="{{ $detalle->contenido['rrhh']['email'] ?? '' }}">
                            </div>
                            <div>
                                <label class="label-field">Celular / Teléfono</label>
                                <input type="text" name="contenido[rrhh][telefono]" class="input-field" 
                                    value="{{ $detalle->contenido['rrhh']['telefono'] ?? '' }}">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- CARD: EQUIPOS --}}
                <div class="glass-panel p-10" x-data="{ items: {{ json_encode($acta->equiposComputo()->where('modulo', 'gestion_administrativa')->get()->values() ?? []) }} }">
                    <div class="flex justify-between items-center mb-8">
                        <h3 class="text-xl font-bold text-slate-800 uppercase tracking-tight flex items-center gap-3">
                            <i data-lucide="monitor" class="w-6 h-6 text-slate-400"></i> Inventario Tecnológico
                        </h3>
                        <button type="button" @click="items.push({descripcion:'MONITOR', cantidad:1, estado:'BUENO', propio:'SI'})" class="bg-blue-600 text-white px-6 py-3 rounded-2xl font-black text-[10px] uppercase tracking-widest shadow-lg shadow-blue-100 active:scale-95 transition-all">
                            + Añadir Recurso
                        </button>
                    </div>
                    <table class="table-custom">
                        <thead>
                            <tr><th>Descripción</th><th class="text-center">Cant</th><th>Estado</th><th>Propiedad</th><th></th></tr>
                        </thead>
                        <tbody>
                            <template x-for="(item, index) in items" :key="index">
                                <tr>
                                    <td><select x-model="item.descripcion" :name="`equipos[${index}][descripcion]`" class="input-field border-none bg-transparent py-2">
                                        <option value="MONITOR">MONITOR</option><option value="CPU">CPU</option><option value="IMPRESORA">IMPRESORA</option><option value="TICKETERA">TICKETERA</option><option value="LECTOR DNI">LECTOR DNI-E</option>
                                    </select></td>
                                    <td><input type="number" x-model="item.cantidad" :name="`equipos[${index}][cantidad]`" class="input-field border-none bg-transparent py-2 text-center font-black"></td>
                                    <td><select x-model="item.estado" :name="`equipos[${index}][estado]`" class="input-field border-none bg-transparent py-2 text-blue-600 font-bold">
                                        <option value="BUENO">OPERATIVO</option><option value="REGULAR">REGULAR</option><option value="MALO">DEFICIENTE</option>
                                    </select></td>
                                    <td><select x-model="item.propio" :name="`equipos[${index}][propio]`" class="text-[10px] font-black text-slate-500 bg-slate-100 rounded-full border-none px-2 py-1">
                                        <option value="SI">PROPIO</option><option value="NO">EXTERNO</option>
                                    </select></td>
                                    <td><button type="button" @click="items.splice(index, 1)" class="text-slate-300 hover:text-red-500 transition-colors"><i data-lucide="trash-2" class="w-4 h-4"></i></button></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- COLUMNA DERECHA --}}
            <div class="lg:col-span-4 space-y-8">
                <div class="glass-panel p-8 bg-blue-600 text-white shadow-2xl shadow-blue-200 text-center">
                    <h4 class="text-[11px] font-black uppercase tracking-[0.2em] mb-6">Evidencia de Monitoreo</h4>
                    <label class="relative block w-full h-48 border-2 border-dashed border-blue-400 rounded-3xl cursor-pointer hover:bg-blue-700 transition-all overflow-hidden group">
                        <div id="placeholder" class="absolute inset-0 flex flex-col items-center justify-center p-4 {{ isset($detalle->contenido['foto_evidencia']) ? 'opacity-0' : '' }}">
                            <i data-lucide="camera" class="w-10 h-10 text-blue-200 mb-2"></i>
                            <span class="text-[10px] font-black uppercase tracking-widest text-blue-100">Capturar Foto</span>
                        </div>
                        <img id="preview" src="{{ isset($detalle->contenido['foto_evidencia']) ? asset('storage/'.$detalle->contenido['foto_evidencia']) : '' }}" class="absolute inset-0 w-full h-full object-cover {{ isset($detalle->contenido['foto_evidencia']) ? '' : 'hidden' }}">
                        <input type="file" name="foto_evidencia" class="hidden" accept="image/*" onchange="handleImage(event)">
                    </label>
                </div>
                
                <div class="glass-panel p-8">
                    <label class="label-field text-center italic">Observaciones Técnicas</label>
                    <textarea name="contenido[comentarios]" rows="6" maxlength="2000" class="input-field bg-slate-50 border-none" placeholder="Reporte de hallazgos...">{{ $detalle->contenido['comentarios'] ?? '' }}</textarea>
                </div>
            </div>
        </div>

        <div class="flex justify-center pt-12 pb-20 border-t border-slate-100">
            <button type="submit" class="bg-slate-900 text-white px-20 py-6 rounded-[2.5rem] font-black text-xs uppercase tracking-[0.4em] shadow-2xl hover:bg-blue-600 transition-all active:scale-95">
                Validar y Sincronizar Registro
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    function handleImage(e) {
        const file = e.target.files[0];
        if (file && file.size > 2 * 1024 * 1024) {
            Swal.fire('Error', 'La imagen supera los 2MB.', 'error');
            e.target.value = ''; return;
        }
        const reader = new FileReader();
        reader.onload = () => {
            const out = document.getElementById('preview');
            out.src = reader.result;
            out.classList.remove('hidden');
            document.getElementById('placeholder').classList.add('opacity-0');
        };
        reader.readAsDataURL(file);
    }

    $(document).ready(function() {
        if (typeof lucide !== 'undefined') lucide.createIcons();

        function ejecutarFiltro() {
            let doc = $('#doc_rrhh').val().trim();
            if (doc.length < 8) return;

            $('#rrhh-loading').removeClass('hidden');
            
            // CONSTRUCCIÓN DE URL SEGURA
            let urlTemplate = "{{ route('usuario.monitoreo.profesional.buscar', ':doc') }}";
            let urlFinal = urlTemplate.replace(':doc', doc);

            $.ajax({
                url: urlFinal,
                method: 'GET',
                dataType: 'json',
                success: function(data) {
                    $('#rrhh-loading').addClass('hidden');
                    
                    if (data.exists) {
                        // JALAR DATOS Y BLOQUEAR CAMPOS
                        $('input[name="contenido[rrhh][apellido_paterno]"]').val(data.apellido_paterno).addClass('text-fetched').prop('readonly', true);
                        $('input[name="contenido[rrhh][apellido_materno]"]').val(data.apellido_materno).addClass('text-fetched').prop('readonly', true);
                        $('input[name="contenido[rrhh][nombres]"]').val(data.nombres).addClass('text-fetched').prop('readonly', true);
                        $('input[name="contenido[rrhh][email]"]').val(data.email).prop('readonly', false);
                        $('input[name="contenido[rrhh][telefono]"]').val(data.telefono).prop('readonly', false);
                        
                        Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: 'Personal Identificado', showConfirmButton: false, timer: 1500 });
                    } else {
                        // LIMPIAR Y HABILITAR PARA REGISTRO NUEVO
                        $('.search-target').val('').removeClass('text-fetched').prop('readonly', false);
                        $('input[name="contenido[rrhh][email]"]').val('').prop('readonly', false);
                        $('input[name="contenido[rrhh][telefono]"]').val('').prop('readonly', false);
                    }
                },
                error: function(xhr) {
                    $('#rrhh-loading').addClass('hidden');
                    console.error("Error AJAX:", xhr.status);
                }
            });
        }

        // DISPARADORES
        $('#doc_rrhh').on('keyup', function() {
            if ($(this).val().trim().length === 8) ejecutarFiltro();
        });

        $('#doc_rrhh').on('keydown', function(e) {
            if (e.keyCode === 13) {
                e.preventDefault();
                ejecutarFiltro();
            }
        });

        $('#doc_rrhh').on('blur', function() {
            ejecutarFiltro();
        });
    });
</script>
@endpush