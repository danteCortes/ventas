@extends('plantillas.cajero')

@section('estilos')
{{Html::style('bootgrid/jquery.bootgrid.min.css')}}
<style media="screen">
  .table>tbody>tr>th{
    border-top:rgba(255, 255, 255, 0);
  }
</style>
@stop

@section('titulo')
Recibo
@stop

@section('contenido')
@include('plantillas.mensajes')
<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-6 col-md-offset-3 col-lg-6 col-lg-offset-3">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title">Recibo {{$recibo->numeracion}}</h3>
      </div>
      <div class="panel-body" id="ticket">
        <table class="table table-condensed">
          <tr>
            <th colspan="3" style="text-align:center; border-top:rgba(255, 255, 255, 0);">{{$recibo->venta->tienda->nombre}}</th>
          </tr>
          <tr>
            <th colspan="3" style="text-align:center; border-top:rgba(255, 255, 255, 0);">{{$recibo->venta->tienda->direccion}}</th>
          </tr>
          <tr>
            <th colspan="3" style="text-align:center; border-top:rgba(255, 255, 255, 0);">R.U.C. N° {{$recibo->venta->tienda->ruc}}</th>
          </tr>
          <tr>
            <th colspan="3" style="text-align:center; border-top:rgba(255, 255, 255, 0);">N° DE SERIE {{$recibo->venta->tienda->ticketera}}</th>
          </tr>
          <tr>
            <th colspan="3" style="text-align:right; border-top:rgba(255, 255, 255, 0);">TICKET N° {{$recibo->numeracion}}</th>
          </tr>
          <tr>
            <th colspan="3" style="text-align:right; border-top:rgba(255, 255, 255, 0);">{{$recibo->venta->updated_at}}</th>
          </tr>
          @if($empresa = $recibo->empresa)
            <tr>
              <th colspan="3" style="text-align:left; border-top:rgba(255, 255, 255, 0);">RUC: {{$empresa->ruc}}</th>
            </tr>
            <tr>
              <th colspan="3" style="text-align:left; border-top:rgba(255, 255, 255, 0);">CLIENTE: {{$empresa->nombre}}</th>
            </tr>
            <tr>
              <th colspan="3" style="text-align:left; border-top:rgba(255, 255, 255, 0);">DIRECCIÓN: {{$empresa->direccion}}</th>
            </tr>
          @endif
          @foreach($recibo->venta->detalles as $detalle)
            <tr>
              <th style="text-align:center; border-top:rgba(255, 255, 255, 0);">{{$detalle->cantidad}}</th>
              <th style="text-align:left; border-top:rgba(255, 255, 255, 0);">{{$detalle->producto->descripcion}}</th>
              <th style="text-align:right; border-top:rgba(255, 255, 255, 0);">{{$detalle->total}}</th>
            </tr>
          @endforeach
          <tr>
            <th colspan="2" style="text-align:right; border-top:rgba(255, 255, 255, 0);">TOTAL</th>
            <th style="text-align:right; border-top:rgba(255, 255, 255, 0);">{{$recibo->venta->total}}</th>
          </tr>
          @if($reclamo = $recibo->venta->reclamo)
            <tr>
              <th colspan="2" style="text-align:left; border-top:rgba(255, 255, 255, 0);">DESCUENTO POR CANJE DE {{$reclamo->puntos}} PUNTOS TÚ</th>
              <th style="text-align:right; border-top:rgba(255, 255, 255, 0);">{{number_format($recibo->venta->descuento, 2, '.', ' ')}}</th>
            </tr>
            <tr>
              <th colspan="2" style="text-align:right; border-top:rgba(255, 255, 255, 0);">TOTAL A PAGAR</th>
              <th style="text-align:right; border-top:rgba(255, 255, 255, 0);">
                {{number_format($recibo->venta->total-$recibo->venta->descuento, 2, '.', ' ')}}</th>
            </tr>
          @endif
        </table>
        @if($persona = $recibo->persona)
          @if($persona->puntos)
            <p class="text-justify">SR(A). {{$persona->nombres}} {{$persona->apellidos}} CON ESTA COMPRA USTED ACUMULA UN TOTAL DE {{$persona->puntos}}
              PUNTOS TÚ. RECUERDE RECLAMAR SU DESCUENTO A PARTIR DE LOS 1 000 PUNTOS.</p>
          @endif
        @endif
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
