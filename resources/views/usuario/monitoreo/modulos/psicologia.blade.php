@extends('layouts.usuario')
@section('title', 'Módulo 07: Psicología')

@section('content')
<div class="py-12 bg-[#f8fafc] min-h-screen" x-data="psicologiaForm()">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        
        {{-- ENCABEZADO --}}
        <div class="mb-10 flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="flex items-center gap-6">
                <div class="h-16 w-16 bg-white rounded-2xl flex items-center justify-center shadow-sm border border-slate-100">
                    <span class="text-2xl font-black text-indigo-600">07</span>
                </div>
                <div>
                    <div class="flex items-center gap-3 mb-1">
                        <span class="px-3 py-1 bg-indigo-100 text-indigo-700 text-[10px] font-black rounded-full uppercase tracking-widest">Módulo Técnico</span>
                        <span class="text-slate-400 font-bold text-[10px] uppercase tracking-wider">ID Acta: #{{ str_pad($acta->id, 5, '0', STR_PAD_LEFT) }}</span>
                    </div>
                    <h2 class="text-3xl font-black text-slate-900 uppercase tracking-tight italic">Módulo Psicología</h2>

                    {{-- FECHA DE ACTUALIZACIÓN --}}
                    @if($fechaValidacion)
                        <div class="flex items-center gap-2 mt-2 text-slate-400 animate-pulse">
                            <i data-lucide="clock" class="w-3 h-3"></i>
                            <span class="text-[10px] font-bold uppercase tracking-widest">
                                Guardado: {{ \Carbon\Carbon::parse($fechaValidacion)->format('d/m/Y - h:i A') }}
                            </span>
                        </div>
                    @endif
                </div>
            </div>
            
            <a href="{{ route('usuario.monitoreo.modulos', $acta->id) }}" class="px-6 py-3 bg-white border border-slate-200 rounded-2xl text-slate-500 font-black text-xs uppercase tracking-widest shadow-sm hover:bg-slate-50 transition-colors">
                Volver
            </a>
        </div>

        {{-- FORMULARIO ÚNICO --}}
        <form @submit.prevent="guardarTodo" class="space-y-8">

            {{-- 1. SECCIÓN INICIO LABORES (ESPECÍFICO PSICOLOGÍA) --}}
            <div class="bg-white border border-slate-200 rounded-[2.5rem] p-8 shadow-xl shadow-slate-200/50 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-24 h-24 bg-indigo-50 rounded-full -mr-12 -mt-12 opacity-60 pointer-events-none"></div>
                
                <div class="flex items-center gap-4 mb-8">
                    <div class="h-12 w-12 rounded-2xl bg-indigo-600 flex items-center justify-center shadow-lg shadow-indigo-200">
                        <i data-lucide="clipboard-list" class="text-white w-6 h-6"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-slate-900 uppercase tracking-tight">Inicio Labores</h3>
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Configuración Inicial</p>
                    </div>
                </div>

                <div class="space-y-8">
                    {{-- Grid: Consultorios y Turno --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        
                        {{-- Cantidad Consultorios --}}
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Cantidad de Consultorios</label>
                            <div class="relative">
                                <input type="number" 
                                       min="0" 
                                       x-model="form.inicio_labores.consultorios" 
                                       class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 pl-4 font-bold text-sm focus:ring-indigo-500">
                            </div>
                        </div>

                        {{-- Nombre del Consultorio --}}
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Nombre del Consultorio</label>
                            <div class="relative">
                                <input type="text" 
                                       placeholder="Ej: Psicología 01"
                                       x-model="form.inicio_labores.nombre_consultorio" 
                                       class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 pl-4 font-bold text-sm focus:ring-indigo-500 uppercase">
                            </div>
                        </div>

                        {{-- Turno --}}
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Turno</label>
                            <div class="relative">
                                <select x-model="form.inicio_labores.turno" 
                                        class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 pl-4 font-bold text-sm focus:ring-indigo-500 uppercase cursor-pointer">
                                    <option value="" disabled>Seleccione...</option>
                                    <option value="MAÑANA">MAÑANA</option>
                                    <option value="TARDE">TARDE</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- Grid de Opciones Específicas (SOLO FUA Y REFERENCIA) --}}
                    <div>
                        <label class="block text-sm font-bold text-slate-800 mb-4 uppercase tracking-tight">Al iniciar cuenta con:</label>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- FUA --}}
                            <div class="bg-slate-50 p-5 rounded-2xl border border-slate-100">
                                <span class="block text-[10px] font-black text-slate-400 uppercase mb-3 tracking-widest">FUA</span>
                                <div class="space-y-3">
                                    <label class="flex items-center gap-3 cursor-pointer group">
                                        <input type="radio" value="ELECTRONICA" x-model="form.inicio_labores.fua" class="w-4 h-4 text-indigo-600 focus:ring-indigo-500 border-slate-300">
                                        <span class="text-xs font-bold text-slate-600 group-hover:text-indigo-600 transition-colors uppercase">FUA Electrónica</span>
                                    </label>
                                    <label class="flex items-center gap-3 cursor-pointer group">
                                        <input type="radio" value="MANUAL" x-model="form.inicio_labores.fua" class="w-4 h-4 text-indigo-600 focus:ring-indigo-500 border-slate-300">
                                        <span class="text-xs font-bold text-slate-600 group-hover:text-indigo-600 transition-colors uppercase">FUA Manual</span>
                                    </label>
                                </div>
                            </div>

                            {{-- Referencia --}}
                            <div class="bg-slate-50 p-5 rounded-2xl border border-slate-100">
                                <span class="block text-[10px] font-black text-slate-400 uppercase mb-3 tracking-widest">Referencia</span>
                                <div class="space-y-3">
                                    <label class="flex items-center gap-3 cursor-pointer group">
                                        <input type="radio" value="SIHCE" x-model="form.inicio_labores.referencia" class="w-4 h-4 text-indigo-600 focus:ring-indigo-500 border-slate-300">
                                        <span class="text-xs font-bold text-slate-600 group-hover:text-indigo-600 transition-colors uppercase">Referencia por SIHCE</span>
                                    </label>
                                    <label class="flex items-center gap-3 cursor-pointer group">
                                        <input type="radio" value="DIRECTO REFCON" x-model="form.inicio_labores.referencia" class="w-4 h-4 text-indigo-600 focus:ring-indigo-500 border-slate-300">
                                        <span class="text-xs font-bold text-slate-600 group-hover:text-indigo-600 transition-colors uppercase">Directo a REFCON</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- 2. DATOS DEL PROFESIONAL --}}
            <div class="bg-white border border-slate-200 rounded-[2.5rem] p-8 shadow-xl shadow-slate-200/50 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-indigo-50 rounded-full -mr-16 -mt-16 opacity-60 pointer-events-none"></div>
                <div class="relative z-10">
                    <div class="flex items-center gap-4 mb-8">
                        <div class="h-12 w-12 rounded-2xl bg-indigo-600 flex items-center justify-center shadow-lg shadow-indigo-200">
                            <i data-lucide="user-cog" class="text-white w-6 h-6"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-black text-slate-900 uppercase tracking-tight">Datos del Profesional</h3>
                            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Responsable</p>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
                            <div class="md:col-span-4">
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Tipo Doc.</label>
                                <select x-model="form.profesional.tipo_doc" class="w-full bg-slate-50 border border-slate-200 text-slate-700 text-sm rounded-xl font-bold uppercase p-3">
                                    <option value="DNI">DNI</option>
                                    <option value="CE">C.E.</option>
                                </select>
                            </div>
                            <div class="md:col-span-8 relative">
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Número Documento</label>
                                <div class="flex gap-2">
                                    <input type="text" x-model="form.profesional.doc" @keydown.enter.prevent="buscarProfesional()" placeholder="Ingrese DNI..." 
                                           class="flex-1 bg-slate-50 border border-slate-200 text-slate-900 text-sm rounded-xl p-3 font-bold tracking-wider">
                                    <button type="button" @click="buscarProfesional()" class="bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl px-4 py-2 transition-colors">
                                        <i data-lucide="search" class="w-5 h-5"></i>
                                    </button>
                                </div>
                                <span x-show="msgProfesional" x-text="msgProfesional" class="absolute -bottom-5 left-0 text-[10px] font-bold text-emerald-500"></span>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Apellido Paterno</label>
                                <input type="text" x-model="form.profesional.apellido_paterno" class="w-full bg-white border border-slate-200 rounded-xl p-3 font-bold uppercase">
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Apellido Materno</label>
                                <input type="text" x-model="form.profesional.apellido_materno" class="w-full bg-white border border-slate-200 rounded-xl p-3 font-bold uppercase">
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Nombres</label>
                                <input type="text" x-model="form.profesional.nombres" class="w-full bg-white border border-slate-200 rounded-xl p-3 font-bold uppercase">
                            </div>
                        </div>
                         
                         <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Email</label>
                                <input type="email" x-model="form.profesional.email" class="w-full bg-white border border-slate-200 rounded-xl p-3 font-medium lowercase">
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Teléfono</label>
                                <input type="text" x-model="form.profesional.telefono" class="w-full bg-white border border-slate-200 rounded-xl p-3 font-bold">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 3. CAPACITACIÓN --}}
            <div class="bg-white border border-slate-200 rounded-[2.5rem] p-8 shadow-xl shadow-slate-200/50 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-24 h-24 bg-indigo-50 rounded-full -mr-12 -mt-12 opacity-60 pointer-events-none"></div>
                <div class="relative z-10">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="h-12 w-12 rounded-2xl bg-indigo-600 flex items-center justify-center shadow-lg shadow-indigo-200">
                            <i data-lucide="graduation-cap" class="text-white w-6 h-6"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-black text-slate-900 uppercase tracking-tight">Capacitación</h3>
                            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Formación</p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <label class="block text-sm font-bold text-slate-700">¿El personal ha recibido capacitación?</label>
                        <div class="flex gap-4">
                            <label class="cursor-pointer flex-1">
                                <input type="radio" value="SI" x-model="form.capacitacion.recibieron_cap" class="peer sr-only">
                                <div class="text-center py-3 rounded-xl border-2 border-slate-100 bg-slate-50 text-slate-400 font-bold text-xs uppercase transition-all peer-checked:bg-indigo-50 peer-checked:text-indigo-600 peer-checked:border-indigo-500 hover:bg-white">SI</div>
                            </label>
                            <label class="cursor-pointer flex-1">
                                <input type="radio" value="NO" x-model="form.capacitacion.recibieron_cap" class="peer sr-only">
                                <div class="text-center py-3 rounded-xl border-2 border-slate-100 bg-slate-50 text-slate-400 font-bold text-xs uppercase transition-all peer-checked:bg-slate-100 peer-checked:text-slate-600 peer-checked:border-slate-300 hover:bg-white">NO</div>
                            </label>
                        </div>

                        <div x-show="form.capacitacion.recibieron_cap === 'SI'" x-transition class="mt-4">
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 pl-1">Entidad que capacitó</label>
                            <select x-model="form.capacitacion.institucion_cap" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 font-bold uppercase text-sm focus:ring-indigo-500">
                                <option value="" disabled>Seleccione...</option>
                                <option value="MINSA">MINSA</option>
                                <option value="DIRESA">DIRESA</option>
                                <option value="UNIDAD EJECUTORA">UNIDAD EJECUTORA</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-slate-100">
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">¿Firmó Declaración Jurada?</label>
                            <div class="flex gap-4">
                                <label class="cursor-pointer">
                                    <input type="radio" value="SI" x-model="form.capacitacion.decl_jurada" class="peer sr-only">
                                    <div class="px-4 py-2 rounded-lg border-2 border-slate-200 text-slate-400 font-bold text-xs peer-checked:bg-indigo-50 peer-checked:text-indigo-600 peer-checked:border-indigo-500 transition-all hover:bg-slate-50">SÍ</div>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="radio" value="NO" x-model="form.capacitacion.decl_jurada" class="peer sr-only">
                                    <div class="px-4 py-2 rounded-lg border-2 border-slate-200 text-slate-400 font-bold text-xs peer-checked:bg-slate-100 peer-checked:text-slate-600 peer-checked:border-slate-400 transition-all hover:bg-slate-50">NO</div>
                                </label>
                            </div>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">¿Firmó Compromiso Confidencialidad?</label>
                            <div class="flex gap-4">
                                <label class="cursor-pointer">
                                    <input type="radio" value="SI" x-model="form.capacitacion.comp_confidencialidad" class="peer sr-only">
                                    <div class="px-4 py-2 rounded-lg border-2 border-slate-200 text-slate-400 font-bold text-xs peer-checked:bg-indigo-50 peer-checked:text-indigo-600 peer-checked:border-indigo-500 transition-all hover:bg-slate-50">SÍ</div>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="radio" value="NO" x-model="form.capacitacion.comp_confidencialidad" class="peer sr-only">
                                    <div class="px-4 py-2 rounded-lg border-2 border-slate-200 text-slate-400 font-bold text-xs peer-checked:bg-slate-100 peer-checked:text-slate-600 peer-checked:border-slate-400 transition-all hover:bg-slate-50">NO</div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 4. INVENTARIO DE EQUIPAMIENTO --}}
            <div class="bg-white border border-slate-200 rounded-[2.5rem] p-8 shadow-xl shadow-slate-200/50 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-24 h-24 bg-indigo-50 rounded-full -mr-12 -mt-12 opacity-60 pointer-events-none"></div>
                <div class="flex items-center gap-4 mb-8">
                    <div class="h-12 w-12 rounded-2xl bg-indigo-600 flex items-center justify-center shadow-lg shadow-indigo-200">
                        <i data-lucide="monitor" class="text-white w-6 h-6"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-slate-900 uppercase tracking-tight">Inventario de Equipamiento</h3>
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Registro de Activos Fijos</p>
                    </div>
                </div>

                {{-- BARRA DE AGREGAR --}}
                <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200 mb-6 flex flex-col md:flex-row gap-4 items-end md:items-center">
                    <div class="flex-1 w-full">
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 pl-1">Seleccionar Equipo</label>
                        <select x-model="itemSeleccionado" class="w-full bg-white border border-slate-200 rounded-xl p-3 font-bold uppercase text-xs focus:ring-indigo-500 cursor-pointer">
                            <option value="">-- SELECCIONE UN ÍTEM --</option>
                            <template x-for="opcion in listaOpciones" :key="opcion">
                                <option :value="opcion" x-text="opcion"></option>
                            </template>
                        </select>
                    </div>
                    <button type="button" 
                            @click="agregarItem()" 
                            :disabled="!itemSeleccionado"
                            class="w-full md:w-auto bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-xl font-black uppercase text-xs tracking-widest shadow-md transition-all flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                        <i data-lucide="plus-circle" class="w-4 h-4"></i>
                        AGREGAR
                    </button>
                </div>

                {{-- TABLA --}}
                <div class="overflow-x-auto min-h-[150px]">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="text-[10px] text-slate-400 uppercase tracking-widest border-b border-slate-100">
                                <th class="pb-4 pl-2 min-w-[150px]">Descripción</th>
                                <th class="pb-4 w-40">Propiedad</th>
                                <th class="pb-4 w-32">Estado</th>
                                <th class="pb-4 w-40">Nro. Serie / Cód</th>
                                <th class="pb-4 min-w-[150px]">Observación</th>
                                <th class="pb-4 w-10"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            <tr x-show="form.inventario.length === 0">
                                <td colspan="6" class="py-8 text-center text-slate-300">
                                    <div class="flex flex-col items-center justify-center gap-2">
                                        <i data-lucide="inbox" class="w-8 h-8"></i>
                                        <span class="text-xs font-bold uppercase tracking-widest">Lista vacía</span>
                                    </div>
                                </td>
                            </tr>
                            <template x-for="(item, index) in form.inventario" :key="item.id">
                                <tr class="group hover:bg-slate-50/50 transition-colors">
                                    <td class="py-3 pl-2 align-middle">
                                        <span class="font-black text-slate-700 text-xs uppercase bg-indigo-50 text-indigo-600 px-3 py-1.5 rounded-lg" x-text="item.descripcion"></span>
                                    </td>
                                    <td class="py-3 px-2 align-middle">
                                        <select x-model="item.propiedad" class="w-full bg-white border border-slate-200 rounded-lg p-2 text-[10px] font-bold uppercase focus:ring-indigo-500">
                                            <option value="COMPARTIDO">COMPARTIDO</option>
                                            <option value="EXCLUSIVO">EXCLUSIVO</option>
                                            <option value="PERSONAL">PERSONAL</option>
                                        </select>
                                    </td>
                                    <td class="py-3 px-2 align-middle">
                                        <select x-model="item.estado" class="w-full bg-white border border-slate-200 rounded-lg p-2 text-[10px] font-bold uppercase focus:ring-indigo-500">
                                            <option value="OPERATIVO">OPERATIVO</option>
                                            <option value="REGULAR">REGULAR</option>
                                            <option value="INOPERATIVO">INOPERATIVO</option>
                                        </select>
                                    </td>

                                    {{-- SELECTOR COMPUESTO (NS/CB + INPUT) --}}
                                    <td class="py-3 px-2 align-middle">
                                        <div class="flex items-center group/input">
                                            <div class="relative">
                                                <select x-model="item.tipo_codigo" 
                                                        class="appearance-none bg-slate-100 border border-slate-200 border-r-0 rounded-l-lg py-2 pl-3 pr-6 text-[10px] font-black text-slate-600 uppercase focus:ring-0 focus:border-indigo-500 cursor-pointer hover:bg-slate-200 transition-colors w-[60px]">
                                                    <option value="NS">NS</option>
                                                    <option value="CB">CB</option>
                                                </select>
                                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-1 text-slate-500">
                                                <i data-lucide="chevron-down" class="w-3 h-3"></i>
                                                </div>
                                            </div>

                                            <div class="relative flex-1">
                                                <input type="text" x-model="item.codigo" placeholder="---" 
                                                    class="w-full bg-white border border-slate-200 rounded-r-lg py-2 px-3 text-[10px] font-bold uppercase focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 relative">
                                            </div>
                                        </div>
                                    </td>

                                    <td class="py-3 px-2 align-middle">
                                        <input type="text" x-model="item.observacion" placeholder="Sin obs."
                                               class="w-full bg-white border border-slate-200 rounded-lg p-2 text-[10px] font-medium uppercase focus:ring-indigo-500">
                                    </td>
                                    <td class="py-3 pr-2 align-middle text-right">
                                        <button type="button" @click="eliminarItem(index)" class="text-red-500 bg-red-50 hover:bg-red-100 hover:text-red-700 border border-red-200 transition-all p-2 rounded-lg shadow-sm" title="Quitar ítem">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- 5. DIFICULTADES CON EL SISTEMA --}}
            <div class="bg-white border border-slate-200 rounded-[2.5rem] p-8 shadow-xl shadow-slate-200/50">
                <div class="flex items-center gap-4 mb-6">
                    <div class="h-12 w-12 rounded-2xl bg-indigo-600 flex items-center justify-center shadow-lg shadow-indigo-200">
                        <i data-lucide="alert-circle" class="text-white w-6 h-6"></i>
                    </div>
                    <h3 class="text-lg font-black text-slate-900 uppercase tracking-tight">Dificultades con el Sistema</h3>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Institución con la que coordina</label>
                        <select x-model="form.dificultades.institucion" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 font-bold uppercase text-sm focus:ring-indigo-500">
                            <option value="">Seleccione una entidad...</option>
                            <option value="MINSA">MINSA</option>
                            <option value="DIRESA">DIRESA</option>
                            <option value="UNIDAD EJECUTORA">UNIDAD EJECUTORA</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Medio de comunicación</label>
                        <select x-model="form.dificultades.medio" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 font-bold uppercase text-sm focus:ring-indigo-500">
                            <option value="">Seleccione una opción...</option>
                            <option value="WHATSAPP">WHATSAPP</option>
                            <option value="ANYDESK">ANYDESK</option>
                            <option value="CELULAR">CELULAR</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- 6. SECCIÓN DNI --}}
            <div class="bg-white border border-slate-200 rounded-[2.5rem] p-8 shadow-xl shadow-slate-200/50 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-24 h-24 bg-indigo-50 rounded-full -mr-12 -mt-12 opacity-60 pointer-events-none"></div>
                
                <div class="flex items-center gap-4 mb-8">
                    <div class="h-12 w-12 rounded-2xl bg-indigo-600 flex items-center justify-center shadow-lg shadow-indigo-200">
                        <i data-lucide="id-card" class="text-white w-6 h-6"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-slate-900 uppercase tracking-tight">Sección DNI</h3>
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Configuración de Identidad Digital</p>
                    </div>
                </div>

                <div class="space-y-8">
                    {{-- 1. TIPO DE DNI --}}
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Tipo de DNI</label>
                        <div class="flex flex-col md:flex-row gap-4">
                            <label class="cursor-pointer flex-1 relative">
                                <input type="radio" value="DNI_ELECTRONICO" x-model="form.seccion_dni.tipo_dni" class="peer sr-only">
                                <div class="text-center py-3 rounded-xl border-2 border-slate-100 bg-slate-50 text-slate-400 font-bold text-xs uppercase transition-all peer-checked:bg-indigo-50 peer-checked:text-indigo-600 peer-checked:border-indigo-500 hover:bg-white">DNI Electrónico</div>
                            </label>
                            <label class="cursor-pointer flex-1 relative">
                                <input type="radio" value="DNI_AZUL" x-model="form.seccion_dni.tipo_dni" class="peer sr-only">
                                <div class="text-center py-3 rounded-xl border-2 border-slate-100 bg-slate-50 text-slate-400 font-bold text-xs uppercase transition-all peer-checked:bg-indigo-50 peer-checked:text-indigo-600 peer-checked:border-indigo-500 hover:bg-white">DNI Azul</div>
                            </label>
                        </div>
                    </div>

                    {{-- 2. BLOQUE CONDICIONAL: VERSIÓN + FIRMA --}}
                    <div x-show="form.seccion_dni.tipo_dni === 'DNI_ELECTRONICO'" 
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 -translate-y-2"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         class="bg-slate-50/50 border border-slate-100 rounded-2xl p-6 grid grid-cols-1 md:grid-cols-2 gap-8">
                        
                        {{-- Versión DNIe --}}
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Versión DNIe</label>
                            <select x-model="form.seccion_dni.version_dnie" class="w-full bg-white border border-slate-200 rounded-xl p-3 font-bold uppercase text-sm focus:ring-indigo-500">
                                <option value="" selected disabled>Seleccione Versión...</option>
                                <option value="v1">Versión 1.0</option>
                                <option value="v2">Versión 2.0</option>
                                <option value="v3">Versión 3.0</option>
                            </select>
                        </div>

                        {{-- Firma SIHCE --}}
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">¿Realiza firma electrónica en SIHCE?</label>
                            <div class="flex gap-4">
                                <label class="cursor-pointer flex-1">
                                    <input type="radio" value="SI" x-model="form.seccion_dni.firma_sihce" class="peer sr-only">
                                    <div class="text-center py-3 rounded-xl border-2 border-slate-200 bg-white text-slate-400 font-bold text-xs uppercase transition-all peer-checked:bg-indigo-50 peer-checked:text-indigo-600 peer-checked:border-indigo-500">SÍ</div>
                                </label>
                                <label class="cursor-pointer flex-1">
                                    <input type="radio" value="NO" x-model="form.seccion_dni.firma_sihce" class="peer sr-only">
                                    <div class="text-center py-3 rounded-xl border-2 border-slate-200 bg-white text-slate-400 font-bold text-xs uppercase transition-all peer-checked:bg-slate-100 peer-checked:text-slate-600 peer-checked:border-slate-300">NO</div>
                                </label>
                            </div>
                        </div>
                    </div>

                    {{-- 3. COMENTARIOS --}}
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Comentarios / Observaciones</label>
                        <textarea x-model="form.seccion_dni.comentarios" rows="2" placeholder="Ingrese observaciones generales del acta" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 font-medium text-sm focus:ring-indigo-500"></textarea>
                    </div>
                </div>
            </div>

            {{-- 7. EVIDENCIA FOTOGRÁFICA --}}
            <div class="bg-white border border-slate-200 rounded-[2.5rem] p-8 shadow-xl shadow-slate-200/50">
                <div class="flex items-center gap-4 mb-6">
                    <div class="h-12 w-12 rounded-2xl bg-indigo-600 flex items-center justify-center shadow-lg shadow-indigo-200">
                        <i data-lucide="camera" class="text-white w-6 h-6"></i>
                    </div>
                    <h3 class="text-lg font-black text-slate-900 uppercase tracking-tight">Evidencia Fotográfica</h3>
                </div>

                {{-- ZONA DE CARGA --}}
                <div class="border-2 border-dashed border-slate-300 rounded-2xl p-10 flex flex-col items-center justify-center text-center hover:bg-slate-50 transition-colors relative cursor-pointer">
                    <input type="file" multiple @change="handleFiles" accept="image/png, image/jpeg, image/jpg" class="absolute inset-0 opacity-0 cursor-pointer">
                    <i data-lucide="cloud-upload" class="w-10 h-10 text-indigo-400 mb-3"></i>
                    <p class="text-indigo-600 font-bold uppercase text-sm">Clic para subir o arrastrar archivos</p>
                    <p class="text-xs text-slate-400 mt-1">PNG, JPG o JPEG (Máx. 5MB)</p>
                </div>
                
                <div class="mt-4 space-y-2" x-show="files.length > 0">
                    <template x-for="(file, i) in files" :key="i">
                        <div class="flex items-center justify-between bg-slate-50 px-4 py-2 rounded-lg border border-slate-100">
                            <span class="text-xs font-bold text-slate-600 truncate" x-text="file.name"></span>
                            <button type="button" @click="removeFile(i)" class="text-red-400 hover:text-red-600">
                                <i data-lucide="x" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </template>
                </div>

                <div class="mt-6 border-t border-slate-100 pt-6" x-show="oldFiles.length > 0">
                    <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Galería Guardada</h4>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <template x-for="(foto, index) in oldFiles" :key="foto.id">
                            <div class="relative group aspect-square bg-slate-100 rounded-xl overflow-hidden border border-slate-200 shadow-sm">
                                <img :src="'/storage/' + foto.url_foto" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                                <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-all flex flex-col items-center justify-center gap-3 backdrop-blur-[2px]">
                                    <a :href="'/storage/' + foto.url_foto" target="_blank" class="flex items-center gap-2 text-white text-[10px] font-bold uppercase tracking-widest hover:text-indigo-300 transition-colors">
                                        <i data-lucide="eye" class="w-4 h-4"></i> Ver
                                    </a>
                                    <button type="button" @click="eliminarFotoGuardada(foto.id, index)" class="bg-red-500 hover:bg-red-600 text-white p-2 rounded-full shadow-lg transition-all transform hover:scale-110" title="Eliminar foto permanentemente">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
            
            {{-- BOTÓN GUARDAR --}}
            <div class="fixed bottom-6 right-6 z-50 md:static md:flex md:justify-end mt-10">
                <button type="submit" :disabled="saving" class="bg-indigo-600 hover:bg-indigo-700 text-white px-10 py-4 rounded-2xl font-black uppercase tracking-widest shadow-xl flex items-center gap-3 transition-all transform hover:scale-105 disabled:opacity-70 disabled:scale-100">
                    <i x-show="!saving" data-lucide="save" class="w-5 h-5"></i>
                    <i x-show="saving" data-lucide="loader-2" class="w-5 h-5 animate-spin"></i>
                    <span x-text="saving ? 'Guardando...' : 'Guardar Cambios'"></span>
                </button>
            </div>

        </form>
    </div>
