<?php

namespace App\Http\Controllers;

use App\Models\Acta;
use App\Models\CabeceraMonitoreo;
use App\Models\Establecimiento;
use App\Helpers\ImplementacionHelper;
use App\Exports\CronogramaActividadesExport;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class CronogramaActividadesController extends Controller
{
    /**
     * Recopila todas las actividades aplicando filtros dados.
     * Retorna colección ordenada por fecha DESC.
     */
    protected function recopilarActividades(
        string $fechaInicio,
        string $fechaFin,
        ?string $filtroProv,
        ?string $filtroTipo,
        bool $enriquecido = false  // true => carga relaciones extra para el Excel
    ) {
        $actividades = collect();

        // ============================================================
        // 1. ACTAS DE ASISTENCIA TÉCNICA
        // ============================================================
        if (!$filtroTipo || $filtroTipo === 'asistencia') {
            $with = $enriquecido ? ['establecimiento', 'participantes', 'actividades'] : ['establecimiento'];

            $queryAt = Acta::where('tipo', 'asistencia')
                ->with($with)
                ->where(fn($q) => $q->where('anulado', false)->orWhereNull('anulado'))
                ->whereDate('fecha', '>=', $fechaInicio)
                ->whereDate('fecha', '<=', $fechaFin);

            if ($filtroProv) {
                $queryAt->whereHas('establecimiento', fn($q) => $q->where('provincia', $filtroProv));
            }

            $queryAt->get()->each(function ($acta) use (&$actividades, $enriquecido) {

                // Participantes para Excel
                $participantesTxt = '—';
                $imagenesPaths = [];
                $nombreEstab = optional($acta->establecimiento)->nombre ?? '—';
                $categoriaEstab = optional($acta->establecimiento)->categoria ?? '';

                if ($enriquecido) {
                    $lineas = [];

                    // Helper: cargo en MAYÚSCULAS
                    $cargoMayus = fn(string $s): string => mb_strtoupper($s, 'UTF-8');

                    // Helper: nombre en Title Case — "YAÑEZ MEDINA" → "Yañez Medina"
                    $titleCase = fn(string $s): string =>
                        mb_convert_case(mb_strtolower($s, 'UTF-8'), MB_CASE_TITLE, 'UTF-8');

                    // Implementador principal — CARGO: Nombre en Title Case
                    if ($acta->implementador) {
                        $lineas[] = 'IMPLEMENTADOR: ' . $titleCase($acta->implementador);
                    }

                    // Participantes del acta — CARGO: Nombre en Title Case
                    foreach ($acta->participantes as $p) {
                        $nombre = trim($p->apellidos . ' ' . $p->nombres);
                        if ($nombre) {
                            $cargoFmt = $p->cargo ? $cargoMayus($p->cargo) . ': ' : '';
                            $lineas[] = $cargoFmt . $titleCase($nombre);
                        }
                    }

                    $participantesTxt = implode("\n", $lineas) ?: '—';

                    // Todas las imágenes disponibles
                    foreach (['imagen1','imagen2','imagen3','imagen4','imagen5'] as $campo) {
                        if (!empty($acta->$campo)) {
                            $path = storage_path('app/public/' . $acta->$campo);
                            if (file_exists($path)) {
                                $imagenesPaths[] = $path;
                            }
                        }
                    }
                }

                $actividades->push([
                    'fecha'                  => $acta->fecha,
                    'tipo'                   => 'Asistencia Técnica',
                    'tipo_key'               => 'asistencia',
                    'establecimiento'        => $nombreEstab,
                    'categoria_establecimiento' => $categoriaEstab,
                    'provincia'              => optional($acta->establecimiento)->provincia ?? '—',
                    'responsable'            => $acta->implementador ?? '—',
                    'actividad'              => $acta->tema ?? '—',
                    'modalidad'              => $acta->modalidad ?? '—',
                    'firmado'                => $acta->firmado,
                    'anulado'                => $acta->anulado ?? false,
                    // Campos extra para Excel
                    'nombre_acta'            => 'Acta de Asistencia Técnica N° ' . $acta->id,
                    'num_acta'               => $acta->id,
                    'participantes_txt'      => $participantesTxt,
                    'imagenes_paths'         => $imagenesPaths,
                ]);
            });
        }

        // ============================================================
        // 2. ACTAS DE MONITOREO
        // ============================================================
        if (!$filtroTipo || $filtroTipo === 'monitoreo') {
            $with = $enriquecido ? ['establecimiento', 'equipo', 'detalles'] : ['establecimiento'];

            $queryMon = CabeceraMonitoreo::with($with)
                ->where(fn($q) => $q->where('anulado', false)->orWhereNull('anulado'))
                ->whereDate('fecha', '>=', $fechaInicio)
                ->whereDate('fecha', '<=', $fechaFin);

            if ($filtroProv) {
                $queryMon->whereHas('establecimiento', fn($q) => $q->where('provincia', $filtroProv));
            }

            $queryMon->get()->each(function ($acta) use (&$actividades, $enriquecido) {

                $participantesTxt = '—';
                $imagenesPaths = [];

                if ($enriquecido) {
                    $lineas = [];

                    // Helper: cargo en MAYÚSCULAS
                    $cargoMayus = fn(string $s): string => mb_strtoupper(trim($s), 'UTF-8');

                    // Helper: nombre en Title Case
                    $titleCase = fn(string $s): string =>
                        mb_convert_case(mb_strtolower(trim($s), 'UTF-8'), MB_CASE_TITLE, 'UTF-8');

                    // Implementador del acta (equipo de implementación)
                    if ($acta->implementador) {
                        $lineas[] = 'IMPLEMENTADOR: ' . $titleCase($acta->implementador);
                    }

                    // Miembros del equipo implementador (tabla mon_equipo_monitoreo)
                    foreach ($acta->equipo as $miembro) {
                        $nombre = trim(
                            ($miembro->apellido_paterno ?? '') . ' ' .
                            ($miembro->apellido_materno ?? '') . ' ' .
                            ($miembro->nombres ?? '')
                        );
                        if ($nombre) {
                            $cargoFmt = $miembro->cargo ? $cargoMayus($miembro->cargo) . ': ' : '';
                            $lineas[] = $cargoFmt . $titleCase($nombre);
                        }
                    }

                    // Profesionales entrevistados en cada módulo (contenido JSON)
                    foreach ($acta->detalles as $modulo) {
                        $contenido = $modulo->contenido;
                        if (empty($contenido)) continue;

                        // Los módulos guardan el profesional en distintas claves según el controlador
                        $prof = $contenido['profesional']           // triaje, consulta_medicina
                             ?? $contenido['datos_del_profesional'] // medicina_familiar ESP, triaje ESP
                             ?? null;

                        if (!$prof) continue;

                        $nombre = trim(
                            ($prof['apellido_paterno'] ?? '') . ' ' .
                            ($prof['apellido_materno'] ?? '') . ' ' .
                            ($prof['nombres']          ?? '')
                        );
                        if (!$nombre) continue;

                        // Cargo: se guarda en 'cargo' o 'profesion' según el módulo
                        $cargo = trim(
                            $prof['cargo']     ??
                            $prof['profesion'] ??
                            ''
                        );
                        $cargoFmt = $cargo ? $cargoMayus($cargo) . ': ' : '';
                        $lineas[] = $cargoFmt . $titleCase($nombre);
                    }

                    $participantesTxt = implode("\n", $lineas) ?: '—';

                    // Foto del acta de monitoreo
                    foreach (['foto1','foto2'] as $campo) {
                        if (!empty($acta->$campo)) {
                            $path = storage_path('app/public/' . $acta->$campo);
                            if (file_exists($path)) {
                                $imagenesPaths[] = $path;
                            }
                        }
                    }
                }

                $actividades->push([
                    'fecha'                     => $acta->fecha,
                    'tipo'                      => 'Monitoreo',
                    'tipo_key'                  => 'monitoreo',
                    'establecimiento'           => optional($acta->establecimiento)->nombre ?? '—',
                    'categoria_establecimiento' => optional($acta->establecimiento)->categoria ?? '',
                    'provincia'                 => optional($acta->establecimiento)->provincia ?? '—',
                    'responsable'               => $acta->implementador ?? '—',
                    'actividad'                 => $acta->tipo_origen ?? '—',
                    'modalidad'                 => '—',
                    'firmado'                   => $acta->firmado,
                    'anulado'                   => $acta->anulado ?? false,
                    'nombre_acta'               => 'Acta de Monitoreo N° ' . ($acta->numero_acta ?? $acta->id),
                    'num_acta'                  => $acta->numero_acta ?? $acta->id,
                    'participantes_txt'         => $participantesTxt,
                    'imagenes_paths'            => $imagenesPaths,
                ]);
            });
        }

        // ============================================================
        // 3. ACTAS DE IMPLEMENTACIÓN (todos los submódulos)
        // ============================================================
        if (!$filtroTipo || $filtroTipo === 'implementacion') {
            $modulos = ImplementacionHelper::getModulos();

            foreach ($modulos as $key => $config) {
                $modeloActa = $config['modelo'];
                if (!class_exists($modeloActa)) continue;

                $with = $enriquecido ? ['implementadores', 'usuarios'] : [];

                $query = $modeloActa::with($with)
                    ->whereBetween('fecha', [$fechaInicio, $fechaFin])
                    ->where(fn($q) => $q->where('anulado', false)->orWhereNull('anulado'));

                if ($filtroProv) {
                    $query->where('provincia', $filtroProv);
                }

                $query->get()->each(function ($acta) use (&$actividades, $key, $config, $enriquecido) {

                    $participantesTxt = '—';
                    $imagenesPaths = [];

                    if ($enriquecido) {
                        $lineas = [];

                        // Helper: cargo en MAYÚSCULAS
                        $cargoMayus = fn(string $s): string => mb_strtoupper(trim($s), 'UTF-8');

                        // Helper: nombre en Title Case
                        $titleCase = fn(string $s): string =>
                            mb_convert_case(mb_strtolower(trim($s), 'UTF-8'), MB_CASE_TITLE, 'UTF-8');

                        if (method_exists($acta, 'implementadores') && $acta->implementadores) {
                            foreach ($acta->implementadores as $impl) {
                                $nombre = trim(
                                    ($impl->apellido_paterno ?? '') . ' ' .
                                    ($impl->apellido_materno ?? '') . ' ' .
                                    ($impl->nombres ?? '')
                                );
                                if ($nombre) $lineas[] = 'IMPLEMENTADOR: ' . $titleCase($nombre);
                            }
                        } elseif ($acta->responsable) {
                            $lineas[] = 'IMPLEMENTADOR: ' . $titleCase($acta->responsable);
                        }

                        if (method_exists($acta, 'usuarios') && $acta->usuarios) {
                            foreach ($acta->usuarios as $u) {
                                $nombre = trim(
                                    ($u->apellido_paterno ?? '') . ' ' .
                                    ($u->apellido_materno ?? '') . ' ' .
                                    ($u->nombres ?? '')
                                );
                                if ($nombre) {
                                    $cargo = !empty(trim($u->cargo ?? ''))
                                        ? $cargoMayus($u->cargo)
                                        : 'PARTICIPANTE';
                                    $lineas[] = $cargo . ': ' . $titleCase($nombre);
                                }
                            }
                        }

                        $participantesTxt = implode("\n", $lineas) ?: '—';

                        // Fotos de implementación
                        foreach (['foto1','foto2','foto3'] as $campo) {
                            if (!empty($acta->$campo)) {
                                $path = storage_path('app/public/' . $acta->$campo);
                                if (file_exists($path)) {
                                    $imagenesPaths[] = $path;
                                }
                            }
                        }
                    }

                    $actividades->push([
                        'fecha'                     => $acta->fecha,
                        'tipo'                      => 'Implementación — ' . $config['nombre'],
                        'tipo_key'                  => 'implementacion',
                        'establecimiento'           => $acta->nombre_establecimiento ?? ($acta->establecimiento ?? '—'),
                        'categoria_establecimiento' => $acta->categoria ?? '',
                        'provincia'                 => $acta->provincia ?? '—',
                        'responsable'               => $acta->responsable ?? '—',
                        'actividad'                 => $config['nombre'],
                        'modalidad'                 => '—',
                        'firmado'                   => $acta->firmado ?? false,
                        'anulado'                   => $acta->anulado ?? false,
                        'nombre_acta'               => 'Acta de Implementación - ' . $config['nombre'] . ' N° ' . $acta->id,
                        'num_acta'                  => $acta->id,
                        'participantes_txt'         => $participantesTxt,
                        'imagenes_paths'            => $imagenesPaths,
                    ]);
                });
            }
        }

        return $actividades->sortByDesc('fecha')->values();
    }

    /**
     * Muestra el Cronograma de Actividades unificado.
     */
    public function index(Request $request)
    {
        // --- Persistencia de fechas en sesión ---
        if ($request->filled('fecha_inicio') || $request->filled('fecha_fin')) {
            session(['cronograma_fecha_inicio' => $request->input('fecha_inicio')]);
            session(['cronograma_fecha_fin'    => $request->input('fecha_fin')]);
        } else {
            $request->merge([
                'fecha_inicio' => session('cronograma_fecha_inicio', now()->startOfMonth()->format('Y-m-d')),
                'fecha_fin'    => session('cronograma_fecha_fin',    now()->endOfMonth()->format('Y-m-d')),
            ]);
        }

        $fechaInicio = $request->input('fecha_inicio');
        $fechaFin    = $request->input('fecha_fin');
        $filtroProv  = $request->input('provincia');
        $filtroTipo  = $request->input('tipo_acta');

        $actividades = $this->recopilarActividades($fechaInicio, $fechaFin, $filtroProv, $filtroTipo);

        // Provincias para filtros
        $provincias = $this->getProvincias();

        // KPIs
        $totalActividades    = $actividades->count();
        $totalFirmadas       = $actividades->where('firmado', true)->count();
        $totalPendientes     = $actividades->where('firmado', false)->count();
        $totalAnuladas       = $actividades->where('anulado', true)->count();
        $countAsistencia     = $actividades->where('tipo_key', 'asistencia')->count();
        $countMonitoreo      = $actividades->where('tipo_key', 'monitoreo')->count();
        $countImplementacion = $actividades->where('tipo_key', 'implementacion')->count();

        // Paginación manual
        $page    = $request->get('page', 1);
        $perPage = 30;
        $offset  = ($page - 1) * $perPage;

        $actasPaginadas = new LengthAwarePaginator(
            $actividades->slice($offset, $perPage),
            $actividades->count(),
            $perPage,
            $page,
            ['path' => route('usuario.reportes.cronograma'), 'query' => $request->query()]
        );

        return view('usuario.reportes.cronograma_actividades', compact(
            'actasPaginadas', 'provincias',
            'fechaInicio', 'fechaFin',
            'totalActividades', 'totalFirmadas', 'totalPendientes', 'totalAnuladas',
            'countAsistencia', 'countMonitoreo', 'countImplementacion'
        ));
    }

    /**
     * Exporta el cronograma a Excel con el formato de la imagen solicitada.
     */
    public function exportarExcel(Request $request)
    {
        $fechaInicio = $request->input('fecha_inicio', now()->startOfMonth()->format('Y-m-d'));
        $fechaFin    = $request->input('fecha_fin',    now()->endOfMonth()->format('Y-m-d'));
        $filtroProv  = $request->input('provincia');
        $filtroTipo  = $request->input('tipo_acta');

        // Carga enriquecida con participantes e imágenes
        $actividades = $this->recopilarActividades($fechaInicio, $fechaFin, $filtroProv, $filtroTipo, true);

        // Para el Excel ordenamos ascendente (fecha más antigua primero)
        $actividades = $actividades->sortBy('fecha')->values();

        $filename = 'Cronograma_Actividades_' . date('Ymd_His') . '.xlsx';

        return Excel::download(new CronogramaActividadesExport($actividades), $filename);
    }

    /**
     * Exporta el cronograma a PDF con orientación horizontal.
     */
    public function exportarPdf(Request $request)
    {
        $fechaInicio = $request->input('fecha_inicio', now()->startOfMonth()->format('Y-m-d'));
        $fechaFin    = $request->input('fecha_fin',    now()->endOfMonth()->format('Y-m-d'));
        $filtroProv  = $request->input('provincia');
        $filtroTipo  = $request->input('tipo_acta');

        // Carga enriquecida con participantes e imágenes
        $actividades = $this->recopilarActividades($fechaInicio, $fechaFin, $filtroProv, $filtroTipo, true);

        // Para el PDF ordenamos ascendente (fecha más antigua primero)
        $actividades = $actividades->sortBy('fecha')->values();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('usuario.reportes.cronograma_pdf', compact('actividades', 'fechaInicio', 'fechaFin'))
            ->setOptions(['isRemoteEnabled' => true])
            ->setPaper('a4', 'landscape');

        $filename = 'Cronograma_Actividades_' . date('Ymd_His') . '.pdf';

        return $pdf->stream($filename);
    }

    /**
     * Helper: Provincias únicas de los tres módulos.
     */
    protected function getProvincias()
    {
        $provincias = collect();

        $provincias = $provincias->merge(
            Establecimiento::whereIn('id', Acta::where('tipo', 'asistencia')->select('establecimiento_id'))
                ->distinct()->pluck('provincia')->filter()
        );
        $provincias = $provincias->merge(
            Establecimiento::whereIn('id', CabeceraMonitoreo::select('establecimiento_id'))
                ->distinct()->pluck('provincia')->filter()
        );

        $modulos = ImplementacionHelper::getModulos();
        foreach ($modulos as $config) {
            if (class_exists($config['modelo'])) {
                $provincias = $provincias->merge(
                    $config['modelo']::select('provincia')->distinct()->pluck('provincia')->filter()
                );
            }
        }

        return $provincias->unique()->sort()->values();
    }

    /**
     * AJAX: Provincias disponibles.
     */
    public function ajaxGetProvincias()
    {
        return response()->json($this->getProvincias());
    }
}
