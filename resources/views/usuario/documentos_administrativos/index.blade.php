@extends('layouts.usuario')

@section('title', 'Documentos Administrativos')

@push('styles')
    <style>
        input[type="date"] {
            position: relative; color: #4b5563;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='20' height='20' viewBox='0 0 24 24' fill='none' stroke='%236366f1' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Crect x='3' y='4' width='18' height='18' rx='2' ry='2'/%3E%3Cline x1='16' y1='2' x2='16' y2='6'/%3E%3Cline x1='8' y1='2' x2='8' y2='6'/%3E%3Cline x1='3' y1='10' x2='21' y2='10'/%3E%3C/svg%3E");
            background-repeat: no-repeat; background-position: right 0.5rem center; background-size: 1.1em;
        }
        [x-cloak] { display: none !important; }
        
        .custom-swal-popup { border-radius: 2rem !important; padding: 2.5rem !important; }
        .custom-swal-confirm { border-radius: 1rem !important; font-weight: 700 !important; text-transform: uppercase !important; }
    </style>
@endpush

@section('header-content')
    <h1 class="text-xl font-bold text-slate-800 tracking-tight">Documentos Administrativos</h1>
    <div class="flex items-center gap-2 text-xs text-slate-500 mt-0.5">
        <span>Operaciones</span>
        <i data-lucide="chevron-right" class="w-3 h-3 text-slate-300"></i>
        <span>Gestión de Formatos Administrativos</span>
    </div>
@endsection

