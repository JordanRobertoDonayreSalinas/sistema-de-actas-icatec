<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MonitoreoModulos;
use App\Models\Establecimiento;
use App\Models\EquipoComputo;
use App\Helpers\ModuloHelper;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Exports\AuditoriaEquiposExport;
use Maatwebsite\Excel\Facades\Excel;

class AuditoriaEquiposController extends Controller
{
    public function index(Request $request)
    {
        $fecha_inicio = $request->input('fecha_inicio', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $fecha_fin = $request->input('fecha_fin', Carbon::now()->format('Y-m-d'));
        $provincia = $request->input('provincia');

        // Query base: Obtener todos los módulos con su cabecera y establecimiento
        $query = MonitoreoModulos::with(['cabecera.establecimiento'])
            ->whereHas('cabecera', function ($q) use ($fecha_inicio, $fecha_fin) {
                $q->whereBetween('fecha', [$fecha_inicio, $fecha_fin]);
            });

        if ($provincia) {
            $query->whereHas('cabecera.establecimiento', function ($q) use ($provincia) {
                $q->where('provincia', $provincia);
            });
        }

        $registros = $query->get();
        $inconsistencias = [];

        foreach ($registros as $reg) {
            $cabecera = $reg->cabecera;
            if (!$cabecera)
                continue;

            $contenido = is_string($reg->contenido) ? json_decode($reg->contenido, true) : $reg->contenido;

            // 1. Obtener conectividad (usando el helper o directamente del JSON)
            $conectividad = $contenido['tipo_conectividad'] ?? null;
            if (!$conectividad) {
                // Algunos módulos guardan conectividad dentro de una sub-clave
                $conectividad = data_get($contenido, 'conectividad.tipo');
            }

            $tiene_conexion = ($conectividad && strtoupper($conectividad) !== 'SIN CONECTIVIDAD' && strtoupper($conectividad) !== 'N/A');

            // 2. Contar equipos en la tabla SQL para ese monitoreo y módulo
            $equipos_count = EquipoComputo::where('cabecera_monitoreo_id', $reg->cabecera_monitoreo_id)
                ->where('modulo', $reg->modulo_nombre)
                ->count();

            $tipo_inconsistencia = null;

            // REGLAS DE NEGOCIO:
            // 1. Si tiene equipos, DEBE tener datos de conectividad obligatoriamente.
            if ($equipos_count > 0 && !$tiene_conexion) {
                $tipo_inconsistencia = 'EQUIPO SIN CONEXIÓN';
            }
            // 2. Si no tiene equipos y han agregado datos de conectividad, es inconsistente.
            elseif ($equipos_count == 0 && $tiene_conexion) {
                $tipo_inconsistencia = 'CONEXIÓN SIN EQUIPO';
            }

            if ($tipo_inconsistencia) {
                $inconsistencias[] = [
                    'acta_id' => $reg->cabecera_monitoreo_id,
                    'numero_acta' => $cabecera->numero_acta,
                    'fecha' => $cabecera->fecha,
                    'ipress' => $cabecera->establecimiento->nombre ?? 'N/A',
                    'provincia' => $cabecera->establecimiento->provincia ?? 'N/A',
                    'distrito' => $cabecera->establecimiento->distrito ?? 'N/A',
                    'modulo_nombre' => ModuloHelper::getNombreAmigable($reg->modulo_nombre),
                    'equipos_count' => $equipos_count,
                    'conectividad' => $conectividad ?: 'SIN DATOS',
                    'tipo_inconsistencia' => $tipo_inconsistencia
                ];
            }
        }

        if ($request->has('export')) {
            return Excel::download(new AuditoriaEquiposExport($inconsistencias), 'auditoria_equipos_' . date('Ymd_His') . '.xlsx');
        }

        return view('usuario.auditoria.equipos', [
            'inconsistencias' => $inconsistencias,
            'provincias' => Establecimiento::distinct()->pluck('provincia'),
            'fecha_inicio' => $fecha_inicio,
            'fecha_fin' => $fecha_fin
        ]);
    }
}
