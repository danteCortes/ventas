@extends('plantillas.administrador')

@section('estilos')
<style media="screen">
  .modal-body>.table-responsive>.table-hover tr:hover{
    background-color: #e69c2d;
  }
</style>
@stop

@section('titulo')
Tiendas
<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#nuevo">
  <span class="glyphicon glyphicon-plus"></span> Nuevo
</button>
<div class="modal fade" id="nuevo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      {{Form::open(['url'=>'tienda'])}}
      {{ csrf_field() }}
      <div class="modal-header" style="background-color:#385a94; color:#fff;">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">NUEVA TIENDA</h4>
      </div>
      <div class="modal-body" style="background-color:#e69c2d">
        <div class="panel" style="background-color:#bd7406">
          <div class="panel-body">
            <div class="form-group">
              <input type="text" data-mask="99999999999" class="form-control ruc input-sm" placeholder="RUC*" required name="ruc"
              id="ruc">
            </div>
            <div class="form-group">
              <input type="text" class="form-control mayuscula input-sm" placeholder="NOMBRE*" required name="nombre" id="nombre">
            </div>
            <div class="form-group">
              <input type="text" class="form-control mayuscula input-sm" placeholder="SERIE*" required name="serie" id="serie">
            </div>
            <div class="form-group">
              <input type="text" class="form-control mayuscula input-sm" placeholder="TICKETERA*" required name="ticketera"
              id="ticketera">
            </div>
            <div class="form-group">
              <input type="text" class="form-control mayuscula input-sm" placeholder="DIRECCIÓN" name="direccion" id="direccion">
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer" style="background-color:#385a94">
        <button type="button" class="btn btn-default" data-dismiss="modal">
          <span class="glyphicon glyphicon-ban-circle"></span> Cancelar</button>
        <button type="submit" class="btn btn-primary"><span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
      </div>
      {{Form::close()}}
    </div>
  </div>
