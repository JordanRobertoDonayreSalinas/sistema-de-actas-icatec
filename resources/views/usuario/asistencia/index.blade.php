@extends('layouts.usuario')

@section('title', 'Actas de Asistencia Técnica')

@push('styles')
    <style>
        [x-cloak] { display: none !important; }
        .input-modern {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 0.75rem;
            color: #334155;
            font-size: 0.75rem;
            font-weight: 600;
            padding: 0.6rem 0.75rem;
            transition: all 0.2s;
        }
        .input-modern:focus {
            border-color: #10b981;
            ring: 2px;
            ring-color: #10b981;
            outline: none;
        }
        /* Estilos para Chips de Correo */
        .email-chips-container {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            padding: 0.5rem;
            border: 1px solid #e2e8f0;
            border-radius: 0.75rem;
            background-color: #f8fafc;
            min-height: 45px;
            cursor: text;
        }
        .email-chip {
            display: flex;
            items-center;
            gap: 0.35rem;
            background-color: #10b981;
            color: white;
            padding: 0.25rem 0.6rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            animation: chip-in 0.2s ease-out;
        }
        .email-chip button {
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(0,0,0,0.1);
            border-radius: 50%;
            width: 14px;
            height: 14px;
            transition: background 0.2s;
        }
        .email-chip button:hover {
            background: rgba(255,255,255,0.2);
        }
        .email-chip-input {
            flex: 1;
            min-width: 120px;
            border: none !important;
            background: transparent !important;
            padding: 2px 5px !important;
            font-size: 0.75rem !important;
            outline: none !important;
            box-shadow: none !important;
        }
        @keyframes chip-in {
            from { opacity: 0; transform: scale(0.9); }
            to { opacity: 1; transform: scale(1); }
        }
        .shake {
            animation: shake 0.3s cubic-bezier(.36,.07,.19,.97) both;
            transform: translate3d(0, 0, 0);
            backface-visibility: hidden;
            perspective: 1000px;
        }
        @keyframes shake {
            10%, 90% { transform: translate3d(-1px, 0, 0); }
            20%, 80% { transform: translate3d(2px, 0, 0); }
            30%, 50%, 70% { transform: translate3d(-4px, 0, 0); }
            40%, 60% { transform: translate3d(4px, 0, 0); }
        }
    </style>
@endpush

@section('header-content')
    <h1 class="text-xl font-bold text-slate-800 tracking-tight">Actas de Asistencia Técnica</h1>
    <div class="flex items-center gap-2 text-xs text-slate-500 mt-0.5">
        <span>Operaciones</span>
        <span class="text-slate-300">•</span>
        <span>Actas de Asistencia Técnica</span>
    </div>
@endsection

