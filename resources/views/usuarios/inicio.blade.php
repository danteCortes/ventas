@extends('plantillas.administrador')

@section('estilos')
<style media="screen">
  .modal-body>.table-responsive>.table-hover tr:hover{
    background-color: #e69c2d;
  }
</style>
@stop

@section('titulo')
Usuarios
<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#nuevo">
  <span class="glyphicon glyphicon-plus"></span> Nuevo
</button>
<div class="modal fade" id="nuevo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      {{Form::open(['url'=>'usuario', 'enctype'=>'multipart/form-data'])}}
      {{ csrf_field() }}
      <div class="modal-header" style="background-color:#385a94; color:#fff;">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">NUEVO USUARIO</h4>
      </div>
      <div class="modal-body" style="background-color:#e69c2d">
        <div class="panel" style="background-color:#bd7406">
          <div class="panel-body">
            <div class="form-group">
              <input type="text" data-mask="99999999" class="form-control input-sm" placeholder="DNI*" required name="dni">
            </div>
            <div class="form-group">
              <input type="text" class="form-control mayuscula input-sm" placeholder="NOMBRES*" required name="nombres">
            </div>
            <div class="form-group">
              <input type="text" class="form-control mayuscula input-sm" placeholder="APELLIDOS*" required name="apellidos">
            </div>
            <div class="form-group">
              <input type="text" class="form-control mayuscula input-sm" placeholder="DIRECCIÓN" name="direccion">
            </div>
            <div class="form-group">
              <input type="text" data-mask="999999999" class="form-control input-sm" placeholder="TELÉFONO" name="telefono">
            </div>
            <div class="form-group">
              <select class="form-control" name="tipo" required>
                <option value=>SELECCIONAR TIPO</option>
                <option value="1">ADMINISTRADOR</option>
                <option value="2">CAJERO</option>
              </select>
            </div>
            <div class="form-group">
              <select class="form-control" name="tienda_id">
                <option value=>SELECCIONAR TIENDA</option>
                @foreach(\App\Tienda::all() as $tienda)
                  <option value="{{$tienda->id}}">{{$tienda->nombre}}</option>
                @endforeach
              </select>
            </div>
            <div class="form-group">
              <input type="file" class="form-control mayuscula input-sm" name="foto">
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
            <th>DNI</th>
            <th>USUARIO</th>
            <th>OPERACIONES</th>
          </tr>
        </thead>
        <tbody>
          @foreach(App\Usuario::where('id', '!=', Auth::user()->id)->get() as $usuario)
            <tr>
              <td>{{$usuario->persona->dni}}</td>
              <td>{{$usuario->persona->nombres}} {{$usuario->persona->apellidos}}</td>
              <td>
                <button type="button" class="btn btn-info btn-xs" data-toggle="modal" data-target="#ver{{$usuario->id}}">
                  <span class="fa fa-eye"></span>
                </button>
                <div class="modal fade" id="ver{{$usuario->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                  <div class="modal-dialog" role="document">
                    <div class="modal-content">
                      <div class="modal-header" style="background-color:#31b0d5; color:#fff;">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">VER USUARIO</h4>
                      </div>
                      <div class="modal-body" style="background-color:#e69c2d">
                        <div class="img-resposive" style="text-align:center">
                          <img src="{{url('storage/usuarios/'.$usuario->foto)}}" alt="" class="img-responsive img-thumbnail" style="height:100px;">
                        </div>
                        <div class="table-responsive" style="background-color:#bd7406">
                          <table class="table table-condensed table-bordered table-hover" style="margin:0px; background-color:#bd7406; color:#ff;">
                            <tr>
                              <th>DNI: </th>
                              <td>{{$usuario->persona->dni}}</td>
                            </tr>
                            <tr>
                              <th>Nombres: </th>
                              <td>{{$usuario->persona->nombres}}</td>
                            </tr>
                            <tr>
                              <th>Apellidos: </th>
                              <td>{{$usuario->persona->apellidos}}</td>
                            </tr>
                            <tr>
                              <th>Dirección: </th>
                              <td>{{$usuario->persona->direccion}}</td>
                            </tr>
                            <tr>
                              <th>Teléfono: </th>
                              <td>{{$usuario->persona->telefono}}</td>
                            </tr>
                            <tr>
                              <th>Tipo: </th>
                              <td>
                                @if($usuario->tipo == 1)
                                  ADMINISTRADOR
                                @else
                                  CAJERO
                                @endif
                              </td>
                            </tr>
                            <tr>
                              <th>Tienda: </th>
                              <td>{{$usuario->tienda ? $usuario->tienda->nombre : ''}}</td>
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
                <button type="button" class="btn btn-warning btn-xs" data-toggle="modal" data-target="#editar{{$usuario->id}}">
                  <span class="glyphicon glyphicon-edit"></span>
                </button>
                <div class="modal fade" id="editar{{$usuario->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                  <div class="modal-dialog" role="document">
                    <div class="modal-content">
                      {{Form::open(['url'=>'usuario/'.$usuario->id, 'method'=>'put', 'enctype'=>'multipart/form-data'])}}
                      {{ csrf_field() }}
                      <div class="modal-header" style="background-color:#385a94; color:#fff;">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">MODIFICAR USUARIO</h4>
                      </div>
                      <div class="modal-body" style="background-color:#e69c2d">
                        <div class="img-resposive" style="text-align:center">
                          <img src="{{url('storage/usuarios/'.$usuario->foto)}}" alt="" class="img-responsive img-thumbnail" style="height:100px;">
                        </div>
                        <div class="panel" style="background-color:#bd7406">
                          <div class="panel-body">
                            <div class="form-group">
                              <input type="text" data-mask="99999999" class="form-control input-sm" placeholder="DNI*" required name="dni" value="{{$usuario->persona->dni}}">
                            </div>
                            <div class="form-group">
                              <input type="text" class="form-control mayuscula input-sm" placeholder="NOMBRES*" required name="nombres" value="{{$usuario->persona->nombres}}">
                            </div>
                            <div class="form-group">
                              <input type="text" class="form-control mayuscula input-sm" placeholder="APELLIDOS*" required name="apellidos" value="{{$usuario->persona->apellidos}}">
                            </div>
                            <div class="form-group">
                              <input type="text" class="form-control mayuscula input-sm" placeholder="DIRECCIÓN" name="direccion" value="{{$usuario->persona->direccion}}">
                            </div>
                            <div class="form-group">
                              <input type="text" data-mask="999999999" class="form-control input-sm" placeholder="TELÉFONO" name="telefono" value="{{$usuario->persona->telefono}}">
                            </div>
                            <div class="form-group">
                              <select class="form-control" name="tipo" required>
                                  <option value="{{$usuario->tipo}}">
                                    @if($usuario->tipo == 1)
                                      ADMINISTRADOR (ACTUAL)
                                    @else
                                      CAJERO (ACTUAL)
                                    @endif
                                  </option>
                                <option value="">SELECCIONAR TIPO</option>
                                <option value="1">ADMINISTRADOR</option>
                                <option value="2">CAJERO</option>
                              </select>
                            </div>
                            <div class="form-group">
                              <select class="form-control" name="tienda_id">
                                  <option value="{{$usuario->tienda_id}}">
                                    {{($usuario->tienda) ? $usuario->tienda->nombre : 'SELECCIONAR TIENDA '}} (ACTUAL)
                                  </option>
                                <option value=>SELECCIONAR TIENDA</option>
                                @foreach(\App\Tienda::all() as $tienda)
                                  <option value="{{$tienda->id}}">{{$tienda->nombre}}</option>
                                @endforeach
                              </select>
                            </div>
                            <div class="form-group">
                              <input type="file" class="form-control mayuscula input-sm" name="foto">
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
                <button type="button" class="btn btn-danger btn-xs" data-toggle="modal" data-target="#eliminar{{$usuario->id}}">
                  <span class="glyphicon glyphicon-trash"></span>
                </button>
                <div class="modal fade" id="eliminar{{$usuario->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                  <div class="modal-dialog" role="document">
                    <div class="modal-content">
                      {{Form::open(['url'=>'usuario/'.$usuario->id, 'method'=>'delete'])}}
                      {{ csrf_field() }}
                      <div class="modal-header" style="background-color:#bb0000; color:#fff;">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">ELIMINAR USUARIO</h4>
                      </div>
                      <div class="modal-body" style="background-color:#e69c2d">
                        <div class="panel" style="background-color:#bd7406">
                          <div class="panel-body">
                            <p>ESTA A PUNTO DE ELIMINAR AL USUARIO {{$usuario->persona->nombres}}, CON ESTA ACCIÓN ELIMINARÁ TODOS
                              LOS REGISTROS RELACIONADOS CON ESTE USUARIO.</p>
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
                <!--Botón para mostrar un modal de advertencia antes de restaurar la contraseña del usuario.-->
                <!--Fecha: 16/09/2017-->
                <button type="button" class="btn btn-success btn-xs" data-toggle="modal" data-target="#restaurar{{$usuario->id}}">
                  <span class="glyphicon glyphicon-refresh"></span>
                </button>
                <!--Modal de advertencia para restaurar la contraseña del usuario. Contiene un formuario que envia el id del
                  usuario al que se le va a resturar al método restaurarContraseña del controlador UsuarioController.-->
                <!--Fecha: 16/09/2017-->
                <div class="modal fade" id="restaurar{{$usuario->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                  <div class="modal-dialog" role="document">
                    <div class="modal-content">
                      {{Form::open(['url'=>'restaurar-contrasenia'])}}
                      {{ csrf_field() }}
                      <div class="modal-header" style="background-color:#449d44; color:#fff;">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">RESTAURAR CONTRASEÑA</h4>
                      </div>
                      <div class="modal-body" style="background-color:#e69c2d">
                        <div class="panel" style="background-color:#bd7406">
                          <div class="panel-body">
                            <p style="color:#fff;">ESTA A PUNTO DE RESTAURAR LA CONTRASEÑA AL USUARIO <strong>{{$usuario->persona->nombres}}</strong>,
                              CON ESTA ACCIÓN LA CONTRASEÑA DEL USUARIO VOLVERÁ A SER SU NÚMERO DE DNI, NO OLVIDE INFORMAR AL USUARIO EN CUESTIÓN DE
                              ESTE CAMBIO.</p>
                            <p style="color:#fff;">SI QUIERE CONTINUAR CON ESTA ACCIÓN HAGA CLIC EN EL BOTÓN RESTAURAR, DE LO CONTRARIO, EN EL BOTÓN
                              CANCELAR.</p>
                          </div>
                        </div>
                      </div>
                      <div class="modal-footer" style="background-color:#449d44">
                        {{Form::hidden('usuario_id', $usuario->id)}}
                        <button type="button" class="btn btn-default" data-dismiss="modal">
                          <span class="glyphicon glyphicon-ban-circle"></span> Cancelar</button>
                        <button type="submit" class="btn btn-primary"><span class="glyphicon glyphicon-refresh"></span> Restaurar</button>
                      </div>
                      {{Form::close()}}
                    </div>
                  </div>
                </div>
                @if($usuario->tipo != 1)
                  @if(!$usuario->estado_caja)
                    <!--Botón para mostrar un modal de advertencia antes de abrir caja  al usuario.-->
                    <!--Fecha: 16/09/2017-->
                    <button type="button" class="btn btn-success btn-xs" data-toggle="modal" data-target="#abrir{{$usuario->id}}"
                      style="background-color:#fff; border-color:black; color:#000;">
                      <span class="fa fa-upload"></span>
                    </button>
                    <!--Modal de advertencia para abrir caja al usuario. Contiene un formuario que envia el id del
                      usuario al que se le va a abrir caja al método abrirCaja del controlador UsuarioController.-->
                    <!--Fecha: 16/09/2017-->
                    <div class="modal fade" id="abrir{{$usuario->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                    <div class="modal-dialog" role="document">
                      <div class="modal-content">
                        {{Form::open(['url'=>'abrir-caja'])}}
                        {{ csrf_field() }}
                        <div class="modal-header" style="background-color:#dddddd; color:#000;">
                          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                          <h4 class="modal-title" id="myModalLabel">ABRIR CAJA</h4>
                        </div>
                        <div class="modal-body" style="background-color:#e69c2d">
                          <div class="panel" style="background-color:#bd7406">
                            <div class="panel-body">
                              <p style="color:#fff;">ESTA A PUNTO DE ABRIR CAJA AL USUARIO <strong>{{$usuario->persona->nombres}}</strong>,
                                CON ESTA ACCIÓN EL USUARIO PODRÁ REALIZAR OPERACIONES EN SU TIENDA ASIGNADA, NO OLVIDE INFORMAR AL USUARIO EN
                                CUESTIÓN DE ESTE CAMBIO.</p>
                              <p style="color:#fff;">SI QUIERE CONTINUAR CON ESTA ACCIÓN HAGA CLIC EN EL BOTÓN ABRIR CAJA, DE LO CONTRARIO, EN EL BOTÓN
                                CANCELAR.</p>
                            </div>
                          </div>
                        </div>
                        <div class="modal-footer" style="background-color:#dddddd">
                          {{Form::hidden('usuario_id', $usuario->id)}}
                          <button type="button" class="btn btn-default" data-dismiss="modal">
                            <span class="glyphicon glyphicon-ban-circle"></span> Cancelar</button>
                          <button type="submit" class="btn btn-primary"><span class="fa fa-upload"></span> Abrir Caja</button>
                        </div>
                        {{Form::close()}}
                      </div>
                    </div>
                  </div>
                  @else
                    <!--Botón para mostrar un modal de advertencia antes de cerrar caja  al usuario.-->
                    <!--Fecha: 16/09/2017-->
                    <button type="button" class="btn btn-success btn-xs" data-toggle="modal" data-target="#cerrar{{$usuario->id}}"
                      style="background-color:#fff; border-color:black; color:#000;">
                      <span class="fa fa-download"></span>
                    </button>
                    <!--Modal de advertencia para cerrar caja al usuario. Contiene un formuario que envia el id del
                      usuario al que se le va a cerrar caja al método cerrarCaja del controlador UsuarioController.-->
                    <!--Fecha: 16/09/2017-->
                    <div class="modal fade" id="cerrar{{$usuario->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                    <div class="modal-dialog" role="document">
                      <div class="modal-content">
                        {{Form::open(['url'=>'abrir-caja'])}}
                        {{ csrf_field() }}
                        <div class="modal-header" style="background-color:#dddddd; color:#000;">
                          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                          <h4 class="modal-title" id="myModalLabel">CERRAR CAJA</h4>
                        </div>
                        <div class="modal-body" style="background-color:#e69c2d">
                          <div class="panel" style="background-color:#bd7406">
                            <div class="panel-body">
                              <p style="color:#fff;">ESTA A PUNTO DE CERRAR CAJA AL USUARIO <strong>{{$usuario->persona->nombres}}</strong>,
                                CON ESTA ACCIÓN EL USUARIO YA NO PODRÁ REALIZAR OPERACIONES EN SU TIENDA ASIGNADA, NO OLVIDE INFORMAR AL USUARIO EN
                                CUESTIÓN DE ESTE CAMBIO.</p>
                              <p style="color:#fff;">SI QUIERE CONTINUAR CON ESTA ACCIÓN HAGA CLIC EN EL BOTÓN CERRAR CAJA, DE LO CONTRARIO, EN EL BOTÓN
                                CANCELAR.</p>
                            </div>
                          </div>
                        </div>
                        <div class="modal-footer" style="background-color:#dddddd">
                          {{Form::hidden('usuario_id', $usuario->id)}}
                          <button type="button" class="btn btn-default" data-dismiss="modal">
                            <span class="glyphicon glyphicon-ban-circle"></span> Cancelar</button>
                          <button type="submit" class="btn btn-primary"><span class="fa fa-download"></span> Cerrar Caja</button>
                        </div>
                        {{Form::close()}}
                      </div>
                    </div>
                  </div>
                  @endif
                @endif
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>
@stop
