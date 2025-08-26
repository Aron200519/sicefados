<?php

use Illuminate\Support\Facades\Route;
use Modules\FABRICASOFT\Http\Controllers\FABRICASOFTController;
use Modules\FABRICASOFT\Http\Controllers\PreregistroController;
use Modules\FABRICASOFT\Http\Controllers\AdminController;
use Modules\FABRICASOFT\Http\Controllers\AnalystController;
use Modules\FABRICASOFT\Http\Controllers\ScrumProjectController;
use Modules\FABRICASOFT\Http\Controllers\ClientAuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your module. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::prefix('fabricasoft')->group(function() {
    // Ruta principal del módulo
    Route::get('/', [FABRICASOFTController::class, 'index'])->name('fabricasoft.index');
    Route::get('/index', [FABRICASOFTController::class, 'index'])->name('fabricasoft.index.alternative');
    
    // Rutas de pre-registro
    Route::get('/preregistro', [PreregistroController::class, 'index'])->name('fabricasoft.preregistro');
    Route::post('/preregistro', [PreregistroController::class, 'store'])->name('fabricasoft.preregistro.store');
    Route::get('/preregistro/success', [PreregistroController::class, 'success'])->name('fabricasoft.preregistro.success');
    
    // Rutas de autenticación para clientes externos
    Route::get('/client/login', [ClientAuthController::class, 'showLoginForm'])->name('fabricasoft.client.login');
    Route::post('/client/login', [ClientAuthController::class, 'login'])->name('fabricasoft.client.login');
    Route::post('/client/logout', [ClientAuthController::class, 'logout'])->name('fabricasoft.client.logout');
    
    // Ruta de prueba para el modal
    Route::get('/test-modal', [AdminController::class, 'testModal'])->name('fabricasoft.admin.test.modal');
    
    // Rutas del admin (requieren autenticación y rol de admin)
    Route::prefix('admin')->middleware(['auth', 'custom.role:fabricasoft.admin'])->group(function() {
        // Página de inicio con gestión de usuarios
        Route::get('/inicio', [AdminController::class, 'inicio'])->name('fabricasoft.admin.inicio');
        
        // Dashboard del admin (redirige a inicio)
        Route::get('/', function() {
            return redirect()->route('fabricasoft.admin.inicio');
        })->name('fabricasoft.admin.dashboard');
        
        // Dashboard del admin (vista directa)
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('fabricasoft.admin.dashboard.view');
        
        // Gestión de solicitudes
        Route::get('/solicitudes', [AdminController::class, 'solicitudes'])->name('fabricasoft.admin.solicitudes');
        Route::get('/solicitud/{id}', [AdminController::class, 'showSolicitud'])->name('fabricasoft.admin.show.solicitud');
        
        // Asignación de analistas
        Route::post('/solicitud/{id}/asignar-analista', [AdminController::class, 'assignAnalyst'])->name('fabricasoft.admin.asignar.analista');
        Route::post('/solicitud/{id}/cambiar-analista', [AdminController::class, 'changeAnalyst'])->name('fabricasoft.admin.cambiar.analista');
        
        // Filtros y búsquedas
        Route::get('/solicitudes/filtrar', [AdminController::class, 'filtrarSolicitudes'])->name('fabricasoft.admin.filtrar.solicitudes');
        
        // Acciones sobre solicitudes
        Route::post('/solicitud/{id}/aprobar', [AdminController::class, 'aprobarSolicitud'])->name('fabricasoft.admin.aprobar.solicitud');
        Route::post('/solicitud/{id}/rechazar', [AdminController::class, 'rechazarSolicitud'])->name('fabricasoft.admin.rechazar.solicitud');
        
        // Creación de equipos Scrum
        Route::post('/solicitud/{id}/crear-proyecto', [ScrumProjectController::class, 'createProject'])->name('fabricasoft.admin.crear.proyecto');
        
        // Descarga de SRS
        Route::get('/solicitud/{id}/descargar-srs', [AdminController::class, 'downloadSRS'])->name('fabricasoft.admin.descargar.srs');
        
        // Gestión de usuarios
        Route::post('/usuarios/crear', [AdminController::class, 'crearUsuario'])->name('fabricasoft.admin.usuarios.crear');
        Route::post('/usuarios/{userId}/cambiar-rol', [AdminController::class, 'cambiarRol'])->name('fabricasoft.admin.usuarios.cambiar.rol');
        Route::put('/usuarios/{userId}/editar', [AdminController::class, 'editarUsuario'])->name('fabricasoft.admin.usuarios.editar');
        Route::delete('/usuarios/{userId}/eliminar', [AdminController::class, 'eliminarUsuario'])->name('fabricasoft.admin.usuarios.eliminar');
        
        // Exportación
        Route::get('/exportar-solicitudes', [AdminController::class, 'exportarSolicitudes'])->name('fabricasoft.admin.exportar.solicitudes');
        
        // Aprobar y rechazar solicitudes
        Route::post('/solicitud/{id}/aprobar', [AdminController::class, 'aprobarSolicitud'])->name('fabricasoft.admin.solicitud.aprobar');
        Route::post('/solicitud/{id}/rechazar', [AdminController::class, 'rechazarSolicitud'])->name('fabricasoft.admin.solicitud.rechazar');
        
                        // Gestión de equipos Scrum
                Route::get('/proyectos', [ScrumProjectController::class, 'listProjects'])->name('fabricasoft.admin.projects.list');
                Route::get('/proyecto/{id}', [ScrumProjectController::class, 'showProject'])->name('fabricasoft.admin.projects.show');
                Route::post('/proyecto/{id}/agregar-miembro', [ScrumProjectController::class, 'addTeamMember'])->name('fabricasoft.admin.projects.add.member');
                Route::delete('/proyecto/{id}/remover-miembro/{memberId}', [ScrumProjectController::class, 'removeTeamMember'])->name('fabricasoft.admin.projects.remove.member');
                
                // Gestión de fases del proyecto
                Route::post('/proyecto/{id}/fase/{phaseId}/asignar', [ScrumProjectController::class, 'assignPhase'])->name('fabricasoft.admin.projects.phase.assign');
                Route::post('/proyecto/{id}/fase/{phaseId}/iniciar', [ScrumProjectController::class, 'startPhase'])->name('fabricasoft.admin.projects.phase.start');
                Route::post('/proyecto/{id}/fase/{phaseId}/completar', [ScrumProjectController::class, 'completePhase'])->name('fabricasoft.admin.projects.phase.complete');
                Route::post('/proyecto/{id}/fase/{phaseId}/completar-con-info', [ScrumProjectController::class, 'completePhaseWithInfo'])->name('fabricasoft.admin.projects.phase.complete.with.info');
                Route::post('/proyecto/{id}/fase/completar-con-info', [ScrumProjectController::class, 'completePhaseWithInfoSimple'])->name('fabricasoft.admin.projects.phase.complete.with.info.simple');
                Route::post('/proyecto/{id}/entregar', [ScrumProjectController::class, 'entregarProyecto'])->name('fabricasoft.admin.projects.deliver');
                
                // Nuevas rutas para el sistema secuencial de fases
                Route::get('/proyecto/{id}/fase/guardar', [ScrumProjectController::class, 'savePhase'])->name('fabricasoft.admin.projects.phase.save');
                Route::post('/proyecto/{id}/fase/{phaseId}/habilitar-siguiente', [ScrumProjectController::class, 'enableNextPhase'])->name('fabricasoft.admin.projects.phase.enable.next');
                Route::post('/proyecto/{id}/fase/agregar-info', [ScrumProjectController::class, 'addPhaseInfo'])->name('fabricasoft.admin.projects.add.phase.info');
                Route::post('/proyecto/{id}/fase/informacion/agregar', [ScrumProjectController::class, 'addPhaseInfo'])->name('fabricasoft.admin.projects.phase.info.add');
                Route::put('/proyecto/{id}/fase/informacion/actualizar', [ScrumProjectController::class, 'updatePhaseInfo'])->name('fabricasoft.admin.projects.phase.info.update');
                Route::delete('/proyecto/{id}/fase/informacion/{historyId}/eliminar', [ScrumProjectController::class, 'deletePhaseInfoNew'])->name('fabricasoft.admin.projects.phase.info.delete');
                Route::get('/proyecto/{id}/fase/informacion/{historyId}/info', [ScrumProjectController::class, 'getPhaseInfo'])->name('fabricasoft.admin.projects.phase.info.get');
                Route::put('/proyecto/{id}/historial/actualizar', [ScrumProjectController::class, 'updatePhaseInfo'])->name('fabricasoft.admin.projects.historial.update');
                Route::delete('/proyecto/{id}/historial/{historyId}/eliminar', [ScrumProjectController::class, 'deletePhaseInfo'])->name('fabricasoft.admin.projects.historial.delete');
                Route::post('/proyecto/{id}/fase2/guardar', [ScrumProjectController::class, 'savePhase2'])->name('fabricasoft.admin.projects.phase2.save');
    });
    
    // Rutas del analista (requieren autenticación y rol de analista)
    Route::prefix('analyst')->middleware(['auth', 'custom.role:fabricasoft.analista'])->group(function() {
        // Dashboard del analista
        Route::get('/', [AnalystController::class, 'dashboard'])->name('fabricasoft.analyst.dashboard');
        
        // Gestión de solicitudes asignadas
        Route::get('/solicitudes', [AnalystController::class, 'assignedRequests'])->name('fabricasoft.analyst.solicitudes');
        Route::get('/solicitud/{id}', [AnalystController::class, 'showRequest'])->name('fabricasoft.analyst.show.request');
        
        // Análisis y SRS
        Route::post('/solicitud/{id}/iniciar-analisis', [AnalystController::class, 'startAnalysis'])->name('fabricasoft.analyst.iniciar.analisis');
        Route::post('/solicitud/{id}/subir-srs', [AnalystController::class, 'uploadSRS'])->name('fabricasoft.analyst.subir.srs');
        Route::post('/solicitud/{id}/actualizar-notas', [AnalystController::class, 'updateNotes'])->name('fabricasoft.analyst.actualizar.notas');
        
        // Descarga de SRS
        Route::get('/solicitud/{id}/descargar-srs', [AnalystController::class, 'downloadSRS'])->name('fabricasoft.analyst.descargar.srs');
        
        // Equipos Scrum asignados al analista
        Route::get('/proyectos', [AnalystController::class, 'assignedProjects'])->name('fabricasoft.analyst.projects');
        Route::get('/proyecto/{id}', [AnalystController::class, 'showProject'])->name('fabricasoft.analyst.projects.show');
        Route::post('/proyecto/{projectId}/fase/info/agregar', [AnalystController::class, 'addPhaseInfo'])->name('fabricasoft.analyst.projects.phase.info.add');
Route::post('/proyecto/{projectId}/fase/info/agregar-especifico', [AnalystController::class, 'addPhaseInfoSpecific'])->name('fabricasoft.analyst.projects.phase.info.add.specific');
        Route::get('/proyecto/{projectId}/fase/{phaseId}/info/{infoId}/editar', [AnalystController::class, 'getPhaseInfoForEdit'])->name('fabricasoft.analyst.projects.phase.info.get');
        Route::put('/proyecto/{projectId}/fase/{phaseId}/info/{historyId}/editar', [AnalystController::class, 'editPhaseInfo'])->name('fabricasoft.analyst.projects.phase.info.edit');
        Route::delete('/proyecto/{projectId}/fase/{phaseId}/info/{infoId}/eliminar', [AnalystController::class, 'deletePhaseInfo'])->name('fabricasoft.analyst.projects.phase.info.delete');
        
        // Rutas adicionales para funcionalidad completa
        Route::get('/proyectos/list', [AnalystController::class, 'assignedProjects'])->name('fabricasoft.analyst.projects.list');
        Route::post('/proyecto/{projectId}/agregar-miembro', [AnalystController::class, 'addTeamMember'])->name('fabricasoft.analyst.projects.add.member');
        Route::delete('/proyecto/{projectId}/remover-miembro/{memberId}', [AnalystController::class, 'removeTeamMember'])->name('fabricasoft.analyst.projects.remove.member');
        Route::post('/proyecto/{projectId}/fase/{phaseId}/completar-con-info', [AnalystController::class, 'completePhaseWithInfo'])->name('fabricasoft.analyst.projects.phase.complete.with.info');
        Route::post('/proyecto/{projectId}/fase/{phaseId}/iniciar', [AnalystController::class, 'startPhase'])->name('fabricasoft.analyst.projects.phase.start');
        Route::put('/proyecto/{projectId}/fase/informacion/actualizar', [AnalystController::class, 'updatePhaseInfo'])->name('fabricasoft.analyst.projects.phase.info.update');
        Route::put('/proyecto/{projectId}/fase/informacion/actualizar-nuevo', [AnalystController::class, 'updatePhaseInfoNew'])->name('fabricasoft.analyst.projects.phase.info.update.new');
        Route::delete('/proyecto/{projectId}/fase/informacion/{historyId}/eliminar', [AnalystController::class, 'deletePhaseInfoSimple'])->name('fabricasoft.analyst.projects.phase.info.delete');
        Route::get('/proyecto/{projectId}/fase/informacion/{historyId}/info', [AnalystController::class, 'getPhaseInfo'])->name('fabricasoft.analyst.projects.phase.info.get');
        
        // Ruta para guardar información general de fase
        Route::post('/proyecto/{projectId}/fase/guardar', [AnalystController::class, 'savePhase'])->name('fabricasoft.analyst.projects.phase.save');
        
        // Ruta para guardar fase 2 (Análisis y SRS)
        Route::post('/proyecto/{projectId}/fase2/guardar', [AnalystController::class, 'savePhase2'])->name('fabricasoft.analyst.projects.phase2.save');
    });
    
    // Rutas de dashboards por rol
    Route::middleware(['auth'])->group(function() {
        Route::get('/desarrollador/dashboard', [FABRICASOFTController::class, 'desarrolladorDashboard'])->name('fabricasoft.desarrollador.dashboard');
        Route::get('/desarrollador/proyectos', [FABRICASOFTController::class, 'desarrolladorProjects'])->name('fabricasoft.desarrollador.projects');
        Route::get('/cliente-interno/dashboard', [FABRICASOFTController::class, 'clienteInternoDashboard'])->name('fabricasoft.cliente_interno.dashboard');
    });
    
    // Rutas específicas para clientes externos
    Route::prefix('cliente-externo')->name('cliente_externo.')->middleware(['auth', 'custom.role:fabricasoft.cliente_externo'])->group(function () {
        Route::get('/dashboard', [FABRICASOFTController::class, 'clienteExternoDashboard'])->name('dashboard');
        Route::get('/proyectos', [FABRICASOFTController::class, 'clienteExternoProjects'])->name('projects');
        Route::get('/proyecto/{id}', [FABRICASOFTController::class, 'showProjectForClient'])->name('projects.show');
        Route::get('/nueva-solicitud', [FABRICASOFTController::class, 'nuevaSolicitudForm'])->name('nueva-solicitud');
        Route::post('/nueva-solicitud', [FABRICASOFTController::class, 'guardarNuevaSolicitud'])->name('nueva-solicitud.store');
        
        // Rutas para ajustes del perfil
        Route::get('/ajustes', [FABRICASOFTController::class, 'ajustesPerfil'])->name('ajustes');
        Route::post('/ajustes', [FABRICASOFTController::class, 'actualizarPerfil'])->name('ajustes.update');
    });
    
    // Rutas específicas para clientes internos
    Route::prefix('cliente-interno')->middleware(['auth', 'custom.role:fabricasoft.cliente_interno'])->group(function() {
        Route::get('/proyectos', [FABRICASOFTController::class, 'clienteInternoProjects'])->name('fabricasoft.cliente_interno.projects');
        Route::get('/proyecto/{id}', [FABRICASOFTController::class, 'clienteInternoShowProject'])->name('fabricasoft.cliente_interno.projects.show');
        Route::get('/nueva-solicitud', [FABRICASOFTController::class, 'clienteInternoNuevaSolicitudForm'])->name('fabricasoft.cliente_interno.nueva-solicitud');
        Route::post('/nueva-solicitud', [FABRICASOFTController::class, 'clienteInternoGuardarNuevaSolicitud'])->name('fabricasoft.cliente_interno.nueva-solicitud.store');
        
        // Rutas para ajustes del perfil
        Route::get('/ajustes', [FABRICASOFTController::class, 'clienteInternoAjustes'])->name('fabricasoft.cliente_interno.ajustes');
        Route::post('/ajustes', [FABRICASOFTController::class, 'clienteInternoActualizarAjustes'])->name('fabricasoft.cliente_interno.ajustes.update');
        

        
        // Ruta para obtener datos del usuario actual
        Route::get('/user-data', function() {
            $user = auth()->user();
            if (!$user) {
                return response()->json(['error' => 'Usuario no autenticado']);
            }
            
            // Obtener preregistro si existe
            $preregistro = \Modules\FABRICASOFT\Entities\Preregistration::where('email', $user->email)
                ->orderBy('created_at', 'desc')
                ->first();
                
            return response()->json([
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'nickname' => $user->nickname,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'created_at' => $user->created_at,
                    'updated_at' => $user->updated_at
                ],
                'preregistro' => $preregistro ? [
                    'id' => $preregistro->id,
                    'organization' => $preregistro->organization,
                    'phone' => $preregistro->phone,
                    'client_type' => $preregistro->client_type,
                    'status' => $preregistro->status,
                    'created_at' => $preregistro->created_at,
                    'updated_at' => $preregistro->updated_at
                ] : null,
                'datos_perfil' => [
                    'name' => $user->nickname ?: $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'organization' => $preregistro ? $preregistro->organization : null
                ]
            ]);
        })->name('fabricasoft.cliente_interno.ajustes.user_data');
    });
    
    // Rutas específicas del desarrollador
Route::prefix('desarrollador')->middleware(['auth', 'custom.role:fabricasoft.desarrollador'])->group(function() {
    Route::get('/proyecto/{id}', [ScrumProjectController::class, 'showProjectForDeveloper'])->name('fabricasoft.desarrollador.projects.show');

// Rutas para ajustes del desarrollador
Route::get('/ajustes', [FABRICASOFTController::class, 'desarrolladorAjustes'])->name('fabricasoft.desarrollador.ajustes');
Route::post('/ajustes', [FABRICASOFTController::class, 'desarrolladorActualizarAjustes'])->name('fabricasoft.desarrollador.ajustes.update');
    
    // Rutas para gestión de información de fases
    Route::post('/proyecto/{projectId}/fase/info/agregar', [ScrumProjectController::class, 'addPhaseInfoForDeveloper'])->name('fabricasoft.desarrollador.projects.phase.info.add');
Route::post('/proyecto/{projectId}/fase/info/agregar-especifico', [ScrumProjectController::class, 'addPhaseInfoSpecific'])->name('fabricasoft.desarrollador.projects.phase.info.add.specific');
    Route::put('/proyecto/{projectId}/fase/informacion/actualizar-nuevo', [ScrumProjectController::class, 'updatePhaseInfoForDeveloper'])->name('fabricasoft.desarrollador.projects.phase.info.update.new');
    Route::delete('/proyecto/{projectId}/fase/informacion/{historyId}/eliminar', [ScrumProjectController::class, 'deletePhaseInfoForDeveloper'])->name('fabricasoft.desarrollador.projects.phase.info.delete');
    Route::get('/proyecto/{projectId}/fase/informacion/{historyId}/info', [ScrumProjectController::class, 'getPhaseInfoForDeveloper'])->name('fabricasoft.desarrollador.projects.phase.info.get');
});

    // Ruta de prueba para verificar que funciona
    Route::get('/test-simple', function() {
        return response()->json([
            'message' => 'Ruta funcionando',
            'timestamp' => now(),
            'user' => auth()->user() ? auth()->user()->email : 'No autenticado'
        ]);
    })->name('fabricasoft.cliente_interno.test.simple');
});
