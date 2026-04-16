<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Acta;
use App\Models\Establecimiento;
use App\Models\CabeceraMonitoreo;
use App\Models\ProgramacionSector;
use App\Models\ProgramacionSectorPropuesta;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\DB as DBFacade;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rules\Password;
use Carbon\Carbon;

class UsuarioController extends Controller
{
    /**
     * DASHBOARD DEL USUARIO (Mapa de Progresión)
     * Muestra en qué etapa del proceso se encuentra cada establecimiento.
     * Sirve como Dashboard General.
     */
    public function index(Request $request)
    {
        $anioFiltro = $request->input('anio', 'todos');
        $modulos = \App\Helpers\ImplementacionHelper::getModulos();

        // Años disponibles (incluyendo monitoreo, asistencia y TODOS los módulos de implementación)
        $aniosCollector = collect([date('Y')]);
        
        $aniosMonitoreo = CabeceraMonitoreo::whereNotNull('fecha')->selectRaw('YEAR(fecha) as anio')->distinct()->pluck('anio');
        $aniosAsistencia = Acta::whereNotNull('fecha')->where('tipo', 'asistencia')->selectRaw('YEAR(fecha) as anio')->distinct()->pluck('anio');
        
        $aniosCollector = $aniosCollector->merge($aniosMonitoreo)->merge($aniosAsistencia);
        
        foreach ($modulos as $mod) {
            $modelo = $mod['modelo'];
            if (class_exists($modelo)) {
                $aniosMod = $modelo::whereNotNull('fecha')->selectRaw('YEAR(fecha) as anio')->distinct()->pluck('anio');
                $aniosCollector = $aniosCollector->merge($aniosMod);
            }
        }
        
        $aniosDisponibles = $aniosCollector->filter()->unique()->sortDesc()->values();

        // ── 1. Códigos que tienen al menos 1 acta de implementación ──────────
        $codigosConImplementacion = collect();
        $modulosPorCodigo = [];   // ['COD' => ['Medicina', 'Citas', ...]]

        foreach ($modulos as $key => $config) {
            $modelo = $config['modelo'];
            if (!class_exists($modelo)) continue;
            
            $query = $modelo::select('codigo_establecimiento', 'nombre_establecimiento');
            if (Schema::hasColumn((new $modelo)->getTable(), 'anulado')) {
                $query->where(function($q) {
                    $q->where('anulado', 0)->orWhereNull('anulado');
                });
            }
            if ($anioFiltro !== 'todos') {
                $query->whereYear('fecha', '<=', $anioFiltro);
            }
            
            $actas = $query->get();
            foreach ($actas as $acta) {
                $cod = $acta->codigo_establecimiento;
                if (!$cod) continue;
                $codigosConImplementacion->push($cod);
                if (!isset($modulosPorCodigo[$cod])) $modulosPorCodigo[$cod] = [];
                if (!in_array($config['nombre'], $modulosPorCodigo[$cod])) {
                    $modulosPorCodigo[$cod][] = $config['nombre'];
                }
            }
        }
        $codigosConImplementacion = $codigosConImplementacion->unique()->values();

        // ── 2. IDs que tienen al menos 1 acta de asistencia técnica ──────────
        $queryAsistencia = Acta::where('tipo', 'asistencia')
                            ->where(function($q) {
                                $q->where('anulado', 0)->orWhereNull('anulado');
                            });
        if ($anioFiltro !== 'todos') {
            $queryAsistencia->whereYear('fecha', '<=', $anioFiltro);
        }
        $idsConAsistencia = $queryAsistencia->distinct()->pluck('establecimiento_id')->toArray();

        $queryTotalAsistencia = Acta::where('tipo', 'asistencia')
                            ->where(function($q) {
                                $q->where('anulado', 0)->orWhereNull('anulado');
                            })
                            ->select('establecimiento_id', DB::raw('count(*) as total'))->groupBy('establecimiento_id');
        if ($anioFiltro !== 'todos') {
            $queryTotalAsistencia->whereYear('fecha', '<=', $anioFiltro);
        }
        $totalAsistenciaPorId = $queryTotalAsistencia->pluck('total', 'establecimiento_id');

        // ── 3. IDs que tienen al menos 1 acta de monitoreo ───────────────────
        $queryMonitoreo = CabeceraMonitoreo::where(function($q) {
                                $q->where('anulado', 0)->orWhereNull('anulado');
                            });
        if ($anioFiltro !== 'todos') {
            $queryMonitoreo->whereYear('fecha', '<=', $anioFiltro);
        }
        $idsConMonitoreo = $queryMonitoreo->distinct()->pluck('establecimiento_id')->toArray();

        $queryTotalMonitoreo = CabeceraMonitoreo::select('establecimiento_id', DB::raw('count(*) as total'))->where(function($q) { $q->where('anulado', 0)->orWhereNull('anulado'); })->groupBy('establecimiento_id');
        if ($anioFiltro !== 'todos') {
            $queryTotalMonitoreo->whereYear('fecha', '<=', $anioFiltro);
        }
        $totalMonitoreoPorId = $queryTotalMonitoreo->pluck('total', 'establecimiento_id');

        // ── 4. Unificar en establecimientos con coordenadas ───────────────────
        $establecimientosMap = Establecimiento::whereNotNull('latitud')
            ->whereNotNull('longitud')
            ->get(['id', 'codigo', 'nombre', 'distrito', 'provincia', 'categoria', 'red', 'microred', 'latitud', 'longitud'])
            ->map(function ($est) use (
                $codigosConImplementacion, $modulosPorCodigo,
                $idsConAsistencia, $totalAsistenciaPorId,
                $idsConMonitoreo, $totalMonitoreoPorId
            ) {
                $tieneImpl      = $codigosConImplementacion->contains($est->codigo);
                $tieneAsist     = in_array($est->id, $idsConAsistencia);
                $tieneMonitoreo = in_array($est->id, $idsConMonitoreo);

                // Etapa de progresión (0-4)
                if ($tieneImpl && $tieneAsist && $tieneMonitoreo) {
                    $etapa = 4; // Ciclo completo
                } elseif ($tieneMonitoreo) {
                    $etapa = 3; // Con Monitoreo
                } elseif ($tieneAsist) {
                    $etapa = 2; // Con Asistencia
                } elseif ($tieneImpl) {
                    $etapa = 1; // Solo Implementado
                } else {
                    $etapa = 0; // Sin inicio
                }

                $est->etapa              = $etapa;
                $est->tiene_impl         = $tieneImpl;
                $est->tiene_asist        = $tieneAsist;
                $est->tiene_monitoreo    = $tieneMonitoreo;
                $est->modulos_impl       = $modulosPorCodigo[$est->codigo] ?? [];
                $est->total_impl         = count($est->modulos_impl);
                $est->total_asistencias  = (int)($totalAsistenciaPorId[$est->id] ?? 0);
                $est->total_monitoreos   = (int)($totalMonitoreoPorId[$est->id] ?? 0);
                return $est;
            });

        // ── 5. Contadores ESTRICTAMENTE EXCLUYENTES por etapa ──────────────────────────────
        $contadores = [
            'total'     => $establecimientosMap->count(),
            'etapa0'    => $establecimientosMap->where('etapa', 0)->count(),
            'etapa1'    => $establecimientosMap->where('etapa', 1)->count(),
            'etapa2'    => $establecimientosMap->where('etapa', 2)->count(),
            'etapa3'    => $establecimientosMap->where('etapa', 3)->count(),
            'etapa4'    => $establecimientosMap->where('etapa', 4)->count(),
        ];

        // ── 6. Filtros ────────────────────────────────────────────────────────
        $provincias = Establecimiento::whereNotNull('latitud')->whereNotNull('longitud')
            ->whereNotNull('provincia')->distinct()->orderBy('provincia')->pluck('provincia');
        $redes      = Establecimiento::whereNotNull('red')->distinct()->orderBy('red')->pluck('red');
        $microredes = Establecimiento::whereNotNull('microred')->distinct()->orderBy('microred')->pluck('microred');
        $categorias = Establecimiento::whereNotNull('categoria')->distinct()->orderBy('categoria')->pluck('categoria');
        $distritos  = Establecimiento::whereNotNull('distrito')->distinct()->orderBy('distrito')->pluck('distrito');

        return view('usuario.dashboard.mapa_progresion', compact(
            'establecimientosMap',
            'contadores',
            'provincias',
            'redes',
            'microredes',
            'categorias',
            'distritos',
            'aniosDisponibles',
            'anioFiltro'
        ));
    }

