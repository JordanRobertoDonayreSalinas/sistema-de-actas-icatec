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

        // 2. DEFINIR LA VARIABLE $detalle (Esto es lo que te faltaba)
        // Buscamos si ya existe un guardado previo para este módulo, o creamos uno vacío en memoria
        $detalle = MonitoreoModulos::where('cabecera_monitoreo_id', $actaId)
            ->where('modulo_nombre', 'medicina_especializado') // Asegúrate de usar el mismo nombre al guardar
            ->firstOrNew();

        // [Opcional] Inicializar contenido como array vacío para evitar errores si es nuevo
        if ($detalle->contenido === null) {
            $detalle->contenido = [];
        }

        // 3. Equipos
        $equipos = []; // Tu lógica de equipos aquí

        $registro = []; // Tu lógica de equipos aquí

        // 4. RETORNO DE LA VISTA
        // ¡IMPORTANTE!: Agregar 'detalle' al compact
        return view('usuario.monitoreo.modulos_especializados.medicina_especializado', compact('acta', 'equipos', 'detalle', 'registro'));
    }

    public function store(Request $request, $actaId)
    {
        // Aquí iría tu lógica para guardar (recuerda usar 'medicina_especializado' como nombre del módulo)
        // ...
    }
}
