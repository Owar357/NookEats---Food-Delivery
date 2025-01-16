@extends('layouts.app')

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laravel</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@200;600&display=swap" rel="stylesheet">
    <style>
        /* Estilos generales */
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

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            background-color: white;
            border-bottom: 2px solid #B013D8;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .logo img {
            width: 30px;
            height: auto;
        }

        .logo span {
            font-size: 25px;
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

    
        .button {
            font-size: medium;
            font-weight: 900;
            padding: 10px 22px;
            border: 2px solid #007bff;
            border-radius: .25rem;
            background-color: #fff;
        }

        .user-info{
            display: flex;
            align-items: center;
            gap:1px;
        }

        .img-perfil-users{
           width: 65px;
           height: 65px;
           border-radius: 10px;
           border: 3px solid #A3D8B7;
           border-radius: 50%;
           margin-right: 1rem;
        }

        .user-name{
            margin-top: 1rem;
            font-size: 16px;
            font-weight: bold;
        }
        
        .config-profile-btn{
            width: 3rem;
            height: 3rem;
            cursor: pointer;
        }

        .config-profile-btn .mdi{
            font-size: 30px;
        }

/* Estilo del menú */
.sub-menu-wrap {
    position: relative;
}

.sub-menu {
    display: none;  /* Inicialmente oculto */
    position: absolute;
    top: 100%;
    right: 0;
    background-color: #fff;
    border: 1px solid #ddd;
    border-radius: 8px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    width: 250px;
    z-index: 10;
}

.sub-menu-link {
    display: block;
    padding: 10px 15px;
    text-decoration: none;
    color: #333;
    transition: background-color 0.3s ease;
}

.sub-menu-link:hover {
    background-color: #f3f3f3;
}

/* Mostrar el menú cuando tenga la clase "open-menu" */
.sub-menu.open-menu {
    display: block;
}

      


    </style>
</head>

<div class="content">
    <header>
        <div class="logo">
            <img src="https://laravel.com/img/logomark.min.svg" alt="Laravel Logo">
            <span>NookEats</span>
        </div>
                                                                            
        <div >
            @if (Auth::check()) 
                @if (Auth::user()->rol == 'U') <!-- Verificar el rol del usuario -->
                    
                <div class="user-info">
                    <img class="img-perfil-users" src="{{ $userData['imagen']}}" alt="foto de perfil del usuario">

                    @if (isset($userData))
                    <p class="user-name">
                        {{ $userData['nombre'] }}
                        <!-- Botón para activar el menú -->
                        <button class="config-profile-btn" onclick="toggleMenu()">
                            <span class="mdi mdi-account-cog"></span>
                        </button>
                    </p>
                    
                    <!-- Menú desplegable -->
                    <div class="sub-menu-wrap">
                        <div class="sub-menu" id="subMenu">
                            <a href="#" class="sub-menu-link">
                                <p>Configuración del Perfil</p>
                            </a>
                            <a href="#" class="sub-menu-link">
                                <p>Ver Historial de Pedidos</p>
                            </a>
                            <a href="#" class="sub-menu-link" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <p>Cerrar Sesión</p>
                            </a>
                        </div>
                    </div>
                    


                    <!-- Formulario de logout -->
<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
    @csrf
</form>

                    <script>
                        function toggleMenu() {
                            // Obtener el contenedor del menú
                            let subMenu = document.getElementById("subMenu");
                    
                            // Alternar la clase "open-menu" para mostrar/ocultar el menú
                            subMenu.classList.toggle("open-menu");
                        }
                    </script>
                    

                
     
               @endif

                </div>
                
            
                @endif
            @else
                <!-- Si el usuario no está logueado, muestra los botones de login y register -->
                @if (Route::has('login'))
                    <button class="button"><a href="{{ route('login') }}">Login</a></button>
                @endif

                @if (Route::has('register'))
                    <button class="button"><a href="{{ route('register') }}">Register</a></button>
                @endif
            @endif
        </div>
    </header>

    <div id="app">
        <home-component></home-component>
    </div>
</div>

</html>
