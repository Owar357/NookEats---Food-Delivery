@extends('layouts.app')

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laravel</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@200;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Nunito', sans-serif;
            background-color: white;
            margin: 0;
            padding: 0;
        }

        .content {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* Estilo para el header */
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            background-color: white;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 10px; /* Espacio entre el logotipo y el texto */
        }

        .logo img {
            width: 40px; /* Ajusta el tamaño del logotipo */
            height: auto;
        }

        .logo span {
            font-size: 30px;   
            font-weight: bold;
            color: #007bff;
        }

        .auth-links {
            display: flex;
            gap: 20px;
        }

        .auth-links a {
            text-decoration: none;
            color: #333;
            font-weight: bold;
        }

        /* Estilo para el pie de página */
        footer {
            margin-top: auto;
            background-color: #f8f9fa;
            text-align: center;
            padding: 10px;
        }

        button {
    font-size: medium;
    font-weight: 900;
    padding: 10px 22px;
    border: 2px solid #007bff;
    border-radius: .25rem;
    background-color: #fff;     
}


        footer a {
            text-decoration: none;
            color: #333;
        }
    </style>
</head>

<div class="content">
        <header>
            <div class="logo">
                <!-- Asegúrate de tener la imagen del logotipo en la ruta correcta -->
                <img src="https://laravel.com/img/logomark.min.svg" alt="Laravel Logo">
                <span>NookEats</span>
            </div>
            <div class="auth-links">
                @if (Route::has('login'))
                   <button> <a href="{{ route('login') }}">Login</a></button>
                @endif

                @if (Route::has('register'))
                   <button>  <a href="{{ route('register') }}">Register</a> </button>
                @endif
            </div>
        </header>
       

        <div id="app">
            <home-component></home-component> 
        </div>
    </div> 
</body>

</html>
