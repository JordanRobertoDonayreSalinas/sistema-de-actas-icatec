<?php

namespace App\Http\Controllers;

use App\Models\CabeceraMonitoreo;
use App\Models\MonitoreoModulos;
use App\Models\Profesional;
use App\Models\EquipoComputo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class GestionAdministrativaController extends Controller
{
    /**
     * Busca un profesional en la tabla mon_profesionales (Maestro).
     * Replicando la lógica de filtrado del equipo de monitoreo.
     */
    public function buscarProfesional($doc)
    {
        // Limpiamos el documento de espacios para evitar fallos de match
        $dni = trim($doc);
        
        // Registro en log para auditoría técnica (opcional)
        Log::info("Filtrando profesional en tabla maestra mon_profesionales: " . $dni);

        $profesional = Profesional::where('doc', $dni)->first();

        if ($profesional) {
            return response()->json([
                'exists'           => true,
                'tipo_doc'         => $profesional->tipo_doc,
                'apellido_paterno' => $profesional->apellido_paterno,
                'apellido_materno' => $profesional->apellido_materno,
                'nombres'          => $profesional->nombres,
                'email'            => $profesional->email,
                'telefono'         => $profesional->telefono,
            ]);
        }

        return response()->json(['exists' => false]);
    }

    /**
     * Carga la vista del módulo 01: Gestión Administrativa.
     */
    public function index($id)
    {
        // Cargamos el acta con su IPRESS relacionada
        $acta = CabeceraMonitoreo::with(['establecimiento'])->findOrFail($id);
        
        // Buscamos si ya existen datos previos guardados para este módulo
        $detalle = MonitoreoModulos::where('cabecera_monitoreo_id', $id)
                    ->where('modulo_nombre', 'gestion_administrativa')
                    ->first();

        return view('usuario.monitoreo.modulos.gestion_administrativa', compact('acta', 'detalle'));
    }

    /**
     * Procesa el guardado completo del módulo 01.
     * Sincroniza la tabla maestra de profesionales y el inventario de equipos.
     */
    public function store(Request $request, $id)
    {
        // Validación de seguridad para la foto de evidencia (Máximo 2MB)
        $request->validate([
            'foto_evidencia' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        try {
            DB::beginTransaction();

            $modulo = 'gestion_administrativa';
            $datos = $request->input('contenido');

            // 1. SINCRONIZACIÓN CON MAESTRO DE PROFESIONALES (RRHH)
            // Replicamos la lógica de mon_equipo_monitoreo para mantener actualizado el maestro
            if (isset($datos['rrhh']) && !empty($datos['rrhh']['doc'])) {
                Profesional::updateOrCreate(
                    ['doc' => trim($datos['rrhh']['doc'])],
                    [
                        'tipo_doc'         => $datos['rrhh']['tipo_doc'] ?? 'DNI',
                        'apellido_paterno' => strtoupper(trim($datos['rrhh']['apellido_paterno'])),
                        'apellido_materno' => strtoupper(trim($datos['rrhh']['apellido_materno'])),
                        'nombres'          => strtoupper(trim($datos['rrhh']['nombres'])),
                        'email'            => strtolower(trim($datos['rrhh']['email'] ?? '')),
                        'telefono'         => trim($datos['rrhh']['telefono'] ?? ''),
                    ]
                );
            }

            // 2. GESTIÓN DE EQUIPOS DE CÓMPUTO
            // Limpiamos registros anteriores de este módulo específico para evitar duplicidad al actualizar
            EquipoComputo::where('cabecera_monitoreo_id', $id)
                         ->where('modulo', $modulo)
                         ->delete();

            if ($request->has('equipos') && is_array($request->equipos)) {
                foreach ($request->equipos as $eq) {
                    if (!empty($eq['descripcion'])) {
                        EquipoComputo::create([
                            'cabecera_monitoreo_id' => $id,
                            'modulo'      => $modulo,
                            'descripcion' => strtoupper(trim($eq['descripcion'])),
                            'cantidad'    => $eq['cantidad'] ?? 1,
                            'estado'      => $eq['estado'] ?? 'BUENO',
                            'propio'      => $eq['propio'] ?? 'SI',
                        ]);
                    }
                }
            }

            // 3. PROCESAMIENTO DE EVIDENCIA FOTOGRÁFICA
            // Buscamos si ya existe una foto guardada para no perderla si no se sube una nueva
            $registroPrevio = MonitoreoModulos::where('cabecera_monitoreo_id', $id)
                                ->where('modulo_nombre', $modulo)
                                ->first();

            if ($request->hasFile('foto_evidencia')) {
                // Si el usuario sube una foto nueva y ya había una anterior, borramos la vieja del disco
                if ($registroPrevio && isset($registroPrevio->contenido['foto_evidencia'])) {
                    Storage::disk('public')->delete($registroPrevio->contenido['foto_evidencia']);
                }
                
                // Guardar nuevo archivo
                $path = $request->file('foto_evidencia')->store('evidencias_monitoreo', 'public');
                $datos['foto_evidencia'] = $path;
            } else {
                // Si no se subió un archivo nuevo, persistimos la ruta de la foto que ya estaba guardada
                if ($registroPrevio && isset($registroPrevio->contenido['foto_evidencia'])) {
                    $datos['foto_evidencia'] = $registroPrevio->contenido['foto_evidencia'];
                }
            }

            // 4. GUARDADO FINAL EN TABLA DE MÓDULOS (JSON)
            MonitoreoModulos::updateOrCreate(
                ['cabecera_monitoreo_id' => $id, 'modulo_nombre' => $modulo],
                ['contenido' => $datos]
            );

            DB::commit();
            
            return redirect()->route('usuario.monitoreo.modulos', $id)
                             ->with('success', '¡Gestión Administrativa sincronizada con éxito!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error crítico en Módulo 01 (Store): " . $e->getMessage());
            return back()->withErrors(['error' => 'Error al guardar los datos: ' . $e->getMessage()])->withInput();
        }
    }
}