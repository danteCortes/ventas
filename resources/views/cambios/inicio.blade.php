@extends('plantillas.cajero')

@section('estilos')
{{Html::style('bootgrid/jquery.bootgrid.min.css')}}
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
            <th data-column-id="ticket" data-order="desc">Ticket</th>
            <th data-column-id="fecha">Fecha y Hora</th>
            <th data-column-id="total">Total</th>
            <th data-column-id="commands" data-formatter="commands" data-sortable="false">Operaciones</th>
          </tr>
        </thead>
      </table>
    </div>
  </div>
</div>
@include('cambios.modales')
@stop

@section('scripts')
{{Html::script('bootgrid/jquery.bootgrid.min.js')}}
{{Html::script('assets/js/jquery.printarea.js')}}
@include('cambios.scripts')
@stop
