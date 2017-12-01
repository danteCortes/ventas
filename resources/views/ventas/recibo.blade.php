@extends('plantillas.cajero')

@section('estilos')
{{Html::style('bootgrid/jquery.bootgrid.min.css')}}
<style media="screen">
  .table>tbody>tr>th{
    border-top:rgba(255, 255, 255, 0);

  }
  .table{
    font-size: 8px;
  }
</style>
@stop

@section('titulo')
Recibo
@stop

@section('contenido')
@include('plantillas.mensajes')
<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-4 col-md-offset-4 col-lg-4 col-lg-offset-4">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title">Recibo {{$recibo->numeracion}}</h3>
      </div>
      <div class="panel-body" id="ticket" style="padding-left:5px; padding-right:5px;">
          <div class="row">
              <div class="col-sm-12">
                  <p class="text-center" style="font-size: 12px; margin-bottom:1px;">{{$recibo->venta->tienda->nombre}}</p>
              </div>
          </div>
          <div class="row">
              <div class="col-sm-12">
                  <p class="text-center" style="font-size: 12px; margin-bottom:1px;">R.U.C. N° {{$recibo->venta->tienda->ruc}}</p>
              </div>
          </div>
          <div class="row">
              <div class="col-sm-12">
                  <p class="text-center" style="font-size: 12px; margin-bottom:1px;">{{$recibo->venta->tienda->direccion}}</p>
              </div>
          </div>
          <div class="row">
              <div class="col-sm-12">
                  <p class="text-center" style="font-size: 12px; margin-bottom:1px;">AUTORIZACION SUNAT NRO. 0193845116923</p>
              </div>
          </div>
          <div class="row">
              <div class="col-sm-12">
                  <p class="text-left" style="font-size: 12px; margin-bottom:1px;">TICKET N° {{$recibo->numeracion}}</p>
              </div>
          </div>
          <div class="row">
              <div class="col-sm-12">
                  <p class="text-left" style="font-size: 12px; margin-bottom:1px;">N° DE SERIE {{$recibo->venta->tienda->ticketera}}</p>
              </div>
          </div>
          @if($empresa = $recibo->empresa)
          <div class="row">
              <div class="col-sm-12">
                  <p class="text-left" style="font-size: 12px; margin-bottom:1px;">RUC: {{$empresa->ruc}}</p>
              </div>
          </div>
          <div class="row">
              <div class="col-sm-12">
                  <p class="text-left" style="font-size: 12px; margin-bottom:1px;">CLIENTE: {{$empresa->nombre}}</p>
              </div>
          </div>
          <div class="row">
              <div class="col-sm-12">
                  <p class="text-left" style="font-size: 12px; margin-bottom:1px;">DIRECCIÓN: {{$empresa->direccion}}</p>
              </div>
          </div>
          @elseif($persona = $recibo->persona)
          <div class="row">
              <div class="col-sm-12">
                  <p class="text-left" style="font-size: 12px; margin-bottom:1px;">CLIENTE: {{$persona->nombres}} {{$persona->apellidos}}</p>
              </div>
          </div>
          @else
          <div class="row">
              <div class="col-sm-12">
                  <p class="text-left" style="font-size: 12px; margin-bottom:1px;">CLIENTE: CLIENTE VARIOS</p>
              </div>
          </div>
          @endif
        <table class="table table-condensed" style="margin-bottom:5px;">
          <tr>
            <td style="border-top:rgba(130, 130, 130, 0.5); padding-top:1px; padding-bottom:0px;">
                <p class="text-left" style="font-size: 12px; margin-bottom:1px;">Cant.</p>
            </td>
            <td style="border-top:rgba(130, 130, 130, 0.5); padding-top:1px; padding-bottom:0px;">
                <p class="text-left" style="font-size: 12px; margin-bottom:1px;">Descripción</p>
            </td>
            <td style="border-top:rgba(130, 130, 130, 0.5); padding-top:1px; padding-bottom:0px;">
                <p class="text-left" style="font-size: 12px; margin-bottom:1px;">Unit.</p>
            </td>
            <td style="width:50px; border-top:rgba(130, 130, 130, 0.5); padding-top:1px; padding-bottom:0px;">
                <p class="text-left" style="font-size: 12px; margin-bottom:1px;">Importe</p>
            </td>
          </tr>
          @foreach($recibo->venta->detalles as $detalle)
            <tr>
                <td style="border-top:rgba(130, 130, 130, 0.5); padding-top:1px; padding-bottom:0px;">
                    <p class="text-left" style="font-size: 12px; margin-bottom:1px;">{{$detalle->cantidad}}</p>
                </td>
                <td style="border-top:rgba(130, 130, 130, 0.5); padding-top:1px; padding-bottom:0px;">
                    <p class="text-left" style="font-size: 12px; margin-bottom:1px;">{{$detalle->producto->familia->nombre}}
                    {{$detalle->producto->marca->nombre}} {{$detalle->producto->descripcion}}</p>
                </td>
                <td style="border-top:rgba(130, 130, 130, 0.5); padding-top:1px; padding-bottom:0px;">
                    <p class="text-right" style="font-size: 12px; margin-bottom:1px;">{{$detalle->producto->precio}}</p>
                </td>
                <td style="width:50px; border-top:rgba(130, 130, 130, 0.5); padding-top:1px; padding-bottom:0px;">
                    <p class="text-right" style="font-size: 12px; margin-bottom:1px;">{{$detalle->total}}</p>
                </td>
            </tr>
          @endforeach
          <tr>
            <td colspan="3" style="text-align:right; border-top:rgba(255, 255, 255, 0); padding-top:1px; padding-bottom:0px; padding-top:1px; padding-bottom:0px;">
                <p class="text-right" style="font-size: 12px; margin-bottom:1px;">TOTAL S/</p>
            </td>
            <td style="text-align:right; border-top:rgba(255, 255, 255, 0); padding-top:1px; padding-bottom:0px; padding-top:1px; padding-bottom:0px;">
                <p class="text-right" style="font-size: 12px; margin-bottom:1px;">{{$recibo->venta->total}}</p>
            </td>
          </tr>
          <?php $vuelto =  $recibo->venta->total;?>
          @if($reclamo = $recibo->venta->reclamo)
            <tr>
              <td colspan="2" style="text-align:left; border-top:rgba(255, 255, 255, 0); padding-top:1px; padding-bottom:0px;">
                  <p class="text-right" style="font-size: 12px; margin-bottom:1px;">DESCUENTO POR CANJE DE {{$reclamo->puntos}} PUNTOS TÚ</p></td>
              <td style="text-align:right; border-top:rgba(255, 255, 255, 0); padding-top:1px; padding-bottom:0px;">
                  <p class="text-right" style="font-size: 12px; margin-bottom:1px;">{{number_format($recibo->venta->descuento, 2, '.', ' ')}}</p></td>
            </tr>
            <tr>
              <td colspan="2" style="text-align:right; border-top:rgba(255, 255, 255, 0); padding-top:1px; padding-bottom:0px;">
                  <p class="text-right" style="font-size: 12px; margin-bottom:1px;">TOTAL A PAGAR</p>
              </td>
              <td style="text-align:right; border-top:rgba(255, 255, 255, 0); padding-top:1px; padding-bottom:0px;">
                  <p class="text-right" style="font-size: 12px; margin-bottom:1px;">{{number_format($recibo->venta->total-$recibo->venta->descuento, 2, '.', ' ')}}</p>
              </td>
            </tr>
          <?php $vuelto =  $recibo->venta->total-$recibo->venta->descuento;?>
          @endif
          @if($recibo->venta->efectivo)
          <tr>
            <td colspan="3" style="text-align:right; border-top:rgba(255, 255, 255, 0); padding-top:1px; padding-bottom:0px;">
                <p class="text-right" style="font-size: 12px; margin-bottom:1px;">EFECTIVO S/ </p>
            </td>
            <td style="text-align:right; border-top:rgba(255, 255, 255, 0); padding-top:1px; padding-bottom:0px;">
                <p class="text-right" style="font-size: 12px; margin-bottom:1px;">{{number_format($recibo->venta->efectivo->monto, 2, '.', ' ')}}</p>
            </td>
          </tr>
          <?php $vuelto = $recibo->venta->efectivo->monto - $vuelto; ?>
          @endif
          @if($recibo->venta->tarjetaVenta)
          <tr>
            <th colspan="3" style="text-align:right; border-top:rgba(255, 255, 255, 0); padding-top:1px; padding-bottom:0px;">
                <p class="text-right" style="font-size: 12px; margin-bottom:1px;"></p>TARJETA S/ </th>
            <th style="text-align:right; border-top:rgba(255, 255, 255, 0); padding-top:1px; padding-bottom:0px;">
                <p class="text-right" style="font-size: 12px; margin-bottom:1px;"></p>{{number_format($recibo->venta->tarjetaVenta->monto, 2, '.', ' ')}}</th>
          </tr>
          <?php $vuelto = $recibo->venta->tarjetaVenta->monto - $vuelto; ?>
          @endif
          <tr>
            <td colspan="3" style="text-align:right; border-top:rgba(255, 255, 255, 0); padding-top:1px; padding-bottom:0px;">
                <p class="text-right" style="font-size: 12px; margin-bottom:1px;">VUELTO S/ </p></td>
            <td style="text-align:right; border-top:rgba(255, 255, 255, 0); padding-top:1px; padding-bottom:0px;">
                <p class="text-right" style="font-size: 12px; margin-bottom:1px;">{{number_format($vuelto, 2, '.', ' ')}}</p></td>
          </tr>
          <tr>
            <td colspan="4" style="text-align:left; border-top:rgba(255, 255, 255, 0); padding-top:1px; padding-bottom:0px;">
                <p class="text-left" style="font-size: 12px; margin-bottom:1px;">SON: {{$letras}}</p></td>
          </tr>
          <tr>
            <td colspan="4" style="text-align:left; border-top:rgba(255, 255, 255, 0); padding-top:1px; padding-bottom:0px;">
                <p class="text-left" style="font-size: 12px; margin-bottom:1px;">HUANUCO, {{\Carbon\Carbon::createFromFormat('Y-m-d H:i:s',$recibo->venta->updated_at)->format('d/m/Y')}}
                - HORA: {{\Carbon\Carbon::createFromFormat('Y-m-d H:i:s',$recibo->venta->updated_at)->format('H:i A')}}</p>
            </td>
          </tr>
          <tr>
            <td colspan="4" style="text-align:left; border-top:rgba(255, 255, 255, 0); padding-top:1px; padding-bottom:0px;">
                <p class="text-left" style="font-size: 12px; margin-bottom:1px;">CAJERA: {{$recibo->venta->usuario->persona->nombres}} {{$recibo->venta->usuario->persona->apellidos}}</p>
            </td>
          </tr>
        </table>
        @if($persona = $recibo->persona)
          @if($persona->puntos)
            <p class="text-justify" style="font-size: 12px; margin-bottom:5px;">SR(A). {{$persona->nombres}} {{$persona->apellidos}} CON ESTA COMPRA USTED ACUMULA UN TOTAL DE {{$persona->puntos}}
              PUNTOS TÚ. RECUERDE RECLAMAR SU DESCUENTO A PARTIR DE LOS 1 000 PUNTOS.</p>
          @endif
        @endif
        <p class="text-justify" style="font-size: 12px; margin-bottom:5px;">BIENES TRANSFERIDOS EN LA AMAZONIA PARA SER CONSUMIDOS EN LA MISMA</p>
      </div>
      <div class="panel-footer">
        <button type="button" class="btn btn-primary imprimir" id="imprimir"><span class="fa fa-print"></span> Imprimir</button>
        <a href="{{url('venta/create')}}" class="btn btn-default"> Salir</a>
      </div>
    </div>
  </div>
</div>
@stop

@section('scripts')
{{Html::script('assets/js/jquery.printarea.js')}}
<script type="text/javascript">
  $(document).ready(function() {
    $(".imprimir").click(function (){
      $("#ticket").printArea();
    });
  });
</script>
@stop
