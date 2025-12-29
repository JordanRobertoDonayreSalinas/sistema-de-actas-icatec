<?php

namespace App\Http\Controllers;

use App\Models\CabeceraMonitoreo;
use App\Models\MonitoreoEquipo;
use App\Models\Establecimiento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EditMonitoreoController extends Controller
{
    /**
     * Muestra el formulario para editar un acta existente.
     */
    public function edit($id)
    {
        // Cargamos la cabecera con sus relaciones para evitar el problema de N+1 consultas
        $monitoreo = CabeceraMonitoreo::with(['establecimiento', 'equipo'])->findOrFail($id);
        
        return view('usuario.monitoreo.edit', compact('monitoreo'));
    }

    /**
     * Procesa la actualización de los datos en la base de datos.
     */
    public function update(Request $request, $id)
    {
        // 1. Validación estricta de datos
        $request->validate([
            'establecimiento_id' => 'required|exists:establecimientos,id',
            'fecha'              => 'required|date',
            'responsable'        => 'required|string|max:255',
            'categoria'          => 'nullable|string|max:50',
            'equipo'             => 'nullable|array',
            'redirect_to'        => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            $monitoreo = CabeceraMonitoreo::findOrFail($id);

            // 2. ACTUALIZAR TABLA MAESTRA DE ESTABLECIMIENTOS
            // Usamos mb_strtoupper para que nombres con tildes o eñes no se corrompan
            $establecimiento = Establecimiento::findOrFail($request->establecimiento_id);
            $establecimiento->update([
                'responsable' => mb_strtoupper(trim($request->responsable), 'UTF-8'),
                'categoria'   => mb_strtoupper(trim($request->categoria), 'UTF-8'),
            ]);

            // 3. ACTUALIZAR CABECERA DEL ACTA (Snapshot Histórico)
            $monitoreo->update([
                'establecimiento_id' => $request->establecimiento_id,
                'fecha'              => $request->fecha,
                'responsable'        => mb_strtoupper(trim($request->responsable), 'UTF-8'),
                'categoria_congelada'=> mb_strtoupper(trim($request->categoria), 'UTF-8'), 
                'implementador'      => $request->implementador,
            ]);

            // 4. SINCRONIZAR EQUIPO DE MONITOREO
            // Eliminamos los registros actuales para evitar duplicados o basura al editar
            MonitoreoEquipo::where('cabecera_monitoreo_id', $id)->delete();

            if ($request->has('equipo') && is_array($request->equipo)) {
                foreach ($request->equipo as $item) {
                    if (!empty($item['doc'])) {
                        MonitoreoEquipo::create([
                            'cabecera_monitoreo_id' => $id,
                            'tipo_doc'              => $item['tipo_doc'] ?? 'DNI',
                            'doc'                   => trim($item['doc']),
                            'apellido_paterno'      => mb_strtoupper(trim($item['apellido_paterno']), 'UTF-8'),
                            'apellido_materno'      => mb_strtoupper(trim($item['apellido_materno']), 'UTF-8'),
                            'nombres'               => mb_strtoupper(trim($item['nombres']), 'UTF-8'),
                            'cargo'                 => mb_strtoupper(trim($item['cargo']), 'UTF-8'),
                            'institucion'           => mb_strtoupper(trim($item['institucion'] ?? 'DIRESA'), 'UTF-8'),
                        ]);
                    }
                }
            }

            DB::commit();

            // 5. FLUJO DE REDIRECCIÓN DINÁMICO
            // Esto permite que el botón "Guardar y Editar Módulos" funcione correctamente
            if ($request->input('redirect_to') === 'modulos') {
                return redirect()->route('usuario.monitoreo.modulos', $id)
                                 ->with('success', "Cabecera actualizada. Ahora puede completar los módulos técnicos.");
            }

            return redirect()->route('usuario.monitoreo.index')
                             ->with('success', "El Acta #{$id} ha sido actualizada exitosamente.");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error en Update Monitoreo: " . $e->getMessage());
            return back()->withErrors(['error' => 'Hubo un problema al guardar los cambios: ' . $e->getMessage()])->withInput();
        }
    }
}