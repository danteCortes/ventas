<?php

namespace App\Http\Controllers;

use App\Cierre;
use Illuminate\Http\Request;
use Auth;

class CierreController extends Controller{

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
  public function create(){
    // Verificamos si un administrador le dió permisos al usuario para abrir caja.
    if (Auth::user()->estado_caja == 1) {
      // Si le dió permiso para abrir caja.
      return view('cajas.nuevo');
    }elseif (Auth::user()->estado_caja == 2) {
      // Si el usuario ya abrió caja, mostramos la vista para cerrar caja.
      $cierre = \App\Cierre::where('usuario_id', Auth::user()->id)
        ->where('tienda_id', Auth::user()->tienda_id)->where('estado', 1)->first();
      return view('cajas.cerrar')->with('cierre', $cierre);
    }else{
      // En este caso el usuario no tiene permiso para abrir caja.
      return redirect('cajero') ->with('error', 'NO TIENE PERMISO PARA ABRIR CAJA, COMUNÍQUESE CON SU ADMINISTRADOR.');
    }
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request){
    $cierre = new Cierre;
    $cierre->usuario_id = Auth::user()->id;
    $cierre->tienda_id = Auth::user()->tienda_id;
    $cierre->inicio = $request->inicio;
    $cierre->total = $request->inicio;
    $cierre->estado = 1;
    $cierre->save();

    // Le damos al usuario el estado_caja 2 para que pueda acceder a las operaciones.
    $usuario = \App\Usuario::find(Auth::user()->id);
    $usuario->estado_caja = 2;
    $usuario->save();

    return redirect('cajero')->with('correcto', 'ABRIÓ CAJA CORRECTAMENTE.');
  }

    /**
     * Display the specified resource.
     *
     * @param  \App\Cierre  $cierre
     * @return \Illuminate\Http\Response
     */
    public function show(Cierre $cierre)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Cierre  $cierre
     * @return \Illuminate\Http\Response
     */
    public function edit(Cierre $cierre)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Cierre  $cierre
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Cierre $cierre)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Cierre  $cierre
     * @return \Illuminate\Http\Response
     */
    public function destroy(Cierre $cierre)
    {
        //
    }

  public function cierreCaja($id){
    $cierre = \App\Cierre::find($id);
    // Primero verificamos que no halla ningun proceso activo o abierto.
    // Verificamos que no halla una venta abierta.
    if (\App\Venta::where('estado', 1)->where('usuario_id', \Auth::user()->id)->where('tienda_id', \Auth::user()->tienda_id)->first()) {
      // Hay una venta sin cerrar o terminar, se retorna a la vista de la venta. con el mensaje respectivo
      return redirect('venta/create')->with('error', 'ANTES DE CERRAR CAJA DEBE CANCELAR O TERMINAR ESTA VENTA');
    }
    // Verificamos que no halla un cambio activo.

    // Cerramos la caja.
    $cierre->estado = 0;
    $cierre->save();

    // cambiamos el estado de caja del usuario.
    $usuario = \App\Usuario::find(\Auth::user()->id);
    $usuario->estado_caja = 0;
    $usuario->save();

    return redirect('cajero')->with('correcto', 'ACABA DE CERRAR SU CAJA SATISFACTORIAMENTE.');
  }
}
