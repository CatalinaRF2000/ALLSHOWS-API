<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\CalendarioController;
use App\Http\Controllers\FacturaController;
use App\Http\Controllers\NominaController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ControlPanelController;
use App\Http\Controllers\CatalogoController;

// ########### Rutas públicas ###################
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

//Subir catalogo

Route::post('catalogo/upload', [CatalogoController::class, 'uploadCatalog']);
Route::get('catalogo/download', [CatalogoController::class, 'downloadCatalog']);

// Rutas públicas: productos sin precio (usando /catalogo)
Route::get('catalogo', [ProductoController::class, 'indexWithoutPrice']);
Route::get('catalogo/{producto}', [ProductoController::class, 'showWithoutPrice']);

 // Clientes pueden ver productos con precio
Route::middleware(['auth:sanctum', \App\Http\Middleware\CheckRole::class . ':admin,cliente'])->group(function () {
    Route::get('productos', [ProductoController::class, 'indexWithPrice']);
    Route::get('productos/{producto}', [ProductoController::class, 'showWithPrice']);
});


// ########### AUTENTICACION ###################
Route::middleware('auth:sanctum')->group(function () {
    // Perfil del usuario autenticado
    Route::get('user', [UserController::class, 'show']);
    Route::put('user', [UserController::class, 'update']);

    // Rutas de recursos para usuarios autenticados (solo visualizar)
    Route::prefix('user')->group(function () {
        Route::get('{user}/facturas', [FacturaController::class, 'index']);
        Route::get('{user}/facturas/{factura}', [FacturaController::class, 'show']);
        Route::get('{user}/facturas/download/{fileName}', [FacturaController::class, 'download']);

        Route::get('{user}/nominas', [NominaController::class, 'index']);
        Route::get('{user}/nominas/{nomina}', [NominaController::class, 'show']);
        Route::get('{user}/nominas/download/{fileName}', [NominaController::class, 'download']);


        Route::get('{user}/calendarios', [CalendarioController::class, 'index']);  
    });

    //Descargas de archivos
    Route::middleware('auth:sanctum')->group(function () {
        
    });
    

    // Cambiar contraseña
    Route::post('change-password', [UserController::class, 'changePassword']);


    // --------------------------------- ADMINISTRADORES -------------------------------------------
    Route::middleware(\App\Http\Middleware\CheckAdminRole::class)->group(function () {
        // Panel de control
        Route::get('control-panel', [ControlPanelController::class, 'index']);

        // Gestión de productos
        Route::post('productos', [ProductoController::class, 'store']);
        Route::put('productos/{producto}', [ProductoController::class, 'update']);
        Route::delete('productos/{producto}', [ProductoController::class, 'destroy']);

        // Gestión de calendarios por administradores
        Route::prefix('user/{user}/calendarios')->group(function () {
            Route::put('{calendario}', [CalendarioController::class, 'update']); // Actualizar calendario
            Route::delete('{calendario}', [CalendarioController::class, 'destroy']); // Eliminar calendario
        });

        // Subida de nóminas y facturas
        Route::post('user/{user}/nominas', [NominaController::class, 'upload']);
        Route::post('user/{user}/facturas', [FacturaController::class, 'upload']);
    });
});
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/user/calendarios', [CalendarioController::class, 'getUserCalendars']);
});



// ACTUALZAR USUARIOS DESDE ADMIN
Route::middleware('auth:sanctum')->group(function () {
    Route::middleware(\App\Http\Middleware\CheckAdminRole::class)->group(function () {
        // Otras rutas de admin
        Route::put('user/{id}', [UserController::class, 'update']); // Actualizar un usuario específico
        Route::post('users', [AuthController::class, 'register']); // Crear un usuario
        Route::delete('user/{id}', [UserController::class, 'destroy']); // Ruta para eliminar un usuario
        Route::get('calendarios', [CalendarioController::class, 'index']); // Lista todos los calendarios
    });
});
