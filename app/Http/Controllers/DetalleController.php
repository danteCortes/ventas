<?php

namespace App\Http\Controllers;

use App\Detalle;
use App\TarjetaVenta;
use Illuminate\Http\Request;
use Auth;
use Validator;

class DetalleController extends Controller{

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(){
      //
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create(){
      //
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request){
    //verificamos que tipo de detalle es
    switch ($request->tipo) {

      case 1:
        // El detalle es de una venta, Validamos los valores enviados desde el formulario.
        $validator = Validator::make(
          $request->all(),
          [
            'producto_codigo' => 'required',
            'precio_unidad' => 'required',
            'cantidad' => 'required',
            'stock' => 'required'
          ]
        );
        if($validator->fails()){
          return response()->json($validator->errors()->all(), 422);
        }
        if($request->cantidad > $request->stock){
          return response()->json(['La cantidad vendida es superior al stock del producto.'], 422);
        }
        // Verificamos si ya existe una venta abierta para este usuario.
        if(!$venta = \App\Venta::where('usuario_id', Auth::user()->id)->where('tienda_id', Auth::user()->tienda_id)->where('estado', 1)->first()){
          // Si no existe una venta abierta, se debe crear una nueva venta.
          $venta = new \App\Venta;
          $venta->usuario_id = Auth::user()->id;
          $venta->cierre_id = \App\Cierre::where('usuario_id', Auth::user()->id)->where('tienda_id', Auth::user()->tienda_id)
          ->where('estado', 1)->first()->id;
          $venta->tienda_id = Auth::user()->tienda_id;
          $venta->estado = 1;
          $venta->total = 0;
          $venta->save();
        }
        // Verificamos si el producto tiene descuento vigente.
        $producto = \App\Producto::find($request->producto_codigo);
        // Ahora guardamos los datos del detalle.
        $detalle = new Detalle;
        $detalle->venta_id = $venta->id;
        $detalle->producto_codigo = $request->producto_codigo;
        $detalle->cantidad = $request->cantidad;
        $detalle->precio_unidad = str_replace(' ', '', $request->precio_unidad);
        if ($producto->precio - str_replace(' ', '', $request->precio_unidad) > 0) {
          $detalle->descuento = $producto->precio - str_replace(' ', '', $request->precio_unidad);
        }
        $detalle->total = $request->cantidad * str_replace(' ', '', $request->precio_unidad);
        $detalle->save();
        // Actualizamos el total de la venta.
        $venta->total += $detalle->total;
        $venta->save();
        // Reducimos las unidades vendidas del stock en la tienda.
        $productoTienda = \App\ProductoTienda::where('producto_codigo', $request->producto_codigo)
          ->where('tienda_id', \Auth::user()->tienda_id)->first();
        $productoTienda->cantidad -= $request->cantidad;
        $productoTienda->save();
    
        $detalles = Detalle::join('productos as p', 'p.codigo', '=', 'detalles.producto_codigo')
          ->join('familias as f', 'f.id', '=', 'p.familia_id')
          ->join('marcas as m', 'm.id', '=', 'p.marca_id')
          ->select(
            'detalles.id',
            'detalles.cantidad',
            'p.codigo',
            \DB::raw("concat(f.nombre, ' ', m.nombre, ' ', p.descripcion) as descripcion"),
            'detalles.precio_unidad',
            'detalles.total'
          )
          ->where('detalles.venta_id', $venta->id)
          ->get()
        ;

        $tarjetaVenta = TarjetaVenta::join('tarjetas as t', 't.id', '=', 'tarjeta_venta.tarjeta_id')
          ->select(
            'tarjeta_venta.id',
            \DB::raw("concat('COMISIÓN POR USO DE TARJETA ', t.nombre, ' ', t.comision, '%') as descripcion"),
            'tarjeta_venta.comision'
          )
          ->where('tarjeta_venta.venta_id', $venta->id)
          ->first()
        ;
        return [
          'venta' => $venta,
          'detalles' => $detalles,
          'tarjetaVenta' => $tarjetaVenta
        ];
        break;

      case 2:
        Validator::make($request->all(), [
          'codigo' => 'required',
          'linea_id' => 'required',
          'familia_id' => 'required',
          'marca_id' => 'required',
          'descripcion' => 'required|max:255',
          'precio' => 'required',
          'cantidad' => 'required',
          'costo' => 'required',
          'tipo' => 'required',
          'foto' => 'nullable|image|max:1024',
        ])->validate();

        if ($foto = $request->file('foto')) {
          $nombre_foto = time().$foto->getClientOriginalName();
          \Storage::disk('productos')->put($nombre_foto,  \File::get($foto));
        }else{
          $nombre_foto = 'producto.png';
        }
        //verificamos si el producto exite.
        if ($producto = \App\Producto::find($request->codigo)) {

          //Si existe actualizamos los datos del producto.
          $producto->linea_id = $request->linea_id;
          $producto->familia_id = $request->familia_id;
          $producto->marca_id = $request->marca_id;
          $producto->descripcion = mb_strtoupper($request->descripcion);
          $producto->vencimiento = $request->vencimiento;
          $producto->precio = $request->precio;
          // Verificamos si ya tenia una foto.
          if ($producto->foto != 'producto.png') {
            // Ya tiene una foto, verificamos si le estamos cambiando de foto.
            if ($nombre_foto != 'producto.png') {
              // Le estamos cambiado la foto.
              $producto->foto = $nombre_foto;
            }
          }else{
            $producto->foto = $nombre_foto;
          }
          // Generamos la imagen del códgo de barras para el producto.
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
          // Generamos la imagen del códgo de barras para el producto.
        }

        // Verificamos si es una compra nueva o se está modificando una compra.
        if ($request->compra) {
          // Esta es una compra por modificar.
          $compra = \App\Compra::find($request->compra);
          $compra->usuario_id = Auth::user()->id;

          //guardamos el detalle de la compra.
          $detalle = new Detalle;
          $detalle->compra_id = $compra->id;
          $detalle->producto_codigo = $request->codigo;
          $detalle->cantidad = $request->cantidad;
          $detalle->precio_unidad = number_format($request->costo, 2, '.', '');
          $detalle->total = number_format($request->cantidad*$request->costo, 2, '.', '');
          $detalle->save();

          //actualizamos la compra
          $compra->total = str_replace(' ', '', $compra->total) + str_replace(' ', '', $detalle->total);
          $compra->save();

          return redirect('compra/'.$compra->id.'/edit')->with('correcto', 'SE AGREGÓ UN NUEVO DETALLE A LA COMPRA.');
        }else{
          //Es una compra, verificamos si ya existe una compra activa por este usuario.
          if (!$compra = \App\Compra::where('usuario_id', Auth::user()->id)->where('estado', 1)->first()) {
            //Si no existe una compra activa, creamos una nueva compra.
            $compra = new \App\Compra;
            $compra->usuario_id = Auth::user()->id;
            $compra->total = 0;
            $compra->estado = 1;
            $compra->save();
          }

          //guardamos el detalle de la compra.
          $detalle = new Detalle;
          $detalle->compra_id = $compra->id;
          $detalle->producto_codigo = $request->codigo;
          $detalle->cantidad = $request->cantidad;
          $detalle->precio_unidad = number_format($request->costo, 2, '.', '');
          $detalle->total = number_format($request->cantidad*$request->costo, 2, '.', '');
          $detalle->save();

          //actualizamos la compra
          $compra->total = str_replace(' ', '', $compra->total) + number_format($request->cantidad*$request->costo, 2, '.', '');
          $compra->save();
          return redirect('compra/create')->with('correcto', 'SE AGREGÓ UN NUEVO DETALLE A LA COMPRA.');
        }
        break;

      default:
      # code...
      break;
    }
  }

  /**
   * Display the specified resource.
   *
   * @param  \App\Detalle  $detalle
   * @return \Illuminate\Http\Response
   */
  public function show(Detalle $detalle){
      //
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  \App\Detalle  $detalle
   * @return \Illuminate\Http\Response
   */
  public function edit(Detalle $detalle){
    //
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \App\Detalle  $detalle
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, Detalle $detalle){
    //
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  \App\Detalle  $detalle
   * @return \Illuminate\Http\Response
   */
  public function destroy(Request $request, Detalle $detalle){

    if($compra = $detalle->compra){
      // Verificamos si es una nueva compra o una compra por modificar.
      if ($compra->estado == 1) {
        // Esta compra está activa ( es una nueva compra).

        //verificamos que este detalle no es el último de la compra.
        if(count($compra->detalles) > 1){
          //si no es el último, descontamos el total de la compra.
          $compra->total = str_replace(' ', '', $compra->total) - str_replace(' ', '', $detalle->total);
          $compra->save();


          //Borramos el detalle.
          $detalle->delete();
        }else{
          //Si este detalle es el ultimo de la compra, borramos la compra.
          $compra->delete();
        }
        // Verificamos si este detalle se había ingresado a una tienda.
        foreach (\App\Tienda::all() as $tienda) {
          if ($productoTienda = \App\ProductoTienda::where('tienda_id', $tienda->id)
          ->where('producto_codigo', $detalle->producto_codigo)->first()) {
            if ($ingreso = \App\Ingreso::where('detalle_id', $detalle->id)->where('producto_tienda_id', $productoTienda->id)
            ->first()) {
              $productoTienda->cantidad -= $ingreso->cantidad;
              $productoTienda->save();
            }
          }
        }

        return redirect('compra/create')->with('info', 'SE ELIMINÓ EL DETALLE DE ESTA COMPRA');
      }else{
        // Es una compra por modificar.
        $compra->usuario_id = Auth::user()->id;
        $compra->total = str_replace(' ', '', $compra->total) - str_replace(' ', '', $detalle->total);
        $compra->save();

        // Verificamos si este detalle se había ingresado a una tienda.
        foreach (\App\Tienda::all() as $tienda) {
          if ($productoTienda = \App\ProductoTienda::where('tienda_id', $tienda->id)
            ->where('producto_codigo', $detalle->producto_codigo)->first()) {
            if ($ingreso = \App\Ingreso::where('detalle_id', $detalle->id)->where('producto_tienda_id', $productoTienda->id)
              ->first()) {
              $productoTienda->cantidad -= $ingreso->cantidad;
              $productoTienda->save();
            }
          }
        }

        //Borramos el detalle.
        $detalle->delete();

        return redirect('compra/'.$compra->id.'/edit')->with('info', 'SE ELIMINÓ EL DETALLE DE LA COMPRA');
      }
    }elseif ($venta = $detalle->venta) {
      // Vamos a borrar el detalle de una venta, primero verificamos si es una venta activa o un cambio.
      if ($venta->estado == 1) {
        // Es una venta activa, Verificamos si es su unica venta.
        if (count($venta->detalles) == 1) {
          // Esta venta tiene un solo detalle, borramos la venta.
          $venta->delete();
        }else{
          // Esta venta tiene más de un detalle, primero descontamos el total de la venta.
          $venta->total -= $detalle->total;
          $venta->save();
          // Regresamos las unidades vendidas al stock de la tienda.
          // Borramos el detalle.
          $detalle->delete();
        }
        $productoTienda = \App\ProductoTienda::where('producto_codigo', $detalle->producto_codigo)
          ->where('tienda_id', \Auth::user()->tienda_id)->first();
        $productoTienda->cantidad += $detalle->cantidad;
        $productoTienda->save();
    
        $detalles = Detalle::join('productos as p', 'p.codigo', '=', 'detalles.producto_codigo')
          ->join('familias as f', 'f.id', '=', 'p.familia_id')
          ->join('marcas as m', 'm.id', '=', 'p.marca_id')
          ->select(
            'detalles.id',
            'detalles.cantidad',
            'p.codigo',
            \DB::raw("concat(f.nombre, ' ', m.nombre, ' ', p.descripcion) as descripcion"),
            'detalles.precio_unidad',
            'detalles.total'
          )
          ->where('detalles.venta_id', $venta->id)
          ->get()
        ;

        $tarjetaVenta = TarjetaVenta::join('tarjetas as t', 't.id', '=', 'tarjeta_venta.tarjeta_id')
          ->select(
            'tarjeta_venta.id',
            \DB::raw("concat('COMISIÓN POR USO DE TARJETA ', t.nombre, ' ', t.comision, '%') as descripcion"),
            'tarjeta_venta.comision'
          )
          ->where('tarjeta_venta.venta_id', $venta->id)
          ->first()
        ;

        return [
          'venta' => $venta,
          'detalles' => $detalles,
          'tarjetaVenta' => $tarjetaVenta
        ];
      }
    }
    return "no se reconoce la venta.";
  }
}
