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

            {{-- 1. DATOS DEL PROFESIONAL (COMPONENTE) --}}
            {{-- Pasamos el modelo 'form.profesional' para que Alpine sepa dónde guardar los datos --}}
            <x-seleccion-profesional 
                model="form.profesional"
                titulo="Datos del Profesional"
                subtitulo="Responsable de Triaje"
            />

            {{-- 2. CAPACITACIÓN (COMPONENTE) --}}
            {{-- Si ya creaste el componente de capacitación, úsalo aquí. Si no, deja el HTML original --}}
            <x-capacitacion 
                model="form.capacitacion" 
            />

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

                {{-- TABLA INVENTARIO --}}
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
                                        <span class="text-[9px] text-slate-300 font-bold ml-1" x-text="'#' + (index + 1)"></span>
                                    </td>
                                    <td class="py-3 px-2 align-middle">
                                        <select x-model="item.propiedad" class="w-full bg-white border border-slate-200 rounded-lg p-2 text-[10px] font-bold uppercase focus:ring-indigo-500">
                                            <option value="ESTABLECIMIENTO">ESTABLECIMIENTO</option>
                                            <option value="PERSONAL">PERSONAL</option>
                                        </select>
                                    </td>
                                    <td class="py-3 px-2 align-middle">
                                        <select x-model="item.estado" class="w-full bg-white border border-slate-200 rounded-lg p-2 text-[10px] font-bold uppercase focus:ring-indigo-500">
                                            <option value="BUENO">BUENO</option>
                                            <option value="REGULAR">REGULAR</option>
                                            <option value="MALO">MALO</option>
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
                                        <button type="button" @click="eliminarItem(index)" class="text-slate-300 hover:text-red-500 transition-colors p-2 hover:bg-red-50 rounded-lg" title="Quitar ítem">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
                <div class="mt-4 border-t border-slate-100 pt-4">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 pl-1">Comentarios Generales</label>
                    <textarea x-model="form.inventario_comentarios" rows="2" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 text-sm focus:ring-indigo-500"></textarea>
                </div>
            </div>
            
            {{-- 4. DIFICULTADES (Mantengo HTML original por ahora si no hay componente) --}}
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

            {{-- 5. EVIDENCIA FOTOGRÁFICA --}}
            <div class="bg-white border border-slate-200 rounded-[2.5rem] p-8 shadow-xl shadow-slate-200/50">
                <div class="flex items-center gap-4 mb-6">
                    <div class="h-12 w-12 rounded-2xl bg-indigo-600 flex items-center justify-center shadow-lg shadow-indigo-200">
                        <i data-lucide="camera" class="text-white w-6 h-6"></i>
                    </div>
                    <h3 class="text-lg font-black text-slate-900 uppercase tracking-tight">Evidencia Fotográfica</h3>
                </div>

                <div class="border-2 border-dashed border-slate-300 rounded-2xl p-10 flex flex-col items-center justify-center text-center hover:bg-slate-50 transition-colors relative cursor-pointer">
                    <input type="file" multiple @change="handleFiles" class="absolute inset-0 opacity-0 cursor-pointer">
                    <i data-lucide="cloud-upload" class="w-10 h-10 text-indigo-400 mb-3"></i>
                    <p class="text-indigo-600 font-bold uppercase text-sm">Clic para subir o arrastrar archivos</p>
                    <p class="text-xs text-slate-400 mt-1">PNG, JPG o JPEG (Máx. 5MB)</p>
                </div>
                
                <div class="mt-4 space-y-2" x-show="files.length > 0">
                    <template x-for="(file, i) in files" :key="i">
                        <div class="flex items-center justify-between bg-slate-50 px-4 py-2 rounded-lg border border-slate-100">
                            <span class="text-xs font-bold text-slate-600 truncate" x-text="file.name"></span>
                            <button type="button" @click="removeFile(i)" class="text-red-400 hover:text-red-600"><i data-lucide="x" class="w-4 h-4"></i></button>
                        </div>
                    </template>
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
        return {
            saving: false,
            msgProfesional: '',
            listaOpciones: ['MONITOR', 'CPU', 'TECLADO', 'MOUSE', 'IMPRESORA', 'LECTORA DE DNIe', 'TICKETERA'],
            itemSeleccionado: '',
            files: [],

            form: {
                profesional: {
                    tipo_doc: 'DNI', doc: '', nombres: '', apellido_paterno: '', apellido_materno: '', email: '', telefono: ''
                },
                capacitacion: {
                    recibieron_cap: '', institucion_cap: ''
                },
                dificultades: {
                    institucion: '', medio: ''
                },
                inventario: [], 
                inventario_comentarios: ''
            },

            agregarItem() {
                if (!this.itemSeleccionado) return;
                this.form.inventario.push({
                    id: Date.now() + Math.random(),
                    descripcion: this.itemSeleccionado,
                    propiedad: 'ESTABLECIMIENTO',
                    estado: 'BUENO',
                    codigo: '',
                    observacion: ''
                });
                this.itemSeleccionado = '';
                this.$nextTick(() => { if(typeof lucide !== 'undefined') lucide.createIcons(); });
            },

            eliminarItem(index) {
                this.form.inventario.splice(index, 1);
            },

            handleFiles(e) {
                this.files = [...this.files, ...Array.from(e.target.files)];
            },
            
            removeFile(index) {
                this.files.splice(index, 1);
            },

            // --- LÓGICA DE BÚSQUEDA CORREGIDA ---
            async buscarProfesional() {
                const doc = this.form.profesional.doc;
                
                if (!doc || doc.length < 8) {
                    this.msgProfesional = 'Ingrese un documento válido.';
                    return;
                }

                this.msgProfesional = 'Buscando...';

                try {
                    // Genera la URL base (sin el ID) usando el helper de Blade
                    const baseUrl = "{{ route('usuario.monitoreo.profesional.buscar', '') }}";
                    
                    // Concatenamos el DNI
                    const response = await fetch(`${baseUrl}/${doc}`);
                    
                    if (!response.ok) throw new Error('No encontrado');

                    const result = await response.json();

                    if (result.found) {
                        const p = result.data;
                        this.form.profesional.tipo_doc = p.tipo_doc || 'DNI';
                        this.form.profesional.nombres = p.nombres;
                        this.form.profesional.apellido_paterno = p.apellido_paterno;
                        this.form.profesional.apellido_materno = p.apellido_materno;
                        this.form.profesional.email = p.email;
                        this.form.profesional.telefono = p.telefono;
                        
                        this.msgProfesional = '✅ Encontrado';
                        setTimeout(() => this.msgProfesional = '', 3000);
                    }

                } catch (error) {
                    console.error(error);
                    this.msgProfesional = '❌ No encontrado (Llenar manual)';
                }
            },

            // --- LÓGICA DE GUARDADO CORREGIDA ---
            async guardarTodo() {
                this.saving = true;
                try {
                    const formData = new FormData();
                    formData.append('data', JSON.stringify(this.form));
                    this.files.forEach((file, index) => {
                        formData.append(`evidencias[${index}]`, file);
                    });

                    // Token CSRF necesario para Laravel
                    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                    
                    // Ruta Store
                    const url = "{{ route('usuario.monitoreo.triaje.store', $acta->id) }}";

                    const response = await fetch(url, {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': token },
                        body: formData
                    });

                    if (!response.ok) {
                        const res = await response.json();
                        throw new Error(res.message || 'Error al guardar');
                    }

                    alert('Guardado exitosamente');
                    window.location.reload(); 

                } catch (error) {
                    console.error(error);
                    alert('Error: ' + error.message);
                } finally {
                    this.saving = false;
                }
            }
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        if (typeof lucide !== 'undefined') lucide.createIcons();
    });
</script>
@endsection