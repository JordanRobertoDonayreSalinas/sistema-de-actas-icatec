<?php

namespace App\Http\Controllers;

use App\Models\CabeceraMonitoreo;
use App\Models\Establecimiento;
use App\Models\MonitoreoModulos;
use App\Models\MonitoreoEquipo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MonitoreoController extends Controller
{
    /**
     * Listado principal de monitoreos con filtros de búsqueda y paginación.
     * Se cargan los 'detalles' para el contador dinámico de firmas en la vista.
     */
    public function index(Request $request)
    {
        $query = CabeceraMonitoreo::with(['establecimiento', 'equipo', 'detalles'])
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

        // Conteo de actas donde el acta final consolidada ya fue firmada
        $countCompletados = (clone $query)->where('firmado', 1)->count();
        $implementadores = CabeceraMonitoreo::distinct()->pluck('implementador');
        $provincias = Establecimiento::distinct()->pluck('provincia');

        return view('usuario.monitoreo.index', compact('monitoreos', 'countCompletados', 'implementadores', 'provincias'));
    }

    /**
     * Vista de creación de nueva acta (Paso 1).
     */
    public function create()
    {
        return view('usuario.monitoreo.create');
    }

    /**
     * AJAX: Buscador inteligente para equipo de monitoreo (Autocomplete).
     */
    public function buscarFiltro(Request $request)
    {
        $term = trim($request->term);
        if (empty($term)) return response()->json([]);

        $equipo = MonitoreoEquipo::select(
            'doc',
            DB::raw('MAX(tipo_doc) as tipo_doc'),
            DB::raw('MAX(apellido_paterno) as apellido_paterno'),
            DB::raw('MAX(apellido_materno) as apellido_materno'),
            DB::raw('MAX(nombres) as nombres'),
            DB::raw('MAX(cargo) as cargo'),
            DB::raw('MAX(institucion) as institucion')
        )
            ->where(function ($q) use ($term) {
                $q->where('doc', 'LIKE', "%$term%")
                    ->orWhere('apellido_paterno', 'LIKE', "%$term%");
            })
            ->groupBy('doc')
            ->limit(10)
            ->get();

        return response()->json($equipo);
    }

    /**
     * AJAX: Busca un miembro específico por DNI para carga rápida.
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
     * PANEL CENTRAL DE GESTIÓN MODULAR (18 Módulos).
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

        // Módulos que ya tienen datos guardados
        $modulosGuardados = MonitoreoModulos::where('cabecera_monitoreo_id', $id)
            ->where('modulo_nombre', '!=', 'config_modulos')
            ->pluck('modulo_nombre')
            ->toArray();

        // Módulos que ya tienen un PDF firmado cargado
        $modulosFirmados = MonitoreoModulos::where('cabecera_monitoreo_id', $id)
            ->whereNotNull('pdf_firmado_path')
            ->pluck('modulo_nombre')
            ->toArray();

        // BUSCAR CONFIGURACIÓN DE INTERRUPTORES
        $config = MonitoreoModulos::where('cabecera_monitoreo_id', $id)
            ->where('modulo_nombre', 'config_modulos')
            ->first();

        /**
         * CAMBIO CRÍTICO:
         * Si no existe configuración ($config es null), devolvemos un array VACÍO [].
         * Esto hará que en la vista todos los módulos aparezcan apagados inicialmente.
         */
        $modulosActivos = $config ? $config->contenido : [];

        return view('usuario.monitoreo.modulos', compact('acta', 'modulosMaster', 'modulosGuardados', 'modulosActivos', 'modulosFirmados'));
    }

    /**
     * AJAX: Guarda la configuración persistente de los interruptores del panel.
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
     * Muestra el resumen de una acta completa (Vista Previa).
     */
    public function show($id)
    {
        $monitoreo = CabeceraMonitoreo::with(['establecimiento', 'equipo', 'user'])->findOrFail($id);

        $detalles = MonitoreoModulos::where('cabecera_monitoreo_id', $id)
            ->where('modulo_nombre', '!=', 'config_modulos')
            ->get();

        return view('usuario.monitoreo.show', compact('monitoreo', 'detalles'));
    }

    /**
     * Eliminar Acta de Monitoreo y sus archivos asociados.
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $monitoreo = CabeceraMonitoreo::findOrFail($id);

            // Eliminar archivos físicos asociados
            $modulos = MonitoreoModulos::where('cabecera_monitoreo_id', $id)->get();
            foreach ($modulos as $m) {
                if ($m->pdf_firmado_path) Storage::disk('public')->delete($m->pdf_firmado_path);
                if (isset($m->contenido['foto_evidencia'])) Storage::disk('public')->delete($m->contenido['foto_evidencia']);
            }

            if ($monitoreo->firmado_pdf) Storage::disk('public')->delete($monitoreo->firmado_pdf);

            $monitoreo->delete();

            DB::commit();
            return redirect()->route('usuario.monitoreo.index')->with('success', 'Acta eliminada correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al eliminar: ' . $e->getMessage());
        }
    }

    /**
     * MOTOR PDF: Generar Acta Consolidada.
     */
    public function generarPDF($id)
    {
        $acta = CabeceraMonitoreo::with(['establecimiento', 'user', 'equipo'])->findOrFail($id);

        $detalles = MonitoreoModulos::where('cabecera_monitoreo_id', $id)
            ->where('modulo_nombre', '!=', 'config_modulos')
            ->get()
            ->keyBy('modulo_nombre');

        return Pdf::loadView('usuario.monitoreo.pdf.acta_consolidada', compact('acta', 'detalles'))
            ->setPaper('a4', 'portrait')
            ->stream("ACTA_CONSOLIDADA_NRO_{$acta->id}.pdf");
    }

    /**
     * SUBIR PDF CONSOLIDADO FIRMADO.
     */
    public function subirPDF(Request $request, $id)
    {
        $request->validate(['pdf_firmado' => 'required|mimes:pdf|max:10240']);
        $monitoreo = CabeceraMonitoreo::where('user_id', Auth::id())->findOrFail($id);

        if ($request->hasFile('pdf_firmado')) {
            if ($monitoreo->firmado_pdf) Storage::disk('public')->delete($monitoreo->firmado_pdf);
            $path = $request->file('pdf_firmado')->store('monitoreos_firmados/consolidados', 'public');
            $monitoreo->update(['firmado_pdf' => $path, 'firmado' => true]);
        }
        return back()->with('success', 'Acta consolidada cargada con éxito.');
    }
}
