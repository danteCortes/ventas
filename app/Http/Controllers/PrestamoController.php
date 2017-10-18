<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Traits\PrestamoTrait;

class PrestamoController extends Controller{

  use PrestamoTrait;

  public function nuevo(){
    return view('prestamos.nuevo.inicio');
  }

  public function agregarDetalle(Request $request){

    // Verificamos si existe un prestamo activo (estado 1) en esta tienda y con este usuario
    if (!$prestamo = \App\Prestamo::where('usuario_id', \Auth::user()->id)->where('tienda_id', \Auth::user()->tienda_id)
      ->where('estado', 1)->first()) {
      // si no existe el prestamo, procedemos a crearlo.
      $prestamo = $this->iniciarPrestamo();
    }
    // Si la cantidad por agregar es menor o igual al stock de la tienda, procedemos a guardar el detalle relacionado con el prestamo.
    $detalle = $this->nuevoDetalle($request, $prestamo->id);
    // Regresamos a la vista anterior con el mensaje correspondiente.
    return redirect('prestamo')->with('correcto', 'EL DETALLE DEL PRESTAMO SE AGREGÓ CON EXITO.');
  }

  public function quitarDetalle($id){
    // Primero identificamos el detalle que vamos a quitar.
    $detalle = \App\Detalle::find($id);
    // Verificamos si es el último detalle del prestamo.
    $prestamo = $detalle->prestamo;
    if (count($prestamo->detalles) > 1) {
      // Si el prestamo tiene más de un detalle, borramos el detalle.
      $detalle->delete();
    }else{
      // Si tiene un solo detalle, verificamos si es un detalle cerrado o activo.
      if ($prestamo->estado) {
        // Si es un prestamo activo, borramos todo el crédito.
        $prestamo->delete();
      }else{
        // Si es un prestamo ya cerrado borramos solo el detalle,
        // puede ser que posteriormente agreguen otro detalle.
        $detalle->delete();
        // regresamos a la vista anterior con el mensaje correspondiente
        return redirect('prestamo/modificar/'.$prestamo->id)->with('info', 'SE QUITO UN DETALLE DEL CREDITO');
      }
    }
    // regresamos a la vista anterior con el mensaje correspondiente
    return redirect('prestamo')->with('info', 'SE QUITO UN DETALLE DEL PRESTAMO.');
  }

  public function terminar(Request $request, $id){
    // Identificamos el prestamo.
    $prestamo = \App\Prestamo::find($id);
    // Verificamos que dirección tiene el prestamo (dar prestamo: 1; recibir prestamo: 0).
    if ($request->direccion[0] == 1) {
      // Verificamos que se quiere prestar una cantidad que tenemos en la tienda.
      foreach ($prestamo->detalles as $detalle) {
        $productoTienda = \App\ProductoTienda::where('producto_codigo', $detalle->producto_codigo)
          ->where('tienda_id', \Auth::user()->tienda_id)->first();
        if ($productoTienda) {
          if ($productoTienda->cantidad < $detalle->cantidad) {
            return redirect('prestamo')->with('error', 'ESTÁ QUERIENDO PRESTAR MÁS DE LO QUE TIENE EN EL ALMACÉN.');
          }
        }else {
          return redirect('prestamo')->with('error', 'ESTÁ QUERIENDO PRESTAR MÁS DE LO QUE TIENE EN EL ALMACÉN.');
        }
      }
      // Si es 1, se va a dar un prestamo a una empresa externa.
      // descontamos las cantidades correspondientes de los productos en la tienda.
      foreach ($prestamo->detalles as $detalle) {
        $this->descontarProducto($detalle);
      }
    }else{
      // si no es 1, es por que se esta recibiendo un prestamo de una empresa externa.
      // Aumentamos las cantidades de los productos en la tienda.
      foreach ($prestamo->detalles as $detalle) {
        $this->devolverProducto($detalle);
      }
    }
    // Luego de identificar la direccion del prestamo, procedemos a cerrar el prestamo.
    $this->cerrarPrestamo($request, $prestamo);
    // Retornamos a la vista de todos los prestamos con el mensaje correspondiente.
    return redirect('prestamo/listar')->with('correcto', 'EL PRESTAMO FUE GUARDADO CON ÉXITO.');

  }

  public function listar(){
    return view('prestamos.listar.inicio');
  }

  public function editar($id){
    $prestamo = \App\Prestamo::find($id);
    return view('prestamos.modificar.inicio')->with('prestamo', $prestamo);
  }

  public function quitarDetalleEditar($detalle_id){
    // Identificamos el detalle a eliminar.
    $detalle = \App\Detalle::find($detalle_id);
    // Primero verificamos verificamos que tipo de prestamo es (salida:1 o Entrada:0).
    if ($detalle->prestamo->direccion[0] == 1) {
      // Si se trata de un prestamo de salida debemos devolver los productos a la tienda.
      $this->devolverProducto($detalle);
    }else{
      // Si es un prestamo de entrada, debemos descontar los productos que se prestaron.
      $this->descontarProducto($detalle);
    }
    // Luego eliminamos el detalle.
    $detalle->delete();
    // regresamos a la vista anterior con el mensaje definido.
    return redirect('prestamo/editar/'.$detalle->prestamo->id)->with('info', 'SE ELIMINO UN DETALLE DEL PRESTAMO.');
  }

