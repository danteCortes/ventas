<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CambioController extends Controller{

  /**
   * Se va a agregar un detalle de venta a una venta que ya fue cerrada (estado 0),
   * esto es para hacer un cambio en la venta.
  */
  public function agregarDetalle(Request $request){
    // Primero validamos los campos enviados.
    \Validator::make(
      $request->all(),
      [
        'precio_unidad' => 'required',
        'cantidad' => 'required',
      ]
    );
    // Verificamos que se halla escogido un producto.
    if (!$request->producto_codigo || ($request->stock == null)) {
      return redirect('venta/'.$request->venta_id.'/edit')->with('error', 'DEBE ESCOGER UN PRODUCTO PARA AGREGAR UN DETALLE DE VENTA.');
    }
    // Verificamos que la cantidad por vender sea menor o igual al stock en tiendas.
    if ($request->cantidad > $request->stock) {
      return redirect('venta/'.$request->venta_id.'/edit')->with('error', 'ESTÁ QUERIENDO VENDER MÁS DE LO QUE TIENE EN EL ALMACÉN.');
    }
    // Identificamos la venta.
    $venta = \App\Venta::find($request->venta_id);
    // como es un cambio o devolución de producto primero verificamos si ya existe un cambio para esta venta.
    // Si existe un cambio para esta venta verificamos si esta activo (estado 1) o cerrado (estado 0).
    // Si esta cerrado ya no se puede hacer cambios en la venta.
    // Si no existe un cambio, la creamos.
    if ($cambio = \App\Cambio::where('venta_id', $venta->id)->first()) {
      // Entra a esta condición por que existe un cambio para esta venta, verificamos si este cambio esta activo.
      if (!$cambio->estado) {
        // Si el cambio ya esta cerrado es por que ya hicieron un cambio para esta venta,
        // por lo tanto no se puede hacer otro cambio.
        return redirect('venta')->with('error', ' ESTA VENTA YA TUVO UN CAMBIO ANTERIORMENTE,
        Y NO SE PODRA MODIFICAR NUEVAMENTE.');
      }
    }else {
      // En este caso aun no se a hecho un cambio a esta venta y creamos un nuevo cambio con los datos correspondientes.
      $cambio = new \App\Cambio;
      $cambio->usuario_id = \Auth::user()->id;
      $cambio->tienda_id = \Auth::user()->tienda_id;
      $cambio->venta_id = $venta->id;
      $cambio->cierre_id = \App\Cierre::where('usuario_id', \Auth::user()->id)->where('tienda_id', \Auth::user()->tienda_id)
        ->where('estado', 1)->first()->id;
      $cambio->estado = 1;
      $cambio->total_anterior = str_replace(' ', '', $venta->total);
      $cambio->diferencia = 0;
      $cambio->save();
    }
    // Verificamos si el producto tiene descuento vigente.
    $producto = \App\Producto::find($request->producto_codigo);
    // Ahora guardamos los datos del detalle.
    $detalle = new \App\Detalle;
    $detalle->venta_id = $venta->id;
    $detalle->producto_codigo = $request->producto_codigo;
    $detalle->cantidad = $request->cantidad;
    $detalle->precio_unidad = $request->precio_unidad;
    $detalle->descuento = $producto->precio - $request->precio_unidad;
    $detalle->total = $request->cantidad * $request->precio_unidad;
    $detalle->save();
    // Actualizamos el total de la venta.
    $venta->total = str_replace(' ', '', $venta->total) + str_replace(' ', '', $detalle->total);
    $venta->save();
    // Reducimos las unidades vendidas del stock en la tienda.
    $productoTienda = \App\ProductoTienda::where('producto_codigo', $request->producto_codigo)
      ->where('tienda_id', \Auth::user()->tienda_id)->first();
    $productoTienda->cantidad -= $request->cantidad;
    $productoTienda->save();
    // Aumentamos el precio del detalle al cierre de caja actual.
    $cierre = \App\Cierre::where('estado', 1)->where('usuario_id', \Auth::user()->id)
      ->where('tienda_id', \Auth::user()->tienda_id)->first();
    $cierre->total = str_replace(' ', '', $cierre->total) + str_replace(' ', '', $detalle->total);
    $cierre->save();
    // Actualizamos la diferencia en el cambio.
    $cambio->diferencia += $request->cantidad * $request->precio_unidad;
    $cambio->save();
    // Retornamos a la vista de venta nueva.
    return redirect('venta/'.$venta->id.'/edit')->with('correcto', 'SE AGREGÓ EL PRODUCTO A LA VENTA QUE ESTÁ POR CAMBIAR.');
  }

  /**
   * Se quita un detalle de venta de una venta que fue cerrada y se está modificando.
  */
  public function quitarDetalleCambio($detalle_id){
    // Primero identificamos el detalle que se quiere quitar.
    $detalle = \App\Detalle::find($detalle_id);
    // como es un cambio o devolución de producto primero verificamos si ya existe un cambio para esta venta.
    // Si existe un cambio para esta venta verificamos si esta activo (estado 1) o cerrado (estado 0).
    // Si esta cerrado ya no se puede hacer cambios en la venta.
    // Si no existe un cambio, la creamos.
    if ($cambio = \App\Cambio::where('venta_id', $detalle->venta->id)->first()) {
      // Entra a esta condición por que existe un cambio para esta venta, verificamos si este cambio esta activo.
      if (!$cambio->estado) {
        // Si el cambio ya esta cerrado es por que ya hicieron un cambio para esta venta,
        // por lo tanto no se puede hacer otro cambio.
        return redirect('venta')->with('error', ' ESTA VENTA YA TUVO UN CAMBIO ANTERIORMENTE,
        Y NO SE PODRA MODIFICAR NUEVAMENTE.');
      }
    }else {
      // En este caso aun no se a hecho un cambio a esta venta y creamos un nuevo cambio con los datos corresondientes.
      $cambio = new \App\Cambio;
      $cambio->usuario_id = \Auth::user()->id;
      $cambio->tienda_id = \Auth::user()->tienda_id;
      $cambio->venta_id = $detalle->venta->id;
      $cambio->cierre_id = \App\Cierre::where('usuario_id', \Auth::user()->id)->where('tienda_id', \Auth::user()->tienda_id)
        ->where('estado', 1)->first()->id;
      $cambio->estado = 1;
      $cambio->total_anterior = str_replace(' ', '', $detalle->venta->total);
      $cambio->diferencia = 0;
      $cambio->save();
    }
    // Actualizamos la diferencia en el cambio.
    $cambio->diferencia -= $detalle->total;
    $cambio->save();
    // Antes de borrar el detalle regresamos los productos a su tienda.
    $productoTienda =  \App\ProductoTienda::where('producto_codigo', $detalle->producto_codigo)
    ->where('tienda_id', $detalle->venta->tienda_id)->first();
    $productoTienda->cantidad += $detalle->cantidad;
    $productoTienda->save();
    // Luego descontamos el total del detalle a la venta que se está modificando.
    $venta = $detalle->venta;
    $venta->total = str_replace(' ', '', $venta->total) - str_replace(' ', '', $detalle->total);
    $venta->save();
    // Descontamos el total del detalle que se está eliminado, del total del cierre que esta activo actualmente.
    $cierre = \App\Cierre::where('estado', 1)->where('usuario_id', \Auth::user()->id)
      ->where('tienda_id', \Auth::user()->tienda_id)->first();
    $cierre->total = str_replace(' ', '', $cierre->total) - str_replace(' ', '', $detalle->total);
    $cierre->save();
    // Por último eliminamos el detalle.
    $detalle->delete();
    return redirect('venta/'.$venta->id.'/edit')->with('info', 'SE QUITO EL DETALLE DE VENTA CON ÉXITO.');
  }

  /**
   * Devuelve el vuelto que se le deberia dar al cliente despues de ingresar el
   * monto en efectivo, dolares y/o tarjeta.
  */
  public function vuelto(Request $request){
    // Primero identificamos la venta que nos envia, para identificar su cambio si es que existiese.
    if($cambio = \App\Cambio::where('usuario_id', \Auth::user()->id)->where('tienda_id', \Auth::user()->tienda_id)
      ->where('estado', 1)->first()){
      // Identificado el cambio activo vamos a ver cual es la diferencia que tiene que pagar el cliente.
      $diferencia = $cambio->diferencia;
      // Asignamos el efectivo enviado por el usuario a una variable $efectivo.
      $efectivo = 0;
      $efectivo += $request->efectivo;
      // Verificamos si nos está pagando con dólares.
      if ($request->dolares != "") {
        // Verificamos si está configurado un tipo de cambio.
        if($Configuracion = \App\Configuracion::whereNotNull('cambio')->first()){
          // Si etá configurado el tipo de cambio, cambiamos los dolares ingresados a soles.
          $efectivo += $request->dolares * $Configuracion->cambio;
        }else{
          // Si no está configurado el tipo de cambio, enviamos una alerta para que se configure el tipo de cambio.
          return "error-cambio";
        }
      }
      // sumamos lo ingresado por tarjeta al efectivo.
      $efectivo += $request->tarjeta;
      // Restamos el efectivo que juntamos de la venta total para saber el vuelto.
      $vuelto = number_format($efectivo-$diferencia, 2, '.', ' ');
      return $vuelto;
    }
    return "";
  }

  /**
   * Devolvemos el tipo de cambio en dolares que esta configurado, si no hay
   * configuracion alguna devolvemos cero.
  */
  public function tipoCambio(Request $request){
    // Verificamos si existe una configuracion del cambio.
    if ($configuracion = \App\Configuracion::whereNotNull('cambio')->first()) {
      // Si está configurado el tipo de cambio, retornamos un 0,
      return $configuracion->cambio;
    }
    return 0;
  }

  /**
   * Devuelve cuanto seria la comision por el uso de la tarjeta como medio de pago.
  */
  public function comisionTarjeta(Request $request){
    // Primero identificamos el tipo de tajeta que quiere utilizar.
    $tarjeta = \App\Tarjeta::find($request->tarjeta_id);
    // Verificamos cuanto sería la comision por el monto que pretende pagar con la tarjeta.
    $comision = number_format($request->monto * ($tarjeta->comision / 100), 2, '.', ' ');

    return $comision;
  }

  /**
   * se guarda un pago con tarjeta para el cambio cuando hay un excedente en el cambio de producto.
  */
  public function pagoTarjeta(Request $request){
    // Primero identificamos el cambio que se tiene que pagar.
    if($cambio = \App\Cambio::where('usuario_id', \Auth::user()->id)->where('tienda_id', \Auth::user()->tienda_id)
      ->where('estado', 1)->first()){
        // Verificamos si ya existe un pago co tarjeta para este cambio.
        if($pagoTarjeta = $cambio->tarjetaVenta){
          // Si ya se hizo un pago con tarjeta verificamos si el monto a cambiado.
          $pagoTarjeta->tarjeta_id = $request->tarjeta_id;
          $pagoTarjeta->monto = $request->monto;
          $pagoTarjeta->comision = number_format($request->monto * (\App\Tarjeta::find($request->tarjeta_id)->comision / 100), 2, '.', '');
          $pagoTarjeta->save();
          return redirect('venta/'.$cambio->venta->id.'/edit')->with('correcto', 'EL PAGO CON TARJETA FUE ACTUALIZADO CON EXITO. PUEDE TERMINAR LA VENTA.');
        }else{
          // Si existe el cambio, verificamos cuanto es la diferencia.
          $diferencia = $cambio->diferencia;
          // Verificamos si la diferencia es mayor a cero.
          if ($diferencia > 0) {
            // Si la diferencia es mayor a cero quiere decir que el cliente tiene que pagar un monto extra por el cambio.
            // Guardamos los datos en la tabla tarjeta_venta.
            $tarjetaVenta = new \App\TarjetaVenta;
            $tarjetaVenta->tarjeta_id = $request->tarjeta_id;
            $tarjetaVenta->cambio_id = $cambio->id;
            $tarjetaVenta->operacion = $request->operacion;
            $tarjetaVenta->monto = $request->monto;
            $tarjetaVenta->comision = number_format($request->monto * (\App\Tarjeta::find($request->tarjeta_id)->comision / 100), 2, '.', '');
            $tarjetaVenta->save();
            return redirect('venta/'.$cambio->venta->id.'/edit')->with('correcto', 'EL PAGO CON TARJETA FUE REGISTRADO CON EXITO. PUEDE TERMINAR LA VENTA.');
          }elseif($diferencia == 0){
            // Si la diferencia es cero no es necesario que se registre el pago con tarjeta.
            // Retornamos a la vista anterior con el mensaje correspondiente.
            return redirect('venta/'.$cambio->venta->id.'/edit')->with('error', 'NO ES NECESARIO REALIZAR UN PAGO CON TARJETA
            PARA ESTA OPERACIÓN, PUEDE TERMINAR LA OPERACIÓN SIN COBRAR NADA.');
          }else {
            // Si la diferencia es menor a cero es por que el cliente requeriria una devolución de dinero,
            // en esta version no se atiende este pedido.
            // Retornamos a la vista anterior con el mensaje correspondiente.
            return redirect('venta/'.$cambio->venta->id.'/edit')->with('error', 'EL TOTAL A COBRAR EN ESTE CAMBIO ES NEGATIVO, PARA CORREGIR ESTO
              QUITE EL PRODUCTO INGRESADO Y MODIFIQUE SU PRECIO DE VENTA PARA QUE COINCIDA CON EL TOTAL DE LA VENTA.');
          }
        }
    }else {
      // Si no existe un cambio activo no es necesario registrar el pago con tarjeta.
      // Retornamos a la vista anterior con el mensaje correspondiente.
      return redirect('venta')->with('error', 'NO EXISTE ESTE CAMBIO.');
    }
  }

  /**
   * Terminamos el cambio de una venta.
  */
  public function terminar(Request $request){
    // Primero verificamos el cambio que estamos terminando.
    if ($cambio = \App\Cambio::where('usuario_id', \Auth::user()->id)->where('tienda_id', \Auth::user()->tienda_id)
      ->where('estado', 1)->first()) {
      // Una vez identificado el cambio que se va a cerrar, verificamos que la diferencia de a venta
      // con las modificaciones sea mayor o igual a cero
      if ($cambio->diferencia > 0) {
        // Si es mayor o igual a cero procedemos a verificar si nos estan enviando el dinero suficiente para cubrir
        // la diferencia por el cambio de prodcto.
        $total_soles = 0;
        if($request->soles){
          // Si el cliente está abonando en efectivo, vamos sumando esto al total de soles que va a dar.
          $total_soles += $request->soles;
        }
        if($request->dolares) {
          // Si el cliente pretende pagar en dolares, verificamos si se configuró el tipo de cambio.
          if ($configuracion = \App\Configuracion::whereNotNull('cambio')->first()) {
            // Si se configuró el tipo de cambio. procedemos a calcular su valor en soles.
            $total_soles += number_format($request->dolares * $configuracion->cambio, 2, '.', '');
          }else{
            // Si no se configuró el tipo de cambio retornamos a la vista de venta nueva con un mensaje de error.
            return redirect('venta/'.$cambio->venta->id.'/edit')->with('error', 'DEBE CONFIGURAR EL TIPO DE CAMBIO ANTES DE RELIZAR UN PAGO EN DOLARES.
              DEBE HACER CLICK EN EL BOTÓN "Tipo Cambio"');
          }
        }
        if ($request->tarjeta) {
          // Si el cliente pretende pagar con tarjeta verificamos si lla registró el pago con tarjeta.
          if($tarjetaVenta = \App\TarjetaVenta::where('cambio_id', $cambio->id)->first()){
            // Si se registró la venta con tarjeta, verificamos que el monto ingresado corresponda al monto registrado.
            if ($tarjetaVenta->monto == $request->tarjeta) {
              // sumamos el monto al total.
              $total_soles += $request->tarjeta;
            }else{
              // Si el monto ingresado no corresponde al registrado regresamos a la vista anterior con un mensaje de error.
              return redirect('venta/'.$cambio->venta->id.'/edit')->with('error', 'ESTA INTENTANDO INGRESAR UN PAGO CON TARJETA DIFERENTE AL QUE REGISTRÓ!.');
            }
          }else{
            // Si no se registró la venta con tarjeta, regresamos a la vista anterior con un mensaje de error.
            return redirect('venta/'.$cambio->venta->id.'/edit')->with('error', 'DEBE REGISTRAR EL PAGO CON TARJETA ANTES DE FINALLIZAR LA VENTA.');
          }
        }
        // Una vez acumulado todos los pagos que hizo el cliente, verificamos si es igual o mayor a la diferencia del cambio.
        if ($total_soles >= $cambio->diferencia) {
          // Si es mayor procedemos a guardar los pagos.
          if ($request->soles) {
            $efectivo = new \App\Efectivo;
            $efectivo->cambio_id = $cambio->id;
            $efectivo->monto = $request->soles;
            $efectivo->save();
          }
          // Guardamos los dolares.
          if ($request->dolares) {
            $configuracion = \App\Configuracion::whereNotNull('cambio')->first();
            $dolares = new \App\Dolar;
            $dolares->cambio_id = $cambio->id;
            $dolares->monto = $request->dolares;
            $dolares->cambio = $configuracion->cambio;
            $dolares->save();
          }
          // El monto en tarjeta ya se debio registrar antes de terminar el cambio.
          // Terminado de guardar los montos, cerramos el cambio.
          $cambio->estado = 0;
          $cambio->save();
          // El cierre de caja se actualizó en cada movimiento que se hizo al agregar y quitar productos.
          // La diferencia de caja se registra en el cierre abierto actualmente.
          // $cierre = \App\Cierre::where('estado', 1)->where('usuario_id', \Auth::user()->id)
          //   ->where('tienda_id', \Auth::user()->tienda_id)->first();
          // $cierre->total = str_replace(' ', '', $cierre->total) + str_replace(' ', '', $cambio->diferencia);
          // $cierre->save();
          // No se emite recibo por cambio de productos.
          return redirect('venta')->with('correcto', 'EL CAMBIO SE TERMINÓ CON ÉXITO');
        }else{
          // Si el monto acumulado es menor que el total de la diferencia, regresamos a la vista de la venta con un mensaje de error.
          return redirect('venta/'.$cambio->venta->id.'/edit')->with('error', 'EL MONTO TOTAL INGRESADO NO COMPLETA EL TOTAL DE LA VENTA.');
        }
      }elseif ($cambio->diferencia == 0) {
        // Si la diferencia es igual a cero no es necesario revisar que el total enviado sea mayor o igual a la diferencia.
        // Tampoco es necesario tener que guardar los montos en la base de datos.
        // Procedemos a guardar el cambio.
        $cambio->estado = 0;
        $cambio->save();
        // El cierre de caja se actualizó en cada movimiento que se hizo al agregar y quitar productos.
        // La diferencia de caja se registra en el cierre abierto actualmente.
        // No se emite recibo por cambio de productos.
        return redirect('venta')->with('correcto', 'EL CAMBIO SE TERMINÓ CON ÉXITO');
      }else{
        // Si la diferencia es un monto negativo quiere decir que el producto a cambio no completa el monto de la venta original
        // Se envia un mensaje de error e instructivo para la corrección de el estado.
        return redirect('venta/'.$cambio->venta->id.'/edit')->with('error', 'EL TOTAL A COBRAR EN ESTE CAMBIO ES NEGATIVO, PARA CORREGIR ESTO
          QUITE EL PRODUCTO INGRESADO Y MODIFIQUE SU PRECIO DE VENTA PARA QUE COINCIDA CON EL TOTAL DE LA VENTA.');
      }
    }else{
      // Si no existe un cambio regresamos a la vista de ventas realizadas con el mensaje de que no se pudo guardar nada.
      return redirect('venta')->with('NO HIZO NINGUN CAMBIO A LA VENTA POR TANTO NO SE PUDO GUARDAR EL CAMBIO.');
    }
  }
}
