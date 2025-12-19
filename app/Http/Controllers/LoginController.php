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
        // --- VALIDACIÓN CON MENSAJES EN ESPAÑOL ---
        $credentials = $request->validate([
            'username' => ['required', 'string', 'size:8'], // DNI: 8 dígitos exactos
            'password' => ['required'],
        ], [
            'username.required' => 'Por favor, ingresa tu usuario.',
            'username.size'     => 'El usuario debe tener exactamente 8 dígitos.',
            'password.required' => 'Debes ingresar tu contraseña.',
        ]);

        // Intentar loguear
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // --- LÓGICA DE ROLES ---
            // Obtenemos el usuario que acaba de entrar
            $user = Auth::user();

            // Decidimos a dónde enviarlo según su rol
            // NOTA: Asegúrate de tener una ruta llamada 'admin.dashboard' en tu web.php
            if ($user->role === 'admin') {
                $rutaDestino = route('admin.dashboard'); 
            } else {
                $rutaDestino = route('actas.index');
            }

            // --- RESPUESTA PARA LA ANIMACIÓN (AJAX) ---
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true, 
                    'redirect' => $rutaDestino // Redirección dinámica
                ]);
            }

            // --- RESPUESTA NORMAL ---
            return redirect()->intended($rutaDestino); 
        }

        // --- SI FALLA EL LOGIN (Credenciales incorrectas) ---
        
        // Error para la animación (AJAX)
        if ($request->wantsJson()) {
            return response()->json([
                'success' => false, 
                'message' => 'Credenciales incorrectas. Inténtalo de nuevo.'
            ], 422);
        }

        // Error normal (recarga la página)
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