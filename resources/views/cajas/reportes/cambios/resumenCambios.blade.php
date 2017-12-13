<?php
  $cambios = \DB::table('cambios')->join('ventas', 'ventas.id', '=', 'cambios.venta_id')
    ->join('recibos', 'ventas.id', '=', 'recibos.venta_id')
    ->where('cambios.cierre_id', $cierre->id)
    ->where('ventas.cierre_id', '!=', $cierre->id)
    ->select(
      'recibos.numeracion as numeracion',
      'ventas.created_at as fecha_venta',
      'cambios.diferencia as diferencia'
    )->get();

 ?>
@if(count($cambios) > 0)
<hr style="margin-bottom: 1px; margin-top: 0px;">
<table class="table table-condensed" id="tblResumenVentas" style="margin-bottom:0px;">
  <tr>
    <th colspan="3" style="text-align:center; border-top:rgba(255, 255, 255, 0);">
      <p class="text-center" style="font-size: 12px; margin-bottom:1px;">RESUMEN DE CAMBIOS</p></th>
  </tr>
  @foreach($cambios as $cambio)
    <tr>
      <td style="border-top:rgba(130, 130, 130, 0.5); padding-top:1px; padding-bottom:0px;">
        <p class="text-left" style="font-size: 12px; margin-bottom:1px;">{{$cambio->numeracion}}</p></td>
      <td style="border-top:rgba(130, 130, 130, 0.5); padding-top:1px; padding-bottom:0px;">
        <p class="text-left" style="font-size: 12px; margin-bottom:1px;">
          {{\Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $cambio->fecha_venta)->format('d/m/Y')}}</p></td>
      <td style="border-top:rgba(130, 130, 130, 0.5); padding-top:1px; padding-bottom:0px;">
        <p class="text-right" style="font-size: 12px; margin-bottom:1px;">{{number_format($cambio->diferencia, 2, '.', ' ')}}</p></td>
    </tr>
  @endforeach
</table>
@endif
