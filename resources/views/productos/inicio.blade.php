@extends('plantillas.administrador')

@section('estilos')
{{Html::style('bootgrid/jquery.bootgrid.min.css')}}
@stop

@section('titulo')
Producto
<!--Boton para mostrar el modal con el formulario para ingresar los datos de un nuevo producto-->
<!--Fecha 13/09/2017-->
<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#nuevo">
  <span class="glyphicon glyphicon-plus"></span> Nuevo
</button>
<!--Modal con el formulario para ingresar los datos del nuevo producto-->
<!--Fecha 13/09/2017-->
<div class="modal fade" id="nuevo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      {{Form::open(['url'=>'producto', 'enctype'=>'multipart/form-data'])}}
      {{ csrf_field() }}
      <div class="modal-header" style="background-color:#385a94; color:#fff;">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">NUEVO PRODUCTO</h4>
      </div>
      <div class="modal-body" style="background-color:#e69c2d">
        <div class="panel" style="background-color:#bd7406">
          <div class="panel-body">
            <div class="form-group">
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <div class="img-responsive" style="text-align:center;" id="imgProducto">
                  <img src="{{url('storage/productos/producto.png')}}" alt="" class="img-responsive img-thumbnail"
                    style="height:100px; margin-bottom:10px;">
                </div>
              </div>
            </div>
            <div class="form-group">
              <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6" style="padding:0px; margin-bottom:15px;">
                <input type="text" class="form-control mayuscula input-sm" placeholder="CÓDIGO*" required name="codigo" id="codigo">
              </div>
              <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6" style="padding-right:0px; margin-bottom:15px;">
                <button type="button" class="btn btn-sm btn-primary" id="btnGenerarCodigo">Generar Código</button>
              </div>
            </div>
            <div class="form-group">
              <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6" style="padding:0px; margin-bottom:15px;">
                <select class="form-control input-sm" name="linea_id" id="linea_id">
                  <option value="">SELECCIONAR LÍNEA</option>
                  @foreach(\App\Linea::all() as $linea)
                  <option value="{{$linea->id}}">{{$linea->nombre}}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6" style="margin-bottom:15px;">
                <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#nuevaLinea">
                  <span class="glyphicon glyphicon-plus"></span> Línea
                </button>
              </div>
            </div>
            <div class="form-group">
              <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6" style="padding:0px; margin-bottom:15px;">
                <select class="form-control input-sm" name="familia_id" id="familia_id">
                  <option value="">SELECCIONAR FAMILIA</option>
                  @foreach(\App\Familia::all() as $familia)
                  <option value="{{$familia->id}}">{{$familia->nombre}}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6" style="margin-bottom:15px;">
                <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#nuevaFamilia">
                  <span class="glyphicon glyphicon-plus"></span> Familia
                </button>
              </div>
            </div>
            <div class="form-group">
              <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6" style="padding:0px; margin-bottom:15px;">
                <select class="form-control input-sm" name="marca_id" id="marca_id">
                  <option value="">SELECCIONAR MARCA</option>
                  @foreach(\App\Marca::all() as $marca)
                  <option value="{{$marca->id}}">{{$marca->nombre}}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6" style="margin-bottom:15px;">
                <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#nuevaMarca">
                  <span class="glyphicon glyphicon-plus"></span> Marca
                </button>
              </div>
            </div>
            <div class="form-group">
              <input type="text" class="form-control mayuscula input-sm" placeholder="DESCRIPCIÓN" name="descripcion" id="descripcion"
                required>
            </div>
            <div class="form-group">
              <input type="text" class="form-control moneda input-sm" placeholder="PRECIO" name="precio" id="precio">
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
  <div class="col-xs-12 col-sm-12 col-md-8 col-md-offset-2 col-lg-8 col-lg-offset-2">
    <div class="table-responsive">
      <table class="table table-hover table-condensed table-bordered" id="tblProductos">
        <thead>
          <tr class="info">
            <th data-column-id="codigo" data-order="desc">Código</th>
            <th data-column-id="descripcion">Descripción</th>
            <th data-column-id="precio">Precio Venta</th>
            <th data-column-id="commands" data-formatter="commands" data-sortable="false">Operaciones</th>
          </tr>
        </thead>
      </table>
    </div>
  </div>
