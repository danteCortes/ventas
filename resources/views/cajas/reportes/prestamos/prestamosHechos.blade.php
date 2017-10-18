<?php

$prestamos = \App\Prestamo::where('cierre_id', '=', $cierre->id)->whereNull('devuelto')->where('direccion', 1)->get();

?>
@if(count($prestamos) > 0)
<hr>
<table class="table table-condensed" id="tblPrestamosHechos">
  <tr>
    <th colspan="2" style="text-align:center; border-top:rgba(255, 255, 255, 0);">PRESTAMOS DE SALIDA</th>
  </tr>
  @foreach($prestamos as $prestamo)
  <tr>
    <th colspan="3" style="text-align:left;">Prestamo Nro {{$prestamo->id}}</th>
  </tr>
    @foreach($prestamo->detalles as $detalle)
    <tr>
      <th style="text-align:left; border-top:rgba(255, 255, 255, 0);">{{$detalle->producto->codigo}}</th>
      <th style="text-align:left; border-top:rgba(255, 255, 255, 0);">{{$detalle->producto->descripcion}}</th>
      <th style="text-align:right; border-top:rgba(255, 255, 255, 0);">{{$detalle->cantidad}}</th>
    </tr>
    @endforeach
  @endforeach
</table>
@endif
