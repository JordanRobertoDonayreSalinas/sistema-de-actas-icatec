<?php

namespace App\Http\Controllers;

use App\Models\DocumentoAdministrativo;
use App\Models\Establecimiento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class DocumentoAdministrativoController extends Controller
{
    /**
     * Listado principal con filtros avanzados.
     */
    public function index(Request $request)
    {
        // Fechas por defecto (Mes actual)
        $fecha_inicio = $request->input('fecha_inicio', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $fecha_fin = $request->input('fecha_fin', Carbon::now()->format('Y-m-d'));

        $query = DocumentoAdministrativo::with(['establecimiento', 'user']);

        // 1. Filtro General (Busca por DNI, Nombre o Apellido del Profesional)
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('profesional_doc', 'like', "%{$search}%")
                    ->orWhere('profesional_nombre', 'like', "%{$search}%")
                    ->orWhere('profesional_apellido_paterno', 'like', "%{$search}%")
                    ->orWhere('profesional_apellido_materno', 'like', "%{$search}%");
            });
        }

        // 2. Filtros de Ubicación (Relación con Establecimiento)
        if ($request->filled('provincia')) {
            $query->whereHas('establecimiento', function ($q) use ($request) {
                $q->where('provincia', $request->provincia);
            });
        }

        if ($request->filled('distrito')) {
            $query->whereHas('establecimiento', function ($q) use ($request) {
                $q->where('distrito', $request->distrito);
            });
        }

        if ($request->filled('establecimiento_nombre')) {
            $query->whereHas('establecimiento', function ($q) use ($request) {
                $q->where('nombre_establecimiento', 'like', "%{$request->establecimiento_nombre}%");
            });
        }

        // 3. Filtro por Estado de Firma
        if ($request->filled('estado')) {
            if ($request->estado == 'firmada') {
                $query->whereNotNull('pdf_firmado_compromiso')
                    ->whereNotNull('pdf_firmado_declaracion');
            } elseif ($request->estado == 'pendiente') {
                $query->where(function ($q) {
                    $q->whereNull('pdf_firmado_compromiso')
                        ->orWhereNull('pdf_firmado_declaracion');
                });
            }
        }

        // 4. Filtro de Fechas
        $query->whereBetween('fecha', [$fecha_inicio, $fecha_fin]);

        // Ejecutar consulta paginada
        $documentos = $query->orderByDesc('id')->paginate(10)->appends($request->query());

        // Estadísticas (Respetando TODOS los filtros de la consulta actual)
        $totalDocs = $documentos->total();

        $countCompletados = (clone $query)
            ->whereNotNull('pdf_firmado_compromiso')
            ->whereNotNull('pdf_firmado_declaracion')
            ->count();

        $countPendientes = $totalDocs - $countCompletados;

        // Listas para los Selects de Filtros (Carga optimizada)
        $provincias = Establecimiento::select('provincia')->distinct()->orderBy('provincia')->pluck('provincia');

        // Si hay provincia seleccionada, cargamos solo sus distritos, si no, todos
        $distritosQuery = Establecimiento::select('distrito')->distinct()->orderBy('distrito');
        if ($request->filled('provincia')) {
            $distritosQuery->where('provincia', $request->provincia);
        }
        $distritos = $distritosQuery->pluck('distrito');

        return view('usuario.documentos_administrativos.index', compact(
            'documentos',
            'fecha_inicio',
            'fecha_fin',
            'countCompletados',
            'countPendientes',
            'provincias',
            'distritos'
        ));
    }

    public function create()
    {
        $detalle = (object) ['contenido' => []];
        return view('usuario.documentos_administrativos.create', compact('detalle'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'establecimiento_id' => 'required|exists:establecimientos,id',
            'fecha' => 'required|date',
            'sistemas_acceso' => 'required|array',
            'area_oficina' => 'required',
            'cargo_rol' => 'required',
            'contenido.solicitante.doc' => 'required',
            'contenido.solicitante.tipo_doc' => 'required',
            'contenido.solicitante.nombres' => 'required',
            'contenido.solicitante.apellido_paterno' => 'required',
            'contenido.solicitante.apellido_materno' => 'required',
        ]);

        $datosProf = $request->input('contenido.solicitante');

        $doc = new DocumentoAdministrativo();
        $doc->establecimiento_id = $request->establecimiento_id;
        $doc->fecha = $request->fecha;
        $doc->tipo_formato = 'AMBOS'; // Forzado según tu requerimiento anterior
        $doc->sistemas_acceso = implode(', ', $request->sistemas_acceso);
        $doc->correo_electronico = $datosProf['email'] ?? null;

        $doc->profesional_tipo_doc = $datosProf['tipo_doc'];
        $doc->profesional_doc = $datosProf['doc'];
        $doc->profesional_nombre = mb_strtoupper($datosProf['nombres'], 'UTF-8');
        $doc->profesional_apellido_paterno = mb_strtoupper($datosProf['apellido_paterno'], 'UTF-8');
        $doc->profesional_apellido_materno = mb_strtoupper($datosProf['apellido_materno'], 'UTF-8');
        $doc->profesional_telefono = $datosProf['telefono'] ?? null;
        $doc->profesional_cargo = $datosProf['cargo'] ?? null;

        $doc->area_oficina = mb_strtoupper($request->area_oficina, 'UTF-8');
        $doc->cargo_rol = mb_strtoupper($request->cargo_rol, 'UTF-8');

        $doc->user_id = Auth::id();
        $doc->save();

        return redirect()->route('usuario.documentos.index')
            ->with('success', 'Registro creado con éxito. Puede generar ambos documentos.');
    }

    public function edit($id)
    {
        $doc = DocumentoAdministrativo::with('establecimiento')->findOrFail($id);

        // Preparar el objeto detalle con los datos del profesional para el componente
        $detalle = (object) [
            'contenido' => [
                'solicitante' => [
                    'tipo_doc' => $doc->profesional_tipo_doc,
                    'doc' => $doc->profesional_doc,
                    'nombres' => $doc->profesional_nombre,
                    'apellido_paterno' => $doc->profesional_apellido_paterno,
                    'apellido_materno' => $doc->profesional_apellido_materno,
                    'email' => $doc->correo_electronico,
                    'telefono' => $doc->profesional_telefono,
                    'cargo' => $doc->profesional_cargo,
                ]
            ]
        ];

        return view('usuario.documentos_administrativos.edit', compact('doc', 'detalle'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'establecimiento_id' => 'required|exists:establecimientos,id',
            'fecha' => 'required|date',
            'sistemas_acceso' => 'required|array',
            'area_oficina' => 'required',
            'cargo_rol' => 'required',
            'contenido.solicitante.doc' => 'required',
            'contenido.solicitante.tipo_doc' => 'required',
            'contenido.solicitante.nombres' => 'required',
            'contenido.solicitante.apellido_paterno' => 'required',
            'contenido.solicitante.apellido_materno' => 'required',
        ]);

        $doc = DocumentoAdministrativo::findOrFail($id);
        $datosProf = $request->input('contenido.solicitante');

        $doc->establecimiento_id = $request->establecimiento_id;
        $doc->fecha = $request->fecha;
        $doc->sistemas_acceso = implode(', ', $request->sistemas_acceso);
        $doc->correo_electronico = $datosProf['email'] ?? null;

        $doc->profesional_tipo_doc = $datosProf['tipo_doc'];
        $doc->profesional_doc = $datosProf['doc'];
        $doc->profesional_nombre = mb_strtoupper($datosProf['nombres'], 'UTF-8');
        $doc->profesional_apellido_paterno = mb_strtoupper($datosProf['apellido_paterno'], 'UTF-8');
        $doc->profesional_apellido_materno = mb_strtoupper($datosProf['apellido_materno'], 'UTF-8');
        $doc->profesional_telefono = $datosProf['telefono'] ?? null;
        $doc->profesional_cargo = $datosProf['cargo'] ?? null;

        $doc->area_oficina = mb_strtoupper($request->area_oficina, 'UTF-8');
        $doc->cargo_rol = mb_strtoupper($request->cargo_rol, 'UTF-8');

        $doc->save();

        return redirect()->route('usuario.documentos.index')
            ->with('success', 'Documento actualizado exitosamente.');
    }

    public function generarPDF(Request $request, $id)
    {
        $doc = DocumentoAdministrativo::with('establecimiento')->findOrFail($id);
        $tipo = $request->query('tipo');

        if ($tipo === 'compromiso') {
            $vista = 'usuario.documentos_administrativos.pdf.compromiso';
            $nombreArchivo = "Compromiso_{$doc->profesional_doc}.pdf";
        } elseif ($tipo === 'declaracion') {
            $vista = 'usuario.documentos_administrativos.pdf.declaracion';
            $nombreArchivo = "DJ_{$doc->profesional_doc}.pdf";
        } else {
            return back()->with('error', 'Debe especificar el tipo de documento.');
        }

        $pdf = Pdf::loadView($vista, compact('doc'))->setPaper('a4', 'portrait');
        return $pdf->stream($nombreArchivo);
    }

    public function subirFirmado(Request $request, $id)
    {
        $request->validate([
            'pdf_firmado' => 'required|mimes:pdf|max:20480',
            'tipo_doc' => 'required|in:compromiso,declaracion'
        ]);

        $doc = DocumentoAdministrativo::findOrFail($id);
        $columna = $request->tipo_doc === 'compromiso' ? 'pdf_firmado_compromiso' : 'pdf_firmado_declaracion';
        $carpeta = 'documentos/administrativos/firmados';

        if ($doc->$columna && Storage::disk('public')->exists($doc->$columna)) {
            Storage::disk('public')->delete($doc->$columna);
        }

        $path = $request->file('pdf_firmado')->store($carpeta, 'public');
        $doc->update([$columna => $path]);

        return response()->json(['success' => true, 'message' => 'Archivo cargado correctamente']);
    }
}