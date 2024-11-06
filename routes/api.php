<?php

use App\Http\Controllers\ComidaRestauranteController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('restaurante/{id}/comidas', [ComidaRestauranteController::class, 'ListarComidas' ]);
Route:: post('restaurante/comida/crear', [ComidaRestauranteController::class, 'AgregarComida' ]);
Route:: put('restaurante/comida/{id}/editar', [ComidaRestauranteController::class, 'EditarComida' ]);
Route:: put('restaurante/comida/{id}/promocion', [ComidaRestauranteController::class, 'ActivarDesactivarPromocion' ]);