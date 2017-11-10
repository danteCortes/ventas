<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Traits\Reportes\Kardex\KardexTrait;

class ReporteController extends Controller{

  use KardexTrait;

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

  public function mostrarKardex(){
    return view('reportes.kardex.ficha')->with('detalles', $detalles);
  }
}
