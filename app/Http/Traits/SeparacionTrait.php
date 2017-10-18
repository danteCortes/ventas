<?php

namespace App\Http\Traits;

use Illuminate\Http\Request;

trait SeparacionTrait{

  private function guardarPago(\App\Separacion $separacion, $monto){
    $pago = new \App\Pago;
    $pago->separacion_id = $separacion->id;
    $pago->cierre_id = $this->cierreActual()->id;
    $pago->usuario_id = \Auth::user()->id;
    $pago->tienda_id = \Auth::user()->tienda_id;
    $pago->monto = $monto;
    $pago->save();
    return \App\Pago::find($pago->id);
  }

  private function primerPago($monto, $separacion){

  }

  private function devolverProducto(\App\Detalle $detalle){
    $productoTienda = \App\ProductoTienda::where('producto_codigo', $detalle->producto_codigo)
      ->where('tienda_id', $detalle->separacion->tienda_id)->first();
    $productoTienda->cantidad += $detalle->cantidad;
    $productoTienda->save();
  }

  private function descontarProducto($producto_codigo, $cantidad){
    $productoTienda = \App\ProductoTienda::where('producto_codigo', $producto_codigo)
      ->where('tienda_id', \Auth::user()->tienda_id)->first();
    $productoTienda->cantidad -= $cantidad;
    $productoTienda->save();
  }

  private function cerrarSeparacion($separacion_id, \App\Persona $persona){
    $separacion = \App\Separacion::find($separacion_id);
    $separacion->persona_dni = $persona->dni;
    $separacion->estado = 0;
    $separacion->save();
    return \App\Separacion::find($separacion_id);
  }

  private function registrarCliente(Request $request){
    // Primero verificamos si la persona existe en la bd.
    if ($persona = \App\Persona::find($request->documento)) {
      // Si la persona existe, actualizamos sus datos.
      $persona->nombres = mb_strtoupper($request->nombres);
      $persona->apellidos = mb_strtoupper($request->apellidos);
      $persona->direccion = mb_strtoupper($request->direccion);
      $persona->save();
    }else{
      // Si no existe, lo guardamos.
      $persona = new \App\Persona;
      $persona->dni = $request->documento;
      $persona->nombres = mb_strtoupper($request->nombres);
      $persona->apellidos = mb_strtoupper($request->apellidos);
      $persona->direccion = mb_strtoupper($request->direccion);
      $persona->save();
    }
    return \App\Persona::find($request->documento);
  }

  private function nuevoDetalle(Request $request, $separacion_id){
    $detalle = new \App\Detalle;
    $detalle->separacion_id = $separacion_id;
    $detalle->producto_codigo = $request->producto_codigo;
    $detalle->cantidad = $request->cantidad;
    $detalle->precio_unidad = $request->precio_unidad;
    $detalle->monto_separacion = number_format($request->monto_separacion * $request->cantidad, 2, '.', '');
    $detalle->total = number_format($request->precio_unidad * $request->cantidad, 2, '.', '');
    $detalle->save();
    return \App\Detalle::find($detalle->id);
  }

  private function iniciarSeparacion(){
    $separacion = new \App\Separacion;
    $separacion->usuario_id = \Auth::user()->id;
    $separacion->tienda_id = \Auth::user()->tienda_id;
    $separacion->cierre_id = $this->cierreActual()->id;
    $separacion->estado = 1;
    $separacion->total = 0;
    $separacion->separacion_total = 0;
    $separacion->save();

    return \App\Separacion::find($separacion->id);
  }

  private function cierreActual(){
    return \App\Cierre::where('usuario_id', \Auth::user()->id)->where('tienda_id', \Auth::user()->tienda_id)
    ->where('estado', 1)->first();
  }

}

 ?>
