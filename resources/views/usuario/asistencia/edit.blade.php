@extends('layouts.usuario')
@section('title', 'Editar acta')
{{-- 1. ESTILOS: Tailwind y jQuery UI --}}
@push('styles')
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }

        @keyframes fade-in {
            from { opacity:0; transform:translateY(8px); }
            to   { opacity:1; transform:translateY(0); }
        }
        .animate-fade-in { animation: fade-in 0.3s ease forwards; }

        @keyframes slide-down {
            from { opacity:0; transform:translateY(-6px); max-height:0; }
            to   { opacity:1; transform:translateY(0);  max-height:120px; }
        }
        .slide-down { animation: slide-down 0.25s ease forwards; overflow:hidden; }

        /* Card de sección */
        .section-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px -1px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
            overflow: hidden;
        }
        .section-header {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.85rem 1.5rem;
            background: #1e293b;
            color: #f8fafc;
            font-weight: 600;
            font-size: 0.95rem;
            letter-spacing: 0.025em;
        }
        .section-body { padding: 1.5rem; }

        .inp {
            width: 100%;
            border: 1px solid #94a3b8;
            border-radius: 0.375rem;
            padding: 0.5rem 0.75rem;
            font-size: 0.875rem;
            transition: all 0.2s;
            background: #fff;
            color: #1e293b;
            font-weight: 500;
        }
        .inp:focus { outline:none; border-color:#10b981; box-shadow:0 0 0 3px rgba(16,185,129,0.15); }
        .inp:read-only { background:#f1f5f9; color:#64748b; cursor:not-allowed; }
        select.inp { cursor:pointer; appearance:auto; }

        /* Labels */
        .lbl { display:block; font-size:0.75rem; font-weight:700; color:#475569; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:4px; }

        /* Tabla participantes */
        .tbl { width:100%; border-collapse:collapse; font-size:0.85rem; }
        .tbl th { background: #f1f5f9; border-bottom: 1px solid #e2e8f0; color:#475569; padding:0.6rem 0.4rem; font-weight:700; text-align:center; font-size:0.75rem; letter-spacing:0.02em; white-space:nowrap; text-transform: uppercase;}
        .tbl td { border-bottom:1px solid #e2e8f0; padding:0.4rem 0.4rem; vertical-align:middle; }
        .tbl tbody tr:hover td { background:#f8fafc; }
        .tbl input, .tbl select { border:1px solid #cbd5e1; border-radius:0.375rem; padding:0.35rem 0.45rem; width:100%; font-size:0.85rem; background:#fff; transition:all 0.15s; }
        .tbl input:focus, .tbl select:focus { outline:none; border-color:#10b981; box-shadow:0 0 0 2px rgba(16,185,129,0.15); }

        /* Botones acción tabla */
        .btn-lupa { background:#f1f5f9; color:#0f172a; border:1px solid #cbd5e1; border-radius:0.375rem; padding:0.3rem 0.5rem; cursor:pointer; font-size:0.85rem; transition:all 0.15s; }
        .btn-lupa:hover { background:#e2e8f0; color:#0f172a; border-color:#94a3b8; }
        .btn-del { background:#fff1f2; color:#be123c; border:1px solid #fecdd3; border-radius:0.375rem; padding:0.3rem 0.5rem; cursor:pointer; font-size:0.85rem; transition:all 0.15s; }
        .btn-del:hover { background:#fecdd3; color:#9f1239; }

        /* Autocomplete */
        .ui-autocomplete { border-radius:0.375rem; box-shadow:0 4px 6px -1px rgba(0,0,0,0.1); border:1px solid #e2e8f0; z-index:9999 !important; }
        .ui-menu-item-wrapper.ui-state-active { background-color:#1e293b !important; border:none !important; color:white !important; }

        /* Botones principales */
        .btn-add { display:inline-flex; align-items:center; gap:0.4rem; padding:0.45rem 1rem; border-radius:0.375rem; font-size:0.85rem; font-weight:600; cursor:pointer; border:1px solid transparent; transition:all 0.2s; background:#10b981; color:#fff; }
        .btn-add:hover { background:#059669; box-shadow:0 4px 6px -1px rgba(0,0,0,0.1); }

        /* Radio modernos */
        .radio-group { display:flex; flex-wrap:wrap; gap:0.75rem; }
        .radio-opt { display:flex; align-items:center; gap:0.5rem; padding:0.5rem 1.2rem; border-radius:3rem; border:1px solid #cbd5e1; background:#f8fafc; cursor:pointer; font-size:0.875rem; font-weight:600; transition:all 0.2s; user-select:none; color:#475569; }
        .radio-opt:hover { border-color:#94a3b8; background:#f1f5f9; }
        .radio-opt:has(input:checked) { background:#1e293b; color:#fff; border-color:#1e293b; }
        .radio-opt input { accent-color:#fff; width:16px; height:16px; margin:0; }

        @keyframes spin { to { transform: rotate(360deg); } }
    </style>
@endpush

{{-- 2. CONTENIDO: El formulario completo --}}
@section('header-content')
    <div>
        <h2 class="text-2l font-bold text-slate-800 tracking-tight">Editar Acta</h2>
        <div class="flex items-center gap-2 text-xs text-slate-500 mt-1 font-medium">
            <span class="text-emerald-600">Operaciones</span>
            <span>&bull;</span>
            <span>Asistencia Técnica</span>
            <span>&bull;</span>
            <span>ID: {{ $acta->id }}</span>
        </div>
    </div>
@endsection

@section('content')
    <div class="min-h-screen pb-10 pt-4 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Título principal --}}
            <div class="mb-10 text-center">
                <h2 class="text-3xl font-black text-slate-800 tracking-tight">Editar Acta de Asistencia Técnica</h2>
                <p class="text-slate-500 text-sm mt-2 max-w-2xl mx-auto">Complete todos los requerimientos para el registro del acta</p>
            </div>

            <form id="actaForm"
                  action="{{ route('usuario.actas.update', $acta->id) }}"
                  method="POST"
                  id="actaFormEdit"
                  enctype="multipart/form-data">
                @csrf
                @method('PUT')

                {{-- ===========================
                     DATOS GENERALES
                     =========================== --}}
                <div class="section-card">
                    <div class="section-header">&#128197; Datos Generales</div>
                    <div class="section-body">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-4">
                            <div>
                                <label class="lbl">Fecha</label>
                                <input type="date" name="fecha" value="{{ old('fecha', date('Y-m-d')) }}" required class="inp">
                            </div>
                            <div>
                                <label class="lbl">Establecimiento</label>
                                <div class="flex gap-2">
                                    <div class="relative flex-1">
                                        <input type="text" id="establecimiento" name="establecimiento"
                                               placeholder="Código o nombre..." required autocomplete="off"
                                               value="{{ old('establecimiento', $acta->establecimiento->nombre ?? '') }}"
                                               class="inp pr-10">
                                        <div id="establecimiento-spinner" class="hidden absolute right-3 top-1/2 -translate-y-1/2">
                                            <i data-lucide="loader-2" class="w-4 h-4 text-emerald-600 animate-spin"></i>
                                        </div>
                                    </div>
                                    <button type="button" ontouchstart="syncEstablecimiento()" onclick="syncEstablecimiento()" id="btn-sync-renipress" 
                                            class="px-4 py-2 bg-slate-800 text-white rounded-lg hover:bg-slate-900 transition-all flex items-center gap-2 group disabled:opacity-50 disabled:cursor-not-allowed"
                                            title="Sincronizar con Susalud (Renipress)">
                                        <i data-lucide="refresh-cw" class="w-4 h-4 group-hover:rotate-180 transition-transform duration-500"></i>
                                        <span class="hidden sm:inline text-xs font-bold uppercase tracking-wider">Sincronizar</span>
                                    </button>
                                </div>
                                <input type="hidden" id="establecimiento_id" name="establecimiento_id"
                                       value="{{ old('establecimiento_id', $acta->establecimiento_id ?? '') }}">

                                {{-- Campos para data Susalud (JSON) --}}
                                <input type="hidden" name="servicios_renipress" id="servicios_renipress" value="{{ old('servicios_renipress', $acta->servicios_renipress ?? '') }}">
                                <input type="hidden" name="especialidades_renipress" id="especialidades_renipress" value="{{ old('especialidades_renipress', $acta->especialidades_renipress ?? '') }}">
                                <input type="hidden" name="cartera_renipress" id="cartera_renipress" value="{{ old('cartera_renipress', $acta->cartera_renipress ?? '') }}">
                            </div>
                        </div>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                            <div>
                                <label class="lbl">Distrito</label>
                                <input type="text" id="distrito" name="distrito" readonly required
                                       value="{{ old('distrito', $acta->establecimiento->distrito ?? '') }}" class="inp">
                            </div>
                            <div>
                                <label class="lbl">Provincia</label>
                                <input type="text" id="provincia" name="provincia" readonly required
                                       value="{{ old('provincia', $acta->establecimiento->provincia ?? '') }}" class="inp">
                            </div>
                            <div>
                                <label class="lbl">Microred</label>
                                <input type="text" id="microred" name="microred" readonly required
                                       value="{{ old('microred', $acta->establecimiento->microred ?? '') }}" class="inp">
                            </div>
                            <div>
                                <label class="lbl">Red</label>
                                <input type="text" id="red" name="red" readonly required
                                       value="{{ old('red', $acta->establecimiento->red ?? '') }}" class="inp">
                            </div>
                        </div>
                        <div>
                            <label class="lbl">Responsable</label>
                            <input type="text" id="responsable" name="responsable"
                                   placeholder="Nombre del responsable" required
                                   value="{{ old('responsable', $acta->responsable ?? '') }}" class="inp">
                        </div>
                    </div>
                </div>

                {{-- ===========================
                     TEMA / MODALIDAD / IMPLEMENTADOR
                     =========================== --}}
                <div class="section-card">
                    <div class="section-header">&#128203; Tema y Modalidad</div>
                    <div class="section-body">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-4">
                            <div>
                                <label class="lbl">Tema / Motivo</label>
                                @php
                                    $opcionesTema = [
                                        'Reactivación de módulo',
                                        'Cambio de responsable del módulo',
                                        'Ingreso de nuevo personal',
                                        'Actualización de cartera de servicios'
                                    ];
                                    $valTema = old('tema', $acta->tema ?? '');
                                    $valOtro = old('tema_otro', '');
                                    
                                    if($valTema !== '' && !in_array($valTema, $opcionesTema)) {
                                        if ($valTema !== 'Otros') {
                                            $valOtro = $valTema; 
                                            $valTema = 'Otros';
                                        }
                                    }
                                    $esOtro = ($valTema === 'Otros');
                                @endphp
                                <select id="selectTema" name="tema" required class="inp">
                                    <option value="">Seleccione un motivo...</option>
                                    <option value="Reactivación de módulo" {{ $valTema == 'Reactivación de módulo' ? 'selected' : '' }}>Reactivación de módulo</option>
                                    <option value="Cambio de responsable del módulo" {{ $valTema == 'Cambio de responsable del módulo' ? 'selected' : '' }}>Cambio de responsable del módulo</option>
                                    <option value="Ingreso de nuevo personal" {{ $valTema == 'Ingreso de nuevo personal' ? 'selected' : '' }}>Ingreso de nuevo personal</option>
                                    <option value="Actualización de cartera de servicios" {{ $valTema == 'Actualización de cartera de servicios' ? 'selected' : '' }}>Actualización de cartera de servicios</option>
                                    <option value="Otros" {{ $esOtro ? 'selected' : '' }}>Otros</option>
                                </select>
                                <div id="divTemaOtro" class="mt-2 {{ $esOtro ? '' : 'hidden' }}">
                                    <input type="text" name="tema_otro" id="temaOtro"
                                           placeholder="Especifique el motivo..."
                                           value="{{ $valOtro }}"
                                           class="inp slide-down"
                                           {{ $esOtro ? 'required' : '' }}>
                                </div>
                            </div>
                            <div>
                                <label class="lbl">Implementador(a)</label>
                                <select name="implementador" required class="inp">
                                    <option value="">Seleccione un implementador...</option>
                                    @php
                                        $usuariosRegistrados = \App\Models\User::where('status','active')->orderBy('apellido_paterno')->get();
                                    @endphp
                                    @foreach($usuariosRegistrados as $u)
                                        @php
                                            $nombreAMostrar = trim("{$u->apellido_paterno} {$u->apellido_materno} {$u->name}");
                                            $selected = (old('implementador', $acta->implementador ?? '') == $nombreAMostrar);
                                        @endphp
                                        <option value="{{ $nombreAMostrar }}" {{ $selected ? 'selected' : '' }}>{{ $nombreAMostrar }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="lbl">Modalidad de asistencia</label>
                            <div class="radio-group mt-1">
                                <label class="radio-opt">
                                    <input type="radio" name="modalidad" value="Presencial" required {{ old('modalidad', $acta->modalidad ?? '') == 'Presencial' ? 'checked' : '' }}>
                                    &#128205; Presencial
                                </label>
                                <label class="radio-opt">
                                    <input type="radio" name="modalidad" value="Virtual" {{ old('modalidad', $acta->modalidad ?? '') == 'Virtual' ? 'checked' : '' }}>
                                    &#128187; Virtual
                                </label>
                                <label class="radio-opt">
                                    <input type="radio" name="modalidad" value="Telefónica" {{ old('modalidad', $acta->modalidad ?? '') == 'Telefónica' ? 'checked' : '' }}>
                                    &#128222; Telefónica
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ===========================
                     SECCIÓN: PARTICIPANTES
                     =========================== --}}
                <div class="section-card">
                    <div class="section-header">&#128101; Participantes</div>
                    <div class="section-body" style="padding:1rem">
                        <div class="overflow-x-auto">
                            <table class="tbl">
                                <thead>
                                    <tr>
                                        <th style="width:36px">N°</th>
                                        <th style="width:170px">Documento</th>
                                        <th>Apellidos</th>
                                        <th>Nombres</th>
                                        <th style="width:180px">Cargo</th>
                                        <th style="width:200px">Módulo</th>
                                        <th style="width:70px" title="¿Es Implementador?">¿Impl.?</th>
                                        <th style="width:52px">Acc.</th>
                                    </tr>
                                </thead>
                                <tbody id="tabla-participantes">
                                    @php
                                        $participantes = old('participantes', $acta->participantes ?? []);
                                        $modulos = ["Atencion Prenatal","Citas","Consulta Externa: Medicina","Consulta Externa: Nutricion","Consulta Externa: Odontologia","Consulta Externa: Psicologia","Cred","Farmacia","FUA","Gestión Administrativa","Inmunizaciones","Laboratorio","Parto","Planificacion Familiar","Puerperio","Teleatiendo","Triaje","VIH"];
                                        $unidades = ["DIRESA ICA","RED DE SALUD ICA","HOSPITAL SAN JOSE DE CHINCHA","HOSPITAL SAN JUAN DE DIOS PISCO","HOSPITAL DE APOYO PALPA","HOSPITAL DE APOYO NAZCA"];
                                    @endphp
                                    @if(count($participantes) === 0)
                                        <tr>
                                            <td class="text-center font-bold text-indigo-600">1</td>
                                            <td>
                                                <div style="display:flex;gap:3px;align-items:center">
                                                    <input type="text" name="participantes[0][dni]" data-base="dni" placeholder="Documento" class="" style="flex:1;min-width:70px">
                                                    <button type="button" class="btn-lupa buscar-participante" title="Buscar">&#128269;</button>
                                                </div>
                                            </td>
                                            <td><input type="text" name="participantes[0][apellidos]" data-base="apellidos" placeholder="Apellidos" required></td>
                                            <td><input type="text" name="participantes[0][nombres]" data-base="nombres" placeholder="Nombres" required></td>
                                            <td><input type="text" name="participantes[0][cargo]" data-base="cargo" placeholder="Cargo"></td>
                                            <td>
                                                <select name="participantes[0][modulo]" data-base="modulo">
                                                    <option value="">-- No aplica --</option>
                                                    @foreach($modulos as $op) <option value="{{ $op }}">{{ $op }}</option> @endforeach
                                                </select>
                                            </td>
                                            <td class="text-center" style="vertical-align: middle;">
                                                <input type="checkbox" style="width: 18px; height: 18px; cursor: pointer;" name="participantes[0][es_implementador]" value="1" data-base="es_implementador">
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn-del eliminar-fila" title="Eliminar">&#10006;</button>
                                            </td>
                                        </tr>
                                    @else
                                        @foreach($participantes as $i => $p)
                                            <tr>
                                                <td class="text-center font-bold text-indigo-600">{{ $i+1 }}</td>
                                                <td>
                                                    <div style="display:flex;gap:3px;align-items:center">
                                                        <input type="text" name="participantes[{{ $i }}][dni]" data-base="dni" value="{{ $p['dni'] ?? $p->dni ?? '' }}" style="flex:1;min-width:70px">
                                                        <button type="button" class="btn-lupa buscar-participante" title="Buscar">&#128269;</button>
                                                    </div>
                                                </td>
                                                <td><input type="text" name="participantes[{{ $i }}][apellidos]" data-base="apellidos" value="{{ $p['apellidos'] ?? $p->apellidos ?? '' }}" required></td>
                                                <td><input type="text" name="participantes[{{ $i }}][nombres]" data-base="nombres" value="{{ $p['nombres'] ?? $p->nombres ?? '' }}" required></td>
                                                <td><input type="text" name="participantes[{{ $i }}][cargo]" data-base="cargo" value="{{ $p['cargo'] ?? $p->cargo ?? '' }}"></td>
                                                <td>
                                                    <select name="participantes[{{ $i }}][modulo]" data-base="modulo">
                                                        <option value="">-- No aplica --</option>
                                                        @foreach($modulos as $op) <option value="{{ $op }}" {{ ($p['modulo'] ?? $p->modulo ?? '') == $op ? 'selected' : '' }}>{{ $op }}</option> @endforeach
                                                    </select>
                                                </td>
                                                <td class="text-center" style="vertical-align: middle;">
                                                    <input type="checkbox" style="width: 18px; height: 18px; cursor: pointer;" name="participantes[{{ $i }}][es_implementador]" value="1" data-base="es_implementador" {{ (!empty($p['es_implementador']) || !empty($p->es_implementador)) ? 'checked' : '' }}>
                                                </td>
                                                <td class="text-center">
                                                    <button type="button" class="btn-del eliminar-fila" title="Eliminar">&#10006;</button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                        <button type="button" id="agregar-participante" class="btn-add mt-3">
                            &#43; Agregar participante
                        </button>
                    </div>
                </div>

                {{-- Template fila participante --}}
                <template id="fila-participante">
                    <tr>
                        <td class="text-center font-bold text-indigo-600"></td>
                        <td>
                            <div style="display:flex;gap:3px;align-items:center">
                                <input type="text" data-base="dni" placeholder="Doc..." style="flex:1;min-width:70px">
                                <button type="button" class="btn-lupa buscar-participante" title="Buscar">&#128269;</button>
                            </div>
                        </td>
                        <td><input type="text" data-base="apellidos" placeholder="Apellidos" required></td>
                        <td><input type="text" data-base="nombres" placeholder="Nombres" required></td>
                        <td><input type="text" data-base="cargo" placeholder="Cargo"></td>
                        <td>
                            <select data-base="modulo">
                                <option value="">-- No aplica --</option>
                                @foreach($modulos as $op) <option value="{{ $op }}">{{ $op }}</option> @endforeach
                            </select>
                        </td>
                        <td class="text-center" style="vertical-align: middle;">
                            <input type="checkbox" style="width: 18px; height: 18px; cursor: pointer;" value="1" data-base="es_implementador">
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn-del eliminar-fila" title="Eliminar">&#10006;</button>
                        </td>
                    </tr>
                </template>

                <!-- ============================
                     SECCIÓN: ACTIVIDADES
                     ============================ -->
                {{-- ===========================
                     SECCIÓN: ACTIVIDADES
                     =========================== --}}
                <div class="section-card">
                    <div class="section-header">&#9998; Actividades desarrolladas</div>
                    <div class="section-body" style="padding:1rem">
                        <table class="tbl">
                            <thead><tr>
                                <th style="width:36px">N°</th>
                                <th>Descripción</th>
                                <th style="width:52px">Acc.</th>
                            </tr></thead>
                            <tbody id="tabla-actividades">
                                @php $actividades = old('actividades', $acta->actividades ?? []); @endphp
                                @if(count($actividades) === 0)
                                    <tr>
                                        <td class="text-center font-bold text-indigo-600">1</td>
                                        <td><input type="text" name="actividades[0][descripcion]" required placeholder="Describa la actividad..."></td>
                                        <td class="text-center"><button type="button" class="btn-del eliminar-fila-generica">&#10006;</button></td>
                                    </tr>
                                @else
                                    @foreach($actividades as $i => $a)
                                        <tr>
                                            <td class="text-center font-bold text-indigo-600">{{ $i+1 }}</td>
                                            <td><input type="text" name="actividades[{{ $i }}][descripcion]" value="{{ $a['descripcion'] ?? $a->descripcion ?? '' }}" required></td>
                                            <td class="text-center"><button type="button" class="btn-del eliminar-fila-generica">&#10006;</button></td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                        <button type="button" id="agregar-actividad" class="btn-add mt-3">
                            &#43; Actividad
                        </button>
                    </div>
                </div>

                {{-- ===========================
                     SECCIÓN: IMÁGENES (máx. 2)
                     =========================== --}}
                <div class="section-card">
                    <div class="section-header">&#128247; Imágenes (máx. 2)</div>
                    <div class="section-body">
                        <div id="grid-evidencias" class="grid grid-cols-2 gap-4 mb-4">
                            @for($i = 1; $i <= 2; $i++)
                                @php $campoSql = "imagen" . $i; @endphp
                                <div class="slot-foto {{ $acta->$campoSql ? 'occupied' : '' }}" id="slot-{{ $i }}" data-slot="{{ $i }}" data-occupied="{{ $acta->$campoSql ? 'true' : 'false' }}" style="position:relative;border:2px dashed #cbd5e1;border-radius:0.5rem;display:flex;align-items:center;justify-content:center;background:#f8fafc;aspect-ratio:3/2;overflow:hidden">
                                    @if($acta->$campoSql)
                                        <img src="{{ asset('storage/' . $acta->$campoSql) }}" style="width:100%;height:100%;object-fit:cover" class="btn-ver-imagen cursor-zoom-in">
                                        <button type="button" class="btn-eliminar-existente" data-campo="{{ $campoSql }}" data-slot="{{ $i }}" style="position:absolute;top:5px;right:5px;background:#ef4444;color:#fff;border-radius:50%;width:24px;height:24px;font-size:12px;font-weight:bold;cursor:pointer">&#10006;</button>
                                    @else
                                        <div style="text-align:center;color:#94a3b8">
                                            <span style="font-size:0.75rem;font-weight:700;text-transform:uppercase">Espacio {{ $i }}</span>
                                        </div>
                                    @endif
                                </div>
                                @if($acta->$campoSql)
                                <input type="hidden" name="eliminar_imagenes[]" id="input-eliminar-{{ $campoSql }}" value="" disabled>
                                @endif
                            @endfor
                        </div>

                        <div id="drop-area"
                             style="border:2px dashed #cbd5e1;border-radius:0.5rem;padding:2rem;text-align:center;background:#f8fafc;cursor:pointer;transition:all 0.2s">
                            <p style="color:#475569;font-weight:600">&#128247; Haz clic o arrastra nuevas fotos aquí</p>
                            <input type="file" id="imagenes" name="imagenes[]" accept="image/*" multiple class="hidden">
                        </div>
                        <div style="display:flex;justify-content:space-between;align-items:center;margin-top:8px">
                            <p id="file-counter" style="font-size:0.82rem;color:#64748b">0 nuevas seleccionadas</p>
                            <button type="button" id="clear-all" class="hidden" style="font-size:0.78rem;color:#e11d48;font-weight:600">Quitar nuevas</button>
                        </div>
                    </div>
                </div>

                {{-- ===========================
                     SECCIÓN: ACUERDOS
                     =========================== --}}
                <div class="section-card">
                    <div class="section-header">&#129309; Acuerdos y compromisos</div>
                    <div class="section-body" style="padding:1rem">
                        <table class="tbl">
                            <thead><tr>
                                <th style="width:36px">N°</th>
                                <th>Descripción</th>
                                <th style="width:52px">Acc.</th>
                            </tr></thead>
                            <tbody id="tabla-acuerdos">
                                @php $acuerdos = old('acuerdos', $acta->acuerdos ?? []); @endphp
                                @if(count($acuerdos) === 0)
                                    <tr>
                                        <td class="text-center font-bold text-indigo-600">1</td>
                                        <td><input type="text" name="acuerdos[0][descripcion]" required placeholder="Describa el acuerdo..."></td>
                                        <td class="text-center"><button type="button" class="btn-del eliminar-fila-generica">&#10006;</button></td>
                                    </tr>
                                @else
                                    @foreach($acuerdos as $i => $ac)
                                        <tr>
                                            <td class="text-center font-bold text-indigo-600">{{ $i+1 }}</td>
                                            <td><input type="text" name="acuerdos[{{ $i }}][descripcion]" value="{{ $ac['descripcion'] ?? $ac->descripcion ?? '' }}" required></td>
                                            <td class="text-center"><button type="button" class="btn-del eliminar-fila-generica">&#10006;</button></td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                        <button type="button" id="agregar-acuerdo" class="btn-add mt-3">
                            &#43; Acuerdo
                        </button>
                    </div>
                </div>

                {{-- ===========================
                     SECCIÓN: OBSERVACIONES
                     =========================== --}}
                <div class="section-card">
                    <div class="section-header">&#128203; Observaciones</div>
                    <div class="section-body" style="padding:1rem">
                        <table class="tbl">
                            <thead><tr>
                                <th style="width:36px">N°</th>
                                <th>Descripción</th>
                                <th style="width:52px">Acc.</th>
                            </tr></thead>
                            <tbody id="tabla-observaciones">
                                @php $observaciones = old('observaciones', $acta->observaciones ?? []); @endphp
                                @if(count($observaciones) === 0)
                                    <tr>
                                        <td class="text-center font-bold text-indigo-600">1</td>
                                        <td><input type="text" name="observaciones[0][descripcion]" required placeholder="Escriba la observación..."></td>
                                        <td class="text-center"><button type="button" class="btn-del eliminar-fila-generica">&#10006;</button></td>
                                    </tr>
                                @else
                                    @foreach($observaciones as $i => $o)
                                        <tr>
                                            <td class="text-center font-bold text-indigo-600">{{ $i+1 }}</td>
                                            <td><input type="text" name="observaciones[{{ $i }}][descripcion]" value="{{ $o['descripcion'] ?? $o->descripcion ?? '' }}" required></td>
                                            <td class="text-center"><button type="button" class="btn-del eliminar-fila-generica">&#10006;</button></td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                        <button type="button" id="agregar-observacion" class="btn-add mt-3">
                            &#43; Observación
                        </button>
                    </div>
                </div>

                {{-- === GESTIÓN DE FIRMAS DIGITALES === --}}
                <div class="section-card">
                    <div class="section-header">
                        <div class="flex items-center justify-between w-full">
                            <div class="flex items-center gap-2">
                                &#9997; Gestión de Firmas Digitales
                            </div>
                            <div class="flex gap-2">
                                <button type="button" onclick="detectarFirmasDigitales()" class="bg-indigo-500 hover:bg-indigo-600 text-white text-[10px] font-bold px-3 py-1.5 rounded-lg transition-all flex items-center gap-1.5">
                                    &#128269; Detectar en Banco
                                </button>
                                <a href="{{ route('usuario.actas.pdf', ['id' => $acta->id, 'digital' => 1]) }}" target="_blank" id="btn-generar-digital" class="hidden bg-emerald-600 hover:bg-emerald-700 text-white text-[10px] font-bold px-3 py-1.5 rounded-lg transition-all flex items-center gap-1.5">
                                    &#128196; Ver PDF con Firmas
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="section-body">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Participantes --}}
                            <div class="space-y-2">
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Estado: Participantes</p>
                                <div id="status-firmas-participantes" class="space-y-2">
                                    @php
                                        // Obtener implementador para filtrar luego
                                        $implNombre = $acta->implementador;
                                    @endphp
                                    @foreach($acta->participantes as $p)
                                        <div class="flex items-center justify-between p-2.5 bg-slate-50 rounded-xl border border-slate-100 dni-status-row" data-dni="{{ $p->dni }}">
                                            <div class="flex items-center gap-2.5">
                                                <div class="w-7 h-7 rounded-full bg-white flex items-center justify-center border border-slate-100 shadow-sm">
                                                    <i class="text-slate-400 text-[10px] font-bold">👤</i>
                                                </div>
                                                <div>
                                                    <p class="text-[11px] font-bold text-slate-700">{{ $p->apellidos }} {{ $p->nombres }}</p>
                                                    <p class="text-[9px] font-mono text-slate-400">{{ $p->dni }}</p>
                                                </div>
                                            </div>
                                            <div class="status-indicator">
                                                <div class="w-3 h-3 bg-slate-200 rounded-full"></div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Implementador --}}
                            <div class="space-y-2">
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Estado: Implementador General</p>
                                <div id="status-firmas-implementador" class="space-y-2">
                                    @php
                                        // Buscar DNI del implementador en mon_profesionales si es posible
                                        $profImpDet = \App\Models\Profesional::where('apellido_paterno', 'LIKE', '%' . explode(' ', $acta->implementador)[0] . '%')
                                            ->where('nombres', 'LIKE', '%' . (explode(', ', $acta->implementador)[1] ?? '') . '%')
                                            ->first();
                                    @endphp
                                    @if($profImpDet)
                                        <div class="flex items-center justify-between p-2.5 bg-indigo-50/50 rounded-xl border border-indigo-100/50 dni-status-row" data-dni="{{ $profImpDet->doc }}">
                                            <div class="flex items-center gap-2.5">
                                                <div class="w-7 h-7 rounded-full bg-white flex items-center justify-center border border-indigo-100 shadow-sm">
                                                    <i class="text-indigo-400 text-[10px] font-bold">🛡️</i>
                                                </div>
                                                <div>
                                                    <p class="text-[11px] font-bold text-indigo-700">{{ $acta->implementador }}</p>
                                                    <p class="text-[9px] font-mono text-slate-400">{{ $profImpDet->doc }}</p>
                                                </div>
                                            </div>
                                            <div class="status-indicator">
                                                <div class="w-3 h-3 bg-slate-200 rounded-full"></div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="p-4 bg-amber-50 rounded-xl border border-amber-100 text-[10px] text-amber-700">
                                            ⚠️ No se encontró al implementador en el maestro de profesionales para validar su firma por DNI.
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 p-3 bg-indigo-50 rounded-lg border border-indigo-100">
                            <p class="text-[11px] text-indigo-700 leading-tight">
                                <b>Nota:</b> El sistema busca automáticamente las firmas en el <a href="{{ route('admin.firmas.index') }}" target="_blank" class="font-bold underline">Banco de Firmas</a> usando el DNI. Si falta alguna, el PDF se generará solo con las firmas detectadas.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Botón Guardar --}}
                <div class="text-center mt-6 pb-4">
                    <button type="button" id="btnActualizar" class="px-8 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-lg shadow-md text-lg transition-colors">
                        &#128190; Actualizar Acta
                    </button>
                </div>

            </form>
        </div>
    </div>

    {{-- Modales fuera del form --}}
    <div id="image-modal" class="fixed inset-0 bg-black bg-opacity-70 hidden items-center justify-center z-50">
        <span id="close-modal" class="absolute top-4 right-6 text-white text-3xl cursor-pointer font-bold">&times;</span>
        <img id="modal-img" class="max-w-[90%] max-h-[90%] rounded-lg shadow-lg" src="">
    </div>
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
                        <td class="text-center font-bold text-indigo-600"></td>
                        <td><input type="text" name="" required style="width:100%;border:1px solid #c7d2fe;border-radius:0.45rem;padding:0.3rem 0.4rem"></td>
                        <td class="text-center"><button type="button" class="btn-del eliminar-fila-generica">&#10006;</button></td>
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

    <!-- Script de Imágenes (Edición) -->
    <style>
        .slot-foto.marked-delete { filter: grayscale(1); opacity: 0.25; pointer-events: none; transform: scale(0.96); transition: all 0.3s }
        @keyframes fade-in { from {opacity:0} to {opacity:1} }
    </style>
    <script>
    document.addEventListener("DOMContentLoaded", () => {
        const input = document.getElementById("imagenes");
        const dropArea = document.getElementById("drop-area");
        const counter = document.getElementById("file-counter");
        const clearBtn = document.getElementById("clear-all");
        
        let bufferNuevasFotos = [];
        const maxFiles = 2; // Fixed to 2 for edit

        if(dropArea) {
            dropArea.addEventListener("click", () => input.click());
            ["dragenter","dragover"].forEach(e => dropArea.addEventListener(e, ev => { ev.preventDefault(); dropArea.style.background = "#e2e8f0"; }));
            ["dragleave","drop"].forEach(e => dropArea.addEventListener(e, ev => { ev.preventDefault(); dropArea.style.background = "#f8fafc"; }));
            dropArea.addEventListener("drop", e => handleFiles(e.dataTransfer.files));
            input.addEventListener("change", e => handleFiles(e.target.files));
        }

        function handleFiles(files) {
            const countOcupadas = $('.slot-foto[data-occupied="true"]').length;
            const disponibles = maxFiles - countOcupadas;
            const toastContainer = document.getElementById("toast-container");
            
            function showToast(msg) {
                const toast = document.createElement("div");
                toast.textContent = msg;
                toast.className = "bg-red-600 text-white px-4 py-2 rounded shadow-md animate-fade-in";
                toastContainer.appendChild(toast);
                setTimeout(() => toast.remove(), 3000);
            }

            if (disponibles <= 0) {
                showToast(`⚠️ Solo puedes subir un máximo de ${maxFiles} imágenes.`);
                input.value = "";
                return;
            }
            if (files.length > disponibles) {
                showToast(`⚠️ Solo puedes subir un máximo de ${maxFiles} imágenes.`);
                files = Array.from(files).slice(0, disponibles);
            }

            for (const file of files) {
                if (!file.type.startsWith("image/")) continue;
                
                // Find next empty slot
                const nextSlot = $('.slot-foto[data-occupied="false"]').first();
                if(nextSlot.length > 0) {
                    // Marcamos de INMEDIATO de forma síncrona para que la siguiente iteración no pise este mismo slot
                    nextSlot.attr('data-occupied', 'true').css('background', '#fff');
                    const sId = nextSlot.data('slot');
                    bufferNuevasFotos.push({ id: sId, file: file });
                    
                    const reader = new FileReader();
                    reader.onload = (fileEv) => {
                        nextSlot.html(`
                            <img src="${fileEv.target.result}" style="width:100%;height:100%;object-fit:cover" class="cursor-zoom-in">
                            <button type="button" class="btn-quitar-nueva" data-slot="${sId}" style="position:absolute;top:5px;right:5px;background:#f97316;color:#fff;border-radius:50%;width:24px;height:24px;font-size:12px;font-weight:bold;cursor:pointer">&#10006;</button>
                        `);
                    };
                    reader.readAsDataURL(file);
                }
            }
            actualizarUIFotos();
            input.value = "";
        }

        $(document).on('click', '.btn-quitar-nueva', function(e) {
            e.stopPropagation();
            const idS = $(this).data('slot');
            bufferNuevasFotos = bufferNuevasFotos.filter(i => i.id !== idS);
            $(`#slot-${idS}`).attr('data-occupied', 'false').html(
                `<div style="text-align:center;color:#94a3b8"><span style="font-size:0.75rem;font-weight:700;text-transform:uppercase">Espacio ${idS}</span></div>`
            );
            actualizarUIFotos();
        });

        if(clearBtn) {
            clearBtn.addEventListener("click", () => {
                bufferNuevasFotos.forEach(item => {
                    $(`#slot-${item.id}`).attr('data-occupied', 'false').html(
                        `<div style="text-align:center;color:#94a3b8"><span style="font-size:0.75rem;font-weight:700;text-transform:uppercase">Espacio ${item.id}</span></div>`
                    );
                });
                bufferNuevasFotos = [];
                input.value = "";
                actualizarUIFotos();
            });
        }

        function actualizarUIFotos() {
            if(counter) counter.textContent = `${bufferNuevasFotos.length} nuevas seleccionadas`;
            if(clearBtn) clearBtn.classList.toggle("hidden", bufferNuevasFotos.length === 0);
        }

        // --- SISTEMA DE BORRADO DE EXISTENTES ---
        $('.btn-eliminar-existente').click(function(e) {
            e.stopPropagation();
            const btn = $(this); const campo = btn.data('campo'); const slot = btn.data('slot');
            
            // Borrado directo sin Swal.fire de confirmación
            const slotEl = $(`#slot-${slot}`);
            slotEl.removeClass('marked-delete').attr('data-occupied', 'false');
            slotEl.html(`<div style="text-align:center;color:#94a3b8"><span style="font-size:0.75rem;font-weight:700;text-transform:uppercase">Espacio ${slot}</span></div>`);
            $(`#input-eliminar-${campo}`).val(campo).prop('disabled', false);
            btn.hide();
        });

        // --- ENVIO FORM ACTUALIZADO ---
        $('#btnActualizar').click(function(e) {
            e.preventDefault();
            Swal.fire({
                title: "¿Actualizar Acta?",
                text: "Se guardarán los cambios y los nuevos participantes serán registrados.",
                icon: "question",
                showCancelButton: true,
                confirmButtonColor: "#10b981",
                cancelButtonText: "Cancelar",
                confirmButtonText: "Sí, Actualizar"
            }).then((result) => {
                if (result.isConfirmed) {
                    const dt = new DataTransfer();
                    bufferNuevasFotos.forEach(item => dt.items.add(item.file));
                    input.files = dt.files;
                    document.getElementById('actaFormEdit').submit();
                }
            });
        });
        
        // Disable original btnGuardar if it exists to prevent conflict
        $('#actaForm').attr('id', 'actaFormEdit');
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
                    <td class="text-center font-bold text-indigo-600">${index + 1}</td>
                    <td><input type="text" name="acuerdos[${index}][descripcion]" required style="width:100%;border:1px solid #c7d2fe;border-radius:0.45rem;padding:0.3rem 0.4rem"></td>
                    <td class="text-center"><button type="button" class="btn-del eliminar-fila-generica">&#10006;</button></td>
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
                    <td class="text-center font-bold text-indigo-600">${index + 1}</td>
                    <td><input type="text" name="observaciones[${index}][descripcion]" required style="width:100%;border:1px solid #c7d2fe;border-radius:0.45rem;padding:0.3rem 0.4rem"></td>
                    <td class="text-center"><button type="button" class="btn-del eliminar-fila-generica">&#10006;</button></td>
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

        // ---------- TEMA / OTRO ----------
        const selectTema = document.getElementById('selectTema');
        const divTemaOtro = document.getElementById('divTemaOtro');
        if (selectTema && divTemaOtro) {
            selectTema.addEventListener('change', function() {
                if (this.value === 'Otros') {
                    divTemaOtro.classList.remove('hidden');
                    document.getElementById('temaOtro').focus();
                } else {
                    divTemaOtro.classList.add('hidden');
                    document.getElementById('temaOtro').value = '';
                }
            });
        }

        // ---------- BUSCAR PARTICIPANTE (botón lupa) ----------
        $(document).on('click', '.buscar-participante', function() {
            const fila = $(this).closest('tr');
            const dniInput = fila.find('input[data-base="dni"]');
            const doc = dniInput.val().trim();

            if (doc.length < 6) {
                Swal.fire({ icon:'warning', title:'Documento muy corto', text:'Ingrese al menos 6 caracteres.', confirmButtonColor:'#4f46e5' });
                return;
            }

            // Mostrar loader
            Swal.fire({
                html: `<div style="display:flex;align-items:center;gap:16px;padding:8px">
                           <div style="width:36px;height:36px;border:4px solid #4f46e5;border-top-color:transparent;border-radius:50%;animation:spin 0.7s linear infinite"></div>
                           <div><b style="color:#1e1b4b">Buscando en base local...</b></div>
                       </div>`,
                showConfirmButton: false, allowOutsideClick: false,
                customClass: { popup: 'rounded-2xl' }
            });

            // 1ro: buscar local en mon_profesionales (sin filtro tipo_doc)
            fetch(`/usuario/monitoreo/profesional/buscar/${encodeURIComponent(doc)}?local_only=1`)
                .then(r => r.json())
                .then(data => {
                    if (data.exists) {
                        Swal.close();
                        fila.find('input[data-base="apellidos"]').val(
                            ((data.apellido_paterno || '') + ' ' + (data.apellido_materno || '')).trim()
                        );
                        fila.find('input[data-base="nombres"]').val(data.nombres || '');
                        fila.find('input[data-base="cargo"]').val(data.cargo || '');
                        Swal.mixin({ toast:true, position:'top-end', showConfirmButton:false, timer:2500 })
                            .fire({ icon:'success', title:'Datos cargados correctamente' });
                    } else {
                        // 2do: intentar RENIEC solo si son exactamente 8 dígitos
                        if (doc.length === 8 && /^\d{8}$/.test(doc)) {
                            Swal.update({
                                html: `<div style="display:flex;align-items:center;gap:16px;padding:8px">
                                           <div style="width:36px;height:36px;border:4px solid #6366f1;border-top-color:transparent;border-radius:50%;animation:spin 0.7s linear infinite"></div>
                                           <div><b style="color:#1e1b4b">Consultando RENIEC...</b></div>
                                       </div>`
                            });
                            fetch(`/usuario/monitoreo/profesional/buscar/${encodeURIComponent(doc)}`)
                                .then(r => r.json())
                                .then(ext => {
                                    Swal.close();
                                    if (ext.quota_exceeded) {
                                        Swal.fire({ icon:'warning', title:'Límite RENIEC excedido', text:'Ingrese los datos manualmente.', confirmButtonColor:'#4f46e5' });
                                        return;
                                    }
                                    if (ext.exists_external) {
                                        fila.find('input[data-base="apellidos"]').val(
                                            ((ext.apellido_paterno || '') + ' ' + (ext.apellido_materno || '')).trim()
                                        );
                                        fila.find('input[data-base="nombres"]').val(ext.nombres || '');
                                        Swal.mixin({ toast:true, position:'top-end', showConfirmButton:false, timer:3000 })
                                            .fire({ icon:'info', title:'Encontrado en RENIEC. Complete el cargo.' });
                                    } else {
                                        Swal.fire({ icon:'info', title:'No encontrado', text:'No figura en RENIEC. Ingrese datos manualmente.', confirmButtonColor:'#4f46e5' });
                                    }
                                })
                                .catch(() => Swal.fire({ icon:'error', title:'Error de conexión con RENIEC' }));
                        } else {
                            Swal.close();
                            Swal.fire({ icon:'info', title:'No encontrado', text:'No figura en el maestro. Ingrese los datos manualmente.', confirmButtonColor:'#4f46e5' });
                        }
                    }
                })
                .catch(() => { Swal.close(); Swal.fire({ icon:'error', title:'Error de conexión' }); });
        });

        // --- GESTIÓN DE FIRMAS DIGITALES ---
        window.detectarFirmasDigitales = async function() {
            const rows = document.querySelectorAll('.dni-status-row');
            const btnGenerar = document.getElementById('btn-generar-digital');
            let totalEncontradas = 0;

            for (const row of rows) {
                const dni = row.getAttribute('data-dni');
                const indicator = row.querySelector('.status-indicator');
                indicator.innerHTML = '<div style="width:12px;height:12px;border:2px solid #6366f1;border-top-color:transparent;border-radius:50%;animation:spin 0.7s linear infinite"></div>';
                
                try {
                    const response = await fetch(`/admin/banco-firmas/search-ajax?term=${dni}`);
                    const results = await response.json();
                    const match = results.find(r => r.text.includes(dni));
                    
                    if (match && match.has_firma) {
                        indicator.innerHTML = '<span style="color:#10b981;font-size:12px;font-weight:bold" title="Firma detectada">✓</span>';
                        totalEncontradas++;
                    } else {
                        indicator.innerHTML = '<span style="color:#ef4444;font-size:12px;font-weight:bold" title="Sin firma">✗</span>';
                    }
                } catch (error) {
                    indicator.innerHTML = '<div class="w-3 h-3 bg-slate-200 rounded-full"></div>';
                }
            }

            if (totalEncontradas > 0) {
                btnGenerar.classList.remove('hidden');
                btnGenerar.classList.add('animate-fade-in');
                
                if (totalEncontradas === rows.length) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Firmas Detectadas!',
                        text: 'Se han detectado las firmas de todos los participantes.',
                        timer: 2000,
                        showConfirmButton: false,
                        customClass: { popup: 'rounded-2xl' }
                    });
                } else {
                    Swal.fire({
                        icon: 'info',
                        title: 'Firmas Parciales',
                        text: `Se detectaron ${totalEncontradas} de ${rows.length} firmas.`,
                        confirmButtonColor: '#4f46e5',
                        customClass: { popup: 'rounded-2xl' }
                    });
                }
            } else {
                Swal.fire({
                    icon: 'warning',
                    title: 'Sin Firmas',
                    text: 'No se encontraron firmas asociadas a los DNIs en el banco.',
                    confirmButtonColor: '#4f46e5',
                    customClass: { popup: 'rounded-2xl' }
                });
            }
        };

        // Auto-detectar al cargar
        setTimeout(detectarFirmasDigitales, 1500);
        });
    </script>

    <!-- Script Renipress Sync -->
    <script>
        async function syncEstablecimiento() {
            const idEst = document.getElementById('establecimiento_id').value;
            const btn = document.getElementById('btn-sync-renipress');
            const spinner = document.getElementById('establecimiento-spinner');
            const baseUrl = "{{ url('/') }}";

            if (!idEst) {
                Swal.fire({ icon: 'warning', title: 'Atención', text: 'Primero debe seleccionar un establecimiento de la lista.' });
                return;
            }

            try {
                btn.disabled = true;
                spinner.classList.remove('hidden');

                const texto = document.getElementById('establecimiento').value;
                const match = texto.match(/\b\d{8}\b/);
                const codigo = match ? match[0] : null;

                if (!codigo) {
                    throw new Error('No se pudo determinar el código Renipress del establecimiento seleccionado.');
                }

                const response = await fetch(`${baseUrl}/usuario/listado-actas/sync-renipress`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ codigo: codigo })
                });

                const data = await response.json();

                if (data && !data.error) {
                    document.getElementById('servicios_renipress').value = JSON.stringify(data.servicios || []);
                    document.getElementById('especialidades_renipress').value = JSON.stringify(data.especialidades || []);
                    document.getElementById('cartera_renipress').value = JSON.stringify(data.cartera || []);

                    Swal.fire({
                        icon: 'success',
                        title: 'Sincronización Exitosa',
                        text: `Se han obtenido ${data.servicios?.length || 0} servicios de Susalud.`,
                        timer: 2500,
                        showConfirmButton: false
                    });
                } else {
                    throw new Error(data.error || 'Error desconocido al sincronizar.');
                }
            } catch (error) {
                console.error(error);
                Swal.fire({ icon: 'error', title: 'Error de Sincronización', text: error.message });
            } finally {
                btn.disabled = false;
                spinner.classList.add('hidden');
            }
        }
    </script>
@endpush
@endsection