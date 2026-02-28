<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Acta;
use App\Models\Establecimiento;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ActaController extends Controller
{
    public function index(Request $request)
    {
        $query = Acta::with('establecimiento');

        if ($request->filled('implementador')) {
            $query->where('implementador', $request->input('implementador'));
        }
        if ($request->filled('provincia')) {
            $query->whereHas('establecimiento', function ($q) use ($request) {
                $q->where('provincia', $request->input('provincia'));
            });
        }
        if ($request->filled('distrito')) {
            $query->whereHas('establecimiento', function ($q) use ($request) {
                $q->where('distrito', $request->input('distrito'));
            });
        }
        if ($request->filled('establecimiento_id')) {
            $query->where('establecimiento_id', $request->input('establecimiento_id'));
        }
        if ($request->filled('firmado')) {
            $val = $request->input('firmado');
            if ($val == '1') {
                $query->where('firmado', 1);
            } else {
                $query->where(function ($q) {
                    $q->where('firmado', 0)->orWhereNull('firmado');
                });
            }
        }

        // Fechas por defecto: primer día del año actual y hoy
        $fechaInicioDefault = Carbon::now()->startOfYear()->format('Y-m-d');
        $fechaFinDefault = Carbon::now()->format('Y-m-d');

        $valInicio = $request->input('fecha_inicio', $fechaInicioDefault);
        $valFin = $request->input('fecha_fin', $fechaFinDefault);

        $query->whereDate('fecha', '>=', $valInicio);
        $query->whereDate('fecha', '<=', $valFin);

        $countFirmadas = (clone $query)->where('firmado', 1)->count();
        $countPendientes = (clone $query)->where(function ($q) {
            $q->where('firmado', 0)->orWhereNull('firmado');
        })->count();

        $actas = $query->orderByDesc('id')->paginate(10)->appends($request->query());
        $implementadores = Acta::distinct()->orderBy('implementador')->pluck('implementador');

        // Cargar provincias solo si tienen actas
        $provincias = Establecimiento::whereHas('actas')->distinct()->orderBy('provincia')->pluck('provincia');

        // Cargar distritos si hay provincia
        $distritos = collect();
        if ($request->filled('provincia')) {
            $distritos = Establecimiento::whereHas('actas')
                ->where('provincia', $request->provincia)
                ->distinct()
                ->orderBy('distrito')
                ->pluck('distrito');
        }

        // Cargar establecimientos si hay distrito
        $establecimientos = collect();
        if ($request->filled('distrito')) {
            $establecimientos = Establecimiento::whereHas('actas')
                ->where('distrito', $request->distrito)
                ->orderBy('nombre')
                ->get(['id', 'nombre']);
        }

        return view('usuario.asistencia.index', compact(
            'actas',
            'implementadores',
            'provincias',
            'distritos',
            'establecimientos',
            'countFirmadas',
            'countPendientes',
            'valInicio',
            'valFin'
        ));
    }

    public function create()
    {
        $usuariosRegistrados = User::where('status', 'active')->orderBy('apellido_paterno')->get();
        return view('usuario.asistencia.create', compact('usuariosRegistrados'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'fecha' => 'required|date',
            'establecimiento_id' => 'required|exists:establecimientos,id',
            'responsable' => 'required|string',
            'tema' => 'required',
            'modalidad' => 'required',
            'implementador' => 'required',
            'imagenes.*' => 'image|mimes:jpeg,png,jpg|max:2048'
        ]);

        try {
            DB::beginTransaction();

            $acta = Acta::create($request->only([
                'fecha',
                'establecimiento_id',
                'responsable',
                'tema',
                'modalidad',
                'implementador'
            ]));

            if ($request->hasFile('imagenes')) {
                foreach ($request->file('imagenes') as $index => $file) {
                    if ($index < 5) {
                        $campo = 'imagen' . ($index + 1);
                        $acta->$campo = $file->store('evidencias', 'public');
                    }
                }
                $acta->save();
            }

            if ($request->has('participantes')) {
                foreach ($request->participantes as $p) {
                    $acta->participantes()->create($p);
                }
            }
            if ($request->has('actividades')) {
                foreach ($request->actividades as $a) {
                    $acta->actividades()->create($a);
                }
            }
            if ($request->has('acuerdos')) {
                foreach ($request->acuerdos as $ac) {
                    $acta->acuerdos()->create($ac);
                }
            }
            if ($request->has('observaciones')) {
                foreach ($request->observaciones as $obs) {
                    $acta->observaciones()->create($obs);
                }
            }

            DB::commit();
            return redirect()->route('usuario.actas.index')->with('success', 'Acta guardada exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors('Error al guardar: ' . $e->getMessage())->withInput();
        }
    }

    public function edit($id)
    {
        $acta = Acta::with(['establecimiento', 'participantes', 'actividades', 'acuerdos', 'observaciones'])->findOrFail($id);
        $usuariosRegistrados = User::where('status', 'active')->orderBy('apellido_paterno')->get();
        return view('usuario.asistencia.edit', compact('acta', 'usuariosRegistrados'));
    }

    public function update(Request $request, $id)
    {
        $acta = Acta::findOrFail($id);
        $request->validate([
            'fecha' => 'required|date',
            'establecimiento_id' => 'required|exists:establecimientos,id',
            'imagenes.*' => 'image|mimes:jpeg,png,jpg|max:2048'
        ]);

        try {
            DB::beginTransaction();
            $acta->update($request->only(['fecha', 'establecimiento_id', 'responsable', 'tema', 'modalidad', 'implementador']));

            if ($request->has('eliminar_imagenes')) {
                foreach ($request->eliminar_imagenes as $campo) {
                    if ($acta->$campo) {
                        Storage::disk('public')->delete($acta->$campo);
                        $acta->$campo = null;
                    }
                }
            }

            if ($request->hasFile('imagenes')) {
                $nuevosArchivos = $request->file('imagenes');
                $archivoIndex = 0;
                for ($i = 1; $i <= 5; $i++) {
                    $campo = 'imagen' . $i;
                    if (is_null($acta->$campo) && isset($nuevosArchivos[$archivoIndex])) {
                        $path = $nuevosArchivos[$archivoIndex]->store('evidencias', 'public');
                        $acta->$campo = $path;
                        $archivoIndex++;
                    }
                }
            }
            $acta->save();

            $acta->participantes()->delete();
            if ($request->has('participantes')) {
                foreach ($request->participantes as $p) {
                    $acta->participantes()->create($p);
                }
            }
            $acta->actividades()->delete();
            if ($request->has('actividades')) {
                foreach ($request->actividades as $a) {
                    $acta->actividades()->create($a);
                }
            }
            $acta->acuerdos()->delete();
            if ($request->has('acuerdos')) {
                foreach ($request->acuerdos as $ac) {
                    $acta->acuerdos()->create($ac);
                }
            }
            $acta->observaciones()->delete();
            if ($request->has('observaciones')) {
                foreach ($request->observaciones as $obs) {
                    $acta->observaciones()->create($obs);
                }
            }

            DB::commit();
            return redirect()->route('usuario.actas.index')->with('success', 'Acta actualizada correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors('Error al actualizar: ' . $e->getMessage())->withInput();
        }
    }

    public function show($id)
    {
        $acta = Acta::with(['establecimiento', 'participantes', 'actividades', 'acuerdos', 'observaciones'])->findOrFail($id);
        return view('usuario.asistencia.show', compact('acta'));
    }

    public function generarPDF($id)
    {
        $acta = Acta::with(['establecimiento', 'participantes', 'actividades', 'acuerdos', 'observaciones'])->findOrFail($id);
        $pdf = Pdf::loadView('usuario.asistencia.pdf', compact('acta'))->setPaper('a4', 'portrait');
        return $pdf->stream("acta_{$acta->id}.pdf");
    }

    public function subirPDF(Request $request, $id)
    {
        $request->validate(['pdf_firmado' => 'required|mimes:pdf|max:20480']);
        $acta = Acta::findOrFail($id);
        if ($acta->firmado_pdf && Storage::disk('public')->exists($acta->firmado_pdf)) {
            Storage::disk('public')->delete($acta->firmado_pdf);
        }
        $path = $request->file('pdf_firmado')->store('actas_firmadas', 'public');
        $acta->update(['firmado_pdf' => $path, 'firmado' => true]);
        return redirect()->back()->with('success', '✅ El acta firmada fue subida correctamente.');
    }

    public function ajaxGetDistritos(Request $request)
    {
        $query = Establecimiento::whereHas('actas');
        if ($request->filled('provincia')) {
            $query->where('provincia', $request->provincia);
        }
        $distritos = $query->distinct()->pluck('distrito')->filter()->sort()->values();
        return response()->json($distritos);
    }

    public function ajaxGetEstablecimientos(Request $request)
    {
        $query = Establecimiento::whereHas('actas');
        if ($request->filled('provincia')) {
            $query->where('provincia', $request->provincia);
        }
        if ($request->filled('distrito')) {
            $query->where('distrito', $request->distrito);
        }
        $establecimientos = $query->orderBy('nombre', 'asc')->get(['id', 'nombre']);
        return response()->json($establecimientos);
    }
}