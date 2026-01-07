@extends('layouts.usuario')

@section('title', 'Generar Documento Administrativo')

@push('styles')
    {{-- jQuery UI para Autocomplete (Mismo que en Asistencia Técnica) --}}
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <style>
        .ui-autocomplete { border-radius: 0.75rem; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); border: 1px solid #e2e8f0; z-index: 9999 !important; }
        .ui-menu-item-wrapper.ui-state-active { background-color: #6366f1 !important; border: none !important; color: white !important; }
        input:read-only { background-color: #f8fafc; cursor: not-allowed; }
    </style>
@endpush

@section('content')
<div class="max-w-5xl mx-auto pb-20">
    <form action="{{ route('usuario.documentos.store') }}" method="POST" class="space-y-6">
        @csrf
        
        <div class="bg-white p-8 rounded-[2.5rem] shadow-xl border border-slate-200">
            <h2 class="text-2xl font-black text-slate-800 mb-6 uppercase tracking-tighter flex items-center gap-3">
                <i data-lucide="file-plus" class="w-8 h-8 text-indigo-600"></i>
                Nuevo Documento Administrativo
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Fecha</label>
                    <input type="date" name="fecha" value="{{ date('Y-m-d') }}" class="w-full rounded-xl border-slate-200 font-bold text-slate-700 py-3">
                </div>
                <div class="md:col-span-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Seleccionar Formato a Generar</label>
                    <select name="tipo_formato" required class="w-full rounded-xl border-slate-200 font-bold text-slate-700 py-3">
                        <option value="Compromiso">Compromiso de Confidencialidad SIHCE</option>
                        <option value="DeclaracionJurada">Declaración Jurada SIHCE (Ventanilla Única)</option>
                    </select>
                </div>
            </div>

            {{-- SECCIÓN 1: ESTABLECIMIENTO (Autocompletado igual al Acta de Asistencia) --}}
            <div class="p-6 bg-slate-50 rounded-[2rem] border border-slate-100 mb-6">
                <label class="text-[10px] font-black text-blue-600 uppercase tracking-widest ml-1 block mb-2">Buscador de Establecimiento</label>
                <input type="text" id="establecimiento" name="establecimiento_nombre" class="w-full rounded-xl border-slate-200 py-3 font-bold mb-4" placeholder="Ingresa nombre o código de establecimiento..." autocomplete="off">
                <input type="hidden" name="establecimiento_id" id="establecimiento_id">

                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div>
                        <label class="text-[9px] text-slate-400 font-bold uppercase">Provincia</label>
                        <input type="text" id="provincia" name="provincia" readonly class="w-full bg-transparent border-none text-xs font-black text-slate-700 p-0">
                    </div>
                    <div>
                        <label class="text-[9px] text-slate-400 font-bold uppercase">Distrito</label>
                        <input type="text" id="distrito" name="distrito" readonly class="w-full bg-transparent border-none text-xs font-black text-slate-700 p-0">
                    </div>
                    <div>
                        <label class="text-[9px] text-slate-400 font-bold uppercase">Microred / Red</label>
                        <div class="flex gap-1">
                            <input type="text" id="microred" readonly class="w-1/2 bg-transparent border-none text-[10px] font-black text-slate-700 p-0">
                            <input type="text" id="red" readonly class="w-1/2 bg-transparent border-none text-[10px] font-black text-slate-700 p-0">
                        </div>
                    </div>
                    <div>
                        <label class="text-[9px] text-slate-400 font-bold uppercase">Jefe (Responsable)</label>
                        <input type="text" id="responsable" name="area_oficina" readonly class="w-full bg-transparent border-none text-xs font-black text-slate-700 p-0">
                    </div>
                </div>
            </div>

            {{-- SECCIÓN 2: PROFESIONAL (Consumiendo tu componente) --}}
            <div class="p-6 bg-indigo-50/50 rounded-[2rem] border border-indigo-100 mb-6">
                <label class="text-[10px] font-black text-indigo-600 uppercase tracking-widest ml-1 block mb-4">Datos del Profesional Solicitante</label>
                
                {{-- Reutilización de tu componente de búsqueda --}}
                @include('components.busqueda-profesional')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
                    <div>
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Cargo / Rol Específico</label>
                        <input type="text" name="cargo_rol" required class="w-full rounded-xl border-slate-200 text-sm font-bold uppercase py-3" placeholder="Ej: MEDICO ASISTENCIAL">
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Correo Institucional / Personal</label>
                        <input type="email" name="correo_electronico" required class="w-full rounded-xl border-slate-200 text-sm font-bold py-3" placeholder="ejemplo@minsa.gob.pe">
                    </div>
                </div>
            </div>

            {{-- SECCIÓN 3: ACCESOS (Requerido por los documentos Word) --}}
            <div class="p-6 bg-white rounded-[2rem] border border-slate-200">
                <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1 block mb-4">Sistemas / Módulos Solicitados</label>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    @php
                        $sistemas = ['Consulta Externa: Medicina', 'Bandeja Electrónica', 'Firma Digital', 'Triaje', 'Admisión', 'Farmacia', 'Laboratorio', 'Emergencia'];
                    @endphp
                    @foreach($sistemas as $sistema)
                    <label class="flex items-center gap-3 p-3 rounded-xl border border-slate-100 hover:bg-slate-50 cursor-pointer transition-all">
                        <input type="checkbox" name="sistemas_acceso[]" value="{{ $sistema }}" class="rounded text-indigo-600">
                        <span class="text-[11px] font-bold text-slate-600">{{ $sistema }}</span>
                    </label>
                    @endforeach
                </div>
            </div>

            <div class="pt-8 flex justify-end gap-4">
                <a href="{{ route('usuario.monitoreo.index') }}" class="px-8 py-4 rounded-2xl font-black text-slate-400 uppercase text-xs">Cancelar</a>
                <button type="submit" class="px-10 py-4 rounded-2xl bg-indigo-600 text-white font-black uppercase text-xs shadow-xl shadow-indigo-200 hover:bg-indigo-700 transition-all">
                    Generar Documento
                </button>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
<script>
    $(function() {
        // Implementación del Autocomplete (Lógica exacta de tu Acta de Asistencia)
        $("#establecimiento").autocomplete({
            minLength: 1,
            delay: 200,
            source: function(request, response) {
                $.ajax({
                    url: "{{ route('establecimientos.buscar') }}",
                    method: "GET",
                    dataType: "json",
                    data: { term: request.term },
                    success: function(data) {
                        response(data);
                    }
                });
            },
            select: function(event, ui) {
                event.preventDefault();
                // Rellenado automático de campos según EstablecimientoController
                $("#establecimiento_id").val(ui.item.id);
                $("#establecimiento").val(ui.item.value);
                $("#provincia").val(ui.item.provincia);
                $("#distrito").val(ui.item.distrito);
                $("#microred").val(ui.item.microred);
                $("#red").val(ui.item.red);
                $("#responsable").val(ui.item.responsable);
                return false;
            }
        });
    });
</script>
@endpush