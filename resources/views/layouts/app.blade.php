    <!doctype html>
    <html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="dns-prefetch" href="//fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    </head>
    <body>
        <div id="app">
            <main class="py-4">
                @yield('content')
            </main>
        </div>



        <footer class="bg-dark text-white text-center py-3">
            <div class="container">
                <p class="mb-0">&copy; {{date('Y')}} Nookeats. Todos los derechos Reservados </p>
                <p class="mb-0">
                    <a href="/politica-privacidad" class="text-light">Política de Privacidad</a> |
                    <a href="/terminos-condiciones" class="text-light">Términos y Condiciones</a>
                </p>
                <p class="mt-2">
                    <a href="https://www.facebook.com" target="_blank" class="text-light me-2">
                        <i class="fab fa-facebook"></i>
                    </a>
                    <a href="https://www.twitter.com" target="_blank" class="text-light me-2">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="https://www.instagram.com" target="_blank" class="text-light">
                        <i class="fab fa-instagram"></i>
                    </a>
                </p>    
            </div>
        </footer>
    </body>
    </html>
