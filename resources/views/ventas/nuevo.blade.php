@extends('plantillas.cajero')

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
{{Html::script('assets/lib/mask/jquery.mask.js')}}
@include('ventas.scripts')
@stop
