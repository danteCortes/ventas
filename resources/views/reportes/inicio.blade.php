@extends('plantillas.administrador')

@section('estilos')
{{Html::style('bootgrid/jquery.bootgrid.min.css')}}
<style media="screen">
  .oculto{
    display: none;
  }
</style>
@stop

@section('titulo')
Reportes
@include('reportes.menu')
@stop

@section('contenido')
  @include('plantillas.mensajes')
  @include('reportes.kardex.frmKardex')
  @include('reportes.inventario.frmInventario')
  @include('reportes.ventas.frmVentas')
  @include('reportes.cierres.frmCierre')
  @include('reportes.kardex.ficha')
  @include('reportes.ventas.ventas')
  @include('reportes.inventario.inventario')
  <div class="row" id="lienzo_reporte">

  </div>
@stop

@section('scripts')
  {{Html::script('bootgrid/jquery.bootgrid.min.js')}}
  {{Html::script('assets/js/jquery.printarea.js')}}
  @include('reportes.scripts')
@stop
