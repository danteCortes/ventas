<?php

namespace App\Http\Controllers;

use App\ProductoTienda;
use Illuminate\Http\Request;

class ProductoTiendaController extends Controller{

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
      //
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
      //
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request){
    $detalle = \App\Detalle::find($request->detalle_id);

    // Verificamos que la cantidad ingresada no sumen más que la cantidad en el detalle.
    $cantidadTotal = 0;
    foreach (\App\Tienda::all() as $tienda) {
      $cantidadTotal += $request->cantidades[$tienda->id];
    }

    if ($cantidadTotal > $detalle->cantidad) {
      return redirect('compra/create')->with('error', 'EL TOTAL DE CANTIDADES INGRESADAS EN LAS DIFERENTES TIENDAS SOBREPASA LA CANTIDAD DE PRODUCTOS COMPRADOS');
    }
    if ($cantidadTotal < $detalle->cantidad) {
      return redirect('compra/create')->with('error', 'EL TOTAL DE CANTIDADES INGRESADAS EN LAS DIFERENTES TIENDAS NO COMPLETA LA CANTIDAD DE PRODUCTOS COMPRADOS');
    }

    //verificamos si ya existe el ingreso de este detalle
    if (!\App\Ingreso::where('detalle_id', $detalle->id)->first()) {
      //Si no existe un ingreso para este detalle, agregamos las cantidades a las tiendas y registramos el ingreso.

      //verificamos si el producto ya existe en las tiendas
      foreach (\App\Tienda::all() as $tienda) {
        if ($productoTienda = ProductoTienda::where('tienda_id', $tienda->id)
          ->where('producto_codigo', $detalle->producto_codigo)->first()) {
          // Si ya existe en la tienda, actualizamos la cantidad.
          if($productoTienda->cantidad >= 40){
            $productoTienda->cantidad = $request->cantidades[$tienda->id];
          }else{
            $productoTienda->cantidad += $request->cantidades[$tienda->id];
          }
          if ($request->ubicaciones[$tienda->id] != "") {
            $productoTienda->ubicacion = mb_strtoupper($request->ubicaciones[$tienda->id]);
          }
          $productoTienda->save();
        }else{
          // Si el producto nunca fue ingresado a la tienda, lo ingresamos como registro nuevo.
          $productoTienda = new ProductoTienda;
          $productoTienda->producto_codigo = $detalle->producto_codigo;
          $productoTienda->tienda_id = $tienda->id;
          $productoTienda->cantidad = $request->cantidades[$tienda->id];
          $productoTienda->ubicacion = mb_strtoupper($request->ubicaciones[$tienda->id]);
          $productoTienda->save();
        }

        //Registramos el ingreso de los productos.
        $ingreso = new \App\Ingreso;
        $ingreso->detalle_id = $detalle->id;
        $ingreso->producto_tienda_id = $productoTienda->id;
        $ingreso->cantidad = $request->cantidades[$tienda->id];
        $ingreso->save();
      }

      //verificamos si fue de una compra activa o terminada.
      if ($detalle->compra->estado) {

        return redirect('compra/create')->with('correcto', 'LA CANTIDAD DE PRODUCTOS SE INGRESARON CORRECTAMENTE A LAS TIENDAS.');
      }else{

        return redirect('compra/'.$detalle->compra->id.'/edit')->with('correcto', 'LA CANTIDAD DE PRODUCTOS SE INGRESARON CORRECTAMENTE A LAS TIENDAS.');
      }
    }else{
      // Primero restamos la cantidad anterior de los ingresos
      foreach (\App\Tienda::all() as $tienda) {
        $productoTienda = ProductoTienda::where('tienda_id', $tienda->id)
          ->where('producto_codigo', $detalle->producto_codigo)->first();

        $ingreso = \App\Ingreso::where('detalle_id', $detalle->id)->where('producto_tienda_id', $productoTienda->id)->first();

        $productoTienda->cantidad += ($request->cantidades[$tienda->id]-$ingreso->cantidad);
        if ($request->ubicaciones[$tienda->id] != "") {
          $productoTienda->ubicacion = mb_strtoupper($request->ubicaciones[$tienda->id]);
        }
        $productoTienda->save();

        $ingreso->cantidad = $request->cantidades[$tienda->id];
        $ingreso->save();
      }

      //verificamos si fue de una compra activa o terminada.
      if ($detalle->compra->estado) {

        return redirect('compra/create')->with('info', 'LA CANTIDAD DE PRODUCTOS SE MODIFICARON CORRECTAMENTE A LAS TIENDAS.');
      }else{

        return redirect('compra/'.$detalle->compra->id.'/edit')->with('correcto', 'LA CANTIDAD DE PRODUCTOS SE INGRESARON CORRECTAMENTE A LAS TIENDAS.');
      }
    }


  }

    /**
     * Display the specified resource.
     *
     * @param  \App\ProductoTienda  $productoTienda
     * @return \Illuminate\Http\Response
     */
    public function show(ProductoTienda $productoTienda)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\ProductoTienda  $productoTienda
     * @return \Illuminate\Http\Response
     */
    public function edit(ProductoTienda $productoTienda)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\ProductoTienda  $productoTienda
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ProductoTienda $productoTienda)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\ProductoTienda  $productoTienda
     * @return \Illuminate\Http\Response
     */
    public function destroy(ProductoTienda $productoTienda)
    {
        //
    }
}
