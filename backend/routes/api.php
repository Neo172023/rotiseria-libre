<?php
use Illuminate\Support\Facades\Route;
// Ningún endpoint administrativo se publica sin autenticación y políticas.
// Los endpoints públicos se añadirán cuando se implemente el filtrado por estado y comercio.
Route::get('/estado', fn () => response()->json(['status' => 'ok', 'api' => 'en construcción']));

Route::get('/comercios/{slug}/catalogo', [\App\Http\Controllers\CatalogoPublicoController::class, 'show']);

Route::post('/comercios/{slug}/pedidos', [\App\Http\Controllers\PedidoPublicoController::class, 'store'])->middleware('throttle:10,1');
