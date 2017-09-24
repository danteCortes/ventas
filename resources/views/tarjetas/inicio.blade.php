@extends('plantillas.administrador')

@section('estilos')
<style media="screen">
  .modal-body>.table-responsive>.table-hover tr:hover{
    background-color: #e69c2d;
  }
</style>
@stop

@section('titulo')
Tarjetas
<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#nuevo">
  <span class="glyphicon glyphicon-plus"></span> Nuevo
</button>
<div class="modal fade" id="nuevo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      {{Form::open(['url'=>'tarjeta'])}}
      {{ csrf_field() }}
      <div class="modal-header" style="background-color:#385a94; color:#fff;">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">NUEVA TARJETA</h4>
      </div>
      <div class="modal-body" style="background-color:#e69c2d">
        <div class="panel" style="background-color:#bd7406">
          <div class="panel-body">
            <div class="form-group">
              <input type="text" class="form-control mayuscula input-sm" placeholder="NOMBRE*" required name="nombre">
            </div>
            <div class="form-group">
              <input type="text" class="form-control numero input-sm" placeholder="COMISIÓN*" required name="comision">
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
            <th>TARJETA</th>
            <th>COMISIÓN</th>
            <th>OPERACIONES</th>
          </tr>
        </thead>
        <tbody>
          @foreach(App\Tarjeta::all() as $tarjeta)
            <tr>
              <td>{{$tarjeta->nombre}}</td>
              <td>{{$tarjeta->comision}} %</td>
              <td>
                <button type="button" class="btn btn-warning btn-xs" data-toggle="modal" data-target="#editar{{$tarjeta->id}}">
                  <span class="glyphicon glyphicon-edit"></span>
                </button>
                <div class="modal fade" id="editar{{$tarjeta->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                  <div class="modal-dialog" role="document">
                    <div class="modal-content">
                      {{Form::open(['url'=>'tarjeta/'.$tarjeta->id, 'method'=>'put'])}}
                      {{ csrf_field() }}
                      <div class="modal-header" style="background-color:#385a94; color:#fff;">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">MODIFICAR TARJETA</h4>
                      </div>
                      <div class="modal-body" style="background-color:#e69c2d">
                        <div class="panel" style="background-color:#bd7406">
                          <div class="panel-body">
                            <div class="form-group">
                              <input type="text" class="form-control mayuscula input-sm" placeholder="NOMBRE*" required name="nombre"
                                 value="{{$tarjeta->nombre}}">
                            </div>
                            <div class="form-group">
                              <input type="text" class="form-control numero input-sm" placeholder="COMISIÓN*" required name="comision"
                                 value="{{$tarjeta->comision}}">
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
                <button type="button" class="btn btn-danger btn-xs" data-toggle="modal" data-target="#eliminar{{$tarjeta->id}}">
                  <span class="glyphicon glyphicon-trash"></span>
                </button>
                <div class="modal fade" id="eliminar{{$tarjeta->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                  <div class="modal-dialog" role="document">
                    <div class="modal-content">
                      {{Form::open(['url'=>'tarjeta/'.$tarjeta->id, 'method'=>'delete'])}}
                      {{ csrf_field() }}
                      <div class="modal-header" style="background-color:#bb0000; color:#fff;">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">ELIMINAR TARJETA</h4>
                      </div>
                      <div class="modal-body" style="background-color:#e69c2d">
                        <div class="panel" style="background-color:#bd7406">
                          <div class="panel-body">
                            <p>ESTA A PUNTO DE ELIMINAR LA TARJETA {{$tarjeta->nombre}}, CON ESTA ACCIÓN ELIMINARÁ TODOS
                              LOS REGISTROS RELACIONADOS CON ESTA TARJETA, QUE INCLUYE PAGOS HECHAS CON ESTA TARJETA.</p>
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

@section('scripts')
<script type="text/javascript">
  $(document).ready(function() {
    $(".numero").mask("##9");
  });
</script>
@stop