  public function agregarDetalleEditar(Request $request, $id){
    // Verificamos que se está agregando una cantidad menor o igual al stock de la tienda.
    if ($request->cantidad > $request->stock) {
      // Si la cantidad que queremos agregar al prestamo es mayor al stock en la tienda retornamos a la vista anterior con el mensaje corresóndiente.
      return redirect('prestamo')->with('error', 'ESTÁ QUERIENDO PRESTAR MÁS DE LO QUE TIENE EN EL ALMACÉN.');
    }
    // Identificamos el prestamo a donde se va a gregar el nuevo detalle.
    $prestamo = \App\Prestamo::find($id);
    // Si la cantidad por agregar es menor o igual al stock de la tienda, procedemos a guardar el detalle relacionado con el prestamo.
    $detalle = $this->nuevoDetalle($request, $prestamo->id);
    // Verificamos que tipo de prestamo es (salida:1 o Entrada:0).
    if ($prestamo->direccion[0] == 1) {
      // Si se trata de un prestamo de salida debemos descontar los productos de la tienda.
      $this->descontarProducto($detalle);
    }else{
      // Si es un prestamo de entrada, debemos aumentar los productos que se prestaron.
      $this->devolverProducto($detalle);
    }
    // regresamos a la vista anterior con el mensaje definido.
    return redirect('prestamo/editar/'.$detalle->prestamo->id)->with('correcto', 'SE AGREGÓ UN NUEVO DETALLE DEL PRESTAMO.');
  }

  public function modificar(Request $request, $id){
    $prestamo = \App\prestamo::find($id);
    $prestamo->usuario_id = \Auth::user()->id;
    $prestamo->cierre_id = $this->cierreActual()->id;
    $prestamo->fecha = $request->fecha;
    $prestamo->socio = mb_strtoupper($request->socio);
    $prestamo->save();
    return redirect('prestamo/listar')->with('info', 'EL PRESTAMO FUE MODIFICADO CON ÉXITO.');
  }

  public function devolver($id){
    //identificamos el prestamo a devolver y cambiamos su atributo devuelto.
    $prestamo = \App\Prestamo::find($id);
    // Verificamos que dirección tiene el prestamo (dar prestamo: 1; recibir prestamo: 0).
    if ($prestamo->direccion[0] == 1) {
      // Si es 1, se dio un prestamo a una empresa externa.
      // devolvemos las cantidades correspondientes de los productos en la tienda.
      foreach ($prestamo->detalles as $detalle) {
        $this->devolverProducto($detalle);
      }
    }else{
      // si no es 1, es por que se recibió un prestamo de una empresa externa.
      // Verificamos si existe la cantidad necesaria para devolver el prestamo.
      foreach ($prestamo->detalles as $detalle) {
        $productoTienda = \App\ProductoTienda::where('tienda_id', $prestamo->tienda_id)
          ->where('producto_codigo', $detalle->producto_codigo)->first();
        if ($detalle->cantidad > $productoTienda->cantidad) {
          return redirect('prestamo')->with('error', 'NO HAY STOCK SUFICIENTE PARA DEVOLVER EL PRESTAMO.');
        }
      }
      // Descontamos las cantidades de los productos en la tienda.
      foreach ($prestamo->detalles as $detalle) {
        $this->descontarProducto($detalle);
      }
    }
    $prestamo->devuelto = 1;
    $prestamo->save();
    // Regresamos a la lista de todos los prestamos con el mensaje correspondiente.
    return redirect('prestamo/listar')->with('correcto', 'EL PRESTAMO '.$id.' FUE DEVUELTO CON SATISFACCIÓN.');
  }

  public function listarDevolver(){
    return view('prestamos.devolver.inicio');
  }

  public function listarRecoger(){
    return view('prestamos.recoger.inicio');
  }

  public function eliminar(Request $request, $id){
    // Primero verificamos si la contraseña ingresada es conrrespondiente a la de un administrador.
    $autorizacion = 0;
    foreach (\App\Usuario::where('tipo', 1)->get() as $administrador) {
      // verificamos que la contraseña ingresada sea del administrador.
      if(\Hash::check($request->password, $administrador->password)){
        $autorizacion = 1;
        break;
      }
    }
    // $verificamos si hay autorización de un administrador.
    if ($autorizacion) {
      $prestamo = \App\Prestamo::find($id);
      // Procedemos a eliminar el prestamo.
      // Antes de eliminar el prestamo verificamos si es un prestamo devuelto.
      if (!($prestamo->devuelto == 1)) {
        // Si es un prestamo aun no devuelto quiere decir que no se devueven o descuentan los productos a las tiendas
        // Primero verificamos que tipo de prestamo es.
        if ($prestamo->direccion[0] == 1) {
          // Si la direccion es 1, era un prestamo dado a otra empresa.
          // Devolvemos los productos a su tienda de origen.
          foreach ($prestamo->detalles as $detalle) {
            $this->devolverProducto($detalle);
          }
        }else{
          // Si la direccion es 0, era un prestamo recibido de otra empresa.
          // descontamos los productos de la tienda.
          foreach ($prestamo->detalles as $detalle) {
            $this->descontarProducto($detalle);
          }
        }
      }
      // // Por ultimo eliminamos el prestamo.
      // $prestamo->delete();
      // si no hay autorización retornamos a la vista anterior con el mensaje correspondiente.
      return redirect('prestamo/listar')->with('info', 'EL PRESTAMO '.$prestamo->id.' FUE ELIMINADO POR SU CUENTA DE CAJERO '.
        \Auth::user()->persona->nombres.' '.\Auth::user()->persona->apellidos.'.');
    }else{
      // si no hay autorización retornamos a la vista anterior con el mensaje correspondiente.
      return redirect('prestamo/listar')->with('error', 'LA CONTRASEÑA INGRESADA NO PERTENECE AL
        ADMINISTRADOR, NO CUENTA CON AUTORIZACIÓN PARA REALIZAR ESTA ACCIÓN.');
    }
  }


}
