@extends('layouts.usuario')

@section('title', 'Documentos Administrativos')

@push('styles')
    <style>
        /* Estilo de Inputs Modernos */
        .input-modern {
            background-color: #f8fafc;
            /* Slate-50 */
            border: 1px solid #e2e8f0;
            /* Slate-200 */
            border-radius: 1rem;
            color: #334155;
            /* Slate-700 */
            font-size: 0.75rem;
            /* text-xs */
            font-weight: 700;
            padding-top: 0.75rem;
            padding-bottom: 0.75rem;
            transition: all 0.2s ease-in-out;
        }

        .input-modern:focus {
            background-color: #ffffff;
            border-color: #6366f1;
            /* Indigo-500 */
            box-shadow: 0 4px 6px -1px rgba(99, 102, 241, 0.1), 0 2px 4px -1px rgba(99, 102, 241, 0.06);
            outline: none;
        }

        .input-modern::placeholder {
            color: #94a3b8;
            font-weight: 500;
        }

        /* Iconos dentro de inputs */
        .input-icon-wrapper {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            pointer-events: none;
            width: 1rem;
            height: 1rem;
        }

        .input-with-icon {
            padding-left: 2.75rem;
        }

        /* Scrollbar tabla */
        .table-container::-webkit-scrollbar {
            height: 6px;
        }

        .table-container::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 3px;
        }

        .table-container::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }

        .table-container::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        [x-cloak] {
            display: none !important;
        }

        .custom-swal-popup {
            border-radius: 1.5rem !important;
            padding: 2rem !important;
        }
    </style>
@endpush

@section('header-content')
    <h1 class="text-xl font-bold text-slate-800 tracking-tight">Documentos Administrativos</h1>
    <div class="flex items-center gap-2 text-xs text-slate-500 mt-0.5">
        <span>Operaciones</span>
        <span class="text-slate-300">•</span>
        <span>Documentos Administrativos</span>
    </div>
@endsection

