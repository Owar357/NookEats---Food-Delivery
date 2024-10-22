<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\PedidoComida;
use App\Models\Repartidor;
use App\Models\Usuario;
use App\Models\UsuarioPedido;
use Illuminate\Database\Console\Migrations\RollbackCommand;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpParser\Builder\Function_;

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
 * @param  int  $id_usuario ID del usuario que realiza el pedido. 
 * @return jsonResponse devuelve la informacion del registro del pedido.
 * @throws Exception Si ocurre un error durante el registro .
 * 
 * 
 */
  public function  RealizarPedido(Request $request, $id_usuario)
  {
    try {

      DB::beginTransaction();

      //* se crea el pedido 
      $pedido = new Pedido();
      $pedido->fecha_hora_pedido = now();
      $pedido->total = $request->total;
      $pedido->estado_pedido = $request->estado_pedido;
      $pedido->metodo_pago = $request->metodo_pago;
      $pedido->hora_entrega = $request->hora_entrega;
      $pedido->foto_entrega = $request->foto_entrega;
      $pedido->comida_restaurante_id = $request->comida_restaurante_id;
      $pedido->repartidor_id = null;

      if ($pedido->save()) {
        $usuario_pedido = new UsuarioPedido();

        $usuario_pedido->usuario_id = $id_usuario;
        $usuario_pedido->pedido_id  = $pedido->id;

        if ($usuario_pedido->save()) {

          foreach ($request->pedido_comida as  $comida) {
            $pedido_comida = new PedidoComida();
            $pedido_comida->cantidad = $comida['cantidad'];
            $pedido_comida->nota = $comida['nota'];
            $pedido_comida->pedido_id = $pedido->id;
            $pedido_comida->comida_restaurante_id = $comida['comida_restaurante_id'];

            if (!$pedido_comida->save()) {
              DB::rollBack();
              return response()->json(['status' => 'error', 'message' => 'Ocurrio un error al agregar los datos al pedido'], 500);
            }
          }

          DB::commit();
          return response()->json(['status' => 'created', 'message' => 'El pedido se ha realizado con exito'], 201);
        } else {
          DB::rollBack();
          return response()->json(['status' => 'error', 'message' => 'Ocurrio un error al tratar de realizar el pedido con el usuario'], 500);
        }
      } else {
        DB::rollBack();
        return response()->json(['status' => 'error', 'message' => 'No se pudo realizar el pedido'], 500);
      }
    } catch (QueryException $q) {
      DB::rollBack();
      return response()->json([
        'status' => 'error',
        'message' => 'Ha ocurrido un error al realizar el pedido. Por favor, intente nuevamente, si el problema persiste, Reportelo'
      ], 500);
    }
  }


 /**
 * Permite aceptar uno o más pedidos de los usuarios.
 *
 * Este método permite aceptar todos aquellos pedidos que estén en estado 'P' (pendiente).
 * Luego asocia el repartidor al pedido y cambia su estado de pendiente a 'A' (aceptado).
 * En caso de algún error, se realizará un rollback para deshacer los cambios
 *  
 * 
 * @param  int  $pedido_id ID del pedido del  cual el repartidor se va asociar. 
 * @param  int  $repartidor_id ID del repartdor que sera asociado al  pedido  . 
 * @return jsonResponse devuelve un mesaje de exito al aceptar el pedido,
 * @throws Exception Si ocurre un error durante el registro .
 * 
 * 
 */
  public function AceptarPedidoRepartidor(Request $request, $pedido_id, $repartidor_id)
  {
    try {
      DB::beginTransaction();

      $pedido = Pedido::findOrFail($pedido_id);

      if ($pedido->estado_pedido !== 'P') {
        return response()->json([
          'status' => 'fail',
          'message' => 'El pedido no está disponible para ser aceptado'
        ], 400);
      }
      $pedido->repartidor_id = $repartidor_id;
      $pedido->estado_pedido = 'A';

      if ($pedido->save()) {
        DB::commit();
        return response()->json([
          'status' => 'ok',
          'message' => 'Pedido aceptado con éxito '
        ], 200);
      } else {
        DB::rollBack();
        return response()->json([
          'status' => 'fail',
          'message' => 'Ocurrio un error al aceptar el pedido'
        ], 500);
      }
    } catch (\Throwable $th) {
      DB::rollBack();
      return response()->json([
        'status' => 'error',
        'message' => 'Ocurrió un error, intente de nuevo'
      ], 500);
    }
  }


 /**
 * Muestra todos los pedidos que aún no han sido aceptados por los repartidores.
 *
 * Este método recupera todos los pedidos que están en estado 'P' (pendiente) 
 * y los carga junto con la información del usuario que realizó el pedido 
 * y los detalles de los alimentos asociados a cada pedido.
 *
 * @return JsonResponse Devuelve un JSON con el estado de la operación y los datos de los pedidos.
 * @throws Exception Si ocurre un error al recuperar los pedidos.
 */
  public function verAceptarPedido()
  {
    try {
      $pedido = Pedido::with(['usuarioPedidos.usuario', 'pedidoComidas'])->where('estado', 'P')->get();

      return response()->json(['status' => 'ok', 'data' => $pedido,], 200);
    } catch (\Exception $e) {
      return response()->json(['status' => 'fail', 'message' => 'Ocurrio un erro al mostrar los datos',], 500);
    }
  }


 /**
 * Muestra todos los pedidos que ha aceptado un repartidor específico.
 *
 * Este método recupera los pedidos asociados al repartidor identificado por 
 * su ID. Incluye la información del usuario que realizó cada pedido 
 * y los detalles de los alimentos asociados a cada pedido.
 *
 * @param int $repartidor_id ID del repartidor cuyas pedidos se desean consultar.
 * @return JsonResponse Devuelve un JSON con el estado de la operación y los datos de los pedidos aceptados.
 * @throws Exception Si ocurre un error al recuperar los pedidos.
 */
  public function verPedidosAceptados($repartidor_id)
  {
    try {
      $pedidos = Pedido::with(['usuarioPedidos.usuario', 'pedidoComidas'])->where('repartidor_id', $repartidor_id)->get();


      if ($pedidos->isEmpty()) {
        return  response()->json(['status' => 'ok', 'message' => 'No hay pendientes'], 204);
      }

      return response()->json(['status' => 'ok', 'data' => $pedidos,], 200);
    } catch (\Exception $e) {
      return response()->json(['status' => 'fail', 'message' => 'Ocurrió un error al mostrar los datos. Por favor, pónganse en contacto con soporte.',], 500);
    }
  }


