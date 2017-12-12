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
<table class="table table-condensed" id="tblDinero" style="margin-bottom:1px;">
  <tr>
    <td  style="border-top:rgba(130, 130, 130, 0.5); padding-top:1px; padding-bottom:0px;">
      <p class="text-right" style="font-size: 12px; margin-bottom:1px;">Inicio: </p>
    </td>
    <td  style="border-top:rgba(130, 130, 130, 0.5); padding-top:1px; padding-bottom:0px;">
      <p class="text-right" style="font-size: 12px; margin-bottom:1px;">{{number_format($cierre->inicio, 2, '.', ' ')}}</p>
    </td>
  </tr>
  <tr>
    <td colspan="2"  style="border-top:rgba(130, 130, 130, 0.5); padding-top:1px; padding-bottom:0px;">
      <p class="text-center" style="font-size: 12px; margin-bottom:1px;">EFECTIVO TOTAL EN VENTAS</p>
    </td>
  </tr>
  <tr>
    <td style="border-top:rgba(130, 130, 130, 0.5); padding-top:1px; padding-bottom:0px;">
      <p class="text-right" style="font-size: 12px; margin-bottom:1px;">Efectivo Soles: </p>
    </td>
    <td style="border-top:rgba(130, 130, 130, 0.5); padding-top:1px; padding-bottom:0px;">
      <p class="text-right" style="font-size: 12px; margin-bottom:1px;">{{number_format($total_soles-$cierre->inicio, 2, '.', ' ')}}</p>
    </td>
  </tr>
  <tr>
    <td style="border-top:rgba(130, 130, 130, 0.5); padding-top:1px; padding-bottom:0px;">
      <p class="text-right" style="font-size: 12px; margin-bottom:1px;">Efectivo Dólares: </p></td>
    <td style="border-top:rgba(130, 130, 130, 0.5); padding-top:1px; padding-bottom:0px;">
      <p class="text-right" style="font-size: 12px; margin-bottom:1px;">{{number_format($total_dolares, 2, '.', ' ')}}</p></td>
  </tr>
  <tr>
    <td style="border-top:rgba(130, 130, 130, 0.5); padding-top:1px; padding-bottom:0px;">
      <p class="text-right" style="font-size: 12px; margin-bottom:1px;">Vouchers de Tarjetas: </p></td>
    <td style="border-top:rgba(130, 130, 130, 0.5); padding-top:1px; padding-bottom:0px;">
      <p class="text-right" style="font-size: 12px; margin-bottom:1px;">{{number_format($total_vouchers, 2, '.', ' ')}}</p></td>
  </tr>
</table>
