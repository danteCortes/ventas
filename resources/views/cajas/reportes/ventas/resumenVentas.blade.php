<?php

  $ventas = \App\Venta::where('cierre_id', $cierre->id)->where('estado', 0)->get();

 ?>
@if(count($ventas) > 0)
<hr style="margin-bottom: 1px; margin-top: 0px;">
<table class="table table-condensed" id="tblResumenVentas" style="margin-bottom:0px;">
  <tr>
    <th colspan="3" style="text-align:center; border-top:rgba(255, 255, 255, 0);">
      <p class="text-center" style="font-size: 12px; margin-bottom:1px;">RESUMEN DE VENTAS</p></th>
  </tr>
  @foreach($ventas as $venta)
    <tr>
      <td colspan="3" style="text-align:left;">
        <p class="text-center" style="font-size: 12px; margin-bottom:1px;">Venta Nro {{$venta->recibo->numeracion}}</p></td>
    </tr>
      @foreach($venta->detalles as $detalle)
        <tr>
          <td style="border-top:rgba(130, 130, 130, 0.5); padding-top:1px; padding-bottom:0px;">
            <p class="text-left" style="font-size: 12px; margin-bottom:1px;">{{$detalle->cantidad}}</p></td>
          <td style="border-top:rgba(130, 130, 130, 0.5); padding-top:1px; padding-bottom:0px;">
            <p class="text-left" style="font-size: 12px; margin-bottom:1px;">{{$detalle->producto->familia->nombre}}
              {{$detalle->producto->marca->nombre}} {{$detalle->producto->descripcion}}</p></td>
          <td style="border-top:rgba(130, 130, 130, 0.5); padding-top:1px; padding-bottom:0px;">
            <p class="text-right" style="font-size: 12px; margin-bottom:1px;">{{$detalle->total}}</p></td>
        </tr>
      @endforeach
      <tr>
        <td colspan="2" style="border-top:rgba(130, 130, 130, 0.5); padding-top:1px; padding-bottom:0px;">
          <p class="text-right" style="font-size: 12px; margin-bottom:1px;">Total</p></td>
        <td style="border-top:rgba(130, 130, 130, 0.5); padding-top:1px; padding-bottom:0px;">
          <p class="text-right" style="font-size: 12px; margin-bottom:1px;">{{$venta->total}}</p></td>
      </tr>
  @endforeach
</table>
@endif
