@extends('plantillas.administrador')

@section('estilos')
<style media="screen">
  .modal-body>.table-responsive>.table-hover tr:hover{
    background-color: #e69c2d;
  }
</style>
@stop

@section('titulo')
Compras
<a href="{{url('compra/create')}}" class="btn btn-primary">
  <span class="glyphicon glyphicon-plus"></span> Nuevo
</a>
<a href="{{url('compra')}}" class="btn btn-primary">
  <span class="glyphicon glyphicon-list"></span> Listar
</a>
@stop

@section('contenido')
@include('plantillas.mensajes')
<div class="row">
  @include('compras.frmProducto')
  @include('compras.frmCompra')
</div>
@include('compras.tblDetalles')
@stop

@section('scripts')
@include('compras.scripts')
@stop
