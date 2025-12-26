@extends('layouts.usuario')
@section('title', 'Crear acta')
{{-- 1. ESTILOS: Tailwind y jQuery UI --}}
@push('styles')
    {{-- jQuery UI para Autocomplete --}}
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    
    <style>
        /* Animación para el Toast y elementos dinámicos */
        @keyframes fade-in { 
            from {opacity:0; transform:translateY(10px);} 
            to {opacity:1; transform:translateY(0);} 
        }
        .animate-fade-in { animation: fade-in 0.3s ease forwards; }

        /* Estilos personalizados para el Autocomplete */
        .ui-autocomplete { 
            border-radius: 0.75rem; 
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); 
            border: 1px solid #e2e8f0; 
            z-index: 9999 !important;
        }
        .ui-menu-item-wrapper.ui-state-active { 
            background-color: #10b981 !important; 
            border: none !important; 
            color: white !important;
        }
        
        /* Inputs readonly para que no parezcan editables */
        input:read-only { background-color: #f8fafc; cursor: not-allowed; }
    </style>
@endpush

{{-- 2. CONTENIDO: El formulario completo --}}
@section('content')
    <div class="py-10 bg-gradient-to-r from-blue-50 to-indigo-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <form id="actaForm"
                  action="{{ route('usuario.actas.store') }}"
                  method="POST"
                  enctype="multipart/form-data"
                  class="bg-white shadow-2xl rounded-xl p-8 w-full border border-gray-300">

                @csrf

                <h1 class="text-3xl font-extrabold text-center uppercase text-indigo-700 underline mb-8">
                    Acta de Asistencia Técnica
                </h1>

                <!-- ============================
                     DATOS GENERALES
                     ============================ -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-gray-700 font-semibold">Fecha:</label>
                        <input type="date" name="fecha" value="{{ old('fecha', date('Y-m-d')) }}" required
                               class="mt-1 block w-full border border-indigo-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 p-2">
                    </div>
                    <div>
                        <label class="block text-gray-700 font-semibold">Establecimiento:</label>
                        <input type="text" id="establecimiento" name="establecimiento" placeholder="Código o nombre..." required autocomplete="off"
                               value="{{ old('establecimiento', $acta->establecimiento->nombre ?? '') }}"
                               class="mt-1 block w-full border border-indigo-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 p-2">
                        <input type="hidden" id="establecimiento_id" name="establecimiento_id" value="{{ old('establecimiento_id', $acta->establecimiento_id ?? '') }}">
                    </div>
                </div>

                <!-- Campos de Ubicación (Readonly) -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-6">
                    <div>
                        <label class="block text-gray-700 font-semibold">Distrito:</label>
                        <input type="text" id="distrito" name="distrito" readonly required value="{{ old('distrito', $acta->distrito ?? '') }}" class="mt-1 block w-full border border-gray-300 rounded-lg bg-gray-100 p-2">
                    </div>
                    <div>
                        <label class="block text-gray-700 font-semibold">Provincia:</label>
                        <input type="text" id="provincia" name="provincia" readonly required value="{{ old('provincia', $acta->provincia ?? '') }}" class="mt-1 block w-full border border-gray-300 rounded-lg bg-gray-100 p-2">
                    </div>
                    <div>
                        <label class="block text-gray-700 font-semibold">Microred:</label>
                        <input type="text" id="microred" name="microred" readonly required value="{{ old('microred', $acta->microred ?? '') }}" class="mt-1 block w-full border border-gray-300 rounded-lg bg-gray-100 p-2">
                    </div>
                    <div>
                        <label class="block text-gray-700 font-semibold">Red:</label>
                        <input type="text" id="red" name="red" readonly required value="{{ old('red', $acta->red ?? '') }}" class="mt-1 block w-full border border-gray-300 rounded-lg bg-gray-100 p-2">
                    </div>
                    <div class="col-span-2 md:col-span-4">
                        <label class="block text-gray-700 font-semibold">Responsable:</label>
                        <input type="text" id="responsable" name="responsable" placeholder="Nombre del responsable" required
                               value="{{ old('responsable', $acta->responsable ?? '') }}"
                               class="mt-1 block w-full border border-indigo-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 p-2">
                    </div>
                </div>

                <!-- Tema y Modalidad -->
                <div class="mb-6">
                    <label class="block text-gray-700 font-semibold">Tema / Motivo:</label>
                    <select name="tema" required class="mt-1 block w-full border border-indigo-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 p-2">
                        <option value="">Seleccione un motivo...</option>
                        <option value="Reactivación de módulo" {{ old('tema', $acta->tema ?? '') == 'Reactivación de módulo' ? 'selected' : '' }}>Reactivación de módulo</option>
                        <option value="Cambio de responsable del módulo" {{ old('tema', $acta->tema ?? '') == 'Cambio de responsable del módulo' ? 'selected' : '' }}>Cambio de responsable del módulo</option>
                        <option value="Ingreso de nuevo personal" {{ old('tema', $acta->tema ?? '') == 'Ingreso de nuevo personal' ? 'selected' : '' }}>Ingreso de nuevo personal</option>
                        <option value="Actualización de cartera de servicios" {{ old('tema', $acta->tema ?? '') == 'Actualización de cartera de servicios' ? 'selected' : '' }}>Actualización de cartera de servicios</option>
                        <option value="Otros" {{ old('tema', $acta->tema ?? '') == 'Otros' ? 'selected' : '' }}>Otros</option>
                    </select>
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 font-semibold mb-2">Modalidad de asistencia:</label>
                    <div class="flex space-x-6">
                        <label class="flex items-center space-x-2">
                            <input type="radio" name="modalidad" value="Presencial" required {{ old('modalidad', $acta->modalidad ?? '') == 'Presencial' ? 'checked' : '' }} class="text-indigo-600 focus:ring-indigo-500"><span>Presencial</span>
                        </label>
                        <label class="flex items-center space-x-2">
                            <input type="radio" name="modalidad" value="Virtual" required {{ old('modalidad', $acta->modalidad ?? '') == 'Virtual' ? 'checked' : '' }} class="text-indigo-600 focus:ring-indigo-500"><span>Virtual</span>
                        </label>
                        <label class="flex items-center space-x-2">
                            <input type="radio" name="modalidad" value="Telefónica" required {{ old('modalidad', $acta->modalidad ?? '') == 'Telefónica' ? 'checked' : '' }} class="text-indigo-600 focus:ring-indigo-500"><span>Telefónica</span>
                        </label>
                    </div>
                </div>

                <div class="mb-6">
    <label class="block text-gray-700 font-semibold">Implementador(a):</label>
    <select name="implementador" required class="mt-1 block w-full border border-indigo-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 p-2">
        <option value="">Seleccione un implementador...</option>
        
        @php
            // 1. Jalamos todos los usuarios activos de la base de datos
            // Usamos App\Models\User directamente o lo que tengas en tu controlador
            $usuariosRegistrados = \App\Models\User::where('status', 'active')
                ->orderBy('apellido_paterno', 'asc')
                ->get();

            // 2. Obtenemos el ID del usuario que tiene la sesión iniciada
            $idUsuarioLogeado = Auth::id();
        @endphp

        @foreach($usuariosRegistrados as $u)
            @php
                // Construimos el nombre completo para mostrarlo en la lista
                $nombreAMostrar = trim("{$u->apellido_paterno} {$u->apellido_materno} {$u->name}");
                
                // Lógica de selección:
                // Prioridad 1: Si hubo un error y el usuario ya había elegido a alguien (old)
                // Prioridad 2: Si es la primera vez que carga y el ID coincide con el logueado
                $selected = (old('implementador') == $nombreAMostrar) || (!old('implementador') && $u->id == $idUsuarioLogeado);
            @endphp
            
            <option value="{{ $nombreAMostrar }}" {{ $selected ? 'selected' : '' }}>
                {{ $nombreAMostrar }}
            </option>
        @endforeach
    </select>
</div>

                <!-- ============================
                     SECCIÓN: PARTICIPANTES
                     ============================ -->
                <div class="mb-6">
                    <label class="block text-gray-700 font-bold text-lg mb-2 underline">Participantes:</label>
                    <div class="overflow-x-auto">
                        <table class="w-full border border-gray-400">
                            <thead class="bg-gray-200">
                                <tr>
                                    <th class="border border-gray-400 px-2 py-1 w-12 text-center">N°</th>
                                    <th class="border border-gray-400 px-2 py-1">DNI</th>
                                    <th class="border border-gray-400 px-2 py-1">Apellidos</th>
                                    <th class="border border-gray-400 px-2 py-1">Nombres</th>
                                    <th class="border border-gray-400 px-2 py-1">Cargo</th>
                                    <th class="border border-gray-400 px-2 py-1">Módulo</th>
                                    <th class="border border-gray-400 px-2 py-1">Unidad Ejecutora</th>
                                    <th class="border border-gray-400 px-2 py-1 w-16 text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="tabla-participantes">
                                @php
                                    $participantes = old('participantes', $acta->participantes ?? []);
                                    $modulos = [
    "Atencion Prenatal",
    "Citas",
    "Consulta Externa: Medicina",
    "Consulta Externa: Nutricion",
    "Consulta Externa: Odontologia",
    "Consulta Externa: Psicologia",
    "Cred",
    "Farmacia",
    "FUA",
    "Gestión Administrativa",
    "Inmunizaciones",
    "Laboratorio",
    "Parto",
    "Planificacion Familiar",
    "Puerperio",
    "Teleatiendo",
    "Triaje",
    "VIH"
];
                                    $unidades = ["DIRESA ICA","RED DE SALUD ICA","HOSPITAL SAN JOSE DE CHINCHA","HOSPITAL SAN JUAN DE DIOS PISCO","HOSPITAL DE APOYO PALPA","HOSPITAL DE APOYO NAZCA"];
                                @endphp

                                @if(count($participantes) === 0)
                                    <tr>
                                        <td class="border border-gray-400 px-2 py-1 text-center">1</td>
                                        <td class="border border-gray-400 px-2 py-1"><input type="text" name="participantes[0][dni]" data-base="dni" required class="w-full border-gray-300 rounded-md p-1"></td>
                                        <td class="border border-gray-400 px-2 py-1"><input type="text" name="participantes[0][apellidos]" data-base="apellidos" required class="w-full border-gray-300 rounded-md p-1"></td>
                                        <td class="border border-gray-400 px-2 py-1"><input type="text" name="participantes[0][nombres]" data-base="nombres" required class="w-full border-gray-300 rounded-md p-1"></td>
                                        <td class="border border-gray-400 px-2 py-1"><input type="text" name="participantes[0][cargo]" data-base="cargo" required class="w-full border-gray-300 rounded-md p-1"></td>
                                        <td class="border border-gray-400 px-2 py-1">
                                            <select name="participantes[0][modulo]" data-base="modulo" class="w-full border-gray-300 rounded-md p-1">
                                                <option value="">-- No aplica --</option>
                                                @foreach($modulos as $opcion) <option value="{{ $opcion }}">{{ $opcion }}</option> @endforeach
                                            </select>
                                        </td>
                                        <td class="border border-gray-400 px-2 py-1">
                                            <select name="participantes[0][unidad_ejecutora]" data-base="unidad_ejecutora" class="w-full border-gray-300 rounded-md p-1">
                                                <option value="">-- No aplica --</option>
                                                @foreach($unidades as $opcion) <option value="{{ $opcion }}">{{ $opcion }}</option> @endforeach
                                            </select>
                                        </td>
                                        <td class="border border-gray-400 px-2 py-1 text-center">
                                            <button type="button" class="text-red-600 font-bold eliminar-fila" aria-label="Eliminar participante">✖</button>
                                        </td>
                                    </tr>
                                @else
                                    @foreach($participantes as $i => $p)
                                        <tr>
                                            <td class="border border-gray-400 px-2 py-1 text-center">{{ $i + 1 }}</td>
                                            <td class="border border-gray-400 px-2 py-1"><input type="text" name="participantes[{{ $i }}][dni]" data-base="dni" value="{{ $p['dni'] ?? $p->dni ?? '' }}" required class="w-full border-gray-300 rounded-md p-1"></td>
                                            <td class="border border-gray-400 px-2 py-1"><input type="text" name="participantes[{{ $i }}][apellidos]" data-base="apellidos" value="{{ $p['apellidos'] ?? $p->apellidos ?? '' }}" required class="w-full border-gray-300 rounded-md p-1"></td>
                                            <td class="border border-gray-400 px-2 py-1"><input type="text" name="participantes[{{ $i }}][nombres]" data-base="nombres" value="{{ $p['nombres'] ?? $p->nombres ?? '' }}" required class="w-full border-gray-300 rounded-md p-1"></td>
                                            <td class="border border-gray-400 px-2 py-1"><input type="text" name="participantes[{{ $i }}][cargo]" data-base="cargo" value="{{ $p['cargo'] ?? $p->cargo ?? '' }}" required class="w-full border-gray-300 rounded-md p-1"></td>
                                            <td class="border border-gray-400 px-2 py-1">
                                                <select name="participantes[{{ $i }}][modulo]" data-base="modulo" class="w-full border-gray-300 rounded-md p-1">
                                                    <option value="">-- No aplica --</option>
                                                    @foreach($modulos as $opcion) <option value="{{ $opcion }}" {{ ($p['modulo'] ?? $p->modulo ?? '') == $opcion ? 'selected' : '' }}>{{ $opcion }}</option> @endforeach
                                                </select>
                                            </td>
                                            <td class="border border-gray-400 px-2 py-1">
                                                <select name="participantes[{{ $i }}][unidad_ejecutora]" data-base="unidad_ejecutora" class="w-full border-gray-300 rounded-md p-1">
                                                    <option value="">-- No aplica --</option>
                                                    @foreach($unidades as $opcion) <option value="{{ $opcion }}" {{ ($p['unidad_ejecutora'] ?? $p->unidad_ejecutora ?? '') == $opcion ? 'selected' : '' }}>{{ $opcion }}</option> @endforeach
                                                </select>
                                            </td>
                                            <td class="border border-gray-400 px-2 py-1 text-center">
                                                <button type="button" class="text-red-600 font-bold eliminar-fila" aria-label="Eliminar participante">✖</button>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                    <button type="button" id="agregar-participante" class="mt-2 px-3 py-1 bg-green-600 text-white rounded-md hover:bg-green-700">
                        + Agregar participante
                    </button>
                </div>

                <!-- Plantilla fila participante (usada por tu JS) -->
                <template id="fila-participante">
                    <tr>
                        <td class="border border-gray-400 px-2 py-1 text-center"></td>
                        <td class="border border-gray-400 px-2 py-1"><input type="text" data-base="dni" class="w-full border-gray-300 rounded-md p-1"></td>
                        <td class="border border-gray-400 px-2 py-1"><input type="text" data-base="apellidos" class="w-full border-gray-300 rounded-md p-1"></td>
                        <td class="border border-gray-400 px-2 py-1"><input type="text" data-base="nombres" class="w-full border-gray-300 rounded-md p-1"></td>
                        <td class="border border-gray-400 px-2 py-1"><input type="text" data-base="cargo" class="w-full border-gray-300 rounded-md p-1"></td>
                        <td class="border border-gray-400 px-2 py-1">
                            <select data-base="modulo" class="w-full border-gray-300 rounded-md p-1">
                                <option value="">-- No aplica --</option>
                                @foreach($modulos as $opcion) <option value="{{ $opcion }}">{{ $opcion }}</option> @endforeach
                            </select>
                        </td>
                        <td class="border border-gray-400 px-2 py-1">
                            <select data-base="unidad_ejecutora" class="w-full border-gray-300 rounded-md p-1">
                                <option value="">-- No aplica --</option>
                                @foreach($unidades as $opcion) <option value="{{ $opcion }}">{{ $opcion }}</option> @endforeach
                            </select>
                        </td>
                        <td class="border border-gray-400 px-2 py-1 text-center">
                            <button type="button" class="text-red-600 font-bold eliminar-fila" aria-label="Eliminar participante">✖</button>
                        </td>
                    </tr>
                </template>

                <!-- ============================
                     SECCIÓN: ACTIVIDADES
                     ============================ -->
                <div class="mb-6">
                    <label class="block text-gray-700 font-bold text-lg mb-2 underline">Actividades desarrolladas:</label>
                    <table class="w-full border border-gray-400">
                        <thead class="bg-gray-200">
                            <tr>
                                <th class="border border-gray-400 px-2 py-1 w-12 text-center">N°</th>
                                <th class="border border-gray-400 px-2 py-1">Descripción</th>
                                <th class="border border-gray-400 px-2 py-1 w-16 text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tabla-actividades">
                            @php $actividades = old('actividades', $acta->actividades ?? []); @endphp
                            @if(count($actividades) === 0)
                                <tr>
                                    <td class="border border-gray-400 px-2 py-1 text-center">1</td>
                                    <td class="border border-gray-400 px-2 py-1">
                                        <input type="text" name="actividades[0][descripcion]" required class="w-full border-gray-300 rounded-md p-1">
                                    </td>
                                    <td class="border border-gray-400 px-2 py-1 text-center">
                                        <button type="button" class="text-red-600 font-bold eliminar-fila-generica">✖</button>
                                    </td>
                                </tr>
                            @else
                                @foreach($actividades as $i => $a)
                                    <tr>
                                        <td class="border border-gray-400 px-2 py-1 text-center">{{ $i + 1 }}</td>
                                        <td class="border border-gray-400 px-2 py-1">
                                            <input type="text" name="actividades[{{ $i }}][descripcion]" value="{{ $a['descripcion'] ?? $a->descripcion ?? '' }}" required class="w-full border-gray-300 rounded-md p-1">
                                        </td>
                                        <td class="border border-gray-400 px-2 py-1 text-center">
                                            <button type="button" class="text-red-600 font-bold eliminar-fila-generica">✖</button>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                    <button type="button" id="agregar-actividad" class="mt-2 px-3 py-1 bg-blue-600 text-white rounded-md hover:bg-blue-700">+ Agregar actividad</button>
                </div>

                <!-- ============================
                     SECCIÓN: IMÁGENES
                     ============================ -->
                <div class="mb-6">
                    <label class="block text-gray-700 font-bold text-lg mb-2 underline">
                        Adjuntar Imágenes (máx. 5)
                    </label>

                    <!-- Contenedor drag & drop -->
                    <div id="drop-area"
                        class="w-full p-6 border-2 border-dashed border-indigo-400 rounded-xl cursor-pointer
                               text-center bg-indigo-50 hover:bg-indigo-100 transition relative">
                        <p class="text-gray-600">Haz clic o arrastra imágenes aquí</p>
                        <p class="text-xs text-gray-500">(máx. 5 imágenes, 2 MB cada una)</p>
                        <input type="file" id="imagenes" name="imagenes[]" accept="image/*" multiple class="hidden">
                    </div>

                    <!-- Contador + botón limpiar -->
                    <div class="flex items-center justify-between mt-2">
                        <p id="file-counter" class="text-sm text-gray-500">0 / 5 imágenes seleccionadas</p>
                        <button type="button" id="clear-all" 
                            class="text-xs text-red-600 font-semibold hover:underline hidden">Quitar todas</button>
                    </div>

                    <!-- Miniaturas cargadas -->
                    <div id="thumbnails" class="mt-4 flex flex-wrap gap-4"></div>
                </div>

                <!-- ============================
                     SECCIÓN: ACUERDOS
                     ============================ -->
                <div class="mb-6">
                    <label class="block text-gray-700 font-bold text-lg mb-2 underline">Acuerdos y compromisos:</label>
                    <table class="w-full border border-gray-400">
                        <thead class="bg-gray-200">
                            <tr>
                                <th class="border border-gray-400 px-2 py-1 w-12 text-center">N°</th>
                                <th class="border border-gray-400 px-2 py-1">Descripción</th>
                                <th class="border border-gray-400 px-2 py-1 w-16 text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tabla-acuerdos">
                            @php $acuerdos = old('acuerdos', $acta->acuerdos ?? []); @endphp
                            @if(count($acuerdos) === 0)
                                <tr>
                                    <td class="border border-gray-400 px-2 py-1 text-center">1</td>
                                    <td class="border border-gray-400 px-2 py-1">
                                        <input type="text" name="acuerdos[0][descripcion]" required class="w-full border-gray-300 rounded-md p-1">
                                    </td>
                                    <td class="border border-gray-400 px-2 py-1 text-center">
                                        <button type="button" class="text-red-600 font-bold eliminar-fila-generica">✖</button>
                                    </td>
                                </tr>
                            @else
                                @foreach($acuerdos as $i => $ac)
                                    <tr>
                                        <td class="border border-gray-400 px-2 py-1 text-center">{{ $i + 1 }}</td>
                                        <td class="border border-gray-400 px-2 py-1">
                                            <input type="text" name="acuerdos[{{ $i }}][descripcion]" value="{{ $ac['descripcion'] ?? $ac->descripcion ?? '' }}" required class="w-full border-gray-300 rounded-md p-1">
                                        </td>
                                        <td class="border border-gray-400 px-2 py-1 text-center">
                                            <button type="button" class="text-red-600 font-bold eliminar-fila-generica">✖</button>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                    <button type="button" id="agregar-acuerdo" class="mt-2 px-3 py-1 bg-purple-600 text-white rounded-md hover:bg-purple-700">+ Agregar acuerdo</button>
                </div>

                <!-- ============================
                     SECCIÓN: OBSERVACIONES
                     ============================ -->
                <div class="mb-6">
                    <label class="block text-gray-700 font-bold text-lg mb-2 underline">Observaciones:</label>
                    <table class="w-full border border-gray-400">
                        <thead class="bg-gray-200">
                            <tr>
                                <th class="border border-gray-400 px-2 py-1 w-12 text-center">N°</th>
                                <th class="border border-gray-400 px-2 py-1">Descripción</th>
                                <th class="border border-gray-400 px-2 py-1 w-16 text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tabla-observaciones">
                            @php $observaciones = old('observaciones', $acta->observaciones ?? []); @endphp
                            @if(count($observaciones) === 0)
                                <tr>
                                    <td class="border border-gray-400 px-2 py-1 text-center">1</td>
                                    <td class="border border-gray-400 px-2 py-1">
                                        <input type="text" name="observaciones[0][descripcion]" required class="w-full border-gray-300 rounded-md p-1">
                                    </td>
                                    <td class="border border-gray-400 px-2 py-1 text-center">
                                        <button type="button" class="text-red-600 font-bold eliminar-fila-generica">✖</button>
                                    </td>
                                </tr>
                            @else
                                @foreach($observaciones as $i => $o)
                                    <tr>
                                        <td class="border border-gray-400 px-2 py-1 text-center">{{ $i + 1 }}</td>
                                        <td class="border border-gray-400 px-2 py-1">
                                            <input type="text" name="observaciones[{{ $i }}][descripcion]" value="{{ $o['descripcion'] ?? $o->descripcion ?? '' }}" required class="w-full border-gray-300 rounded-md p-1">
                                        </td>
                                        <td class="border border-gray-400 px-2 py-1 text-center">
                                            <button type="button" class="text-red-600 font-bold eliminar-fila-generica">✖</button>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                    <button type="button" id="agregar-observacion" class="mt-2 px-3 py-1 bg-yellow-600 text-white rounded-md hover:bg-yellow-700">+ Agregar observación</button>
                </div>

                <!-- Botón Guardar -->
                <div class="text-center mt-6">
                    <button type="button" id="btnGuardar" 
                        class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-lg shadow-md text-lg">
                        Guardar Acta
                    </button>
                </div>

            </form>
        </div>
    </div>

    {{-- Modales fuera del form --}}
    <!-- Modal para ver imagen grande -->
    <div id="image-modal" class="fixed inset-0 bg-black bg-opacity-70 hidden items-center justify-center z-50">
        <span id="close-modal" class="absolute top-4 right-6 text-white text-3xl cursor-pointer font-bold">&times;</span>
        <img id="modal-img" class="max-w-[90%] max-h-[90%] rounded-lg shadow-lg" src="">
    </div>

    <!-- Toast contenedor -->
    <div id="toast-container" class="fixed bottom-4 right-4 space-y-2 z-50"></div>

@endsection

{{-- 3. SCRIPTS: Toda tu lógica JavaScript aquí abajo --}}
@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Script de Actividades -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tabla = document.getElementById('tabla-actividades');
            const btnAgregar = document.getElementById('agregar-actividad');

            function actualizarNumeracion() {
                tabla.querySelectorAll('tr').forEach((tr, index) => {
                    tr.querySelector('td:first-child').textContent = index + 1;
                    tr.querySelector('input').name = `actividades[${index}][descripcion]`;
                });
            }

            if(btnAgregar) {
                btnAgregar.addEventListener('click', () => {
                    const nuevaFila = document.createElement('tr');
                    nuevaFila.innerHTML = `
                        <td class="border border-gray-400 px-2 py-1 text-center"></td>
                        <td class="border border-gray-400 px-2 py-1">
                            <input type="text" name="" required class="w-full border-gray-300 rounded-md p-1">
                        </td>
                        <td class="border border-gray-400 px-2 py-1 text-center">
                            <button type="button" class="text-red-600 font-bold eliminar-fila-generica">✖</button>
                        </td>
                    `;
                    tabla.appendChild(nuevaFila);
                    actualizarNumeracion();
                });
            }

            if(tabla) {
                tabla.addEventListener('click', function(e) {
                    if(e.target.classList.contains('eliminar-fila-generica')) {
                        e.target.closest('tr').remove();
                        actualizarNumeracion();
                    }
                });
            }

            // Inicializar numeración
            if(tabla) actualizarNumeracion();
        });
    </script>

    <!-- Script de Imágenes (Drag & Drop) -->
    <script>
    document.addEventListener("DOMContentLoaded", () => {
        const input = document.getElementById("imagenes");
        const dropArea = document.getElementById("drop-area");
        const counter = document.getElementById("file-counter");
        const clearBtn = document.getElementById("clear-all");
        const thumbnails = document.getElementById("thumbnails");
        const toastContainer = document.getElementById("toast-container");
        const modal = document.getElementById("image-modal");
        const modalImg = document.getElementById("modal-img");
        const closeModal = document.getElementById("close-modal");

        if(!dropArea) return; // Seguridad si no existe

        const maxFiles = 5;
        const maxSize = 2 * 1024 * 1024; // 2 MB
        let filesArray = [];

        dropArea.addEventListener("click", () => input.click());

        ["dragenter","dragover"].forEach(eName => dropArea.addEventListener(eName, e => { 
            e.preventDefault(); 
            dropArea.classList.add("bg-indigo-100","scale-105"); 
        }));
        ["dragleave","drop"].forEach(eName => dropArea.addEventListener(eName, e => { 
            e.preventDefault(); 
            dropArea.classList.remove("bg-indigo-100","scale-105"); 
        }));

        dropArea.addEventListener("drop", e => handleFiles(e.dataTransfer.files));
        input.addEventListener("change", e => handleFiles(e.target.files));

        clearBtn.addEventListener("click", () => {
            filesArray = [];
            input.value = "";
            updateCounter();
            updateThumbnails();
        });

        function handleFiles(files) {
            for (const file of files) {
                if (!file.type.startsWith("image/")) { 
                    showToast(`❌ ${file.name} no es una imagen válida.`); 
                    continue; 
                }
                if (file.size > maxSize) { 
                    showToast(`⚠️ ${file.name} supera los 2 MB.`); 
                    continue; 
                }
                if (filesArray.length >= maxFiles) { 
                    showToast(`⚠️ Solo puedes subir un máximo de ${maxFiles} imágenes.`); 
                    break; 
                }
                if (!filesArray.some(f => f.name === file.name && f.size === file.size)) {
                    filesArray.push(file);
                }
            }
            input.files = createFileList(filesArray);
            updateCounter();
            updateThumbnails();
        }

        function createFileList(files) {
            const dt = new DataTransfer();
            files.forEach(f => dt.items.add(f));
            return dt.files;
        }

        function updateCounter() {
            const count = filesArray.length;
            counter.textContent = `${count} / ${maxFiles} imágenes seleccionadas`;
            counter.className = count >= maxFiles ? "text-sm text-red-600 font-semibold" : "text-sm text-gray-500";
            clearBtn.classList.toggle("hidden", count === 0);
        }

        function updateThumbnails() {
            thumbnails.innerHTML = "";
            filesArray.forEach(file => {
                const reader = new FileReader();
                reader.onload = e => {
                    const div = document.createElement("div");
                    div.className = "relative border border-gray-300 rounded-lg shadow-sm bg-white flex justify-center items-center hover:scale-105 transition-transform";
                    div.style.width = "180px";
                    div.style.height = "180px";
                    div.style.padding = "8px";

                    const img = document.createElement("img");
                    img.src = e.target.result;
                    img.style.maxWidth = "100%";
                    img.style.maxHeight = "100%";
                    img.style.objectFit = "contain";
                    img.style.borderRadius = "6px";

                    img.addEventListener("click", () => {
                        modalImg.src = e.target.result;
                        modal.classList.remove("hidden");
                    });

                    const btn = document.createElement("button");
                    btn.type = "button";
                    btn.innerHTML = "✖";
                    btn.className = "absolute top-2 right-2 bg-red-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs shadow";
                    btn.addEventListener("click", (ev) => {
                        ev.stopPropagation(); 
                        filesArray = filesArray.filter(f => f !== file);
                        input.files = createFileList(filesArray);
                        updateCounter();
                        updateThumbnails();
                    });

                    div.appendChild(img);
                    div.appendChild(btn);
                    thumbnails.appendChild(div);
                };
                reader.readAsDataURL(file);
            });
        }

        function showToast(msg) {
            const toast = document.createElement("div");
            toast.textContent = msg;
            toast.className = "bg-red-600 text-white px-4 py-2 rounded shadow-md animate-fade-in";
            toastContainer.appendChild(toast);
            setTimeout(() => toast.remove(), 3000);
        }

        closeModal.addEventListener("click", () => modal.classList.add("hidden"));
        modal.addEventListener("click", e => { if (e.target === modal) modal.classList.add("hidden"); });
    });
    </script>

    <!-- Script de Acuerdos -->
    <script>
    document.addEventListener("DOMContentLoaded", () => {
        const tabla = document.getElementById("tabla-acuerdos");
        const btnAgregar = document.getElementById("agregar-acuerdo");

        function actualizarNumeros() {
            if(!tabla) return;
            const filas = tabla.querySelectorAll("tr");
            filas.forEach((fila, idx) => {
                fila.querySelector("td:first-child").textContent = idx + 1;
                const input = fila.querySelector("input");
                if(input) input.name = `acuerdos[${idx}][descripcion]`;
            });
        }

        function eliminarFila(fila) {
            fila.remove();
            actualizarNumeros();
        }

        if(tabla) {
            tabla.addEventListener("click", (e) => {
                if (e.target.classList.contains("eliminar-fila-generica")) {
                    const fila = e.target.closest("tr");
                    eliminarFila(fila);
                }
            });
        }

        if(btnAgregar) {
            btnAgregar.addEventListener("click", () => {
                const index = tabla.querySelectorAll("tr").length;
                const tr = document.createElement("tr");

                tr.innerHTML = `
                    <td class="border border-gray-400 px-2 py-1 text-center">${index + 1}</td>
                    <td class="border border-gray-400 px-2 py-1">
                        <input type="text" name="acuerdos[${index}][descripcion]" required class="w-full border-gray-300 rounded-md p-1">
                    </td>
                    <td class="border border-gray-400 px-2 py-1 text-center">
                        <button type="button" class="text-red-600 font-bold eliminar-fila-generica">✖</button>
                    </td>
                `;
                tabla.appendChild(tr);
            });
        }
    });
    </script>

    <!-- Script de Observaciones -->
    <script>
    document.addEventListener("DOMContentLoaded", () => {
        const tablaObs = document.getElementById("tabla-observaciones");
        const btnAgregarObs = document.getElementById("agregar-observacion");

        function actualizarNumerosObs() {
            if(!tablaObs) return;
            const filas = tablaObs.querySelectorAll("tr");
            filas.forEach((fila, idx) => {
                fila.querySelector("td:first-child").textContent = idx + 1;
                const input = fila.querySelector("input");
                if(input) input.name = `observaciones[${idx}][descripcion]`;
            });
        }

        function eliminarFilaObs(fila) {
            fila.remove();
            actualizarNumerosObs();
        }

        if(tablaObs) {
            tablaObs.addEventListener("click", (e) => {
                if (e.target.classList.contains("eliminar-fila-generica")) {
                    const fila = e.target.closest("tr");
                    eliminarFilaObs(fila);
                }
            });
        }

        if(btnAgregarObs) {
            btnAgregarObs.addEventListener("click", () => {
                const index = tablaObs.querySelectorAll("tr").length;
                const tr = document.createElement("tr");

                tr.innerHTML = `
                    <td class="border border-gray-400 px-2 py-1 text-center">${index + 1}</td>
                    <td class="border border-gray-400 px-2 py-1">
                        <input type="text" name="observaciones[${index}][descripcion]" required class="w-full border-gray-300 rounded-md p-1">
                    </td>
                    <td class="border border-gray-400 px-2 py-1 text-center">
                        <button type="button" class="text-red-600 font-bold eliminar-fila-generica">✖</button>
                    </td>
                `;
                tablaObs.appendChild(tr);
            });
        }
    });
    </script>

    <!-- Script Guardar (SweetAlert) -->
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        const btnGuardar = document.getElementById('btnGuardar');
        const formActa = document.getElementById('actaForm');

        if(btnGuardar) {
            btnGuardar.addEventListener('click', function(e) {
                e.preventDefault(); 
                Swal.fire({
                    title: '¿Está seguro(a) de guardar el acta?',
                    text: "En caso de no estar seguro(a), revise los datos antes de confirmar.",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#4f46e5',
                    cancelButtonColor: '#ef4444', 
                    confirmButtonText: 'Sí, guardar',
                    cancelButtonText: 'Cancelar',
                    reverseButtons: true,
                }).then((result) => {
                    if (result.isConfirmed) {
                        formActa.submit();
                    }
                });
            });
        }
    });
    </script>

    <!-- Scripts Generales (Autocomplete y Reindexado) -->
    <script>
    $(function() {
        // ---------- CONFIG ----------
        const buscarUrl = "{{ route('establecimientos.buscar') }}";

        const prefixMap = {
            '#tabla-participantes': 'participantes',
            '#tabla-actividades': 'actividades',
            '#tabla-acuerdos': 'acuerdos',
            '#tabla-observaciones': 'observaciones'
        };

        // ---------- UTIL ----------
        function reindexRows(tbodySelector) {
            $(tbodySelector).find('tr').each(function(i){
                $(this).find('td').first().text(i + 1);
                $(this).find('input, select, textarea').each(function(){
                    const $el = $(this);
                    const name = $el.attr('name');
                    if (name) {
                        const nuevo = name.replace(/\[\d+\]/, '[' + i + ']');
                        $el.attr('name', nuevo);
                    } else if ($el.data('base')) {
                        const base = $el.data('base');
                        const prefix = prefixMap[tbodySelector] || 'items';
                        $el.attr('name', `${prefix}[${i}][${base}]`);
                    }
                });
            });
        }

        // ---------- AUTOCOMPLETE ----------
        let xhrAutocomplete = null;
        $("#establecimiento").autocomplete({
            minLength: 1,
            delay: 200,
            source: function(request, response) {
                if (xhrAutocomplete && xhrAutocomplete.readyState !== 4) xhrAutocomplete.abort();
                xhrAutocomplete = $.ajax({
                    url: buscarUrl,
                    method: "GET",
                    dataType: "json",
                    data: { term: request.term },
                    success: function(data) {
                        if (data && data.data && Array.isArray(data.data)) data = data.data;
                        if (!Array.isArray(data)) { response([]); return; }
                        const items = data.map(item => ({
                            id: item.id || '',
                            label: (item.label || ((item.codigo ? item.codigo + ' - ' : '') + (item.nombre || ''))).trim(),
                            value: item.value || item.nombre || item.label || '',
                            provincia: item.provincia || '',
                            distrito: item.distrito || '',
                            categoria: item.categoria || '',
                            red: item.red || '',
                            microred: item.microred || '',
                            responsable: item.responsable || ''
                        }));
                        response(items);
                    },
                    error: function(xhr, status, err) {
                        if (status !== 'abort') response([]);
                    }
                });
            },
            select: function(event, ui) {
                event.preventDefault();
                $("#establecimiento_id").val(ui.item.id || '');
                $("#establecimiento").val(ui.item.value || ui.item.label || '');
                $("#provincia").val(ui.item.provincia || '');
                $("#distrito").val(ui.item.distrito || '');
                $("#microred").val(ui.item.microred || '');
                $("#red").val(ui.item.red || '');
                $("#responsable").val(ui.item.responsable || '');
                $(this).data('selected', $("#establecimiento").val());
                return false;
            }
        });

        // Limpiar campos si borran establecimiento
        $("#establecimiento").on('input', function(){
            const v = $(this).val().trim();
            const selected = $(this).data('selected') || '';
            if (v.length === 0 || v !== selected) {
                $("#establecimiento_id").val('');
                $("#provincia, #distrito, #microred, #red, #responsable").val('');
            }
        });

        // ---------- TABLA PARTICIPANTES (Usando Template) ----------
        const tplPart = document.getElementById('fila-participante');
        
        $('#agregar-participante').on('click', function(e){
            e.preventDefault();
            const clone = tplPart.content.cloneNode(true);
            $('#tabla-participantes').append(clone);
            reindexRows('#tabla-participantes');
        });

        $(document).on('click', '.eliminar-fila', function(e){
            e.preventDefault();
            $(this).closest('tr').remove();
            reindexRows('#tabla-participantes');
        });

        // Reindex inicial
        reindexRows('#tabla-participantes');
        reindexRows('#tabla-actividades');
        reindexRows('#tabla-acuerdos');
        reindexRows('#tabla-observaciones');
    });
    </script>
@endpush