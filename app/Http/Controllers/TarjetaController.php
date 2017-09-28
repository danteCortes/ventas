<?php

namespace App\Http\Controllers;

use App\Tarjeta;
use Illuminate\Http\Request;
use Validator;

class TarjetaController extends Controller{

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(){

    return view('tarjetas.inicio');
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request){

    Validator::make($request->all(), [
      'nombre'=>'required|string',
      'comision'=>'required|numeric',
    ])->validate();

    $tarjeta = new Tarjeta;
    $tarjeta->nombre = mb_strtoupper($request->nombre);
    $tarjeta->comision = $request->comision;
    $tarjeta->save();

    return redirect('tarjeta')->with('correcto', 'SE INGRESARON LOS DATOS DE LA NUEVA TARJETA CON ÉXITO.');
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \App\Tarjeta  $tarjeta
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, $id){

    Validator::make($request->all(), [
      'nombre'=>'required|string',
      'comision'=>'required|numeric',
    ])->validate();

    $tarjeta = Tarjeta::find($id);
    $tarjeta->nombre = mb_strtoupper($request->nombre);
    $tarjeta->comision = $request->comision;
    $tarjeta->save();

    return redirect('tarjeta')->with('correcto', 'SE MODIFICARON LOS DATOS DE LA TARJETA '.$tarjeta->nombre.' CON ÉXITO.');
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  \App\Tarjeta  $tarjeta
   * @return \Illuminate\Http\Response
   */
  public function destroy($id){
    $tarjeta = Tarjeta::find($id);
    $tarjeta->delete();

    return redirect('tarjeta')->with('info', 'LA TARJETA '.$tarjeta->nombre.' FUE ELIMINADO DE LA BASE DE DATOS.');
  }

  /**
   * Calcula el incremento que va a tener una venta al usar una tarjeta.
   * Fecha: 23/09/2017
  */
  public function comision(Request $request){
    // Verificamos si existe una venta activa ára el usuario logeado en esta tienda.
    if ($venta = \App\Venta::where('usuario_id', \Auth::user()->id)->where('tienda_id', \Auth::user()->tienda_id)
      ->where('estado', 1)->first()) {
      // Si existe una venta activa, verificamos si existe un pago con tarjeta ya registrado.
      $comision_anterior = 0;
      if($tarjetaVenta = $venta->tarjetaVenta){
        // Si existe un pago con tarjeta ya registrado para esta venta, calculamos el total de la venta sin la comision ya registrada.
        $comision_anterior = $tarjetaVenta->comision;
      }
      // Verificamos que tipo de tarjeta quiere usar el cliente.
      $tarjeta = \App\Tarjeta::find($request->tarjeta_id);
      // Calculamos cuanto sería la comisión que pagaría el cliente por el uso de la tarjeta.
      $comision = ($request->monto - $comision_anterior) * ($tarjeta->comision/100);
      return "S/. ".number_format($comision, 2, '.', ' ');
    }
    return 0;
  }
}
