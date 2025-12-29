<?php

namespace App\Http\Controllers;

use App\Models\CabeceraMonitoreo;
use App\Models\Establecimiento;
use App\Models\User;
use App\Models\MonitoreoModulos; 
use App\Models\MonitoreoEquipo; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class MonitoreoController extends Controller
{
    /**
     * Listado principal de monitoreos con filtros de búsqueda.
     */
    public function index(Request $request)
    {
        $query = CabeceraMonitoreo::with(['establecimiento', 'equipo'])
                    ->where('user_id', Auth::id());

        if ($request->filled('implementador')) {
            $query->where('implementador', $request->input('implementador'));
        }
        
        if ($request->filled('provincia')) {
            $query->whereHas('establecimiento', function ($q) use ($request) {
                $q->where('provincia', $request->input('provincia'));
            });
        }

        if ($request->filled('fecha_inicio') && $request->filled('fecha_fin')) {
            $query->whereBetween('fecha', [$request->fecha_inicio, $request->fecha_fin]);
        }

        $monitoreos = $query->orderByDesc('id')->paginate(10)->appends($request->query());
        $countCompletados = (clone $query)->where('firmado', 1)->count();
        $implementadores = CabeceraMonitoreo::distinct()->pluck('implementador');
        $provincias = Establecimiento::distinct()->pluck('provincia');

        return view('usuario.monitoreo.index', compact('monitoreos', 'countCompletados', 'implementadores', 'provincias'));
    }

    /**
     * Muestra la vista de creación de nueva acta.
     */
    public function create()
    {
        return view('usuario.monitoreo.create');
    }

    /**
     * AJAX: Buscador inteligente para equipo de monitoreo.
     * SOLUCIÓN DUPLICADOS: Usa groupBy('doc') para que cada persona salga una sola vez.
     */
    public function buscarFiltro(Request $request)
    {
        $term = trim($request->term);
        if (empty($term)) return response()->json([]);

        // Usamos MAX() para que MySQL permita el agrupamiento en modo estricto
        $equipo = MonitoreoEquipo::select(
                        'doc', 
                        DB::raw('MAX(tipo_doc) as tipo_doc'),
                        DB::raw('MAX(apellido_paterno) as apellido_paterno'),
                        DB::raw('MAX(apellido_materno) as apellido_materno'),
                        DB::raw('MAX(nombres) as nombres'),
                        DB::raw('MAX(cargo) as cargo'),
                        DB::raw('MAX(institucion) as institucion')
                    )
                    ->where(function($q) use ($term) {
                        $q->where('doc', 'LIKE', "%$term%")
                          ->orWhere('apellido_paterno', 'LIKE', "%$term%");
                    })
                    ->groupBy('doc') // Asegura unicidad por DNI
                    ->limit(10)
                    ->get();
                    
        return response()->json($equipo);
    }

    /**
     * Busca un miembro específico por DNI para carga rápida en formularios.
     */
    public function buscarMiembroEquipo($doc)
    {
        try {
            $miembro = MonitoreoEquipo::where('doc', trim($doc))
                        ->orderBy('created_at', 'desc')
                        ->first();

            if ($miembro) {
                return response()->json([
                    'exists' => true,
                    'doc' => $miembro->doc,
                    'apellido_paterno' => $miembro->apellido_paterno,
                    'apellido_materno' => $miembro->apellido_materno,
                    'nombres' => $miembro->nombres,
                    'cargo' => $miembro->cargo,
                    'institucion' => $miembro->institucion
                ]);
            }
            return response()->json(['exists' => false]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * GUARDAR PASO 1: Cabecera e Integrantes.
     */
    public function store(Request $request)
    {
        $request->validate([
            'establecimiento_id' => 'required|exists:establecimientos,id',
            'fecha' => 'required|date',
            'responsable' => 'required|string|max:255',
            'categoria' => 'nullable|string|max:50',
            'implementador' => 'required|string',
            'equipo' => 'required|array|min:1', 
        ]);

        try {
            DB::beginTransaction();

            $establecimiento = Establecimiento::findOrFail($request->establecimiento_id);
            $establecimiento->update([
                'responsable' => mb_strtoupper(trim($request->responsable), 'UTF-8'),
                'categoria'   => mb_strtoupper(trim($request->categoria), 'UTF-8'),
            ]);

            $monitoreo = new CabeceraMonitoreo();
            $monitoreo->fecha = $request->fecha;
            $monitoreo->establecimiento_id = $request->establecimiento_id;
            $monitoreo->responsable = mb_strtoupper(trim($request->responsable), 'UTF-8');
            $monitoreo->categoria_congelada = mb_strtoupper(trim($request->categoria), 'UTF-8'); 
            $monitoreo->implementador = mb_strtoupper(trim($request->implementador), 'UTF-8'); 
            $monitoreo->user_id = Auth::id();
            $monitoreo->save();

            foreach ($request->equipo as $persona) {
                if (!empty($persona['doc'])) {
                    // Tras la migración, cada fila tiene su propio ID
                    MonitoreoEquipo::create([
                        'cabecera_monitoreo_id' => $monitoreo->id, 
                        'tipo_doc'              => $persona['tipo_doc'] ?? 'DNI',
                        'doc'                   => trim($persona['doc']),
                        'apellido_paterno'      => mb_strtoupper(trim($persona['apellido_paterno']), 'UTF-8'),
                        'apellido_materno'      => mb_strtoupper(trim($persona['apellido_materno']), 'UTF-8'),
                        'nombres'               => mb_strtoupper(trim($persona['nombres']), 'UTF-8'),
                        'cargo'                 => mb_strtoupper(trim($persona['cargo'] ?? 'MONITOR'), 'UTF-8'),
                        'institucion'           => mb_strtoupper(trim($persona['institucion'] ?? 'DIRESA'), 'UTF-8'),
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('usuario.monitoreo.modulos', $monitoreo->id)
                             ->with('success', 'Acta iniciada correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors('Error: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * PANEL DE MÓDULOS (18 Módulos + Gestión de Toggles).
     */
    public function gestionarModulos($id)
    {
        $acta = CabeceraMonitoreo::with(['establecimiento', 'equipo'])->findOrFail($id);

        $modulosMaster = [
            'gestion_administrativa' => ['nombre' => '01. Gestión Administrativa', 'icon' => 'folder-kanban'],
            'citas'                  => ['nombre' => '02. Citas', 'icon' => 'calendar-clock'],
            'triaje'                 => ['nombre' => '03. Triaje', 'icon' => 'stethoscope'],
            'consulta_medicina'      => ['nombre' => '04. Consulta Externa: Medicina', 'icon' => 'user-cog'],
            'consulta_odontologia'   => ['nombre' => '05. Consulta Externa: Odontología', 'icon' => 'smile'],
            'consulta_nutricion'     => ['nombre' => '06. Consulta Externa: Nutrición', 'icon' => 'apple'],
            'consulta_psicologia'    => ['nombre' => '07. Consulta Externa: Psicología', 'icon' => 'brain'],
            'cred'                   => ['nombre' => '08. CRED', 'icon' => 'baby'],
            'inmunizaciones'         => ['nombre' => '09. Inmunizaciones', 'icon' => 'syringe'],
            'atencion_prenatal'      => ['nombre' => '10. Atención Prenatal', 'icon' => 'heart-pulse'],
            'planificacion_familiar' => ['nombre' => '11. Planificación Familiar', 'icon' => 'users'],
            'parto'                  => ['nombre' => '12. Parto', 'icon' => 'bed'],
            'puerperio'              => ['nombre' => '13. Puerperio', 'icon' => 'home'],
            'fua_electronico'        => ['nombre' => '14. FUA Electrónico', 'icon' => 'file-digit'],
            'farmacia'               => ['nombre' => '15. Farmacia', 'icon' => 'pill'],
            'referencias'            => ['nombre' => '16. Referencias y Contrareferencias', 'icon' => 'map-pinned'],
            'laboratorio'            => ['nombre' => '17. Laboratorio', 'icon' => 'test-tube-2'],
            'urgencias'              => ['nombre' => '18. Urgencias y Emergencias', 'icon' => 'ambulance'],
        ];

        $modulosGuardados = MonitoreoModulos::where('cabecera_monitoreo_id', $id)
                            ->where('modulo_nombre', '!=', 'config_modulos')
                            ->pluck('modulo_nombre')
                            ->toArray();

        $config = MonitoreoModulos::where('cabecera_monitoreo_id', $id)
                    ->where('modulo_nombre', 'config_modulos')
                    ->first();
        
        $modulosActivos = $config ? $config->contenido : array_keys($modulosMaster);

        return view('usuario.monitoreo.modulos', compact('acta', 'modulosMaster', 'modulosGuardados', 'modulosActivos'));
    }

    /**
     * AJAX: Guarda la configuración de módulos activos (toggles).
     */
    public function toggleModulos(Request $request, $id)
    {
        try {
            MonitoreoModulos::updateOrCreate(
                ['cabecera_monitoreo_id' => $id, 'modulo_nombre' => 'config_modulos'],
                ['contenido' => $request->modulos_activos] 
            );
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * GUARDAR CONTENIDO DE UN MÓDULO.
     */
    public function guardarDetalle(Request $request, $id)
    {
        $modulo = $request->input('modulo_nombre');
        $datos = $request->input('contenido');

        if (!$modulo) return back()->withErrors('Módulo no identificado.');

        try {
            MonitoreoModulos::updateOrCreate(
                ['cabecera_monitoreo_id' => $id, 'modulo_nombre' => $modulo],
                ['contenido' => $datos]
            );
            return redirect()->route('usuario.monitoreo.modulos', $id)
                             ->with('success', "Módulo guardado correctamente.");
        } catch (\Exception $e) {
            return back()->withErrors('Error: ' . $e->getMessage());
        }
    }

    /**
     * CARGAR VISTA DE MÓDULO ESPECÍFICO.
     */
    public function cargarSeccionModulo($id, $seccion)
    {
        $acta = CabeceraMonitoreo::with(['establecimiento'])->findOrFail($id);
        
        $vistas = [
            'gestion_administrativa' => 'usuario.monitoreo.modulos.gestion_administrativa',
            'citas'                  => 'usuario.monitoreo.modulos.citas',
            'triaje'                 => 'usuario.monitoreo.modulos.triaje',
            'consulta_medicina'      => 'usuario.monitoreo.modulos.medicina',
            'consulta_odontologia'   => 'usuario.monitoreo.modulos.odontologia',
            'consulta_nutricion'     => 'usuario.monitoreo.modulos.nutricion',
            'consulta_psicologia'    => 'usuario.monitoreo.modulos.psicologia',
            'cred'                   => 'usuario.monitoreo.modulos.cred',
            'inmunizaciones'         => 'usuario.monitoreo.modulos.inmunizaciones',
            'atencion_prenatal'      => 'usuario.monitoreo.modulos.prenatal',
            'planificacion_familiar' => 'usuario.monitoreo.modulos.planificacion',
            'parto'                  => 'usuario.monitoreo.modulos.parto',
            'puerperio'              => 'usuario.monitoreo.modulos.puerperio',
            'fua_electronico'        => 'usuario.monitoreo.modulos.fua',
            'farmacia'               => 'usuario.monitoreo.modulos.farmacia',
            'referencias'            => 'usuario.monitoreo.modulos.referencias',
            'laboratorio'            => 'usuario.monitoreo.modulos.laboratorio',
            'urgencias'              => 'usuario.monitoreo.modulos.urgencias',
        ];

        if (!array_key_exists($seccion, $vistas)) abort(404);

        $detalle = MonitoreoModulos::where('cabecera_monitoreo_id', $id)
                    ->where('modulo_nombre', $seccion)
                    ->first();

        return view($vistas[$seccion], compact('acta', 'seccion', 'detalle'));
    }

    /**
     * GENERAR PDF DEL ACTA.
     */
    public function generarPDF($id)
    {
        $acta = CabeceraMonitoreo::with(['establecimiento', 'user', 'equipo'])->findOrFail($id);
        $logoPath = public_path('img/logo.png'); 
        $logoBase64 = file_exists($logoPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath)) : '';

        return Pdf::loadView('usuario.monitoreo.pdf', compact('acta', 'logoBase64'))
                  ->setPaper('a4', 'portrait')
                  ->stream("ACTA_MONITOREO_{$acta->id}.pdf");
    }

    /**
     * SUBIR PDF FIRMADO.
     */
    public function subirPDF(Request $request, $id)
    {
        $request->validate(['pdf_firmado' => 'required|mimes:pdf|max:10240']);
        $monitoreo = CabeceraMonitoreo::where('user_id', Auth::id())->findOrFail($id);

        if ($request->hasFile('pdf_firmado')) {
            if ($monitoreo->firmado_pdf) Storage::disk('public')->delete($monitoreo->firmado_pdf);
            $path = $request->file('pdf_firmado')->store('monitoreos_firmados', 'public');
            $monitoreo->update(['firmado_pdf' => $path, 'firmado' => true]);
        }
        return back()->with('success', 'Archivo cargado con éxito.');
    }
}