    /**
     * MAPA DE PROGRAMACIÓN POR SECTORES
     * Carga la programación desde BD, cruza con datos de progresión y pasa a la vista.
     */
    public function mapaProgramacion()
    {
        $modulos = \App\Helpers\ImplementacionHelper::getModulos();

        // ── Códigos con implementación ───────────────────────────────
        $codigosConImplementacion = collect();
        $modulosPorCodigo = [];
        foreach ($modulos as $config) {
            $modelo = $config['modelo'];
            if (!class_exists($modelo)) continue;
            $actas = $modelo::select('codigo_establecimiento')->get();
            foreach ($actas as $acta) {
                $cod = $acta->codigo_establecimiento;
                if (!$cod) continue;
                $codigosConImplementacion->push($cod);
                if (!isset($modulosPorCodigo[$cod])) $modulosPorCodigo[$cod] = [];
                if (!in_array($config['nombre'], $modulosPorCodigo[$cod])) {
                    $modulosPorCodigo[$cod][] = $config['nombre'];
                }
            }
        }
        $codigosConImplementacion = $codigosConImplementacion->unique()->values();

        // ── IDs con asistencia ───────────────────────────────────────
        $idsConAsistencia   = Acta::where('tipo', 'asistencia')->distinct()->pluck('establecimiento_id')->toArray();
        $totalAsistenciaPorId = Acta::where('tipo', 'asistencia')
            ->select('establecimiento_id', DB::raw('count(*) as total'))
            ->groupBy('establecimiento_id')->pluck('total', 'establecimiento_id');

        // ── IDs con monitoreo ────────────────────────────────────────
        $idsConMonitoreo    = CabeceraMonitoreo::distinct()->pluck('establecimiento_id')->toArray();
        $totalMonitoreoPorId = CabeceraMonitoreo::select('establecimiento_id', DB::raw('count(*) as total'))
            ->groupBy('establecimiento_id')->pluck('total', 'establecimiento_id');

        // ── Índice de establecimientos por ID ────────────────────────
        $establecimientosIdx = Establecimiento::all(['id','codigo','nombre','distrito','provincia',
            'latitud','longitud','categoria','red','microred'])
            ->keyBy('id');

        // ── Cargar programación ──────────────────────────────────────
        $programacion = ProgramacionSector::orderBy('sector')->orderBy('cuadril')->get();

        $programacion = $programacion->map(function ($p) use (
            $establecimientosIdx, $codigosConImplementacion, $modulosPorCodigo,
            $idsConAsistencia, $totalAsistenciaPorId, $idsConMonitoreo, $totalMonitoreoPorId
        ) {
            $est = $p->establecimiento_id ? ($establecimientosIdx[$p->establecimiento_id] ?? null) : null;

            $lat = null; $lon = null;
            $tieneImpl = false; $tieneAsist = false; $tieneMonitoreo = false;
            $etapa = 0; $modulos = []; $totalImpl = 0; $totalAsist = 0; $totalMon = 0;
            $nombreDisplay = $p->nombre_pdf;
            $distrito = null; $red = null; $microred = null; $categoria = null; $codigo = null;

            if ($est) {
                $lat = (float) $est->latitud;
                $lon = (float) $est->longitud;
                if (abs($lat) > 180) $lat /= 100000000;
                if (abs($lon) > 180) $lon /= 100000000;

                $nombreDisplay = $est->nombre;
                $distrito      = $est->distrito;
                $red           = $est->red;
                $microred      = $est->microred;
                $categoria     = $est->categoria;
                $codigo        = $est->codigo;

                $tieneImpl      = $codigosConImplementacion->contains($est->codigo);
                $tieneAsist     = in_array($est->id, $idsConAsistencia);
                $tieneMonitoreo = in_array($est->id, $idsConMonitoreo);

                if ($tieneImpl && $tieneAsist && $tieneMonitoreo) $etapa = 4;
                elseif ($tieneMonitoreo) $etapa = 3;
                elseif ($tieneAsist)     $etapa = 2;
                elseif ($tieneImpl)      $etapa = 1;

                $modulos    = $modulosPorCodigo[$est->codigo] ?? [];
                $totalImpl  = count($modulos);
                $totalAsist = (int)($totalAsistenciaPorId[$est->id] ?? 0);
                $totalMon   = (int)($totalMonitoreoPorId[$est->id] ?? 0);
            }

            return [
                'id'              => $p->id,
                'nombre_pdf'      => $p->nombre_pdf,
                'nombre'          => $nombreDisplay,
                'provincia'       => $est ? $est->provincia : $p->provincia,  // Provincia real de la BD
                'provincia_pdf'   => $p->provincia,                            // Provincia del PDF (referencia)
                'sector'          => $p->sector,
                'cuadril'         => $p->cuadril,
                'comienzo'        => $p->comienzo ? $p->comienzo->format('d/m/Y') : null,
                'fin'             => $p->fin      ? $p->fin->format('d/m/Y')      : null,
                'comienzo_iso'    => $p->comienzo ? $p->comienzo->format('Y-m-d') : null,
                'fin_iso'         => $p->fin      ? $p->fin->format('Y-m-d')      : null,
                'dias'            => $p->dias,
                'lat'             => $lat && !is_nan($lat) ? $lat : null,
                'lon'             => $lon && !is_nan($lon) ? $lon : null,
                'tiene_est'       => !!$est,
                'establecimiento_id' => $p->establecimiento_id,
                'distrito'        => $distrito,
                'red'             => $red,
                'microred'        => $microred,
                'categoria'       => $categoria,
                'codigo'          => $codigo,
                'etapa'           => $etapa,
                'tiene_impl'      => $tieneImpl,
                'tiene_asist'     => $tieneAsist,
                'tiene_monitoreo' => $tieneMonitoreo,
                'modulos_impl'    => $modulos,
                'total_impl'      => $totalImpl,
                'total_asistencias' => $totalAsist,
                'total_monitoreos'  => $totalMon,
            ];
        })->values();

        return view('usuario.dashboard.mapa_sectores', compact('programacion'));
    }

