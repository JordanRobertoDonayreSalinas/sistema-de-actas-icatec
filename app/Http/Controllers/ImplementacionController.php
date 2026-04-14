<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\ImplementacionHelper;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\Establecimiento;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use App\Mail\ActaImplementacionMail;
use App\Services\RenipressService;
use App\Services\SignatureService;


class ImplementacionController extends Controller
{
    /**
     * Muestra el listado de todas las actas de implementación (todos los módulos mezclados).
     */
    public function index(Request $request)
    {
        $fecha_inicio = $request->input('fecha_inicio', \Carbon\Carbon::now()->startOfYear()->format('Y-m-d'));
        $fecha_fin = $request->input('fecha_fin', \Carbon\Carbon::now()->format('Y-m-d'));

        $actasTodas = collect();
        $modulos = ImplementacionHelper::getModulos();
        $filtroModulo = $request->get('modulo_key');

        $provinciasSet = collect();
        
        foreach ($modulos as $key => $config) {
            $modeloActa = $config['modelo'];
            if (!class_exists($modeloActa)) continue;

            $provinciasSet = $provinciasSet->merge($modeloActa::distinct()->pluck('provincia'));

            if ($filtroModulo && $filtroModulo !== $key) {
                continue;
            }

            $query = $modeloActa::with('implementadores')
                ->whereBetween('fecha', [$fecha_inicio, $fecha_fin]);

            if ($request->filled('provincia')) {
                $query->where('provincia', $request->provincia);
            }
            if ($request->filled('distrito')) {
                $query->where('distrito', $request->distrito);
            }
            if ($request->filled('estado')) {
                if ($request->estado == 'firmada') {
                    $query->whereNotNull('archivo_pdf');
                } elseif ($request->estado == 'pendiente') {
                    $query->whereNull('archivo_pdf');
                }
            }
            
            // Filtro por implementador mejorado
            if ($request->filled('implementador')) {
                $query->whereHas('implementadores', function ($q) use ($request) {
                    $search = $request->implementador;
                    $q->where(function ($sub) use ($search) {
                        $sub->where('nombres', 'like', '%' . $search . '%')
                            ->orWhere('apellido_paterno', 'like', '%' . $search . '%')
                            ->orWhere('apellido_materno', 'like', '%' . $search . '%')
                            ->orWhere(\Illuminate\Support\Facades\DB::raw("CONCAT(nombres, ' ', apellido_paterno, ' ', IFNULL(apellido_materno, ''))"), 'like', '%' . $search . '%')
                            ->orWhere(\Illuminate\Support\Facades\DB::raw("CONCAT(apellido_paterno, ' ', IFNULL(apellido_materno, ''), ' ', nombres)"), 'like', '%' . $search . '%');
                    });
                });
            }

            $actasModulo = $query->get()->map(function ($acta) use ($key, $config) {
                $implementadores = $acta->implementadores->map(function ($imp) {
                    return $imp->apellido_paterno . ', ' . $imp->nombres;
                })->implode(' | ');

                return [
                    'id' => $acta->id,
                    'nombre' => $config['nombre'] . ' #' . $acta->id,
                    'fecha' => $acta->fecha,
                    'establecimiento' => $acta->codigo_establecimiento . ' - ' . $acta->nombre_establecimiento,
                    'provincia' => $acta->provincia,
                    'distrito' => $acta->distrito,
                    'implementadores' => $implementadores,
                    'implementadores_data' => $acta->implementadores,
                    'archivo_pdf' => $acta->archivo_pdf,
                    'anulado' => (bool)$acta->anulado,
                    'ruta_pdf' => route('usuario.implementacion.pdf', ['modulo' => $key, 'id' => $acta->id]),
                    'ruta_editar' => route('usuario.implementacion.edit', ['modulo' => $key, 'id' => $acta->id]),
                    'ruta_eliminar' => route('usuario.implementacion.destroy', ['modulo' => $key, 'id' => $acta->id]),
                    'ruta_anular' => route('usuario.implementacion.anular', ['modulo' => $key, 'id' => $acta->id]),
                    'created_at' => $acta->created_at,
                    'tipo_key' => $key,
                    'tipo_nombre' => $config['nombre'],
                ];
            });

            $actasTodas = $actasTodas->merge($actasModulo);
        }

        $actasTodas = $actasTodas->sortByDesc('created_at')->values();
        $provincias = $provinciasSet->filter()->unique()->sort()->values();
        
        $distritos = collect();
        if ($request->filled('provincia')) {
            foreach ($modulos as $k => $c) {
                if (class_exists($c['modelo'])) {
                    $d = $c['modelo']::where('provincia', $request->provincia)->distinct()->pluck('distrito');
                    $distritos = $distritos->merge($d);
                }
            }
        }
        $distritos = $distritos->filter()->unique()->sort()->values();

        $implementadoresUnicos = collect();
        if ($actasTodas->count() > 0) {
            $implementadoresUnicos = $actasTodas->pluck('implementadores_data')->flatten()
                ->map(function($i) { 
                    return mb_strtoupper(trim($i->apellido_paterno . ' ' . ($i->apellido_materno ?? '') . ', ' . $i->nombres), 'UTF-8'); 
                })
                ->filter()->unique()->sort()->values();
        }

        $totalActas = $actasTodas->count();
        $countCompletados = $actasTodas->whereNotNull('archivo_pdf')->count();
        $countPendientes = $totalActas - $countCompletados;

        $page = $request->get('page', 1);
        $perPage = $request->get('perPage', 10);
        $offset = ($page - 1) * $perPage;

        $actasPaginadas = new LengthAwarePaginator(
            $actasTodas->slice($offset, $perPage),
            $totalActas,
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('usuario.implementacion.index', [
            'actas' => $actasPaginadas,
            'modulos' => $modulos,
            'provincias' => $provincias,
            'distritos' => $distritos,
            'implementadores' => $implementadoresUnicos,
            'countCompletados' => $countCompletados,
            'countPendientes' => $countPendientes,
            'totalActas' => $totalActas,
            'fecha_inicio' => $fecha_inicio,
            'fecha_fin' => $fecha_fin,
            'filtroModulo' => $filtroModulo
        ]);
    }

    /**
     * Muestra el formulario para crear una nueva acta (requiere saber qué módulo).
     */
    public function create(Request $request)
    {
        $modulos = ImplementacionHelper::getModulos();
        $moduloKey = $request->get('modulo', array_key_first($modulos));
        
        if (!isset($modulos[$moduloKey])) {
            abort(404, 'Módulo no válido');
        }

        return view('usuario.implementacion.create', [
            'moduloKey' => $moduloKey,
            'moduloConfig' => $modulos[$moduloKey],
            'modulos' => $modulos
        ]);
    }

    /**
     * Guarda el acta de cualquier módulo.
     */
    public function store(Request $request)
    {
        $moduloKey = $request->input('modulo_key');
        $modulos = ImplementacionHelper::getModulos();
        if (!isset($modulos[$moduloKey])) {
            return redirect()->back()->with('error', 'Módulo inválido.');
        }

        $config = $modulos[$moduloKey];
        $ModeloActa = $config['modelo'];

        // REGLA: Un acta activa por establecimiento y módulo
        if ($ModeloActa::where('codigo_establecimiento', $request->codigo_establecimiento)->where('anulado', 0)->exists()) {
            return redirect()->back()
                ->with('error', 'Ya existe un acta activa y válida para este establecimiento en este módulo. No es necesario registrar una nueva.')
                ->withInput();
        }

        $rules = [
            'fecha' => 'required|date',
            'codigo_establecimiento' => 'required|string',
            'nombre_establecimiento' => 'required|string',
            'provincia' => 'required|string',
            'distrito' => 'required|string',
            'categoria' => 'required|string',
            'responsable' => 'required|string',
            'archivo_pdf' => 'nullable|mimes:pdf|max:5120', // Max 5MB
            'foto1' => 'nullable|image|max:5120',
            'foto2' => 'nullable|image|max:5120',
        ];

        if ($moduloKey === 'citas') {
            $rules['modalidad'] = 'required|string';
        }

        $request->validate($rules);

        $rutaPdf = null;
        if ($request->hasFile('archivo_pdf')) {
            $archivo = $request->file('archivo_pdf');
            $nombreArchivo = 'acta_' . $moduloKey . '_' . time() . '.' . $archivo->getClientOriginalExtension();
            $rutaPdf = $archivo->storeAs('actas_implementacion/' . $moduloKey, $nombreArchivo, 'public');
        }

        $rutaFoto1 = null;
        if ($request->hasFile('foto1')) {
            $f = $request->file('foto1');
            $rutaFoto1 = $f->storeAs('actas_implementacion/' . $moduloKey . '/fotos', 'foto1_' . time() . '.' . $f->getClientOriginalExtension(), 'public');
        }

        $rutaFoto2 = null;
        if ($request->hasFile('foto2')) {
            $f = $request->file('foto2');
            $rutaFoto2 = $f->storeAs('actas_implementacion/' . $moduloKey . '/fotos', 'foto2_' . time() . '.' . $f->getClientOriginalExtension(), 'public');
        }

        $actaData = [
            'modulo' => mb_strtoupper($config['nombre'], 'UTF-8'),
            'fecha' => $request->fecha,
            'codigo_establecimiento' => mb_strtoupper($request->codigo_establecimiento, 'UTF-8'),
            'nombre_establecimiento' => mb_strtoupper($request->nombre_establecimiento, 'UTF-8'),
            'provincia' => mb_strtoupper($request->provincia, 'UTF-8'),
            'distrito' => mb_strtoupper($request->distrito, 'UTF-8'),
            'categoria' => mb_strtoupper($request->categoria, 'UTF-8'),
            'red' => mb_strtoupper($request->red ?? '', 'UTF-8'),
            'microred' => mb_strtoupper($request->microred ?? '', 'UTF-8'),
            'responsable' => mb_strtoupper($request->responsable, 'UTF-8'),
            'observaciones' => mb_strtoupper($request->observaciones ?? '', 'UTF-8'),
            'archivo_pdf' => $rutaPdf,
            'foto1' => $rutaFoto1,
            'foto2' => $rutaFoto2,
        ];

        if ($request->has('firma_digital')) {
            $actaData['firma_digital'] = mb_strtoupper($request->firma_digital, 'UTF-8');
        }

        if ($moduloKey === 'citas' && $request->has('modalidad')) {
            $actaData['modalidad'] = mb_strtoupper($request->modalidad, 'UTF-8');
        }

        if ($request->filled('renipress_data')) {
            $actaData['renipress_data'] = json_decode($request->renipress_data, true);
        }

        // Registrar acta
        $acta = $ModeloActa::create($actaData);

        // Registrar participantes (usuarios)
        if ($request->has('usuarios')) {
            $this->updateGlobalProfesionales($request->usuarios);
            foreach ($request->usuarios as $user) {
                if (!empty($user['dni'])) {
                    $acta->usuarios()->create([
                        'dni' => $user['dni'],
                        'apellido_paterno' => mb_strtoupper($user['apellido_paterno'] ?? '', 'UTF-8'),
                        'apellido_materno' => mb_strtoupper($user['apellido_materno'] ?? '', 'UTF-8'),
                        'nombres' => mb_strtoupper($user['nombres'] ?? '', 'UTF-8'),
                        'celular' => preg_match('/^\d{9,}$/', preg_replace('/[^0-9]/', '', $user['celular'] ?? '')) ? preg_replace('/[^0-9]/', '', $user['celular']) : 999999999,
                        'correo' => strtolower($user['correo'] ?? ''),
                        'permisos' => mb_strtoupper($user['permisos'] ?? '', 'UTF-8'),
                    ]);
                }
            }
        }

        // Registrar implementadores
        if ($request->has('implementadores')) {
            $this->updateGlobalProfesionales($request->implementadores);
            foreach ($request->implementadores as $impl) {
                if (!empty($impl['dni'])) {
                    $acta->implementadores()->create([
                        'dni' => $impl['dni'],
                        'apellido_paterno' => mb_strtoupper($impl['apellido_paterno'] ?? '', 'UTF-8'),
                        'apellido_materno' => mb_strtoupper($impl['apellido_materno'] ?? '', 'UTF-8'),
                        'nombres' => mb_strtoupper($impl['nombres'] ?? '', 'UTF-8'),
                        'cargo' => mb_strtoupper($impl['cargo'] ?? '', 'UTF-8'),
                    ]);
                }
            }
        }

        return redirect()->route('usuario.implementacion.index')->with('success', 'Acta registrada correctamente.');
    }

    /**
     * Muestra el formulario de edición.
     */
    public function edit($modulo, $id)
    {
        $modulos = ImplementacionHelper::getModulos();
        if (!isset($modulos[$modulo])) abort(404);

        $config = $modulos[$modulo];
        $ModeloActa = $config['modelo'];
        
        $acta = $ModeloActa::with(['usuarios', 'implementadores'])->findOrFail($id);

        return view('usuario.implementacion.edit', [
            'acta' => $acta,
            'moduloKey' => $modulo,
            'moduloConfig' => $config,
            'modulos' => $modulos
        ]);
    }

    /**
     * Actualiza el acta de un módulo.
     */
    public function update(Request $request, $modulo, $id)
    {
        $modulos = ImplementacionHelper::getModulos();
        if (!isset($modulos[$modulo])) abort(404);

        $config = $modulos[$modulo];
        $ModeloActa = $config['modelo'];
        
        $rules = [
            'fecha' => 'required|date',
            'codigo_establecimiento' => 'required|string',
            'nombre_establecimiento' => 'required|string',
            'provincia' => 'required|string',
            'distrito' => 'required|string',
            'categoria' => 'required|string',
            'responsable' => 'required|string',
            'archivo_pdf' => 'nullable|mimes:pdf|max:5120', // Max 5MB
            'foto1' => 'nullable|image|max:5120',
            'foto2' => 'nullable|image|max:5120',
        ];

        if ($modulo === 'citas') {
            $rules['modalidad'] = 'required|string';
        }

        $request->validate($rules);

        $acta = $ModeloActa::findOrFail($id);
        
        $rutaPdf = $acta->archivo_pdf;
        if ($request->hasFile('archivo_pdf')) {
            if ($rutaPdf && Storage::disk('public')->exists($rutaPdf)) {
                Storage::disk('public')->delete($rutaPdf);
            }
            $archivo = $request->file('archivo_pdf');
            $nombreArchivo = 'acta_' . $modulo . '_' . time() . '.' . $archivo->getClientOriginalExtension();
            $rutaPdf = $archivo->storeAs('actas_implementacion/' . $modulo, $nombreArchivo, 'public');
        }

        $rutaFoto1 = $acta->foto1;
        if ($request->hasFile('foto1')) {
            if ($rutaFoto1 && Storage::disk('public')->exists($rutaFoto1)) {
                Storage::disk('public')->delete($rutaFoto1);
            }
            $f = $request->file('foto1');
            $rutaFoto1 = $f->storeAs('actas_implementacion/' . $modulo . '/fotos', 'foto1_' . time() . '.' . $f->getClientOriginalExtension(), 'public');
        }

        $rutaFoto2 = $acta->foto2;
        if ($request->hasFile('foto2')) {
            if ($rutaFoto2 && Storage::disk('public')->exists($rutaFoto2)) {
                Storage::disk('public')->delete($rutaFoto2);
            }
            $f = $request->file('foto2');
            $rutaFoto2 = $f->storeAs('actas_implementacion/' . $modulo . '/fotos', 'foto2_' . time() . '.' . $f->getClientOriginalExtension(), 'public');
        }

        // 1. Actualizar datos principales
        $actaData = [
            'fecha' => $request->fecha,
            'codigo_establecimiento' => mb_strtoupper($request->codigo_establecimiento, 'UTF-8'),
            'nombre_establecimiento' => mb_strtoupper($request->nombre_establecimiento, 'UTF-8'),
            'provincia' => mb_strtoupper($request->provincia, 'UTF-8'),
            'distrito' => mb_strtoupper($request->distrito, 'UTF-8'),
            'categoria' => mb_strtoupper($request->categoria, 'UTF-8'),
            'red' => mb_strtoupper($request->red ?? '', 'UTF-8'),
            'microred' => mb_strtoupper($request->microred ?? '', 'UTF-8'),
            'responsable' => mb_strtoupper($request->responsable, 'UTF-8'),
            'observaciones' => mb_strtoupper($request->observaciones ?? '', 'UTF-8'),
            'archivo_pdf' => $rutaPdf,
            'foto1' => $rutaFoto1,
            'foto2' => $rutaFoto2,
        ];

        if ($request->has('firma_digital')) {
            $actaData['firma_digital'] = mb_strtoupper($request->firma_digital, 'UTF-8');
        }

        if ($modulo === 'citas' && $request->has('modalidad')) {
            $actaData['modalidad'] = mb_strtoupper($request->modalidad, 'UTF-8');
        }

        if ($request->filled('renipress_data')) {
            $actaData['renipress_data'] = json_decode($request->renipress_data, true);
        }

        $acta->update($actaData);

        // Reemplazar usuarios
        $acta->usuarios()->delete();
        if ($request->has('usuarios')) {
            $this->updateGlobalProfesionales($request->usuarios);
            foreach ($request->usuarios as $user) {
                if (!empty($user['dni'])) {
                    $acta->usuarios()->create([
                        'dni' => $user['dni'],
                        'apellido_paterno' => mb_strtoupper($user['apellido_paterno'] ?? '', 'UTF-8'),
                        'apellido_materno' => mb_strtoupper($user['apellido_materno'] ?? '', 'UTF-8'),
                        'nombres' => mb_strtoupper($user['nombres'] ?? '', 'UTF-8'),
                        'celular' => preg_match('/^\d{9,}$/', preg_replace('/[^0-9]/', '', $user['celular'] ?? '')) ? preg_replace('/[^0-9]/', '', $user['celular']) : 999999999,
                        'correo' => strtolower($user['correo'] ?? ''),
                        'permisos' => mb_strtoupper($user['permisos'] ?? '', 'UTF-8'),
                    ]);
                }
            }
        }

        // Reemplazar implementadores
        $acta->implementadores()->delete();
        if ($request->has('implementadores')) {
            $this->updateGlobalProfesionales($request->implementadores);
            foreach ($request->implementadores as $impl) {
                if (!empty($impl['dni'])) {
                    $acta->implementadores()->create([
                        'dni' => $impl['dni'],
                        'apellido_paterno' => mb_strtoupper($impl['apellido_paterno'] ?? '', 'UTF-8'),
                        'apellido_materno' => mb_strtoupper($impl['apellido_materno'] ?? '', 'UTF-8'),
                        'nombres' => mb_strtoupper($impl['nombres'] ?? '', 'UTF-8'),
                        'cargo' => mb_strtoupper($impl['cargo'] ?? '', 'UTF-8'),
                    ]);
                }
            }
        }

        return redirect()->route('usuario.implementacion.index')->with('success', 'Acta actualizada correctamente.');
    }

    /**
     * Elimina el acta.
     */
    public function destroy($modulo, $id)
    {
        $modulos = ImplementacionHelper::getModulos();
        if (!isset($modulos[$modulo])) abort(404);

        $ModeloActa = $modulos[$modulo]['modelo'];
        $acta = $ModeloActa::findOrFail($id);
        
        if ($acta->archivo_pdf && Storage::disk('public')->exists($acta->archivo_pdf)) {
            Storage::disk('public')->delete($acta->archivo_pdf);
        }

        // Asumiendo que las FK tienen ON DELETE CASCADE, sino borrar relaciones primero
        $acta->usuarios()->delete();
        $acta->implementadores()->delete();
        $acta->delete();

        // Reajustar AUTO_INCREMENT para evitar saltos en la numeración
        $tableName = (new $ModeloActa)->getTable();
        $maxId = $ModeloActa::max('id') ?? 0;
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `{$tableName}` AUTO_INCREMENT = " . ($maxId + 1));

        return redirect()->back()->with('success', 'Acta eliminada correctamente.');
    }

    /**
     * Cambia el acta de un módulo a otro, migrando sus datos básicos.
     */
    public function cambiar_modulo(Request $request, $modulo, $id)
    {
        $modulos = ImplementacionHelper::getModulos();
        $nuevoModulo = $request->input('nuevo_modulo');
        
        if (!isset($modulos[$modulo]) || !isset($modulos[$nuevoModulo])) abort(404);

        $ModeloViejo = $modulos[$modulo]['modelo'];
        $actaVieja = $ModeloViejo::with(['usuarios', 'implementadores'])->findOrFail($id);
        
        $ModeloNuevo = $modulos[$nuevoModulo]['modelo'];
        
        $datosNuevos = [
            'modulo' => mb_strtoupper($modulos[$nuevoModulo]['nombre'], 'UTF-8'),
            'fecha' => $actaVieja->fecha,
            'codigo_establecimiento' => $actaVieja->codigo_establecimiento,
            'nombre_establecimiento' => $actaVieja->nombre_establecimiento,
            'provincia' => $actaVieja->provincia,
            'distrito' => $actaVieja->distrito,
            'categoria' => $actaVieja->categoria,
            'red' => $actaVieja->red,
            'microred' => $actaVieja->microred,
            'responsable' => $actaVieja->responsable,
            'observaciones' => $actaVieja->observaciones,
            'archivo_pdf' => $actaVieja->archivo_pdf,
            'foto1' => $actaVieja->foto1,
            'foto2' => $actaVieja->foto2,
        ];

        if (in_array($nuevoModulo, ['medicina', 'odontologia', 'nutricion', 'psicologia', 'mental', 'emergencia', 'referencias', 'laboratorio', 'farmacia', 'fua'])) {
            $datosNuevos['firma_digital'] = $actaVieja->firma_digital ?? 'NO';
        }

        if ($nuevoModulo === 'citas') {
            $datosNuevos['modalidad'] = $actaVieja->modalidad ?? 'POR HORARIO';
        }

        $nuevaActa = $ModeloNuevo::create($datosNuevos);
        
        // Copiar usuarios
        foreach($actaVieja->usuarios as $u) {
            $nuevaActa->usuarios()->create($u->only(['tipo_doc', 'dni', 'apellido_paterno', 'apellido_materno', 'nombres', 'celular', 'correo', 'permisos']));
        }
        
        // Copiar implementadores
        foreach($actaVieja->implementadores as $i) {
            $nuevaActa->implementadores()->create($i->only(['dni', 'apellido_paterno', 'apellido_materno', 'nombres', 'cargo']));
        }
        
        // Eliminar acta vieja y resetear AUTO_INCREMENT
        $actaVieja->usuarios()->delete();
        $actaVieja->implementadores()->delete();
        $actaVieja->delete();

        $tableNameViejo = (new $ModeloViejo)->getTable();
        $maxIdViejo = $ModeloViejo::max('id') ?? 0;
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `{$tableNameViejo}` AUTO_INCREMENT = " . ($maxIdViejo + 1));

        return redirect()->route('usuario.implementacion.edit', ['modulo' => $nuevoModulo, 'id' => $nuevaActa->id])
            ->with('success', 'El acta ha sido cambiada al módulo ' . $modulos[$nuevoModulo]['nombre'] . ' exitosamente.');
    }

    /**
     * Genera el PDF del acta.
     */
    public function pdf(Request $request, $modulo, $id)
    {
        $modulos = ImplementacionHelper::getModulos();
        if (!isset($modulos[$modulo])) abort(404);

        $ModeloActa = $modulos[$modulo]['modelo'];
        $acta = $ModeloActa::with(['usuarios', 'implementadores'])->findOrFail($id);

        // Cargar relaciones adicionales según el módulo
        if ($modulo === 'ges_adm') {
            if (method_exists($acta, 'upss')) {
                $acta->load('upss');
            } else {
                $acta->setRelation('upss', collect());
            }
            if (method_exists($acta, 'sugeridas')) {
                $acta->load('sugeridas');
            } else {
                $acta->setRelation('sugeridas', collect());
            }
        }

        // --- SISTEMA DE FIRMAS ---
        $firmas = collect();
        if ($request->get('digital') == '1') {
            $service = app(SignatureService::class);
            
            // 1. Obtener DNI de todos los involucrados
            $dnis = array_merge(
                $acta->usuarios->pluck('dni')->toArray(),
                $acta->implementadores->pluck('dni')->toArray()
            );

            // 2. Buscar firmas en el banco
            $firmasResult = $service->getMultipleSignatures($dnis);
            
            // 3. Mapear por DNI para la vista
            foreach ($firmasResult as $dni => $profesional) {
                $firmas->put($dni, [
                    'url' => Storage::disk('public')->path($profesional->firma_path), // Usar path absoluto para DomPDF
                    'profesional' => $profesional->apellido_paterno . ' ' . $profesional->nombres
                ]);
            }
        }

        // Usar la vista específica de cada módulo
        $viewName = 'usuario.implementacion.impresiones.' . $modulo;
        if (!view()->exists($viewName)) {
            $viewName = 'usuario.implementacion.impresiones.triaje';
        }

        $pdf = Pdf::loadView($viewName, [
            'acta' => $acta,
            'firmas' => $firmas,
            'digital' => $request->get('digital') == '1'
        ]);
        
        $pdf->getDomPDF()->getOptions()->setIsPhpEnabled(true);
        $pdf->getDomPDF()->getOptions()->setIsRemoteEnabled(true);
        
        return $pdf->stream('AI Nº ' . $acta->id . '-' . $modulos[$modulo]['nombre'] . '-' . $acta->nombre_establecimiento . '.pdf');
    }

    /**
     * Endpoint API para buscar establecimientos
     */
    public function buscarEstablecimiento(Request $request)
    {
        $q = $request->get('q');
        return Establecimiento::where('nombre', 'like', "%$q%")
            ->orWhere('codigo', 'like', "%$q%")
            ->limit(10)
            ->get(['codigo as codigo_establecimiento', 'nombre as nombre_establecimiento', 'provincia', 'distrito', 'categoria', 'red', 'microred', 'responsable']);
    }

    /**
     * Endpoint API para buscar UPSS en la tabla maestra
     */
    public function buscarUpss(Request $request)
    {
        $q = $request->get('q');
        return \Illuminate\Support\Facades\DB::table('upss_ups')
            ->where('codigo_ups', 'like', "%$q%")
            ->orWhere('descripcion_ups', 'like', "%$q%")
            ->limit(20)
            ->get();
    }

    /**
     * Endpoint AJAX para verificar si ya existe un acta activa
     */
    public function checkDuplicado(Request $request)
    {
        $codigo = $request->get('codigo');
        $moduloKey = $request->get('modulo');
        $modulos = ImplementacionHelper::getModulos();
        
        if (!isset($modulos[$moduloKey])) {
            return response()->json(['exists' => false]);
        }
        
        $ModeloActa = $modulos[$moduloKey]['modelo'];
        $acta = $ModeloActa::where('codigo_establecimiento', $codigo)
            ->where('anulado', 0)
            ->first();

        if ($acta) {
            return response()->json([
                'exists' => true,
                'id' => $acta->id,
                'fecha' => $acta->fecha,
                'nombre' => $acta->nombre_establecimiento
            ]);
        }

        return response()->json(['exists' => false]);
    }

    /**
     * Endpoint API para sincronizar datos desde RENIPRESS (Susalud)
     */
    public function syncRenipress(Request $request)
    {
        $codigo = $request->get('codigo');
        if (empty($codigo)) {
            return response()->json(['success' => false, 'message' => 'Código IPRESS requerido'], 400);
        }

        try {
            $service = new RenipressService();
            $data = $service->getDatosEstablecimiento($codigo);

            if (!$data) {
                // Retornamos 200 con success false para que el frontend abra el modo manual sin errores de consola
                return response()->json([
                    'success' => false, 
                    'message' => 'SUSALUD ha bloqueado el acceso automático momentáneamente. Por favor, use el modo manual.'
                ], 200);
            }

            return response()->json(['success' => true, 'data' => $data]);
        } catch (\Exception $e) {
            Log::error("Error en syncRenipress para {$codigo}: " . $e->getMessage());
            return response()->json([
                'success' => false, 
                'message' => 'Error de conexión con SUSALUD. Se ha habilitado el modo manual.',
                'error' => $e->getMessage()
            ], 200);
        }
    }
    /**
     * Actualiza o crea registros en la tabla global de profesionales
     * para facilitar la búsqueda con autocompletado en futuras actas.
     */
    private function updateGlobalProfesionales($personas)
    {
        if (!$personas || !is_array($personas)) return;

        foreach ($personas as $persona) {
            if (!empty($persona['dni'])) {
                $profesional = \App\Models\Profesional::firstOrNew(['doc' => $persona['dni']]);
                
                if (!empty($persona['apellido_paterno'])) {
                    $profesional->apellido_paterno = mb_strtoupper($persona['apellido_paterno'], 'UTF-8');
                }
                if (!empty($persona['apellido_materno'])) {
                    $profesional->apellido_materno = mb_strtoupper($persona['apellido_materno'], 'UTF-8');
                }
                if (!empty($persona['nombres'])) {
                    $profesional->nombres = mb_strtoupper($persona['nombres'], 'UTF-8');
                }
                if (!empty($persona['celular'])) {
                    $profesional->telefono = $persona['celular'];
                }
                if (!empty($persona['correo'])) {
                    $profesional->email = strtolower($persona['correo']);
                }
                
                // Si es un registro nuevo, o si viene el tipo_doc, actualizarlo
                if (isset($persona['tipo_doc'])) {
                    $profesional->tipo_doc = mb_strtoupper($persona['tipo_doc'], 'UTF-8');
                } else if (!$profesional->exists) {
                    $profesional->tipo_doc = 'DNI';
                }

                $profesional->save();
            }
        }
    }

    /**
     * Sube el acta firmada en formato PDF.
     */
    public function subirPdf(Request $request, $modulo, $id)
    {
        $modulos = ImplementacionHelper::getModulos();
        if (!isset($modulos[$modulo])) {
            return response()->json(['success' => false, 'message' => 'Módulo no válido'], 404);
        }

        $config = $modulos[$modulo];
        $ModeloActa = $config['modelo'];
        $acta = $ModeloActa::findOrFail($id);

        $request->validate([
            'pdf_firmado' => 'required|mimes:pdf|max:10240', // Máximo 10MB
        ]);

        if ($request->hasFile('pdf_firmado')) {
            // Eliminar archivo anterior si existe
            if ($acta->archivo_pdf && Storage::disk('public')->exists($acta->archivo_pdf)) {
                Storage::disk('public')->delete($acta->archivo_pdf);
            }

            $archivo = $request->file('pdf_firmado');
            $nombreArchivo = 'acta_firmada_' . $modulo . '_' . $id . '_' . time() . '.pdf';
            $ruta = $archivo->storeAs('actas_implementacion/' . $modulo, $nombreArchivo, 'public');

            // Actualizar registro en la base de datos
            $acta->update(['archivo_pdf' => $ruta]);

            return response()->json([
                'success' => true,
                'message' => 'El acta firmada ha sido subida correctamente.',
                'ruta' => Storage::url($ruta)
            ]);
        }


        return response()->json(['success' => false, 'message' => 'No se recibió ningún archivo.'], 400);
    }

    /**
     * Envía el acta firmada por correo a los participantes.
     * Basado estrictamente en la lógica de Mesa de Ayuda.
     */
    public function enviarCorreo(Request $request, $modulo, $id)
    {
        try {
            $modulos = ImplementacionHelper::getModulos();
            if (!isset($modulos[$modulo])) {
                return response()->json(['success' => false, 'message' => 'Módulo no válido.'], 404);
            }

            $config = $modulos[$modulo];
            $ModeloActa = $config['modelo'];
            $acta = $ModeloActa::with('usuarios')->findOrFail($id);

            // 1. Verificar si tiene archivo adjunto
            if (!$acta->archivo_pdf || !Storage::disk('public')->exists($acta->archivo_pdf)) {
                return response()->json(['success' => false, 'message' => 'No se puede enviar el acta porque no tiene un archivo firmado cargado.'], 400);
            }

            // 2. Filtrar correos de participantes registrados
            $correos = $acta->usuarios->pluck('correo')->filter(function ($email) {
                return !empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL);
            })->unique();

            if ($correos->isEmpty()) {
                return response()->json(['success' => false, 'message' => 'No se encontraron participantes con correos electrónicos válidos.'], 400);
            }

            // 3. Ejecutar envío individual (como en Mesa de Ayuda)
            $enviados = 0;
            foreach ($correos as $correo) {
                try {
                    // Log previo (útil si el mailer es 'log')
                    \Illuminate\Support\Facades\Log::info("📨 Preparando envío de acta {$config['nombre']} #{$acta->id} para: {$correo}");
                    
                    Mail::to($correo)->send(new ActaImplementacionMail($acta, $config['nombre']));
                    $enviados++;
                } catch (\Throwable $mailEx) {
                    \Illuminate\Support\Facades\Log::warning('⚠️ No se pudo enviar correo de acta de implementación', [
                        'error'   => $mailEx->getMessage(),
                        'acta'    => $acta->id,
                        'email'   => $correo
                    ]);
                }
            }

            if ($enviados > 0) {
                return response()->json([
                    'success' => true,
                    'message' => "✅ Proceso completado: se enviaron {$enviados} correos exitosamente.",
                    'destinatarios' => $correos
                ]);
            }

            return response()->json(['success' => false, 'message' => 'No se pudo completar el envío de ningún correo. Verifique logs del servidor.'], 500);

        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('❌ Error crítico en ImplementacionController@enviarCorreo', [
                'error' => $e->getMessage(),
                'line'  => $e->getLine(),
                'file'  => $e->getFile()
            ]);
            return response()->json(['success' => false, 'message' => 'Error interno: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Anula el acta cambiando su estado.
     */
    public function anular($modulo, $id)
    {
        try {
            $modulos = ImplementacionHelper::getModulos();
            if (!isset($modulos[$modulo])) abort(404);

            $ModeloActa = $modulos[$modulo]['modelo'];
            $acta = $ModeloActa::findOrFail($id);
            
            // Alternar estado de anulación
            $nuevoEstado = !$acta->anulado;
            $acta->update(['anulado' => $nuevoEstado]);

            $mensaje = $nuevoEstado ? 'Acta anulada correctamente.' : 'Acta reactivada correctamente.';

            return response()->json([
                'success' => true,
                'message' => $mensaje,
                'anulado' => $nuevoEstado
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al anular el acta: ' . $e->getMessage()
            ], 500);
        }
    }
}

