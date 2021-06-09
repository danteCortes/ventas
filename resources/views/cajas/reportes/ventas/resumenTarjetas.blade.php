<?php
$pagos_tarjeta_ventas = \DB::table('tarjeta_venta')->join('ventas', 'ventas.id', '=', 'tarjeta_venta.venta_id')
  ->join('recibos', 'recibos.venta_id', '=', 'ventas.id')
  ->join('tarjetas', 'tarjetas.id', '=', 'tarjeta_venta.tarjeta_id')
  ->whereDate('ventas.created_at', $fecha)
  ->where('ventas.usuario_id', $usuario->id)
  ->where('ventas.tienda_id', $tienda->id)
  ->select(
    'tarjeta_venta.operacion as operacion',
    'tarjeta_venta.monto as monto',
    'recibos.numeracion as numeracion',
    'tarjetas.nombre as nombre'
    )->orderBy('tarjetas.nombre');
$pagos_tarjeta = \DB::table('tarjeta_venta')->join('cambios', 'cambios.id', '=', 'tarjeta_venta.cambio_id')
  ->join('ventas', 'ventas.id', '=', 'cambios.venta_id')
  ->join('recibos', 'recibos.venta_id', '=', 'ventas.id')
  ->join('tarjetas', 'tarjetas.id', '=', 'tarjeta_venta.tarjeta_id')
  ->whereDate('cambios.created_at', $fecha)
  ->where('cambios.usuario_id', $usuario->id)
  ->where('cambios.tienda_id', $tienda->id)
  ->select(
    'tarjeta_venta.operacion as operacion',
    'tarjeta_venta.monto as monto',
    'recibos.numeracion as numeracion',
    'tarjetas.nombre as nombre'
    )->orderBy('tarjetas.nombre')->union($pagos_tarjeta_ventas)->get();
 ?>
 @if(count($pagos_tarjeta) > 0)
<hr style="margin-bottom: 1px; margin-top: 0px;">
<table class="table table-condensed" id="tblPagosTarjeta" style="margin-bottom:1px;">
  <tr>
    <th colspan="4" style="border-top:rgba(130, 130, 130, 0.5); padding-top:1px; padding-bottom:0px;">
      <p class="text-center" style="font-size: 12px; margin-bottom:1px;">RESUMEN DE PAGOS CON TARJETA</p></th>
  </tr>
  @foreach($pagos_tarjeta as $tarjeta)
    <tr>
      <td style="border-top:rgba(130, 130, 130, 0.5); padding-top:1px; padding-bottom:0px;">
        <p class="text-left" style="font-size: 12px; margin-bottom:1px;">{{$tarjeta->operacion}}</p></td>
      <td style="border-top:rgba(130, 130, 130, 0.5); padding-top:1px; padding-bottom:0px;">
        <p class="text-left" style="font-size: 12px; margin-bottom:1px;">{{$tarjeta->nombre}}</p></td>
      <td style="border-top:rgba(130, 130, 130, 0.5); padding-top:1px; padding-bottom:0px;">
        <p class="text-left" style="font-size: 12px; margin-bottom:1px;">{{$tarjeta->numeracion}}</p></td>
      <td style="border-top:rgba(130, 130, 130, 0.5); padding-top:1px; padding-bottom:0px;">
        <p class="text-right" style="font-size: 12px; margin-bottom:1px;">{{number_format($tarjeta->monto, 2, '.', ' ')}}</p></td>
    </tr>
  @endforeach
</table>
@endif
