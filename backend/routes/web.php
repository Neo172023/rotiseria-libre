<?php
use Illuminate\Support\Facades\Route;
Route::get('/', fn () => response()->json(['app' => 'Rotisería Libre', 'status' => 'backend en construcción']));
