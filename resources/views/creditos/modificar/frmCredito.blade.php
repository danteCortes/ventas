<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
    <div class="panel panel-default">
      <div class="panel-body" style="background-color:#bfbfbf;">
        {{Form::open(['url'=>'modificar-credito/'.$credito->id, 'id'=>'frmCredito'])}}
        <div class="row">
          <div class="col-xs-12 col-sm-12 col-md-6 col-lg-4">
            <div class="form-group">
              <div class="input-group">
                <input type="text" name="documento" class="form-control" placeholder="DNI" data-mask="99999999" id="documento" required
                  value="{{$credito->persona->dni}}" readonly>
                <span class="input-group-btn">
                  <button class="btn btn-default" type="button"><span class="fa fa-search"></span></button>
                </span>
              </div>
            </div>
          </div>
          <div class="col-xs-12 col-sm-12 col-md-6 col-lg-4">
            <div class="form-group">
              <input type="text" name="nombres" class="form-control mayuscula" placeholder="NOMBRES" id="nombres" readonly
                value="{{$credito->persona->nombres}}" required>
            </div>
          </div>
          <div class="col-xs-12 col-sm-12 col-md-6 col-lg-4">
            <div class="form-group">
              <input type="text" name="apellidos" class="form-control mayuscula" placeholder="APELLIDOS" id="apellidos" readonly
                value="{{$credito->persona->apellidos}}" required>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-xs-12 col-sm-12 col-md-8 col-lg-6">
            <div class="form-group">
              <input type="text" name="direccion" class="form-control mayuscula" placeholder="DIRECCIÓN" id="direccion" readonly
                value="{{$credito->persona->direccion}}">
            </div>
          </div>
          <div class="col-xs-12 col-sm-12 col-md-4 col-lg-6">
            <div class="form-group">
              <input type="date" name="fecha" class="form-control" placeholder="FECHA" id="fecha"
                value="{{\Carbon\Carbon::createFromFormat('d/m/Y', $credito->fecha)->format('Y-m-d')}}">
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-xs-6 col-sm-6 col-md-3 col-lg-3">
            <input type="hidden" name="credito_id" value="{{$credito->id}}">
            <button type="submit" class="btn btn-primary">Terminar</button>
            <button type="button" class="btn btn-warning">Cancelar</button>
          </div>
        </div>
        {{Form::close()}}
      </div>
    </div>
  </div>
</div>
