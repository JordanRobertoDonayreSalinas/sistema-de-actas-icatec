<?php

namespace App\Http\Controllers;

use App\Models\EquipoComputo;
use App\Models\CabeceraMonitoreo;
use App\Models\Establecimiento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Exports\EquiposExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;

class ReporteEquiposController extends Controller
{
    /**
     * Muestra la vista principal de reportes de equipos de cómputo.
     */
    public function index(Request $request)
    {
        // Manejar fechas con persistencia en sesión
        if ($request->filled('fecha_inicio') || $request->filled('fecha_fin')) {
            // Si el usuario envía fechas, guardarlas en sesión
            $fechaInicio = $request->input('fecha_inicio');
            $fechaFin = $request->input('fecha_fin');

            session(['equipos_fecha_inicio' => $fechaInicio]);
            session(['equipos_fecha_fin' => $fechaFin]);
        } else {
            // Si no hay fechas en el request, usar las de sesión o valores por defecto
            $fechaInicio = session('equipos_fecha_inicio', now()->startOfYear()->format('Y-m-d'));
            $fechaFin = session('equipos_fecha_fin', now()->format('Y-m-d'));
        }

        // Obtener listas para filtros
        $provincias = Establecimiento::whereIn('id', function ($subQuery) {
            $subQuery->select('establecimiento_id')
                ->from('mon_cabecera_monitoreo')
                ->whereIn('id', function ($subSubQuery) {
                    $subSubQuery->select('cabecera_monitoreo_id')
                        ->from('mon_equipos_computo')
                        ->distinct();
                });
        })->distinct()->pluck('provincia')->filter()->sort();

        $establecimientos = Establecimiento::whereIn('id', function ($subQuery) {
            $subQuery->select('establecimiento_id')
                ->from('mon_cabecera_monitoreo')
                ->whereIn('id', function ($subSubQuery) {
                    $subSubQuery->select('cabecera_monitoreo_id')
                        ->from('mon_equipos_computo')
                        ->distinct();
                });
        })->orderBy('nombre', 'asc')->get(['id', 'nombre']);

        // Obtener lista de módulos ordenados con nombres amigables
        $modulos = \App\Helpers\ModuloHelper::getTodosLosModulos();

        // Inicializar query
        $query = EquipoComputo::with(['cabecera.establecimiento', 'cabecera.detalles']);

        // Aplicar filtro de fecha (siempre aplicado con valores por defecto)
        $query->whereHas('cabecera', function ($q) use ($fechaInicio) {
            $q->whereDate('fecha', '>=', $fechaInicio);
        });

        $query->whereHas('cabecera', function ($q) use ($fechaFin) {
            $q->whereDate('fecha', '<=', $fechaFin);
        });

        // Filtro por establecimiento
        if ($request->filled('establecimiento_id')) {
            $query->whereHas('cabecera', function ($q) use ($request) {
                $q->where('establecimiento_id', $request->establecimiento_id);
            });
        }

        // Filtro por provincia
        if ($request->filled('provincia')) {
            $query->whereHas('cabecera.establecimiento', function ($q) use ($request) {
                $q->where('provincia', $request->provincia);
            });
        }

        // Filtro por módulo
        if ($request->filled('modulo')) {
            $query->where('modulo', $request->modulo);
        }

        // Filtro por descripción
        if ($request->filled('descripcion')) {
            $query->where('descripcion', $request->descripcion);
        }

        // Filtro por tipo de establecimiento (ESPECIALIZADO/NO ESPECIALIZADO)
        if ($request->filled('tipo')) {
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

            if ($request->tipo === 'ESPECIALIZADO') {
                $query->whereHas('cabecera.establecimiento', function ($q) use ($codigosCSMC, $nombresCSMC) {
                    $q->whereIn('codigo', $codigosCSMC)
                        ->orWhereIn(DB::raw('UPPER(TRIM(nombre))'), $nombresCSMC);
                });
            } elseif ($request->tipo === 'NO ESPECIALIZADO') {
                $query->whereHas('cabecera.establecimiento', function ($q) use ($codigosCSMC, $nombresCSMC) {
                    $q->whereNotIn('codigo', $codigosCSMC)
                        ->whereNotIn(DB::raw('UPPER(TRIM(nombre))'), $nombresCSMC);
                });
            }
        }

        // Ordenar por fecha más reciente
        $equipos = $query->orderBy('created_at', 'desc')->paginate(20);

        // Obtener descripciones únicas
        $descripciones = EquipoComputo::distinct()->pluck('descripcion')->filter()->sort()->values();

        return view('usuario.reportes.equipos', compact('equipos', 'establecimientos', 'provincias', 'modulos', 'descripciones', 'fechaInicio', 'fechaFin'));
    }

