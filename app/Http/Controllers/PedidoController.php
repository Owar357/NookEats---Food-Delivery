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


      if (!Auth::check()) {
        return response()->json([
            'status' => 'unauthorized',
            'message' => 'Usuario no autenticado. Por favor, inicie sesión para realizar un pedido.'
        ], 401); // 401 Unauthorized
    }

      
      DB::beginTransaction();

      $pedido = new Pedido();
      $pedido->fecha_hora_pedido = now();
      $pedido->numero_pedido = $this->numerodePedido();
      $pedido->metodo_pago = $request->metodoPago;  

      $pedido->usuario_id = Auth::user()->id;
      $pedido->total = $request->total;

      if (!$pedido->save()) {
        throw new  \Exception('Ocurrio un error al guardar el pedido en la base de datos');
      }
      
      foreach ($request->pedido_comida as $comida) {
        $pedidoComida = new PedidoComida();
        $pedidoComida->pedido_id = $pedido->id;
        $pedidoComida->cantidad = $comida['cantidad'];
        $pedidoComida -> nombre_comida = $comida['nombre'];
        $pedidoComida->nota = $comida['nota'];
        $pedidoComida->precio = $comida['precioOriginal'];
        $pedidoComida->precio_descuento = $comida['precioDescuento'];
        $pedidoComida->promocion_activa = $comida['promocionActiva'];
        $pedidoComida->precioCantidad = $comida['precioFinalCantidadComida'];

        if (!$pedidoComida->save()) {
          DB::rollBack();
          return response()->json(['status' => 'error', 'message' => 'Ocurrió un error al guardar un elemento del pedido'], 500);
        }
      }

      DB::commit();
      return response()->json(['status' => 'created', 'message' => 'El pedido se ha realizado con éxito'], 201);
    } catch (QueryException $q) {
      DB::rollBack();
      return response()->json([
        'status' => 'error',
        'message' => 'Ha ocurrido un error al realizar el pedido'
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
      $perfilVentas = Restaurante::with(['horarios', 'categorias.comidasRestaurante'])->where('id', $restaurante_id)
        ->get()
        ->map(function ($restaurante) {

          return [
            'imgPerfil' => $restaurante -> imagen_hash
            ?asset('storage/img/foto_perfil_restaurante/'. $restaurante->imagen_hash )
            :asset('storage/banner.png'),
            'imgBanner' => $restaurante->imagen_hash_banner
            ?asset('storage/img/foto_banner_rest/'. $restaurante->imagen_hash_banner )
            :asset('storage/banner.png'),
            'nombre' => $restaurante->nombre,
            'direccion' => $restaurante->ubicacion,
            'descripcion' => $restaurante -> descripcion,
            'telefono' => $restaurante -> telefono,
            'telefonoSecundario'=> $restaurante -> telefono_secundario,
            'horarios' => $restaurante->horarios->map(function ($horario) {
              return [
                'dia' => $horario->horario_dia,
                'entrada' => $horario->hora_apertura,
                'salida' => $horario->hora_cierre,
              ];
            }),
            'categorias' =>  $restaurante->categorias->map(function ($categoria) {
              return [
                'nombreCategoria' => $categoria->nombre,
                'comidas' => $categoria->comidasRestaurante->map(function ($comida) {

                  return [
                    'nombreComida' => $comida->nombre,
                    'precio' => $comida->precio,
                    'descripcionComida' => $comida->descripcion,
                    'precioDescuento' => $comida->precioDescuento,
                    'promocionActiva' => $comida->promocion_activa,
                    'estadoComida' => $comida->disponibilidad,
                    'imagenRuta' => $comida->imagen_hash
                    ? asset('storage/img/comida/'. $comida -> imagen_hash) 
                    : asset('imagen/default_comida.png'),
                    'imagen' => $comida->imagen_original,
                  ];
                })

              ];
            })

          ];
        });

      return response()->json(['status' => 'ok', 'message' => 'informacion del perfil restaurante recuperada con exito', 'perfil' => $perfilVentas], 200);
    } catch (\Throwable $th) {
      return response()->json(['status' => 'ok', 'message' => 'ocurrio un error inesperado'], 500);
    }
  }



  //**Lista de restaurante para la busqueda delos mismos */
  public function listaRestaurantes()
  {
    try {
      $listaRestaurantes = Restaurante::select('id', 'nombre', 'imagen_original', 'imagen_hash', 'descripcion')->get();


      $data  = $listaRestaurantes->map(function ($listaRestaurantes) {
        return [
          'id' => $listaRestaurantes->id,
          'nombre' => $listaRestaurantes->nombre,
          'imagen_hash' => $listaRestaurantes->imagen_hash
            ? asset('storage/img/foto_perfil_restuarante/' . $listaRestaurantes->imagen_hash)
            : asset('image/default_img_logo.png'),
          'imagen_original' => $listaRestaurantes->imagen_original,
          'descripcion' => $listaRestaurantes->descripcion
        ];
      });
      return response()->json(['restaurantes' =>  $data], 200);
    } catch (\Throwable $th) {
      return response()->json(['status' => 'error', 'message' => 'Ocurrio un error inesperado',], 500);
    }
  }


  public function  restaurantesRecientes()
  {

    try {
      $restaurantes = Restaurante::with(['tipoNegocio'])->latest()->take(6)->get();

      $response = $restaurantes->map(function ($restaurante) {



        return [
          'id' => $restaurante->id,
          'nombre' => $restaurante->nombre,

          'descripcion' => $restaurante->descripcion,
          'imagen' => $restaurante->imagen_hash
            ? asset('storage/img/foto_perfil_restaurante/' . $restaurante->imagen_hash)
            : asset('storage/imagen_default.avif'),
          'imagenOriginal' => $restaurante->imagen_original,
          'tipoNegocio' =>
          [
            'id' => $restaurante->tipoNegocio->id,
            'nombre' => $restaurante->tipoNegocio->nombre,
          ]
        ];
      });

      return response()->json(['status' => 'ok', 'restaurante' => $response], 200);
    } catch (\Throwable $th) {
      return response()->json(['status' => 'error', 'Message' => 'Ocurrio un error inesperado'], 500);
    }
  }
   
   public function  verPerfilRestauranteView($id){
     
     return view('user.perfil-venta-restaurante', compact('id'));

   }
}
