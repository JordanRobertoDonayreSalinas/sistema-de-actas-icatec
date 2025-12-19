<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ActaController;
use App\Http\Controllers\EstablecimientoController;

/*
|--------------------------------------------------------------------------
| CONFIGURACIÓN DE VERBOS PERSONALIZADOS
|--------------------------------------------------------------------------
| Cambiamos los verbos globales para que los recursos generen las URLs en español.
*/
Route::resourceVerbs([
    'create' => 'crear-acta',
    'edit'   => 'editar-acta', // Corregido a 'editar-acta' según tu última petición
]);

/*
|--------------------------------------------------------------------------
| 1. AUTENTICACIÓN
|--------------------------------------------------------------------------
*/
Route::controller(LoginController::class)->group(function () {
    Route::get('/actas-asistencia-tecnica/login', 'showLoginForm')->name('login');
    Route::post('/actas-asistencia-tecnica/login', 'login');
    Route::post('/logout', 'logout')->name('logout');
});

/*
|--------------------------------------------------------------------------
| 2. SISTEMA PROTEGIDO (Requiere Login)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    Route::get('/', function () {
        if (Auth::user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('admin.actas.index'); 
    });

    // =========================================================================
    // B. MÓDULO ADMINISTRADOR (URLs con prefijo /admin)
    // =========================================================================
    Route::prefix('admin')->name('admin.')->group(function () {
        
        // Dashboard Principal -> admin.dashboard
        Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');

        // GESTIÓN DE USUARIOS: /admin/gestionar-usuarios
        Route::prefix('gestionar-usuarios')->name('users.')->group(function () {
            Route::get('/', [AdminController::class, 'usersIndex'])->name('index');
            Route::get('/crear-usuario', [AdminController::class, 'usersCreate'])->name('create');
            Route::post('/', [AdminController::class, 'usersStore'])->name('store');
            Route::get('/{user}/editar-usuario', [AdminController::class, 'usersEdit'])->name('edit');
            Route::put('/{user}', [AdminController::class, 'usersUpdate'])->name('update');
            Route::delete('/{user}', [AdminController::class, 'usersDestroy'])->name('destroy');
            Route::patch('/{user}/toggle-status', [AdminController::class, 'toggleStatus'])->name('toggleStatus');
        });

        // LISTADO DE ACTAS: /admin/listado-actas
        // create -> /admin/listado-actas/crear-acta
        // edit   -> /admin/listado-actas/{id}/editar-acta
        Route::resource('listado-actas', ActaController::class)
            ->names('actas')
            ->parameters(['listado-actas' => 'acta']);
    });

    // =========================================================================
    // C. MÓDULO OPERATIVO Y UTILIDADES
    // =========================================================================
    Route::get('/establecimientos/buscar', [EstablecimientoController::class, 'buscar'])->name('establecimientos.buscar');

    Route::controller(ActaController::class)->group(function () {
        Route::get('/acta-asistencia-tecnica/{id}/pdf', 'generarPDF')->name('actas.generarPDF');
        Route::post('/acta-asistencia-tecnica/{id}/subir-pdf', 'subirPDF')->name('actas.subirPDF');
    });
});