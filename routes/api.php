<?php

use App\Http\Controllers\ComidaRestauranteController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\VentasController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('restaurante/comidas', [ComidaRestauranteController::class, 'ListarComidas' ]);
Route:: post('restaurante/comida/crear', [ComidaRestauranteController::class, 'AgregarComida' ]);
Route:: put('restaurante/comida/{id}/editar', [ComidaRestauranteController::class, 'EditarComida' ]);
Route:: put('restaurante/comida/{id}/promocion', [ComidaRestauranteController::class, 'ActivarDesactivarPromocion' ]);
Route:: get('restaunrante/{id}/compras',[ComidaRestauranteController::class, 'verPefilRestaurante']);



//ruta del pedidoController:
Route::post('usuario/Pedido/Crear',[PedidoController::class,'RealizarPedido']); //*
Route::get('usuario/Pedidos/Ver',[PedidoController::class,'listaRestaurantes']);//*
Route::put('usuario/Pedido/{id}/Cancelar',[PedidoController::class,'CancelarCompra']);



//rutas de de las  VentasController:
Route::get('restaurante/{restauranteId}/pedidos/ver', [VentasController::class,'VerPedidosRestaurante']);//*
Route::put('restaurante/pedidos/{id}/estado', [VentasController::class,'ModificarEstadoPedido']);//*
