@extends('plantillas.administrador')

@section('estilos')
{{Html::style('bootgrid/jquery.bootgrid.min.css')}}
@stop

@section('titulo')
Reportes
@include('reportes.menu')
@stop

@section('contenido')
  @include('plantillas.mensajes')
  @include('reportes.kardex.frmKardex')
  <div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
      <div class="table-responsive">
        <table class="table table-bordered table-condensed">
          <thead>
            <tr>
              <th colspan="2">Artículo:</th>
              <th colspan="3"></th>
              <th colspan="3">Línea:</th>
              <th colspan="3"></th>
            </tr>
            <tr>
              <th colspan="2">Familia:</th>
              <th colspan="3"></th>
              <th colspan="3">Marca:</th>
              <th colspan="3"></th>
            </tr>
            <tr>
              <th rowspan="2">Fecha</th>
              <th rowspan="2">Detalle</th>
              <th colspan="3">Ingresos</th>
              <th colspan="3">Salidas</th>
              <th colspan="3">Existencias</th>
            </tr>
            <tr>
              <th>Cantidad</th>
              <th>V. Unit.</th>
              <th>V. Total</th>
              <th>Cantidad</th>
              <th>V. Unit.</th>
              <th>V. Total</th>
              <th>Cantidad</th>
              <th>V. Unit.</th>
              <th>V. Total</th>
            </tr>
          </thead>
        </table>
      </div>
    </div>
  </div>
@stop

@section('scripts')
  {{Html::script('bootgrid/jquery.bootgrid.min.js')}}
  {{Html::script('assets/js/jquery.printarea.js')}}
  @include('reportes.scripts')
@stop
