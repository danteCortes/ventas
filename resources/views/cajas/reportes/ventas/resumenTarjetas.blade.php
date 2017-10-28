<?php
$pagos_tarjeta_ventas = \DB::table('tarjeta_venta')->join('ventas', 'ventas.id', '=', 'tarjeta_venta.venta_id')
  ->join('recibos', 'recibos.venta_id', '=', 'ventas.id')
  ->join('tarjetas', 'tarjetas.id', '=', 'tarjeta_venta.tarjeta_id')
  ->where('ventas.cierre_id', $cierre->id)->select(
    'tarjeta_venta.operacion as operacion',
    'tarjeta_venta.monto as monto',
    'recibos.numeracion as numeracion',
    'tarjetas.nombre as nombre'
    )->orderBy('tarjetas.nombre');
$pagos_tarjeta = \DB::table('tarjeta_venta')->join('cambios', 'cambios.id', '=', 'tarjeta_venta.cambio_id')
  ->join('ventas', 'ventas.id', '=', 'cambios.venta_id')
  ->join('recibos', 'recibos.venta_id', '=', 'ventas.id')
  ->join('tarjetas', 'tarjetas.id', '=', 'tarjeta_venta.tarjeta_id')
  ->where('ventas.cierre_id', $cierre->id)->select(
    'tarjeta_venta.operacion as operacion',
    'tarjeta_venta.monto as monto',
    'recibos.numeracion as numeracion',
    'tarjetas.nombre as nombre'
    )->orderBy('tarjetas.nombre')->union($pagos_tarjeta_ventas)->get();
 ?>
 @if(count($pagos_tarjeta) > 0)
<hr>
<table class="table table-condensed" id="tblPagosTarjeta">
  <tr>
    <th colspan="4" style="text-align:center; border-top:rgba(255, 255, 255, 0);">RESUMEN DE PAGOS CON TARJETA</th>
  </tr>
  @foreach($pagos_tarjeta as $tarjeta)
    <tr>
      <th style="text-align:left; border-top:rgba(255, 255, 255, 0);">{{$tarjeta->operacion}}</th>
      <th style="text-align:left; border-top:rgba(255, 255, 255, 0);">{{$tarjeta->nombre}}</th>
      <th style="text-align:left; border-top:rgba(255, 255, 255, 0);">{{$tarjeta->numeracion}}</th>
      <th style="text-align:right; border-top:rgba(255, 255, 255, 0);">{{number_format($tarjeta->monto, 2, '.', ' ')}}</th>
    </tr>
  @endforeach
</table>
@endif
