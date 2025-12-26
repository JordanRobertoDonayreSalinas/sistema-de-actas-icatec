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
     * Muestra estadísticas personales del implementador logueado.
     * Vista: resources/views/usuario/dashboard/dashboard.blade.php
     */
    public function index()
    {
        // Construimos el nombre exacto como se guarda en el campo 'implementador' de las actas
        $nombreCompleto = trim(Auth::user()->apellido_paterno . ' ' . Auth::user()->apellido_materno . ' ' . Auth::user()->name);
        
        $totalActas = Acta::where('implementador', $nombreCompleto)->count(); 

        // Ranking de establecimientos donde el usuario ha trabajado (Top 5)
        $topEstablecimientos = Establecimiento::withCount(['actas' => function($query) use ($nombreCompleto) {
                $query->where('implementador', $nombreCompleto);
            }])
            ->having('actas_count', '>', 0)
            ->orderBy('actas_count', 'desc')
            ->take(5)
            ->get();

        // Mapeo de meses completos en español
        $mesesEspañol = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 5 => 'Mayo', 6 => 'Junio',
            7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
        ];

        // Actas por mes del año actual (Orden DESC: más reciente primero)
        $actasPorMesRaw = Acta::select(
                DB::raw('MONTH(fecha) as month'),
                DB::raw('COUNT(*) as total')
            )
            ->where('implementador', $nombreCompleto)
            ->whereYear('fecha', date('Y'))
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->get();

        $actasPorMes = $actasPorMesRaw->map(function ($item) use ($mesesEspañol) {
            return [
                'nombre_mes' => $mesesEspañol[$item->month] ?? 'S/N', 
                'total' => $item->total
            ];
        }); 

        return view('usuario.dashboard.dashboard', compact('totalActas', 'actasPorMes', 'topEstablecimientos'));
    }

    /**
     * LISTADO DE ASISTENCIAS TÉCNICAS
     * Vista: resources/views/usuario/asistencia/index.blade.php
     */
    public function actasIndex(Request $request)
    {
        $query = Acta::query();

        // Aplicación de filtros dinámicos
        if ($request->filled('implementador')) {
            $query->where('implementador', $request->implementador);
        }
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

        // Datos para cargar los selects de los filtros
        $implementadores = Acta::distinct()->pluck('implementador');
        $provincias = Establecimiento::distinct()->pluck('provincia');

        // Contadores para la tarjeta superior
        $countFirmadas = Acta::where('firmado', 1)->count();
        $countPendientes = Acta::where('firmado', 0)->count();

        return view('usuario.asistencia.index', compact('actas', 'implementadores', 'provincias', 'countFirmadas', 'countPendientes'));
    }

    /**
     * CREAR ACTA (FORMULARIO)
     * Vista: resources/views/usuario/asistencia/create.blade.php
     */
    public function actasCreate()
    {
        return view('usuario.asistencia.create');
    }

    /**
     * EDITAR ACTA (FORMULARIO)
     * Vista: resources/views/usuario/asistencia/edit.blade.php
     */
    public function actasEdit(Acta $acta)
    {
        return view('usuario.asistencia.edit', compact('acta'));
    }

    /**
     * SUBIR O REEMPLAZAR PDF FIRMADO (Acción vía listado)
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
     * LISTADO DE MONITOREO
     * Vista: resources/views/usuario/monitoreo/index.blade.php
     */
    public function monitoreoIndex(Request $request)
    {
        // Filtrar actas que sean de tipo monitoreo (según tu lógica de 'tema')
        $query = Acta::where('tema', 'like', '%Monitoreo%');

        if ($request->filled('implementador')) {
            $query->where('implementador', $request->implementador);
        }

        $monitoreos = $query->orderBy('fecha', 'desc')->paginate(10);
        
        $implementadores = Acta::distinct()->pluck('implementador');
        $provincias = Establecimiento::distinct()->pluck('provincia');
        $countCompletados = $monitoreos->where('firmado', 1)->count();

        return view('usuario.monitoreo.index', compact('monitoreos', 'implementadores', 'provincias', 'countCompletados'));
    }

    /**
     * GESTIÓN DE PERFIL
     * Vista: resources/views/usuario/perfil/perfil.blade.php
     */
    public function perfil()
    {
        $user = Auth::user();
        return view('usuario.perfil.perfil', compact('user'));
    }

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