</div>
<!--Modal que muestra algunos errores del sistema.-->
<!--Fecha 13/09/2017-->
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
<!--Modal con formulario para ingresar una nueva línea de producto-->
<!--Fecha 13/09/2017-->
<div class="modal fade" id="nuevaLinea" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header" style="background-color:#385a94; color:#fff;">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">NUEVA LÍNEA</h4>
      </div>
      <div class="modal-body" style="background-color:#e69c2d">
        <div class="panel" style="background-color:#bd7406">
          <div class="panel-body">
            <div class="form-group">
              <input type="text" class="form-control mayuscula input-sm" placeholder="NOMBRE*" required name="nombreLinea" id="nombreLinea">
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer" style="background-color:#385a94">
        <button type="button" class="btn btn-default" data-dismiss="modal">
          <span class="glyphicon glyphicon-ban-circle"></span> Cancelar</button>
        <button type="button" class="btn btn-primary" id="btnLinea">
          <span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
      </div>
    </div>
  </div>
</div>
<!--Modal con formulario para ingresar una nueva familia de producto-->
<!--Fecha 13/09/2017-->
<div class="modal fade" id="nuevaFamilia" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header" style="background-color:#385a94; color:#fff;">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">NUEVA FAMILIA</h4>
      </div>
      <div class="modal-body" style="background-color:#e69c2d">
        <div class="panel" style="background-color:#bd7406">
          <div class="panel-body">
            <div class="form-group">
              <input type="text" class="form-control mayuscula input-sm" placeholder="NOMBRE*" required name="nombre"
                id="nombreFamilia">
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer" style="background-color:#385a94">
        <button type="button" class="btn btn-default" data-dismiss="modal">
          <span class="glyphicon glyphicon-ban-circle"></span> Cancelar</button>
        <button type="button" class="btn btn-primary" id="btnFamilia">
          <span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
      </div>
    </div>
  </div>
</div>
<!--Modal con formulario para ingresar una nueva marca para los productos-->
<!--Fecha 13/09/2017-->
<div class="modal fade" id="nuevaMarca" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header" style="background-color:#385a94; color:#fff;">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">NUEVA MARCA</h4>
      </div>
      <div class="modal-body" style="background-color:#e69c2d">
        <div class="panel" style="background-color:#bd7406">
          <div class="panel-body">
            <div class="form-group">
              <input type="text" class="form-control mayuscula input-sm" placeholder="NOMBRE*" required name="nombre"
              id="nombreMarca">
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer" style="background-color:#385a94">
        <button type="button" class="btn btn-default" data-dismiss="modal">
          <span class="glyphicon glyphicon-ban-circle"></span> Cancelar</button>
        <button type="button" class="btn btn-primary" id="btnMarca">
          <span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
      </div>
    </div>
  </div>
