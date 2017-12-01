@extends('plantillas.cajero')

@section('estilos')
{{Html::style('bootgrid/jquery.bootgrid.min.css')}}
@stop

@section('titulo')
Prestamos
<a href="{{url('prestamo')}}" class="btn btn-primary">
  <span class="glyphicon glyphicon-plus"></span> Nuevo
</a>
<a href="{{url('prestamo/listar')}}" class="btn btn-primary">
  <span class="glyphicon glyphicon-list"></span> Listar
</a>
<a href="{{url('prestamo/listar-devolver')}}" class="btn btn-primary">
  <span class="glyphicon glyphicon-list"></span> Devolver
</a>
<a href="{{url('prestamo/listar-pedir')}}" class="btn btn-primary">
  <span class="glyphicon glyphicon-list"></span> Recoger
</a>
@stop

@section('contenido')
@include('plantillas.mensajes')
<div class="row">
  @include('prestamos.tblProductos')
  @include('prestamos.nuevo.frmAgregarProducto')
</div>
@include('prestamos.nuevo.tblDetalles')
@include('prestamos.nuevo.frmPrestamo')
@stop

@section('scripts')
{{Html::script('bootgrid/jquery.bootgrid.min.js')}}
@include('prestamos.scripts')
@stop