    /**
     * AJAX: Actualizar el sector de un registro de programación
     */
    public function actualizarSector(Request $request, $id)
    {
        $request->validate([
            'sector'  => 'required|integer|min:1|max:30',
            'cuadril' => 'nullable|string|max:15',
        ]);

        $prog = ProgramacionSector::findOrFail($id);
        $prog->sector  = $request->sector;
        $prog->cuadril = $request->cuadril ?? $prog->cuadril;
        $prog->save();

        return response()->json(['success' => true, 'sector' => $prog->sector, 'cuadril' => $prog->cuadril]);
    }

    /**
     * Dashboard de Equipos de Cómputo
     */
    public function dashboardEquipos()
    {
        // Obtener años disponibles
        $aniosDisponibles = CabeceraMonitoreo::selectRaw('DISTINCT YEAR(fecha) as anio')
            ->orderBy('anio', 'desc')
            ->pluck('anio');

        // Obtener tipos de establecimiento
        $tipos = ['ESPECIALIZADO', 'NO ESPECIALIZADO'];

        // Obtener provincias (solo las que tienen equipos)
        $establecimientosConEquipos = \App\Models\EquipoComputo::select('cabecera_monitoreo_id')
            ->distinct()
            ->pluck('cabecera_monitoreo_id');

        $establecimientosIds = CabeceraMonitoreo::whereIn('id', $establecimientosConEquipos)
            ->pluck('establecimiento_id')
            ->unique();

        $provincias = Establecimiento::select('provincia')
            ->distinct()
            ->whereNotNull('provincia')
            ->whereIn('id', $establecimientosIds)
            ->orderBy('provincia')
            ->pluck('provincia');

        // Obtener establecimientos (solo los que tienen equipos)
        $establecimientos = Establecimiento::select('id', 'nombre', 'codigo')
            ->whereIn('id', $establecimientosIds)
            ->orderBy('nombre')
            ->get();

        // Obtener TODOS los módulos posibles (estándar y especializados)
        // Esto permite filtrar por módulos incluso si no tienen equipos registrados aún
        $todosLosModulos = \App\Helpers\ModuloHelper::getTodosLosModulos();

        $modulos = collect($todosLosModulos)->map(function ($nombre, $valor) {
            return [
                'valor' => $valor,
                'nombre' => $nombre
            ];
        })->values()->toArray();

        // Obtener descripciones (solo las que tienen equipos registrados)
        $descripciones = \App\Models\EquipoComputo::select('descripcion')
            ->distinct()
            ->whereNotNull('descripcion')
            ->whereHas('cabecera')
            ->orderBy('descripcion')
            ->pluck('descripcion');

        // Estadísticas iniciales (mes y año actual)
        $mesActual = now()->month;
        $anioActual = now()->year;

        $totalEquipos = \App\Models\EquipoComputo::whereHas('cabecera', function ($q) use ($mesActual, $anioActual) {
            $q->whereMonth('fecha', $mesActual)
                ->whereYear('fecha', $anioActual);
        })->count();

        return view('usuario.dashboard.dashboard_equipos', compact(
            'aniosDisponibles',
            'tipos',
            'provincias',
            'establecimientos',
            'modulos',
            'descripciones',
            'totalEquipos'
        ));
    }



