<?php

use App\Http\Controllers\ComidaRestauranteController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\RestaurantePerfilController;
use App\Http\Controllers\UserController;
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
Route::get('/dashboard', [HomeController::class, 'restDash'])->middleware('auth.rest');



Route::middleware('auth.rest')->group(function(){
   Route::get('/restaurante/admin/comidas', [ComidaRestauranteController::class, 'listarComidas' ]);
   Route::post('/restaurante/admin/comida/crear', [ComidaRestauranteController::class, 'agregarComida' ]);
   Route::put('/restaurante/admin/comida/{id}/editar', [ComidaRestauranteController::class, 'editarComida' ]); 
   Route::put('/restaurante/admin/comida/{id}/promocion', [ComidaRestauranteController::class, 'activarDesactivarPromocion' ]);
   Route::delete('/restaurante/admin/comida/{id}/eliminar', [ComidaRestauranteController::class, 'eliminarComida' ]); 
   Route::post('/restaurante/admin/categorias/crear', [ComidaRestauranteController::class, 'crearCategoria' ]); 
   Route::get('/restaurante/admin/categorias/listar',[ComidaRestauranteController::class, 'listarCategorias']);
   Route::delete('/restaurante/admin/categoria/{id}/eliminar',[ComidaRestauranteController::class,'eliminarCategoria']);
   Route::put('/restaurante/admin/categoria/{id}/editar',[ComidaRestauranteController::class,'editarCategoria']); 
   Route::get('/comidas', [ComidaRestauranteController::class, 'getViewComidas'])->name('comidas');
});


Route::middleware('auth.rest')->group(function(){
 Route::get('/restaurante/admin/ver/pedidos',[VentasController::class,'verPedidosRestaurante']);
 Route::patch('/restaurante/admin/pedido/{id}/cambiar-estado',[VentasController::class,'modificarEstadoPedido']);
 Route::get('/restaurante/admin/ver/historial-ventas',[VentasController::class,'verHistorialVentas']);
});

Route::middleware('auth.rest')->group(function(){
Route::post('/registro',[RestaurantePerfilController::class,'envioFormularioRegistro']);//***/
Route::post('restaurante/config/imagen',[RestaurantePerfilController::class,'añadirActualizarImagen']);
Route::get('restaurante/config/horario',[RestaurantePerfilController::class,'listarHorarios']);
Route::post('restaurante/config/horario',[RestaurantePerfilController::class,'agregarHorario']);
Route::put('restaurante/config/horario/{id}',[RestaurantePerfilController::class,'editarHorario']);
Route::delete('restaurante/config/horario/{id}',[RestaurantePerfilController::class,'eliminarHorario']);
Route::get('/seleccionar/negocio',[RestaurantePerfilController::class,'listarTipoNegocio']);
//ver configs nos hacen falta.
});



    Route::middleware('auth')->group(function(){
    Route::post('usuario/pedido',[PedidoController::class,'realizarPedido']);
    Route::get('usuario/pedidos',[PedidoController::class,'verHistorialCompras']);
    Route::put('usuario/pedido/{id}/cancelar',[PedidoController::class,'cancelarCompra']);
    Route::get('restaurantes/{id}/menu/',[PedidoController::class,'verPerfilRestaurante']);
    Route::get('restaurantes/lista',[PedidoController::class,'listaRestaurantes']);

    //Configuracion del perfil
    Route::put('usuario/config',[UserController::class,'actualizarPerfil']);
    Route::get('usuario/imagen_perfil',[UserController::class,'mostrarImagenPefil']);

    //ruta de las vistas
        });

Route::get('/usuario/formulario',[RestaurantePerfilController::class,'viewFormulario'])->name('formularioR');
Route::get('restaurantes/recientes',[PedidoController::class,'restaurantesRecientes'])->name('restaurantesRecientes');





