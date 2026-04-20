<?php

namespace App\Http\Controllers;

use App\Models\Reunion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class ReunionController extends Controller
{
    public function index(Request $request)
    {
        $query = Reunion::query();

        // Filtros opcionales si se envían por request
        if ($request->filled('titulo')) {
            $query->where('titulo_reunion', 'LIKE', '%' . $request->titulo . '%');
        }
        if ($request->filled('fecha_desde')) {
            $query->whereDate('fecha_reunion', '>=', $request->fecha_desde);
        }
        if ($request->filled('fecha_hasta')) {
            $query->whereDate('fecha_reunion', '<=', $request->fecha_hasta);
        }
        if ($request->filled('anulado')) {
            $query->where('anulado', $request->anulado);
        }

        $reuniones = $query->orderBy('fecha_reunion', 'desc')->paginate(15);
        $total_reuniones = Reunion::count();
        $total_anuladas = Reunion::where('anulado', true)->count();

        return view('usuario.reuniones.index', compact('reuniones', 'total_reuniones', 'total_anuladas'));
    }

    public function create()
    {
        return view('usuario.reuniones.create', ['reunion' => new Reunion()]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'titulo_reunion' => 'required|string|max:255',
            'fecha_reunion' => 'required|date',
            'hora_reunion' => 'required',
            'nombre_institucion' => 'required|string|max:255',
            'descripcion_general' => 'required|string',
        ]);

        DB::beginTransaction();
        try {
            $data = $request->only([
                'titulo_reunion', 'fecha_reunion', 'hora_reunion', 'hora_finalizada_reunion',
                'nombre_institucion', 'descripcion_general'
            ]);

            // Convertir a mayúsculas
            $data['titulo_reunion'] = mb_strtoupper($data['titulo_reunion'], 'UTF-8');
            $data['nombre_institucion'] = mb_strtoupper($data['nombre_institucion'], 'UTF-8');

            // Arrays dinámicos
            $data['acuerdos'] = $request->input('acuerdos', []);
            $data['comentarios_observaciones'] = $request->input('observaciones', []); // form says observaciones
            
            $participantes = $request->input('participantes', []);
            foreach ($participantes as &$p) {
                if (isset($p['apellidos'])) $p['apellidos'] = mb_strtoupper($p['apellidos'], 'UTF-8');
                if (isset($p['nombres'])) $p['nombres'] = mb_strtoupper($p['nombres'], 'UTF-8');
                if (isset($p['cargo'])) $p['cargo'] = mb_strtoupper($p['cargo'], 'UTF-8');
                if (isset($p['institucion'])) $p['institucion'] = mb_strtoupper($p['institucion'], 'UTF-8');
            }
            $data['participantes'] = $participantes;
            $data['anulado'] = false;

            $reunion = Reunion::create($data);

            // Manejar imágenes si las hay
            if ($request->hasFile('imagenes')) {
                $files = $request->file('imagenes');
                $updates = [];
                for ($i = 0; $i < min(2, count($files)); $i++) {
                    $file = $files[$i];
                    $path = $file->store('reuniones', 'public');
                    // Retener la ruta relativa para la DB incluyendo 'storage/'
                    $updates['foto_' . ($i + 1)] = 'storage/' . $path;
                }
                
                if (!empty($updates)) {
                    $reunion->update($updates);
                }
            }

            DB::commit();
            return redirect()->route('usuario.reuniones.index')->with('success', 'Acta de reunión creada correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Ocurrió un error al guardar: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $reunion = Reunion::findOrFail($id);
        if ($reunion->anulado) {
            return redirect()->route('usuario.reuniones.index')->with('error', 'No se puede editar un acta anulada.');
        }

        return view('usuario.reuniones.edit', compact('reunion'));
    }

    public function update(Request $request, $id)
    {
        $reunion = Reunion::findOrFail($id);

        if ($reunion->anulado) {
            return redirect()->route('usuario.reuniones.index')->with('error', 'No se puede editar un acta anulada.');
        }

        $request->validate([
            'titulo_reunion' => 'required|string|max:255',
            'fecha_reunion' => 'required|date',
            'hora_reunion' => 'required',
            'nombre_institucion' => 'required|string|max:255',
            'descripcion_general' => 'required|string',
        ]);

        DB::beginTransaction();
        try {
            $data = $request->only([
                'titulo_reunion', 'fecha_reunion', 'hora_reunion', 'hora_finalizada_reunion',
                'nombre_institucion', 'descripcion_general'
            ]);

            $data['titulo_reunion'] = mb_strtoupper($data['titulo_reunion'], 'UTF-8');
            $data['nombre_institucion'] = mb_strtoupper($data['nombre_institucion'], 'UTF-8');

            $data['acuerdos'] = $request->input('acuerdos', []);
            $data['comentarios_observaciones'] = $request->input('observaciones', []);
            
            $participantes = $request->input('participantes', []);
            foreach ($participantes as &$p) {
                if (isset($p['apellidos'])) $p['apellidos'] = mb_strtoupper($p['apellidos'], 'UTF-8');
                if (isset($p['nombres'])) $p['nombres'] = mb_strtoupper($p['nombres'], 'UTF-8');
                if (isset($p['cargo'])) $p['cargo'] = mb_strtoupper($p['cargo'], 'UTF-8');
                if (isset($p['institucion'])) $p['institucion'] = mb_strtoupper($p['institucion'], 'UTF-8');
            }
            $data['participantes'] = $participantes;

            // Eliminar fotos si se solicita
            if ($request->input('quitar_foto_1') == '1' && $reunion->foto_1) {
                // Eliminar archivo
                $filePath = str_replace('storage/', '', $reunion->foto_1); // Queda 'reuniones/xyz.jpg'
                Storage::disk('public')->delete($filePath);
                $data['foto_1'] = null;
            }
            if ($request->input('quitar_foto_2') == '1' && $reunion->foto_2) {
                $filePath = str_replace('storage/', '', $reunion->foto_2);
                Storage::disk('public')->delete($filePath);
                $data['foto_2'] = null;
            }

            // Actualizar imágenes nuevas
            if ($request->hasFile('imagenes')) {
                $files = $request->file('imagenes');
                $indice_libre = 1;
                foreach($files as $file) {
                    if ($indice_libre > 2) break; // max 2
                    
                    // Buscar hueco (si foto_1 esta vacia o se la acaba de vaciar)
                    if (empty($data['foto_1']) && empty($reunion->foto_1)) {
                        $path = $file->store('reuniones', 'public');
                        $data['foto_1'] = 'storage/' . $path;
                    } else if (empty($data['foto_2']) && empty($reunion->foto_2)) {
                        $path = $file->store('reuniones', 'public');
                        $data['foto_2'] = 'storage/' . $path;
                    } else if (isset($data['foto_1']) && empty($data['foto_1'])) {
                        // Si decidio quitar la foto 1, podemos llenarlo aqui
                        $path = $file->store('reuniones', 'public');
                        $data['foto_1'] = 'storage/' . $path;
                    } else if (isset($data['foto_2']) && empty($data['foto_2'])) {
                        $path = $file->store('reuniones', 'public');
                        $data['foto_2'] = 'storage/' . $path;
                    } else {
                        // Si ambas estan llenas y manda nuevas, podemos omitir o sobreescribir. Sobreescribamos la 1 y 2
                        if ($indice_libre == 1) {
                            if (!empty($reunion->foto_1)) {
                                Storage::disk('public')->delete(str_replace('storage/', '', $reunion->foto_1));
                            }
                            $path = $file->store('reuniones', 'public');
                            $data['foto_1'] = 'storage/' . $path;
                        } else {
                            if (!empty($reunion->foto_2)) {
                                Storage::disk('public')->delete(str_replace('storage/', '', $reunion->foto_2));
                            }
                            $path = $file->store('reuniones', 'public');
                            $data['foto_2'] = 'storage/' . $path;
                        }
                    }
                    $indice_libre++;
                }
            }

            $reunion->update($data);

            DB::commit();
            return redirect()->route('usuario.reuniones.index')->with('success', 'Acta de reunión actualizada correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Ocurrió un error al actualizar: ' . $e->getMessage());
        }
    }

    public function anular($id)
    {
        try {
            $reunion = Reunion::findOrFail($id);
            $reunion->anulado = !$reunion->anulado; // Toggle
            $reunion->save();

            $mensaje = $reunion->anulado ? 'Acta anulada correctamente.' : 'Acta reactivada correctamente.';
            return response()->json(['success' => true, 'message' => $mensaje, 'estado' => $reunion->anulado]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function pdf($id)
    {
        $reunion = Reunion::findOrFail($id);
        
        $pdf = Pdf::loadView('usuario.reuniones.pdf', compact('reunion'))
                  ->setOptions(['isRemoteEnabled' => true, 'isPhpEnabled' => true])
                  ->setPaper('a4', 'portrait');
                  
        $titulo = mb_strtoupper($reunion->titulo_reunion, 'UTF-8');
        $correlativo = str_pad($reunion->id, 3, '0', STR_PAD_LEFT);
        return $pdf->stream("ACTA DE REUNION Nº {$correlativo} - {$titulo}.pdf");
    }
}
