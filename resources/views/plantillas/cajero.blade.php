@extends('plantillas.dashboard')

@section('titulo')
Cajero
@stop

@section('menu')
@if(Auth::user()->estado_caja == 2)
<li class="">
  <a href="{{url('venta/create')}}">
    <i class="fa fa-money"></i><span class="link-title">&nbsp;Ventas</span>
  </a>
</li>
<li class="">
  <a href="{{(Auth::user()->estado_caja == 2) ? url('venta') : url('caja/create')}}">
    <i class="fa fa-exchange"></i><span class="link-title">&nbsp;Cambios</span>
  </a>
</li>
<li class="">
  <a href="{{(Auth::user()->estado_caja == 2) ? url('prestamo') : url('caja/create')}}">
    <i class="fa fa-refresh"></i><span class="link-title">&nbsp;Prestamos</span>
  </a>
</li>
<li class="">
  <a href="{{(Auth::user()->estado_caja == 2) ? url('credito') : url('caja/create')}}">
    <i class="fa fa-money"></i><span class="link-title">&nbsp;Creditos</span>
  </a>
</li>
<li class="">
  <a href="{{(Auth::user()->estado_caja == 2) ? url('separacion') : url('caja/create')}}">
    <i class="fa fa-money"></i><span class="link-title">&nbsp;Separaciones</span>
  </a>
</li>
<li class="">
  <a href="{{(Auth::user()->estado_caja == 2) ? url('traslado/create') : url('caja/create')}}">
    <i class="fa fa-shopping-bag"></i><span class="link-title">&nbsp;Traslados</span>
  </a>
</li>
<li class="">
  <a href="{{url('caja/create')}}">
    <i class="fa fa-home"></i><span class="link-title">&nbsp;Caja</span>
  </a>
</li>
@elseif(Auth::user()->estado_caja == 1)
<li class="">
  <a href="{{url('caja/create')}}">
    <i class="fa fa-home"></i><span class="link-title">&nbsp;Caja</span>
  </a>
</li>
@endif
@stop

@section('contenido')
<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
    @if(Session::has('correcto'))
      <div class="alert alert-success alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <strong>Correcto!</strong> {{Session::get('correcto')}}
      </div>
    @elseif(Session::has('info'))
      <div class="alert alert-info alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <strong>Correcto!</strong> {{Session::get('info')}}
      </div>
    @elseif(Session::has('error'))
      <div class="alert alert-danger alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <strong>Error!</strong> {{Session::get('error')}}
      </div>
    @endif
    @foreach($errors->all() as $mensaje)
    <div class="alert alert-info alert-dismissible" role="alert">
      <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <strong>Ups!</strong> {{$mensaje}}
    </div>
    @endforeach
  </div>
</div>
@stop
