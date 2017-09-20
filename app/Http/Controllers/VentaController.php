<?php

namespace App\Http\Controllers;

use App\Venta;
use Illuminate\Http\Request;
use Auth;

class VentaController extends Controller{

  /**
   * Muestra una lista de ventas hechas en esta tienda.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(){
    return view('ventas.inicio');
  }

  /**
   * Muestra un formulario para crear una nueva venta.
   * Fecha: 18/09/2017
   * @return \Illuminate\Http\Response
   */
  public function create(){
    return view('ventas.nuevo');
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
