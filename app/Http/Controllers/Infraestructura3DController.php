<?php

namespace App\Http\Controllers;

use App\Models\CabeceraMonitoreo;
use App\Models\MonitoreoModulos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Infraestructura3DController extends Controller
{
    public function index($id)
    {
        $acta = CabeceraMonitoreo::findOrFail($id);
        
        $modulo = MonitoreoModulos::where('cabecera_monitoreo_id', $id)
            ->where('modulo_nombre', 'infraestructura_3d')
            ->first();

        $contenido = $modulo ? $modulo->contenido : [
            'consultorios' => []
        ];

        return view('usuario.monitoreo.modulos.infraestructura_3d', compact('acta', 'contenido'));
    }

    public function store(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            MonitoreoModulos::updateOrCreate(
                [
                    'cabecera_monitoreo_id' => $id,
                    'modulo_nombre' => 'infraestructura_3d'
                ],
                [
                    'contenido' => $request->contenido
                ]
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Croquis 3D guardado correctamente.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al guardar: ' . $e->getMessage()
            ], 500);
        }
    }
}
