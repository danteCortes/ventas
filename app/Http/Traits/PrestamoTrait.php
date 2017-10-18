<?php

namespace App\Http\Traits;

use Illuminate\Http\Request;

trait PrestamoTrait{

  public function buscar(Request $request){
    $prestamo = \App\Prestamo::find($request->id);
    $prestamo->detalles;
    $prestamo->usuario->persona;
    foreach ($prestamo->detalles as $detalle) {
      $detalle->producto;
    }
    return $prestamo;
  }

  public function llenarTablaRecoger(Request $request){
    $line_quantity = intVal($request->current);
    $line_number = intVal($request->rowCount);
    $where = $request->searchPhrase;
    $sort = $request->sort;

    if (isset($sort['id'])) {
        $order_by = 'id';
        $order_name = $sort['id'];
    }
    if (isset($sort['socio'])) {
        $order_by = 'socio';
        $order_name = $sort['socio'];
    }
    if (isset($sort['fecha_credito'])) {
        $order_by = 'created_at';
        $order_name = $sort['fecha_credito'];
    }
    if (isset($sort['fecha_devolucion'])) {
        $order_by = 'fecha';
        $order_name = $sort['fecha_devolucion'];
    }
    if (isset($sort['direccion'])) {
        $order_by = 'direccion';
        $order_name = $sort['direccion'];
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
        $prestamos = \App\Prestamo::where('tienda_id', \Auth::user()->tienda_id)->where('estado', 0)->whereNull('devuelto')->where('direccion', 1)
          ->select(
            'prestamos.id as id',
            'prestamos.created_at as created_at',
            'prestamos.fecha as fecha',
            'prestamos.direccion as direccion',
            'prestamos.socio as socio'
            )
          ->distinct()
          ->offset($skip)
          ->limit($take)
          ->orderBy($order_by, $order_name)
          ->get();

      } else {
        $prestamos = \App\Prestamo::where('tienda_id', \Auth::user()->tienda_id)->where('estado', 0)->whereNull('devuelto')->where('direccion', 1)
          ->where('prestamos.id', 'like', '%'.$where.'%')
          ->orWhere('prestamos.created_at', 'like', '%'.$where.'%')
          ->orWhere('prestamos.fecha', 'like', '%'.$where.'%')
          ->orWhere('prestamos.socio', 'like', '%'.$where.'%')
          ->select(
            'prestamos.id as id',
            'prestamos.created_at as created_at',
            'prestamos.fecha as fecha',
            'prestamos.direccion as direccion',
            'prestamos.socio as socio'
            )
          ->distinct()
          ->offset($skip)
          ->limit($take)
          ->orderBy($order_by, $order_name)
          ->get();
      }

      if (empty($where)) {
        $total = \App\Prestamo::where('tienda_id', \Auth::user()->tienda_id)->where('estado', 0)->whereNull('devuelto')->where('direccion', 1)
          ->distinct()
          ->offset($skip)
          ->limit($take)
          ->orderBy($order_by, $order_name)
          ->get();

        $total = count($total);
      } else {
        $total = \App\Prestamo::where('tienda_id', \Auth::user()->id)->where('estado', 0)->whereNull('devuelto')->where('direccion', 1)
          ->where('prestamos.id', 'like', '%'.$where.'%')
          ->orWhere('prestamos.created_at', 'like', '%'.$where.'%')
          ->orWhere('prestamos.fecha', 'like', '%'.$where.'%')
          ->orWhere('prestamos.socio', 'like', '%'.$where.'%')
          ->distinct()
          ->get();

        $total = count($total);
      }
    }

    $datas = [];

    foreach ($prestamos as $prestamo):