    /**
     * LISTADO DE ASISTENCIAS TÉCNICAS (Vista Global Histórica)
     */
    public function actasIndex(Request $request)
    {
        $query = Acta::query();

        // Filtros dinámicos sobre todas las actas del sistema
        if ($request->filled('provincia')) {
            $query->whereHas('establecimiento', function ($q) use ($request) {
                $q->where('provincia', $request->provincia);
            });
        }
        if ($request->filled('firmado')) {
            $query->where('firmado', $request->firmado);
        }
        if ($request->filled('fecha_inicio') && $request->filled('fecha_fin')) {
            $query->whereBetween('fecha', [$request->fecha_inicio, $request->fecha_fin]);
        }

        $actas = $query->orderBy('fecha', 'desc')->paginate(10);

        $provincias = Establecimiento::distinct()->pluck('provincia');

        // Contadores globales históricos
        $countFirmadas = Acta::where('firmado', 1)->count();
        $countPendientes = Acta::where('firmado', 0)->count();

        return view('usuario.asistencia.index', compact('actas', 'provincias', 'countFirmadas', 'countPendientes'));
    }

    /**
     * SUBIR O REEMPLAZAR PDF FIRMADO
     */
    public function subirPDF(Request $request, $id)
    {
        $request->validate([
            'pdf_firmado' => 'required|mimes:pdf|max:5120',
        ]);

        $acta = Acta::findOrFail($id);

        if ($request->hasFile('pdf_firmado')) {
            // Eliminar archivo físico anterior si existe
            if ($acta->firmado_pdf && Storage::disk('public')->exists($acta->firmado_pdf)) {
                Storage::disk('public')->delete($acta->firmado_pdf);
            }

            // Guardar nuevo archivo
            $path = $request->file('pdf_firmado')->store('actas_firmadas', 'public');

            $acta->update([
                'firmado_pdf' => $path,
                'firmado' => 1
            ]);
        }

        return back()->with('success', 'Archivo PDF cargado correctamente.');
    }

    /**
     * LISTADO DE MONITOREO (Vista Global Histórica)
     */
    public function monitoreoIndex(Request $request)
    {
        // Filtrar todas las actas que contengan "Monitoreo" en el sistema
        $query = Acta::where('tema', 'like', '%Monitoreo%');

        $monitoreos = $query->orderBy('fecha', 'desc')->paginate(10);
        $countCompletados = (clone $query)->where('firmado', 1)->count();

        return view('usuario.monitoreo.index', compact('monitoreos', 'countCompletados'));
    }

    /**
     * GESTIÓN DE PERFIL
     */
    public function perfil()
    {
        $user = Auth::user();
        return view('usuario.perfil.perfil', compact('user'));
    }

