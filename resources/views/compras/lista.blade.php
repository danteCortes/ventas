@extends('plantillas.administrador')

@section('estilos')
{{Html::style('bootgrid/jquery.bootgrid.min.css')}}
<style media="screen">
  .modal-body>.table-responsive>.table-hover tr:hover{
    /*background-color: #e69c2d;*/
  }
</style>
@stop

@section('titulo')
Compras
<a href="{{url('compra/create')}}" class="btn btn-primary">
  <span class="glyphicon glyphicon-plus"></span> Nuevo
</a>
<a href="{{url('compra')}}" class="btn btn-primary">
  <span class="glyphicon glyphicon-list"></span> Listar
</a>
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
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
    <div class="table-responsive">
      <table class="table table-condensed table-hover table-bordered" id="tblCompras">
        <thead>
          <tr class="info">
            <th data-column-id="recibo" data-order="desc">RECIBO</th>
            <th data-column-id="proveedor">PROVEEDOR</th>
            <th data-column-id="usuario">USUARIO</th>
            <th data-column-id="fecha">FECHA</th>
            <th data-column-id="commands" data-formatter="commands" data-sortable="false">OPERACIONES</th>
          </tr>
        </thead>
      </table>
    </div>
  </div>
</div>
<!--Modal para ver los datos de la compra-->
<!--Fecha 15/09/2017-->
<div class="modal fade" id="ver" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header" style="background-color:#31b0d5; color:#fff;">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">DATOS DE LA COMPRA</h4>
      </div>
      <div class="modal-body" style="background-color:#e69c2d">
        <div class="table-responsive" style="background-color:#bd7406">
          <table class="table table-condensed table-bordered table-hover" style="margin:0px; background-color:#bfbfbf;">
            <tr>
              <th>Recibo: </th>
              <td class="recibo"></td>
            </tr>
            <tr>
              <th>Proveedor: </th>
              <td>
                <div class="panel panel-default" style="margin-bottom:0px;">
                  <div class="panel-heading" style="background-color:#575757; color: #FFF;">
                    <h3 class="panel-title">Proveedor
                      <button type="button" class="btn btn-xs btn-default pull-right" data-toggle="collapse" data-target="#panelProveedor"
                         aria-controls="panelProveedor">
                        <span class="fa fa-minus"></span>
                      </button>
                    </h3>
                  </div>
                  <div class="panel-body collapse" id="panelProveedor" style="background-color:#bfbfbf;">
                    <div class="table-responsive">
                      <table class="table table-condensed table-bordered" style="margin:0px; background-color:#bfbfbf; color:#ff;">
                        <tr>
                          <th>RUC:</th>
                          <td class="prov_ruc"></td>
                        </tr>
                        <tr>
                          <th>Nombre:</th>
                          <td class="prov_nombre"></td>
                        </tr>
                        <tr>
                          <th>Dirección:</th>
                          <td class="prov_direccion"></td>
                        </tr>
                        <tr>
                          <th>Teléfono:</th>
                          <td class="prov_telefono"></td>
                        </tr>
                        <tr>
                          <th>Representante:</th>
                          <td class="prov_representante"></td>
                        </tr>
                      </table>
                    </div>
                  </div>
                </div>
              </td>
            </tr>
            <tr>
              <th>Usuario: </th>
              <td>
                <div class="panel panel-default" style="margin-bottom:0px;">
                  <div class="panel-heading" style="background-color:#575757; color: #FFF;">
                    <h3 class="panel-title">Usuario
                      <button type="button" class="btn btn-xs btn-default pull-right" data-toggle="collapse" data-target="#panelUsuario"
                         aria-controls="panelUsuario">
                        <span class="fa fa-minus"></span>
                      </button>
                    </h3>
                  </div>
                  <div class="panel-body collapse" id="panelUsuario" style="background-color:#bfbfbf;">
                    <div class="table-responsive">
                      <table class="table table-condensed table-bordered" style="margin:0px; background-color:#bfbfbf; color:#ff;">
                        <tr>
                          <th>DNI:</th>
                          <td class="usu_dni"></td>
                        </tr>
                        <tr>
                          <th>Nombres y Apellidos:</th>
                          <td class="usu_nombres_apellidos"></td>
                        </tr>
                        <tr>
                          <th>Dirección:</th>
                          <td class="usu_direccion"></td>
                        </tr>
                        <tr>
                          <th>Teléfono:</th>
                          <td class="usu_telefono"></td>
                        </tr>
                      </table>
                    </div>
                  </div>
                </div>
              </td>
            </tr>
            <tr>
              <th>Detalles: </th>
              <td>
                <div class="panel panel-default" style="margin-bottom:0px;">
                  <div class="panel-heading" style="background-color:#575757; color: #FFF;">
                    <h3 class="panel-title">Detalles
                      <button type="button" class="btn btn-xs btn-default pull-right" data-toggle="collapse" data-target="#panelDetalles"
                         aria-controls="panelDetalles">
                        <span class="fa fa-minus"></span>
                      </button>
                    </h3>
                  </div>
                  <div class="panel-body collapse" id="panelDetalles" style="background-color:#bfbfbf;">
                    <div class="table-responsive">
                      <table class="table table-condensed table-bordered" style="margin:0px; background-color:#bfbfbf;">
                        <thead>
                          <tr>
                            <th>Cant.</th>
                            <th>Descripción</th>
                            <th>P. Unit.</th>
                            <th style="width:65px;">P. Total</th>
                          </tr>
                        </thead>
                        <tbody class="detalles">
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </td>
            </tr>
            <tr>
              <th>Fecha: </th>
              <td class="fecha"></td>
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
<div class="modal fade" id="eliminar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      {{Form::open(['id'=>'frmEliminar', 'method'=>'delete'])}}
      {{ csrf_field() }}
      <div class="modal-header" style="background-color:#bb0000; color:#fff;">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">ELIMINAR COMPRA</h4>
      </div>
      <div class="modal-body" style="background-color:#e69c2d">
        <div class="panel" style="background-color:#bd7406">
          <div class="panel-body">
            <p>ESTA A PUNTO DE ELIMINAR LA COMPRA <strong class="recibo"></strong>, CON ESTA ACCIÓN ELIMINARÁ TODOS
              LOS REGISTROS RELACIONADOS CON ESTA COMPRA, INCLUYENDO LA DISMINUCIÓN EN LA CANTIDAD DE PRODUCTOS
              INGRESADOS A LAS TIENDAS.</p>
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
     * Fecha 15/09/2017
     */
    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });

    /**
     * Lista las compras llamanto por ajax al metodo lista del controlador CompraController.
     * Fecha 15/09/2017
    */
    var grid = $("#tblCompras").bootgrid({
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
      url: "{{url('listar-compras')}}",
      formatters: {
        "commands": function(column, row){
          return "<button type='button' class='btn btn-xs btn-info command-show' data-row-id='"+row.id+"' style='margin:2px'>"+
            "<span class='fa fa-eye'></span></button>"+
            "<a href='compra/"+row.id+"/edit' class='btn btn-warning btn-xs' style='margin:2px'>"+
              "<span class='glyphicon glyphicon-edit'></span>"+
            "</a>"+
            "<button type='button' class='btn btn-xs btn-danger command-delete' data-row-id='"+row.id+"' style='margin:2px'>"+
              "<span class='fa fa-trash'></span></button>";
        }
      }
    }).on("loaded.rs.jquery.bootgrid", function(){
      /* Se ejecuta despues de cargar y procesar los datos */
      grid.find(".command-show").on("click", function(e){
        $.post("{{url('buscar-compra')}}", {id: $(this).data("row-id")}, function(data, textStatus, xhr) {
          $(".recibo").html(data['compra']['numero']);
          $(".fecha").html(data['compra']['created_at']);
          $(".prov_ruc").html(data['empresa']['ruc']);
          $(".prov_nombre").html(data['empresa']['nombre']);
          $(".prov_direccion").html(data['empresa']['direccion']);
          $(".prov_telefono").html(data['proveedor']['telefono']);
          $(".prov_representante").html(data['proveedor']['representante']);
          $(".usu_dni").html(data['persona']['dni']);
          $(".usu_nombres_apellidos").html(data['persona']['nombres'] + " " + data['persona']['apellidos']);
          $(".usu_direccion").html(data['persona']['direccion']);
          $(".usu_telefono").html(data['persona']['telefono']);
          $(".detalles").html(data['detalles']);
          $("#ver").modal('show');
        });
      }).end().find(".command-delete").on('click', function(e) {
        $.post("{{url('buscar-compra')}}", {id: $(this).data("row-id")}, function(data, textStatus, xhr) {
          $(".recibo").html(data['compra']['numero']);
          $("#frmEliminar").prop('action', "{{url('compra')}}/" + data['compra']['id']);
          $("#eliminar").modal('show');
        });
      });
    });



  });
</script>
@stop
