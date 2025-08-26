<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| FABRICASOFT Module API Routes
|--------------------------------------------------------------------------
|
| Aquí se definen las rutas API del módulo FABRICASOFT
|
*/

Route::prefix('fabricasoft')->group(function () {
    
    // Rutas que requieren autenticación
    Route::middleware(['auth:sanctum'])->group(function () {
        
        // Ruta para obtener información del usuario autenticado
        Route::get('/user', function (Request $request) {
            return $request->user();
        });
        
        // Aquí se pueden agregar más rutas API según sea necesario
        // Route::get('/proyectos', [ProyectoController::class, 'index']);
        // Route::post('/proyectos', [ProyectoController::class, 'store']);
        
    });
});
