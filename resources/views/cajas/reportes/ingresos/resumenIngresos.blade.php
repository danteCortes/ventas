<?php

  $ingresos = \App\OtroIngreso::whereDate('created_at', $fecha)
    ->where('usuario_id', $usuario->id)
    ->where('tienda_id', $tienda->id)
    ->get()
  ;

 ?>
@if(count($ingresos) > 0)
<hr style="margin-bottom: 1px; margin-top: 0px;">
<table class="table table-condensed" id="tblResumenVentas" style="margin-bottom:0px;">
  <tr>
    <th colspan="2" style="text-align:center; border-top:rgba(255, 255, 255, 0);">
      <p class="text-center" style="font-size: 12px; margin-bottom:1px;">RESUMEN DE INGRESOS DE EFECTIVO</p></th>
  </tr>
  @foreach($ingresos as $ingreso)
    <tr>
      <td style="border-top:rgba(130, 130, 130, 0.5); padding-top:1px; padding-bottom:0px;">
        <p class="text-left" style="font-size: 12px; margin-bottom:1px;">{{$ingreso->descripcion}}</p></td>
      <td style="border-top:rgba(130, 130, 130, 0.5); padding-top:1px; padding-bottom:0px;">
        <p class="text-right" style="font-size: 12px; margin-bottom:1px;">{{number_format($ingreso->total, 2, '.', ' ')}}</p></td>
    </tr>
  @endforeach
</table>
@endif
