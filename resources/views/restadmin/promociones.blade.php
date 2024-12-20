@extends('adminlte::page')

@section('title', 'NookEats')

@section('content')
    <div id="app">
        <Promociones-component></Promociones-component>
    </div> 
@stop

@section('js')
@vite('resources/js/app.js')
@stop