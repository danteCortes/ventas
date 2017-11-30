<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Tienda;
use App\Traslado;
use App\Detalle;
use App\ProductoTienda;
use Auth;

class TrasladoController extends Controller
{
    public function index()
    {
        //
    }

    public function create()
    {
        $traslados = Traslado::where('usuario_id', Auth::user()->id)->where('tienda_origen', Auth::user()->tienda_id)->where('estado', 1)->first();

        $counttraslados = count($traslados);

        $tiendas = Tienda::select('id', 'nombre')->where('id', '!=', \Auth::user()->tienda_id)->get();

        return view('traslados.nuevo.inicio', compact('tiendas', 'traslados', 'counttraslados'));
    }

    public function store(Request $request)
    {
        if($request->cantidad > $request->stock ){
            return redirect('traslado/create')->with('error', 'ESTÁ QUERIENDO TRASLADAR MÁS DE LO QUE TIENE EN EL ALMACÉN.');
        }
        else{
            if(!$traslado = Traslado::where('tienda_origen', \Auth::user()->tienda_id)->where('usuario_id', \Auth::user()->id)->where('estado', 1)->first()){

                $traslado = new \App\Traslado;
                $traslado->usuario_id = Auth::user()->id;
                $traslado->tienda_origen = Auth::user()->tienda_id;
                $traslado->cierre_id = \App\Cierre::where('estado', 1)->where('usuario_id', Auth::user()->id)
                  ->where('tienda_id', Auth::user()->tienda_id)->first()->id;
                $traslado->estado = 1;
                $traslado->save();
            }

            //guardar el detalle
            $detalle = new Detalle;
            $detalle->traslado_id =  $traslado->id;
            $detalle->producto_codigo =  $request->producto_codigo; // este como hidden
            $detalle->cantidad =  $request->cantidad;
            $detalle->save();

            $producto_tienda = ProductoTienda::where('producto_codigo', $request->producto_codigo)->where('tienda_id', Auth::user()->tienda_id)->first();
            $producto_tienda->cantidad -= $request->cantidad;
            $producto_tienda->save();

            return redirect('traslado/create')->with('correcto', 'EL PRODUCTO SE AGREGO CORRECTAMENTE PARA SER TRASLADADO.');
        }
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id, Request $request)
    {
        $detalle_traslado = Detalle::find($id);
        $detalle_traslado->delete();

        $producto_tienda = ProductoTienda::where('producto_codigo', $request->producto_codigo)->where('tienda_id', Auth::user()->tienda_id)->first();
            $producto_tienda->cantidad += $request->cantidad;
            $producto_tienda->save();

        return redirect('traslado/create')->with('info', 'EL PRODUCTO A SER TRASLADADO FUE ELIMINADO DE LOS REGISTROS.');
    }

    public function terminar(Request $request){
        $tienda = Tienda::select()->where('id', $request->tienda_traslado)->first();

        if($request->id_traslado > 0){

            $traslado = Traslado::where('id', $request->id_traslado)->first();
            $traslado->tienda_destino = $request->tienda_traslado;
            $traslado->estado = 0;
            $traslado->save();

            foreach($traslado->detalle as $detalle){
                // Aca recorres cada detalle del traslado, no olvides hacer la
                // relacion en el modelo Traslado.
                $producto = $detalle->producto; // obtienes el producto.
                // verificas que exista el registro en la tabla producto_tienda
                if(!$producto_tienda = ProductoTienda::where('producto_codigo', $producto->codigo)->where('tienda_id', $request->tienda_traslado)->first()){

                    $producto_tienda = new ProductoTienda;
                    $producto_tienda->producto_codigo = $producto->codigo;
                    $producto_tienda->tienda_id = $request->tienda_traslado;
                    $producto_tienda->cantidad = 0;
                    $producto_tienda->save();
                }

                $producto_tienda->cantidad += $detalle->cantidad;
                $producto_tienda->save();
                // si no existe lo creas y aumentas las cantidades del detalle.
            }

            return redirect('traslado/create')->with('correcto', 'EL TRASLADO SE HIZO SATISFACTIRAMENTE A LA SIGUIENTE TIENDA: '.$tienda->nombre.' .');
        }
        else{
            return redirect('traslado/create')->with('error', 'NO EXISTE NINGUN PRODUCTO A TRASLADAR, PORFAVOR AÑADA ALGUN PRODUCTO A TRASLADAR.');
        }
    }
}
