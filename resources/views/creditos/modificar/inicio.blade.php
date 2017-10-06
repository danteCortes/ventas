@extends('plantillas.cajero')

@section('estilos')
{{Html::style('bootgrid/jquery.bootgrid.min.css')}}
@stop

@section('titulo')
Modificar Credito
<a href="{{url('credito')}}" class="btn btn-primary">
  <span class="glyphicon glyphicon-plus"></span> Nuevo
</a>
<a href="{{url('listar-creditos')}}" class="btn btn-primary">
  <span class="glyphicon glyphicon-list"></span> Listar
</a>
@stop

@section('contenido')
@include('plantillas.mensajes')
<div class="row">
  @include('creditos.tblProductos')
  @include('creditos.modificar.frmAgregarProducto')
</div>
@include('creditos.modificar.tblDetalles')
@include('creditos.modificar.frmCredito')
@stop

@section('scripts')
{{Html::script('bootgrid/jquery.bootgrid.min.js')}}
@include('creditos.scripts')
@stop
