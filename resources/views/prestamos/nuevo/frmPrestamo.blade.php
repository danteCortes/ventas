@if($prestamo = \App\Prestamo::where('usuario_id', \Auth::user()->id)->where('tienda_id', \Auth::user()->tienda_id)->where('estado', 1)->first())
<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
    <div class="panel panel-default">
      <div class="panel-body" style="background-color:#bfbfbf;">
        {{Form::open(['url'=>'prestamo/terminar/'.$prestamo->id, 'id'=>'frmPrestamo'])}}
        {{ csrf_field() }}
        <div class="row">
          <div class="col-xs-12 col-sm-12 col-md-6 col-lg-4">
            <div class="form-group">
              <select class="form-control" name="direccion" required>
                <option value="">TIPO DE PRESTAMO</option>
                <option value="0">PRESTAMO ENTRADA</option>
                <option value="1">PRESTAMO SALIDA</option>
              </select>
            </div>
          </div>
          <div class="col-xs-12 col-sm-12 col-md-6 col-lg-4">
          </div>
          <div class="col-xs-12 col-sm-12 col-md-6 col-lg-4">
          </div>
        </div>
        <div class="row">
          <div class="col-xs-12 col-sm-12 col-md-8 col-lg-8">
            <div class="form-group">
              <input type="text" name="socio" class="form-control mayuscula" placeholder="SOCIO" id="socio" required>
            </div>
          </div>
          <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
            <div class="form-group">
              <input type="date" name="fecha" class="form-control" placeholder="FECHA" id="fecha">
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-xs-6 col-sm-6 col-md-3 col-lg-3">
            <button type="submit" class="btn btn-primary">Terminar</button>
            <button type="button" class="btn btn-warning">Cancelar</button>
          </div>
        </div>
        {{Form::close()}}
      </div>
    </div>
  </div>
</div>
@endif
