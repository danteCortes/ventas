@extends('plantillas.cajero')

@section('estilos')
{{Html::style('bootgrid/jquery.bootgrid.min.css')}}
@stop

@section('titulo')
Lista de Prestamos
<a href="{{url('prestamo')}}" class="btn btn-primary">
  <span class="glyphicon glyphicon-plus"></span> Nuevo
</a>
<a href="{{url('prestamo/listar')}}" class="btn btn-primary">
  <span class="glyphicon glyphicon-list"></span> Listar
</a>
<a href="{{url('prestamo/listar-devolver')}}" class="btn btn-primary">
  <span class="glyphicon glyphicon-list"></span> Devolver
</a>
<a href="{{url('prestamo/listar-pedir')}}" class="btn btn-primary">
  <span class="glyphicon glyphicon-list"></span> Recoger
</a>
@stop

@section('contenido')
  @include('plantillas.mensajes')
  <div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
      <div class="table-responsive">
        <table class="table table-condensed table-hover table-bordered" id="tblPrestamos">
          <thead>
            <tr class="info">
              <th data-column-id="id" data-order="desc" style="text-align:center;">NÚMERO</th>
              <th data-column-id="socio">SOCIO</th>
              <th data-column-id="direccion">DIRECCIÓN</th>
              <th data-column-id="fecha_prestamo">FECHA PRESTAMO</th>
              <th data-column-id="fecha_devolucion">FECHA DEVOLUCIÓN</th>
              <th data-column-id="commands" data-formatter="commands" data-sortable="false">OPERACIONES</th>
            </tr>
          </thead>
        </table>
      </div>
    </div>
  </div>
  @include('prestamos.modales')
@stop

@section('scripts')
  {{Html::script('bootgrid/jquery.bootgrid.min.js')}}
  @include('prestamos.scripts.listar')
@stop
