<?php

namespace App\Http\Controllers;

use App\Models\EquipoComputo;
use App\Models\CabeceraMonitoreo;
use App\Models\Establecimiento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Exports\EquiposExport;
use App\Exports\Ficha42Export;
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
     * Exporta la Ficha 42 (Anexo 1) a Excel.
     */
    public function exportarFicha42(Request $request)
    {
        // 1. Identificar las últimas visitas (cabeceras) por establecimiento en el rango de fechas y con los filtros aplicados
        $subQuery = DB::table('mon_cabecera_monitoreo as c')
            ->join('establecimientos as e', 'c.establecimiento_id', '=', 'e.id')
            ->select('c.establecimiento_id', DB::raw('MAX(c.id) as max_id'))
            ->when($request->filled('fecha_inicio'), fn($q) => $q->whereDate('c.fecha', '>=', $request->fecha_inicio))
            ->when($request->filled('fecha_fin'), fn($q) => $q->whereDate('c.fecha', '<=', $request->fecha_fin))
            ->when($request->filled('establecimiento_id'), fn($q) => $q->where('c.establecimiento_id', $request->establecimiento_id))
            ->when($request->filled('provincia'), fn($q) => $q->where('e.provincia', $request->provincia))
            ->when($request->filled('tipo'), function($q) use ($request) {
                // Si el filtro de tipo (ESP/NO ESP) está activo
                $codigosCSMC = ['25933', '28653', '27197', '34021', '25977', '33478', '27199', '30478'];
                if ($request->tipo == 'ESPECIALIZADO') {
                    $q->whereIn('e.codigo', $codigosCSMC);
                } else {
                    $q->whereNotIn('e.codigo', $codigosCSMC);
                }
            })
            ->groupBy('c.establecimiento_id');

        $latestCabecerasIds = $subQuery->pluck('max_id');

        // 2. Obtener las cabeceras con su relación de equipos y detalles (de ambas tablas)
        $cabeceras = CabeceraMonitoreo::with(['establecimiento', 'equipos'])
            ->whereIn('id', $latestCabecerasIds)
            ->get();

        // 3. Definir los tipos de equipo a reportar (Basado en la Ficha 42 / Anexo 1)
        $tiposReporte = [
            '01' => '01. PC (CPU + MONITOR) (CANTIDAD)',
            '02' => '02. IMPRESORA (CANTIDAD)',
            '03' => '03. IMPRESORA TIKETERA TERMICA (CANTIDAD)',
            '04' => '04. LECTORA DE DNI (CANTIDAD)',
            '05' => '05. LECTOR DE HUELLAS DACTILARES (CANTIDAD)',
            '06' => '06. SWITCH (CANTIDAD)',
            '07' => '07. RJ45 (CANTIDAD)',
            '08' => '08. CABLEADO (SI / NO)',
            '09' => '09. OPERADOR (CLARO, MOVISTAR, BITEL, E...)',
            '10' => '10. ANCHO DE BANDA EN (MB)',
            '11' => '11. FIBRA (SI / NO)',
            '12' => '12. COBRE (SI / NO)',
        ];

        // 4. Procesar cada cabecera para el reporte
        $rows = [];
        foreach ($cabeceras as $cabecera) {
            $est = $cabecera->establecimiento;
            if (!$est) continue;

            // Unificar detalles (Nuevos + Antiguos)
            $allDetalles = DB::table('mon_detalle_modulos')->where('cabecera_monitoreo_id', $cabecera->id)->get()
                ->keyBy('modulo_nombre')
                ->merge(DB::table('mon_monitoreo_modulos')->where('cabecera_monitoreo_id', $cabecera->id)->get()->keyBy('modulo_nombre'))
                ->values();
            
            // Recolectar Equipos (SQL + JSON Fallback)
            $listaEquipos = collect();
            foreach ($cabecera->equipos as $e) { $listaEquipos->push($e); }
            if ($listaEquipos->isEmpty()) {
                foreach ($allDetalles as $det) {
                    $cont = is_string($det->contenido) ? json_decode($det->contenido, true) : $det->contenido;
                    $equiposJson = $cont['equipos_data'] ?? ($cont['inventario'] ?? ($cont['equipos'] ?? ($cont['equipos_de_computo'] ?? [])));
                    if (is_array($equiposJson)) {
                        foreach ($equiposJson as $ej) {
                            $obj = new \stdClass();
                            $obj->modulo = $det->modulo_nombre;
                            $obj->descripcion = $ej['descripcion'] ?? ($ej['nombre'] ?? 'PC');
                            $obj->cantidad = $ej['cantidad'] ?? 1;
                            $listaEquipos->push($obj);
                        }
                    }
                }
            }

            // Generar los registros por cada tipo de equipo
            foreach ($tiposReporte as $key => $label) {
                $row = [
                    'categoria' => ($key === '01') ? $est->categoria : '',
                    'codigo' => ($key === '01') ? $est->codigo : '',
                    'nombre' => ($key === '01') ? $est->nombre : '',
                    'tipo_equipo' => $label,
                    'triaje' => 0,
                    'consultorio' => 0,
                    'admision' => 0,
                    'programacion' => 0,
                    'red' => 0,
                    'internet' => 0,
                ];

                if (in_array($key, ['01', '02', '03', '04', '05', '06', '07'])) {
                    // Lógica de conteo por tipo
                    foreach ($listaEquipos as $equipo) {
                        $desc = strtoupper(trim($equipo->descripcion));
                        $match = false;
                        
                        switch($key) {
                            case '01': $match = in_array($desc, ['CPU', 'LAPTOP', 'ALL IN ONE', 'ALL-IN-ONE', '.CPU', 'CP', 'PC']); break;
                            case '02': $match = str_contains($desc, 'IMPRESORA') && !str_contains($desc, 'TICKETERA'); break;
                            case '03': $match = str_contains($desc, 'TICKETERA'); break;
                            case '04': $match = str_contains($desc, 'LECTOR') && (str_contains($desc, 'DNI') || str_contains($desc, 'DNIE')); break;
                            case '05': $match = str_contains($desc, 'HUELLA') || str_contains($desc, 'BIOMETRICO'); break;
                            case '06': $match = str_contains($desc, 'SWITCH'); break;
                            case '07': $match = str_contains($desc, 'RJ45') || str_contains($desc, 'PUNTO DE RED'); break;
                        }

                        if ($match) {
                            $modulo = strtolower(trim($equipo->modulo));
                            if (in_array($modulo, ['triaje', 'triaje_esp'])) $row['triaje'] += $equipo->cantidad;
                            elseif (in_array($modulo, ['citas', 'citas_esp'])) $row['admision'] += $equipo->cantidad;
                            elseif (in_array($modulo, ['gestion_administrativa', 'gestion_admin_esp'])) $row['programacion'] += $equipo->cantidad;
                            else $row['consultorio'] += $equipo->cantidad;
                        }
                    }
                } else {
                    // Lógica para Conectividad (08-12)
                    foreach ($allDetalles as $detalle) {
                        $cont = is_string($detalle->contenido) ? json_decode($detalle->contenido, true) : $detalle->contenido;
                        if (!is_array($cont)) continue;
                        
                        $tipoConect = strtoupper($cont['tipo_conectividad'] ?? ($cont['tipo'] ?? ''));
                        $operador = strtoupper($cont['operador_servicio'] ?? ($cont['operador'] ?? ''));
                        $val = '';

                        switch($key) {
                            case '08': $val = (!empty($tipoConect) && $tipoConect !== 'NINGUNA') ? 'SI' : 'NO'; break;
                            case '09': $val = $operador ?: '-'; break;
                            case '10': $val = $cont['ancho_banda'] ?? ($cont['velocidad'] ?? '-'); break;
                            case '11': $val = str_contains($tipoConect, 'FIBRA') ? 'SI' : 'NO'; break;
                            case '12': $val = (str_contains($tipoConect, 'COBRE') || str_contains($tipoConect, 'HFC')) ? 'SI' : 'NO'; break;
                        }

                        if ($val && $val !== 'NO' && $val !== '-') {
                            // Asignamos el valor al módulo correspondiente (simplificado: si está en el módulo, se marca)
                            $modulo = strtolower(trim($detalle->modulo_nombre));
                            $col = 'consultorio';
                            if (in_array($modulo, ['triaje', 'triaje_esp'])) $col = 'triaje';
                            elseif (in_array($modulo, ['citas', 'citas_esp'])) $col = 'admision';
                            elseif (in_array($modulo, ['gestion_administrativa', 'gestion_admin_esp'])) $col = 'programacion';
                            
                            $row[$col] = $val;
                        }
                    }
                }

                // Solo para la primera fila del EESS (01. PC) mostramos el estado general de red/internet
                if ($key === '01') {
                    foreach ($allDetalles as $detalle) {
                        $cont = is_string($detalle->contenido) ? json_decode($detalle->contenido, true) : $detalle->contenido;
                        if (is_array($cont)) {
                            $tc = strtoupper($cont['tipo_conectividad'] ?? ($cont['tipo'] ?? ''));
                            $op = strtoupper($cont['operador_servicio'] ?? ($cont['operador'] ?? ''));
                            if ($tc && $tc !== 'NINGUNA' && $tc !== 'N/A') $row['red'] = 1;
                            if ($op && $op !== 'NINGUNA' && $op !== 'N/A') $row['internet'] = 1;
                        }
                    }
                }

                $rows[] = $row;
            }
        }

        $filename = 'Ficha_42_Anexo1_' . date('Ymd_His') . '.xlsx';
        return Excel::download(new Ficha42Export(collect($rows)), $filename);
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