</div>

<script>
    function psicologiaForm() {
        // DATOS BD (Inyectados por Laravel)
        const dbCapacitacion  = @json($dbCapacitacion ?? null);
        const dbInventario    = @json($dbInventario ?? []);
        const dbDificultad    = @json($dbDificultad ?? null);
        const dbFotos         = @json($dbFotos ?? []);
        const dbInicioLabores = @json($dbInicioLabores ?? null);
        const dbDni           = @json($dbDni ?? null); 

        // --- 1. Inicialización ---
        let initProfesional = { tipo_doc: 'DNI', doc: '', nombres: '', apellido_paterno: '', apellido_materno: '', email: '', telefono: '' };
        let initCapacitacion = { recibieron_cap: '', institucion_cap: '', decl_jurada: '', comp_confidencialidad: ''};

        if (dbCapacitacion) {
            initCapacitacion.recibieron_cap = dbCapacitacion.recibieron_cap || '';
            initCapacitacion.institucion_cap = dbCapacitacion.institucion_cap || '';
            initCapacitacion.decl_jurada = dbCapacitacion.decl_jurada || ''; 
            initCapacitacion.comp_confidencialidad = dbCapacitacion.comp_confidencialidad || '';
            if (dbCapacitacion.profesional) {
                initProfesional = { ...dbCapacitacion.profesional };
            }
        }

        // --- 2. Inicio Labores (ESPECÍFICO DE PSICOLOGÍA) ---
        // Se inicializan todas, pero en la vista solo usamos fua y referencia
        let initInicioLabores = { consultorios: '', nombre_consultorio: '', turno: '', fua: '', referencia: '', receta: '', orden_lab: '' };
        if (dbInicioLabores) {
            initInicioLabores.consultorios = dbInicioLabores.cant_consultorios || '';
            initInicioLabores.nombre_consultorio = dbInicioLabores.nombre_consultorio || '';
            initInicioLabores.turno = dbInicioLabores.turno || '';
            initInicioLabores.fua = dbInicioLabores.fua || '';
            initInicioLabores.referencia = dbInicioLabores.referencia || '';
            initInicioLabores.receta = dbInicioLabores.receta || '';
            initInicioLabores.orden_lab = dbInicioLabores.orden_laboratorio || '';
        }

        // --- 3. Sección DNI ---
        let initDni = { tipo_dni: '', version_dnie: '', firma_sihce: '', comentarios: '' };
        if (dbDni) {
            initDni.tipo_dni = dbDni.tip_dni || '';
            initDni.version_dnie = dbDni.version_dni || '';
            initDni.firma_sihce = dbDni.firma_sihce || '';
            initDni.comentarios = dbDni.comentarios || '';
        }

        // --- 4. Inventario (LOGICA SPLIT NS/CB APLICADA) ---
        let initInventario = [];
        if (dbInventario && dbInventario.length > 0) {
            initInventario = dbInventario.map(item => {
                let fullCode = item.nro_serie || '';
                let tipoDetectado = 'NS'; 
                let codigoLimpio = fullCode;

                if (fullCode.includes(' ')) {
                    let partes = fullCode.split(' ');
                    if (partes.length > 0 && (partes[0] === 'NS' || partes[0] === 'CB')) {
                        tipoDetectado = partes[0];
                        codigoLimpio = fullCode.substring(3); 
                    }
                }

                return {
                    id: Date.now() + Math.random(),
                    descripcion: item.descripcion,
                    propiedad: item.propio,       
                    estado: item.estado,
                    tipo_codigo: tipoDetectado, 
                    codigo: codigoLimpio,       
                    observacion: item.observacion
                };
            });
        }

        // --- 5. Dificultades ---
        let initDificultades = { institucion: '', medio: '' };
        if (dbDificultad) {
            initDificultades.institucion = dbDificultad.insti_comunica || '';
            initDificultades.medio = dbDificultad.medio_comunica || '';
        }

        return {
            saving: false,
            buscando: false,
            msgProfesional: '',
            files: [],
            oldFiles: dbFotos,
            listaOpciones: ['CPU', 'IMPRESORA', 'LAPTOP', 'LECTOR DE DNIe', 'MONITOR', 'MOUSE', 'SCANNER', 'TABLET', 'TECLADO', 'TICKETERA', 'ALL IN ONE'], 
            itemSeleccionado: '',

            form: {
                profesional: initProfesional,
                capacitacion: initCapacitacion,
                inicio_labores: initInicioLabores,
                seccion_dni: initDni,
                inventario: initInventario,
                dificultades: initDificultades,
            },

            // --- Funciones ---
            handleFiles(event) {
                const newFiles = Array.from(event.target.files).filter(file => file.type.startsWith('image/'));
                if (newFiles.length < event.target.files.length) alert("Solo se permiten imágenes.");
                this.files = [...this.files, ...newFiles];
                event.target.value = ''; 
            },

            removeFile(index) { this.files.splice(index, 1); },

            agregarItem() {
                if (!this.itemSeleccionado) return;
                this.form.inventario.push({
                    id: Date.now(),
                    descripcion: this.itemSeleccionado,
                    propiedad: 'COMPARTIDO',
                    estado: 'OPERATIVO',
                    tipo_codigo: 'NS', 
                    codigo: '',
                    observacion: ''
                });
                this.itemSeleccionado = '';
            },

            eliminarItem(index) { this.form.inventario.splice(index, 1); },

            eliminarFotoGuardada(id, index) {
                if(!confirm('¿Estás seguro de eliminar esta foto?')) return;
                fetch(`/usuario/monitoreo/modulo/consulta-psicologia/foto/${id}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                }).then(r=>r.json()).then(d=>{
                    if(d.success) this.oldFiles.splice(index,1);
                    else alert('Error: ' + d.message);
                });
            },

            async buscarProfesional() {
                let doc = this.form.profesional.doc;
                if (!doc || doc.length < 8) return;
                this.buscando = true;
                this.msgProfesional = "Buscando...";
                try {
                    let r = await fetch(`/usuario/monitoreo/modulo/consulta-psicologia/buscar-profesional/${doc}`);
                    let d = await r.json();
                    if (d.success) {
                        this.form.profesional = { ...this.form.profesional, ...d.data };
                        this.msgProfesional = "Encontrado.";
                    } else {
                        this.msgProfesional = "No encontrado.";
                        this.limpiarDatosPersonales();
                    }
                } catch (e) { this.msgProfesional = "Error."; } 
                finally { this.buscando = false; }
            },

            limpiarDatosPersonales() {
                this.form.profesional.nombres = '';
                this.form.profesional.apellido_paterno = '';
                this.form.profesional.apellido_materno = '';
                this.form.profesional.email = '';
                this.form.profesional.telefono = '';
            },

            guardarTodo() {
                this.saving = true;
                
                // 1. Clonar
                let formToSend = JSON.parse(JSON.stringify(this.form));

                // 2. Unir códigos Inventario (NS + 123)
                formToSend.inventario = formToSend.inventario.map(item => {
                    let tipo = item.tipo_codigo || 'NS';
                    let valor = item.codigo || '';
                    item.codigo = (tipo + ' ' + valor).trim(); 
                    return item;
                });

                // 3. Enviar
                let fd = new FormData();
                fd.append('data', JSON.stringify(formToSend));
                this.files.forEach(f => fd.append('fotos[]', f));

                fetch("{{ route('usuario.monitoreo.consulta-psicologia.store', $acta->id) }}", {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: fd
                }).then(r=>r.json()).then(d=>{
                    if(d.success) window.location.href = d.redirect || window.location.reload();
                    else { alert('Error: ' + JSON.stringify(d.message)); this.saving=false; }
                }).catch(e=>{ alert('Error técnico.'); console.error(e); this.saving=false; });
            }
        }
    }
</script>
@endsection