<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
    <div class="panel panel-default">
      <div class="panel-body" style="background-color:#bfbfbf;">
        {{Form::open(['url'=>'terminar-cambio'])}}
        <div class="row">
          <div class="col-xs-12 col-sm-12 col-md-6 col-lg-4">
            <div class="form-group">
              <div class="input-group">
                @if($persona = $venta->recibo->persona)
                  <input type="text" name="documento" class="form-control" placeholder="DNI/RUC" data-mask="99999999999"
                    value="{{$persona->dni}}" id="documento" readonly>
                @elseif($empresa = $venta->recibo->empresa)
                  <input type="text" name="documento" class="form-control" placeholder="DNI/RUC" data-mask="99999999999"
                    value="{{$empresa->ruc}}" id="documento" readonly>
                @else
                  <input type="text" name="documento" class="form-control" placeholder="DNI/RUC" data-mask="99999999999" id="documento"
                    readonly>
                @endif
                <span class="input-group-btn">
                  <button class="btn btn-default" type="button"><span class="fa fa-search"></span></button>
                </span>
              </div>
            </div>
          </div>
          <div class="col-xs-12 col-sm-12 col-md-6 col-lg-8">
            <div class="form-group">
              @if($empresa = $venta->recibo->empresa)
                <input type="text" name="nombre" class="form-control mayuscula" placeholder="RAZÓN SOCIAL" id="nombre" readonly
                  value="{{$empresa->nombre}}">
              @else
                <input type="text" name="nombre" class="form-control mayuscula" placeholder="RAZÓN SOCIAL" id="nombre" readonly>
              @endif
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-xs-12 col-sm-12 col-md-4 col-lg-3">
            <div class="form-group">
              @if($persona = $venta->recibo->persona)
                <input type="text" name="nombres" class="form-control mayuscula" placeholder="NOMBRES" id="nombres" readonly
                  value="{{$persona->nombres}}">
              @else
                <input type="text" name="nombres" class="form-control mayuscula" placeholder="NOMBRES" id="nombres" readonly>
              @endif
            </div>
          </div>
          <div class="col-xs-12 col-sm-12 col-md-4 col-lg-3">
            <div class="form-group">
              @if($persona = $venta->recibo->persona)
                <input type="text" name="apellidos" class="form-control mayuscula" placeholder="APELLIDOS" id="apellidos" readonly
                  value="{{$persona->apellidos}}">
              @else
                <input type="text" name="apellidos" class="form-control mayuscula" placeholder="APELLIDOS" id="apellidos" readonly>
              @endif
            </div>
          </div>
          <div class="col-xs-12 col-sm-12 col-md-4 col-lg-6">
            <div class="form-group">
              @if($persona = $venta->recibo->persona)
                <input type="text" name="direccion" class="form-control mayuscula" placeholder="DIRECCIÓN" id="direccion" readonly
                  value="{{$persona->direccion}}">
              @elseif($empresa = $venta->recibo->empresa)
                <input type="text" name="direccion" class="form-control mayuscula" placeholder="DIRECCIÓN" id="direccion" readonly
                  value="{{$empresa->direccion}}">
              @else
                <input type="text" name="direccion" class="form-control mayuscula" placeholder="DIRECCIÓN" id="direccion" readonly>
              @endif
            </div>
          </div>
        </div>
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
                @if($cambio = \App\Cambio::where('venta_id', $venta->id)->first())
                  @if($pagoTarjeta = \App\TarjetaVenta::where('cambio_id', $cambio->id)->first())
                    <input type="text" name="tarjeta" class="form-control moneda" placeholder="TARJETA" id="tarjeta" value="{{$pagoTarjeta->monto}}">
                  @else
                    <input type="text" name="tarjeta" class="form-control moneda" placeholder="TARJETA" id="tarjeta">
                  @endif
                @endif
                <span class="input-group-btn">
                  <button class="btn btn-default" type="button" id="btnRegistrarTarjeta">Registrar</button>
                </span>
              </div>
            </div>
          </div>
          <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
            <div class="form-group">
              @if($cambio = $venta->cambio)
                <input type="text" name="vuelto" class="form-control" value="{{number_format($cambio->diferencia, 2, '.', ' ')}}" placeholder="VUELTO" readonly id="vuelto">
              @else
                <input type="text" name="vuelto" class="form-control" placeholder="VUELTO" readonly id="vuelto">
              @endif
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-xs-6 col-sm-6 col-md-3 col-lg-3">
            <input type="hidden" name="venta_id" value="{{$venta->id}}" id="venta_id">
            <button type="submit" class="btn btn-primary">Terminar</button>
            <button type="button" class="btn btn-warning">Cancelar</button>
          </div>
        </div>
        {{Form::close()}}
      </div>
    </div>
  </div>
</div>
