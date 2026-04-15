<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// --- IMPORTACIÓN DE CONTROLADORES ---
use App\Http\Controllers\LoginController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuditoriaController;
use App\Http\Controllers\AuditoriaEquiposController;
use App\Http\Controllers\DocumentoAdministrativoController;
use App\Http\Controllers\ActaController;
use App\Http\Controllers\AsistentaSocialEspecializadoController;
use App\Http\Controllers\CitaController;
use App\Http\Controllers\CitaESPController;
use App\Http\Controllers\CitaESPpdfController;
use App\Http\Controllers\MonitoreoController;
use App\Http\Controllers\EditMonitoreoController;
use App\Http\Controllers\GestionAdministrativaController;
use App\Http\Controllers\GestionAdministrativaESPController;
use App\Http\Controllers\GestionAdministrativaESPpdfController;
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
use App\Http\Controllers\ConsolidadoESPPdfController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\TriajeController;
use App\Http\Controllers\TriajePdfController;
use App\Http\Controllers\TriajeESPController;
use App\Http\Controllers\TriajeESPpdfController;
use App\Http\Controllers\OdontologiaController;
use App\Http\Controllers\OdontologiaPdfController;
use App\Http\Controllers\PsicologiaController;
use App\Http\Controllers\PsicologiaPdfController;
use App\Http\Controllers\ConsultaMedicinaController;
use App\Http\Controllers\ConsultaMedicinaPdfController;
use App\Http\Controllers\ConsultaNutricionController;
use App\Http\Controllers\ConsultaNutricionPdfController;
use App\Http\Controllers\InmunizacionesController;
use App\Http\Controllers\InmunizacionesPdfController;
use App\Http\Controllers\CredController;
use App\Http\Controllers\CredPdfController;
use App\Http\Controllers\FarmaciaController;
use App\Http\Controllers\FarmaciaPdfController;
use App\Http\Controllers\PlanificacionController;
use App\Http\Controllers\PlanificacionPdfController;
use App\Http\Controllers\ReferenciasController;
use App\Http\Controllers\ReferenciasPdfController;
use App\Http\Controllers\FuaElectronicoController;
use App\Http\Controllers\FuaElectronicoPdfController;
use App\Http\Controllers\MedicinaEspecializadoController;
use App\Http\Controllers\UrgenciasController;
use App\Http\Controllers\UrgenciasPdfController;
use App\Http\Controllers\FarmaciaESPController;
use App\Http\Controllers\FarmaciaESPpdfController;
use App\Http\Controllers\TerapiaESPController;
use App\Http\Controllers\TerapiaESPpdfController;
use App\Http\Controllers\PsiquiatriaESPController;
use App\Http\Controllers\PsiquiatriaESPpdfController;
use App\Http\Controllers\PsicologiaESPController;
use App\Http\Controllers\PsicologiaESPpdfController;
use App\Http\Controllers\MedicinaFamiliarESPController;
use App\Http\Controllers\MedicinaFamiliarESPpdfController;
use App\Http\Controllers\EnfermeriaESPController;
use App\Http\Controllers\EnfermeriaESPpdfController;
use App\Http\Controllers\ASocialESPController;
use App\Http\Controllers\TomaDeMuestraController;
use App\Http\Controllers\TomaDeMuestrapdfController;
use App\Http\Controllers\ReporteEquiposController;
use App\Http\Controllers\ReporteActasController;
use App\Http\Controllers\ReporteMonitoreoController;
use App\Http\Controllers\ReporteImplementacionController;
use App\Http\Controllers\ImplementacionController;
use App\Http\Controllers\Infraestructura2DController;
use App\Http\Controllers\Infraestructura2DPdfController;
use App\Http\Controllers\MesaAyudaController;
use App\Http\Controllers\CroquisColaboracionController;
use App\Http\Controllers\SignatureBankController;


// --- CONFIGURACIÓN DE VERBOS ---
Route::resourceVerbs([
    'create' => 'crear-acta',
    'edit' => 'editar-acta',
]);

// --- AUTENTICACIÓN PÚBLICA ---
Route::controller(LoginController::class)->group(function () {
    Route::get('/actas/login', 'showLoginForm')->name('login');
    Route::post('/actas/login', 'login');
    Route::match(['get', 'post'], '/logout', 'logout')->name('logout');
});


