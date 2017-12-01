@extends('plantillas.cajero')

@section('estilos')
{{Html::style('bootgrid/jquery.bootgrid.min.css')}}
@stop

@section('titulo')
Modificar Préstamo {{$prestamo->id}}
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
  @include('prestamos.modificar.frmAgregarProducto')
</div>
@include('prestamos.modificar.tblDetalles')
@include('prestamos.modificar.frmPrestamo')
@stop

@section('scripts')
{{Html::script('bootgrid/jquery.bootgrid.min.js')}}
@include('prestamos.scripts')
@stop
