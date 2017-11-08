<?php
$productos = \DB::table('detalles')->join('creditos', 'creditos.id', '=', 'detalles.credito_id')
  ->join('productos', 'productos.codigo', '=', 'detalles.producto_codigo')
  ->where('creditos.cierre_id', $cierre->id)->select(
    'productos.codigo as codigo',
    DB::raw("SUM(detalles.cantidad) as cantidad"),
    'productos.descripcion as descripcion'
    )->groupBy('productos.codigo', 'productos.descripcion')->get();
 ?>
 @if(count($productos) > 0)
<hr>
<table class="table table-condensed" id="tblVentasProductos">
  <tr>
    <th colspan="3" style="text-align:center; border-top:rgba(255, 255, 255, 0);">RESUMEN DE CRÉDITOS POR PRODUCTOS</th>
  </tr>
  @foreach($productos as $producto)
    <tr>
      <th style="text-align:left; border-top:rgba(255, 255, 255, 0);">{{$producto->codigo}}</th>
      <th style="text-align:left; border-top:rgba(255, 255, 255, 0);">{{$producto->descripcion}}</th>
      <th style="text-align:right; border-top:rgba(255, 255, 255, 0);">{{$producto->cantidad}}</th>
    </tr>
  @endforeach
</table>
@endif
