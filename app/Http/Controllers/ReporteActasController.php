<?php

namespace App\Http\Controllers;

use App\Models\Acta;
use App\Models\Establecimiento;
use Illuminate\Http\Request;
use App\Exports\ActasAsistenciaExport;
use Maatwebsite\Excel\Facades\Excel;

class ReporteActasController extends Controller
{
    /**
     * Muestra el reporte de Actas de Asistencia Técnica.
     */
    public function index(Request $request)
    {
        // Persistencia de fechas en sesión
        if ($request->filled('fecha_inicio') || $request->filled('fecha_fin')) {
            session(['actas_at_fecha_inicio' => $request->input('fecha_inicio')]);
            session(['actas_at_fecha_fin'    => $request->input('fecha_fin')]);
        } else {
            $request->merge([
                'fecha_inicio' => session('actas_at_fecha_inicio', now()->startOfYear()->format('Y-m-d')),
                'fecha_fin'    => session('actas_at_fecha_fin',    now()->format('Y-m-d')),
            ]);
        }

        $fechaInicio = $request->input('fecha_inicio');
        $fechaFin    = $request->input('fecha_fin');

        // Listas para filtros en cascada
        $implementadores = Acta::where('tipo', 'asistencia')
            ->distinct()->orderBy('implementador')->pluck('implementador')->filter();

        $temas = Acta::where('tipo', 'asistencia')
            ->distinct()->orderBy('tema')->pluck('tema')->filter();

        $modulos = [
            'Atencion Prenatal', 'Citas', 'Consulta Externa: Medicina',
            'Consulta Externa: Nutricion', 'Consulta Externa: Odontologia',
            'Consulta Externa: Psicologia', 'Cred', 'Farmacia', 'FUA',
            'Gestión Administrativa', 'Inmunizaciones', 'Laboratorio',
            'Parto', 'Planificacion Familiar', 'Puerperio',
            'Teleatiendo', 'Triaje', 'VIH',
        ];

        $provincias = Establecimiento::whereIn('id',
            Acta::where('tipo', 'asistencia')->select('establecimiento_id')
        )->distinct()->orderBy('provincia')->pluck('provincia')->filter();

        // Query principal — solo actas de asistencia técnica
        $query = Acta::where('tipo', 'asistencia')
            ->with(['establecimiento', 'participantes'])
            ->whereDate('fecha', '>=', $fechaInicio)
            ->whereDate('fecha', '<=', $fechaFin);

        if ($request->filled('implementador')) {
            $query->where('implementador', $request->implementador);
        }

        if ($request->filled('tema')) {
            $query->where('tema', $request->tema);
        }

        if ($request->filled('modulo')) {
            $query->whereHas('participantes', fn($q) =>
                $q->where('modulo', $request->modulo));
        }

        if ($request->filled('provincia')) {
            $query->whereHas('establecimiento', fn($q) =>
                $q->where('provincia', $request->provincia));
        }

        if ($request->filled('distrito')) {
            $query->whereHas('establecimiento', fn($q) =>
                $q->where('distrito', $request->distrito));
        }

        if ($request->filled('establecimiento_id')) {
            $query->where('establecimiento_id', $request->establecimiento_id);
        }

        if ($request->filled('firmado') && $request->firmado !== '') {
            $query->where('firmado', (bool) $request->firmado);
        }

        $actas = $query->orderByDesc('fecha')->paginate(25)->withQueryString();

        // KPIs
        $totalBase   = Acta::where('tipo', 'asistencia')
            ->whereDate('fecha', '>=', $fechaInicio)
            ->whereDate('fecha', '<=', $fechaFin);

        $totalFirmadas   = (clone $totalBase)->where('firmado', true)->count();
        $totalPendientes = (clone $totalBase)->where('firmado', false)->count();

        return view('usuario.reportes.actas_asistencia', compact(
            'actas', 'implementadores', 'temas', 'modulos', 'provincias',
            'fechaInicio', 'fechaFin',
            'totalFirmadas', 'totalPendientes'
        ));
    }

    /**
     * Exporta el reporte a Excel con los mismos filtros.
     */
    public function exportarExcel(Request $request)
    {
        $fechaInicio = $request->input('fecha_inicio', now()->startOfYear()->format('Y-m-d'));
        $fechaFin    = $request->input('fecha_fin',    now()->format('Y-m-d'));

        $query = Acta::where('tipo', 'asistencia')
            ->with(['establecimiento', 'participantes'])
            ->whereDate('fecha', '>=', $fechaInicio)
            ->whereDate('fecha', '<=', $fechaFin);

        if ($request->filled('implementador')) {
            $query->where('implementador', $request->implementador);
        }
        if ($request->filled('tema')) {
            $query->where('tema', $request->tema);
        }
        if ($request->filled('modulo')) {
            $query->whereHas('participantes', fn($q) =>
                $q->where('modulo', $request->modulo));
        }
        if ($request->filled('provincia')) {
            $query->whereHas('establecimiento', fn($q) =>
                $q->where('provincia', $request->provincia));
        }
        if ($request->filled('distrito')) {
            $query->whereHas('establecimiento', fn($q) =>
                $q->where('distrito', $request->distrito));
        }
        if ($request->filled('establecimiento_id')) {
            $query->where('establecimiento_id', $request->establecimiento_id);
        }
        if ($request->filled('firmado') && $request->firmado !== '') {
            $query->where('firmado', (bool) $request->firmado);
        }

        $actas    = $query->orderByDesc('fecha')->get();
        $filename = 'Reporte_Actas_AsistenciaTecnica_' . date('Y-m-d_His') . '.xlsx';

        return Excel::download(new ActasAsistenciaExport($actas), $filename);
    }

    /**
     * AJAX: Distritos filtrados por provincia.
     */
    public function ajaxGetDistritos(Request $request)
    {
        $query = Establecimiento::whereIn('id',
            Acta::where('tipo', 'asistencia')->select('establecimiento_id'));

        if ($request->filled('provincia')) {
            $query->where('provincia', $request->provincia);
        }

        return response()->json(
            $query->distinct()->orderBy('distrito')->pluck('distrito')->filter()->values()
        );
    }

    /**
     * AJAX: Establecimientos filtrados por provincia y/o distrito.
     */
    public function ajaxGetEstablecimientos(Request $request)
    {
        $query = Establecimiento::whereIn('id',
            Acta::where('tipo', 'asistencia')->select('establecimiento_id'));

        if ($request->filled('provincia')) {
            $query->where('provincia', $request->provincia);
        }
        if ($request->filled('distrito')) {
            $query->where('distrito', $request->distrito);
        }

        return response()->json(
            $query->orderBy('nombre')->get(['id', 'nombre'])
        );
    }
}
