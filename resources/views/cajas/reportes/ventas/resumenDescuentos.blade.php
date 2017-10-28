<?php

  $descuentos = \DB::table('detalles')
    ->join('productos', 'productos.codigo', '=', 'detalles.producto_codigo')
    ->join('ventas', 'ventas.id', '=', 'detalles.venta_id')
    ->join('recibos', 'recibos.venta_id', '=', 'ventas.id')
    ->where('detalles.descuento', '!=', 0)
    ->where('ventas.cierre_id', $cierre->id)
    ->select(
      'recibos.numeracion as numeracion',
      'productos.descripcion as descripcion',
      'detalles.descuento as descuento'
    )
    ->orderBy('ventas.id')
    ->get();


 ?>
@if(count($descuentos) > 0)
<hr>
<table class="table table-condensed" id="tblResumenVentas">
  <tr>
    <th colspan="3" style="text-align:center; border-top:rgba(255, 255, 255, 0);">RESUMEN DE DESCUENTOS</th>
  </tr>
  @foreach($descuentos as $descuento)
    <tr>
      <th style="text-align:left; border-top:rgba(255, 255, 255, 0);">{{$descuento->numeracion}}</th>
      <th style="text-align:left; border-top:rgba(255, 255, 255, 0);">{{$descuento->descripcion}}</th>
      <th style="text-align:right; border-top:rgba(255, 255, 255, 0);">{{number_format($descuento->descuento, 2, '.', ' ')}}</th>
    </tr>
  @endforeach
</table>
@endif
