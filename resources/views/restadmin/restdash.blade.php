 @extends('adminlte::page')

@section('title', 'Marcas')

@section('content_header')
    <h1>Ruta administrador de restaurante</h1>
@stop

@section('content')
    <div id="app">
        <editar-comidas-component></editar-comidas-component>
    </div> {{-- Contenedor para Vue --}}
@stop

@section('js')
@vite('resources/js/app.js')
@stop