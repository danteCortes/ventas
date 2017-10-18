<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Traits\SeparacionTrait;

class SeparacionController extends Controller{

  use SeparacionTrait;

  public function nuevo(){
    return view('separaciones.nuevo.inicio');
  }

  public function agregarDetalle(Request $request){
    // Verificamos que se está agregando una cantidad menor o igual al stock de la tienda.
    if ($request->cantidad > $request->stock) {
      // Si la cantidad que queremos agregar al credito es mayor al stock en la tienda retornamos a la vista anterior con el mensaje corresóndiente.
      return redirect('credito')->with('error', 'ESTÁ QUERIENDO VENDER MÁS DE LO QUE TIENE EN EL ALMACÉN.');
    }
    // Verificamos si existe un crédito activo (estado 1) en esta tienda y con este usuario
    if (!$separacion = \App\Separacion::where('usuario_id', \Auth::user()->id)->where('tienda_id', \Auth::user()->tienda_id)
    ->where('estado', 1)->first()) {
      // si no existe el credito, procedemos a crearlo.
      $separacion = $this->iniciarSeparacion();
    }
    // Si la cantidad por agregar es menor o igual al stock de la tienda, procedemos a guardar el detalle relacionado con la separación.
    $detalle = $this->nuevoDetalle($request, $separacion->id);
    // Actualizamos el total de la separación.
    $separacion->total += $detalle->total;
    $separacion->separacion_total += $detalle->monto_separacion;
    $separacion->save();
    // Regresamos a la vista anterior con el mensaje correspondiente.
    return redirect('separacion')->with('correcto', 'EL DETALLE DE LA SEPARACIÓN SE AGREGÓ CON EXITO.');
  }

  public function quitarDetalle($id){
    // Primero identificamos el detalle que vamos a quitar.
    $detalle = \App\Detalle::find($id);
    // Descontamos el total del detalle del total de la separacion.
    $separacion = $detalle->separacion;
    $separacion->usuario_id = \Auth::user()->id;
    $separacion->total -= number_format($detalle->total, 2, '.', '');
    $separacion->separacion_total -= number_format($detalle->monto_separacion, 2, '.', '');
    $separacion->save();
    // Verificamos si es el último detalle de la separacion.
    if (count($separacion->detalles) > 1) {
      // Si e la separacion tiene más de un detalle, borramos el detalle.
      $detalle->delete();
    }else{
      // Si tiene un solo detalle, verificamos si es una separacion cerrada o activa.
      if ($separacion->estado) {
        // Si es una separacion activa, borramos toda la separacion.
        $separacion->delete();
      }else{
        // Si es una separacion ya cerrado borramos solo el detalle,
        // puede ser que posteriormente agreguen otro detalle.
        $detalle->delete();
      }
    }
    if ($separacion->estado) {
      // regresamos a la vista anterior con el mensaje correspondiente
      return redirect('separacion')->with('info', 'SE QUITO UN DETALLE DE LA SEPARACIÓN.');
    }else{
      // regresamos a la vista anterior con el mensaje correspondiente
      return redirect('separacion/modificar/'.$separacion->id)->with('info', 'SE QUITO UN DETALLE DE LA SEPARACIÓN.');
    }
  }

  public function terminar(Request $request, $id){
    // Guardamos o actualizamos los datos del cliente.
    $persona = $this->registrarCliente($request);
    // Cerramos el credito.
    $separacion = $this->cerrarSeparacion($id, $persona);
    // regresamos a la vista anterior con el mensaje correspondiente
    return redirect('separacion/listar')->with('correcto', 'LA SEPARACIÓN SE REGISTRÓ CON ÉXITO');
  }

