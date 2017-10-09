<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
    <div class="table-responsive">
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
                {{Form::open(['url'=>'prestamo/quitar-detalle-editar/'.$detalle->id, 'method'=>'delete', 'class'=>'pull-left'])}}
                  {{ csrf_field() }}
                  <button class="btn btn-xs btn-danger">Quitar</button>
                {{Form::close()}}</td>
              <td>{{$detalle->cantidad}}</td>
              <td>{{$detalle->producto->descripcion}}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>
