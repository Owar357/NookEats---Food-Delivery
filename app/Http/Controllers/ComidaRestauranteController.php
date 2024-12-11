<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\ComidaRestaurante;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use phpDocumentor\Reflection\Types\Nullable;
use Throwable;

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
    public function listarComidas()
    {
        $sesionUsuario = Auth::user();
        $restauranteId = $sesionUsuario->restaurante->id;
        try {
            $comidas = Categoria::with(['comidasRestaurante'])
                ->where('restaurante_id', $restauranteId)
                ->get();

                


            $resultado = $comidas->map(function ($comidas) {
                return [
                    'categoria' => $comidas->nombre,
                    'comidas' => $comidas->comidasRestaurante->map(function ($comidas) {
                        return [
                            'id' => $comidas -> id,
                            'nombre' => $comidas->nombre,
                            'descripcion' => $comidas->descripcion,
                            'precio' => number_format($comidas->precio, 2),
                            'precioDescuento' => $comidas->precioDescuento,
                            'disponibilidad' => $comidas->disponibilidad , 
                            'categoriaid'=> $comidas -> categoria_id,
                            'imagenoriginal' => $comidas->imagen_original,
                            'imagen_hash' => $comidas->imagen_hash
                                ? asset('storage/img/comida/' . $comidas->imagen_hash)
                                : asset('images/default_comida.png'),
                        ];
                    }),
                ];
            });
            return response()->json(["data" => $resultado], 200);
        } catch (\Throwable $th) {
            return response()->json([
                "status" => "error",
                "message" => "Ocurrió un error en el servidor: " . $th->getMessage()
            ], 500);
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
    public function agregarComida(Request $request)
    {
        try {


            $comida  = new ComidaRestaurante();

            $data = json_decode($request->input('data'), true);

            $comida->nombre = $data['nombre'];
            $comida->descripcion = $data['descripcion'];
            $comida->precio = $data['precio'];
            $comida->categoria_id = $data['categoria'];

            if ($request->hasFile('imagen')) {

                $nombreOriginal = $request->file('imagen')->getClientOriginalName();
                $nombreHash = $request->file('imagen')->hashName();
                $request->file('imagen')->storeAs('img/comida', $nombreHash);
                $comida->imagen_original = $nombreOriginal;
                $comida->imagen_hash =  $nombreHash;
            } else {

                $comida->imagen_original = null;
                $comida->imagen_hash =  null;
            }
            if ($comida->save()) {
                return response()->json(['status' => 'ok', 'message' => 'comida creada con exito', 'data' => $comida], 201);
            } else {
                return response()->json(['status' => 'fail', 'message' => 'A ocurrido  un error al intentar crear la comida'], 500);
            }
        } catch (\Throwable $th) {
            return response()->json(['status' => 'error', 'message' => 'Ocurrió un error interno al actualizar la comida: ' . $th->getMessage()], 500);

        }
    }

    public function viewAñadirComida()
    {
        return  view('restadmin.añadir-comida');
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

     public function  editarComida(Request $request,$id )
     { 
        
         try {
             $comida  =  ComidaRestaurante::findOrFail($id);
 
             $data = json_decode($request->input('data'),true);
         
         
             $comida->nombre = $data['nombre']; 
             $comida->descripcion = $data['descripcion'];
             $comida->precio = $data['precio'];
             $comida->categoria_id = $data['categoria'] ;
 
                if ($request->hasFile('imagen')) {
    
                    if ($comida->imagen_hash) {
                        Storage::disk('public')->delete('img/comida/' . $comida->imagen_hash);
                    }
                    $nombreOriginal = $request->file('imagen')->getClientOriginalName();
                    $nombreHash = $request->file('imagen')->hashName();
                    $request->file('imagen')->storeAs('img/comida', $nombreHash);
                    $comida->imagen_original = $nombreOriginal;
                    $comida->imagen_hash = $nombreHash;
            } else {
                    $comida->imagen_hash = $request -> input("imagenhash");
                }
 
 
             if ($comida->save()) {
                 return response()->json(['status' => 'ok', 'data' => $comida, 'message' => 'comida Actualizada correctamente '], 200);
             } else {
                 return response()->json(['status' => 'fail', 'message' => 'Ha ocurrido  un error al actualizar la comida'], 500);
             }
         } catch (\Throwable $th) {
             return response()->json(['status' => 'error', 'message' => 'Ocurrio  un error interno: ' . $th->getMessage()], 500);
         }
     }


     public function estadoComida($id,Request $request){
       try {
           
         $comida = ComidaRestaurante::findOrFail($id);

        $comida -> disponibilidad =  $request -> disponibilidad;

         if($comida-> save()){

            return response()->json(['message' => 'estado Actualizado'],200);
         }
       } catch (\Throwable $th) {
        return response()->json(['message' => 'error inesperado'],500);
       }
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

    //!Arreglar logica de la funcion
    public function activarDesactivarPromocion(Request $request, $id)
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


    public function getViewComidas()
    {
        return view('restadmin.comidas');
    }



    //**------------------------Sección de Categorias----------------------

    //!PROBAR APIpostman
    public function crearCategoria(Request $request)
    {
        try {

            $categoria = new Categoria();

            if (Categoria::where('nombre', $request->nombre)->exists()) {
                return  response()->json(['message' => 'Ya existe una categoria registrada'], 409);
            }

            $sesionUsuario = Auth::user();
            $restauranteId = $sesionUsuario->restaurante->id;


            $categoria->nombre = $request->nombre;
            $categoria->restaurante_id =  $restauranteId;



            if ($categoria->save()) {
                return response()->json(['status' => 'ok', 'message' => 'Se ha creado una nueva categoria', 'categoria' => $categoria], 201);
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

    public function listarCategorias()
    {
        try {

            $usuario = Auth::user();


            $categorias = $usuario->restaurante->categorias()->select('id', 'nombre', 'restaurante_id')->get();


            return response()->json(['status' => 'ok', 'message' => 'Categorias listadas con exíto', 'categorias' => $categorias], 200);
        } catch (\Throwable $th) {
            return response()->json(['status' => 'error', 'error' => 'Ocurrio un error inesperado', 'message' => $th->getMessage()], 500);
        }
    }


  
    //!Probar ApiPostMan
    public function editarCategoria(Request $request, $categoriaId)
    {

        try {

            $request->validate([
                'nombre' => 'required|string|max:50',
            ]);


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
