@extends('plantillas.cajero')

@section('estilos')
{{Html::style('bootgrid/jquery.bootgrid.min.css')}}
@stop

@section('titulo')
Cambiar Venta
@stop

@section('contenido')
@include('plantillas.mensajes')
<div class="row">
  @include('ventas.frmBuscarProducto')
  @include('cambios.frmAgregarDetalle')
</div>
@include('cambios.tblDetalles')
@include('cambios.frmVenta')
@include('cambios.modales')
<!--Modal con el formulario para registrar la venta con tarjeta al sistema.-->
<!--Fecha 22/09/2017-->
<div class="modal fade" id="registrarTarjeta" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header" style="background-color:#385a94; color:#fff;">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">REGISTRAR PAGO CON TARJETA</h4>
      </div>
      <div class="modal-body" style="background-color:#e69c2d">
        <div class="panel" style="background-color:#bd7406">
          <div class="panel-body">
            <p id="msjRegistrarTarjeta">EL PAGO CON TARJETA PUEDE INCLUIR UNA COMISIÓN SEGÚN LA TARJETA QUE USA.</p>
            {{Form::open(['url'=>'pago-tarjeta-cambio', 'class'=>'form-horizontal'])}}
            <div class="form-group">
              <label for="tarjeta_id" class="control-label col-xs-2 col-sm-2 col-md-3 col-lg-3">Tarjeta*:</label>
              <div class="col-xs-10 col-sm-10 col-md-9 col-lg-9">
                <select class="form-control input-sm" name="tarjeta_id" id="tarjeta_id">
                  @if($cambio = \App\Cambio::where('usuario_id', Auth::user()->id)->where('tienda_id', Auth::user()->tienda_id)
                    ->where('estado', 1)->where('venta_id', $venta->id)->first())
                    @if($tarjetaVenta = $cambio->tarjetaVenta)
                      <option value="{{$tarjetaVenta->tarjeta_id}}">{{$tarjetaVenta->tarjeta->nombre}}
                        {{$tarjetaVenta->tarjeta->comision}}% (ACTUAL)</option>
                    @endif
                  @endif
                  <option value="">SELECCIONAR UNA OPCIÓN</option>
                  @foreach(\App\Tarjeta::all() as $tarjeta)
                    <option value="{{$tarjeta->id}}">{{$tarjeta->nombre}} {{$tarjeta->comision}}%</option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="form-group">
              <label for="operacion" class="control-label col-xs-2 col-sm-2 col-md-3 col-lg-3">Operación*:</label>
              <div class="col-xs-10 col-sm-10 col-md-9 col-lg-9">
                @if($cambio = \App\Cambio::where('usuario_id', Auth::user()->id)->where('tienda_id', Auth::user()->tienda_id)
                  ->where('estado', 1)->where('venta_id', $venta->id)->first())
                  @if($tarjetaVenta = $cambio->tarjetaVenta)
                    {{Form::text('operacion', $tarjetaVenta->operacion, ['class'=>'form-control input-sm numero', 'id'=>'operacion'])}}
                  @else
                    {{Form::text('operacion', null, ['class'=>'form-control input-sm numero', 'id'=>'operacion'])}}
                  @endif
                @else
                  {{Form::text('operacion', null, ['class'=>'form-control input-sm numero', 'id'=>'operacion'])}}
                @endif
              </div>
            </div>
            <div class="form-group">
              <div class="col-xs-10 col-xs-offset-2 col-sm-10 col-sm-offset-2 col-md-9 col-md-offset-3 col-lg-9 col-lg-offset-3">
                @if($cambio = \App\Cambio::where('usuario_id', Auth::user()->id)->where('tienda_id', Auth::user()->tienda_id)
                  ->where('estado', 1)->where('venta_id', $venta->id)->first())
                  @if($tarjetaVenta = $cambio->tarjetaVenta)
                    {{Form::hidden('monto', $tarjetaVenta->monto, ['id'=>'hdnMontoTarjeta'])}}
                  @else
                    {{Form::hidden('monto', null, ['id'=>'hdnMontoTarjeta'])}}
                  @endif
                @else
                  {{Form::hidden('monto', null, ['id'=>'hdnMontoTarjeta'])}}
                @endif
                {{Form::button('Guardar', ['type'=>'submit', 'class'=>'btn btn-primary btn-sm'])}}
              </div>
            </div>
            {{Form::close()}}
            <p id="comision"></p>
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
{{Html::script('bootgrid/jquery.bootgrid.min.js')}}
{{Html::script('assets/lib/mask/jquery.mask.js')}}
@include('cambios.scriptEditar')
@stop
