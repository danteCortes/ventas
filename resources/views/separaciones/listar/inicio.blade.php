@extends('plantillas.cajero')

@section('estilos')
  {{Html::style('bootgrid/jquery.bootgrid.min.css')}}
@stop

@section('titulo')
  Lista de Separaciones
  @include('separaciones.menu')
@stop

@section('contenido')
  @include('plantillas.mensajes')
  <div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
      <div class="table-responsive">
        <table class="table table-condensed table-hover table-bordered" id="tblSeparaciones">
          <thead>
            <tr class="info">
              <th data-column-id="id" data-order="desc" style="text-align:center;">NÚMERO</th>
              <th data-column-id="cliente">CLIENTE</th>
              <th data-column-id="fecha_separacion">FECHA SEPARACION</th>
              <th data-column-id="total">TOTAL</th>
              <th data-column-id="commands" data-formatter="commands" data-sortable="false">OPERACIONES</th>
            </tr>
          </thead>
        </table>
      </div>
    </div>
  </div>
  @include('separaciones.listar.mostrar')
  @include('separaciones.listar.eliminar')
  @include('separaciones.listar.pagar')
@stop

@section('scripts')
  {{Html::script('bootgrid/jquery.bootgrid.min.js')}}
  @include('separaciones.listar.scripts')
@stop
