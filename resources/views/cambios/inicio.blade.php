@extends('plantillas.cajero')

@section('estilos')
  {{Html::style('bootgrid/jquery.bootgrid.min.css')}}
  <style>
    .text {
      text-align: center;
    }
  </style>
@stop

@section('titulo')
Cambiar Venta
@stop

@section('contenido')
  @include('plantillas.mensajes')
  <div class="row">
    <div class="col-xs-12 col-sm-12 col-md-10 col-md-offset-1 col-lg-10 col-lg-offset-1">
      <div class="table-responsive">
        <table class="table table-hover table-condensed table-bordered" id="tblVentas">
          <thead>
            <tr class="info">
              <th data-column-id="ticket">SERIE Y NUMERO</th>
              <th data-column-id="fecha" data-order="desc">FECHA Y HORA</th>
              <th data-column-id="total" data-align="right">TOTAL</th>
              <th data-column-id="estado">ESTADO</th>
              <th data-column-id="commands" data-align="center" data-formatter="commands" data-sortable="false">OPERACIONES</th>
            </tr>
          </thead>
        </table>
      </div>
    </div>
  </div>
  @include('cambios.modales')
  @include('cambios.modals.mdlAnularVenta')
@stop

@section('scripts')
  {{Html::script('bootgrid/jquery.bootgrid.min.js')}}
  {{Html::script('assets/js/jquery.printarea.js')}}
  {{Html::script('sistema/cambios/inicio.js')}}
@stop
