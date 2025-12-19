<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Acta;
use App\Models\Establecimiento;

class ActaController extends Controller
{
    protected $maxImages = 5;

    public function index(Request $request)
    {
        // Consulta base con relación a establecimiento
        $query = Acta::with('establecimiento');

        // Filtro por implementador (exacto)
        if ($request->filled('implementador')) {
            $query->where('implementador', $request->input('implementador'));
        }

        // Filtro por provincia (relación)
        if ($request->filled('provincia')) {
            $query->whereHas('establecimiento', function ($q) use ($request) {
                $q->where('provincia', $request->input('provincia'));
            });
        }
        
        // Filtro por estado firmado
        if ($request->filled('firmado')) {
            $val = $request->input('firmado');

            if ($val == '1') {
                $query->where('firmado', 1);
            } elseif ($val == '0') {
                $query->where(function ($q) {
                    $q->where('firmado', 0)
                      ->orWhereNull('firmado');
                });
            }
        }
        
        // Rango de fechas
        if ($request->filled('fecha_inicio')) {
            $query->whereDate('fecha', '>=', $request->input('fecha_inicio'));
        }
        if ($request->filled('fecha_fin')) {
            $query->whereDate('fecha', '<=', $request->input('fecha_fin'));
        }

        // 1. Clonamos la consulta CON FILTROS para obtener los conteos
        $queryParaCuentas = $query->clone();

        // 2. Obtenemos los conteos específicos
        $countFirmadas = (clone $queryParaCuentas)->where('firmado', 1)->count();
        $countPendientes = (clone $queryParaCuentas)->where(function ($q) {
            $q->where('firmado', 0)
              ->orWhereNull('firmado');
        })->count();

        // 3. Paginamos la consulta original
        $actas = $query->orderByDesc('id')
                      ->paginate(10)
                      ->appends($request->query());

        // Datos para selects de filtro
        $implementadores = Acta::select('implementador')
            ->whereNotNull('implementador')
            ->distinct()
            ->orderBy('implementador')
            ->pluck('implementador');

        $provincias = Establecimiento::select('provincia')
            ->whereNotNull('provincia')
            ->distinct()
            ->orderBy('provincia')
            ->pluck('provincia');

        // 4. Pasamos las nuevas variables a la vista
        return view('actas.index', compact(
            'actas', 
            'implementadores', 
            'provincias', 
            'countFirmadas', 
            'countPendientes'
        ));
    }

    public function create()
    {
        // Apunta al archivo resources/views/acta_asistencia.blade.php
        return view('acta_asistencia');
    }

    public function store(Request $request)
    {
        $request->validate(array_merge([
            'fecha' => 'required|date',
            'establecimiento_id' => 'required|exists:establecimientos,id',
            'responsable' => 'required|string|max:255',
            'tema' => 'required|string',
            'modalidad' => 'required|string',
            'implementador' => 'required|string',
            'imagenes.*' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ], $this->individualImageValidationRules()));

        // Crear acta con campos básicos
        $acta = new Acta($request->only(['fecha','establecimiento_id','responsable','tema','modalidad','implementador']));

        // Asegurar que exista el directorio 'actas'
        if (!Storage::disk('public')->exists('actas')) {
            Storage::disk('public')->makeDirectory('actas');
        }

        // Procesar imágenes
        $newFiles = $this->collectNewImageFiles($request);
        foreach ($newFiles as $index => $file) {
            if (!$file) continue;
            $i = $index + 1;
            $filename = time() . '_' . uniqid() . "_img{$i}." . $file->getClientOriginalExtension();
            $file->storeAs('actas', $filename, 'public');
            $acta->{'imagen'.$i} = 'actas/' . $filename;
        }

        $acta->save();

        // Guardar participantes
        if ($request->has('participantes')) {
            foreach ($request->participantes as $p) {
                $acta->participantes()->create([
                    'dni' => $p['dni'] ?? null,
                    'apellidos' => $p['apellidos'] ?? null,
                    'nombres' => $p['nombres'] ?? null,
                    'cargo' => $p['cargo'] ?? null,
                    'modulo' => $p['modulo'] ?? null,
                    'unidad_ejecutora' => $p['unidad_ejecutora'] ?? null,
                ]);
            }
        }

        // Actividades
        if ($request->has('actividades')) {
            foreach ($request->actividades as $a) {
                $acta->actividades()->create([
                    'descripcion' => $a['descripcion'] ?? '',
                ]);
            }
        }

        // Acuerdos
        if ($request->has('acuerdos')) {
            foreach ($request->acuerdos as $ac) {
                $acta->acuerdos()->create([
                    'descripcion' => $ac['descripcion'] ?? '',
                ]);
            }
        }

        // Observaciones
        if ($request->has('observaciones')) {
            foreach ($request->observaciones as $o) {
                $acta->observaciones()->create([
                    'descripcion' => $o['descripcion'] ?? '',
                ]);
            }
        }

        // Actualizar responsable en establecimiento
        if ($establecimiento = Establecimiento::find($request->establecimiento_id)) {
            $establecimiento->responsable = $request->responsable;
            $establecimiento->save();
        }

        // Redirigir usando el nuevo nombre de ruta admin.actas.index
        return redirect()->route('admin.actas.index')->with('success', '✅ Acta registrada correctamente.');
    }

