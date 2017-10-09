<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CreditoController extends Controller{

  /**
   * Muestra la vista para realizar un crédito a un cliente.
  */
  public function index(){
    return view('creditos.nuevo');
  }

  /**
   * Agrega un detalle a la base de datos relacionado con el credito.
  */
  public function agregarDetalle(Request $request){
    // Verificamos que se está agregando una cantidad menor o igual al stock de la tienda.
    if ($request->cantidad > $request->stock) {
      // Si la cantidad que queremos agregar al credito es mayor al stock en la tienda retornamos a la vista anterior con el mensaje corresóndiente.
      return redirect('credito')->with('error', 'ESTÁ QUERIENDO VENDER MÁS DE LO QUE TIENE EN EL ALMACÉN.');
    }
    // Verificamos si existe un crédito activo (estado 1) en esta tienda y con este usuario
    if (!$credito = \App\Credito::where('usuario_id', \Auth::user()->id)->where('tienda_id', \Auth::user()->tienda_id)
    ->where('estado', 1)->first()) {
      // si no existe el credito, procedemos a crearlo.
      $credito = $this->iniciarCredito();
    }
    // Si la cantidad por agregar es menor o igual al stock de la tienda, procedemos a guardar el detalle relacionado con el crédito.
    $detalle = $this->nuevoDetalle($request, $credito->id);
    // Descontamos la cantidad agregada al credito del stock de la tienda.
    $this->descontarProducto($request);
    // Actualizamos el total del credito.
    $credito->total += $detalle->total;
    $credito->save();
    // Regresamos a la vista anterior con el mensaje correspondiente.
    return redirect('credito')->with('correcto', 'EL DETALLE DEL CRÉDITO SE AGREGÓ CON EXITO.');
  }

  public function quitarDetalle($id){
    // Primero identificamos el detalle que vamos a quitar.
    $detalle = \App\Detalle::find($id);
    // Regresamos los productos a la tienda correspondiente.
    $this->devolverProducto($detalle);
    // Descontamos el total del detalle del total del credito.
    $credito = $detalle->credito;
    $credito->usuario_id = \Auth::user()->id;
    $credito->total -= number_format($detalle->total, 2, '.', '');
    $credito->save();
    // Verificamos si es el último detalle del crédito.
    if (count($credito->detalles) > 1) {
      // Si el crédito tiene más de un detalle, borramos el detalle.
      $detalle->delete();
    }else{
      // Si tiene un solo detalle, verificamos si es un credito cerrado o activo.
      if ($credito->estado) {
        // Si es un credito activo, borramos todo el crédito.
        $credito->delete();
      }else{
        // Si es un credito ya cerrado borramos solo el detalle,
        // puede ser que posteriormente agreguen otro detalle.
        $detalle->delete();
        // regresamos a la vista anterior con el mensaje correspondiente
        return redirect('modificar-credito/'.$credito->id)->with('info', 'SE QUITO UN DETALLE DEL CREDITO');
      }
    }
    // regresamos a la vista anterior con el mensaje correspondiente
    return redirect('credito')->with('info', 'SE QUITO UN DETALLE DEL CREDITO');
  }

  public function terminar(Request $request){
    // Primero verificamos si la persona existe en la bd.
    if ($persona = \App\Persona::find($request->documento)) {
      // Si la persona existe, actualizamos sus datos.
      $persona->nombres = mb_strtoupper($request->nombres);
      $persona->apellidos = mb_strtoupper($request->apellidos);
      $persona->direccion = mb_strtoupper($request->direccion);
      $persona->save();
    }else{
      // Si no existe, lo guardamos.
      $persona = new \App\Persona;
      $persona->dni = $request->documento;
      $persona->nombres = mb_strtoupper($request->nombres);
      $persona->apellidos = mb_strtoupper($request->apellidos);
      $persona->direccion = mb_strtoupper($request->direccion);
      $persona->save();
    }
    // Cerramos el credito.
    $credito = \App\Credito::find($request->credito_id);
    $credito->persona_dni = $request->documento;
    $credito->estado = 0;
    $credito->fecha = $request->fecha;
    $credito->save();
    // regresamos a la vista anterior con el mensaje correspondiente
    return redirect('credito')->with('correcto', 'EL CREDITO FUE GUARDADO CON ÉXITO');
  }

  public function listar(){
    return view('creditos.listar');


  }

  public function llenarTabla(Request $request){
    $line_quantity = intVal($request->current);
    $line_number = intVal($request->rowCount);
    $where = $request->searchPhrase;
    $sort = $request->sort;

    if (isset($sort['id'])) {
        $order_by = 'id';
        $order_name = $sort['id'];
    }
    if (isset($sort['cliente'])) {
        $order_by = 'nombres';
        $order_name = $sort['cliente'];
    }
    if (isset($sort['fecha_credito'])) {
        $order_by = 'created_at';
        $order_name = $sort['fecha_credito'];
    }
    if (isset($sort['fecha_cobro'])) {
        $order_by = 'fecha';
        $order_name = $sort['fecha_cobro'];
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
      //
    } else {
      if (empty($where)) {
        $creditos = \App\Credito::join('personas', 'creditos.persona_dni', '=', 'personas.dni')
          ->where('tienda_id', \Auth::user()->tienda_id)->where('estado', 0)
          ->select(
            'creditos.id as id',
            'personas.nombres as nombres',
            'personas.apellidos as apellidos',
            'creditos.created_at as created_at',
            'creditos.fecha as fecha',
            'creditos.total as total'
            )
          ->distinct()
          ->offset($skip)
          ->limit($take)
          ->orderBy($order_by, $order_name)
          ->get();

      } else {

        $creditos = \App\Credito::join('personas', 'creditos.persona_dni', '=', 'personas.dni')
          ->where('tienda_id', \Auth::user()->tienda_id)->where('estado', 0)
          ->where('creditos.id', 'like', '%'.$where.'%')
          ->orWhere('creditos.created_at', 'like', '%'.$where.'%')
          ->orWhere('creditos.fecha', 'like', '%'.$where.'%')
          ->orWhere('personas.nombres', 'like', '%'.$where.'%')
          ->orWhere('personas.apellidos', 'like', '%'.$where.'%')
          ->orWhere('creditos.total', 'like', '%'.$where.'%')
          ->select(
            'creditos.id as id',
            'personas.nombres as nombres',
            'personas.apellidos as apellidos',
            'creditos.created_at as created_at',
            'creditos.fecha as fecha',
            'creditos.total as total'
            )
          ->distinct()
          ->offset($skip)
          ->limit($take)
          ->orderBy($order_by, $order_name)
          ->get();
      }

      if (empty($where)) {
        $total = \App\Credito::join('personas', 'creditos.persona_dni', '=', 'personas.dni')
          ->where('tienda_id', \Auth::user()->tienda_id)->where('estado', 0)
          ->distinct()
          ->offset($skip)
          ->limit($take)
          ->orderBy($order_by, $order_name)
          ->get();

        $total = count($total);
      } else {
        $total = \App\Credito::join('personas', 'creditos.persona_dni', '=', 'personas.dni')
          ->where('tienda_id', \Auth::user()->id)->where('estado', 0)
          ->where('creditos.id', 'like', '%'.$where.'%')
          ->orWhere('creditos.created_at', 'like', '%'.$where.'%')
          ->orWhere('creditos.fecha', 'like', '%'.$where.'%')
          ->orWhere('personas.nombres', 'like', '%'.$where.'%')
          ->orWhere('personas.apellidos', 'like', '%'.$where.'%')
          ->orWhere('creditos.total', 'like', '%'.$where.'%')
          ->distinct()
          ->get();

        $total = count($total);
      }
    }

    $datas = [];

    foreach ($creditos as $credito):

      $data = array_merge(
        array
        (
          "id" => $credito->id,
          "cliente" => $credito->nombres." ".$credito->apellidos,
          "total" => number_format($credito->total, 2, '.', ' '),
          "fecha_credito" => $credito->created_at,
          "fecha_cobro" => $credito->fecha,
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
    // Buscamos el credito segun el id que nos mandan.
    $credito = \App\Credito::find($request->id);
    $productos = [];
    foreach ($credito->detalles as $detalle) {
      $detalle->producto;
    }
    return ['credito'=>$credito, 'cliente'=>$credito->persona, 'cajero'=>$credito->usuario->persona,
      'detalles'=>$credito->detalles, 'pagos'=>$credito->pagos];
  }

  public function editar($id){
    $credito = \App\Credito::find($id);
    return view('creditos.modificar.inicio')->with('credito', $credito);
  }

  public function modificarDetalle(Request $request, $id){
    // Verificamos que se está agregando una cantidad menor o igual al stock de la tienda.
    if ($request->cantidad > $request->stock) {
      // Si la cantidad que queremos agregar al credito es mayor al stock en la tienda retornamos a la vista anterior con el mensaje corresóndiente.
      return redirect('credito')->with('error', 'ESTÁ QUERIENDO VENDER MÁS DE LO QUE TIENE EN EL ALMACÉN.');
    }
    $credito = \App\Credito::find($id);
    // Si la cantidad por agregar es menor o igual al stock de la tienda, procedemos a guardar el detalle relacionado con el crédito.
    $detalle = $this->nuevoDetalle($request, $credito->id);
    // Descontamos la cantidad agregada al credito del stock de la tienda.
    $this->descontarProducto($request);
    // Actualizamos el total del credito.
    $credito->total += $detalle->total;
    $credito->save();
    // Regresamos a la vista anterior con el mensaje correspondiente.
    return redirect('modificar-credito/'.$id)->with('correcto', 'EL DETALLE DEL CRÉDITO SE AGREGÓ CON EXITO.');
    dd($request);
  }

  public function modificar(Request $request, $id){
    $credito = \App\Credito::find($id);
    // Verificamos si el credito tiene algun detalle.
    if (count($credito->detalles) == 0) {
      // Si no tiene detalles se regresa a la vista anterior con el mensaje de error.
      return redirect('modificar-credito/'.$id)->with('error', 'ES NECESARIO INGRESAR
        UN PRODUCTO AL CRÉDITO, DE LO CONTRARIO DEBERÍA ELIMINARLO DESDE LA LISTA DE CREDITOS.');
    }else {
      // Si tiene al menos un detalle, procedemos a modificar sus datos.
      $credito->usuario_id = \Auth::user()->id;
      $credito->fecha = $request->fecha;
      $credito->save();
      // regresamos a la vista anterior con el mensaje correspondiente
      return redirect('credito')->with('correcto', 'EL CREDITO FUE MODIFICADO CON ÉXITO');
    }
  }

  public function pagar(Request $request, $id){
    $credito = \App\Credito::find($id);
    // Verificamos cuanto esta pagado hasta el momento.
    $pagado = 0;
    foreach ($credito->pagos as $pago) {
      $pagado += $pago->monto;
    }
    // verificamos cuanto falta pagar para cancelar el crédito.
    $saldo = $credito->total - $pagado;
    $vuelto = 0;
    // Verificamos cuanto esta queriendo pagar el cliente del saldo.
    if ($saldo >= $request->monto) {
      // Si el saldo es mayor que el monto, va a pagar una parte del credito.
      $pago = $this->guardarPago($credito, $request->monto);
      // Calculamos el nuevo saldo.
      $saldo -= $request->monto;
    }else{
      // Si el saldo por pagar es menor a la cantidad ingresada por el cliente,
      // solo guardamos el pago con la cantidad del saldo.
      $pago = $this->guardarPago($credito, $saldo);
      // el nuevo saldo será cero.
      $vuelto = $request->monto - $saldo;
      $saldo = 0;
    }
    // Tenemos que incrementar el monto pagado al cierre activo.
    $cierre = $this->cierreActual();
    $cierre->total = str_replace(' ', '', $cierre->total) + $pago->monto;
    $cierre->save();
    // Retornamos a la vista anterior con el mensaje correspondiente.
    return redirect('listar-creditos')->with('correcto', 'EL PAGO DEL CRÉDITO '.$id.
      ' SE GUARDO CON EXITO. TIENE UN SALDO DE S/ '.$saldo.', Y HUBO UN EXCESO DE S/ '.$vuelto.'.');
  }

  public function eliminar(Request $request, $id){
    // Primero verificamos si la contraseña ingresada es conrrespondiente a la de un administrador.
    $autorizacion = 0;
    foreach (\App\Usuario::where('tipo', 1)->get() as $administrador) {
      // verificamos que la contraseña ingresada sea del administrador.
      if(\Hash::check($request->password, $administrador->password)){
        $autorizacion = 1;
        break;
      }
    }
    // $verificamos si hay autorización de un administrador.
    if ($autorizacion) {
      $credito = \App\Credito::find($id);
      // Procedemos a eliminar el credito.
      // Primero devolvemos los productos a la tienda de origen.
      foreach ($credito->detalles as $detalle) {
        $this->devolverProducto($detalle);
      }
      // En cuanto al pago, verificamos si el crédito tuvo un pago o aun no hicieron un pago para cancelar el credito.
      if (count($credito->pagos) != 0) {
        // Como tiene pagos hechos, debemos restar el monto pagado de los cierres que corresponden.
        foreach ($credito->pagos as $pago) {
          // Identificamos el cierre al que pertenece el pago.
          $cierre = $pago->cierre;
          $cierre->total = str_replace(' ', '', $cierre->total) - $pago->monto;
          $cierre->save();
        }
      }
      // Por ultimo eliminamos el crédito.
      $credito->delete();
      // si no hay autorización retornamos a la vista anterior con el mensaje correspondiente.
      return redirect('listar-creditos')->with('info', 'EL CRÉDITO '.$credito->id.' FUE ELIMINADO POR SU CUENTA DE CAJERO '.
        \Auth::user()->persona->nombres.' '.\Auth::user()->persona->apellidos.'.');
    }else{
      // si no hay autorización retornamos a la vista anterior con el mensaje correspondiente.
      return redirect('listar-creditos')->with('error', 'LA CONTRASEÑA INGRESADA NO PERTENECE AL
        ADMINISTRADOR, NO CUENTA CON AUTORIZACIÓN PARA REALIZAR ESTA ACCIÓN.');
    }
  }

  public function listarCobrar(){
    return view('creditos.cobrar.listar');
  }

  public function llenarTablaCobrar(Request $request){
    $line_quantity = intVal($request->current);
    $line_number = intVal($request->rowCount);
    $where = $request->searchPhrase;
    $sort = $request->sort;

    if (isset($sort['id'])) {
      $order_by = 'id';
      $order_name = $sort['id'];
    }
    if (isset($sort['cliente'])) {
      $order_by = 'nombres';
      $order_name = $sort['cliente'];
    }
    if (isset($sort['fecha_credito'])) {
      $order_by = 'created_at';
      $order_name = $sort['fecha_credito'];
    }
    if (isset($sort['fecha_cobro'])) {
      $order_by = 'fecha';
      $order_name = $sort['fecha_cobro'];
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
      //
    } else {
      if (empty($where)) {
        $creditos = \App\Credito::join('personas', 'creditos.persona_dni', '=', 'personas.dni')
        ->where('creditos.tienda_id', \Auth::user()->tienda_id)->where('estado', 0)
        ->select(
          'creditos.id as id',
          'personas.nombres as nombres',
          'personas.apellidos as apellidos',
          'creditos.created_at as created_at',
          'creditos.fecha as fecha',
          'creditos.total as total'
          )
          ->distinct()
          ->offset($skip)
          ->limit($take)
          ->orderBy($order_by, $order_name)
          ->get();

        } else {

          $creditos = \App\Credito::join('personas', 'creditos.persona_dni', '=', 'personas.dni')
          ->where('tienda_id', \Auth::user()->tienda_id)->where('estado', 0)
          ->where('creditos.id', 'like', '%'.$where.'%')
          ->orWhere('creditos.created_at', 'like', '%'.$where.'%')
          ->orWhere('creditos.fecha', 'like', '%'.$where.'%')
          ->orWhere('personas.nombres', 'like', '%'.$where.'%')
          ->orWhere('personas.apellidos', 'like', '%'.$where.'%')
          ->orWhere('creditos.total', 'like', '%'.$where.'%')
          ->select(
          'creditos.id as id',
          'personas.nombres as nombres',
          'personas.apellidos as apellidos',
          'creditos.created_at as created_at',
          'creditos.fecha as fecha',
          'creditos.total as total'
          )
          ->distinct()
          ->offset($skip)
          ->limit($take)
          ->orderBy($order_by, $order_name)
          ->get();
        }

        if (empty($where)) {
          $total = \App\Credito::join('personas', 'creditos.persona_dni', '=', 'personas.dni')
          ->where('tienda_id', \Auth::user()->tienda_id)->where('estado', 0)
          ->distinct()
          ->offset($skip)
          ->limit($take)
          ->orderBy($order_by, $order_name)
          ->get();

          $total = count($total);
        } else {
          $total = \App\Credito::join('personas', 'creditos.persona_dni', '=', 'personas.dni')
          ->where('tienda_id', \Auth::user()->id)->where('estado', 0)
          ->where('creditos.id', 'like', '%'.$where.'%')
          ->orWhere('creditos.created_at', 'like', '%'.$where.'%')
          ->orWhere('creditos.fecha', 'like', '%'.$where.'%')
          ->orWhere('personas.nombres', 'like', '%'.$where.'%')
          ->orWhere('personas.apellidos', 'like', '%'.$where.'%')
          ->orWhere('creditos.total', 'like', '%'.$where.'%')
          ->distinct()
          ->get();

          $total = count($total);
        }
      }

      $datas = [];

      foreach ($creditos as $credito):
        // Verificamos si ya está pagado el crédito.
        $total_pagado = 0;
        foreach ($credito->pagos as $pago) {
          $total_pagado += $pago->monto;
        }
        if($total_pagado < $credito->total){

          $data = array_merge(
            array
            (
              "id" => $credito->id,
              "cliente" => $credito->nombres." ".$credito->apellidos,
              "total" => number_format($credito->total, 2, '.', ' '),
              "fecha_credito" => $credito->created_at,
              "fecha_cobro" => $credito->fecha,
              )
            );
            //Asignamos un grupo de datos al array datas
            $datas[] = $data;
        }

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

  public function listarPagados(){
    return view('creditos.pagados.listar');
  }

  public function llenarTablaPagados(Request $request){
    $line_quantity = intVal($request->current);
    $line_number = intVal($request->rowCount);
    $where = $request->searchPhrase;
    $sort = $request->sort;

    if (isset($sort['id'])) {
      $order_by = 'id';
      $order_name = $sort['id'];
    }
    if (isset($sort['cliente'])) {
      $order_by = 'nombres';
      $order_name = $sort['cliente'];
    }
    if (isset($sort['fecha_credito'])) {
      $order_by = 'created_at';
      $order_name = $sort['fecha_credito'];
    }
    if (isset($sort['fecha_cobro'])) {
      $order_by = 'fecha';
      $order_name = $sort['fecha_cobro'];
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
      //
    } else {
      if (empty($where)) {
        $creditos = \App\Credito::join('personas', 'creditos.persona_dni', '=', 'personas.dni')
        ->where('creditos.tienda_id', \Auth::user()->tienda_id)->where('estado', 0)
        ->select(
          'creditos.id as id',
          'personas.nombres as nombres',
          'personas.apellidos as apellidos',
          'creditos.created_at as created_at',
          'creditos.fecha as fecha',
          'creditos.total as total'
          )
          ->distinct()
          ->offset($skip)
          ->limit($take)
          ->orderBy($order_by, $order_name)
          ->get();

        } else {

          $creditos = \App\Credito::join('personas', 'creditos.persona_dni', '=', 'personas.dni')
          ->where('tienda_id', \Auth::user()->tienda_id)->where('estado', 0)
          ->where('creditos.id', 'like', '%'.$where.'%')
          ->orWhere('creditos.created_at', 'like', '%'.$where.'%')
          ->orWhere('creditos.fecha', 'like', '%'.$where.'%')
          ->orWhere('personas.nombres', 'like', '%'.$where.'%')
          ->orWhere('personas.apellidos', 'like', '%'.$where.'%')
          ->orWhere('creditos.total', 'like', '%'.$where.'%')
          ->select(
          'creditos.id as id',
          'personas.nombres as nombres',
          'personas.apellidos as apellidos',
          'creditos.created_at as created_at',
          'creditos.fecha as fecha',
          'creditos.total as total'
          )
          ->distinct()
          ->offset($skip)
          ->limit($take)
          ->orderBy($order_by, $order_name)
          ->get();
        }

        if (empty($where)) {
          $total = \App\Credito::join('personas', 'creditos.persona_dni', '=', 'personas.dni')
          ->where('tienda_id', \Auth::user()->tienda_id)->where('estado', 0)
          ->distinct()
          ->offset($skip)
          ->limit($take)
          ->orderBy($order_by, $order_name)
          ->get();

          $total = count($total);
        } else {
          $total = \App\Credito::join('personas', 'creditos.persona_dni', '=', 'personas.dni')
          ->where('tienda_id', \Auth::user()->id)->where('estado', 0)
          ->where('creditos.id', 'like', '%'.$where.'%')
          ->orWhere('creditos.created_at', 'like', '%'.$where.'%')
          ->orWhere('creditos.fecha', 'like', '%'.$where.'%')
          ->orWhere('personas.nombres', 'like', '%'.$where.'%')
          ->orWhere('personas.apellidos', 'like', '%'.$where.'%')
          ->orWhere('creditos.total', 'like', '%'.$where.'%')
          ->distinct()
          ->get();

          $total = count($total);
        }
      }

      $datas = [];

      foreach ($creditos as $credito):
        // Verificamos si ya está pagado el crédito.
        $total_pagado = 0;
        foreach ($credito->pagos as $pago) {
          $total_pagado += $pago->monto;
        }
        if($total_pagado == $credito->total){

          $data = array_merge(
            array
            (
              "id" => $credito->id,
              "cliente" => $credito->nombres." ".$credito->apellidos,
              "total" => number_format($credito->total, 2, '.', ' '),
              "fecha_credito" => $credito->created_at,
              "fecha_cobro" => $credito->fecha,
              )
            );
            //Asignamos un grupo de datos al array datas
            $datas[] = $data;
        }

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

  private function guardarPago(\App\Credito $credito, $monto){
    $pago = new \App\Pago;
    $pago->credito_id = $credito->id;
    $pago->cierre_id = $this->cierreActual()->id;
    $pago->usuario_id = \Auth::user()->id;
    $pago->tienda_id = \Auth::user()->tienda_id;
    $pago->monto = $monto;
    $pago->save();
    return \App\Pago::find($pago->id);
  }

  private function devolverProducto(\App\Detalle $detalle){
    $productoTienda = \App\ProductoTienda::where('producto_codigo', $detalle->producto_codigo)->where('tienda_id', $detalle->credito->tienda_id)->first();
    $productoTienda->cantidad += $detalle->cantidad;
    $productoTienda->save();
  }

  private function descontarProducto(Request $request){
    $productoTienda = \App\ProductoTienda::where('producto_codigo', $request->producto_codigo)->where('tienda_id', \Auth::user()->tienda_id)->first();
    $productoTienda->cantidad -= $request->cantidad;
    $productoTienda->save();
  }

  private function nuevoDetalle(Request $request, $credito_id){
    $detalle = new \App\Detalle;
    $detalle->credito_id = $credito_id;
    $detalle->producto_codigo = $request->producto_codigo;
    $detalle->cantidad = $request->cantidad;
    $detalle->precio_unidad = $request->precio_unidad;
    $detalle->total = number_format($request->precio_unidad * $request->cantidad, 2, '.', '');
    $detalle->save();
    return \App\Detalle::find($detalle->id);
  }

  private function iniciarCredito(){
    $credito = new \App\Credito;
    $credito->usuario_id = \Auth::user()->id;
    $credito->tienda_id = \Auth::user()->tienda_id;
    $credito->cierre_id = $this->cierreActual()->id;
    $credito->estado = 1;
    $credito->total = 0;
    $credito->save();

    return \App\Credito::find($credito->id);
  }

  private function cierreActual(){
    return \App\Cierre::where('usuario_id', \Auth::user()->id)->where('tienda_id', \Auth::user()->tienda_id)
    ->where('estado', 1)->first();
  }



}
