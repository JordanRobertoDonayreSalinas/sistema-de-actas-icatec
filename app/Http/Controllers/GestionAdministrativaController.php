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
use Illuminate\Support\Facades\Http;

class GestionAdministrativaController extends Controller
{
    /**
     * Busca un profesional por su número de documento.
     */
    public function buscarProfesional($doc)
    {
        $doc = trim($doc);
        $profesional = Profesional::where('doc', $doc)->first();
        if ($profesional) {
            return response()->json([
                'exists'           => true,
                'exists_external'  => false,
                'tipo_doc'         => $profesional->tipo_doc,
                'apellido_paterno' => $profesional->apellido_paterno,
                'apellido_materno' => $profesional->apellido_materno,
                'nombres'          => $profesional->nombres,
                'email'            => $profesional->email,
                'telefono'         => $profesional->telefono,
                'cargo'            => $profesional->cargo, // Retorna el cargo para el formulario
            ]);
        }

        // Retornar solo resultado local si se requiere (para el flujo en 2 pasos del frontend)
        if (request()->has('local_only')) {
            return response()->json(['exists' => false, 'exists_external' => false]);
        }

        // Si no existe localmente, y es un DNI (8 dígitos), buscar en APIs externas
        if (preg_match('/^\d{8}$/', $doc)) {
            $decolecta     = new \App\Services\DecolectaService();
            $quotaExceeded = false;

            // --- Fuente 1: Decolecta ---
            $result = $decolecta->consultarDni($doc);

            if (isset($result['error']) && $result['error'] === 'quota_exceeded') {
                $quotaExceeded = true; // cupo agotado, pero seguimos con MPI Engineers
            } elseif (isset($result['success']) && $result['success']) {
                $data = $result['data'];
                return response()->json([
                    'exists'           => false,
                    'exists_external'  => true,
                    'fuente'           => 'decolecta',
                    'tipo_doc'         => 'DNI',
                    'apellido_paterno' => $data['apellido_paterno'],
                    'apellido_materno' => $data['apellido_materno'],
                    'nombres'          => $data['nombres'],
                    'email'            => '',
                    'telefono'         => '',
                    'remaining_tokens' => $data['remaining_tokens'] ?? null,
                ]);
            }

            // --- Fuente 2: MPI Engineers (fallback) ---
            $mpiResult = $decolecta->consultarMpiEngineers($doc);

            if (isset($mpiResult['success']) && $mpiResult['success']) {
                $data = $mpiResult['data'];
                return response()->json([
                    'exists'           => false,
                    'exists_external'  => true,
                    'fuente'           => 'mpi_engineers',
                    'tipo_doc'         => $data['tipo_doc'],
                    'apellido_paterno' => $data['apellido_paterno'],
                    'apellido_materno' => $data['apellido_materno'],
                    'nombres'          => $data['nombres'],
                    'email'            => $data['email'] ?? '',
                    'telefono'         => $data['telefono'] ?? '',
                    'remaining_tokens' => null,
                ]);
            }

            // Si ambas APIs fallaron y Decolecta tenía el cupo excedido, informar al frontend
            if ($quotaExceeded) {
                return response()->json([
                    'exists'          => false,
                    'exists_external' => false,
                    'quota_exceeded'  => true,
                    'message'         => 'Límite mensual de validaciones en RENIEC excedido y no se pudo obtener datos de fuente alternativa.',
                ]);
            }
        }

        return response()->json(['exists' => false, 'exists_external' => false]);
    }

    /**
     * Muestra la vista principal del Módulo 01.
     */
    public function index($id)
    {
        $acta = CabeceraMonitoreo::with(['establecimiento'])->findOrFail($id);
        $modulo = 'gestion_administrativa';

        // Intentar obtener equipos actuales
        $equipos = EquipoComputo::where('cabecera_monitoreo_id', $id)
            ->where('modulo', $modulo)
            ->get();

        // Lógica de "Guía Histórica": Si no hay equipos, buscar en la última acta
        if ($equipos->isEmpty()) {
            $ultimaActaId = CabeceraMonitoreo::where('establecimiento_id', $acta->establecimiento_id)
                ->where('id', '<', $id) 
                ->orderBy('id', 'desc')
                ->value('id');

            if ($ultimaActaId) {
                $equipos = EquipoComputo::where('cabecera_monitoreo_id', $ultimaActaId)
                    ->where('modulo', $modulo)
                    ->get();
            }
        }
        
        $detalle = MonitoreoModulos::where('cabecera_monitoreo_id', $id)
                    ->where('modulo_nombre', $modulo)
                    ->first();

        return view('usuario.monitoreo.modulos.gestion_administrativa', compact('acta', 'detalle', 'equipos'));
    }

