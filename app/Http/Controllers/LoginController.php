<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    // 1. Mostrar el formulario
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // 2. Procesar el Login
    public function login(Request $request)
    {
        // --- VALIDACIÓN ---
        $credentials = $request->validate([
            'username' => ['required', 'string', 'size:8'],
            'password' => ['required'],
        ], [
            'username.required' => 'Por favor, ingresa tu usuario.',
            'username.size' => 'El usuario debe tener exactamente 8 dígitos.',
            'password.required' => 'Debes ingresar tu contraseña.',
        ]);

        // Intentar loguear
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $user = Auth::user();

            // --- LÓGICA DE REDIRECCIÓN POR ROLES ---
            if ($user->role === 'admin') {
                $rutaDestino = route('admin.dashboard');
            } else {
                // CAMBIO: Ahora apunta a la ruta del dashboard de usuario normal
                // Asegúrate de haber definido 'usuario.dashboard' en web.php
                $rutaDestino = route('usuario.dashboard.general');
            }

            // --- RESPUESTA PARA AJAX (JSON) ---
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'redirect' => $rutaDestino
                ]);
            }

            // --- RESPUESTA NORMAL ---
            return redirect()->intended($rutaDestino);
        }

        // --- SI FALLA EL LOGIN ---
        if ($request->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Credenciales incorrectas. Inténtalo de nuevo.'
            ], 422);
        }

        return back()->withErrors([
            'username' => 'El usuario o la contraseña son incorrectos.',
        ])->onlyInput('username');
    }

    // 3. Cerrar sesión
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}