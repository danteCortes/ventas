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
          @foreach($credito->detalles as $detalle)
            <tr>
              <td>
                {{Form::open(['url'=>'quitar-detalle-credito/'.$detalle->id, 'method'=>'delete', 'class'=>'pull-left'])}}
                  {{ csrf_field() }}
                  <button class="btn btn-xs btn-danger">Quitar</button>
                {{Form::close()}}</td>
              <td>{{$detalle->cantidad}}</td>
              <td>{{$detalle->producto->descripcion}}</td>
              <td style="text-align:right">{{$detalle->precio_unidad}}</td>
              <td style="text-align:right">{{$detalle->total}}</td>
            </tr>
          @endforeach
          <tr>
            <td colspan="4"><strong class="pull-right">TOTAL: </strong></td>
            <td style="text-align:right">{{number_format($credito->total, 2, '.', ' ')}}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</div>
