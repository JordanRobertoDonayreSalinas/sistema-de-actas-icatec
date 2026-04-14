@extends('layouts.usuario')

@section('title', 'Actas de Implementación')

@section('header-content')
    <h1 class="text-xl font-bold text-slate-800 tracking-tight">Actas de Implementación</h1>
    <div class="flex items-center gap-2 text-xs text-slate-500 mt-0.5">
        <span>Operaciones</span>
        <span class="text-slate-300">•</span>
        <span>Panel de Control de Implementación</span>
    </div>
@endsection

@push('styles')
    <style>
        input[type="date"] {
            position: relative;
            color: #4b5563;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='20' height='20' viewBox='0 0 24 24' fill='none' stroke='%233b82f6' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Crect x='3' y='4' width='18' height='18' rx='2' ry='2'/%3E%3Cline x1='16' y1='2' x2='16' y2='6'/%3E%3Cline x1='8' y1='2' x2='8' y2='6'/%3E%3Cline x1='3' y1='10' x2='21' y2='10'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 0.5rem center;
            background-size: 1.2em;
        }
        input[type="date"]::-webkit-calendar-picker-indicator {
            opacity: 0;
            cursor: pointer;
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
        }
        [x-cloak] { display: none !important; }
    </style>
@endpush

@section('content')

    @php
        $filtersAreActive = request()->anyFilled(['implementador', 'provincia', 'fecha_inicio', 'fecha_fin', 'estado', 'modulo_key', 'distrito']);
        
        $valInicio = request('fecha_inicio', $fecha_inicio);
        $valFin = request('fecha_fin', $fecha_fin);
    @endphp

    <div x-data="{ open: {{ $filtersAreActive ? 'true' : 'false' }} }" class="w-full">

        {{-- TARJETA AZUL SUPERIOR --}}
        <div class="bg-gradient-to-r from-purple-800 to-violet-600 p-5 rounded-2xl shadow-xl mb-6 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full blur-3xl -mr-16 -mt-16 pointer-events-none"></div>

            <div class="relative z-10 flex flex-col lg:flex-row items-center justify-between gap-6">
                <div class="flex flex-col gap-4 w-full">
                    <div class="flex flex-wrap items-center gap-3">
                        <div class="bg-slate-900 text-white rounded-xl px-5 py-2.5 shadow-lg border border-slate-700 flex flex-col items-center min-w-[100px]">
                            <span class="text-2xl font-bold leading-none">{{ $totalActas }}</span>
                            <span class="text-[0.65rem] uppercase tracking-widest text-slate-400 font-semibold mt-1">TOTAL</span>
                        </div>
                        <div class="bg-emerald-500/20 backdrop-blur-md text-white rounded-xl px-5 py-2.5 border border-emerald-500/30 flex flex-col items-center min-w-[100px]">
                            <span class="text-2xl font-bold leading-none text-emerald-400">{{ $countCompletados ?? 0 }}</span>
                            <span class="text-[0.65rem] uppercase tracking-widest text-emerald-100 font-semibold mt-1">FIRMADAS</span>
                        </div>
                        <div class="bg-amber-500/20 backdrop-blur-md text-white rounded-xl px-5 py-2.5 border border-amber-500/30 flex flex-col items-center min-w-[100px]">
                            <span class="text-2xl font-bold leading-none text-amber-400">{{ $countPendientes ?? 0 }}</span>
                            <span class="text-[0.65rem] uppercase tracking-widest text-amber-100 font-semibold mt-1">Pendientes</span>
                        </div>
                        <div class="bg-slate-900 text-white rounded-xl px-5 py-2.5 shadow-lg border border-slate-700 flex flex-col items-center min-w-[100px]">
                            <span class="text-2xl font-bold leading-none">{{ $countAnuladas ?? 0 }}</span>
                            <span class="text-[0.65rem] uppercase tracking-widest text-slate-400 font-semibold mt-1">Anuladas</span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-3 w-full lg:w-auto justify-center lg:justify-end mt-2 lg:mt-0">
                    <button @click="open = !open" type="button"
                        class="flex items-center gap-2 px-5 py-3 rounded-xl font-bold text-sm transition-all shadow-lg border border-white/20 text-white bg-white/10 hover:bg-white/20 backdrop-blur-sm">
                        <i data-lucide="filter" class="w-4 h-4"></i>
                        <span x-text="open ? 'Ocultar Filtros' : 'Mostrar Filtros'"></span>
                    </button>

                    <a href="{{ route('usuario.implementacion.create') }}"
                        class="flex items-center gap-2 px-5 py-3 rounded-xl font-bold text-sm transition-all shadow-lg bg-white text-purple-700 hover:bg-purple-50 border border-transparent">
                        <i data-lucide="activity" class="w-5 h-5"></i>
                        <span>Nueva Acta</span>
                    </a>
                </div>
            </div>
        </div>

        {{-- FILTROS ACTUALIZADOS --}}
        <form x-show="open" x-cloak 
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
            method="GET" action="{{ route('usuario.implementacion.index') }}" id="filterForm"
            class="bg-white p-4 rounded-2xl shadow-sm border border-slate-200 mb-6">
            
            <div class="flex flex-wrap lg:flex-nowrap items-end gap-3">
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 flex-grow w-full">
                    
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1 tracking-wider">Módulo</label>
                        <select name="modulo_key" class="w-full text-[11px] font-bold text-slate-700 border-slate-200 bg-slate-50 rounded-xl focus:ring-2 focus:ring-purple-500 py-2">
                            <option value="">TODOS</option>
                            @foreach($modulos as $key => $config)
                                <option value="{{ $key }}" {{ request('modulo_key') == $key ? 'selected' : '' }}>{{ strtoupper($config['nombre']) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1 tracking-wider">Provincia</label>
                        <select name="provincia" id="provinciaSelect" class="w-full text-[11px] font-bold text-slate-700 border-slate-200 bg-slate-50 rounded-xl focus:ring-2 focus:ring-purple-500 py-2">
                            <option value="">TODAS</option>
                            @foreach ($provincias as $prov)
                                <option value="{{ $prov }}" {{ request('provincia') == $prov ? 'selected' : '' }}>{{ $prov }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1 tracking-wider">Distrito</label>
                        <select name="distrito" id="distritoSelect" class="w-full text-[11px] font-bold text-slate-700 border-slate-200 bg-slate-50 rounded-xl focus:ring-2 focus:ring-purple-500 py-2">
                            <option value="">TODOS</option>
                            @foreach ($distritos as $dist)
                                <option value="{{ $dist }}" {{ request('distrito') == $dist ? 'selected' : '' }}>{{ $dist }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1 tracking-wider">Implementador</label>
                        <select name="implementador" class="w-full text-[11px] font-bold text-slate-700 border-slate-200 bg-slate-50 rounded-xl focus:ring-2 focus:ring-purple-500 py-2">
                            <option value="">TODOS</option>
                            @foreach ($implementadores as $impl)
                                <option value="{{ $impl }}" {{ request('implementador') == $impl ? 'selected' : '' }}>{{ $impl }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap lg:flex-nowrap items-end gap-3 mt-3">
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 flex-grow w-full">
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1 tracking-wider">Estado</label>
                        <select name="estado" class="w-full text-[11px] font-bold text-slate-700 border-slate-200 bg-slate-50 rounded-xl focus:ring-2 focus:ring-purple-500 py-2">
                            <option value="">TODOS</option>
                            <option value="firmada" {{ request('estado') == 'firmada' ? 'selected' : '' }}>FIRMADO</option>
                            <option value="pendiente" {{ request('estado') == 'pendiente' ? 'selected' : '' }}>PENDIENTE</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1 tracking-wider">Desde</label>
                        <input type="date" name="fecha_inicio" value="{{ $valInicio }}" 
                            class="w-full text-[11px] font-bold text-slate-700 border-slate-200 bg-slate-50 rounded-xl py-2">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1 tracking-wider">Hasta</label>
                        <input type="date" name="fecha_fin" value="{{ $valFin }}" 
                            class="w-full text-[11px] font-bold text-slate-700 border-slate-200 bg-slate-50 rounded-xl py-2">
                    </div>
                    
                    <div class="flex items-end gap-2 shrink-0">
                        <button type="submit" class="w-full lg:w-10 h-10 flex items-center justify-center rounded-xl bg-blue-600 hover:bg-blue-700 text-white shadow-lg shadow-blue-500/30 transition-all hover:scale-105" title="Filtrar">
                            <i data-lucide="search" class="w-4 h-4"></i>
                            <span class="block lg:hidden ml-2 text-xs font-bold">Buscar</span>
                        </button>
                        <a href="{{ route('usuario.implementacion.index') }}" 
                            class="w-full lg:w-10 h-10 flex items-center justify-center rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 shadow-sm transition-all hover:scale-105 border border-slate-200" title="Limpiar">
                            <i data-lucide="rotate-cw" class="w-4 h-4"></i>
                            <span class="block lg:hidden ml-2 text-xs font-bold">Limpiar</span>
                        </a>
                        @if($totalActas > 0)
                        <button type="button" onclick="exportarExcel()" class="w-full lg:w-auto h-10 px-4 py-2 bg-green-50 text-green-700 hover:bg-green-100 font-bold text-xs rounded-xl flex items-center justify-center lg:justify-start gap-2 transition-all border border-green-200" title="Exportar a Excel">
                            <i data-lucide="file-spreadsheet" class="w-4 h-4"></i> 
                            <span class="lg:hidden xl:inline">EXPORTAR EXCEL</span>
                        </button>
                        @endif
                    </div>
                </div>
            </div>
        </form>

        {{-- TABLA --}}
        <div class="bg-white rounded-2xl shadow-xl shadow-slate-200/50 border border-slate-100 overflow-hidden mb-4">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-800 text-white">
                    <tr>
                            <th class="px-5 py-4 text-left text-xs font-bold uppercase tracking-wider">Fecha</th>
                            <th class="px-5 py-4 text-left text-xs font-bold uppercase tracking-wider">Módulo / ID</th>
                            <th class="px-5 py-4 text-left text-xs font-bold uppercase tracking-wider">Establecimiento</th>
                            <th class="px-5 py-4 text-left text-xs font-bold uppercase tracking-wider">Implementadores</th>
                            <th class="px-5 py-4 text-center text-xs font-bold uppercase tracking-wider">Estado Doc.</th>
                            <th class="px-5 py-4 text-right text-xs font-bold uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($actas as $acta)
                    <tr class="hover:bg-purple-50/50 transition-colors {{ $acta['anulado'] ? 'bg-slate-50 opacity-65 grayscale-[0.5]' : '' }}">
                        <td class="px-4 py-3 font-semibold">{{ \Carbon\Carbon::parse($acta['fecha'])->format('d/m/Y') }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <div class="font-bold text-slate-800">{{ $acta['nombre'] }}</div>
                                @if($acta['anulado'])
                                    <span class="px-1.5 py-0.5 rounded text-[8px] font-black bg-red-100 text-red-600 border border-red-200 uppercase tracking-tighter">ANULADA</span>
                                @endif
                            </div>
                            <div class="text-[10px] text-purple-600 font-semibold uppercase">{{ $acta['tipo_nombre'] }}</div>
                        </td>
                        <td class="px-4 py-3 max-w-xs truncate" title="{{ $acta['establecimiento'] }}">
                            {{ $acta['establecimiento'] }}
                        </td>
                        <td class="px-5 py-3 whitespace-nowrap">
                            <div class="flex items-center gap-1.5 flex-wrap w-48">
                                @php $count = 0; @endphp
                                @foreach($acta['implementadores_data'] as $imp)
                                    @if($count < 2)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-slate-100 text-slate-600 border border-slate-200 truncate max-w-[180px]" title="{{ $imp->apellido_paterno }} {{ $imp->apellido_materno }}, {{ $imp->nombres }}">
                                            {{ $imp->apellido_paterno }} {{ $imp->apellido_materno }}, {{ $imp->nombres }}
                                        </span>
                                    @endif
                                    @php $count++; @endphp
                                @endforeach
                                @if($count > 2)
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-slate-200 text-slate-500 border border-slate-300">
                                        +{{ $count - 2 }}
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td class="px-5 py-3 whitespace-nowrap text-center">
                            @if(isset($acta['archivo_pdf']) && $acta['archivo_pdf'])
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700 border border-emerald-200" title="Acta Firmada Subida">
                                    <i data-lucide="file-check-2" class="w-3.5 h-3.5"></i> Firmada
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-700 border border-amber-200" title="Sin acta adjunta">
                                    <i data-lucide="clock" class="w-3.5 h-3.5"></i> Pendiente
                                </span>
                            @endif
                        </td>
                        <td class="px-5 py-3 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center justify-end gap-2">
                                @if(!$acta['anulado'])
                                <button onclick="abrirModalSubir('{{ $acta['tipo_key'] }}', {{ $acta['id'] }})"
                                    class="p-1.5 rounded-lg {{ isset($acta['archivo_pdf']) && $acta['archivo_pdf'] ? 'text-emerald-500 bg-emerald-50' : 'text-slate-400 hover:bg-slate-50' }} transition-all" 
                                    title="Subir acta firmada">
                                    <i data-lucide="upload-cloud" class="w-4 h-4"></i>
                                </button>

                                @if(isset($acta['archivo_pdf']) && $acta['archivo_pdf'])
                                <a href="{{ Storage::url($acta['archivo_pdf']) }}" target="_blank"
                                    class="p-1.5 text-emerald-600 hover:text-emerald-900 hover:bg-emerald-50 rounded-lg transition-colors border border-transparent hover:border-emerald-200" title="Ver Acta Firmada Subida">
                                    <i data-lucide="file-check-2" class="w-4 h-4"></i>
                                </a>
                                <button onclick="confirmarEnvioCorreo('{{ $acta['tipo_key'] }}', {{ $acta['id'] }}, '{{ $acta['nombre'] }}')"
                                    class="p-1.5 text-blue-600 hover:text-blue-900 hover:bg-blue-50 rounded-lg transition-colors border border-transparent hover:border-blue-200" 
                                    title="Enviar Acta por Correo a Participantes">
                                    <i data-lucide="mail" class="w-4 h-4"></i>
                                </button>
                                @else
                                <a href="{{ $acta['ruta_pdf'] }}" target="_blank"
                                    class="p-1.5 text-purple-600 hover:text-purple-900 hover:bg-purple-50 rounded-lg transition-colors border border-transparent hover:border-purple-200" title="Generar PDF (Temporal)">
                                    <i data-lucide="file-text" class="w-4 h-4"></i>
                                </a>
                                @endif
                                
                                <a href="{{ $acta['ruta_editar'] }}" 
                                    class="p-1.5 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors border border-transparent hover:border-indigo-200">
                                    <i data-lucide="edit-2" class="w-4 h-4"></i>
                                </a>
                                @endif

                                <button onclick="confirmarAnulacion('{{ $acta['tipo_key'] }}', {{ $acta['id'] }}, '{{ $acta['nombre'] }}', {{ $acta['anulado'] ? 'true' : 'false' }})"
                                    class="p-1.5 {{ $acta['anulado'] ? 'text-emerald-500 hover:bg-emerald-50' : 'text-red-400 hover:bg-red-50' }} transition-all rounded-lg" 
                                    title="{{ $acta['anulado'] ? 'Reactivar Acta' : 'Anular Acta' }}">
                                    <i data-lucide="{{ $acta['anulado'] ? 'rotate-ccw' : 'ban' }}" class="w-4 h-4"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-12 text-center text-slate-400 font-medium">
                            <i data-lucide="folder-open" class="w-8 h-8 mx-auto mb-2 text-slate-300"></i>
                            No hay actas registradas para este filtro
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    @if (method_exists($actas, 'hasPages') && $actas->hasPages())
        <div class="mt-4">{{ $actas->appends(request()->query())->links() }}</div>
    @endif
</div>

{{-- Formulario oculto para exportar Excel --}}
<form id="excelForm" method="POST" action="{{ route('usuario.reportes.actas.implementacion.excel') }}" style="display:none;">
    @csrf
    <input type="hidden" name="fecha_inicio"       value="{{ $valInicio }}">
    <input type="hidden" name="fecha_fin"           value="{{ $valFin }}">
    <input type="hidden" name="implementador"       value="{{ request('implementador') }}">
    <input type="hidden" name="provincia"           value="{{ request('provincia') }}">
    <input type="hidden" name="distrito"            value="{{ request('distrito') }}">
    <input type="hidden" name="modulo_key"          value="{{ request('modulo_key') }}">
    <input type="hidden" name="estado"              value="{{ request('estado') === 'firmada' ? '1' : (request('estado') === 'pendiente' ? '0' : '') }}">
</form>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function exportarExcel() {
        document.getElementById('excelForm').submit();
    }

    document.addEventListener('DOMContentLoaded', () => {
        if (typeof lucide !== 'undefined') lucide.createIcons();

        const baseUrl = "{{ url('/') }}";

        const provinciaSelect = document.getElementById('provinciaSelect');
        const distritoSelect = document.getElementById('distritoSelect');

        if (provinciaSelect) {
            provinciaSelect.addEventListener('change', async () => {
                const provincia = provinciaSelect.value;
                
                // Limpiar distrito
                distritoSelect.innerHTML = '<option value="">TODOS</option>';

                if (provincia) {
                    // LLamaremos a la ruta de reporte de implementacion que devuelve distritos
                    const resDist = await fetch(`{{ route('usuario.reportes.actas.implementacion.ajax.distritos') }}?provincia=${provincia}`);
                    const distritos = await resDist.json();
                    distritos.forEach(d => {
                        const opt = document.createElement('option');
                        opt.value = d;
                        opt.textContent = d;
                        distritoSelect.appendChild(opt);
                    });
                }
            });
        }
    });

    function abrirModalSubir(modulo, id) {
        Swal.fire({
            title: 'Subir Acta Firmada',
            text: 'Adjunte el archivo PDF con todas las firmas.',
            input: 'file',
            inputAttributes: {
                'accept': 'application/pdf',
                'aria-label': 'Subir acta firmada'
            },
            showCancelButton: true,
            confirmButtonText: 'Subir PDF',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#9333ea',
            showLoaderOnConfirm: true,
            preConfirm: (file) => {
                if (!file) {
                    Swal.showValidationMessage('Debe seleccionar un archivo PDF');
                    return;
                }
                
                const formData = new FormData();
                formData.append('pdf_firmado', file);
                formData.append('_token', '{{ csrf_token() }}');

                const baseUrl = "{{ url('/') }}";
                return fetch(`${baseUrl}/usuario/implementacion/${modulo}/${id}/subir-pdf`, {
                    method: 'POST',
                    body: formData,
                    headers: { 
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => { throw new Error(err.message || 'Error en el servidor'); });
                    }
                    return response.json();
                })
                .catch(error => {
                    Swal.showValidationMessage(`Hubo un problema: ${error.message}`);
                });
            },
            allowOutsideClick: () => !Swal.isLoading()
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    icon: 'success',
                    title: '¡Acta Cargada!',
                    text: 'El acta firmada ha sido subida correctamente.',
                    timer: 2000
                }).then(() => location.reload());
            }
        });
    }

    function confirmarEnvioCorreo(modulo, id, nombreActa) {
        Swal.fire({
            title: '¿Enviar Acta por Correo?',
            text: `Se enviará el acta firmada de ${nombreActa} a todos los participantes con correo registrado.`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, enviar ahora',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#3b82f6',
            cancelButtonColor: '#64748b',
            showLoaderOnConfirm: true,
            preConfirm: () => {
                const baseUrl = "{{ url('/') }}";
                return fetch(`${baseUrl}/usuario/implementacion/${modulo}/${id}/enviar-correo`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => { throw new Error(err.message || 'Error al enviar el correo'); });
                    }
                    return response.json();
                })
                .catch(error => {
                    Swal.showValidationMessage(`Error: ${error.message}`);
                });
            },
            allowOutsideClick: () => !Swal.isLoading()
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    icon: 'success',
                    title: '¡Correos Enviados!',
                    text: result.value.message || 'El acta ha sido enviada exitosamente.',
                    confirmButtonColor: '#3b82f6'
                });
            }
        });
    }

    function confirmarAnulacion(modulo, id, nombreActa, esAnulada) {
        const accion = esAnulada ? 'Reactivar' : 'Anular';
        const icono = esAnulada ? 'question' : 'warning';
        const color = esAnulada ? '#10b981' : '#ef4444';

        Swal.fire({
            title: `¿${accion} Acta?`,
            text: `¿Está seguro que desea ${accion.toLowerCase()} el acta ${nombreActa}?`,
            icon: icono,
            showCancelButton: true,
            confirmButtonText: `Sí, ${accion.toLowerCase()}`,
            cancelButtonText: 'Cancelar',
            confirmButtonColor: color,
            showLoaderOnConfirm: true,
            preConfirm: () => {
                const baseUrl = "{{ url('/') }}";
                return fetch(`${baseUrl}/usuario/implementacion/${modulo}/${id}/anular`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => { throw new Error(err.message || 'Error al procesar la solicitud'); });
                    }
                    return response.json();
                })
                .catch(error => {
                    Swal.showValidationMessage(`Error: ${error.message}`);
                });
            },
            allowOutsideClick: () => !Swal.isLoading()
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    icon: 'success',
                    title: result.value.anulado ? '¡Acta Anulada!' : '¡Acta Reactivada!',
                    text: result.value.message,
                    timer: 2000
                }).then(() => location.reload());
            }
        });
    }
</script>
@endpush
