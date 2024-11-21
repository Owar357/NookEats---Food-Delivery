<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\ComidaRestaurante;
use App\Models\Restaurante;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
     * 
     * @return jsonResponse devuelve un JSON con la informacion de los  de las comidas registradas.
     * @throws Exception Si ocurre un error durante la consulta a la base de datos.
     * 
     * 
     */
    public function ListarComidas()
    {

        $sesionUsuario = Auth::user();

        $restauranteId = $sesionUsuario->restaurante->id;

        try {
            $comidas = Categoria::with(['comidasRestaurante'])->where('restaurante_id', $restauranteId)->get();

            if ($comidas->isEmpty()) {
                return response()->json(["message" => "El restaurante no existe o no tiene comidas registradas"], 404);
            }

            return response()->json(["status" => "ok", "data" => $comidas], 200);
        } catch (\Throwable $th) {
            return response()->json(["status" => "error", "message" => "Ocurrio un error en el servidor: "], 500);
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
            $request->validate([
                'nombre' => 'required|string|max:125',
                'descripcion' => 'required|string|max:300',
                'precio' => 'required|decimal:5,2',
                'categoria_id' => 'required|integer',
                'imagen' => 'required|image|mimes:jpg,jpeg,png|max:2048 '
            ]);
            $comida  = new ComidaRestaurante();

            $comida->nombre = $request->input('nombre');
            $comida->descripcion = $request->input('descripcion');
            $comida->precio = $request->input('precio');
            $comida->categoria_id = $request->input('categoria_id');

            if ($request->hasFile('imagen')) {

                $nombreOriginal = $request->file('imagen')->getClientOriginalName();
                $extension  =  $request->file('imagen')->getClientOriginalExtension();
                $timestamp = now()->format('Ymd_His');
                $nuevoNombre = 'img_' . pathinfo($nombreOriginal, PATHINFO_FILENAME) . '_' . $timestamp . '.' . $extension;
                $img = $request->file('imagen')->storeAs('public/img', $nuevoNombre);
                $comida->imagen = $nuevoNombre;
            } else {
                return response()->json(['status' => 'error', 'message' => 'error al guardar la imagen'], 500);
            }

            if ($comida->save()) {
                return response()->json(['status' => 'ok', 'message' => 'comida creada con exito', 'data' => $comida], 201);
            } else {
                return response()->json(['status' => 'fail', 'message' => 'A ocurrido  un error al intentar crear la comida'], 500);
            }
        } catch (\Throwable $th) {
            return response()->json(['status' => 'error', 'message' => 'A ocurrido  un error  interno'], 500);
        }
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
            $request->validate([
                'nombre' => 'required|string|max:125',
                'descripcion' => 'required|string|max:300',
                'precio' => 'required|decimal:5,2',
                'categoria_id' => 'required|integer',
                'disponibilidad' => 'required|boolean| ',
                'imagen' => 'required|image|mimes:jpg,jpeg,png|max:2048 '
            ]);


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
        return response()->json(['status' => 'error', 'message' => 'A ocurrido  un error interno' . $th->getMessage()], 500);
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

            if ($request->promocion_activa ===  true) {
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


    public function EliminarComida($id)
    {
        try {

            $comida = ComidaRestaurante::findOrFail($id);

            if ($comida->delete()) {

                return response()->json(['status' => 'ok', 'message' => 'Eliminado exitosamente'], 200);
            } else {
                return response()->json(['status' => 'fail', 'message' => 'Fallo al eliminar'], 500);
            }
        } catch (\Throwable $th) {
            return response()->json(['status' => 'error', 'message' => 'Ocurrio un error'], 500);
        }
    }

    public function getViewComidas()
    {
        return view('restadmin.comidas');
    }



   //------------------------Sección de Categorias----------------------

    //!PROBAR APIpostman
    public function CrearCategoria(Request $request)
    {
        try {
            $request->validate([
                'nombre' => 'required|string|max:50',
             ]);
        
            $categoria = new Categoria();

            if (!Auth::check()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Usuario no autenticado'
                ], 401);
            }

            $sesionUsuario = Auth::user();
            $restauranteId = $sesionUsuario->restaurante->id;


            $categoria->nombre = $request->nombre;
            $categoria->restaurante_id =  $restauranteId;

            if ($categoria->save()) {
                return response()->json(['status' => 'ok', 'message' => 'Se ha creado una nueva categoria', 'Categoria' => $categoria], 201);
            }
            // Respuesta si no se pudo guardar por una razón inesperada
            return response()->json([
                'status' => 'error',
                'message' => 'No se ha podido guardar la nueva categoría.'
            ], 500);
        } catch (\Throwable $th) {
            return response()->json(['status' => 'error', 'message' => 'Ocurrio un error al intentar crear la categoria'], 500);
        }
    }

    public function ListarCategorias()
    {
        try {
            if (!Auth::check()) {
                return response()->json(['status' => 'fail', 'message' => 'Error: Usuario no autenticado'], 401);
            }


            //Listar categoria junto a los productos dispnibles;
            $usuario = Auth::user();

            $restaurante = $usuario->restaurante;

            $categorias =  $restaurante->categorias->select('id', 'nombre', 'restaurante_id');


            return response()->json(['status' => 'ok', 'message' => 'Categorias listadas con exíto', 'data' => $categorias], 200);
        } catch (\Throwable $th) {
            return response()->json(['status' => 'error', 'error' => 'Ocurrio un error inesperado', 'message' => $th->getMessage()], 500);
        }
    }


    //!ProbarApiPostman
    public function EliminarCategoria($categoriaId)
    {
        try {

            if (!Auth::check()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Usuario no autenticado'
                ], 401);
            }

            $sesionusuario = Auth::user();
            $restauranteId =  '';

            //Agregar si delete on cascade para que al eliminar la categoria se valla con su atributos
            $categoria = Categoria::where('id', $categoriaId)
                ->where('restaurante_id', $restauranteId)->first();

            if (!$categoria) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Categoria no encontrado o no pertenece al restaurante autenticado'
                ], 404);
            }


            $categoria->delete();
            return response()->json([
                'status' => 'ok',
                'message' => 'Categoria eliminada con éxito'
            ], 200);
        } catch (\Throwable $th) {

            return response()->json(['status' => 'error', 'message' => 'Ocurrio un error inesperado'], 500);
        }
    }


    //!Probar ApiPostMan
    public function EditarCategoria(Request $request, $categoriaId)
    {
        try {

            $categoria = Categoria::findOrFail($categoriaId);

            $categoria->nombre = $request->nombre;

            if ($categoria->save()) {
                return response()->json(['status' => 'ok', 'message' => 'Categoria Actualizada'], 200);
            }
        } catch (\Throwable $th) {
            return response()->json(['status' => 'error', 'message' => 'Ocurrio un error inesperado'], 500);
        }
    }
}
