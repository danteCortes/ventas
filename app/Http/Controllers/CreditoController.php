<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CreditoController extends Controller{

  /**
   * Muestra la vista para realizar un crédito a un cliente.
  */
  public function index(){
    return view('creditos.nuevo');
  }

  /**
   * Agrega un detalle a la base de datos relacionado con el credito.
  */
  public function agregarDetalle(Request $request){
    // Verificamos que se está agregando una cantidad menor o igual al stock de la tienda.
    if ($request->cantidad > $request->stock) {
      // Si la cantidad que queremos agregar al credito es mayor al stock en la tienda retornamos a la vista anterior con el mensaje corresóndiente.
      return redirect('credito')->with('error', 'ESTÁ QUERIENDO VENDER MÁS DE LO QUE TIENE EN EL ALMACÉN.');
    }
    // Verificamos si existe un crédito activo (estado 1) en esta tienda y con este usuario
    if (!$credito = \App\Credito::where('usuario_id', \Auth::user()->id)->where('tienda_id', \Auth::user()->tienda_id)
    ->where('estado', 1)->first()) {
      // si no existe el credito, procedemos a crearlo.
      $credito = $this->iniciarCredito();
    }
    // Si la cantidad por agregar es menor o igual al stock de la tienda, procedemos a guardar el detalle relacionado con el crédito.
    $detalle = $this->nuevoDetalle($request, $credito->id);
    // Descontamos la cantidad agregada al credito del stock de la tienda.
    $this->descontarProducto($request);
    // Actualizamos el total del credito.
    $credito->total += $detalle->total;
    $credito->save();
    // Regresamos a la vista anterior con el mensaje correspondiente.
    return redirect('credito')->with('correcto', 'EL DETALLE DEL CRÉDITO SE AGREGÓ CON EXITO.');
  }

  public function quitarDetalle($id){
    // Primero identificamos el detalle que vamos a quitar.
    $detalle = \App\Detalle::find($id);
    // Regresamos los productos a la tienda correspondiente.
    $this->devolverProducto($detalle);
    // Descontamos el total del detalle del total del credito.
    $credito = $detalle->credito;
    $credito->total -= number_format($detalle->total, 2, '.', '');
    $credito->save();
    // Verificamos si es el último detalle del crédito.
    if (count($credito->detalles) > 1) {
      // Si el crédito tiene más de un detalle, borramos el detalle.
      $detalle->delete();
    }else{
      // Si tiene un solo detalle, borramos el crédito.
      $credito->delete();
    }
    // regresamos a la vista anterior con el mensaje correspondiente
    return redirect('credito')->with('info', 'SE QUITO UN DETALLE DEL CREDITO');
  }

  public function terminar(Request $request){
    // Primero verificamos si la persona existe en la bd.
    if ($persona = \App\Persona::find($request->documento)) {
      // Si la persona existe, actualizamos sus datos.
      $persona->nombres = $request->nombres;
      $persona->apellidos = $request->apellidos;
      $persona->direccion = $request->direccion;
      $persona->save();
    }else{
      // Si no existe, lo guardamos.
      $persona = new \App\Persona;
      $persona->dni = $request->documento;
      $persona->nombres = $request->nombres;
      $persona->apellidos = $request->apellidos;
      $persona->direccion = $request->direccion;
      $persona->save();
    }
    // Cerramos el credito.
    $credito = \App\Credito::find($request->credito_id);
    $credito->persona_dni = $request->documento;
    $credito->estado = 0;
    $credito->fecha = $request->fecha;
    $credito->save();
    // regresamos a la vista anterior con el mensaje correspondiente
    return redirect('credito')->with('correcto', 'EL CREDITO FUE GUARDADO CON ÉXITO');
  }

  public function listar(){
    return view('creditos.listar');
  }

  private function devolverProducto(\App\Detalle $detalle){
    $productoTienda = \App\ProductoTienda::where('producto_codigo', $detalle->producto_codigo)->where('tienda_id', $detalle->credito->tienda_id)->first();
    $productoTienda->cantidad += $detalle->cantidad;
    $productoTienda->save();
  }

  private function descontarProducto(Request $request){
    $productoTienda = \App\ProductoTienda::where('producto_codigo', $request->producto_codigo)->where('tienda_id', \Auth::user()->tienda_id)->first();
    $productoTienda->cantidad -= $request->cantidad;
    $productoTienda->save();
  }

  private function nuevoDetalle(Request $request, $credito_id){
    $detalle = new \App\Detalle;
    $detalle->credito_id = $credito_id;
    $detalle->producto_codigo = $request->producto_codigo;
    $detalle->cantidad = $request->cantidad;
    $detalle->precio_unidad = $request->precio_unidad;
    $detalle->total = number_format($request->precio_unidad * $request->cantidad, 2, '.', '');
    $detalle->save();
    return \App\Detalle::find($detalle->id);
  }

  private function iniciarCredito(){
    $credito = new \App\Credito;
    $credito->usuario_id = \Auth::user()->id;
    $credito->tienda_id = \Auth::user()->tienda_id;
    $credito->cierre_id = $this->cierreActual()->id;
    $credito->estado = 1;
    $credito->total = 0;
    $credito->save();

    return \App\Credito::find($credito->id);
  }

  private function cierreActual(){
    return \App\Cierre::where('usuario_id', \Auth::user()->id)->where('tienda_id', \Auth::user()->tienda_id)
    ->where('estado', 1)->first();
  }



}
