<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
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
                  {{Form::open(['url'=>'quitar-detalle-cambio/'.$detalle->id, 'method'=>'delete', 'class'=>'pull-left'])}}
                    {{ csrf_field() }}
                    <button class="btn btn-xs btn-danger">Quitar</button>
                    {{Form::hidden('venta_id', $venta->id)}}
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
          @if($cambio = $venta->cambio)
          <tr>
            <td colspan="4"><strong class="pull-right">TOTAL VENTA: </strong></td>
            <td style="text-align:right">{{number_format($cambio->total_anterior, 2, '.', ' ')}}</td>
          </tr>
          <tr>
            <td colspan="4"><strong class="pull-right">DIFERENCIA: </strong></td>
            <td style="text-align:right">{{number_format($cambio->diferencia, 2, '.', ' ')}}</td>
          </tr>
          @endif
        </tbody>
      </table>
    </div>
  </div>
</div>
