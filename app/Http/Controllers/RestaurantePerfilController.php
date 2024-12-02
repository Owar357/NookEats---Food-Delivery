<?php

namespace App\Http\Controllers;

use App\Models\Horario;
use App\Models\Restaurante;
use App\Models\TipoNegocio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class RestaurantePerfilController extends Controller
{
  public function envioFormularioRegistro(Request $request)
  {
    try {
      $sesionUsuarioId = Auth::user()->id;

      if (Auth::user()->restaurante) {
        return response()->json(['message' => 'Ya existe un restaurante para este usuario.'], 400);
      }
      $restauranteRegistro = new Restaurante();

      $data = json_decode($request->input('data'), true);
      $restauranteRegistro->nombre = $data['nombre'];
      $restauranteRegistro->ubicacion = $data['ubicacion'];
      $restauranteRegistro->telefono = $data['telefono'];
      $restauranteRegistro->telefono_secundario = $data['telefonoSecundario'];
      $restauranteRegistro->descripcion = $data['descripcion'];
      $restauranteRegistro->modelo_negocio = $data['modeloNegocio'];
      $restauranteRegistro->tipo_negocio_id = $data['tipoNegocio'];
      $restauranteRegistro->user_id = $sesionUsuarioId;

      $restauranteRegistro->codigo_operacion = $this->generarCodigoOperacion($restauranteRegistro->modelo_negocio);

      if ($request->hasFile('imagen')) {
        $nombreOriginal = $request->file('imagen')->getClientOriginalName();
        $nombreHash = $request->file('imagen')->hashName();


        $request->file('imagen')->storeAs('img/foto_perfil_restuarante', $nombreHash);
        $restauranteRegistro->imagen_original = $nombreOriginal;
        $restauranteRegistro->imagen_hash = $nombreHash;
      } else {
        $restauranteRegistro->imagen_original = null;
        $restauranteRegistro->imagen_hash = null;
      }
      if ($restauranteRegistro->save()) {
        return response()->json(['status' => 'ok', 'message'  => 'Se ha enviado el formulario', 'data' => $restauranteRegistro], 200);
      }
    } catch (\Throwable $th) {
      return response()->json(['status' => 'fail', 'message'  => 'Ocurrio un error inesperado : ' . $th->getMessage()], 500);
    }
  }

  public function generarCodigoOperacion($modelo_negocio)
  {
    do {
      $timestamp = substr((string)time(), -5);

      $prefijo =  strtoupper(substr($modelo_negocio, 0, 1));

      $randomnumber = rand(1000, 99999);

      $codigoOperacion = $prefijo . $timestamp . $randomnumber;

      //Lanza una carga de busqueda al motor de la base de datos
      //busca por medio del index generado por unique();
      //y devuelve true o false
      $existe = Restaurante::Where('codigo_operacion', $codigoOperacion)->exists();
    } while ($existe);
    return $codigoOperacion;
  }
  
   
  public function viewFormulario(){
      return view ('user.formulario');
  }

  
  public function añadirActualizarImagen(Request $request)
  {
    try {
      $sesionUsuario = Auth::user();

      $restauranteId = $sesionUsuario->restaurante->id;

      $restaurante = Restaurante::findOrfail($restauranteId);

      if ($restaurante->imagen_hash) {
        Storage::delete('img/foto_perfil_restaurante/' . $restaurante->imagen_hash);
      }

      if ($request->hasFile('imagen_original')) {

        $nombreOriginal = $request->file('imagen_original')->getClientOriginalName();
        $imagenHash = $request->file('imagen_original')->hashName();
        $request->file('imagen_original')->storeAs('img/foto_perfil_restaurante', $imagenHash);
        $restaurante->imagen_original = $nombreOriginal;
        $restaurante->imagen_hash = $imagenHash;

        if ($restaurante->save()) {

          return response()->json(['message' => 'exito al guardar la imagen'], 200);
        }
      }
      return response()->json(['message' => 'No se recibio ninguna imagen'], 400);
    } catch (\Throwable $th) {

      return response()->json(['message' => 'Ocurrio un error ineperado'], 500);
    }
  }

  public function agregarHorario(Request $request)
  {
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

      $horarios->hora_apertura = $request['hora_apertura'];
      $horarios->hora_cierre = $request['hora_cierre'];
      $horarios->horario_dia = $request['horario_dia'];
      $horarios->restaurante_id = $restauranteId;

      if ($horarios->save()) {
        return response()->json(['status' => 'ok', 'message' => 'Horarios creados existosamente'], 201);
      }
    } catch (\Throwable $th) {
      return response()->json(['status' => 'error', 'message' => 'Ocurrio un error inesperado'], 500);
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
      $horario->hora_apertura = $request->hora_apertura;
      $horario->hora_cierre = $request->hora_cierre;
      $horario->horario_dia = $request->horario_dia;
      if ($horario->save()) {
        return response()->json(['status' => 'ok', 'message' => 'se ha editado el horario correctamente'], 200);
      }
    } catch (\Throwable $th) {

      return response()->json(['status' => 'error', 'message' => 'A ocurrido un error inesperado'], 500);
    }
  }

  public function eliminarHorario($horarioId)
  {
    try {
      $horario = Horario::FindOrFail($horarioId);

      if ($horario->delete()) {
        return response()->json(['status' => 'ok', 'message' => 'Se a eliminado el horario'], 200);
      }
    } catch (\Throwable $th) {
      return response()->json(['status' => 'error', 'message' => 'Ocurrio un error inesperado'], 500);
    }
  }

  public function listarHorarios()
  {
    try {
      $sesionUsuario = Auth::user();
      $horarios = $sesionUsuario->restaurante->horarios;

      return response()->json(['status' => 'ok', 'Horarios_de_trabajo' => $horarios,], 200);


      if ($horarios->empty) {
        return response()->json(['status' => 'ok', 'message' => 'No hay  horarios configurados'], 200);
      }
    } catch (\Throwable $th) {

      return response()->json(['status' => 'error', 'message' => 'Ocurrio un error interno'], 500);
    }
  }


  public function listarTipoNegocio()
  {
    try {
      $tipo_negocio = TipoNegocio::all();

      return response()->json(['status' => 'ok', 'message' => 'Lista de tipos de productos', 'tiponegocios' => $tipo_negocio], 200);
    } catch (\Throwable $th) {
      return response()->json(['status' => 'error', 'message' => 'Ocurrio un error inesperado'], 500);
    }
  }
}
