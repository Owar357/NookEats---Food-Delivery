<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\ComidaRestaurante;
use App\Models\Restaurante;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ComidaRestauranteController extends Controller
{

    /**
     * Permite listar las comidas del restaurante
     *
     * Este método devuelve todas las categorias  de un restaurante  junto con las comidas asociadas
     * a cada categoria.
     * 
     * 
     * @param  int $id del restaurante,se pasara atraves de la url
     * @return jsonResponse devuelve un JSON con la informacion de los  de las comidas registradas.
     * @throws Exception Si ocurre un error durante la consulta a la base de datos.
     * 
     * 
     */
    public function ListarComidas($id)
    {
        try {
            $comidas = Categoria::with(['comidasRestaurante'])->where('restaurante_id', $id)->get();

            if ($comidas->isEmpty()) {
                return response()->json(["message" => "El restaurante no existe o no tiene comidas registradas"], 404);
            }

            return response()->json(["status" => "ok", "data" => $comidas], 200);
        } catch (QueryException $q) {
            return response()->json(["status" => "error", "message" => "Ocurrio un error en el servidor: " . $q], 500);
        }
    }




    /**
     * Permite al restaurante agregar una nueva comida  
     * 
     * 
     * Este metodo permite Agregar una comida con los datos necesarios para que sea
     *  visible por el usuario y el restaurante.
     * 
     * 
     * @param  Request $request cuerpo de la peticion por la cual se pasaran los datos
     * @return jsonResponse devuelve un JSON con la informacion de los  de las comida que a sido registrada.
     * @throws QueryException Si ocurre un error durante la consulta a la base de datos.
     * 
     * 
     */
    public function AgregarComida(Request $request)
    {

        try {

            $comida  = new ComidaRestaurante();

            $comida->id = $request->id;
            $comida->nombre = $request->nombre;
            $comida->descripcion = $request->descripcion;
            $comida->precio = $request->precio;
            $comida->imagen = $request->imagen;
            $comida->categoria_id = $request->categoria_id;
            


            if ($comida->save()) {
                return response()->json(['status' => 'ok', 'data' => $comida], 201);
            } else {
                return response()->json(['status' => 'fail', 'message' => 'A ocurrido  un error'], 500);
            }
        } catch (\Throwable $th) {
        }
        return response()->json(['status' => 'error', 'message' => 'A ocurrido  un error  interno'. $th->getMessage()], 500);
    }




    /**
     * Permite al restaurante editar una comida existente  
     * 
     * 
     * Este metodo permite Realizar cambios en los datos de una comida , 
     * en caso de que se requiera actualizar o corregir la informacion.
     * 
     * @param  Request $request cuerpo de la peticion el cual contiene los datos actualizados
     * @param  int $id  de la comida que se va editar,se pasara atraves de la url
     * @return jsonResponse devuelve un JSON con la informacion de la comida que ha sido editada.
     * @throws QueryException Si ocurre un error durante la consulta a la base de datos.
     * 
     * 
     */

    public function  EditarComida(Request $request, $id)
    {

        try {

            $comida  =  ComidaRestaurante::findOrFail($id);

            $comida->nombre = $request->nombre;
            $comida->descripcion = $request->descripcion;
            $comida->precio = $request->precio;
            $comida->imagen = $request->imagen;
            $comida->disponibilidad = $request->disponibilidad;
            $comida->categoria_id = $request->categoria_id;

            if ($comida->save()) {
                return response()->json(['status' => 'ok', 'data' => $comida, 'message ' => 'comida Atualizada '], 200);
            } else {
                return response()->json(['status' => 'fail', 'message' => 'A ocurrido  un error'], 500);
            }
        } catch (\Throwable $th) {
        }
        return response()->json(['status' => 'error', 'message' => 'A ocurrido  un error interno'. $th->getMessage()], 500);
    }

    
    /**
 * Activa o desactiva la promoción de una comida específica.
 *
 * Este método busca una comida por su ID y, dependiendo del estado de
 * 'promocion_activa' que se recibe en la solicitud, activa o desactiva
 * la promoción. Si se activa, se calcula el precio con descuento y se
 * actualiza la comida en la base de datos. Si ocurre algún error, se
 * maneja y se devuelve un mensaje correspondiente.
 *
 * @param Request $request  El objeto de la solicitud que contiene losa dtos
 *                          para activar/desactivar la promoción.
 * @param int $id           El ID de la comida que se va a modificar/se mandaria por url.
 * @return jsonResponse     Devuelve un JSON con el estado de la operación
 *                          y un mensaje informativo.
 * @throws \Throwable       Si ocurre un error al buscar o guardar la comida,
 *                          se lanza una excepción.
 */
        public function ActivarDesactivarPromocion(Request $request, $id)
        {
            try {

                $comida = ComidaRestaurante::findOrFail($id);

                if ($request->promocion_activa ===  true ) {
                    $comida->promocion_activa  = true;
                    $comida->descuento = $request->descuento;
                    $descuentoAplicado = $request->descuento;
                    $preciOriginal = $comida->precio;
                    $precioDesc =  $this->aplicarDescuento($descuentoAplicado, $preciOriginal);


                    $comida->precioDescuento = $precioDesc;
                } else {
                    $comida->promocion_activa  = false;
                    $comida->fecha_promfinal = now();
                }



                if ($comida->save()) {
                    return response()->json(['status' => 'ok', 'message' => 'Se ha actualizado la promocion']);
                } else {
                    return response()->json(['status' => 'error', 'message' => 'Ocurrio un error al activar la promocion'], 500);
                }
            } catch (\Throwable $th) {
                return response()->json(['status' => 'fail', 'message' => 'Ocurrio un error interno'], 500);
            }
        }


        /**
 * Calcula el precio final después de aplicar un descuento.
 *
 * Este método toma un porcentaje de descuento y un precio original,
 * y calcula el precio final después de aplicar el descuento.
 *
 * @param float $descuento   El porcentaje de descuento a aplicar.
 * @param float $precio      El precio original antes de aplicar el descuento.
 * @return float             Devuelve el precio final después de aplicar el descuento.
 */
    public function aplicarDescuento($descuento, $precio)
    {
        $descuentoObtenido  =  $precio * ($descuento / 100);


        $precioFinal = $precio - $descuentoObtenido;

        return $precioFinal;
    }
}
