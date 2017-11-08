@extends('plantillas.administrador')

@section('estilos')
{{Html::style('bootgrid/jquery.bootgrid.min.css')}}
@stop

@section('titulo')
Kardex
@include('reportes.menu')
@stop

@section('contenido')
  @include('plantillas.mensajes')
  @include('reportes.kardex.frmKardex')
@stop

@section('scripts')
  {{Html::script('bootgrid/jquery.bootgrid.min.js')}}
  {{Html::script('assets/js/jquery.printarea.js')}}
  @include('reportes.scripts')
@stop
