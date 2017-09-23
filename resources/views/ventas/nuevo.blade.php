@extends('plantillas.cajero')

@section('titulo')
Ventas
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
  <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
    <div class="panel panel-default">
      <div class="panel-heading" style="background-color:#575757; color: #FFF;">
        <h3 class="panel-title">Buscar Producto
          <button type="button" class="btn btn-xs btn-default pull-right" data-toggle="collapse" data-target="#panelBuscar"
             aria-controls="panelBuscar" id="btnbuscar">
            <span class="fa fa-minus"></span>
          </button>
        </h3>
      </div>
      <div class="panel-body collapse in" id="panelBuscar" style="background-color:#bfbfbf;">
          <div class="input-group">
            <span class="input-group-addon">Código <span class="fa fa-barcode"></span></span>
            <input type="text" class="form-control" placeholder="CÓDIGO" id="txtCodigo">
            <span class="input-group-btn">
              <input type="hidden" name="tienda_id" value="{{Auth::user()->tienda_id}}" id="tienda_id">
              <button class="btn btn-default" type="button"><span class="fa fa-search"></span></button>
            </span>
          </div>
      </div>
    </div>
  </div>
  <div class="col-xs-12 col-sm-12 col-md-8 col-lg-8">
    <div class="panel panel-default">
      <div class="panel-heading" style="background-color:#575757; color: #FFF;">
        <h3 class="panel-title">Agregar Producto
          <button type="button" class="btn btn-xs btn-default pull-right" data-toggle="collapse" data-target="#panelAgregar"
             aria-controls="panelAgregar" id="btnAgregar">
            <span class="fa fa-minus"></span>
          </button>
        </h3>
      </div>
      <div class="panel-body collapse in" id="panelAgregar" style="background-color:#bfbfbf;">
        <div class="row">
          <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div class="table-resposive">
              <table class="table table-bordered table-condensed">
                <thead>
                  <tr>
                    <th>Código</th>
                    <th>Descripción</th>
                    <th style="width:80px;">P. Venta</th>
                    <th>Stock</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td class="codigo"></td>
                    <td class="descripcion"></td>
                    <td class="precio"></td>
                    <td class="cantidad"></td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 foto">
            <img src="{{url('storage/productos').'/producto.png'}}" style="width:100px;">
          </div>
          {{Form::open(['url'=>'detalle'])}}
            {{ csrf_field() }}
            {{Form::hidden('producto_codigo', null, ['id'=>'producto_codigo', 'required'=>''])}}
            {{Form::hidden('stock', null, ['id'=>'stock', 'required'=>''])}}
            {{Form::hidden('tipo', 1)}}
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
              <div class="input-group">
                <span class="input-group-addon">Prec. Unit.: S/</span>
                <input type="text" class="form-control precio" placeholder="PRECIO" id="precio_venta" data-mask="##9.00" name="precio_unidad" required>
              </div>
              <div class="input-group">
                <span class="input-group-addon">Cantidad: </span>
                <input type="text" class="form-control" placeholder="CANTIDAD" name="cantidad" required>
                <span class="input-group-btn">
                  <button class="btn btn-default" type="submit"><span class="fa fa-check"></span> Agregar</button>
                </span>
              </div>
            </div>
          {{Form::close()}}
        </div>
      </div>
    </div>
  </div>
</div>
<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
    <div class="table-responsive">
      <table class="table table-condensed table-bordered" style="background-color:#bfbfbf;">
        <thead>
          <tr>
            <th style="width:80px;">Operación</th>
            <th style="width:60px;">Cant.</th>
            <th>Descripción</th>
            <th style="width:80px;">P. Unit.</th>
            <th style="width:80px;">Total</th>
          </tr>
        </thead>
        <tbody>
          @if($venta = \App\Venta::where('usuario_id', Auth::user()->id)->where('tienda_id', Auth::user()->tienda_id)->where('estado', 1)->first())
            @foreach($venta->detalles as $detalle)
              <tr>
                <td><button type="button" class="btn btn-xs btn-danger">Quitar</button></td>
                <td>{{$detalle->cantidad}}</td>
                <td>{{$detalle->producto->descripcion}}</td>
                <td style="text-align:right">{{$detalle->precio_unidad}}</td>
                <td style="text-align:right">{{$detalle->total}}</td>
              </tr>
            @endforeach
          <tr>
            <td colspan="4"><strong class="pull-right">TOTAL: </strong></td>
            <td style="text-align:right">{{$venta->total}}</td>
          </tr>
          @endif
        </tbody>
      </table>
    </div>
  </div>
