@extends('layouts.usuario')

@section('title', 'Evaluación de Programación - ' . $acta->establecimiento->nombre)

@push('styles')
    <style>
        .technical-input {
            border: none;
            border-bottom: 1px solid #e2e8f0;
            background-color: #f8fafc;
            border-radius: 0.25rem 0.25rem 0 0;
            padding: 0.6rem 0.5rem;
            width: 100%;
            font-size: 0.875rem;
            font-weight: 700;
            color: #1e293b;
            transition: all 0.2s;
        }
        .technical-input:focus {
            outline: none;
            border-bottom: 2px solid #4f46e5;
            background-color: #fff;
        }
        .technical-label {
            display: block;
            font-size: 10px;
            font-weight: 800;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.25rem;
        }
        .radio-custom { display: none; }
        .radio-label {
            padding: 0.4rem 1rem;
            border: 1px solid #e2e8f0;
            border-radius: 0.5rem;
            font-size: 11px;
            font-weight: 800;
            cursor: pointer;
            transition: all 0.2s;
            color: #94a3b8;
            text-align: center;
        }
        .radio-custom:checked + .radio-label-si { background-color: #10b981; color: white; border-color: #10b981; }
        .radio-custom:checked + .radio-label-no { background-color: #ef4444; color: white; border-color: #ef4444; }

        /* Estilo para sección bloqueada */
        .section-disabled {
            opacity: 0.4;
            pointer-events: none;
            filter: grayscale(1);
            transition: all 0.4s ease;
        }
    </style>
@endpush

@section('content')
<div class="py-12 bg-slate-100 min-h-screen">
    <div class="max-w-4xl mx-auto px-6">
        
        <div class="flex justify-between items-center mb-8">
            <a href="{{ route('usuario.monitoreo.modulos', $acta->id) }}" class="flex items-center gap-2 text-slate-500 hover:text-indigo-600 transition-colors group">
                <i data-lucide="arrow-left" class="w-4 h-4 group-hover:-translate-x-1 transition-transform"></i>
                <span class="text-[10px] font-black uppercase tracking-widest">Panel de Control</span>
            </a>
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Formato de Auditoría Técnica</span>
        </div>

        <div class="bg-white shadow-2xl rounded-3xl overflow-hidden border border-slate-200">
            
            <div class="bg-white border-b border-slate-100 p-10 flex justify-between items-start">
                <div class="space-y-1">
                    <h1 class="text-2xl font-black text-slate-900 tracking-tighter uppercase italic">Programación de Consultorios</h1>
                    <div class="flex items-center gap-2 text-indigo-600 font-bold text-[10px] uppercase tracking-widest">
                        <i data-lucide="file-text" class="w-3 h-3"></i> Módulo de Gestión Administrativa
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Establecimiento</p>
                    <p class="text-sm font-black text-slate-800 uppercase">{{ $acta->establecimiento->nombre }}</p>
                </div>
            </div>

            <form action="{{ route('usuario.monitoreo.guardarDetalle', $acta->id) }}" method="POST" class="p-10 space-y-12">
                @csrf
                <input type="hidden" name="modulo_nombre" value="programacion">

                {{-- BLOQUE 1: RESPONSABLE RRHH --}}
                <div class="space-y-8">
                    <div class="flex items-center gap-3">
                        <div class="h-1 w-8 bg-indigo-600 rounded-full"></div>
                        <p class="text-xs font-black text-slate-800 uppercase tracking-wider">I. Información del Personal Responsable</p>
                    </div>

                    <div class="grid grid-cols-1 gap-8 ml-4">
                        <div class="flex flex-col">
                            <label class="technical-label">Apellidos y Nombres</label>
                            <input type="text" id="responsable_nombre" name="contenido[rrhh_nombre]" value="{{ $acta->programacion->rrhh_nombre ?? '' }}" class="technical-input" placeholder="Nombre Completo" required>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label class="technical-label">DNI</label>
                                <input type="text" name="contenido[rrhh_dni]" value="{{ $acta->programacion->rrhh_dni ?? '' }}" class="technical-input" maxlength="8">
                            </div>
                            <div>
                                <label class="technical-label">Teléfono</label>
                                <input type="text" name="contenido[rrhh_telefono]" value="{{ $acta->programacion->rrhh_telefono ?? '' }}" class="technical-input">
                            </div>
                            <div>
                                <label class="technical-label">Correo Electrónico</label>
                                <input type="email" name="contenido[rrhh_correo]" value="{{ $acta->programacion->rrhh_correo ?? '' }}" class="technical-input">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- BLOQUE 2: ACCESO SISTEMA (DISPARADOR) --}}
                <div class="bg-slate-50 rounded-[2rem] p-8 space-y-8 border border-slate-100">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <p class="text-sm font-bold text-slate-700">¿Cuenta con Usuario y contraseña en el módulo de Gestión Administrativa?</p>
                        <div class="flex gap-2">
                            @php $odoo = $acta->programacion->odoo ?? ''; @endphp
                            <input type="radio" id="odoo_si" name="contenido[odoo]" value="SI" class="radio-custom toggle-section" {{ $odoo == 'SI' ? 'checked' : '' }}>
                            <label for="odoo_si" class="radio-label radio-label-si">SÍ CUENTA</label>
                            
                            <input type="radio" id="odoo_no" name="contenido[odoo]" value="NO" class="radio-custom toggle-section" {{ $odoo == 'NO' ? 'checked' : '' }}>
                            <label for="odoo_no" class="radio-label radio-label-no">NO CUENTA</label>
                        </div>
                    </div>

                    {{-- ESTA SECCIÓN SE BLOQUEARÁ --}}
                    <div id="section_quien_programa" class="space-y-6 pt-6 border-t border-slate-200 transition-all duration-500">
                        <p class="text-[10px] font-black text-indigo-600 uppercase tracking-widest">Si la respuesta es NO ¿Quién programa?:</p>
                        <div class="ml-4 space-y-6">
                            <div class="flex flex-col">
                                <label class="technical-label">Apellidos y Nombres</label>
                                <input type="text" name="contenido[quien_programa_nombre]" value="{{ $acta->programacion->quien_programa_nombre ?? '' }}" class="technical-input input-to-disable">
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div><label class="technical-label">DNI</label><input type="text" name="contenido[quien_programa_dni]" value="{{ $acta->programacion->quien_programa_dni ?? '' }}" class="technical-input input-to-disable"></div>
                                <div><label class="technical-label">Teléfono</label><input type="text" name="contenido[quien_programa_telefono]" value="{{ $acta->programacion->quien_programa_telefono ?? '' }}" class="technical-input input-to-disable"></div>
                                <div><label class="technical-label">Correo</label><input type="text" name="contenido[quien_programa_correo]" value="{{ $acta->programacion->quien_programa_correo ?? '' }}" class="technical-input input-to-disable"></div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- BLOQUE 3: OTROS --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-10 items-end px-4">
                    <div class="space-y-4">
                        <label class="technical-label">¿Capacitación en Gestión Administrativa?</label>
                        <div class="flex gap-2">
                            @php $cap = $acta->programacion->capacitacion ?? ''; @endphp
                            <input type="radio" id="cap_si" name="contenido[capacitacion]" value="SI" class="radio-custom" {{ $cap == 'SI' ? 'checked' : '' }}>
                            <label for="cap_si" class="radio-label radio-label-si w-full">SÍ</label>
                            
                            <input type="radio" id="cap_no" name="contenido[capacitacion]" value="NO" class="radio-custom" {{ $cap == 'NO' ? 'checked' : '' }}>
                            <label for="cap_no" class="radio-label radio-label-no w-full">NO</label>
                        </div>
                    </div>
                    <div>
                        <label class="technical-label">Mes de Programación en el Sistema</label>
                        <input type="month" name="contenido[mes_sistema]" value="{{ $acta->programacion->mes_sistema ?? '' }}" class="technical-input">
                    </div>
                </div>

                <div class="space-y-4 pt-4">
                    <label class="technical-label">Comentarios del Usuario y/o Entrevistado</label>
                    <textarea name="contenido[comentarios]" rows="4" class="w-full bg-slate-50 border border-slate-200 rounded-2xl p-6 text-sm font-medium focus:ring-2 focus:ring-indigo-500 outline-none transition-all shadow-inner">{{ $acta->programacion->comentarios ?? '' }}</textarea>
                </div>


                <button type="submit" class="w-full bg-slate-900 text-white py-6 rounded-2xl font-black text-[11px] uppercase tracking-[0.3em] hover:bg-indigo-600 transition-all shadow-xl flex items-center justify-center gap-4 group">
                    <i data-lucide="save" class="w-5 h-5 group-hover:scale-110 transition-transform"></i>
                    Finalizar y Registrar Información
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        lucide.createIcons();

        const inputNombre = document.getElementById('responsable_nombre');
        const firmaVisual = document.getElementById('firma_visual');
        const radioButtons = document.querySelectorAll('.toggle-section');
        const sectionToToggle = document.getElementById('section_quien_programa');
        const inputsToDisable = document.querySelectorAll('.input-to-disable');

        // Sincronización de Firma
        inputNombre.addEventListener('input', (e) => {
            firmaVisual.textContent = e.target.value ? e.target.value.toUpperCase() : "..........................................................";
        });

        // Lógica de Bloqueo de Sección
        const handleSectionToggle = () => {
            const isOdooSi = document.getElementById('odoo_si').checked;
            if (isOdooSi) {
                sectionToToggle.classList.add('section-disabled');
                inputsToDisable.forEach(input => {
                    input.disabled = true;
                    input.value = ""; // Limpiar si se bloquea
                });
            } else {
                sectionToToggle.classList.remove('section-disabled');
                inputsToDisable.forEach(input => input.disabled = false);
            }
        };

        radioButtons.forEach(radio => radio.addEventListener('change', handleSectionToggle));
        
        // Ejecutar al cargar por si ya viene con datos
        handleSectionToggle();
    });
</script>
@endpush