    public function show($id)
    {
        $acta = Acta::with(['establecimiento','participantes','actividades','acuerdos','observaciones'])
            ->findOrFail($id);

        return view('actas.show', compact('acta'));
    }

    public function generarPDF($id)
    {
        $acta = Acta::with(['establecimiento','participantes','actividades','acuerdos','observaciones'])
            ->findOrFail($id);

        $pdf = Pdf::loadView('actas.pdf', compact('acta'))->setPaper('a4', 'portrait');
        return $pdf->stream("acta_{$acta->id}.pdf");
    }

    public function edit(Acta $acta)
    {
        $acta->load(['participantes','actividades','acuerdos','observaciones']);
        return view('actas.edit', compact('acta'));
    }

    public function update(Request $request, Acta $acta)
    {
        $request->validate(array_merge([
            'fecha' => 'required|date',
            'establecimiento_id' => 'required|exists:establecimientos,id',
            'responsable' => 'required|string|max:255',
            'tema' => 'required|string',
            'modalidad' => 'required|string',
            'implementador' => 'required|string',
            'imagenes.*' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ], $this->individualImageValidationRules()));

        $acta->update($request->only(['fecha','establecimiento_id','responsable','tema','modalidad','implementador']));

        $newFiles = $this->collectNewImageFiles($request);
        foreach ($newFiles as $index => $file) {
            if (!$file) continue;
            $i = $index + 1;
            $campo = 'imagen' . $i;

            if (!empty($acta->$campo) && Storage::disk('public')->exists($acta->$campo)) {
                Storage::disk('public')->delete($acta->$campo);
            }

            $filename = time() . '_' . uniqid() . "_img{$i}." . $file->getClientOriginalExtension();
            $file->storeAs('actas', $filename, 'public');
            $acta->$campo = 'actas/' . $filename;
        }

        $acta->save();

        // Actualizar relaciones (Borrar y crear)
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
            foreach ($request->observaciones as $o) {
                $acta->observaciones()->create($o);
            }
        }

        if ($establecimiento = Establecimiento::find($request->establecimiento_id)) {
            $establecimiento->responsable = $request->responsable;
            $establecimiento->save();
        }

        return redirect()->route('admin.actas.index')->with('success', '✅ Acta actualizada correctamente.');
    }

    public function destroy(Acta $acta)
    {
        for ($i = 1; $i <= $this->maxImages; $i++) {
            $campo = 'imagen' . $i;
            if (!empty($acta->$campo) && Storage::disk('public')->exists($acta->$campo)) {
                Storage::disk('public')->delete($acta->$campo);
            }
        }

        $acta->delete();

        return redirect()->route('admin.actas.index')->with('success', 'Acta eliminada correctamente.');
    }

    protected function individualImageValidationRules()
    {
        $rules = [];
        for ($i = 1; $i <= $this->maxImages; $i++) {
            $rules["imagen{$i}"] = 'nullable|image|mimes:jpg,jpeg,png|max:2048';
        }
        return $rules;
    }

    protected function collectNewImageFiles(Request $request)
    {
        $max = $this->maxImages;
        $newFiles = array_fill(0, $max, null);

        if ($request->hasFile('imagenes')) {
            foreach ($request->file('imagenes') as $i => $file) {
                if ($i >= $max) break;
                $newFiles[$i] = $file;
            }
        }

        for ($i = 1; $i <= $max; $i++) {
            $field = "imagen{$i}";
            if ($request->hasFile($field)) {
                $newFiles[$i - 1] = $request->file($field);
            }
        }

        return $newFiles;
    }

    public function subirPDF(Request $request, $id)
    {
        $request->validate([
            'pdf_firmado' => 'required|mimes:pdf|max:20480', 
        ]);

        $acta = Acta::findOrFail($id);
        $path = $request->file('pdf_firmado')->store('actas_firmadas', 'public');

        $acta->update([
            'firmado_pdf' => $path,
            'firmado' => true,
        ]);

        return redirect()->back()->with('success', 'El acta firmada fue subida correctamente.');
    }
}