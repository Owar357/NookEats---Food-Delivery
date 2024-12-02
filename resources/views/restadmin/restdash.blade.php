 @extends('adminlte::page')

@section('title', 'Marcas')

@section('content_header')
    <h1>Ruta administrador de restaurante</h1>
@stop

@section('content')
 <div id="app">
    <formulario-component> </formulario-component>
 </div>
@stop


@section('js')
    @vite('resources/js/app.js')
@stop