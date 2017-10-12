@extends('plantillas.cajero')

@section('estilos')
{{Html::style('bootgrid/jquery.bootgrid.min.css')}}
@stop

@section('titulo')
Nueva Separación
@include('separaciones.menu')
@stop

@section('contenido')
@include('plantillas.mensajes')
<div class="row">
  @include('separaciones.nuevo.tblProductos')
	@include('separaciones.nuevo.frmAgregarDetalle')
</div>
@include('separaciones.nuevo.tblDetalles')
@include('separaciones.nuevo.frmSeparacion')
@stop

@section('scripts')
  {{Html::script('bootgrid/jquery.bootgrid.min.js')}}
	{{Html::script('assets/lib/mask/jquery.mask.js')}}
	@include('separaciones.nuevo.scripts')
@stop
