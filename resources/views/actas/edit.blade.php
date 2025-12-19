@extends('layouts.panel')
@section('title', 'Editar acta')
@section('header-content')
    <div class="flex flex-col">
        <h1 class="text-xl font-bold text-slate-800 tracking-tight">Editar acta</h1>
        <div class="flex items-center gap-2 text-xs text-slate-500 mt-0.5">
            <span>Plataforma</span>
            <span class="text-slate-300">•</span>
            <span>ID: {{ $acta->id }}</span>
        </div>
    </div>
@endsection
{{-- 1. ESTILOS --}}
@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    
    <style>
        /* Animación para el Toast */
        @keyframes fade-in { 
            from {opacity:0; transform:translateY(10px);} 
            to {opacity:1; transform:translateY(0);} 
        }
        .animate-fade-in { animation: fade-in 0.3s ease forwards; }
    </style>
@endpush

{{-- 2. CONTENIDO PRINCIPAL --}}
@section('content')
    <div class="py-10 bg-gradient-to-r from-blue-50 to-indigo-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <form id="actaForm"
                  action="{{ route('admin.actas.update', $acta->id) }}"
                  method="POST"
                  enctype="multipart/form-data"
                  class="bg-white shadow-2xl rounded-xl p-8 w-full border border-gray-300">

                @csrf
                @method('PUT')

                <h1 class="text-3xl font-extrabold text-center uppercase text-indigo-700 underline mb-8">
                    Editar Acta de Asistencia Técnica #{{ $acta->id }}
                </h1>

                <!-- ============================
                     DATOS GENERALES
                     ============================ -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-gray-700 font-semibold">Fecha:</label>
                        <input type="date" name="fecha" value="{{ old('fecha', $acta->fecha) }}" required
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

                <!-- Campos Automáticos (Readonly) -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-6">
                    <div>
                        <label class="block text-gray-700 font-semibold">Distrito:</label>
                        <input type="text" id="distrito" value="{{ old('distrito', $acta->establecimiento->distrito ?? '') }}" readonly required class="mt-1 block w-full border border-gray-300 rounded-lg bg-gray-100 p-2">
                    </div>
                    <div>
                        <label class="block text-gray-700 font-semibold">Provincia:</label>
                        <input type="text" id="provincia" value="{{ old('provincia', $acta->establecimiento->provincia ?? '') }}" readonly required class="mt-1 block w-full border border-gray-300 rounded-lg bg-gray-100 p-2">
                    </div>
                    <div>
                        <label class="block text-gray-700 font-semibold">Microred:</label>
                        <input type="text" id="microred" value="{{ old('microred', $acta->establecimiento->microred ?? '') }}" readonly required class="mt-1 block w-full border border-gray-300 rounded-lg bg-gray-100 p-2">
                    </div>
                    <div>
                        <label class="block text-gray-700 font-semibold">Red:</label>
                        <input type="text" id="red" value="{{ old('red', $acta->establecimiento->red ?? '') }}" readonly required class="mt-1 block w-full border border-gray-300 rounded-lg bg-gray-100 p-2">
                    </div>
                    <div class="col-span-2 md:col-span-4">
                        <label class="block text-gray-700 font-semibold">Responsable:</label>
                        <input type="text" id="responsable" name="responsable" value="{{ old('responsable', $acta->responsable) }}" required
                               class="mt-1 block w-full border border-indigo-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 p-2">
                    </div>
                </div>

                <!-- Tema -->
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

                <!-- Modalidad -->
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

                <!-- Implementador -->
                <div class="mb-6">
                    <label class="block text-gray-700 font-semibold">Implementador(a):</label>
                    <select name="implementador" required class="mt-1 block w-full border border-indigo-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 p-2">
                        <option value="">Seleccione un implementador...</option>
                        <option value="Juan Carlos Gutierrez Hilario" {{ old('implementador', $acta->implementador ?? '') == 'Juan Carlos Gutierrez Hilario' ? 'selected' : '' }}>Juan Carlos Gutierrez Hilario</option>
                        <option value="Erick Montes Guillermo" {{ old('implementador', $acta->implementador ?? '') == 'Erick Montes Guillermo' ? 'selected' : '' }}>Erick Montes Guillermo</option>
                        <option value="Jordan Roberto Donayre Salinas" {{ old('implementador', $acta->implementador ?? '') == 'Jordan Roberto Donayre Salinas' ? 'selected' : '' }}>Jordan Roberto Donayre Salinas</option>
                        <option value="Lida Graciela Yañez Medina" {{ old('implementador', $acta->implementador ?? '') == 'Lida Graciela Yañez Medina' ? 'selected' : '' }}>Lida Graciela Yañez Medina</option>
                        <option value="Carmen Selene Pineda Moran" {{ old('implementador', $acta->implementador ?? '') == 'Carmen Selene Pineda Moran' ? 'selected' : '' }}>Carmen Selene Pineda Moran</option>
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
                                    $modulos = ["Atencion Prenatal","Citas","Consulta Externa: Medicina","Consulta Externa: Nutricion",
"Consulta Externa: Odontologia","Consulta Externa: Psicologia","Cred","Gestión Administrativa","Inmunizaciones","Laboratorio",
"Parto","Planificacion Familiar","Puerperio","Teleatiendo","Triaje","VIH"];$unidades = ["DIRESA ICA","RED DE SALUD ICA","HOSPITAL SAN JOSE DE CHINCHA","HOSPITAL SAN JUAN DE DIOS PISCO","HOSPITAL DE APOYO PALPA","HOSPITAL DE APOYO NAZCA"];
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
                                            <td class="border border-gray-400 px-2 py-1 text-center"><button type="button" class="text-red-600 font-bold eliminar-fila" aria-label="Eliminar participante">✖</button></td>
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

                <!-- Plantilla fila participante -->
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
                    <label class="block text-gray-700 font-bold text-lg mb-2 underline">Adjuntar Imágenes (máx. 5)</label>
                    
                    <div id="drop-area" class="w-full p-6 border-2 border-dashed border-indigo-400 rounded-xl cursor-pointer text-center bg-indigo-50 hover:bg-indigo-100 transition relative">
                        <p class="text-gray-600">Haz clic o arrastra imágenes aquí</p>
                        <p class="text-xs text-gray-500">(máx. 5 imágenes, 2 MB cada una)</p>
                        <input type="file" id="imagenes" name="imagenes[]" accept="image/*" multiple class="hidden">
                    </div>

                    <div class="flex items-center justify-between mt-2">
                        <p id="file-counter" class="text-sm text-gray-500">0 / 5 imágenes seleccionadas</p>
                        <button type="button" id="clear-all" class="text-xs text-red-600 font-semibold hover:underline hidden">Quitar todas</button>
                    </div>

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

                <!-- Botón Guardar Cambios -->
                <div class="text-center mt-6">
                    <button type="button" id="btnGuardar" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-lg shadow-md text-lg">
                        Guardar Cambios
                    </button>
                </div>

            </form>
        </div>
    </div>

    <!-- Modales -->
    <div id="image-modal" class="fixed inset-0 bg-black bg-opacity-70 hidden items-center justify-center z-50">
        <span id="close-modal" class="absolute top-4 right-6 text-white text-3xl cursor-pointer font-bold">&times;</span>
        <img id="modal-img" class="max-w-[90%] max-h-[90%] rounded-lg shadow-lg" src="">
    </div>
    <div id="toast-container" class="fixed bottom-4 right-4 space-y-2 z-50"></div>

    <!-- IMPORTANTE: Esta plantilla faltaba en tu código original y es necesaria para que funcionen los botones de agregar -->
    <template id="fila-generica">
        <tr>
            <td class="border border-gray-400 px-2 py-1 text-center"></td>
            <td class="border border-gray-400 px-2 py-1">
                <input type="text" required class="w-full border-gray-300 rounded-md p-1">
            </td>
            <td class="border border-gray-400 px-2 py-1 text-center">
                <button type="button" class="text-red-600 font-bold eliminar-fila-generica">✖</button>
            </td>
        </tr>
    </template>

