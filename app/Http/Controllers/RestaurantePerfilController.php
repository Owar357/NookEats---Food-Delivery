<?php

namespace App\Http\Controllers;

use App\Models\Horario;
use App\Models\HorarioDia;
use App\Models\Restaurante;
use App\Models\TipoNegocio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RestaurantePerfilController extends Controller
{
    public function EnvioFormularioRegistro(Request $request)
    {
        try {
          $request->validate([
            'nombre' => 'required|string|max:150',
            'imagen' => 'required|image|mimes:jpg,jpeg,png|file|size:2048',
            'ubicacion' => 'required|string|max:300',
            'telefono' => 'required|string|max:15',
            'telefono_secundario' => 'nullable|string|max:15',
            'descripcion' => 'required|string',
            'modelo_negocio' => 'required|string|15',
            'tipo_negocio' => 'required|integer',
        ]);
            

        $sesionUsuarioId = Auth::user()->id;

        $restauranteRegistro = new Restaurante();
       
        $restauranteRegistro -> nombre = $request ->nombre;
        $restauranteRegistro -> imagen = $request ->imagen;
        $restauranteRegistro -> ubicacion = $request -> ubicacion;
        $restauranteRegistro ->telefono = $request ->telefono;
        $restauranteRegistro -> telefono_secundario = $request ->telefono_secundario;
        $restauranteRegistro -> descripcion = $request -> descripcion;
        $restauranteRegistro -> modelo_negocio = $request ->modelo_negocio;
        $restauranteRegistro -> tipo_negocio = $request ->tipo_negocio;
        $restauranteRegistro -> user_id = $sesionUsuarioId;

        $restauranteRegistro -> codigoOperacion =$this->generarCodigoOperacion($restauranteRegistro->modelo_negocio);



        if($restauranteRegistro->save())
        {
           return response()->json(['status' => 'ok', 'message'  => 'Se ha enviado el formulario', 'data' => $restauranteRegistro],200);   
        }
        

        } catch (\Throwable $th) {
            return response()->json(['status' => 'fail', 'message'  => 'Ocurrio un error inesperado'],500);   
        }
    }

    public function generarCodigoOperacion($modelo_negocio){
     
       do {
        $timestamp = substr((string)time(),-5);

        $prefijo =  strtoupper(substr($modelo_negocio,0,1));

        $randomnumber = rand(1000,99999);

        $codigoOperacion = $prefijo.$timestamp.$randomnumber;

     
        //Lanza una carga de busqueda al motor de la base de datos
        //busca por medio del index generado por unique();
        //y devuelve true o false
         $existe = Restaurante::Where('codigo_operacion', $codigoOperacion)->exits();
       } while ($existe);
       

       return $codigoOperacion;

    }

    

    public function agregarHorario(Request $request){
      try {
        $request->validate([
          //formato 24 hora por que eso acepta la bd;  ejemplo 3:45
          'hora_apertura' => 'required|date_format:H:i',
          'hora_cierra' => 'required|date_format:H:i|after_or_equal:hora_apertura',
          'ubicacion' => 'required|string|in:lunes,martes,miércoles,jueves,viernes,sábado,domingo',
        ]);

        $sesionUsuario = Auth::user();
        $restauranteId = $sesionUsuario->restaurante->id;

        $horarios = new Horario();

        $horarios -> hora_apertura = $request['hora_apertura'];
        $horarios -> hora_cierre = $request['hora_cierre'];
        $horarios-> horario_dia = $request['horario_dia'];
        $horarios -> restaurante_id= $restauranteId;
        
        if($horarios -> save())
        {
            return response()->json(['status'=> 'ok','message' => 'Horarios creados existosamente' ],201);
        }
      } catch (\Throwable $th) {
        return response()->json(['status'=> 'error','message' => 'Ocurrio un error inesperado' ],500);
      }
    }

    public function editarHorario(Request $request, $horarioId)
    {
      try {
        $horario = Horario::findOrFail($horarioId);

        $request->validate([
          //formato 24 hora por que eso acepta la bd;  ejemplo 3:45
          'hora_apertura' => 'required|date_format:H:i',
          'hora_cierra' => 'required|date_format:H:i|after_or_equal:hora_apertura',
          'ubicacion' => 'required|string|in:lunes,martes,miércoles,jueves,viernes,sábado,domingo',
        ]);
        
        $horario -> hora_apertura = $request->hora_apertura;
        $horario -> hora_cierre = $request->hora_cierre;
        $horario-> horario_dia = $request->horario_dia;


        if($horario->save())
        {
           return response()->json(['status'=>'ok','message' => 'se ha editado el horario correctamente' ],200);
        }

      } catch (\Throwable $th) {

        return response()->json(['status'=>'error','message' => 'A ocurrido un error inesperado' ],500);
      }

    }

 public function eliminarHorario($horarioId)
  {
    try {
      $horario = Horario::FindOrFail($horarioId);

      if($horario->delete())
      {
        return response()->json(['status' => 'ok' , 'message' => 'Se a eliminado el horario'],200);
      }

    } catch (\Throwable $th) {
      return response()->json(['status' => 'error' , 'message' => 'Ocurrio un error inesperado'],500);
    } 
  }



  public function ListarTipoNegocio()
  {
   try {
    $tipo_negocio = TipoNegocio::all();

    return response()->json(['status'=> 'ok' ,'message' =>'Lista de tipos de productos', 'data' => $tipo_negocio],200);
   } catch (\Throwable $th) {
    return response()->json(['status'=> 'error' ,'message' =>'Ocurrio un error inesperado'],500);
   }
  }
    
    
}
