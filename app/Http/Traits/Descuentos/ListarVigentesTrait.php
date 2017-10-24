<?php

namespace App\Http\Traits\Descuentos;

use Illuminate\Http\Request;

trait ListarVigentesTrait{

  public function llenarTablaVigentes(Request $request){
    $line_quantity = intVal($request->current);
    $line_number = intVal($request->rowCount);
    $where = $request->searchPhrase;
    $sort = $request->sort;

    if (isset($sort['id'])) {
        $order_by = 'descuentos.id';
        $order_name = $sort['id'];
    }
    if (isset($sort['tienda'])) {
        $order_by = 'tienda_id';
        $order_name = $sort['tienda'];
    }
    if (isset($sort['conceptos'])) {
        $order_by = 'conceptos';
        $order_name = $sort['conceptos'];
    }
    if (isset($sort['porcentaje'])) {
        $order_by = 'porcentaje';
        $order_name = $sort['porcentaje'];
    }
    if (isset($sort['inicio'])) {
        $order_by = 'inicio';
        $order_name = $sort['inicio'];
    }
    if (isset($sort['final'])) {
        $order_by = 'final';
        $order_name = $sort['final'];
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
        $descuentos = \DB::table('descuentos')
          ->leftJoin('lineas', 'lineas.id', '=', 'descuentos.linea_id')
          ->leftJoin('familias', 'familias.id', '=', 'descuentos.familia_id')
          ->leftJoin('marcas', 'marcas.id', '=', 'descuentos.marca_id')
          ->join('tiendas', 'tiendas.id', '=', 'descuentos.tienda_id')
          ->whereDate('descuentos.fecha_fin', '>=', date('Y-m-d'))
          ->select(
            'descuentos.id as id',
            'tiendas.nombre as tienda',
            \DB::raw("concat(COALESCE(lineas.nombre, ''), ' ', COALESCE(familias.nombre, ''), ' ', COALESCE(marcas.nombre, '')) as conceptos"),
            'descuentos.porcentaje as porcentaje',
            // 'lineas.nombre as linea',
            // 'familias.nombre as familia',
            // 'marcas.nombre as marca',
            'descuentos.created_at as inicio',
            'descuentos.fecha_fin as final'
            )
          ->distinct()
          ->offset($skip)
          ->limit($take)
          ->orderBy($order_by, $order_name)
          ->get();

      } else {
        $descuentos = \DB::table('descuentos')
          ->leftJoin('lineas', 'lineas.id', '=', 'descuentos.linea_id')
          ->leftJoin('familias', 'familias.id', '=', 'descuentos.familia_id')
          ->leftJoin('marcas', 'marcas.id', '=', 'descuentos.marca_id')
          ->join('tiendas', 'tiendas.id', '=', 'descuentos.tienda_id')
          ->whereDate('descuentos.fecha_fin', '>=', date('Y-m-d'))
          ->where('descuentos.id', 'like', '%'.$where.'%')
          ->orWhere('tiendas.nombre', 'like', '%'.$where.'%')
          ->orWhere(\DB::raw("concat(COALESCE(lineas.nombre, ''), ' ', COALESCE(familias.nombre, ''), ' ', COALESCE(marcas.nombre, ''))"),
            'like', '%'.$where.'%')
          ->orWhere('descuentos.porcentaje', 'like', '%'.$where.'%')
          // ->orWhere('lineas.nombre', 'like', '%'.$where.'%')
          // ->orWhere('familias.nombre', 'like', '%'.$where.'%')
          // ->orWhere('marcas.nombre', 'like', '%'.$where.'%')
          ->orWhere('descuentos.created_at', 'like', '%'.$where.'%')
          ->orWhere('descuentos.fecha_fin', 'like', '%'.$where.'%')
          ->select(
            'descuentos.id as id',
            'tiendas.nombre as tienda',
            \DB::raw("concat_ws(COALESCE(lineas.nombre, ''), ' ', COALESCE(familias.nombre, ''), ' ', COALESCE(marcas.nombre, '')) as conceptos"),
            'descuentos.porcentaje as porcentaje',
            // 'lineas.nombre as linea',
            // 'familias.nombre as familia',
            // 'marcas.nombre as marca',
            'descuentos.created_at as inicio',
            'descuentos.fecha_fin as final'
            )
          ->distinct()
          ->offset($skip)
          ->limit($take)
          ->orderBy($order_by, $order_name)
          ->get();
      }

      if (empty($where)) {
        $total = \DB::table('descuentos')
          ->leftJoin('lineas', 'lineas.id', '=', 'descuentos.linea_id')
          ->leftJoin('familias', 'familias.id', '=', 'descuentos.familia_id')
          ->leftJoin('marcas', 'marcas.id', '=', 'descuentos.marca_id')
          ->join('tiendas', 'tiendas.id', '=', 'descuentos.tienda_id')
          ->whereDate('descuentos.fecha_fin', '>=', date('Y-m-d'))
          ->distinct()
          ->get();

        $total = count($total);
      } else {
        $total = \DB::table('descuentos')
          ->leftJoin('lineas', 'lineas.id', '=', 'descuentos.linea_id')
          ->leftJoin('familias', 'familias.id', '=', 'descuentos.familia_id')
          ->leftJoin('marcas', 'marcas.id', '=', 'descuentos.marca_id')
          ->join('tiendas', 'tiendas.id', '=', 'descuentos.tienda_id')
          ->whereDate('descuentos.fecha_fin', '>=', date('Y-m-d'))
          ->where('descuentos.id', 'like', '%'.$where.'%')
          ->orWhere('tiendas.nombre', 'like', '%'.$where.'%')
          ->orWhere(\DB::raw("concat(COALESCE(lineas.nombre, ''), ' ', COALESCE(familias.nombre, ''), ' ', COALESCE(marcas.nombre, ''))"),
          'like', '%'.$where.'%')
          ->orWhere('descuentos.porcentaje', 'like', '%'.$where.'%')
          // ->orWhere('lineas.nombre', 'like', '%'.$where.'%')
          // ->orWhere('familias.nombre', 'like', '%'.$where.'%')
          // ->orWhere('marcas.nombre', 'like', '%'.$where.'%')
          ->orWhere('descuentos.created_at', 'like', '%'.$where.'%')
          ->orWhere('descuentos.fecha_fin', 'like', '%'.$where.'%')
          ->distinct()
          ->get();

        $total = count($total);
      }
    }

    $datas = [];

    foreach ($descuentos as $descuento):

      $data = array_merge(
        array
        (
          "id" => $descuento->id,
          "tienda" => $descuento->tienda,
          //"conceptos" => $descuento->linea." ".$descuento->familia." ".$descuento->marca,
          "conceptos" => $descuento->conceptos,
          "porcentaje" => $descuento->porcentaje."%",
          "inicio" => \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $descuento->inicio)->format('d/m/Y'),
          "final" => \Carbon\Carbon::createFromFormat('Y-m-d', $descuento->final)->format('d/m/Y'),
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

 ?>
