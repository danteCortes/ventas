<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReporteController extends Controller{

  public function frmKardex(){
    return view('reportes.inicio');
  }

  public function crearKardex(Request $request){
    // Verificamos que inicio sea una fecha antes que fin.
    $inicio = \Carbon\Carbon::createFromFormat('Y-m-d', $request->inicio);
    $fin = \Carbon\Carbon::createFromFormat('Y-m-d', $request->fin);
    if ($inicio > $fin) {
      return "error";
    }
    // Mostramos el kardex del producto en cuestion.
    $producto = \App\Producto::find($request->producto_codigo);
    $producto_tienda = \App\ProductoTienda::where('producto_codigo', $request->producto_codigo)
      ->where('tienda_id', $request->tienda_id)->first();
    $detalles = \DB::table('detalles')
      ->where('detalles.producto_codigo', $request->producto_codigo)
      ->orWhereIn('venta_id', \DB::table('ventas')->select('id')->where('tienda_id', $request->tienda_id)->whereBetween('created_at', [$inicio, $fin]))
      ->orWhereIn('compra_id', \DB::table('compras')->select('id')->where('tienda_id', $request->tienda_id)->whereBetween('created_at', [$inicio, $fin]))
      ->orWhereIn('credito_id', \DB::table('creditos')->select('id')->where('tienda_id', $request->tienda_id)->whereBetween('created_at', [$inicio, $fin]))
      ->orWhereIn('prestamo_id', \DB::table('prestamos')->select('id')->where('tienda_id', $request->tienda_id)->whereBetween('created_at', [$inicio, $fin]))
      ->orWhereIn('traslado_id', \DB::table('traslados')->select('id')->where('tienda_id', $request->tienda_id)->whereBetween('created_at', [$inicio, $fin]))
      ->orWhereIn('separacion_id', \DB::table('separaciones')->select('id')->where('tienda_id', $request->tienda_id)->whereBetween('created_at', [$inicio, $fin]))
      ->get();

    $cantidad = $producto_tienda->cantidad;

    $ingresos = \DB::table('detalles')
      ->where('detalles.producto_codigo', $request->producto_codigo)
      ->orWhereIn('compra_id', \DB::table('compras')->select('id')->where('tienda_id', $request->tienda_id)
        ->whereBetween('created_at', [$inicio, $fin]))
      ->orWhereIn('prestamo_id', \DB::table('prestamos')->select('id')->where('tienda_id', $request->tienda_id)
        ->whereBetween('updated_at', [$inicio, $fin])->where('direccion', 0)->whereNull('devuelto'))
      ->orWhereIn('prestamo_id', \DB::table('prestamos')->select('id')->where('tienda_id', $request->tienda_id)
        ->whereBetween('updated_at', [$inicio, $fin])->where('direccion', 1)->whereNotNull('devuelto'))
      ->orWhereIn('traslado_id', \DB::table('traslados')->select('id')->where('tienda_origen', $request->tienda_id)
        ->whereBetween('updated_at', [$inicio, $fin]))
      ->orWhereIn('separacion_id', \DB::table('separaciones')->select('id')->where('tienda_id', $request->tienda_id)
        ->whereBetween('created_at', [$inicio, $fin]))
      ->get()->sum('cantidad');

    return $ingresos;
  }

  public function mostrarKardex(){
    return view('reportes.kardex.ficha')->with('detalles', $detalles);
  }
}
