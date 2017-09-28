<?php

namespace App\Http\Controllers;

use App\Venta;
use Illuminate\Http\Request;
use Auth;
use Validator;

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
  public function store(Request $request){
    // Validamos el numero de documento que se está mandando.
    Validator::make($request->all(), [
      'documento'=>'nullable|max:11|min:8',
    ])->validate();
    // Pasado la verificación del documento, Verificamos el tipo de documento que se ingreso.
    if(strlen($request->documento) == 8){
      // si es un DNI,Validamos los campos enviados.
      Validator::make($request->all(), [
        'nombres'=>'required',
        'apellidos'=>'required',
      ])->validate();
      // Pasado la validación, se verifica si el cliente existe.
      if ($persona = \App\Persona::find($request->documento)) {
        // Si la persona existe, actualizamos sus datos.
        $persona->nombres = mb_strtoupper($request->nombres);
        $persona->apellidos = mb_strtoupper($request->apellidos);
        $persona->direccion = mb_strtoupper($request->direccion);
        $persona->save();
      }else{
        // Si no existe la persona lo agregamos a la base de datos.
        $persona = new \App\Persona;
        $persona->dni = $request->documento;
        $persona->nombres = mb_strtoupper($request->nombres);
        $persona->apellidos = mb_strtoupper($request->apellidos);
        $persona->direccion = mb_strtoupper($request->direccion);
        $persona->save();
      }
    }elseif (strlen($request->documento) == 11) {
      // Si es un RUC, Validamos los campos enviados.
      Validator::make($request->all(), [
        'nombre'=>'required',
        'direccion'=>'required',
      ])->validate();
      // Pasado la validación, se verifica si la empresa ya existe.
      if ($empresa = \App\Empresa::find($request->documento)) {
        // Si existe la empresa, acctualizamos sus datos.
        $empresa->nombre = mb_strtoupper($request->nombre);
        $empresa->direccion = mb_strtoupper($request->direccion);
        $empresa->save();
      }else{
        // Si no existe, guardamos los datos de la nueva empresa.
        $empresa = new \App\Empresa;
        $empresa->ruc = $request->documento;
        $empresa->nombre = mb_strtoupper($request->nombre);
        $empresa->direccion = mb_strtoupper($request->direccion);
        $empresa->save();
      }
    }
    // Verificamos que existe una venta activa para este usuario.
    if ($venta = \App\Venta::where('usuario_id', Auth::user()->id)->where('tienda_id', Auth::user()->tienda_id)
      ->where('estado', 1)->first()) {
      // Si existe una venta activa para este usuario, Verificamos el total de los montos ingresados.
      $total_soles = 0;
      if($request->efectivo){
        // Si el cliente paga en efectivo se le suma al total en soles.
        $total_soles += $request->efectivo;
      }
      if($request->dolares) {
        // Si el cliente pretende pagar en dolares, verificamos si se configuró el tipo de cambio.
        if ($configuracion = \App\Configuracion::whereNotNull('cambio')->first()) {
          // Si se configuró el tipo de cambio. procedemos a calcular su valor en soles.
          $total_soles += number_format($request->dolares * $configuracion->cambio, 2, '.', ' ');
        }else{
          // Si no se configuró el tipo de cambio retornamos a la vista de venta nueva con un mensaje de error.
          return redirect('venta/create')->with('error', 'DEBE CONFIGURAR EL TIPO DE CAMBIO ANTES DE RELIZAR UN PAGO EN DOLARES.
            DEBE HACER CLICK EN EL BOTÓN "Tipo Cambio"');
        }
      }
      if ($request->tarjeta) {
        // Si el cliente pretende pagar con tarjeta verificamos si lla registró el pago con tarjeta.
        if($tarjetaVenta = \App\TarjetaVenta::where('venta_id', $venta->id)->first()){
          // Si se registró la venta con tarjeta, verificamos que el monto ingresado corresponda al monto registrado.
          if ($tarjetaVenta->monto == $request->tarjeta) {
            // sumamos el monto al total.
            $total_soles += $request->tarjeta;
          }else{
            // Si el monto ingresado no corresponde al registrado regresamos a la vista anterior con un mensaje de error.
            return redirect('venta/create')->with('error', 'ESTA INTENTANDO INGRESAR UN PAGO CON TARJETA DIFERENTE AL QUE REGISTRÓ!.');
          }
        }else{
          // Si no se registró la venta con tarjeta, regresamos a la vista anterior con un mensaje de error.
          return redirect('venta/create')->with('error', 'DEBE REGISTRAR EL PAGO CON TARJETA ANTES DE FINALLIZAR LA VENTA.');
        }
      }
      // Verificamos si el total acumulado. es igual o mayor al total de la venta.
      if ($total_soles >= $venta->total) {
        // Si es mayor o igual al total de la venta, guardamos los montos en la base de datos.
        // Guradamos el efectivo.
        $efectivo = new \App\Efectivo;
        $efectivo->venta_id = $venta->id;
        $efectivo->monto = $request->efectivo;
        $efectivo->save;
        // Guardamos los dolares.
        $dolares = new \App\Dolar;
        $dolares->venta_id = $venta->id;
        $dolares->monto = $request->monto;
        $dolares->cambio = $configuracion->cambio;
        $dolares->save();
        // El monto en tarjeta ya se debio registrar antes de terminar la venta.
        // Terminado de guardar los montos, cerramos la venta.
        $venta->estado = 0;
        $venta->save();
        // Actualizamos la caja de la tienda y el usuario.
        $cierre = $venta->cierre;
        $cierre->total += $venta->total;
        // Creamos el recibo.
        $recibo = new \App\Recibo;
        if(strlen($request->documento) == 8){
          $recibo->persona_dni = $request->documento;
        }elseif (strlen($request->documento) == 11) {
          $recibo->empresa_ruc = $request->documento;
        }
        $recibo->venta_id = $venta->id;
        $recibo->numeracion = Auth::user()->tienda->serie."-".$this->numeracion($request->documento, Auth::user()->tienda_id);
        return redirect('imprimir-recibo/'.$venta->id)->with('correcto', 'LA VENTA SE CONCLUYÓ CON ÉXITO, PUEDE IMPRIMIR SU RECIBO.');;
      }else{
        // Si el monto acumulado es menor que el total de la venta, regresamos a la vista de la venta con un mensaje de error.
        return redirect('venta/create')->with('error', 'EL MONTO TOTAL INGRESADO NO COMPLETA EL TOTAL DE LA VENTA.');
      }
      dd($request);
    }else{
      // Si no existe una venta activa para este usuario, retornams a la vista de venta con un mensaje de error.
      return redirect('venta/create')->with('error', 'NO EXISTE UNA VENTA ACTIVA EN ESTE MOMENTO.');
    }
  }

  private function numeracion($documento, $tienda){
    // Verificamos si es boleta o factura.
    if (strlen($documento) == 1) {
      // el recibo es factura. Buscamos la última factura que se emitió.
      if($factura = \App\Recibo::whereNotNull('empresa_ruc')->whereNotNull('venta_id')->where('tienda_id', $tienda)->last()){
        $numero = explode("-", $factura->numeracion)[1];
      }
      $numero = 1;
    }else{
      // el recibo es boleta. Buscamos la última factura que se emitió.
      if($boleta = \App\Recibo::whereNull('empresa_ruc')->whereNotNull('venta_id')->where('tienda_id', $tienda)->last()){
        $numero = explode("-", $boleta->numeracion);
      }
      $numero = 1;
    }
    // rellenamos el numero a 6 dígitos.
    while (strlen($numero) < 6) {
      $numero = "0".$numero;
    }
    return $numero;
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
  public function destroy(Venta $venta){
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
      $efectivo = 0;
      $efectivo += $request->efectivo;
      // Verificamos si nos está pagando con dólares.
      if ($request->dolares != "") {
        // Verificamos si está configurado un tipo de cambio.
        if($Configuracion = \App\Configuracion::whereNotNull('cambio')->first()){
          // Si etá configurado el tipo de cambio, cambiamos los dolares ingresados a soles.
          $efectivo += $request->dolares * $Configuracion->cambio;
        }else{
          // Si no está configurado el tipo de cambio, enviamos una alerta para que se configure el tipo de cambio.
          return "error";
        }
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
