<?php
$productos = \DB::table('detalles')->join('ventas', 'ventas.id', '=', 'detalles.venta_id')
  ->join('productos', 'productos.codigo', '=', 'detalles.producto_codigo')
  ->join('familias', 'familias.id', '=', 'productos.familia_id')
  ->join('marcas', 'marcas.id', '=', 'productos.marca_id')
  ->whereDate('ventas.created_at', $fecha)
  ->where('ventas.usuario_id', $usuario->id)
  ->where('ventas.tienda_id', $tienda->id)
  ->select(
    'productos.codigo as codigo',
    DB::raw("SUM(detalles.cantidad) as cantidad"),
    'productos.descripcion as descripcion',
    'familias.nombre as familia',
    'marcas.nombre as marca'
    )->groupBy('productos.codigo', 'productos.descripcion', 'familias.nombre', 'marcas.nombre')->get();
 ?>
 @if(count($productos) > 0)
<hr style="margin-bottom: 5px; margin-top: 0px;">
<table class="table table-condensed" id="tblVentasProductos" style="margin-bottom:0px;">
  <tr>
    <th colspan="3" style="border-top:rgba(130, 130, 130, 0.5); padding-top:1px; padding-bottom:0px;">
      <p class="text-center" style="font-size: 12px; margin-bottom:1px;">RESUMEN DE VENTAS POR PRODUCTOS</p></th>
  </tr>
  @foreach($productos as $producto)
    <tr>
      <td style="border-top:rgba(130, 130, 130, 0.5); padding-top:1px; padding-bottom:0px;">
        <p class="text-left" style="font-size: 12px; margin-bottom:1px;">{{$producto->codigo}}</p></td>
      <td style="border-top:rgba(130, 130, 130, 0.5); padding-top:1px; padding-bottom:0px;">
        <p class="text-left" style="font-size: 12px; margin-bottom:1px;">{{$producto->familia}}
          {{$producto->marca}} {{$producto->descripcion}}</p></td>
      <td style="border-top:rgba(130, 130, 130, 0.5); padding-top:1px; padding-bottom:0px;">
        <p class="text-right" style="font-size: 12px; margin-bottom:1px;">{{$producto->cantidad}}</p></td>
    </tr>
  @endforeach
</table>
@endif
