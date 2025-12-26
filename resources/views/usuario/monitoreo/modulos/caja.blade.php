@extends('layouts.usuario')

@section('title', 'Módulo de Caja - ' . $acta->establecimiento->nombre)

@push('styles')
    <style>
        .check-card {
            transition: all 0.2s;
            border: 2px solid #f1f5f9;
            cursor: pointer;
        }
        .check-card:hover { border-color: #e2e8f0; background: white; }
        .check-card input:checked + div { color: #10b981; border-color: #10b981; }
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
                    <h3 class="text-2xl font-black uppercase italic tracking-tight">Módulo de Caja</h3>
                    <p class="text-emerald-400 text-xs font-bold uppercase tracking-widest mt-2 flex items-center gap-2">
                        <i data-lucide="banknote" class="w-4 h-4"></i> Monitoreo de Recaudación y Equipamiento
                    </p>
                </div>
                <div class="bg-white/10 px-6 py-3 rounded-2xl border border-white/10 text-right">
                    <label class="block text-[9px] font-black text-emerald-300 uppercase mb-1">Nro. de Cajas</label>
                    <input type="number" name="contenido[nro_cajas]" class="bg-transparent border-none text-xl font-black p-0 focus:ring-0 text-right w-16" placeholder="0" value="{{ $acta->detalles->where('modulo_nombre', 'caja')->first()->contenido['nro_cajas'] ?? '' }}">
                </div>
            </div>

            <form action="{{ route('usuario.monitoreo.guardarDetalle', $acta->id) }}" method="POST" class="p-10 space-y-12">
                @csrf
                <input type="hidden" name="modulo_nombre" value="caja">

                {{-- 1. EQUIPO DE TRABAJO --}}
                <div class="space-y-6">
                    <div class="flex items-center gap-3 border-b border-slate-100 pb-4">
                        <span class="h-8 w-8 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center text-xs font-black">01</span>
                        <h4 class="text-xs font-black text-slate-700 uppercase tracking-widest">Equipo de Trabajo y Capacitación</h4>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
                        <div class="md:col-span-6 space-y-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Apellidos y Nombres:</label>
                            <input type="text" name="contenido[personal][nombre]" class="w-full bg-slate-50 border-none rounded-2xl p-4 text-sm font-bold focus:ring-2 focus:ring-emerald-500">
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
                            <p class="text-[11px] font-black text-slate-500 uppercase italic">¿Recibieron capacitación?</p>
                            <div class="flex gap-4">
                                <label class="flex items-center gap-2 cursor-pointer font-black text-xs text-slate-400"><input type="radio" name="contenido[capacitacion][recibio]" value="SI" class="text-emerald-600"> SÍ</label>
                                <label class="flex items-center gap-2 cursor-pointer font-black text-xs text-slate-400"><input type="radio" name="contenido[capacitacion][recibio]" value="NO" class="text-emerald-600"> NO</label>
                            </div>
                        </div>
                        <div class="flex-1 border-t md:border-t-0 md:border-l border-slate-200 pt-4 md:pt-0 md:pl-8">
                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-3">De parte de quien:</p>
                            <div class="flex gap-6">
                                @foreach(['MINSA', 'DIRIS / DIRESA', 'Otros'] as $ente)
                                <label class="flex items-center gap-2 text-[10px] font-bold text-slate-600">
                                    <input type="checkbox" name="contenido[capacitacion][ente][]" value="{{ $ente }}" class="rounded text-emerald-600"> {{ $ente }}
                                </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 2. EQUIPAMIENTO --}}
                <div class="space-y-6">
                    <div class="flex items-center gap-3 border-b border-slate-100 pb-4">
                        <span class="h-8 w-8 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center text-xs font-black">02</span>
                        <h4 class="text-xs font-black text-slate-700 uppercase tracking-widest">Equipamiento Técnico</h4>
                    </div>

                    <div class="flex items-center justify-between p-6 bg-emerald-50/50 rounded-[1.5rem] border border-emerald-100">
                        <p class="text-xs font-bold text-emerald-800 uppercase tracking-tight italic">¿Al iniciar sus labores cuenta con equipo de cómputo operativo?</p>
                        <div class="flex gap-4">
                            <label class="flex items-center gap-2 font-black text-xs text-slate-500"><input type="radio" name="contenido[computo_operativo]" value="SI"> SÍ</label>
                            <label class="flex items-center gap-2 font-black text-xs text-slate-500"><input type="radio" name="contenido[computo_operativo]" value="NO"> NO</label>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        @foreach(['Monitor', 'CPU', 'Teclado', 'Mouse', 'Impresora'] as $equipo)
                        <label class="flex items-center justify-between p-4 bg-white border border-slate-200 rounded-2xl hover:bg-slate-50 transition-all cursor-pointer shadow-sm">
                            <span class="text-[10px] font-bold text-slate-600 uppercase">{{ $equipo }}</span>
                            <input type="checkbox" name="contenido[equipamiento][]" value="{{ $equipo }}" class="rounded text-emerald-600 focus:ring-emerald-500">
                        </label>
                        @endforeach
                        <div class="col-span-2 md:col-span-3">
                            <input type="text" name="contenido[equipamiento_otros]" placeholder="Otros equipos..." class="w-full bg-slate-50 border-none rounded-2xl p-4 text-xs font-bold">
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Comentario del Estado y/o dificultades:</label>
                        <textarea name="contenido[comentarios_estado]" rows="3" class="w-full bg-slate-50 border-none rounded-[1.5rem] p-6 text-sm font-medium focus:ring-2 focus:ring-emerald-500 shadow-inner" placeholder="Describa el estado de los equipos o dificultades encontradas..."></textarea>
                    </div>
                </div>

                {{-- 3. SOPORTE TÉCNICO --}}
                <div class="space-y-6">
                    <div class="flex items-center gap-3 border-b border-slate-100 pb-4">
                        <span class="h-8 w-8 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center text-xs font-black">03</span>
                        <h4 class="text-xs font-black text-slate-700 uppercase tracking-widest">Soporte ante Dificultades</h4>
                    </div>

                    <div class="p-8 bg-slate-900 rounded-[2.5rem] shadow-xl space-y-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                            <div class="space-y-4">
                                <label class="text-[10px] font-black text-emerald-400 uppercase tracking-[0.2em] block ml-1">¿A quién le comunica?</label>
                                <div class="flex flex-wrap gap-4">
                                    @foreach(['MINSA', 'DIRIS / DIRESA', 'EESS'] as $com)
                                    <label class="flex items-center gap-3 text-xs font-bold text-white/70 hover:text-white cursor-pointer transition-colors group">
                                        <input type="radio" name="contenido[soporte][comunica]" value="{{$com}}" class="w-4 h-4 bg-white/10 border-white/20 text-emerald-500 focus:ring-emerald-500">
                                        {{$com}}
                                    </label>
                                    @endforeach
                                </div>
                            </div>
                            <div class="space-y-4">
                                <label class="text-[10px] font-black text-emerald-400 uppercase tracking-[0.2em] block ml-1">¿Qué medio utiliza?</label>
                                <div class="flex flex-wrap gap-4">
                                    @foreach(['WhatsApp', 'Teléfono', 'Email'] as $medio)
                                    <label class="flex items-center gap-3 text-xs font-bold text-white/70 hover:text-white cursor-pointer transition-colors group">
                                        <input type="radio" name="contenido[soporte][medio]" value="{{$medio}}" class="w-4 h-4 bg-white/10 border-white/20 text-emerald-500 focus:ring-emerald-500">
                                        {{$medio}}
                                    </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- CIERRE Y FIRMA --}}
                <div class="pt-10 space-y-8">
                    <div class="max-w-md mx-auto p-6 border-t-2 border-dashed border-slate-200 text-center">
                        <p class="text-[10px] font-black text-slate-400 uppercase mb-4 tracking-widest">Firma del Responsable</p>
                        <div class="grid grid-cols-1 gap-4">
                            <input type="text" name="contenido[firma][nombre]" placeholder="Apellidos y Nombres Responsable CAJA" class="w-full bg-slate-50 border-none rounded-xl p-4 text-xs font-bold text-center uppercase tracking-tighter">
                            <div class="grid grid-cols-2 gap-4">
                                <input type="text" name="contenido[firma][dni]" placeholder="DNI" class="w-full bg-slate-50 border-none rounded-xl p-4 text-xs font-bold text-center">
                                <input type="text" name="contenido[firma][telf]" placeholder="TELF." class="w-full bg-slate-50 border-none rounded-xl p-4 text-xs font-bold text-center">
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-emerald-600 text-white py-6 rounded-[2rem] font-black text-sm shadow-xl shadow-emerald-100 hover:bg-emerald-700 transition-all flex items-center justify-center gap-4 active:scale-[0.98]">
                        <i data-lucide="check-circle" class="w-6 h-6"></i>
                        GUARDAR EVALUACIÓN DE CAJA
                    </button>
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