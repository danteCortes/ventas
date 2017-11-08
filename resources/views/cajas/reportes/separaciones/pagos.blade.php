<?php
// Primero calculamos todos los dolares que se recibieron y lo convertimos a soles.
$pagos = \DB::table('pagos')->where('cierre_id', $cierre->id)->whereNotNull('credito_id')->get();

?>
@if(count($pagos) > 0)
<hr>
<table class="table table-condensed" id="tblPagosCreditos">
  <tr>
    <th colspan="2" style="text-align:center; border-top:rgba(255, 255, 255, 0);">PAGOS DE CRÉDITOS</th>
  </tr>
  @foreach($pagos as $pago)
    <tr>
      <th style="text-align:left; border-top:rgba(255, 255, 255, 0);">Crédito Nro {{$pago->credito_id}} </th>
      <th style="text-align:right; border-top:rgba(255, 255, 255, 0);">{{number_format($pago->monto, 2, '.', ' ')}}</th>
    </tr>
  @endforeach
</table>
@endif
