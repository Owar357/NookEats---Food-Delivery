<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\PedidoComida;
use App\Models\Restaurante;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PedidoController extends Controller
{


  /**
   * Permite realizar pedido
   *
   * Este método crea un nuevo pedido a través de una transacción.Si ocurre un error
   * durante el registro, se realizara un rollback para revertir los cambios realizados
   * hasta el momento.
   *  
   * 
   * @param Request $request Cuerpo de la peticion tipo JSON que contiene los datos del pedido.
   * @param  int  $id_usuario ID del usuario que realiza el pedido,ira dentro del cuerpo de la peticion. 
   * @return jsonResponse devuelve la informacion del registro del pedido.
   * @throws Exception Si ocurre un error durante el registro .
   * 
   * 
   */
  public function  realizarPedido(Request $request)
  {
    try {
      $request->validate([
        'metodo_pago' => 'required|string|max:1',
        'total' => 'required|numeric|min:0|max:9999.99',
        'cantidad' => 'required|integer|min:1',
        'nota' => 'nullable|string',
        'comida_restaurante_id' => 'required|integer',
    ]);
      DB::beginTransaction();
      $pedido = new Pedido();
      $pedido->fecha_hora_pedido = now();
      $pedido-> numero_generado = $this->numerodePedido();
      $pedido->metodo_pago = $request->metodo_pago;
      
      // Activar cuando la ruta esté protegida con el middleware para esperar los resultados correspondientes
      $pedido->id_usuario = Auth::user()->id;
      $pedido->total = $request->total;
      if ($pedido->save()) {
        foreach ($request->pedido_comida as $comida) {
          $pedido_comida = new PedidoComida();
          $pedido_comida->cantidad = $comida['cantidad'];
          $pedido_comida->nota = $comida['nota'];
          $pedido_comida->pedido_id = $pedido->id;
          $pedido_comida->comida_restaurante_id = $comida['comida_restaurante_id'];
          if (!$pedido_comida->save()) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => 'Ocurrió un error al agregar los datos del pedido'], 500);
          }
        }

        DB::commit();
        return response()->json(['status' => 'created', 'message' => 'El pedido se ha realizado con éxito'], 201);
      } else {
        DB::rollBack();
        return response()->json(['status' => 'error', 'message' => 'No se pudo realizar el pedido'], 500);
      }
    } catch (QueryException $q) {
      DB::rollBack();
      return response()->json([
        'status' => 'error',
        'message' => 'Ha ocurrido un error al realizar el pedido. Por favor, intente nuevamente, si el problema persiste, repórtelo.' 
      ], 500);
    }
  }


  public function numerodePedido()
  {
    try {

      do {
        $numero_pedido = strtoupper('P' . str_pad(mt_rand(0, 9999999999), 10, 0, STR_PAD_LEFT));
      } while (Pedido::where('numero_pedido', $numero_pedido)->exists());

      return $numero_pedido;
    } catch (\Exception $e) {

      throw new \Exception('Ocurrió un error al generar el número de pedido: ' . $e->getMessage());
    }
  }



  //Permite ver  el historial de compras del usuario asi como el pedido mas reciente
   // Definir el ID del usuario que estamos buscando las compras.
        // En este caso, se asigna manualmente a 1 para pruebas.
        // Cuando la autenticación esté implementada, se descomenta la siguiente línea
        // para usar el ID del usuario autenticado.
         

        // $id = Auth::id(); // Obtiene el ID del usuario autenticado
        // Recupar todos los pedidos realizados por el usuario con el ID especificado
        // La consulta busca pedidos donde 'usuario_id' es igual al ID del usuario
        // que ha realizado las compras.
  public function  verHistorialCompras()
  {
    try {
 
      $pedidos = Auth::user()->pedidos;

      return response()->json(['status' => 'ok', 'message' => 'Historial de pedidos', 'data' => $pedidos,], 200);
    } catch (\Throwable $th) {
 
      return response()->json(['status' => 'fail', 'message' => 'ocurrio un error inesperado'], 500);
    }
  }


  /**
   * Cancelar pedido
   *
   * Este método permite cancelar  el pedido atraves de una validacion, se pasa el id del pedido
   * luego se verifica que el id del usuario del pedido coincida con el usuario autentificado 
   * que lo quiera eliminar.
   *  
   * 
   * @param  int  $id  id del pedido  que se quiere cancelar,vendra atraves de la url  
   * @return jsonResponse devuelve una respuesta exitosa cuando el pedido es cancelado.
   * @throws Exception Si ocurre un error durante el registro .
   * 
   * 
   */
  public function cancelarCompra($id)
  {
    try {

      //verifica que el pedido pertenece al usuario
      $CancelarPedido = Pedido::where('id', $id)->where('usuario_id', Auth::user())->id->first();

      if (!$CancelarPedido) {
        return response()->json(['status' => 'fail', 'message' => 'Pedido no encontrado No autorizado para Cancelar el pedido'], 404);
      }
      $CancelarPedido->estado = "C";
      $CancelarPedido->save();

      return response()->json(['status' => 'ok', 'message' => 'Pedido cancelado', 200]);
    } catch (\Throwable $th) {

      return response()->json(['status' => 'error', 'message' => 'ocurrio un error'], 500);
    }
  }


   //**funcion que permitira ver al usuario ver el  perfil completo del restaurante seleccionado*/
    //**cosas como foto de perfi, horarios, comidas con sus respectivo datos y categoria disponibles del restaurante ademas de su descripicion*/    
   
     //!Agregar funcionamiento para traer imagen del rest y mostrarlo
    public function verPerfilRestaurante($restaurante_id)
    {
    try {
     $perfilVentas = Restaurante::with(['horarios','categorias.comidasRestaurante'])->where('id',$restaurante_id)->get();

      return response()->json(['status' => 'ok', 'message' => 'informacion del perfil restaurante recuperada con exito', 'data' => $perfilVentas],200);
     } catch (\Throwable $th) {
       return response()->json(['status' => 'ok','message' => 'ocurrio un error inesperado'],500);
    }
    }


    
  //**Lista de restaurante para la busqueda delos mismos */
    public function listaRestaurantes()
    {
      try {
        $listaRestaurantes = Restaurante::select('id','nombre','imagen_original','imagen_hash','descripcion')->get();

   
        $data  = $listaRestaurantes -> map(function($listaRestaurantes){
          return[
             'id' => $listaRestaurantes -> id,
             'nombre'=> $listaRestaurantes -> nombre,
             'imagen_hash' => $listaRestaurantes -> imagen_hash
              ? asset('storage/img/foto_perfil_restuarante/' .$listaRestaurantes -> imagen_hash)
              : asset('image/default_img_logo.png'),
              'imagen_original' => $listaRestaurantes ->imagen_original,
             'descripcion' => $listaRestaurantes -> descripcion
          ];          
        }); 
        return response()->json([ 'restaurantes' =>  $data],200);
      } catch (\Throwable $th) {
        return response()->json(['status' => 'error', 'message' => 'Ocurrio un error inesperado', ],500);
      }
    }


    public function  restaurantesRecientes(){

      try {
        $restaurantes = Restaurante::with(['tipoNegocio'])->latest()->take(6)->get();

        $response = $restaurantes->map(function($restaurante){
          
          
          
        return[
        'id' => $restaurante -> id,
        'nombre' => $restaurante->nombre,
       
        'descripcion' => $restaurante -> descripcion,
        'imagen' => $restaurante ->  imagen
        ? asset('storage/image/foto_perfil_restaurante/' . $restaurante -> imagen_hash)
        : asset ('storage/imagen_default.avif'),
        'imagenOriginal' => $restaurante -> imagen_original,
        'tipoNegocio' => 
         ['id' => $restaurante -> tipoNegocio -> id,
          'nombre' => $restaurante -> tipoNegocio -> nombre,
         ]
        ];
      });

        return response()->json(['status'=> 'ok', 'restaurante' => $response ],200);
      } catch (\Throwable $th) {
        return response()->json(['status'=> 'error', 'Message' =>'Ocurrio un error inesperado'],500);
      }
   


    }
}
