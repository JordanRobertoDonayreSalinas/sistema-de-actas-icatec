@extends('layouts.usuario')

@section('title', 'Generar Documentos de Acceso')

@push('styles')
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <style>
        /* Animaciones */
        @keyframes fade-in-up {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in-up {
            animation: fade-in-up 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        /* Autocomplete UI */
        .ui-autocomplete {
            border-radius: 1rem !important;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1) !important;
            border: 1px solid #e2e8f0 !important;
            padding: 0.5rem !important;
            z-index: 9999 !important;
            background: white !important;
            max-height: 300px;
            overflow-y: auto;
        }

        .ui-menu-item-wrapper {
            padding: 12px 16px !important;
            border-radius: 0.5rem !important;
            font-size: 12px !important;
            font-weight: 600 !important;
            color: #475569 !important;
            text-transform: uppercase;
        }

        .ui-state-active {
            background: #4f46e5 !important;
            color: white !important;
            border: none !important;
        }

        /* Checkbox Cards - Updated for peer-checked pattern */
        .peer:checked~.check-icon {
            transform: scale(1);
            opacity: 1;
        }

        /* Inputs */
        .input-nice {
            background-color: #f8fafc;
            border: 1px solid #cbd5e1;
            transition: all 0.2s ease;
        }

        .input-nice:focus {
            background-color: #ffffff;
            border-color: #6366f1;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
            outline: none;
        }
    </style>
@endpush

@section('content')
    <div class="min-h-screen bg-slate-50/50 pb-20 pt-8">
        <div class="max-w-5xl mx-auto px-4 sm:px-6">

            <form action="{{ route('usuario.documentos.store') }}" method="POST" class="animate-fade-in-up space-y-8">
                @csrf

                {{-- ENVIAMOS "AMBOS" AUTOMÁTICAMENTE --}}
                <input type="hidden" name="tipo_formato" value="AMBOS">

                {{-- CABECERA --}}
                <div
                    class="bg-white rounded-[2rem] p-6 shadow-lg border border-slate-200/60 flex flex-col md:flex-row items-center justify-between gap-6">
                    <div class="flex items-center gap-4 w-full md:w-auto">
                        <div class="p-3 bg-indigo-600 rounded-2xl text-white shadow-lg shadow-indigo-200">
                            <i data-lucide="files" class="w-7 h-7"></i>
                        </div>
                        <div>
                            <h1 class="text-2xl font-black text-slate-800 tracking-tighter uppercase leading-none">Generar
                                Documentos</h1>
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-1">Se generarán:
                                Compromiso y Declaración Jurada</p>
                        </div>
                    </div>

                    <div class="bg-slate-50 px-6 py-3 rounded-2xl border border-slate-200 min-w-[200px]">
                        <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-1">Fecha
                            Emisión</label>
                        <input type="date" name="fecha" value="{{ date('Y-m-d') }}"
                            class="font-black text-slate-700 bg-transparent border-none p-0 focus:ring-0 text-base w-full cursor-pointer">
                    </div>
                </div>

                {{-- 1. DATOS DEL PROFESIONAL (VERTICAL) --}}
                <div class="bg-white p-8 rounded-[2.5rem] shadow-xl border border-slate-200/60 relative overflow-hidden">
                    <div class="absolute top-0 left-0 w-1.5 h-full bg-gradient-to-b from-indigo-500 to-purple-500"></div>

                    <h3 class="text-slate-800 font-black text-xs uppercase tracking-widest mb-8 flex items-center gap-3">
                        <span
                            class="bg-indigo-100 text-indigo-600 w-8 h-8 rounded-xl flex items-center justify-center text-sm shadow-sm border border-indigo-200/50">1</span>
                        Datos Personales del Solicitante
                    </h3>

                    <div class="pl-2">
                        <x-busqueda-profesional prefix="solicitante" :detalle="$detalle" />
                    </div>
                </div>

                {{-- 2. DATOS LABORALES (VERTICAL) --}}
                <div class="bg-white p-8 rounded-[2.5rem] shadow-xl border border-slate-200/60 relative overflow-hidden">
                    <div class="absolute top-0 left-0 w-1.5 h-full bg-gradient-to-b from-purple-500 to-pink-500"></div>

                    <h3 class="text-slate-800 font-black text-xs uppercase tracking-widest mb-8 flex items-center gap-3">
                        <span
                            class="bg-purple-100 text-purple-600 w-8 h-8 rounded-xl flex items-center justify-center text-sm shadow-sm border border-purple-200/50">2</span>
                        Datos Laborales y Ubicación
                    </h3>

                    <div class="pl-2 space-y-8">
                        {{-- Buscador IPRESS --}}
                        <div>
                            <label
                                class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 mb-2 block">Establecimiento
                                de Salud (IPRESS)</label>
                            <div class="relative group">
                                <input type="text" id="establecimiento_search"
                                    placeholder="Escribe el nombre del establecimiento..." autocomplete="off"
                                    class="input-nice w-full rounded-2xl py-4 pl-5 pr-12 text-sm font-bold text-slate-700 placeholder:text-slate-400 uppercase">
                                <div
                                    class="absolute right-4 top-4 text-slate-300 group-hover:text-purple-400 transition-colors">
                                    <i data-lucide="search" class="w-5 h-5"></i>
                                </div>
                                <input type="hidden" name="establecimiento_id" id="establecimiento_id">
                            </div>

                            <div id="establecimiento_seleccionado" class="mt-3 hidden animate-fade-in-up">
                                <div
                                    class="bg-emerald-50 rounded-2xl p-4 border border-emerald-100 flex items-center gap-4 shadow-sm">
                                    <div class="bg-emerald-100 p-2 rounded-xl text-emerald-600">
                                        <i data-lucide="map-pin" class="w-5 h-5"></i>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-[9px] font-black text-emerald-400 uppercase tracking-widest mb-0.5">
                                            IPRESS Seleccionada</p>
                                        <p id="establecimiento_nombre"
                                            class="text-sm font-black text-emerald-900 leading-tight uppercase"></p>
                                    </div>
                                    <button type="button" id="btn_clear_estab"
                                        class="text-slate-400 hover:text-red-500 p-2 hover:bg-red-50 rounded-xl transition-all">
                                        <i data-lucide="x" class="w-5 h-5"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            {{-- Área / Rol --}}
                            <div>
                                <label
                                    class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 mb-2 block">Área
                                    / Oficina Solicitante</label>
                                <input type="text" name="area_oficina" required
                                    placeholder="EJ: ADMISION, TRIAJE, EMERGENCIA"
                                    class="input-nice w-full rounded-2xl py-4 px-5 text-sm font-bold text-slate-700 uppercase">
                            </div>

                            {{-- Cargo / Funciones --}}
                            <div>
                                <label
                                    class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 mb-2 block">Cargo
                                    / Funciones</label>
                                <input type="text" name="cargo_rol" required placeholder="EJ: MEDICO CIRUJANO, ENFERMERA"
                                    class="input-nice w-full rounded-2xl py-4 px-5 text-sm font-bold text-slate-700 uppercase">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 3. SELECCIÓN DE MÓDULOS (VERTICAL) --}}
                <div class="bg-white p-8 rounded-[2.5rem] shadow-xl border border-slate-200/60 relative overflow-hidden">
                    <div class="absolute top-0 left-0 w-1.5 h-full bg-gradient-to-b from-pink-500 to-orange-500"></div>

                    <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-6 gap-4">
                        <h3 class="text-slate-800 font-black text-xs uppercase tracking-widest flex items-center gap-3">
                            <span
                                class="bg-pink-100 text-pink-600 w-8 h-8 rounded-xl flex items-center justify-center text-sm shadow-sm border border-pink-200/50">3</span>
                            Selección de Módulos
                        </h3>
                        <div class="flex items-center gap-3">
                            <span id="contador-modulos"
                                class="text-[10px] font-black text-slate-400 bg-slate-100 px-4 py-2 rounded-xl uppercase tracking-wide border border-slate-200">
                                <span class="text-indigo-600" id="count-selected">0</span> seleccionados
                            </span>
                            <span
                                class="text-[10px] font-bold text-indigo-500 bg-indigo-50 px-4 py-2 rounded-xl uppercase tracking-wide border border-indigo-100">
                                Seleccione uno o varios
                            </span>
                        </div>
                    </div>

                    <div class="pl-2 grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                        @php
                            $modulos = [
                                ['nombre' => 'Gestion Administrativa', 'icono' => 'folder-cog'],
                                ['nombre' => 'Citas', 'icono' => 'calendar-check'],
                                ['nombre' => 'Triaje', 'icono' => 'stethoscope'],
                                ['nombre' => 'Consulta Externa: Medicina', 'icono' => 'user-round-check'],
                                ['nombre' => 'Consulta Externa: Odontologia', 'icono' => 'smile'],
                                ['nombre' => 'Consulta Externa: Nutricion', 'icono' => 'apple'],
                                ['nombre' => 'Consulta Externa: Psicologia', 'icono' => 'brain'],
                                ['nombre' => 'Cred', 'icono' => 'baby'],
                                ['nombre' => 'Inmunizaciones', 'icono' => 'syringe'],
                                ['nombre' => 'Atencion Prenatal', 'icono' => 'heart-pulse'],
                                ['nombre' => 'Planificacion Familiar', 'icono' => 'users'],
                                ['nombre' => 'Parto', 'icono' => 'heart-handshake'],
                                ['nombre' => 'Puerperio', 'icono' => 'bed'],
                                ['nombre' => 'Fua Electronico', 'icono' => 'file-text'],
                                ['nombre' => 'Farmacia', 'icono' => 'pill'],
                                ['nombre' => 'Refcon', 'icono' => 'arrow-right-left'],
                                ['nombre' => 'Laboratorio', 'icono' => 'flask-conical'],
                                ['nombre' => 'Urgencias y Emergencias', 'icono' => 'ambulance']
                            ];
                        @endphp

                        @foreach($modulos as $modulo)
                            <label class="cursor-pointer group relative block modulo-checkbox">
                                <input type="checkbox" name="sistemas_acceso[]" value="{{ $modulo['nombre'] }}"
                                    class="checkbox-card peer absolute opacity-0 w-0 h-0" onchange="actualizarContador()">

                                <div
                                    class="h-full min-h-[90px] px-4 py-3.5 rounded-xl border-2 border-slate-200/80 bg-white hover:border-indigo-300 hover:shadow-md hover:scale-[1.02] transition-all duration-200 flex flex-col gap-2 relative overflow-hidden peer-checked:border-indigo-500 peer-checked:bg-gradient-to-br peer-checked:from-indigo-50 peer-checked:to-purple-50/30 peer-checked:shadow-lg peer-checked:shadow-indigo-100/50 peer-checked:scale-[1.02]">
                                    <div class="flex items-start justify-between gap-2">
                                        <div
                                            class="flex-shrink-0 w-7 h-7 rounded-lg bg-slate-100 flex items-center justify-center text-slate-400 transition-colors group-has-[:checked]:bg-indigo-100 group-has-[:checked]:text-indigo-600">
                                            <i data-lucide="{{ $modulo['icono'] }}" class="w-4 h-4"></i>
                                        </div>
                                        <div
                                            class="flex-shrink-0 w-5 h-5 rounded-full border-2 border-slate-300 bg-white flex items-center justify-center transition-all group-has-[:checked]:border-indigo-600 group-has-[:checked]:bg-indigo-600 group-has-[:checked]:scale-110">
                                            <i data-lucide="check"
                                                class="w-3 h-3 text-white opacity-0 transition-opacity group-has-[:checked]:opacity-100"></i>
                                        </div>
                                    </div>
                                    <span
                                        class="text-[10px] font-bold uppercase text-slate-700 leading-tight transition-colors group-has-[:checked]:text-indigo-900">
                                        {{ $modulo['nombre'] }}
                                    </span>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                @push('scripts')
                    <script>
                        function actualizarContador() {
                            const checkboxes = document.querySelectorAll('.modulo-checkbox input[type="checkbox"]');
                            const count = Array.from(checkboxes).filter(cb => cb.checked).length;
                            document.getElementById('count-selected').textContent = count;
                        }
                        // Actualizar contador al cargar la página
                        document.addEventListener('DOMContentLoaded', actualizarContador);
                    </script>
                @endpush

                {{-- BOTONES --}}
                <div class="flex flex-col sm:flex-row justify-end items-center gap-4 pt-6 pb-12">
                    <a href="{{ route('usuario.documentos.index') }}"
                        class="w-full sm:w-auto text-center px-8 py-4 rounded-2xl font-black text-slate-400 uppercase text-[11px] hover:bg-slate-100 hover:text-slate-600 transition-colors">
                        Cancelar
                    </a>
                    <button type="submit"
                        class="w-full sm:w-auto bg-indigo-600 hover:bg-indigo-700 text-white px-12 py-4 rounded-2xl font-black text-xs uppercase shadow-xl shadow-indigo-200 transition-all transform hover:scale-[1.02] active:scale-95 flex items-center justify-center gap-3">
                        <span>Guardar y Generar Documentos</span>
                        <i data-lucide="save" class="w-5 h-5"></i>
                    </button>
                </div>

            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
    <script>
        $(function () {
            lucide.createIcons();

            $("#establecimiento_search").autocomplete({
                minLength: 2,
                source: "{{ route('establecimientos.buscar') }}",
                select: function (event, ui) {
                    event.preventDefault();
                    $("#establecimiento_search").val('');
                    $("#establecimiento_id").val(ui.item.id);
                    $("#establecimiento_nombre").text(ui.item.value);
                    $("#establecimiento_seleccionado").removeClass('hidden').addClass('flex');
                    return false;
                }
            });

            $("#btn_clear_estab").on('click', function () {
                $("#establecimiento_id").val('');
                $("#establecimiento_seleccionado").addClass('hidden').removeClass('flex');
                $("#establecimiento_search").focus();
            });
        });
    </script>
@endpush