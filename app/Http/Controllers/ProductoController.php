<?php

namespace App\Http\Controllers;

use App\Producto;
use Illuminate\Http\Request;
use Validator;
use DB;
use Auth;
use DNS1D;

class ProductoController extends Controller{

  /**
   * Muestra una tabla con todos los productos que se venden en las tiendas.
   * Fecha 13/09/2017
   *
   * @return \Illuminate\Http\Response
   */
  public function index(){

    return view('productos.inicio');
  }

  /**
   * Guarda un nuevo producto si n exitía, si ya existía, modifica sus datos.
   * Fecha 13/09/2017
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request){
    // Validamos los datos enviados por el formulario.
    Validator::make($request->all(), [
      'codigo' => 'required',
      'linea_id' => 'required',
      'familia_id' => 'required',
      'marca_id' => 'required',
      'descripcion' => 'required|max:255',
      'vencimiento' => 'nullable|date',
      'precio' => 'required',
      'foto' => 'nullable|image|max:1024',
    ])->validate();
    // Verificamos si se está enviando una foto por el formulario.
    if ($foto = $request->file('foto')) {
      // Si se envia una foto por el formulario, guardamos la foto en la carpeta productos
      // y almacenamos el nombre de la foto en la variable $nombre_foto.
      $nombre_foto = time().$foto->getClientOriginalName();
      \Storage::disk('productos')->put($nombre_foto,  \File::get($foto));
    }else{
      // Si no se envia una foto por el formulario, alamcenamos el nombre estandar en la
      // variable $nombre_foto.
      $nombre_foto = 'producto.png';
    }
    //verificamos si el producto exite.
    if ($producto = \App\Producto::find($request->codigo)) {
      //Si existe un producto con el código enviado por el formulario, actualizamos los datos del producto.
      $producto->linea_id = $request->linea_id;
      $producto->familia_id = $request->familia_id;
      $producto->marca_id = $request->marca_id;
      $producto->descripcion = mb_strtoupper($request->descripcion);
      $producto->vencimiento = $request->vencimiento;
      $producto->precio = $request->precio;
      // Verificamos si ya tenia una foto.
      if ($producto->foto != 'producto.png') {
        // si ya tiene una foto, verificamos si le estamos cambiando de foto.
        if ($nombre_foto != 'producto.png') {
          // Le estamos cambiado la foto.
          $producto->foto = $nombre_foto;
        }
      }else{
        $producto->foto = $nombre_foto;
      }
      $producto->save();
    }else{
      //Si no exite el producto, creamos uno nuevo con sus datos.
      $producto = new \App\producto;
      $producto->codigo = mb_strtoupper($request->codigo);
      $producto->linea_id = $request->linea_id;
      $producto->familia_id = $request->familia_id;
      $producto->marca_id = $request->marca_id;
      $producto->descripcion = mb_strtoupper($request->descripcion);
      $producto->vencimiento = $request->vencimiento;
      $producto->precio = $request->precio;
      $producto->foto = $nombre_foto;
      $producto->save();
    }
    // Generamos la imagen del códgo de barras para el producto.
    DNS1D::setStorPath(storage_path("/codigosBarra/"));
    DNS1D::getBarcodePNGPath(mb_strtolower($request->codigo), "C128");
    // Retornamos una redirección al método index de este controlador para volver a mostrar
    // la lista de productos mostrando un mensaje de satisfacción.
    return redirect('producto')->with('correcto', 'SE INGRESARON LOS DATOS DEL PRODUCTO CORRECTAMENTE.');
  }

  /**
   * Modifica el producto con los nuevo datos ingresados por el formulario.
   * Fecha 13/09/2017
   * @param  \Illuminate\Http\Request  $request
   * @param  \App\Producto  $producto
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, Producto $producto){
    // Validamos los datos enviados por el formulario.
    Validator::make($request->all(), [
      'codigo' => 'required',
      'linea_id' => 'required',
      'familia_id' => 'required',
      'marca_id' => 'required',
      'descripcion' => 'required|max:255',
      'vencimiento' => 'nullable|date',
      'precio' => 'required',
      'foto' => 'nullable|image|max:1024',
    ])->validate();
    // Verificamos si se está enviando una foto por el formulario.
    if ($foto = $request->file('foto')) {
      // Si se envia una foto por el formulario, guardamos la foto en la carpeta productos
      // y almacenamos el nombre de la foto en la variable $nombre_foto.
      $nombre_foto = time().$foto->getClientOriginalName();
      \Storage::disk('productos')->put($nombre_foto,  \File::get($foto));
    }else{
      // Si no se envia una foto por el formulario, alamcenamos el nombre estandar en la
      // variable $nombre_foto.
      $nombre_foto = 'producto.png';
    }
    // Verificamos si está cambiando el código.
    if ($producto->codigo != $request->codigo) {
      // Se está cambiando el código del producto, verificamos si no existe otro producto con el nuevo código.
      if (Producto::find($request->codigo)) {
        // Si existe un producto con ese código, retornamos a la lista de productos con el mensaje de error correspondiente.
        return redirect('producto')->this('error', 'YA EXISTE UN PRODUCTO CON EL CÓDIGO '.$request->codigo.
        ', NO SE MODIFICARON LOS DATOS DEL PRODUCTO '.$producto->codigo);
      }
    }
    // Modificamos los datos del producto.
    $producto->codigo = $request->codigo;
    $producto->linea_id = $request->linea_id;
    $producto->familia_id = $request->familia_id;
    $producto->marca_id = $request->marca_id;
    $producto->descripcion = mb_strtoupper($request->descripcion);
    $producto->vencimiento = $request->vencimiento;
    $producto->precio = $request->precio;
    // Verificamos si ya tenia una foto.
    if ($producto->foto != 'producto.png') {
      // si ya tiene una foto, verificamos si le estamos cambiando de foto.
      if ($nombre_foto != 'producto.png') {
        // Le estamos cambiado la foto.
        $producto->foto = $nombre_foto;
      }
    }else{
      $producto->foto = $nombre_foto;
    }
    $producto->save();
    // Retornamos a la lista de todos los producto.
    return redirect('producto')->with('correcto', 'SE MODIFICARON LOS DATOS DEL PRODUCTO SIN PROBLEMAS.');
  }

  /**
   * Elimina un producto determinado de la base de datos, eliminando sus existencias
   * en las tiendas, sus ingresos a las tiendas, sus detalles de venta, compra, prestamos y creditos.
   * Se debe disminuir el monto total de las compras, ventas, creditos y prestamos que esten
   * relacionadas con este producto.
   * Fecha 13/09/2017
   * @param  \App\Producto  $producto
   * @return \Illuminate\Http\Response
   */
  public function destroy(Producto $producto){
    // verificamos si el producto tenia detalles.
    if (count($producto->detalles) != 0) {
      // Recorremos todos los detalles en los que estaba el producto.
      foreach ($producto->detalles as $detalle) {
        // Verificamos si el detalle era de una venta, compra, credito o prestamo.
        if ($venta = $detalle->venta) {
          // Si es un detalle de venta, verificamos si era el único detalle en esa venta.
          if (count($venta->detalles) == 1) {
            // Tiene solo un detalle, el que se va a eliminar. Eliminamos la venta.
            $venta->delete();
          }else{
            // Tiene más detalles, reducimos el monto total de la venta lo que era del detalle.
            $venta->total -= $detalle->total;
            $venta->save();
          }
        }elseif ($compra = $detalle->compra) {
          // Si es un detalle de compra, verificamos si era el único detalle en esa compra.
          if (count($compra->detalles) == 1) {
            // Tiene solo un detalle, el que se va a eliminar. Eliminamos la compra.
            $compra->delete();
          }else{
            // Tiene más detalles, reducimos el monto total de la compra lo que era del detalle.
            $compra->total -= $detalle->total;
            $compra->save();
          }
        }elseif ($credito = $detalle->credito) {
          // Si es un detalle de crédito, verificamos si era el único detalle de ese crédito.
          if (count($credito->detalles) == 1) {
            // Tiene solo un detalle, el que se va a eliminar. Eliminamos el credito.
            $credito->delete();
          }else{
            // Tiene más detalles, reducimos el monto total del credito lo que era del detalle.
            $credito->total -= $detalle->total;
            $credito->save();
          }
        }elseif ($prestamo = $detalle->prestamo) {
          // Si es un detalle de prestamo, verificamos si era el único detalle de ese prestamo.
          if (count($prestamo->detalles) == 1) {
            // Tiene solo un detalle, el que se va a eliminar. Eliminamos el prestamo.
            $prestamo->delete();
          }
        }
      }
      // Eliminamos el producto de la base de datos.
    }
    // Debemos borrar su imagen si lo tuviera.
    if ($producto->foto != "producto.png") {
      \Storage::disk('productos')->delete($producto->foto);
    }
    // Borramos su imagen de código de barras.
    \Storage::disk('barcode')->delete($producto->codigo);
    $producto->delete();
    // redireccionamos a la lista de productos con el mensaje correspondiente.
    return redirect('producto')->with('info', 'SE ELIMINÓ CORRECTAMENTE EL PRODUCTO '.$producto->codigo.'.');
  }

