@extends('layouts.usuario')

@section('title', 'Programación de Consultorios - ' . $acta->establecimiento->nombre)

@push('styles')
    <style>
        .input-turno {
            background: transparent;
            border: none;
            width: 100%;
            text-align: center;
            font-weight: 700;
            color: #1e293b;
            padding: 0.75rem 0.25rem;
        }
        .input-turno:focus { outline: none; background: #f8fafc; }
        .cell-highlight { background-color: #f8fafc; border-left: 1px solid #e2e8f0; border-right: 1px solid #e2e8f0; }
    </style>
@endpush

@section('content')
<div class="py-10 bg-slate-50 min-h-screen">
    <div class="max-w-5xl mx-auto px-4">
        
        {{-- BOTÓN REGRESAR AL PANEL --}}
        <a href="{{ route('usuario.monitoreo.modulos', $acta->id) }}" class="inline-flex items-center gap-2 text-slate-400 font-black text-[10px] uppercase tracking-[0.2em] mb-6 hover:text-indigo-600 transition-all group">
            <i data-lucide="chevron-left" class="w-4 h-4 transition-transform group-hover:-translate-x-1"></i>
            Volver al Panel de Módulos
        </a>

        <div class="bg-white border border-slate-200 rounded-[2.5rem] shadow-sm overflow-hidden">
            {{-- ENCABEZADO DEL MÓDULO --}}
            <div class="bg-slate-900 p-10 text-white relative">
                <div class="absolute top-0 right-0 p-10 opacity-10">
                    <i data-lucide="calendar-days" class="w-32 h-32"></i>
                </div>
                <div class="relative z-10">
                    <h3 class="text-2xl font-black uppercase italic tracking-tight">Programación de Consultorios y Turnos</h3>
                    <p class="text-indigo-400 text-xs font-bold uppercase tracking-widest mt-2 flex items-center gap-2">
                        <i data-lucide="info" class="w-4 h-4"></i> Información General del Establecimiento
                    </p>
                </div>
            </div>

            <form action="{{ route('usuario.monitoreo.guardarDetalle', $acta->id) }}" method="POST" class="p-10 space-y-12">
                @csrf
                <input type="hidden" name="modulo_nombre" value="programacion">

                {{-- SECCIÓN 1: RECURSOS HUMANOS --}}
                <div class="space-y-6">
                    <div class="flex items-center gap-3 border-b border-slate-100 pb-4">
                        <span class="h-8 w-8 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center text-xs font-black">01</span>
                        <h4 class="text-xs font-black text-slate-700 uppercase tracking-widest">Personal Responsable de RRHH</h4>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Apellidos y Nombres:</label>
                            <input type="text" name="contenido[rrhh_nombre]" placeholder="Nombre completo del responsable" class="w-full bg-slate-50 border-none rounded-2xl p-4 text-sm font-bold focus:ring-2 focus:ring-indigo-500 transition-all" required>
                        </div>
                        <div class="space-y-2 text-center">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1 block">¿Cuenta con Usuario ODOO?</label>
                            <div class="flex justify-center gap-6 bg-slate-50 p-3 rounded-2xl border border-slate-100">
                                <label class="flex items-center gap-2 cursor-pointer group">
                                    <input type="radio" name="contenido[odoo]" value="SI" class="w-4 h-4 text-indigo-600 border-slate-300 focus:ring-indigo-500">
                                    <span class="text-xs font-black text-slate-400 group-hover:text-slate-700 uppercase">SI</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer group">
                                    <input type="radio" name="contenido[odoo]" value="NO" class="w-4 h-4 text-indigo-600 border-slate-300 focus:ring-indigo-500">
                                    <span class="text-xs font-black text-slate-400 group-hover:text-slate-700 uppercase">NO</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="p-8 bg-indigo-50/30 rounded-[2rem] border border-indigo-100 border-dashed space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-[9px] font-black text-indigo-700 uppercase tracking-widest italic ml-1 flex items-center gap-2">
                                    <i data-lucide="help-circle" class="w-3 h-3"></i> Si respondió NO: ¿Quién programa?
                                </label>
                                <input type="text" name="contenido[quien_programa]" placeholder="Apellidos y Nombres" class="w-full bg-white border-none rounded-xl p-4 text-sm font-medium shadow-sm focus:ring-2 focus:ring-indigo-500">
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="space-y-2">
                                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-1">Capacitación</label>
                                    <select name="contenido[capacitacion]" class="w-full bg-white border-none rounded-xl p-4 text-xs font-bold shadow-sm focus:ring-2 focus:ring-indigo-500">
                                        <option value="SI">SÍ TIENE</option>
                                        <option value="NO">NO TIENE</option>
                                    </select>
                                </div>
                                <div class="space-y-2">
                                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-1">Mes Programado</label>
                                    <input type="month" name="contenido[mes_sistema]" class="w-full bg-white border-none rounded-xl p-4 text-xs font-bold shadow-sm focus:ring-2 focus:ring-indigo-500">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- SECCIÓN 2: CARTERA DE SERVICIOS --}}
                <div class="space-y-6">
                    <div class="flex items-center gap-3 border-b border-slate-100 pb-4">
                        <span class="h-8 w-8 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center text-xs font-black">02</span>
                        <h4 class="text-xs font-black text-slate-700 uppercase tracking-widest">Cartera de Servicios, Turnos y Cupos</h4>
                    </div>

                    <div class="overflow-hidden rounded-[2rem] border border-slate-200 shadow-sm">
                        <table class="w-full border-collapse">
                            <thead class="bg-slate-800 text-white text-[10px] font-black uppercase tracking-widest">
                                <tr>
                                    <th rowspan="2" class="p-5 text-left border-r border-slate-700">Cartera de Servicios</th>
                                    <th colspan="2" class="p-3 text-center border-r border-slate-700 bg-slate-700">Mes 1</th>
                                    <th colspan="2" class="p-3 text-center border-r border-slate-700 bg-slate-700">Mes 2</th>
                                    <th colspan="2" class="p-3 text-center bg-slate-700">Mes 3</th>
                                </tr>
                                <tr class="bg-slate-700 text-[9px] border-t border-slate-600">
                                    <th class="p-2 text-center border-r border-slate-600">Turnos</th>
                                    <th class="p-2 text-center border-r border-slate-600">Cupos</th>
                                    <th class="p-2 text-center border-r border-slate-600">Turnos</th>
                                    <th class="p-2 text-center border-r border-slate-600">Cupos</th>
                                    <th class="p-2 text-center border-r border-slate-600">Turnos</th>
                                    <th class="p-2 text-center">Cupos</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach(['CRED', 'MEDICINA', 'OBSTETRICIA', 'LABORATORIO', 'IMAGENES'] as $servicio)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="p-5 text-xs font-black text-slate-700 border-r border-slate-100">{{ $servicio }}</td>
                                    <td class="cell-highlight"><input type="number" name="contenido[servicios][{{$servicio}}][t1]" class="input-turno"></td>
                                    <td class="border-r border-slate-100"><input type="number" name="contenido[servicios][{{$servicio}}][c1]" class="input-turno"></td>
                                    <td class="cell-highlight"><input type="number" name="contenido[servicios][{{$servicio}}][t2]" class="input-turno"></td>
                                    <td class="border-r border-slate-100"><input type="number" name="contenido[servicios][{{$servicio}}][c2]" class="input-turno"></td>
                                    <td class="cell-highlight"><input type="number" name="contenido[servicios][{{$servicio}}][t3]" class="input-turno"></td>
                                    <td><input type="number" name="contenido[servicios][{{$servicio}}][c3]" class="input-turno"></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- SECCIÓN 3: CIERRE DEL MÓDULO --}}
                <div class="space-y-6">
                    <div class="flex items-center gap-3 border-b border-slate-100 pb-4">
                        <span class="h-8 w-8 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center text-xs font-black">03</span>
                        <h4 class="text-xs font-black text-slate-700 uppercase tracking-widest">Comentarios y Firma del Entrevistado</h4>
                    </div>

                    <div class="space-y-4">
                        <textarea name="contenido[comentarios]" rows="3" placeholder="Comentarios del usuario y/o entrevistado..." class="w-full bg-slate-50 border-none rounded-[1.5rem] p-6 text-sm font-medium focus:ring-2 focus:ring-indigo-500"></textarea>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4">
                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Nombre del Entrevistado:</label>
                                <input type="text" name="contenido[entrevistado][nombre]" class="w-full bg-slate-50 border-none rounded-xl p-4 text-xs font-bold">
                            </div>
                            <div class="grid grid-cols-3 gap-2">
                                <div class="col-span-1 space-y-2">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">DNI:</label>
                                    <input type="text" name="contenido[entrevistado][dni]" maxlength="8" class="w-full bg-slate-50 border-none rounded-xl p-4 text-xs font-bold">
                                </div>
                                <div class="col-span-2 space-y-2">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Teléfono / Correo:</label>
                                    <input type="text" name="contenido[entrevistado][contacto]" class="w-full bg-slate-50 border-none rounded-xl p-4 text-xs font-bold">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pt-10 flex flex-col gap-4">
                    <button type="submit" class="w-full bg-indigo-600 text-white py-6 rounded-[2rem] font-black text-sm shadow-xl shadow-indigo-100 hover:bg-indigo-700 transition-all flex items-center justify-center gap-4 active:scale-[0.98]">
                        <i data-lucide="check-circle" class="w-6 h-6"></i>
                        FINALIZAR Y GUARDAR ESTE MÓDULO
                    </button>
                    <p class="text-[10px] text-center text-slate-400 font-bold uppercase tracking-widest">Al guardar, volverá automáticamente al panel principal de módulos</p>
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