@extends('plantillas.cajero')

@section('contenido')
<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-6 col-md-offset-3 col-lg-6 col-lg-offset-3">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title">Cerrar Caja
        </h3>
      </div>
      <div class="panel-body">
        <p>ESTÁ A PUNTO DE CERRAR CAJA CON {{\App\Cierre::where('usuario_id', Auth::user()->id)->where('tienda_id', Auth::user()->tienda_id)
          ->where('estado', 1)->first()->total}}</p>
      </div>
      <div class="panel-footer">
        <button type="submit" class="btn btn-primary">Cerrar</button>
      </div>
    </div>
  </div>
</div>
@stop
