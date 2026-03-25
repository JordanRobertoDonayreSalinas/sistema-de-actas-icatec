<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\ImplementacionHelper;
use Illuminate\Pagination\LengthAwarePaginator;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ActasImplementacionExport;
use Carbon\Carbon;

class ReporteImplementacionController extends Controller
{
    public function index(Request $request)
    {
        // 1. Filtros
        $fechaInicio = $request->get('fecha_inicio', Carbon::now()->startOfMonth()->toDateString());
        $fechaFin    = $request->get('fecha_fin', Carbon::now()->endOfMonth()->toDateString());
        $provincia   = $request->get('provincia');
        $distrito    = $request->get('distrito');
        $responsable = $request->get('responsable');
        $filtroModulo = $request->get('modulo_key');

        $modulos = ImplementacionHelper::getModulos();
        $actasTodas = collect();

        // 2. Recolectar datos y aplicar filtros
        foreach ($modulos as $key => $config) {
            if ($filtroModulo && $filtroModulo !== $key) continue;

            $modeloActa = $config['modelo'];
            if (class_exists($modeloActa)) {
                $query = $modeloActa::with(['usuarios', 'implementadores'])
                    ->whereBetween('fecha', [$fechaInicio, $fechaFin]);

                if ($provincia) {
                    $query->where('provincia', $provincia);
                }
                if ($distrito) {
                    $query->where('distrito', $distrito);
                }
                if ($responsable) {
                    $query->where('responsable', 'like', "%{$responsable}%");
                }

                $actasDb = $query->get();

                // Formatear para la colección global
                $actasFormateadas = $actasDb->map(function ($acta) use ($key, $config) {
                    $acta->tipo_key = $key;
                    $acta->tipo_nombre = $config['nombre'];
                    return $acta;
                });

                $actasTodas = $actasTodas->merge($actasFormateadas);
            }
        }

        $actasTodas = $actasTodas->sortByDesc('fecha')->values();

        // 3. Extraer listas únicas para los select de filtros
        $provincias = collect();
        foreach ($modulos as $k => $c) {
            if (class_exists($c['modelo'])) {
                $p = $c['modelo']::select('provincia')->distinct()->pluck('provincia');
                $provincias = $provincias->merge($p);
            }
        }
        $provincias = $provincias->filter()->unique()->sort()->values();

        // 4. Cálculos para KPIs
        $totalGeneral = $actasTodas->count();
        $totalPersonasAsistentes = $actasTodas->sum(function($acta) {
            return $acta->usuarios ? $acta->usuarios->count() : 0;
        });
        
        // Módulo más implementado
        $moduloMasImplementado = $actasTodas->groupBy('tipo_nombre')
            ->map(fn($g) => $g->count())
            ->sortDesc()
            ->keys()
            ->first() ?? 'Ninguno';

        // 5. Paginación
        $page = $request->get('page', 1);
        $perPage = 15;
        $offset = ($page - 1) * $perPage;
        
        $actasPaginadas = new LengthAwarePaginator(
            $actasTodas->slice($offset, $perPage),
            $actasTodas->count(),
            $perPage,
            $page,
            ['path' => route('usuario.reportes.actas.implementacion'), 'query' => $request->query()]
        );

        return view('usuario.reportes.actas_implementacion', compact(
            'actasPaginadas',
            'fechaInicio',
            'fechaFin',
            'provincias',
            'modulos',
            'totalGeneral',
            'totalPersonasAsistentes',
            'moduloMasImplementado'
        ));
    }

    public function exportarExcel(Request $request)
    {
        $fechaInicio = $request->input('fecha_inicio');
        $fechaFin    = $request->input('fecha_fin');
        $provincia   = $request->input('provincia');
        $distrito    = $request->input('distrito');
        $responsable = $request->input('responsable');
        $filtroModulo = $request->input('modulo_key');

        $modulos = ImplementacionHelper::getModulos();
        $actasTodas = collect();

        foreach ($modulos as $key => $config) {
            if ($filtroModulo && $filtroModulo !== $key) continue;

            $modeloActa = $config['modelo'];
            if (class_exists($modeloActa)) {
                $query = $modeloActa::with(['usuarios', 'implementadores']);
                
                if ($fechaInicio && $fechaFin) {
                    $query->whereBetween('fecha', [$fechaInicio, $fechaFin]);
                }
                if ($provincia) {
                    $query->where('provincia', $provincia);
                }
                if ($distrito) {
                    $query->where('distrito', $distrito);
                }
                if ($responsable) {
                    $query->where('responsable', 'like', "%{$responsable}%");
                }

                $actasDb = $query->get()->map(function ($acta) use ($key, $config) {
                    $acta->tipo_key = $key;
                    return $acta;
                });

                $actasTodas = $actasTodas->merge($actasDb);
            }
        }

        $actasTodas = $actasTodas->sortByDesc('fecha')->values();

        return Excel::download(
            new ActasImplementacionExport($actasTodas),
            'Reporte_Actas_Implementacion_' . date('Ymd_His') . '.xlsx'
        );
    }

    public function getDistritos(Request $request)
    {
        $provincia = $request->get('provincia');
        if (!$provincia) return response()->json([]);

        $modulos = ImplementacionHelper::getModulos();
        $distritos = collect();

        foreach ($modulos as $k => $c) {
            if (class_exists($c['modelo'])) {
                $d = $c['modelo']::where('provincia', $provincia)
                    ->select('distrito')
                    ->distinct()
                    ->pluck('distrito');
                $distritos = $distritos->merge($d);
            }
        }
        
        $distritosStr = $distritos->filter()->unique()->sort()->values();
        return response()->json($distritosStr);
    }
}
