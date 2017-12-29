<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MovimientoController extends Controller{

  public function inicio(){
    return view('movimientos.inicio');
  }

  public function ingresos(){
    return view('movimientos.ingresos.inicio');
  }

  public function gastos(){
    return view('movimientos.gastos.inicio');
  }

  public function nuevoIngreso(Request $request){

    $cierre = \App\Cierre::where('usuario_id', \Auth::user()->id)->where('estado', 1)
      ->where('tienda_id', \Auth::user()->tienda_id)->first();

    $otroIngreso = new \App\OtroIngreso;
    $otroIngreso->usuario_id = \Auth::user()->id;
    $otroIngreso->tienda_id = \Auth::user()->tienda_id;
    $otroIngreso->cierre_id = $cierre->id;
    $otroIngreso->descripcion = mb_strtoupper($request->descripcion);
    $otroIngreso->total = str_replace(" ", "", $request->monto);
    $otroIngreso->save();

    $cierre->total += str_replace(" ", "", $request->monto);
    $cierre->save();

    return redirect('movimiento/ingresos')->with('correcto', 'EL INGRESO SE REGISTRÓ CORRECTAMENTE.');
  }

  public function nuevoGasto(Request $request){

    $cierre = \App\Cierre::where('usuario_id', \Auth::user()->id)->where('estado', 1)
      ->where('tienda_id', \Auth::user()->tienda_id)->first();

    $otroGasto = new \App\OtroGasto;
    $otroGasto->usuario_id = \Auth::user()->id;
    $otroGasto->tienda_id = \Auth::user()->tienda_id;
    $otroGasto->cierre_id = \App\Cierre::where('usuario_id', \Auth::user()->id)->where('estado', 1)
      ->where('tienda_id', \Auth::user()->tienda_id)->first()->id;
    $otroGasto->descripcion = mb_strtoupper($request->descripcion);
    $otroGasto->total = str_replace(" ", "", $request->monto);
    $otroGasto->save();

    $cierre->total -= str_replace(" ", "", $request->monto);
    $cierre->save();

    return redirect('movimiento/gastos')->with('correcto', 'EL GASTO SE REGISTRÓ CORRECTAMENTE.');
  }

  public function listarIngresos(Request $request){
    $line_quantity = intVal($request->current);
    $line_number = intVal($request->rowCount);
    $where = $request->searchPhrase;
    $sort = $request->sort;

    if (isset($sort['fecha'])) {
        $order_by = 'created_at';
        $order_name = $sort['fecha'];
    }
    if (isset($sort['total'])) {
        $order_by = 'total';
        $order_name = $sort['total'];
    }
    if (isset($sort['descripcion'])) {
        $order_by = 'descripcion';
        $order_name = $sort['descripcion'];
    }

    $skip = 0;
    $take = $line_number;

    if ($line_quantity > 1) {
        //DESDE QUE REGISTRO SE INICIA
        $skip = $line_number * ($line_quantity - 1);
        //CANTIDAD DE RANGO
        $take = $line_number;
    }

    if (empty($where)) {
      $otrosIngresos = \DB::table('otros_ingresos')
        ->distinct()
        ->offset($skip)
        ->limit($take)
        ->orderBy($order_by, $order_name)
        ->get();
    } else {
      $otrosIngresos = \DB::table('otros_ingresos')
        ->where('descripcion', 'like', '%'.$where.'%')
        ->orWhere('total', 'like', '%'.$where.'%')
        ->distinct()
        ->offset($skip)
        ->limit($take)
        ->orderBy($order_by, $order_name)
        ->get();
    }

    if (empty($where)) {
      $total = \DB::table('otros_ingresos')
        ->distinct()
        ->get();

      $total = count($total);
    } else {
      $total = \DB::table('otros_ingresos')
        ->where('descripcion', 'like', '%'.$where.'%')
        ->orWhere('total', 'like', '%'.$where.'%')
        ->distinct()
        ->get();

      $total = count($total);
    }

    $datas = [];
    $cantidad = 0;
    foreach ($otrosIngresos as $otroIngreso):
      $data = array_merge(
        array
        (
          "id" => $otroIngreso->id,
          "fecha" => \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $otroIngreso->created_at)->format('d/m/y'),
          "descripcion" => $otroIngreso->descripcion,
          "total" => number_format($otroIngreso->total, 2, '.', ' ')
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

  public function listarGastos(Request $request){
    $line_quantity = intVal($request->current);
    $line_number = intVal($request->rowCount);
    $where = $request->searchPhrase;
    $sort = $request->sort;

    if (isset($sort['fecha'])) {
        $order_by = 'created_at';
        $order_name = $sort['fecha'];
    }
    if (isset($sort['total'])) {
        $order_by = 'total';
        $order_name = $sort['total'];
    }
    if (isset($sort['descripcion'])) {
        $order_by = 'descripcion';
        $order_name = $sort['descripcion'];
    }

    $skip = 0;
    $take = $line_number;

    if ($line_quantity > 1) {
        //DESDE QUE REGISTRO SE INICIA
        $skip = $line_number * ($line_quantity - 1);
        //CANTIDAD DE RANGO
        $take = $line_number;
    }

    if (empty($where)) {
      $otrosGastos = \DB::table('otros_gastos')
        ->distinct()
        ->offset($skip)
        ->limit($take)
        ->orderBy($order_by, $order_name)
        ->get();
    } else {
      $otrosGastos = \DB::table('otros_gastos')
        ->where('descripcion', 'like', '%'.$where.'%')
        ->orWhere('total', 'like', '%'.$where.'%')
        ->distinct()
        ->offset($skip)
        ->limit($take)
        ->orderBy($order_by, $order_name)
        ->get();
    }

    if (empty($where)) {
      $total = \DB::table('otros_gastos')
        ->distinct()
        ->get();

      $total = count($total);
    } else {
      $total = \DB::table('otros_gastos')
        ->where('descripcion', 'like', '%'.$where.'%')
        ->orWhere('total', 'like', '%'.$where.'%')
        ->distinct()
        ->get();

      $total = count($total);
    }

    $datas = [];
    $cantidad = 0;
    foreach ($otrosGastos as $otroGasto):
      $data = array_merge(
        array
        (
          "id" => $otroGasto->id,
          "fecha" => \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $otroGasto->created_at)->format('d/m/y'),
          "descripcion" => $otroGasto->descripcion,
          "total" => number_format($otroGasto->total, 2, '.', ' ')
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

  public function buscarIngreso(Request $request){
    $otroIngreso = \App\OtroIngreso::find($request->id);
    return $otroIngreso;
  }

  public function buscarGasto(Request $request){
    $otroGasto = \App\OtroGasto::find($request->id);
    return $otroGasto;
  }

  public function modificarGasto($id, Request $request){
    $otroGasto = \App\OtroGasto::find($id);

    $cierre = \App\Cierre::where('usuario_id', \Auth::user()->id)->where('estado', 1)
      ->where('tienda_id', \Auth::user()->tienda_id)->first();
    $cierre->total  -= (str_replace(" ", "", $request->monto) - $otroGasto->total);
    $cierre->save();

    $otroGasto->usuario_id = \Auth::user()->id;
    $otroGasto->descripcion = mb_strtoupper($request->descripcion);
    $otroGasto->total = $request->monto;
    $otroGasto->save();

    return redirect('movimiento/gastos')->with('correcto', 'EL GASTO SE MODIFICÓ CON ÉXITO.');

  }

  public function modificarIngreso($id, Request $request){

    $otroIngreso = \App\OtroIngreso::find($id);

    $cierre = \App\Cierre::where('usuario_id', \Auth::user()->id)->where('estado', 1)
      ->where('tienda_id', \Auth::user()->tienda_id)->first();
    $cierre->total  += str_replace(" ", "", $request->monto) - $otroIngreso->total;
    $cierre->save();

    $otroIngreso->usuario_id = \Auth::user()->id;
    $otroIngreso->descripcion = mb_strtoupper($request->descripcion);
    $otroIngreso->total = str_replace(" ", "", $request->monto);
    $otroIngreso->save();

    return redirect('movimiento/ingresos')->with('correcto', 'EL INGRESO SE MODIFICÓ CON ÉXITO.');

  }

  public function eliminarIngreso($id, Request $request){
    $autorizacion = 0;
    foreach (\App\Usuario::where('tipo', 1)->get() as $administrador) {
      // verificamos que la contraseña ingresada sea del administrador.
      if(\Hash::check($request->password, $administrador->password)){
        $autorizacion = 1;
        break;
      }
    }
    if (!$autorizacion) {
      return redirect('movimiento/ingresos')->with('error', 'EL PASSWORD INGRESADO ES INCORRECTO, INTENTE NUEVAMENTE.');
    }
    $ingreso = \App\OtroIngreso::find($id);

    $cierre = \App\Cierre::where('usuario_id', \Auth::user()->id)->where('estado', 1)
      ->where('tienda_id', \Auth::user()->tienda_id)->first();
    $cierre->total -= $ingreso->total;
    $cierre->save();

    $ingreso->delete();

    return redirect('movimiento/ingresos')->with('info', 'EL INGRESO FUE ELIMINADO CON ÉXITO.');
  }

  public function eliminarGasto($id, Request $request){
    $autorizacion = 0;
    foreach (\App\Usuario::where('tipo', 1)->get() as $administrador) {
      // verificamos que la contraseña ingresada sea del administrador.
      if(\Hash::check($request->password, $administrador->password)){
        $autorizacion = 1;
        break;
      }
    }
    if (!$autorizacion) {
      return redirect('movimiento/gastos')->with('error', 'EL PASSWORD INGRESADO ES INCORRECTO, INTENTE NUEVAMENTE.');
    }
    $gasto = \App\OtroGasto::find($id);

    $cierre = \App\Cierre::where('usuario_id', \Auth::user()->id)->where('estado', 1)
      ->where('tienda_id', \Auth::user()->tienda_id)->first();
    $cierre->total += $gasto->total;
    $cierre->save();

    $gasto->delete();

    return redirect('movimiento/gastos')->with('info', 'EL GASTO FUE ELIMINADO CON ÉXITO.');
  }

}
