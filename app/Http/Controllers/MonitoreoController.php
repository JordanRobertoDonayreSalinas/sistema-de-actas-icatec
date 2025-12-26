<?php

namespace App\Http\Controllers;

use App\Models\Monitoreo; 
use App\Models\Establecimiento;
use App\Models\User;
use App\Models\MonitoreoDetalle; 
use App\Models\MonitoreoProgramacion; 
use App\Models\MonitoreoEquipo; // NUEVO: Importamos el modelo de equipo
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
        $query = Monitoreo::with('establecimiento')
                    ->where('user_id', Auth::id());

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
        $implementadores = Monitoreo::distinct()->pluck('implementador');
        $provincias = Establecimiento::distinct()->pluck('provincia');

        return view('usuario.monitoreo.index', compact('monitoreos', 'countCompletados', 'implementadores', 'provincias'));
    }

    /**
     * Formulario de creación (Paso 1).
     */
    public function create()
    {
        $usuariosRegistrados = User::where('status', 'active')
                                    ->orderBy('apellido_paterno')
                                    ->get();
        return view('usuario.monitoreo.create', compact('usuariosRegistrados'));
    }

    /**
     * GUARDAR PASO 1: Crea la cabecera y el equipo de trabajo.
     */
    public function store(Request $request)
    {
        $request->validate([
            'establecimiento_id' => 'required|exists:establecimientos,id',
            'fecha' => 'required|date',
            'responsable' => 'required|string|max:255',
            'implementador' => 'required|string',
            'equipo' => 'nullable|array', // Validación para el equipo de trabajo
        ]);

        try {
            DB::beginTransaction();

            // 1. Crear cabecera
            $monitoreo = new Monitoreo();
            $monitoreo->fecha = $request->fecha;
            $monitoreo->establecimiento_id = $request->establecimiento_id;
            $monitoreo->responsable = $request->responsable;
            $monitoreo->implementador = $request->implementador;
            $monitoreo->user_id = Auth::id();
            $monitoreo->save();

            // 2. Guardar Equipo de Trabajo (Tabla Independiente)
            if ($request->has('equipo')) {
                foreach ($request->equipo as $persona) {
                    MonitoreoEquipo::create([
                        'monitoreo_id'    => $monitoreo->id,
                        'user_id'         => $persona['id'],
                        'nombre_completo' => $persona['nombre'],
                        'cargo'           => $persona['cargo'] ?? 'Implementador',
                        'institucion'     => $persona['institucion'] ?? 'DIRESA',
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('usuario.monitoreo.modulos', $monitoreo->id)
                             ->with('success', 'Cabecera y equipo de trabajo registrados.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors('Error al crear el monitoreo: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * PANEL DE MÓDULOS: Detecta el progreso de cada tabla independiente.
     */
    public function gestionarModulos($id)
    {
        $acta = Monitoreo::with(['establecimiento', 'programacion', 'equipo']) 
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

        $modulosGuardados = [];
        if ($acta->programacion) $modulosGuardados[] = 'programacion';

        return view('usuario.monitoreo.modulos', compact('acta', 'modulos', 'modulosGuardados'));
    }

    /**
     * GUARDAR DETALLE: Procesa el array 'contenido' hacia la tabla correspondiente.
     */
    public function guardarDetalle(Request $request, $id)
    {
        $modulo = $request->input('modulo_nombre');
        $datos = $request->input('contenido');

        try {
            if ($modulo === 'programacion') {
                MonitoreoProgramacion::updateOrCreate(
                    ['monitoreo_id' => $id],
                    $datos
                );
            } else {
                MonitoreoDetalle::updateOrCreate(
                    ['monitoreo_id' => $id, 'modulo_nombre' => $modulo],
                    ['contenido' => $datos]
                );
            }

            return redirect()->route('usuario.monitoreo.modulos', $id)
                             ->with('success', "Datos del componente $modulo guardados.");

        } catch (\Exception $e) {
            return back()->withErrors('Error al guardar: ' . $e->getMessage());
        }
    }

    /**
     * Carga la vista de cada sección.
     */
    public function cargarSeccionModulo($id, $seccion)
    {
        $acta = Monitoreo::with(['establecimiento', $seccion])->findOrFail($id);
        
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

        return view($vistas[$seccion], compact('acta', 'seccion'));
    }

    /**
     * GENERAR PDF CONSOLIDADO
     */
    public function generarPDF($id)
    {
        // Cargamos el monitoreo con Equipo de Trabajo y Programación
        $acta = Monitoreo::with([
            'establecimiento', 
            'user', 
            'programacion',
            'equipo' // Cargamos la relación del equipo para el PDF
        ])->findOrFail($id);

        $logoPath = public_path('img/logo.png'); 
        $logoBase64 = '';
        if (file_exists($logoPath)) {
            $logoData = base64_encode(file_get_contents($logoPath));
            $logoBase64 = 'data:image/png;base64,' . $logoData;
        }

        $pdf = Pdf::loadView('usuario.monitoreo.pdf', compact('acta', 'logoBase64'))
                  ->setPaper('a4', 'portrait');

        $filename = "MONITOREO_" . str_replace(' ', '_', $acta->establecimiento->nombre) . "_" . $acta->fecha . ".pdf";

        return $pdf->stream($filename);
    }

    /**
     * Subida de PDF firmado.
     */
    public function subirPDF(Request $request, $id)
    {
        $request->validate(['pdf_firmado' => 'required|mimes:pdf|max:10240']);
        $monitoreo = Monitoreo::where('user_id', Auth::id())->findOrFail($id);

        if ($request->hasFile('pdf_firmado')) {
            if ($monitoreo->firmado_pdf) Storage::disk('public')->delete($monitoreo->firmado_pdf);
            $path = $request->file('pdf_firmado')->store('monitoreos_firmados', 'public');
            
            $monitoreo->update([
                'firmado_pdf' => $path, 
                'firmado' => true
            ]);
        }

        return back()->with('success', 'El documento firmado ha sido cargado.');
    }
}