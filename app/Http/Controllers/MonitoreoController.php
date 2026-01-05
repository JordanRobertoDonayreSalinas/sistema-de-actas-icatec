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

        $countCompletados = (clone $query)->where('firmado', 1)->count();
        $implementadores = CabeceraMonitoreo::distinct()->pluck('implementador');
        $provincias = Establecimiento::distinct()->pluck('provincia');

        return view('usuario.monitoreo.index', compact('monitoreos', 'countCompletados', 'implementadores', 'provincias'));
    }

    public function create()
    {
        return view('usuario.monitoreo.create');
    }

    /**
     * AJAX: Buscador inteligente para equipo de monitoreo.
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
     * GUARDAR PASO 1: Cabecera, Integrantes e Imágenes.
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
            'imagenes.*' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
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

            if ($request->hasFile('imagenes')) {
                $files = $request->file('imagenes');
                if (isset($files[0])) { $monitoreo->foto1 = $files[0]->store('evidencias', 'public'); }
                if (isset($files[1])) { $monitoreo->foto2 = $files[1]->store('evidencias', 'public'); }
            }

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
            return redirect()->route('usuario.monitoreo.modulos', $monitoreo->id)->with('success', 'Acta iniciada correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors('Error: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * ACTUALIZAR PASO 1 Y EVIDENCIAS.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'establecimiento_id' => 'required|exists:establecimientos,id',
            'fecha' => 'required|date',
            'responsable' => 'required|string|max:255',
            'equipo' => 'required|array|min:1',
            'imagenes.*' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
        ]);

        try {
            DB::beginTransaction();
            $monitoreo = CabeceraMonitoreo::findOrFail($id);
            
            // Actualizar datos de cabecera
            $monitoreo->fecha = $request->fecha;
            $monitoreo->establecimiento_id = $request->establecimiento_id;
            $monitoreo->responsable = mb_strtoupper(trim($request->responsable), 'UTF-8');
            $monitoreo->categoria_congelada = mb_strtoupper(trim($request->categoria), 'UTF-8');
            $monitoreo->implementador = mb_strtoupper(trim($request->implementador), 'UTF-8');

            // Procesar imágenes (solo si se suben nuevas)
            if ($request->hasFile('imagenes')) {
                $files = $request->file('imagenes');
                if (isset($files[0])) {
                    if ($monitoreo->foto1) Storage::disk('public')->delete($monitoreo->foto1);
                    $monitoreo->foto1 = $files[0]->store('evidencias', 'public');
                }
                if (isset($files[1])) {
                    if ($monitoreo->foto2) Storage::disk('public')->delete($monitoreo->foto2);
                    $monitoreo->foto2 = $files[1]->store('evidencias', 'public');
                }
            }

            $monitoreo->save();

            // Sincronizar equipo (Eliminar y volver a crear)
            MonitoreoEquipo::where('cabecera_monitoreo_id', $id)->delete();
            foreach ($request->equipo as $persona) {
                if (!empty($persona['doc'])) {
                    MonitoreoEquipo::create([
                        'cabecera_monitoreo_id' => $monitoreo->id,
                        'tipo_doc'              => $persona['tipo_doc'] ?? 'DNI',
                        'doc'                   => trim($persona['doc']),
                        'apellido_paterno'      => mb_strtoupper(trim($persona['apellido_paterno']), 'UTF-8'),
                        'apellido_materno'      => mb_strtoupper(trim($persona['apellido_materno']), 'UTF-8'),
                        'nombres'               => mb_strtoupper(trim($persona['nombres']), 'UTF-8'),
                        'cargo'                 => mb_strtoupper(trim($persona['cargo']), 'UTF-8'),
                        'institucion'           => mb_strtoupper(trim($persona['institucion']), 'UTF-8'),
                    ]);
                }
            }

            DB::commit();

            if ($request->redirect_to === 'modulos') {
                return redirect()->route('usuario.monitoreo.modulos', $monitoreo->id)->with('success', 'Cabecera actualizada.');
            }
            return redirect()->route('usuario.monitoreo.index')->with('success', 'Acta actualizada correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors('Error: ' . $e->getMessage())->withInput();
        }
    }

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

        $modulosGuardados = MonitoreoModulos::where('cabecera_monitoreo_id', $id)->where('modulo_nombre', '!=', 'config_modulos')->pluck('modulo_nombre')->toArray();
        $modulosFirmados = MonitoreoModulos::where('cabecera_monitoreo_id', $id)->whereNotNull('pdf_firmado_path')->pluck('modulo_nombre')->toArray();
        $config = MonitoreoModulos::where('cabecera_monitoreo_id', $id)->where('modulo_nombre', 'config_modulos')->first();
        $modulosActivos = $config ? $config->contenido : [];

        return view('usuario.monitoreo.modulos', compact('acta', 'modulosMaster', 'modulosGuardados', 'modulosActivos', 'modulosFirmados'));
    }

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

    public function show($id)
    {
        $monitoreo = CabeceraMonitoreo::with(['establecimiento', 'equipo', 'user'])->findOrFail($id);
        $detalles = MonitoreoModulos::where('cabecera_monitoreo_id', $id)->where('modulo_nombre', '!=', 'config_modulos')->get();
        return view('usuario.monitoreo.show', compact('monitoreo', 'detalles'));
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $monitoreo = CabeceraMonitoreo::findOrFail($id);
            if ($monitoreo->foto1) Storage::disk('public')->delete($monitoreo->foto1);
            if ($monitoreo->foto2) Storage::disk('public')->delete($monitoreo->foto2);

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

    public function generarPDF($id)
    {
        $acta = CabeceraMonitoreo::with(['establecimiento', 'user', 'equipo'])->findOrFail($id);
        $detalles = MonitoreoModulos::where('cabecera_monitoreo_id', $id)->where('modulo_nombre', '!=', 'config_modulos')->get()->keyBy('modulo_nombre');
        return Pdf::loadView('usuario.monitoreo.pdf.acta_consolidada', compact('acta', 'detalles'))->setPaper('a4', 'portrait')->stream("ACTA_CONSOLIDADA_NRO_{$acta->id}.pdf");
    }

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