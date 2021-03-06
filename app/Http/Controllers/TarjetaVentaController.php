<?php

namespace App\Http\Controllers;

use App\Detalle;
use App\TarjetaVenta;
use Illuminate\Http\Request;

class TarjetaVentaController extends Controller{

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
      //
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
      //
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request){
    $validator = \Validator::make($request->all(), [
      'tarjeta_id'=>'required',
      'operacion'=>'required',
      'monto'=>'required',
    ]);
    if($validator->fails()){
      return response()->json($validator->errors()->all(), 422);
    }

    // Verificamos si existe una venta activa para este usuario.
    if ($venta = \App\Venta::where('usuario_id', \Auth::user()->id)->where('tienda_id', \Auth::user()->tienda_id)->where('estado', 1)->first()) {
      // Calculamos la comisión por uso de tarjeta.
      $comision = number_format(\App\Tarjeta::find($request->tarjeta_id)->comision / 100, 2);
      // Si existe una venta activa, verificamos si ya se registro un pago con tarjeta para esta venta.
      if ($tarjetaVenta = TarjetaVenta::where('venta_id', $venta->id)->first()) {
        // Verificamos la comision anterior.
        $comision_anterior = $tarjetaVenta->comision;
        $comision = number_format(($request->monto) * $comision, 2, '.', ' ');
        // Si hay registrado un pago con tarjeta para esta venta, actualizamos el registro.
        $tarjetaVenta->tarjeta_id = $request->tarjeta_id;
        $tarjetaVenta->operacion = $request->operacion;
        $tarjetaVenta->monto = $request->monto;
        $tarjetaVenta->comision = $comision;
        $tarjetaVenta->save();
        // Incrementamos el total de la venta por la comision.
        $venta->total += ($comision-$comision_anterior);
        $venta->save();
      }else{
        $comision = number_format($request->monto * $comision, 2, '.', ' ');
        $tarjetaVenta = new \App\TarjetaVenta;
        $tarjetaVenta->tarjeta_id = $request->tarjeta_id;
        $tarjetaVenta->venta_id = $venta->id;
        $tarjetaVenta->operacion = $request->operacion;
        $tarjetaVenta->monto = $request->monto;
        $tarjetaVenta->comision = $comision;
        $tarjetaVenta->save();
        // Verificamos si la comisión es diferente de cero.
        if($comision != 0){
          // Incrementamos el total de la venta por la comision.
          $venta->total += $comision;
          $venta->save();
        }
      }
    
      $detalles = Detalle::join('productos as p', 'p.codigo', '=', 'detalles.producto_codigo')
        ->join('familias as f', 'f.id', '=', 'p.familia_id')
        ->join('marcas as m', 'm.id', '=', 'p.marca_id')
        ->select(
          'detalles.id',
          'detalles.cantidad',
          'p.codigo',
          \DB::raw("concat(f.nombre, ' ', m.nombre, ' ', p.descripcion) as descripcion"),
          'detalles.precio_unidad',
          'detalles.total'
        )
        ->where('detalles.venta_id', $venta->id)
        ->get()
      ;
  
      $tarjetaVenta = TarjetaVenta::join('tarjetas as t', 't.id', '=', 'tarjeta_venta.tarjeta_id')
        ->select(
          'tarjeta_venta.id',
          \DB::raw("concat('COMISIÓN POR USO DE TARJETA ', t.nombre, ' ', t.comision, '%') as descripcion"),
          'tarjeta_venta.comision'
        )
        ->where('tarjeta_venta.venta_id', $venta->id)
        ->first()
      ;
  
      return [
        'venta' => $venta,
        'detalles' => $detalles,
        'tarjetaVenta' => $tarjetaVenta
      ];
    }

    return response()->json(['No hay una venta activa en este momento, agregue detalles para comenzar una venta.'], 422);
  }

  /**
   * Display the specified resource.
   *
   * @param  \App\TarjetaVenta  $tarjetaVenta
   * @return \Illuminate\Http\Response
   */
  public function show(TarjetaVenta $tarjetaVenta)
  {
      //
  }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\TarjetaVenta  $tarjetaVenta
     * @return \Illuminate\Http\Response
     */
    public function edit(TarjetaVenta $tarjetaVenta)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\TarjetaVenta  $tarjetaVenta
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TarjetaVenta $tarjetaVenta)
    {
        //
    }

  /**
   * Remove the specified resource from storage.
   *
   * @param  \App\TarjetaVenta  $tarjetaVenta
   * @return \Illuminate\Http\Response
   */
  public function destroy($id){
    $tarjetaVenta = TarjetaVenta::find($id);
    // Restamos la comision de la venta.
    $venta = $tarjetaVenta->venta;
    $venta->total -= $tarjetVenta->comision;
    $venta->save();
    // Restamos la comision de la caja.
    $caja = $venta->caja;
  }
}
