<?php
// Primero calculamos todos los dolares que se recibieron y lo convertimos a soles.

$total_dolares = 0;
$total_vouchers = 0;
$total_soles = $cierre->total;
foreach ($cierre->ventas as $venta) {
  if ($dolar = $venta->dolar) {
    $total_dolares += $dolar->monto;
    $total_soles -= $dolar->monto * $dolar->cambio;
  }
  if ($tarjeta = $venta->TarjetaVenta) {
    $total_vouchers += $tarjeta->monto;
    $total_soles -= $tarjeta->monto;
  }
}
foreach ($cierre->cambios as $cambio) {
  if ($dolar = $cambio->dolar) {
    $total_dolares += $dolar->monto;
    $total_soles -= $dolar->monto * $dolar->cambio;
  }
  if ($tarjeta = $cambio->TarjetaVenta) {
    $total_vouchers += $tarjeta->monto;
    $total_soles -= $tarjeta->monto;
  }
}
$total_pagos = \DB::table('pagos')->where('cierre_id', $cierre->id)
  ->whereNotNull('credito_id')
  ->select(
    DB::raw("SUM(pagos.monto) as monto")
  )
  ->first();
$total_soles += $total_pagos->monto;
?>
<table class="table table-condensed" id="tblDinero">
  <tr>
    <th style="text-align:right; border-top:rgba(255, 255, 255, 0);">Inicio: </th>
    <th style="text-align:right; border-top:rgba(255, 255, 255, 0);">{{number_format($cierre->inicio, 2, '.', ' ')}}</th>
  </tr>
  <hr>
  <tr>
    <th colspan="2" style="text-align:center; border-top:rgba(255, 255, 255, 0);">EFECTIVO TOTAL EN VENTAS</th>
  </tr>
  <tr>
    <th style="text-align:right; border-top:rgba(255, 255, 255, 0);">Efectivo Soles: </th>
    <th style="text-align:right; border-top:rgba(255, 255, 255, 0);">{{number_format($total_soles-$cierre->inicio, 2, '.', ' ')}}</th>
  </tr>
  <tr>
    <th style="text-align:right; border-top:rgba(255, 255, 255, 0);">Efectivo Dólares: </th>
    <th style="text-align:right; border-top:rgba(255, 255, 255, 0);">{{number_format($total_dolares, 2, '.', ' ')}}</th>
  </tr>
  <tr>
    <th style="text-align:right; border-top:rgba(255, 255, 255, 0);">Vouchers de Tarjetas: </th>
    <th style="text-align:right; border-top:rgba(255, 255, 255, 0);">{{number_format($total_vouchers, 2, '.', ' ')}}</th>
  </tr>
</table>
