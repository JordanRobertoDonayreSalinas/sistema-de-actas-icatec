<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Establecimiento;

class EstablecimientoController extends Controller
{
    public function buscar(Request $request)
    {
        $term = $request->get('term');

        // 1. Validación básica: Si no hay término, devolver array vacío
        if (!$term) {
            return response()->json([]);
        }

        $establecimientos = Establecimiento::query()
            ->where(function($query) use ($term) {
                $query->where('codigo', 'LIKE', "%{$term}%")
                      ->orWhere('nombre', 'LIKE', "%{$term}%");
            })
            // 2. MEJORA: Ordenar alfabéticamente ayuda visualmente al usuario
            ->orderBy('nombre', 'asc')
            ->limit(10)
            ->get([
                'id', 'codigo', 'nombre', 'provincia', 'distrito', 
                'categoria', 'red', 'microred', 'responsable'
            ]);

        $resultados = $establecimientos->map(function ($e) {
            return [
                'id'          => $e->id,
                // 'label': Lo que se ve en la lista desplegable
                'label'       => $e->codigo . ' - ' . $e->nombre, 
                // 'value': Lo que se escribe en el input al seleccionar (A veces es mejor solo el nombre)
                'value'       => $e->nombre, 
                // Datos extra para rellenar otros inputs automáticamente
                'provincia'   => $e->provincia ?? '',
                'distrito'    => $e->distrito ?? '',
                'categoria'   => $e->categoria ?? '',
                'red'         => $e->red ?? '',
                'microred'    => $e->microred ?? '',
                'responsable' => $e->responsable ?? '',
            ];
        });

        return response()->json($resultados);
    }
}