@extends('layouts.usuario')
@section('title', 'Módulo 07: Psicologia')

@section('content')
<div class="py-12 bg-[#f8fafc] min-h-screen" x-data="psicologiaForm()">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        
        {{-- ENCABEZADO --}}
        <div class="mb-10 flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="flex items-center gap-6">
                <div class="h-16 w-16 bg-white rounded-2xl flex items-center justify-center shadow-sm border border-slate-100">
                    <span class="text-2xl font-black text-indigo-600">03</span>
                </div>

                

                <div>
                    <div class="flex items-center gap-3 mb-1">
                        <span class="px-3 py-1 bg-indigo-100 text-indigo-700 text-[10px] font-black rounded-full uppercase tracking-widest">Módulo Técnico</span>
                        <span class="text-slate-400 font-bold text-[10px] uppercase tracking-wider">ID Acta: #{{ str_pad($acta->id, 5, '0', STR_PAD_LEFT) }}</span>
                    </div>
                    <h2 class="text-3xl font-black text-slate-900 uppercase tracking-tight italic">Módulo Psicologia</h2>
                </div>
            </div>
            
            {{-- Enlace de PDF Temporal --}}
            <a href="{{ route('usuario.monitoreo.consulta-psicologia.pdf', $acta->id) }}" target="_blank" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl font-black text-xs uppercase tracking-widest shadow-md transition-colors flex items-center gap-2">
                    <i data-lucide="file-text" class="w-4 h-4"></i> Exportar PDF
            </a>

            <a href="{{ route('usuario.monitoreo.modulos', $acta->id) }}" class="px-6 py-3 bg-white border border-slate-200 rounded-2xl text-slate-500 font-black text-xs uppercase tracking-widest shadow-sm hover:bg-slate-50 transition-colors">
                Volver
            </a>
        </div>

        {{-- FORMULARIO ÚNICO --}}
        <form @submit.prevent="guardarTodo" class="space-y-8">

            {{-- 1. DATOS DEL PROFESIONAL (Color: Índigo) --}}
            <div class="bg-white border border-slate-200 rounded-[2.5rem] p-8 shadow-xl shadow-slate-200/50 relative overflow-hidden">
                {{-- Decoración --}}
                <div class="absolute top-0 right-0 w-32 h-32 bg-indigo-50 rounded-full -mr-16 -mt-16 opacity-60 pointer-events-none"></div>
                
                <div class="relative z-10">
                    <div class="flex items-center gap-4 mb-8">
                        {{-- Icono Unificado --}}
                        <div class="h-12 w-12 rounded-2xl bg-indigo-600 flex items-center justify-center shadow-lg shadow-indigo-200">
                            <i data-lucide="user-cog" class="text-white w-6 h-6"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-black text-slate-900 uppercase tracking-tight">Datos del Profesional</h3>
                            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Responsable</p>
                        </div>
                    </div>

                    <div class="space-y-6">
                        {{-- Buscador DNI --}}
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

                        {{-- Datos Personales --}}
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
                         {{-- Contacto --}}
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

            {{-- 2. CAPACITACIÓN (Color unificado: Índigo) --}}
            <div class="bg-white border border-slate-200 rounded-[2.5rem] p-8 shadow-xl shadow-slate-200/50 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-24 h-24 bg-indigo-50 rounded-full -mr-12 -mt-12 opacity-60 pointer-events-none"></div>
                <div class="relative z-10">
                    <div class="flex items-center gap-4 mb-6">
                        {{-- Icono Unificado --}}
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
                                {{-- Estilos Checked en Índigo --}}
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
                                <option value="UUEE">UUEE</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>


            {{-- 3. INVENTARIO DE EQUIPAMIENTO --}}
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

                {{-- BARRA DE AGREGAR (SIN RESTRICCIONES) --}}
                <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200 mb-6 flex flex-col md:flex-row gap-4 items-end md:items-center">
                    <div class="flex-1 w-full">
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 pl-1">Seleccionar Equipo</label>
                        <select x-model="itemSeleccionado" class="w-full bg-white border border-slate-200 rounded-xl p-3 font-bold uppercase text-xs focus:ring-indigo-500 cursor-pointer">
                            <option value="">-- SELECCIONE UN ÍTEM --</option>
                            <template x-for="opcion in listaOpciones" :key="opcion">
                                {{-- Quitamos el :disabled para permitir múltiples selecciones --}}
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
                                <th class="pb-4 w-40">Cód. Barras</th>
                                <th class="pb-4 min-w-[150px]">Observación</th>
                                <th class="pb-4 w-10"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            
                            {{-- Mensaje Vacío (Chequeamos el length del array) --}}
                            <tr x-show="form.inventario.length === 0">
                                <td colspan="6" class="py-8 text-center text-slate-300">
                                    <div class="flex flex-col items-center justify-center gap-2">
                                        <i data-lucide="inbox" class="w-8 h-8"></i>
                                        <span class="text-xs font-bold uppercase tracking-widest">Lista vacía</span>
                                    </div>
                                </td>
                            </tr>

                            {{-- Filas (Iteramos Array) --}}
                            <template x-for="(item, index) in form.inventario" :key="item.id">
                                <tr class="group hover:bg-slate-50/50 transition-colors">
                                    
                                    {{-- Descripción (Leemos item.descripcion) --}}
                                    <td class="py-3 pl-2 align-middle">
                                        <span class="font-black text-slate-700 text-xs uppercase bg-indigo-50 text-indigo-600 px-3 py-1.5 rounded-lg" x-text="item.descripcion"></span>
                                        {{-- Mostrar índice opcional para diferenciar: #1, #2... --}}
                                        <span class="text-[9px] text-slate-300 font-bold ml-1" x-text="'#' + (index + 1)"></span>
                                    </td>

                                    {{-- Propiedad --}}
                                    <td class="py-3 px-2 align-middle">
                                        <select x-model="item.propiedad" class="w-full bg-white border border-slate-200 rounded-lg p-2 text-[10px] font-bold uppercase focus:ring-indigo-500">
                                            <option value="ESTABLECIMIENTO">ESTABLECIMIENTO</option>
                                            <option value="PERSONAL">PERSONAL</option>
                                        </select>
                                    </td>

                                    {{-- Estado --}}
                                    <td class="py-3 px-2 align-middle">
                                        <select x-model="item.estado" class="w-full bg-white border border-slate-200 rounded-lg p-2 text-[10px] font-bold uppercase focus:ring-indigo-500">
                                            <option value="BUENO">BUENO</option>
                                            <option value="REGULAR">REGULAR</option>
                                            <option value="MALO">MALO</option>
                                        </select>
                                    </td>

                                    {{-- Código --}}
                                    <td class="py-3 px-2 align-middle">
                                        <div class="relative">
                                            <input type="text" x-model="item.codigo" placeholder="---" 
                                                   class="w-full bg-white border border-slate-200 rounded-lg py-2 pl-7 pr-2 text-[10px] font-bold uppercase focus:ring-indigo-500">
                                            <i data-lucide="scan-barcode" class="absolute left-2 top-2.5 w-3.5 h-3.5 text-slate-400"></i>
                                        </div>
                                    </td>

                                    {{-- Observación --}}
                                    <td class="py-3 px-2 align-middle">
                                        <input type="text" x-model="item.observacion" placeholder="Sin obs."
                                               class="w-full bg-white border border-slate-200 rounded-lg p-2 text-[10px] font-medium uppercase focus:ring-indigo-500">
                                    </td>

                                    {{-- Eliminar (Pasamos el index) --}}
                                    <td class="py-3 pr-2 align-middle text-right">
                                        <button type="button" 
                                                @click="eliminarItem(index)" 
                                                class="text-red-500 bg-red-50 hover:bg-red-100 hover:text-red-700 border border-red-200 transition-all p-2 rounded-lg shadow-sm" 
                                                title="Quitar ítem">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                {{-- Comentarios Generales --}}
                <div class="mt-4 border-t border-slate-100 pt-4">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 pl-1">Comentarios Generales</label>
                    <textarea x-model="form.inventario_comentarios" rows="2" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 text-sm focus:ring-indigo-500"></textarea>
                </div>
            </div>
            

            {{-- 4. DIFICULTADES CON EL SISTEMA (Color unificado: Índigo) --}}
            <div class="bg-white border border-slate-200 rounded-[2.5rem] p-8 shadow-xl shadow-slate-200/50">
                <div class="flex items-center gap-4 mb-6">
                    {{-- Icono Unificado --}}
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
                            <option value="UUEE">UUEE</option>
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


            {{--  SECCIÓN INICIO LABORES --}}
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
                    {{-- Cantidad Consultorios --}}
                    <div class="max-w-xs">
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Cantidad de Consultorios</label>
                        <div class="relative">
                            <input type="number" x-model="form.inicio_labores.consultorios" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 pl-4 font-bold text-sm focus:ring-indigo-500">
                        </div>
                    </div>

                    {{-- Grid de Opciones --}}
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

                            {{-- Receta --}}
                            <div class="bg-slate-50 p-5 rounded-2xl border border-slate-100">
                                <span class="block text-[10px] font-black text-slate-400 uppercase mb-3 tracking-widest">Receta</span>
                                <div class="space-y-3">
                                    <label class="flex items-center gap-3 cursor-pointer group">
                                        <input type="radio" value="SIHCE" x-model="form.inicio_labores.receta" class="w-4 h-4 text-indigo-600 focus:ring-indigo-500 border-slate-300">
                                        <span class="text-xs font-bold text-slate-600 group-hover:text-indigo-600 transition-colors uppercase">Receta por SIHCE</span>
                                    </label>
                                    <label class="flex items-center gap-3 cursor-pointer group">
                                        <input type="radio" value="MANUAL" x-model="form.inicio_labores.receta" class="w-4 h-4 text-indigo-600 focus:ring-indigo-500 border-slate-300">
                                        <span class="text-xs font-bold text-slate-600 group-hover:text-indigo-600 transition-colors uppercase">Receta Manual</span>
                                    </label>
                                </div>
                            </div>

                            {{-- Orden Laboratorio --}}
                            <div class="bg-slate-50 p-5 rounded-2xl border border-slate-100">
                                <span class="block text-[10px] font-black text-slate-400 uppercase mb-3 tracking-widest">Orden Laboratorio</span>
                                <div class="space-y-3">
                                    <label class="flex items-center gap-3 cursor-pointer group">
                                        <input type="radio" value="SIHCE" x-model="form.inicio_labores.orden_lab" class="w-4 h-4 text-indigo-600 focus:ring-indigo-500 border-slate-300">
                                        <span class="text-xs font-bold text-slate-600 group-hover:text-indigo-600 transition-colors uppercase">Orden por SIHCE</span>
                                    </label>
                                    <label class="flex items-center gap-3 cursor-pointer group">
                                        <input type="radio" value="MANUAL" x-model="form.inicio_labores.orden_lab" class="w-4 h-4 text-indigo-600 focus:ring-indigo-500 border-slate-300">
                                        <span class="text-xs font-bold text-slate-600 group-hover:text-indigo-600 transition-colors uppercase">Orden Manual</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            {{-- 4. [NUEVO] SECCIÓN DNI --}}
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
                    
                    {{-- 1. TIPO DE DNI (Siempre visible) --}}
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
                    {{-- Solo aparece si es DNI Electrónico. Agrupa ambos campos en una cuadrícula --}}
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

                    {{-- 3. COMENTARIOS (Siempre visible) --}}
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Comentarios / Observaciones</label>
                        <textarea x-model="form.seccion_dni.comentarios" rows="2" placeholder="Ingrese observaciones adicionales sobre el DNI o firma..." class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 font-medium text-sm focus:ring-indigo-500"></textarea>
                    </div>
                </div>
            </div>


            {{-- 5. EVIDENCIA FOTOGRÁFICA (Color unificado: Índigo) --}}
            <div class="bg-white border border-slate-200 rounded-[2.5rem] p-8 shadow-xl shadow-slate-200/50">
                <div class="flex items-center gap-4 mb-6">
                    {{-- Icono Unificado --}}
                    <div class="h-12 w-12 rounded-2xl bg-indigo-600 flex items-center justify-center shadow-lg shadow-indigo-200">
                        <i data-lucide="camera" class="text-white w-6 h-6"></i>
                    </div>
                    <h3 class="text-lg font-black text-slate-900 uppercase tracking-tight">Evidencia Fotográfica</h3>
                </div>

                {{-- ZONA DE CARGA (DRAG & DROP) --}}
                <div class="border-2 border-dashed border-slate-300 rounded-2xl p-10 flex flex-col items-center justify-center text-center hover:bg-slate-50 transition-colors relative cursor-pointer">
                    <input type="file" multiple @change="handleFiles" class="absolute inset-0 opacity-0 cursor-pointer">
                    <i data-lucide="cloud-upload" class="w-10 h-10 text-indigo-400 mb-3"></i>
                    <p class="text-indigo-600 font-bold uppercase text-sm">Clic para subir o arrastrar archivos</p>
                    <p class="text-xs text-slate-400 mt-1">PNG, JPG o JPEG (Máx. 5MB)</p>
                </div>
                
                {{-- LISTA DE ARCHIVOS NUEVOS (Recién seleccionados) --}}
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

                {{-- GALERÍA DE FOTOS GUARDADAS (Vienen de la Base de Datos) --}}
                <div class="mt-6 border-t border-slate-100 pt-6" x-show="oldFiles.length > 0">
                    <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Galería Guardada</h4>
                    
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <template x-for="(foto, index) in oldFiles" :key="foto.id">
                            <div class="relative group aspect-square bg-slate-100 rounded-xl overflow-hidden border border-slate-200 shadow-sm">
                                
                                {{-- Imagen --}}
                                <img :src="'/storage/' + foto.url_foto" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                                
                                {{-- Overlay con Acciones --}}
                                <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-all flex flex-col items-center justify-center gap-3 backdrop-blur-[2px]">
                                    
                                    {{-- Botón Ver --}}
                                    <a :href="'/storage/' + foto.url_foto" target="_blank" class="flex items-center gap-2 text-white text-[10px] font-bold uppercase tracking-widest hover:text-indigo-300 transition-colors">
                                        <i data-lucide="eye" class="w-4 h-4"></i> Ver
                                    </a>

                                    {{-- Botón Eliminar --}}
                                    <button type="button" 
                                            @click="eliminarFotoGuardada(foto.id, index)" 
                                            class="bg-red-500 hover:bg-red-600 text-white p-2 rounded-full shadow-lg transition-all transform hover:scale-110" 
                                            title="Eliminar foto permanentemente">
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
        // DATOS BD
        const dbCapacitacion  = @json($dbCapacitacion ?? null);
        const dbInventario    = @json($dbInventario ?? []);
        const dbDificultad    = @json($dbDificultad ?? null);
        const dbFotos         = @json($dbFotos ?? []);
        const dbInicioLabores = @json($dbInicioLabores ?? null);
        // NUEVO: Variable DNI
        const dbDni           = @json($dbDni ?? null);

        // --- Inicializaciones ---
        let initProfesional = {
            tipo_doc: 'DNI', doc: '', nombres: '', apellido_paterno: '', apellido_materno: '', email: '', telefono: ''
        };
        let initCapacitacion = { recibieron_cap: '', institucion_cap: '' };

        if (dbCapacitacion) {
            initCapacitacion.recibieron_cap = dbCapacitacion.recibieron_cap || '';
            initCapacitacion.institucion_cap = dbCapacitacion.institucion_cap || '';
            if (dbCapacitacion.profesional) {
                initProfesional = {
                    tipo_doc: dbCapacitacion.profesional.tipo_doc,
                    doc: dbCapacitacion.profesional.doc,
                    nombres: dbCapacitacion.profesional.nombres,
                    apellido_paterno: dbCapacitacion.profesional.apellido_paterno,
                    apellido_materno: dbCapacitacion.profesional.apellido_materno,
                    email: dbCapacitacion.profesional.email,
                    telefono: dbCapacitacion.profesional.telefono
                };
            }
        }

        let initInicioLabores = { consultorios: '', fua: '', referencia: '', receta: '', orden_lab: '' };
        if (dbInicioLabores) {
            initInicioLabores.consultorios = dbInicioLabores.cant_consultorios || '';
            initInicioLabores.fua = dbInicioLabores.fua || '';
            initInicioLabores.referencia = dbInicioLabores.referencia || '';
            initInicioLabores.receta = dbInicioLabores.receta || '';
            initInicioLabores.orden_lab = dbInicioLabores.orden_laboratorio || '';
        }

        // --- Inicializar Sección DNI (NUEVO) ---
        let initDni = { 
            tipo_dni: '', 
            version_dnie: '', 
            firma_sihce: '', 
            comentarios: '' 
        };

        if (dbDni) {
            initDni.tipo_dni = dbDni.tip_dni || '';
            initDni.version_dnie = dbDni.version_dni || '';
            initDni.firma_sihce = dbDni.firma_sihce || '';
            initDni.comentarios = dbDni.comentarios || '';
        }

        // Resto de inicializaciones...
        let initInventario = [];
        let initComentariosInv = '';
        if (dbInventario && dbInventario.length > 0) {
            initComentariosInv = dbInventario[0].comentarios || '';
            initInventario = dbInventario.map(item => ({
                id: Date.now() + Math.random(),
                descripcion: item.descripcion,
                propiedad: item.propiedad,
                estado: item.estado,
                codigo: item.cod_barras || '',
                observacion: item.observaciones
            }));
        }

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

            listaOpciones: ['MONITOR', 'CPU', 'TECLADO', 'MOUSE', 'IMPRESORA', 'LECTORA DE DNIe', 'TICKETERA'], 
            itemSeleccionado: '',

            form: {
                profesional: initProfesional,
                capacitacion: initCapacitacion,
                inicio_labores: initInicioLabores,
                // Agregamos la nueva sección
                seccion_dni: initDni,
                inventario: initInventario,
                inventario_comentarios: initComentariosInv,
                dificultades: initDificultades,
            },

            // ... (Resto de funciones: handleFiles, guardarTodo, etc. se mantienen IGUAL) ...
            
            handleFiles(event) {
                this.files = [...this.files, ...Array.from(event.target.files)];
            },
            removeFile(index) {
                this.files.splice(index, 1);
            },
            agregarItem() {
                if (!this.itemSeleccionado) return;
                this.form.inventario.push({
                    id: Date.now(),
                    descripcion: this.itemSeleccionado,
                    propiedad: 'ESTABLECIMIENTO',
                    estado: 'BUENO',
                    codigo: '',
                    observacion: ''
                });
                this.itemSeleccionado = '';
            },
            eliminarItem(index) {
                this.form.inventario.splice(index, 1);
            },
            limpiarDatosPersonales() {
                this.form.profesional.nombres = '';
                this.form.profesional.apellido_paterno = '';
                this.form.profesional.apellido_materno = '';
                this.form.profesional.email = '';
                this.form.profesional.telefono = '';
            },
            async buscarProfesional() {
                let doc = this.form.profesional.doc;
                if (!doc || doc.length < 8) return;
                this.buscando = true;
                this.msgProfesional = "Buscando...";
                try {
                    let response = await fetch(`/usuario/monitoreo/modulo/consulta-psicologia/buscar-profesional/${doc}`);
                    let data = await response.json();
                    if (data.success) {
                        this.form.profesional = { ...this.form.profesional, ...data.data };
                        this.msgProfesional = "Encontrado.";
                    } else {
                        this.limpiarDatosPersonales();
                        this.msgProfesional = "No encontrado.";
                    }
                } catch (error) { this.msgProfesional = "Error."; } 
                finally { this.buscando = false; }
            },
            eliminarFotoGuardada(id, index) {
                fetch(`/usuario/monitoreo/modulo/consulta-psicologia/foto/${id}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' }
                })
                .then(r => r.json())
                .then(d => {
                    if (d.success) this.oldFiles.splice(index, 1);
                    else alert('Error: ' + d.message);
                });
            },
            guardarTodo() {
                this.saving = true;
                let formData = new FormData();
                formData.append('data', JSON.stringify(this.form));
                this.files.forEach(file => formData.append('fotos[]', file));

                fetch("{{ route('usuario.monitoreo.consulta-psicologia.store', $acta->id) }}", {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: formData
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        if (data.redirect) window.location.href = data.redirect;
                        else window.location.reload();
                    } else {
                        this.saving = false;
                        alert('Error: ' + JSON.stringify(data.message));
                    }
                })
                .catch(error => {
                    this.saving = false;
                    alert('Error técnico.');
                    console.error(error);
                });
            }
        }
    }
</script>



@endsection