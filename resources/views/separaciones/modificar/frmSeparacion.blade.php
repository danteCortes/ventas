<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
    <div class="panel panel-default">
      <div class="panel-body" style="background-color:#bfbfbf;">
        {{Form::open(['url'=>'separacion/modificar/'.$separacion->id, 'id'=>'frmSeparacion'])}}
        <div class="row">
          <div class="col-xs-12 col-sm-12 col-md-6 col-lg-4">
            <div class="form-group">
              <div class="input-group">
                <input type="text" name="documento" class="form-control" placeholder="DNI" data-mask="99999999" id="documento" value="{{$separacion->persona->dni}}" readonly required>
                <span class="input-group-btn">
                  <button class="btn btn-default" type="button"><span class="fa fa-search"></span></button>
                </span>
              </div>
            </div>
          </div>
          <div class="col-xs-12 col-sm-12 col-md-6 col-lg-4">
            <div class="form-group">
              <input type="text" name="nombres" class="form-control mayuscula" placeholder="NOMBRES" id="nombres" value="{{$separacion->persona->nombres}}" readonly required>
            </div>
          </div>
          <div class="col-xs-12 col-sm-12 col-md-6 col-lg-4">
            <div class="form-group">
              <input type="text" name="apellidos" class="form-control mayuscula" placeholder="APELLIDOS" id="apellidos" value="{{$separacion->persona->apellidos}}" readonly required>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-xs-12 col-sm-12 col-md-8 col-lg-6">
            <div class="form-group">
              <input type="text" name="direccion" class="form-control mayuscula" placeholder="DIRECCIÓN" id="direccion" value="{{$separacion->persona->direccion}}" readonly>
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
