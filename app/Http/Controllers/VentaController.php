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
    return view('cambios.inicio');
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
        $persona->puntos = 0;
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
      if($request->soles){
        // Si el cliente paga en efectivo se le suma al total en soles.
        $total_soles += $request->soles;
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
      // Verificamos si el cliente quiere canjear sus puntos.
      if ($request->puntos) {
        // Si quiere canjear puntos, verificamos si tiene esa cantidad de puntos.
        if ($persona->puntos >= $request->puntos*1000) {
          // Si tiene esa cantidad de puntos,se va a ingresar el descuento en la tabla ventas.
          // Revisamos cuanto puntos esta reclamando y cuanto es su descuento.
          $descuento = $request->puntos*10;
          $venta->descuento = $descuento;

          // registramos el reclamo de los puntos en la tabla reclamos.
          $reclamo = new \App\Reclamo;
          $reclamo->persona_dni = $persona->dni;
          $reclamo->venta_id = $venta->id;
          $reclamo->puntos = $request->puntos*1000;
          $reclamo->save();

          // Reducimos el total de puntos de la persona.
          $persona->puntos -= $request->puntos*1000;

          $total_soles += $request->puntos*10;

        }else {
          return redirect('venta/create')->with('error', 'EL CLIENTE NO TIENE '.$request->puntos.'000 PUNTOS TÚ COMO TRATA DE RECLAMAR.
            INGRESE LOS DATOS NUEVAMENTE POR FAVOR.');
        }
      }
      // Verificamos si el total acumulado. es igual o mayor al total de la venta.
      if ($total_soles >= $venta->total) {
        // Si es mayor o igual al total de la venta, guardamos los montos en la base de datos.
        // Guradamos el efectivo.
        if ($request->soles) {
          $efectivo = new \App\Efectivo;
          $efectivo->venta_id = $venta->id;
          $efectivo->monto = $request->soles;
          $efectivo->save();
        }
        // Guardamos los dolares.
        if ($request->dolares) {
          $configuracion = \App\Configuracion::whereNotNull('cambio')->first();
          $dolares = new \App\Dolar;
          $dolares->venta_id = $venta->id;
          $dolares->monto = $request->dolares;
          $dolares->cambio = $configuracion->cambio;
          $dolares->save();
        }
        // El monto en tarjeta ya se debio registrar antes de terminar la venta.
        // Terminado de guardar los montos, cerramos la venta.
        $venta->estado = 0;
        $venta->save();
        // Actualizamos la caja de la tienda y el usuario.
        $cierre = $venta->cierre;
        $cierre->total += $venta->total;
        $cierre->save();
        // Creamos el recibo.
        $recibo = new \App\Recibo;
        if(strlen($request->documento) == 8){
          $recibo->persona_dni = $request->documento;
        }elseif (strlen($request->documento) == 11) {
          $recibo->empresa_ruc = $request->documento;
        }
        $recibo->venta_id = $venta->id;
        $recibo->tienda_id = \Auth::user()->tienda_id;
        $recibo->numeracion = Auth::user()->tienda->serie."-".$this->numeracion($request->documento, Auth::user()->tienda_id);
        $recibo->save();
        // Guardamos el total de puntos en la persona si hay persona.
        if ($persona = \App\Persona::find($request->documento)) {
          if ($persona->puntos) {
            $persona->puntos += number_format($venta->total, 0, '.', '');
          }else {
            $persona->puntos = number_format($venta->total, 0, '.', '');
          }
        }
        $persona->save();
        return redirect('imprimir-recibo/'.$venta->id)->with('correcto', 'LA VENTA SE CONCLUYÓ CON ÉXITO, PUEDE IMPRIMIR SU RECIBO.');;
      }else{
        // Si el monto acumulado es menor que el total de la venta, regresamos a la vista de la venta con un mensaje de error.
        return redirect('venta/create')->with('error', 'EL MONTO TOTAL INGRESADO NO COMPLETA EL TOTAL DE LA VENTA.');
      }
    }else{
      // Si no existe una venta activa para este usuario, retornams a la vista de venta con un mensaje de error.
      return redirect('venta/create')->with('error', 'NO EXISTE UNA VENTA ACTIVA EN ESTE MOMENTO.');
    }
  }

  private function numeracion($documento, $tienda){
    // Verificamos si es boleta o factura.
    if (strlen($documento) == 1) {
      // el recibo es factura. Buscamos la última factura que se emitió.
      if($factura = \App\Recibo::whereNotNull('empresa_ruc')->whereNotNull('venta_id')->where('tienda_id', $tienda)->latest('id')->first()){
        $numero = explode("-", $factura->numeracion)[1]+1;
      }else{
        $numero = 1;
      }
    }else{
      // el recibo es boleta. Buscamos la última factura que se emitió.
      if($boleta = \App\Recibo::whereNull('empresa_ruc')->whereNotNull('venta_id')->where('tienda_id', $tienda)->latest('id')->first()){
        $numero = explode("-", $boleta->numeracion)[1]+1;
      }else{
        $numero = 1;
      }
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
  public function show(Venta $venta){
      //
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  \App\Venta  $venta
   * @return \Illuminate\Http\Response
   */
  public function edit($id){
    // Primero verificamos si existe un cambio activo (estado 1) para este usuario y en esta tienda.
    if ($cambio = \App\Cambio::where('usuario_id', \Auth::user()->id)->where('tienda_id', \Auth::user()->tienda_id)
      ->where('estado', 1)->first()) {
      // Verificamos si la venta de este cambio es igual a la venta que estamos queriendo hacer cambios.
      if($cambio->venta->id != $id){
        // Si existe un cambio activo en esta tienda y con el usuario logueado lo enviamos a la vista del cambio de la venta activa.
        return redirect('venta/'.$cambio->venta->id.'/edit')->with('info', 'ESTE CAMBIO NO FUE TERMINADO,
        HAGA CLIC EN TERMINAR ANTES DE EMPEZAR OTRO CAMBIO.');
      }
    }
    // Si no existe buscamos la venta que queremos cambiar.
    $venta = Venta::find($id);
    // mostramos la vista
    return view('cambios.editar')->with('venta', $venta);
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \App\Venta  $venta
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, Venta $venta){
      //
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  \App\Venta  $venta
   * @return \Illuminate\Http\Response
   */
  public function destroy(Request $request, $id){
    // Primero verificamos si es una venta terminada (estado 0) o una venta encurso (estado 1).
    $venta = Venta::find($id);
    // Identificamos la tienda de la que se vendio.
    $tienda = \App\Tienda::find($venta->tienda_id);
    if (!$venta->estado) {
      $autorizacion = 0;
      // Si la venta esta cerrada, verificamos que se halla ingresado la contraseña de un administrador.
      foreach (\App\Usuario::where('tipo', 1)->get() as $administrador) {
        // verificamos que la contraseña ingresada sea del administrador.
        if(\Hash::check($request->password, $administrador->password)){
          $autorizacion = 1;
          break;
        }
      }
    }else{
      $autorizacion = 1;
    }
    if (!$autorizacion) {
      return redirect('venta')->with('error', 'LA CONTRASEÑA INGRESADA NO PERTENECE AL ADMINISTRADOR.');
    }
    // Procedemos a retornar la cantidad de los productos a la tienda que se vendió.
    foreach ($venta->detalles as $detalle) {
      // Recorremos todos los detalles de la venta e identificamos el producto que se vendio.
      $producto = \App\Producto::find($detalle->producto_codigo);
      // Identificamos la cantidad de productos que se vedio.
      $cantidad = $detalle->cantidad;
      // Regresamos la cantidad a la tienda de origen.
      $productoTienda = \App\productoTienda::where('producto_codigo', $producto->codigo)
        ->where('tienda_id', $tienda->id)->first();
      $productoTienda->cantidad += $cantidad;
      $productoTienda->save();
    }
    // Ya regresados las cantidades de los productos a las tiendas de origen, procedemos a eliminar la venta.
    $venta->delete();
    return redirect('venta')->with('info', 'SE ELIMINÓ UNA VENTA DE ESTA TIENDA, ESTE CAMBIO SE REGISTRÓ
      CON SU USUARIO '.\Auth::user()->persona->nombres.' '.\Auth::user()->persona->apellidos.'.');
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

  public function imprimirRecibo($recibo_id){
    $recibo = Venta::find($recibo_id)->recibo;
    return view('ventas.recibo')->with('recibo', $recibo);
  }

  public function listar(Request $request){

    $line_quantity = intVal($request->current);
    $line_number = intVal($request->rowCount);
    $where = $request->searchPhrase;
    $sort = $request->sort;

    if (isset($sort['ticket'])) {
        $order_by = 'numeracion';
        $order_name = $sort['ticket'];
    }
    if (isset($sort['fecha'])) {
        $order_by = 'updated_at';
        $order_name = $sort['fecha'];
    }
    if (isset($sort['total'])) {
        $order_by = 'total';
        $order_name = $sort['total'];
    }

    $skip = 0;
    $take = $line_number;

    if ($line_quantity > 1) {
        //DESDE QUE REGISTRO SE INICIA
        $skip = $line_number * ($line_quantity - 1);
        //CANTIDAD DE RANGO
        $take = $line_number;
    }

    //Grupo de datos que enviaremos al modelo para filtrar
    if ($request->rowCount < 0) {

    } else {
      if (empty($where)) {
        $ventas = Venta::join('recibos', 'ventas.id', '=', 'recibos.venta_id')
          ->where('ventas.tienda_id', \Auth::user()->tienda_id)
          ->select(
            'recibos.numeracion as ticket',
            'ventas.updated_at as fecha',
            'ventas.total as total',
            'ventas.id as id'
            )
          ->distinct()
          ->offset($skip)
          ->limit($take)
          ->orderBy($order_by, $order_name)
          ->get();
      } else {
        $ventas = Venta::join('recibos', 'ventas.id', '=', 'recibos.venta_id')
          ->where('ventas.tienda_id', \Auth::user()->tienda_id)
          ->where('recibos.numeracion', 'like', '%'.$where.'%')
          ->orWhere('ventas.updated_at', 'like', '%'.$where.'%')
          ->orWhere('ventas.total', 'like', '%'.$where.'%')
          ->select(
            'recibos.numeracion as ticket',
            'ventas.updated_at as fecha',
            'ventas.total as total',
            'ventas.id as id'
            )
          ->distinct()
          ->offset($skip)
          ->limit($take)
          ->orderBy($order_by, $order_name)
          ->get();
      }

      if (empty($where)) {
        $total = Venta::where('tienda_id', \Auth::user()->tienda_id);

        $total = count($total);
      } else {
        $total = Venta::join('recibos', 'ventas.id', '=', 'recibos.venta_id')
          ->where('ventas.tienda_id', \Auth::user()->tienda_id)
          ->where('recibos.numeracion', 'like', '%'.$where.'%')
          ->orWhere('ventas.updated_at', 'like', '%'.$where.'%')
          ->orWhere('ventas.total', 'like', '%'.$where.'%')
          ->distinct()
          ->get();

        $total = count($total);
      }
    }

    $datas = [];

    foreach ($ventas as $venta):

      $data = array_merge(
        array
        (
          "ticket" => $venta->ticket,
          "fecha" => \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $venta->fecha)->format('d/m/Y H:i A'),
          "total" => $venta->total,
          "id" => $venta->id,
        )
      );
      //Asignamos un grupo de datos al array datas
      $datas[] = $data;
    endforeach;

    return response()->json(
      array(
        'current' => $line_quantity,
        'rowCount' => $line_number,
        'rows' => $datas,
        'total' => $total,
        'skip' => $skip,
        'take' => $take
      )
    );

  }

  public function buscar(Request $request){
    $venta = Venta::find($request->id);
    $recibo = $venta->recibo;
    $tablaTicket = "<table class='table table-condensed'>
      <tr>
        <th colspan='3' style='text-align:center; border-top:rgba(255, 255, 255, 0);'>".$recibo->venta->tienda->nombre."</th>
      </tr>
      <tr>
        <th colspan='3' style='text-align:center; border-top:rgba(255, 255, 255, 0);'>".$recibo->venta->tienda->direccion."</th>
      </tr>
      <tr>
        <th colspan='3' style='text-align:center; border-top:rgba(255, 255, 255, 0);'>R.U.C. N° ".$recibo->venta->tienda->ruc."</th>
      </tr>
      <tr>
        <th colspan='3' style='text-align:center; border-top:rgba(255, 255, 255, 0);'>N° DE SERIE ".$recibo->venta->tienda->ticketera."</th>
      </tr>
      <tr>
        <th colspan='3' style='text-align:right; border-top:rgba(255, 255, 255, 0);'>".$recibo->numeracion."</th>
      </tr>
      <tr>
        <th colspan='3' style='text-align:right; border-top:rgba(255, 255, 255, 0);'>".$recibo->venta->updated_at."</th>
      </tr>";
      foreach($recibo->venta->detalles as $detalle){
        $tablaTicket .= "<tr>
          <th style='text-align:center; border-top:rgba(255, 255, 255, 0);'>".$detalle->cantidad."</th>
          <th style='text-align:left; border-top:rgba(255, 255, 255, 0);'>".$detalle->producto->descripcion."</th>
          <th style='text-align:right; border-top:rgba(255, 255, 255, 0);'>".$detalle->total."</th>
        </tr>";
      }
      $tablaTicket .= "<tr>
        <th colspan='2' style='text-align:right; border-top:rgba(255, 255, 255, 0);'>TOTAL</th>
        <th colspan='2' style='text-align:right; border-top:rgba(255, 255, 255, 0);'>".$recibo->venta->total."</th>
      </tr>
    </table>";
    return ['ticket'=>$tablaTicket, 'recibo'=>$recibo, 'venta'=>$venta];
  }


}
