@extends('layouts.app')

@section('title', 'Marcas')

@section('content')
<div id="app">
    <perfil-venta-restaurante-component></perfil-venta-restaurante-component>
</div>
   
@stop


@section('js')
    @vite('resources/js/app.js')
@stop