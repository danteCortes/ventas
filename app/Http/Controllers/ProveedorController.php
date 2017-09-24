<?php

namespace App\Http\Controllers;

use App\Proveedor;
use Illuminate\Http\Request;
use DB;

class ProveedorController extends Controller{

  /**
   * Muestra la vista proveedores/inicio.blade.php que muestra una lista de proveedores.
   * Fecha 14/09/2017
   * @return \Illuminate\Http\Response
   */
  public function index(){
    return view('proveedores.inicio');
  }

  /**
   * Almacenamos un nuevo proveedor en la base de datos.
   * Fecha 14/09/2017
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request){
    //Verificamos si la empresa ya existe;
    if ($empresa = \App\Empresa::find($request->ruc)) {
      // Si la empresa exite, actualizamos sus datos.
      $empresa->nombre = mb_strtoupper($request->nombre);
      $empresa->direccion = mb_strtoupper($request->direccion);
      $empresa->save();
    }else {
      // Si no existe, lo guardamos en la base de datos.
      $empresa = new \App\Empresa;
      $empresa->ruc = $request->ruc;
      $empresa->nombre = mb_strtoupper($request->nombre);
      $empresa->direccion = mb_strtoupper($request->direccion);
      $empresa->save();
    }
    // Verificamos si la empresa es un proveedor.
    if ($proveedor = \App\Proveedor::where('empresa_ruc', $request->ruc)->first()) {
      // Si el proveedor existe, actualizamos sus datos.
      $proveedor->telefono = mb_strtoupper($request->telefono);
      $proveedor->representante = mb_strtoupper($request->representante);
      $proveedor->save();
    }else {
      // Si no existe, guardamos sus datos.
      $proveedor = new \App\Proveedor;
      $proveedor->empresa_ruc = $request->ruc;
      $proveedor->telefono = mb_strtoupper($request->telefono);
      $proveedor->representante = mb_strtoupper($request->representante);
      $proveedor->save();
    }
    // Retornamos a la lista de todos los proveedores.
    return redirect('proveedor')->with('correcto', 'EL PROVEEDOR '.$proveedor->nombre.' FUE INGRESADO CON ÉXITO.');
  }

  /**
   * Actualizamos los datos del proveedor.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \App\Proveedor  $proveedor
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, Proveedor $proveedor){
    // Verificamos si cambio el número de ruc.
    if ($request->ruc != $proveedor->empresa->ruc) {
      // Si se cambia el número de ruc, verificamos si otro proveedor tiene ese número de ruc.
      if (Proveedor::where('empresa_ruc', $request->ruc)->first()) {
        // Si otro proveedor tiene ese número de ruc, retornamos a la lista de proveedores con un mensaje de error.
        return redirect('proveedor')->with('error', 'YA EXISTE OTRO PROVEEDOR CON EL MISMO NÚMERO DE RUC QUE ESTA TRATANDO DE CAMBIAR.');
      }else {
        // Si no existe un proveedor con el nuevo número de ruc, verificamos si ya existe una empresa con ese número de ruc.
        if ($empresa = \App\Empresa::find('ruc')) {
          // Si existe una empresa con ese número de ruc, actualizamos sus datos.
          $empresa->nombre = mb_strtoupper($request->nombre);
          $empresa->direccion = mb_strtoupper($request->direccion);
          $empresa->save();
        }else{
          // Si no existe una empresa con ese número de ruc, actualizamos los datos de la empresa del proveedor.
          $empresa = $proveedor->empresa;
          $empresa->ruc = $request->ruc;
          $empresa->nombre = mb_strtoupper($request->nombre);
          $empresa->direccion = mb_strtoupper($request->direccion);
          $empresa->save();
        }
      }
    }else {
      // Si no se cambia el número de ruc, se actualizan los datos de la empresa con normalidad.
      $empresa = $proveedor->empresa;
      $empresa->nombre = mb_strtoupper($request->nombre);
      $empresa->direccion = mb_strtoupper($request->direccion);
      $empresa->save();
    }
    // Actualizamos los datos del proveedor.
    $proveedor->telefono = mb_strtoupper($request->telefono);
    $proveedor->representante = mb_strtoupper($request->representante);
    $proveedor->save();
    // Retornamos a la lista de todos los proveedores.
    return redirect('proveedor')->with('correcto', 'EL PROVEEDOR FUE AC TUALIZADO CON ÉXITO.');
  }

  /**
   * Elimina el proveedor especificado de la base de datos.
   *
   * @param  \App\Proveedor  $proveedor
   * @return \Illuminate\Http\Response
   */
  public function destroy(Proveedor $proveedor){
    $proveedor->delete();
    return redirect('proveedor')->with('info', 'EL PROVEEDOR '.$proveedor->empresa->nombre.' FUE ELIMINADO SIN PROBLEMAS.');
  }

