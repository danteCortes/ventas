<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
    <div class="panel panel-default">
      <div class="panel-body" style="background-color:#bfbfbf;">
        {{Form::open(['url'=>'prestamo/modificar/'.$prestamo->id, 'id'=>'frmCredito'])}}
        <div class="row">
          <div class="col-xs-12 col-sm-12 col-md-6 col-lg-4">
            <div class="form-group">
              <select class="form-control" name="direccion" readonly>
                <option value="{{$prestamo->direccion[0]}}">{{$prestamo->direccion[1]}} (ACTUAL)</option>
              </select>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-xs-12 col-sm-12 col-md-8 col-lg-8">
            <div class="form-group">
              <input type="text" name="socio" class="form-control mayuscula" placeholder="SOCIO" id="socio"
                value="{{$prestamo->socio}}">
            </div>
          </div>
          <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
            <div class="form-group">
              @if($prestamo->fecha != 'INDEFINIDO')
                <input type="date" name="fecha" class="form-control" placeholder="FECHA" id="fecha"
                  value="{{\Carbon\Carbon::createFromFormat('d/m/Y', $prestamo->fecha)->format('Y-m-d')}}">
              @else
                <input type="date" name="fecha" class="form-control" placeholder="FECHA" id="fecha">
              @endif
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-xs-6 col-sm-6 col-md-3 col-lg-3">
            <button type="submit" class="btn btn-primary">Terminar</button>
          </div>
        </div>
        {{Form::close()}}
      </div>
    </div>
  </div>
</div>
