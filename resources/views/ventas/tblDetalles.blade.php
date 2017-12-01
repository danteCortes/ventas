<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
    @if($venta = \App\Venta::where('usuario_id', Auth::user()->id)->where('tienda_id', Auth::user()->tienda_id)->where('estado', 1)->first())
    <div class="table-responsive">
      <table class="table table-condensed table-bordered" style="background-color:#bfbfbf;">
        <thead>
          <tr>
            <th style="width:80px;">Operación</th>
            <th style="width:60px;">Cant.</th>
            <th>Descripción</th>
            <th style="width:80px;">P. Unit.</th>
            <th style="width:80px;">Total</th>
          </tr>
        </thead>
        <tbody id="detalles">
            @foreach($venta->detalles as $detalle)
              <tr>
                <td>
                  {{Form::open(['url'=>'detalle/'.$detalle->id, 'method'=>'delete', 'class'=>'pull-left'])}}
                    {{ csrf_field() }}
                    <button class="btn btn-xs btn-danger">Quitar</button>
                  {{Form::close()}}</td>
                <td>{{$detalle->cantidad}}</td>
                <td>{{$detalle->producto->familia->nombre}} {{$detalle->producto->marca->nombre}} {{$detalle->producto->descripcion}}</td>
                <td style="text-align:right">{{$detalle->precio_unidad}}</td>
                <td style="text-align:right">{{$detalle->total}}</td>
              </tr>
            @endforeach
            @if($tarjetaVenta = $venta->tarjetaVenta)
              @if($tarjetaVenta->tarjeta->comision != 0)
                <tr>
                  <td>{{Form::open(['url'=>'tarjeta-venta/'.$tarjetaVenta->id, 'method'=>'delete', 'class'=>'pull-left'])}}
                    {{ csrf_field() }}
                    <button class="btn btn-xs btn-danger">Quitar</button>
                  {{Form::close()}}</td>
                  <td>1</td>
                  <td>COMISIÓN POR USO DE TARJETA {{$tarjetaVenta->tarjeta->nombre}} {{$tarjetaVenta->tarjeta->comision}}%</td>
                  <td style="text-align:right">{{$tarjetaVenta->comision}}</td>
                  <td style="text-align:right">{{$tarjetaVenta->comision}}</td>
                </tr>
              @endif
            @endif
          <tr>
            <td colspan="4"><strong class="pull-right">TOTAL: </strong></td>
            <td style="text-align:right">{{$venta->total}}</td>
          </tr>
        </tbody>
      </table>
    </div>
    @endif
  </div>
</div>
