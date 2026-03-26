<?php

namespace App\Http\Controllers;

use App\Mail\IncidenciaConfirmacion;
use App\Mail\IncidenciaNotificacionAdmin;
use App\Models\Establecimiento;
use App\Models\Incidencia;
use App\Models\RespuestaIncidencia;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class MesaAyudaController extends Controller
{
    // ─────────────────────────────────────────────────────────
    // PÚBLICO: Formulario de reporte de incidencia
    // ─────────────────────────────────────────────────────────

    public function formulario()
    {
        $modulos = [
            'gestion_administrativa'  => 'Gestión Administrativa',
            'citas'                   => 'Citas',
            'triaje'                  => 'Triaje',
            'consulta_medicina'       => 'Consulta Externa – Medicina',
            'consulta_odontologia'    => 'Consulta Externa – Odontología',
            'consulta_nutricion'      => 'Consulta Externa – Nutrición',
            'consulta_psicologia'     => 'Consulta Externa – Psicología',
            'cred'                    => 'CRED',
            'inmunizaciones'          => 'Inmunizaciones',
            'atencion_prenatal'       => 'Atención Prenatal',
            'planificacion_familiar'  => 'Planificación Familiar',
            'parto'                   => 'Parto',
            'puerperio'               => 'Puerperio',
            'fua_electronico'         => 'FUA Electrónico',
            'farmacia'                => 'Farmacia',
            'referencias'             => 'Referencias',
            'laboratorio'             => 'Laboratorio',
            'urgencias'               => 'Urgencias/Emergencias',
            'otro'                    => 'Otro',
        ];

        return view('usuario.mesa-ayuda.formulario', compact('modulos'));
    }

    // ─────────────────────────────────────────────────────────
    // PÚBLICO: Guardar incidencia
    // ─────────────────────────────────────────────────────────

    public function store(Request $request)
    {
        try {
            // Validar imágenes primero
            if (!$request->hasFile('imagenes')) {
                return response()->json(['message' => 'Debe adjuntar al menos una imagen como evidencia.'], 422);
            }

            $imagenes = $request->file('imagenes');
            if (!is_array($imagenes) || count($imagenes) > 3) {
                return response()->json(['message' => 'Solo se pueden adjuntar hasta 3 imágenes.'], 422);
            }

            foreach ($imagenes as $archivo) {
                if (!$archivo->isValid()) {
                    return response()->json(['message' => 'Una o más imágenes no son válidas.'], 422);
                }
                $ext  = strtolower($archivo->getClientOriginalExtension());
                $mime = $archivo->getMimeType();
                if (!in_array($ext, ['jpg', 'jpeg', 'png']) || !str_starts_with($mime, 'image/')) {
                    return response()->json(['message' => 'Las imágenes deben ser JPG, JPEG o PNG.'], 422);
                }
                if ($archivo->getSize() > 5 * 1024 * 1024) {
                    return response()->json([
                        'message' => "La imagen '{$archivo->getClientOriginalName()}' excede los 5 MB."
                    ], 422);
                }
            }

            // Validar formulario
            $validated = $request->validate([
                'dni'                        => 'required|digits:8',
                'apellidos'                  => 'required|string|max:100',
                'nombres'                    => 'required|string|max:100',
                'celular'                    => 'required|digits:9',
                'correo'                     => 'required|email',
                'codigo_ipress'              => 'required|string',
                'nombre_establecimiento'     => 'required|string',
                'distrito_establecimiento'   => 'required|string',
                'provincia_establecimiento'  => 'required|string',
                'categoria'                  => 'required|string|max:20',
                'red'                        => 'required|string|max:100',
                'microred'                   => 'required|string|max:100',
                'modulos'                    => 'required|string|max:100',
                'observacion'                => 'required|string|max:2000',
            ]);

            // Guardar incidencia
            $incidencia = Incidencia::create([
                ...$validated,
                'estado' => 'Pendiente',
            ]);

            // Guardar imágenes
            $paths = [];
            foreach ($imagenes as $img) {
                $filename = Str::uuid() . '.' . $img->getClientOriginalExtension();
                $ruta     = $img->storeAs('incidencias', $filename, 'public');
                $paths[]  = $ruta;
            }

            $incidencia->update([
                'imagen1' => $paths[0] ?? null,
                'imagen2' => $paths[1] ?? null,
                'imagen3' => $paths[2] ?? null,
            ]);

            // Enviar correos (sin romper el flujo si falla)
            try {
                // 1. Confirmación al reportante
                Mail::to($incidencia->correo)->send(new IncidenciaConfirmacion($incidencia));

                // 2. Notificación a todos los administradores
                $admins = User::where('role', 'admin')->get();
                foreach ($admins as $admin) {
                    if (!empty($admin->email)) {
                        Mail::to($admin->email)->send(new IncidenciaNotificacionAdmin($incidencia));
                    }
                }
            } catch (\Throwable $mailEx) {
                Log::warning('⚠️ No se pudo enviar correo de incidencia', [
                    'error'       => $mailEx->getMessage(),
                    'incidencia'  => $incidencia->id,
                ]);
            }

            return response()->json([
                'message' => '✅ Incidencia registrada correctamente. El equipo técnico la atenderá a la brevedad.',
                'ticket'  => $incidencia->id,
            ]);

        } catch (\Throwable $e) {
            Log::error('❌ Error al guardar incidencia Mesa de Ayuda', [
                'error' => $e->getMessage(),
                'line'  => $e->getLine(),
                'file'  => $e->getFile(),
            ]);
            return response()->json(['message' => 'Error interno al registrar la incidencia. Intente nuevamente.'], 500);
        }
    }

    // ─────────────────────────────────────────────────────────
    // AJAX PÚBLICO: Buscar establecimiento por código IPRESS
    // ─────────────────────────────────────────────────────────

    public function buscarEstablecimiento(Request $request)
    {
        $codigo = $request->query('codigo');

        $establecimiento = Establecimiento::where('codigo', $codigo)->first();

        if (!$establecimiento) {
            return response()->json(['found' => false, 'message' => 'Código IPRESS no encontrado.'], 404);
        }

        return response()->json([
            'found'    => true,
            'nombre'   => $establecimiento->nombre,
            'distrito' => $establecimiento->distrito,
            'provincia'=> $establecimiento->provincia,
            'categoria'=> $establecimiento->categoria,
            'red'      => $establecimiento->red,
            'microred' => $establecimiento->microred,
        ]);
    }

    // ─────────────────────────────────────────────────────────
    // PROTEGIDO: Listado de incidencias para técnicos
    // ─────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $query = Incidencia::query();

        if ($request->filled('ticket')) {
            $query->where('id', $request->ticket);
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('provincia')) {
            $query->where('provincia_establecimiento', $request->provincia);
        }

        $incidencias = $query->orderByDesc('created_at')->paginate(12)->appends($request->query());

        $provincias = Incidencia::distinct()->orderBy('provincia_establecimiento')->pluck('provincia_establecimiento');

        $stats = [
            'total'      => Incidencia::count(),
            'pendientes' => Incidencia::where('estado', 'Pendiente')->count(),
            'en_proceso' => Incidencia::where('estado', 'En proceso')->count(),
            'resueltos'  => Incidencia::where('estado', 'Resuelto')->count(),
        ];

        return view('usuario.mesa-ayuda.index', compact('incidencias', 'provincias', 'stats'));
    }

    // ─────────────────────────────────────────────────────────
    // PROTEGIDO: Formulario de respuesta
    // ─────────────────────────────────────────────────────────

    public function responder($id)
    {
        $incidencia = Incidencia::with('respuestas.usuario')->findOrFail($id);

        // Cambiar automáticamente a "En proceso" si está pendiente
        if ($incidencia->estado === 'Pendiente') {
            $incidencia->update(['estado' => 'En proceso']);
        }

        return view('usuario.mesa-ayuda.responder', compact('incidencia'));
    }

    // ─────────────────────────────────────────────────────────
    // PROTEGIDO: Guardar respuesta
    // ─────────────────────────────────────────────────────────

    public function guardarRespuesta(Request $request, $id)
    {
        try {
            $request->validate([
                'respuesta' => 'required|string|max:2000',
                'estado'    => 'required|in:En proceso,Resuelto',
                'imagenes'  => 'nullable|array|max:3',
                'imagenes.*'=> 'nullable|image|mimes:jpg,jpeg,png|max:5120',
            ]);

            $incidencia = Incidencia::findOrFail($id);

            $paths = [];
            if ($request->hasFile('imagenes')) {
                foreach ($request->file('imagenes') as $img) {
                    if ($img->isValid()) {
                        $filename = Str::uuid() . '.' . $img->getClientOriginalExtension();
                        $paths[]  = $img->storeAs('respuestas', $filename, 'public');
                    }
                }
            }

            RespuestaIncidencia::create([
                'incidencia_id' => $incidencia->id,
                'user_id'       => auth()->id(),
                'respuesta'     => strtoupper($request->respuesta),
                'estado'        => $request->estado,
                'imagen1'       => $paths[0] ?? null,
                'imagen2'       => $paths[1] ?? null,
                'imagen3'       => $paths[2] ?? null,
            ]);

            $incidencia->update(['estado' => $request->estado]);

            return redirect()
                ->route('usuario.mesa-ayuda.index')
                ->with('success', '✅ Respuesta registrada correctamente.');

        } catch (\Throwable $e) {
            Log::error('❌ Error al guardar respuesta de incidencia', [
                'error' => $e->getMessage(),
                'line'  => $e->getLine(),
            ]);
            return back()->withInput()->with('error', 'Error al guardar la respuesta. Intente nuevamente.');
        }
    }
}
