<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
    @if($venta = \App\Venta::where('usuario_id', Auth::user()->id)->where('tienda_id', Auth::user()->tienda_id)->where('estado', 1)->first())
      <div class="panel panel-default">
      <div class="panel-body" style="background-color:#bfbfbf;">
        {{Form::open(['url'=>'venta', 'id'=>'frmNuevaVenta'])}}
        <p class="puntos text-success" style="font-family: Cambria, Georgia"></p>
        <div class="row">
          <div class="col-xs-12 col-sm-12 col-md-6 col-lg-4">
            <div class="form-group">
              <div class="input-group">
                <input type="text" name="documento" class="form-control" placeholder="DNI/RUC" data-mask="99999999999" id="documento">
                <span class="input-group-btn">
                  <button class="btn btn-default" type="button"><span class="fa fa-search"></span></button>
                </span>
              </div>
            </div>
          </div>
          <div class="col-xs-12 col-sm-12 col-md-6 col-lg-8">
            <div class="form-group">
              <input type="text" name="nombre" class="form-control mayuscula" placeholder="RAZÓN SOCIAL" id="nombre" readonly>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-xs-12 col-sm-12 col-md-4 col-lg-3">
            <div class="form-group">
              <input type="text" name="nombres" class="form-control mayuscula" placeholder="NOMBRES" id="nombres" readonly>
            </div>
          </div>
          <div class="col-xs-12 col-sm-12 col-md-4 col-lg-3">
            <div class="form-group">
              <input type="text" name="apellidos" class="form-control mayuscula" placeholder="APELLIDOS" id="apellidos" readonly>
            </div>
          </div>
          <div class="col-xs-12 col-sm-12 col-md-4 col-lg-6">
            <div class="form-group">
              <input type="text" name="direccion" class="form-control mayuscula" placeholder="DIRECCIÓN" id="direccion" readonly>
            </div>
          </div>
        </div>
        <hr>
        <div class="row">
          <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
            <div class="form-group">
              <input type="text" name="soles" class="form-control moneda" placeholder="SOLES" id="efectivo">
            </div>
          </div>
          <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
            <div class="form-group">
              <div class="input-group">
                <input type="text" name="dolares" class="form-control moneda" placeholder="DOLARES" id="dolares">
                <span class="input-group-btn">
                  <button class="btn btn-default" type="button" id="btnTipoCambio">Tipo Cambio</button>
                </span>
              </div>
            </div>
          </div>
          <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
            <div class="form-group">
              <div class="input-group">
                @if($venta = \App\Venta::where('usuario_id', Auth::user()->id)->where('tienda_id', Auth::user()->tienda_id)
                  ->where('estado', 1)->first())
                  @if($tarjetaVenta = $venta->tarjetaVenta)
                    <input type="text" name="tarjeta" class="form-control moneda" placeholder="TARJETA" id="tarjeta"
                      value="{{$tarjetaVenta->monto}}">
                  @else
                    <input type="text" name="tarjeta" class="form-control moneda" placeholder="TARJETA" id="tarjeta">
                  @endif
                @else
                  <input type="text" name="tarjeta" class="form-control moneda" placeholder="TARJETA" id="tarjeta">
                @endif
                <span class="input-group-btn">
                  <button class="btn btn-default" type="button" id="btnRegistrarTarjeta">Registrar</button>
                </span>
              </div>
            </div>
          </div>
          <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
            <div class="form-group">
              @if($venta = \App\Venta::where('usuario_id', Auth::user()->id)->where('tienda_id', Auth::user()->tienda_id)
                ->where('estado', 1)->first())
                @if($tarjetaVenta = $venta->tarjetaVenta)
                  <input type="text" name="vuelto" class="form-control" placeholder="VUELTO"
                    value="{{number_format($tarjetaVenta->monto-$venta->total, 2, '.', ' ')}}" readonly id="vuelto">
                @else
                  <input type="text" name="vuelto" class="form-control" placeholder="VUELTO" readonly id="vuelto">
                @endif
              @else
                <input type="text" name="vuelto" class="form-control" placeholder="VUELTO" readonly id="vuelto">
              @endif
            </div>
          </div>
        </div>
        <hr>
        <div class="row">
          <div class="col-xs-6 col-sm-6 col-md-3 col-lg-3">
            <button type="submit" class="btn btn-primary">Terminar</button>
            <button type="button" class="btn btn-warning pull-right">Cancelar</button>
          </div>
          <div class="col-xs-6 col-sm-6 col-md-3 col-lg-3">
            <div class="form-group ">
              <div class="input-group grupo-puntos">
              </div>
            </div>
          </div>
        </div>
        {{Form::close()}}
      </div>
    </div>
    @endif
  </div>
</div>
