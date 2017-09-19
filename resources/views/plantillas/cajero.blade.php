@extends('plantillas.dashboard')

@section('titulo')
Cajero
@stop

@section('menu')
<li class="">
  <a href="{{(Auth::user()->estado_caja == 1) ? url('venta') : url('caja-cerrada')}}">
    <i class="fa fa-money"></i><span class="link-title">&nbsp;Ventas</span>
  </a>
</li>
<li class="">
  <a href="{{(Auth::user()->estado_caja == 1) ? url('cambio') : url('caja-cerrada')}}">
    <i class="fa fa-exchange"></i><span class="link-title">&nbsp;Cambios</span>
  </a>
</li>
<li class="">
  <a href="{{(Auth::user()->estado_caja == 1) ? url('prestamo') : url('caja-cerrada')}}">
    <i class="fa fa-refresh"></i><span class="link-title">&nbsp;Prestamos</span>
  </a>
</li>
<li class="">
  <a href="{{(Auth::user()->estado_caja == 1) ? url('credito') : url('caja-cerrada')}}">
    <i class="fa fa-money"></i><span class="link-title">&nbsp;Creditos</span>
  </a>
</li>
<li class="">
  <a href="{{(Auth::user()->estado_caja == 1) ? url('traslado') : url('caja-cerrada')}}">
    <i class="fa fa-shopping-bag"></i><span class="link-title">&nbsp;Traslados</span>
  </a>
</li>
<li class="">
  <a href="{{url('cierre')}}">
    <i class="fa fa-home"></i><span class="link-title">&nbsp;Caja</span>
  </a>
</li>
@stop
