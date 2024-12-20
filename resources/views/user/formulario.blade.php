@extends('adminlte::page')


@section('content')
<div id="app">
    <formulario-component></formulario-component>
</div>
@stop

@section('js')
    @vite('resources/js/app.js')
@stop
