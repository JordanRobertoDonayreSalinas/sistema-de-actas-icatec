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
     * Busca un profesional en la tabla maestra 'mon_profesionales'.
     * Esta lógica permite el autocompletado en el formulario mediante AJAX.
     */
    public function buscarProfesional($doc)
    {
        // 1. Limpieza del DNI para asegurar el match exacto en la BD
        $dni = trim($doc);
        
        // Log para seguimiento técnico verificable en storage/logs/laravel.log
        Log::info("Filtrando profesional en tabla maestra mon_profesionales: " . $dni);

        // 2. Consulta al modelo Profesional (vinculado a mon_profesionales)
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
     * Carga la interfaz del Módulo 01: Gestión Administrativa.
     */
    public function index($id)
    {
        // Carga el acta incluyendo la relación de equiposComputo para evitar consultas lentas
        $acta = CabeceraMonitoreo::with(['establecimiento', 'equiposComputo'])->findOrFail($id);
        
        // Recupera los datos guardados previamente en formato JSON
        $detalle = MonitoreoModulos::where('cabecera_monitoreo_id', $id)
                    ->where('modulo_nombre', 'gestion_administrativa')
                    ->first();

        return view('usuario.monitoreo.modulos.gestion_administrativa', compact('acta', 'detalle'));
    }

    /**
     * Procesa el guardado masivo y la sincronización con el maestro de profesionales.
     */
    public function store(Request $request, $id)
    {
        // Validación de imagen y campos obligatorios mínimos
        $request->validate([
            'foto_evidencia' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        try {
            DB::beginTransaction();

            $modulo = 'gestion_administrativa';
            $datos = $request->input('contenido', []);

            // 1. SINCRONIZACIÓN CON EL MAESTRO DE PROFESIONALES (RRHH)
            if (isset($datos['rrhh']) && !empty($datos['rrhh']['doc'])) {
                Profesional::updateOrCreate(
                    ['doc' => trim($datos['rrhh']['doc'])],
                    [
                        'tipo_doc'         => $datos['rrhh']['tipo_doc'] ?? 'DNI',
                        // mb_strtoupper garantiza que "Ñ" y tildes se procesen bien en XAMPP
                        'apellido_paterno' => mb_strtoupper(trim($datos['rrhh']['apellido_paterno']), 'UTF-8'),
                        'apellido_materno' => mb_strtoupper(trim($datos['rrhh']['apellido_materno']), 'UTF-8'),
                        'nombres'          => mb_strtoupper(trim($datos['rrhh']['nombres']), 'UTF-8'),
                        'email'            => strtolower(trim($datos['rrhh']['email'] ?? '')),
                        'telefono'         => trim($datos['rrhh']['telefono'] ?? ''),
                    ]
                );
            }

            // 2. GESTIÓN RELACIONAL DE EQUIPOS DE CÓMPUTO
            // Se limpian registros previos para permitir actualizaciones limpias (Borrar y Crear)
            EquipoComputo::where('cabecera_monitoreo_id', $id)
                         ->where('modulo', $modulo)
                         ->delete();

            if ($request->has('equipos') && is_array($request->equipos)) {
                foreach ($request->equipos as $eq) {
                    if (!empty($eq['descripcion'])) {
                        EquipoComputo::create([
                            'cabecera_monitoreo_id' => $id,
                            'modulo'      => $modulo,
                            'descripcion' => mb_strtoupper(trim($eq['descripcion']), 'UTF-8'),
                            'cantidad'    => $eq['cantidad'] ?? 1,
                            'estado'      => $eq['estado'] ?? 'BUENO',
                            'propio'      => $eq['propio'] ?? 'SI',
                        ]);
                    }
                }
            }

            // 3. PERSISTENCIA DE EVIDENCIA FOTOGRÁFICA
            $registroPrevio = MonitoreoModulos::where('cabecera_monitoreo_id', $id)
                                ->where('modulo_nombre', $modulo)
                                ->first();

            if ($request->hasFile('foto_evidencia')) {
                // Borrado de archivo físico antiguo para ahorrar espacio en disco
                if ($registroPrevio && isset($registroPrevio->contenido['foto_evidencia'])) {
                    Storage::disk('public')->delete($registroPrevio->contenido['foto_evidencia']);
                }
                $path = $request->file('foto_evidencia')->store('evidencias_monitoreo', 'public');
                $datos['foto_evidencia'] = $path;
            } else {
                // Si no se subió una nueva, rescatamos la ruta de la foto guardada anteriormente
                if ($registroPrevio && isset($registroPrevio->contenido['foto_evidencia'])) {
                    $datos['foto_evidencia'] = $registroPrevio->contenido['foto_evidencia'];
                }
            }

            // 4. GUARDADO FINAL EN TABLA DE MÓDULOS (Formato JSON)
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