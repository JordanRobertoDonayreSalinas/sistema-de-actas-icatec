<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\ImplementacionHelper;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\Establecimiento;
use Illuminate\Support\Facades\Storage;

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
            
            // Filtro por implementador
            if ($request->filled('implementador')) {
                $query->whereHas('implementadores', function ($q) use ($request) {
                    $q->where('nombres', 'like', '%' . $request->implementador . '%')
                      ->orWhere('apellido_paterno', 'like', '%' . $request->implementador . '%')
                      ->orWhere('apellido_materno', 'like', '%' . $request->implementador . '%');
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
                    'ruta_pdf' => route('usuario.implementacion.pdf', ['modulo' => $key, 'id' => $acta->id]),
                    'ruta_editar' => route('usuario.implementacion.edit', ['modulo' => $key, 'id' => $acta->id]),
                    'ruta_eliminar' => route('usuario.implementacion.destroy', ['modulo' => $key, 'id' => $acta->id]),
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
                ->map(function($i) { return $i->nombres . ' ' . $i->apellido_paterno; })
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
            'modulo' => strtoupper($config['nombre']),
            'fecha' => $request->fecha,
            'codigo_establecimiento' => strtoupper($request->codigo_establecimiento),
            'nombre_establecimiento' => strtoupper($request->nombre_establecimiento),
            'provincia' => strtoupper($request->provincia),
            'distrito' => strtoupper($request->distrito),
            'categoria' => strtoupper($request->categoria),
            'red' => strtoupper($request->red ?? ''),
            'microred' => strtoupper($request->microred ?? ''),
            'responsable' => strtoupper($request->responsable),
            'observaciones' => strtoupper($request->observaciones ?? ''),
            'archivo_pdf' => $rutaPdf,
            'foto1' => $rutaFoto1,
            'foto2' => $rutaFoto2,
        ];

        if ($request->has('firma_digital')) {
            $actaData['firma_digital'] = strtoupper($request->firma_digital);
        }

        if ($moduloKey === 'citas' && $request->has('modalidad')) {
            $actaData['modalidad'] = strtoupper($request->modalidad);
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
                        'apellido_paterno' => strtoupper($user['apellido_paterno'] ?? ''),
                        'apellido_materno' => strtoupper($user['apellido_materno'] ?? ''),
                        'nombres' => strtoupper($user['nombres'] ?? ''),
                        'celular' => preg_match('/^\d{9,}$/', preg_replace('/[^0-9]/', '', $user['celular'] ?? '')) ? preg_replace('/[^0-9]/', '', $user['celular']) : 999999999,
                        'correo' => strtolower($user['correo'] ?? ''),
                        'permisos' => strtoupper($user['permisos'] ?? ''),
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
                        'apellido_paterno' => strtoupper($impl['apellido_paterno'] ?? ''),
                        'apellido_materno' => strtoupper($impl['apellido_materno'] ?? ''),
                        'nombres' => strtoupper($impl['nombres'] ?? ''),
                        'cargo' => strtoupper($impl['cargo'] ?? ''),
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
            'codigo_establecimiento' => strtoupper($request->codigo_establecimiento),
            'nombre_establecimiento' => strtoupper($request->nombre_establecimiento),
            'provincia' => strtoupper($request->provincia),
            'distrito' => strtoupper($request->distrito),
            'categoria' => strtoupper($request->categoria),
            'red' => strtoupper($request->red ?? ''),
            'microred' => strtoupper($request->microred ?? ''),
            'responsable' => strtoupper($request->responsable),
            'observaciones' => strtoupper($request->observaciones ?? ''),
            'archivo_pdf' => $rutaPdf,
            'foto1' => $rutaFoto1,
            'foto2' => $rutaFoto2,
        ];

        if ($request->has('firma_digital')) {
            $actaData['firma_digital'] = strtoupper($request->firma_digital);
        }

        if ($modulo === 'citas' && $request->has('modalidad')) {
            $actaData['modalidad'] = strtoupper($request->modalidad);
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
                        'apellido_paterno' => strtoupper($user['apellido_paterno'] ?? ''),
                        'apellido_materno' => strtoupper($user['apellido_materno'] ?? ''),
                        'nombres' => strtoupper($user['nombres'] ?? ''),
                        'celular' => preg_match('/^\d{9,}$/', preg_replace('/[^0-9]/', '', $user['celular'] ?? '')) ? preg_replace('/[^0-9]/', '', $user['celular']) : 999999999,
                        'correo' => strtolower($user['correo'] ?? ''),
                        'permisos' => strtoupper($user['permisos'] ?? ''),
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
                        'apellido_paterno' => strtoupper($impl['apellido_paterno'] ?? ''),
                        'apellido_materno' => strtoupper($impl['apellido_materno'] ?? ''),
                        'nombres' => strtoupper($impl['nombres'] ?? ''),
                        'cargo' => strtoupper($impl['cargo'] ?? ''),
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
            'modulo' => strtoupper($modulos[$nuevoModulo]['nombre']),
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
    public function pdf($modulo, $id)
    {
        $modulos = ImplementacionHelper::getModulos();
        if (!isset($modulos[$modulo])) abort(404);

        $ModeloActa = $modulos[$modulo]['modelo'];
        $acta = $ModeloActa::with(['usuarios', 'implementadores'])->findOrFail($id);

        // Cargar relaciones adicionales según el módulo (si existen en el modelo)
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

        // Usar la vista específica de cada módulo (impresiones/{modulo})
        $viewName = 'usuario.implementacion.impresiones.' . $modulo;
        if (!view()->exists($viewName)) {
            // Fallback: vista genérica si no existe una específica para el módulo
            $viewName = 'usuario.implementacion.impresiones.triaje';
        }

        $pdf = Pdf::loadView($viewName, ['acta' => $acta]);
        $pdf->getDomPDF()->getOptions()->setIsPhpEnabled(true);
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
     * Helper para guardar o actualizar la tabla global de Profesionales
     * para facilitar la búsqueda con autocompletado en futuras actas.
     */
    private function updateGlobalProfesionales($personas)
    {
        if (!$personas || !is_array($personas)) return;

        foreach ($personas as $persona) {
            if (!empty($persona['dni'])) {
                $profesional = \App\Models\Profesional::firstOrNew(['doc' => $persona['dni']]);
                
                if (!empty($persona['apellido_paterno'])) {
                    $profesional->apellido_paterno = strtoupper($persona['apellido_paterno']);
                }
                if (!empty($persona['apellido_materno'])) {
                    $profesional->apellido_materno = strtoupper($persona['apellido_materno']);
                }
                if (!empty($persona['nombres'])) {
                    $profesional->nombres = strtoupper($persona['nombres']);
                }
                if (!empty($persona['celular'])) {
                    $profesional->telefono = $persona['celular'];
                }
                if (!empty($persona['correo'])) {
                    $profesional->email = strtolower($persona['correo']);
                }
                
                // Si es un registro nuevo, o si viene el tipo_doc, actualizarlo
                if (isset($persona['tipo_doc'])) {
                    $profesional->tipo_doc = strtoupper($persona['tipo_doc']);
                } else if (!$profesional->exists) {
                    $profesional->tipo_doc = 'DNI';
                }

                $profesional->save();
            }
        }
    }
}
