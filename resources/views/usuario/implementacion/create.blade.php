@extends('layouts.usuario')

@section('title', 'Nueva Acta ' . $moduloConfig['nombre'])

@section('header-content')
<div class="flex flex-wrap items-center justify-between w-full gap-4">
    <div>
        <h1 class="text-xl font-bold text-slate-800 tracking-tight">📝 Nueva Acta: {{ $moduloConfig['nombre'] }}</h1>
        <div class="flex items-center gap-2 text-xs text-slate-500 mt-0.5">
            <a href="{{ route('usuario.implementacion.index') }}" class="hover:text-blue-600">Actas</a>
            <span class="text-slate-300">•</span>
            <span>Crear</span>
        </div>
    </div>
    <a href="{{ route('usuario.implementacion.index') }}" class="flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 text-slate-700 rounded-lg hover:bg-slate-50 hover:text-blue-600 transition-colors text-sm font-semibold shadow-sm">
        <i data-lucide="arrow-left" class="w-4 h-4"></i>
        Regresar a la lista
    </a>
</div>
@endsection

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 mb-6">
        <form action="{{ route('usuario.implementacion.store') }}" method="POST" enctype="multipart/form-data" class="p-6 md:p-8 space-y-8">
            @csrf
            <input type="hidden" name="modulo_key" value="{{ $moduloKey }}">
            
            {{-- Sección: Datos Principales --}}
            <div>
                <div class="flex items-center gap-2 mb-4 border-b border-slate-100 pb-2">
                    <i data-lucide="book-open" class="w-4 h-4 text-blue-600"></i>
                    <h3 class="font-bold text-slate-800 text-sm uppercase tracking-wide">Módulo a Implementar</h3>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Módulo</label>
                        <select onchange="window.location.href='?modulo='+this.value" class="w-full bg-slate-50 border border-slate-200 rounded-xl text-sm p-2.5 outline-none focus:border-blue-500 font-bold text-slate-700">
                            @foreach($modulos as $k => $cfg)
                                <option value="{{ $k }}" {{ $k == $moduloKey ? 'selected' : '' }}>{{ $cfg['nombre'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Fecha de Implementación</label>
                        <input type="date" name="fecha" required value="{{ date('Y-m-d') }}" class="w-full border border-slate-200 rounded-xl text-sm p-2.5 outline-none focus:border-blue-500">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Acta Firmada (PDF)</label>
                        <div class="relative">
                            <input type="file" name="archivo_pdf" accept="application/pdf" class="w-full text-sm text-slate-500
                                file:mr-4 file:py-2.5 file:px-4 file:rounded-xl
                                file:border-0 file:text-sm file:font-bold
                                file:bg-blue-50 file:text-blue-700
                                hover:file:bg-blue-100 transition-all border border-slate-200 rounded-xl bg-white
                            ">
                        </div>
                        <p class="text-[10px] text-slate-400 mt-1"><i data-lucide="info" class="w-3 h-3 inline"></i> Opcional. Sube el documento escaneado con las firmas correspondientes.</p>
                    </div>
                </div>
            </div>

            {{-- Sección: Establecimiento --}}
            <div>
                <div class="flex items-center gap-2 mb-4 border-b border-slate-100 pb-2 mt-2">
                    <i data-lucide="hospital" class="w-4 h-4 text-blue-600"></i>
                    <h3 class="font-bold text-slate-800 text-sm uppercase tracking-wide">Datos del Establecimiento</h3>
                </div>
                
                <div class="relative mb-5">
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Buscar Establecimiento (Renipress / Nombre)</label>
                    <div class="relative">
                        <i data-lucide="search" class="absolute left-3 top-3 w-4 h-4 text-slate-400"></i>
                        <input type="text" id="busqueda_establecimiento" placeholder="Busque y seleccione el establecimiento..." autocomplete="off"
                            class="w-full pl-9 pr-4 py-2.5 border border-slate-200 rounded-xl text-sm outline-none focus:border-blue-500 bg-blue-50/30">
                    </div>
                    <ul id="sugerencias_establecimiento" class="absolute z-50 bg-white border border-slate-200 w-full rounded-xl shadow-xl mt-1 hidden max-h-60 overflow-y-auto divide-y divide-slate-50"></ul>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="col-span-1 md:col-span-2">
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Nombre Establecimiento</label>
                        <input type="text" id="nombre_establecimiento" name="nombre_establecimiento" readonly required class="w-full bg-slate-50 border border-slate-200 rounded-lg text-sm p-2 outline-none text-slate-500 font-medium">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Código RENIPRESS</label>
                        <input type="text" id="codigo_establecimiento" name="codigo_establecimiento" readonly required class="w-full bg-slate-50 border border-slate-200 rounded-lg text-sm p-2 outline-none text-slate-500 font-mono">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Provincia</label>
                        <input type="text" id="provincia" name="provincia" readonly class="w-full bg-slate-50 border border-slate-200 rounded-lg text-sm p-2 outline-none text-slate-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Distrito</label>
                        <input type="text" id="distrito" name="distrito" readonly class="w-full bg-slate-50 border border-slate-200 rounded-lg text-sm p-2 outline-none text-slate-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Categoría</label>
                        <input type="text" id="categoria" name="categoria" readonly class="w-full bg-slate-50 border border-slate-200 rounded-lg text-sm p-2 outline-none text-slate-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Red</label>
                        <input type="text" id="red" name="red" readonly class="w-full bg-slate-50 border border-slate-200 rounded-lg text-sm p-2 outline-none text-slate-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Microred</label>
                        <input type="text" id="microred" name="microred" readonly class="w-full bg-slate-50 border border-slate-200 rounded-lg text-sm p-2 outline-none text-slate-500">
                    </div>
                    <div class="md:col-span-3">
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Responsable del Establecimiento</label>
                        <input type="text" id="responsable" name="responsable" required placeholder="Nombre del médico jefe o responsable" class="w-full border border-slate-200 rounded-lg text-sm p-2 outline-none focus:border-blue-500">
                    </div>
                </div>
            </div>

            {{-- Modalidad --}}
            @if($moduloKey === 'citas')
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Modalidad de Implementación</label>
                <select name="modalidad" class="w-full md:w-1/2 border border-slate-200 rounded-xl text-sm p-2.5 outline-none focus:border-blue-500 bg-white">
                    <option value="">Seleccione Modalidad</option>
                    <option value="POR HORARIO">Por Horario</option>
                    <option value="POR SELECCION">Por Selección (Exclusividad)</option>
                </select>
            </div>
            @endif

            {{-- UPSS/UPS (Solo Gestión Administrativa - Solo Visual) --}}
            @if($moduloKey === 'ges_adm')
            <div id="seccion-upss" class="mb-4 space-y-4">
                {{-- Tabla 1: Renipress SUSALUD --}}
                <div class="border border-slate-200 rounded-xl p-4 bg-slate-50">
                    <h3 class="font-bold text-slate-800 text-sm mb-3">Renipress SUSALUD (UPS / UPSS)</h3>
                    <div class="bg-white rounded-lg overflow-hidden border border-slate-200">
                        <table class="w-full text-left text-xs text-slate-600">
                            <thead class="bg-blue-600 text-white font-semibold">
                                <tr>
                                    <th class="p-2">UPSS</th>
                                    <th class="p-2 border-l border-blue-500">Estado UPSS</th>
                                    <th class="p-2 border-l border-blue-500">UPS</th>
                                    <th class="p-2 border-l border-blue-500">Estado UPS</th>
                                </tr>
                            </thead>
                            <tbody id="upss-establecimiento-body">
                                <tr>
                                    <td colspan="4" class="p-3 text-center text-slate-400">Seleccione un establecimiento</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Tabla 2: Regularizar en Renipress SUSALUD --}}
                <div class="border border-slate-200 rounded-xl p-4 bg-slate-50">
                    <h3 class="font-bold text-slate-800 text-sm mb-3">(UPSS/UPS) Regularizar en Renipress SUSALUD</h3>
                    <div class="w-full mb-3 shadow-[0_4px_10px_rgba(0,0,0,0.1)] rounded p-2 bg-white relative">
                        <div class="relative w-full">
                            <input type="text" id="upss-search-input" class="w-full border-b border-slate-300 rounded text-sm p-3 outline-none focus:border-blue-500 shadow-inner" placeholder="Escriba código o descripción de UPSS/UPS para buscar..." onkeyup="buscarUpsGlobal(this)">
                            <div id="upss_global_results" class="absolute z-50 w-full bg-white border border-slate-200 shadow-xl mt-1 rounded hidden max-h-60 overflow-y-auto"></div>
                        </div>
                    </div>
                    
                    <button type="button" onclick="agregarUpssManual()" class="text-xs font-bold text-white bg-green-600 hover:bg-green-700 px-3 py-2 rounded-lg transition-colors flex items-center gap-1 mb-3">
                        + Agregar UPSS/UPS Manual
                    </button>
                    <div id="upss-container" class="space-y-2"></div>
                </div>
            </div>
            @endif

            {{-- Firma Digital (Solo Módulos Específicos) --}}
            @if(in_array($moduloKey, ['medicina', 'odontologia', 'nutricion', 'psicologia', 'mental', 'emergencia', 'referencias', 'laboratorio', 'farmacia', 'fua']))
            <div>
                <label class="block text-xs font-semibold text-slate-800 mb-1">Firma Digital</label>
                <select name="firma_digital" required class="w-full border border-slate-300 rounded text-sm p-2.5 outline-none focus:border-blue-500 bg-white">
                    <option value="">-- Seleccionar --</option>
                    <option value="SI">SI</option>
                    <option value="NO">NO</option>
                </select>
            </div>
            @endif

            {{-- Sección: Participantes (Usuarios) --}}
            <div>
                <div class="flex items-center justify-between mb-3 border-b border-slate-100 pb-2 mt-4">
                    <div class="flex items-center gap-2">
                        <i data-lucide="users" class="w-4 h-4 text-emerald-600"></i>
                        <h3 class="font-bold text-slate-800 text-sm uppercase tracking-wide">Usuarios Participantes</h3>
                    </div>
                    <button type="button" onclick="agregarParticipante()" class="text-xs font-bold text-emerald-600 bg-emerald-50 hover:bg-emerald-100 px-3 py-1.5 rounded-lg transition-colors flex items-center gap-1">
                        <i data-lucide="plus" class="w-3 h-3"></i> AGREGAR
                    </button>
                </div>
                <div id="usuarios-container" class="space-y-3"></div>
            </div>

            {{-- Sección: Implementadores --}}
            <div>
                <div class="flex items-center justify-between mb-3 border-b border-slate-100 pb-2 mt-4">
                    <div class="flex items-center gap-2">
                        <i data-lucide="user-check" class="w-4 h-4 text-purple-600"></i>
                        <h3 class="font-bold text-slate-800 text-sm uppercase tracking-wide">Personal Implementador</h3>
                    </div>
                    <button type="button" id="btn-add-implem" onclick="agregarImplementador()" class="text-xs font-bold text-purple-600 bg-purple-50 hover:bg-purple-100 px-3 py-1.5 rounded-lg transition-colors flex items-center gap-1">
                        <i data-lucide="plus" class="w-3 h-3"></i> AGREGAR
                    </button>
                </div>
                <div id="implementadores-container" class="space-y-3"></div>
            </div>

            {{-- Observaciones --}}
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Observaciones Finales</label>
                <textarea name="observaciones" rows="3" class="w-full border border-slate-200 rounded-xl text-sm p-3 outline-none focus:border-blue-500" placeholder="Ingrese anotaciones u observaciones sobre la implementación..."></textarea>
            </div>

            {{-- Footer / Botones --}}
            <div class="flex justify-end gap-3 pt-6 border-t border-slate-100">
                <a href="{{ route('usuario.implementacion.index') }}" class="px-5 py-2.5 rounded-xl font-bold text-sm text-slate-500 bg-slate-100 hover:bg-slate-200 transition-colors">Cancelar</a>
                <button type="submit" class="px-6 py-2.5 rounded-xl font-bold text-sm text-white bg-blue-600 hover:bg-blue-700 shadow-md shadow-blue-500/20 transition-all">Guardar Acta</button>
            </div>
            
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    lucide.createIcons();
    
    // --- ESTABLECIMIENTO AUTOCOMPLETE ---
    const inputEst = document.getElementById('busqueda_establecimiento');
    const listaEst = document.getElementById('sugerencias_establecimiento');

    inputEst.addEventListener('input', function () {
        const val = this.value.trim();
        if (val.length >= 3) {
            fetch(`/usuario/implementacion/ajax/establecimiento?q=${encodeURIComponent(val)}`) // Ajustaré la ruta en web.php
                .then(r => r.json())
                .then(data => {
                    listaEst.innerHTML = '';
                    if (data.length > 0) {
                        listaEst.classList.remove('hidden');
                        data.forEach(est => {
                            const li = document.createElement('li');
                            li.className = 'px-4 py-3 hover:bg-blue-50 cursor-pointer flex flex-col gap-0.5';
                            li.innerHTML = `<span class="font-bold text-slate-700 text-sm">${est.codigo_establecimiento} - ${est.nombre_establecimiento}</span>
                                            <span class="text-[10px] text-slate-500">${est.distrito}, ${est.provincia} (${est.categoria})</span>`;
                            li.addEventListener('click', () => {
                                document.getElementById('codigo_establecimiento').value = est.codigo_establecimiento;
                                document.getElementById('nombre_establecimiento').value = est.nombre_establecimiento;
                                document.getElementById('provincia').value = est.provincia;
                                document.getElementById('distrito').value = est.distrito;
                                document.getElementById('categoria').value = est.categoria;
                                document.getElementById('red').value = est.red ?? '';
                                document.getElementById('microred').value = est.microred ?? '';
                                document.getElementById('responsable').value = est.responsable ?? '';
                                inputEst.value = `${est.codigo_establecimiento} - ${est.nombre_establecimiento}`;
                                listaEst.classList.add('hidden');
                            });
                            listaEst.appendChild(li);
                        });
                    } else {
                        listaEst.classList.add('hidden');
                    }
                });
        } else {
            listaEst.classList.add('hidden');
        }
    });

    document.addEventListener('click', (e) => {
        if (!inputEst.contains(e.target) && !listaEst.contains(e.target)) listaEst.classList.add('hidden');
    });

    // --- PARTICIPANTES ---
    let idxUsu = 0;
    function agregarParticipante() {
        const tpl = `
        <div class="participante-row bg-slate-50/50 border border-slate-200 rounded-xl p-4 relative pr-10">
            <button type="button" onclick="this.closest('.participante-row').remove()" class="absolute top-4 right-4 text-slate-300 hover:text-red-500 transition-colors" title="Quitar">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg>
            </button>
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 text-sm">
                <div>
                    <label class="block text-[10px] uppercase font-bold text-slate-500 mb-1">DNI</label>
                    <div class="flex items-center gap-2">
                        <div class="relative w-full">
                            <input type="text" id="participante_dni_${idxUsu}" name="usuarios[${idxUsu}][dni]" maxlength="15" required class="w-full border-slate-200 rounded-lg p-2 h-9 outline-none focus:border-emerald-500 bg-white font-bold" onkeydown="if(event.key === 'Enter'){event.preventDefault(); buscarPersona('participante', ${idxUsu});}">
                            <div id="loading_participante_${idxUsu}" class="hidden absolute right-3 top-2">
                                <i data-lucide="loader-2" class="w-5 h-5 animate-spin text-emerald-600"></i>
                            </div>
                        </div>
                        <button type="button" onclick="buscarPersona('participante', ${idxUsu})" class="bg-emerald-600 hover:bg-emerald-700 text-white p-1.5 rounded-lg shadow-sm transition-colors flex-shrink-0" title="Buscar">
                            <i data-lucide="search" class="w-5 h-5"></i>
                        </button>
                    </div>
                    <p id="msg_participante_${idxUsu}" class="text-[10px] text-red-500 mt-1 hidden"></p>
                </div>
                <div><label class="block text-[10px] uppercase font-bold text-slate-500 mb-1">Ap. Paterno</label><input type="text" id="participante_ap_${idxUsu}" name="usuarios[${idxUsu}][apellido_paterno]" required class="w-full border-slate-200 rounded-lg p-2 h-9 outline-none focus:border-emerald-500 bg-white"></div>
                <div><label class="block text-[10px] uppercase font-bold text-slate-500 mb-1">Ap. Materno</label><input type="text" id="participante_am_${idxUsu}" name="usuarios[${idxUsu}][apellido_materno]" required class="w-full border-slate-200 rounded-lg p-2 h-9 outline-none focus:border-emerald-500 bg-white"></div>
                <div><label class="block text-[10px] uppercase font-bold text-slate-500 mb-1">Nombres</label><input type="text" id="participante_nom_${idxUsu}" name="usuarios[${idxUsu}][nombres]" required class="w-full border-slate-200 rounded-lg p-2 h-9 outline-none focus:border-emerald-500 bg-white"></div>
                <div><label class="block text-[10px] uppercase font-bold text-slate-500 mb-1">Celular</label><input type="text" name="usuarios[${idxUsu}][celular]" class="w-full border-slate-200 rounded-lg p-2 h-9 outline-none focus:border-emerald-500 bg-white"></div>
                <div><label class="block text-[10px] uppercase font-bold text-slate-500 mb-1">Correo</label><input type="email" name="usuarios[${idxUsu}][correo]" class="w-full border-slate-200 rounded-lg p-2 h-9 outline-none focus:border-emerald-500 bg-white"></div>
                <div class="col-span-2"><label class="block text-[10px] uppercase font-bold text-slate-500 mb-1">Estado Credencial</label>
                    <select name="usuarios[${idxUsu}][permisos]" required class="w-full border-slate-200 rounded-lg px-2 h-9 outline-none focus:border-emerald-500 bg-white text-sm">
                        <option value="">Seleccione...</option><option value="C.C. Y D.J.">Entregado (C.C. y D.J.)</option><option value="POR REGULARIZAR">Por regularizar</option>
                    </select>
                </div>
            </div>
        </div>`;
        document.getElementById('usuarios-container').insertAdjacentHTML('beforeend', tpl);
        idxUsu++;
        setTimeout(() => lucide.createIcons(), 10);
    }

    // --- IMPLEMENTADORES ---
    let idxImp = 0;
    function agregarImplementador() {
        let defaultDni = '';
        let defaultApPat = '';
        let defaultApMat = '';
        let defaultNom = '';
        let defaultCargo = 'IMPLEMENTADOR(A)';

        if (idxImp === 0) {
            defaultDni = '{{ Auth::user()->documento ?? Auth::user()->username ?? '' }}';
            defaultApPat = '{{ Auth::user()->apellido_paterno ?? '' }}';
            defaultApMat = '{{ Auth::user()->apellido_materno ?? '' }}';
            defaultNom = '{{ Auth::user()->name ?? '' }}';
        }

        const tpl = `
        <div class="implem-row bg-purple-50/30 border border-purple-100 rounded-xl p-4 relative pr-10">
            <button type="button" onclick="this.closest('.implem-row').remove();" class="absolute top-4 right-4 text-purple-300 hover:text-red-500 transition-colors" title="Quitar">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg>
            </button>
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 text-sm">
                <div>
                    <label class="block text-[10px] uppercase font-bold text-purple-600 mb-1">DNI</label>
                    <div class="flex items-center gap-2">
                        <div class="relative w-full">
                            <input type="text" id="implementador_dni_${idxImp}" name="implementadores[${idxImp}][dni]" maxlength="15" required class="w-full border-purple-200 rounded-lg p-2 h-9 outline-none focus:border-purple-500 bg-white font-bold" onkeydown="if(event.key === 'Enter'){event.preventDefault(); buscarPersona('implementador', ${idxImp});}" value="${defaultDni}">
                            <div id="loading_implementador_${idxImp}" class="hidden absolute right-3 top-2">
                                <i data-lucide="loader-2" class="w-5 h-5 animate-spin text-purple-600"></i>
                            </div>
                        </div>
                        <button type="button" onclick="buscarPersona('implementador', ${idxImp})" class="bg-purple-600 hover:bg-purple-700 text-white p-1.5 rounded-lg shadow-sm transition-colors flex-shrink-0" title="Buscar">
                            <i data-lucide="search" class="w-5 h-5"></i>
                        </button>
                    </div>
                    <p id="msg_implementador_${idxImp}" class="text-[10px] text-red-500 mt-1 hidden"></p>
                </div>
                <div><label class="block text-[10px] uppercase font-bold text-purple-600 mb-1">Ap. Paterno</label><input type="text" id="implementador_ap_${idxImp}" name="implementadores[${idxImp}][apellido_paterno]" required class="w-full border-purple-200 rounded-lg p-2 h-9 outline-none focus:border-purple-500 bg-white" value="${defaultApPat}"></div>
                <div><label class="block text-[10px] uppercase font-bold text-purple-600 mb-1">Ap. Materno</label><input type="text" id="implementador_am_${idxImp}" name="implementadores[${idxImp}][apellido_materno]" required class="w-full border-purple-200 rounded-lg p-2 h-9 outline-none focus:border-purple-500 bg-white" value="${defaultApMat}"></div>
                <div><label class="block text-[10px] uppercase font-bold text-purple-600 mb-1">Nombres</label><input type="text" id="implementador_nom_${idxImp}" name="implementadores[${idxImp}][nombres]" required class="w-full border-purple-200 rounded-lg p-2 h-9 outline-none focus:border-purple-500 bg-white" value="${defaultNom}"></div>
                <div class="col-span-2"><label class="block text-[10px] uppercase font-bold text-purple-600 mb-1">Cargo / Equipo</label><input type="text" name="implementadores[${idxImp}][cargo]" class="w-full border-purple-200 rounded-lg p-2 h-9 outline-none focus:border-purple-500 bg-white" placeholder="Ej. Equipo de Implementación MINSA" value="${defaultCargo}"></div>
            </div>
        </div>`;
        document.getElementById('implementadores-container').insertAdjacentHTML('beforeend', tpl);
        idxImp++;
        setTimeout(() => lucide.createIcons(), 10);
    }

    // --- RELLENADO DE DATOS (RENIEC / DATA LOCAL) ---
    async function buscarPersona(tipo, index) {
        const docInput = document.getElementById(`${tipo}_dni_${index}`);
        const doc = docInput.value.trim();
        const loader = document.getElementById(`loading_${tipo}_${index}`);
        const msg = document.getElementById(`msg_${tipo}_${index}`);

        if (doc.length < 5) return;

        loader.classList.remove('hidden');
        msg.classList.add('hidden');

        const baseUrl = `{{ route('usuario.monitoreo.citas.buscar.profesional') }}`;

        try {
            // Paso 1: Búsqueda Local
            const response = await fetch(`${baseUrl}?type=doc&q=${doc}&local_only=1`);
            const data = await response.json();

            if (data.length > 0) {
                rellenarPersona(tipo, index, data[0]);
                msg.textContent = "Persona encontrada.";
                msg.className = `text-[10px] ${tipo == 'participante' ? 'text-emerald-600' : 'text-purple-600'} mt-1`;
                msg.classList.remove('hidden');
            } else {
                // Paso 2: Búsqueda Externa
                if (doc.length === 8) {
                    Swal.fire({
                        html: `
                            <div class="p-4 flex flex-col items-center">
                                <div class="relative w-24 h-24 flex items-center justify-center mb-6">
                                    <div class="absolute inset-0 border-[6px] border-blue-50 rounded-full animate-ping opacity-75"></div>
                                    <div class="absolute inset-3 border-4 border-blue-100 rounded-full animate-pulse"></div>
                                    <div class="h-14 w-14 bg-gradient-to-br from-blue-500 to-blue-700 rounded-full flex items-center justify-center shadow-xl shadow-blue-500/50 z-10 relative">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="text-white animate-bounce"><path d="M2 12h20"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                                    </div>
                                </div>
                                <h3 class="text-2xl font-black text-blue-900 uppercase tracking-tight mb-2">Conectando RENIEC</h3>
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] text-center mb-6 leading-relaxed">
                                    Extrayendo nombres oficiales<br>de la plataforma nacional.
                                </p>
                                <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden flex gap-1">
                                    <div class="bg-blue-300 h-full w-1/3 rounded-full animate-pulse"></div>
                                    <div class="bg-blue-500 h-full w-1/3 rounded-full animate-pulse delay-75"></div>
                                    <div class="bg-blue-700 h-full w-1/3 rounded-full animate-pulse delay-150"></div>
                                </div>
                            </div>
                        `,
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        customClass: { popup: 'rounded-[2rem] border-2 border-blue-50 shadow-2xl p-0 bg-white' }
                    });

                    const responseExt = await fetch(`${baseUrl}?type=doc&q=${doc}`);
                    const dataExt = await responseExt.json();
                    
                    Swal.close();

                    if (dataExt.length > 0 && dataExt[0].exists_external) {
                        rellenarPersona(tipo, index, dataExt[0]);
                        msg.textContent = "Extraído de RENIEC.";
                        msg.className = "text-[10px] text-blue-600 mt-1 font-bold";
                        msg.classList.remove('hidden');
                        
                        const Toast = Swal.mixin({
                            toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, timerProgressBar: true
                        });
                        Toast.fire({ icon: 'info', title: 'Nombres encontrados en RENIEC.' });
                    } else {
                        mostrarMsgNuevoPersona(tipo, index, msg);
                    }
                } else {
                    mostrarMsgNuevoPersona(tipo, index, msg);
                }
            }
        } catch (error) {
            console.error('Error:', error);
            if (Swal.isVisible()) Swal.close();
        } finally {
            loader.classList.add('hidden');
        }
    }

    function mostrarMsgNuevoPersona(tipo, index, msg) {
        msg.textContent = "Persona nueva. Complete los datos.";
        msg.className = "text-[10px] text-blue-600 mt-1 font-bold";
        msg.classList.remove('hidden');
        document.getElementById(`${tipo}_ap_${index}`).focus();
    }

    function rellenarPersona(tipo, index, prof) {
        if(prof.apellido_paterno) document.getElementById(`${tipo}_ap_${index}`).value = prof.apellido_paterno;
        if(prof.apellido_materno) document.getElementById(`${tipo}_am_${index}`).value = prof.apellido_materno;
        if(prof.nombres) document.getElementById(`${tipo}_nom_${index}`).value = prof.nombres;
        
        if (tipo === 'participante') {
            const celularInput = document.querySelector(`input[name="usuarios[${index}][celular]"]`);
            if(celularInput && (prof.celular || prof.telefono)) celularInput.value = prof.celular || prof.telefono;
            
            const correoInput = document.querySelector(`input[name="usuarios[${index}][correo]"]`);
            if(correoInput && (prof.email || prof.correo)) correoInput.value = prof.email || prof.correo;
        }
    }

    // Inicializar siempre con 1 participante e implementador vacíos al cargar
    window.onload = () => { agregarParticipante(); agregarImplementador(); }

    // ====== LÓGICA VISUAL UPSS ======
    let upssIndex = 0;
    
    async function buscarUpsGlobal(input) {
        const val = input.value.toLowerCase();
        const resultsDiv = document.getElementById('upss_global_results');
        if (val.length < 3) {
            resultsDiv.classList.add('hidden');
            return;
        }
        
        try {
            const response = await fetch(`{{ route('usuario.implementacion.ajax.upss') }}?q=${val}`);
            const data = await response.json();
            
            let html = '';
            data.forEach(item => {
                // Generar un código UPSS falso basado en el inicio si no existe para la UI (solo si faltase)
                const codUpss = item.codigo_upss || item.codigo_ups.substring(0,2) + '0000';
                const nombreUpss = item.descripcion_upss || 'UPSS PREDETERMINADA';
                html += `
                <div class="p-3 border-b border-slate-100 hover:bg-blue-50 cursor-pointer transition-colors" 
                     onclick="agregarFilaUpss('${codUpss}', '${nombreUpss}', '${item.codigo_ups}', '${item.descripcion_ups}')">
                    <div class="text-xs font-semibold text-slate-700">${item.codigo_ups} - ${item.descripcion_ups}</div>
                    <div class="text-[10px] text-slate-400 font-bold">${codUpss} - ${nombreUpss}</div>
                </div>`;
            });
            
            if(html) {
                resultsDiv.innerHTML = html;
                resultsDiv.classList.remove('hidden');
            } else {
                resultsDiv.innerHTML = '<div class="p-3 text-xs text-slate-500 text-center">No se encontraron resultados</div>';
                resultsDiv.classList.remove('hidden');
            }
        } catch(error) {
            console.error('Error buscando UPSS', error);
        }
    }

    function agregarFilaUpss(c_upss, n_upss, c_ups, n_ups) {
        const container = document.getElementById('upss-container');
        const div = document.createElement('div');
        div.className = "flex flex-wrap lg:flex-nowrap gap-2 p-3 bg-white border border-slate-200 rounded-lg upss-row relative items-center";
        div.innerHTML = `
            <input type="text" name="upss_regularizar[${upssIndex}][codigo_upss]" value="${c_upss}" class="w-full lg:w-32 border border-slate-300 bg-slate-50 rounded text-xs p-2 outline-none" placeholder="UPSS" readonly>
            <input type="text" name="upss_regularizar[${upssIndex}][nombre_ups]" value="${c_ups} - ${n_ups}" class="flex-1 border border-slate-300 bg-slate-50 rounded text-xs p-2 outline-none" placeholder="UPS Nombre" readonly>
            <input type="hidden" name="upss_regularizar[${upssIndex}][codigo_ups]" value="${c_ups}">
            <button type="button" onclick="this.closest('.upss-row').remove()" class="bg-red-600 text-white px-3 py-2 rounded text-xs font-bold hover:bg-red-700 shadow flex items-center whitespace-nowrap">
                × Eliminar
            </button>
        `;
        container.appendChild(div);
        upssIndex++;
        
        // Limpiar
        document.getElementById('upss-search-input').value = '';
        document.getElementById('upss_global_results').classList.add('hidden');
    }

    function agregarUpssManual() {
        agregarFilaUpss('', '', '', '');
        // Quitar readonly del último agregado manual para que lo llenen
        const container = document.getElementById('upss-container');
        const rows = container.querySelectorAll('.upss-row');
        const lastRow = rows[rows.length - 1];
        lastRow.querySelectorAll('input').forEach(input => input.removeAttribute('readonly'));
    }

</script>
@endpush
