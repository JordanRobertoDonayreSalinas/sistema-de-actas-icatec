@extends('layouts.usuario')

@section('title', 'Documentos Administrativos')

@push('styles')
    <style>
        .input-modern {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 1rem;
            color: #334155;
            font-size: 0.75rem;
            font-weight: 700;
            padding-top: 0.75rem;
            padding-bottom: 0.75rem;
            transition: all 0.2s ease-in-out;
        }

        .input-modern:focus {
            background-color: #ffffff;
            border-color: #6366f1;
            box-shadow: 0 4px 6px -1px rgba(99, 102, 241, 0.1), 0 2px 4px -1px rgba(99, 102, 241, 0.06);
            outline: none;
        }

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

        [x-cloak] {
            display: none !important;
        }

        .custom-swal-popup {
            border-radius: 1.5rem !important;
            padding: 2rem !important;
        }

        /* Estilos Premium para la Tabla */
        .btn-circle-alt {
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
            border: 1px solid transparent;
        }

        .btn-circle-alt:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .bg-view { background-color: #ecfdf5; color: #10b981; }
        .bg-view:hover { border-color: #10b981; }
        
        .bg-refresh { background-color: #eff6ff; color: #3b82f6; }
        .bg-refresh:hover { border-color: #3b82f6; }

        .bg-upload { background-color: #fffbeb; color: #f59e0b; }
        .bg-upload:hover { border-color: #f59e0b; }

        .progress-bar-container {
            width: 100%;
            height: 6px;
            background-color: #f1f5f9;
            border-radius: 10px;
            overflow: hidden;
            margin-top: 6px;
        }

        .progress-bar-fill {
            height: 100%;
            background: linear-gradient(90deg, #10b981, #34d399);
            border-radius: 10px;
            transition: width 0.6s cubic-bezier(0.4, 0, 0.2, 1);
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

        {{-- ETFs --}}
        <div
            class="bg-gradient-to-r from-indigo-900 to-indigo-700 p-5 rounded-2xl shadow-xl mb-6 relative overflow-hidden text-white">
            <div
                class="absolute right-0 top-0 w-64 h-64 bg-white/10 rounded-full -mr-16 -mt-16 blur-3xl pointer-events-none">
            </div>
            <div class="relative z-10 flex flex-col lg:flex-row items-center justify-between gap-6">
                <div class="flex flex-wrap items-center gap-3">
                    <div
                        class="bg-slate-900 text-white rounded-xl px-5 py-2.5 shadow-lg border border-slate-700 flex flex-col items-center min-w-[100px]">
                        <span class="text-2xl font-bold leading-none">{{ $documentos->total() }}</span>
                        <span
                            class="text-[0.65rem] uppercase tracking-widest text-slate-400 font-semibold mt-1">Total</span>
                    </div>
                    <div
                        class="bg-white/20 backdrop-blur-md text-white rounded-xl px-5 py-2.5 border border-white/30 flex flex-col items-center min-w-[100px]">
                        <span class="text-2xl font-bold leading-none">{{ $countCompletados ?? 0 }}</span>
                        <span
                            class="text-[0.65rem] uppercase tracking-widest text-indigo-100 font-semibold mt-1">Firmadas</span>
                    </div>
                    <div
                        class="bg-amber-500 text-white rounded-xl px-5 py-2.5 shadow-lg border border-amber-400 flex flex-col items-center min-w-[100px]">
                        <span class="text-2xl font-bold leading-none">{{ $countPendientes ?? 0 }}</span>
                        <span
                            class="text-[0.65rem] uppercase tracking-widest text-amber-100 font-semibold mt-1">Pendientes</span>
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

        {{-- PANEL DE FILTROS --}}
        <form x-show="open" x-cloak x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
            method="GET" action="{{ route('usuario.documentos.index') }}"
            class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200 mb-6 space-y-4">

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 xl:grid-cols-6 gap-4">
                {{-- Búsqueda --}}
                <div class="xl:col-span-2">
                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1 ml-1">Profesional</label>
                    <div class="input-icon-wrapper">
                        <i data-lucide="search" class="input-icon"></i>
                        <input type="text" name="search" value="{{ request('search') }}"
                            class="input-modern w-full input-with-icon uppercase" placeholder="DNI, Nombres o Apellidos...">
                    </div>
                </div>

                {{-- Provincia --}}
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1 ml-1">Provincia</label>
                    <select id="provincia" name="provincia" class="input-modern w-full px-4 uppercase">
                        <option value="">TODAS</option>
                        @foreach($provincias as $prov)
                            <option value="{{ $prov }}" {{ request('provincia') == $prov ? 'selected' : '' }}>{{ $prov }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Distrito --}}
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1 ml-1">Distrito</label>
                    <select id="distrito" name="distrito" class="input-modern w-full px-4 uppercase">
                        <option value="">TODOS</option>
                        @foreach($distritos as $dist)
                            <option value="{{ $dist }}" {{ request('distrito') == $dist ? 'selected' : '' }}>{{ $dist }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Establecimiento --}}
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1 ml-1">Establecimiento</label>
                    <select id="establecimiento" name="establecimiento_id" class="input-modern w-full px-4 uppercase">
                        <option value="">TODOS</option>
                        @foreach($establecimientos as $est)
                            <option value="{{ $est->id }}" {{ request('establecimiento_id') == $est->id ? 'selected' : '' }}>{{ $est->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Estado --}}
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1 ml-1">Estado</label>
                    <select name="estado" class="input-modern w-full px-4 uppercase">
                        <option value="">TODOS</option>
                        <option value="firmada" {{ request('estado') == 'firmada' ? 'selected' : '' }}>COMPLETADOS</option>
                        <option value="pendiente" {{ request('estado') == 'pendiente' ? 'selected' : '' }}>PENDIENTES</option>
                    </select>
                </div>

                {{-- Fecha Inicio --}}
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1 ml-1">Desde</label>
                    <input type="date" name="fecha_inicio" value="{{ $fecha_inicio }}" class="input-modern w-full px-4">
                </div>

                {{-- Fecha Fin --}}
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1 ml-1">Hasta</label>
                    <input type="date" name="fecha_fin" value="{{ $fecha_fin }}" class="input-modern w-full px-4">
                </div>
            </div>

            <div class="flex items-center gap-3 pt-2 border-t border-slate-50">
                <button type="submit"
                    class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs flex items-center gap-2 transition-all">
                    <i data-lucide="search" class="w-4 h-4"></i> FILTRAR
                </button>
                <a href="{{ route('usuario.documentos.index') }}"
                    class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs flex items-center gap-2 transition-all">
                    <i data-lucide="rotate-ccw" class="w-4 h-4"></i> LIMPIAR
                </a>
            </div>
        </form>

        {{-- TABLA --}}
        <div class="bg-white rounded-2xl shadow-xl shadow-slate-200/50 border border-slate-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-[#1e293b] text-white">
                        <tr>
                            <th class="px-6 py-4 text-[10px] font-bold uppercase">PROFESIONAL SOLICITANTE</th>
                            <th class="px-6 py-4 text-[10px] font-bold uppercase text-center">ESTABLECIMIENTO</th>
                            <th class="px-6 py-4 text-[10px] font-bold uppercase text-center">ESTADO</th>
                            <th class="px-6 py-4 text-[10px] font-bold uppercase text-center">COMPROMISO</th>
                            <th class="px-6 py-4 text-[10px] font-bold uppercase text-center">D. JURADA</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($documentos as $doc)
                            @php
                                $firmados = ($doc->pdf_firmado_compromiso ? 1 : 0) + ($doc->pdf_firmado_declaracion ? 1 : 0);
                                $porcentaje = ($firmados / 2) * 100;
                            @endphp
                            <tr class="hover:bg-slate-50/50 transition-all group pointer cursor-pointer"
                                onclick="window.location='{{ route('usuario.documentos.edit', $doc->id) }}'">
                                {{-- Profesional --}}
                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <span class="font-bold text-slate-800 text-sm uppercase leading-tight">
                                            {{ $doc->profesional_nombre }} {{ $doc->profesional_apellido_paterno }} {{ $doc->profesional_apellido_materno }}
                                        </span>
                                        <div class="flex items-center gap-2 mt-1.5">
                                            <span class="px-1.5 py-0.5 bg-slate-100 text-slate-500 rounded text-[9px] font-bold border border-slate-200">DNI</span>
                                            <span class="text-slate-400 text-[11px] font-mono tracking-wider">{{ $doc->profesional_doc }}</span>
                                        </div>
                                    </div>
                                </td>

                                {{-- Establecimiento --}}
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-2 text-slate-400 uppercase font-semibold text-[10px]">
                                        <i data-lucide="map-pin" class="w-3 h-3 text-slate-300"></i>
                                        <span>{{ $doc->establecimiento->nombre }}</span>
                                    </div>
                                </td>

                                {{-- Estado General --}}
                                <td class="px-6 py-4">
                                    <div class="flex flex-col items-center">
                                        <span class="px-4 py-1.5 rounded-full text-[9px] font-black leading-none {{ $porcentaje == 100 ? 'bg-emerald-100 text-emerald-600' : 'bg-amber-50 text-amber-600' }}">
                                            {{ $porcentaje == 100 ? 'COMPLETADO' : ($porcentaje == 0 ? 'PENDIENTE' : 'PARCIAL') }}
                                        </span>
                                        <div class="progress-bar-container w-24">
                                            <div class="progress-bar-fill" style="width: {{ $porcentaje }}%"></div>
                                        </div>
                                    </div>
                                </td>

                                {{-- Compromiso --}}
                                <td class="px-6 py-4" onclick="event.stopPropagation()">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('usuario.documentos.pdf', ['id' => $doc->id, 'tipo' => 'compromiso']) }}"
                                            target="_blank" class="btn-circle-alt bg-white border border-slate-100 text-slate-400 hover:text-indigo-600"
                                            title="Imprimir Compromiso">
                                            <i data-lucide="printer" class="w-4 h-4"></i>
                                        </a>
                                        @if($doc->pdf_firmado_compromiso)
                                            <a href="{{ asset('storage/' . $doc->pdf_firmado_compromiso) }}" target="_blank"
                                                class="btn-circle-alt bg-view hover:bg-emerald-200 transition-colors" title="Ver Firma">
                                                <i data-lucide="eye" class="w-4 h-4"></i>
                                            </a>
                                            <button onclick="abrirModalSubirFirmado({{ $doc->id }}, '{{ $doc->profesional_nombre }}', 'compromiso')"
                                                class="btn-circle-alt bg-refresh hover:bg-blue-200 transition-colors" title="Reemplazar Documento">
                                                <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                                            </button>
                                        @else
                                            <button onclick="abrirModalSubirFirmado({{ $doc->id }}, '{{ $doc->profesional_nombre }}', 'compromiso')"
                                                class="btn-circle-alt bg-upload hover:bg-amber-200 transition-colors" title="Subir Compromiso Firmado">
                                                <i data-lucide="upload-cloud" class="w-4 h-4"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>

                                {{-- Declaración Jurada --}}
                                <td class="px-6 py-4" onclick="event.stopPropagation()">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('usuario.documentos.pdf', ['id' => $doc->id, 'tipo' => 'declaracion']) }}"
                                            target="_blank" class="btn-circle-alt bg-white border border-slate-100 text-slate-400 hover:text-purple-600"
                                            title="Imprimir DJ">
                                            <i data-lucide="printer" class="w-4 h-4"></i>
                                        </a>
                                        @if($doc->pdf_firmado_declaracion)
                                            <a href="{{ asset('storage/' . $doc->pdf_firmado_declaracion) }}" target="_blank"
                                                class="btn-circle-alt bg-view hover:bg-emerald-200 transition-colors" title="Ver Firma">
                                                <i data-lucide="eye" class="w-4 h-4"></i>
                                            </a>
                                            <button onclick="abrirModalSubirFirmado({{ $doc->id }}, '{{ $doc->profesional_nombre }}', 'declaracion')"
                                                class="btn-circle-alt bg-refresh hover:bg-blue-200 transition-colors" title="Reemplazar DJ">
                                                <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                                            </button>
                                        @else
                                            <button onclick="abrirModalSubirFirmado({{ $doc->id }}, '{{ $doc->profesional_nombre }}', 'declaracion')"
                                                class="btn-circle-alt bg-upload hover:bg-amber-200 transition-colors" title="Subir DJ Firmada">
                                                <i data-lucide="upload-cloud" class="w-4 h-4"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-20 text-center text-slate-400 text-xs italic">
                                    <i data-lucide="folder-open" class="w-8 h-8 mx-auto mb-3 opacity-20"></i>
                                    No hay documentos registrados en este periodo
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($documentos->hasPages())
                <div class="p-4 border-t border-slate-50 text-xs">{{ $documentos->appends(request()->query())->links() }}</div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => { lucide.createIcons(); });

        const provinciaSelect = document.getElementById('provincia');
        const distritoSelect = document.getElementById('distrito');
        const establecimientoSelect = document.getElementById('establecimiento');

        provinciaSelect.addEventListener('change', async () => {
            distritoSelect.innerHTML = '<option value="">TODOS</option>';
            establecimientoSelect.innerHTML = '<option value="">TODOS</option>';
            if (provinciaSelect.value) {
                const resDist = await fetch(`{{ route('usuario.documentos.ajax.distritos') }}?provincia=${provinciaSelect.value}`);
                const dataDist = await resDist.json();
                dataDist.forEach(d => {
                    const opt = document.createElement('option');
                    opt.value = d;
                    opt.textContent = d;
                    distritoSelect.appendChild(opt);
                });
            }
        });

        distritoSelect.addEventListener('change', async () => {
            establecimientoSelect.innerHTML = '<option value="">TODOS</option>';
            if (distritoSelect.value) {
                const resEst = await fetch(`{{ route('usuario.documentos.ajax.establecimientos') }}?provincia=${provinciaSelect.value}&distrito=${distritoSelect.value}`);
                const dataEst = await resEst.json();
                dataEst.forEach(e => {
                    const opt = document.createElement('option');
                    opt.value = e.id;
                    opt.textContent = e.nombre;
                    establecimientoSelect.appendChild(opt);
                });
            }
        });

        function abrirModalSubirFirmado(id, profesional, tipo) {
            const tituloTipo = tipo === 'compromiso' ? 'Compromiso de Confidencialidad' : 'Declaración Jurada';
            Swal.fire({
                title: 'SUBIR DOCUMENTO FIRMADO',
                html: `<div class="p-4 bg-slate-50 rounded-xl mb-4 text-xs font-bold text-slate-600 uppercase">${tituloTipo}<br><span class="text-[10px] text-slate-400">${profesional}</span></div>`,
                input: 'file',
                inputAttributes: { 'accept': 'application/pdf' },
                showCancelButton: true,
                confirmButtonText: 'GUARDAR',
                preConfirm: (file) => {
                    if (!file) { Swal.showValidationMessage('Seleccione un PDF'); return; }
                    const formData = new FormData();
                    formData.append('pdf_firmado', file);
                    formData.append('tipo_doc', tipo);
                    return fetch(`/usuario/documentos-administrativos/${id}/subir-firmado`, {
                        method: 'POST', body: formData,
                        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                    })
                        .then(r => { if (!r.ok) throw new Error(); return r.json(); })
                        .catch(() => { Swal.showValidationMessage('Error al subir el archivo'); });
                }
            }).then((result) => { if (result.isConfirmed) location.reload(); });
        }
    </script>
@endpush