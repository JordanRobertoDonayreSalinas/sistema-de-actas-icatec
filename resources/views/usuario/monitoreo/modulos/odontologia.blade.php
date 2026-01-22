@extends('layouts.usuario')
@section('title', 'Módulo 05: Odontología')

@section('content')
<div class="py-12 bg-[#f8fafc] min-h-screen" x-data="odontologiaForm()">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        
        {{-- ENCABEZADO --}}
        <div class="mb-10 flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="flex items-center gap-6">
                <div class="h-16 w-16 bg-white rounded-2xl flex items-center justify-center shadow-sm border border-slate-100">
                    <span class="text-2xl font-black text-indigo-600">05</span>
                </div>
                <div>
                    <div class="flex items-center gap-3 mb-1">
                        <span class="px-3 py-1 bg-indigo-100 text-indigo-700 text-[10px] font-black rounded-full uppercase tracking-widest">Módulo Técnico</span>
                        <span class="text-slate-400 font-bold text-[10px] uppercase tracking-wider">ID Acta: #{{ str_pad($acta->id, 5, '0', STR_PAD_LEFT) }}</span>
                    </div>
                    <h2 class="text-3xl font-black text-slate-900 uppercase tracking-tight italic">Módulo Odontología</h2>

                </div>
            </div>
            
            <a href="{{ route('usuario.monitoreo.modulos', $acta->id) }}" class="px-6 py-3 bg-white border border-slate-200 rounded-2xl text-slate-500 font-black text-xs uppercase tracking-widest shadow-sm hover:bg-slate-50 transition-colors">
                Volver
            </a>
        </div>

        {{-- FORMULARIO --}}
        <form @submit.prevent="guardarTodo" class="space-y-8">

            {{-- 1. SECCIÓN INICIO LABORES (Componente polimórfico) --}}
            <x-documentos model="form.inicio_labores" tipo="odontologia" />

            {{-- 2. DATOS DEL PROFESIONAL (Componente) --}}
            {{-- Nota: Si usas el mismo componente de Triaje, asegúrate que odontología tenga los campos necesarios. --}}
            {{-- Por ahora usaré el componente 'seleccion-profesional' que ya arreglamos --}}
            <x-seleccion-profesional model="form.profesional" />

            {{-- 3. CAPACITACIÓN (Componente) --}}
            <x-capacitacion model="form.capacitacion" />

            {{-- 4. INVENTARIO DE EQUIPAMIENTO (Componente) --}}
            <x-equipamiento model="form.inventario" />

            {{-- 5. DIFICULTADES CON EL SISTEMA (Componente) --}}
            <x-dificultad model="form.dificultades" />

            {{-- 6. SECCIÓN DNI (Componente) --}}
            <x-dni model="form.seccion_dni" />

            {{-- 7. COMENTARIOS GENERALES (Nuevo bloque HTML) --}}
            <div class="bg-white border border-slate-200 rounded-[2.5rem] p-8 shadow-xl shadow-slate-200/50 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-24 h-24 bg-indigo-50 rounded-full -mr-12 -mt-12 opacity-60 pointer-events-none"></div>
                <div class="flex items-center gap-4 mb-6 relative z-10">
                    <div class="h-12 w-12 rounded-2xl bg-indigo-600 flex items-center justify-center shadow-lg shadow-indigo-200">
                        <i data-lucide="message-square-plus" class="text-white w-6 h-6"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-slate-900 uppercase tracking-tight">Comentarios Generales</h3>
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Observaciones Adicionales</p>
                    </div>
                </div>
                <div class="relative z-10">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Detalle de observaciones</label>
                    <textarea x-model="form.inicio_labores.comentarios" rows="3" placeholder="Ingrese cualquier observación general..." class="w-full bg-slate-50 border border-slate-200 rounded-xl p-4 font-medium text-sm focus:ring-indigo-500 text-slate-700 uppercase"></textarea>
                </div>
            </div>

            {{-- 8. EVIDENCIA FOTOGRÁFICA (Componente) --}}
            <x-fotos files="files" old-files="oldFiles" />
            
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
    function odontologiaForm() {
        const dbCapacitacion  = @json($dbCapacitacion ?? null);
        const dbInventario    = @json($dbInventario ?? []);
        const dbDificultad    = @json($dbDificultad ?? null);
        const dbFotos         = @json($dbFotos ?? []);
        const dbInicioLabores = @json($dbInicioLabores ?? null);
        const dbDni           = @json($dbDni ?? null); 

        // 1. Profesional & Capacitación
        let initProfesional = { tipo_doc: 'DNI', doc: '', nombres: '', apellido_paterno: '', apellido_materno: '', email: '', cargo: '', telefono: '' };
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

        // 2. Inicio Labores (AGREGADO: fecha_registro y comentarios)
        let initInicioLabores = { 
            fecha_registro: '', 
            consultorios: '', 
            nombre_consultorio: '', 
            turno: '', 
            fua: '', 
            referencia: '', 
            receta: '', 
            orden_lab: '',
            comentarios: '' 
        };
        
        if (dbInicioLabores) {
            initInicioLabores.fecha_registro = dbInicioLabores.fecha_registro || '';
            initInicioLabores.consultorios = dbInicioLabores.cant_consultorios || '';
            initInicioLabores.nombre_consultorio = dbInicioLabores.nombre_consultorio || '';
            initInicioLabores.turno = dbInicioLabores.turno || '';
            initInicioLabores.fua = dbInicioLabores.fua || '';
            initInicioLabores.referencia = dbInicioLabores.referencia || '';
            initInicioLabores.receta = dbInicioLabores.receta || '';
            initInicioLabores.orden_lab = dbInicioLabores.orden_laboratorio || '';
            initInicioLabores.comentarios = dbInicioLabores.comentarios || '';
        }

        // 3. Sección DNI
        let initDni = { tipo_dni: '', version_dnie: '', firma_sihce: '', comentarios: '' };
        if (dbDni) {
            initDni.tipo_dni = dbDni.tip_dni || '';
            initDni.version_dnie = dbDni.version_dni || '';
            initDni.firma_sihce = dbDni.firma_sihce || '';
            initDni.comentarios = dbDni.comentarios || '';
        }

        // 4. Inventario (Preparación)
        let initInventario = [];
        if (dbInventario && dbInventario.length > 0) {
            initInventario = dbInventario.map(item => {
                let fullCode = item.nro_serie || '';
                let tipoDetectado = 'NS'; 
                let codigoLimpio = fullCode;
                if (fullCode.includes(' ')) {
                    let partes = fullCode.split(' ');
                    // Soporte para NS, CB y S/C
                    if (partes.length > 0 && ['NS', 'CB', 'S/C', 'S', 'CP'].includes(partes[0])) {
                        tipoDetectado = partes[0];
                        codigoLimpio = partes.slice(1).join(' '); 
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

        // 5. Dificultades
        let initDificultades = { institucion: '', medio: '' };
        if (dbDificultad) {
            initDificultades.institucion = dbDificultad.insti_comunica || '';
            initDificultades.medio = dbDificultad.medio_comunica || '';
        }

        return {
            saving: false,
            files: [],
            oldFiles: dbFotos,
            
            form: {
                profesional: initProfesional,
                capacitacion: initCapacitacion,
                inicio_labores: initInicioLabores,
                seccion_dni: initDni,
                inventario: initInventario,
                dificultades: initDificultades,
            },

            guardarTodo() {
                this.saving = true;
                let formToSend = JSON.parse(JSON.stringify(this.form));

                // Unir códigos Inventario
                formToSend.inventario = formToSend.inventario.map(item => {
                    let tipo = item.tipo_codigo || 'NS';
                    let valor = item.codigo || '';
                    item.codigo = (tipo + ' ' + valor).trim(); 
                    return item;
                });

                let fd = new FormData();
                fd.append('data', JSON.stringify(formToSend));
                this.files.forEach(f => fd.append('fotos[]', f));

                fetch("{{ route('usuario.monitoreo.consulta-odontologia.store', $acta->id) }}", {
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