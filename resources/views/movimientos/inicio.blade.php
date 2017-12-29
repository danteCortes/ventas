@extends('plantillas.cajero')

@section('estilos')
{{Html::style('bootgrid/jquery.bootgrid.min.css')}}
@stop

@section('titulo')
Movimientos
@include('movimientos.menu')
@stop

@section('contenido')
  @include('plantillas.mensajes')
  @include('movimientos.ingresos.frmNuevoIngreso')
  @include('movimientos.gastos.frmNuevoGasto')
@stop

@section('scripts')
  {{Html::script('bootgrid/jquery.bootgrid.min.js')}}
  {{Html::script('assets/js/jquery.printarea.js')}}
  <script type="text/javascript">
    $(document).ready(function() {
      $(".moneda").mask('#  ##0.00', {reverse: true});
    });
  </script>
@stop
