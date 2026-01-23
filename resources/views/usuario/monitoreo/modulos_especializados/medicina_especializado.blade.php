@extends('layouts.usuario')

@section('title', 'Consulta Externa - Medicina')

@section('content')
    <div class="py-10 bg-[#F8F9FC] min-h-screen font-sans text-slate-600">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- ENCABEZADO: Limpio y Minimalista --}}
            <div class="mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Consulta Externa: Medicina</h2>
                    <div class="flex items-center gap-2 mt-1.5">
                        <span
                            class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-100">
                            MÓDULO CLÍNICO
                        </span>
                        <span class="text-slate-400 text-xs font-medium">|</span>
                        <span class="text-slate-500 text-xs font-medium flex items-center gap-1">
                            <i data-lucide="building-2" class="w-3 h-3"></i> {{ $acta->establecimiento->nombre }}
                        </span>
                    </div>
                </div>
                <a href="{{ route('usuario.monitoreo.modulos', $acta->id) }}"
                    class="group inline-flex items-center gap-2 px-5 py-2.5 bg-white border border-slate-200 rounded-xl text-sm font-semibold text-slate-600 hover:border-indigo-300 hover:text-indigo-600 hover:shadow-sm transition-all">
                    <i data-lucide="arrow-left" class="w-4 h-4 group-hover:-translate-x-1 transition-transform"></i>
                    Volver al Panel
                </a>
            </div>

            <form action="{{ route('usuario.monitoreo.medicina.store', $acta->id) }}" method="POST"
                enctype="multipart/form-data" class="space-y-6" id="form-medicina">
                @csrf

                {{-- SECCIÓN 1: DETALLES DEL CONSULTORIO --}}
                <div
                    class="bg-white rounded-2xl p-6 sm:p-8 shadow-sm border border-slate-100 relative overflow-hidden group">
                    <div class="absolute top-0 left-0 w-1.5 h-full bg-indigo-500"></div>

                    <div class="flex items-center gap-3 mb-8">
                        <div
                            class="flex items-center justify-center w-8 h-8 rounded-lg bg-indigo-600 text-white font-bold text-sm shadow-md shadow-indigo-200">
                            1</div>
                        <h3 class="text-lg font-bold text-slate-800">Detalles del Consultorio</h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        {{-- Fecha --}}
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Fecha</label>
                            <input type="date" name="contenido[fecha_monitoreo]"
                                value="{{ $detalle->contenido['fecha_monitoreo'] ?? date('Y-m-d') }}"
                                class="w-full bg-slate-50 border border-slate-200 text-slate-700 text-sm rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 block p-2.5 outline-none transition-all font-semibold">
                        </div>

                        {{-- Turno --}}
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Turno</label>
                            <div class="flex bg-slate-50 p-1 rounded-xl border border-slate-200">
                                <label class="flex-1 cursor-pointer">
                                    <input type="radio" name="contenido[turno]" value="MAÑANA"
                                        {{ ($detalle->contenido['turno'] ?? '') == 'MAÑANA' ? 'checked' : '' }}
                                        class="peer sr-only">
                                    <div
                                        class="py-2 text-center rounded-lg text-xs font-bold text-slate-400 transition-all peer-checked:bg-white peer-checked:text-amber-600 peer-checked:shadow-sm">
                                        MAÑANA</div>
                                </label>
                                <label class="flex-1 cursor-pointer">
                                    <input type="radio" name="contenido[turno]" value="TARDE"
                                        {{ ($detalle->contenido['turno'] ?? '') == 'TARDE' ? 'checked' : '' }}
                                        class="peer sr-only">
                                    <div
                                        class="py-2 text-center rounded-lg text-xs font-bold text-slate-400 transition-all peer-checked:bg-white peer-checked:text-indigo-600 peer-checked:shadow-sm">
                                        TARDE</div>
                                </label>
                            </div>
                        </div>

                        {{-- Nro Consultorio --}}
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">N° Consultorio</label>
                            <input type="number" name="contenido[num_consultorio]"
                                value="{{ $detalle->contenido['num_consultorio'] ?? '' }}"
                                class="w-full bg-slate-50 border border-slate-200 text-slate-700 text-sm rounded-xl focus:ring-indigo-500 focus:border-indigo-500 block p-2.5 outline-none font-semibold text-center"
                                placeholder="01">
                        </div>

                        {{-- Denominación --}}
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Denominación</label>
                            <input type="text" name="contenido[denominacion]"
                                value="{{ $detalle->contenido['denominacion'] ?? '' }}"
                                class="w-full bg-slate-50 border border-slate-200 text-slate-700 text-sm rounded-xl focus:ring-indigo-500 focus:border-indigo-500 block p-2.5 outline-none font-semibold uppercase"
                                placeholder="MEDICINA GENERAL">
                        </div>
                    </div>
                </div>

                {{-- SECCIÓN 2: DATOS DEL MÉDICO (REDISEÑADO INTEGRADO) --}}
                <div class="bg-white rounded-2xl p-6 sm:p-8 shadow-sm border border-slate-100 relative overflow-hidden">
                    <div class="absolute top-0 left-0 w-1.5 h-full bg-indigo-500"></div>

                    <div class="flex items-center gap-3 mb-8">
                        <div
                            class="flex items-center justify-center w-8 h-8 rounded-lg bg-indigo-600 text-white font-bold text-sm shadow-md shadow-indigo-200">
                            2</div>
                        <h3 class="text-lg font-bold text-slate-800">Datos del Médico</h3>
                    </div>

                    {{-- TARJETA ESTILO COMPONENTE --}}
                    <div class="border border-slate-200 rounded-2xl p-6 bg-slate-50/50">
                        <div class="flex items-center justify-between mb-6">
                            <div class="flex items-center gap-4">
                                <div
                                    class="h-12 w-12 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-slate-400">
                                    <i data-lucide="user-search" class="w-6 h-6"></i>
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-slate-700 uppercase tracking-wide">Módulo de Identidad
                                    </h4>
                                    <p class="text-xs text-slate-400 font-medium">Validación de datos del profesional</p>
                                </div>
                            </div>
                            <div class="flex gap-2">
                                <button type="button"
                                    class="px-4 py-2 bg-slate-900 text-white text-xs font-bold rounded-lg hover:bg-slate-800 transition-colors flex items-center gap-2">
                                    <i data-lucide="shield-check" class="w-3 h-3"></i> VALIDAR DOC
                                </button>
                                <button type="button"
                                    class="px-4 py-2 bg-white border border-slate-200 text-slate-600 text-xs font-bold rounded-lg hover:bg-slate-50 transition-colors flex items-center gap-2">
                                    <i data-lucide="user-plus" class="w-3 h-3"></i> NUEVO
                                </button>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                            {{-- Tipo Doc --}}
                            <div class="md:col-span-2">
                                <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1.5">Tipo Doc.</label>
                                <select name="contenido[profesional][tipo_doc]" id="profesional_tipo_doc"
                                    class="w-full bg-white border border-slate-200 text-slate-700 text-sm rounded-xl focus:ring-indigo-500 focus:border-indigo-500 p-2.5 outline-none font-bold">
                                    <option value="DNI" selected>DNI</option>
                                    <option value="CE">C.E.</option>
                                </select>
                            </div>
                            {{-- Nro Identidad --}}
                            <div class="md:col-span-3">
                                <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1.5">N°
                                    Identidad</label>
                                <div class="relative">
                                    <input type="text" name="contenido[profesional][nro_doc]"
                                        class="w-full bg-white border border-slate-200 text-slate-700 text-sm rounded-xl focus:ring-indigo-500 focus:border-indigo-500 p-2.5 pl-10 outline-none font-bold placeholder:text-slate-300"
                                        placeholder="Número">
                                    <i data-lucide="fingerprint" class="absolute left-3 top-2.5 w-4 h-4 text-slate-300"></i>
                                </div>
                            </div>
                            {{-- Nombres --}}
                            <div class="md:col-span-7">
                                <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1.5">Nombres
                                    Completos</label>
                                <input type="text" name="contenido[profesional][nombres]"
                                    class="w-full bg-white border border-slate-200 text-slate-700 text-sm rounded-xl focus:ring-indigo-500 focus:border-indigo-500 p-2.5 outline-none font-bold uppercase">
                            </div>

                            {{-- Apellido Paterno --}}
                            <div class="md:col-span-4">
                                <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1.5">Apellido
                                    Paterno</label>
                                <input type="text" name="contenido[profesional][ape_paterno]"
                                    class="w-full bg-white border border-slate-200 text-slate-700 text-sm rounded-xl focus:ring-indigo-500 focus:border-indigo-500 p-2.5 outline-none font-bold uppercase">
                            </div>
                            {{-- Apellido Materno --}}
                            <div class="md:col-span-4">
                                <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1.5">Apellido
                                    Materno</label>
                                <input type="text" name="contenido[profesional][ape_materno]"
                                    class="w-full bg-white border border-slate-200 text-slate-700 text-sm rounded-xl focus:ring-indigo-500 focus:border-indigo-500 p-2.5 outline-none font-bold uppercase">
                            </div>
                            {{-- CMP --}}
                            <div class="md:col-span-4">
                                <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1.5">CMP /
                                    Colegiatura</label>
                                <input type="text" name="contenido[profesional][colegiatura]"
                                    class="w-full bg-white border border-slate-200 text-slate-700 text-sm rounded-xl focus:ring-indigo-500 focus:border-indigo-500 p-2.5 outline-none font-bold uppercase">
                            </div>
                        </div>
                    </div>

                    {{-- PREGUNTA SIHCE --}}
                    <div
                        class="mt-6 flex flex-col md:flex-row md:items-center justify-between gap-4 p-4 bg-indigo-50/50 border border-indigo-100 rounded-xl">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-indigo-100 rounded-lg text-indigo-600"><i data-lucide="monitor-check"
                                    class="w-5 h-5"></i></div>
                            <div>
                                <span class="block text-sm font-bold text-indigo-900">Uso del Sistema SIHCE</span>
                                <span class="text-xs text-indigo-600/80">¿El profesional utiliza el sistema
                                    activamente?</span>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <label class="cursor-pointer relative">
                                <input type="radio" name="contenido[utiliza_sihce]" value="SI"
                                    {{ ($detalle->contenido['utiliza_sihce'] ?? '') == 'SI' ? 'checked' : '' }}
                                    onchange="toggleSihce('SI')" class="peer sr-only">
                                <span
                                    class="block px-6 py-2 rounded-lg border-2 border-indigo-200 bg-white text-xs font-bold text-slate-400 peer-checked:bg-indigo-600 peer-checked:text-white peer-checked:border-indigo-600 transition-all">SÍ</span>
                            </label>
                            <label class="cursor-pointer relative">
                                <input type="radio" name="contenido[utiliza_sihce]" value="NO"
                                    {{ ($detalle->contenido['utiliza_sihce'] ?? '') == 'NO' ? 'checked' : '' }}
                                    onchange="toggleSihce('NO')" class="peer sr-only">
                                <span
                                    class="block px-6 py-2 rounded-lg border-2 border-slate-200 bg-white text-xs font-bold text-slate-400 peer-checked:bg-slate-600 peer-checked:text-white peer-checked:border-slate-600 transition-all">NO</span>
                            </label>
                        </div>
                    </div>

                    {{-- DOCUMENTACIÓN (Condicional) --}}
                    <div id="bloque_doc_administrativa"
                        class="mt-6 pt-6 border-t border-slate-100 {{ ($detalle->contenido['utiliza_sihce'] ?? '') == 'NO' ? 'hidden' : '' }} animate-fade-in">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div
                                class="flex items-center justify-between p-3.5 rounded-xl border border-slate-200 hover:border-indigo-200 transition-colors">
                                <span class="text-sm font-semibold text-slate-600">Declaración Jurada</span>
                                <div class="flex gap-2">
                                    <label class="cursor-pointer"><input type="radio" name="contenido[firmo_dj]"
                                            value="SI"
                                            {{ ($detalle->contenido['firmo_dj'] ?? '') == 'SI' ? 'checked' : '' }}
                                            class="accent-indigo-600 mr-1"><span
                                            class="text-xs font-bold">SI</span></label>
                                    <label class="cursor-pointer"><input type="radio" name="contenido[firmo_dj]"
                                            value="NO"
                                            {{ ($detalle->contenido['firmo_dj'] ?? '') == 'NO' ? 'checked' : '' }}
                                            class="accent-red-500 mr-1"><span class="text-xs font-bold">NO</span></label>
                                </div>
                            </div>
                            <div
                                class="flex items-center justify-between p-3.5 rounded-xl border border-slate-200 hover:border-indigo-200 transition-colors">
                                <span class="text-sm font-semibold text-slate-600">Compromiso Confidencialidad</span>
                                <div class="flex gap-2">
                                    <label class="cursor-pointer"><input type="radio"
                                            name="contenido[firmo_confidencialidad]" value="SI"
                                            {{ ($detalle->contenido['firmo_confidencialidad'] ?? '') == 'SI' ? 'checked' : '' }}
                                            class="accent-indigo-600 mr-1"><span
                                            class="text-xs font-bold">SI</span></label>
                                    <label class="cursor-pointer"><input type="radio"
                                            name="contenido[firmo_confidencialidad]" value="NO"
                                            {{ ($detalle->contenido['firmo_confidencialidad'] ?? '') == 'NO' ? 'checked' : '' }}
                                            class="accent-red-500 mr-1"><span class="text-xs font-bold">NO</span></label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- SECCIÓN 3: DNI Y FIRMA DIGITAL --}}
                <div id="seccion_dni_firma"
                    class="bg-white rounded-2xl p-6 sm:p-8 shadow-sm border border-slate-100 relative overflow-hidden {{ ($detalle->contenido['profesional']['tipo_doc'] ?? 'DNI') !== 'DNI' ? 'hidden' : '' }}">
                    <div class="absolute top-0 left-0 w-1.5 h-full bg-indigo-500"></div>
                    <div class="flex items-center gap-3 mb-8">
                        <div
                            class="flex items-center justify-center w-8 h-8 rounded-lg bg-indigo-600 text-white font-bold text-sm shadow-md shadow-indigo-200">
                            3</div>
                        <h3 class="text-lg font-bold text-slate-800">DNI y Firma Digital</h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <label class="relative cursor-pointer group">
                            <input type="radio" name="contenido[tipo_dni_fisico]" value="ELECTRONICO"
                                class="peer sr-only" onchange="toggleDniFields()"
                                {{ ($detalle->contenido['tipo_dni_fisico'] ?? '') == 'ELECTRONICO' ? 'checked' : '' }}>
                            <div
                                class="p-4 rounded-xl border-2 border-slate-100 bg-white peer-checked:border-indigo-600 peer-checked:bg-indigo-50/20 hover:border-indigo-200 transition-all">
                                <div class="flex items-center gap-3">
                                    <div class="bg-indigo-100 p-2 rounded-lg text-indigo-600"><i data-lucide="credit-card"
                                            class="w-5 h-5"></i></div>
                                    <div>
                                        <span class="block text-sm font-bold text-slate-700">DNI Electrónico</span>
                                        <span class="text-[10px] text-slate-400 font-semibold uppercase">Con Chip</span>
                                    </div>
                                    <i data-lucide="check-circle-2"
                                        class="w-5 h-5 text-indigo-600 ml-auto opacity-0 peer-checked:opacity-100"></i>
                                </div>
                            </div>
                        </label>
                        <label class="relative cursor-pointer group">
                            <input type="radio" name="contenido[tipo_dni_fisico]" value="AZUL" class="peer sr-only"
                                onchange="toggleDniFields()"
                                {{ ($detalle->contenido['tipo_dni_fisico'] ?? '') == 'AZUL' ? 'checked' : '' }}>
                            <div
                                class="p-4 rounded-xl border-2 border-slate-100 bg-white peer-checked:border-blue-500 peer-checked:bg-blue-50/20 hover:border-blue-200 transition-all">
                                <div class="flex items-center gap-3">
                                    <div class="bg-blue-100 p-2 rounded-lg text-blue-600"><i data-lucide="user-square"
                                            class="w-5 h-5"></i></div>
                                    <div>
                                        <span class="block text-sm font-bold text-slate-700">DNI Azul / Amarillo</span>
                                        <span class="text-[10px] text-slate-400 font-semibold uppercase">Sin Chip</span>
                                    </div>
                                    <i data-lucide="check-circle-2"
                                        class="w-5 h-5 text-blue-500 ml-auto opacity-0 peer-checked:opacity-100"></i>
                                </div>
                            </div>
                        </label>
                    </div>

                    <div id="block-info-dnie"
                        class="hidden animate-fade-in mb-6 p-5 bg-indigo-50 rounded-xl border border-indigo-100 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-bold text-indigo-800 mb-2">Versión del DNIe</label>
                            <select name="contenido[dnie_version]"
                                class="w-full bg-white border border-indigo-200 text-slate-700 text-sm rounded-lg outline-none focus:ring-2 focus:ring-indigo-500/20 p-2.5 font-bold">
                                <option value="">-- Seleccionar --</option>
                                <option value="1.0"
                                    {{ ($detalle->contenido['dnie_version'] ?? '') == '1.0' ? 'selected' : '' }}>Versión
                                    1.0 (Antiguo)</option>
                                <option value="2.0"
                                    {{ ($detalle->contenido['dnie_version'] ?? '') == '2.0' ? 'selected' : '' }}>Versión
                                    2.0 (Actual)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-indigo-800 mb-2">¿Firma Digitalmente la HCE?</label>
                            <div class="flex gap-3 pt-1">
                                <label
                                    class="flex items-center gap-2 cursor-pointer bg-white px-3 py-2 rounded-lg border border-indigo-100 shadow-sm"><input
                                        type="radio" name="contenido[dnie_firma_sihce]" value="SI"
                                        {{ ($detalle->contenido['dnie_firma_sihce'] ?? '') == 'SI' ? 'checked' : '' }}
                                        class="accent-indigo-600 w-4 h-4"><span
                                        class="text-xs font-bold text-slate-600">SÍ</span></label>
                                <label
                                    class="flex items-center gap-2 cursor-pointer bg-white px-3 py-2 rounded-lg border border-indigo-100 shadow-sm"><input
                                        type="radio" name="contenido[dnie_firma_sihce]" value="NO"
                                        {{ ($detalle->contenido['dnie_firma_sihce'] ?? '') == 'NO' ? 'checked' : '' }}
                                        class="accent-slate-400 w-4 h-4"><span
                                        class="text-xs font-bold text-slate-600">NO</span></label>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- SECCIÓN 4, 5, 6 (GRID COMPACTA) --}}
                <div class="{{ ($detalle->contenido['utiliza_sihce'] ?? '') == 'NO' ? 'hidden' : '' }}"
                    id="bloque_central_funcionalidad">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        {{-- SECCIÓN 4: CAPACITACIÓN --}}
                        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 relative overflow-hidden"
                            id="seccion_capacitacion">
                            <div class="absolute top-0 left-0 w-1.5 h-full bg-indigo-500"></div>
                            <div class="flex items-center gap-3 mb-6">
                                <div
                                    class="flex items-center justify-center w-8 h-8 rounded-lg bg-indigo-600 text-white font-bold text-sm shadow-md">
                                    4</div>
                                <h3 class="text-base font-bold text-slate-800">Capacitación</h3>
                            </div>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-xs font-bold text-slate-500 uppercase mb-2">¿Recibió
                                        capacitación?</label>
                                    <div class="flex gap-2">
                                        <label class="flex-1 cursor-pointer"><input type="radio"
                                                name="contenido[recibio_capacitacion]" value="SI"
                                                {{ ($detalle->contenido['recibio_capacitacion'] ?? '') == 'SI' ? 'checked' : '' }}
                                                class="peer sr-only" onchange="toggleInstCapacitacion(this.value)">
                                            <div
                                                class="py-2 text-center rounded-lg border border-slate-200 text-xs font-bold text-slate-500 peer-checked:bg-emerald-50 peer-checked:text-emerald-700 peer-checked:border-emerald-200 transition-all">
                                                SÍ</div>
                                        </label>
                                        <label class="flex-1 cursor-pointer"><input type="radio"
                                                name="contenido[recibio_capacitacion]" value="NO"
                                                {{ ($detalle->contenido['recibio_capacitacion'] ?? '') == 'NO' ? 'checked' : '' }}
                                                class="peer sr-only" onchange="toggleInstCapacitacion(this.value)">
                                            <div
                                                class="py-2 text-center rounded-lg border border-slate-200 text-xs font-bold text-slate-500 peer-checked:bg-red-50 peer-checked:text-red-700 peer-checked:border-red-200 transition-all">
                                                NO</div>
                                        </label>
                                    </div>
                                </div>
                                <div id="section_inst_capacitacion"
                                    class="{{ ($detalle->contenido['recibio_capacitacion'] ?? '') === 'NO' ? 'hidden' : '' }}">
                                    <label
                                        class="block text-xs font-bold text-slate-500 uppercase mb-2">Institución</label>
                                    <select name="contenido[inst_capacitacion]"
                                        class="w-full bg-slate-50 border border-slate-200 text-slate-700 text-xs rounded-lg p-2.5 outline-none font-bold">
                                        @foreach (['MINSA', 'DIRESA', 'RED DE SALUD', 'OTROS'] as $op)
                                            <option value="{{ $op }}"
                                                {{ ($detalle->contenido['inst_capacitacion'] ?? '') == $op ? 'selected' : '' }}>
                                                {{ $op }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- SECCIÓN 5: PROCESOS --}}
                        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 relative overflow-hidden"
                            id="seccion_funcionalidad_medicina">
                            <div class="absolute top-0 left-0 w-1.5 h-full bg-indigo-500"></div>
                            <div class="flex items-center gap-3 mb-6">
                                <div
                                    class="flex items-center justify-center w-8 h-8 rounded-lg bg-indigo-600 text-white font-bold text-sm shadow-md">
                                    5</div>
                                <h3 class="text-base font-bold text-slate-800">Procesos</h3>
                            </div>
                            <div class="space-y-2">
                                @foreach ([['l' => 'CIE-10', 'n' => 'func_cie10'], ['l' => 'Órdenes Lab/Img', 'n' => 'func_ordenes'], ['l' => 'Receta Electrónica', 'n' => 'func_receta']] as $item)
                                    <div
                                        class="flex items-center justify-between p-2 rounded-lg hover:bg-slate-50 border border-transparent hover:border-slate-100 transition-all">
                                        <span
                                            class="text-xs font-bold text-slate-600 uppercase">{{ $item['l'] }}</span>
                                        <div class="flex gap-1">
                                            <label class="cursor-pointer"><input type="radio"
                                                    name="contenido[{{ $item['n'] }}]" value="SI"
                                                    {{ ($detalle->contenido[$item['n']] ?? '') == 'SI' ? 'checked' : '' }}
                                                    class="peer sr-only"><span
                                                    class="px-2 py-1 rounded text-[10px] font-bold text-slate-400 peer-checked:bg-indigo-100 peer-checked:text-indigo-700">SI</span></label>
                                            <label class="cursor-pointer"><input type="radio"
                                                    name="contenido[{{ $item['n'] }}]" value="NO"
                                                    {{ ($detalle->contenido[$item['n']] ?? '') == 'NO' ? 'checked' : '' }}
                                                    class="peer sr-only"><span
                                                    class="px-2 py-1 rounded text-[10px] font-bold text-slate-400 peer-checked:bg-red-50 peer-checked:text-red-700">NO</span></label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                {{-- SECCIÓN 6: MATERIALES --}}
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 relative overflow-hidden">
                    <div class="absolute top-0 left-0 w-1.5 h-full bg-indigo-500"></div>
                    <div class="flex items-center gap-3 mb-6">
                        <div
                            class="flex items-center justify-center w-8 h-8 rounded-lg bg-indigo-600 text-white font-bold text-sm shadow-md">
                            6</div>
                        <h3 class="text-base font-bold text-slate-800">Formatos y Materiales</h3>
                    </div>
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
                        @foreach (['fua' => 'FUA', 'recetario' => 'Receta', 'orden_lab' => 'Lab.', 'orden_imagenes' => 'Rayos X', 'hoja_referencia' => 'Referencia', 'consentimiento' => 'Consentim.'] as $key => $label)
                            <label
                                class="flex flex-col items-center justify-center p-3 rounded-xl border border-slate-200 cursor-pointer hover:bg-slate-50 hover:border-indigo-200 transition-all bg-slate-50/50">
                                <input type="checkbox" name="contenido[materiales][{{ $key }}]" value="1"
                                    {{ isset($detalle->contenido['materiales'][$key]) && $detalle->contenido['materiales'][$key] ? 'checked' : '' }}
                                    class="peer sr-only">
                                <div
                                    class="w-4 h-4 rounded border border-slate-300 peer-checked:bg-indigo-600 peer-checked:border-indigo-600 mb-2 transition-colors flex items-center justify-center text-white">
                                    <i data-lucide="check" class="w-3 h-3"></i>
                                </div>
                                <span
                                    class="text-[10px] font-bold text-slate-500 uppercase text-center">{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- SECCIÓN 7: EQUIPAMIENTO (REDISEÑADO INTEGRADO) --}}
                <div class="bg-white rounded-2xl p-6 sm:p-8 shadow-sm border border-slate-100 relative overflow-hidden">
                    <div class="absolute top-0 left-0 w-1.5 h-full bg-indigo-500"></div>
                    <div class="flex items-center gap-3 mb-8">
                        <div
                            class="flex items-center justify-center w-8 h-8 rounded-lg bg-indigo-600 text-white font-bold text-sm shadow-md shadow-indigo-200">
                            7</div>
                        <h3 class="text-lg font-bold text-slate-800">Equipamiento Informático</h3>
                    </div>

                    {{-- TARJETA ESTILO COMPONENTE --}}
                    <div class="border border-slate-200 rounded-2xl p-0 overflow-hidden bg-white">
                        <div
                            class="p-6 bg-slate-50/50 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div class="flex items-center gap-4">
                                <div
                                    class="h-10 w-10 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-indigo-600">
                                    <i data-lucide="pc-case" class="w-5 h-5"></i>
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-slate-700 uppercase tracking-wide">Inventario de
                                        Equipamiento</h4>
                                    <p class="text-xs text-slate-400 font-medium">Gestión de activos tecnológicos</p>
                                </div>
                            </div>
                            <button type="button"
                                class="px-4 py-2 bg-indigo-600 text-white text-xs font-bold rounded-lg hover:bg-indigo-700 shadow-sm shadow-indigo-200 transition-colors flex items-center gap-2">
                                <i data-lucide="plus-circle" class="w-3 h-3"></i> AÑADIR EQUIPO
                            </button>
                        </div>

                        {{-- TABLA SIMULADA VACÍA --}}
                        <div class="overflow-x-auto">
                            <table class="w-full text-xs text-left">
                                <thead class="text-[10px] text-slate-400 uppercase bg-slate-50 border-b border-slate-100">
                                    <tr>
                                        <th class="px-6 py-3 font-bold">Descripción</th>
                                        <th class="px-6 py-3 font-bold">Cant.</th>
                                        <th class="px-6 py-3 font-bold">Estado</th>
                                        <th class="px-6 py-3 font-bold">Propiedad</th>
                                        <th class="px-6 py-3 font-bold text-right">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- Si no hay equipos --}}
                                    <tr>
                                        <td colspan="5" class="px-6 py-10 text-center">
                                            <div class="flex flex-col items-center justify-center text-slate-300">
                                                <i data-lucide="hard-drive" class="w-10 h-10 mb-2 opacity-50"></i>
                                                <span class="font-bold text-xs uppercase tracking-wide">Sin registros de
                                                    hardware</span>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- SECCIÓN 8: SOPORTE --}}
                <div id="seccion_soporte"
                    class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 relative overflow-hidden {{ ($detalle->contenido['utiliza_sihce'] ?? '') == 'NO' ? 'hidden' : '' }}">
                    <div class="absolute top-0 left-0 w-1.5 h-full bg-indigo-500"></div>
                    <div class="flex items-center gap-3 mb-6">
                        <div
                            class="flex items-center justify-center w-8 h-8 rounded-lg bg-indigo-600 text-white font-bold text-sm shadow-md">
                            8</div>
                        <h3 class="text-base font-bold text-slate-800">Soporte Técnico</h3>
                    </div>
                    <div class="flex flex-col md:flex-row gap-6">
                        <div class="w-full md:w-1/2">
                            <label class="text-xs font-bold text-slate-500 uppercase mb-2 block">¿A quién reporta?</label>
                            <select name="contenido[comunica_a]"
                                class="w-full bg-slate-50 border border-slate-200 text-slate-700 text-xs rounded-lg p-2.5 outline-none font-bold">
                                @foreach (['MINSA (Mesa Ayuda)', 'DIRESA', 'RED DE SALUD', 'ESTADÍSTICA', 'OTROS'] as $op)
                                    <option value="{{ $op }}"
                                        {{ ($detalle->contenido['comunica_a'] ?? '---') == $op ? 'selected' : '' }}>
                                        {{ $op }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="w-full md:w-1/2">
                            <label class="text-xs font-bold text-slate-500 uppercase mb-2 block">Medio utilizado</label>
                            <div class="flex gap-2">
                                @foreach (['CELULAR', 'CORREO', 'WHATSAPP'] as $label)
                                    <label class="cursor-pointer">
                                        <input type="radio" name="contenido[medio_soporte]"
                                            value="{{ $label }}"
                                            {{ ($detalle->contenido['medio_soporte'] ?? '') == $label ? 'checked' : '' }}
                                            class="peer sr-only">
                                        <span
                                            class="px-3 py-1.5 rounded-lg border border-slate-200 bg-white text-[10px] font-bold text-slate-500 peer-checked:bg-slate-800 peer-checked:text-white peer-checked:border-slate-800 transition-all">{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                @csrf

                {{-- Otros campos... --}}

                {{-- Aquí llamas a tu nuevo componente --}}
                <x-soporte_esp :detalle="$detalle" />

                <x-detalle_consultorio_esp :detalle="$detalle" />

                {{-- SECCIÓN FINAL: COMENTARIOS Y FOTOS (SEPARADOS Y LIMPIOS) --}}
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                    {{-- BLOQUE 1: OBSERVACIONES --}}
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 flex flex-col h-full">
                        <h3 class="text-sm font-bold text-slate-700 mb-4 flex items-center gap-2">
                            <div class="p-1.5 bg-indigo-50 rounded text-indigo-600"><i data-lucide="message-square"
                                    class="w-4 h-4"></i></div>
                            Observaciones Generales
                        </h3>
                        <textarea name="contenido[comentarios]"
                            class="flex-1 w-full bg-slate-50/50 border border-slate-200 rounded-xl p-4 text-sm text-slate-700 font-medium outline-none focus:ring-2 focus:ring-indigo-100 focus:border-indigo-400 transition-all placeholder:text-slate-400 resize-none min-h-[160px]"
                            placeholder="Describa aquí cualquier incidencia, observación o comentario relevante sobre el monitoreo...">{{ $detalle->contenido['comentarios'] ?? '' }}</textarea>
                    </div>

                    {{-- BLOQUE 2: FOTOS --}}
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 flex flex-col h-full">
                        <h3 class="text-sm font-bold text-slate-700 mb-4 flex items-center gap-2">
                            <div class="p-1.5 bg-amber-50 rounded text-amber-600"><i data-lucide="camera"
                                    class="w-4 h-4"></i></div>
                            Evidencia Fotográfica
                        </h3>

                        <div class="relative group flex-1">
                            <input type="file" name="foto_evidencia[]" id="foto_evidencia" accept="image/*" multiple
                                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-20"
                                onchange="simplePreview(event)">

                            {{-- Dropzone Estilo "Clean" --}}
                            <div id="dropzone"
                                class="h-full min-h-[160px] border-2 border-dashed border-slate-300 rounded-xl flex flex-col items-center justify-center bg-slate-50 group-hover:bg-slate-100 group-hover:border-indigo-400 transition-all">
                                <div id="placeholder-content"
                                    class="flex flex-col items-center text-slate-400 group-hover:text-indigo-500 transition-colors">
                                    <i data-lucide="image-plus" class="w-8 h-8 mb-2"></i>
                                    <span class="text-xs font-semibold">Click o arrastra para subir fotos</span>
                                </div>
                                {{-- Previews nuevas --}}
                                <div id="new-previews"
                                    class="hidden absolute inset-2 bg-white rounded-lg p-2 grid grid-cols-3 gap-2 overflow-hidden pointer-events-none z-10">
                                </div>
                            </div>
                        </div>

                        {{-- Fotos Guardadas --}}
                        @php
                            $fotosActuales = isset($detalle->contenido['foto_evidencia'])
                                ? (is_array($detalle->contenido['foto_evidencia'])
                                    ? $detalle->contenido['foto_evidencia']
                                    : [$detalle->contenido['foto_evidencia']])
                                : [];
                        @endphp
                        @if (count($fotosActuales) > 0)
                            <div id="saved-images-block" class="mt-4 pt-4 border-t border-slate-100">
                                <p class="text-[10px] font-bold text-emerald-600 mb-2 flex items-center gap-1">
                                    <i data-lucide="check" class="w-3 h-3"></i> IMÁGENES ACTUALES
                                </p>
                                <div class="flex gap-2 overflow-x-auto pb-1">
                                    @foreach ($fotosActuales as $foto)
                                        <a href="{{ asset('storage/' . $foto) }}" target="_blank"
                                            class="flex-shrink-0 w-16 h-16 rounded-lg overflow-hidden border border-slate-200 hover:opacity-80 transition">
                                            <img src="{{ asset('storage/' . $foto) }}"
                                                class="w-full h-full object-cover">
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- BOTÓN GUARDAR FLOTANTE --}}
                <div
                    class="fixed bottom-0 left-0 w-full bg-white border-t border-slate-200 p-4 shadow-lg z-50 md:relative md:bg-transparent md:border-0 md:shadow-none md:p-0 md:pt-6 md:pb-12">
                    <button type="submit" id="btn-submit-action"
                        class="max-w-6xl mx-auto w-full bg-indigo-600 text-white h-12 md:h-14 rounded-xl font-bold text-sm shadow-lg shadow-indigo-200 hover:bg-indigo-700 hover:shadow-indigo-300 hover:-translate-y-0.5 transition-all duration-300 flex items-center justify-center gap-3">
                        <span id="icon-save-loader"><i data-lucide="save" class="w-5 h-5"></i></span>
                        <span class="tracking-wide">GUARDAR MONITOREO</span>
                    </button>
                </div>



            </form>
        </div>
    </div>

    {{-- JAVASCRIPT --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Inicialización de iconos
            if (typeof lucide !== 'undefined') lucide.createIcons();

            // Lógica DNI
            toggleDniFields(true);
            const estadoSihce = document.querySelector('input[name="contenido[utiliza_sihce]"]:checked')?.value;
            if (estadoSihce === 'NO') toggleSihce('NO');

            // Detección automática del tipo de documento para mostrar/ocultar sección
            const inputTipoDoc = document.getElementById('profesional_tipo_doc');
            const sectionDni = document.getElementById('seccion_dni_firma');

            function checkTipoDoc() {
                if (!inputTipoDoc || !sectionDni) return;
                const valor = inputTipoDoc.value.toUpperCase().trim();
                valor === 'DNI' ? sectionDni.classList.remove('hidden') : sectionDni.classList.add('hidden');
            }

            if (inputTipoDoc) {
                checkTipoDoc();
                inputTipoDoc.addEventListener('change', checkTipoDoc);
            }
        });

        // Toggle para secciones dependientes de SIHCE
        function toggleSihce(valor) {
            const bloqueDoc = document.getElementById('bloque_doc_administrativa');
            const bloqueSoporte = document.getElementById('seccion_soporte');
            const bloqueCapacitacion = document.getElementById('seccion_capacitacion');
            const bloqueFuncionalidad = document.getElementById('seccion_funcionalidad_medicina');
            const bloqueCentral = document.getElementById('bloque_central_funcionalidad'); // Contenedor grid

            if (valor === 'SI') {
                if (bloqueDoc) bloqueDoc.classList.remove('hidden');
                if (bloqueCapacitacion) bloqueCapacitacion.classList.remove('hidden');
                if (bloqueSoporte) bloqueSoporte.classList.remove('hidden');
                if (bloqueFuncionalidad) bloqueFuncionalidad.classList.remove('hidden');
                if (bloqueCentral) bloqueCentral.classList.remove('hidden');
            } else {
                // Ocultar y limpiar
                [bloqueDoc, bloqueSoporte, bloqueCentral].forEach(el => {
                    if (el) {
                        el.classList.add('hidden');
                        // Resetear inputs internos (opcional)
                        el.querySelectorAll('input[type="radio"], input[type="checkbox"]').forEach(r => r.checked =
                            false);
                        el.querySelectorAll('select, textarea, input[type="text"]').forEach(s => s.value = '');
                    }
                });
            }
        }

        // Toggle campos DNIe vs DNI Azul
        function toggleDniFields(isInitialLoad = false) {
            const tipoDni = document.querySelector('input[name="contenido[tipo_dni_fisico]"]:checked')?.value;
            const blockDnie = document.getElementById('block-info-dnie');

            if (tipoDni === 'ELECTRONICO') {
                blockDnie.classList.remove('hidden');
            } else {
                blockDnie.classList.add('hidden');
                if (!isInitialLoad) {
                    const select = document.querySelector('select[name="contenido[dnie_version]"]');
                    if (select) select.value = '';
                    document.querySelectorAll('input[name="contenido[dnie_firma_sihce]"]').forEach(r => r.checked = false);
                }
            }
        }

        // Toggle Institución de capacitación
        function toggleInstCapacitacion(value) {
            const section = document.getElementById('section_inst_capacitacion');
            value === 'NO' ? section.classList.add('hidden') : section.classList.remove('hidden');
        }

        // Previsualización de Imágenes
        function simplePreview(event) {
            const input = event.target;
            const previewContainer = document.getElementById('new-previews');
            const placeholder = document.getElementById('placeholder-content');

            previewContainer.innerHTML = '';

            if (input.files && input.files.length > 0) {
                // Validación simple de tipo
                for (let i = 0; i < input.files.length; i++) {
                    if (!input.files[i].type.startsWith('image/')) {
                        alert('Solo se permiten imágenes.');
                        input.value = "";
                        previewContainer.classList.add('hidden');
                        placeholder.classList.remove('hidden');
                        return;
                    }
                }
                // Mostrar imágenes
                previewContainer.classList.remove('hidden');
                placeholder.classList.add('hidden');

                Array.from(input.files).slice(0, 5).forEach(file => {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.className = 'w-full h-full object-cover rounded-md border border-slate-200';
                        previewContainer.appendChild(img);
                    }
                    reader.readAsDataURL(file);
                });
            } else {
                previewContainer.classList.add('hidden');
                placeholder.classList.remove('hidden');
            }
        }

        // Loader al guardar
        document.getElementById('form-medicina').onsubmit = function() {
            const btn = document.getElementById('btn-submit-action');
            const icon = document.getElementById('icon-save-loader');
            btn.disabled = true;
            btn.classList.add('opacity-75', 'cursor-not-allowed');
            icon.innerHTML = '<i data-lucide="loader-2" class="w-5 h-5 text-white animate-spin"></i>';
            if (typeof lucide !== 'undefined') lucide.createIcons();
            return true;
        };
    </script>
@endsection
