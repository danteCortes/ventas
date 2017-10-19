<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Traits\Descuentos\ListarTodosTrait;

class DescuentoController extends Controller{

  use ListarTodosTrait;

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
}
