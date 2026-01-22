@extends('layouts.usuario')

@section('title', 'Crear Acta de Monitoreo')

@push('styles')
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <style>
        /* Estilos generales y animaciones */
        @keyframes fade-in { from {opacity:0; transform:translateY(15px);} to {opacity:1; transform:translateY(0);} }
        .animate-fade-in { animation: fade-in 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
        
        /* Estilos de Autocomplete */
        .ui-autocomplete { border-radius: 1.25rem !important; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1) !important; border: 1px solid #e2e8f0 !important; z-index: 9999 !important; padding: 0.5rem !important; background: white !important; }
        .ui-menu-item-wrapper { padding: 10px 15px !important; border-radius: 0.75rem !important; font-size: 12px !important; font-weight: 600 !important; }
        .ui-state-active { background: #6366f1 !important; color: white !important; border: none !important; }

        /* Estilos de Tabla */
        .tabla-contenedor { overflow-x: auto; }
        .tabla-profesional { width: 100%; border-collapse: separate; border-spacing: 0 0.5rem; }
        .tabla-profesional th { padding: 0.75rem; color: #64748b; font-weight: 800; text-transform: uppercase; font-size: 10px; text-align: left; }
        .tabla-profesional td { padding: 0.75rem; background: #ffffff; border-top: 1px solid #f1f5f9; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
        .tabla-profesional td:first-child { border-left: 1px solid #f1f5f9; border-radius: 1rem 0 0 1rem; }
        .tabla-profesional td:last-child { border-right: 1px solid #f1f5f9; border-radius: 0 1rem 1rem 0; }
        
        /* Inputs */
        .input-inline { width: 100%; border: 1px solid #e2e8f0; background: #f8fafc; padding: 0.5rem 0.75rem; border-radius: 0.75rem; font-size: 11px; font-weight: 700; text-transform: uppercase; }
        .input-inline:focus { background: white; border-color: #6366f1; outline: none; }
        
        /* Grid de Info Establecimiento */
        .info-grid-estab { display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.5rem; margin-top: 1rem; }
        .info-box-estab { background: #f8fafc; border: 1px solid #f1f5f9; padding: 0.6rem 0.75rem; border-radius: 1rem; }
        .info-label { display: block; font-size: 7px; font-weight: 900; color: #94a3b8; text-transform: uppercase; margin-bottom: 2px; }
        .info-value { display: block; font-size: 10px; font-weight: 800; color: #334155; text-transform: uppercase; }
        .info-editable { background: #fff !important; border: 1px solid #6366f1 !important; }
        .input-editable { color: #6366f1 !important; font-weight: 900 !important; }

        /* Carga de Imágenes */
        .preview-img { width: 100%; height: 120px; object-cover: cover; border-radius: 1rem; display: none; }
        .drop-zone { border: 2px dashed #e2e8f0; border-radius: 1.5rem; height: 120px; display: flex; flex-direction: column; align-items: center; justify-content: center; cursor: pointer; transition: all 0.3s; }
        .drop-zone:hover { border-color: #6366f1; background: #f8fafc; }
        
        /* Ajuste para que el componente de búsqueda se vea bien integrado */
        #componente_busqueda input { background-color: #f1f5f9; border-color: #e2e8f0; }
        #componente_busqueda input:focus { background-color: #ffffff; border-color: #6366f1; }
    </style>
@endpush

@section('content')
<div class="py-6 bg-slate-50 min-h-screen">
    <div class="max-w-full mx-auto px-4">
        
        {{-- ALERTA DE ERRORES LARAVEL --}}
        @if ($errors->any())
            <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-xl shadow-sm animate-fade-in">
                <div class="flex items-center gap-3">
                    <i data-lucide="alert-circle" class="text-red-500 w-6 h-6"></i>
                    <div>
                        <h4 class="text-red-800 font-bold text-sm uppercase">No se pudo guardar el acta</h4>
                        <ul class="text-red-600 text-xs mt-1 list-disc list-inside font-medium">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <form id="monitoreoForm" action="{{ route('usuario.monitoreo.store') }}" method="POST" enctype="multipart/form-data" class="animate-fade-in">
            @csrf
            <input type="hidden" name="implementador" id="implementador_input" value="{{ Auth::user()->apellido_paterno }} {{ Auth::user()->apellido_materno }} {{ Auth::user()->name }}">

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                
                {{-- COLUMNA IZQUIERDA --}}
                <div class="lg:col-span-4 space-y-6">
                    {{-- TARJETA IMPLEMENTADOR --}}
                    <div class="p-6 bg-slate-900 rounded-[2.5rem] text-white shadow-xl relative overflow-hidden">
                        <div class="flex items-center gap-4 mb-6">
                            <div class="h-11 w-11 rounded-xl bg-indigo-500 flex items-center justify-center">
                                <i data-lucide="user-check" class="w-5 h-5 text-white"></i>
                            </div>
                            <div>
                                <p class="text-[8px] font-black text-indigo-300 uppercase tracking-widest">Implementador</p>
                                <h2 class="text-sm font-bold uppercase">
                                    {{ Auth::user()->apellido_paterno }} {{ Auth::user()->apellido_materno }} {{ Auth::user()->name }}
                                </h2>
                            </div>
                        </div>
                        <div class="bg-white/5 p-4 rounded-2xl border border-white/10">
                            <label class="text-[9px] font-bold text-slate-400 uppercase block mb-1">Fecha de Monitoreo</label>
                            <input type="date" name="fecha" value="{{ date('Y-m-d') }}" required class="w-full bg-transparent border-none p-0 text-lg font-black text-white focus:ring-0">
                        </div>
                    </div>

                    {{-- TARJETA ESTABLECIMIENTO --}}
                    <div class="p-6 bg-white rounded-[2.5rem] border border-slate-200 shadow-sm">
                        <h3 class="text-slate-800 font-black text-[10px] uppercase tracking-widest mb-6 flex items-center gap-2">
                            <i data-lucide="hospital" class="w-4 h-4 text-indigo-600"></i> Datos del Establecimiento
                        </h3>
                        <div class="space-y-4">
                            <div class="relative">
                                <label class="text-[9px] font-black text-slate-400 uppercase mb-2 block">Buscar IPRESS</label>
                                <input type="text" id="establecimiento_search" required autocomplete="off" placeholder="Nombre o IPRESS"
                                       class="w-full px-4 py-3 bg-slate-50 border-2 border-slate-100 rounded-xl focus:bg-white focus:border-indigo-500 outline-none font-bold text-xs">
                                <input type="hidden" name="establecimiento_id" id="establecimiento_id" required>
                            </div>

                            <div class="info-grid-estab">
                                <div class="info-box-estab info-editable">
                                    <span class="info-label text-indigo-600">Categoría (Editable)</span>
                                    <input type="text" id="categoria" name="categoria" class="info-value input-editable bg-transparent border-none p-0 w-full focus:ring-0" placeholder="—">
                                </div>
                                <div class="info-box-estab"><span class="info-label">Red</span><input type="text" id="red" readonly class="info-value bg-transparent border-none p-0 w-full focus:ring-0" value="—"></div>
                                <div class="info-box-estab"><span class="info-label">Microred</span><input type="text" id="microred" readonly class="info-value bg-transparent border-none p-0 w-full focus:ring-0" value="—"></div>
                                <div class="info-box-estab"><span class="info-label">Provincia</span><input type="text" id="provincia" readonly class="info-value bg-transparent border-none p-0 w-full focus:ring-0" value="—"></div>
                                <div class="info-box-estab col-span-2"><span class="info-label">Distrito</span><input type="text" id="distrito" readonly class="info-value bg-transparent border-none p-0 w-full focus:ring-0" value="—"></div>
                            </div>

                            <div class="p-4 bg-indigo-50 rounded-2xl border border-indigo-100">
                                <label class="text-[9px] font-black text-indigo-600 uppercase mb-2 block">Jefe de Establecimiento (Editable)</label>
                                <input type="text" name="responsable" id="responsable" required 
                                       class="w-full px-3 py-2 bg-white border border-indigo-200 rounded-lg focus:border-indigo-500 font-bold text-xs uppercase text-slate-700">
                            </div>
                        </div>
                    </div>

                    {{-- SECCIÓN DE IMÁGENES --}}
                    <div class="p-6 bg-white rounded-[2.5rem] border border-slate-200 shadow-sm">
                        <h3 class="text-slate-800 font-black text-[10px] uppercase tracking-widest mb-4 flex items-center gap-2">
                            <i data-lucide="camera" class="w-4 h-4 text-indigo-600"></i> Evidencia (Máx. 2)
                        </h3>
                        <div class="grid grid-cols-2 gap-4">
                            @for($i=1; $i<=2; $i++)
                            <div class="relative group">
                                <input type="file" name="imagenes[]" id="file_{{$i}}" accept="image/*" class="hidden file-input">
                                <label for="file_{{$i}}" class="drop-zone" id="label_{{$i}}">
                                    <i data-lucide="image-plus" class="w-6 h-6 text-slate-300 mb-1"></i>
                                    <span class="text-[8px] font-bold text-slate-400 uppercase">Subir Foto {{$i}}</span>
                                </label>
                                <img src="#" id="preview_{{$i}}" class="preview-img">
                                <button type="button" onclick="resetFile({{$i}})" id="remove_{{$i}}" class="hidden absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 shadow-lg">
                                    <i data-lucide="x" class="w-3 h-3"></i>
                                </button>
                            </div>
                            @endfor
                        </div>
                    </div>
                </div>

                {{-- COLUMNA DERECHA --}}
                <div class="lg:col-span-8">
                    <div class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-slate-200 h-full flex flex-col">
                        
                        <div class="flex flex-col gap-6 mb-8">
                            <div class="flex items-center justify-between">
                                <h3 class="text-slate-800 font-black text-xs uppercase tracking-widest flex items-center gap-2">
                                    <i data-lucide="users" class="w-4 h-4 text-indigo-600"></i> Equipo de Monitoreo
                                </h3>
                            </div>

                            {{-- COMPONENTE BUSCADOR --}}
                            {{-- Contenedor con ID específico para limitar la búsqueda de inputs --}}
                            <div id="componente_busqueda" class="bg-slate-50 p-5 rounded-3xl border border-slate-200">
                                <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-3">
                                    1. Buscar Profesional (RENIEC / BD Local)
                                </label>
                                
                                {{-- Pasamos dummy data para que el componente renderice vacío --}}
                                @php $dummy = (object)['contenido' => []]; @endphp
                                <x-busqueda-profesional :detalle="$dummy" prefix="busqueda_temporal" />
                                
                                <div class="mt-4 flex justify-end">
                                    <button type="button" id="btn_agregar_a_tabla" 
                                            class="bg-slate-900 text-white px-5 py-3 rounded-xl text-[10px] font-black uppercase tracking-wider hover:bg-indigo-600 transition-colors flex items-center gap-2 shadow-lg">
                                        <i data-lucide="plus-circle" class="w-4 h-4"></i> 
                                        2. Agregar a la Lista
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- TABLA DE EQUIPO --}}
                        <div class="tabla-contenedor flex-1">
                            <table class="tabla-profesional">
                                <thead>
                                    <tr>
                                        <th width="100">Tipo Doc.</th>
                                        <th width="120">N° Documento</th>
                                        <th>Ap. Paterno</th>
                                        <th>Ap. Materno</th>
                                        <th>Nombres</th>
                                        <th>Cargo</th>
                                        <th>Institución</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody id="body_equipo">
                                    <tr id="empty_row">
                                        <td colspan="8" class="text-center py-20">
                                            <div class="flex flex-col items-center opacity-40">
                                                <i data-lucide="user-plus" class="w-12 h-12 text-slate-300 mb-3"></i>
                                                <span class="text-slate-400 italic text-xs font-black uppercase tracking-widest">
                                                    La lista está vacía
                                                </span>
                                                <span class="text-[10px] text-slate-300 font-bold mt-1">
                                                    Utilice el buscador superior para añadir integrantes
                                                </span>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-8 flex justify-end">
                            <button type="submit" class="bg-indigo-600 text-white px-10 py-4 rounded-2xl font-black text-sm shadow-xl hover:bg-indigo-700 transition-all flex items-center gap-3 transform hover:-translate-y-1">
                                <span>GUARDAR ACTA</span>
                                <i data-lucide="chevron-right" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    lucide.createIcons();

    // 1. Lógica de Imágenes (Sin cambios)
    $(".file-input").on("change", function() {
        const id = $(this).attr('id').split('_')[1];
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $(`#preview_${id}`).attr('src', e.target.result).show();
                $(`#label_${id}`).hide();
                $(`#remove_${id}`).show();
            }
            reader.readAsDataURL(file);
        }
    });

    window.resetFile = function(id) {
        $(`#file_${id}`).val("");
        $(`#preview_${id}`).hide();
        $(`#label_${id}`).show();
        $(`#remove_${id}`).hide();
    };

    // 2. Autocomplete Establecimiento (Sin cambios)
    $("#establecimiento_search").autocomplete({
        minLength: 2,
        source: "{{ route('establecimientos.buscar') }}",
        select: function(e, ui) {
            $("#establecimiento_id").val(ui.item.id);
            $("#distrito").val(ui.item.distrito || '—');
            $("#provincia").val(ui.item.provincia || '—');
            $("#categoria").val(ui.item.categoria || '—');
            $("#red").val(ui.item.red || '—');
            $("#microred").val(ui.item.microred || '—');
            $("#responsable").val(ui.item.responsable || '');
            setTimeout(() => $("#categoria").focus(), 100);
            return true;
        }
    });

    // 3. LÓGICA DE AGREGAR EQUIPO (CORREGIDA CON COMODINES)
    // Usamos selectores que buscan "contiene" (*=) para ignorar prefijos complejos
    $('#btn_agregar_a_tabla').on('click', function() {
        
        // Buscamos dentro del div #componente_busqueda cualquier input que contenga el nombre del campo
        const tipoDoc = $('#componente_busqueda select[name*="[tipo_doc]"]').val();
        const doc     = $('#componente_busqueda input[name*="[doc]"]').val();
        const apePat  = $('#componente_busqueda input[name*="[apellido_paterno]"]').val();
        const apeMat  = $('#componente_busqueda input[name*="[apellido_materno]"]').val();
        const nombres = $('#componente_busqueda input[name*="[nombres]"]').val();
        
        // Validación: Si alguno de los campos clave está vacío
        if (!doc || !apePat || !nombres) {
            Swal.fire({
                title: 'Faltan Datos',
                text: 'Por favor busque y seleccione un profesional válido usando la lupa antes de agregar.',
                icon: 'warning',
                confirmButtonColor: '#4f46e5'
            });
            return;
        }

        // Si pasa, construimos el objeto
        const data = {
            tipo_doc: tipoDoc,
            doc: doc,
            apellido_paterno: apePat,
            apellido_materno: apeMat,
            nombres: nombres,
            cargo: 'MONITOR',
            institucion: 'DIRESA'
        };

        addMiembroRow(data);

        // Limpiamos los campos visuales para el siguiente (usando los mismos selectores comodín)
        $('#componente_busqueda input').val(''); 
        $('#componente_busqueda select').prop('selectedIndex', 0);
    });

    function addMiembroRow(data) {
        $('#empty_row').hide();
        const doc = data.doc;
        
        if ($(`#row_${doc}`).length > 0) {
            Swal.fire('Duplicado', 'Este integrante ya está en la lista.', 'info');
            return;
        }

        const row = `
            <tr id="row_${doc}" class="animate-fade-in">
                <td>
                    <select name="equipo[${doc}][tipo_doc]" class="input-inline border-slate-200 py-1">
                        <option value="DNI" ${(data.tipo_doc == 'DNI') ? 'selected' : ''}>DNI</option>
                        <option value="PASS" ${(data.tipo_doc == 'PASS') ? 'selected' : ''}>PASS</option>
                        <option value="CEX" ${(data.tipo_doc == 'CEX') ? 'selected' : ''}>CEX</option>
                    </select>
                </td>
                <td>
                    <div class="flex flex-col leading-tight">
                        <span class="text-indigo-600 font-bold text-xs">${doc}</span>
                        <input type="hidden" name="equipo[${doc}][doc]" value="${doc}">
                    </div>
                </td>
                <td><input type="text" name="equipo[${doc}][apellido_paterno]" value="${data.apellido_paterno}" class="input-inline" readonly></td>
                <td><input type="text" name="equipo[${doc}][apellido_materno]" value="${data.apellido_materno}" class="input-inline" readonly></td>
                <td><input type="text" name="equipo[${doc}][nombres]" value="${data.nombres}" class="input-inline" readonly></td>
                <td><input type="text" name="equipo[${doc}][cargo]" value="${data.cargo}" class="input-inline border-slate-200 text-indigo-600 font-bold" required placeholder="CARGO"></td>
                <td>
                    <select name="equipo[${doc}][institucion]" class="input-inline border-slate-200">
                        <option value="DIRESA">DIRESA</option>
                        <option value="MINSA">MINSA</option>
                        <option value="U.E RED DE SALUD ICA">U.E RED DE SALUD ICA</option>
                        <option value="OTRO">OTRO</option>
                    </select>
                </td>
                <td class="text-center">
                    <button type="button" onclick="$(this).closest('tr').remove()" class="text-red-400 hover:text-red-600 transition-colors"><i data-lucide="trash-2" class="w-4 h-4"></i></button>
                </td>
            </tr>`;
        
        $('#body_equipo').append(row);
        lucide.createIcons();
    }

    // 4. SUBMIT FORMULARIO
    $('#monitoreoForm').on('submit', function(e) {
        e.preventDefault();
        
        // Validar establecimiento
        if (!$('#establecimiento_id').val()) {
            Swal.fire('Atención', 'Debe buscar y seleccionar un establecimiento válido.', 'warning');
            return;
        }

        // Validar equipo
        if ($('#body_equipo tr').not('#empty_row').length === 0) {
            Swal.fire('Atención', 'Debe agregar al menos un integrante al equipo.', 'warning');
            return;
        }

        // Limpiar inputs del buscador temporal para que no se envíen
        $('#componente_busqueda input, #componente_busqueda select').prop('disabled', true);

        // Mayúsculas
        $(this).find('input[type="text"]').not('[name^="busqueda_temporal"]').each(function() {
            $(this).val($(this).val().toUpperCase().trim());
        });

        $('#implementador_input').val("{{ Auth::user()->apellido_paterno }} {{ Auth::user()->apellido_materno }} {{ Auth::user()->name }}".toUpperCase());

        Swal.fire({
            title: '¿Confirmar Registro?',
            text: "Se guardará el acta y se proseguirá con el registro de los módulos",
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, guardar',
            confirmButtonColor: '#4f46e5',
        }).then((result) => { 
            if (result.isConfirmed) { 
                document.getElementById('monitoreoForm').submit(); 
            } else {
                // Si cancela, reactivamos los inputs del buscador por si quiere seguir editando
                $('#componente_busqueda input, #componente_busqueda select').prop('disabled', false);
            }
        });
    });
});
</script>
@endpush