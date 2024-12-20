@extends('adminlte::page')

@section('title', 'NookEats')

@section('content')
<div id="app">
  <editar-comidas-component></editar-comidas-component>
</div>
@stop

@section('js')
    @vite('resources/js/app.js')
@stop