// --- RUTAS PROTEGIDAS (Middleware Auth) ---
Route::middleware(['auth'])->group(function () {

    Route::get('/', function () {
        return match(Auth::user()->role) {
            'admin'    => redirect()->route('usuario.dashboard.general'),
            'operador' => redirect()->route('usuario.monitoreo.index'),
            default    => redirect()->route('usuario.mesa-ayuda.index'),
        };
    });

    Route::get('/establecimientos/buscar', [EstablecimientoController::class, 'buscar'])->name('establecimientos.buscar');
    Route::patch('/establecimientos/{id}/coordenadas', [EstablecimientoController::class, 'updateCoordenadas'])->name('establecimientos.coordenadas');

    // --- GRUPO USUARIO (Monitor / Técnico) ---
    Route::prefix('usuario')->name('usuario.')->group(function () {

        Route::prefix('dashboard')->name('dashboard.')->middleware('is_admin')->group(function () {
            Route::get('/', [UsuarioController::class, 'index'])->name('general');
            Route::get('/equipos', [UsuarioController::class, 'dashboardEquipos'])->name('equipos');
            Route::get('/programacion-sectores', [UsuarioController::class, 'mapaProgramacion'])->name('programacion.sectores');
            Route::put('/programacion-sectores/{id}/sector', [UsuarioController::class, 'actualizarSector'])->name('programacion.sectores.update');
        });

        // AJAX para Dashboard - Equipos de Cómputo (Protegidos)
        Route::middleware('is_admin')->group(function () {
            Route::get('/dashboard/ajax/equipos-stats', [UsuarioController::class, 'getEquiposStats'])->name('dashboard.ajax.equipos.stats');
            Route::get('/dashboard/ajax/equipos-filter-options', [UsuarioController::class, 'getFilterOptions'])->name('dashboard.ajax.equipos.filter-options');
            Route::get('/dashboard/ajax/equipos-provincias', [ReporteEquiposController::class, 'getProvincias'])->name('dashboard.ajax.equipos.provincias');
            Route::get('/dashboard/ajax/equipos-establecimientos', [ReporteEquiposController::class, 'getEstablecimientos'])->name('dashboard.ajax.equipos.establecimientos');
            Route::get('/dashboard/ajax/equipos-modulos', [ReporteEquiposController::class, 'getModulos'])->name('dashboard.ajax.equipos.modulos');
            Route::get('/dashboard/ajax/equipos-descripciones', [ReporteEquiposController::class, 'getDescripciones'])->name('dashboard.ajax.equipos.descripciones');
        });

        Route::get('/mi-perfil', [UsuarioController::class, 'perfil'])->name('perfil');
        Route::put('/mi-perfil', [UsuarioController::class, 'perfilUpdate'])->name('perfil.update');

        // --- SECCIÓN: ASISTENCIA TÉCNICA ---
        Route::prefix('listado-actas')->name('actas.')->middleware('is_admin')->group(function () {
            Route::get('/', [ActaController::class, 'index'])->name('index');
            Route::get('/crear-acta', [ActaController::class, 'create'])->name('create');
            Route::post('/', [ActaController::class, 'store'])->name('store');
            Route::get('/{acta}/editar-acta', [ActaController::class, 'edit'])->name('edit');
            Route::put('/{acta}', [ActaController::class, 'update'])->name('update');
            Route::get('/{acta}', [ActaController::class, 'show'])->name('show');
            Route::get('/{id}/pdf', [ActaController::class, 'generarPDF'])->name('generarPDF');
            Route::post('/{id}/subir-pdf', [ActaController::class, 'subirPDF'])->name('subirPDF');
            Route::post('/{id}/enviar-correo', [ActaController::class, 'enviarCorreo'])->name('enviarCorreo');
            Route::get('/{id}/emails', [ActaController::class, 'getParticipantesEmails'])->name('get-emails');
            Route::post('/{id}/anular', [ActaController::class, 'anular'])->name('anular');
            Route::post('/sync-renipress', [ActaController::class, 'syncRenipress'])->name('sync-renipress');

            // AJAX endpoints para filtros dinámicos
            Route::get('/ajax/distritos', [ActaController::class, 'ajaxGetDistritos'])->name('ajax.distritos');
            Route::get('/ajax/establecimientos', [ActaController::class, 'ajaxGetEstablecimientos'])->name('ajax.establecimientos');
        });

        // --- SECCIÓN: DOCUMENTOS ADMINISTRATIVOS ---
        Route::prefix('documentos-administrativos')->name('documentos.')->middleware('is_admin')->group(function () {
            Route::get('/', [DocumentoAdministrativoController::class, 'index'])->name('index');
            Route::get('/crear', [DocumentoAdministrativoController::class, 'create'])->name('create');
            Route::post('/guardar', [DocumentoAdministrativoController::class, 'store'])->name('store');
            Route::get('/{id}/editar', [DocumentoAdministrativoController::class, 'edit'])->name('edit');
            Route::put('/{id}', [DocumentoAdministrativoController::class, 'update'])->name('update');
            Route::get('/{id}/pdf', [DocumentoAdministrativoController::class, 'generarPDF'])->name('pdf');
            Route::post('/{id}/subir-firmado', [DocumentoAdministrativoController::class, 'subirFirmado'])->name('subir-firmado');

            // AJAX endpoints para filtros dinámicos
            Route::get('/ajax/distritos', [DocumentoAdministrativoController::class, 'ajaxGetDistritos'])->name('ajax.distritos');
            Route::get('/ajax/establecimientos', [DocumentoAdministrativoController::class, 'ajaxGetEstablecimientos'])->name('ajax.establecimientos');
        });

        // --- SECCIÓN: REPORTES ---
        Route::prefix('reportes')->name('reportes.')->middleware('is_admin')->group(function () {
            // Reportes de Equipos de Cómputo
            Route::get('/equipos', [ReporteEquiposController::class, 'index'])->name('equipos');
            Route::post('/equipos/excel', [ReporteEquiposController::class, 'exportarExcel'])->name('equipos.excel');

            // AJAX endpoints para filtros dinámicos
            Route::get('/equipos/ajax/establecimientos', [ReporteEquiposController::class, 'getEstablecimientos'])->name('equipos.ajax.establecimientos');
            Route::get('/equipos/ajax/provincias', [ReporteEquiposController::class, 'getProvincias'])->name('equipos.ajax.provincias');
            Route::get('/equipos/ajax/distritos', [ReporteEquiposController::class, 'ajaxGetDistritos'])->name('equipos.ajax.distritos');
            Route::get('/equipos/ajax/modulos', [ReporteEquiposController::class, 'getModulos'])->name('equipos.ajax.modulos');
            Route::get('/equipos/ajax/descripciones', [ReporteEquiposController::class, 'getDescripciones'])->name('equipos.ajax.descripciones');

            // Reporte de Actas de Asistencia Técnica
            Route::get('/actas-asistencia', [ReporteActasController::class, 'index'])->name('actas.asistencia');
            Route::post('/actas-asistencia/excel', [ReporteActasController::class, 'exportarExcel'])->name('actas.asistencia.excel');
            Route::get('/actas-asistencia/ajax/distritos', [ReporteActasController::class, 'ajaxGetDistritos'])->name('actas.asistencia.ajax.distritos');
            Route::get('/actas-asistencia/ajax/establecimientos', [ReporteActasController::class, 'ajaxGetEstablecimientos'])->name('actas.asistencia.ajax.establecimientos');

            // Reporte de Actas de Monitoreo
            Route::get('/actas-monitoreo', [ReporteMonitoreoController::class, 'index'])->name('actas.monitoreo');
            Route::post('/actas-monitoreo/excel', [ReporteMonitoreoController::class, 'exportarExcel'])->name('actas.monitoreo.excel');
            Route::get('/actas-monitoreo/ajax/distritos', [ReporteMonitoreoController::class, 'ajaxGetDistritos'])->name('actas.monitoreo.ajax.distritos');
            Route::get('/actas-monitoreo/ajax/establecimientos', [ReporteMonitoreoController::class, 'ajaxGetEstablecimientos'])->name('actas.monitoreo.ajax.establecimientos');

            // Reporte de Actas de Implementación
            Route::get('/actas-implementacion', [ReporteImplementacionController::class, 'index'])->name('actas.implementacion');
            Route::post('/actas-implementacion/excel', [ReporteImplementacionController::class, 'exportarExcel'])->name('actas.implementacion.excel');
            Route::get('/actas-implementacion/ajax/distritos', [ReporteImplementacionController::class, 'getDistritos'])->name('actas.implementacion.ajax.distritos');
        });

        // --- SECCIÓN: AUDITORÍA DE CONSISTENCIA (Protegida) ---
        Route::middleware('is_admin')->group(function () {
            Route::get('/auditoria-consistencia', [AuditoriaController::class, 'index'])->name('auditoria.index');
            Route::get('/auditoria-consistencia/ajax/distritos', [AuditoriaController::class, 'ajaxGetDistritos'])->name('auditoria.ajax.distritos');
            Route::get('/auditoria-consistencia/ajax/establecimientos', [AuditoriaController::class, 'ajaxGetEstablecimientos'])->name('auditoria.ajax.establecimientos');

            Route::get('/auditoria-equipos', [AuditoriaEquiposController::class, 'index'])->name('auditoria.equipos');
            Route::get('/auditoria-equipos/ajax/distritos', [AuditoriaEquiposController::class, 'ajaxGetDistritos'])->name('auditoria.equipos.ajax.distritos');
            Route::get('/auditoria-equipos/ajax/establecimientos', [AuditoriaEquiposController::class, 'ajaxGetEstablecimientos'])->name('auditoria.equipos.ajax.establecimientos');
        });

        // --- SECCIÓN: COLABORACIÓN EN TIEMPO REAL (Croquis) ---
        Route::post('/croquis/{actaId}/sync', [CroquisColaboracionController::class, 'sync'])->name('croquis.sync');
        Route::post('/croquis/{actaId}/leave', [CroquisColaboracionController::class, 'leave'])->name('croquis.leave');

        // --- SECCIÓN: MESA DE AYUDA ---
        Route::prefix('incidencias')->name('mesa-ayuda.')->group(function () {
            Route::get('/', [MesaAyudaController::class, 'index'])->name('index');
            Route::get('/{id}/responder', [MesaAyudaController::class, 'responder'])->name('responder');
            Route::post('/{id}/responder', [MesaAyudaController::class, 'guardarRespuesta'])->name('guardar-respuesta');
            
            // Rutas de reporte (ahora protegidas)
            Route::get('/formulario', [MesaAyudaController::class, 'formulario'])->name('form');
            Route::post('/store', [MesaAyudaController::class, 'store'])->name('store');
            Route::get('/{id}/pdf', [MesaAyudaController::class, 'verPdf'])->name('pdf');
            Route::get('/buscar-establecimiento', [MesaAyudaController::class, 'buscarEstablecimiento'])->name('buscar');
            Route::get('/buscar-dni', [MesaAyudaController::class, 'buscarDni'])->name('buscar-dni');
        });

        // --- SECCIÓN: ACTAS DE IMPLEMENTACIÓN ---
        Route::prefix('implementacion')->name('implementacion.')->middleware('is_admin')->group(function () {
            Route::get('/', [ImplementacionController::class, 'index'])->name('index');
            Route::get('/create', [ImplementacionController::class, 'create'])->name('create');
            Route::post('/', [ImplementacionController::class, 'store'])->name('store');
            Route::get('/{modulo}/{id}/edit', [ImplementacionController::class, 'edit'])->name('edit');
            Route::put('/{modulo}/{id}', [ImplementacionController::class, 'update'])->name('update');
            Route::delete('/{modulo}/{id}', [ImplementacionController::class, 'destroy'])->name('destroy');
            Route::get('/{modulo}/{id}/pdf', [ImplementacionController::class, 'pdf'])->name('pdf');
            Route::post('/{modulo}/{id}/subir-pdf', [ImplementacionController::class, 'subirPdf'])->name('subirPdf');
            Route::post('/{modulo}/{id}/enviar-correo', [ImplementacionController::class, 'enviarCorreo'])->name('enviarCorreo');
            Route::post('/{modulo}/{id}/cambiar-modulo', [ImplementacionController::class, 'cambiar_modulo'])->name('cambiar_modulo');
            Route::post('/{modulo}/{id}/anular', [ImplementacionController::class, 'anular'])->name('anular');

            
            // Endpoint AJAX para buscar establecimientos
            Route::get('/ajax/establecimiento', [ImplementacionController::class, 'buscarEstablecimiento'])->name('ajax.establecimiento');
            Route::get('/ajax/upss-ups', [ImplementacionController::class, 'buscarUpss'])->name('ajax.upss');
            Route::get('/ajax/renipress-sync', [ImplementacionController::class, 'syncRenipress'])->name('ajax.renipress-sync');
            Route::get('/ajax/check-duplicado', [ImplementacionController::class, 'checkDuplicado'])->name('ajax.check-duplicado');
        });

        // --- SECCIÓN: MONITOREO MODULAR ---
        Route::prefix('monitoreo')->name('monitoreo.')->middleware('is_operador_or_admin')->group(function () {
            Route::get('/ajax/distritos', [MonitoreoController::class, 'ajaxGetDistritos'])->name('ajax.distritos');
            Route::get('/ajax/establecimientos', [MonitoreoController::class, 'ajaxGetEstablecimientos'])->name('ajax.establecimientos');

            // Utilitarios de búsqueda
            Route::get('/profesional/buscar/{doc}', [GestionAdministrativaController::class, 'buscarProfesional'])->name('profesional.buscar');
            Route::get('/equipo/buscar/{doc}', [MonitoreoController::class, 'buscarMiembroEquipo'])->name('equipo.buscar');
            Route::get('/equipo/buscar-filtro', [MonitoreoController::class, 'buscarFiltro'])->name('equipo.filtro');

            // Gestión de Acta de Monitoreo
            Route::get('/', [MonitoreoController::class, 'index'])->name('index');
            Route::get('/crear-acta', [MonitoreoController::class, 'create'])->name('create');
            Route::post('/', [MonitoreoController::class, 'store'])->name('store');
            Route::get('/{id}/modulos', [MonitoreoController::class, 'gestionarModulos'])->name('modulos');
            Route::post('/{id}/toggle-modulos', [MonitoreoController::class, 'toggleModulos'])->name('toggle');

            // GESTIÓN DE FIRMAS POR MÓDULO
            Route::post('/{id}/subir-pdf-firmado', [FirmasMonitoreoController::class, 'subir'])->name('subir-pdf-firmado');
            Route::get('/{id}/ver-pdf-firmado/{modulo}', [FirmasMonitoreoController::class, 'ver'])->name('ver-pdf-firmado');

            Route::get('/{id}/editar-acta', [EditMonitoreoController::class, 'edit'])->name('edit');
            Route::put('/{id}/actualizar', [EditMonitoreoController::class, 'update'])->name('update');

            // ==================================================================================
            // RUTA NIVEL 2: PANEL DE SALUD MENTAL (4.1 - 4.7)
            // ==================================================================================
            Route::get('/{id}/salud-mental-panel', [MonitoreoController::class, 'gestionarSaludMental'])
                ->name('salud_mental_group.index');

            // ==================================================================================
            // SUB-MÓDULOS DE SALUD MENTAL (4.x)
            // ==================================================================================
            Route::prefix('modulo/salud-mental')->group(function () {

                // 4.1 Medicina General
                Route::prefix('medicina-general')->name('sm_medicina_general.')->group(function () {
                    // TODO: Crear SmMedicinaGeneralController (Usando ConsultaMedicina temporalmente)
                    Route::get('/{id}', [MedicinaEspecializadoController::class, 'index'])->name('index');
                    Route::post('/{id}', [MedicinaEspecializadoController::class, 'store'])->name('store');
                    Route::get('/{id}/pdf', [MedicinaEspecializadoController::class, 'generar'])->name('pdf');
                });

                // 4.2 Psiquiatría
                Route::prefix('psiquiatria')->name('sm_psiquiatria.')->group(function () {
                    // TODO: Crear SmPsiquiatriaController (Usando ConsultaMedicina temporalmente)
                    Route::get('/{id}', [PsiquiatriaESPController::class, 'index'])->name('index');
                    Route::post('/{id}', [PsiquiatriaESPController::class, 'store'])->name('store');
                    Route::get('/{id}/pdf', [PsiquiatriaESPpdfController::class, 'generar'])->name('pdf');
                });

                // 4.3 Medicina Familiar y Comunitaria
                Route::prefix('medicina-familiar')->name('sm_med_familiar.')->group(function () {
                    // TODO: Crear SmMedFamiliarController (Usando ConsultaMedicina temporalmente)
                    Route::get('/{id}', [MedicinaFamiliarESPController::class, 'index'])->name('index');
                    Route::post('/{id}', [MedicinaFamiliarESPController::class, 'store'])->name('store');
                    Route::get('/{id}/pdf', [MedicinaFamiliarESPpdfController::class, 'generar'])->name('pdf');
                });

                // 4.4 Psicología
                Route::prefix('psicologia')->name('sm_psicologia.')->group(function () {
                    // Usamos el controlador de Psicología existente
                    Route::get('/{id}', [PsicologiaESPController::class, 'index'])->name('index');
                    Route::post('/{id}', [PsicologiaESPController::class, 'store'])->name('store');
                    Route::get('/{id}/pdf', [PsicologiaESPPdfController::class, 'generar'])->name('pdf');
                });

                // 4.5 Enfermería
                Route::prefix('enfermeria')->name('sm_enfermeria.')->group(function () {
                    // TODO: Crear SmEnfermeriaController (Usando Triaje temporalmente)
                    Route::get('/{id}', [EnfermeriaESPController::class, 'index'])->name('index');
                    Route::post('/{id}', [EnfermeriaESPController::class, 'store'])->name('store');
                    Route::get('/{id}/pdf', [EnfermeriaESPpdfController::class, 'generar'])->name('pdf');
                });

                // 4.6 Servicio Social
                Route::prefix('servicio-social')->name('sm_servicio_social.')->group(function () {
                    // TODO: Crear SmServicioSocialController (Usando GestionAdmin temporalmente)
                    Route::get('/{id}', [ASocialESPController::class, 'index'])->name('index');
                    Route::post('/{id}', [ASocialESPController::class, 'store'])->name('store');
                    Route::get('/{id}/pdf', [ASocialESPController::class, 'generar'])->name('pdf');
                });

                // 4.7 Terapias (Lenguaje / Ocupacional)
                Route::prefix('terapias')->name('sm_terapias.')->group(function () {
                    // Usamos TerapiaESPController
                    Route::get('/{id}', [TerapiaESPController::class, 'index'])->name('index');
                    Route::post('/{id}', [TerapiaESPController::class, 'store'])->name('store');
                    Route::get('/{id}/pdf', [TerapiaESPpdfController::class, 'generar'])->name('pdf');
                });
            });

            // ==================================================================================
            // MÓDULOS ESPECIALIZADOS NIVEL 1 (CSMC)
            // ==================================================================================

            // 1. Gestión Administrativa (Reutiliza el estándar o crear GestionAdminESP)
            Route::prefix('modulo/gestion-administrativa-especializada')->name('gestion_admin_esp.')->group(function () {
                Route::get('/{id}', [GestionAdministrativaESPController::class, 'index'])->name('index');
                Route::post('/{id}', [GestionAdministrativaESPController::class, 'store'])->name('store');
                Route::get('/{id}/pdf', [GestionAdministrativaESPpdfController::class, 'generar'])->name('pdf');
            });

            // 2. Citas (CSMC)
            Route::prefix('modulo/citas-especializada')->name('citas_esp.')->group(function () {
                Route::get('/{id}', [CitaESPController::class, 'index'])->name('index');
                Route::post('/{id}', [CitaESPController::class, 'store'])->name('store');
                Route::get('/{id}/pdf', [CitaESPpdfController::class, 'generar'])->name('pdf');
            });

            // 3. Triaje (CSMC)
            Route::prefix('modulo/triaje-especializada')->name('triaje_esp.')->group(function () {
                Route::get('/{id}', [TriajeESPController::class, 'index'])->name('index');
                Route::post('/{id}', [TriajeESPController::class, 'store'])->name('store');
                Route::get('/{id}/pdf', [TriajeESPpdfController::class, 'generar'])->name('pdf');
            });

            // 5. Toma de Muestra (CSMC)
            Route::prefix('modulo/toma-muestra')->name('toma_muestra.')->group(function () {
                Route::get('/{id}', [TomaDeMuestraController::class, 'index'])->name('index');
                Route::post('/{id}', [TomaDeMuestraController::class, 'store'])->name('store');
                Route::get('/{id}/pdf', [TomaDeMuestraPdfController::class, 'generar'])->name('pdf');
            });

            // 6. Farmacia (CSMC)
            Route::prefix('modulo/farmacia-especializada')->name('farmacia_esp.')->group(function () {
                Route::get('/{id}', [FarmaciaESPController::class, 'index'])->name('index');
                Route::post('/{id}', [FarmaciaESPController::class, 'store'])->name('store');
                Route::get('/{id}/pdf', [FarmaciaESPpdfController::class, 'generar'])->name('pdf');
            });

            // ==================================================================================
            // MÓDULOS ESTÁNDAR (IPRESS)
            // ==================================================================================

            // Módulo 01: Gestión Administrativa
            Route::prefix('modulo/gestion-administrativa')->name('gestion-administrativa.')->group(function () {
                Route::get('/{id}', [GestionAdministrativaController::class, 'index'])->name('index');
                Route::post('/{id}', [GestionAdministrativaController::class, 'store'])->name('store');
                Route::get('/{id}/pdf', [GestionAdministrativaPdfController::class, 'generar'])->name('pdf');
            });

            // Módulo 02: Citas
            Route::prefix('modulo/citas')->name('citas.')->group(function () {
                Route::get('/buscar-profesional', [CitaController::class, 'buscarProfesional'])->name('buscar.profesional');
                Route::get('/{id}', [CitaController::class, 'index'])->name('index');
                Route::post('/{id}', [CitaController::class, 'create'])->name('create');
                Route::get('/{id}/pdf', [CitaController::class, 'generar'])->name('pdf');
            });

            // Módulo 03: Triaje
            Route::prefix('modulo/triaje')->name('triaje.')->group(function () {
                Route::get('/{id}', [TriajeController::class, 'index'])->name('index');
                Route::post('/{id}', [TriajeController::class, 'store'])->name('store');
                Route::get('/{id}/pdf', [TriajePdfController::class, 'generar'])->name('pdf');
                Route::get('/buscar-profesional/{doc}', [TriajeController::class, 'buscarProfesional'])->name('buscarProfesional');
                Route::delete('/foto/{id}', [TriajeController::class, 'eliminarFoto'])->name('eliminarFoto');
            });

            // Módulo 04: Consulta Externa - Medicina
            Route::prefix('modulo/consulta-medicina')->name('consulta-medicina.')->group(function () {
                Route::get('/{id}', [ConsultaMedicinaController::class, 'index'])->name('index');
                Route::post('/{id}', [ConsultaMedicinaController::class, 'store'])->name('store');
                Route::get('/{id}/pdf', [ConsultaMedicinaPdfController::class, 'generar'])->name('pdf');
            });


            // Módulo 05: Odontologia
            Route::prefix('modulo/consulta-odontologia')->name('consulta-odontologia.')->group(function () {
                Route::get('/{id}', [OdontologiaController::class, 'index'])->name('index');
                Route::post('/{id}', [OdontologiaController::class, 'store'])->name('store');
                Route::get('/{id}/pdf', [OdontologiaPdfController::class, 'generar'])->name('pdf');
                Route::get('/buscar-profesional/{doc}', [OdontologiaController::class, 'buscarProfesional'])->name('buscarProfesional');
                Route::delete('/foto/{id}', [OdontologiaController::class, 'eliminarFoto'])->name('eliminarFoto');
            });

            // Módulo 06: Consulta Externa - Nutrición
            Route::prefix('modulo/consulta-nutricion')->name('consulta-nutricion.')->group(function () {
                Route::get('/{id}', [ConsultaNutricionController::class, 'index'])->name('index');
                Route::post('/{id}', [ConsultaNutricionController::class, 'store'])->name('store');
                Route::get('/{id}/pdf', [ConsultaNutricionPdfController::class, 'generar'])->name('pdf');
            });

            // Módulo 07: Psicologia
            Route::prefix('modulo/consulta-psicologia')->name('consulta-psicologia.')->group(function () {
                Route::get('/{id}', [PsicologiaController::class, 'index'])->name('index');
                Route::post('/{id}', [PsicologiaController::class, 'store'])->name('store');
                Route::get('/{id}/pdf', [PsicologiaPdfController::class, 'generar'])->name('pdf');
                Route::get('/buscar-profesional/{doc}', [PsicologiaController::class, 'buscarProfesional'])->name('buscarProfesional');
                Route::delete('/foto/{id}', [PsicologiaController::class, 'eliminarFoto'])->name('eliminarFoto');
            });

            // Módulo 08: CRED
            Route::prefix('modulo/cred')->name('cred.')->group(function () {
                Route::get('/{id}', [CredController::class, 'index'])->name('index');
                Route::post('/{id}', [CredController::class, 'store'])->name('store');
                Route::get('/{id}/pdf', [CredPdfController::class, 'generar'])->name('pdf');
            });

            // Módulo 09: Inmunizaciones
            Route::prefix('modulo/inmunizaciones')->name('inmunizaciones.')->group(function () {
                Route::get('/{id}', [InmunizacionesController::class, 'index'])->name('index');
                Route::post('/{id}', [InmunizacionesController::class, 'store'])->name('store');
                Route::get('/{id}/pdf', [InmunizacionesPdfController::class, 'generar'])->name('pdf');
            });

            // Módulo 10: Atencion Prenatal
            Route::prefix('modulo/atencion_prenatal')->name('atencion-prenatal.')->group(function () {
                Route::get('/buscar-profesional', [CitaController::class, 'buscarProfesional'])->name('buscar.profesional');
                Route::get('/{id}', [PrenatalController::class, 'index'])->name('index');
                Route::post('/{id}', [PrenatalController::class, 'create'])->name('create');
                Route::get('/{id}/pdf', [PrenatalController::class, 'generar'])->name('pdf');
            });

            // Módulo 11: Planificación Familiar
            Route::prefix('modulo/planificacion-familiar')->name('planificacion-familiar.')->group(function () {
                Route::get('/{id}', [PlanificacionController::class, 'index'])->name('index');
                Route::post('/{id}', [PlanificacionController::class, 'store'])->name('store');
                Route::get('/{id}/pdf', [PlanificacionPdfController::class, 'generar'])->name('pdf');
            });

            // Módulo 12: Parto
            Route::prefix('modulo/parto')->name('parto.')->group(function () {
                Route::get('/buscar-profesional', [CitaController::class, 'buscarProfesional'])->name('buscar.profesional');
                Route::get('/{id}', [PartoController::class, 'index'])->name('index');
                Route::post('/{id}', [PartoController::class, 'create'])->name('create');
                Route::get('/{id}/pdf', [PartoController::class, 'generar'])->name('pdf');
            });

            // Módulo 13: Puerperio
            Route::prefix('modulo/puerperio')->name('puerperio.')->group(function () {
                Route::get('/{id}', [PuerperioController::class, 'index'])->name('index');
                Route::post('/{id}', [PuerperioController::class, 'store'])->name('store');
                Route::get('/{id}/pdf', [PuerperioPdfController::class, 'generar'])->name('pdf');
            });

            // Módulo 14: FUA Electrónico
            Route::prefix('modulo/fua-electronico')->name('fua-electronico.')->group(function () {
                Route::get('/{id}', [FuaElectronicoController::class, 'index'])->name('index');
                Route::post('/{id}', [FuaElectronicoController::class, 'store'])->name('store');
                Route::get('/{id}/pdf', [FuaElectronicoPdfController::class, 'generar'])->name('pdf');
            });

            // Módulo 15: Farmacia
            Route::prefix('modulo/farmacia')->name('farmacia.')->group(function () {
                Route::get('/{id}', [FarmaciaController::class, 'index'])->name('index');
                Route::post('/{id}', [FarmaciaController::class, 'store'])->name('store');
                Route::get('/{id}/pdf', [FarmaciaPdfController::class, 'generar'])->name('pdf');
            });

            // Módulo 16: Referencias
            Route::prefix('modulo/referencias')->name('referencias.')->group(function () {
                Route::get('/{id}', [ReferenciasController::class, 'index'])->name('index');
                Route::post('/{id}', [ReferenciasController::class, 'store'])->name('store');
                Route::get('/{id}/pdf', [ReferenciasPdfController::class, 'generar'])->name('pdf');
            });

            // Reportes Monitoreo
            Route::prefix('actas-monitoreo')->name('actas.monitoreo')->group(function () {
                Route::get('/', [ReporteMonitoreoController::class, 'index']);
                Route::post('/excel', [ReporteMonitoreoController::class, 'exportarExcel'])->name('.excel');
                Route::get('/ajax/distritos', [ReporteMonitoreoController::class, 'getDistritos'])->name('.ajax.distritos');
            });

            // Reportes Implementación
            Route::prefix('actas-implementacion')->name('actas.implementacion')->group(function () {
                Route::get('/', [ReporteImplementacionController::class, 'index']);
                Route::post('/excel', [ReporteImplementacionController::class, 'exportarExcel'])->name('.excel');
                Route::get('/ajax/distritos', [ReporteImplementacionController::class, 'getDistritos'])->name('.ajax.distritos');
            });

            // Módulo 17: Laboratorio
            Route::prefix('modulo/laboratorio')->name('laboratorio.')->group(function () {
                Route::get('/{id}', [LaboratorioController::class, 'index'])->name('index');
                Route::post('/{id}', [LaboratorioController::class, 'store'])->name('store');
                Route::get('/{id}/pdf', [LaboratorioPdfController::class, 'generar'])->name('pdf');
            });

            // Módulo 18: Urgencias
            Route::prefix('modulo/urgencias')->name('urgencias.')->group(function () {
                Route::get('/{id}', [UrgenciasController::class, 'index'])->name('index');
                Route::post('/{id}', [UrgenciasController::class, 'store'])->name('store');
                Route::get('/{id}/pdf', [UrgenciasPdfController::class, 'generar'])->name('pdf');
            });

            // Módulo 19: Infraestructura 2D
            Route::prefix('modulo/infraestructura-2d')->name('infraestructura-2d.')->group(function () {
                Route::get('/{id}', [Infraestructura2DController::class, 'index'])->name('index');
                Route::post('/{id}', [Infraestructura2DController::class, 'store'])->name('store');
                Route::get('/{id}/pdf', [Infraestructura2DPdfController::class, 'generar'])->name('pdf');
            });

            // MOTOR DE CONSOLIDADO
            Route::get('/{id}/pdf-consolidado', [MonitoreoController::class, 'generarPDF'])->name('generarPDF');
            Route::post('/{id}/subir-consolidado-final', [MonitoreoController::class, 'subirPDF'])->name('subirConsolidado');
            Route::get('/ver-detalle/{monitoreo}', [MonitoreoController::class, 'show'])->name('show');
            Route::post('/{id}/anular', [MonitoreoController::class, 'anular'])->name('anular');
        });
    });

    // Acta Consolidada (Global)
    Route::get('monitoreo/{id}/consolidado/pdf', [ConsolidadoPdfController::class, 'generar'])
        ->name('usuario.monitoreo.pdf');
    // Acta Consolidada Especializada    
    Route::get('monitoreo/{id}/consolidado_esp/pdf', [ConsolidadoESPPdfController::class, 'generar'])
        ->name('usuario.monitoreoESP.pdf');

    // --- GRUPO ADMINISTRADOR ---
    Route::prefix('admin')->name('admin.')->middleware('is_admin')->group(function () {
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
        Route::get('/buscar-dni', [AdminController::class, 'buscarDni'])->name('buscarDni');

        // --- BANCO DE FIRMAS ---
        Route::prefix('banco-firmas')->name('firmas.')->group(function () {
            Route::get('/', [SignatureBankController::class, 'index'])->name('index');
            Route::post('/{id}/upload', [SignatureBankController::class, 'upload'])->name('upload');
            Route::post('/harvest', [SignatureBankController::class, 'harvest'])->name('harvest');
            Route::delete('/{id}', [SignatureBankController::class, 'destroy'])->name('destroy');
        });
    });

    // Búsqueda de firmas (accesible para todos los usuarios autenticados)
    Route::get('/banco-firmas/search-ajax', [SignatureBankController::class, 'search'])->name('admin.firmas.ajax.search');
});
