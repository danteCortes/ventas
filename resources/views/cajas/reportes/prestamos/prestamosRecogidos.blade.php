<?php

$prestamos = \App\Prestamo::where('cierre_id', '=', $cierre->id)->where('devuelto', 1)->where('direccion', 1)->get();

?>
@if(count($prestamos) > 0)
<hr style="margin-bottom: 1px; margin-top: 0px;">
<table class="table table-condensed" id="tblPrestamosHechos" style="margin-bottom:0px;">
  <tr>
    <th colspan="2" style="text-align:center; border-top:rgba(255, 255, 255, 0);">
      <p class="text-center" style="font-size: 12px; margin-bottom:1px;">PRESTAMOS RECOGIDOS</p></th>
  </tr>
  @foreach($prestamos as $prestamo)
  <tr>
    <td colspan="3">
      <p class="text-center" style="font-size: 12px; margin-bottom:1px;">Prestamo Nro {{$prestamo->id}}</p></td>
  </tr>
    @foreach($prestamo->detalles as $detalle)
    <tr>
      <td style="border-top:rgba(255, 255, 255, 0);">
        <p class="text-left" style="font-size: 12px; margin-bottom:1px;">{{$detalle->producto->codigo}}</p></td>
      <td style="border-top:rgba(255, 255, 255, 0);">
        <p class="text-left" style="font-size: 12px; margin-bottom:1px;">{{$detalle->producto->familia->nombre}}
          {{$detalle->producto->marca->nombre}} {{$detalle->producto->descripcion}}</p></td>
      <td style="border-top:rgba(255, 255, 255, 0);">
        <p class="text-right" style="font-size: 12px; margin-bottom:1px;">{{$detalle->cantidad}}</p></td>
    </tr>
    @endforeach
  @endforeach
</table>
@endif
