<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Traits\Descuentos\ListarTodosTrait;
use App\Http\Traits\Descuentos\ListarVencidosTrait;
use App\Http\Traits\Descuentos\ListarVigentesTrait;

class DescuentoController extends Controller{

  use ListarTodosTrait, ListarVencidosTrait, ListarVigentesTrait;

  public function listarVigentes(){
    return view('descuentos.listarVigentes.inicio');
  }

  public function listarVencidos(){
    return view('descuentos.listarVencidos.inicio');
  }

  public function eliminar($id){
    $descuento = \App\Descuento::find($id);
    $descuento->delete();

    return redirect('descuento/listar-todos')->with('info', 'EL DESCUENTO NRO '.$descuento->id.' FUE ELIMINADO DE LOS REGISTROS DEL SISTEMA.');
  }

  public function modificar(Request $request, $id){
    $descuento = \App\Descuento::find($id);
    $descuento->linea_id = $request->linea_id;
    $descuento->familia_id = $request->familia_id;
    $descuento->marca_id = $request->marca_id;
    $descuento->porcentaje = $request->porcentaje;
    $descuento->fecha_fin = $request->fecha_fin;
    $descuento->save();

    return redirect('descuento/listar-todos')->with('correcto', 'EL DESCUENTO NRO '.$descuento->id.' PARA LA TIENDA '.$descuento->tienda->nombre.
      ' FUE MODIFICADO CON ÉXITO.');
  }

  public function listarTodos(){
    return view('descuentos.listarTodos.inicio');
  }

  public function guardar(Request $request){
    // Verificamos que halla escogido una de las opciones.
    if (!$request->linea_id && !$request->familia_id && !$request->marca_id) {
      return redirect('descuento/listar-todos')->with('error', 'DEBE ESCOGER AL MENOS UNA OPCIÓN ENTRE LÍNEA, FAMILIA O MARCA.');
    }
    // Si escogió al menos una opción, verificamos que halla elegido al menos una tienda.
    if (count($request->tiendas) == 0) {
      return redirect('descuento/listar-todos')->with('error', 'DEBE ESCOGER AL MENOS UNA TIENDA DONDE SE APLICARÁ EL DESCUENTO.');
    }
    // Si pasó las verificaciones, procedemos a guardar los descuentos.
    foreach ($request->tiendas as $key => $value) {
      $descuento = new \App\Descuento;
      $descuento->linea_id = $request->linea_id;
      $descuento->familia_id = $request->familia_id;
      $descuento->marca_id = $request->marca_id;
      $descuento->tienda_id = $key;
      $descuento->porcentaje = $request->porcentaje;
      $descuento->fecha_fin = $request->fecha_fin;
      $descuento->save();
    }
    return redirect('descuento/listar-todos')->with('correcto', 'LOS DECUENTOS FUERON AGREGADOS CON ÉXITO.');
    dd($request);
  }

  public function buscar(Request $request){
    $descuento = \App\Descuento::find($request->id);
    $descuento->linea;
    $descuento->familia;
    $descuento->marca;
    $descuento->tienda;
    return ['descuento'=>$descuento, 'lineas'=>\App\Linea::all(), 'familias'=>\App\Familia::all(), 'marcas'=>\App\Marca::all()];
  }
}