    /**
     * Exporta el reporte de equipos de cómputo a Excel.
     */
    public function exportarExcel(Request $request)
    {
        // Validar filtros
        $request->validate([
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'establecimiento_id' => 'nullable|exists:establecimientos,id',
            'provincia' => 'nullable|string',
            'modulo' => 'nullable|string',
        ]);

        // Construir query con los mismos filtros
        $query = EquipoComputo::with(['cabecera.establecimiento', 'cabecera.detalles']);

        if ($request->filled('fecha_inicio')) {
            $query->whereHas('cabecera', function ($q) use ($request) {
                $q->whereDate('fecha', '>=', $request->fecha_inicio);
            });
        }

        if ($request->filled('fecha_fin')) {
            $query->whereHas('cabecera', function ($q) use ($request) {
                $q->whereDate('fecha', '<=', $request->fecha_fin);
            });
        }

        if ($request->filled('establecimiento_id')) {
            $query->whereHas('cabecera', function ($q) use ($request) {
                $q->where('establecimiento_id', $request->establecimiento_id);
            });
        }

        if ($request->filled('provincia')) {
            $query->whereHas('cabecera.establecimiento', function ($q) use ($request) {
                $q->where('provincia', $request->provincia);
            });
        }

        if ($request->filled('modulo')) {
            $query->where('modulo', $request->modulo);
        }

        // Filtro por tipo de establecimiento
        if ($request->filled('tipo')) {
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

            if ($request->tipo === 'ESPECIALIZADO') {
                $query->whereHas('cabecera.establecimiento', function ($q) use ($codigosCSMC, $nombresCSMC) {
                    $q->whereIn('codigo', $codigosCSMC)
                        ->orWhereIn(DB::raw('UPPER(TRIM(nombre))'), $nombresCSMC);
                });
            } elseif ($request->tipo === 'NO ESPECIALIZADO') {
                $query->whereHas('cabecera.establecimiento', function ($q) use ($codigosCSMC, $nombresCSMC) {
                    $q->whereNotIn('codigo', $codigosCSMC)
                        ->whereNotIn(DB::raw('UPPER(TRIM(nombre))'), $nombresCSMC);
                });
            }
        }

        $equipos = $query->orderBy('created_at', 'desc')->get();

        $filename = 'Reporte_Equipos_Computo_' . date('Y-m-d_His') . '.xlsx';

        return Excel::download(new EquiposExport($equipos), $filename);
    }

    /**
     * Obtiene establecimientos filtrados por provincia y tipo
     */
    public function getEstablecimientos(Request $request)
    {
        $query = Establecimiento::whereIn('id', function ($subQuery) {
            $subQuery->select('establecimiento_id')
                ->from('mon_cabecera_monitoreo')
                ->whereIn('id', function ($subSubQuery) {
                    $subSubQuery->select('cabecera_monitoreo_id')
                        ->from('mon_equipos_computo')
                        ->distinct();
                });
        });

        // Filtrar por provincia si se proporciona
        if ($request->filled('provincia')) {
            $query->where('provincia', $request->provincia);
        }

        // Filtrar por distrito si se proporciona
        if ($request->filled('distrito')) {
            $query->where('distrito', $request->distrito);
        }

        // Filtrar por tipo si se proporciona
        if ($request->filled('tipo')) {
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

            if ($request->tipo === 'ESPECIALIZADO') {
                $query->where(function ($q) use ($codigosCSMC, $nombresCSMC) {
                    $q->whereIn('codigo', $codigosCSMC)
                        ->orWhereIn(DB::raw('UPPER(TRIM(nombre))'), $nombresCSMC);
                });
            } elseif ($request->tipo === 'NO ESPECIALIZADO') {
                $query->whereNotIn('codigo', $codigosCSMC)
                    ->whereNotIn(DB::raw('UPPER(TRIM(nombre))'), $nombresCSMC);
            }
        }

        $establecimientos = $query->orderBy('nombre', 'asc')->get(['id', 'nombre']);
        return response()->json($establecimientos);
    }

    /**
     * Obtiene distritos filtrados por provincia y tipo
     */
    public function ajaxGetDistritos(Request $request)
    {
        $query = Establecimiento::whereIn('id', function ($subQuery) {
            $subQuery->select('establecimiento_id')
                ->from('mon_cabecera_monitoreo')
                ->whereIn('id', function ($subSubQuery) {
                    $subSubQuery->select('cabecera_monitoreo_id')
                        ->from('mon_equipos_computo')
                        ->distinct();
                });
        });

        if ($request->filled('provincia')) {
            $query->where('provincia', $request->provincia);
        }

        // Aplicar filtro de tipo si existe
        if ($request->filled('tipo')) {
            $this->applyTipoFilter($query, $request->tipo);
        }

        $distritos = $query->distinct()->pluck('distrito')->filter()->sort()->values();
        return response()->json($distritos);
    }