@section('content')
    <div x-data="{ open: {{ request()->anyFilled(['search', 'tipo_doc_busqueda', 'estado', 'provincia', 'distrito', 'establecimiento_nombre']) ? 'true' : 'false' }} }"
        class="w-full">

        {{-- 1. TARJETA DE ESTADÍSTICAS (KPIs) --}}
        <div
            class="bg-gradient-to-r from-indigo-900 to-indigo-700 p-5 rounded-2xl shadow-xl mb-6 relative overflow-hidden text-white group">
            {{-- Efectos de fondo --}}
            <div
                class="absolute right-0 top-0 w-64 h-64 bg-white/10 rounded-full -mr-16 -mt-16 blur-3xl pointer-events-none">
            </div>

            <div class="relative z-10 flex flex-col lg:flex-row items-center justify-between gap-6">
                <div class="flex flex-col gap-4 w-full">
                    <div class="flex flex-wrap items-center gap-3">
                        {{-- KPI Total --}}
                        <div
                            class="bg-slate-900 text-white rounded-xl px-5 py-2.5 shadow-lg border border-slate-700 flex flex-col items-center min-w-[100px]">
                            <span class="text-2xl font-bold leading-none">{{ $documentos->total() }}</span>
                            <span
                                class="text-[0.65rem] uppercase tracking-widest text-slate-400 font-semibold mt-1">Total</span>
                        </div>
                        {{-- KPI Completados --}}
                        <div
                            class="bg-white/20 backdrop-blur-md text-white rounded-xl px-5 py-2.5 border border-white/30 flex flex-col items-center min-w-[100px]">
                            <span class="text-2xl font-bold leading-none">{{ $countCompletados ?? 0 }}</span>
                            <span
                                class="text-[0.65rem] uppercase tracking-widest text-indigo-100 font-semibold mt-1">Firmadas</span>
                        </div>
                        {{-- KPI Pendientes --}}
                        <div
                            class="bg-amber-500 text-white rounded-xl px-5 py-2.5 shadow-lg border border-amber-400 flex flex-col items-center min-w-[100px]">
                            <span class="text-2xl font-bold leading-none">{{ $countPendientes ?? 0 }}</span>
                            <span
                                class="text-[0.65rem] uppercase tracking-widest text-amber-100 font-semibold mt-1">Pendientes</span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-3 w-full lg:w-auto justify-center lg:justify-end mt-2 lg:mt-0">
                    <button @click="open = !open" type="button"
                        class="flex items-center gap-2 px-5 py-3 rounded-xl font-bold text-sm transition-all shadow-lg border border-white/20 text-white bg-white/10 hover:bg-white/20 backdrop-blur-sm">
                        <i data-lucide="filter" class="w-4 h-4" x-show="!open"></i>
                        <i data-lucide="filter-x" class="w-4 h-4" x-show="open" x-cloak></i>
                        <span x-text="open ? 'Ocultar Filtros' : 'Mostrar Filtros'"></span>
                    </button>
                    <a href="{{ route('usuario.documentos.create') }}"
                        class="flex items-center gap-2 px-5 py-3 rounded-xl font-bold text-sm transition-all shadow-lg bg-white text-indigo-700 hover:bg-indigo-50 border border-transparent">
                        <i data-lucide="plus-circle" class="w-5 h-5"></i>
                        <span>Nuevo Registro</span>
                    </a>
                </div>
            </div>
        </div>

        {{-- 2. PANEL DE FILTROS (DISEÑO GRID) --}}
        <form x-show="open" x-cloak x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
            method="GET" action="{{ route('usuario.documentos.index') }}"
            class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200 mb-6">

            <div class="flex flex-col gap-6">
                {{-- SECCIÓN 1: BÚSQUEDA PROFESIONAL --}}
                <div>
                    <h3
                        class="text-[10px] font-black text-indigo-400 uppercase tracking-widest mb-3 flex items-center gap-2">
                        <i data-lucide="user-search" class="w-3 h-3"></i> Búsqueda de Profesional
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        {{-- Filtro Tipo Documento --}}
                        <div class="md:col-span-1">
                            <select name="tipo_doc_busqueda" class="input-modern w-full px-4 cursor-pointer">
                                <option value="">TODOS</option>
                                <option value="DNI" {{ request('tipo_doc_busqueda') == 'DNI' ? 'selected' : '' }}>DNI
                                </option>
                                <option value="CE" {{ request('tipo_doc_busqueda') == 'CE' ? 'selected' : '' }}>CE
                                </option>
                            </select>
                        </div>
                        {{-- Input Buscador General --}}
                        <div class="md:col-span-3 input-icon-wrapper">
                            <i data-lucide="search" class="input-icon"></i>
                            <input type="text" name="search" value="{{ request('search') }}"
                                class="input-modern w-full input-with-icon uppercase"
                                placeholder="INGRESE DNI, NOMBRES O APELLIDOS DEL PROFESIONAL...">
                        </div>
                    </div>
                </div>

                <div class="h-px w-full bg-slate-100"></div>

                {{-- SECCIÓN 2: UBICACIÓN Y ESTADO --}}
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

                    {{-- Ubicación --}}
                    <div>
                        <h3
                            class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 flex items-center gap-2">
                            <i data-lucide="map-pin" class="w-3 h-3"></i> Ubicación (IPRESS)
                        </h3>
                        <div class="grid grid-cols-2 gap-3 mb-3">
                            <select name="provincia" onchange="this.form.submit()"
                                class="input-modern w-full px-4 uppercase">
                                <option value="">PROVINCIA: TODAS</option>
                                @foreach($provincias as $prov)
                                    <option value="{{ $prov }}" {{ request('provincia') == $prov ? 'selected' : '' }}>
                                        {{ $prov }}
                                    </option>
                                @endforeach
                            </select>
                            <select name="distrito" class="input-modern w-full px-4 uppercase">
                                <option value="">DISTRITO: TODOS</option>
                                @foreach($distritos as $dist)
                                    <option value="{{ $dist }}" {{ request('distrito') == $dist ? 'selected' : '' }}>
                                        {{ $dist }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="input-icon-wrapper">
                            <i data-lucide="building-2" class="input-icon"></i>
                            <input type="text" name="establecimiento_nombre" value="{{ request('establecimiento_nombre') }}"
                                class="input-modern w-full input-with-icon uppercase"
                                placeholder="NOMBRE DEL ESTABLECIMIENTO...">
                        </div>
                    </div>

                    {{-- Estado y Fecha --}}
                    <div>
                        <h3
                            class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 flex items-center gap-2">
                            <i data-lucide="sliders-horizontal" class="w-3 h-3"></i> Estado y Periodo
                        </h3>
                        <div class="mb-3">
                            <select name="estado" class="input-modern w-full px-4 uppercase">
                                <option value="">ESTADO: TODOS</option>
                                <option value="firmada" {{ request('estado') == 'firmada' ? 'selected' : '' }}>COMPLETADOS
                                    (100%)</option>
                                <option value="pendiente" {{ request('estado') == 'pendiente' ? 'selected' : '' }}>
                                    PENDIENTES</option>
                            </select>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <input type="date" name="fecha_inicio" value="{{ $fecha_inicio }}"
                                class="input-modern w-full px-4 text-center">
                            <input type="date" name="fecha_fin" value="{{ $fecha_fin }}"
                                class="input-modern w-full px-4 text-center">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Footer Botones --}}
            <div class="flex items-center gap-3 shrink-0">
                <button type="submit"
                    class="w-11 h-11 flex items-center justify-center rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white shadow-lg shadow-indigo-500/30 transition-all hover:scale-105"
                    title="Aplicar Filtros">
                    <i data-lucide="search" class="w-5 h-5"></i>
                </button>
                <a href="{{ route('usuario.documentos.index') }}"
                    class="w-11 h-11 flex items-center justify-center rounded-xl bg-slate-400 hover:bg-slate-500 text-white shadow-lg shadow-slate-400/30 transition-all hover:scale-105"
                    title="Limpiar Filtros">
                    <i data-lucide="rotate-cw" class="w-5 h-5"></i>
                </a>
            </div>
        </form>

        {{-- 3. TABLA DE RESULTADOS --}}
        <div class="bg-white rounded-2xl shadow-xl shadow-slate-200/50 border border-slate-100 overflow-hidden">
            <div class="overflow-x-auto table-container">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-slate-800">
                        <tr>
                            <th class="px-3 py-3 text-[10px] font-bold text-white uppercase tracking-wider text-center">
                                #</th>
                            <th class="px-3 py-3 text-[10px] font-bold text-white uppercase tracking-wider text-center">
                                Fecha</th>
                            <th class="px-3 py-3 text-[10px] font-bold text-white uppercase tracking-wider">
                                Profesional Solicitante</th>
                            <th class="px-3 py-3 text-[10px] font-bold text-white uppercase tracking-wider">
                                Establecimiento</th>
                            <th class="px-3 py-3 text-[10px] font-bold text-white uppercase tracking-wider text-center">
                                Estado</th>
                            <th class="px-3 py-3 text-[10px] font-bold text-white uppercase tracking-wider text-center">
                                Compromiso</th>
                            <th class="px-3 py-3 text-[10px] font-bold text-white uppercase tracking-wider text-center">
                                D. Jurada</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($documentos as $doc)
                            @php
                                $firmados = 0;
                                if ($doc->pdf_firmado_compromiso)
                                    $firmados++;
                                if ($doc->pdf_firmado_declaracion)
                                    $firmados++;
                                $porcentaje = ($firmados / 2) * 100;

                                // Lógica de colores de estado
                                if ($porcentaje == 100) {
                                    $estadoClass = 'bg-emerald-100 text-emerald-700 border border-emerald-200';
                                    $barraClass = 'bg-emerald-500';
                                    $textoEstado = 'COMPLETADO';
                                } elseif ($porcentaje == 50) {
                                    $estadoClass = 'bg-amber-100 text-amber-700 border border-amber-200';
                                    $barraClass = 'bg-amber-500';
                                    $textoEstado = 'EN PROCESO';
                                } else {
                                    $estadoClass = 'bg-slate-100 text-slate-500 border border-slate-200';
                                    $barraClass = 'bg-slate-300';
                                    $textoEstado = 'PENDIENTE';
                                }
                            @endphp

                            <tr class="hover:bg-indigo-50/30 transition-all group cursor-pointer"
                                onclick="window.location='{{ route('usuario.documentos.edit', $doc->id) }}'">
                                {{-- ID --}}
                                <td class="px-6 py-4 font-mono font-bold text-slate-300 text-center text-xs align-middle">
                                    {{ str_pad($doc->id, 4, '0', STR_PAD_LEFT) }}
                                </td>

                                {{-- Fecha --}}
                                <td class="px-4 py-4 text-center align-middle">
                                    <div class="inline-flex bg-slate-50 px-3 py-2 rounded-lg border border-slate-100">
                                        <span
                                            class="font-bold text-slate-700 text-xs">{{ \Carbon\Carbon::parse($doc->fecha)->format('d/m/Y') }}</span>
                                    </div>
                                </td>

                                {{-- Profesional --}}
                                <td class="px-4 py-4 align-middle">
                                    <div class="flex flex-col">
                                        <span
                                            class="font-bold text-slate-800 uppercase text-xs mb-1 group-hover:text-indigo-700 transition-colors">
                                            {{ $doc->profesional_apellido_paterno }} {{ $doc->profesional_apellido_materno }},
                                            {{ $doc->profesional_nombre }}
                                        </span>
                                        <div class="flex items-center gap-2">
                                            <span
                                                class="bg-slate-100 px-2 py-0.5 rounded text-[9px] font-bold text-slate-500 uppercase tracking-wide border border-slate-200">{{ $doc->profesional_tipo_doc }}</span>
                                            <span
                                                class="text-[10px] font-medium text-slate-400 tracking-wide font-mono">{{ $doc->profesional_doc }}</span>
                                        </div>
                                    </div>
                                </td>

                                {{-- Establecimiento --}}
                                <td class="px-4 py-4 align-middle">
                                    <div class="flex flex-col">
                                        <span class="font-bold text-slate-700 uppercase text-[10px] truncate max-w-[200px]"
                                            title="{{ $doc->establecimiento->nombre_establecimiento }}">
                                            {{ $doc->establecimiento->nombre_establecimiento }}
                                        </span>
                                        <span class="text-[9px] text-slate-400 uppercase mt-0.5 flex items-center gap-1">
                                            <i data-lucide="map-pin" class="w-2.5 h-2.5 text-slate-300"></i>
                                            {{ $doc->establecimiento->distrito }}
                                        </span>
                                    </div>
                                </td>

                                {{-- Estado / Barra Progreso --}}
                                <td class="px-4 py-4 text-center align-middle">
                                    <div class="flex flex-col items-center gap-2">
                                        <span
                                            class="px-3 py-1 rounded-full font-black text-[8px] uppercase tracking-wider {{ $estadoClass }}">
                                            {{ $textoEstado }}
                                        </span>
                                        <div class="w-full max-w-[80px] h-1.5 bg-slate-100 rounded-full overflow-hidden">
                                            <div class="h-full {{ $barraClass }} transition-all duration-700 ease-out"
                                                style="width: {{ $porcentaje }}%"></div>
                                        </div>
                                    </div>
                                </td>

                                {{-- Acciones Compromiso --}}
                                <td class="px-2 py-4 border-l border-slate-50 bg-indigo-50/5 align-middle"
                                    onclick="event.stopPropagation()">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('usuario.documentos.pdf', ['id' => $doc->id, 'tipo' => 'compromiso']) }}"
                                            target="_blank"
                                            class="w-8 h-8 rounded-xl flex items-center justify-center text-slate-400 bg-white border-2 border-slate-100 hover:text-indigo-600 hover:border-indigo-200 transition-all shadow-sm"
                                            title="Imprimir">
                                            <i data-lucide="printer" class="w-3.5 h-3.5"></i>
                                        </a>
                                        @if($doc->pdf_firmado_compromiso)
                                            <a href="{{ asset('storage/' . $doc->pdf_firmado_compromiso) }}" target="_blank"
                                                class="w-8 h-8 rounded-xl flex items-center justify-center text-emerald-600 bg-emerald-50 border-2 border-emerald-100 hover:bg-emerald-100 hover:border-emerald-200 transition-all shadow-sm"
                                                title="Ver Firmado"><i data-lucide="eye" class="w-3.5 h-3.5"></i></a>
                                            <button
                                                onclick="abrirModalSubirFirmado({{ $doc->id }}, '{{ $doc->profesional_nombre }}', 'compromiso')"
                                                class="w-8 h-8 rounded-xl flex items-center justify-center text-blue-500 bg-blue-50 border-2 border-blue-100 hover:bg-blue-100 hover:border-blue-200 transition-all shadow-sm"
                                                title="Reemplazar"><i data-lucide="refresh-cw" class="w-3.5 h-3.5"></i></button>
                                        @else
                                            <button
                                                onclick="abrirModalSubirFirmado({{ $doc->id }}, '{{ $doc->profesional_nombre }}', 'compromiso')"
                                                class="w-8 h-8 rounded-xl flex items-center justify-center text-amber-500 bg-amber-50 border-2 border-amber-100 hover:bg-amber-100 hover:border-amber-200 transition-all shadow-sm animate-pulse"
                                                title="Subir Pendiente"><i data-lucide="upload-cloud"
                                                    class="w-3.5 h-3.5"></i></button>
                                        @endif
                                    </div>
                                </td>

                                {{-- Acciones Declaración --}}
                                <td class="px-2 py-4 border-l border-slate-50 bg-purple-50/5 align-middle"
                                    onclick="event.stopPropagation()">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('usuario.documentos.pdf', ['id' => $doc->id, 'tipo' => 'declaracion']) }}"
                                            target="_blank"
                                            class="w-8 h-8 rounded-xl flex items-center justify-center text-slate-400 bg-white border-2 border-slate-100 hover:text-purple-600 hover:border-purple-200 transition-all shadow-sm"
                                            title="Imprimir">
                                            <i data-lucide="printer" class="w-3.5 h-3.5"></i>
                                        </a>
                                        @if($doc->pdf_firmado_declaracion)
                                            <a href="{{ asset('storage/' . $doc->pdf_firmado_declaracion) }}" target="_blank"
                                                class="w-8 h-8 rounded-xl flex items-center justify-center text-emerald-600 bg-emerald-50 border-2 border-emerald-100 hover:bg-emerald-100 hover:border-emerald-200 transition-all shadow-sm"
                                                title="Ver Firmado"><i data-lucide="eye" class="w-3.5 h-3.5"></i></a>
                                            <button
                                                onclick="abrirModalSubirFirmado({{ $doc->id }}, '{{ $doc->profesional_nombre }}', 'declaracion')"
                                                class="w-8 h-8 rounded-xl flex items-center justify-center text-blue-500 bg-blue-50 border-2 border-blue-100 hover:bg-blue-100 hover:border-blue-200 transition-all shadow-sm"
                                                title="Reemplazar"><i data-lucide="refresh-cw" class="w-3.5 h-3.5"></i></button>
                                        @else
                                            <button
                                                onclick="abrirModalSubirFirmado({{ $doc->id }}, '{{ $doc->profesional_nombre }}', 'declaracion')"
                                                class="w-8 h-8 rounded-xl flex items-center justify-center text-amber-500 bg-amber-50 border-2 border-amber-100 hover:bg-amber-100 hover:border-amber-200 transition-all shadow-sm animate-pulse"
                                                title="Subir Pendiente"><i data-lucide="upload-cloud"
                                                    class="w-3.5 h-3.5"></i></button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7"
                                    class="px-6 py-24 text-center text-slate-300 font-bold uppercase tracking-widest text-xs italic">
                                    No se encontraron resultados</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if ($documentos->hasPages())
            <div class="mt-4">{{ $documentos->appends(request()->query())->links() }}</div>
        @endif
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => { if (typeof lucide !== 'undefined') lucide.createIcons(); });

        function abrirModalSubirFirmado(id, profesional, tipo) {
            const tituloTipo = tipo === 'compromiso' ? 'Compromiso de Confidencialidad' : 'Declaración Jurada';
            const colorTipo = tipo === 'compromiso' ? 'text-indigo-600' : 'text-purple-600';

            Swal.fire({
                title: '<h2 class="text-lg font-black text-slate-800 tracking-tight uppercase text-center">Gestión de Archivos</h2>',
                html: `
                                                    <div class="mt-2 text-left">
                                                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 mb-4 text-center shadow-sm">
                                                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Tipo de Documento</p>
                                                            <p class="text-sm font-black ${colorTipo} leading-tight uppercase tracking-tight">${tituloTipo}</p>
                                                        </div>
                                                        <p class="text-center text-[10px] text-slate-400 font-bold uppercase mb-4 tracking-wide">Profesional: <span class="text-slate-700">${profesional}</span></p>
                                                        <div class="text-[10px] text-slate-500 mb-2 font-bold uppercase tracking-wide ml-1">Seleccione el PDF firmado:</div>
                                                    </div>
                                                `,
                input: 'file',
                inputAttributes: { 'accept': 'application/pdf', 'aria-label': 'Seleccionar PDF', 'class': 'swal2-file-input text-xs' },
                showCancelButton: true,
                confirmButtonText: 'Guardar Archivo',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#4f46e5',
                showLoaderOnConfirm: true,
                customClass: { popup: 'custom-swal-popup', confirmButton: 'custom-swal-confirm shadow-lg', cancelButton: 'rounded-xl font-bold' },
                preConfirm: (file) => {
                    if (!file) { Swal.showValidationMessage('Debe seleccionar un archivo PDF'); return; }
                    const formData = new FormData();
                    formData.append('pdf_firmado', file);
                    formData.append('tipo_doc', tipo);
                    formData.append('_token', '{{ csrf_token() }}');

                    return fetch(`/usuario/documentos-administrativos/${id}/subir-firmado`, {
                        method: 'POST', body: formData,
                        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                    })
                        .then(r => { if (!r.ok) throw new Error(r.statusText); return r.json(); })
                        .catch(error => { Swal.showValidationMessage(`Error al subir: ${error}`); });
                }
            }).then((result) => { if (result.isConfirmed) { location.reload(); } });
        }
    </script>
@endpush