<?php

namespace App\Http\Traits\Reportes\Kardex;

use Illuminate\Http\Request;

trait IngresosTrait{

  /**
   * Este método devuelve un arrleglo con el total de datos de todos los ingresos
   * de hay de un producto en una tienda durante un determinado tiempo.
   * @param array $datos
   * @return array
   */
  public function totalIngresos(array $datos){
    $detallesIngreso = [];

    // Juntamos los arreglo de ingresos.
    $this->juntarIngresos($detallesIngreso, $this->detallesIngresosTienda($datos));
    $this->juntarIngresos($detallesIngreso, $this->detallesPrestamosEntrada($datos));
    $this->juntarIngresos($detallesIngreso, $this->detallesPrestamosSalidaDevuelto($datos));
    $this->juntarIngresos($detallesIngreso, $this->detallesTrasladosRecibidos($datos));

    return $detallesIngreso;
  }

  /**
   * Este método devuelve un arreglo de arreglos con los datos de los traslados
   * que otra tienda hizo hacia esta tienda.
   * @param array $datos
   * @return array
   */
  private function detallesTrasladosRecibidos(array $datos){
    $detalles = [];

    $traslados = \DB::table('detalles')->join('traslados', 'traslados.id', '=', 'detalles.traslado_id')
      ->select(
        'traslados.created_at as fecha',
        'traslados.id as numero',
        'detalles.cantidad as cantidad',
        'detalles.precio_unidad as precio_unidad'
      )
      ->where('detalles.producto_codigo', $datos['producto_codigo'])
      ->where('traslados.tienda_destino', $datos['tienda_id'])
      ->whereBetween('traslados.created_at', [$datos['inicio'], $datos['fin']])->get();

    foreach ($traslados as $traslado) {
      array_push($detalles, ['fecha'=>$traslado->fecha,
        'detalle'=>'TRASLADO RECIBIDO NÚMERO '.$traslado->numero, 'cantidad'=>$traslado->cantidad,
        'unitario'=>$traslado->precio_unidad, 'tipo'=>1]);
    }
    return $detalles;
  }

  /**
   * Este método devuelve un arreglo de arreglos con los datos de los detalles de
   * un presatamo dado a otra entidad pero que ya fue devuelto en esta tienda y
   * entre un rango de fechas que el usuario define.
   * @param array $datos
   * @return array
   */
  private function detallesPrestamosSalidaDevuelto(array $datos){
    $detalles = [];

    $prestamos = \DB::table('detalles')->join('prestamos', 'prestamos.id', '=', 'detalles.prestamo_id')
      ->select(
        'prestamos.updated_at as fecha',
        'prestamos.id as numero',
        'detalles.cantidad as cantidad',
        'detalles.precio_unidad as precio_unidad'
      )
      ->where('detalles.producto_codigo', $datos['producto_codigo'])
      ->where('prestamos.direccion', 1)
      ->whereNotNull('devuelto')
      ->where('prestamos.tienda_id', $datos['tienda_id'])
      ->whereBetween('prestamos.updated_at', [$datos['inicio'], $datos['fin']])->get();

    foreach ($prestamos as $prestamo) {
      array_push($detalles, ['fecha'=>$prestamo->fecha,
        'detalle'=>'PRÉSTAMO DADO NÚMERO '.$prestamo->numero.' DEVUELTO', 'cantidad'=>$prestamo->cantidad,
        'unitario'=>$prestamo->precio_unidad, 'tipo'=>1]);
    }
    return $detalles;
  }

  /**
   * Este método devuelve un arreglo de arreglos con los datos necesarios de los préstamos que
   * se hizo la tienda de otra entidad y que aun no fueron devueltos.
   * @param array
   * @return array
   */
  private function detallesPrestamosEntrada(array $datos){
    $detalles = [];

    $prestamos = \DB::table('detalles')->join('prestamos', 'prestamos.id', '=', 'detalles.prestamo_id')
      ->select(
        'prestamos.created_at as fecha',
        'prestamos.id as numero',
        'detalles.cantidad as cantidad',
        'detalles.precio_unidad as precio_unidad'
      )
      ->where('prestamos.direccion', 0)
      ->where('detalles.producto_codigo', $datos['producto_codigo'])
      ->where('prestamos.tienda_id', $datos['tienda_id'])
      ->whereBetween('prestamos.created_at', [$datos['inicio'], $datos['fin']])->get();

    foreach ($prestamos as $prestamo) {
      array_push($detalles, ['fecha'=>$prestamo->fecha,
        'detalle'=>'PRÉSTAMO RECIBIDO NÚMERO '.$prestamo->numero, 'cantidad'=>$prestamo->cantidad,
        'unitario'=>$prestamo->precio_unidad, 'tipo'=>1]);
    }
    return $detalles;
  }

  /**
   * Este método devuelve un arreglo de arreglos de los detalles de ingreso de productos a las tiendas
   * en el momento que se hizo la compra con los datos necesarios para general el kardex.
   * estructura del arreglo: ['fecha', 'detalle', 'unitario', 'tipo']
   * @param array $datos
   * @return array
   */
  private function detallesIngresosTienda(array $datos){
    $detalles = [];
    $productoTienda = \App\ProductoTienda::where('producto_codigo', $datos['producto_codigo'])
      ->where('tienda_id', $datos['tienda_id'])->first();

    $ingresos = \DB::table('ingresos')->join('detalles', 'detalles.id', '=', 'ingresos.detalle_id')
      ->join('compras', 'compras.id', '=', 'detalles.compra_id')
      ->select(
        'ingresos.created_at as fecha',
        'compras.numero as numero',
        'ingresos.cantidad as cantidad',
        'detalles.precio_unidad as precio_unidad'
        )
      ->where('ingresos.producto_tienda_id', $productoTienda->id)
      ->whereBetween('ingresos.created_at', [$datos['inicio'], $datos['fin']])->get();

    foreach ($ingresos as $ingreso) {
      array_push($detalles, ['fecha'=>$ingreso->fecha,
        'detalle'=>'COMPRA SEGÚN RECIBO '.$ingreso->numero, 'cantidad'=>$ingreso->cantidad,
        'unitario'=>$ingreso->precio_unidad, 'tipo'=>1]);
    }

    return $detalles;
  }

  /**
   * Este método junta todos los ingresos de todos los tipos en un sollo arreglo.
   * @param array $detallesJuntos
   * @param array $detalles
   * @return array
   */
  private function juntarIngresos(&$detallesJuntos, $detalles){

    foreach ($detalles as $detalle) {
      array_push($detallesJuntos, $detalle);
    }

    return $detallesJuntos;
  }

  /**
   * Este método devuelve la cantidad total ingresada del producto a una tienda
   * en un determinado rango de fechas.
   * @param array $detalles
   * @return int
   */
  public function cantidadIngresada(array $detalles){
    $total = 0;
    foreach ($detalles as $detalle) {
      $total += $detalle['cantidad'];
    }
    return $total;
  }

}

 ?>
