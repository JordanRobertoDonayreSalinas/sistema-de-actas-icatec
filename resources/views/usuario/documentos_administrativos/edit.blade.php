@extends('layouts.usuario')

@section('title', 'Editar Documento Administrativo')

@push('styles')
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <style>
        /* Animaciones */
        @keyframes fade-in-up { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        .animate-fade-in-up { animation: fade-in-up 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
        
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
        .ui-state-active { background: #4f46e5 !important; color: white !important; border: none !important; }
        
        /* Checkbox Cards */
        .checkbox-card:checked + div { 
            background-color: #eef2ff; 
            border-color: #6366f1; 
            color: #4338ca; 
            box-shadow: 0 4px 6px -1px rgba(99, 102, 241, 0.1); 
        }
        .checkbox-card:checked + div .check-icon { transform: scale(1); opacity: 1; }
        
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
        
        <form action="{{ route('usuario.documentos.update', $doc->id) }}" method="POST" class="animate-fade-in-up space-y-8">
            @csrf
            @method('PUT')
            
            {{-- ENVIAMOS "AMBOS" AUTOMÁTICAMENTE --}}
            <input type="hidden" name="tipo_formato" value="AMBOS">

            {{-- CABECERA --}}
            <div class="bg-white rounded-[2rem] p-6 shadow-lg border border-slate-200/60 flex flex-col md:flex-row items-center justify-between gap-6">
                <div class="flex items-center gap-4 w-full md:w-auto">
                    <div class="p-3 bg-purple-600 rounded-2xl text-white shadow-lg shadow-purple-200">
                        <i data-lucide="edit" class="w-7 h-7"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-black text-slate-800 tracking-tighter uppercase leading-none">Editar Documento</h1>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-1">Modificar datos del registro #{{ str_pad($doc->id, 4, '0', STR_PAD_LEFT) }}</p>
                    </div>
                </div>

                <div class="bg-slate-50 px-6 py-3 rounded-2xl border border-slate-200 min-w-[200px]">
                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-1">Fecha Emisión</label>
                    <input type="date" name="fecha" value="{{ $doc->fecha }}" class="font-black text-slate-700 bg-transparent border-none p-0 focus:ring-0 text-base w-full cursor-pointer">
                </div>
            </div>

            {{-- 1. DATOS DEL PROFESIONAL (VERTICAL) --}}
            <div class="bg-white p-8 rounded-[2.5rem] shadow-xl border border-slate-200/60 relative overflow-hidden">
                <div class="absolute top-0 left-0 w-1.5 h-full bg-gradient-to-b from-indigo-500 to-purple-500"></div>
                
                <h3 class="text-slate-800 font-black text-xs uppercase tracking-widest mb-8 flex items-center gap-3">
                    <span class="bg-indigo-100 text-indigo-600 w-8 h-8 rounded-xl flex items-center justify-center text-sm shadow-sm border border-indigo-200/50">1</span>
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
                    <span class="bg-purple-100 text-purple-600 w-8 h-8 rounded-xl flex items-center justify-center text-sm shadow-sm border border-purple-200/50">2</span>
                    Datos Laborales y Ubicación
                </h3>

                <div class="pl-2 space-y-8">
                    {{-- Buscador IPRESS --}}
                    <div>
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 mb-2 block">Establecimiento de Salud (IPRESS)</label>
                        
                        {{-- Campo de búsqueda (oculto inicialmente porque ya hay un establecimiento) --}}
                        <div id="establecimiento_search_wrapper" class="relative group hidden">
                            <input type="text" id="establecimiento_search" placeholder="Escribe el nombre del establecimiento..." autocomplete="off"
                                   class="input-nice w-full rounded-2xl py-4 pl-5 pr-12 text-sm font-bold text-slate-700 placeholder:text-slate-400 uppercase">
                            <div class="absolute right-4 top-4 text-slate-300 group-hover:text-purple-400 transition-colors">
                                <i data-lucide="search" class="w-5 h-5"></i>
                            </div>
                        </div>
                        
                        <input type="hidden" name="establecimiento_id" id="establecimiento_id" value="{{ $doc->establecimiento_id }}">
                        
                        {{-- Establecimiento seleccionado (visible inicialmente) --}}
                        <div id="establecimiento_seleccionado" class="flex animate-fade-in-up">
                            <div class="bg-emerald-50 rounded-2xl p-4 border border-emerald-100 flex items-center gap-4 shadow-sm w-full">
                                <div class="bg-emerald-100 p-2 rounded-xl text-emerald-600">
                                    <i data-lucide="building-2" class="w-5 h-5"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-black text-emerald-900 leading-tight uppercase mb-1" id="establecimiento_nombre">{{ $doc->establecimiento->nombre }}</p>
                                    <p class="text-[9px] font-bold text-emerald-600 uppercase tracking-widest flex items-center gap-1.5">
                                        <span class="bg-emerald-200/50 px-2 py-0.5 rounded">Código: {{ $doc->establecimiento->codigo }}</span>
                                        <span class="text-emerald-400">•</span>
                                        <span>{{ $doc->establecimiento->distrito }}</span>
                                    </p>
                                </div>
                                <button type="button" id="btn_clear_estab" class="text-slate-400 hover:text-purple-600 p-2 hover:bg-purple-50 rounded-xl transition-all" title="Cambiar establecimiento">
                                    <i data-lucide="edit-3" class="w-5 h-5"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        {{-- Área / Rol --}}
                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 mb-2 block">Área / Oficina Solicitante</label>
                            <input type="text" name="area_oficina" required placeholder="EJ: ADMISION, TRIAJE, EMERGENCIA" value="{{ $doc->area_oficina }}"
                                   class="input-nice w-full rounded-2xl py-4 px-5 text-sm font-bold text-slate-700 uppercase">
                        </div>

                        {{-- Cargo / Funciones --}}
                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 mb-2 block">Cargo / Funciones</label>
                            <input type="text" name="cargo_rol" required placeholder="EJ: MEDICO CIRUJANO, ENFERMERA" value="{{ $doc->cargo_rol }}"
                                   class="input-nice w-full rounded-2xl py-4 px-5 text-sm font-bold text-slate-700 uppercase">
                        </div>
                    </div>
                </div>
            </div>

            {{-- 3. SELECCIÓN DE MÓDULOS (VERTICAL) --}}
            <div class="bg-white p-8 rounded-[2.5rem] shadow-xl border border-slate-200/60 relative overflow-hidden">
                <div class="absolute top-0 left-0 w-1.5 h-full bg-gradient-to-b from-pink-500 to-orange-500"></div>

                <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-8 gap-4">
                    <h3 class="text-slate-800 font-black text-xs uppercase tracking-widest flex items-center gap-3">
                        <span class="bg-pink-100 text-pink-600 w-8 h-8 rounded-xl flex items-center justify-center text-sm shadow-sm border border-pink-200/50">3</span>
                        Selección de Módulos
                    </h3>
                    <span class="text-[10px] font-bold text-indigo-500 bg-indigo-50 px-4 py-2 rounded-xl uppercase tracking-wide border border-indigo-100">
                        Seleccione uno o varios accesos
                    </span>
                </div>
                
                <div class="pl-2 grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
                    @php
                        $modulos = [
                            'Gestion Administrativa', 'Citas', 'Triaje', 
                            'Consulta Externa: Medicina', 'Consulta Externa: Odontologia', 
                            'Consulta Externa: Nutricion', 'Consulta Externa: Psicologia',
                            'Cred', 'Inmunizaciones', 'Atencion Prenatal', 
                            'Planificacion Familiar', 'Parto', 'Puerperio', 
                            'Fua Electronico', 'Farmacia', 'Refcon',
                            'Laboratorio', 'Urgencias y Emergencias'
                        ];
                        
                        // Convertir los sistemas de acceso guardados en un array
                        $sistemasSeleccionados = array_map('trim', explode(',', $doc->sistemas_acceso));
                    @endphp

                    @foreach($modulos as $modulo)
                    <label class="cursor-pointer group relative">
                        <input type="checkbox" name="sistemas_acceso[]" value="{{ $modulo }}" 
                               {{ in_array($modulo, $sistemasSeleccionados) ? 'checked' : '' }}
                               class="checkbox-card absolute opacity-0 w-0 h-0">
                        
                        <div class="h-full min-h-[90px] p-5 rounded-2xl border-2 border-slate-100 bg-slate-50 group-hover:border-indigo-200 group-hover:bg-white transition-all duration-200 flex flex-col justify-between relative overflow-hidden">
                            <span class="text-[10px] font-black uppercase text-slate-600 group-hover:text-indigo-800 leading-snug z-10 pr-6">
                                {{ $modulo }}
                            </span>
                            <div class="absolute bottom-3 right-3 z-10">
                                <div class="check-icon w-6 h-6 bg-indigo-600 rounded-full flex items-center justify-center text-white opacity-0 transform scale-50 transition-all duration-300 shadow-lg shadow-indigo-200">
                                    <i data-lucide="check" class="w-3.5 h-3.5"></i>
                                </div>
                            </div>
                            <div class="absolute -bottom-6 -right-6 w-20 h-20 bg-gradient-to-br from-slate-200/40 to-transparent rounded-full z-0 group-hover:from-indigo-100/50 transition-colors"></div>
                        </div>
                    </label>
                    @endforeach
                </div>
            </div>

            {{-- BOTONES --}}
            <div class="flex flex-col sm:flex-row justify-end items-center gap-4 pt-6 pb-12">
                <a href="{{ route('usuario.documentos.index') }}" class="w-full sm:w-auto text-center px-8 py-4 rounded-2xl font-black text-slate-400 uppercase text-[11px] hover:bg-slate-100 hover:text-slate-600 transition-colors">
                    Cancelar
                </a>
                <button type="submit" class="w-full sm:w-auto bg-purple-600 hover:bg-purple-700 text-white px-12 py-4 rounded-2xl font-black text-xs uppercase shadow-xl shadow-purple-200 transition-all transform hover:scale-[1.02] active:scale-95 flex items-center justify-center gap-3">
                    <span>Guardar Cambios</span>
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
    $(function() {
        lucide.createIcons();

        $("#establecimiento_search").autocomplete({
            minLength: 2,
            source: "{{ route('establecimientos.buscar') }}",
            select: function(event, ui) {
                event.preventDefault();
                $("#establecimiento_search").val(''); 
                $("#establecimiento_id").val(ui.item.id);
                $("#establecimiento_nombre").text(ui.item.value);
                $("#establecimiento_seleccionado").removeClass('hidden').addClass('flex');
                $("#establecimiento_search_wrapper").addClass('hidden');
                lucide.createIcons(); // Recrear iconos después de cambiar
                return false;
            }
        });

        $("#btn_clear_estab").on('click', function() {
            $("#establecimiento_id").val('');
            $("#establecimiento_seleccionado").addClass('hidden').removeClass('flex');
            $("#establecimiento_search_wrapper").removeClass('hidden');
            $("#establecimiento_search").focus();
        });
    });
</script>
@endpush
