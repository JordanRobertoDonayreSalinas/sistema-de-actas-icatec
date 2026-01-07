<?php

namespace App\Http\Controllers;

use App\Models\DocumentoAdministrativo;
use App\Models\Establecimiento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf; // Asegúrate de tener instalado dompdf

class DocumentoAdministrativoController extends Controller
{
    /**
     * Listado principal con filtros de mes actual.
     */
    public function index(Request $request)
    {
        // Filtro de fecha por defecto (Mes actual)
        $fecha_inicio = $request->input('fecha_inicio', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $fecha_fin = $request->input('fecha_fin', Carbon::now()->format('Y-m-d'));

        $query = DocumentoAdministrativo::with(['establecimiento', 'user']);

        // Filtro por estado de firma
        if ($request->filled('estado')) {
            $request->estado == 'firmada' 
                ? $query->whereNotNull('pdf_firmado_path') 
                : $query->whereNull('pdf_firmado_path');
        }

        // Filtro por rango de fechas
        $query->whereBetween('fecha', [$fecha_inicio, $fecha_fin]);

        $documentos = $query->orderByDesc('id')->paginate(10)->appends($request->query());

        // Estadísticas para las tarjetas del index
        $totalDocs = $documentos->total();
        // Clonamos la query para contar sin afectar la paginación actual
        $countCompletados = DocumentoAdministrativo::whereBetween('fecha', [$fecha_inicio, $fecha_fin])
                            ->whereNotNull('pdf_firmado_path')->count();
        $countPendientes = $totalDocs - $countCompletados;

        return view('usuario.documentos_administrativos.index', compact(
            'documentos', 
            'fecha_inicio', 
            'fecha_fin', 
            'countCompletados', 
            'countPendientes'
        ));
    }

    /**
     * Mostrar formulario de creación.
     */
    public function create()
    {
        return view('usuario.documentos_administrativos.create');
    }

    /**
     * Guardar el registro en la base de datos.
     */
    public function store(Request $request)
    {
        $request->validate([
            'establecimiento_id' => 'required|exists:establecimientos,id',
            'profesional_tipo_doc' => 'required',
            'profesional_doc' => 'required',
            'profesional_nombre' => 'required',
            'profesional_apellido_paterno' => 'required',
            'profesional_apellido_materno' => 'required',
            'tipo_formato' => 'required',
            'sistemas_acceso' => 'required|array',
            'fecha' => 'required|date'
        ]);

        $doc = new DocumentoAdministrativo($request->all());
        
        // Transformar a mayúsculas para consistencia
        $doc->profesional_nombre = mb_strtoupper($request->profesional_nombre, 'UTF-8');
        $doc->profesional_apellido_paterno = mb_strtoupper($request->profesional_apellido_paterno, 'UTF-8');
        $doc->profesional_apellido_materno = mb_strtoupper($request->profesional_apellido_materno, 'UTF-8');
        $doc->cargo_rol = mb_strtoupper($request->cargo_rol, 'UTF-8');
        $doc->area_oficina = mb_strtoupper($request->area_oficina, 'UTF-8');
        
        // Convertir array de sistemas a una cadena separada por comas
        $doc->sistemas_acceso = implode(', ', $request->sistemas_acceso);
        
        $doc->user_id = Auth::id();
        $doc->save();

        return redirect()->route('usuario.documentos.index')
            ->with('success', 'Registro creado con éxito. Ahora puede generar el PDF.');
    }

    /**
     * Generar el PDF basado en los formatos DOCX (Compromiso o Declaración).
     */
    public function generarPDF($id)
    {
        $doc = DocumentoAdministrativo::with('establecimiento')->findOrFail($id);
        
        // Seleccionar la vista del PDF según el formato
        $vista = $doc->tipo_formato === 'Compromiso' 
            ? 'usuario.documentos_administrativos.pdf.compromiso' 
            : 'usuario.documentos_administrativos.pdf.declaracion';

        $pdf = Pdf::loadView($vista, compact('doc'))
                  ->setPaper('a4', 'portrait');

        return $pdf->stream("documento_{$doc->profesional_doc}.pdf");
    }

    /**
     * Subir el escaneado del documento firmado físicamente.
     */
    public function subirFirmado(Request $request, $id)
    {
        $request->validate([
            'pdf_firmado' => 'required|mimes:pdf|max:20480' // Máximo 20MB
        ]);

        $doc = DocumentoAdministrativo::findOrFail($id);

        // Si ya existía un archivo anterior, lo eliminamos
        if ($doc->pdf_firmado_path) {
            Storage::disk('public')->delete($doc->pdf_firmado_path);
        }

        $path = $request->file('pdf_firmado')->store('documentos/administrativos/firmados', 'public');
        
        $doc->update([
            'pdf_firmado_path' => $path
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Archivo cargado correctamente'
        ]);
    }
}