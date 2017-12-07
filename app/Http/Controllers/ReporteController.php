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
      return redirect('reporte')->with('error', 'LAS FECHAS SON INCORRECTAS.');
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

  public function crearCierre(Request $request){
    $cierres = \App\Cierre::where('tienda_id', $request->tienda_id)->where('estado', 1)
      ->whereDate('created_at', $request->fecha)->get();

    $html = "<tr>
      <td><p class='text-center' style='font-size: 12px; margin-bottom:1px;'>Cierres del día ".
        \Carbon\Carbon::createFromFormat('Y-m-d', $request->fecha)->format('d/m/Y')."</p></td>";
    foreach ($cierres as $cierre) {
      $html .= "";
    }
    $html .= "</tr>";
    return $html;
  }

  public function ventas(Request $request){
    $inicio = \Carbon\Carbon::createFromFormat('Y-m-d', $request->inicio)->startOfDay();
    $fin = \Carbon\Carbon::createFromFormat('Y-m-d', $request->fin)->endOfDay();
    if ($inicio > $fin) {
      return redirect('reporte')->with('error', 'LAS FECHAS SON INCORRECTAS.');
    }
    $ventas = \App\Venta::where('tienda_id', $request->tienda_id)->whereDate('created_at', '>=', $inicio)
      ->whereDate('created_at', '<=', $fin)->get();

    $html = "<thead>
      <tr>
        <th>Ticket</th>
        <th>Fecha</th>
        <th>Cod. Producto</th>
        <th>Descripción</th>
        <th>Cant.</th>
        <th>P. Unit.</th>
        <th>P. Total</th>
      </tr>
    </thead>
    <tbody>";

    foreach($ventas as $venta){
      foreach ($venta->detalles as $detalle) {
        $html .= "<tr>
        <td>".$venta->recibo['numeracion']."</td>
        <td>".\Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $venta->created_at)->format('d/m/Y H:i:s')."</td>
        <td>".$detalle->producto->codigo."</td>
        <td>".$detalle->producto->familia->nombre." ".$detalle->producto->marca->nombre." ".$detalle->producto->descripcion."</td>
        <td>".$detalle->cantidad."</td>
        <td>".number_format($detalle->precio_unidad, 2, '.', ' ')."</td>
        <td>".number_format($detalle->total, 2, '.', ' ')."</td>
        </tr>";
      }
    }
    $html .= "</tbody>";
    return $html;
  }
}
