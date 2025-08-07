<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductoController;
use App\Http\Controllers\Api\ClienteController;
use App\Http\Controllers\Api\ConfiguracionController;
use App\Http\Controllers\Api\StockController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Rutas públicas (sin autenticación)
Route::prefix('v1')->group(function () {
    // Configuraciones públicas
    Route::get('/configuraciones', [ConfiguracionController::class, 'index']);
    Route::get('/configuraciones/{clave}', [ConfiguracionController::class, 'show']);
    
    // Productos (solo lectura pública)
    Route::get('/productos', [ProductoController::class, 'index']);
    Route::get('/productos/{id}', [ProductoController::class, 'show']);
    Route::get('/productos/buscar/{termino}', [ProductoController::class, 'search']);
});

// Rutas protegidas (requieren autenticación)
Route::middleware('auth:sanctum')->prefix('v1')->group(function () {
    // Productos CRUD completo
    Route::apiResource('productos', ProductoController::class)->except(['index', 'show']);
    
    // Clientes CRUD completo
    Route::apiResource('clientes', ClienteController::class);
    Route::get('/clientes/buscar/{termino}', [ClienteController::class, 'search']);
    
    // Configuraciones admin
    Route::post('/configuraciones', [ConfiguracionController::class, 'store']);
    Route::put('/configuraciones/{id}', [ConfiguracionController::class, 'update']);
    Route::delete('/configuraciones/{id}', [ConfiguracionController::class, 'destroy']);
    
    // Gestión de Stock (para CRM)
    Route::prefix('stock')->group(function () {
        Route::get('/', [StockController::class, 'index']);
        Route::post('/ajustar', [StockController::class, 'ajustar']);
        Route::post('/ingreso', [StockController::class, 'ingreso']);
        Route::get('/alertas', [StockController::class, 'alertas']);
        Route::get('/para-fabricar', [StockController::class, 'paraFabricar']);
        Route::get('/historial/{producto}', [StockController::class, 'historial']);
        Route::post('/marcar-fabricar/{producto}', [StockController::class, 'marcarFabricar']);
    });
});