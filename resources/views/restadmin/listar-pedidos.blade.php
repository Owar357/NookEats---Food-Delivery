@extends('adminlte::page')

@section('title','NooKEats')

@section('content')
  <div id="app">
     <listar-pedidos-component></listar-pedidos-component> 
  </div>
@stop


@section('js')
@vite('resources/js/app.js')
@stop