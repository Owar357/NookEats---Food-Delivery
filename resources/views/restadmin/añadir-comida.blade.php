@extends('adminlte::page')

@section('title', 'NookEats')

@section('content')
<div id="app">
  <añadir-comidas-component></añadir-comidas-component>
</div>
@stop

@section('js')
    @vite('resources/js/app.js')
@stop
