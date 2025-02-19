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

    


//Rutas para el manejo de los dashboards
Route::get('/admin', [HomeController::class, 'dash'])->name('admin')->middleware('auth.admin');
Route::get('/admin-rest', [HomeController::class, 'restDash'])->name('admin-rest')->middleware('auth.rest');
Route::get('/dashboard', [HomeController::class, 'restDash'])->middleware('auth.rest');



Route::middleware('auth.rest')->prefix('admin/rest/comida')->group(function () {
    //** Listar comidas */
    Route::get('/listar', [ComidaRestauranteController::class, 'listarComidas']); //****/

    // **Rutas para crear comida */
    Route::get('/añadir', [ComidaRestauranteController::class, 'viewAñadirComida'])->name('comida.añadir'); //****/
    Route::post('/crear', [ComidaRestauranteController::class, 'agregarComida']); //****/

    // **Rutas para editar comida
    Route::get('/editar', [ComidaRestauranteController::class, 'ViewEditarComida'])->name('comida.editarVista'); //****/
    Route::post('/{id}/editar', [ComidaRestauranteController::class, 'editarComida']); //****/
    Route::put('/{id}/estado', [ComidaRestauranteController::class, 'estadoComida']); //****/

    // **Rutas para promociones */
    Route::get('/promociones', [ComidaRestauranteController::class, 'ViewPromociones'])->name('comida.promociones'); //****/
    Route::put('/{id}/promocion/anadir', [ComidaRestauranteController::class, 'activarDatosPromocion']); //****/
    Route::get('/promocion/listar', [ComidaRestauranteController::class, 'listarPromociones']); //****/
    Route::put('/{id}/promocion/estado', [ComidaRestauranteController::class, 'cambiarEstadoPromocion']); //****/

    //**Rutas para las categorias */

    Route::get('/categorias/edit', [ComidaRestauranteController::class, 'viewCategorias'])->name('comida.categorias');
    Route::post('/categorias/crear', [ComidaRestauranteController::class, 'crearCategoria']); //****/
    Route::get('/categorias/listar', [ComidaRestauranteController::class, 'listarCategorias']); //****/
    Route::put('/categoria/{id}/editar', [ComidaRestauranteController::class, 'editarCategoria']);



    //**Rutas para pedidos */
    Route::get('/pedidos', [VentasController::class, 'viewPedidosRestaurante'])->name('pedidos');
    Route::get('/pedidos/listar', [VentasController::class, 'verPedidosRestaurante']);
    Route::patch('/pedido/{id}/estado', [VentasController::class, 'modificarEstadoPedido']);

    //**Rutas para Historial de Ventas */
    Route::get('/ventas/historial', [VentasController::class, 'viewHistorialCompras'])->name('ventas.historial');
    Route::get('/ventas/historial/listar', [VentasController::class, 'verHistorialVentas']);



    //**Ruta de los Horarios *
    Route::get('/horario', [RestaurantePerfilController::class, 'HorariosView'])->name('horarios.lista');
    Route::get('/horario/config', [RestaurantePerfilController::class, 'listarHorarios']);
    Route::post('/horario/config/anadir', [RestaurantePerfilController::class, 'agregarHorario']);
    Route::delete('/horario/config/eliminar/{id}', [RestaurantePerfilController::class, 'eliminarHorario']);
    Route::put('/horario/config/editar/{id}', [RestaurantePerfilController::class, 'editarHorario']);
});


Route::middleware('auth.user')->group(function () {

    //*Ruta para redirrecionar a la vista del perfil del compras del usuario  */
    Route::get('/dashboard/user', [UserController::class, 'viewUserDashboard'])->name('user.dashboard');
    //*ruta para poder realizar los pedidos  como usuario de la aplicacion
    Route::post('restaurante/usuario/pedido', [PedidoController::class, 'realizarPedido']);
    //*Ver restaurantes recientes  para cuando se esta logueado*/
    Route::get('/restaurantes/recientes', [PedidoController::class, 'restaurantesRecientes']); 
});
//*Ruta para mostrar restaurantes recien agregados a usuarios no logeados*/
Route::get('restaurantes/recientes', [PedidoController::class, 'restaurantesRecientes']); //***/    



Route::middleware('auth.rest')->group(function () {
    Route::post('/registro', [RestaurantePerfilController::class, 'envioFormularioRegistro']); //***/
    Route::post('restaurante/config/imagen', [RestaurantePerfilController::class, 'añadirActualizarImagen']);
    Route::get('/seleccionar/negocio', [RestaurantePerfilController::class, 'listarTipoNegocio']); //***/
});



Route::middleware('auth')->group(function () {
    Route::get('usuario/pedidos', [PedidoController::class, 'verHistorialCompras']);
    Route::put('usuario/pedido/{id}/cancelar', [PedidoController::class, 'cancelarCompra']);
    Route::get('restaurantes/lista', [PedidoController::class, 'listaRestaurantes']);
    //Configuracion del perfil
    Route::put('usuario/config', [UserController::class, 'actualizarPerfil']);
    Route::get('usuario/imagen_perfil', [UserController::class, 'mostrarImagenPefil']);
});

Route::get('/usuario/formulario', [RestaurantePerfilController::class, 'viewFormulario'])->name('formularioR'); //***/
//Ruta para redireccion de midllware;
Route::get('/index/welcome',[UserController::class,'viewHomePage'])->name('homePage');






Route::get('/restaurante/{id}', [PedidoController::class, 'verPerfilRestauranteView']);
Route::get('/{id}/menu/', [PedidoController::class, 'verPerfilRestaurante']);