</div>
<!--Modal con los datos del producto-->
<!--Fecha 14/09/2017-->
<div class="modal fade" id="ver" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header" style="background-color:#31b0d5; color:#fff;">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">DATOS DEL PRODUCTO</h4>
      </div>
      <div class="modal-body" style="background-color:#e69c2d">
        <div class="img-resposive imgMostrarProducto" style="text-align:center">
        </div>
        <div class="table-responsive" style="background-color:#bd7406">
          <table class="table table-condensed table-bordered table-hover" style="margin:0px; background-color:#bfbfbf;">
            <tr>
              <th>Código: </th>
              <td class="codigo"></td>
            </tr>
            <tr>
              <th>Línea: </th>
              <td class="linea"></td>
            </tr>
            <tr>
              <th>Familia: </th>
              <td class="familia"></td>
            </tr>
            <tr>
              <th>Marca: </th>
              <td class="marca"></td>
            </tr>
            <tr>
              <th>Descripción: </th>
              <td class="descripcion"></td>
            </tr>
            <tr>
              <th>Precio: </th>
              <td class="precio"></td>
            </tr>
            <tr>
              <th>Stock: </th>
              <td>
                <table class="table table-condensed table-bordered stock" style="background-color:#385a94; color:#fff;">
                  
                </table>
              </td>
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
<!--Modal con el formulario para modificar los datos del producto-->
<!--Fecha 14/09/2017-->
<div class="modal fade" id="editar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      {{Form::open(['id'=>'frmEditar', 'method'=>'put', 'enctype'=>'multipart/form-data'])}}
      {{ csrf_field() }}
      <div class="modal-header" style="background-color:#385a94; color:#fff;">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">MODIFICAR PRODUCTO</h4>
      </div>
      <div class="modal-body" style="background-color:#e69c2d">
        <div class="panel" style="background-color:#bd7406">
          <div class="panel-body">
            <div class="form-group">
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <div class="img-responsive imgMostrarProducto" style="text-align:center;">
                </div>
              </div>
            </div>
            <div class="form-group">
              <input type="text" class="form-control input-sm mayuscula codigo" name="codigo">
            </div>
            <div class="form-group">
              <select class="form-control input-sm linea" name="linea_id">
              </select>
            </div>
            <div class="form-group">
              <select class="form-control input-sm familia" name="familia_id">
              </select>
            </div>
            <div class="form-group">
              <select class="form-control input-sm marca" name="marca_id">
              </select>
            </div>
            <div class="form-group">
              <input type="text" class="form-control mayuscula input-sm descripcion" placeholder="DESCRIPCIÓN" name="descripcion"
                required>
            </div>
            <div class="form-group">
              <input type="text" class="form-control moneda input-sm precio" placeholder="PRECIO" name="precio">
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
<!--Modal de advertencia antes de eliminar el producto-->
<!--Fecha 14/09/2017-->
<div class="modal fade" id="eliminar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      {{Form::open(['id'=>'frmEliminar', 'method'=>'delete'])}}
      {{ csrf_field() }}
      <div class="modal-header" style="background-color:#bb0000; color:#fff;">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">ELIMINAR PRODUCTO</h4>
      </div>
      <div class="modal-body" style="background-color:#e69c2d">
        <div class="panel" style="background-color:#bd7406">
          <div class="panel-body">
            <p>ESTA A PUNTO DE ELIMINAR EL PRODUCTO <strong class="codigo"></strong>, CON ESTA ACCIÓN ELIMINARÁ TODOS
              LOS REGISTROS RELACIONADOS CON ESTE PRODUCTO INCLUYENDO SUS EXISTENCIAS E INGRESOS A LAS TIENDAS;
              DETALLES DE VENTAS, COMPRAS, CRÉDITOS Y PRÉSTAMOS.</p>
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
     * Fecha 13/09/2017
     */
    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });

    /**
     * Lista los productos llamanto por ajax al metodo lista del controlador ProveedorController.
     * Fecha 14/09/2017
    */
    var grid = $("#tblProductos").bootgrid({
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
      url: "{{url('listar-productos')}}",
      formatters: {
        "commands": function(column, row){
          return "<button type='button' class='btn btn-xs btn-info command-show' data-row-codigo='"+row.codigo+"' style='margin:2px'>"+
            "<span class='fa fa-eye'></span></button>"+
            "<button type='button' class='btn btn-xs btn-warning command-edit' data-row-codigo='"+row.codigo+"' style='margin:2px'>"+
              "<span class='fa fa-edit'></span></button>"+
            "<button type='button' class='btn btn-xs btn-danger command-delete' data-row-codigo='"+row.codigo+"' style='margin:2px'>"+
              "<span class='fa fa-trash'></span></button>";
        }
      }
    }).on("loaded.rs.jquery.bootgrid", function(){
      /* Se ejecuta despues de cargar y procesar los datos */
      grid.find(".command-show").on("click", function(e){
        $.post("{{url('buscar-producto')}}", {codigo: $(this).data("row-codigo")}, function(data, textStatus, xhr) {
          $(".imgMostrarProducto").html(data['foto']);
          $(".codigo").html(data['producto']['codigo']);
          $(".linea").html(data['linea'][1]['nombre']);
          $(".familia").html(data['familia'][1]['nombre']);
          $(".marca").html(data['marca'][1]['nombre']);
          $(".descripcion").html(data['producto']['descripcion']);
          $(".precio").html(data['producto']['precio']);
          $(".stock").html(data['stock']);
          $("#ver").modal('show');
        });
      }).end().find(".command-edit").on("click", function(e){
        $.post("{{url('buscar-producto')}}", {codigo: $(this).data("row-codigo")}, function(data, textStatus, xhr) {
          $(".imgMostrarProducto").html(data['foto']);
          $(".codigo").val(data['producto']['codigo']);
          $(".linea").html(data['linea'][0]);
          $(".familia").html(data['familia'][0]);
          $(".marca").html(data['marca'][0]);
          $(".descripcion").val(data['producto']['descripcion']);
          $(".precio").val(data['producto']['precio']);
          $("#frmEditar").prop('action', "{{url('producto')}}/" + data['producto']['codigo']);
          $("#editar").modal('show');
        });
      }).end().find(".command-delete").on('click', function(e) {
        $.post("{{url('buscar-producto')}}", {codigo: $(this).data("row-codigo")}, function(data, textStatus, xhr) {
          $(".codigo").html(data['producto']['codigo']);
          $("#frmEliminar").prop('action', "{{url('producto')}}/" + data['producto']['codigo']);
          $("#eliminar").modal('show');
        });
      });
    });

    /*
     * Genera un código para el producto a partir de la linea, familia y la marca
     * que se le propone antes de generar.
     * Se envian esos datos a la función generarCodigo del contolador ProductoController por un método post.
     * Fecha 13/09/2017
     */
    $("#btnGenerarCodigo").click(function() {
      if ($("#linea_id").val() && $("#familia_id").val() && $("#marca_id").val()) {

        $.post("{{url('generar-codigo')}}",
          {
            linea_id: $("#linea_id").val(),
            familia_id: $("#familia_id").val(),
            marca_id: $("#marca_id").val()
          }, function(data, textStatus, xhr) {

          $("#codigo").val(data);
        });
      }else{
        $("#nuevo").modal('hide');
        $("#mensaje").html("<p>DEBE ESCOGER UNA LINEA, FAMILIA Y MARCA PARA EL PRODUCTO.</p>");
        $('#errores').modal('show');
      }
    });

    /*
     * Llama al método create del controlador LineaController por medio del metodo post
     * de ajax para guardar una nueva linea.
     * Fecha 13/09/2017
    */
    $("#btnLinea").click(function() {
      $.post("{{url('linea')}}", {nombre: $("#nombreLinea").val()}, function(data, textStatus, xhr) {
        $("#linea_id").html(data);
      });
      $('#nuevaLinea').modal('hide');
    });

    /*
     * Limpia el campo nombre del formulario para ingresar una nueva Linea y lo enfoca.
     * Fecha 13/09/2017
    */
    $('#nuevaLinea').on('shown.bs.modal', function () {
      $('#nombreLinea').val("");
      $('#nombreLinea').focus();
    });

    /*
     * Llama al método create del controlador FamiliaaController por medio del metodo post
     * de ajax para guardar una nueva familia.
     * Fecha 13/09/2017
    */
    $("#btnFamilia").click(function() {
      $.post("{{url('familia')}}", {nombre: $("#nombreFamilia").val()}, function(data, textStatus, xhr) {
        $("#familia_id").html(data);
      });
      $('#nuevaFamilia').modal('hide');
    });

    /*
     * Limpia el campo nombre del formulario para ingresar una nueva familia y lo enfoca.
     * Fecha 13/09/2017
    */
    $('#nuevaFamilia').on('shown.bs.modal', function () {
      $('#nombreFamilia').val("");
      $('#nombreFamilia').focus();
    });

    /*
     * Llama al método create del controlador MarcaController por medio del metodo post
     * de ajax para guardar una nueva marca.
     * Fecha 13/09/2017
    */
    $("#btnMarca").click(function() {
      $.post("{{url('marca')}}", {nombre: $("#nombreMarca").val()}, function(data, textStatus, xhr) {
        $("#marca_id").html(data);
      });
      $('#nuevaMarca').modal('hide');
    });

    /*
     * Limpia el campo nombre del formulario para ingresar una nueva marca y lo enfoca.
     * Fecha 13/09/2017
    */
    $('#nuevaMarca').on('shown.bs.modal', function () {
      $('#nombreMarca').val("");
      $('#nombreMarca').focus();
    });

    /*
     * Llama al método change del campo codigo para buscar un producto con ese codigo
     * enviandolo por ajax al método buscarProducto del controlador ProductoController.
     * Luego rellena los campor correspondientes si encuentra un producto con ese código.
     * Fecha 13/09/2017
    */
    $("#codigo").change(function(){
      $.post(
        "{{url('buscar-producto')}}",
        {
          codigo: $("#codigo").val()
        },
        function(data, textStatus, xhr){
          if (data['producto'] != 0) {

            $("#descripcion").val(data['producto']['descripcion']);
            $("#precio").val(data['producto']['precio']);
            $("#linea_id").html(data['linea']);
            $("#familia_id").html(data['familia']);
            $("#marca_id").html(data['marca']);
            $("#imgProducto").html(data['foto']);
          }else{

            $("#descripcion").val("");
            $("#precio").val("");
            $("#linea_id").html(data['linea']);
            $("#familia_id").html(data['familia']);
            $("#marca_id").html(data['marca']);
            $("#imgProducto").html(data['foto']);
          }
        }
      );
    });

    /*
     * Define una máscara de moneda para los campos necesarios con valor de moneda.
    */
    $('.moneda').mask("# ##0.00", {reverse: true});

  });
</script>
@stop
