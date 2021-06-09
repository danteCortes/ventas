<?php

namespace App\Http\Controllers;

use App\Tienda;
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
    $tienda = Tienda::find($request->tienda_id);
    $fecha = $request->fecha_cierre;

    return view('reportes.cierres.vista', compact('tienda', 'fecha'));


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

  public function resumenVentas(Request $request){
    $inicio = \Carbon\Carbon::createFromFormat('Y-m-d', $request->inicio)->startOfDay();
    $fin = \Carbon\Carbon::createFromFormat('Y-m-d', $request->fin)->endOfDay();
    if ($inicio > $fin) {
      return redirect('reporte')->with('error', 'LAS FECHAS SON INCORRECTAS.');
    }
    $ventas = \DB::table('ventas as v')
      ->join('recibos as r', 'r.venta_id', '=', 'v.id')
      ->where('v.tienda_id', $request->tienda_id)
      ->where('v.estado', 0)
      ->where('v.created_at', '>=', $inicio)
      ->where('v.created_at', '<=', $fin)
      ->whereNull('r.empresa_ruc')
      ->select(
        \DB::raw('count(r.id) as total_ventas'),
        \DB::raw('min(r.numeracion) as minimo'),
        \DB::raw('max(r.numeracion) as maximo'),
        \DB::raw('sum(v.total) as total'),
        \DB::raw('date(v.created_at) as created_at')
        )
      ->groupBy(\DB::raw('date(v.created_at)'))
      ->get();

    $tienda = \App\Tienda::find($request->tienda_id);

    $html = "<thead>
      <tr>
        <th colspan='3' rowspan='2'align='left' style='vertical-align:middle; border-right-width:0px;
        border-right-width:0px;'><p style='margin-bottom:0px;'>".$tienda->nombre." - ".$tienda->direccion."</p></th>
        <th style='border-left-width:0px; border-bottom-width:0px;'><p style='margin-bottom:0px;'>Fecha: ".
        \Carbon\Carbon::now()->format('d/m/Y')."</p></th>
      </tr>
      <tr>
        <th style='border-left-width:0px; border-bottom-width:0px; border-top-width:0px;'><p style='margin-bottom:0px;'>
        Hora: ".\Carbon\Carbon::now()->format('H:i:s')."</p></th>
      </tr>
      <tr>
        <th colspan='4' style='border-top-width:0px;'><p align='center' style='margin-bottom:0px; vertical-align:middle;'>
        TICKETS BOLETAS del ".$inicio->format('d/m/Y')." al ".$fin->format('d/m/Y')."</p></th>
      </tr>
      <tr>
        <th>Fecha</th>
        <th>Del</th>
        <th>Al</th>
        <th>P. Total</th>
      </tr>
    </thead>
    <tbody>";

    $total = 0;
    foreach($ventas as $venta){
      $total += $venta->total;
      $html .= "<tr>
      <td align='right'>".\Carbon\Carbon::createFromFormat('Y-m-d', $venta->created_at)->format('d/m/Y')."</td>
      <td align='right'>".$venta->minimo."</td>
      <td align='right'>".$venta->maximo."</td>
      <td align='right'>".number_format($venta->total, 2, '.', ' ')."</td>
      </tr>";
    }
    $html .= "</tbody>
    <tfood>
    <tr>
    <td align='right'></td>
    <td align='right'></td>
    <td align='right'></td>
    <td align='right'>".number_format($total, 2, '.', ' ')."</td>
    </tr>
    </tfood>";
    return $html;
  }

  public function resumenVentasTickets(Request $request){
    $inicio = \Carbon\Carbon::createFromFormat('Y-m-d', $request->inicio)->startOfDay();
    $fin = \Carbon\Carbon::createFromFormat('Y-m-d', $request->fin)->endOfDay();
    if ($inicio > $fin) {
      return redirect('reporte')->with('error', 'LAS FECHAS SON INCORRECTAS.');
    }
    $boletas = \DB::table('ventas as v')
      ->join('recibos as r', 'r.venta_id', '=', 'v.id')
      ->where('v.tienda_id', $request->tienda_id)
      ->where('v.estado', 0)
      ->where('v.created_at', '>=', $inicio)
      ->where('v.created_at', '<=', $fin)
      ->whereNull('r.empresa_ruc')
      ->select(
        \DB::raw('sum(v.total) as total'),
        \DB::raw('date(v.created_at) as created_at')
        )
      ->groupBy(\DB::raw('date(v.created_at)'))
      ->get();

    $facturas = \DB::table('ventas as v')
      ->join('recibos as r', 'r.venta_id', '=', 'v.id')
      ->where('v.tienda_id', $request->tienda_id)
      ->where('v.estado', 0)
      ->where('v.created_at', '>=', $inicio)
      ->where('v.created_at', '<=', $fin)
      ->whereNotNull('r.empresa_ruc')
      ->select(
        \DB::raw('sum(v.total) as total'),
        \DB::raw('date(v.created_at) as created_at')
        )
      ->groupBy(\DB::raw('date(v.created_at)'))
      ->get();

    $tienda = \App\Tienda::find($request->tienda_id);

    $html = "<thead>
      <tr>
        <th colspan='3' rowspan='2'align='left' style='vertical-align:middle; border-right-width:0px;
        border-right-width:0px;'><p style='margin-bottom:0px;'>".$tienda->nombre." - ".$tienda->direccion."</p></th>
        <th style='border-left-width:0px; border-bottom-width:0px;'><p style='margin-bottom:0px;'>Fecha: ".
        \Carbon\Carbon::now()->format('d/m/Y')."</p></th>
      </tr>
      <tr>
        <th style='border-left-width:0px; border-bottom-width:0px; border-top-width:0px;'><p style='margin-bottom:0px;'>
        Hora: ".\Carbon\Carbon::now()->format('H:i:s')."</p></th>
      </tr>
      <tr>
        <th colspan='4' style='border-top-width:0px;'><p align='center' style='margin-bottom:0px; vertical-align:middle;'>
        RESUMEN DE VENTAS del ".$inicio->format('d/m/Y')." al ".$fin->format('d/m/Y')."</p></th>
      </tr>
      <tr>
        <th></th>
        <th>Tickets Boletas</th>
        <th>Tickets Facturas</th>
        <th></th>
      </tr>
      <tr>
        <th>Fecha</th>
        <th> Serie ".$tienda->serie."</th>
        <th> Serie ".$tienda->serie."</th>
        <th>Total</th>
      </tr>
    </thead>
    <tbody>";

    $total_boletas = 0;
    $total_facturas = 0;
    $total = 0;
    foreach($boletas as $boleta){
      $total_boletas += $boleta->total;
      foreach ($facturas as $factura) {
        if($boleta->created_at == $factura->created_at){
          $total += $boleta->total+$factura->total;
          $total_facturas += $factura->total;
          $html .= "<tr>
          <td align='right'>".\Carbon\Carbon::createFromFormat('Y-m-d', $boleta->created_at)->format('d/m/Y')."</td>
          <td align='right'>".number_format($boleta->total, 2, '.', ' ')."</td>
          <td align='right'>".number_format($factura->total, 2, '.', ' ')."</td>
          <td align='right'>".number_format($boleta->total+$factura->total, 2, '.', ' ')."</td>
          </tr>";
          break;
        }else{
          $total += $boleta->total;
          $html .= "<tr>
          <td align='right'>".\Carbon\Carbon::createFromFormat('Y-m-d', $boleta->created_at)->format('d/m/Y')."</td>
          <td align='right'>".number_format($boleta->total, 2, '.', ' ')."</td>
          <td align='right'>0.00</td>
          <td align='right'>".number_format($boleta->total, 2, '.', ' ')."</td>
          </tr>";
          break;
        }
      }
    }
    $html .= "</tbody>
    <tfood>
    <tr>
    <td align='right'></td>
    <td align='right'>".number_format($total_boletas, 2, '.', ' ')."</td>
    <td align='right'>".number_format($total_facturas, 2, '.', ' ')."</td>
    <td align='right'>".number_format($total, 2, '.', ' ')."</td>
    </tr>
    </tfood>";
    return $html;
  }

  public function ventasDiarias(Request $request){
    $inicio = \Carbon\Carbon::createFromFormat('Y-m-d', $request->inicio)->startOfDay();
    $fin = \Carbon\Carbon::createFromFormat('Y-m-d', $request->fin)->endOfDay();
    if ($inicio > $fin) {
      return redirect('reporte')->with('error', 'LAS FECHAS SON INCORRECTAS.');
    }
    $boletas = \DB::table('ventas as v')
      ->join('recibos as r', 'r.venta_id', '=', 'v.id')
      ->where('v.tienda_id', $request->tienda_id)
      ->where('v.estado', 0)
      ->whereNull('r.empresa_ruc', 0)
      ->select(
        \DB::raw('count(r.id) as total_ventas'),
        \DB::raw('count(r.id) as total_ventas'),
        \DB::raw('min(r.numeracion) as minimo'),
        \DB::raw('max(r.numeracion) as maximo'),
        \DB::raw('sum(v.total) as total'),
        \DB::raw('date(v.created_at) as created_at')
        )
      ->groupBy(\DB::raw('date(v.created_at)'))
      ->having('v.created_at', '>=', $inicio)
      ->having('v.created_at', '<=', $fin)
      ->get();

    $tienda = \App\Tienda::find($request->tienda_id);

    $html = "<thead>
      <tr>
        <th colspan='3' rowspan='2'align='left' style='vertical-align:middle; border-right-width:0px;
        border-right-width:0px;'><p style='margin-bottom:0px;'>".$tienda->nombre." - ".$tienda->direccion."</p></th>
        <th style='border-left-width:0px; border-bottom-width:0px;'><p style='margin-bottom:0px;'>Fecha: ".
        \Carbon\Carbon::now()->format('d/m/Y')."</p></th>
      </tr>
      <tr>
        <th style='border-left-width:0px; border-bottom-width:0px; border-top-width:0px;'><p style='margin-bottom:0px;'>
        Hora: ".\Carbon\Carbon::now()->format('H:i:s')."</p></th>
      </tr>
      <tr>
        <th colspan='4' style='border-top-width:0px;'><p align='center' style='margin-bottom:0px; vertical-align:middle;'>
        RESUMEN DE VENTAS del ".$inicio->format('d/m/Y')." al ".$fin->format('d/m/Y')."</p></th>
      </tr>
      <tr>
        <th></th>
        <th>Tickets Boletas</th>
        <th>Tiquets Facturas</th>
        <th></th>
      </tr>
      <tr>
        <th>Fecha</th>
        <th>".$tienda->serie."</th>
        <th>".$tienda->serie."</th>
        <th>P. Total</th>
      </tr>
    </thead>
    <tbody>";

    $total = 0;
    foreach($boletas as $venta){
      $total += $venta->total;
      $html .= "<tr>
      <td align='right'>".\Carbon\Carbon::createFromFormat('Y-m-d', $venta->created_at)->format('d/m/Y')."</td>
      <td align='right'>".$venta->minimo."</td>
      <td align='right'>".$venta->maximo."</td>
      <td align='right'>".number_format($venta->total, 2, '.', ' ')."</td>
      </tr>";
    }
    $html .= "</tbody>
    <tfood>
    <tr>
    <td align='right'></td>
    <td align='right'></td>
    <td align='right'></td>
    <td align='right'>".number_format($total, 2, '.', ' ')."</td>
    </tr>
    </tfood>";
    return $html;
  }
}
