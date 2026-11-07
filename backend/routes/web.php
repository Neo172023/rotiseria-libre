<?php
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SuperadminComercioController;
use App\Http\Controllers\SuperadminUsuarioController;
use App\Http\Controllers\ComercianteController;
use App\Http\Controllers\ComercianteProductoController;
use App\Http\Middleware\RequireSuperadmin;
use Illuminate\Support\Facades\Route;
Route::get('/', fn () => response()->json(['app' => 'Rotisería Libre', 'status' => 'backend en construcción']));
Route::middleware('guest')->group(function () {
    Route::view('/ingresar', 'auth.login')->name('login');
    Route::post('/ingresar', [AuthController::class, 'login'])->middleware('throttle:5,1');
});
Route::post('/salir', [AuthController::class, 'logout'])->middleware('auth');
Route::middleware(['auth', RequireSuperadmin::class])->prefix('admin')->group(function () {
    Route::get('/comercios', [SuperadminComercioController::class, 'index']);
    Route::post('/comercios', [SuperadminComercioController::class, 'store']);
    Route::post('/comercios/{comercio}/usuarios', [SuperadminUsuarioController::class, 'store']);
    Route::patch('/comercios/{comercio}/estado', [SuperadminComercioController::class, 'estado']);
});

Route::middleware('auth')->get('/comerciante', [ComercianteController::class, 'index']);

Route::middleware('auth')->prefix('comerciante/{comercio}')->group(function () {
    Route::get('/productos', [ComercianteProductoController::class, 'index']);
    Route::post('/productos', [ComercianteProductoController::class, 'store']);
    Route::patch('/productos/{producto}', [ComercianteProductoController::class, 'update']);
    Route::delete('/productos/{producto}', [ComercianteProductoController::class, 'destroy']);
});

Route::middleware('auth')->prefix('comerciante/{comercio}')->group(function () {
 Route::get('/pedidos', [\App\Http\Controllers\ComerciantePedidoController::class, 'index']);
 Route::patch('/pedidos/{pedido}/estado', [\App\Http\Controllers\ComerciantePedidoController::class, 'estado']);
});

Route::middleware('auth')->post('/comerciante/{comercio}/logo', [\App\Http\Controllers\ComercianteImagenController::class, 'logo']);

Route::middleware('auth')->patch('/comerciante/{comercio}/identidad', [\App\Http\Controllers\ComercianteIdentidadController::class, 'update']);

Route::middleware('auth')->get('/comerciante/{comercio}/tablero', [\App\Http\Controllers\ComercianteDashboardController::class, 'index']);

Route::middleware('auth')->get('/comerciante/{comercio}/movimientos-stock', [\App\Http\Controllers\ComercianteMovimientoStockController::class, 'index']);
