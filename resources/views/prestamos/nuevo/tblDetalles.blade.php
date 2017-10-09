<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
    <div class="table-responsive">
      @if($prestamo = \App\Prestamo::where('usuario_id', Auth::user()->id)->where('tienda_id', Auth::user()->tienda_id)->where('estado', 1)->first())
      <table class="table table-condensed table-bordered" style="background-color:#bfbfbf;">
        <thead>
          <tr>
            <th style="width:80px;">Operación</th>
            <th style="width:60px;">Cant.</th>
            <th>Descripción</th>
          </tr>
        </thead>
        <tbody id="detalles">
          @foreach($prestamo->detalles as $detalle)
            <tr>
              <td>
                {{Form::open(['url'=>'prestamo/quitar-detalle/'.$detalle->id, 'method'=>'delete', 'class'=>'pull-left'])}}
                  {{ csrf_field() }}
                  <button class="btn btn-xs btn-danger">Quitar</button>
                {{Form::close()}}</td>
              <td>{{$detalle->cantidad}}</td>
              <td>{{$detalle->producto->descripcion}}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
      @endif
    </div>
  </div>
</div>
