@extends('layouts.usuario')

@section('title', 'Editar Acta ' . $moduloConfig['nombre'])

@section('header-content')
    <div>
        <h1 class="text-xl font-bold text-slate-800 tracking-tight">📝 Editar Acta: {{ $moduloConfig['nombre'] }}</h1>
        <div class="flex items-center gap-2 text-xs text-slate-500 mt-0.5">
            <a href="{{ route('usuario.implementacion.index') }}" class="hover:text-blue-600">Actas</a>
            <span class="text-slate-300">•</span>
            <span>Editar #{{ $acta->id }}</span>
        </div>
    </div>
@endsection

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 mb-6">
        <form action="{{ route('usuario.implementacion.update', ['modulo' => $moduloKey, 'id' => $acta->id]) }}" method="POST" enctype="multipart/form-data" class="p-6 md:p-8 space-y-8">
            @csrf
            @method('PUT')
            
            {{-- Sección: Datos Principales --}}
            <div>
                <div class="flex items-center justify-between mb-4 border-b border-slate-100 pb-2 flex-wrap gap-2">
                    <div class="flex items-center gap-2">
                        <i data-lucide="book-open" class="w-4 h-4 text-blue-600"></i>
                        <h3 class="font-bold text-slate-800 text-sm uppercase tracking-wide">Módulo a Implementar</h3>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Módulo</label>
                        <input type="text" value="{{ $moduloConfig['nombre'] }}" readonly class="w-full bg-slate-100 border border-slate-200 rounded-xl text-sm p-2.5 outline-none font-bold text-slate-500 cursor-not-allowed">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Fecha de Implementación</label>
                        <input type="date" name="fecha" required value="{{ date('Y-m-d', strtotime($acta->fecha)) }}" class="w-full border border-slate-200 rounded-xl text-sm p-2.5 outline-none focus:border-blue-500">
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
                        @if($acta->archivo_pdf)
                        <div class="mt-2 text-[11px] font-medium text-slate-500 flex items-center gap-2 bg-emerald-50 w-max px-3 py-1.5 rounded-lg border border-emerald-100">
                            <i data-lucide="check-circle" class="w-4 h-4 text-emerald-500"></i>
                            Acta PDF actual:
                            <a href="{{ Storage::url($acta->archivo_pdf) }}" target="_blank" class="text-blue-600 font-bold hover:underline flex items-center gap-1">Ver Archivo <i data-lucide="external-link" class="w-3 h-3"></i></a>
                            <span class="text-slate-400 mx-2">|</span>
                            Si subes uno nuevo, reemplazará al actual.
                        </div>
                        @else
                        <p class="text-[10px] text-slate-400 mt-1"><i data-lucide="info" class="w-3 h-3 inline"></i> Opcional. Sube el documento escaneado con las firmas correspondientes.</p>
                        @endif
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
                        <input type="text" id="busqueda_establecimiento" placeholder="Opcional: Volver a buscar establecimiento para actualizar campos..." autocomplete="off"
                            class="w-full pl-9 pr-4 py-2.5 border border-slate-200 rounded-xl text-sm outline-none focus:border-blue-500 bg-blue-50/30">
                    </div>
                    <ul id="sugerencias_establecimiento" class="absolute z-50 bg-white border border-slate-200 w-full rounded-xl shadow-xl mt-1 hidden max-h-60 overflow-y-auto divide-y divide-slate-50"></ul>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="col-span-1 md:col-span-2">
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Nombre Establecimiento</label>
                        <input type="text" id="nombre_establecimiento" name="nombre_establecimiento" value="{{ $acta->nombre_establecimiento }}" readonly required class="w-full bg-slate-50 border border-slate-200 rounded-lg text-sm p-2 outline-none text-slate-500 font-medium">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Código RENIPRESS</label>
                        <input type="text" id="codigo_establecimiento" name="codigo_establecimiento" value="{{ $acta->codigo_establecimiento }}" readonly required class="w-full bg-slate-50 border border-slate-200 rounded-lg text-sm p-2 outline-none text-slate-500 font-mono">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Provincia</label>
                        <input type="text" id="provincia" name="provincia" value="{{ $acta->provincia }}" readonly class="w-full bg-slate-50 border border-slate-200 rounded-lg text-sm p-2 outline-none text-slate-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Distrito</label>
                        <input type="text" id="distrito" name="distrito" value="{{ $acta->distrito }}" readonly class="w-full bg-slate-50 border border-slate-200 rounded-lg text-sm p-2 outline-none text-slate-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Categoría</label>
                        <input type="text" id="categoria" name="categoria" value="{{ $acta->categoria }}" readonly class="w-full bg-slate-50 border border-slate-200 rounded-lg text-sm p-2 outline-none text-slate-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Red</label>
                        <input type="text" id="red" name="red" value="{{ $acta->red }}" readonly class="w-full bg-slate-50 border border-slate-200 rounded-lg text-sm p-2 outline-none text-slate-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Microred</label>
                        <input type="text" id="microred" name="microred" value="{{ $acta->microred }}" readonly class="w-full bg-slate-50 border border-slate-200 rounded-lg text-sm p-2 outline-none text-slate-500">
                    </div>
                    <div class="md:col-span-3">
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Responsable del Establecimiento</label>
                        <input type="text" id="responsable" name="responsable" value="{{ $acta->responsable }}" required placeholder="Nombre del médico jefe o responsable" class="w-full border border-slate-200 rounded-lg text-sm p-2 outline-none focus:border-blue-500">
                    </div>
                </div>
            </div>

            {{-- Modalidad --}}
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Modalidad de Implementación</label>
                <select name="modalidad" required class="w-full md:w-1/2 border border-slate-200 rounded-xl text-sm p-2.5 outline-none focus:border-blue-500 bg-white">
                    <option value="">Seleccione Modalidad</option>
                    <option value="POR HORARIO" {{ $acta->modalidad == 'POR HORARIO' ? 'selected' : '' }}>Por Horario</option>
                    <option value="POR SELECCION" {{ $acta->modalidad == 'POR SELECCION' ? 'selected' : '' }}>Por Selección (Exclusividad)</option>
                </select>
            </div>

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
                <div id="usuarios-container" class="space-y-3">
                    @forelse($acta->usuarios as $index => $usu)
                    <div class="participante-row bg-slate-50/50 border border-slate-200 rounded-xl p-4 relative pr-10">
                        <button type="button" onclick="this.closest('.participante-row').remove()" class="absolute top-4 right-4 text-slate-300 hover:text-red-500 transition-colors" title="Quitar">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg>
                        </button>
                        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 text-sm">
                            <div><label class="block text-[10px] uppercase font-bold text-slate-500 mb-1">DNI</label><input type="text" name="usuarios[{{$index}}][dni]" value="{{$usu->dni}}" required class="w-full border-slate-200 rounded-lg p-2 h-9 outline-none focus:border-emerald-500 bg-white"></div>
                            <div><label class="block text-[10px] uppercase font-bold text-slate-500 mb-1">Ap. Paterno</label><input type="text" name="usuarios[{{$index}}][apellido_paterno]" value="{{$usu->apellido_paterno}}" required class="w-full border-slate-200 rounded-lg p-2 h-9 outline-none focus:border-emerald-500 bg-white"></div>
                            <div><label class="block text-[10px] uppercase font-bold text-slate-500 mb-1">Ap. Materno</label><input type="text" name="usuarios[{{$index}}][apellido_materno]" value="{{$usu->apellido_materno}}" required class="w-full border-slate-200 rounded-lg p-2 h-9 outline-none focus:border-emerald-500 bg-white"></div>
                            <div><label class="block text-[10px] uppercase font-bold text-slate-500 mb-1">Nombres</label><input type="text" name="usuarios[{{$index}}][nombres]" value="{{$usu->nombres}}" required class="w-full border-slate-200 rounded-lg p-2 h-9 outline-none focus:border-emerald-500 bg-white"></div>
                            <div><label class="block text-[10px] uppercase font-bold text-slate-500 mb-1">Celular</label><input type="text" name="usuarios[{{$index}}][celular]" value="{{$usu->celular}}" class="w-full border-slate-200 rounded-lg p-2 h-9 outline-none focus:border-emerald-500 bg-white"></div>
                            <div><label class="block text-[10px] uppercase font-bold text-slate-500 mb-1">Correo</label><input type="email" name="usuarios[{{$index}}][correo]" value="{{$usu->correo}}" class="w-full border-slate-200 rounded-lg p-2 h-9 outline-none focus:border-emerald-500 bg-white"></div>
                            <div class="col-span-2"><label class="block text-[10px] uppercase font-bold text-slate-500 mb-1">Estado Credencial</label>
                                <select name="usuarios[{{$index}}][permisos]" required class="w-full border-slate-200 rounded-lg px-2 h-9 outline-none focus:border-emerald-500 bg-white text-sm">
                                    <option value="">Seleccione...</option>
                                    <option value="C.C. Y D.J." {{ $usu->permisos == 'C.C. Y D.J.' ? 'selected' : '' }}>Entregado (C.C. y D.J.)</option>
                                    <option value="POR REGULARIZAR" {{ $usu->permisos == 'POR REGULARIZAR' ? 'selected' : '' }}>Por regularizar</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    @empty
                    @endforelse
                </div>
            </div>

            {{-- Sección: Implementadores --}}
            <div>
                <div class="flex items-center justify-between mb-3 border-b border-slate-100 pb-2 mt-4">
                    <div class="flex items-center gap-2">
                        <i data-lucide="user-check" class="w-4 h-4 text-purple-600"></i>
                        <h3 class="font-bold text-slate-800 text-sm uppercase tracking-wide">Personal Implementador</h3>
                    </div>
                    <button type="button" id="btn-add-implem" onclick="agregarImplementador()" class="text-xs font-bold text-purple-600 bg-purple-50 hover:bg-purple-100 px-3 py-1.5 rounded-lg transition-colors flex items-center gap-1" style="{{ $acta->implementadores->count() > 0 ? 'display:none;' : '' }}">
                        <i data-lucide="plus" class="w-3 h-3"></i> AGREGAR
                    </button>
                </div>
                <div id="implementadores-container" class="space-y-3">
                    @forelse($acta->implementadores as $index => $imp)
                    <div class="implem-row bg-purple-50/30 border border-purple-100 rounded-xl p-4 relative pr-10">
                        <button type="button" onclick="this.closest('.implem-row').remove(); document.getElementById('btn-add-implem').style.display='flex';" class="absolute top-4 right-4 text-purple-300 hover:text-red-500 transition-colors" title="Quitar">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg>
                        </button>
                        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 text-sm">
                            <div><label class="block text-[10px] uppercase font-bold text-purple-600 mb-1">DNI (Escribir manual)</label><input type="text" name="implementadores[{{$index}}][dni]" value="{{$imp->dni}}" required class="w-full border-purple-200 rounded-lg p-2 h-9 outline-none focus:border-purple-500 bg-white"></div>
                            <div><label class="block text-[10px] uppercase font-bold text-purple-600 mb-1">Ap. Paterno</label><input type="text" name="implementadores[{{$index}}][apellido_paterno]" value="{{$imp->apellido_paterno}}" required class="w-full border-purple-200 rounded-lg p-2 h-9 outline-none focus:border-purple-500 bg-white"></div>
                            <div><label class="block text-[10px] uppercase font-bold text-purple-600 mb-1">Ap. Materno</label><input type="text" name="implementadores[{{$index}}][apellido_materno]" value="{{$imp->apellido_materno}}" required class="w-full border-purple-200 rounded-lg p-2 h-9 outline-none focus:border-purple-500 bg-white"></div>
                            <div><label class="block text-[10px] uppercase font-bold text-purple-600 mb-1">Nombres</label><input type="text" name="implementadores[{{$index}}][nombres]" value="{{$imp->nombres}}" required class="w-full border-purple-200 rounded-lg p-2 h-9 outline-none focus:border-purple-500 bg-white"></div>
                            <div class="col-span-2"><label class="block text-[10px] uppercase font-bold text-purple-600 mb-1">Cargo / Equipo</label><input type="text" name="implementadores[{{$index}}][cargo]" value="{{$imp->cargo}}" class="w-full border-purple-200 rounded-lg p-2 h-9 outline-none focus:border-purple-500 bg-white" placeholder="Ej. Equipo de Implementación MINSA"></div>
                        </div>
                    </div>
                    @empty
                    @endforelse
                </div>
            </div>

            {{-- Observaciones --}}
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Observaciones Finales</label>
                <textarea name="observaciones" rows="3" class="w-full border border-slate-200 rounded-xl text-sm p-3 outline-none focus:border-blue-500" placeholder="Ingrese anotaciones u observaciones sobre la implementación...">{{ $acta->observaciones }}</textarea>
            </div>

            {{-- Footer / Botones --}}
            <div class="flex justify-end gap-3 pt-6 border-t border-slate-100">
                <a href="{{ route('usuario.implementacion.index') }}" class="px-5 py-2.5 rounded-xl font-bold text-sm text-slate-500 bg-slate-100 hover:bg-slate-200 transition-colors">Cancelar</a>
                <button type="submit" class="px-6 py-2.5 rounded-xl font-bold text-sm text-white bg-blue-600 hover:bg-blue-700 shadow-md shadow-blue-500/20 transition-all">Guardar Cambios</button>
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
            fetch(`/implementacion/ajax/establecimiento?q=${encodeURIComponent(val)}`)
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
    let idxUsu = {{ $acta->usuarios->count() > 0 ? $acta->usuarios->keys()->last() + 1 : 0 }};
    function agregarParticipante() {
        const tpl = `
        <div class="participante-row bg-slate-50/50 border border-slate-200 rounded-xl p-4 relative pr-10">
            <button type="button" onclick="this.closest('.participante-row').remove()" class="absolute top-4 right-4 text-slate-300 hover:text-red-500 transition-colors" title="Quitar">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg>
            </button>
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 text-sm">
                <div><label class="block text-[10px] uppercase font-bold text-slate-500 mb-1">DNI</label><input type="text" name="usuarios[${idxUsu}][dni]" required class="w-full border-slate-200 rounded-lg p-2 h-9 outline-none focus:border-emerald-500 bg-white"></div>
                <div><label class="block text-[10px] uppercase font-bold text-slate-500 mb-1">Ap. Paterno</label><input type="text" name="usuarios[${idxUsu}][apellido_paterno]" required class="w-full border-slate-200 rounded-lg p-2 h-9 outline-none focus:border-emerald-500 bg-white"></div>
                <div><label class="block text-[10px] uppercase font-bold text-slate-500 mb-1">Ap. Materno</label><input type="text" name="usuarios[${idxUsu}][apellido_materno]" required class="w-full border-slate-200 rounded-lg p-2 h-9 outline-none focus:border-emerald-500 bg-white"></div>
                <div><label class="block text-[10px] uppercase font-bold text-slate-500 mb-1">Nombres</label><input type="text" name="usuarios[${idxUsu}][nombres]" required class="w-full border-slate-200 rounded-lg p-2 h-9 outline-none focus:border-emerald-500 bg-white"></div>
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
    }

    // --- IMPLEMENTADORES ---
    let idxImp = {{ $acta->implementadores->count() > 0 ? $acta->implementadores->keys()->last() + 1 : 0 }};
    function agregarImplementador() {
        if(document.querySelectorAll('.implem-row').length > 0) return; // Solo 1 implementador
        
        const tpl = `
        <div class="implem-row bg-purple-50/30 border border-purple-100 rounded-xl p-4 relative pr-10">
            <button type="button" onclick="this.closest('.implem-row').remove(); document.getElementById('btn-add-implem').style.display='flex';" class="absolute top-4 right-4 text-purple-300 hover:text-red-500 transition-colors" title="Quitar">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg>
            </button>
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 text-sm">
                <div><label class="block text-[10px] uppercase font-bold text-purple-600 mb-1">DNI (Escribir manual)</label><input type="text" name="implementadores[${idxImp}][dni]" required class="w-full border-purple-200 rounded-lg p-2 h-9 outline-none focus:border-purple-500 bg-white"></div>
                <div><label class="block text-[10px] uppercase font-bold text-purple-600 mb-1">Ap. Paterno</label><input type="text" name="implementadores[${idxImp}][apellido_paterno]" required class="w-full border-purple-200 rounded-lg p-2 h-9 outline-none focus:border-purple-500 bg-white"></div>
                <div><label class="block text-[10px] uppercase font-bold text-purple-600 mb-1">Ap. Materno</label><input type="text" name="implementadores[${idxImp}][apellido_materno]" required class="w-full border-purple-200 rounded-lg p-2 h-9 outline-none focus:border-purple-500 bg-white"></div>
                <div><label class="block text-[10px] uppercase font-bold text-purple-600 mb-1">Nombres</label><input type="text" name="implementadores[${idxImp}][nombres]" required class="w-full border-purple-200 rounded-lg p-2 h-9 outline-none focus:border-purple-500 bg-white"></div>
                <div class="col-span-2"><label class="block text-[10px] uppercase font-bold text-purple-600 mb-1">Cargo / Equipo</label><input type="text" name="implementadores[${idxImp}][cargo]" class="w-full border-purple-200 rounded-lg p-2 h-9 outline-none focus:border-purple-500 bg-white" placeholder="Ej. Equipo de Implementación MINSA"></div>
            </div>
        </div>`;
        document.getElementById('implementadores-container').insertAdjacentHTML('beforeend', tpl);
        document.getElementById('btn-add-implem').style.display = 'none';
        idxImp++;
    }

</script>
@endpush
