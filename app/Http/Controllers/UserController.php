<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class UserController extends Controller
{
    public function actualizarPerfil(Request $request)
    {
        try {
            $sesionUsuario = Auth::user()->id;

            $user = User::findOrFail($sesionUsuario);

            $data = json_decode($request->input('data'), true);
            $user->name = $data['name'];

            if ($request->hasFile('imagen')) {

                $request->validate([
                    'imagen' => 'image|mimes:png,jpg,svg,webp|max:2048'
                ]);

                $nombreOriginal = $request->file('imagen')->getClientOriginalName();
                $nombreHash = $request->file('imagen')->hashName();
                $request->file('imagen')->storeAs('img/foto_perfil_usuario', $nombreHash);
                $user->imagen_original = $nombreOriginal;
                $user->imagen_hash = $nombreHash;
            } else {

                return response()->json(['status' => 'warning', ' message' => 'Ocurrio un error al intentar subir la imagen'], 200);
            }

            if ($user->save()) {
                return response()->json(['status' => 'ok', ' message' => 'Imagen Subida con éxito'], 200);
            }
        } catch (\Throwable $th) {
            return response()->json(['status' => 'error', ' message' => 'Ocurrio un error inesperado'], 500);
        }
    }


    public function mostrarImagenPefil()
    {
        try {
            $sesionUsuario =  Auth::user();

            $imagenUrl =  asset('img/foto_perfil_usuario/' . $sesionUsuario->imagen_hash);

            $nombreOriginal = $sesionUsuario->imagen_original;

            return response()->json(['message' => 'datos de imagen', 'nombreOriginal' => $nombreOriginal, 'imagenurl' => $imagenUrl], 200);
        } catch (\Throwable $th) {

            return response()->json(['status' => 'error', ' message' => 'Ocurrio un error inesperado'], 500);
        }
    }

    public function viewUserDashboard()
    {

        $user = Auth::user();


        $userData = [
            'nombre'  => $user->name,
            'imagen' => $user->imagen_hash 
            ? asset('storage/img/foto_perfil_usuario/' . $user->imagen_hash)
            : asset('storage/img/foto_perfil_usuario/default_img.jpg'),

        ];
        return view('welcome', compact('userData'));
    }

    public function viewHomePage()
    {
        return view('welcome');
    }
}
