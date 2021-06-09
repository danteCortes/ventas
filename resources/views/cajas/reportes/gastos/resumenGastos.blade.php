<?php

  $gastos = \App\OtroGasto::whereDate('created_at', $fecha)
    ->where('usuario_id', $usuario->id)
    ->where('tienda_id', $tienda->id)
    ->get()
  ;

 ?>
@if(count($gastos) > 0)
<hr style="margin-bottom: 1px; margin-top: 0px;">
<table class="table table-condensed" id="tblResumenVentas" style="margin-bottom:0px;">
  <tr>
    <th colspan="2" style="text-align:center; border-top:rgba(255, 255, 255, 0);">
      <p class="text-center" style="font-size: 12px; margin-bottom:1px;">RESUMEN DE EGRESOS DE EFECTIVO</p></th>
  </tr>
  @foreach($gastos as $gasto)
    <tr>
      <td style="border-top:rgba(130, 130, 130, 0.5); padding-top:1px; padding-bottom:0px;">
        <p class="text-left" style="font-size: 12px; margin-bottom:1px;">{{$gasto->descripcion}}</p></td>
      <td style="border-top:rgba(130, 130, 130, 0.5); padding-top:1px; padding-bottom:0px;">
        <p class="text-right" style="font-size: 12px; margin-bottom:1px;">{{number_format($gasto->total, 2, '.', ' ')}}</p></td>
    </tr>
  @endforeach
</table>
@endif
