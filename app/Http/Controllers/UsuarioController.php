<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Acta;
use App\Models\Establecimiento;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use Carbon\Carbon;

class UsuarioController extends Controller
{
    /**
     * DASHBOARD DEL USUARIO
     * Muestra estadísticas GLOBALES e HISTÓRICAS ordenadas por fecha reciente.
     * Vista: resources/views/usuario/dashboard/dashboard.blade.php
     */
    public function index()
    {
        // 1. Total de Actas global histórico (sin filtro de año)
        $totalActas = Acta::count(); 

        // 2. Mapeo de meses en español
        $mesesEspañol = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 
            5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto', 
            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
        ];

        // 3. Actas por mes y año (Ordenado de más reciente a más antiguo)
        // Se agrupa por año y mes para diferenciar periodos (ej. Enero 2025 vs Enero 2026)
        $actasPorMes = Acta::selectRaw('YEAR(fecha) as anio, MONTH(fecha) as mes, COUNT(*) as total')
            ->groupBy('anio', 'mes')
            ->orderBy('anio', 'desc') 
            ->orderBy('mes', 'desc') 
            ->get()
            ->map(function($item) use ($mesesEspañol) {
                return [
                    // Formato: "Mes Año" (ej: Enero 2026)
                    'nombre_mes' => $mesesEspañol[$item->mes] . ' ' . $item->anio,
                    'total' => $item->total
                ];
            });

        // 4. Ranking de establecimientos histórico global (Top 5 con más actividad)
        $topEstablecimientos = Establecimiento::withCount('actas')
            ->having('actas_count', '>', 0)
            ->orderBy('actas_count', 'desc')
            ->take(5)
            ->get();

        return view('usuario.dashboard.dashboard', compact('totalActas', 'actasPorMes', 'topEstablecimientos'));
    }

    /**
     * LISTADO DE ASISTENCIAS TÉCNICAS (Vista Global Histórica)
     */
    public function actasIndex(Request $request)
    {
        $query = Acta::query();

        // Filtros dinámicos sobre todas las actas del sistema
        if ($request->filled('provincia')) {
            $query->whereHas('establecimiento', function($q) use ($request) {
                $q->where('provincia', $request->provincia);
            });
        }
        if ($request->filled('firmado')) {
            $query->where('firmado', $request->firmado);
        }
        if ($request->filled('fecha_inicio') && $request->filled('fecha_fin')) {
            $query->whereBetween('fecha', [$request->fecha_inicio, $request->fecha_fin]);
        }

        $actas = $query->orderBy('fecha', 'desc')->paginate(10);
        
        $provincias = Establecimiento::distinct()->pluck('provincia');
        
        // Contadores globales históricos
        $countFirmadas = Acta::where('firmado', 1)->count();
        $countPendientes = Acta::where('firmado', 0)->count();

        return view('usuario.asistencia.index', compact('actas', 'provincias', 'countFirmadas', 'countPendientes'));
    }

    /**
     * SUBIR O REEMPLAZAR PDF FIRMADO
     */
    public function subirPDF(Request $request, $id)
    {
        $request->validate([
            'pdf_firmado' => 'required|mimes:pdf|max:5120',
        ]);

        $acta = Acta::findOrFail($id);

        if ($request->hasFile('pdf_firmado')) {
            // Eliminar archivo físico anterior si existe
            if ($acta->firmado_pdf && Storage::disk('public')->exists($acta->firmado_pdf)) {
                Storage::disk('public')->delete($acta->firmado_pdf);
            }

            // Guardar nuevo archivo
            $path = $request->file('pdf_firmado')->store('actas_firmadas', 'public');
            
            $acta->update([
                'firmado_pdf' => $path,
                'firmado' => 1
            ]);
        }

        return back()->with('success', 'Archivo PDF cargado correctamente.');
    }

    /**
     * LISTADO DE MONITOREO (Vista Global Histórica)
     */
    public function monitoreoIndex(Request $request)
    {
        // Filtrar todas las actas que contengan "Monitoreo" en el sistema
        $query = Acta::where('tema', 'like', '%Monitoreo%');

        $monitoreos = $query->orderBy('fecha', 'desc')->paginate(10);
        $countCompletados = (clone $query)->where('firmado', 1)->count();

        return view('usuario.monitoreo.index', compact('monitoreos', 'countCompletados'));
    }

    /**
     * GESTIÓN DE PERFIL
     */
    public function perfil()
    {
        $user = Auth::user();
        return view('usuario.perfil.perfil', compact('user'));
    }

    /**
     * ACTUALIZAR PERFIL
     */
    public function perfilUpdate(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name'             => 'required|string|max:255',
            'apellido_paterno' => 'required|string|max:255',
            'apellido_materno' => 'required|string|max:255',
            'email'            => 'required|email|unique:users,email,' . $user->id,
            'password'         => ['nullable', 'confirmed', Password::defaults()],
        ]);

        $user->fill($request->only('name', 'apellido_paterno', 'apellido_materno', 'email'));

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return back()->with('success', 'Tu perfil ha sido actualizado correctamente.');
    }
}