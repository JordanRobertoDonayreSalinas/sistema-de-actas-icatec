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
        if ($request->filled('firmado')) {
            $val = $request->input('firmado');
            $val == '1' ? $query->where('firmado', 1) : $query->where('firmado', 0)->orWhereNull('firmado');
        }
        if ($request->filled('fecha_inicio')) {
            $query->whereDate('fecha', '>=', $request->input('fecha_inicio'));
        }
        if ($request->filled('fecha_fin')) {
            $query->whereDate('fecha', '<=', $request->input('fecha_fin'));
        }

        $countFirmadas = (clone $query)->where('firmado', 1)->count();
        $countPendientes = (clone $query)->where(function ($q) {
            $q->where('firmado', 0)->orWhereNull('firmado');
        })->count();

        $actas = $query->orderByDesc('id')->paginate(10)->appends($request->query());
        $implementadores = Acta::distinct()->orderBy('implementador')->pluck('implementador');
        $provincias = Establecimiento::distinct()->orderBy('provincia')->pluck('provincia');

        return view('usuario.asistencia.index', compact('actas', 'implementadores', 'provincias', 'countFirmadas', 'countPendientes'));
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
                'fecha', 'establecimiento_id', 'responsable', 'tema', 'modalidad', 'implementador'
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
                foreach ($request->participantes as $p) { $acta->participantes()->create($p); }
            }
            if ($request->has('actividades')) {
                foreach ($request->actividades as $a) { $acta->actividades()->create($a); }
            }
            if ($request->has('acuerdos')) {
                foreach ($request->acuerdos as $ac) { $acta->acuerdos()->create($ac); }
            }
            if ($request->has('observaciones')) {
                foreach ($request->observaciones as $obs) { $acta->observaciones()->create($obs); }
            }

            DB::commit();
            return redirect()->route('usuario.actas.index')->with('success', 'Acta guardada exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors('Error al guardar: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * MÉTODO EDIT: Carga el acta y la lista de usuarios para el selector
     */
    public function edit($id)
    {
        $acta = Acta::with(['establecimiento','participantes','actividades','acuerdos','observaciones'])->findOrFail($id);
        
        // CORRECCIÓN: Buscamos los usuarios para que el selector del Implementador no falle
        $usuariosRegistrados = User::where('status', 'active')
            ->orderBy('apellido_paterno', 'asc')
            ->get();

        return view('usuario.asistencia.edit', compact('acta', 'usuariosRegistrados'));
    }

    /**
     * MÉTODO UPDATE: Maneja el sistema de slots para no perder imágenes
     */
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
            
            // 1. Actualizar campos básicos
            $acta->update($request->only(['fecha', 'establecimiento_id', 'responsable', 'tema', 'modalidad', 'implementador']));

            // 2. BORRADO SELECTIVO: Limpiar slots marcados por el usuario
            if ($request->has('eliminar_imagenes')) {
                foreach ($request->eliminar_imagenes as $campo) {
                    if ($acta->$campo) {
                        Storage::disk('public')->delete($acta->$campo);
                        $acta->$campo = null; // Liberamos el slot
                    }
                }
            }

            // 3. CARGA EN SLOTS VACÍOS: Rellenar huecos disponibles con fotos nuevas
            if ($request->hasFile('imagenes')) {
                $nuevosArchivos = $request->file('imagenes');
                $archivoIndex = 0;

                // Recorremos los 5 slots buscando cuáles están libres (null)
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

            // 4. RELACIONES: Limpieza y Re-inserción (Evita duplicados y desorden)
            $acta->participantes()->delete();
            if ($request->has('participantes')) {
                foreach ($request->participantes as $p) { $acta->participantes()->create($p); }
            }

            $acta->actividades()->delete();
            if ($request->has('actividades')) {
                foreach ($request->actividades as $a) { $acta->actividades()->create($a); }
            }

            $acta->acuerdos()->delete();
            if ($request->has('acuerdos')) {
                foreach ($request->acuerdos as $ac) { $acta->acuerdos()->create($ac); }
            }

            $acta->observaciones()->delete();
            if ($request->has('observaciones')) {
                foreach ($request->observaciones as $obs) { $acta->observaciones()->create($obs); }
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
        $acta = Acta::with(['establecimiento','participantes','actividades','acuerdos','observaciones'])->findOrFail($id);
        return view('usuario.asistencia.show', compact('acta'));
    }

    public function generarPDF($id)
    {
        $acta = Acta::with(['establecimiento','participantes','actividades','acuerdos','observaciones'])->findOrFail($id);
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
}