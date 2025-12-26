<?php

namespace App\Http\Controllers;

use App\Models\Acta;
use App\Models\Establecimiento;
use App\Models\User;
use App\Models\MonitoreoDetalle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class MonitoreoController extends Controller
{
    /**
     * Listado principal de monitoreos del usuario.
     */
    public function index(Request $request)
    {
        $query = Acta::with('establecimiento')
                    ->where('user_id', Auth::id())
                    ->where('tipo', 'monitoreo');

        if ($request->filled('implementador')) {
            $query->where('implementador', $request->input('implementador'));
        }
        
        if ($request->filled('provincia')) {
            $query->whereHas('establecimiento', function ($q) use ($request) {
                $q->where('provincia', $request->input('provincia'));
            });
        }

        $monitoreos = $query->orderByDesc('id')->paginate(10)->appends($request->query());

        $countCompletados = (clone $query)->where('firmado', 1)->count();
        $implementadores = Acta::where('tipo', 'monitoreo')->distinct()->pluck('implementador');
        $provincias = Establecimiento::distinct()->pluck('provincia');

        return view('usuario.monitoreo.index', compact('monitoreos', 'countCompletados', 'implementadores', 'provincias'));
    }

    /**
     * PASO 1: Formulario de creación (Cabecera y Participantes).
     */
    public function create()
    {
        $usuariosRegistrados = User::where('status', 'active')->orderBy('apellido_paterno')->get();
        return view('usuario.monitoreo.create', compact('usuariosRegistrados'));
    }

    /**
     * GUARDAR PASO 1: Procesa la cabecera y redirige a la gestión modular.
     */
    public function store(Request $request)
    {
        $request->validate([
            'establecimiento_id' => 'required|exists:establecimientos,id',
            'fecha' => 'required|date',
            'responsable' => 'required|string|max:255',
            'implementador' => 'required|string',
            'participantes.*.dni' => 'nullable|digits:8',
        ]);

        try {
            DB::beginTransaction();

            $acta = new Acta();
            $acta->fecha = $request->fecha;
            $acta->establecimiento_id = $request->establecimiento_id;
            $acta->responsable = $request->responsable;
            $acta->implementador = $request->implementador;
            
            $acta->tema = 'Monitoreo de Servicios';
            $acta->modalidad = 'Presencial';
            
            $acta->user_id = Auth::id();
            $acta->tipo = 'monitoreo'; 
            $acta->save();

            if ($request->has('participantes')) {
                foreach ($request->participantes as $p) {
                    if (!empty($p['dni']) || !empty($p['nombres'])) {
                        $acta->participantes()->create($p);
                    }
                }
            }

            if ($request->hasFile('imagenes')) {
                foreach ($request->file('imagenes') as $index => $file) {
                    if ($index < 5) {
                        $campo = 'imagen' . ($index + 1);
                        $acta->$campo = $file->store('evidencias/monitoreo', 'public');
                    }
                }
                $acta->save();
            }

            DB::commit();

            return redirect()->route('usuario.monitoreo.modulos', $acta->id)
                             ->with('success', 'Cabecera guardada. Proceda con los módulos de evaluación.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors('Error al guardar: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * PASO 2: Panel General de Módulos.
     */
    public function gestionarModulos($id)
    {
        $acta = Acta::with(['establecimiento', 'participantes'])
                    ->where('user_id', Auth::id())
                    ->findOrFail($id);

        $modulos = [
            'programacion'   => 'Programación de consultorios y turnos',
            'ventanilla'     => 'Ventanilla única',
            'caja'           => 'Caja',
            'triaje'         => 'Triaje',
            'medicina'       => 'Consulta Externa: Medicina',
            'cred'           => 'Control de Crecimiento y Desarrollo',
            'inmunizaciones' => 'Inmunizaciones',
            'prenatal'       => 'Atención Prenatal',
        ];

        $modulosGuardados = MonitoreoDetalle::where('acta_id', $id)
                                          ->pluck('modulo_nombre')
                                          ->toArray();

        return view('usuario.monitoreo.modulos', compact('acta', 'modulos', 'modulosGuardados'));
    }

    /**
     * REDIRECCIÓN A MÓDULOS INDEPENDIENTES:
     * Carga el archivo blade específico para cada sección.
     */
    public function cargarSeccionModulo($id, $seccion)
    {
        $acta = Acta::with('establecimiento')->findOrFail($id);

        // Mapeo de slugs a rutas de archivos blade
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

        if (!array_key_exists($seccion, $vistas)) {
            abort(404, "El módulo solicitado no existe.");
        }

        return view($vistas[$seccion], compact('acta', 'seccion'));
    }

    /**
     * Guardar el detalle JSON de cada módulo individual.
     */
    public function guardarDetalle(Request $request, $id)
    {
        $request->validate([
            'modulo_nombre' => 'required|string',
            'contenido' => 'required|array'
        ]);

        try {
            MonitoreoDetalle::updateOrCreate(
                [
                    'acta_id' => $id, 
                    'modulo_nombre' => $request->modulo_nombre
                ],
                [
                    'contenido' => $request->contenido 
                ]
            );

            // Después de guardar, redirigimos de vuelta al panel principal de módulos
            return redirect()->route('usuario.monitoreo.modulos', $id)
                             ->with('success', 'Información del módulo guardada correctamente.');

        } catch (\Exception $e) {
            return back()->withErrors('Error al guardar el módulo: ' . $e->getMessage());
        }
    }

    /**
     * Generar PDF Consolidado.
     */
    public function generarPDF($id)
    {
        $acta = Acta::with(['establecimiento', 'participantes', 'user'])
                    ->where('tipo', 'monitoreo')
                    ->findOrFail($id);
        
        $detalles = MonitoreoDetalle::where('acta_id', $id)->get();
        
        $pdf = Pdf::loadView('usuario.monitoreo.pdf', compact('acta', 'detalles'))
                  ->setPaper('a4', 'portrait');
                  
        return $pdf->stream("acta_monitoreo_{$acta->id}.pdf");
    }

    /**
     * Subida de PDF Final Firmado.
     */
    public function subirPDF(Request $request, $id)
    {
        $request->validate(['pdf_firmado' => 'required|mimes:pdf|max:10240']);
        $acta = Acta::where('user_id', Auth::id())->findOrFail($id);

        if ($request->hasFile('pdf_firmado')) {
            if ($acta->firmado_pdf) { 
                Storage::disk('public')->delete($acta->firmado_pdf); 
            }
            $path = $request->file('pdf_firmado')->store('actas_firmadas/monitoreo', 'public');
            $acta->update(['firmado_pdf' => $path, 'firmado' => true]);
        }

        return back()->with('success', 'Archivo firmado subido correctamente.');
    }
}