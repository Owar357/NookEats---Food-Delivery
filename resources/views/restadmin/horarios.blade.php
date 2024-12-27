@extends('adminlte::page')

@section('title','Nookeats')


@section('content')
<div id='app'>
    <horarios-component></horarios-component> 
</div> 
@stop


@section('js')
@vite('resources/js/app.js')
@stop
