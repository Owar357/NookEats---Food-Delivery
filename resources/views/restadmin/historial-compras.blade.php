@extends('adminlte::page')

@section('title,Nookeats')

@section('content')
<div id='app'>
    <historial-compras-component></historial-compras-component>
</div>

@stop


@section('js')
@vite('resources/js/app.js')
@stop