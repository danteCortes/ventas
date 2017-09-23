<?php

namespace App\Http\Controllers;

use App\Venta;
use Illuminate\Http\Request;
use Auth;

class VentaController extends Controller{

  /**
   * Muestra una lista de ventas hechas en esta tienda.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(){
    return view('ventas.inicio');
  }

  /**
   * Muestra un formulario para crear una nueva venta.
   * Fecha: 18/09/2017
   * @return \Illuminate\Http\Response
   */
  public function create(){
    return view('ventas.nuevo');
  }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Venta  $venta
     * @return \Illuminate\Http\Response
     */
    public function show(Venta $venta)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Venta  $venta
     * @return \Illuminate\Http\Response
     */
    public function edit(Venta $venta)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Venta  $venta
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Venta $venta)
    {
        //
    }

  /**
   * Remove the specified resource from storage.
   *
   * @param  \App\Venta  $venta
   * @return \Illuminate\Http\Response
   */
  public function destroy(Venta $venta)
  {
      //
  }

  /**
   * Verifica el vuelto del cliente despues de ingresar el efectivo en soles, dolares y tarjeta.
   * Fecha: 21/09/2017
  */
  public function vuelto(Request $request){
    // Verificamos si el usuario tiene una venta activa en este momento.
    if ($venta = \App\Venta::where('usuario_id', \Auth::user()->id)->where('tienda_id', \Auth::user()->tienda_id)
      ->where('estado', 1)->first()) {
      // Si tiene una venta activa, verificamos cuanto es el total de la venta.
      $total = $venta->total;
      // Asignamos el efectivo enviado por el usuario a una variable $efectivo.
      $efectivo = $request->efectivo;
      // Verificamos si está configurado un tipo de cambio.
      if($Configuracion = \App\Configuracion::whereNotNull('cambio')->first()){
        // Si etá configurado el tipo de cambio, cambiamos los dolares ingresados a soles.
        $efectivo += $request->dolares * $Configuracion->cambio;
      }else{
        // Si no está configurado el tipo de cambio, enviamos una alerta para que se configure el tipo de cambio.
        return 0;
      }
      // sumamos lo ingresado por tarjeta al efectivo.
      $efectivo += $request->tarjeta;
      // Restamos el efectivo que juntamos de la venta total para saber el vuelto.
      $vuelto = number_format($efectivo-$total, 2, '.', ' ');
      return $vuelto;
    }
    return "";
  }

  /**
   * Verifica el tipo de cambio de dolares a soles, si no hay configurado ese tipo de cambio,
   * envía un mensaje para que configure el tipo de cambio.
   * Fecha: 21/09/2017
  */
  public function tipoCambio(Request $request){
    // Verificamos si el usuario tiene una venta activa en este momento.
    if ($venta = \App\Venta::where('usuario_id', \Auth::user()->id)->where('tienda_id', \Auth::user()->tienda_id)
      ->where('estado', 1)->first()) {
      // Si tiene una venta activa, verificamos si esta configurado el tipo de cambio.
      if (!$configuracion = \App\Configuracion::whereNotNull('cambio')->first()) {
        // Si no está configurado el tipo de cambio, retornamos un 0,
        return 0;
      }
      return number_format($configuracion->cambio, 2, '.', ' ');
    }
    return 1;
  }
}
