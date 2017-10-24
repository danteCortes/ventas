@extends('plantillas.dashboard')

@section('titulo')
Administrador
@stop

@section('menu')
<li class="">
  <a href="{{url('tienda')}}">
    <i class="fa fa-home"></i><span class="link-title">&nbsp;Tiendas</span>
  </a>
</li>
<li class="">
  <a href="{{url('usuario')}}">
    <i class="fa fa-users"></i><span class="link-title">&nbsp;Usuarios</span>
  </a>
</li>
<li class="">
  <a href="{{url('producto')}}">
    <i class="fa fa-shopping-bag"></i><span class="link-title">&nbsp;Productos</span>
  </a>
</li>
<li class="">
  <a href="{{url('compra')}}">
    <i class="fa fa-money"></i><span class="link-title">&nbsp;Compras</span>
  </a>
</li>
<li class="">
  <a href="{{url('proveedor')}}">
    <i class="fa fa-user-circle"></i><span class="link-title">&nbsp;Proveedores</span>
  </a>
</li>
<li class="">
  <a href="{{url('tarjeta')}}">
    <i class="fa fa-credit-card"></i><span class="link-title">&nbsp;Tarjetas</span>
  </a>
</li>
<li class="">
  <a href="{{url('descuento/listar-todos')}}">
    <i class="fa fa-tags"></i><span class="link-title">&nbsp;Descuentos</span>
  </a>
</li>
<li class="">
  <a href="#">
    <i class="fa fa-file"></i><span class="link-title">&nbsp;Reportes</span>
  </a>
</li>
@stop
