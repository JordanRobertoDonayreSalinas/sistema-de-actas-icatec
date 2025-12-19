<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Acta; 
use App\Models\Establecimiento; 
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB; 
use Carbon\Carbon; 

class AdminController extends Controller
{
    /**
     * =========================================================================
     * 1. DASHBOARD PRINCIPAL
     * =========================================================================
     */
    public function index()
    {
        $totalActas = Acta::count(); 

        // Ranking de Establecimientos (Top 10 con más actas)
        $topEstablecimientos = Establecimiento::withCount('actas')
            ->having('actas_count', '>', 0)
            ->orderBy('actas_count', 'desc')
            ->take(10)
            ->get();

        // Actas por Mes (Para gráficos o listas)
        $actasPorMes = Acta::select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->take(12) 
            ->get();

        Carbon::setLocale('es');
        
        $actasPorMes = $actasPorMes->map(function ($item) {
            $fecha = Carbon::createFromDate($item->year, $item->month, 1);
            return [
                'nombre_mes' => ucfirst($fecha->translatedFormat('F Y')), 
                'total' => $item->total
            ];
        });

        return view('admin.dashboard', compact('totalActas', 'actasPorMes', 'topEstablecimientos'));
    }

    /**
     * =========================================================================
     * 2. GESTIÓN DE USUARIOS
     * =========================================================================
     */

    // LISTAR
    public function usersIndex()
    {
        $users = User::orderBy('id', 'desc')->paginate(10);
        return view('admin.users.index', compact('users'));
    }

    // CREAR VISTA
    public function usersCreate()
    {
        return view('admin.users.create');
    }

    // GUARDAR EN BD
    public function usersStore(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email', // Validación de Email obligatoria
            'username' => 'required|digits:8|unique:users',
            'role'     => 'required|in:admin,user',
            'password' => 'required|string|min:6|confirmed',
        ]);

        User::create([
            'name'      => $request->name,
            'email'     => $request->email, // Guardar Email
            'username'  => $request->username,
            'role'      => $request->role,
            'password'  => Hash::make($request->password),
            'is_active' => true, // Por defecto activo
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'Usuario creado correctamente.');
    }

    // EDITAR VISTA
    public function usersEdit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    // ACTUALIZAR EN BD
    public function usersUpdate(Request $request, User $user)
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            // Validar email único ignorando el ID actual del usuario
            'email'     => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'username'  => ['required', 'digits:8', Rule::unique('users')->ignore($user->id)],
            'role'      => 'required|in:admin,user',
            'is_active' => 'boolean', 
            'password'  => 'nullable|string|min:6|confirmed',
        ]);

        $user->name     = $request->name;
        $user->email    = $request->email;
        $user->username = $request->username;
        $user->role     = $request->role;
        
        // Actualizar estado si viene del formulario (requiere input hidden en la vista para funcionar bien)
        if ($request->has('is_active')) {
            $user->is_active = $request->is_active;
        }

        // Solo actualizar contraseña si se escribió una nueva
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('admin.users.index')
            ->with('success', 'Usuario actualizado correctamente.');
    }

    // DESACTIVAR USUARIO (SOFT DELETE LÓGICO)
    public function usersDestroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'No puedes desactivar tu propia cuenta actual.');
        }

        $user->update(['is_active' => false]);

        return redirect()->route('admin.users.index')
            ->with('success', 'El usuario ha sido desactivado.');
    }

    /**
     * =========================================================================
     * 3. MÉTODO AJAX (SWITCH DE ESTADO)
     * =========================================================================
     */
    public function toggleStatus(User $user)
    {
        // Protección: No desactivarse a sí mismo
        if ($user->id === auth()->id()) {
            return response()->json(['error' => 'No puedes cambiar el estado de tu propia cuenta.'], 403);
        }

        // Invertir estado
        $user->is_active = !$user->is_active;
        $user->save();

        return response()->json([
            'success'   => true,
            'is_active' => $user->is_active,
            'message'   => $user->is_active ? 'Usuario activado correctamente.' : 'Usuario desactivado correctamente.'
        ]);
    }
}