  public function listar(){
    return view('separaciones.listar.inicio');
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
        $separaciones = \App\Separacion::join('personas', 'separaciones.persona_dni', '=', 'personas.dni')
          ->where('tienda_id', \Auth::user()->tienda_id)->where('estado', 0)
          ->select(
            'separaciones.id as id',
            'personas.nombres as nombres',
            'personas.apellidos as apellidos',
            'separaciones.created_at as created_at',
            'separaciones.total as total'
            )
          ->distinct()
          ->offset($skip)
          ->limit($take)
          ->orderBy($order_by, $order_name)
          ->get();

      } else {

        $separaciones = \App\Separacion::join('personas', 'separaciones.persona_dni', '=', 'personas.dni')
          ->where('tienda_id', \Auth::user()->tienda_id)->where('estado', 0)
          ->where('separaciones.id', 'like', '%'.$where.'%')
          ->orWhere('separaciones.created_at', 'like', '%'.$where.'%')
          ->orWhere('personas.nombres', 'like', '%'.$where.'%')
          ->orWhere('personas.apellidos', 'like', '%'.$where.'%')
          ->orWhere('separaciones.total', 'like', '%'.$where.'%')
          ->select(
            'separaciones.id as id',
            'personas.nombres as nombres',
            'personas.apellidos as apellidos',
            'separaciones.created_at as created_at',
            'separaciones.total as total'
            )
          ->distinct()
          ->offset($skip)
          ->limit($take)
          ->orderBy($order_by, $order_name)
          ->get();
      }

