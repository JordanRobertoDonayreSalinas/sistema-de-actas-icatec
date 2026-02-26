<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Acta;
use App\Models\Establecimiento;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use Carbon\Carbon;

class UsuarioController extends Controller
{
    /**
     * DASHBOARD DEL USUARIO
     * Muestra estadísticas GLOBALES e HISTÓRICAS ordenadas por fecha reciente.
     * Vista: resources/views/usuario/dashboard/dashboard.blade.php
     */
    public function index()
    {
        // 1. IDs de establecimientos con actas de monitoreo (tabla mon_cabecera_monitoreo)
        $idsConMonitoreo = \App\Models\CabeceraMonitoreo::distinct()
            ->pluck('establecimiento_id')
            ->toArray();

        // 2. Establecimientos con coordenadas para el mapa (con flag de monitoreo y provincia)
        $establecimientosMap = Establecimiento::whereNotNull('latitud')
            ->whereNotNull('longitud')
            ->get(['id', 'nombre', 'distrito', 'provincia', 'categoria', 'latitud', 'longitud'])
            ->map(function ($est) use ($idsConMonitoreo) {
                $est->has_monitoreo = in_array($est->id, $idsConMonitoreo);
                return $est;
            });

        // 3. Lista de provincias únicas para el filtro
        $provincias = Establecimiento::whereNotNull('latitud')
            ->whereNotNull('longitud')
            ->whereNotNull('provincia')
            ->distinct()
            ->orderBy('provincia')
            ->pluck('provincia');

        // 4. Categorías y Distritos para filtros
        $categorias = Establecimiento::whereNotNull('categoria')->distinct()->orderBy('categoria')->pluck('categoria');
        $distritos = Establecimiento::whereNotNull('distrito')->distinct()->orderBy('distrito')->pluck('distrito');

        return view('usuario.dashboard.dashboard', compact(
            'establecimientosMap',
            'provincias',
            'categorias',
            'distritos'
        ));
    }

    /**
     * MAPA DE ASISTENCIAS TÉCNICAS
     * Muestra la intensidad de asistencias técnicas por establecimiento.
     */
    public function mapaSoportes()
    {
        // 1. Obtener conteo de asistencias (Soportes) por establecimiento
        // Filtramos por tema 'Asistencia' o similar si es necesario
        $asistenciasCount = Acta::select('establecimiento_id', DB::raw('count(*) as total'))
            ->where('tipo', 'asistencia')
            ->groupBy('establecimiento_id')
            ->pluck('total', 'establecimiento_id');

        // 2. Obtener establecimientos con coordenadas
        $establecimientosMap = Establecimiento::whereNotNull('latitud')
            ->whereNotNull('longitud')
            ->get(['id', 'nombre', 'distrito', 'provincia', 'categoria', 'latitud', 'longitud'])
            ->map(function ($est) use ($asistenciasCount) {
                $est->total_asistencias = $asistenciasCount[$est->id] ?? 0;
                return $est;
            });

        // 3. Lista de provincias para el filtro
        $provincias = Establecimiento::whereNotNull('latitud')
            ->whereNotNull('longitud')
            ->whereNotNull('provincia')
            ->distinct()
            ->orderBy('provincia')
            ->pluck('provincia');

        // 4. Categorías y Distritos para filtros
        $categorias = Establecimiento::whereNotNull('categoria')->distinct()->orderBy('categoria')->pluck('categoria');
        $distritos = Establecimiento::whereNotNull('distrito')->distinct()->orderBy('distrito')->pluck('distrito');

        return view('usuario.dashboard.mapa_soportes', compact(
            'establecimientosMap',
            'provincias',
            'categorias',
            'distritos'
        ));
    }

    /**
     * Dashboard de Equipos de Cómputo
     */
    public function dashboardEquipos()
    {
        // Obtener años disponibles
        $aniosDisponibles = \App\Models\CabeceraMonitoreo::selectRaw('DISTINCT YEAR(fecha) as anio')
            ->orderBy('anio', 'desc')
            ->pluck('anio');

        // Obtener tipos de establecimiento
        $tipos = ['ESPECIALIZADO', 'NO ESPECIALIZADO'];

        // Obtener provincias (solo las que tienen equipos)
        $establecimientosConEquipos = \App\Models\EquipoComputo::select('cabecera_monitoreo_id')
            ->distinct()
            ->pluck('cabecera_monitoreo_id');

        $establecimientosIds = \App\Models\CabeceraMonitoreo::whereIn('id', $establecimientosConEquipos)
            ->pluck('establecimiento_id')
            ->unique();

        $provincias = \App\Models\Establecimiento::select('provincia')
            ->distinct()
            ->whereNotNull('provincia')
            ->whereIn('id', $establecimientosIds)
            ->orderBy('provincia')
            ->pluck('provincia');

        // Obtener establecimientos (solo los que tienen equipos)
        $establecimientos = \App\Models\Establecimiento::select('id', 'nombre', 'codigo')
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

            // Retornar datos
            $response = [
                'totalEquipos' => $totalEquipos,
                'periodoTexto' => $periodoTexto,
                'equiposPorEstado' => $equiposPorEstado,
                'equiposPorTipo' => $equiposPorTipo,
                'equiposPorModulo' => $equiposPorModulo,
                'topDescripciones' => $topDescripciones,
                'equiposPorEstablecimiento' => $equiposPorEstablecimiento
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
            $establecimientosIds = \App\Models\CabeceraMonitoreo::whereIn('id', $cabecerasIds)
                ->pluck('establecimiento_id')
                ->unique();

            // Obtener provincias disponibles
            $provincias = \App\Models\Establecimiento::select('provincia')
                ->distinct()
                ->whereNotNull('provincia')
                ->whereIn('id', $establecimientosIds)
                ->orderBy('provincia')
                ->pluck('provincia')
                ->values();

            // Obtener establecimientos disponibles
            $establecimientos = \App\Models\Establecimiento::select('id', 'nombre', 'codigo')
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

    /**
     * Helper: Obtener nombre del mes en español
     */
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
}