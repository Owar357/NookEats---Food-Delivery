<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use Illuminate\Support\Facades\Auth;
use Throwable;

class VentasController extends Controller
{
  /**
   * Muestra los diferentes pedidos que van dirigidos a un restaurante específico.
   *
   * Este método recupera los pedidos que están en estado 'P' (Pendiente) 
   * y las comidas que están asociadas al  restaurante  por medio de su mismo id a traves de las categoria pertnecientes 
   * 
   *
   * @param string $restuauranteI  id del restaurante;
   * @return JsonResponse Devuelve un JSON con el estado de la operación y los datos de los pedidos.
   * @throws Throwable Si ocurre un error al recuperar los pedidos.
   */

  public function  verPedidosRestaurante()
  {
    try {

      $sessionUsuario = Auth::user();

      $restauranteId = $sessionUsuario->restaurante->id;
      $pedidos = Pedido::with(['user:id,name', 'pedidoComidas.comidaRestaurante'])->where('estado_pedido', 'P')->  //primero busca los pedidos que esten pendientes Y traemos la info del usaurio
        whereHas('pedidoComidas.comidaRestaurante.categoria.restaurante', function ($query) use ($restauranteId) {
          $query->where('id', $restauranteId); //filtramos hasta verificar que el id del restaunte coincida de la comidas  coincida  con el existente
        })->get();

      return response()->json(['status' => 'ok', 'message' => 'Se han cargado correctamente los datos', 'data' => $pedidos], 200);
    } catch (\Throwable $th) {

      return response()->json(['status' => 'fail', 'message' => 'Ocurrio un error inesperado' . $th->getMessage()], 500);
    }
  }


  /**
   * Permite cambiar los estados del pedido.
   *
   * Este metodo permite al restaurante cambiar los estados del pedido segun  las etapas del mismo, 
   *
   * 
   * - - - - - - - - - - - - - - -  
   * 4 tipos de estado existentes
   * P = Pendiente(pedido recien ingresado o oen espera a ser antendido);
   * E = En camino(Cuando el pedido ya ha salido de la sucursal);
   * F = Finalizado(Cuando el pedido ya esta entregado);
   * C = Cancelado(El pedido fue cancelado por el usuario);
   * - - - - - - - - - - - - - - -
   *
   * @param int $id ID del pedido el cual se usara para alterar el estado del mismo .
   * @return JsonResponse Devuelve un JSON con una respuesta existosa
   * @throws Exception Se activara solo si ocurre un error durante la alteracion de los firentes estados.
   */

    public function modificarEstadoPedido($id)
    {
      try {

        $pedido = Pedido::findorFail($id);

        if ($pedido->estado_pedido == 'P') {
          $pedido->estado_pedido = 'E'; //En camino 
        } else if ($pedido->estado_pedido == 'E') {
          $pedido->estado_pedido = 'F'; //Finalizado
        } else if ($pedido->estado_pedido == 'F' || $pedido->estado_pedido == 'C') {
          return response()->json([
            'status' => 'error',
            'message' => 'No se puede modificar el estado, el pedido  ya esta Finalizado o Cancelado '
          ], 400);
        }
        $pedido->save();

        return response()->json(['status' => 'ok', 'message' => 'Se ha modificado el estado del pedido'], 200);
      } catch (\Throwable $th) {

        return response()->json(['status' => 'error', 'message' => 'A ocurrido un error inesperado '], 500);
      }
    }


  public function verHistorialVentas()
  {
    try {
      // Verifica que el usuario esté autenticado
      if (!Auth::check()) {
        return response()->json([
          'status' => 'error',
          'message' => 'Usuario no autenticado',
        ], 401);
      }
 

         //accede a la relaciones mediante la sesión activa 
         $usuario = Auth::user();
         $restauranteId = $usuario->restaurante->id;


        // Consulta los pedidos finalizados
        $pedidos = Pedido::with(['pedidoComidas.comidaRestaurante'])
            ->where('estado_pedido', 'F')
            ->whereHas('pedidoComidas.comidaRestaurante.categoria.restaurante', function ($query) use ($restauranteId) {
                $query->where('id', $restauranteId);
            })
            ->get();

      return response()->json([
        'status' => 'ok',
        'message' => 'Pedidos cargados Finalidos exitosamente',
        'data' => $pedidos,
      ], 200);
    } catch (\Throwable $th) {
      // Manejo de errores
      return response()->json([
        'status' => 'error',
        'message' => 'Error al cargar los pedidos',
        'error' => $th->getMessage(),
      ], 500);
    }
  }


 
}
