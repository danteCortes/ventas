@extends('plantillas.cajero')

@section('titulo')
Editar Usuario
@stop

@section('contenido')
<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
    @if(Session::has('correcto'))
      <div class="alert alert-success alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <strong>Correcto!</strong> {{Session::get('correcto')}}
      </div>
    @elseif(Session::has('info'))
      <div class="alert alert-info alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <strong>Correcto!</strong> {{Session::get('info')}}
      </div>
    @elseif(Session::has('error'))
      <div class="alert alert-danger alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <strong>Error!</strong> {{Session::get('error')}}
      </div>
    @endif
    @foreach($errors->all() as $mensaje)
    <div class="alert alert-info alert-dismissible" role="alert">
      <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <strong>Ups!</strong> {{$mensaje}}
    </div>
    @endforeach
  </div>
</div>
<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
    {{Form::open(['url'=>'cambiar-foto', 'class'=>'form-horizontal', 'enctype'=>'multipart/form-data'])}}
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title">Cambiar Foto</h3>
      </div>
      <div class="panel-body">
        <div class="img-responsive">
          <img src="{{url('storage/usuarios/'.Auth::user()->foto)}}" class="img-responsive img-thumbnail" style="height:100px;">
        </div>
        <div class="form-group">
          <label class="control-label col-md-3">FOTO</label>
          <div class="col-xs-12 col-sm-12 col-md-9 col-lg-9">
            <input type="file" name="foto" class="form-control" required>
          </div>
        </div>
      </div>
      <div class="panel-footer">
        <button type="submit" class="btn btn-primary">Guardar</button>
      </div>
    </div>
    {{Form::close()}}
  </div>
  <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
    {{Form::open(['url'=>'cambiar-password', 'class'=>'form-horizontal'])}}
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title">Cambiar Contraseña</h3>
      </div>
      <div class="panel-body">
        <div class="form-group">
          <label class="control-label col-md-4">Password Actual</label>
          <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <input type="password" name="password" class="form-control" required>
          </div>
        </div>
        <div class="form-group">
          <label class="control-label col-md-4">Nuevo Password</label>
          <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <input type="password" name="password1" class="form-control" required>
          </div>
        </div>
        <div class="form-group">
          <label class="control-label col-md-4">Confirmar Password</label>
          <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <input type="password" name="password2" class="form-control" required>
          </div>
        </div>
      </div>
      <div class="panel-footer">
        <button type="submit" class="btn btn-primary">Guardar</button>
      </div>
    </div>
    {{Form::close()}}
  </div>
</div>
@stop
