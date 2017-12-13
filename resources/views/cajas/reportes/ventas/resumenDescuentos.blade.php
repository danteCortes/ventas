<?php

  $descuentos = \DB::table('detalles')
    ->join('productos', 'productos.codigo', '=', 'detalles.producto_codigo')
    ->join('ventas', 'ventas.id', '=', 'detalles.venta_id')
    ->join('recibos', 'recibos.venta_id', '=', 'ventas.id')
    ->join('familias', 'familias.id', '=', 'productos.familia_id')
    ->join('marcas', 'marcas.id', '=', 'productos.marca_id')
    ->where('detalles.descuento', '!=', 0)
    ->where('ventas.cierre_id', $cierre->id)
    ->select(
      'recibos.numeracion as numeracion',
      \DB::raw("concat(familias.nombre, ' ', marcas.nombre, ' ', productos.descripcion) as descripcion"),
      'detalles.descuento as descuento'
    )
    ->orderBy('ventas.id')
    ->get();


 ?>
@if(count($descuentos) > 0)
<hr style="margin-bottom: 1px; margin-top: 0px;">
<table class="table table-condensed" id="tblResumenVentas" style="margin-bottom:1px;">
  <tr>
    <th colspan="3" style="border-top:rgba(130, 130, 130, 0.5); padding-top:1px; padding-bottom:0px;">
      <p class="text-center" style="font-size: 12px; margin-bottom:1px;">RESUMEN DE DESCUENTOS</p></th>
  </tr>
  @foreach($descuentos as $descuento)
    <tr>
      <td style="border-top:rgba(130, 130, 130, 0.5); padding-top:1px; padding-bottom:0px;">
        <p class="text-left" style="font-size: 12px; margin-bottom:1px;">{{$descuento->numeracion}}</p></td>
      <td style="border-top:rgba(130, 130, 130, 0.5); padding-top:1px; padding-bottom:0px;">
        <p class="text-left" style="font-size: 12px; margin-bottom:1px;">{{$descuento->descripcion}}</p></td>
      <td style="border-top:rgba(130, 130, 130, 0.5); padding-top:1px; padding-bottom:0px;">
        <p class="text-right" style="font-size: 12px; margin-bottom:1px;">{{number_format($descuento->descuento, 2, '.', ' ')}}</p></td>
    </tr>
  @endforeach
</table>
@endif
