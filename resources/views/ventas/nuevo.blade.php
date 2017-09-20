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
                <input type="text" name="documento" class="form-control" placeholder="DNI/RUC" data-mask="99999999999">
                <span class="input-group-btn">
                  <button class="btn btn-default" type="button"><span class="fa fa-search"></span></button>
                </span>
              </div>
            </div>
          </div>
          <div class="col-xs-12 col-sm-12 col-md-6 col-lg-8">
            <div class="form-group">
              <input type="text" name="documento" class="form-control" placeholder="RAZÓN SOCIAL" readonly>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-xs-12 col-sm-12 col-md-4 col-lg-3">
            <div class="form-group">
              <input type="text" name="documento" class="form-control" placeholder="NOMBRES" readonly>
            </div>
          </div>
          <div class="col-xs-12 col-sm-12 col-md-4 col-lg-3">
            <div class="form-group">
              <input type="text" name="documento" class="form-control" placeholder="APELLIDOS" readonly>
            </div>
          </div>
          <div class="col-xs-12 col-sm-12 col-md-4 col-lg-6">
            <div class="form-group">
              <input type="text" name="documento" class="form-control" placeholder="DIRECCIÓN" readonly>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
            <div class="form-group">
              <input type="text" name="soles" class="form-control" placeholder="SOLES" >
            </div>
          </div>
          <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
            <div class="form-group">
              <div class="input-group">
                <input type="text" name="dolares" class="form-control" placeholder="DOLARES" >
                <span class="input-group-btn">
                  <button class="btn btn-default" type="button">Tipo Cambio</button>
                </span>
              </div>
            </div>
          </div>
          <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
            <div class="form-group">
              <div class="input-group">
                <input type="text" name="tarjeta" class="form-control" placeholder="TARJETA" >
                <span class="input-group-btn">
                  <button class="btn btn-default" type="button">Registrar</button>
                </span>
              </div>
            </div>
          </div>
          <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
            <div class="form-group">
              <input type="text" name="vuelto" class="form-control" placeholder="VUELTO" readonly>
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
@stop

@section('scripts')
{{Html::script('assets/lib/mask/jquery.mask.js')}}
<script type="text/javascript">
  $(document).ready(function() {

    /**
     * Busca un cliente, ya sea persona o empresa, si el campo queda vacio puede guardarse la venta.
     * Fecha: 19/09/2017
    */

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
