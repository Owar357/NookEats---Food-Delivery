<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(Request $request) { $credentials = $request->only('email', 'password'); 
        if (Auth::attempt($credentials)) { $user = Auth::user(); $token = $user->createToken('Personal Access Token')->accessToken; return response()->json(['token' => $token], 200); } else { return response()->json(['error' => 'Unauthorized'], 401); } }
}
