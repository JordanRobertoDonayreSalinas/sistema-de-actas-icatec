<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Importación de Controladores
use App\Http\Controllers\LoginController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ActaController;
use App\Http\Controllers\CitaController;
use App\Http\Controllers\MonitoreoController;
use App\Http\Controllers\EditMonitoreoController;
use App\Http\Controllers\GestionAdministrativaController;
use App\Http\Controllers\GestionAdministrativaPdfController;
use App\Http\Controllers\LaboratorioController;
use App\Http\Controllers\LaboratorioPdfController;
use App\Http\Controllers\PuerperioController;
use App\Http\Controllers\PuerperioPdfController;
use App\Http\Controllers\FirmasMonitoreoController; 
use App\Http\Controllers\EstablecimientoController;
use App\Http\Controllers\PartoController;
use App\Http\Controllers\PrenatalController;
use App\Http\Controllers\ConsolidadoPdfController;
use App\Http\Controllers\UsuarioController;


use App\Http\Controllers\TriajeController;
use App\Http\Controllers\TriajePdfController;
use App\Http\Controllers\OdontologiaController;
use App\Http\Controllers\OdontologiaPdfController;
use App\Http\Controllers\PsicologiaController;
use App\Http\Controllers\PsicologiaPdfController;







// --- CONFIGURACIÓN DE VERBOS ---
Route::resourceVerbs([
    'create' => 'crear-acta',
    'edit'   => 'editar-acta',
]);

// --- AUTENTICACIÓN PÚBLICA ---
Route::controller(LoginController::class)->group(function () {
    Route::get('/actas/login', 'showLoginForm')->name('login');
    Route::post('/actas/login', 'login');
    Route::post('/logout', 'logout')->name('logout');
});

