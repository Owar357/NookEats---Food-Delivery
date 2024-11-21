<?php

use App\Http\Controllers\ComidaRestauranteController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\VentasController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

/// esto significa: /***/ que estamos ocupando la sesión del usuario para filtrar cosas;


Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

//Rutas para el manejo de los dashboards
Route::get('/admin', [HomeController::class, 'dash'])->name('admin')->middleware('auth.admin');
Route::get('/admin-rest', [HomeController::class, 'restDash'])->name('admin-rest')->middleware('auth.rest');

//* Rutas para ComidaRestauranteController
Route::get('/restaurante/comidas', [ComidaRestauranteController::class, 'ListarComidas' ])->middleware('auth.rest');//***/
Route::post('/restaurante/comida/crear', [ComidaRestauranteController::class, 'AgregarComida' ])->middleware('auth.rest');
Route::put('/restaurante/comida/{id}/editar', [ComidaRestauranteController::class, 'EditarComida' ])->middleware('auth.rest');
Route::put('/restaurante/comida/{id}/promocion', [ComidaRestauranteController::class, 'ActivarDesactivarPromocion' ])->middleware('auth.rest');
Route::get('/comidas', [ComidaRestauranteController::class, 'getViewComidas'])->name('comidas');


//* Rutas para  VentasController
Route::get('restaurante/pedidos/ver', [VentasController::class,'VerPedidosRestaurante'])->middleware('auth.rest');//***/
Route::put('restaurante/pedidos/{id}/estado', [VentasController::class,'ModificarEstadoPedido'])->middleware('auth.rest');
Route::get('restaurante/categoria/listar',[VentasController::class,'ListarCategorias'])->middleware('auth.rest');//***/
Route::get('restaurante/Pedido/Ver/Historial',[VentasController::class,'verHistorialVentas'])->middleware('auth.rest');//***/




//*Rutas para pedidos del usuario
Route::post('usuario/Pedido/Crear',[PedidoController::class,'RealizarPedido']); //*
Route::get('usuario/Pedidos/Ver',[PedidoController::class,'VerHistorialCompras']);
Route::put('usuario/Pedido/{id}/Cancelar',[PedidoController::class,'CancelarCompra']);



