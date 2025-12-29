@extends('layouts.usuario')

@section('title', 'Editar Acta de Monitoreo #' . $monitoreo->id)

@push('styles')
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <style>
        @keyframes fade-in { from {opacity:0; transform:translateY(15px);} to {opacity:1; transform:translateY(0);} }
        .animate-fade-in { animation: fade-in 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
        
        .ui-autocomplete { 
            border-radius: 1.25rem !important; 
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1) !important; 
            border: 1px solid #e2e8f0 !important; 
            z-index: 9999 !important; padding: 0.5rem !important; background: white !important;
        }

        .ui-menu-item-wrapper { padding: 10px 15px !important; border-radius: 0.75rem !important; font-size: 12px !important; font-weight: 600 !important; }
        .ui-state-active { background: #6366f1 !important; color: white !important; border: none !important; }

        .tabla-contenedor { overflow-x: auto; }
        .tabla-profesional { width: 100%; border-collapse: separate; border-spacing: 0 0.5rem; }
        .tabla-profesional th { padding: 0.75rem; color: #64748b; font-weight: 800; text-transform: uppercase; font-size: 10px; text-align: left; }
        .tabla-profesional td { padding: 0.75rem; background: #ffffff; border-top: 1px solid #f1f5f9; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
        .tabla-profesional td:first-child { border-left: 1px solid #f1f5f9; border-radius: 1rem 0 0 1rem; }
        .tabla-profesional td:last-child { border-right: 1px solid #f1f5f9; border-radius: 0 1rem 1rem 0; }
        
        .input-inline { 
            width: 100%; border: 1px solid #e2e8f0; background: #f8fafc; padding: 0.5rem 0.75rem; 
            border-radius: 0.75rem; font-size: 11px; font-weight: 700; text-transform: uppercase; 
        }
        .input-inline:focus { background: white; border-color: #6366f1; outline: none; }
        
        .info-grid-estab { display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.5rem; margin-top: 1rem; }
        .info-box-estab { background: #f8fafc; border: 1px solid #f1f5f9; padding: 0.6rem 0.75rem; border-radius: 1rem; }
        .info-label { display: block; font-size: 7px; font-weight: 900; color: #94a3b8; text-transform: uppercase; margin-bottom: 2px; }
        .info-value { display: block; font-size: 10px; font-weight: 800; color: #334155; text-transform: uppercase; }
        
        .info-editable { background: #fff !important; border: 1px solid #6366f1 !important; }
        .input-editable { color: #6366f1 !important; font-weight: 900 !important; }
    </style>
@endpush

@section('content')
<div class="py-6 bg-slate-50 min-h-screen">
    <div class="max-w-full mx-auto px-4">
        <form id="monitoreoForm" action="{{ route('usuario.monitoreo.update', $monitoreo->id) }}" method="POST" class="animate-fade-in">
            @csrf
            @method('PUT')
            
            <input type="hidden" name="implementador" id="implementador_input" value="{{ $monitoreo->implementador }}">
            {{-- Campo oculto para decidir el redireccionamiento --}}
            <input type="hidden" name="redirect_to" id="redirect_to" value="index">

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                
                {{-- COLUMNA IZQUIERDA --}}
                <div class="lg:col-span-4 space-y-6">
                    {{-- Bloque Responsable y Fecha --}}
                    <div class="p-6 bg-slate-900 rounded-[2.5rem] text-white shadow-xl relative overflow-hidden">
                        <div class="flex items-center gap-4 mb-6">
                            <div class="h-11 w-11 rounded-xl bg-indigo-500 flex items-center justify-center">
                                <i data-lucide="user-check" class="w-5 h-5 text-white"></i>
                            </div>
                            <div>
                                <p class="text-[8px] font-black text-indigo-300 uppercase tracking-widest">Implementador</p>
                                <h2 class="text-sm font-bold uppercase">{{ Auth::user()->name }} {{ Auth::user()->apellido_paterno }}</h2>
                            </div>
                        </div>
                        <div class="bg-white/5 p-4 rounded-2xl border border-white/10">
                            <label class="text-[9px] font-bold text-slate-400 uppercase block mb-1">Fecha de Monitoreo</label>
                            <input type="date" name="fecha" value="{{ $monitoreo->fecha }}" required class="w-full bg-transparent border-none p-0 text-lg font-black text-white focus:ring-0">
                        </div>
                    </div>

                    {{-- Bloque Establecimiento --}}
                    <div class="p-6 bg-white rounded-[2.5rem] border border-slate-200 shadow-sm">
                        <h3 class="text-slate-800 font-black text-[10px] uppercase tracking-widest mb-6 flex items-center gap-2">
                            <i data-lucide="hospital" class="w-4 h-4 text-indigo-600"></i> Datos del Establecimiento
                        </h3>
                        <div class="space-y-4">
                            <div class="relative">
                                <label class="text-[9px] font-black text-slate-400 uppercase mb-2 block">Buscar IPRESS</label>
                                <input type="text" id="establecimiento_search" required autocomplete="off" placeholder="Nombre o Código"
                                       value="{{ $monitoreo->establecimiento->nombre }}"
                                       class="w-full px-4 py-3 bg-slate-50 border-2 border-slate-100 rounded-xl focus:bg-white focus:border-indigo-500 outline-none font-bold text-xs">
                                <input type="hidden" name="establecimiento_id" id="establecimiento_id" value="{{ $monitoreo->establecimiento_id }}" required>
                            </div>

                            <div class="info-grid-estab">
                                <div class="info-box-estab info-editable">
                                    <span class="info-label text-indigo-600">Categoría (Editable)</span>
                                    <input type="text" id="categoria" name="categoria" 
                                           value="{{ $monitoreo->categoria_congelada ?? $monitoreo->establecimiento->categoria }}" 
                                           class="info-value input-editable bg-transparent border-none p-0 w-full focus:ring-0">
                                </div>
                                <div class="info-box-estab"><span class="info-label">Red</span><input type="text" id="red" readonly class="info-value bg-transparent border-none p-0 w-full focus:ring-0" value="{{ $monitoreo->establecimiento->red }}"></div>
                                <div class="info-box-estab"><span class="info-label">Microred</span><input type="text" id="microred" readonly class="info-value bg-transparent border-none p-0 w-full focus:ring-0" value="{{ $monitoreo->establecimiento->microred }}"></div>
                                <div class="info-box-estab"><span class="info-label">Provincia</span><input type="text" id="provincia" readonly class="info-value bg-transparent border-none p-0 w-full focus:ring-0" value="{{ $monitoreo->establecimiento->provincia }}"></div>
                                <div class="info-box-estab col-span-2"><span class="info-label">Distrito</span><input type="text" id="distrito" readonly class="info-value bg-transparent border-none p-0 w-full focus:ring-0" value="{{ $monitoreo->establecimiento->distrito }}"></div>
                            </div>

                            <div class="p-4 bg-indigo-50 rounded-2xl border border-indigo-100">
                                <label class="text-[9px] font-black text-indigo-600 uppercase mb-2 block">Jefe de Establecimiento (Editable)</label>
                                <input type="text" name="responsable" id="responsable" value="{{ $monitoreo->responsable }}" required 
                                       class="w-full px-3 py-2 bg-white border border-indigo-200 rounded-lg focus:border-indigo-500 font-bold text-xs uppercase text-slate-700">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- COLUMNA DERECHA --}}
                <div class="lg:col-span-8">
                    <div class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-slate-200 h-full flex flex-col">
                        <div class="flex flex-col md:flex-row items-center justify-between gap-4 mb-8">
                            <h3 class="text-slate-800 font-black text-xs uppercase tracking-widest flex items-center gap-2">
                                <i data-lucide="users" class="w-4 h-4 text-indigo-600"></i> Equipo de Monitoreo
                            </h3>
                            <div class="flex items-center gap-2 bg-slate-900 p-1.5 rounded-2xl shadow-xl w-full md:w-auto">
                                <input type="text" id="buscar_miembro_inteligente" placeholder="DOC o Apellido..." class="text-[11px] border-none bg-transparent focus:ring-0 font-bold w-full md:w-64 pl-4 text-white">
                                <button type="button" id="btn_manual_add" class="bg-indigo-600 text-white p-2 rounded-xl"><i data-lucide="plus" class="w-4 h-4"></i></button>
                            </div>
                        </div>

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
                                <tbody id="body_equipo"></tbody>
                            </table>
                        </div>

                        {{-- ACCIONES DE GUARDADO --}}
                        <div class="mt-8 flex flex-wrap justify-end gap-3">
                            <a href="{{ route('usuario.monitoreo.index') }}" class="px-6 py-4 rounded-2xl font-black text-xs text-slate-400 hover:bg-slate-100 transition-all uppercase">Cancelar</a>
                            
                            {{-- Botón para ir a los módulos directamente --}}
                            <button type="button" onclick="submitForm('modulos')" class="bg-slate-800 text-white px-8 py-4 rounded-2xl font-black text-xs shadow-xl hover:bg-slate-900 transition-all flex items-center gap-3">
                                <i data-lucide="layers" class="w-4 h-4 text-indigo-400"></i>
                                <span>GUARDAR Y EDITAR MÓDULOS</span>
                            </button>

                            {{-- Botón para guardar y volver al index --}}
                            <button type="button" onclick="submitForm('index')" class="bg-indigo-600 text-white px-8 py-4 rounded-2xl font-black text-xs shadow-xl hover:bg-indigo-700 transition-all flex items-center gap-3">
                                <i data-lucide="save" class="w-4 h-4"></i>
                                <span>SOLO ACTUALIZAR CABECERA</span>
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
    // Función para manejar el envío según el botón presionado
    function submitForm(destination) {
        $('#redirect_to').val(destination);
        $('#monitoreoForm').submit();
    }

    $(document).ready(function() {
        lucide.createIcons();

        // Carga inicial del equipo
        @foreach($monitoreo->equipo as $persona)
            addMiembroRow({
                tipo_doc: "{{ $persona->tipo_doc ?? 'DNI' }}",
                doc: "{{ $persona->doc }}",
                apellido_paterno: "{{ $persona->apellido_paterno }}",
                apellido_materno: "{{ $persona->apellido_materno }}",
                nombres: "{{ $persona->nombres }}",
                cargo: "{{ $persona->cargo }}",
                institucion: "{{ $persona->institucion }}"
            }, false);
        @endforeach

        // Autocomplete Establecimiento
        $("#establecimiento_search").autocomplete({
            minLength: 2,
            source: "{{ route('establecimientos.buscar') }}",
            focus: function(event, ui) { return false; },
            select: function(e, ui) {
                $("#establecimiento_id").val(ui.item.id);
                $("#distrito").val(ui.item.distrito || '—');
                $("#provincia").val(ui.item.provincia || '—');
                $("#categoria").val(ui.item.categoria || '—');
                $("#red").val(ui.item.red || '—');
                $("#microred").val(ui.item.microred || '—');
                $("#responsable").val(ui.item.responsable || '');
                return true;
            }
        });

        // Buscador Miembros
        $("#buscar_miembro_inteligente").autocomplete({
            minLength: 2,
            source: function(request, response) {
                $.ajax({
                    url: "{{ route('usuario.monitoreo.equipo.filtro') }}",
                    data: { term: request.term },
                    success: function(data) {
                        response($.map(data, function(item) {
                            return {
                                label: (item.apellido_paterno || '') + " " + (item.apellido_materno || '') + ", " + (item.nombres || ''),
                                doc: item.doc || item.documento,
                                data: item
                            };
                        }));
                    }
                });
            },
            select: function(event, ui) {
                addMiembroRow(ui.item.data, false);
                $(this).val('');
                return false;
            }
        });

        // Manual Add
        $('#btn_manual_add').on('click', function() {
            Swal.fire({
                title: 'Agregar Integrante',
                input: 'text',
                showCancelButton: true,
                confirmButtonText: 'Buscar',
                confirmButtonColor: '#4f46e5',
            }).then((result) => {
                if (result.isConfirmed && result.value) {
                    $.get("/usuario/monitoreo/equipo/buscar/" + result.value, function(data) {
                        addMiembroRow(data.exists ? data : { doc: result.value, tipo_doc: 'DNI' }, !data.exists);
                    });
                }
            });
        });

        function addMiembroRow(data, isNew) {
            const doc = data.doc || data.documento;
            if ($(`#row_${doc}`).length > 0) return;
            const tipoDoc = data.tipo_doc || 'DNI';

            const row = `
                <tr id="row_${doc}" class="animate-fade-in">
                    <td>
                        <select name="equipo[${doc}][tipo_doc]" class="input-inline border-slate-200 py-1">
                            <option value="DNI" ${tipoDoc == 'DNI' ? 'selected' : ''}>DNI</option>
                            <option value="PASS" ${tipoDoc == 'PASS' ? 'selected' : ''}>PASS</option>
                            <option value="DIE" ${tipoDoc == 'DIE' ? 'selected' : ''}>DIE</option>
                        </select>
                    </td>
                    <td><span class="text-indigo-600 font-bold text-xs">${doc}</span><input type="hidden" name="equipo[${doc}][doc]" value="${doc}"></td>
                    <td><input type="text" name="equipo[${doc}][apellido_paterno]" value="${data.apellido_paterno || ''}" class="input-inline" required ${isNew?'':'readonly'}></td>
                    <td><input type="text" name="equipo[${doc}][apellido_materno]" value="${data.apellido_materno || ''}" class="input-inline" required ${isNew?'':'readonly'}></td>
                    <td><input type="text" name="equipo[${doc}][nombres]" value="${data.nombres || ''}" class="input-inline" required ${isNew?'':'readonly'}></td>
                    <td><input type="text" name="equipo[${doc}][cargo]" value="${data.cargo || ''}" class="input-inline" required></td>
                    <td>
                        <select name="equipo[${doc}][institucion]" class="input-inline">
                            <option value="DIRESA" ${data.institucion == 'DIRESA' ? 'selected' : ''}>DIRESA</option>
                            <option value="MINSA" ${data.institucion == 'MINSA' ? 'selected' : ''}>MINSA</option>
                            <option value="U.E HOSPITAL DE APOYO NASCA" ${data.institucion == 'U.E HOSPITAL DE APOYO NASCA' ? 'selected' : ''}>U.E HOSPITAL DE APOYO NASCA</option>
                            <option value="U.E HOSPITAL DE APOYO DE PALPA" ${data.institucion == 'U.E HOSPITAL DE APOYO DE PALPA' ? 'selected' : ''}>U.E HOSPITAL DE APOYO DE PALPA</option>
                            <option value="U.E HOSPITAL SAN JOSE DE CHINCHA" ${data.institucion == 'U.E HOSPITAL SAN JOSE DE CHINCHA' ? 'selected' : ''}>U.E HOSPITAL SAN JOSE DE CHINCHA</option>
                            <option value="U.E HOSPITAL SAN JUAN DE DIOS DE PISCO" ${data.institucion == 'U.E HOSPITAL SAN JUAN DE DIOS PISCO' ? 'selected' : ''}>U.E HOSPITAL SAN JUAN DE DIOS PISCO</option>
                            <option value="U.E RED DE SALUD ICA" ${data.institucion == 'U.E RED DE SALUD ICA' ? 'selected' : ''}>U.E RED DE SALUD ICA</option>
                            <option value="OTRO" ${data.institucion == 'OTRO' ? 'selected' : ''}>OTRO</option>
                        </select>
                    </td>
                    <td class="text-center"><button type="button" onclick="$(this).closest('tr').remove()" class="text-red-400 hover:text-red-600"><i data-lucide="trash-2" class="w-4 h-4"></i></button></td>
                </tr>`;
            $('#body_equipo').append(row);
            lucide.createIcons();
        }

        $('#monitoreoForm').on('submit', function(e) {
            e.preventDefault();
            $(this).find('input[type="text"]').not('#implementador_input').each(function() {
                $(this).val($(this).val().toUpperCase().trim());
            });
            this.submit();
        });
    });
</script>
@endpush