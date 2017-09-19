<?php

namespace App\Http\Controllers;

use App\Venta;
use Illuminate\Http\Request;

class VentaController extends Controller{

  /**
   * Muestra una lista de ventas hechas en esta tienda.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(){
    // Verificamos si se creó un cierre.
    if (\App\Cierre::where('usuario_id', Auth::user()->id)->where('tienda_id', Auth::user()->tienda_id)->where('estado', 1)) {
      return view('ventas.inicio');
    }else{
      return redirect("{{url('cierre/create')}}");
    }
  }

  /**
   * Muestra un formulario para crear una nueva venta.
   * Fecha: 18/09/2017
   * @return \Illuminate\Http\Response
   */
  public function create(){
    if (\App\Cierre::where('usuario_id', Auth::user()->id)->where('tienda_id', Auth::user()->tienda_id)->where('estado', 1)) {
      return view('ventas.nuevo');
    }else{
      return redirect("{{url('cierre/create')}}");
    }
  }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Venta  $venta
     * @return \Illuminate\Http\Response
     */
    public function show(Venta $venta)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Venta  $venta
     * @return \Illuminate\Http\Response
     */
    public function edit(Venta $venta)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Venta  $venta
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Venta $venta)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Venta  $venta
     * @return \Illuminate\Http\Response
     */
    public function destroy(Venta $venta)
    {
        //
    }
}