    /**
     * Guarda los datos del módulo y sincroniza las tablas relacionadas.
     */
    public function store(Request $request, $id)
    {
        // Validación ajustada para UNA sola imagen
        $request->validate([
            'contenido.fecha' => 'required|date',
            'foto_evidencia'  => 'nullable|image|mimes:jpeg,png,jpg|max:4096',
        ]);

        try {
            DB::beginTransaction();
            $modulo = 'gestion_administrativa';
            $datos = $request->input('contenido', []);

            // 1. Sincronizar Datos del Profesional Responsable (RRHH)
            if (isset($datos['rrhh']) && !empty($datos['rrhh']['doc'])) {
                $info = $datos['rrhh'];
                
                Profesional::updateOrCreate(
                    ['doc' => trim($info['doc'])],
                    [
                        'tipo_doc'         => $info['tipo_doc'] ?? 'DNI',
                        'apellido_paterno' => mb_strtoupper(trim($info['apellido_paterno'] ?? ''), 'UTF-8'),
                        'apellido_materno' => mb_strtoupper(trim($info['apellido_materno'] ?? ''), 'UTF-8'),
                        'nombres'          => mb_strtoupper(trim($info['nombres'] ?? ''), 'UTF-8'),
                        'email'            => isset($info['email']) ? strtolower(trim($info['email'])) : null,
                        'telefono'         => $info['telefono'] ?? null,
                        'cargo'            => isset($info['cargo']) ? mb_strtoupper(trim($info['cargo']), 'UTF-8') : null,
                    ]
                );
            }

            // 2. Sincronizar Inventario de Equipos de Cómputo
            EquipoComputo::where('cabecera_monitoreo_id', $id)->where('modulo', $modulo)->delete();
            
            if ($request->has('equipos') && is_array($request->equipos)) {
                foreach ($request->equipos as $eq) {
                    if (!empty($eq['descripcion'])) {
                        EquipoComputo::create([
                            'cabecera_monitoreo_id' => $id,
                            'modulo'      => $modulo,
                            'descripcion' => mb_strtoupper(trim($eq['descripcion']), 'UTF-8'),
                            'cantidad'    => 1,
                            'estado'      => $eq['estado'] ?? 'BUENO',
                            'nro_serie'   => mb_strtoupper($eq['nro_serie'] ?? '', 'UTF-8'),
                            'propio'      => mb_strtoupper($eq['propio'] ?? 'PERSONAL', 'UTF-8'),
                            'observacion' => isset($eq['observacion']) ? mb_strtoupper(trim($eq['observacion']), 'UTF-8') : null,
                        ]);
                    }
                }
            }

            // 3. Sincronizar Tabla de Respuestas Maestras
            DB::table('mon_respuesta_entrevistado')->updateOrInsert(
                ['cabecera_monitoreo_id' => $id, 'modulo' => $modulo],
                [
                    'doc_profesional'       => $datos['rrhh']['doc'] ?? null,
                    'recibio_capacitacion'  => $datos['recibio_capacitacion'] ?? 'NO',
                    'inst_que_lo_capacito'  => $datos['inst_que_lo_capacito'] ?? null,
                    'inst_a_quien_comunica' => $datos['inst_a_quien_comunica'] ?? null,
                    'medio_que_utiliza'     => $datos['medio_que_utiliza'] ?? null,
                    'updated_at'            => now()
                ]
            );

            // 4. Gestión de Evidencia Fotográfica (UNA SOLA FOTO)
            $registroPrevio = MonitoreoModulos::where('cabecera_monitoreo_id', $id)
                                ->where('modulo_nombre', $modulo)
                                ->first();

            if ($request->hasFile('foto_evidencia')) {
                // Si existe una foto anterior, la borramos para no acumular basura
                if ($registroPrevio && !empty($registroPrevio->contenido['foto_evidencia'])) {
                    // Verificamos que sea string (ruta) y que exista el archivo
                    $oldPath = $registroPrevio->contenido['foto_evidencia'];
                    if (is_string($oldPath) && Storage::disk('public')->exists($oldPath)) {
                        Storage::disk('public')->delete($oldPath);
                    }
                }
                
                // Guardar la nueva foto
                $datos['foto_evidencia'] = $request->file('foto_evidencia')->store('evidencias_monitoreo', 'public');

            } elseif ($registroPrevio && isset($registroPrevio->contenido['foto_evidencia'])) {
                // Si no se subió nueva foto, mantenemos la ruta anterior
                $datos['foto_evidencia'] = $registroPrevio->contenido['foto_evidencia'];
            } else {
                // Si no hay foto nueva ni anterior
                $datos['foto_evidencia'] = null;
            }

            // 5. Guardar el JSON consolidado en la tabla de módulos
            MonitoreoModulos::updateOrCreate(
                ['cabecera_monitoreo_id' => $id, 'modulo_nombre' => $modulo],
                ['contenido' => $datos]
            );

            DB::commit();
            return redirect()->route('usuario.monitoreo.modulos', $id)
                             ->with('success', 'Módulo 01 sincronizado correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error en GestionAdministrativaController@store: " . $e->getMessage());
            return back()->withInput()->withErrors(['error' => 'Error al guardar los datos: ' . $e->getMessage()]);
        }
    }
}