@props(['model'])

{{-- 
    Uso: <x-seleccion-profesional model="form.profesional" /> 
    'model' debe ser la cadena de texto que apunta al objeto de datos en el padre (ej: "form.profesional")
--}}

<div x-data="seleccionProfesional({{ $model }})" 
     x-init="initComponent()"
     class="bg-white border border-slate-200 rounded-[2.5rem] p-8 shadow-xl shadow-slate-200/50 relative overflow-hidden">
    
    {{-- Decoración de fondo --}}
    <div class="absolute top-0 right-0 w-32 h-32 bg-indigo-50 rounded-full -mr-16 -mt-16 opacity-60 pointer-events-none"></div>
    
    <div class="relative z-10">
        {{-- Encabezado de la sección --}}
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
            {{-- Fila 1: Tipo Doc y Buscador --}}
            <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
                <div class="md:col-span-4">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Tipo Doc.</label>
                    <select x-model="entidad.tipo_doc" class="w-full bg-slate-50 border border-slate-200 text-slate-700 text-sm rounded-xl font-bold uppercase p-3">
                        <option value="DNI">DNI</option>
                        <option value="CE">C.E.</option>
                    </select>
                </div>
                <div class="md:col-span-8 relative">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Número Documento</label>
                    <div class="flex gap-2">
                        <input type="text" 
                               x-model="entidad.doc" 
                               @keydown.enter.prevent="buscar()" 
                               placeholder="Ingrese Documento..." 
                               class="flex-1 bg-slate-50 border border-slate-200 text-slate-900 text-sm rounded-xl p-3 font-bold tracking-wider">
                        
                        <button type="button" @click="buscar()" :disabled="buscando" class="bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 text-white rounded-xl px-4 py-2 transition-colors">
                            <i x-show="!buscando" data-lucide="search" class="w-5 h-5"></i>
                            <i x-show="buscando" data-lucide="loader-2" class="w-5 h-5 animate-spin"></i>
                        </button>
                    </div>
                    {{-- Mensaje de Feedback --}}
                    <span x-show="msgEstado" x-text="msgEstado" 
                          :class="encontrado ? 'text-emerald-500' : 'text-red-400'"
                          class="absolute -bottom-5 left-0 text-[10px] font-bold"></span>
                </div>
            </div>

            {{-- Fila 2: Nombres y Apellidos --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Apellido Paterno</label>
                    <input type="text" x-model="entidad.apellido_paterno" class="w-full bg-white border border-slate-200 rounded-xl p-3 font-bold uppercase">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Apellido Materno</label>
                    <input type="text" x-model="entidad.apellido_materno" class="w-full bg-white border border-slate-200 rounded-xl p-3 font-bold uppercase">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Nombres</label>
                    <input type="text" x-model="entidad.nombres" class="w-full bg-white border border-slate-200 rounded-xl p-3 font-bold uppercase">
                </div>
            </div>
                
            {{-- Fila 3: Profesión, Email, Teléfono --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                
                {{-- Lógica de Profesión (Select + Input 'Otros') --}}
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Profesión / Cargo</label>
                    <div class="flex flex-col gap-2">
                        {{-- Select --}}
                        <select x-model="cargoSelect" 
                                @change="actualizarCargoFinal()"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 font-bold uppercase text-xs focus:ring-indigo-500 cursor-pointer">
                            <option value="" disabled>SELECCIONE...</option>
                            <template x-for="prof in listaProfesiones" :key="prof">
                                <option :value="prof" x-text="prof"></option>
                            </template>
                            <option value="OTROS">OTROS</option>
                        </select>

                        {{-- Input Manual (Solo si es OTROS) --}}
                        <div x-show="cargoSelect === 'OTROS'" 
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 -translate-y-2"
                             x-transition:enter-end="opacity-100 translate-y-0">
                            <input type="text" 
                                   x-model="cargoManual" 
                                   @input="actualizarCargoFinal()"
                                   placeholder="Especifique cargo..."
                                   class="w-full bg-white border border-indigo-300 text-indigo-700 rounded-xl p-3 font-bold uppercase text-xs focus:ring-indigo-500 shadow-sm">
                        </div>
                    </div>
                </div>

                {{-- Email --}}
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Email</label>
                    <input type="email" x-model="entidad.email" class="w-full bg-white border border-slate-200 rounded-xl p-3 font-medium lowercase">
                </div>

                {{-- Teléfono --}}
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Teléfono</label>
                    <input type="text" x-model="entidad.telefono" class="w-full bg-white border border-slate-200 rounded-xl p-3 font-bold">
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('seleccionProfesional', (entidadVinculada) => ({
            entidad: entidadVinculada,
            
            buscando: false,
            msgEstado: '',
            encontrado: false,
            
            listaProfesiones: [
                'MEDICO', 'ODONTOLOGO(A)', 'ENFERMERO(A)', 'TECNICO ENFERMERIA', 
                'TECNICO LABORATORIO', 'BIOLOGO(A)', 'QUIMICO FARMACEUTICO(A)', 
                'NUTRICIONISTA', 'PSICOLOGO(A)', 'OBSTETRA'
            ],
            cargoSelect: '', 
            cargoManual: '',

            initComponent() {
                // 1. Intentar detectar al cargar
                this.detectarCargoInicial();
                
                // 2. Observar cambios (por si la data llega un poco después desde el padre)
                this.$watch('entidad.cargo', (val) => {
                    this.detectarCargoInicial();
                });
            },

            actualizarCargoFinal() {
                if (this.cargoSelect === 'OTROS') {
                    this.entidad.cargo = this.cargoManual.toUpperCase();
                } else {
                    this.entidad.cargo = this.cargoSelect;
                    this.cargoManual = ''; 
                }
            },

            detectarCargoInicial() {
                    let cargoBD = this.entidad.cargo;

                    if (!cargoBD) {
                        this.cargoSelect = '';
                        this.cargoManual = '';
                        return;
                    }

                    // 1. Normalizar
                    let cargoLimpio = String(cargoBD).trim().toUpperCase();

                    // 2. Buscar coincidencia
                    let coincidencia = this.listaProfesiones.find(prof => 
                        prof.trim().toUpperCase() === cargoLimpio
                    );

                    if (coincidencia) {
                        // --- AQUÍ ESTÁ LA CORRECCIÓN ---
                        // Usamos $nextTick para esperar que el x-for termine de renderizar las opciones
                        this.$nextTick(() => {
                            this.cargoSelect = coincidencia;
                        });
                        this.cargoManual = ''; 
                    } else {
                        // "OTROS" ya existe en el HTML fijo, así que no necesita espera
                        this.cargoSelect = 'OTROS';
                        this.cargoManual = cargoBD; 
                    }
                },

            async buscar() {
                let doc = this.entidad.doc;
                if (!doc || doc.length < 8) return;

                this.buscando = true;
                this.msgEstado = "Buscando...";
                this.encontrado = false;

                try {
                    let response = await fetch(`/usuario/monitoreo/modulo/triaje/buscar-profesional/${doc}`);
                    let data = await response.json();

                    if (data.success) {
                        this.entidad.tipo_doc = data.data.tipo_doc || 'DNI';
                        this.entidad.apellido_paterno = data.data.apellido_paterno;
                        this.entidad.apellido_materno = data.data.apellido_materno;
                        this.entidad.nombres = data.data.nombres;
                        this.entidad.email = data.data.email;
                        this.entidad.telefono = data.data.telefono;
                        this.entidad.cargo = data.data.cargo; 
                        
                        this.msgEstado = "Encontrado.";
                        this.encontrado = true;
                        
                        this.detectarCargoInicial();
                    } else {
                        this.limpiarCampos();
                        this.msgEstado = "No encontrado en la base de datos.";
                        this.encontrado = false;
                    }
                } catch (error) {
                    console.error(error);
                    this.msgEstado = "Error de conexión.";
                } finally {
                    this.buscando = false;
                }
            },

            limpiarCampos() {
                this.entidad.nombres = '';
                this.entidad.apellido_paterno = '';
                this.entidad.apellido_materno = '';
                this.entidad.email = '';
                this.entidad.cargo = '';
                this.entidad.telefono = '';
                this.detectarCargoInicial(); 
            }
        }));
    });
</script>
</div>