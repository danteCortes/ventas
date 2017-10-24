@extends('plantillas.administrador')

@section('estilos')
{{Html::style('bootgrid/jquery.bootgrid.min.css')}}
@stop

@section('titulo')
Descuentos Vigentes
@include('descuentos.menu')
@include('descuentos.modales.nuevo')
@stop

@section('contenido')
  @include('plantillas.mensajes')
  <div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
      <div class="table-responsive">
        <table class="table table-condensed table-hover table-bordered" id="tblDescuentos">
          <thead>
            <tr class="info">
              <th data-column-id="id" data-order="desc" style="text-align:center;">NÚMERO</th>
              <th data-column-id="tienda">TIENDA</th>
              <th data-column-id="conceptos">CONCEPTOS</th>
              <th data-column-id="porcentaje">PORCENTAJE</th>
              <th data-column-id="inicio">INICIO</th>
              <th data-column-id="final">FINAL</th>
              <th data-column-id="commands" data-formatter="commands" data-sortable="false">OPERACIONES</th>
            </tr>
          </thead>
        </table>
      </div>
    </div>
  </div>
  @include('descuentos.modales.editar')
  @include('descuentos.modales.eliminar')
@stop

@section('scripts')
  {{Html::script('bootgrid/jquery.bootgrid.min.js')}}
  @include('descuentos.listarVigentes.scripts')
@stop
