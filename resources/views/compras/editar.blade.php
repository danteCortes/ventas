@extends('plantillas.administrador')

@section('estilos')
<style media="screen">
  .modal-body>.table-responsive>.table-hover tr:hover{
    background-color: #e69c2d;
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
  <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
    <div class="panel panel-default">
      <div class="panel-heading" style="background-color:#575757; color: #FFF;">
        <h3 class="panel-title">Producto
          <button type="button" class="btn btn-xs btn-default pull-right" data-toggle="collapse" data-target="#panelBuscar"
             aria-controls="panelBuscar" id="btnbuscar">
            <span class="fa fa-minus"></span>
          </button>
        </h3>
      </div>
      {{Form::open(['url'=>'detalle', 'enctype'=>'multipart/form-data', 'id'=>'frmProducto'])}}
        {{ csrf_field() }}
        <div class="panel-body collapse in" id="panelBuscar" style="background-color:#bfbfbf;">
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
              <input type="text" name="codigo" class="form-control input-sm mayuscula" placeholder="CÓDIGO" id="codigo">
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
            <input type="text" name="descripcion" class="form-control input-sm mayuscula" placeholder="DESCRIPCIÓN"
              id="descripcion">
          </div>
          <div class="form-group">
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6" style="padding:0px; margin-bottom:15px;">
              <input type="text" name="precio" class="form-control input-sm moneda" placeholder="PRECIO DE VENTA" id="precio">
            </div>
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6" style="padding-right:0px; margin-bottom:15px;">
              <input type="file" name="foto" class="form-control input-sm" id="foto">
            </div>
          </div>
          <div class="form-group">
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6" style="padding:0px; margin-bottom:15px;">
              <input type="text" name="cantidad" class="form-control input-sm numero" placeholder="CANTIDAD" id="cantidad">
              <div class="modal fade" id="errorCantidad" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
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
            </div>
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6" style="padding-right:0px; margin-bottom:15px;">
              <input type="text" name="costo" class="form-control input-sm moneda" placeholder="COSTO UNITARIO" id="costoUnitario">
            </div>
          </div>
          <div class="form-group">
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6" style="padding:0px; margin-bottom:15px;">
              <input type="text" name="total" class="form-control input-sm moneda" placeholder="COSTO TOTAL" readonly id="costoTotal">
            </div><div class="col-xs-6 col-sm-6 col-md-6 col-lg-6" style="padding:0px; margin-bottom:15px; height: 30px;">
            </div>
          </div>
          <div class="form-group">
            <button type="submit" class="btn btn-sm btn-primary" id="btnAgregarDetalle">Agregar Detalle</button>
          </div>
        </div>
        <input type="hidden" value="2" name="tipo">
        <input type="hidden" value="{{$compra->id}}" name="compra">
      {{Form::close()}}
    </div>
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
  </div>
  <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
    <div class="panel panel-default">
      <div class="panel-heading" style="background-color:#575757; color: #FFF;">
        <h3 class="panel-title">Datos de la Compra
          <button type="button" class="btn btn-xs btn-default pull-right" data-toggle="collapse" data-target="#panelCompra"
             aria-controls="panelCompra" id="btnCompra">
            <span class="fa fa-minus"></span>
          </button>
        </h3>
      </div>
      {{Form::open(['url'=>'compra/'.$compra->id, 'method'=>'put'])}}
      {{ csrf_field() }}
      <div class="panel-body collapse in" id="panelCompra" style="background-color:#bfbfbf;">
        <div class="form-group">
          <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4" style="padding:0px; margin-bottom:15px;">
            <input type="text" name="ruc" class="form-control input-sm ruc" placeholder="RUC" id="ruc" required
              value="{{$compra->proveedor->empresa_ruc}}">
          </div>
          <div class="col-xs-8 col-sm-8 col-md-8 col-lg-8" style="padding-right:0px; margin-bottom:15px;">
            <input type="text" name="razonSocial" class="form-control input-sm mayuscula" placeholder="PROVEEDOR" id="razonSocial" required
              value="{{$compra->proveedor->empresa->nombre}}">
          </div>
        </div>
        <div class="form-group">
          <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="padding:0px; margin-bottom:15px;">
            <input type="text" name="direccion" class="form-control input-sm mayuscula" placeholder="DIRECCIÓN" id="direccion"
              value="{{$compra->proveedor->empresa->direccion}}">
          </div>
        </div>
        <div class="form-group">
          <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4" style="padding:0px; margin-bottom:15px;">
            <input type="text" name="telefono" class="form-control input-sm" placeholder="TELEFONO" id="telefono"
              value="{{$compra->proveedor->telefono}}">
          </div>
          <div class="col-xs-8 col-sm-8 col-md-8 col-lg-8" style="padding-right:0px; margin-bottom:15px;">
            <input type="text" name="representante" class="form-control input-sm mayuscula" placeholder="REPRESENTANTE" id="representante"
              value="{{$compra->proveedor->representante}}">
          </div>
        </div>
        <div class="form-group">
          <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6" style="padding:0px; margin-bottom:15px;">
            <input type="text" name="numero" class="form-control input-sm mayuscula" placeholder="NRO RECIBO" id="numero"
              value="{{$compra->numero}}">
          </div>
          <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6" style="padding:0px; margin-bottom:15px; height: 30px;">
          </div>
        </div>
        <div class="form-group">
          <button type="submit" class="btn btn-sm btn-primary" id="btnAgregarDetalle">Modificar Compra</button>
        </div>
      </div>
      {{Form::close()}}
    </div>
  </div>
</div>
<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
    <div class="table-responsive">
      <table class="table table-bordered">
        <thead>
          <tr>
            <th style="width:130px;">Operaciones</th>
            <th style="width:50px;">Cant.</th>
            <th>Código</th>
            <th>Descripción</th>
            <th>P. unit.</th>
            <th>P. Total</th>
          </tr>
        </thead>
        <tbody id="detalles">
          @foreach($compra->detalles as $detalle)
            <tr>
              <td style="width:123px;">
                {{Form::open(['url'=>'detalle/'.$detalle->id, 'method'=>'delete', 'class'=>'pull-left'])}}
                  {{ csrf_field() }}
                  <button class="btn btn-xs btn-danger">Quitar</button>
                {{Form::close()}}
                <button type="button" class="btn btn-xs{{(!\App\Ingreso::where('detalle_id', $detalle->id)->first())? " btn-warning":" btn-success"}}" data-toggle="modal" data-target="#tiendas_{{$detalle->id}}" style="margin-left:5px;">Tiendas</button>
                <div class="modal fade" id="tiendas_{{$detalle->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                  <div class="modal-dialog" role="document">
                    <div class="modal-content">
                      <div class="modal-header" style="background-color:#329a15; color:#fff;">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">AGREGAR PRODUCTOS A TIENDA - TOTAL {{$detalle->cantidad}}</h4>
                      </div>
                      @if(!\App\Ingreso::where('detalle_id', $detalle->id)->first())
                        {{Form::open(['url'=>'producto-tienda'])}}
                        <div class="modal-body" style="background-color:#e69c2d">
                          <div class="panel" style="background-color:#bd7406">
                            <div class="panel-body">
                              <div class="table-responsive">
                                <table class="table table-condensed table-bordered">
                                  <thead>
                                    <tr>
                                      <th style="text-align:center">Tienda</th>
                                      <th style="width:50px">Cantidad</th>
                                      <th>Ubicación</th>
                                    </tr>
                                  </thead>
                                      <tbody>
                                        @foreach(\App\Tienda::all() as $tienda)
                                          <tr>
                                            <th style="text-align:right; padding-right:15px; padding-top:10px;">{{$tienda->nombre}}</th>
                                            <td>
                                              <input type="text" name="cantidades[{{$tienda->id}}]" class="form-control input-sm"
                                                style="width:50px" required>
                                            </td>
                                            <td>
                                              <input type="text" name="ubicaciones[{{$tienda->id}}]" class="form-control input-sm mayuscula">
                                            </td>
                                          </tr>
                                        @endforeach
                                      </tbody>
                                </table>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="modal-footer" style="background-color:#329a15">
                          <button type="button" class="btn btn-default" data-dismiss="modal">
                            <span class="glyphicon glyphicon-ban-circle"></span> Cancelar</button>
                          <button type="submit" class="btn btn-primary" id="btnAgregarTiendas">
                            <span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
                        </div>
                        <input type="hidden" name="detalle_id" value="{{$detalle->id}}">
                        {{Form::close()}}
                      @else
                        {{Form::open(['url'=>'producto-tienda'])}}
                        <div class="modal-body" style="background-color:#e69c2d">
                          <div class="panel" style="background-color:#bd7406">
                            <div class="panel-body">
                              <div class="table-responsive">
                                <table class="table table-condensed table-bordered">
                                  <thead>
                                    <tr>
                                      <th style="text-align:center">Tienda</th>
                                      <th style="width:50px">Cantidad</th>
                                      <th>Ubicación</th>
                                    </tr>
                                  </thead>
                                      <tbody>
                                        @foreach(\App\Tienda::all() as $tienda)
                                          <tr>
                                            <th style="text-align:right; padding-right:15px; padding-top:10px;">{{$tienda->nombre}}</th>
                                            <td>
                                              <input type="text" name="cantidades[{{$tienda->id}}]" class="form-control input-sm"
                                                style="width:50px" required value="{{\App\Ingreso::where('detalle_id', $detalle->id)->where('producto_tienda_id', \App\ProductoTienda::where('producto_codigo', $detalle->producto_codigo)->where('tienda_id', $tienda->id)->first()->id)->first()->cantidad}}">
                                            </td>
                                            <td>
                                              <input type="text" name="ubicaciones[{{$tienda->id}}]" class="form-control input-sm mayuscula"
                                                value="{{\App\ProductoTienda::where('producto_codigo', $detalle->producto_codigo)->where('tienda_id', $tienda->id)->first()->ubicacion}}">
                                            </td>
                                          </tr>
                                        @endforeach
                                      </tbody>
                                </table>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="modal-footer" style="background-color:#329a15">
                          <button type="button" class="btn btn-default" data-dismiss="modal">
                            <span class="glyphicon glyphicon-ban-circle"></span> Cancelar</button>
                          <button type="submit" class="btn btn-primary" id="btnAgregarTiendas">
                            <span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
                        </div>
                        <input type="hidden" name="detalle_id" value="{{$detalle->id}}">
                        {{Form::close()}}
                      @endif
                    </div>
                  </div>
                </div>
              </td>
              <td>{{$detalle->cantidad}}</td>
              <td>{{$detalle->producto->codigo}}</td>
              <td>{{$detalle->producto->familia->nombre}} {{$detalle->producto->marca->nombre}} {{$detalle->producto->descripcion}}</td>
              <td style="text-align: right;">{{$detalle->precio_unidad}}</td>
              <td style="text-align: right;">{{$detalle->total}}</td>
            </tr>
          @endforeach
          <tr>
            <th colspan="5" style="text-align: right;">Total</th>
            <td style="text-align: right;">{{$compra->total}}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</div>
@stop

@section('scripts')
<script type="text/javascript">
  $(document).ready(function() {

    $("#frmProducto").keypress(function(event) {
      if (event.which == 13) {
        return false;
      }
    });

    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });

    $("#btnLinea").click(function() {
      $.post("{{url('linea')}}", {nombre: $("#nombreLinea").val()}, function(data, textStatus, xhr) {
        $("#linea_id").html(data);
      });
      $('#nuevaLinea').modal('hide');
    });

    $('#nuevaLinea').on('shown.bs.modal', function () {
      $('#nombreLinea').val("");
      $('#nombreLinea').focus();
    });

    $("#btnFamilia").click(function() {
      $.post("{{url('familia')}}", {nombre: $("#nombreFamilia").val()}, function(data, textStatus, xhr) {
        $("#familia_id").html(data);
      });
      $('#nuevaFamilia').modal('hide');
    });

    $('#nuevaFamilia').on('shown.bs.modal', function () {
      $('#nombreFamilia').val("");
      $('#nombreFamilia').focus();
    });

    $("#btnMarca").click(function() {
      $.post("{{url('marca')}}", {nombre: $("#nombreMarca").val()}, function(data, textStatus, xhr) {
        $("#marca_id").html(data);
      });
      $('#nuevaMarca').modal('hide');
    });

    $('#nuevaMarca').on('shown.bs.modal', function () {
      $('#nombreMarca').val("");
      $('#nombreMarca').focus();
    });

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
        $("#mensaje").html("<p>DEBE ESCOGER UNA LINEA, FAMILIA Y MARCA PARA EL PRODUCTO.</p>");
        $('#errorCantidad').modal('show');
      }
    });

    $("#costoUnitario").keyup(function() {
      if ($("#cantidad").val()) {

        $("#costoTotal").val($(this).val()*$("#cantidad").val());
      }else {
        $("#mensaje").html("<p>EL CAMPO CANTIDAD DEBE CONTENER UN VALOR NUMÉRICO.</p>");
        $('#errorCantidad').modal('show');
        $(this).val("");
      }
    });

    $("#costoTotal").keyup(function() {
      if ($("#cantidad").val()) {

        $("#costoUnitario").val(Math.round($(this).val()/$("#cantidad").val()));
      }else {
        $("#mensaje").html("<p>EL CAMPO CANTIDAD DEBE CONTENER UN VALOR NUMÉRICO.</p>");
        $('#errorCantidad').modal('show');
        $(this).val("");
      }
    });

    $("#cantidad").change(function() {
      $("#costoTotal").val("");
      $("#costoUnitario").val("");
    });

    // $("#codigo").change(function(){
    //   $.post(
    //     "{{url('buscar-producto')}}",
    //     {
    //       codigo: $("#codigo").val()
    //     },
    //     function(data, textStatus, xhr){
    //       if (data['producto'] != 0) {
    //
    //         $("#descripcion").val(data['producto']['descripcion']);
    //         $("#precio").val(data['producto']['precio']);
    //         $("#linea_id").html(data['linea']);
    //         $("#familia_id").html(data['familia']);
    //         $("#marca_id").html(data['marca']);
    //         $("#imgProducto").html(data['foto']);
    //       }else{
    //
    //         $("#descripcion").val("");
    //         $("#precio").val("");
    //         $("#linea_id").html(data['linea']);
    //         $("#familia_id").html(data['familia']);
    //         $("#marca_id").html(data['marca']);
    //         $("#imgProducto").html(data['foto']);
    //       }
    //     }
    //   );
    // });

    $("#codigo").keypress(function(event) {
      if (event.which == 13) {
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
      }
    });

    $("#btnAgregarDetalle").click(function(){
      if (!$("#codigo").val()) {
        $("#mensaje").html("<p>EL CAMPO CÓDIGO ES REQUERIDO.</p>");
        $('#errorCantidad').modal('show');
        return false;
      }if (!$("#linea_id").val()) {
        $("#mensaje").html("<p>DEBE ELEGIR UNA LINEA PARA EL PRODUCTO.</p>");
        $('#errorCantidad').modal('show');
        return false;
      }if (!$("#familia_id").val()) {
        $("#mensaje").html("<p>DEBE ELEGIR UNA FAMILIA PARA EL PRODUCTO.</p>");
        $('#errorCantidad').modal('show');
        return false;
      }if (!$("#marca_id").val()) {
        $("#mensaje").html("<p>DEBE ELEGIR UNA MARCA PARA EL PRODUCTO.</p>");
        $('#errorCantidad').modal('show');
        return false;
      }if (!$("#descripcion").val()) {
        $("#mensaje").html("<p>EL CAMPO DESCRIPCION NO DEBE ESTAR VACIO.</p>");
        $('#errorCantidad').modal('show');
        return false;
      }if (!$("#precio").val()) {
        $("#mensaje").html("<p>EL CAMPO PRECIO NO DEBE ESTAR VACIO.</p>");
        $('#errorCantidad').modal('show');
        return false;
      }if (!$("#cantidad").val()) {
        $("#mensaje").html("<p>EL CAMPO CANTIDAD NO DEBE ESTAR VACIO.</p>");
        $('#errorCantidad').modal('show');
        return false;
      }if (!$("#costoUnitario").val()) {
        $("#mensaje").html("<p>EL CAMPO COSTO UNITARIO NO DEBE ESTAR VACIO.</p>");
        $('#errorCantidad').modal('show');
        return false;
      }
    });

    $("#ruc").change(function() {

      $.post("{{url('buscar-proveedor')}}", {ruc: $(this).val()}, function(data, textStatus, xhr) {
        $("#razonSocial").val(data['empresa']['nombre']);
        $("#direccion").val(data['empresa']['direccion']);
        $("#telefono").val(data['proveedor']['telefono']);
        $("#representante").val(data['proveedor']['representante']);
      });
    });

    $('.moneda').mask("# ##0.00", {reverse: true});
    $('.numero').mask("###", {reverse: true});
    $('.ruc').mask("99999999999", {reverse: true});

  });
</script>
@stop
