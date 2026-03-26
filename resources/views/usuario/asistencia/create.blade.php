@extends('layouts.usuario')
@section('title', 'Crear acta')
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
        <h2 class="text-2l font-bold text-slate-800 tracking-tight">Crear Acta</h2>
        <div class="flex items-center gap-2 text-xs text-slate-500 mt-1 font-medium">
            <span class="text-emerald-600">Operaciones</span>
            <span>&bull;</span>
            <span>Asistencia Técnica</span>
        </div>
    </div>
@endsection

@section('content')
    <div class="min-h-screen pb-10 pt-4 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Título principal --}}
            <div class="mb-10 text-center">
                <h2 class="text-3xl font-black text-slate-800 tracking-tight">Acta de Asistencia Técnica</h2>
                <p class="text-slate-500 text-sm mt-2 max-w-2xl mx-auto">Complete todos los requerimientos para el registro del acta</p>
            </div>

            <form id="actaForm"
                  action="{{ route('usuario.actas.store') }}"
                  method="POST"
                  enctype="multipart/form-data">
                @csrf

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
                                <input type="text" id="establecimiento" name="establecimiento"
                                       placeholder="Código o nombre..." required autocomplete="off"
                                       value="{{ old('establecimiento', $acta->establecimiento->nombre ?? '') }}"
                                       class="inp">
                                <input type="hidden" id="establecimiento_id" name="establecimiento_id"
                                       value="{{ old('establecimiento_id', $acta->establecimiento_id ?? '') }}">
                            </div>
                        </div>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                            <div>
                                <label class="lbl">Distrito</label>
                                <input type="text" id="distrito" name="distrito" readonly required
                                       value="{{ old('distrito', $acta->distrito ?? '') }}" class="inp">
                            </div>
                            <div>
                                <label class="lbl">Provincia</label>
                                <input type="text" id="provincia" name="provincia" readonly required
                                       value="{{ old('provincia', $acta->provincia ?? '') }}" class="inp">
                            </div>
                            <div>
                                <label class="lbl">Microred</label>
                                <input type="text" id="microred" name="microred" readonly required
                                       value="{{ old('microred', $acta->microred ?? '') }}" class="inp">
                            </div>
                            <div>
                                <label class="lbl">Red</label>
                                <input type="text" id="red" name="red" readonly required
                                       value="{{ old('red', $acta->red ?? '') }}" class="inp">
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
                                <select id="selectTema" name="tema" required class="inp">
                                    <option value="">Seleccione un motivo...</option>
                                    <option value="Reactivación de módulo" {{ old('tema', $acta->tema ?? '') == 'Reactivación de módulo' ? 'selected' : '' }}>Reactivación de módulo</option>
                                    <option value="Cambio de responsable del módulo" {{ old('tema', $acta->tema ?? '') == 'Cambio de responsable del módulo' ? 'selected' : '' }}>Cambio de responsable del módulo</option>
                                    <option value="Ingreso de nuevo personal" {{ old('tema', $acta->tema ?? '') == 'Ingreso de nuevo personal' ? 'selected' : '' }}>Ingreso de nuevo personal</option>
                                    <option value="Actualización de cartera de servicios" {{ old('tema', $acta->tema ?? '') == 'Actualización de cartera de servicios' ? 'selected' : '' }}>Actualización de cartera de servicios</option>
                                    <option value="Otros" {{ old('tema', $acta->tema ?? '') == 'Otros' ? 'selected' : '' }}>Otros</option>
                                </select>
                                <div id="divTemaOtro" class="mt-2 {{ old('tema', $acta->tema ?? '') == 'Otros' ? '' : 'hidden' }}">
                                    <input type="text" name="tema_otro" id="temaOtro"
                                           placeholder="Especifique el motivo..."
                                           value="{{ old('tema_otro', $acta->tema_otro ?? '') }}"
                                           class="inp slide-down">
                                </div>
                            </div>
                            <div>
                                <label class="lbl">Implementador(a)</label>
                                <select name="implementador" required class="inp">
                                    <option value="">Seleccione un implementador...</option>
                                    @php
                                        $usuariosRegistrados = \App\Models\User::where('status','active')->orderBy('apellido_paterno')->get();
                                        $idUsuarioLogeado = Auth::id();
                                    @endphp
                                    @foreach($usuariosRegistrados as $u)
                                        @php
                                            $nombreAMostrar = trim("{$u->apellido_paterno} {$u->apellido_materno} {$u->name}");
                                            $selected = (old('implementador') == $nombreAMostrar) || (!old('implementador') && $u->id == $idUsuarioLogeado);
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
                                        <th style="width:200px">Cargo</th>
                                        <th style="width:260px">Módulo</th>
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
                        <div id="drop-area"
                             style="border:2px dashed #cbd5e1;border-radius:0.5rem;padding:2rem;text-align:center;background:#f8fafc;cursor:pointer;transition:all 0.2s">
                            <p style="color:#475569;font-weight:600">&#128247; Haz clic o arrastra imágenes aquí</p>
                            <p style="color:#64748b;font-size:0.78rem;margin-top:4px">(máx. 2 imágenes, 2 MB cada una)</p>
                            <input type="file" id="imagenes" name="imagenes[]" accept="image/*" multiple class="hidden">
                        </div>
                        <div style="display:flex;justify-content:space-between;align-items:center;margin-top:8px">
                            <p id="file-counter" style="font-size:0.82rem;color:#64748b">0 / 2 imágenes</p>
                            <button type="button" id="clear-all" class="hidden" style="font-size:0.78rem;color:#e11d48;font-weight:600">Quitar todas</button>
                        </div>
                        <div id="thumbnails" style="margin-top:1rem;display:flex;flex-wrap:wrap;gap:1rem"></div>
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

                {{-- Botón Guardar --}}
                <div class="text-center mt-6 pb-4">
                    <button type="button" id="btnGuardar" class="px-8 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-lg shadow-md text-lg transition-colors">
                        &#128190; Guardar Acta
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

        const maxFiles = 2;
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
            counter.textContent = `${count} / 2 imágenes`;
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
    });
    </script>
@endpush