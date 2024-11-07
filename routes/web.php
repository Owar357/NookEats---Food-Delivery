<?php

use App\Http\Controllers\ComidaRestauranteController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

//Rutas para el manejo de los dashboards
Route::get('/admin', [HomeController::class, 'dash'])->name('admin')->middleware('auth.admin');
Route::get('/admin-rest', [HomeController::class, 'restDash'])->name('admin-rest')->middleware('auth.rest');

//* Rutas para ComidaRestauranteController
Route:: get('/restaurante/{id}/comidas', [ComidaRestauranteController::class, 'ListarComidas' ]);
Route:: post('/restaurante/comida/crear', [ComidaRestauranteController::class, 'AgregarComida' ]);
Route:: put('/restaurante/comida/{id}/editar', [ComidaRestauranteController::class, 'EditarComida' ]);
Route:: put('/restaurante/comida/{id}/promocion', [ComidaRestauranteController::class, 'ActivarDesactivarPromocion' ]);