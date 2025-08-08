<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductoController;
use App\Http\Controllers\Api\ClienteController;
use App\Http\Controllers\Api\ConfiguracionController;
use App\Http\Controllers\Api\StockController;
use App\Http\Controllers\Api\VehiculoController;
use App\Http\Controllers\Api\RepartoController;
use App\Http\Controllers\Api\ReporteController;

// Authentication routes versionadas
Route::middleware('throttle:auth')->prefix('v1/auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/user', [AuthController::class, 'user']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/refresh', [AuthController::class, 'refresh']);
    });
});

// Rutas públicas (sin autenticación)
Route::middleware('throttle:api-read')->prefix('v1')->group(function () {
    // Configuraciones públicas
    Route::get('/configuraciones', [ConfiguracionController::class, 'index']);
    Route::get('/configuraciones/{clave}', [ConfiguracionController::class, 'show']);
    
    // Productos (solo lectura pública)
    Route::get('/productos', [ProductoController::class, 'index']);
    Route::get('/productos/{id}', [ProductoController::class, 'show']);
    Route::get('/productos/buscar/{termino}', [ProductoController::class, 'search']);
});

// Rutas protegidas (requieren autenticación)
Route::middleware(['auth:sanctum', 'throttle:api-read'])->prefix('v1')->group(function () {
    // GET routes (lectura)
    Route::apiResource('clientes', ClienteController::class)->only(['index', 'show']);
    Route::get('/clientes/buscar/{termino}', [ClienteController::class, 'search']);
    
    Route::prefix('stock')->group(function () {
        Route::get('/', [StockController::class, 'index']);
        Route::get('/alertas', [StockController::class, 'alertas']);
        Route::get('/para-fabricar', [StockController::class, 'paraFabricar']);
        Route::get('/historial/{producto}', [StockController::class, 'historial']);
    });
    
    Route::apiResource('vehiculos', VehiculoController::class)->only(['index', 'show']);
    Route::get('/vehiculos-disponibles', [VehiculoController::class, 'disponibles']);
    
    Route::apiResource('repartos', RepartoController::class)->only(['index', 'show']);
    Route::get('/planificacion-semanal', [RepartoController::class, 'planificacionSemanal']);
    Route::get('/seguimiento-tiempo-real', [RepartoController::class, 'seguimientoTiempoReal']);
    
    Route::prefix('reportes')->group(function () {
        Route::get('/dashboard', [ReporteController::class, 'dashboard']);
        Route::get('/vehiculos', [ReporteController::class, 'reporteVehiculos']);
        Route::get('/entregas', [ReporteController::class, 'reporteEntregas']);
        Route::get('/operativo', [ReporteController::class, 'reporteOperativo']);
    });
});

// Rutas de escritura (POST, PUT, DELETE) con rate limiting más restrictivo
Route::middleware(['auth:sanctum', 'throttle:api-write'])->prefix('v1')->group(function () {
    // Productos CRUD (escritura)
    Route::apiResource('productos', ProductoController::class)->except(['index', 'show']);
    
    // Clientes CRUD (escritura)
    Route::apiResource('clientes', ClienteController::class)->except(['index', 'show']);
    
    // Configuraciones admin
    Route::post('/configuraciones', [ConfiguracionController::class, 'store']);
    Route::put('/configuraciones/{id}', [ConfiguracionController::class, 'update']);
    Route::delete('/configuraciones/{id}', [ConfiguracionController::class, 'destroy']);
    
    // Gestión de Stock (escritura crítica - REQUIERE IDEMPOTENCIA)
    Route::prefix('stock')->middleware(\App\Http\Middleware\Idempotency::class)->group(function () {
        Route::post('/ajustar', [StockController::class, 'ajustar']);
        Route::post('/ingreso', [StockController::class, 'ingreso']);
        Route::post('/marcar-fabricar/{producto}', [StockController::class, 'marcarFabricar']);
    });
    
    // Gestión de Vehículos (escritura)
    Route::apiResource('vehiculos', VehiculoController::class)->except(['index', 'show']);
    Route::patch('/vehiculos/{vehiculo}/activar', [VehiculoController::class, 'activar']);
    Route::patch('/vehiculos/{vehiculo}/desactivar', [VehiculoController::class, 'desactivar']);
    
    // Gestión de Repartos (escritura crítica - REQUIERE IDEMPOTENCIA)
    Route::middleware(\App\Http\Middleware\Idempotency::class)->group(function () {
        Route::apiResource('repartos', RepartoController::class)->except(['index', 'show']);
        Route::patch('/repartos/{reparto}/estado', [RepartoController::class, 'cambiarEstado']);
    });
});