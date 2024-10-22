<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\Rol;
use App\Models\Usuario;
use Exception;
use Illuminate\Database\Console\Migrations\RollbackCommand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UsuarioController extends Controller
{

/**
 * Genera un código único para cada perfil.
 *
 * Este método crea un código basado en la información del perfil,
 * asegurando que sea único en la base de datos.
 *
 * @param string $nombre Nombre del perfil.
 * @param int $id Identificador único del perfil.
 * @return string El código único generado.
 * @throws Exception Si el código no se puede generar.
 */
    public function generadorCodigoPerfil($nombre, $apellido)
    {
        $separarnombre = explode(' ', $nombre);
        $separarapellido = explode(' ', $apellido);

        $codigo = '';

        foreach ($separarnombre as  $parte) {
            $codigo .= strtoUpper(substr($parte, 0, 2));
        }

        foreach ($separarapellido as  $parte) {
            $codigo .= strtoupper(substr($parte, 0, 2));
        }


        $numeroaleatorio = rand(1000000, 9999999);

        return '#' . $codigo . $numeroaleatorio;
    }

    

/**
 * Registra a un usuario nuevo dentro de la aplicacion
 *
 * Este método crea un nuevo usuario, asegurando que el correo electrónico sea único
 * en la base de datos.
 * 
 * 
 * @param Request $request Cuerpo de la peticion tipo JSON que contiene los datos del usuario.
 * @return jsonResponse devuelve la data del registro.
 * @throws Exception Si ocurre un error durante el registro .
 * 
 * @todo Implementar validación por correo electrónio.
 */
    public function registroUsuario(Request $request)
    {
            try {

                DB::beginTransaction();

                $registroUsuario =  new Usuario();

                $registroUsuario->nombre  =  $request->nombre;
                $registroUsuario->apellido  =  $request->apellido;
                $registroUsuario->correo  =  $request->correo;
                $registroUsuario->contraseña  =  $request->contraseña;
                $registroUsuario->imagen_perfil_r  =  $request->imagen_perfil_r;
                $registroUsuario->fechaNac  =  $request->fechaNac;

                $registroUsuario->codigo_perfil  =  $this->GeneradorCodigoPerfil($request->nombre, $request->apellido);;
            
                if ($registroUsuario->save()) {
                
                $asignarol = new Rol();
                
                $asignarol -> nombre = 'U';
                $asignarol -> usuario_id = $registroUsuario -> id;

                  if(!$asignarol ->save())
                  {
                     throw new \Exception('No se puede asignar el rol al usuario');
                  } 
                  DB::commit();
                    return response()->json([
                        'status' => 'created',
                        'data' => $registroUsuario,
                        'message' => 'El usuario se a registrado exitosamente '
                    ], 201); 
                } else {
                    return response()->json([
                        'status' => 'fail',
                        'data' => $registroUsuario,
                        'message' => 'El usuario no se a podido registrar'
                    ], 500);
                }
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    'status' => 'error',
                    'message' => 'A ocurrido un error inesperado , por favor vuelve a intentar '
                ], 409);
            }
        }
}
