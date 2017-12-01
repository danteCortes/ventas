<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Traits\Reportes\Kardex\KardexTrait;

class ReporteController extends Controller{

  use KardexTrait;

  public function porVencer(){
    $productosPorVencer = \App\Producto::whereDate('vencimiento', '<=', date('Y-m-d', strtotime('+3 month')))
      ->whereDate('vencimiento', '>=', date('Y-m-d'))->get();
    return view('reportes.porVencer.inicio')->with('productos', $productosPorVencer);
  }

  public function vencidos(){
    $productosVencidos = \App\Producto::whereDate('vencimiento', '<', date('Y-m-d'))->get();
    return view('reportes.vencidos.inicio')->with('productos', $productosVencidos);
  }

  public function frmKardex(){
    return view('reportes.inicio');
  }

  public function crearKardex(Request $request){
    // Verificamos que inicio sea una fecha antes que fin.
    $inicio = \Carbon\Carbon::createFromFormat('Y-m-d', $request->inicio)->startOfDay();
    $fin = \Carbon\Carbon::createFromFormat('Y-m-d', $request->fin)->endOfDay();
    if ($inicio > $fin) {
      return "error";
    }
    $producto = \App\Producto::find($request->producto_codigo);
    $producto->linea;
    $producto->familia;
    $producto->marca;
    $tienda = \App\Tienda::find($request->tienda_id);

    $datos = [
      'producto_codigo'=>$request->producto_codigo,
      'tienda_id'=>$request->tienda_id,
      'inicio'=>$inicio,
      'fin'=>$fin
    ];

    return ['detalles'=>$this->detalles($datos), 'saldo_anterior'=>$this->saldoAnterior($datos),
      'producto'=>$producto, 'tienda'=>$tienda, 'inicio'=>$inicio->format('d/m/Y')];
  }

  public function crearInventario(Request $request){

    $tienda = \App\Tienda::find($request->tienda_id);

    $productosTienda = \DB::table('producto_tienda')
      ->join('productos', 'productos.codigo', '=', 'producto_tienda.producto_codigo')
      ->join('familias', 'familias.id', '=', 'productos.familia_id')
      ->join('marcas', 'marcas.id', '=', 'productos.marca_id')
      ->select(
        'productos.codigo as codigo',
        \DB::raw("concat(familias.nombre, ' ', marcas.nombre, ' ', productos.descripcion) as descripcion"),
        'producto_tienda.cantidad as cantidad',
        'productos.precio as precio'
      )
      ->where('tienda_id', $request->tienda_id)->get();
    return ['productos'=>$productosTienda, 'tienda'=>$tienda];
  }
}
