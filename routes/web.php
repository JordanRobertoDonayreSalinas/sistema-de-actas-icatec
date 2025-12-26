<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ActaController;
use App\Http\Controllers\MonitoreoController;
use App\Http\Controllers\EstablecimientoController;
use App\Http\Controllers\UsuarioController; 

Route::resourceVerbs([
    'create' => 'crear-acta',
    'edit'   => 'editar-acta',
]);

Route::controller(LoginController::class)->group(function () {
    Route::get('/actas/login', 'showLoginForm')->name('login');
    Route::post('/actas/login', 'login');
    Route::post('/logout', 'logout')->name('logout');
});

Route::middleware(['auth'])->group(function () {

    Route::get('/', function () {
        return Auth::user()->role === 'admin' 
            ? redirect()->route('admin.dashboard') 
            : redirect()->route('usuario.dashboard'); 
    });

    Route::prefix('usuario')->name('usuario.')->group(function () {
        
        Route::get('/dashboard', [UsuarioController::class, 'index'])->name('dashboard');
        Route::get('/mi-perfil', [UsuarioController::class, 'perfil'])->name('perfil');
        Route::put('/mi-perfil', [UsuarioController::class, 'perfilUpdate'])->name('perfil.update');

        // --- ASISTENCIA TÉCNICA ---
        Route::prefix('listado-actas')->name('actas.')->group(function () {
            Route::get('/', [ActaController::class, 'index'])->name('index');
            Route::get('/crear-acta', [ActaController::class, 'create'])->name('create');
            Route::post('/', [ActaController::class, 'store'])->name('store');
            Route::get('/{acta}/editar-acta', [ActaController::class, 'edit'])->name('edit');
            Route::put('/{acta}', [ActaController::class, 'update'])->name('update');
            Route::get('/{acta}', [ActaController::class, 'show'])->name('show');
            Route::get('/{id}/pdf', [ActaController::class, 'generarPDF'])->name('generarPDF');
            Route::post('/{id}/subir-pdf', [ActaController::class, 'subirPDF'])->name('subirPDF');
        });

        // --- MONITOREO (Sistema Modular) ---
        Route::prefix('monitoreo')->name('monitoreo.')->group(function () {
            Route::get('/', [MonitoreoController::class, 'index'])->name('index');
            Route::get('/crear-acta', [MonitoreoController::class, 'create'])->name('create');
            Route::post('/', [MonitoreoController::class, 'store'])->name('store');
            Route::get('/{monitoreo}/editar-acta', [MonitoreoController::class, 'edit'])->name('edit');
            Route::put('/{monitoreo}', [MonitoreoController::class, 'update'])->name('update');
            Route::get('/{monitoreo}', [MonitoreoController::class, 'show'])->name('show');

            // PASO 2: Panel General y Redirección a Módulos Específicos
            Route::get('/{id}/modulos', [MonitoreoController::class, 'gestionarModulos'])->name('modulos');
            
            // RUTA DINÁMICA PARA REDIRECCIÓN A FORMULARIOS INDEPENDIENTES
            Route::get('/{id}/modulo/{seccion}', [MonitoreoController::class, 'cargarSeccionModulo'])->name('seccion');
            
            Route::post('/{id}/guardar-detalle', [MonitoreoController::class, 'guardarDetalle'])->name('guardarDetalle');

            Route::get('/{id}/pdf', [MonitoreoController::class, 'generarPDF'])->name('pdf');
            Route::post('/{id}/subir-pdf', [MonitoreoController::class, 'subirPDF'])->name('subirPDF');
        });
    });

    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');
        Route::prefix('gestionar-usuarios')->name('users.')->group(function () {
            Route::get('/', [AdminController::class, 'usersIndex'])->name('index');
            Route::get('/crear-usuario', [AdminController::class, 'usersCreate'])->name('create');
            Route::post('/', [AdminController::class, 'usersStore'])->name('store');
            Route::get('/{user}/editar-usuario', [AdminController::class, 'usersEdit'])->name('edit');
            Route::put('/{user}', [AdminController::class, 'usersUpdate'])->name('update');
            Route::delete('/{user}', [AdminController::class, 'usersDestroy'])->name('destroy');
            Route::patch('/{user}/toggle-status', [AdminController::class, 'toggleStatus'])->name('toggleStatus');
        });
    });

    Route::get('/establecimientos/buscar', [EstablecimientoController::class, 'buscar'])->name('establecimientos.buscar');
});