</div>
<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
    <div class="panel panel-default">
      <div class="panel-body" style="background-color:#bfbfbf;">
        <div class="row">
          <div class="col-xs-12 col-sm-12 col-md-6 col-lg-4">
            <div class="form-group">
              <div class="input-group">
                <input type="text" name="documento" class="form-control" placeholder="DNI/RUC" data-mask="99999999999" id="documento">
                <span class="input-group-btn">
                  <button class="btn btn-default" type="button"><span class="fa fa-search"></span></button>
                </span>
              </div>
            </div>
          </div>
          <div class="col-xs-12 col-sm-12 col-md-6 col-lg-8">
            <div class="form-group">
              <input type="text" name="nombre" class="form-control" placeholder="RAZÓN SOCIAL" id="nombre" readonly>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-xs-12 col-sm-12 col-md-4 col-lg-3">
            <div class="form-group">
              <input type="text" name="nombres" class="form-control" placeholder="NOMBRES" id="nombres" readonly>
            </div>
          </div>
          <div class="col-xs-12 col-sm-12 col-md-4 col-lg-3">
            <div class="form-group">
              <input type="text" name="apellidos" class="form-control" placeholder="APELLIDOS" id="apellidos" readonly>
            </div>
          </div>
          <div class="col-xs-12 col-sm-12 col-md-4 col-lg-6">
            <div class="form-group">
              <input type="text" name="direccion" class="form-control" placeholder="DIRECCIÓN" id="direccion" readonly>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
            <div class="form-group">
              <input type="text" name="soles" class="form-control moneda" placeholder="SOLES" id="efectivo">
            </div>
          </div>
          <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
            <div class="form-group">
              <div class="input-group">
                <input type="text" name="dolares" class="form-control moneda" placeholder="DOLARES" id="dolares">
                <span class="input-group-btn">
                  <button class="btn btn-default" type="button" id="btnTipoCambio">Tipo Cambio</button>
                </span>
              </div>
            </div>
          </div>
          <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
            <div class="form-group">
              <div class="input-group">
                <input type="text" name="tarjeta" class="form-control moneda" placeholder="TARJETA" id="tarjeta">
                <span class="input-group-btn">
                  <button class="btn btn-default" type="button">Registrar</button>
                </span>
              </div>
            </div>
          </div>
          <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
            <div class="form-group">
              <input type="text" name="vuelto" class="form-control" placeholder="VUELTO" readonly id="vuelto">
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-xs-6 col-sm-6 col-md-3 col-lg-3">
            <button type="button" class="btn btn-primary">Terminar</button>
            <button type="button" class="btn btn-warning">Cancelar</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<!--Modal que muestra algunos errores del sistema.-->
