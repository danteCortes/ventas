<?php
  $cambios = \DB::table('cambios')->join('ventas', 'ventas.id', '=', 'cambios.venta_id')
    ->join('recibos', 'ventas.id', '=', 'recibos.venta_id')
    ->where('cambios.cierre_id', $cierre->id)
    ->select(
      'recibos.numeracion as numeracion',
      'ventas.created_at as fecha_venta',
      'cambios.diferencia as diferencia'
    )->get();

 ?>
@if(count($cambios) > 0)
<hr>
<table class="table table-condensed" id="tblResumenVentas">
  <tr>
    <th colspan="3" style="text-align:center; border-top:rgba(255, 255, 255, 0);">RESUMEN DE CAMBIOS</th>
  </tr>
  @foreach($cambios as $cambio)
    <tr>
      <th style="text-align:left;">{{$cambio->numeracion}}</th>
      <th style="text-align:left;">{{\Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $cambio->fecha_venta)->format('d/m/Y')}}</th>
      <th style="text-align:right;">{{number_format($cambio->diferencia, 2, '.', ' ')}}</th>
    </tr>
  @endforeach
</table>
@endif
