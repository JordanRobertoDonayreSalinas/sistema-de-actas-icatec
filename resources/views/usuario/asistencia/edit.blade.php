@extends('layouts.usuario')

@section('title', 'Editar acta')

{{-- 1. ESTILOS: Copiados exactamente de tu Create --}}
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

        /* Sistema de Slots para Edición: Integrado en el estilo original */
        .slot-foto {
            aspect-ratio: 1 / 1;
            border: 2px dashed #cbd5e1;
            border-radius: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
            background-color: #f9fafb;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .slot-foto.occupied { border: 2px solid #4f46e5; background-color: white; }
        .slot-foto.marked-delete { filter: grayscale(1); opacity: 0.25; pointer-events: none; transform: scale(0.96); }
        
        .btn-delete-action {
            position: absolute;
            top: 0.4rem;
            right: 0.4rem;
            background-color: #ef4444;
            color: white;
            width: 22px;
            height: 22px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 40;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
            font-size: 10px;
        }

        /* Estilos de tabla del Create */
        .tabla-asistencia { width: 100%; border-collapse: collapse; }
        .tabla-asistencia thead th { 
            background-color: #f3f4f6; 
            border: 1px solid #9ca3af; 
            padding: 8px; 
            text-align: center;
        }
        .tabla-asistencia tbody td { border: 1px solid #9ca3af; padding: 4px; }
    </style>
@endpush

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

{{-- 2. CONTENIDO: Espejo total del formulario Create --}}
@section('content')
    <div class="py-10 bg-gradient-to-r from-blue-50 to-indigo-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <form id="actaForm"
                  action="{{ route('usuario.actas.update', $acta->id) }}"
                  method="POST"
                  enctype="multipart/form-data"
                  class="bg-white shadow-2xl rounded-xl p-8 w-full border border-gray-300">

                @csrf
                @method('PUT')

                <h1 class="text-3xl font-extrabold text-center uppercase text-indigo-700 underline mb-8">
                    Acta de Asistencia Técnica
                </h1>

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
                        <input type="hidden" id="establecimiento_id" name="establecimiento_id" value="{{ $acta->establecimiento_id }}">
                    </div>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-6">
                    <div>
                        <label class="block text-gray-700 font-semibold">Distrito:</label>
                        <input type="text" id="distrito" name="distrito" readonly required value="{{ $acta->establecimiento->distrito ?? '' }}" class="mt-1 block w-full border border-gray-300 rounded-lg bg-gray-100 p-2">
                    </div>
                    <div>
                        <label class="block text-gray-700 font-semibold">Provincia:</label>
                        <input type="text" id="provincia" name="provincia" readonly required value="{{ $acta->establecimiento->provincia ?? '' }}" class="mt-1 block w-full border border-gray-300 rounded-lg bg-gray-100 p-2">
                    </div>
                    <div>
                        <label class="block text-gray-700 font-semibold">Microred:</label>
                        <input type="text" id="microred" name="microred" readonly required value="{{ $acta->establecimiento->microred ?? '' }}" class="mt-1 block w-full border border-gray-300 rounded-lg bg-gray-100 p-2">
                    </div>
                    <div>
                        <label class="block text-gray-700 font-semibold">Red:</label>
                        <input type="text" id="red" name="red" readonly required value="{{ $acta->establecimiento->red ?? '' }}" class="mt-1 block w-full border border-gray-300 rounded-lg bg-gray-100 p-2">
                    </div>
                    <div class="col-span-2 md:col-span-4">
                        <label class="block text-gray-700 font-semibold">Responsable:</label>
                        <input type="text" id="responsable" name="responsable" placeholder="Nombre del responsable" required
                               value="{{ old('responsable', $acta->responsable) }}"
                               class="mt-1 block w-full border border-indigo-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 p-2">
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 font-semibold">Tema / Motivo:</label>
                    <select name="tema" required class="mt-1 block w-full border border-indigo-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 p-2">
                        <option value="">Seleccione un motivo...</option>
                        <option value="Reactivación de módulo" {{ old('tema', $acta->tema) == 'Reactivación de módulo' ? 'selected' : '' }}>Reactivación de módulo</option>
                        <option value="Cambio de responsable del módulo" {{ old('tema', $acta->tema) == 'Cambio de responsable del módulo' ? 'selected' : '' }}>Cambio de responsable del módulo</option>
                        <option value="Ingreso de nuevo personal" {{ old('tema', $acta->tema) == 'Ingreso de nuevo personal' ? 'selected' : '' }}>Ingreso de nuevo personal</option>
                        <option value="Actualización de cartera de servicios" {{ old('tema', $acta->tema) == 'Actualización de cartera de servicios' ? 'selected' : '' }}>Actualización de cartera de servicios</option>
                        <option value="Otros" {{ old('tema', $acta->tema) == 'Otros' ? 'selected' : '' }}>Otros</option>
                    </select>
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 font-semibold mb-2">Modalidad de asistencia:</label>
                    <div class="flex space-x-6">
                        <label class="flex items-center space-x-2 cursor-pointer">
                            <input type="radio" name="modalidad" value="Presencial" required {{ old('modalidad', $acta->modalidad) == 'Presencial' ? 'checked' : '' }} class="text-indigo-600 focus:ring-indigo-500">
                            <span>Presencial</span>
                        </label>
                        <label class="flex items-center space-x-2 cursor-pointer">
                            <input type="radio" name="modalidad" value="Virtual" required {{ old('modalidad', $acta->modalidad) == 'Virtual' ? 'checked' : '' }} class="text-indigo-600 focus:ring-indigo-500">
                            <span>Virtual</span>
                        </label>
                        <label class="flex items-center space-x-2 cursor-pointer">
                            <input type="radio" name="modalidad" value="Telefónica" required {{ old('modalidad', $acta->modalidad) == 'Telefónica' ? 'checked' : '' }} class="text-indigo-600 focus:ring-indigo-500">
                            <span>Telefónica</span>
                        </label>
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 font-semibold">Implementador(a):</label>
                    <select name="implementador" required class="mt-1 block w-full border border-indigo-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 p-2">
                        <option value="">Seleccione un implementador...</option>
                        @foreach($usuariosRegistrados as $u)
                            @php $nombreComp = trim("{$u->apellido_paterno} {$u->apellido_materno} {$u->name}"); @endphp
                            <option value="{{ $nombreComp }}" {{ old('implementador', $acta->implementador) == $nombreComp ? 'selected' : '' }}>{{ $nombreComp }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 font-bold text-lg mb-2 underline">Participantes:</label>
                    <div class="overflow-x-auto">
                        <table class="w-full border border-gray-400">
                            <thead class="bg-gray-200">
                                <tr>
                                    <th class="border border-gray-400 px-2 py-1 w-12 text-center text-xs">N°</th>
                                    <th class="border border-gray-400 px-2 py-1 text-xs">DNI</th>
                                    <th class="border border-gray-400 px-2 py-1 text-xs">Apellidos</th>
                                    <th class="border border-gray-400 px-2 py-1 text-xs">Nombres</th>
                                    <th class="border border-gray-400 px-2 py-1 text-xs">Cargo</th>
                                    <th class="border border-gray-400 px-2 py-1 text-xs">Módulo</th>
                                    <th class="border border-gray-400 px-2 py-1 text-xs">Unidad Ejecutora</th>
                                    <th class="border border-gray-400 px-2 py-1 w-16 text-center text-xs">Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="tabla-participantes">
                                @php
                                    $modulosSalud = ["Atencion Prenatal", "Citas", "Consulta Externa: Medicina", "Consulta Externa: Nutricion", "Consulta Externa: Odontologia", "Consulta Externa: Psicologia", "Cred", "Farmacia", "FUA", "Gestión Administrativa", "Inmunizaciones", "Laboratorio", "Parto", "Planificacion Familiar", "Puerperio", "Teleatiendo", "Triaje", "VIH"];
                                    $unidadesEjecutoras = ["DIRESA ICA","RED DE SALUD ICA","HOSPITAL SAN JOSE DE CHINCHA","HOSPITAL SAN JUAN DE DIOS PISCO","HOSPITAL DE APOYO PALPA","HOSPITAL DE APOYO NAZCA"];
                                @endphp
                                @foreach(old('participantes', $acta->participantes) as $i => $p)
                                    <tr>
                                        <td class="border border-gray-400 px-2 py-1 text-center font-bold">{{ $i + 1 }}</td>
                                        <td class="border border-gray-400 px-2 py-1"><input type="text" name="participantes[{{ $i }}][dni]" data-base="dni" value="{{ $p['dni'] ?? $p->dni }}" required class="w-full border-gray-300 rounded-md p-1 text-sm"></td>
                                        <td class="border border-gray-400 px-2 py-1"><input type="text" name="participantes[{{ $i }}][apellidos]" data-base="apellidos" value="{{ $p['apellidos'] ?? $p->apellidos }}" required class="w-full border-gray-300 rounded-md p-1 text-sm uppercase"></td>
                                        <td class="border border-gray-400 px-2 py-1"><input type="text" name="participantes[{{ $i }}][nombres]" data-base="nombres" value="{{ $p['nombres'] ?? $p->nombres }}" required class="w-full border-gray-300 rounded-md p-1 text-sm uppercase"></td>
                                        <td class="border border-gray-400 px-2 py-1"><input type="text" name="participantes[{{ $i }}][cargo]" data-base="cargo" value="{{ $p['cargo'] ?? $p->cargo }}" required class="w-full border-gray-300 rounded-md p-1 text-sm"></td>
                                        <td class="border border-gray-400 px-2 py-1">
                                            <select name="participantes[{{ $i }}][modulo]" data-base="modulo" class="w-full border-gray-300 rounded-md p-1 text-sm bg-white">
                                                <option value="">-- No aplica --</option>
                                                @foreach($modulosSalud as $opcion) <option value="{{ $opcion }}" {{ ($p['modulo'] ?? $p->modulo) == $opcion ? 'selected' : '' }}>{{ $opcion }}</option> @endforeach
                                            </select>
                                        </td>
                                        <td class="border border-gray-400 px-2 py-1">
                                            <select name="participantes[{{ $i }}][unidad_ejecutora]" data-base="unidad_ejecutora" class="w-full border-gray-300 rounded-md p-1 text-sm bg-white">
                                                <option value="">-- No aplica --</option>
                                                @foreach($unidadesEjecutoras as $opcion) <option value="{{ $opcion }}" {{ ($p['unidad_ejecutora'] ?? $p->unidad_ejecutora) == $opcion ? 'selected' : '' }}>{{ $opcion }}</option> @endforeach
                                            </select>
                                        </td>
                                        <td class="border border-gray-400 px-2 py-1 text-center"><button type="button" class="text-red-600 font-bold eliminar-fila">✖</button></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <button type="button" id="agregar-participante" class="mt-2 px-3 py-1 bg-green-600 text-white rounded-md hover:bg-green-700">+ Agregar participante</button>
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 font-bold text-lg mb-2 underline text-blue-700 uppercase">Actividades desarrolladas:</label>
                    <table class="w-full border border-gray-400 shadow-sm">
                        <thead class="bg-gray-200">
                            <tr><th class="border border-gray-400 px-2 py-1 w-12 text-center text-xs">N°</th><th class="border border-gray-400 px-2 py-1 text-xs">Descripción</th><th class="border border-gray-400 px-2 py-1 w-16 text-center text-xs">Acciones</th></tr>
                        </thead>
                        <tbody id="tabla-actividades">
                            @foreach(old('actividades', $acta->actividades) as $i => $a)
                                <tr>
                                    <td class="border border-gray-400 px-2 py-1 text-center font-bold text-indigo-600 bg-gray-50">{{ $i + 1 }}</td>
                                    <td class="border border-gray-400 px-2 py-1"><input type="text" name="actividades[{{ $i }}][descripcion]" value="{{ $a['descripcion'] ?? $a->descripcion }}" required class="w-full border-gray-300 rounded-md p-1 text-sm"></td>
                                    <td class="border border-gray-400 px-2 py-1 text-center"><button type="button" class="text-red-600 font-bold eliminar-fila-generica">✖</button></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <button type="button" id="agregar-actividad" class="mt-2 px-3 py-1 bg-blue-600 text-white rounded-md hover:bg-blue-700">+ Agregar actividad</button>
                </div>

                <div class="mb-10 p-6 bg-slate-50 border-2 border-indigo-100 rounded-2xl shadow-inner">
                    <label class="block text-indigo-800 font-black text-xl mb-6 underline tracking-tight">Adjuntar Imágenes (máx. 5):</label>
                    
                    <div id="grid-evidencias" class="grid grid-cols-2 md:grid-cols-5 gap-6 mb-6">
                        @for($i = 1; $i <= 5; $i++)
                            @php $campoSql = "imagen$i"; @endphp
                            <div class="slot-foto {{ $acta->$campoSql ? 'occupied' : '' }}" id="slot-{{ $i }}" data-slot="{{ $i }}" data-occupied="{{ $acta->$campoSql ? 'true' : 'false' }}">
                                @if($acta->$campoSql)
                                    {{-- Previsualización servidor --}}
                                    <img src="{{ asset('storage/' . $acta->$campoSql) }}" class="w-full h-full object-cover btn-ver-imagen cursor-zoom-in">
                                    <button type="button" class="btn-delete-action btn-eliminar-existente" data-campo="{{ $campoSql }}" data-slot="{{ $i }}">✖</button>
                                    <input type="hidden" name="eliminar_imagenes[]" id="input-eliminar-{{ $campoSql }}" value="" disabled>
                                @else
                                    {{-- Placeholder visual --}}
                                    <div class="flex flex-col items-center gap-2 text-slate-300 placeholder-info">
                                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        <span class="text-[9px] font-black uppercase tracking-widest text-center">Espacio {{ $i }}</span>
                                    </div>
                                @endif
                            </div>
                        @endfor
                    </div>

                    <div id="drop-area" class="w-full p-8 border-2 border-dashed border-indigo-400 rounded-3xl cursor-pointer text-center bg-white hover:bg-indigo-50 transition-all group relative">
                        <input type="file" id="input-files" name="imagenes[]" accept="image/*" multiple class="hidden">
                        <p class="text-indigo-900 font-bold text-lg">Haz clic o arrastra nuevas fotos aquí</p>
                        <p class="text-xs text-indigo-400 italic uppercase">Los espacios libres se ocuparán automáticamente</p>
                    </div>
                    <div class="flex items-center justify-between mt-2">
                        <p id="file-counter" class="text-sm text-gray-500 italic">0 nuevas imágenes seleccionadas</p>
                        <button type="button" id="clear-all" class="text-xs text-red-600 font-semibold hover:underline hidden">Quitar todas las nuevas</button>
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 font-bold text-lg mb-2 underline text-purple-700 uppercase">Acuerdos y compromisos:</label>
                    <table class="w-full border border-gray-400 shadow-sm">
                        <thead class="bg-gray-200">
                            <tr><th class="border border-gray-400 px-2 py-1 w-12 text-center text-xs">N°</th><th class="border border-gray-400 px-2 py-1 text-xs">Descripción</th><th class="border border-gray-400 px-2 py-1 w-16 text-center text-xs">Acciones</th></tr>
                        </thead>
                        <tbody id="tabla-acuerdos">
                            @foreach(old('acuerdos', $acta->acuerdos) as $i => $ac)
                                <tr>
                                    <td class="border border-gray-400 px-2 py-1 text-center font-bold text-indigo-600 bg-gray-50">{{ $i + 1 }}</td>
                                    <td class="border border-gray-400 px-2 py-1"><input type="text" name="acuerdos[{{ $i }}][descripcion]" value="{{ $ac['descripcion'] ?? $ac->descripcion }}" required class="w-full border-gray-300 rounded-md p-1 text-sm"></td>
                                    <td class="border border-gray-400 px-2 py-1 text-center"><button type="button" class="text-red-600 font-bold eliminar-fila-generica">✖</button></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <button type="button" id="agregar-acuerdo" class="mt-2 px-3 py-1 bg-purple-600 text-white rounded-md hover:bg-purple-700">+ Agregar acuerdo</button>
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 font-bold text-lg mb-2 underline text-yellow-700 uppercase">Observaciones:</label>
                    <table class="w-full border border-gray-400 shadow-sm">
                        <thead class="bg-gray-200">
                            <tr><th class="border border-gray-400 px-2 py-1 w-12 text-center text-xs">N°</th><th class="border border-gray-400 px-2 py-1 text-xs">Descripción</th><th class="border border-gray-400 px-2 py-1 w-16 text-center text-xs">Acciones</th></tr>
                        </thead>
                        <tbody id="tabla-observaciones">
                            @foreach(old('observaciones', $acta->observaciones) as $i => $o)
                                <tr>
                                    <td class="border border-gray-400 px-2 py-1 text-center font-bold text-indigo-600 bg-gray-50">{{ $i + 1 }}</td>
                                    <td class="border border-gray-400 px-2 py-1"><input type="text" name="observaciones[{{ $i }}][descripcion]" value="{{ $o['descripcion'] ?? $o->descripcion }}" required class="w-full border-gray-300 rounded-md p-1 text-sm"></td>
                                    <td class="border border-gray-400 px-2 py-1 text-center"><button type="button" class="text-red-600 font-bold eliminar-fila-generica">✖</button></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <button type="button" id="agregar-observacion" class="mt-2 px-3 py-1 bg-yellow-600 text-white rounded-md hover:bg-yellow-700">+ Agregar observación</button>
                </div>

                <div class="text-center mt-12">
                    <button type="button" id="btnGuardar" 
                        class="px-12 py-4 bg-indigo-600 hover:bg-indigo-700 text-white font-black rounded-2xl shadow-2xl shadow-indigo-200 text-xl transition-all active:scale-95 flex items-center justify-center mx-auto gap-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        GUARDAR CAMBIOS DEL ACTA
                    </button>
                </div>

            </form>
        </div>
    </div>

    <div id="image-modal" class="fixed inset-0 bg-black/90 hidden items-center justify-center z-[500] backdrop-blur-sm p-4">
        <span id="close-modal" class="absolute top-6 right-8 text-white text-6xl cursor-pointer font-thin hover:text-indigo-400 transition-all">&times;</span>
        <img id="modal-img" class="max-w-full max-h-full rounded-lg shadow-2xl border-4 border-white/10" src="">
    </div>
    <div id="toast-container" class="fixed bottom-10 right-10 space-y-4 z-[1000]"></div>

    <template id="fila-participante">
        <tr>
            <td class="border border-gray-400 px-2 py-1 text-center font-bold text-indigo-600 bg-gray-50"></td>
            <td class="border border-gray-400 px-2 py-1"><input type="text" data-base="dni" class="w-full border-gray-300 rounded p-1 text-sm"></td>
            <td class="border border-gray-400 px-2 py-1"><input type="text" data-base="apellidos" class="w-full border-gray-300 rounded p-1 text-sm uppercase"></td>
            <td class="border border-gray-400 px-2 py-1"><input type="text" data-base="nombres" class="w-full border-gray-300 rounded p-1 text-sm uppercase"></td>
            <td class="border border-gray-400 px-2 py-1"><input type="text" data-base="cargo" class="w-full border-gray-300 rounded p-1 text-sm"></td>
            <td class="border border-gray-400 px-2 py-1">
                <select data-base="modulo" class="w-full border-gray-300 p-1 text-sm bg-white">
                    <option value="">-- No aplica --</option>
                    @foreach($modulosSalud as $opcion) <option value="{{ $opcion }}">{{ $opcion }}</option> @endforeach
                </select>
            </td>
            <td class="border border-gray-400 px-2 py-1">
                <select data-base="unidad_ejecutora" class="w-full border-gray-300 p-1 text-sm bg-white">
                    <option value="">-- No aplica --</option>
                    @foreach($unidadesEjecutoras as $opcion) <option value="{{ $opcion }}">{{ $opcion }}</option> @endforeach
                </select>
            </td>
            <td class="border border-gray-400 px-2 py-1 text-center"><button type="button" class="text-red-600 font-bold eliminar-fila">✖</button></td>
        </tr>
    </template>

    <template id="fila-generica">
        <tr>
            <td class="border border-gray-400 px-2 py-1 text-center font-bold text-indigo-600 bg-gray-50"></td>
            <td class="border border-gray-400 px-2 py-1">
                <input type="text" required class="w-full border-gray-300 rounded-md p-1 text-sm">
            </td>
            <td class="border border-gray-400 px-2 py-1 text-center">
                <button type="button" class="text-red-600 font-bold eliminar-fila-generica">✖</button>
            </td>
        </tr>
    </template>

@endsection

{{-- 3. SCRIPTS: Lógica detallada (Aprox. 400+ líneas de JS puro) --}}
@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(function() {
    const buscarUrl = "{{ route('establecimientos.buscar') }}";
    let bufferNuevasFotos = []; 

    const prefixMap = {
        '#tabla-participantes': 'participantes',
        '#tabla-actividades': 'actividades',
        '#tabla-acuerdos': 'acuerdos',
        '#tabla-observaciones': 'observaciones'
    };

    // ---------- UTILIDADES DE REINDEXADO ----------
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
    $("#establecimiento").autocomplete({
        minLength: 1,
        delay: 200,
        source: function(request, response) {
            $.getJSON(buscarUrl, { term: request.term }, response);
        },
        select: function(event, ui) {
            event.preventDefault();
            $("#establecimiento_id").val(ui.item.id);
            $("#establecimiento").val(ui.item.value);
            $("#provincia").val(ui.item.provincia);
            $("#distrito").val(ui.item.distrito);
            $("#microred").val(ui.item.microred);
            $("#red").val(ui.item.red);
            $("#responsable").val(ui.item.responsable);
            $(this).data('selected', ui.item.value);
            return false;
        }
    });

    $("#establecimiento").on('input', function(){
        const v = $(this).val().trim();
        if (v.length === 0 || v !== $(this).data('selected')) {
            $("#establecimiento_id").val('');
            $("#provincia, #distrito, #microred, #red, #responsable").val('');
        }
    });

    // ---------- DINAMISMO TABLAS ----------
    $('#agregar-participante').on('click', function(e){
        e.preventDefault();
        $('#tabla-participantes').append($('#fila-participante').html());
        reindexRows('#tabla-participantes');
    });

    $('#agregar-actividad').on('click', function(e){
        e.preventDefault();
        $('#tabla-actividades').append($('#fila-generica').html());
        reindexRows('#tabla-actividades');
    });

    $('#agregar-acuerdo').on('click', function(e){
        e.preventDefault();
        $('#tabla-acuerdos').append($('#fila-generica').html());
        reindexRows('#tabla-acuerdos');
    });

    $('#agregar-observacion').on('click', function(e){
        e.preventDefault();
        $('#tabla-observaciones').append($('#fila-generica').html());
        reindexRows('#tabla-observaciones');
    });

    $(document).on('click', '.eliminar-fila, .eliminar-fila-generica', function(e){
        e.preventDefault();
        const tbody = $(this).closest('tbody');
        $(this).closest('tr').fadeOut(200, function() { 
            $(this).remove(); 
            reindexRows('#' + tbody.attr('id'));
        });
    });

    // ---------- LÓGICA DE IMÁGENES (SISTEMA DE SLOTS) ----------
    $('.btn-eliminar-existente').click(function(e) {
        e.stopPropagation();
        const btn = $(this); const campo = btn.data('campo'); const slot = btn.data('slot');
        Swal.fire({
            title: '¿Marcar para eliminar?',
            text: "Se borrará físicamente de los archivos al actualizar.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            confirmButtonText: 'Sí, marcar'
        }).then((result) => {
            if (result.isConfirmed) {
                const slotEl = $(`#slot-${slot}`);
                slotEl.addClass('marked-delete').attr('data-occupied', 'false');
                $(`#input-eliminar-${campo}`).val(campo).prop('disabled', false);
                btn.hide();
                showToast("Evidencia marcada para borrado");
            }
        });
    });

    const inputFiles = document.getElementById('input-files');
    $('#drop-area').click(() => inputFiles.click());

    inputFiles.onchange = function(e) {
        const files = Array.from(e.target.files);
        files.forEach(file => {
            // Buscamos el primer slot que NO esté ocupado (ni original ni por una foto nueva)
            const nextSlot = $('.slot-foto[data-occupied="false"]').not('.marked-delete').first();
            
            if(nextSlot.length > 0) {
                const sId = nextSlot.data('slot');
                bufferNuevasFotos.push({ id: sId, file: file });
                
                const reader = new FileReader();
                reader.onload = (fileEv) => {
                    nextSlot.attr('data-occupied', 'true').addClass('occupied animate-fade-in');
                    nextSlot.html(`
                        <img src="${fileEv.target.result}" class="w-full h-full object-cover">
                        <button type="button" class="absolute top-1 right-1 bg-orange-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs btn-quitar-nueva" data-slot="${sId}">✖</button>
                    `);
                    actualizarUIFotos();
                };
                reader.readAsDataURL(file);
            }
        });
        inputFiles.value = ""; 
    };

    $(document).on('click', '.btn-quitar-nueva', function(e) {
        e.stopPropagation();
        const idS = $(this).data('slot');
        bufferNuevasFotos = bufferNuevasFotos.filter(i => i.id !== idS);
        $(`#slot-${idS}`).attr('data-occupied', 'false').removeClass('occupied').html(`<div class="flex flex-col items-center gap-2 text-slate-300 placeholder-info"><svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg><span class="text-[9px] font-bold uppercase tracking-tighter text-center">Espacio {{ $i }}</span></div>`);
        actualizarUIFotos();
    });

    function actualizarUIFotos() {
        $('#file-counter').text(`${bufferNuevasFotos.length} nuevas fotos seleccionadas`);
        $('#clear-all').toggleClass('hidden', bufferNuevasFotos.length === 0);
    }

    $('#clear-all').click(function() {
        bufferNuevasFotos = [];
        $('.slot-foto.occupied').not('.marked-delete').each(function() {
            const id = $(this).data('slot');
            $(this).attr('data-occupied', 'false').removeClass('occupied').html(`<div class="flex flex-col items-center gap-2 text-slate-300 placeholder-info"><svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg><span class="text-[9px] font-bold uppercase tracking-tighter text-center">Espacio Disponible</span></div>`);
        });
        actualizarUIFotos();
    });

    // ---------- ENVÍO FINAL ----------
    $('#btnGuardar').click(function(e) {
        e.preventDefault();
        Swal.fire({
            title: '¿Confirmar actualización?',
            text: "Se guardarán todos los cambios en el acta.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#4f46e5',
            confirmButtonText: 'Sí, Actualizar'
        }).then((res) => {
            if (res.isConfirmed) {
                const dt = new DataTransfer();
                bufferNuevasFotos.forEach(item => dt.items.add(item.file));
                inputFiles.files = dt.files;
                $('#actaForm').submit();
            }
        });
    });

    $(document).on('click', '.btn-ver-imagen', function() { $('#modal-img').attr('src', $(this).attr('src')); $('#image-modal').removeClass('hidden').addClass('flex'); });
    $('#close-modal, #image-modal').click(function(e) { if(e.target !== $('#modal-img')[0]) $('#image-modal').addClass('hidden'); });

    function showToast(msg) {
        const toast = $(`<div class="bg-indigo-600 text-white px-8 py-4 rounded-2xl shadow-2xl font-black text-sm animate-fade-in border-b-4 border-black/10">${msg}</div>`);
        $('#toast-container').append(toast);
        setTimeout(() => toast.fadeOut(() => toast.remove()), 4000);
    }

    // Inicialización inicial
    reindexRows('#tabla-participantes'); reindexRows('#tabla-actividades'); reindexRows('#tabla-acuerdos'); reindexRows('#tabla-observaciones');
});
</script>
@endpush