<!--Fecha 21/09/2017-->
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
<!--Modal con el formulario para ingresar el tipo de cambio.-->
<!--Fecha 21/09/2017-->
<div class="modal fade" id="tipoCambio" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header" style="background-color:#385a94; color:#fff;">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">CONFIGURAR TIPO DE CAMBIO</h4>
      </div>
      <div class="modal-body" style="background-color:#e69c2d">
        <div class="panel" style="background-color:#bd7406">
          <div class="panel-body">
            <p id="msjTipoCambio">DEBE CONFIGURAR EL TIPO DE CAMBIO DE DOLARES A SOLES. ESTO SOLO SE RELIAZA UNA VEZ,
              PARA ACTUALIZAR EL TIPO DE CAMBIO PULSE EL BOTÓN "Tipo Cambio".</p>
            {{Form::open(['class'=>'form-horizontal'])}}
              <div class="form-group">
                <label for="cambio" class="control-label col-xs-2 col-sm-2 col-md-3 col-lg-3">Cambio*:</label>
                <div class="col-xs-10 col-sm-10 col-md-9 col-lg-9">
                  <input type="text" name="cambio" class="form-control input-sm moneda" id="txtCambio">
                </div>
              </div>
              <div class="form-group">
                <div class="col-xs-10 col-xs-offset-2 col-sm-10 col-sm-offset-2 col-md-9 col-md-offset-3 col-lg-9 col-lg-offset-3">
                  {{Form::button('Guardar', ['class'=>'btn btn-primary btn-sm', 'id'=>'btnCambio'])}}
                </div>
              </div>
            {{Form::close()}}
          </div>
        </div>
      </div>
      <div class="modal-footer" style="background-color:#385a94; color:#fff;">
        <button type="button" class="btn btn-default" data-dismiss="modal">
          <span class="glyphicon glyphicon-ban-circle"></span> Cancelar</button>
      </div>
    </div>
  </div>
</div>
<!--Modal con el formulario para registrar la venta con tarjeta al sistema.-->
<!--Fecha 22/09/2017-->
<div class="modal fade" id="registrarTarjeta" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header" style="background-color:#385a94; color:#fff;">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">REGISTRAR VENTA CON TARJETA</h4>
      </div>
      <div class="modal-body" style="background-color:#e69c2d">
        <div class="panel" style="background-color:#bd7406">
          <div class="panel-body">
            <p id="msjRegistrarTarjeta"></p>
            {{Form::open(['class'=>'form-horizontal'])}}
            <div class="form-group">
              <label for="tarjeta_id" class="control-label col-xs-2 col-sm-2 col-md-3 col-lg-3">Tarjeta*:</label>
              <div class="col-xs-10 col-sm-10 col-md-9 col-lg-9">
                <select class="form-control input-sm" name="tarjeta_id" id="tarjeta_id">
                  <option value="">SELECCIONAR UNA OPCIÓN</option>
                  @foreach(\App\Tarjeta::all() as $tarjeta)
                    <option value="{{$tarjeta->id}}">{{$tarjeta->nombre}}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="form-group">
              <label for="operacion" class="control-label col-xs-2 col-sm-2 col-md-3 col-lg-3">Operación*:</label>
              <div class="col-xs-10 col-sm-10 col-md-9 col-lg-9">
                {{Form::text('operacion', null, ['class'=>'form-control input-sm mayuscula', 'id'=>'operacion'])}}
              </div>
            </div>
            <div class="form-group">
              <div class="col-xs-10 col-xs-offset-2 col-sm-10 col-sm-offset-2 col-md-9 col-md-offset-3 col-lg-9 col-lg-offset-3">
                {{Form::button('Guardar', ['class'=>'btn btn-primary btn-sm', 'id'=>'btnRegistrarTarjeta'])}}
              </div>
            </div>
            {{Form::close()}}
          </div>
        </div>
      </div>
      <div class="modal-footer" style="background-color:#385a94; color:#fff;">
        <button type="button" class="btn btn-default" data-dismiss="modal">
          <span class="glyphicon glyphicon-ban-circle"></span> Cancelar</button>
      </div>
    </div>
  </div>
</div>
@stop