/**
 * Muestra todas las órdenes de un pedido que pertenecen a un restaurante específico.
 *
 * Este método recupera los pedidos que están en estado 'A' (Aceptado) 
 * y las comidas que están asociadas al restaurante identificado por su código de operación.
 * 
 * ! Requiere cambios en su funcionalidad: debe agregarse un ID adicional 
 * ! Requiere agregar la columna ID a la base de datos para mejorar la integridad.
 *
 * @param string $codigo_operacion Código de operación del restaurante.
 * @return JsonResponse Devuelve un JSON con el estado de la operación y los datos de los pedidos.
 * @throws Exception Si ocurre un error al recuperar los pedidos.
 */

  
  public function  verPedidosRestaurante($codigo_operacion)
  {
    try {
      $pedidos =  Pedido::where('estado', 'A')->whereHas([
        'pedidoComidas.comidaRestaurante',
        function ($query) use ($codigo_operacion) {
          $query->where('codigo_operacion', $codigo_operacion);
        }
      ])
        ->with(['usuarioPedidos.usuario:id,nombre,apellido'])->withCount('pedidoComidas')->get();

      if ($pedidos->isEmpty()) {
        return response()->json([['status' => 'ok', 'message' => 'No hay pedidos para este restaurante'], 200]);
      }


      return response()->json(['status' => 'ok', 'data' => $pedidos], 200);
    } catch (\Throwable $th) {
      return response()->json(['status' => 'fail', 'message' => 'Ocurrió un error al mostrar los pedidos. Por favor, pónganse en contacto con soporte.'], 500);
    }
  }


 /**
 * Muestra los detalles específicos de un pedido para un restaurante.
 *
 * Este método recupera un pedido específico junto con sus detalles asociados, 
 * incluyendo los elementos del pedido relacionados con el restaurante.
 *
 * @param int $pedido_id ID del pedido que se desea visualizar.
 * @return JsonResponse Devuelve un JSON con el estado de la operación y los detalles del pedido.
 * @throws ModelNotFoundException Si no se encuentra el pedido con el ID proporcionado.
 * @throws Exception Si ocurre un error durante la recuperación de los detalles.
 */
  public function verDetallePedido($pedido_id){
    try {
      $verPedidos = Pedido::with(['pedidoComidas.ComidaRestaurante'])->findOrFail($pedido_id);

      return response()-> json(['status' => 'ok', 'data' => $verPedidos ],200);
    } catch(ModelNotFoundException $e ){
      return response()-> json(['status' => 'error',  'message' => 'No exiten detalles del pedido,Vuelva a intentar o  Por favor pongase en contacto con soporte' ],404);
    } 
    catch (\Throwable $th) {
      return response()-> json(['status' => 'fail',  'message' => 'Ocurrrio un error  al mostrar los pedidos. Por favor intente de nuevo  o Contacte a soporte' ],500);
    }
  }
}
