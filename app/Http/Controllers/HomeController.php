<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    //Devuelve la vista de Usuario simple
    public function index()
    {
        return view('home');
    }

    //devuelve la vista de administrador
    public function dash()
    {
        return view('admin.dash');
    }

    //devuelve la vista de restaurante
    public function restDash()
    {
        return view('restadmin.restdash');
    }


    
}
