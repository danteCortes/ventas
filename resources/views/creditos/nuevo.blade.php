@extends('plantillas.cajero')

@section('estilos')
{{Html::style('bootgrid/jquery.bootgrid.min.css')}}
@stop

@section('titulo')
Creditos
<a href="{{url('credito')}}" class="btn btn-primary">
  <span class="glyphicon glyphicon-plus"></span> Nuevo
</a>
<a href="{{url('listar-creditos')}}" class="btn btn-primary">
  <span class="glyphicon glyphicon-list"></span> Listar
</a>
<a href="{{url('listar-cobrar-creditos')}}" class="btn btn-primary">
  <span class="glyphicon glyphicon-list"></span> Por Cobrar
</a>
<a href="{{url('listar-pagados-creditos')}}" class="btn btn-primary">
  <span class="glyphicon glyphicon-list"></span> Pagados
</a>
@stop

@section('contenido')
@include('plantillas.mensajes')
<div class="row">
  @include('creditos.tblProductos')
  @include('creditos.frmAgregarProducto')
</div>
@include('creditos.tblDetalles')
@include('creditos.frmCredito')
@stop

@section('scripts')
{{Html::script('bootgrid/jquery.bootgrid.min.js')}}
@include('creditos.scripts')
@stop
