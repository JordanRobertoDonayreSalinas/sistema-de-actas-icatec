@extends('layouts.usuario')
@section('title', 'Módulo 03: Triaje')

@section('content')
<div class="py-12 bg-[#f8fafc] min-h-screen" x-data="triajeForm()">
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
                    <h2 class="text-3xl font-black text-slate-900 uppercase tracking-tight italic">Módulo Triaje</h2>
                </div>
            </div>
            
            <a href="{{ route('usuario.monitoreo.modulos', $acta->id) }}" class="px-6 py-3 bg-white border border-slate-200 rounded-2xl text-slate-500 font-black text-xs uppercase tracking-widest shadow-sm hover:bg-slate-50 transition-colors">
                Volver
            </a>
        </div>

        {{-- FORMULARIO ÚNICO --}}
        <form @submit.prevent="guardarTodo" class="space-y-8">

            {{-- 1. DATOS DEL PROFESIONAL --}}
            <div class="bg-white border border-slate-200 rounded-[2.5rem] p-8 shadow-xl shadow-slate-200/50 relative overflow-hidden">
                {{-- Decoración --}}
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

            {{-- 2. CAPACITACIÓN --}}
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
                    
                    {{-- NUEVOS CAMPOS: Declaración y Confidencialidad --}}
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
                                    <td class="py-3 px-2 align-middle">
                                        <div class="relative">
                                            <input type="text" x-model="item.codigo" placeholder="---" 
                                                   class="w-full bg-white border border-slate-200 rounded-lg py-2 pl-7 pr-2 text-[10px] font-bold uppercase focus:ring-indigo-500">
                                            <i data-lucide="scan-barcode" class="absolute left-2 top-2.5 w-3.5 h-3.5 text-slate-400"></i>
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
                {{-- AQUÍ SE ELIMINÓ EL DIV DE COMENTARIOS GENERALES --}}
            </div>
            

            {{-- 4. DIFICULTADES CON EL SISTEMA --}}
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
                    {{-- Grid: Cantidad y Nombre de Consultorio --}}
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

                        {{-- Nombre del Consultorio (NUEVO) --}}
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Nombre del Consultorio</label>
                            <div class="relative">
                                <input type="text" 
                                       placeholder="Ej: Consultorio 01"
                                       x-model="form.inicio_labores.nombre_consultorio" 
                                       class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 pl-4 font-bold text-sm focus:ring-indigo-500 uppercase">
                            </div>
                        </div>

                        {{-- NUEVO CAMPO: TURNO --}}
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

                    
                </div>
            </div>

            {{-- 5. EVIDENCIA FOTOGRÁFICA --}}
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
                
                {{-- LISTA DE ARCHIVOS NUEVOS --}}
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

                {{-- GALERÍA DE FOTOS GUARDADAS --}}
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
    function triajeForm() {
        // 1. RECEPCIÓN DE DATOS DESDE EL CONTROLADOR
        const dbCapacitacion = @json($dbCapacitacion ?? null);
        const dbInventario   = @json($dbInventario ?? []);
        const dbDificultad   = @json($dbDificultad ?? null);
        const dbFotos        = @json($dbFotos ?? []);
        // NUEVO: Recibir datos de inicio labores
        const dbInicioLabores = @json($dbInicioLabores ?? null);

        // --- Inicializaciones ---
        
        // A. Profesional
        let initProfesional = {
            tipo_doc: 'DNI', doc: '', nombres: '', apellido_paterno: '', apellido_materno: '', email: '', telefono: ''
        };
        // B. Capacitación
        let initCapacitacion = { recibieron_cap: '', institucion_cap: '', decl_jurada: '', comp_confidencialidad: '' };

        // Mapeo si existen datos previos de Capacitación/Profesional
        if (dbCapacitacion) {
            initCapacitacion.recibieron_cap = dbCapacitacion.recibieron_cap || '';
            initCapacitacion.institucion_cap = dbCapacitacion.institucion_cap || '';
            initCapacitacion.decl_jurada = dbCapacitacion.decl_jurada || ''; 
            initCapacitacion.comp_confidencialidad = dbCapacitacion.comp_confidencialidad || '';
            if (dbCapacitacion.profesional) {
                initProfesional = { ...dbCapacitacion.profesional };
            }
        }

        // C. NUEVO: Inicio Labores (Aquí conectamos los campos nuevos)
        let initInicioLabores = { consultorios: '', nombre_consultorio: '', turno: '' };
        
        if (dbInicioLabores) {
            initInicioLabores.consultorios = dbInicioLabores.cant_consultorios || '';
            initInicioLabores.nombre_consultorio = dbInicioLabores.nombre_consultorio || '';
            initInicioLabores.turno = dbInicioLabores.turno || '';
        }

        // D. Inventario
        let initInventario = [];
        if (dbInventario && dbInventario.length > 0) {
            initInventario = dbInventario.map(item => ({
                id: Date.now() + Math.random(),
                descripcion: item.descripcion,
                propiedad: item.propio,
                estado: item.estado,
                codigo: item.nro_serie || '',
                observacion: item.observacion
            }));
        }

        // E. Dificultades
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
            listaOpciones: ['CPU', 'IMPRESORA', 'LAPTOP', 'LECTOR DE DNIe', 'MONITOR', 'MOUSE', 'SCANNER', 'TABLET', 'TECLADO', 'TICKETERA'],
            itemSeleccionado: '',

            form: {
                profesional: initProfesional,
                capacitacion: initCapacitacion,
                // NUEVO: Agregamos el objeto al formulario principal
                inicio_labores: initInicioLabores, 
                inventario: initInventario,
                dificultades: initDificultades,
            },

            // --- MANEJO DE ARCHIVOS ---
            handleFiles(event) {
                const newFiles = Array.from(event.target.files).filter(file => file.type.startsWith('image/'));
                if (newFiles.length < event.target.files.length) alert("Solo imágenes permitidas.");
                this.files = [...this.files, ...newFiles];
                event.target.value = '';
            },
            removeFile(index) { this.files.splice(index, 1); },

            // --- Inventario ---
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
            eliminarItem(index) { this.form.inventario.splice(index, 1); },

            // --- Buscador ---
            async buscarProfesional() {
                let doc = this.form.profesional.doc;
                if (!doc || doc.length < 8) return;
                this.buscando = true;
                this.msgProfesional = "Buscando...";
                try {
                    let r = await fetch(`/usuario/monitoreo/modulo/triaje/buscar-profesional/${doc}`);
                    let d = await r.json();
                    if (d.success) {
                        this.form.profesional = { ...this.form.profesional, ...d.data };
                        this.msgProfesional = "Encontrado.";
                    } else {
                        this.limpiarDatos();
                        this.msgProfesional = "No encontrado.";
                    }
                } catch (e) { this.msgProfesional = "Error."; } 
                finally { this.buscando = false; }
            },
            limpiarDatos() {
                this.form.profesional.nombres = '';
                this.form.profesional.apellido_paterno = '';
                this.form.profesional.apellido_materno = '';
                this.form.profesional.email = '';
                this.form.profesional.telefono = '';
            },

            // --- Eliminar Foto ---
            eliminarFotoGuardada(id, index) {
                fetch(`/usuario/monitoreo/modulo/triaje/foto/${id}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                }).then(r=>r.json()).then(d=>{
                    if(d.success) this.oldFiles.splice(index, 1);
                    else alert('Error: ' + d.message);
                });
            },

            // --- Guardar ---
            guardarTodo() {
                this.saving = true;
                let fd = new FormData();
                fd.append('data', JSON.stringify(this.form)); // Aquí ya va incluido inicio_labores
                this.files.forEach(f => fd.append('fotos[]', f));

                fetch("{{ route('usuario.monitoreo.triaje.store', $acta->id) }}", {
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