  /**
   * Busca un producto almacenado.
   * Fecha 13/09/2017
   * @param  \Illuminate\Http\Request  $request
   * @return array
  */
  public function buscarProducto(Request $request){

    if($producto = Producto::find($request->codigo)){

      $htmlLinea = "<option value=".$producto->linea->id.">".$producto->linea->nombre."</option>".
        "<option value=''>SELECCIONAR LÍNEA</option>";
      foreach (\App\Linea::all() as $linea) {
        $htmlLinea .= "<option value=".$linea->id.">$linea->nombre</option>";
      }
      $linea = $producto->linea;

      $htmlFamilia = "<option value=".$producto->familia->id.">".$producto->familia->nombre."</option>".
        "<option value=''>SELECCIONAR FAMILIA</option>";
      foreach (\App\Familia::all() as $familia) {
        $htmlFamilia .= "<option value=".$familia->id.">$familia->nombre</option>";
      }
      $familia = $producto->familia;

      $htmlMarca = "<option value=".$producto->marca->id.">".$producto->marca->nombre."</option>".
        "<option value=''>SELECCIONAR FAMILIA</option>";
      foreach (\App\Marca::all() as $marca) {
        $htmlMarca .= "<option value=".$marca->id.">$marca->nombre</option>";
      }
      $marca = $producto->marca;

      $htmlFoto = "<img src='".url('storage/productos/'.$producto->foto)."' class='img-responsive img-thumbnail' ".
        "style='height:100px; margin-bottom:10px;'>";

      $stock = "<tr><th>Tienda</th><th>Cantidad</th><th>Ubicación</th></tr>";

      $cantidad = 0;

      foreach ($producto->productoTiendas as $productoTienda) {
        $stock .= "<tr>".
            "<th>".$productoTienda->tienda->nombre."</th>".
            "<td>".$productoTienda->cantidad."</td>".
            "<td>".$productoTienda->ubicacion."</td>".
          "</tr>";
        if ($productoTienda->tienda_id == Auth::user()->tienda_id) {
          $cantidad = $productoTienda->cantidad;
        }
      }

      // Verificamos si el producto tiene algún descuento.
      $descuento = \DB::table('descuentos')
        ->where('linea_id', $producto->linea_id)
        ->where('familia_id', $producto->familia_id)
        ->where('marca_id', $producto->marca_id)
        ->where('tienda_id', Auth::user()->tienda_id)->first();

    }else {

      $htmlLinea = "<option value=''>SELECCIONAR LÍNEA</option>";
      foreach (\App\Linea::all() as $linea) {
        $htmlLinea .= "<option value=".$linea->id.">$linea->nombre</option>";
      }

      $htmlFamilia = "<option value=''>SELECCIONAR FAMILIA</option>";
      foreach (\App\Familia::all() as $familia) {
        $htmlFamilia .= "<option value=".$familia->id.">$familia->nombre</option>";
      }

      $htmlMarca = "<option value=''>SELECCIONAR MARCA</option>";
      foreach (\App\Marca::all() as $marca) {
        $htmlMarca .= "<option value=".$marca->id.">$marca->nombre</option>";
      }

      $htmlFoto = "<img src='".url('storage/productos/producto.png')."' class='img-responsive img-thumbnail' ".
        "style='height:100px; margin-bottom:10px;'>";

      $producto = 0;

      $stock = 0;
      $cantidad = 0;
      $descuento = "";

    }

    return ['producto'=>$producto, 'linea'=>[$htmlLinea, $linea], 'familia'=>[$htmlFamilia, $familia],
      'marca'=>[$htmlMarca, $marca], 'foto'=>$htmlFoto, 'stock'=>[$stock, $cantidad], 'descuento'=>$descuento];
  }

