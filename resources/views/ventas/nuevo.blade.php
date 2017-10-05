@extends('plantillas.cajero')

@section('estilos')
{{Html::style('bootgrid/jquery.bootgrid.min.css')}}
@stop

@section('titulo')
Ventas
@stop

@section('contenido')
@include('plantillas.mensajes')
<div class="row">
  @include('ventas.frmBuscarProducto')
  @include('ventas.frmAgregarDetalle')
</div>
@include('ventas.tblDetalles')
@include('ventas.frmVenta')
@include('ventas.modales')
@stop

@section('scripts')
{{Html::script('bootgrid/jquery.bootgrid.min.js')}}
{{Html::script('assets/lib/mask/jquery.mask.js')}}
@include('ventas.scripts')
@stop
