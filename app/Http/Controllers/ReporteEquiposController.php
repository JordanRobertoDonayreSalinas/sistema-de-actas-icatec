<?php

namespace App\Http\Controllers;

use App\Models\EquipoComputo;
use App\Models\CabeceraMonitoreo;
use App\Models\Establecimiento;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
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
            $fechaInicio = session('equipos_fecha_inicio', now()->startOfMonth()->format('Y-m-d'));
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
     * Genera el PDF del reporte de equipos de cómputo.
     */
    public function generarPDF(Request $request)
    {
        // Validar que al menos un filtro esté presente
        $request->validate([
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'establecimiento_id' => 'nullable|exists:establecimientos,id',
            'provincia' => 'nullable|string',
            'modulo' => 'nullable|string',
            'tipo' => 'nullable|in:ESPECIALIZADO,NO ESPECIALIZADO',
        ]);

        // Construir query con los mismos filtros
        $query = EquipoComputo::with(['cabecera.establecimiento']);

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

        // Obtener información de filtros para mostrar en el PDF
        $filtros = [
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_fin' => $request->fecha_fin,
            'establecimiento' => null,
            'provincia' => $request->provincia,
            'modulo' => $request->modulo,
            'tipo' => $request->tipo, // Add tipo to filters
        ];

        if ($request->filled('establecimiento_id')) {
            $filtros['establecimiento'] = Establecimiento::find($request->establecimiento_id);
        }

        $usuarioLogeado = Auth::user();

        // Generar PDF
        $pdf = Pdf::loadView('usuario.reportes.equipos_pdf', compact('equipos', 'filtros', 'usuarioLogeado'));
        $pdf->setPaper('a4', 'landscape'); // Horizontal para que quepan más columnas

        // Renderizar para obtener el total de páginas
        $pdf->render();

        // Inyectar pie de página
        $dompdf = $pdf->getDomPDF();
        $canvas = $dompdf->get_canvas();

        $h = $canvas->get_height();
        $w = $canvas->get_width();

        $fontMetrics = $dompdf->getFontMetrics();
        $font = $fontMetrics->get_font("Helvetica", "normal");
        $size = 8;
        $color = array(0.58, 0.64, 0.72);

        // Texto izquierdo
        $textLeft = "SISTEMA DE ACTAS - REPORTE DE EQUIPOS DE CÓMPUTO";
        $canvas->page_text(42, $h - 40, $textLeft, $font, $size, $color);

        // Paginación derecha
        $textPag = "PAG. {PAGE_NUM} / {PAGE_COUNT}";
        $widthPag = $fontMetrics->getTextWidth("PAG. 00 / 00", $font, $size);
        $canvas->page_text($w - 42 - $widthPag, $h - 40, $textPag, $font, $size, $color);

        // Línea divisoria
        $canvas->page_script('
            $pdf->line(42, $pdf->get_height() - 50, $pdf->get_width() - 42, $pdf->get_height() - 50, array(0.88, 0.91, 0.94), 1);
        ');

        $filename = 'Reporte_Equipos_Computo_' . date('Y-m-d_His') . '.pdf';

        return $pdf->stream($filename);
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

        // Filtrar por tipo
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

        $modulosTecnicos = $query->distinct()->pluck('modulo')->filter()->sort()->values();

        // Convertir a formato esperado por JavaScript: [{valor, nombre}, ...]
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

        // Filtrar por tipo
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

        // Filtrar por módulo
        if ($request->filled('modulo')) {
            $query->where('modulo', $request->modulo);
        }

        $descripciones = $query->distinct()->pluck('descripcion')->filter()->sort()->values();
        return response()->json($descripciones);
    }
}
