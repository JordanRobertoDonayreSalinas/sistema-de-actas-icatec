<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Profesional;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Services\SignatureHarvestingService;

class SignatureBankController extends Controller
{
    /**
     * Muestra el banco de firmas.
     */
    public function index(Request $request)
    {
        $query = Profesional::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('doc', 'like', "%$search%")
                  ->orWhere('nombres', 'like', "%$search%")
                  ->orWhere('apellido_paterno', 'like', "%$search%")
                  ->orWhere('apellido_materno', 'like', "%$search%");
            });
        }

        if ($request->filled('estado')) {
            if ($request->estado == 'con_firma') {
                $query->where('tipo_firma', 'DIGITAL')->orWhereNotNull('firma_path');
            } else {
                $query->where('tipo_firma', 'MANUAL')->whereNull('firma_path');
            }
        }

        $profesionales = $query->orderBy('apellido_paterno')->paginate(20);

        return view('usuario.firmas.index', compact('profesionales'));
    }

    /**
     * Ejecuta el proceso de cosecha automática de firmas.
     */
    public function harvest(SignatureHarvestingService $harvester)
    {
        $result = $harvester->harvest();

        if ($result['success']) {
            return back()->with('success', $result['message'] . " (Nuevos: {$result['stats']['new_professionals']}, Digitales: {$result['stats']['updated_digital']})");
        }

        return back()->with('error', $result['message']);
    }

    /**
     * Sube o actualiza la firma de un profesional.
     */
    public function upload(Request $request, $id)
    {
        $request->validate([
            'firma' => 'required|image|mimes:png,jpg,jpeg|max:2048', // Recomendado PNG transparente
        ]);

        try {
            DB::beginTransaction();
            $profesional = Profesional::findOrFail($id);

            // Eliminar firma anterior si existe
            if ($profesional->firma_path && Storage::disk('public')->exists($profesional->firma_path)) {
                Storage::disk('public')->delete($profesional->firma_path);
            }

            $profesional->update([
                'tipo_firma' => 'MANUAL'
            ]);

            if ($request->hasFile('firma')) {
                // Guardar nueva firma
                $file = $request->file('firma');
                $filename = 'firma_' . $profesional->doc . '_' . time() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('banco_firmas', $filename, 'public');

                $profesional->update([
                    'firma_path' => $path,
                    'ultima_actualizacion_firma' => Carbon::now(),
                ]);
            }

            DB::commit();
            return back()->with('success', "Firma de " . $profesional->nombres . " actualizada correctamente.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', "Error al subir la firma: " . $e->getMessage());
        }
    }

    /**
     * Elimina la firma de un profesional.
     */
    public function destroy($id)
    {
        try {
            $profesional = Profesional::findOrFail($id);

            if ($profesional->firma_path && Storage::disk('public')->exists($profesional->firma_path)) {
                Storage::disk('public')->delete($profesional->firma_path);
            }

            $profesional->update([
                'firma_path' => null,
                'ultima_actualizacion_firma' => null,
            ]);

            return back()->with('success', "Firma eliminada correctamente.");
        } catch (\Exception $e) {
            return back()->with('error', "Error al eliminar la firma.");
        }
    }

    /**
     * Búsqueda AJAX para autocompletado en actas.
     */
    public function search(Request $request)
    {
        $term = $request->get('term');
        $profesionales = Profesional::where('doc', 'like', "%$term%")
            ->orWhere('apellido_paterno', 'like', "%$term%")
            ->orWhere('nombres', 'like', "%$term%")
            ->limit(10)
            ->get()
            ->map(function($p) {
                return [
                    'id' => $p->id,
                    'text' => $p->doc . ' - ' . $p->apellido_paterno . ' ' . $p->nombres,
                    'has_firma' => !empty($p->firma_path),
                    'firma_url' => $p->firma_path ? Storage::url($p->firma_path) : null
                ];
            });

        return response()->json($profesionales);
    }
}
