@extends('layouts.usuario')

@section('title', 'Nueva Acta de Monitoreo')

@push('styles')
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <style>
        @keyframes fade-in { from {opacity:0; transform:translateY(10px);} to {opacity:1; transform:translateY(0);} }
        .animate-fade-in { animation: fade-in 0.3s ease forwards; }
        
        .ui-autocomplete { 
            border-radius: 0.75rem; 
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); 
            border: 1px solid #e2e8f0; 
            z-index: 9999 !important; 
        }

        .input-auto-fill { background-color: #f0f9ff !important; border-color: #bae6fd !important; color: #0369a1 !important; font-weight: 700; }
        
        .tabla-monitoreo { width: 100%; border-collapse: collapse; border: 1px solid #e2e8f0; }
        .tabla-monitoreo thead th { background-color: #f8fafc; border: 1px solid #e2e8f0; padding: 12px; color: #64748b; font-weight: 800; text-transform: uppercase; font-size: 10px; }
        .tabla-monitoreo tbody td { border: 1px solid #e2e8f0; padding: 8px; background: white; }
    </style>
@endpush

@section('header-content')
    <div class="flex flex-col">
        <h1 class="text-xl font-bold text-slate-800 tracking-tight italic">Nueva Acta de Monitoreo</h1>
        <div class="flex items-center gap-2 text-xs text-slate-500 mt-0.5">
            <span>Operaciones</span>
            <span class="text-slate-300">•</span>
            <span>Paso 1: Cabecera del Acta</span>
        </div>
    </div>
@endsection

@section('content')
<div class="py-8 bg-slate-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4">
        
        {{-- EL FORMULARIO AHORA SE ENFOCA EN LA CABECERA --}}
        <form id="monitoreoForm" action="{{ route('usuario.monitoreo.store') }}" method="POST" enctype="multipart/form-data"
              class="bg-white shadow-xl rounded-3xl p-10 border border-slate-200 animate-fade-in">
            @csrf

            {{-- 1. BLOQUE IMPLEMENTADOR (DATOS LOGUEADO) --}}
            <div class="mb-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-6 p-6 bg-indigo-50/50 rounded-2xl border border-indigo-100 shadow-sm">
                <div class="flex items-center gap-4">
                    <div class="h-14 w-14 rounded-full bg-indigo-600 text-white flex items-center justify-center shadow-lg shadow-indigo-200">
                        <i data-lucide="user-check" class="w-8 h-8"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-indigo-400 uppercase tracking-widest">Implementador Responsable</p>
                        <h2 class="text-lg font-bold text-slate-800 leading-tight">{{ Auth::user()->apellido_paterno }} {{ Auth::user()->apellido_materno }}, {{ Auth::user()->name }}</h2>
                        <input type="hidden" name="implementador" value="{{ Auth::user()->apellido_paterno }} {{ Auth::user()->apellido_materno }} {{ Auth::user()->name }}">
                    </div>
                </div>
                <div class="bg-white px-6 py-3 rounded-xl border border-indigo-100 text-right">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1">Fecha del Acta</label>
                    <input type="date" name="fecha" value="{{ date('Y-m-d') }}" 
                           class="text-sm font-bold text-indigo-600 border-none p-0 focus:ring-0 text-right cursor-pointer">
                </div>
            </div>

            {{-- 2. LOCALIZACIÓN Y RESPONSABLE --}}
            <div class="mb-12">
                <h3 class="text-slate-800 font-black uppercase text-sm tracking-widest mb-6 flex items-center gap-2">
                    <span class="w-8 h-8 rounded-lg bg-slate-800 text-white flex items-center justify-center text-xs">01</span>
                    Localización y Responsable del EESS
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                    <div class="relative group">
                        <label class="block text-[11px] font-bold text-slate-500 uppercase mb-2 ml-1">Buscar Establecimiento de Salud:</label>
                        <div class="relative">
                            <i data-lucide="search" class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400 group-focus-within:text-indigo-600 transition-colors"></i>
                            <input type="text" id="establecimiento_search" placeholder="Ingrese nombre o código RENIPRESS..." 
                                   class="w-full pl-12 pr-4 py-4 border-2 border-slate-100 rounded-2xl focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/5 outline-none transition-all shadow-sm font-medium">
                        </div>
                        <input type="hidden" name="establecimiento_id" id="establecimiento_id">
                    </div>

                    <div class="relative">
                        <label class="block text-[11px] font-bold text-slate-500 uppercase mb-2 ml-1">Jefe / Responsable del Establecimiento:</label>
                        <div class="relative">
                            <i data-lucide="user" class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400"></i>
                            <input type="text" name="responsable" id="responsable" required 
                                   class="w-full pl-12 pr-4 py-4 border-2 border-slate-100 rounded-2xl focus:border-indigo-500 outline-none transition-all shadow-sm font-bold"
                                   placeholder="Se cargará automáticamente...">
                        </div>
                    </div>
                </div>

                {{-- Ubicación Geográfica y Categoría --}}
                <div class="grid grid-cols-2 md:grid-cols-5 gap-4 p-6 bg-slate-50 rounded-3xl border border-slate-200 border-dashed">
                    @foreach(['Distrito' => 'distrito', 'Provincia' => 'provincia', 'Microred' => 'microred', 'Red' => 'red', 'Categoría' => 'categoria'] as $label => $id)
                    <div class="bg-white p-3 rounded-xl border border-slate-100 shadow-sm">
                        <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-1">{{ $label }}</label>
                        <input type="text" id="{{ $id }}" readonly class="w-full bg-transparent border-none p-0 text-slate-600 font-bold text-xs focus:ring-0 cursor-default" value="—">
                    </div>
                    @endforeach
                </div>
            </div>


            {{-- BOTÓN DE GUARDADO Y PASO A MÓDULOS --}}
            <div class="flex justify-center border-t border-slate-100 pt-10">
                <button type="submit" id="btnGuardar" class="bg-indigo-600 text-white px-12 py-5 rounded-2xl font-black text-lg shadow-2xl hover:bg-indigo-700 hover:-translate-y-1 transition-all active:scale-95 flex items-center gap-4">
                    <i data-lucide="arrow-right-circle" class="w-7 h-7"></i> 
                    CONTINUAR A MÓDULOS
                </button>
            </div>

        </form>
    </div>
</div>

{{-- TEMPLATE PARA FILAS --}}
<template id="p-row-template">
    <tr class="animate-fade-in group">
        <td class="text-center font-bold text-slate-400 bg-slate-50/30"></td>
        <td><input type="text" data-name="dni" maxlength="8" required class="w-full border-none focus:ring-0 p-2 text-sm"></td>
        <td><input type="text" data-name="nombres" required class="w-full border-none focus:ring-0 p-2 text-sm uppercase"></td>
        <td><input type="text" data-name="cargo" required class="w-full border-none focus:ring-0 p-2 text-sm"></td>
        <td><input type="text" data-name="modulo" class="w-full border-none focus:ring-0 p-2 text-sm"></td>
        <td class="text-center">
            <button type="button" class="btn-del p-2 text-slate-300 hover:text-red-500 transition-colors">
                <i data-lucide="trash-2" class="w-5 h-5"></i>
            </button>
        </td>
    </tr>
</template>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(function() {
    const buscarUrl = "{{ route('establecimientos.buscar') }}";
    
    // AUTOCOMPLETE
    $("#establecimiento_search").autocomplete({
        minLength: 2,
        source: function(req, res) { $.getJSON(buscarUrl, { term: req.term }, res); },
        select: function(e, ui) {
            $("#establecimiento_id").val(ui.item.id);
            $("#distrito").val(ui.item.distrito || '—');
            $("#provincia").val(ui.item.provincia || '—');
            $("#microred").val(ui.item.microred || '—');
            $("#red").val(ui.item.red || '—');
            $("#categoria").val(ui.item.categoria || '—');
            $("#responsable").val(ui.item.responsable || '').addClass('input-auto-fill');
            $(this).val(ui.item.value);
            return false;
        }
    });

    // TABLA DINÁMICA
    const $tbody = $('#tabla-p-body');
    const $tpl = $('#p-row-template');

    function addRow() {
        const $clone = $($tpl.html());
        $tbody.append($clone);
        reindex();
        lucide.createIcons();
    }

    function reindex() {
        $tbody.find('tr').each(function(i) {
            $(this).find('td:first').text(i + 1);
            $(this).find('input').each(function() {
                const name = $(this).data('name');
                $(this).attr('name', `participantes[${i}][${name}]`);
            });
        });
    }

    $('#btn-add-p').click(addRow);
    $tbody.on('click', '.btn-del', function() { $(this).closest('tr').remove(); reindex(); });
    addRow(); 

    // IMÁGENES PREVIEW
    $('#drop-area').click(() => $('#imagenes').click());
    $('#imagenes').change(function() {
        $('#thumbnails').empty();
        Array.from(this.files).slice(0, 5).forEach(file => {
            const reader = new FileReader();
            reader.onload = (e) => $('#thumbnails').append(`<div class="aspect-square rounded-xl overflow-hidden border shadow-sm"><img src="${e.target.result}" class="w-full h-full object-cover"></div>`);
            reader.readAsDataURL(file);
        });
    });

    // CONFIRMACIÓN
    $('#monitoreoForm').on('submit', function(e) {
        e.preventDefault();
        Swal.fire({
            title: '¿Guardar Cabecera?',
            text: "Se registrará el inicio del acta y pasaremos a llenar los módulos.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#4f46e5',
            confirmButtonText: 'Sí, continuar'
        }).then((res) => { if(res.isConfirmed) this.submit(); });
    });
});
</script>
@endpush