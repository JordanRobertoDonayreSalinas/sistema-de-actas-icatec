<?php

namespace App\Http\Controllers;

use App\Models\CabeceraMonitoreo;
use App\Models\MonitoreoEquipo;
use App\Models\Establecimiento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EditMonitoreoController extends Controller
{
    /**
     * Muestra el formulario para editar un acta existente.
     */
    public function edit($id)
    {
        // Cargamos la cabecera con sus relaciones para evitar múltiples consultas
        $monitoreo = CabeceraMonitoreo::with(['establecimiento', 'equipo'])->findOrFail($id);
        
        return view('usuario.monitoreo.edit', compact('monitoreo'));
    }

    /**
     * Procesa la actualización de los datos en la base de datos.
     */
    public function update(Request $request, $id)
    {
        // Validación de datos de entrada
        $request->validate([
            'establecimiento_id' => 'required|exists:establecimientos,id',
            'fecha'              => 'required|date',
            'responsable'        => 'required|string|max:255',
            'categoria'          => 'nullable|string|max:50',
            'equipo'             => 'nullable|array'
        ]);

        try {
            DB::beginTransaction();

            $monitoreo = CabeceraMonitoreo::findOrFail($id);

            // 1. ACTUALIZAR TABLA MAESTRA DE ESTABLECIMIENTOS (Consistencia futura)
            $establecimiento = Establecimiento::findOrFail($request->establecimiento_id);
            $establecimiento->update([
                'responsable' => strtoupper($request->responsable),
                'categoria'   => strtoupper($request->categoria),
            ]);

            // 2. ACTUALIZAR CABECERA DEL ACTA (Snapshot Histórico)
            $monitoreo->update([
                'establecimiento_id' => $request->establecimiento_id,
                'fecha'              => $request->fecha,
                'responsable'        => strtoupper($request->responsable),
                'categoria_congelada'=> strtoupper($request->categoria), // Guardamos el histórico
                'implementador'      => $request->implementador,
            ]);

            // 3. SINCRONIZAR EQUIPO DE MONITOREO
            // Eliminamos los registros previos del equipo para esta acta
            MonitoreoEquipo::where('cabecera_monitoreo_id', $id)->delete();

            if ($request->has('equipo') && is_array($request->equipo)) {
                foreach ($request->equipo as $item) {
                    if (!empty($item['doc'])) {
                        MonitoreoEquipo::create([
                            'cabecera_monitoreo_id' => $id,
                            'tipo_doc'              => $item['tipo_doc'] ?? 'DNI',
                            'doc'                   => $item['doc'],
                            'apellido_paterno'      => strtoupper($item['apellido_paterno']),
                            'apellido_materno'      => strtoupper($item['apellido_materno']),
                            'nombres'               => strtoupper($item['nombres']),
                            'cargo'                 => strtoupper($item['cargo']),
                            'institucion'           => strtoupper($item['institucion'] ?? 'DIRESA'),
                        ]);
                    }
                }
            }

            DB::commit();

            return redirect()->route('usuario.monitoreo.index')
                             ->with('success', "¡Acta #{$id} actualizada y datos de IPRESS sincronizados!.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al procesar la actualización: ' . $e->getMessage()])->withInput();
        }
    }
}