    /**
     * ACTUALIZAR PERFIL
     */
    public function perfilUpdate(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'apellido_paterno' => 'required|string|max:255',
            'apellido_materno' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => ['nullable', 'confirmed', Password::defaults()],
        ]);

        $user->fill($request->only('name', 'apellido_paterno', 'apellido_materno', 'email'));

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return back()->with('success', 'Tu perfil ha sido actualizado correctamente.');
    }

    /**
     * AJAX: Obtener estadísticas de equipos según filtros
     */
    public function getEquiposStats(Request $request)
    {
        try {
            \Illuminate\Support\Facades\Log::info('getEquiposStats called', $request->all());

            $mes = $request->input('mes');
            $anio = $request->input('anio');
            $tipo = $request->input('tipo');
            $provincia = $request->input('provincia');
            $distrito = $request->input('distrito');
            $establecimientoId = $request->input('establecimiento_id');
            $modulo = $request->input('modulo');
            $descripcion = $request->input('descripcion');

            // Códigos y nombres de establecimientos especializados
            $codigosCSMC = ['25933', '28653', '27197', '34021', '25977', '33478', '27199', '30478'];
            $nombresCSMC = [
                'CSMC TUPAC AMARU',
                'CSMC COLOR ESPERANZA',
                'CSMC DECÍDETE A SER FELIZ',
                'CSMC SANTISIMA VIRGEN DE YAUCA',
                'CSMC VITALIZA',
                'CSMC CRISTO MORENO DE LUREN',
                'CSMC NUEVO HORIZONTE',
                'CSMC MENTE SANA'
            ];

            // Construir query base
            $query = \App\Models\EquipoComputo::query();

            // Filtros de fecha
            if ($mes && $anio) {
                $query->whereHas('cabecera', function ($q) use ($mes, $anio) {
                    $q->whereMonth('fecha', $mes)->whereYear('fecha', $anio);
                });
                $periodoTexto = $this->getNombreMes($mes) . ' ' . $anio;
            } elseif ($mes) {
                $query->whereHas('cabecera', function ($q) use ($mes) {
                    $q->whereMonth('fecha', $mes);
                });
                $periodoTexto = $this->getNombreMes($mes) . ' (Todos los años)';
            } elseif ($anio) {
                $query->whereHas('cabecera', function ($q) use ($anio) {
                    $q->whereYear('fecha', $anio);
                });
                $periodoTexto = 'Año ' . $anio;
            } else {
                $periodoTexto = 'Todos los períodos';
            }

            // Filtro por Tipo de Establecimiento
            if ($tipo) {
                if ($tipo === 'ESPECIALIZADO') {
                    $query->whereHas('cabecera.establecimiento', function ($q) use ($codigosCSMC, $nombresCSMC) {
                        $q->where(function ($subQ) use ($codigosCSMC, $nombresCSMC) {
                            $subQ->whereIn('codigo', $codigosCSMC)
                                ->orWhereIn(DB::raw('UPPER(TRIM(nombre))'), $nombresCSMC);
                        });
                    });
                } elseif ($tipo === 'NO ESPECIALIZADO') {
                    $query->whereHas('cabecera.establecimiento', function ($q) use ($codigosCSMC, $nombresCSMC) {
                        $q->whereNotIn('codigo', $codigosCSMC)
                            ->whereNotIn(DB::raw('UPPER(TRIM(nombre))'), $nombresCSMC);
                    });
                }
            }

            // Filtro por Provincia
            if ($provincia) {
                $query->whereHas('cabecera.establecimiento', function ($q) use ($provincia) {
                    $q->where('provincia', $provincia);
                });
            }

            // Filtro por Distrito
            if ($distrito) {
                $query->whereHas('cabecera.establecimiento', function ($q) use ($distrito) {
                    $q->where('distrito', $distrito);
                });
            }

            // Filtro por Establecimiento
            if ($establecimientoId) {
                $query->whereHas('cabecera', function ($q) use ($establecimientoId) {
                    $q->where('establecimiento_id', $establecimientoId);
                });
            }

            // Filtro por Módulo (puede ser múltiple)
            $modulos = $request->input('modulos', []);
            if (!empty($modulos) && is_array($modulos)) {
                $query->whereIn('modulo', $modulos);
            }

            // Filtro por Descripción
            if ($descripcion) {
                $query->where('descripcion', $descripcion);
            }

            // Total de equipos
            $totalEquipos = (clone $query)->count();
            \Illuminate\Support\Facades\Log::info('Total equipos encontrados: ' . $totalEquipos);

            // Equipos por Estado
            $equiposPorEstado = (clone $query)
                ->select('estado', \DB::raw('count(*) as total'))
                ->groupBy('estado')
                ->pluck('total', 'estado')
                ->toArray();

            // Equipos por Tipo de Establecimiento
            $queryEspecializados = clone $query;
            $equiposEspecializados = $queryEspecializados->whereHas('cabecera.establecimiento', function ($q) use ($codigosCSMC, $nombresCSMC) {
                $q->where(function ($subQ) use ($codigosCSMC, $nombresCSMC) {
                    $subQ->whereIn('codigo', $codigosCSMC)
                        ->orWhereIn(DB::raw('UPPER(TRIM(nombre))'), $nombresCSMC);
                });
            })->count();

            $queryNoEspecializados = clone $query;
            $equiposNoEspecializados = $queryNoEspecializados->whereHas('cabecera.establecimiento', function ($q) use ($codigosCSMC, $nombresCSMC) {
                $q->whereNotIn('codigo', $codigosCSMC)
                    ->whereNotIn(DB::raw('UPPER(TRIM(nombre))'), $nombresCSMC);
            })->count();

            $equiposPorTipo = [
                'ESPECIALIZADO' => $equiposEspecializados,
                'NO ESPECIALIZADO' => $equiposNoEspecializados
            ];

            // Equipos por Módulo
            $equiposPorModulo = (clone $query)
                ->select('modulo', DB::raw('count(*) as total'))
                ->whereNotNull('modulo')
                ->groupBy('modulo')
                ->orderBy('total', 'desc')
                ->get()
                ->mapWithKeys(function ($item) {
                    $nombreAmigable = \App\Helpers\ModuloHelper::getNombreAmigable($item->modulo);
                    return [($nombreAmigable ?? $item->modulo) => $item->total];
                })
                ->toArray();

            // Todas las Descripciones (sin límite Top 10)
            $topDescripciones = (clone $query)
                ->select('descripcion', DB::raw('count(*) as total'))
                ->whereNotNull('descripcion')
                ->groupBy('descripcion')
                ->orderBy('total', 'desc')
                ->get()
                ->mapWithKeys(function ($item) {
                    return [$item->descripcion => $item->total];
                })
                ->toArray();

            // Equipos por Establecimiento - Query robusto
            $queryEstablecimientos = \DB::table('mon_equipos_computo')
                ->join('mon_cabecera_monitoreo', 'mon_equipos_computo.cabecera_monitoreo_id', '=', 'mon_cabecera_monitoreo.id')
                ->join('establecimientos', 'mon_cabecera_monitoreo.establecimiento_id', '=', 'establecimientos.id');

            // Aplicar filtros de fecha
            if ($mes && $anio) {
                $queryEstablecimientos->whereMonth('mon_cabecera_monitoreo.fecha', $mes)
                    ->whereYear('mon_cabecera_monitoreo.fecha', $anio);
            } elseif ($mes) {
                $queryEstablecimientos->whereMonth('mon_cabecera_monitoreo.fecha', $mes);
            } elseif ($anio) {
                $queryEstablecimientos->whereYear('mon_cabecera_monitoreo.fecha', $anio);
            }

            // Aplicar filtro de tipo
            if ($tipo) {
                if ($tipo === 'ESPECIALIZADO') {
                    $queryEstablecimientos->whereIn('establecimientos.codigo', $codigosCSMC);
                } else {
                    $queryEstablecimientos->whereNotIn('establecimientos.codigo', $codigosCSMC);
                }
            }

            // Aplicar filtro de provincia
            if ($provincia) {
                $queryEstablecimientos->where('establecimientos.provincia', $provincia);
            }

            // Aplicar filtro de establecimiento
            if ($establecimientoId) {
                $queryEstablecimientos->where('establecimientos.id', $establecimientoId);
            }

            // Aplicar filtro de módulo
            if ($modulo) {
                $queryEstablecimientos->where('mon_equipos_computo.modulo', $modulo);
            }

            // Aplicar filtro de descripción
            if ($descripcion) {
                $queryEstablecimientos->where('mon_equipos_computo.descripcion', $descripcion);
            }

            // Group by ID y Nombre para SQL strict mode y evitar errores de ambigüedad
            $equiposPorEstablecimiento = $queryEstablecimientos
                ->select('establecimientos.nombre', \DB::raw('count(mon_equipos_computo.id) as total'))
                ->groupBy('establecimientos.id', 'establecimientos.nombre')
                ->orderByDesc('total')
                ->pluck('total', 'nombre')
                ->toArray();

            // --- NUEVO: Estadísticas de Conectividad para Gráficos ---
            $equiposPorConectividad = [];
            $equiposPorFuenteWifi = [];
            $equiposPorProveedor = [];

            // Obtenemos todos los equipos filtrados con sus cabeceras y detalles
            $equiposParaStats = (clone $query)->with(['cabecera.detalles'])->get();

            foreach ($equiposParaStats as $e) {
                $con = \App\Helpers\ModuloHelper::getConectividadActa($e->cabecera);

                // Conectividad
                $tipo = $con['tipo'];
                $equiposPorConectividad[$tipo] = ($equiposPorConectividad[$tipo] ?? 0) + ($e->cantidad ?? 1);

                // Fuente WiFi (solo si tiene algo)
                $fuente = $con['fuente'];
                if ($fuente && $fuente !== '---') {
                    $equiposPorFuenteWifi[$fuente] = ($equiposPorFuenteWifi[$fuente] ?? 0) + ($e->cantidad ?? 1);
                }

                // Proveedor (solo si tiene algo)
                $operador = $con['operador'];
                if ($operador && $operador !== '---') {
                    $equiposPorProveedor[$operador] = ($equiposPorProveedor[$operador] ?? 0) + ($e->cantidad ?? 1);
                }
            }

            // Retornar datos
            $response = [
                'totalEquipos' => $totalEquipos,
                'periodoTexto' => $periodoTexto,
                'equiposPorEstado' => $equiposPorEstado,
                'equiposPorTipo' => $equiposPorTipo,
                'equiposPorModulo' => $equiposPorModulo,
                'topDescripciones' => $topDescripciones,
                'equiposPorEstablecimiento' => $equiposPorEstablecimiento,
                'equiposPorConectividad' => $equiposPorConectividad,
                'equiposPorFuenteWifi' => $equiposPorFuenteWifi,
                'equiposPorProveedor' => $equiposPorProveedor
            ];

            \Illuminate\Support\Facades\Log::info('DEBUG DATA:', [
                'totalEquipos' => $totalEquipos,
                'count_estado' => count($equiposPorEstado),
                'count_tipo' => count($equiposPorTipo),
                'count_modulo' => count($equiposPorModulo),
                'count_desc' => count($topDescripciones),
                'count_estab' => count($equiposPorEstablecimiento),
                'sample_estado' => $equiposPorEstado,
            ]);

            return response()->json($response);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error in getEquiposStats: ' . $e->getMessage());
            \Illuminate\Support\Facades\Log::error($e->getTraceAsString());

            return response()->json([
                'error' => $e->getMessage(),
                'totalEquipos' => 0,
                'periodoTexto' => 'Error',
                'equiposPorEstado' => [],
                'equiposPorTipo' => ['ESPECIALIZADO' => 0, 'NO ESPECIALIZADO' => 0],
                'equiposPorModulo' => [],
                'topDescripciones' => []
            ], 500);
        }
    }

    /**
     * AJAX: Obtener opciones de filtros según selección actual
     */
    public function getFilterOptions(Request $request)
    {
        try {
            $mes = $request->input('mes');
            $anio = $request->input('anio');
            $tipo = $request->input('tipo');
            $provincia = $request->input('provincia');
            $distrito = $request->input('distrito');
            $establecimientoId = $request->input('establecimiento_id');
            $modulos = $request->input('modulos', []);

            // Códigos y nombres de establecimientos especializados
            $codigosCSMC = ['25933', '28653', '27197', '34021', '25977', '33478', '27199', '30478'];
            $nombresCSMC = [
                'CSMC TUPAC AMARU',
                'CSMC COLOR ESPERANZA',
                'CSMC DECÍDETE A SER FELIZ',
                'CSMC SANTISIMA VIRGEN DE YAUCA',
                'CSMC VITALIZA',
                'CSMC CRISTO MORENO DE LUREN',
                'CSMC NUEVO HORIZONTE',
                'CSMC MENTE SANA'
            ];

            // Construir query base
            $query = \App\Models\EquipoComputo::query();

            // Aplicar filtros de fecha
            if ($mes && $anio) {
                $query->whereHas('cabecera', function ($q) use ($mes, $anio) {
                    $q->whereMonth('fecha', $mes)->whereYear('fecha', $anio);
                });
            } elseif ($mes) {
                $query->whereHas('cabecera', function ($q) use ($mes) {
                    $q->whereMonth('fecha', $mes);
                });
            } elseif ($anio) {
                $query->whereHas('cabecera', function ($q) use ($anio) {
                    $q->whereYear('fecha', $anio);
                });
            }

            // Aplicar filtro por Tipo de Establecimiento
            if ($tipo) {
                if ($tipo === 'ESPECIALIZADO') {
                    $query->whereHas('cabecera.establecimiento', function ($q) use ($codigosCSMC, $nombresCSMC) {
                        $q->where(function ($subQ) use ($codigosCSMC, $nombresCSMC) {
                            $subQ->whereIn('codigo', $codigosCSMC)
                                ->orWhereIn(DB::raw('UPPER(TRIM(nombre))'), $nombresCSMC);
                        });
                    });
                } elseif ($tipo === 'NO ESPECIALIZADO') {
                    $query->whereHas('cabecera.establecimiento', function ($q) use ($codigosCSMC, $nombresCSMC) {
                        $q->whereNotIn('codigo', $codigosCSMC)
                            ->whereNotIn(DB::raw('UPPER(TRIM(nombre))'), $nombresCSMC);
                    });
                }
            }

            // Aplicar filtro por Provincia
            if ($provincia) {
                $query->whereHas('cabecera.establecimiento', function ($q) use ($provincia) {
                    $q->where('provincia', $provincia);
                });
            }

            // Aplicar filtro por Distrito
            if ($distrito) {
                $query->whereHas('cabecera.establecimiento', function ($q) use ($distrito) {
                    $q->where('distrito', $distrito);
                });
            }

            // Aplicar filtro por Establecimiento
            if ($establecimientoId) {
                $query->whereHas('cabecera', function ($q) use ($establecimientoId) {
                    $q->where('establecimiento_id', $establecimientoId);
                });
            }

            // Aplicar filtro por Módulos
            if (!empty($modulos) && is_array($modulos)) {
                $query->whereIn('modulo', $modulos);
            }

            // Obtener IDs de cabeceras y establecimientos con equipos filtrados
            $cabecerasIds = (clone $query)->pluck('cabecera_monitoreo_id')->unique();
            $establecimientosIds = CabeceraMonitoreo::whereIn('id', $cabecerasIds)
                ->pluck('establecimiento_id')
                ->unique();

            // Obtener provincias disponibles
            $provincias = Establecimiento::select('provincia')
                ->distinct()
                ->whereNotNull('provincia')
                ->whereIn('id', $establecimientosIds)
                ->orderBy('provincia')
                ->pluck('provincia')
                ->values();

            // Obtener distritos disponibles (filtrados por provincia si aplica)
            $distritosQuery = Establecimiento::select('distrito')
                ->distinct()
                ->whereNotNull('distrito')
                ->whereIn('id', $establecimientosIds)
                ->orderBy('distrito');
            if ($provincia) {
                $distritosQuery->where('provincia', $provincia);
            }
            $distritos = $distritosQuery->pluck('distrito')->values();

            // Obtener establecimientos disponibles
            $establecimientos = Establecimiento::select('id', 'nombre', 'codigo')
                ->whereIn('id', $establecimientosIds)
                ->orderBy('nombre')
                ->get()
                ->map(function ($est) {
                    return [
                        'id' => $est->id,
                        'nombre' => $est->nombre,
                        'codigo' => $est->codigo
                    ];
                })
                ->values();

            // Obtener módulos disponibles (de equipos filtrados)
            $modulosDisponibles = (clone $query)
                ->select('modulo')
                ->distinct()
                ->whereNotNull('modulo')
                ->orderBy('modulo')
                ->pluck('modulo')
                ->map(function ($modulo) {
                    return [
                        'valor' => $modulo,
                        'nombre' => \App\Helpers\ModuloHelper::getNombreAmigable($modulo) ?? $modulo
                    ];
                })
                ->values();

            // Obtener descripciones disponibles
            $descripciones = (clone $query)
                ->select('descripcion')
                ->distinct()
                ->whereNotNull('descripcion')
                ->orderBy('descripcion')
                ->pluck('descripcion')
                ->values();

            return response()->json([
                'success' => true,
                'provincias' => $provincias,
                'distritos' => $distritos,
                'establecimientos' => $establecimientos,
                'modulos' => $modulosDisponibles,
                'descripciones' => $descripciones
            ]);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error in getFilterOptions: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function getNombreMes($numeroMes)
    {
        $meses = [
            1 => 'Enero',
            2 => 'Febrero',
            3 => 'Marzo',
            4 => 'Abril',
            5 => 'Mayo',
            6 => 'Junio',
            7 => 'Julio',
            8 => 'Agosto',
            9 => 'Septiembre',
            10 => 'Octubre',
            11 => 'Noviembre',
            12 => 'Diciembre'
        ];
        return $meses[$numeroMes] ?? '';
    }

    /* ══════════════════════════════════════════════════════
       MAPA PROGRAMACIÓN PROPUESTA (CLONADO / BORRADOR)
    ══════════════════════════════════════════════════════ */

    public function mapaProgramacionPropuesta()
    {
        // 1. Verificar si la tabla de propuesta esta vacia
        $conteo = ProgramacionSectorPropuesta::count();

        // 2. Si esta vacia, clonar todo de la tabla oficial para tener una base
        if ($conteo === 0) {
            $oficial = ProgramacionSector::all();
            foreach ($oficial as $item) {
                ProgramacionSectorPropuesta::create([
                    'establecimiento_id' => $item->establecimiento_id,
                    'nombre_pdf'         => $item->nombre_pdf,
                    'provincia'          => $item->provincia,
                    'sector'             => $item->sector,
                    'cuadril'            => $item->cuadril,
                    'comienzo'           => $item->comienzo,
                    'fin'                => $item->fin,
                    'dias'               => $item->dias,
                ]);
            }
        }

        $programacion = $this->getProgramacionPropuestaData();
        return view('usuario.dashboard.mapa_sectores_propuesta', compact('programacion'));
    }

    private function getProgramacionPropuestaData()
    {
        $modulosConfig = \App\Helpers\ImplementacionHelper::getModulos();
        $codigosConImplementacion = collect();
        $modulosPorCodigo = [];
        
        foreach ($modulosConfig as $config) {
            $modelo = $config['modelo'];
            if (class_exists($modelo)) {
                $items = $modelo::select('codigo_establecimiento')->get();
                foreach ($items as $item) {
                    $cod = $item->codigo_establecimiento;
                    if ($cod) {
                        $codigosConImplementacion->push($cod);
                        if (!isset($modulosPorCodigo[$cod])) $modulosPorCodigo[$cod] = [];
                        if (!in_array($config['nombre'], $modulosPorCodigo[$cod])) {
                            $modulosPorCodigo[$cod][] = $config['nombre'];
                        }
                    }
                }
            }
        }
        $codigosConImplementacion = $codigosConImplementacion->unique()->values();

        $idsConAsistencia = Acta::where('tipo', 'asistencia')->distinct()->pluck('establecimiento_id')->toArray();
        $idsConMonitoreo  = CabeceraMonitoreo::distinct()->pluck('establecimiento_id')->toArray();

        $establecimientosIdx = Establecimiento::all(['id','codigo','nombre','distrito','provincia','latitud','longitud','categoria','red','microred'])->keyBy('id');

        return ProgramacionSectorPropuesta::orderBy('sector')->orderBy('cuadril')->get()
            ->map(function ($p) use ($establecimientosIdx, $codigosConImplementacion, $idsConAsistencia, $idsConMonitoreo) {
                $est = $p->establecimiento_id ? ($establecimientosIdx[$p->establecimiento_id] ?? null) : null;
                $lat = null; $lon = null; $etapa = 0; $tiene_est = false;
                $provincia = $p->provincia;
                $nombre = $p->nombre_pdf;
                $distrito = null;
                $categoria = null;

                if ($est) {
                    $tiene_est = true;
                    $lat = (float) $est->latitud;
                    $lon = (float) $est->longitud;
                    if (abs($lat) > 180) $lat /= 100000000;
                    if (abs($lon) > 180) $lon /= 100000000;

                    $nombre = $est->nombre;
                    $provincia = $est->provincia ?? $p->provincia;
                    $distrito = $est->distrito;
                    $categoria = $est->categoria;

                    $tieneImpl = $codigosConImplementacion->contains($est->codigo);
                    $tieneAsist = in_array($est->id, $idsConAsistencia);
                    $tieneMon = in_array($est->id, $idsConMonitoreo);

                    if ($tieneMon) $etapa = 3;
                    elseif ($tieneAsist) $etapa = 2;
                    elseif ($tieneImpl) $etapa = 1;

                    if ($tieneImpl && $tieneAsist && $tieneMon) $etapa = 4;
                }

                return (object) [
                    'id'           => $p->id,
                    'nombre'       => $nombre,
                    'provincia'    => $provincia,
                    'distrito'     => $distrito,
                    'categoria'    => $categoria,
                    'sector'       => $p->sector,
                    'cuadril'      => $p->cuadril,
                    'comienzo'     => $p->comienzo ? $p->comienzo->format('d/m/Y') : null,
                    'fin'          => $p->fin ? $p->fin->format('d/m/Y') : null,
                    'comienzo_iso' => $p->comienzo ? $p->comienzo->format('Y-m-d') : null,
                    'fin_iso'      => $p->fin ? $p->fin->format('Y-m-d') : null,
                    'dias'         => $p->dias,
                    'lat'          => $lat,
                    'lon'          => $lon,
                    'etapa'        => $etapa,
                    'tiene_est'    => $tiene_est,
                ];
            });
    }

    public function actualizarSectorPropuesta(Request $request, $id)
    {
        $prog = ProgramacionSectorPropuesta::findOrFail($id);
        $prog->sector  = $request->sector;
        $prog->cuadril = strtoupper($request->cuadril);
        $prog->save();

        return response()->json([
            'success' => true,
            'sector'  => $prog->sector,
            'cuadril' => $prog->cuadril
        ]);
    }
}