@section('content')
    <div x-data="{ open: {{ request()->anyFilled(['implementador', 'provincia', 'distrito', 'establecimiento_id', 'firmado']) ? 'true' : 'false' }} }" class="w-full">

        {{-- ETFs --}}
        <div class="bg-gradient-to-r from-emerald-600 to-teal-500 p-5 rounded-2xl shadow-xl mb-6 relative overflow-hidden text-white">
            <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full blur-3xl -mr-16 -mt-16 pointer-events-none"></div>
            <div class="relative z-10 flex flex-col lg:flex-row items-center justify-between gap-6">
                <div class="flex flex-wrap items-center gap-3">
                    <div class="bg-slate-900 text-white rounded-xl px-5 py-2.5 shadow-lg border border-slate-700 flex flex-col items-center min-w-[100px]">
                        <span class="text-2xl font-bold leading-none">{{ $actas->total() }}</span>
                        <span class="text-[0.65rem] uppercase tracking-widest text-slate-400 font-semibold mt-1">Total</span>
                    </div>
                    <div class="bg-white/20 backdrop-blur-md text-white rounded-xl px-5 py-2.5 border border-white/30 flex flex-col items-center min-w-[100px]">
                        <span class="text-2xl font-bold leading-none">{{ $countFirmadas }}</span>
                        <span class="text-[0.65rem] uppercase tracking-widest text-emerald-100 font-semibold mt-1">Firmadas</span>
                    </div>
                    <div class="bg-amber-500 text-white rounded-xl px-5 py-2.5 shadow-lg border border-amber-400 flex flex-col items-center min-w-[100px]">
                        <span class="text-2xl font-bold leading-none">{{ $countPendientes }}</span>
                        <span class="text-[0.65rem] uppercase tracking-widest text-amber-100 font-semibold mt-1">Pendientes</span>
                    </div>
                    <div class="bg-slate-800 text-white rounded-xl px-5 py-2.5 shadow-lg border border-slate-700 flex flex-col items-center min-w-[100px]">
                        <span class="text-2xl font-bold leading-none">{{ $countAnuladas }}</span>
                        <span class="text-[0.65rem] uppercase tracking-widest text-slate-400 font-semibold mt-1">Anuladas</span>
                    </div>
                </div>
                <div class="flex items-center gap-3 w-full lg:w-auto justify-center lg:justify-end mt-2 lg:mt-0">
                    <button @click="open = !open" type="button" class="flex items-center gap-2 px-5 py-3 rounded-xl font-bold text-sm transition-all shadow-lg border border-white/20 text-white bg-white/10 hover:bg-white/20 backdrop-blur-sm">
                        <i data-lucide="filter" class="w-4 h-4" x-show="!open"></i>
                        <i data-lucide="filter-x" class="w-4 h-4" x-show="open" x-cloak></i>
                        <span x-text="open ? 'Ocultar Filtros' : 'Mostrar Filtros'"></span>
                    </button>
                    <a href="{{ route('usuario.actas.create') }}" class="flex items-center gap-2 px-5 py-3 rounded-xl font-bold text-sm transition-all shadow-lg bg-white text-emerald-700 hover:bg-emerald-50 border border-transparent">
                        <i data-lucide="plus-circle" class="w-5 h-5"></i>
                        <span>Nueva Acta</span>
                    </a>
                </div>
            </div>
        </div>

        {{-- FILTROS --}}
        <form x-show="open" x-cloak x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
            method="GET" action="{{ route('usuario.actas.index') }}"
            class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200 mb-6 space-y-4">
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-7 gap-3">
                {{-- Implementador --}}
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1 ml-1">Implementador</label>
                    <select name="implementador" class="input-modern w-full uppercase">
                        <option value="">Todos</option>
                        @foreach ($implementadores as $impl)
                            <option value="{{ $impl }}" {{ request('implementador') == $impl ? 'selected' : '' }}>{{ $impl }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Provincia --}}
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1 ml-1">Provincia</label>
                    <select id="provincia" name="provincia" class="input-modern w-full uppercase">
                        <option value="">Todas</option>
                        @foreach ($provincias as $prov)
                            <option value="{{ $prov }}" {{ request('provincia') == $prov ? 'selected' : '' }}>{{ $prov }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Distrito --}}
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1 ml-1">Distrito</label>
                    <select id="distrito" name="distrito" class="input-modern w-full uppercase">
                        <option value="">Todos</option>
                        @isset($distritos)
                            @foreach ($distritos as $dist)
                                <option value="{{ $dist }}" {{ request('distrito') == $dist ? 'selected' : '' }}>{{ $dist }}</option>
                            @endforeach
                        @endisset
                    </select>
                </div>

                {{-- Establecimiento --}}
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1 ml-1">Establ.</label>
                    <select id="establecimiento_id" name="establecimiento_id" class="input-modern w-full uppercase">
                        <option value="">Todos</option>
                        @isset($establecimientos)
                            @foreach ($establecimientos as $est)
                                <option value="{{ $est->id }}" {{ request('establecimiento_id') == $est->id ? 'selected' : '' }}>{{ $est->nombre }}</option>
                            @endforeach
                        @endisset
                    </select>
                </div>

                {{-- Estado --}}
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1 ml-1">Estado</label>
                    <select name="firmado" class="input-modern w-full uppercase">
                        <option value="">Todos</option>
                        <option value="1" {{ request('firmado') === '1' ? 'selected' : '' }}>Firmado</option>
                        <option value="0" {{ request('firmado') === '0' ? 'selected' : '' }}>Pendiente</option>
                    </select>
                </div>

                {{-- Fechas --}}
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1 ml-1">Desde</label>
                    <input type="date" name="fecha_inicio" value="{{ $valInicio }}" class="input-modern w-full">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1 ml-1">Hasta</label>
                    <input type="date" name="fecha_fin" value="{{ $valFin }}" class="input-modern w-full">
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1 ml-1">Visibilidad</label>
                    <select name="estado_anulado" class="input-modern w-full uppercase">
                        <option value="todos" {{ request('estado_anulado', 'todos') == 'todos' ? 'selected' : '' }}>Todas</option>
                        <option value="activo" {{ request('estado_anulado') == 'activo' ? 'selected' : '' }}>Activas</option>
                        <option value="anulado" {{ request('estado_anulado') == 'anulado' ? 'selected' : '' }}>Anuladas</option>
                    </select>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2 pt-2 border-t border-slate-50 w-full">
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs flex items-center gap-2 transition-all">
                    <i data-lucide="search" class="w-4 h-4"></i> FILTRAR
                </button>
                <a href="{{ route('usuario.actas.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs flex items-center gap-2 transition-all">
                    <i data-lucide="rotate-ccw" class="w-4 h-4"></i> LIMPIAR
                </a>
                <div class="ml-auto w-full sm:w-auto mt-2 sm:mt-0">
                    @if($actas->total() > 0)
                    <button type="button" onclick="exportarExcel()" class="w-full sm:w-auto px-5 py-2.5 bg-green-50 text-green-700 hover:bg-green-100 font-bold text-xs rounded-xl flex items-center justify-center gap-2 transition-all border border-green-200">
                        <i data-lucide="file-spreadsheet" class="w-4 h-4"></i> EXPORTAR EXCEL
                    </button>
                    @endif
                </div>
            </div>
        </form>

        {{-- TABLA --}}
        <div class="bg-white rounded-2xl shadow-xl shadow-slate-200/50 border border-slate-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-slate-800 text-white">
                        <tr>
                            <th class="px-3 py-3 text-[10px] font-bold uppercase text-center">#</th>
                            <th class="px-3 py-3 text-[10px] font-bold uppercase text-center">Fecha</th>
                            <th class="px-3 py-3 text-[10px] font-bold uppercase">Establecimiento</th>
                            <th class="px-3 py-3 text-[10px] font-bold uppercase">Modalidad</th>
                            <th class="px-3 py-3 text-[10px] font-bold uppercase">Implementadores</th>
                            <th class="px-3 py-3 text-[10px] font-bold uppercase text-center">Estado</th>
                            <th class="px-3 py-3 text-[10px] font-bold uppercase text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs text-slate-600">
                        @forelse($actas as $acta)
                            <tr class="hover:bg-emerald-50/30 transition-colors group {{ $acta->anulado ? 'bg-red-50/50 grayscale-[0.5]' : '' }}">
                                <td class="px-3 py-3 font-mono font-bold text-center text-slate-400">{{ $acta->id }}</td>
                                <td class="px-3 py-3 text-center font-bold">{{ \Carbon\Carbon::parse($acta->fecha)->format('d/m/Y') }}</td>
                                <td class="px-3 py-3 font-semibold text-slate-800">{{ $acta->establecimiento->nombre ?? '—' }}</td>
                                <td class="px-3 py-3">{{ $acta->modalidad }}</td>
                                <td class="px-3 py-3 whitespace-nowrap">
                                    <div class="flex items-center gap-1.5 flex-wrap w-48">
                                        @php 
                                            // Recopilar implementadores únicos
                                            $listaImpl = collect([$acta->implementador])->filter();
                                            foreach ($acta->participantes ?? [] as $p) {
                                                if (!empty($p->es_implementador)) {
                                                    $nombreCompleto = trim(($p->apellidos ?? '') . ' ' . ($p->nombres ?? ''));
                                                    if (!empty($nombreCompleto) && !$listaImpl->contains($nombreCompleto)) {
                                                        $listaImpl->push($nombreCompleto);
                                                    }
                                                }
                                            }
                                            $count = 0; 
                                        @endphp
                                        @foreach($listaImpl as $imp)
                                            @if($count < 2)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-slate-100 text-slate-600 border border-slate-200 truncate max-w-[180px]" title="{{ $imp }}">
                                                    {{ $imp }}
                                                </span>
                                            @endif
                                            @php $count++; @endphp
                                        @endforeach
                                        @if($count > 2)
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-slate-200 text-slate-500 border border-slate-300" title="Ver detalle para la lista completa">
                                                +{{ $count - 2 }}
                                            </span>
                                        @endif
                                    </div>
                                                               <td class="px-3 py-3 text-center whitespace-nowrap">
                                    <div class="flex items-center justify-center gap-1">
                                        @if($acta->anulado)
                                            <span class="inline-flex px-2 py-0.5 rounded-md text-[9px] font-black bg-red-100 text-red-700 border border-red-200 uppercase">ANULADO</span>
                                        @elseif ($acta->firmado)
                                            <span class="inline-flex px-2 py-0.5 rounded-md text-[9px] font-black bg-emerald-100 text-emerald-700 border border-emerald-200 uppercase">Firmado</span>
                                            @if (!empty($acta->firmado_pdf))
                                                <a href="{{ asset('storage/' . $acta->firmado_pdf) }}" target="_blank" class="text-slate-400 hover:text-emerald-600 p-1"><i data-lucide="eye" class="w-3.5 h-3.5"></i></a>
                                            @endif
                                            <form action="{{ route('usuario.actas.subirPDF', $acta->id) }}" method="POST" enctype="multipart/form-data" class="inline-block m-0">
                                                @csrf
                                                <input type="file" name="pdf_firmado" accept="application/pdf" onchange="this.form.submit()" hidden id="pdf-{{ $acta->id }}">
                                                <label for="pdf-{{ $acta->id }}" class="cursor-pointer text-slate-300 hover:text-emerald-500 p-1" title="Reemplazar"><i data-lucide="refresh-cw" class="w-3.5 h-3.5"></i></label>
                                            </form>
                                        @else
                                            <span class="inline-flex px-2 py-0.5 rounded-md text-[9px] font-black bg-amber-100 text-amber-700 border border-amber-200 uppercase">Pendiente</span>
                                            <form action="{{ route('usuario.actas.subirPDF', $acta->id) }}" method="POST" enctype="multipart/form-data" class="inline-block m-0 ml-1">
                                                @csrf
                                                <input type="file" name="pdf_firmado" accept="application/pdf" onchange="this.form.submit()" hidden id="pdf-u-{{ $acta->id }}">
                                                <label for="pdf-u-{{ $acta->id }}" class="cursor-pointer text-slate-400 hover:text-emerald-600 p-1" title="Subir"><i data-lucide="upload-cloud" class="w-3.5 h-3.5"></i></label>
                                            </form>
                                        @endif
                                    </div>
                                </td>
  </td>
                                <td class="px-3 py-3 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        @if(!$acta->anulado)
                                        <a href="{{ route('usuario.actas.generarPDF', $acta->id) }}" target="_blank" class="p-1.5 rounded-lg text-slate-400 hover:text-red-600 hover:bg-red-50 transition-all" title="PDF"><i data-lucide="file-text" class="w-4 h-4"></i></a>
                                        @if($acta->firmado_pdf)
                                        <button onclick="confirmarEnvioCorreo({{ $acta->id }}, '{{ $acta->establecimiento->nombre ?? 'N/A' }}')" 
                                            class="p-1.5 rounded-lg text-slate-400 hover:text-blue-600 hover:bg-blue-50 transition-all" title="Enviar por Correo">
                                            <i data-lucide="mail" class="w-4 h-4"></i>
                                        </button>
                                        @endif
                                        <a href="{{ route('usuario.actas.edit', $acta->id) }}" class="p-1.5 rounded-lg text-slate-400 hover:text-amber-600 hover:bg-amber-50 transition-all" title="Editar"><i data-lucide="pencil" class="w-4 h-4"></i></a>
                                        @endif
                                        <button onclick="confirmarAnulacion('{{ $acta->id }}', {{ $acta->anulado ? 'true' : 'false' }})" 
                                            class="p-1.5 {{ $acta->anulado ? 'text-emerald-500 hover:bg-emerald-50' : 'text-red-400 hover:bg-red-50' }} transition-all rounded-lg" 
                                            title="{{ $acta->anulado ? 'Reactivar Acta' : 'Anular Acta' }}">
                                            <i data-lucide="{{ $acta->anulado ? 'rotate-ccw' : 'ban' }}" class="w-4 h-4"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="px-6 py-12 text-center text-slate-400 text-xs italic">No se encontraron actas registradas</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($actas->hasPages()) <div class="p-4 border-t border-slate-50 text-xs">{{ $actas->appends(request()->query())->links() }}</div> @endif
        </div>
    </div>

    {{-- Formulario oculto para exportar Excel --}}
    <form id="excelForm" method="POST" action="{{ route('usuario.reportes.actas.asistencia.excel') }}" style="display:none;">
        @csrf
        <input type="hidden" name="fecha_inicio"       value="{{ $valInicio }}">
        <input type="hidden" name="fecha_fin"           value="{{ $valFin }}">
        <input type="hidden" name="implementador"       value="{{ request('implementador') }}">
        <input type="hidden" name="provincia"           value="{{ request('provincia') }}">
        <input type="hidden" name="distrito"            value="{{ request('distrito') }}">
        <input type="hidden" name="establecimiento_id"  value="{{ request('establecimiento_id') }}">
        <input type="hidden" name="firmado"             value="{{ request('firmado') }}">
    </form>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => { if (window.lucide) window.lucide.createIcons(); });

        function confirmarAnulacion(id, isAnulado) {
            const action = isAnulado ? 'reactivar' : 'anular';
            const color = isAnulado ? '#10b981' : '#ef4444';
            const baseUrl = "{{ url('/') }}";

            Swal.fire({
                title: `¿Estás seguro de ${action} esta acta?`,
                text: isAnulado ? "El acta volverá a estar activa y visible." : "El acta quedará marcada como anulada.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: `Sí, ${action}`,
                cancelButtonText: 'Cancelar',
                confirmButtonColor: color,
                showLoaderOnConfirm: true,
                preConfirm: () => {
                    return fetch(`${baseUrl}/usuario/listado-actas/${id}/anular`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) return data;
                        throw new Error(data.message || 'Error en la operación');
                    })
                    .catch(error => Swal.showValidationMessage(`Fallo: ${error}`));
                },
                allowOutsideClick: () => !Swal.isLoading()
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({ icon: 'success', title: '¡Hecho!', text: result.value.message, timer: 2000, showConfirmButton: false })
                    .then(() => window.location.reload());
                }
            });
        }

        function exportarExcel() {
            document.getElementById('excelForm').submit();
        }

        const provinciaSelect = document.getElementById('provincia');
        const distritoSelect = document.getElementById('distrito');
        const establecimientoSelect = document.getElementById('establecimiento_id');

        provinciaSelect.addEventListener('change', () => {
            fetch(`{{ route('usuario.actas.ajax.distritos') }}?provincia=${provinciaSelect.value}`)
                .then(r => r.json())
                .then(data => {
                    distritoSelect.innerHTML = '<option value="">Todos</option>';
                    establecimientoSelect.innerHTML = '<option value="">Todos</option>';
                    data.forEach(d => {
                        distritoSelect.innerHTML += `<option value="${d}">${d}</option>`;
                    });
                });
        });

        distritoSelect.addEventListener('change', () => {
            fetch(`{{ route('usuario.actas.ajax.establecimientos') }}?provincia=${provinciaSelect.value}&distrito=${distritoSelect.value}`)
                .then(r => r.json())
                .then(data => {
                    establecimientoSelect.innerHTML = '<option value="">Todos</option>';
                    data.forEach(e => {
                        establecimientoSelect.innerHTML += `<option value="${e.id}">${e.nombre}</option>`;
                    });
                });
        });

        function confirmarEnvioCorreo(id, nombre) {
            // Primero obtenemos los correos posibles (participantes)
            Swal.fire({
                title: 'Preparando envío...',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            fetch(`{{ url('/usuario/listado-actas') }}/${id}/emails`)
                .then(r => r.json())
                .then(data => {
                    const defaultEmails = data.emails || [];
                    const summary = `
                        <div class="text-left mb-3 p-3 bg-slate-50 rounded-xl border border-slate-100">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Detalles del Acta</p>
                            <div class="grid grid-cols-2 gap-1.5 text-[11px]">
                                <div><span class="text-slate-500">🏥</span> <span class="font-bold">${data.establecimiento}</span></div>
                                <div><span class="text-slate-500">📅</span> <span class="font-bold">${data.fecha}</span></div>
                                <div class="col-span-2"><span class="text-slate-500">📝</span> <span class="font-bold">${data.tema}</span></div>
                            </div>
                        </div>
                        <div class="text-left mb-2">
                            <div class="flex items-center justify-between mb-1">
                                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Destinatarios</label>
                                ${defaultEmails.length > 0 ? '<button type="button" id="btn-precargar" class="text-[10px] font-bold text-emerald-600 hover:text-emerald-800 bg-emerald-50 border border-emerald-200 rounded-lg px-2 py-1 transition-all">⚡ Precargar participantes ('+defaultEmails.length+')</button>' : ''}
                            </div>
                            <div id="chips-wrapper" class="email-chips-container">
                                <input type="text" id="tag-input" class="email-chip-input" placeholder="Escriba un correo y presione Enter o ;">
                            </div>
                            <p class="text-[9px] text-slate-400 mt-1 italic">Use coma (,), punto y coma (;) o Enter para agregar.</p>
                            <div id="chip-list-preview" class="mt-2 hidden">
                                <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">📋 Lista de envío:</p>
                                <div id="chip-list-container" class="flex flex-wrap gap-1 max-h-16 overflow-y-auto"></div>
                            </div>
                        </div>
                    `;

                    Swal.fire({
                        title: '✉️ Enviar Acta por Correo',
                        html: summary,
                        showCancelButton: true,
                        confirmButtonText: '🚀 Enviar Ahora',
                        cancelButtonText: 'Cancelar',
                        confirmButtonColor: '#10b981',
                        width: '520px',
                        didOpen: () => {
                            const wrapper = document.getElementById('chips-wrapper');
                            const input = document.getElementById('tag-input');
                            const listPreview = document.getElementById('chip-list-preview');
                            const listContainer = document.getElementById('chip-list-container');
                            const tags = new Set();

                            const renderTags = () => {
                                wrapper.querySelectorAll('.email-chip').forEach(c => c.remove());
                                listContainer.innerHTML = '';
                                tags.forEach(email => {
                                    // Chip en el input area
                                    const chip = document.createElement('div');
                                    chip.className = 'email-chip';
                                    chip.innerHTML = `${email} <button type="button" data-email="${email}"><i data-lucide="x" class="w-3 h-3"></i></button>`;
                                    wrapper.insertBefore(chip, input);
                                    // Item en la sublista
                                    const item = document.createElement('span');
                                    item.className = 'inline-flex items-center gap-1 text-[10px] bg-slate-100 text-slate-600 border border-slate-200 px-2 py-0.5 rounded-full';
                                    item.innerHTML = `<i data-lucide="mail" class="w-2.5 h-2.5"></i> ${email}`;
                                    listContainer.appendChild(item);
                                });
                                listPreview.classList.toggle('hidden', tags.size === 0);
                                if (window.lucide) window.lucide.createIcons();
                            };

                            // Botón precargar participantes
                            const btnPrecargar = document.getElementById('btn-precargar');
                            if (btnPrecargar) {
                                btnPrecargar.addEventListener('click', () => {
                                    defaultEmails.forEach(e => tags.add(e.toLowerCase()));
                                    renderTags();
                                    btnPrecargar.disabled = true;
                                    btnPrecargar.textContent = '✅ Participantes cargados';
                                    btnPrecargar.className = 'text-[10px] font-bold text-slate-400 bg-slate-50 border border-slate-200 rounded-lg px-2 py-1';
                                });
                            }

                            wrapper.addEventListener('click', () => input.focus());

                            input.addEventListener('keydown', (e) => {
                                if ([';', ',', 'Enter'].includes(e.key)) {
                                    e.preventDefault();
                                    const val = input.value.trim().toLowerCase();
                                    if (val && /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val)) {
                                        tags.add(val);
                                        input.value = '';
                                        renderTags();
                                    } else if (val) {
                                        input.classList.add('shake');
                                        setTimeout(() => input.classList.remove('shake'), 300);
                                    }
                                }
                                if (e.key === 'Backspace' && !input.value && tags.size > 0) {
                                    const last = Array.from(tags).pop();
                                    tags.delete(last);
                                    renderTags();
                                }
                            });

                            wrapper.addEventListener('click', (e) => {
                                const btn = e.target.closest('button');
                                if (btn && btn.dataset.email) {
                                    tags.delete(btn.dataset.email);
                                    renderTags();
                                }
                            });

                            window._currentTags = tags;
                        },
                        preConfirm: () => {
                            const tags = Array.from(window._currentTags);
                            if (tags.length === 0) {
                                Swal.showValidationMessage('Debe ingresar al menos un correo');
                                return false;
                            }
                            
                            return fetch(`{{ url('/usuario/listado-actas') }}/${id}/enviar-correo`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify({ correos: tags.join(',') })
                            })
                            .then(response => {
                                if (!response.ok) {
                                    return response.json().then(json => { throw new Error(json.message || 'Error en el envío'); });
                                }
                                return response.json();
                            })
                            .catch(error => {
                                Swal.showValidationMessage(`Error: ${error.message}`);
                            });
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            Swal.fire({
                                icon: 'success',
                                title: '✅ ¡Envío Exitoso!',
                                text: result.value.message,
                                confirmButtonColor: '#10b981'
                            });
                        }
                    });
                })
                .catch(err => {
                    Swal.fire('Error', 'No se pudieron cargar los datos del acta.', 'error');
                });
        }

        @if (session('success'))
            Swal.fire({ icon: 'success', title: '¡Éxito!', text: @json(session('success')), toast: true, position: 'top-end', showConfirmButton: false, timer: 3000 });
        @endif
    </script>
@endpush