</div>
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
  <div class="col-xs-12 col-sm-12 col-md-8 col-md-offset-2 col-lg-6 col-lg-offset-3">
    <div class="table-responsive">
      <table class="table table-condensed table-hover table-bordered">
        <thead>
          <tr style="background-color:#385a94; color:#FFF;">
            <th>RUC</th>
            <th>TIENDA</th>
            <th>OPERACIONES</th>
          </tr>
        </thead>
        <tbody>
          @foreach(App\Tienda::all() as $tienda)
            <tr>
              <td>{{$tienda->ruc}}</td>
              <td>{{$tienda->nombre}}</td>
              <td>
                <button type="button" class="btn btn-info btn-xs" data-toggle="modal" data-target="#ver{{$tienda->id}}">
                  <span class="fa fa-eye"></span>
                </button>
                <div class="modal fade" id="ver{{$tienda->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                  <div class="modal-dialog" role="document">
                    <div class="modal-content">
                      <div class="modal-header" style="background-color:#31b0d5; color:#fff;">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">DATOS DE LA TIENDA</h4>
                      </div>
                      <div class="modal-body" style="background-color:#e69c2d">
                        <div class="table-responsive" style="background-color:#bd7406">
                          <table class="table table-condensed table-bordered table-hover" style="margin:0px; background-color:#bd7406; color:#ff;">
                            <tr>
                              <th>RUC: </th>
                              <td>{{$tienda->ruc}}</td>
                            </tr>
                            <tr>
                              <th>Nombre: </th>
                              <td>{{$tienda->nombre}}</td>
                            </tr>
                            <tr>
                              <th>Dirección: </th>
                              <td>{{$tienda->direccion}}</td>
                            </tr>
                            <tr>
                              <th>Serie: </th>
                              <td>{{$tienda->serie}}</td>
                            </tr>
                            <tr>
                              <th>Ticketera: </th>
                              <td>{{$tienda->ticketera}}</td>
                            </tr>
                          </table>
                        </div>
                      </div>
                      <div class="modal-footer" style="background-color:#31b0d5">
                        <button type="button" class="btn btn-default" data-dismiss="modal">
                          <span class="glyphicon glyphicon-remove"></span> Cerrar</button>
                      </div>
                    </div>
                  </div>
                </div>
                <button type="button" class="btn btn-warning btn-xs" data-toggle="modal" data-target="#editar{{$tienda->id}}">
                  <span class="glyphicon glyphicon-edit"></span>
                </button>
                <div class="modal fade" id="editar{{$tienda->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                  <div class="modal-dialog" role="document">
                    <div class="modal-content">
                      {{Form::open(['url'=>'tienda/'.$tienda->id, 'method'=>'put'])}}
                      {{ csrf_field() }}
                      <div class="modal-header" style="background-color:#385a94; color:#fff;">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">MODIFICAR TIENDA</h4>
                      </div>
                      <div class="modal-body" style="background-color:#e69c2d">
                        <div class="panel" style="background-color:#bd7406">
                          <div class="panel-body">
                            <div class="form-group">
                              <input type="text" data-mask="99999999999" class="form-control ruc input-sm" placeholder="RUC*" required
                                name="ruc" id="ruc" value="{{$tienda->ruc}}">
                            </div>
                            <div class="form-group">
                              <input type="text" class="form-control mayuscula input-sm" placeholder="NOMBRE*" required name="nombre" id="nombre"
                                 value="{{$tienda->nombre}}">
                            </div>
                            <div class="form-group">
                              <input type="text" class="form-control mayuscula input-sm" placeholder="SERIE*" required name="serie" id="serie"
                                 value="{{$tienda->serie}}">
                            </div>
                            <div class="form-group">
                              <input type="text" class="form-control mayuscula input-sm" placeholder="TICKETERA*" required name="ticketera"
                              id="ticketera" value="{{$tienda->ticketera}}">
                            </div>
                            <div class="form-group">
                              <input type="text" class="form-control mayuscula input-sm" placeholder="DIRECCIÓN" name="direccion" id="direccion"
                                 value="{{$tienda->direccion}}">
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="modal-footer" style="background-color:#385a94">
                        <button type="button" class="btn btn-default" data-dismiss="modal">
                          <span class="glyphicon glyphicon-ban-circle"></span> Cancelar</button>
                        <button type="submit" class="btn btn-primary"><span class="glyphicon glyphicon-floppy-disk"></span> Modificar</button>
                      </div>
                      {{Form::close()}}
                    </div>
                  </div>
                </div>
                <button type="button" class="btn btn-danger btn-xs" data-toggle="modal" data-target="#eliminar{{$tienda->id}}">
                  <span class="glyphicon glyphicon-trash"></span>
                </button>
                <div class="modal fade" id="eliminar{{$tienda->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                  <div class="modal-dialog" role="document">
                    <div class="modal-content">
                      {{Form::open(['url'=>'tienda/'.$tienda->id, 'method'=>'delete'])}}
                      {{ csrf_field() }}
                      <div class="modal-header" style="background-color:#bb0000; color:#fff;">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">ELIMINAR TIENDA</h4>
                      </div>
                      <div class="modal-body" style="background-color:#e69c2d">
                        <div class="panel" style="background-color:#bd7406">
                          <div class="panel-body">
                            <p>ESTA A PUNTO DE ELIMINAR LA TIENDA {{$tienda->nombre}}, CON ESTA ACCIÓN ELIMINARÁ TODOS
                              LOS REGISTROS RELACIONADOS CON ESTA TIENDA.</p>
                            <p>SI QUIERE CONTINUAR CON ESTA ACCIÓN HAGA CLIC EN EL BOTÓN ELIMINAR, DE LO CONTRARIO, EN EL BOTÓN
                              CANCELAR.</p>
                          </div>
                        </div>
                      </div>
                      <div class="modal-footer" style="background-color:#bb0000">
                        <button type="button" class="btn btn-default" data-dismiss="modal">
                          <span class="glyphicon glyphicon-ban-circle"></span> Cancelar</button>
                        <button type="submit" class="btn btn-danger"><span class="glyphicon glyphicon-trash"></span> Eliminar</button>
                      </div>
                      {{Form::close()}}
                    </div>
                  </div>
                </div>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>
@stop
