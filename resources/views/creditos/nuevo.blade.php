@extends('plantillas.cajero')

@section('estilos')
{{Html::style('bootgrid/jquery.bootgrid.min.css')}}
@stop

@section('contenido')
@include('plantillas.mensajes')
<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
    @include('creditos.tblProductos')
  </div>
  @include('creditos.frmAgregarProducto')
</div>
@include('creditos.tblDetalles')
@include('creditos.frmCredito')
@stop

@section('scripts')
{{Html::script('bootgrid/jquery.bootgrid.min.js')}}
@include('creditos.scripts')
@stop
