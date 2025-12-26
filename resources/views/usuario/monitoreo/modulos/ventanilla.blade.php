@extends('layouts.usuario')

@section('title', 'Ventanilla Única - ' . $acta->establecimiento->nombre)

@push('styles')
    <style>
        .check-card {
            transition: all 0.2s;
            border: 2px solid #f1f5f9;
            cursor: pointer;
        }
        .check-card:hover { border-color: #e2e8f0; background: white; }
        .check-card input:checked + div { color: #4f46e5; }
        
        .table-produccion input {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            width: 100%;
            text-align: center;
            font-weight: 800;
            border-radius: 0.75rem;
            padding: 0.5rem;
        }
    </style>
@endpush

@section('content')
<div class="py-10 bg-slate-50 min-h-screen">
    <div class="max-w-5xl mx-auto px-4">
        
        {{-- NAVEGACIÓN --}}
        <a href="{{ route('usuario.monitoreo.modulos', $acta->id) }}" class="inline-flex items-center gap-2 text-slate-400 font-black text-[10px] uppercase tracking-[0.2em] mb-6 hover:text-indigo-600 transition-all group">
            <i data-lucide="chevron-left" class="w-4 h-4 transition-transform group-hover:-translate-x-1"></i>
            Volver al Panel de Módulos
        </a>

        <div class="bg-white border border-slate-200 rounded-[2.5rem] shadow-sm overflow-hidden">
            
            {{-- HEADER DEL MÓDULO --}}
            <div class="bg-slate-900 p-10 text-white flex justify-between items-center">
                <div>
                    <h3 class="text-2xl font-black uppercase italic tracking-tight">Ventanilla Única</h3>
                    <p class="text-indigo-400 text-xs font-bold uppercase tracking-widest mt-2 flex items-center gap-2">
                        <i data-lucide="monitor" class="w-4 h-4"></i> Evaluación de Citas y Atención al Usuario
                    </p>
                </div>
                <div class="bg-white/10 px-6 py-3 rounded-2xl border border-white/10 text-right">
                    <label class="block text-[9px] font-black text-indigo-300 uppercase mb-1">Nro. de Ventanillas</label>
                    <input type="number" name="contenido[nro_ventanillas]" class="bg-transparent border-none text-xl font-black p-0 focus:ring-0 text-right w-16" placeholder="00">
                </div>
            </div>

            <form action="{{ route('usuario.monitoreo.guardarDetalle', $acta->id) }}" method="POST" class="p-10 space-y-12">
                @csrf
                <input type="hidden" name="modulo_nombre" value="ventanilla">

                {{-- 1. DATOS DEL PERSONAL Y CAPACITACIÓN --}}
                <div class="space-y-6">
                    <div class="flex items-center gap-3 border-b border-slate-100 pb-4">
                        <span class="h-8 w-8 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center text-xs font-black">01</span>
                        <h4 class="text-xs font-black text-slate-700 uppercase tracking-widest">Información del Responsable de Admisión</h4>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
                        <div class="md:col-span-6 space-y-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Apellidos y Nombres:</label>
                            <input type="text" name="contenido[personal][nombre]" class="w-full bg-slate-50 border-none rounded-2xl p-4 text-sm font-bold focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div class="md:col-span-2 space-y-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 text-center block">DNI:</label>
                            <input type="text" name="contenido[personal][dni]" maxlength="8" class="w-full bg-slate-50 border-none rounded-2xl p-4 text-sm font-bold text-center">
                        </div>
                        <div class="md:col-span-2 space-y-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 text-center block">Turno:</label>
                            <input type="text" name="contenido[personal][turno]" class="w-full bg-slate-50 border-none rounded-2xl p-4 text-sm font-bold text-center uppercase">
                        </div>
                        <div class="md:col-span-2 space-y-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 text-center block">Rol:</label>
                            <input type="text" name="contenido[personal][rol]" class="w-full bg-slate-50 border-none rounded-2xl p-4 text-sm font-bold text-center uppercase">
                        </div>
                    </div>

                    <div class="bg-slate-50 p-6 rounded-[2rem] border border-slate-100 flex flex-col md:flex-row gap-8 items-center">
                        <div class="flex items-center gap-6">
                            <p class="text-[11px] font-black text-slate-500 uppercase italic">¿Recibió capacitación?</p>
                            <div class="flex gap-4">
                                <label class="flex items-center gap-2 cursor-pointer font-black text-xs text-slate-400"><input type="radio" name="contenido[capacitacion][recibio]" value="SI" class="text-indigo-600"> SÍ</label>
                                <label class="flex items-center gap-2 cursor-pointer font-black text-xs text-slate-400"><input type="radio" name="contenido[capacitacion][recibio]" value="NO" class="text-indigo-600"> NO</label>
                            </div>
                        </div>
                        <div class="flex-1 border-t md:border-t-0 md:border-l border-slate-200 pt-4 md:pt-0 md:pl-8">
                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-3">De parte de quién:</p>
                            <div class="flex gap-6">
                                @foreach(['MINSA', 'DIRIS / DIRESA', 'Otros'] as $ente)
                                <label class="flex items-center gap-2 text-[10px] font-bold text-slate-600">
                                    <input type="checkbox" name="contenido[capacitacion][ente][]" value="{{ $ente }}" class="rounded text-indigo-600"> {{ $ente }}
                                </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 2. INSUMOS Y EQUIPAMIENTO --}}
                <div class="space-y-6">
                    <div class="flex items-center gap-3 border-b border-slate-100 pb-4">
                        <span class="h-8 w-8 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center text-xs font-black">02</span>
                        <h4 class="text-xs font-black text-slate-700 uppercase tracking-widest">Insumos y Equipamiento</h4>
                    </div>

                    <div class="space-y-4">
                        <p class="text-[10px] font-black text-slate-400 uppercase ml-1">Al iniciar sus labores diarias cuenta con:</p>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            @foreach(['Papel para TICKET', 'FUA', 'HOJA DE FILIACIÓN'] as $insumo)
                            <label class="check-card p-4 rounded-2xl bg-slate-50 flex items-center justify-between group">
                                <input type="checkbox" name="contenido[insumos][]" value="{{ $insumo }}" class="hidden">
                                <span class="text-[10px] font-black text-slate-500 uppercase">{{ $insumo }}</span>
                                <div class="h-6 w-6 rounded-lg bg-white border border-slate-200 flex items-center justify-center text-indigo-500 opacity-0 group-hover:opacity-100"><i data-lucide="check" class="w-3 h-3"></i></div>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="space-y-4 pt-4">
                        <p class="text-[10px] font-black text-slate-400 uppercase ml-1">Checklist de Equipamiento:</p>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                            @foreach(['Monitor', 'CPU', 'Teclado', 'Mouse', 'Impresora', 'Ticketera', 'Lectora Código Barra', 'Lector Tarjeta Inteligente'] as $equipo)
                            <label class="flex items-center justify-between p-3 px-4 bg-white border border-slate-100 rounded-xl hover:bg-slate-50 transition-all cursor-pointer">
                                <span class="text-[10px] font-bold text-slate-600">{{ $equipo }}</span>
                                <input type="checkbox" name="contenido[equipamiento][]" value="{{ $equipo }}" class="rounded text-indigo-600">
                            </label>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- 3. PRODUCCIÓN (CITAS OTORGADAS) --}}
                <div class="space-y-6">
                    <div class="flex items-center gap-3 border-b border-slate-100 pb-4">
                        <span class="h-8 w-8 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center text-xs font-black">03</span>
                        <h4 class="text-xs font-black text-slate-700 uppercase tracking-widest">Citas Otorgadas en el Mes</h4>
                    </div>

                    <div class="max-w-xl mx-auto overflow-hidden rounded-[2rem] border border-slate-200 shadow-sm">
                        <table class="w-full text-xs border-collapse table-produccion">
                            <thead class="bg-slate-800 text-white text-[9px] font-black uppercase">
                                <tr>
                                    <th class="p-4 text-left border-r border-slate-700">Cartera de Servicio</th>
                                    <th class="p-4 text-center">Citas Otorgadas</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach(['CRED', 'OBSTETRICIA', 'MEDICINA'] as $serv)
                                <tr>
                                    <td class="p-5 font-black text-slate-700 bg-slate-50/50 italic">{{ $serv }}</td>
                                    <td class="p-3"><input type="number" name="contenido[produccion][{{$serv}}]" placeholder="0"></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- 4. CALIDAD Y SOPORTE --}}
                <div class="space-y-6">
                    <div class="flex items-center gap-3 border-b border-slate-100 pb-4">
                        <span class="h-8 w-8 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center text-xs font-black">04</span>
                        <h4 class="text-xs font-black text-slate-700 uppercase tracking-widest">Evaluación del Sistema y Soporte</h4>
                    </div>

                    <div class="grid grid-cols-1 gap-4">
                        @foreach([
                            'disminuir_espera' => '¿El sistema cumple con disminuir el tiempo de espera del paciente?',
                            'paciente_satisfecho' => '¿Con el otorgamiento de las citas el paciente se muestra satisfecho?',
                            'usa_reportes' => '¿Utiliza reportes del sistema?'
                        ] as $slug => $pregunta)
                        <div class="flex items-center justify-between p-6 bg-slate-50 rounded-[1.5rem] border border-slate-100">
                            <p class="text-xs font-bold text-slate-600 pr-10">{{ $pregunta }}</p>
                            <div class="flex gap-4">
                                <label class="flex items-center gap-2 font-black text-[10px] text-slate-400"><input type="radio" name="contenido[calidad][{{$slug}}]" value="SI"> SÍ</label>
                                <label class="flex items-center gap-2 font-black text-[10px] text-slate-400"><input type="radio" name="contenido[calidad][{{$slug}}]" value="NO"> NO</label>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    {{-- Soporte ante dificultades --}}
                    <div class="p-8 bg-indigo-50/30 rounded-[2.5rem] border border-indigo-100 border-dashed space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-3">
                                <label class="text-[10px] font-black text-indigo-700 uppercase ml-1 italic">¿A quién comunica alguna dificultad?</label>
                                <div class="flex gap-4">
                                    @foreach(['MINSA', 'DIRIS/DIRESA', 'EESS'] as $com)
                                    <label class="flex items-center gap-2 text-[10px] font-bold text-slate-500 uppercase"><input type="radio" name="contenido[soporte][comunica]" value="{{$com}}"> {{$com}}</label>
                                    @endforeach
                                </div>
                            </div>
                            <div class="space-y-3">
                                <label class="text-[10px] font-black text-indigo-700 uppercase ml-1 italic">¿Qué medio utiliza?</label>
                                <div class="flex gap-4">
                                    @foreach(['WhatsApp', 'Teléfono', 'Email'] as $medio)
                                    <label class="flex items-center gap-2 text-[10px] font-bold text-slate-500 uppercase"><input type="radio" name="contenido[soporte][medio]" value="{{$medio}}"> {{$medio}}</label>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pt-10 flex flex-col gap-4">
                    <button type="submit" class="w-full bg-slate-900 text-white py-6 rounded-[2rem] font-black text-sm shadow-xl shadow-slate-200 hover:bg-black transition-all flex items-center justify-center gap-4 active:scale-[0.98]">
                        <i data-lucide="check-circle" class="w-6 h-6 text-indigo-400"></i>
                        GUARDAR EVALUACIÓN DE VENTANILLA ÚNICA
                    </button>
                    <p class="text-[10px] text-center text-slate-400 font-bold uppercase tracking-widest italic">Los datos del responsable de admisión se capturarán para la firma final del PDF</p>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        lucide.createIcons();
    });
</script>
@endpush