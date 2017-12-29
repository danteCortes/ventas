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
  <div class="row">
    <div class="col-xs-12 col-sm-12 col-md-8 col-lg-8">
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title">Lista de Ingresos</h3>
        </div>
        <div class="panel-body">
          <div class="table-responsive">
            <table class="table table-hover table-condensed table-bordered" id="tblIngresos">
              <thead>
                <tr>
                  <th data-column-id="fecha" data-order="desc">Fecha</th>
                  <th data-column-id="total">Monto</th>
                  <th data-column-id="descripcion">Descripción</th>
                  <th data-column-id="commands" data-formatter="commands" data-sortable="false">Operaciones</th>
                </tr>
              </thead>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
  @include('movimientos.ingresos.frmNuevoIngreso')
  @include('movimientos.ingresos.frmEditarIngreso')
  @include('movimientos.ingresos.frmEliminarIngreso')
  @include('movimientos.gastos.frmNuevoGasto')
@stop

@section('scripts')
  {{Html::script('bootgrid/jquery.bootgrid.min.js')}}
  {{Html::script('assets/js/jquery.printarea.js')}}
  @include('movimientos.ingresos.scripts')
@stop
