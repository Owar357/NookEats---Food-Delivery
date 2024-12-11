@extends('layouts.app')


@section('content')
<div id="app">
  <añadir-comidas-component></añadir-comidas-component>
</div>
@stop

@section('js')
    @vite('resources/js/app.js')
@stop
