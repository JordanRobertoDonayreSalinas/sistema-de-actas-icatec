@extends('layouts.usuario')
@section('title', 'Actas de Reunión')

@section('header-content')
    <div>
        <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Actas de Reunión</h2>
        <div class="flex items-center gap-2 text-xs text-slate-500 mt-1 font-medium">
            <span class="text-indigo-600">Operaciones</span>
            <span>&bull;</span>
            <span>Listado General</span>
        </div>
    </div>
@endsection

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <h3 class="text-lg font-bold text-slate-800">Registros Recientes</h3>
            <span class="px-2.5 py-1 text-xs font-semibold bg-indigo-100 text-indigo-700 rounded-lg">
                {{ $total_reuniones }} total
            </span>
        </div>
        <a href="{{ route('usuario.reuniones.create') }}" 
           class="group flex items-center justify-center gap-2 px-5 py-2.5 bg-indigo-600 text-white font-semibold rounded-xl hover:bg-indigo-700 transition-all shadow-sm shadow-indigo-200">
            <i data-lucide="plus" class="w-4 h-4 group-hover:rotate-90 transition-transform"></i>
            Nueva Acta
        </a>
    </div>

    {{-- Filtros Básicos --}}
    <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100">
        <form action="{{ route('usuario.reuniones.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1">Título</label>
                <input type="text" name="titulo" value="{{ request('titulo') }}" placeholder="Buscar título..." 
                       class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-lg focus:border-indigo-500 focus:bg-white transition-colors text-sm outline-none">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1">Desde</label>
                <input type="date" name="fecha_desde" value="{{ request('fecha_desde') }}" 
                       class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-lg focus:border-indigo-500 focus:bg-white transition-colors text-sm outline-none">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1">Hasta</label>
                <input type="date" name="fecha_hasta" value="{{ request('fecha_hasta') }}" 
                       class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-lg focus:border-indigo-500 focus:bg-white transition-colors text-sm outline-none">
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="w-full px-4 py-2 bg-slate-800 text-white font-semibold rounded-lg hover:bg-slate-900 transition-colors text-sm flex items-center justify-center gap-2">
                    <i data-lucide="search" class="w-4 h-4"></i> Buscar
                </button>
                <a href="{{ route('usuario.reuniones.index') }}" class="px-4 py-2 bg-slate-100 text-slate-600 font-semibold rounded-lg hover:bg-slate-200 transition-colors text-sm flex items-center justify-center">
                    <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                </a>
            </div>
        </form>
    </div>

    {{-- Tabla --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="bg-slate-50 border-b border-slate-100 text-slate-500">
                    <tr>
                        <th class="px-6 py-4 font-semibold w-16">ID</th>
                        <th class="px-6 py-4 font-semibold">Fecha</th>
                        <th class="px-6 py-4 font-semibold">Título de Reunión</th>
                        <th class="px-6 py-4 font-semibold">Estado Doc.</th>
                        <th class="px-6 py-4 font-semibold w-24 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($reuniones as $item)
                    <tr class="hover:bg-slate-50 transition-colors group">
                        <td class="px-6 py-4">
                            <span class="font-mono text-xs font-bold text-slate-400">#{{ str_pad($item->id, 4, '0', STR_PAD_LEFT) }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-medium text-slate-700">{{ \Carbon\Carbon::parse($item->fecha_reunion)->format('d/m/Y') }}</div>
                            <div class="text-xs text-slate-400">{{ \Carbon\Carbon::parse($item->hora_reunion)->format('H:i') }} hrs</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-bold text-slate-800">{{ $item->titulo_reunion }}</div>
                            <div class="text-xs text-slate-500 max-w-xs truncate">{{ $item->nombre_institucion }}</div>
                        </td>
                        <td class="px-6 py-4">
                            @if($item->anulado)
                                <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-red-50 text-red-600 border border-red-100">
                                    <i data-lucide="ban" class="w-3.5 h-3.5"></i>
                                    <span class="text-xs font-bold uppercase tracking-wider">Anulada</span>
                                </div>
                            @else
                                <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-emerald-50 text-emerald-600 border border-emerald-100">
                                    <i data-lucide="check-circle-2" class="w-3.5 h-3.5"></i>
                                    <span class="text-xs font-bold uppercase tracking-wider">Activa</span>
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center gap-2">
                                @if(!$item->anulado)
                                    <a href="{{ route('usuario.reuniones.edit', $item->id) }}" 
                                       class="p-2 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors" title="Editar">
                                        <i data-lucide="edit-2" class="w-4 h-4"></i>
                                    </a>
                                    <a href="{{ route('usuario.reuniones.pdf', $item->id) }}" target="_blank"
                                       class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors" title="PDF">
                                        <i data-lucide="file-down" class="w-4 h-4"></i>
                                    </a>
                                @endif
                                <button type="button" onclick="confirmarAnulacion({{ $item->id }}, {{ $item->anulado ? 'true' : 'false' }})"
                                        class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="{{ $item->anulado ? 'Reactivar' : 'Anular' }}">
                                    <i data-lucide="{{ $item->anulado ? 'refresh-cw' : 'ban' }}" class="w-4 h-4"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center">
                            <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-slate-100 text-slate-400 mb-3">
                                <i data-lucide="inbox" class="w-6 h-6"></i>
                            </div>
                            <h3 class="font-medium text-slate-900">No hay registros</h3>
                            <p class="text-sm text-slate-500 mt-1">No se encontraron actas de reunión que coincidan con los filtros.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($reuniones->hasPages())
        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50">
            {{ $reuniones->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
function confirmarAnulacion(id, isAnulado) {
    const title = isAnulado ? '¿Reactivar acta?' : '¿Anular acta?';
    const text = isAnulado ? 'El acta volverá a estar activa y editable.' : 'El acta quedará inactiva y no se podrá editar.';
    const confButton = isAnulado ? 'Sí, reactivar' : 'Sí, anular';

    Swal.fire({
        title: title,
        text: text,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: isAnulado ? '#10b981' : '#ef4444',
        cancelButtonColor: '#64748b',
        confirmButtonText: confButton,
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/usuario/actas-reunion/${id}/anular`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    Swal.fire('Éxito', data.message, 'success').then(() => window.location.reload());
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            });
        }
    });
}
</script>
@endpush