      if (empty($where)) {
        $total = \App\Separacion::join('personas', 'separaciones.persona_dni', '=', 'personas.dni')
          ->where('tienda_id', \Auth::user()->tienda_id)->where('estado', 0)
          ->distinct()
          ->offset($skip)
          ->limit($take)
          ->orderBy($order_by, $order_name)
          ->get();

        $total = count($total);
      } else {
        $total = \App\Separacion::join('personas', 'separaciones.persona_dni', '=', 'personas.dni')
          ->where('tienda_id', \Auth::user()->id)->where('estado', 0)
          ->where('separaciones.id', 'like', '%'.$where.'%')
          ->orWhere('separaciones.created_at', 'like', '%'.$where.'%')
          ->orWhere('personas.nombres', 'like', '%'.$where.'%')
          ->orWhere('personas.apellidos', 'like', '%'.$where.'%')
          ->orWhere('separaciones.total', 'like', '%'.$where.'%')
          ->distinct()
          ->get();

        $total = count($total);
      }
    }

    $datas = [];

    foreach ($separaciones as $separacion):

      $data = array_merge(
        array
        (
          "id" => $separacion->id,
          "cliente" => $separacion->nombres." ".$separacion->apellidos,
          "total" => number_format($separacion->total, 2, '.', ' '),
          "fecha_separacion" => $separacion->created_at,
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
    $separacion = \App\Separacion::find($request->id);
    $separacion->persona;
    $separacion->usuario->persona;
    $separacion->pagos;
    foreach ($separacion->detalles as $detalle) {
      $detalle->producto;
    }
    return $separacion;
  }

  public function editar($id){
    $separacion = \App\Separacion::find($id);
    return view('separaciones.modificar.inicio')->with('separacion', $separacion);
  }

  public function modificarDetalle(Request $request, $id){
    // Verificamos que se está agregando una cantidad menor o igual al stock de la tienda.
    if ($request->cantidad > $request->stock) {
      // Si la cantidad que queremos agregar al separacion es mayor al stock en la tienda retornamos a la vista anterior con el mensaje corresóndiente.
      return redirect('separacion/modifcar/'.$id)->with('error', 'ESTÁ QUERIENDO VENDER MÁS DE LO QUE TIENE EN EL ALMACÉN.');
    }
    $separacion = \App\Separacion::find($id);
    // Si la cantidad por agregar es menor o igual al stock de la tienda, procedemos a guardar el detalle relacionado con el crédito.
    $detalle = $this->nuevoDetalle($request, $separacion->id);
    // Actualizamos el total del separacion.
    $separacion->total += $detalle->total;
    $separacion->separacion_total += $detalle->monto_separacion;
    $separacion->save();
    // Regresamos a la vista anterior con el mensaje correspondiente.
    return redirect('separacion/modificar/'.$id)->with('correcto', 'EL DETALLE DE LA SEPARACIÓN SE AGREGÓ CON EXITO.');
  }

  public function modificar(Request $request, $id){
    $separacion = \App\Separacion::find($id);
    // Verificamos si la separacion tiene algun detalle.
    if (count($separacion->detalles) == 0) {
      // Si no tiene detalles se regresa a la vista anterior con el mensaje de error.
      return redirect('separacion/modificar/'.$id)->with('error', 'ES NECESARIO INGRESAR
        UN PRODUCTO A LA SEPARACIÓN, DE LO CONTRARIO DEBERÍA ELIMINARLO DESDE LA LISTA DE SEPARACIONES.');
    }else {
      // Si tiene al menos un detalle, procedemos a modificar sus datos.
      $separacion->usuario_id = \Auth::user()->id;
      $separacion->save();
      // regresamos a la vista anterior con el mensaje correspondiente
      return redirect('separacion/listar')->with('correcto', 'LA SEPARACIÓN FUE MODIFICADO CON ÉXITO');
    }
  }

  public function pagar(Request $request , $id){
    $separacion = \App\Separacion::find($id);
    // Verificamos cuanto esta pagado hasta el momento.
    $pagado = $separacion->separacion_total;
    foreach ($separacion->pagos as $pago) {
      $pagado += $pago->monto;
    }
    // verificamos cuanto falta pagar para cancelar el crédito.
    $saldo = $separacion->total - $pagado;
    $vuelto = 0;
    // Verificamos cuanto esta queriendo pagar el cliente del saldo.
    if ($saldo >= $request->monto) {
      // Si el saldo es mayor que el monto, va a pagar una parte del credito.
      $pago = $this->guardarPago($separacion, $request->monto);
      // Calculamos el nuevo saldo.
      $saldo -= $request->monto;
    }else{
      // Si el saldo por pagar es menor a la cantidad ingresada por el cliente,
      // solo guardamos el pago con la cantidad del saldo.
      $pago = $this->guardarPago($separacion, $saldo);
      // el nuevo saldo será cero.
      $vuelto = $request->monto - $saldo;
      $saldo = 0;
    }
    // Tenemos que incrementar el monto pagado al cierre activo.
    $cierre = $this->cierreActual();
    $cierre->total = str_replace(' ', '', $cierre->total) + $pago->monto;
    $cierre->save();
    // Por último, verificamos si se completo el pago, de ser asi descontamos los productos correspondientes.
    if ($saldo == 0) {
      // Si el saldo es igual a cero, se completo el pago total de los productos.
      // Procedemos a descontar los productos de la tienda.
      foreach ($separacion->detalles as $detalle) {
        $this->descontarProducto($detalle->producto_codigo, $detalle->cantidad);
      }
    }
    // Retornamos a la vista anterior con el mensaje correspondiente.
    return redirect('separacion/listar')->with('correcto', 'EL PAGO DEL CRÉDITO '.$id.
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
      $separacion = \App\Separacion::find($id);
      // Procedemos a eliminar el credito.
      // Primero devolvemos los productos a la tienda de origen.
      // Primero verificamos el saldo.
      $total_pagado = $separacion->separacion_total;
      foreach ($separacion->pagos as $pago) {
        $total_pagado += $pago->monto;
      }
      if ($total_pagado == $separacion->total) {
        // Esto es una separacion de producto que ya fue cancelado y entregado.
        // Procedemos a devolver los productos a su tienda de origen.
        foreach ($separacion->detalles as $detalle) {
          $this->devolverProducto($detalle);
        }
      }
      // Restamos el pago por separación que se hizo del cierre que le pertenece al pago.
      $cierre = $separacion->cierre;
      $cierre->total = str_replace(' ', '', $cierre->total) - $separacion->separacion_total;
      $cierre->save();
      // En cuanto al pago, verificamos si la separación tuvo un pago o aun no hicieron un pago para cancelarlo.
      if (count($separacion->pagos) != 0) {
        // Como tiene pagos hechos, debemos restar el monto pagado de los cierres que corresponden.
        foreach ($separacion->pagos as $pago) {
          // Identificamos el cierre al que pertenece el pago.
          $cierre = $pago->cierre;
          $cierre->total = str_replace(' ', '', $cierre->total) - $pago->monto;
          $cierre->save();
        }
      }
      // Por ultimo eliminamos el crédito.
      $separacion->delete();
      // si no hay autorización retornamos a la vista anterior con el mensaje correspondiente.
      return redirect('separacion/listar')->with('info', 'LA SEPARACIÓN '.$separacion->id.' FUE ELIMINADO POR SU CUENTA DE CAJERO '.
        \Auth::user()->persona->nombres.' '.\Auth::user()->persona->apellidos.'.');
    }else{
      // si no hay autorización retornamos a la vista anterior con el mensaje correspondiente.
      return redirect('separacion/listar')->with('error', 'LA CONTRASEÑA INGRESADA NO PERTENECE AL
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

}
