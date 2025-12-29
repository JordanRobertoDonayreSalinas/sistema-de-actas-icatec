@extends('layouts.usuario')

@section('title', 'Gestión Administrativa')

@push('styles')
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
<style>
    body { background-color: #f8fafc; }
    .card-minimal { @apply bg-white rounded-3xl border border-slate-200/60 shadow-sm p-8 mb-6; }
    .input-clean { @apply w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-sm font-semibold text-slate-700 outline-none focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/5 transition-all; }
    .label-clean { @apply block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2 ml-1; }
    
    /* Toggles Estilo Moderno */
    .option-btn { @apply flex-1 py-3 px-4 rounded-xl font-bold text-xs uppercase transition-all border-2 flex items-center justify-center gap-2; }
    .btn-active { @apply bg-slate-900 border-slate-900 text-white shadow-md; }
    .btn-inactive { @apply bg-white border-slate-100 text-slate-400 hover:bg-slate-50; }

    .loading-pulse { animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite; }
    @keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: .5; } }
</style>
@endpush

@section('content')
<div class="max-w-5xl mx-auto py-10 px-6">
    
    {{-- Navegación e Identificación --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-10 gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">01. Gestión Administrativa</h1>
            <p class="text-slate-500 font-medium flex items-center gap-2 mt-1">
                <i data-lucide="hospital" class="w-4 h-4"></i> {{ $acta->establecimiento->nombre }}
            </p>
        </div>
        <a href="{{ route('usuario.monitoreo.modulos', $acta->id) }}" class="flex items-center gap-2 text-slate-400 hover:text-slate-600 font-bold text-xs uppercase tracking-widest bg-white px-5 py-3 rounded-2xl border border-slate-200 shadow-sm transition-all">
            <i data-lucide="chevron-left" class="w-4 h-4"></i> Volver al Panel
        </a>
    </div>

    <form action="{{ route('usuario.monitoreo.guardarDetalle', $acta->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="modulo_nombre" value="gestion_administrativa">

        {{-- SECCIÓN 1: RECURSOS HUMANOS --}}
        <div class="card-minimal">
            <div class="flex items-center gap-3 mb-8 border-b border-slate-50 pb-5">
                <div class="p-2.5 bg-blue-50 text-blue-600 rounded-xl"><i data-lucide="users" class="w-5 h-5"></i></div>
                <h3 class="font-bold text-slate-800">Personal de Recursos Humanos</h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="md:col-span-1">
                    <label class="label-clean">Tipo de Doc.</label>
                    <select name="contenido[rrhh][tipo_doc]" class="input-clean">
                        <option value="DNI">DNI - NACIONAL</option>
                        <option value="C.E">CARNET EXTRANJERÍA</option>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="label-clean">Número de Documento</label>
                    <div class="relative">
                        <input type="text" id="doc_rrhh" name="contenido[rrhh][doc]" class="input-clean font-mono text-base" placeholder="Ingrese para buscar...">
                        <div id="rrhh-status" class="absolute right-4 top-3.5 hidden"><div class="loading-pulse text-blue-500 font-black text-[10px]">BUSCANDO...</div></div>
                    </div>
                </div>

                <div class="md:col-span-3 grid grid-cols-1 md:grid-cols-2 gap-4 bg-slate-50/50 p-6 rounded-3xl border border-slate-100 mt-2">
                    <div class="space-y-4">
                        <div>
                            <label class="label-clean">Apellido Paterno</label>
                            <input type="text" name="contenido[rrhh][apellido_paterno]" class="input-clean bg-white uppercase" required>
                        </div>
                        <div>
                            <label class="label-clean">Apellido Materno</label>
                            <input type="text" name="contenido[rrhh][apellido_materno]" class="input-clean bg-white uppercase" required>
                        </div>
                        <div>
                            <label class="label-clean">Nombres</label>
                            <input type="text" name="contenido[rrhh][nombres]" class="input-clean bg-white uppercase" required>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div>
                            <label class="label-clean">Email</label>
                            <input type="email" name="contenido[rrhh][email]" class="input-clean bg-white lowercase" placeholder="correo@ejemplo.com">
                        </div>
                        <div>
                            <label class="label-clean">Teléfono</label>
                            <input type="text" name="contenido[rrhh][telefono]" class="input-clean bg-white" placeholder="999 999 999">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- SECCIÓN 2: ACCESOS SIHCE --}}
        <div class="card-minimal" x-data="{ cuenta_sihce: 'SI' }">
            <div class="flex items-center gap-3 mb-8 border-b border-slate-50 pb-5">
                <div class="p-2.5 bg-indigo-50 text-indigo-600 rounded-xl"><i data-lucide="key" class="w-5 h-5"></i></div>
                <h3 class="font-bold text-slate-800">Credenciales SIHCE</h3>
            </div>

            <p class="text-sm text-slate-500 font-semibold mb-5">¿Cuenta con usuario y contraseña activa en el sistema?</p>
            <div class="flex gap-4 max-w-md mb-8">
                <input type="hidden" name="contenido[cuenta_sihce]" :value="cuenta_sihce">
                <button type="button" @click="cuenta_sihce = 'SI'" :class="cuenta_sihce === 'SI' ? 'btn-active' : 'btn-inactive'" class="option-btn">SÍ, CUENTA</button>
                <button type="button" @click="cuenta_sihce = 'NO'" :class="cuenta_sihce === 'NO' ? 'btn-active' : 'btn-inactive'" class="option-btn">NO CUENTA</button>
            </div>

            <div x-show="cuenta_sihce === 'NO'" x-transition class="p-8 bg-slate-50 rounded-[2.5rem] border border-slate-200">
                <label class="label-clean text-blue-600">Responsable alterno de programación</label>
                <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                    <input type="text" id="doc_prog" name="contenido[programador][doc]" class="md:col-span-4 input-clean bg-white" placeholder="DNI...">
                    <div id="data_prog" class="md:col-span-8 grid grid-cols-2 gap-3">
                        <input type="text" name="contenido[programador][apellido_paterno]" placeholder="Ap. Paterno" class="input-clean bg-white uppercase">
                        <input type="text" name="contenido[programador][apellido_materno]" placeholder="Ap. Materno" class="input-clean bg-white uppercase">
                        <input type="text" name="contenido[programador][nombres]" placeholder="Nombres Completos" class="col-span-2 input-clean bg-white uppercase">
                    </div>
                </div>
            </div>
        </div>

        {{-- SECCIÓN 3: CAPACITACIÓN Y EQUIPAMIENTO --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            {{-- Soporte --}}
            <div class="card-minimal">
                <h3 class="font-bold text-slate-800 mb-6 flex items-center gap-2"><i data-lucide="message-square" class="w-4 h-4 text-blue-500"></i> Soporte Técnico</h3>
                <div class="space-y-5">
                    <div>
                        <label class="label-clean">¿A quién comunica dificultades?</label>
                        <select name="inst_a_quien_comunica" class="input-clean">
                            <option value="DIRESA">DIRESA</option>
                            <option value="MINSA">MINSA</option>
                            <option value="JEFE DE ESTABLECIMIENTO">JEFE DE ESTABLECIMIENTO</option>
                        </select>
                    </div>
                    <div>
                        <label class="label-clean">Medio utilizado</label>
                        <select name="medio_que_utiliza" class="input-clean">
                            <option value="WHATSAPP">WHATSAPP</option>
                            <option value="TELEFONO">TELÉFONO</option>
                            <option value="EMAIL">EMAIL</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- Alcance --}}
            <div class="card-minimal">
                <h3 class="font-bold text-slate-800 mb-6 flex items-center gap-2"><i data-lucide="calendar" class="w-4 h-4 text-blue-500"></i> Periodo Programado</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="label-clean">Mes</label>
                        <select name="contenido[prog_mes]" class="input-clean">
                            @foreach(['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'] as $m)
                                <option value="{{ $m }}">{{ $m }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="label-clean">Año</label>
                        <input type="number" name="contenido[prog_anio]" value="2025" class="input-clean">
                    </div>
                </div>
            </div>
        </div>

        {{-- SECCIÓN 4: EQUIPOS --}}
        <div class="card-minimal" x-data="{ items: [] }">
            <div class="flex justify-between items-center mb-8">
                <h3 class="font-bold text-slate-800 flex items-center gap-2"><i data-lucide="monitor" class="w-5 h-5 text-blue-500"></i> Equipamiento del Área</h3>
                <button type="button" @click="items.push({desc:'', cant:1, est:'BUENO', prop:'SI'})" class="text-blue-600 font-bold text-xs uppercase flex items-center gap-2 hover:bg-blue-50 px-4 py-2 rounded-xl transition-all">
                    <i data-lucide="plus-circle" class="w-4 h-4"></i> Añadir Equipo
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="text-left border-b border-slate-100 text-slate-400 text-[10px] font-black uppercase tracking-widest">
                            <th class="pb-4 px-2">Descripción</th>
                            <th class="pb-4 px-2 text-center">Cantidad</th>
                            <th class="pb-4 px-2">Estado</th>
                            <th class="pb-4 px-2">Propiedad</th>
                            <th class="pb-4"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        <template x-for="(item, index) in items" :key="index">
                            <tr>
                                <td class="py-4 px-2">
                                    <select x-model="item.desc" :name="`equipos[${index}][descripcion]`" class="input-clean py-2 text-xs">
                                        <option value="MONITOR">MONITOR</option>
                                        <option value="CPU">CPU</option>
                                        <option value="TECLADO">TECLADO</option>
                                        <option value="MOUSE">MOUSE</option>
                                        <option value="IMPRESORA">IMPRESORA</option>
                                    </select>
                                </td>
                                <td class="py-4 px-2">
                                    <input type="number" x-model="item.cant" :name="`equipos[${index}][cantidad]`" class="input-clean py-2 text-xs text-center mx-auto w-20">
                                </td>
                                <td class="py-4 px-2">
                                    <select x-model="item.est" :name="`equipos[${index}][estado]`" class="input-clean py-2 text-xs">
                                        <option value="BUENO">BUENO</option>
                                        <option value="REGULAR">REGULAR</option>
                                        <option value="MALO">MALO</option>
                                    </select>
                                </td>
                                <td class="py-4 px-2">
                                    <select x-model="item.prop" :name="`equipos[${index}][propio]`" class="input-clean py-2 text-[10px] bg-slate-100 border-none font-black text-slate-500">
                                        <option value="SI">PROPIO</option>
                                        <option value="NO">EXTERNO</option>
                                    </select>
                                </td>
                                <td class="py-4 text-right">
                                    <button type="button" @click="items.splice(index, 1)" class="text-slate-300 hover:text-red-500 p-2"><i data-lucide="trash-2" class="w-4 h-4"></i></button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
                <div x-show="items.length === 0" class="py-10 text-center text-slate-300 italic text-sm">No se han registrado equipos</div>
            </div>
        </div>

        {{-- SECCIÓN 5: EVIDENCIA --}}
        <div class="card-minimal">
            <h3 class="font-bold text-slate-800 mb-6 flex items-center gap-2"><i data-lucide="camera" class="w-4 h-4 text-blue-500"></i> Evidencia Fotográfica</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
                <label class="flex flex-col items-center justify-center w-full h-44 border-2 border-dashed border-slate-200 rounded-[2.5rem] cursor-pointer hover:bg-slate-50 transition-all overflow-hidden group">
                    <div id="placeholder" class="flex flex-col items-center justify-center pt-5 pb-6">
                        <i data-lucide="image-plus" class="w-10 h-10 text-slate-300 mb-3 group-hover:scale-110 transition-transform"></i>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Subir Imagen</p>
                    </div>
                    <img id="preview" class="hidden w-full h-full object-cover">
                    <input type="file" name="foto_evidencia" class="hidden" accept="image/*" required onchange="previewFile(event)">
                </label>

                <div class="space-y-4">
                    <label class="label-clean">Observaciones Finales</label>
                    <textarea name="contenido[comentarios]" rows="4" class="input-clean" placeholder="Escriba aquí los comentarios del entrevistado..."></textarea>
                </div>
            </div>
        </div>

        {{-- BOTÓN GUARDAR --}}
        <div class="flex justify-end mt-10">
            <button type="submit" class="bg-blue-600 text-white px-12 py-5 rounded-[2rem] font-black text-sm uppercase tracking-[0.2em] shadow-xl shadow-blue-200 hover:bg-slate-900 hover:shadow-none transition-all flex items-center gap-4 group">
                <span>Guardar Módulo</span>
                <i data-lucide="check-circle" class="w-5 h-5 group-hover:rotate-12 transition-transform"></i>
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    function previewFile(event) {
        const reader = new FileReader();
        reader.onload = () => {
            const out = document.getElementById('preview');
            out.src = reader.result;
            out.classList.remove('hidden');
            document.getElementById('placeholder').classList.add('hidden');
        };
        reader.readAsDataURL(event.target.files[0]);
    }

    $(document).ready(function() {
        lucide.createIcons();

        function setupAjaxSearch(inputId, containerId) {
            $(`#${inputId}`).on('blur', function() {
                let doc = $(this).val();
                if(doc.length < 8) return;
                
                $(`#rrhh-status`).show();
                
                $.get(`/usuario/monitoreo/profesional/buscar/${doc}`, function(data) {
                    $(`#rrhh-status`).hide();
                    if(data.exists) {
                        let c = $(`[name*="rrhh"]`).closest('.grid');
                        c.find('[name*="apellido_paterno"]').val(data.apellido_paterno);
                        c.find('[name*="apellido_materno"]').val(data.apellido_materno);
                        c.find('[name*="nombres"]').val(data.nombres);
                        c.find('[name*="email"]').val(data.email);
                        c.find('[name*="telefono"]').val(data.telefono);
                        
                        Swal.fire({
                            toast: true, position: 'top-end', icon: 'success',
                            title: 'Profesional identificado', showConfirmButton: false, timer: 2000
                        });
                    }
                });
            });
        }
        setupAjaxSearch('doc_rrhh');
        setupAjaxSearch('doc_prog');
    });
</script>
@endpush