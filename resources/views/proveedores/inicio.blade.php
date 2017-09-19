@extends('plantillas.administrador')

@section('estilos')
{{Html::style('bootgrid/jquery.bootgrid.min.css')}}
<style media="screen">
  .modal-body>.table-responsive>.table-hover tr:hover{
    background-color: #e69c2d;
  }
</style>
@stop

@section('titulo')
Proveedores
<!--Boton para mostrar el modal con el formulario para ingresar los datos de un nuevo proveedor-->
<!--Fecha 14/09/2017-->
<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#nuevo">
  <span class="glyphicon glyphicon-plus"></span> Nuevo
</button>
<!--Modal con el formulario para ingresar los datos del nuevo proveedor-->
<!--Fecha 14/09/2017-->
<div class="modal fade" id="nuevo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      {{Form::open(['url'=>'proveedor'])}}
      {{ csrf_field() }}
      <div class="modal-header" style="background-color:#385a94; color:#fff;">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">NUEVO PROVEEDOR</h4>
      </div>
      <div class="modal-body" style="background-color:#e69c2d">
        <div class="panel" style="background-color:#bd7406">
          <div class="panel-body">
            <div class="form-group">
              <input type="text" class="form-control input-sm" data-mask="99999999999" placeholder="RUC*" required name="ruc" id="ruc">
            </div>
            <div class="form-group">
              <input type="text" class="form-control mayuscula input-sm" placeholder="NOMBRE*" name="nombre" id="nombre"
                required>
            </div>
            <div class="form-group">
              <input type="text" class="form-control mayuscula input-sm" placeholder="DIRECCIÓN" name="direccion" id="direccion">
            </div>
            <div class="form-group">
              <input type="text" class="form-control mayuscula input-sm" placeholder="TELÉFONO" name="telefono" id="telefono">
            </div>
            <div class="form-group">
              <input type="text" class="form-control mayuscula input-sm" placeholder="REPRESENTANTE" name="representante" id="representante">
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
  <div class="col-xs-12 col-sm-12 col-md-8 col-md-offset-2 col-lg-8 col-lg-offset-2">
    <div class="table-responsive">
      <table class="table table-hover table-condensed table-bordered" id="tblProveedores">
        <thead>
          <tr class="info">
            <th data-column-id="ruc" data-type="numeric" data-order="desc">RUC</th>
            <th data-column-id="nombre">Nombre</th>
            <th data-column-id="commands" data-formatter="commands" data-sortable="false">Operaciones</th>
          </tr>
        </thead>
      </table>
    </div>
  </div>
</div>
<!--Modal que muestra algunos errores del sistema.-->
<!--Fecha 14/09/2017-->
<div class="modal fade" id="errores" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header" style="background-color:#bb0000; color:#fff;">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">HUBO UN ERROR</h4>
      </div>
      <div class="modal-body" style="background-color:#e69c2d">
        <div class="panel" style="background-color:#bd7406">
          <div class="panel-body" id="mensaje">

          </div>
        </div>
      </div>
      <div class="modal-footer" style="background-color:#bb0000">
        <button type="button" class="btn btn-default" data-dismiss="modal">
          <span class="glyphicon glyphicon-ban-circle"></span> Cancelar</button>
      </div>
    </div>
  </div>
</div>
<!--Modal con los datos del proveedor-->
<!--Fecha 14/09/2017-->
<div class="modal fade" id="ver" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header" style="background-color:#31b0d5; color:#fff;">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">DATOS DEL PROVEEDOR</h4>
      </div>
      <div class="modal-body" style="background-color:#e69c2d">
        <div class="table-responsive" style="background-color:#bd7406">
          <table class="table table-condensed table-bordered table-hover" style="margin:0px; background-color:#bd7406; color:#ff;">
            <tr>
              <th>RUC: </th>
              <td id="tdRuc"></td>
            </tr>
            <tr>
              <th>Nombre: </th>
              <td id="tdNombre"></td>
            </tr>
            <tr>
              <th>Dirección: </th>
              <td id="tdDireccion"></td>
            </tr>
            <tr>
              <th>Teléfono: </th>
              <td id="tdTelefono"></td>
            </tr>
            <tr>
              <th>Representante: </th>
              <td id="tdRepresentante"></td>
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
<!--Modal con el formulario para modificar los datos del proveedor-->
<!--Fecha 14/09/2017-->
<div class="modal fade" id="editar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      {{Form::open(['id'=>'frmEditar', 'method'=>'put'])}}
      {{ csrf_field() }}
      <div class="modal-header" style="background-color:#385a94; color:#fff;">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">MODIFICAR PROVEEDOR</h4>
      </div>
      <div class="modal-body" style="background-color:#e69c2d">
        <div class="panel" style="background-color:#bd7406">
          <div class="panel-body">
            <div class="form-group">
              <input type="text" class="form-control input-sm" data-mask="99999999999" placeholder="RUC*" required name="ruc" id="editarRuc">
            </div>
            <div class="form-group">
              <input type="text" class="form-control mayuscula input-sm" placeholder="NOMBRE*" name="nombre" id="editarNombre"
                required>
            </div>
            <div class="form-group">
              <input type="text" class="form-control mayuscula input-sm" placeholder="DIRECCIÓN" name="direccion" id="editarDireccion">
            </div>
            <div class="form-group">
              <input type="text" class="form-control mayuscula input-sm" placeholder="TELÉFONO" name="telefono" id="editarTelefono">
            </div>
            <div class="form-group">
              <input type="text" class="form-control mayuscula input-sm" placeholder="REPRESENTANTE" name="representante" id="editarRepresentante">
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
<!--Modal de advertencia antes de eliminar el proveedor-->
<!--Fecha 14/09/2017-->
<div class="modal fade" id="eliminar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      {{Form::open(['id'=>'frmEliminar', 'method'=>'delete'])}}
      {{ csrf_field() }}
      <div class="modal-header" style="background-color:#bb0000; color:#fff;">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">ELIMINAR PROVEEDOR</h4>
      </div>
      <div class="modal-body" style="background-color:#e69c2d">
        <div class="panel" style="background-color:#bd7406">
          <div class="panel-body">
            <p>ESTA A PUNTO DE ELIMINAR EL PROVEEDOR <strong id="parProveedor"></strong>, CON ESTA ACCIÓN ELIMINARÁ TODOS
                LOS REGISTROS RELACIONADOS CON ESTE PROVEEDOR INCLUYENDO LAS COMPRAS HECHAS A ESTE PROVEEDOR, SUS
                DETALLES E INGRESOS A LAS TIENDAS.</p>
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
@stop

