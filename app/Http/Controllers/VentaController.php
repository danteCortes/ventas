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
            $persona->save();
          }else {
            $persona->puntos = number_format($venta->total, 0, '.', '');
            $persona->save();
          }
        }
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
      if($recibo = \App\Recibo::whereNotNull('venta_id')->where('tienda_id', $tienda)->latest('id')->first()){
        $numero = explode("-", $recibo->numeracion)[1]+1;
      }else{
        $numero = 1;
      }
    // rellenamos el numero a 6 dígitos.
    while (strlen($numero) < 8) {
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
      $productoTienda = \App\ProductoTienda::where('producto_codigo', $producto->codigo)
        ->where('tienda_id', $tienda->id)->first();
      $productoTienda->cantidad += $cantidad;
      $productoTienda->save();
    }
    // descontamos el total del cierre.
    $cierre = $venta->cierre;
    $cierre->total -= $venta->total;
    $cierre->save();
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
    $total = $recibo->venta->total;
    if($reclamo = $recibo->venta->reclamo){
        $total -= $recibo->venta->descuento;
    }
    $letras = $this->numtoletras($total);
    return view('ventas.recibo')->with('recibo', $recibo)->with('letras', $letras);
  }

  private function numtoletras($xcifra){
  	$xarray = array(0 => "Cero",
  			1 => "UN", "DOS", "TRES", "CUATRO", "CINCO", "SEIS", "SIETE", "OCHO", "NUEVE",
  			"DIEZ", "ONCE", "DOCE", "TRECE", "CATORCE", "QUINCE", "DIECISEIS", "DIECISIETE", "DIECIOCHO", "DIECINUEVE",
  			"VEINTI", 30 => "TREINTA", 40 => "CUARENTA", 50 => "CINCUENTA", 60 => "SESENTA", 70 => "SETENTA", 80 => "OCHENTA", 90 => "NOVENTA",
  			100 => "CIENTO", 200 => "DOSCIENTOS", 300 => "TRESCIENTOS", 400 => "CUATROCIENTOS", 500 => "QUINIENTOS", 600 => "SEISCIENTOS", 700 => "SETECIENTOS", 800 => "OCHOCIENTOS", 900 => "NOVECIENTOS"
  	);

  	$xcifra = trim($xcifra);
  	$xlength = strlen($xcifra);
  	$xpos_punto = strpos($xcifra, ".");
  	$xaux_int = $xcifra;
  	$xdecimales = "00";
  	if (!($xpos_punto === false)) {
  		if ($xpos_punto == 0) {
  			$xcifra = "0" . $xcifra;
  			$xpos_punto = strpos($xcifra, ".");
  		}
  		$xaux_int = substr($xcifra, 0, $xpos_punto); // obtengo el entero de la cifra a covertir
  		$xdecimales = substr($xcifra . "00", $xpos_punto + 1, 2); // obtengo los valores decimales
  	}

  	$XAUX = str_pad($xaux_int, 18, " ", STR_PAD_LEFT);
  	$xcadena = "";
  	for ($xz = 0; $xz < 3; $xz++) {
  			$xaux = substr($XAUX, $xz * 6, 6);
  			$xi = 0;
  			$xlimite = 6; // inicializo el contador de centenas xi y establezco el límite a 6 dígitos en la parte entera
  			$xexit = true; // bandera para controlar el ciclo del While
  			while ($xexit) {
  					if ($xi == $xlimite) { // si ya llegó al límite máximo de enteros
  							break; // termina el ciclo
  					}

  					$x3digitos = ($xlimite - $xi) * -1; // comienzo con los tres primeros digitos de la cifra, comenzando por la izquierda
  					$xaux = substr($xaux, $x3digitos, abs($x3digitos)); // obtengo la centena (los tres dígitos)
  					for ($xy = 1; $xy < 4; $xy++) { // ciclo para revisar centenas, decenas y unidades, en ese orden
  							switch ($xy) {
  									case 1: // checa las centenas
  											if (substr($xaux, 0, 3) < 100) { // si el grupo de tres dígitos es menor a una centena ( < 99) no hace nada y pasa a revisar las decenas

  											} else {
  													$key = (int) substr($xaux, 0, 3);
  													if (TRUE === array_key_exists($key, $xarray)){  // busco si la centena es número redondo (100, 200, 300, 400, etc..)
  															$xseek = $xarray[$key];
  															$xsub = $this->subfijo($xaux); // devuelve el subfijo correspondiente (Millón, Millones, Mil o nada)
  															if (substr($xaux, 0, 3) == 100)
  																	$xcadena = " " . $xcadena . " CIEN " . $xsub;
  															else
  																	$xcadena = " " . $xcadena . " " . $xseek . " " . $xsub;
  															$xy = 3; // la centena fue redonda, entonces termino el ciclo del for y ya no reviso decenas ni unidades
  													}
  													else { // entra aquí si la centena no fue numero redondo (101, 253, 120, 980, etc.)
  															$key = (int) substr($xaux, 0, 1) * 100;
  															$xseek = $xarray[$key]; // toma el primer caracter de la centena y lo multiplica por cien y lo busca en el arreglo (para que busque 100,200,300, etc)
  															$xcadena = " " . $xcadena . " " . $xseek;
  													} // ENDIF ($xseek)
  											} // ENDIF (substr($xaux, 0, 3) < 100)
  											break;
  									case 2: // checa las decenas (con la misma lógica que las centenas)
  											if (substr($xaux, 1, 2) < 10) {

  											} else {
  													$key = (int) substr($xaux, 1, 2);
  													if (TRUE === array_key_exists($key, $xarray)) {
  															$xseek = $xarray[$key];
  															$xsub = $this->subfijo($xaux);
  															if (substr($xaux, 1, 2) == 20)
  																	$xcadena = " " . $xcadena . " VEINTE " . $xsub;
  															else
  																	$xcadena = " " . $xcadena . " " . $xseek . " " . $xsub;
  															$xy = 3;
  													}
  													else {
  															$key = (int) substr($xaux, 1, 1) * 10;
  															$xseek = $xarray[$key];
  															if (20 == substr($xaux, 1, 1) * 10)
  																	$xcadena = " " . $xcadena . " " . $xseek;
  															else
  																	$xcadena = " " . $xcadena . " " . $xseek . " Y ";
  													} // ENDIF ($xseek)
  											} // ENDIF (substr($xaux, 1, 2) < 10)
  											break;
  									case 3: // checa las unidades
  											if (substr($xaux, 2, 1) < 1) { // si la unidad es cero, ya no hace nada

  											} else {
  													$key = (int) substr($xaux, 2, 1);
  													$xseek = $xarray[$key]; // obtengo directamente el valor de la unidad (del uno al nueve)
  													$xsub = $this->subfijo($xaux);
  													$xcadena = " " . $xcadena . " " . $xseek . " " . $xsub;
  											} // ENDIF (substr($xaux, 2, 1) < 1)
  											break;
  							} // END SWITCH
  					} // END FOR
  					$xi = $xi + 3;
  			} // ENDDO

  			if (substr(trim($xcadena), -5, 5) == "ILLON") // si la cadena obtenida termina en MILLON o BILLON, entonces le agrega al final la conjuncion DE
  					$xcadena.= " DE";

  			if (substr(trim($xcadena), -7, 7) == "ILLONES") // si la cadena obtenida en MILLONES o BILLONES, entoncea le agrega al final la conjuncion DE
  					$xcadena.= " DE";

  			// ----------- esta línea la puedes cambiar de acuerdo a tus necesidades o a tu país -------
  			if (trim($xaux) != "") {
  					switch ($xz) {
  							case 0:
  									if (trim(substr($XAUX, $xz * 6, 6)) == "1")
  											$xcadena.= "UN BILLON ";
  									else
  											$xcadena.= " BILLONES ";
  									break;
  							case 1:
  									if (trim(substr($XAUX, $xz * 6, 6)) == "1")
  											$xcadena.= "UN MILLON ";
  									else
  											$xcadena.= " MILLONES ";
  									break;
  							case 2:
  									if ($xcifra < 1) {
  											$xcadena = "CERO Y $xdecimales/100 SOLES";
  									}
  									if ($xcifra >= 1 && $xcifra < 2) {
  											$xcadena = "UNO Y $xdecimales/100 SOLES";
  									}
  									if ($xcifra >= 2) {
  											$xcadena.= " Y $xdecimales/100 SOLES"; //
  									}
  									break;
  					} // endswitch ($xz)
  			} // ENDIF (trim($xaux) != "")
  			// ------------------      en este caso, para México se usa esta leyenda     ----------------
  			$xcadena = str_replace("VEINTI ", "VEINTI", $xcadena); // quito el espacio para el VEINTI, para que quede: VEINTICUATRO, VEINTIUN, VEINTIDOS, etc
  			$xcadena = str_replace("  ", " ", $xcadena); // quito espacios dobles
  			$xcadena = str_replace("UN UN", "UN", $xcadena); // quito la duplicidad
  			$xcadena = str_replace("  ", " ", $xcadena); // quito espacios dobles
  			$xcadena = str_replace("BILLON DE MILLONES", "BILLON DE", $xcadena); // corrigo la leyenda
  			$xcadena = str_replace("BILLONES DE MILLONES", "BILLONES DE", $xcadena); // corrigo la leyenda
  			$xcadena = str_replace("DE UN", "UN", $xcadena); // corrigo la leyenda
  	}
  	return trim($xcadena);
    }

    private function subfijo($xx){
  	$xx = trim($xx);
  	$xstrlen = strlen($xx);
  	if ($xstrlen == 1 || $xstrlen == 2 || $xstrlen == 3)
  			$xsub = "";

  	if ($xstrlen == 4 || $xstrlen == 5 || $xstrlen == 6)
  			$xsub = "MIL";

  	return $xsub;
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
    $total = $recibo->venta->total;
    if($reclamo = $recibo->venta->reclamo){
        $total -= $recibo->venta->descuento;
    }
    $letras = $this->numtoletras($total);
    $tablaTicket = "--Copia del ticket original--<div class='row'>
        <div class='col-sm-12'>
            <p class='text-center' style='font-size: 12px; margin-bottom:1px;'>".$recibo->venta->tienda->nombre."</p>
        </div>
    </div>
    <div class='row'>
        <div class='col-sm-12'>
            <p class='text-center' style='font-size: 12px; margin-bottom:1px;'>R.U.C. N° ".$recibo->venta->tienda->ruc."</p>
        </div>
    </div>
    <div class='row'>
        <div class='col-sm-12'>
            <p class='text-center' style='font-size: 12px; margin-bottom:1px;'>".$recibo->venta->tienda->direccion."</p>
        </div>
    </div>
    <div class='row'>
        <div class='col-sm-12'>
            <p class='text-center' style='font-size: 12px; margin-bottom:1px;'>AUTORIZACION SUNAT NRO. 0193845116923</p>
        </div>
    </div>
    <div class='row'>
        <div class='col-sm-12'>
            <p class='text-left' style='font-size: 12px; margin-bottom:1px;'>TICKET N° $recibo->numeracion</p>
        </div>
    </div>
    <div class='row'>
        <div class='col-sm-12'>
            <p class='text-left' style='font-size: 12px; margin-bottom:1px;'>N° DE SERIE ".$recibo->venta->tienda->ticketera."</p>
        </div>
    </div>";
    if($empresa = $recibo->empresa){
      $tablaTicket .= "<div class='row'>
          <div class='col-sm-12'>
              <p class='text-left' style='font-size: 12px; margin-bottom:1px;'>RUC: $empresa->ruc</p>
          </div>
      </div>
      <div class='row'>
          <div class='col-sm-12'>
              <p class='text-left' style='font-size: 12px; margin-bottom:1px;'>CLIENTE: $empresa->nombre</p>
          </div>
      </div>
      <div class='row'>
          <div class='col-sm-12'>
              <p class='text-left' style='font-size: 12px; margin-bottom:1px;'>DIRECCIÓN: $empresa->direccion</p>
          </div>
      </div>";
    }elseif($persona = $recibo->persona){
      $tablaTicket .= "<div class='row'>
          <div class='col-sm-12'>
              <p class='text-left' style='font-size: 12px; margin-bottom:1px;'>CLIENTE: $persona->nombres $persona->apellidos</p>
          </div>
      </div>";
    }else{
      $tablaTicket .= "<div class='row'>
          <div class='col-sm-12'>
              <p class='text-left' style='font-size: 12px; margin-bottom:1px;'>CLIENTE: CLIENTE VARIOS</p>
          </div>
      </div>";
    }
    $tablaTicket .= "<table class='table table-condensed' style='margin-bottom:5px;'>
      <tr>
        <td style='border-top:rgba(130, 130, 130, 0.5); padding-top:1px; padding-bottom:0px;'>
            <p class='text-left' style='font-size: 12px; margin-bottom:1px;'>Cant.</p>
        </td>
        <td style='border-top:rgba(130, 130, 130, 0.5); padding-top:1px; padding-bottom:0px;'>
            <p class='text-left' style='font-size: 12px; margin-bottom:1px;'>Descripción</p>
        </td>
        <td style='border-top:rgba(130, 130, 130, 0.5); padding-top:1px; padding-bottom:0px;'>
            <p class='text-left' style='font-size: 12px; margin-bottom:1px;'>Unit.</p>
        </td>
        <td style='width:50px; border-top:rgba(130, 130, 130, 0.5); padding-top:1px; padding-bottom:0px;'>
            <p class='text-left' style='font-size: 12px; margin-bottom:1px;'>Importe</p>
        </td>
      </tr>";
      foreach($recibo->venta->detalles as $detalle){
        $tablaTicket .= "<tr>
            <td style='border-top:rgba(130, 130, 130, 0.5); padding-top:1px; padding-bottom:0px;'>
                <p class='text-left' style='font-size: 12px; margin-bottom:1px;'>$detalle->cantidad</p>
            </td>
            <td style='border-top:rgba(130, 130, 130, 0.5); padding-top:1px; padding-bottom:0px;'>
                <p class='text-left' style='font-size: 12px; margin-bottom:1px;'>".$detalle->producto->familia->nombre.
                " ".$detalle->producto->marca->nombre." ".$detalle->producto->descripcion."</p>
            </td>
            <td style='border-top:rgba(130, 130, 130, 0.5); padding-top:1px; padding-bottom:0px;'>
                <p class='text-right' style='font-size: 12px; margin-bottom:1px;'>".$detalle->producto->precio."</p>
            </td>
            <td style='width:50px; border-top:rgba(130, 130, 130, 0.5); padding-top:1px; padding-bottom:0px;'>
                <p class='text-right' style='font-size: 12px; margin-bottom:1px;'>$detalle->total</p>
            </td>
        </tr>";
      }
      $tablaTicket .= "<tr>
        <td colspan='3' style='text-align:right; border-top:rgba(255, 255, 255, 0); padding-top:1px; padding-bottom:0px; padding-top:1px; padding-bottom:0px;'>
            <p class='text-right' style='font-size: 12px; margin-bottom:1px;'>TOTAL S/</p>
        </td>
        <td style='text-align:right; border-top:rgba(255, 255, 255, 0); padding-top:1px; padding-bottom:0px; padding-top:1px; padding-bottom:0px;'>
            <p class='text-right' style='font-size: 12px; margin-bottom:1px;'>".$recibo->venta->total."</p>
        </td>
      </tr>";
      $vuelto =  $recibo->venta->total;
      if($reclamo = $recibo->venta->reclamo){
        $tablaTicket .= "<tr>
          <td colspan='2' style='text-align:left; border-top:rgba(255, 255, 255, 0); padding-top:1px; padding-bottom:0px;'>
              <p class='text-right' style='font-size: 12px; margin-bottom:1px;'>DESCUENTO POR CANJE DE $reclamo->puntos PUNTOS TÚ</p></td>
          <td style='text-align:right; border-top:rgba(255, 255, 255, 0); padding-top:1px; padding-bottom:0px;'>
              <p class='text-right' style='font-size: 12px; margin-bottom:1px;'>".number_format($recibo->venta->descuento, 2, '.', ' ')."</p></td>
        </tr>
        <tr>
          <td colspan='2' style='text-align:right; border-top:rgba(255, 255, 255, 0); padding-top:1px; padding-bottom:0px;'>
              <p class='text-right' style='font-size: 12px; margin-bottom:1px;'>TOTAL A PAGAR</p>
          </td>
          <td style='text-align:right; border-top:rgba(255, 255, 255, 0); padding-top:1px; padding-bottom:0px;'>
              <p class='text-right' style='font-size: 12px; margin-bottom:1px;'>".number_format($recibo->venta->total-$recibo->venta->descuento, 2, '.', ' ')."</p>
          </td>
        </tr>";
        $vuelto =  $recibo->venta->total-$recibo->venta->descuento;
      }
      if($recibo->venta->efectivo){
        $tablaTicket .= "<tr>
          <td colspan='3' style='text-align:right; border-top:rgba(255, 255, 255, 0); padding-top:1px; padding-bottom:0px;'>
              <p class='text-right' style='font-size: 12px; margin-bottom:1px;'>EFECTIVO S/ </p>
          </td>
          <td style='text-align:right; border-top:rgba(255, 255, 255, 0); padding-top:1px; padding-bottom:0px;'>
              <p class='text-right' style='font-size: 12px; margin-bottom:1px;'>".number_format($recibo->venta->efectivo->monto, 2, '.', ' ')."</p>
          </td>
        </tr>";
        $vuelto = $recibo->venta->efectivo->monto - $vuelto;
      }
      if($recibo->venta->tarjetaVenta){
        $tablaTicket .= "<tr>
          <th colspan='3' style='text-align:right; border-top:rgba(255, 255, 255, 0); padding-top:1px; padding-bottom:0px;'>
              <p class='text-right' style='font-size: 12px; margin-bottom:1px;'></p>TARJETA S/ </th>
          <th style='text-align:right; border-top:rgba(255, 255, 255, 0); padding-top:1px; padding-bottom:0px;'>
              <p class='text-right' style='font-size: 12px; margin-bottom:1px;'></p>".number_format($recibo->venta->tarjetaVenta->monto, 2, '.', ' ')."</th>
        </tr>";
        $vuelto = $recibo->venta->tarjetaVenta->monto - $vuelto;
      }
      $tablaTicket .= "<tr>
        <td colspan='3' style='text-align:right; border-top:rgba(255, 255, 255, 0); padding-top:1px; padding-bottom:0px;'>
            <p class='text-right' style='font-size: 12px; margin-bottom:1px;'>VUELTO S/ </p></td>
        <td style='text-align:right; border-top:rgba(255, 255, 255, 0); padding-top:1px; padding-bottom:0px;'>
            <p class='text-right' style='font-size: 12px; margin-bottom:1px;'>".number_format($vuelto, 2, '.', ' ')."</p></td>
      </tr>
      <tr>
        <td colspan='4' style='text-align:left; border-top:rgba(255, 255, 255, 0); padding-top:1px; padding-bottom:0px;'>
            <p class='text-left' style='font-size: 12px; margin-bottom:1px;'>SON: $letras</p></td>
      </tr>
      <tr>
        <td colspan='4' style='text-align:left; border-top:rgba(255, 255, 255, 0); padding-top:1px; padding-bottom:0px;'>
            <p class='text-left' style='font-size: 12px; margin-bottom:1px;'>HUANUCO, ".\Carbon\Carbon::createFromFormat('Y-m-d H:i:s',$recibo->venta->updated_at)->format('d/m/Y')."
            - HORA: ".\Carbon\Carbon::createFromFormat('Y-m-d H:i:s',$recibo->venta->updated_at)->format('H:i A')."</p>
        </td>
      </tr>
      <tr>
        <td colspan='4' style='text-align:left; border-top:rgba(255, 255, 255, 0); padding-top:1px; padding-bottom:0px;'>
            <p class='text-left' style='font-size: 12px; margin-bottom:1px;'>CAJERA: ".$recibo->venta->usuario->persona->nombres." ".$recibo->venta->usuario->persona->apellidos."</p>
        </td>
      </tr>
    </table>";
    if($persona = $recibo->persona){
      if($persona->puntos){
        $tablaTicket .= "<p class='text-justify' style='font-size: 12px; margin-bottom:5px;'>SR(A). $persona->nombres $persona->apellidos CON ESTA COMPRA USTED ACUMULA UN TOTAL DE $persona->puntos
          PUNTOS TÚ. RECUERDE RECLAMAR SU DESCUENTO A PARTIR DE LOS 1 000 PUNTOS.</p>";
      }
    }
    $tablaTicket .= "<p class='text-justify' style='font-size: 12px; margin-bottom:5px;'>BIENES TRANSFERIDOS EN LA AMAZONIA PARA SER CONSUMIDOS EN LA MISMA</p>--Copia del ticket original--";

    return ['ticket'=>$tablaTicket, 'recibo'=>$recibo, 'venta'=>$venta];
  }


}