  /**
   * Lista todos los productos que se venden en las tiendas en la tabla.
   * Fecha 14/09/2017
   * @param  \Illuminate\Http\Request  $request
   * @return array
  */
  public function listar(Request $request){
    $line_quantity = intVal($request->current);
    $line_number = intVal($request->rowCount);
    $where = $request->searchPhrase;
    $sort = $request->sort;

    if (isset($sort['codigo'])) {
        $order_by = 'codigo';
        $order_name = $sort['codigo'];
    }
    if (isset($sort['descripcion'])) {
        $order_by = 'descripcion';
        $order_name = $sort['descripcion'];
    }
    if (isset($sort['precio'])) {
        $order_by = 'precio';
        $order_name = $sort['precio'];
    }
    if (isset($sort['stock'])) {
        $order_by = 'cantidad';
        $order_name = $sort['stock'];
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
        $productos = \DB::table('productos as p')
          ->join('familias as f', 'f.id', '=', 'p.familia_id')
          ->join('marcas as m', 'm.id', '=', 'p.marca_id')
          ->join('producto_tienda as pt', 'pt.producto_codigo', '=', 'p.codigo')
          ->where(function($consulta){
            if (\Auth::user()->tienda_id) {
              $consulta->where('pt.tienda_id', \Auth::user()->tienda_id);
            }else{
              $consulta->where('pt.tienda_id', \DB::table('tiendas')->first()->id);
            }
          })
          ->select(
            'p.codigo as codigo',
            \DB::raw("concat(f.nombre, ' ',m.nombre, ' ', p.descripcion) as descripcion"),
            'p.precio as precio',
            'pt.cantidad as cantidad',
            'pt.tienda_id as tienda'
          )
          ->distinct()
          ->offset($skip)
          ->limit($take)
          ->orderBy($order_by, $order_name)
          ->get();
      } else {
        $productos = \DB::table('productos as p')
          ->join('familias as f', 'f.id', '=', 'p.familia_id')
          ->join('marcas as m', 'm.id', '=', 'p.marca_id')
          ->join('producto_tienda as pt', 'pt.producto_codigo', '=', 'p.codigo')
          ->where(function($consulta){
            if (\Auth::user()->tienda_id) {
              $consulta->where('pt.tienda_id', \Auth::user()->tienda_id);
            }else{
              $consulta->where('pt.tienda_id', \DB::table('tiendas')->first()->id);
            }
          })
          ->where(function($query) use ($where){
            $query->where('p.codigo', 'like', '%'.$where.'%')
            ->orWhere(\DB::raw("concat(f.nombre, ' ',m.nombre, ' ', p.descripcion)"), 'like', '%'.$where.'%');
          })
          ->select(
            'p.codigo as codigo',
            \DB::raw("concat(f.nombre, ' ',m.nombre, ' ', p.descripcion) as descripcion"),
            'p.precio as precio',
            'pt.cantidad as cantidad',
            'pt.tienda_id as tienda'
          )
          ->distinct()
          ->offset($skip)
          ->limit($take)
          ->orderBy($order_by, $order_name)
          ->get();
      }

      if (empty($where)) {
        $total = \DB::table('productos as p')
          ->join('familias as f', 'f.id', '=', 'p.familia_id')
          ->join('marcas as m', 'm.id', '=', 'p.marca_id')
          ->join('producto_tienda as pt', 'pt.producto_codigo', '=', 'p.codigo')
          ->where(function($consulta){
            if (\Auth::user()->tienda_id) {
              $consulta->where('pt.tienda_id', \Auth::user()->tienda_id);
            }else{
              $consulta->where('pt.tienda_id', \DB::table('tiendas')->first()->id);
            }
          })
          ->select(
            'p.codigo as codigo',
            \DB::raw("concat(f.nombre, ' ',m.nombre, ' ', p.descripcion) as descripcion"),
            'p.precio as precio',
            'pt.cantidad as cantidad',
            'pt.tienda_id as tienda'
          )
          ->distinct()
          ->get();

        $total = count($total);
      } else {
        $total = \DB::table('productos as p')
          ->join('familias as f', 'f.id', '=', 'p.familia_id')
          ->join('marcas as m', 'm.id', '=', 'p.marca_id')
          ->join('producto_tienda as pt', 'pt.producto_codigo', '=', 'p.codigo')
          ->where(function($consulta){
            if (\Auth::user()->tienda_id) {
              $consulta->where('pt.tienda_id', \Auth::user()->tienda_id);
            }else{
              $consulta->where('pt.tienda_id', \DB::table('tiendas')->first()->id);
            }
          })
          ->where(function($query) use ($where){
            $query->where('p.codigo', 'like', '%'.$where.'%')
            ->orWhere(\DB::raw("concat(f.nombre, ' ',m.nombre, ' ', p.descripcion)"), 'like', '%'.$where.'%');
          })
          ->select(
            'p.codigo as codigo',
            \DB::raw("concat(f.nombre, ' ',m.nombre, ' ', p.descripcion) as descripcion"),
            'p.precio as precio',
            'pt.cantidad as cantidad',
            'pt.tienda_id as tienda'
          )
          ->distinct()
          ->get();

        $total = count($total);
      }
    }

    $datas = [];
    $cantidad = 0;
    foreach ($productos as $producto):
      $data = array_merge(
        array
        (
          "codigo" => $producto->codigo,
          "descripcion" => $producto->descripcion,
          "precio" => number_format($producto->precio, 2, '.', ' '),
          "stock" => $producto->cantidad,
          "tienda" => $producto->tienda
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

  /**
   * Envia un arreglo con el código del producto y su respectivo código de barras.
   * Fecha: 14/09/2017
  */
  public function imprimirCodigo(Request $request){
    if ($producto = Producto::find($request->codigo)) {
      $imgCodigo = "<img src='".url('storage/codigosBarra/'.mb_strtolower($producto->codigo)).".png' class='img-responsive img-thumbnail' ".
        "style='height:25px; width:95px;'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src='".url('storage/codigosBarra/'.mb_strtolower($producto->codigo)).".png' class='img-responsive img-thumbnail' ".
          "style='height:25px; width:95px;'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src='".url('storage/codigosBarra/'.mb_strtolower($producto->codigo)).".png' class='img-responsive img-thumbnail' ".
            "style='height:25px; width:95px;'>";
      return ['producto'=>$producto, 'codigoBarras'=>$imgCodigo];
    }
  }
}