@section('scripts')
{{Html::script('bootgrid/jquery.bootgrid.min.js')}}
<script type="text/javascript">
  $(document).ready(function() {
    /*
     * Token necesario para hacer consultas por ajax.
     * Fecha 14/09/2017
     */
    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });

    /**
     * Lista los proveedores llamanto por ajax al metodo lista del controlador ProveedorController.
     * Fecha 14/09/2017
    */
    var grid = $("#tblProveedores").bootgrid({
      labels: {
        all: "todos",
        infos: "",
        loading: "Cargando datos...",
        noResults: "Ningun resultado encontrado",
        refresh: "Actualizar",
        search: "Buscar"
      },
      ajax: true,
      post: function (){
        return {
          '_token': '{{ csrf_token() }}',
          id: "b0df282a-0d67-40e5-8558-c9e93b7befed"
        };
      },
      url: "{{url('listar-proveedores')}}",
      formatters: {
        "commands": function(column, row)
        {
            return "<button type='button' class='btn btn-xs btn-info command-show' data-row-ruc='"+row.ruc+"' style='margin:2px'>"+
              "<span class='fa fa-eye'></span></button>"+
              "<button type='button' class='btn btn-xs btn-warning command-edit' data-row-ruc='"+row.ruc+"' style='margin:2px'>"+
                "<span class='fa fa-edit'></span></button>"+
              "<button type='button' class='btn btn-xs btn-danger command-delete' data-row-ruc='"+row.ruc+"' style='margin:2px'>"+
                "<span class='fa fa-trash'></span></button>";
        }
      }
    }).on("loaded.rs.jquery.bootgrid", function(){
      /* Se ejecuta despues de cargar y procesar los datos */
      grid.find(".command-show").on("click", function(e){
        $.post("{{url('buscar-proveedor')}}", {ruc: $(this).data("row-ruc")}, function(data, textStatus, xhr) {
          $("#tdRuc").html(data['empresa']['ruc']);
          $("#tdNombre").html(data['empresa']['nombre']);
          $("#tdDireccion").html(data['empresa']['direccion']);
          $("#tdTelefono").html(data['proveedor']['telefono']);
          $("#tdRepresentante").html(data['proveedor']['representante']);
          $("#ver").modal('show');
        });
      }).end().find(".command-edit").on("click", function(e){
        $.post("{{url('buscar-proveedor')}}", {ruc: $(this).data("row-ruc")}, function(data, textStatus, xhr) {
          $("#editarRuc").val(data['empresa']['ruc']);
          $("#editarNombre").val(data['empresa']['nombre']);
          $("#editarDireccion").val(data['empresa']['direccion']);
          $("#editarTelefono").val(data['proveedor']['telefono']);
          $("#editarRepresentante").val(data['proveedor']['representante']);
          $("#frmEditar").prop('action', "{{url('proveedor')}}/" + data['proveedor']['id']);
          $("#editar").modal('show');
        });
      }).end().find(".command-delete").on('click', function(e) {
        $.post("{{url('buscar-proveedor')}}", {ruc: $(this).data("row-ruc")}, function(data, textStatus, xhr) {
          $("#parProveedor").html(data['empresa']['nombre']);
          $("#frmEliminar").prop('action', "{{url('proveedor')}}/" + data['proveedor']['id']);
          $("#eliminar").modal('show');
        });
      });
    });

    /**
     * Mostramos los datos del proveedor si coincide con el ruc enviado.
    */
    $("#ruc").change(function() {
      $.post("{{url('buscar-proveedor')}}", {ruc: $(this).val()}, function(data, textStatus, xhr) {
        if (data['proveedor'] != 0) {

          $("#nombre").val(data['empresa']['nombre']);
          $("#direccion").val(data['empresa']['direccion']);
          $("#telefono").val(data['proveedor']['telefono']);
          $("#representante").val(data['proveedor']['representante']);
        }else{

          $("#nombre").val("");
          $("#direccion").val("");
          $("#telefono").val("");
          $("#representante").val("");
        }
      });
    });
  });
</script>
@stop
