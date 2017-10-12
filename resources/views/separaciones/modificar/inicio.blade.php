@extends('plantillas.cajero')

@section('estilos')
{{Html::style('bootgrid/jquery.bootgrid.min.css')}}
@stop

@section('titulo')
Modificar Separacion
@include('separaciones.menu')
@stop

@section('contenido')
@include('plantillas.mensajes')
<div class="row">
  @include('separaciones.nuevo.tblProductos')
  @include('separaciones.modificar.frmAgregarProducto')
</div>
@include('separaciones.modificar.tblDetalles')
@include('separaciones.modificar.frmSeparacion')
@stop

@section('scripts')
{{Html::script('bootgrid/jquery.bootgrid.min.js')}}
@include('separaciones.nuevo.scripts')
@stop