      $data = array_merge(
        array
        (
          "id" => $prestamo->id,
          "socio" => $prestamo->socio,
          "direccion" => $prestamo->direccion[1],
          "fecha_prestamo" => $prestamo->created_at,
          "fecha_devolucion" => $prestamo->fecha,
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

  public function llenarTablaDevolver(Request $request){
    $line_quantity = intVal($request->current);
    $line_number = intVal($request->rowCount);
    $where = $request->searchPhrase;
    $sort = $request->sort;

    if (isset($sort['id'])) {
        $order_by = 'id';
        $order_name = $sort['id'];
    }
    if (isset($sort['socio'])) {
        $order_by = 'socio';
        $order_name = $sort['socio'];
    }
    if (isset($sort['fecha_credito'])) {
        $order_by = 'created_at';
        $order_name = $sort['fecha_credito'];
    }
    if (isset($sort['fecha_devolucion'])) {
        $order_by = 'fecha';
        $order_name = $sort['fecha_devolucion'];
    }
    if (isset($sort['direccion'])) {
        $order_by = 'direccion';
        $order_name = $sort['direccion'];
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
        $prestamos = \App\Prestamo::where('tienda_id', \Auth::user()->tienda_id)->where('estado', 0)->whereNull('devuelto')->where('direccion', 0)
          ->select(
            'prestamos.id as id',
            'prestamos.created_at as created_at',
            'prestamos.fecha as fecha',
            'prestamos.direccion as direccion',
            'prestamos.socio as socio'
            )
          ->distinct()
          ->offset($skip)
          ->limit($take)
          ->orderBy($order_by, $order_name)
          ->get();

      } else {
        $prestamos = \App\Prestamo::where('tienda_id', \Auth::user()->tienda_id)->where('estado', 0)->whereNull('devuelto')->where('direccion', 0)
          ->where('prestamos.id', 'like', '%'.$where.'%')
          ->orWhere('prestamos.created_at', 'like', '%'.$where.'%')
          ->orWhere('prestamos.fecha', 'like', '%'.$where.'%')
          ->orWhere('prestamos.socio', 'like', '%'.$where.'%')
          ->select(
            'prestamos.id as id',
            'prestamos.created_at as created_at',
            'prestamos.fecha as fecha',
            'prestamos.direccion as direccion',
            'prestamos.socio as socio'
            )
          ->distinct()
          ->offset($skip)
          ->limit($take)
          ->orderBy($order_by, $order_name)
          ->get();
      }

      if (empty($where)) {
        $total = \App\Prestamo::where('tienda_id', \Auth::user()->tienda_id)->where('estado', 0)->whereNull('devuelto')->where('direccion', 0)
          ->distinct()
          ->offset($skip)
          ->limit($take)
          ->orderBy($order_by, $order_name)
          ->get();

        $total = count($total);
      } else {
        $total = \App\Prestamo::where('tienda_id', \Auth::user()->id)->where('estado', 0)->whereNull('devuelto')->where('direccion', 0)
          ->where('prestamos.id', 'like', '%'.$where.'%')
          ->orWhere('prestamos.created_at', 'like', '%'.$where.'%')
          ->orWhere('prestamos.fecha', 'like', '%'.$where.'%')
          ->orWhere('prestamos.socio', 'like', '%'.$where.'%')
          ->distinct()
          ->get();

        $total = count($total);
      }
    }

    $datas = [];

    foreach ($prestamos as $prestamo):

      $data = array_merge(
        array
        (
          "id" => $prestamo->id,
          "socio" => $prestamo->socio,
          "direccion" => $prestamo->direccion[1],
          "fecha_prestamo" => $prestamo->created_at,
          "fecha_devolucion" => $prestamo->fecha,
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

  public function llenarTabla(Request $request){
    $line_quantity = intVal($request->current);
    $line_number = intVal($request->rowCount);
    $where = $request->searchPhrase;
    $sort = $request->sort;

    if (isset($sort['id'])) {
        $order_by = 'id';
        $order_name = $sort['id'];
    }
    if (isset($sort['socio'])) {
        $order_by = 'socio';
        $order_name = $sort['socio'];
    }
    if (isset($sort['fecha_credito'])) {
        $order_by = 'created_at';
        $order_name = $sort['fecha_credito'];
    }
    if (isset($sort['fecha_devolucion'])) {
        $order_by = 'fecha';
        $order_name = $sort['fecha_devolucion'];
    }
    if (isset($sort['direccion'])) {
        $order_by = 'direccion';
        $order_name = $sort['direccion'];
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
        $prestamos = \App\Prestamo::where('tienda_id', \Auth::user()->tienda_id)->where('estado', 0)
          ->select(
            'prestamos.id as id',
            'prestamos.created_at as created_at',
            'prestamos.fecha as fecha',
            'prestamos.direccion as direccion',
            'prestamos.socio as socio'
            )
          ->distinct()
          ->offset($skip)
          ->limit($take)
          ->orderBy($order_by, $order_name)
          ->get();

      } else {
        $prestamos = \App\Prestamo::where('tienda_id', \Auth::user()->tienda_id)->where('estado', 0)
          ->where('prestamos.id', 'like', '%'.$where.'%')
          ->orWhere('prestamos.created_at', 'like', '%'.$where.'%')
          ->orWhere('prestamos.fecha', 'like', '%'.$where.'%')
          ->orWhere('prestamos.socio', 'like', '%'.$where.'%')
          ->select(
            'prestamos.id as id',
            'prestamos.created_at as created_at',
            'prestamos.fecha as fecha',
            'prestamos.direccion as direccion',
            'prestamos.socio as socio'
            )
          ->distinct()
          ->offset($skip)
          ->limit($take)
          ->orderBy($order_by, $order_name)
          ->get();
      }

      if (empty($where)) {
        $total = \App\Prestamo::where('tienda_id', \Auth::user()->tienda_id)->where('estado', 0)
          ->distinct()
          ->offset($skip)
          ->limit($take)
          ->orderBy($order_by, $order_name)
          ->get();

        $total = count($total);
      } else {
        $total = \App\Prestamo::where('tienda_id', \Auth::user()->id)->where('estado', 0)
          ->where('prestamos.id', 'like', '%'.$where.'%')
          ->orWhere('prestamos.created_at', 'like', '%'.$where.'%')
          ->orWhere('prestamos.fecha', 'like', '%'.$where.'%')
          ->orWhere('prestamos.socio', 'like', '%'.$where.'%')
          ->distinct()
          ->get();

        $total = count($total);
      }
    }

    $datas = [];

    foreach ($prestamos as $prestamo):

      $data = array_merge(
        array
        (
          "id" => $prestamo->id,
          "socio" => $prestamo->socio,
          "direccion" => $prestamo->direccion[1],
          "fecha_prestamo" => $prestamo->created_at,
          "fecha_devolucion" => $prestamo->fecha,
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

  private function cerrarPrestamo(Request $request, \App\Prestamo $prestamo){
    $prestamo->fecha = $request->fecha;
    $prestamo->estado = 0;
    $prestamo->direccion = $request->direccion;
    $prestamo->socio = mb_strtoupper($request->socio);
    $prestamo->save();
    return \App\Prestamo::find($prestamo->id);
  }

  private function devolverProducto(\App\Detalle $detalle){
    if (!$productoTienda = \App\ProductoTienda::where('producto_codigo', $detalle->producto_codigo)
      ->where('tienda_id', $detalle->prestamo->tienda_id)->first()) {
      $productoTienda = new \App\ProductoTienda;
      $productoTienda->producto_codigo = $detalle->producto_codigo;
      $productoTienda->tienda_id = $detalle->prestamo->tienda_id;
      $productoTienda->cantidad = 0;
      $productoTienda->save();
    }
    $productoTienda->cantidad += $detalle->cantidad;
    $productoTienda->save();
  }

  private function descontarProducto(\App\Detalle $detalle){
    $productoTienda = \App\ProductoTienda::where('producto_codigo', $detalle->producto_codigo)
      ->where('tienda_id', \Auth::user()->tienda_id)->first();
    $productoTienda->cantidad -= $detalle->cantidad;
    $productoTienda->save();
  }

  private function nuevoDetalle(Request $request, $prestamo_id){
    $detalle = new \App\Detalle;
    $detalle->prestamo_id = $prestamo_id;
    $detalle->producto_codigo = $request->producto_codigo;
    $detalle->cantidad = $request->cantidad;
    $detalle->save();
    return \App\Detalle::find($detalle->id);
  }

  private function iniciarPrestamo(){
    $credito = new \App\Prestamo;
    $credito->usuario_id = \Auth::user()->id;
    $credito->tienda_id = \Auth::user()->tienda_id;
    $credito->cierre_id = $this->cierreActual()->id;
    $credito->estado = 1;
    $credito->save();

    return \App\Prestamo::find($credito->id);
  }

  private function cierreActual(){
    return \App\Cierre::where('usuario_id', \Auth::user()->id)->where('tienda_id', \Auth::user()->tienda_id)
    ->where('estado', 1)->first();
  }
}

 ?>