// --- RUTAS PROTEGIDAS (Middleware Auth) ---
Route::middleware(['auth'])->group(function () {

    Route::get('/', function () {
        return Auth::user()->role === 'admin'
            ? redirect()->route('admin.dashboard')
            : redirect()->route('usuario.dashboard');
    });

    Route::get('/establecimientos/buscar', [EstablecimientoController::class, 'buscar'])->name('establecimientos.buscar');

    // --- GRUPO USUARIO (Monitor / Técnico) ---
    Route::prefix('usuario')->name('usuario.')->group(function () {

        Route::get('/dashboard', [UsuarioController::class, 'index'])->name('dashboard');
        Route::get('/mi-perfil', [UsuarioController::class, 'perfil'])->name('perfil');
        Route::put('/mi-perfil', [UsuarioController::class, 'perfilUpdate'])->name('perfil.update');

        // --- SECCIÓN: ASISTENCIA TÉCNICA ---
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

        // --- SECCIÓN: MONITOREO MODULAR ---
        Route::prefix('monitoreo')->name('monitoreo.')->group(function () {

            Route::get('/profesional/buscar/{doc}', [GestionAdministrativaController::class, 'buscarProfesional'])->name('profesional.buscar');
            Route::get('/equipo/buscar/{doc}', [MonitoreoController::class, 'buscarMiembroEquipo'])->name('equipo.buscar');
            Route::get('/equipo/buscar-filtro', [MonitoreoController::class, 'buscarFiltro'])->name('equipo.filtro');

            Route::get('/', [MonitoreoController::class, 'index'])->name('index');
            Route::get('/crear-acta', [MonitoreoController::class, 'create'])->name('create');
            Route::post('/', [MonitoreoController::class, 'store'])->name('store');
            Route::get('/{id}/modulos', [MonitoreoController::class, 'gestionarModulos'])->name('modulos');
            Route::post('/{id}/toggle-modulos', [MonitoreoController::class, 'toggleModulos'])->name('toggle');

            // --- GESTIÓN DE FIRMAS POR MÓDULO (Controlador especializado) ---
            // 1. Ruta para procesar la subida del archivo
            Route::post('/{id}/subir-pdf-firmado', [FirmasMonitoreoController::class, 'subir'])->name('subir-pdf-firmado');
            // 2. Ruta para visualizar el PDF cargado en el navegador
            Route::get('/{id}/ver-pdf-firmado/{modulo}', [FirmasMonitoreoController::class, 'ver'])->name('ver-pdf-firmado');

            Route::get('/{id}/editar-acta', [EditMonitoreoController::class, 'edit'])->name('edit');
            Route::put('/{id}/actualizar', [EditMonitoreoController::class, 'update'])->name('update');

           // Módulo 01: Gestión Administrativa
// Quitamos 'usuario.monitoreo' del name si ya estás dentro de un grupo con ese nombre
        Route::prefix('modulo/gestion-administrativa')
            ->name('gestion-administrativa.') 
            ->group(function () {
        Route::get('/{id}', [GestionAdministrativaController::class, 'index'])->name('index');
        Route::post('/{id}', [GestionAdministrativaController::class, 'store'])->name('store');
        Route::get('/{id}/pdf', [GestionAdministrativaPdfController::class, 'generar'])->name('pdf');
    });
            // Módulo 02: Citas
            Route::prefix('modulo/citas')->name('citas.')->group(function () {
                // Nueva ruta de búsqueda (Colócala ANTES de las rutas con {id} para evitar conflictos)
                Route::get('/buscar-profesional', [CitaController::class, 'buscarProfesional'])->name('buscar.profesional');

                Route::get('/{id}', [CitaController::class, 'index'])->name('index');
                Route::post('/{id}', [CitaController::class, 'create'])->name('create');
                Route::get('/{id}/pdf', [CitaController::class, 'generar'])->name('pdf');
            });

<<<<<<< HEAD
            Route::get('/profesional/buscar/{doc}', [TriajeController::class, 'buscarProfesional'])->name('profesional.buscar');

            // --- API DE PROFESIONALES (Buscar) ---
            // Route::controller(App\Http\Controllers\ProfesionalController::class)
            //     ->prefix('modulo/profesionales') // Prefijo URL
            //     ->name('profesionales.')         // Prefijo Nombre: usuario.monitoreo.profesionales.
            //     ->group(function () {
            //         Route::get('/buscar/{doc}', 'buscar')->name('buscar');
            //     });

            // Módulo 03: Triaje
            Route::prefix('modulo/triaje')->name('triaje.')->group(function () {
                Route::get('/{id}', [TriajeController::class, 'index'])->name('index');
                Route::post('/{id}', [TriajeController::class, 'store'])->name('store');
                Route::get('/{id}/pdf', [TriajePdfController::class, 'generar'])->name('pdf');

                Route::get('/buscar-profesional/{doc}', [TriajeController::class, 'buscarProfesional'])->name('buscarProfesional');
                Route::delete('/foto/{id}', [TriajeController::class, 'eliminarFoto'])->name('eliminarFoto');  
            });
            

            // Módulo 05: Odontologia
            Route::prefix('modulo/consulta-odontologia')->name('consulta-odontologia.')->group(function () {
                Route::get('/{id}', [OdontologiaController::class, 'index'])->name('index');
                Route::post('/{id}', [OdontologiaController::class, 'store'])->name('store');
                Route::get('/{id}/pdf', [OdontologiaPdfController::class, 'generar'])->name('pdf');

                Route::get('/buscar-profesional/{doc}', [OdontologiaController::class, 'buscarProfesional'])->name('buscarProfesional');
                Route::delete('/foto/{id}', [OdontologiaController::class, 'eliminarFoto'])->name('eliminarFoto');  
            });

            // Módulo 07: Psicologia
            Route::prefix('modulo/consulta-psicologia')->name('consulta-psicologia.')->group(function () {
                Route::get('/{id}', [PsicologiaController::class, 'index'])->name('index');
                Route::post('/{id}', [PsicologiaController::class, 'store'])->name('store');
                Route::get('/{id}/pdf', [PsicologiaPdfController::class, 'generar'])->name('pdf');

                Route::get('/buscar-profesional/{doc}', [PsicologiaController::class, 'buscarProfesional'])->name('buscarProfesional');
                Route::delete('/foto/{id}', [PsicologiaController::class, 'eliminarFoto'])->name('eliminarFoto');  
            });

=======
            // Módulo 10: Atencion Prenatal
            Route::prefix('modulo/atencion_prenatal')->name('atencion-prenatal.')->group(function () {
                // Nueva ruta de búsqueda (Colócala ANTES de las rutas con {id} para evitar conflictos)
                Route::get('/buscar-profesional', [CitaController::class, 'buscarProfesional'])->name('buscar.profesional');

                Route::get('/{id}', [PrenatalController::class, 'index'])->name('index');
                Route::post('/{id}', [PrenatalController::class, 'create'])->name('create');
                Route::get('/{id}/pdf', [PrenatalController::class, 'generar'])->name('pdf');
            });

            // Módulo 12: Atencion Prenatal
            Route::prefix('modulo/parto')->name('parto.')->group(function () {
                // Nueva ruta de búsqueda (Colócala ANTES de las rutas con {id} para evitar conflictos)
                Route::get('/buscar-profesional', [CitaController::class, 'buscarProfesional'])->name('buscar.profesional');

                Route::get('/{id}', [PartoController::class, 'index'])->name('index');
                Route::post('/{id}', [PartoController::class, 'create'])->name('create');
                Route::get('/{id}/pdf', [PartoController::class, 'generar'])->name('pdf');
            });

            // Módulo 17: Laboratorio
Route::prefix('modulo/laboratorio')
    ->name('laboratorio.') 
    ->group(function () {
        Route::get('/{id}', [LaboratorioController::class, 'index'])->name('index');
        Route::post('/{id}', [LaboratorioController::class, 'store'])->name('store');
        Route::get('/{id}/pdf', [LaboratorioPdfController::class, 'generar'])->name('pdf');
    });
// Módulo 13: Puerperio
    Route::prefix('modulo/puerperio')->name('puerperio.')->group(function () {
        Route::get('/{id}', [PuerperioController::class, 'index'])->name('index');
        Route::post('/{id}', [PuerperioController::class, 'store'])->name('store');
        Route::get('/{id}/pdf', [PuerperioPdfController::class, 'generar'])->name('pdf');
    });

>>>>>>> main
            // Motor de PDF consolidado y visor final
            Route::get('/{id}/pdf-consolidado', [MonitoreoController::class, 'generarPDF'])->name('pdf');
            Route::post('/{id}/subir-pdf', [MonitoreoController::class, 'subirPDF'])->name('subirPDF');
            Route::get('/{monitoreo}', [MonitoreoController::class, 'show'])->name('show');
        });
    });

    // Esta es la ruta que dispara el botón "Acta Consolidada"
Route::get('monitoreo/{id}/consolidado/pdf', [ConsolidadoPdfController::class, 'generar'])
    ->name('usuario.monitoreo.pdf');

    // --- GRUPO ADMINISTRADOR ---
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
});
