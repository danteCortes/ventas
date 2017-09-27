<?php

namespace App\Http\Controllers;

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
    // Verificamos si existe una venta activa para este usuario.
    if ($venta = \App\Venta::where('usuario_id', \Auth::user()->id)->where('tienda_id', \Auth::user()->tienda_id)->where('estado', 1)->first()) {
      // Calculamos la comisión por uso de tarjeta.
      $comision = number_format(\App\Tarjeta::find($request->tarjeta_id)->comision / 100, 2);
      $comision = number_format($venta->total * $comision, 2, '.', ' ');
      // Si existe una venta activa, verificamos si ya se registro un pago con tarjeta para esta venta.
      if ($tarjetaVenta = TarjetaVenta::where('venta_id', $venta->id)->first()) {
        // Si hay registrado un pago con tarjeta para esta venta, actualizamos el registro.
        $tarjetaVenta->tarjeta_id = $request->tarjeta_id;
        $tarjetaVenta->operacion = $request->operacion;
        $tarjetaVenta->monto = $request->monto;
        $tarjetaVenta->comision = $comision;
        $tarjetaVenta->save();
      }else{
        $tarjetaVenta = new \App\TarjetaVenta;
        $tarjetaVenta->tarjeta_id = $request->tarjeta_id;
        $tarjetaVenta->venta_id = $venta->id;
        $tarjetaVenta->operacion = $request->operacion;
        $tarjetaVenta->monto = $request->monto;
        $tarjetaVenta->comision = $comision;
        $tarjetaVenta->save();
      }
      // retornamos los elemento que se va a llenar.
      if($comision != 0){
        // Incrementamos el total de la venta por la comision.
        $venta->total += $comision;
        $venta->save();
        // Incrementamos el total de la caja por la comisión.
        $cierre = $venta->cierre;
        $cierre->total = $venta->total;
        $cierre->save();
        //Generamos la fila de detalle por incremento de tarjeta.
        $detalle = "<tr>
          <td><button type='button' class='btn btn-xs btn-danger'>Quitar</button></td>
          <td>1</td>
          <td>COMISIÓN POR USO DE TARJETA ".$tarjetaVenta->tarjeta->nombre." ".$tarjetaVenta->tarjeta->comision."%</td>
          <td style='text-align:right'>".$tarjetaVenta->comision."</td>
          <td style='text-align:right'>".$tarjetaVenta->comision."</td>
        </tr>";
      }
        return $detalle;
    }
      return 0;
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
    public function destroy(TarjetaVenta $tarjetaVenta)
    {
        //
    }
}