  /**
  * busca y envia los datos de un proveedor
  * Fecha 14/09/2017
  */
  public function buscar(Request $request){
    if($proveedor = Proveedor::where('empresa_ruc', $request->ruc)->first()){

      return ['proveedor'=>$proveedor, 'empresa'=>$proveedor->empresa];
    }
    return 0;
  }

  /**
   * Lista a todos los proveedores y retorna el arreglo.
  */
  public function listar(Request $request){
    $line_quantity = intVal($request->current);
    $line_number = intVal($request->rowCount);
    $where = $request->searchPhrase;
    $sort = $request->sort;

    if (isset($sort['ruc'])) {
        $order_by = 'ruc';
        $order_name = $sort['ruc'];
    }
    if (isset($sort['nombre'])) {
        $order_by = 'nombre';
        $order_name = $sort['nombre'];
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
        $proveedores = DB::table('proveedores')
          ->join('empresas', 'proveedores.empresa_ruc', '=', 'empresas.ruc')
          ->select(
            'empresas.ruc as ruc',
            'empresas.nombre as nombre',
            'empresas.direccion as direccion',
            'proveedores.telefono as telefono',
            'proveedores.representante as representante'
          )
          ->distinct()
          ->offset($skip)
          ->limit($take)
          ->orderBy($order_by, $order_name)
          ->get();
      } else {
        $proveedores = DB::table('proveedores')
          ->join('empresas', 'proveedores.empresa_ruc', '=', 'empresas.ruc')
          ->select(
            'empresas.ruc as ruc',
            'empresas.nombre as nombre',
            'empresas.direccion as direccion',
            'proveedores.telefono as telefono',
            'proveedores.representante as representante'
          )
          ->where('ruc', 'like', '%'.$where.'%')
          ->orWhere('nombre', 'like', '%'.$where.'%')
          ->distinct()
          ->offset($skip)
          ->limit($take)
          ->orderBy($order_by, $order_name)
          ->get();
      }

      if (empty($where)) {
        $total = DB::table('proveedores')
          ->join('empresas', 'proveedores.empresa_ruc', '=', 'empresas.ruc')
          ->select(
            'empresas.ruc as ruc',
            'empresas.nombre as nombre',
            'empresas.direccion as direccion',
            'proveedores.telefono as telefono',
            'proveedores.representante as representante'
          )
          ->distinct()
          ->get();

        $total = count($total);
      } else {
        $total = DB::table('proveedores')
          ->join('empresas', 'proveedores.empresa_ruc', '=', 'empresas.ruc')
          ->select(
            'empresas.ruc as ruc',
            'empresas.nombre as nombre',
            'empresas.direccion as direccion',
            'proveedores.telefono as telefono',
            'proveedores.representante as representante'
          )
          ->where('ruc', 'like', '%'.$where.'%')
          ->orWhere('nombre', 'like', '%'.$where.'%')
          ->distinct()
          ->get();

        $total = count($total);
      }
    }

    $datas = [];

    foreach ($proveedores as $proveedor):

      $data = array_merge(
        array
        (
          "ruc" => $proveedor->ruc,
          "nombre" => $proveedor->nombre,
          "direccion" => $proveedor->direccion,
          "telefono" => $proveedor->telefono,
          "representante" => $proveedor->representante,
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


}