@section('content')
<div x-data="{ open: {{ request()->anyFilled(['estado', 'fecha_inicio', 'fecha_fin']) ? 'true' : 'false' }} }" class="w-full space-y-6">
    
    {{-- TARJETA INDIGO SUPERIOR (Resumen de estadísticas) --}}
    <div class="bg-gradient-to-br from-indigo-800 to-indigo-600 p-6 rounded-[2rem] shadow-xl relative overflow-hidden text-white">
        <div class="absolute right-0 top-0 w-64 h-64 bg-white/5 rounded-full -mr-20 -mt-20 blur-3xl"></div>
        <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="flex gap-4">
                <div class="bg-white/10 backdrop-blur-md p-4 rounded-2xl border border-white/10 min-w-[120px]">
                    <span class="block text-2xl font-black">{{ $documentos->total() }}</span>
                    <span class="text-[10px] font-bold uppercase tracking-widest opacity-60">TOTAL</span>
                </div>
                <div class="bg-emerald-500/20 backdrop-blur-md p-4 rounded-2xl border border-emerald-500/20 min-w-[120px]">
                    <span class="block text-2xl font-black text-emerald-400">{{ $countCompletados ?? 0 }}</span>
                    <span class="text-[10px] font-bold uppercase tracking-widest text-emerald-100 opacity-60">FIRMADOS</span>
                </div>
                <div class="bg-amber-500/20 backdrop-blur-md p-4 rounded-2xl border border-amber-500/20 min-w-[120px]">
                    <span class="block text-2xl font-black text-amber-400">{{ $countPendientes ?? 0 }}</span>
                    <span class="text-[10px] font-bold uppercase tracking-widest text-amber-100 opacity-60">PENDIENTES</span>
                </div>
            </div>
            <div class="flex gap-3">
                <button @click="open = !open" class="px-5 py-3 rounded-2xl bg-white/10 hover:bg-white/20 border border-white/10 transition-all font-bold text-sm flex items-center gap-2">
                    <i data-lucide="filter" class="w-4 h-4"></i> Filtros
                </button>
                <a href="{{ route('usuario.documentos.create') }}" class="px-6 py-3 rounded-2xl bg-white text-indigo-700 shadow-lg hover:bg-indigo-50 transition-all font-bold text-sm flex items-center gap-2">
                    <i data-lucide="plus-circle" class="w-5 h-5"></i> Agregar Nuevo
                </a>
            </div>
        </div>
    </div>

    {{-- FILTROS --}}
    <form x-show="open" x-cloak method="GET" action="{{ route('usuario.documentos.index') }}" 
          class="bg-white p-6 rounded-3xl shadow-sm border border-slate-200 grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
            <label class="block text-[10px] font-black text-slate-400 uppercase mb-1.5 ml-1">Estado Firma</label>
            <select name="estado" class="w-full text-xs font-bold border-slate-100 bg-slate-50 rounded-xl py-3 focus:ring-4 focus:ring-indigo-500/10">
                <option value="">Todos</option>
                <option value="firmada" {{ request('estado') == 'firmada' ? 'selected' : '' }}>Firmado (Cargado)</option>
                <option value="pendiente" {{ request('estado') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
            </select>
        </div>
        <div>
            <label class="block text-[10px] font-black text-slate-400 uppercase mb-1.5 ml-1">Fecha Início</label>
            <input type="date" name="fecha_inicio" value="{{ $fecha_inicio }}" class="w-full text-xs font-bold border-slate-100 bg-slate-50 rounded-xl py-3">
        </div>
        <div>
            <label class="block text-[10px] font-black text-slate-400 uppercase mb-1.5 ml-1">Fecha Fin</label>
            <input type="date" name="fecha_fin" value="{{ $fecha_fin }}" class="w-full text-xs font-bold border-slate-100 bg-slate-50 rounded-xl py-3">
        </div>
        <div class="flex items-end gap-2">
            <button type="submit" class="flex-1 h-11 bg-indigo-600 text-white rounded-xl font-bold shadow-lg hover:bg-indigo-700 transition-all flex items-center justify-center gap-2">
                <i data-lucide="search" class="w-4 h-4"></i> Buscar
            </button>
            <a href="{{ route('usuario.documentos.index') }}" class="w-11 h-11 bg-slate-100 text-slate-400 rounded-xl flex items-center justify-center hover:bg-slate-200">
                <i data-lucide="refresh-cw" class="w-4 h-4"></i>
            </a>
        </div>
    </form>

    {{-- TABLA DE RESULTADOS --}}
    <div class="bg-white rounded-[2rem] shadow-xl border border-slate-200/60 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100">
                        <th class="px-6 py-5 font-black text-slate-400 uppercase tracking-widest">#</th>
                        <th class="px-4 py-5 font-black text-slate-400 uppercase tracking-widest">Fecha</th>
                        <th class="px-4 py-5 font-black text-slate-400 uppercase tracking-widest">Profesional Solicitante</th>
                        <th class="px-4 py-5 font-black text-slate-400 uppercase tracking-widest">Establecimiento</th>
                        <th class="px-4 py-5 font-black text-slate-400 uppercase tracking-widest">Formato</th>
                        <th class="px-4 py-5 font-black text-slate-400 uppercase tracking-widest text-center">Estado</th>
                        <th class="pr-8 py-5 font-black text-slate-400 uppercase tracking-widest text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($documentos as $doc)
                        <tr class="hover:bg-slate-50/80 transition-all group">
                            <td class="px-6 py-4 font-mono font-bold text-slate-400">{{ $doc->id }}</td>
                            <td class="px-4 py-4 font-bold text-slate-700">{{ \Carbon\Carbon::parse($doc->fecha)->format('d/m/Y') }}</td>
                            <td class="px-4 py-4">
                                <div class="flex flex-col">
                                    <span class="font-black text-slate-800 text-sm tracking-tight uppercase">
                                        {{ $doc->profesional_nombre }} {{ $doc->profesional_apellido_paterno }} {{ $doc->profesional_apellido_materno }}
                                    </span>
                                    <span class="text-[10px] text-slate-400 uppercase tracking-widest">
                                        {{ $doc->profesional_tipo_doc }}: {{ $doc->profesional_doc }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-4 py-4">
                                <div class="flex flex-col">
                                    <span class="font-medium text-slate-600 uppercase">{{ $doc->establecimiento->nombre }}</span>
                                    <span class="text-[9px] text-slate-400 uppercase tracking-tighter">{{ $doc->establecimiento->distrito }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-4">
                                <span class="px-2 py-0.5 rounded-md bg-indigo-50 text-indigo-600 font-black uppercase text-[9px] border border-indigo-100">
                                    {{ $doc->tipo_formato == 'Compromiso' ? 'Confidencialidad' : 'D. Jurada' }}
                                </span>
                            </td>
                            <td class="px-4 py-4 text-center">
                                @if($doc->pdf_firmado_path)
                                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-emerald-100 text-emerald-700 font-black text-[9px] uppercase">
                                        <i data-lucide="check-circle" class="w-3 h-3"></i> CARGADA
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-amber-100 text-amber-700 font-black text-[9px] uppercase">
                                        <i data-lucide="clock" class="w-3 h-3"></i> PENDENTE
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-4 text-right pr-8">
                                <div class="flex justify-end gap-1.5 opacity-60 group-hover:opacity-100 transition-opacity">
                                    {{-- Ver PDF generado --}}
                                    <a href="{{ route('usuario.documentos.pdf', $doc->id) }}" target="_blank" 
                                       class="p-2.5 rounded-xl bg-slate-50 text-slate-400 hover:text-red-600 transition-all border border-slate-100" 
                                       title="Ver Formato Generado">
                                        <i data-lucide="file-text" class="w-4 h-4"></i>
                                    </a>
                                    
                                    {{-- Subir escaneado firmado --}}
                                    <button onclick="abrirModalSubirFirmado({{ $doc->id }}, '{{ $doc->profesional_nombre }} {{ $doc->profesional_apellido_paterno }}')" 
                                            class="p-2.5 rounded-xl bg-indigo-50 text-indigo-600 hover:bg-indigo-100 transition-all border border-indigo-100" 
                                            title="Subir Archivo Firmado">
                                        <i data-lucide="upload-cloud" class="w-4 h-4"></i>
                                    </button>
                                    
                                    {{-- Ver archivo firmado si existe --}}
                                    @if($doc->pdf_firmado_path)
                                        <a href="{{ asset('storage/' . $doc->pdf_firmado_path) }}" target="_blank" 
                                           class="p-2.5 rounded-xl bg-emerald-50 text-emerald-600 hover:bg-emerald-100 transition-all border border-emerald-100" 
                                           title="Ver Documento Escaneado">
                                            <i data-lucide="eye" class="w-4 h-4"></i>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-6 py-20 text-center text-slate-300 font-bold uppercase tracking-widest text-sm">No se encontraron documentos registrados</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if ($documentos->hasPages()) <div class="mt-8">{{ $documentos->links() }}</div> @endif
</div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => { if (typeof lucide !== 'undefined') lucide.createIcons(); });

        function abrirModalSubirFirmado(id, profesional) {
            Swal.fire({
                title: '<h2 class="text-xl font-black text-slate-800 tracking-tight uppercase text-center">Subir Archivo Firmado</h2>',
                html: `
                    <div class="mt-4 text-left">
                        <div class="p-4 rounded-2xl bg-indigo-50 border border-indigo-100 mb-4 text-center">
                            <p class="text-[10px] font-black text-indigo-400 uppercase tracking-widest mb-1">Profesional</p>
                            <p class="text-sm font-bold text-indigo-600 leading-tight">${profesional}</p>
                        </div>
                        <div class="flex items-start gap-3 p-4 rounded-2xl bg-amber-50 border border-amber-100">
                            <div class="p-2 rounded-lg bg-amber-100 text-amber-600">
                                <i data-lucide="alert-circle" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-amber-800 mb-0.5">Atención</p>
                                <p class="text-[11px] text-amber-700/80 leading-snug">Adjunte el <b>PDF escaneado</b> con las firmas y sellos físicos originales.</p>
                            </div>
                        </div>
                    </div>
                `,
                input: 'file',
                inputAttributes: { 'accept': 'application/pdf', 'aria-label': 'Seleccionar PDF' },
                showCancelButton: true,
                confirmButtonText: 'Subir Documento',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#4f46e5',
                showLoaderOnConfirm: true,
                customClass: { popup: 'custom-swal-popup', confirmButton: 'custom-swal-confirm' },
                didOpen: () => { if (window.lucide) window.lucide.createIcons(); },
                preConfirm: (file) => {
                    if (!file) { Swal.showValidationMessage('Debe seleccionar un archivo'); return; }
                    const formData = new FormData();
                    formData.append('pdf_firmado', file);
                    formData.append('_token', '{{ csrf_token() }}');

                    return fetch(`/usuario/documentos-administrativos/${id}/subir-firmado`, {
                        method: 'POST',
                        body: formData,
                        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                    })
                    .then(response => {
                        if (!response.ok) return response.json().then(err => { throw new Error(err.message || 'Error en el servidor'); });
                        return response.json();
                    })
                    .catch(error => { Swal.showValidationMessage(`Error: ${error.message}`); });
                }
            }).then((result) => { if (result.isConfirmed) { location.reload(); } });
        }
    </script>
@endpush