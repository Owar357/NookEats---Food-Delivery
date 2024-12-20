@extends('adminlte::page')

@section('title','NookEats')


@section('content')
<div id="app">
    <editar-categorias-component></editar-categorias-component>
</div>
@stop

@section('js')
    @vite('resources/js/app.js')
@stop
