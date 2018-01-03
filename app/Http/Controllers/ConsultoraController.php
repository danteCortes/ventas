<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ConsultoraController extends Controller{

  public function inicio(){
    return view('consultoras.inicio');
  }

  public function ingresar(){
    return view('consultoras.ingresos.nuevo.inicio');
  }

  public function nuevoIngreso(Request $request){

    if (!$productoTienda = \App\ProductoTienda::where('producto_codigo', $request->producto_codigo)
      ->where('tienda_id', \Auth::user()->tienda_id)->first()) {
      $productoTienda = new \App\ProductoTienda;
      $productoTienda->producto_codigo = $request->producto_codigo;
      $productoTienda->tienda_id = \Auth::user()->tienda_id;
      $productoTienda->cantidad = 0;
      $productoTienda->save();
    }
    $productoTienda->cantidad += $request->cantidad;
    $productoTienda->save();
    return redirect('consultora/ingresar')->with('correcto', 'EL PRODUCTO SE INGRESÓ CON ÉXITO');
  }

  public function sacar(){
    return view('consultoras.salidas.nuevo.inicio');
  }

  public function nuevaSalida(Request $request){
    // Revisamos que exista el stock correcto para disminuir la cantidad.
    if ($request->stock < $request->cantidad) {
      return redirect('consultora/sacar')->with('error', 'ESTA INTENTANDO REDUCIR UNA CANTIDAD MAYOR AL STOCK EN LA TIENDA,
        INTENTE NUEVAMENTE.');
    }
    $productoTienda = \App\ProductoTienda::where('producto_codigo', $request->producto_codigo)
      ->where('tienda_id', \Auth::user()->tienda_id)->first();
    $productoTienda->cantidad -= $request->cantidad;
    $productoTienda->save();
    return redirect('consultora/sacar')->with('correcto', 'EL PRODUCTO SE INGRESÓ CON ÉXITO');
  }



}