@section('scripts')
{{Html::script('assets/lib/mask/jquery.mask.js')}}
<script type="text/javascript">
  $(document).ready(function() {

    /**
     * Al hacer clic en el botón btnTipoCambio, se muestra un modal con el tipo de cambio ya configurado
     * o vacio para configurar.
     * Fecha: 22/09/2017
    */
    $("#btnTipoCambio").click(function() {
      $.post("{{url('tipo-cambio')}}", {efectivo: $(this).val()}, function(data, textStatus, xhr) {
        // Si el retorno es 0, mostramos un modal para que configure el tipo de cambio.
        if (tipoCambio == 0) {
          $("#msjTipoCambio").html("DEBE CONFIGURAR EL TIPO DE CAMBIO DE DOLARES A SOLES. ESTO SOLO SE RELIAZA UNA VEZ,"+
            "PARA ACTUALIZAR EL TIPO DE CAMBIO PULSE EL BOTÓN \"Tipo Cambio\".");
          $("#txtCambio").val("");
        }else{
          $("#msjTipoCambio").html("EL TIPO DE CAMBIO DE DOLARES A SOLES ESTÁ CONFIGURADO ACTUELMENTE A "+data);
          $("#txtCambio").val(data);
        }
        $("#tipoCambio").modal("show");
      });
    });

    $('.moneda').mask("# ##0.00", {reverse: true});

    /**
     * Al cambiar el valor de la tarjeta, se tiene que registrar la venta por tarjeta con el sistema.
     * Para esto, mostramos el modal para que el usuario registre esa venta.
     * Fecha: 22/09/2017
    */
    $("#tarjeta").change(function() {
      $.post("{{url('tipo-cambio')}}", {efectivo: $(this).val()}, function(data, textStatus, xhr) {
        // Se muestra el modal con el formulario para registrar la venta con tarjeta con el sistema.
        $("#registrarTarjeta").modal("show");
      });
      $.post("{{url('vuelto')}}", {efectivo: $("#efectivo").val(), dolares: $("#dolares").val(), tarjeta: $(this).val()},
        function(data, textStatus, xhr) {
        $("#vuelto").val(data);
      });
    });

    $("#btnCambio").click(function() {
      if ($("#txtCambio").val() != "") {
        $.post("{{url('cambio')}}", {cambio: $("#txtCambio").val()}, function(data, textStatus, xhr) {
          $("#tipoCambio").modal("hide");
        });
      }
      $.post("{{url('vuelto')}}", {efectivo: $("#efectivo").val(), dolares: $("#dolares").val(), tarjeta: $(this).val()},
        function(data, textStatus, xhr) {
        $("#vuelto").val(data);
      });
    });

    /**
     * Verificamos si el tipo de cambio está configurado y posteriormente el vuelto del cliente.
     * Fecha: 21/09/2017
    */
    $("#dolares").change(function() {
      $.post("{{url('tipo-cambio')}}", {efectivo: $(this).val()}, function(data, textStatus, xhr) {
        // Si el retorno es 0, mostramos un modal para que configure el tipo de cambio.
        if (tipoCambio == 0) {
          $("#tipoCambio").modal("show");
        }
      });
      $.post("{{url('vuelto')}}", {efectivo: $("#efectivo").val(), dolares: $(this).val(), tarjeta: $("#tarjeta").val()},
        function(data, textStatus, xhr) {
        $("#vuelto").val(data);
      });
    });

    /**
     * Verificamos el estado o características del dinero entregado al cajer, si es mayor
     * al monto total de la venta se muestra el vuelto, de lo contrario, se muestra lo que falta en negativo.
     * Fecha: 21/09/2017
    */
    $("#efectivo").change(function() {
      $.post("{{url('vuelto')}}", {efectivo: $(this).val(), dolares: $("#dolares").val(), tarjeta: $("#tarjeta").val()}, function(data, textStatus, xhr) {
        $("#vuelto").val(data);
      });
    });

    /**
     * Busca un cliente, ya sea persona o empresa, si el campo queda vacio puede guardarse la venta.
     * Fecha: 21/09/2017
    */
    $("#documento").change(function(){
      // Verificamos si el campo está vacio.
      if ($(this).val() != "") {
        // Verificamos si es un número de DNI, o RUC.
        if($(this).val().length == 8){
          // Buscarmos los datos de la persona que tenga este dni, si existe mostramos los datos, de lo contrario
          // solo activamos los inputs para que igresen los datos del nuevo cliente.
          $.post("{{url('buscar-persona')}}", {dni: $(this).val()}, function(data, textStatus, xhr) {
            $("#nombre").prop('readonly', true);
            $("#nombres").prop('readonly', false);
            $("#apellidos").prop('readonly', false);
            $("#direccion").prop('readonly', false);
            $("#nombre").val("");
            $("#nombres").val(data['nombres']);
            $("#apellidos").val(data['apellidos']);
            $("#direccion").val(data['direccion']);
          });
        }else if ($(this).val().length == 11) {
          // Buscarmos los datos de la empresa que tenga este ruc, si existe mostramos los datos, de lo contrario
          // solo activamos los inputs para que igresen los datos del nuevo cliente.
          $.post("{{url('buscar-empresa')}}", {ruc: $(this).val()}, function(data, textStatus, xhr) {
            $("#nombre").prop('readonly', false);
            $("#nombres").prop('readonly', true);
            $("#apellidos").prop('readonly', true);
            $("#direccion").prop('readonly', false);
            $("#nombre").val(data['nombre']);
            $("#nombres").val("");
            $("#apellidos").val("");
            $("#direccion").val(data['direccion']);
          });
        }else{
          // Si no tiene 8 ni 11 dítos el documento, se limpia el formulario y se muestra un mensaje de error.
          $("#nombre").prop('readonly', true);
          $("#nombres").prop('readonly', true);
          $("#apellidos").prop('readonly', true);
          $("#direccion").prop('readonly', false);
          $("#nombre").val("");
          $("#nombres").val("");
          $("#apellidos").val("");
          $("#direccion").val("");
          $("#mensaje").html("EL DOCUMENTO SOLO PUEDE CONTENER 8 U 11 DÍGITOS.");
          $("#errores").modal("show");
        }
      }else{
        // Limpiamos todos los campos y la venta se hará con un cliente común.
        $("#nombre").prop('readonly', true);
        $("#nombres").prop('readonly', true);
        $("#apellidos").prop('readonly', true);
        $("#direccion").prop('readonly', true);
        $("#nombre").val("");
        $("#nombres").val("");
        $("#apellidos").val("");
        $("#direccion").val("");
      }
    });

    /*
     * Token necesario para hacer consultas por ajax.
     * Fecha 18/09/2017
    */
    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });

    /**
     * se llama al método enter del input txtCodigo para buscar el producto que tenga ese código.
     * Fecha: 18/09/2017
    */
    $("#txtCodigo").change(function() {
      if ($(this).val() != "") {
        $.post("{{url('buscar-producto')}}", {codigo: $(this).val(), tienda_id: $("#tienda_id").val()}, function(data, textStatus, xhr) {
          if (data['producto'] != 0) {
            $(".codigo").html(data['producto']['codigo']);
            $("#producto_codigo").val(data['producto']['codigo']);
            $(".descripcion").html(data['producto']['descripcion']);
            $(".precio").html(data['producto']['precio']);
            $(".precio").val(data['producto']['precio']);
            $(".cantidad").html(data['stock'][1]);
            $("#stock").val(data['stock'][1]);
            $(".foto").html(data['foto']);
          }else{
            $(".codigo").html("");
            $("#producto_codigo").val("");
            $(".descripcion").html("");
            $(".precio").html("");
            $(".precio").val("");
            $(".cantidad").html("");
            $("#stock").val(null);
            $(".foto").html("<img src='"+"{{url('storage/productos').'/producto.png'}}"+"' style='width:100px;'>");
          }
        });
      }
    });


    $('#panelBuscar').on('hidden.bs.collapse', function () {
      $("#btnbuscar").html("<span class='fa fa-plus'></span>")
    });
    $('#panelBuscar').on('shown.bs.collapse', function () {
      $("#btnbuscar").html("<span class='fa fa-minus'></span>")
    });
    $('#panelAgregar').on('hidden.bs.collapse', function () {
      $("#btnAgregar").html("<span class='fa fa-plus'></span>")
    });
    $('#panelAgregar').on('shown.bs.collapse', function () {
      $("#btnAgregar").html("<span class='fa fa-minus'></span>")
    });
    $("#precio_venta").keyup(function() {
      var ganancia = $(this).val()-59;
      $("#ganancia").val(ganancia);
    });
  });
</script>
@stop
