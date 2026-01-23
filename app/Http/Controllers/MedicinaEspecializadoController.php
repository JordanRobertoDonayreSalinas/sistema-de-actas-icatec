<?php

namespace App\Http\Controllers;

use App\Models\Acta;
use App\Models\MonitoreoModulos;
use Illuminate\Http\Request;

class MedicinaEspecializadoController extends Controller
{
    public function index($actaId)
    {
        // 1. Recuperar el Acta
        $acta = Acta::findOrFail($actaId);

        // 2. BUSCAR O CREAR EL DETALLE
        $detalle = MonitoreoModulos::where('cabecera_monitoreo_id', $actaId)
            ->where('modulo_nombre', 'medicina_especializado')
            ->firstOrNew();

        // Si es nuevo, inicializamos contenido como array vacío
        if ($detalle->contenido === null) {
            $detalle->contenido = [];
        }

        // 3. RECUPERAR EQUIPOS DESDE LA BD
        // Gracias al 'cast' del Paso 1, $detalle->contenido ya es un array.
        // Buscamos la llave 'equipos'. Si no existe, usamos array vacío [].
        $equipos = $detalle->contenido['equipos'] ?? [];

        $prefix = 'medicina';

        // 4. RETORNO DE LA VISTA
        // Pasamos 'detalle' (para los inputs normales) y 'equipos' (para la tabla dinámica)
        return view('usuario.monitoreo.modulos_especializados.medicina_especializado', compact('acta', 'equipos', 'detalle', 'prefix'));
    }

    public function store(Request $request, $actaId)
    {
        // Lógica de guardado (como te mostré antes)
        $data = $request->validate([
            'contenido' => 'array', // Validar que llegue el array
        ]);

        MonitoreoModulos::updateOrCreate(
            [
                'cabecera_monitoreo_id' => $actaId,
                'modulo_nombre' => 'medicina_especializado'
            ],
            [
                'contenido' => $data['contenido'] // Laravel lo convertirá a JSON al guardar
            ]
        );

        return back()->with('success', 'Guardado correctamente');
    }
}