    /**
     * Obtiene provincias filtradas por tipo
     */
    public function getProvincias(Request $request)
    {
        $query = Establecimiento::whereIn('id', function ($subQuery) {
            $subQuery->select('establecimiento_id')
                ->from('mon_cabecera_monitoreo')
                ->whereIn('id', function ($subSubQuery) {
                    $subSubQuery->select('cabecera_monitoreo_id')
                        ->from('mon_equipos_computo')
                        ->distinct();
                });
        });

        // Filtrar por tipo si se proporciona
        if ($request->filled('tipo')) {
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

            if ($request->tipo === 'ESPECIALIZADO') {
                $query->where(function ($q) use ($codigosCSMC, $nombresCSMC) {
                    $q->whereIn('codigo', $codigosCSMC)
                        ->orWhereIn(DB::raw('UPPER(TRIM(nombre))'), $nombresCSMC);
                });
            } elseif ($request->tipo === 'NO ESPECIALIZADO') {
                $query->whereNotIn('codigo', $codigosCSMC)
                    ->whereNotIn(DB::raw('UPPER(TRIM(nombre))'), $nombresCSMC);
            }
        }

        $provincias = $query->distinct()->pluck('provincia')->filter()->sort()->values();
        return response()->json($provincias);
    }

    /**
     * Obtiene módulos filtrados por establecimiento, provincia y tipo
     */
    public function getModulos(Request $request)
    {
        $query = EquipoComputo::query();

        // Filtrar por establecimiento
        if ($request->filled('establecimiento_id')) {
            $query->whereHas('cabecera', function ($q) use ($request) {
                $q->where('establecimiento_id', $request->establecimiento_id);
            });
        }

        // Filtrar por provincia
        if ($request->filled('provincia')) {
            $query->whereHas('cabecera.establecimiento', function ($q) use ($request) {
                $q->where('provincia', $request->provincia);
            });
        }

        // Filtrar por distrito
        if ($request->filled('distrito')) {
            $query->whereHas('cabecera.establecimiento', function ($q) use ($request) {
                $q->where('distrito', $request->distrito);
            });
        }

        // Filtrar por tipo
        if ($request->filled('tipo')) {
            $query->whereHas('cabecera.establecimiento', function ($q) use ($request) {
                $this->applyTipoFilter($q, $request->tipo);
            });
        }

        $modulosTecnicos = $query->distinct()->pluck('modulo')->filter()->sort()->values();

        $modulos = [];
        foreach ($modulosTecnicos as $moduloTecnico) {
            $modulos[] = [
                'valor' => $moduloTecnico,
                'nombre' => \App\Helpers\ModuloHelper::getNombreAmigable($moduloTecnico) ?? $moduloTecnico
            ];
        }

        return response()->json($modulos);
    }

    /**
     * Obtiene descripciones filtradas por establecimiento, provincia, tipo y módulo
     */
    public function getDescripciones(Request $request)
    {
        $query = EquipoComputo::query();

        // Filtrar por establecimiento
        if ($request->filled('establecimiento_id')) {
            $query->whereHas('cabecera', function ($q) use ($request) {
                $q->where('establecimiento_id', $request->establecimiento_id);
            });
        }

        // Filtrar por provincia
        if ($request->filled('provincia')) {
            $query->whereHas('cabecera.establecimiento', function ($q) use ($request) {
                $q->where('provincia', $request->provincia);
            });
        }

        // Filtrar por distrito
        if ($request->filled('distrito')) {
            $query->whereHas('cabecera.establecimiento', function ($q) use ($request) {
                $q->where('distrito', $request->distrito);
            });
        }

        // Filtrar por tipo
        if ($request->filled('tipo')) {
            $query->whereHas('cabecera.establecimiento', function ($q) use ($request) {
                $this->applyTipoFilter($q, $request->tipo);
            });
        }

        // Filtrar por módulo
        if ($request->filled('modulo')) {
            $query->where('modulo', $request->modulo);
        }

        $descripciones = $query->distinct()->pluck('descripcion')->filter()->sort()->values();
        return response()->json($descripciones);
    }

    /**
     * Helper centralizado para el filtro de tipo de establecimiento
     */
    private function applyTipoFilter($query, $tipo)
    {
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

        if ($tipo === 'ESPECIALIZADO') {
            $query->where(function ($q) use ($codigosCSMC, $nombresCSMC) {
                $q->whereIn('codigo', $codigosCSMC)
                    ->orWhereIn(DB::raw('UPPER(TRIM(nombre))'), $nombresCSMC);
            });
        } elseif ($tipo === 'NO ESPECIALIZADO') {
            $query->where(function ($q) use ($codigosCSMC, $nombresCSMC) {
                $q->whereNotIn('codigo', $codigosCSMC)
                    ->whereNotIn(DB::raw('UPPER(TRIM(nombre))'), $nombresCSMC);
            });
        }
    }
}