@endsection

{{-- 3. SCRIPTS --}}
@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
    document.addEventListener("DOMContentLoaded", () => {
        // --- 1. LÓGICA TABLAS GENÉRICAS (Actividades, Acuerdos, Obs) ---
        const tplGen = document.getElementById('fila-generica');

        function reindexRowsGeneric(tbodyId) {
            const table = document.getElementById(tbodyId);
            if(!table) return;
            const prefix = tbodyId.replace('tabla-', ''); // actividades, acuerdos, observaciones
            
            table.querySelectorAll('tr').forEach((tr, index) => {
                tr.querySelector('td:first-child').textContent = index + 1;
                const input = tr.querySelector('input');
                if(input) input.name = `${prefix}[${index}][descripcion]`;
            });
        }

        // Función para agregar fila genérica
        function setupGenericTable(type) {
            const btn = document.getElementById(`agregar-${type}`); // ej: agregar-actividad
            const tbodyId = `tabla-${type}s`; // ej: tabla-actividades (OJO: plural en ID)
            // Fix: en tu HTML los IDs son 'tabla-actividades', 'tabla-acuerdos', 'tabla-observaciones' (ya tienen plural)
            // Pero el botón es 'agregar-actividad' (singular). Ajustamos la lógica:
            
            let realTbodyId = `tabla-${type}`;
            if(type === 'actividad') realTbodyId = 'tabla-actividades';
            if(type === 'acuerdo') realTbodyId = 'tabla-acuerdos';
            if(type === 'observacion') realTbodyId = 'tabla-observaciones';

            const tableBody = document.getElementById(realTbodyId);

            if(btn && tableBody) {
                btn.addEventListener('click', (e) => {
                    e.preventDefault();
                    if(tplGen) {
                        const clone = tplGen.content.cloneNode(true);
                        tableBody.appendChild(clone);
                        reindexRowsGeneric(realTbodyId);
                    }
                });

                tableBody.addEventListener('click', (e) => {
                    if (e.target.classList.contains('eliminar-fila-generica')) {
                        e.target.closest('tr').remove();
                        reindexRowsGeneric(realTbodyId);
                    }
                });
                
                // Inicializar índices
                reindexRowsGeneric(realTbodyId);
            }
        }

        setupGenericTable('actividad');
        setupGenericTable('acuerdo');
        setupGenericTable('observacion');


        // --- 2. LÓGICA IMÁGENES (Drag & Drop) ---
        const input = document.getElementById("imagenes");
        const dropArea = document.getElementById("drop-area");
        const counter = document.getElementById("file-counter");
        const clearBtn = document.getElementById("clear-all");
        const thumbnails = document.getElementById("thumbnails");
        const toastContainer = document.getElementById("toast-container");
        const modal = document.getElementById("image-modal");
        const modalImg = document.getElementById("modal-img");
        const closeModal = document.getElementById("close-modal");

        if(dropArea) {
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
        }

        // --- 3. CONFIRMACIÓN GUARDAR ---
        const btnGuardar = document.getElementById('btnGuardar');
        const formActa = document.getElementById('actaForm');

        if(btnGuardar) {
            btnGuardar.addEventListener('click', function(e) {
                e.preventDefault(); 
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: '¿Guardar Cambios?',
                        text: "Se actualizará el acta con los nuevos datos.",
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#4f46e5',
                        cancelButtonColor: '#ef4444', 
                        confirmButtonText: 'Sí, actualizar',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) formActa.submit();
                    });
                } else {
                    if (confirm("¿Guardar cambios?")) formActa.submit();
                }
            });
        }
    });

    // --- 4. AUTOCOMPLETE Y PARTICIPANTES (JQUERY) ---
    $(function() {
        const buscarUrl = "{{ route('establecimientos.buscar') }}";
        
        // Autocomplete Establecimiento
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
                    error: function(xhr, status, err) { if (status !== 'abort') response([]); }
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

        // Tabla Participantes (Lógica jQuery)
        const tplPart = document.getElementById('fila-participante');
        
        function reindexParticipantes() {
            $('#tabla-participantes tr').each(function(i){
                $(this).find('td').first().text(i + 1);
                $(this).find('input, select').each(function(){
                    const $el = $(this);
                    const name = $el.attr('name');
                    if (name) {
                        const nuevo = name.replace(/\[\d+\]/, '[' + i + ']');
                        $el.attr('name', nuevo);
                    } else if ($el.data('base')) {
                        $el.attr('name', `participantes[${i}][${$el.data('base')}]`);
                    }
                });
            });
        }

        $('#agregar-participante').on('click', function(e){
            e.preventDefault();
            if(tplPart) {
                const clone = tplPart.content.cloneNode(true);
                $('#tabla-participantes').append(clone);
                reindexParticipantes();
            }
        });

        $(document).on('click', '.eliminar-fila', function(e){
            e.preventDefault();
            $(this).closest('tr').remove();
            reindexParticipantes();
        });

        reindexParticipantes();
    });
    </script>
@endpush