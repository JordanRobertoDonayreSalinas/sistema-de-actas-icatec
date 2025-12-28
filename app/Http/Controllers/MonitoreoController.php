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
     * Listado principal de monitoreos con filtros mejorados.
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

    public function create()
    {
        return view('usuario.monitoreo.create');
    }

    public function buscarFiltro(Request $request)
    {
        $term = $request->term;
        $equipo = MonitoreoEquipo::where('doc', 'LIKE', "%$term%")
                    ->orWhere('apellido_paterno', 'LIKE', "%$term%")
                    ->limit(10)
                    ->get();
                    
        return response()->json($equipo);
    }

    public function buscarMiembroEquipo($doc)
    {
        try {
            $miembro = MonitoreoEquipo::where('doc', $doc)->first();
            
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
     * GUARDAR PASO 1: Crea cabecera, actualiza maestro IPRESS y registra equipo.
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

            // 1. ACTUALIZAR TABLA MAESTRA DE ESTABLECIMIENTOS (Para sugerir en futuras actas)
            $establecimiento = Establecimiento::findOrFail($request->establecimiento_id);
            $establecimiento->update([
                'responsable' => strtoupper($request->responsable),
                'categoria'   => strtoupper($request->categoria),
            ]);

            // 2. CREAR CABECERA DEL ACTA CON DATOS HISTÓRICOS (SNAPSHOT)
            $monitoreo = new CabeceraMonitoreo();
            $monitoreo->fecha = $request->fecha;
            $monitoreo->establecimiento_id = $request->establecimiento_id;
            
            // Guardamos los datos actuales para el histórico de esta acta específica
            // Asegúrate de que estos campos existan en tu tabla 'mon_cabecera_monitoreo'
            $monitoreo->responsable = strtoupper($request->responsable);
            $monitoreo->categoria_congelada = strtoupper($request->categoria); // Campo histórico
            
            $monitoreo->implementador = $request->implementador; 
            $monitoreo->user_id = Auth::id();
            $monitoreo->save();

            // 3. PROCESAR EQUIPO DE TRABAJO
            foreach ($request->equipo as $persona) {
                if (!empty($persona['doc'])) {
                    MonitoreoEquipo::updateOrCreate(
                        ['doc' => $persona['doc']], 
                        [
                            'cabecera_monitoreo_id' => $monitoreo->id, 
                            'tipo_doc'              => $persona['tipo_doc'] ?? 'DNI',
                            'apellido_paterno'      => strtoupper($persona['apellido_paterno']),
                            'apellido_materno'      => strtoupper($persona['apellido_materno']),
                            'nombres'               => strtoupper($persona['nombres']),
                            'cargo'                 => strtoupper($persona['cargo'] ?? 'MONITOR'),
                            'institucion'           => strtoupper($persona['institucion'] ?? 'DIRESA'),
                        ]
                    );
                }
            }

            DB::commit();

            return redirect()->route('usuario.monitoreo.modulos', $monitoreo->id)
                             ->with('success', 'Acta iniciada y datos del establecimiento actualizados.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors('Error: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * PANEL DE MÓDULOS.
     */
    public function gestionarModulos($id)
    {
        $acta = CabeceraMonitoreo::with(['establecimiento', 'equipo']) 
                    ->where('user_id', Auth::id())
                    ->findOrFail($id);

        $modulos = [
            'programacion'   => '01. Programación de consultorios y turnos',
            'ventanilla'     => '02. Ventanilla única',
            'caja'           => '03. Caja',
            'triaje'         => '04. Triaje',
            'medicina'       => '05. Consulta Externa: Medicina',
            'cred'           => '06. Control de Crecimiento y Desarrollo',
            'inmunizaciones' => '07. Inmunizaciones',
            'prenatal'       => '08. Atención Prenatal',
        ];

        $modulosGuardados = MonitoreoModulos::where('cabecera_monitoreo_id', $id)
                            ->pluck('modulo_nombre')
                            ->toArray();

        return view('usuario.monitoreo.modulos', compact('acta', 'modulos', 'modulosGuardados'));
    }

    /**
     * GUARDAR DETALLE.
     */
    public function guardarDetalle(Request $request, $id)
    {
        $modulo = $request->input('modulo_nombre');
        $datos = $request->input('contenido');

        if (!$modulo) {
            return back()->withErrors('No se especificó el nombre del módulo.');
        }

        try {
            MonitoreoModulos::updateOrCreate(
                ['cabecera_monitoreo_id' => $id, 'modulo_nombre' => $modulo],
                ['contenido' => $datos]
            );

            return redirect()->route('usuario.monitoreo.modulos', $id)
                             ->with('success', "Datos del módulo guardados correctamente.");

        } catch (\Exception $e) {
            return back()->withErrors('Error: ' . $e->getMessage());
        }
    }

    public function cargarSeccionModulo($id, $seccion)
    {
        $acta = CabeceraMonitoreo::with(['establecimiento'])->findOrFail($id);
        
        $vistas = [
            'programacion'   => 'usuario.monitoreo.modulos.programacion',
            'ventanilla'     => 'usuario.monitoreo.modulos.ventanilla',
            'caja'           => 'usuario.monitoreo.modulos.caja',
            'triaje'         => 'usuario.monitoreo.modulos.triaje',
            'medicina'       => 'usuario.monitoreo.modulos.medicina',
            'cred'           => 'usuario.monitoreo.modulos.cred',
            'inmunizaciones' => 'usuario.monitoreo.modulos.inmunizaciones',
            'prenatal'       => 'usuario.monitoreo.modulos.prenatal',
        ];

        if (!array_key_exists($seccion, $vistas)) abort(404);

        $detalle = MonitoreoModulos::where('cabecera_monitoreo_id', $id)
                    ->where('modulo_nombre', $seccion)
                    ->first();

        return view($vistas[$seccion], compact('acta', 'seccion', 'detalle'));
    }

    public function generarPDF($id)
    {
        $acta = CabeceraMonitoreo::with(['establecimiento', 'user', 'equipo'])->findOrFail($id);

        $logoPath = public_path('img/logo.png'); 
        $logoBase64 = '';
        if (file_exists($logoPath)) {
            $logoData = base64_encode(file_get_contents($logoPath));
            $logoBase64 = 'data:image/png;base64,' . $logoData;
        }

        $pdf = Pdf::loadView('usuario.monitoreo.pdf', compact('acta', 'logoBase64'))
                  ->setPaper('a4', 'portrait');

        return $pdf->stream("ACTA_MONITOREO_{$acta->id}.pdf");
    }

    public function subirPDF(Request $request, $id)
    {
        $request->validate(['pdf_firmado' => 'required|mimes:pdf|max:10240']);
        $monitoreo = CabeceraMonitoreo::where('user_id', Auth::id())->findOrFail($id);

        if ($request->hasFile('pdf_firmado')) {
            if ($monitoreo->firmado_pdf) {
                Storage::disk('public')->delete($monitoreo->firmado_pdf);
            }
            $path = $request->file('pdf_firmado')->store('monitoreos_firmados', 'public');
            
            $monitoreo->update([
                'firmado_pdf' => $path, 
                'firmado' => true
            ]);
        }

        return back()->with('success', 'Archivo cargado con éxito.');
    }
}