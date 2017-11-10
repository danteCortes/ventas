<?php

namespace App\Http\Traits\Reportes\Kardex;

use Illuminate\Http\Request;

trait SalidasTrait{

  /**
   * Este método devuelve un arrleglo con el total de datos de todas las salidas
   * que hay de un producto en una tienda durante un determinado tiempo.
   * @param array $datos
   * @return array
   */
  public function totalSalidas(array $datos){
    $detallesSalida = [];

    // Juntamos los arreglo de salidas.
    $this->juntarSalidas($detallesSalida, $this->detallesVentas($datos));
    $this->juntarSalidas($detallesSalida, $this->detallesCreditos($datos));
    $this->juntarSalidas($detallesSalida, $this->detallesPrestamosSalida($datos));
    $this->juntarSalidas($detallesSalida, $this->detallesPrestamosEntradaDevuelto($datos));
    $this->juntarSalidas($detallesSalida, $this->detallesTrasladosHechos($datos));
    $this->juntarSalidas($detallesSalida, $this->detallesSeparaciones($datos));

    return $detallesSalida;
  }

  private function detallesSeparaciones(array $datos){
    $detalles = [];

    $separaciones = \DB::table('detalles')->join('separaciones', 'separaciones.id', '=', 'detalles.separacion_id')
    ->select(
      'separaciones.updated_at as fecha',
      'separaciones.id as numero',
      'detalles.cantidad as cantidad',
      'detalles.precio_unidad as precio_unidad'
    )
    ->whereRaw('separaciones.total =
      (SELECT SUM(pagos.monto) FROM pagos WHERE pagos.separacion_id = separaciones.id) + separaciones.separacion_total')
    ->where('detalles.producto_codigo', $datos['producto_codigo'])
    ->where('separaciones.tienda_id', $datos['tienda_id'])
    ->whereBetween('separaciones.updated_at', [$datos['inicio'], $datos['fin']])->get();

    foreach ($separaciones as $separacion) {
      array_push($detalles, ['fecha'=>$separacion->fecha,
        'detalle'=>'SEPARACIÓN NÚMERO '.$separacion->numero.' ENTREGADA', 'cantidad'=>$separacion->cantidad,
        'unitario'=>$separacion->precio_unidad, 'tipo'=>0]);
    }
    return $detalles;
  }

  /**
   * Este método devuelve un arreglo de arreglos con los datos de los traslados
   * hechos a otras tiendas.
   * @param array $datos
   * @return array
   */
  private function detallesTrasladosHechos(array $datos){
    $detalles = [];

    $traslados = \DB::table('detalles')->join('traslados', 'traslados.id', '=', 'detalles.traslado_id')
      ->select(
        'traslados.created_at as fecha',
        'traslados.id as numero',
        'detalles.cantidad as cantidad',
        'detalles.precio_unidad as precio_unidad'
      )
      ->where('detalles.producto_codigo', $datos['producto_codigo'])
      ->where('traslados.tienda_origen', $datos['tienda_id'])
      ->whereBetween('traslados.updated_at', [$datos['inicio'], $datos['fin']])->get();

    foreach ($traslados as $traslado) {
      array_push($detalles, ['fecha'=>$traslado->fecha,
        'detalle'=>'TRASLADO HECHO NÚMERO '.$traslado->numero, 'cantidad'=>$traslado->cantidad,
        'unitario'=>$traslado->precio_unidad, 'tipo'=>0]);
    }
    return $detalles;
  }

  /**
   * Este método devuelve un arreglo de arreglos con los datos de los detalles de
   * un presatamo que nos hizo otra entidad pero que ya fue devuelto en esta tienda y
   * entre un rango de fechas que el usuario define.
   * @param array $datos
   * @return array
   */
  private function detallesPrestamosEntradaDevuelto(array $datos){
    $detalles = [];

    $prestamos = \DB::table('detalles')->join('prestamos', 'prestamos.id', '=', 'detalles.prestamo_id')
      ->select(
        'prestamos.updated_at as fecha',
        'prestamos.id as numero',
        'detalles.cantidad as cantidad',
        'detalles.precio_unidad as precio_unidad'
      )
      ->where('detalles.producto_codigo', $datos['producto_codigo'])
      ->where('direccion', 0)
      ->whereNotNull('devuelto')
      ->where('prestamos.tienda_id', $datos['tienda_id'])
      ->whereBetween('prestamos.updated_at', [$datos['inicio'], $datos['fin']])->get();

    foreach ($prestamos as $prestamo) {
      array_push($detalles, ['fecha'=>$prestamo->fecha,
        'detalle'=>'PRÉSTAMO RECIBIDO NÚMERO '.$prestamo->numero.' DEVUELTO', 'cantidad'=>$prestamo->cantidad,
        'unitario'=>$prestamo->precio_unidad, 'tipo'=>0]);
    }
    return $detalles;
  }

  /**
   * Este método devuelve un arreglo de arreglos con los datos necesarios de los préstamos que
   * hizo la tienda hacia otra entidad y que aun no fueron devueltos.
   * @param array
   * @return array
   */
  private function detallesPrestamosSalida(array $datos){
    $detalles = [];

    $prestamos = \DB::table('detalles')->join('prestamos', 'prestamos.id', '=', 'detalles.prestamo_id')
      ->select(
        'prestamos.created_at as fecha',
        'prestamos.id as numero',
        'detalles.cantidad as cantidad',
        'detalles.precio_unidad as precio_unidad'
      )
      ->where('detalles.producto_codigo', $datos['producto_codigo'])
      ->where('direccion', 1)
      ->where('prestamos.tienda_id', $datos['tienda_id'])
      ->whereBetween('prestamos.created_at', [$datos['inicio'], $datos['fin']])->get();

    foreach ($prestamos as $prestamo) {
      array_push($detalles, ['fecha'=>$prestamo->fecha,
        'detalle'=>'PRÉSTAMO DADO NÚMERO '.$prestamo->numero, 'cantidad'=>$prestamo->cantidad,
        'unitario'=>$prestamo->precio_unidad, 'tipo'=>0]);
    }
    return $detalles;
  }

  /**
   * Este método devuelve un arreglo de arreglos con los datos de los créditos efectuadas
   * de un producto en una tienda durante un rango de tiempo determinado.
   * @param array $datos
   * @return array
   */
  private function detallesCreditos(array $datos){
    $detalles = [];

    $creditos = \DB::table('detalles')->join('creditos', 'creditos.id', '=', 'detalles.credito_id')
      ->select(
        'creditos.created_at as fecha',
        'creditos.id as numero',
        'detalles.cantidad as cantidad',
        'detalles.precio_unidad as precio_unidad'
      )
      ->where('detalles.producto_codigo', $datos['producto_codigo'])
      ->where('creditos.tienda_id', $datos['tienda_id'])
      ->whereBetween('creditos.created_at', [$datos['inicio'], $datos['fin']])->get();

    foreach ($creditos as $credito) {
      array_push($detalles, ['fecha'=>$credito->fecha,
        'detalle'=>'CRÉDITO NÚMERO '.$credito->numero, 'cantidad'=>$credito->cantidad,
        'unitario'=>$credito->precio_unidad, 'tipo'=>0]);
    }
    return $detalles;
  }

  /**
   * Este método devuelve un arreglo de arreglos con los datos de las ventas efectuadas
   * de un producto en una tienda durante un rango de tiempo determinado.
   * @param array $datos
   * @return array
   */
  private function detallesVentas(array $datos){
    $detalles = [];

    $ventas = \DB::table('detalles')->join('ventas', 'ventas.id', '=', 'detalles.venta_id')
      ->join('recibos', 'recibos.venta_id', '=', 'ventas.id')
      ->select(
        'ventas.created_at as fecha',
        'recibos.numeracion as numero',
        'detalles.cantidad as cantidad',
        'detalles.precio_unidad as precio_unidad'
      )
      ->where('detalles.producto_codigo', $datos['producto_codigo'])
      ->where('ventas.tienda_id', $datos['tienda_id'])
      ->whereBetween('ventas.created_at', [$datos['inicio'], $datos['fin']])->get();

    foreach ($ventas as $venta) {
      array_push($detalles, ['fecha'=>$venta->fecha,
      'detalle'=>'VENTA SEGUN RECIBO '.$venta->numero, 'cantidad'=>$venta->cantidad,
      'unitario'=>$venta->precio_unidad, 'tipo'=>0]);
    }
    return $detalles;
  }

  /**
   * Este método junta todas las salidas de todos los tipos en un solo arreglo.
   * @param array $detallesJuntos
   * @param array $detalles
   * @return array
   */
  public function juntarSalidas(&$detallesJuntos, $detalles){

    foreach ($detalles as $detalle) {
      array_push($detallesJuntos, $detalle);
    }

    return $detallesJuntos;
  }

  /**
   * Este método devuelve la cantidad total salida del producto de una tienda
   * en un determinado rango de fechas.
   * @param array $detalles
   * @return int
   */
  public function cantidadSalida(array $detalles){
    $total = 0;
    foreach ($detalles as $detalle) {
      $total += $detalle['cantidad'];
    }
    return $total;
  }
}

 ?>
