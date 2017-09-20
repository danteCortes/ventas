@extends('plantillas.cajero')

@section('contenido')
<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-6 col-md-offset-3 col-lg-6 col-lg-offset-3">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title">Abrir Caja
        </h3>
      </div>
      <div class="panel-body">
        {{Form::open(['url'=>'caja', 'class'=>'form-horizontal'])}}
          <div class="form-group">
            <label for="inicio" class="control-label col-xs-2 col-sm-2 col-md-3 col-lg-3">Inicio*: </label>
            <div class="col-xs-10 col-sm-10 col-md-9 col-lg-9">
              <input type="text" name="inicio" class="form-control input-sm" placeholder="INICIO" data-mask="##9.00" required>
            </div>
          </div>
          <div class="form-group">
            <div class="col-xs-10 col-xs-offset-2 col-sm-10 col-sm-offset-2 col-md-9 col-md-offset-3 col-lg-9 col-lg-offset-3">
              <button type="submit" class="btn btn-primary">Abrir Caja</button>
            </div>
          </div>
        {{Form::close()}}
      </div>
    </div>
  </div>
</div>
@stop
