<?php

namespace App\Http\Controllers;

use App\Compra;
use Illuminate\Http\Request;
use Auth;

class CompraController extends Controller{

  /**
   * Muestra una lista co todas las compras.
   * Fecha 15/09/2017
   * @return \Illuminate\Http\Response
   */
  public function index(){
    return view('compras.lista');
  }

  /**
   * Muestra un formulario para ingresar los datos de la compra.
   * Fecha 15/09/2917
   * @return \Illuminate\Http\Response
   */
  public function create(){
    return view('compras.inicio');
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request){
    // Verificamos que exista una compra activa por el usuario logeado.
    if($compra = Compra::where('usuario_id', Auth::user()->id)->where('estado', 1)->first()){
      // Si existe la compra, verificamos que todos los detalles de la compra
      // hallan registrado sus ingresos a las tiendas.
      foreach ($compra->detalles as $detalle) {
        if (count($detalle->ingresos) == 0) {
          // Si el detalle no tiene registrado ingresos a las tiendas, no se guarda la compra.
          return redirect('compra/create')->with('error', 'DEBE INGRESAR TODOS LOS PRODUCTOS A LAS TIENDAS.');
        }
      }
      // Si ya estan registrados todos los ingresos a las tiendas, guardamos la compra
      // y mostamos la lista de compras.

      // Primero guardamos al proveedor.

      //Verificamos si la empresa ya existe;
      if ($empresa = \App\Empresa::find($request->ruc)) {
        // Si la empresa exite, actualizamos sus datos.
        $empresa->nombre = mb_strtoupper($request->razonSocial);
        $empresa->direccion = mb_strtoupper($request->direccion);
        $empresa->save();
      }else {
        // Si no existe, lo guardamos en la base de datos.
        $empresa = new \App\Empresa;
        $empresa->ruc = $request->ruc;
        $empresa->nombre = mb_strtoupper($request->razonSocial);
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

      // Actualizamos los datos de la compra y cambiamos su estado a 0.
      $compra->proveedor_id = $proveedor->id;
      $compra->numero = $request->numero;
      $compra->estado = 0;
      $compra->save();

      return redirect('compra')->with('correcto', 'LA COMPRA FUE GUARDADA CON EXITO.');
    }
    return redirect('compra/create')->with('error', 'NO EXISTE UNA COMPRA ACTIVA EN ESTE MOMENTO.');
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  \App\Compra  $compra
   * @return \Illuminate\Http\Response
   */
  public function edit(Compra $compra){
    return view('compras.editar')->with('compra', $compra);
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \App\Compra  $compra
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, Compra $compra){

    // Verificamos que todos los detalles de la compra
    // hallan registrado sus ingresos a las tiendas.
    foreach ($compra->detalles as $detalle) {
      if (count($detalle->ingresos) == 0) {
        // Si el detalle no tiene registrado ingresos a las tiendas, no se guarda la compra.
        return redirect('compra/'.$compra->id.'/edit')->with('error', 'DEBE INGRESAR TODOS LOS PRODUCTOS A LAS TIENDAS.');
      }
    }
    // Si ya estan registrados todos los ingresos a las tiendas, guardamos la compra
    // y mostamos la lista de compras.

    // Primero guardamos al proveedor.

    //Verificamos si la empresa ya existe;
    if ($empresa = \App\Empresa::find($request->ruc)) {
      // Si la empresa exite, actualizamos sus datos.
      $empresa->nombre = mb_strtoupper($request->razonSocial);
      $empresa->direccion = mb_strtoupper($request->direccion);
      $empresa->save();
    }else {
      // Si no existe, lo guardamos en la base de datos.
      $empresa = new \App\Empresa;
      $empresa->ruc = $request->ruc;
      $empresa->nombre = mb_strtoupper($request->razonSocial);
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

    // Actualizamos los datos de la compra y cambiamos su estado a 0.
    // Primero verificamos si tienes detalles de compra.
    if (count($compra->detalles) > 0) {
      // Si existen detalles, actualizamos los datos de la compra.
      $compra->usuario_id = Auth::user()->id;
      $compra->proveedor_id = $proveedor->id;
      $compra->numero = $request->numero;
      $compra->estado = 0;
      $compra->save();

      return redirect('compra')->with('correcto', 'LA COMPRA FUE MODIFICADA CON EXITO CON EXITO.');
    }else{
      // si no tiene detalles de compra, eliminamos la compra.
      $compra->delete();
      return redirect('compra')->with('info', 'LA COMPRA FUE ELIMINADO POR NO CONTAR CON DETALLES.');
    }
  }

  /**
   * Elimina una compra, eliminando automáticamente los detalles y los registros de sus ingresos a las tiendas.
   * Ademas reduce la cantidad registrada en los ingresos a las tiendas de los productos correspondientes.
   * fecha 13/09/2017
   *
   * @param  \App\Compra  $compra
   * @return \Illuminate\Http\Response
   */
  public function destroy(Compra $compra){

    // Primero reducimos la cantidad de los productos en las tiendas correspondientes a los ingresos
    // generados por los detalles de la compra por eliminar.
    // Recorremos todos los detalles de la compra.
    foreach ($compra->detalles as $detalle) {
      // Por cada detalle de la compra, recorremos cada ingreso del detalle (Si la compra esta en lista
      // es por que su estado es cero, ya se registró sus ingresos a las tiendas y se incrementaron las
      // cantidades en cada tienda).
      foreach ($detalle->ingresos as $ingreso) {
        // Por cada ingreso del detalle se va a buscar el registro del producto en cada tienda, y luego
        // se le va a reducir la cantidad del ingreso de ese producto en esa tienda.
        $productoTienda = $ingreso->productoTienda;
        $productoTienda->cantidad -= $ingreso->cantidad;
        $productoTienda->save();
      }
    }
    // Obtenemos el número de la compra, si no tiene número le desinamos 'SIN NÚMERO'.
    if (!$numero = $compra->numero) {
      $numero = "SIN NÚMERO";
    }
    // Por último borramos la compra, al borrar la compra se borran sus detalles y,
    // por esto se borran sus ingresos automáticamente (La restriccción está en casacda).
    $compra->delete();
    // Retornamos una redirección a la lista de compras con un mensaje informando que se borro una compra.
    return redirect('compra')->with('info', 'SE ELIMINÓ LA COMPRA '.$numero.'. ESTO PRODUJO UNA REDUCCIÓN
      EN LA CANTIDAD DE PRODUCTOS EN LAS TIENDAS');
  }

  public function generarCodigo(Request $request){

    $linea = \App\Linea::find($request->linea_id);
    $familia = \App\Familia::find($request->familia_id);
    $marca = \App\Marca::find($request->marca_id);

    $codigo = substr($linea->nombre, 0, 2).substr($familia->nombre, 0, 2).substr($marca->nombre, 0, 2).time();
    return $codigo;
  }

  /**
   * Envía una lista de compras a la tabla de compras.
  */
  public function listar(Request $request){
    $line_quantity = intVal($request->current);
    $line_number = intVal($request->rowCount);
    $where = $request->searchPhrase;
    $sort = $request->sort;

    if (isset($sort['recibo'])) {
        $order_by = 'numero';
        $order_name = $sort['recibo'];
    }
    if (isset($sort['proveedor'])) {
        $order_by = 'proveedor';
        $order_name = $sort['proveedor'];
    }
    if (isset($sort['usuario'])) {
        $order_by = 'usuario';
        $order_name = $sort['usuario'];
    }
    if (isset($sort['fecha'])) {
        $order_by = 'fecha';
        $order_name = $sort['fecha'];
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
        $compras = Compra::join('proveedores', 'compras.proveedor_id', '=', 'proveedores.id')
          ->join('empresas', 'proveedores.empresa_ruc', '=', 'empresas.ruc')
          ->join('usuarios', 'compras.usuario_id', '=', 'usuarios.id')
          ->join('personas', 'usuarios.persona_dni', '=', 'personas.dni')
          ->select(
            'compras.numero as recibo',
            'compras.id as id',
            'compras.created_at as fecha',
            'empresas.nombre as proveedor',
            'personas.nombres as usuario'
            )
          ->distinct()
          ->offset($skip)
          ->limit($take)
          ->orderBy($order_by, $order_name)
          ->get();
      } else {
        $compras = Compra::join('proveedores', 'compras.proveedor_id', '=', 'proveedores.id')
          ->join('empresas', 'proveedores.empresa_ruc', '=', 'empresas.ruc')
          ->join('usuarios', 'compras.usuario_id', '=', 'usuarios.id')
          ->join('personas', 'usuarios.persona_dni', '=', 'personas.dni')
          ->where('compras.numero', 'like', '%'.$where.'%')
          ->orWhere('compras.created_at', 'like', '%'.$where.'%')
          ->orWhere('empresas.nombre', 'like', '%'.$where.'%')
          ->orWhere('personas.nombres', 'like', '%'.$where.'%')
          ->orWhere('personas.apellidos', 'like', '%'.$where.'%')
          ->select(
            'compras.numero as recibo',
            'compras.id as id',
            'compras.created_at as fecha',
            'empresas.nombre as proveedor',
            'personas.nombres as usuario'
            )
          ->distinct()
          ->offset($skip)
          ->limit($take)
          ->orderBy($order_by, $order_name)
          ->get();
      }

      if (empty($where)) {
        $total = Compra::all();

        $total = count($total);
      } else {
        $total = Compra::join('proveedores', 'compras.proveedor_id', '=', 'proveedores.id')
          ->join('empresas', 'proveedores.empresa_ruc', '=', 'empresas.ruc')
          ->join('usuarios', 'compras.usuario_id', '=', 'usuarios.id')
          ->join('personas', 'usuarios.persona_dni', '=', 'personas.dni')
          ->where('compras.numero', 'like', '%'.$where.'%')
          ->orWhere('compras.created_at', 'like', '%'.$where.'%')
          ->orWhere('empresas.nombre', 'like', '%'.$where.'%')
          ->orWhere('personas.nombres', 'like', '%'.$where.'%')
          ->orWhere('personas.apellidos', 'like', '%'.$where.'%')
          ->distinct()
          ->get();

        $total = count($total);
      }
    }

    $datas = [];

    foreach ($compras as $compra):

      $data = array_merge(
        array
        (
          "id" => $compra->id,
          "recibo" => $compra->recibo,
          "proveedor" => $compra->proveedor,
          "usuario" => $compra->usuario,
          "fecha" => \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $compra->fecha)->format('d/m/Y H:i A'),
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
    if($compra = Compra::find($request->id)){
      $proveedor = $compra->proveedor;
      $empresa = $proveedor->empresa;
      $usuario = $compra->usuario;
      $persona = $usuario->persona;
      $detalles = "";
      foreach ($compra->detalles as $detalle) {
        $detalles .= "<tr>
          <td>".$detalle->cantidad."</td>
          <td>".$detalle->producto->familia->nombre." ".$detalle->producto->marca->nombre." ".$detalle->producto->descripcion."</td>
          <td style='text-align:right;'>".$detalle->precio_unidad."</td>
          <td style='text-align:right;'>".$detalle->total."</td>
        </tr>";
      }
      $detalles .= "<th style='text-align:right;' colspan='3'>TOTAL</th>
      <td style='text-align:right;' class='total'>".$compra->total."</td>";
    }else{
      $proveedor = 0;
      $empresa = 0;
      $usuario = 0;
      $persona = 0;
      $detalles = 0;
    }
    return ['compra'=>$compra, 'proveedor'=>$proveedor, 'empresa'=>$empresa, 'usuario'=>$usuario, 'detalles'=>$detalles, 'persona'=>$persona];
  }
}
