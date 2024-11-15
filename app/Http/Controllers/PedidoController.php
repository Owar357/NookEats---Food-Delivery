<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\PedidoComida;
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
  public function  RealizarPedido(Request $request)
  {

    try {
      DB::beginTransaction();

      // Se crea el pedido
      $pedido = new Pedido();
      $pedido->fecha_hora_pedido = now();
      $numero_generado = $this->numerodePedido();
      $pedido->metodo_pago = $request->metodo_pago;
      // Para pruebas API
      $pedido->usuario_id = $request->usuario_id;
      // Activar cuando la ruta esté protegida con el middleware para esperar los resultados correspondientes
      // $pedido->id_usuario = auth()->id();
      $pedido->total = $request->total;
      $pedido->estado_pedido = $request->estado_pedido;

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
        'message' => 'Ha ocurrido un error al realizar el pedido. Por favor, intente nuevamente, si el problema persiste, repórtelo.' . $q->getMessage()
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
         
        // !! Cuando la ruta esté protegida con middleware, descomentar la línea siguiente
        // $id = Auth::id(); // Obtiene el ID del usuario autenticado
        // Recupar todos los pedidos realizados por el usuario con el ID especificado
        // La consulta busca pedidos donde 'usuario_id' es igual al ID del usuario
        // que ha realizado las compras.
  public function  VerHistorialCompras(Request $request)
  {
    try {

      $id = 1;

      //Activar cuando se implemente la autentificacion
      //$id = Auth::id();

      //!!borrar cuando la ruta  este protegida con midleware en web .php
      $compras =  Pedido::where('usuario_id', $id)->get();

      //activar cuando la ruta este protegida con el midleware en web.php; 
      //$pedidos = Auth::user()->pedidos;

      return response()->json(['status' => 'ok', 'message' => 'Historial de pedidos', 'data' => $compras,], 200);
    } catch (\Throwable $th) {

      return response()->json(['status' => 'fail', 'message' => 'ocurrio un error'], 500);
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
  public function CancelarCompra($id)
  {
    try {

      //verifica que el pedido pertenece al usuario
      $CancelarPedido = Pedido::where('id', $id)->where('usuario_id', Auth::id())->first();

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
}
