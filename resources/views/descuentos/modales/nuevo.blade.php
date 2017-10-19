<div class="modal fade" id="nuevo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      {{Form::open(['url'=>'descuento/guardar', 'id'=>'frmNuevoDescuento'])}}
      {{ csrf_field() }}
      <div class="modal-header" style="background-color:#385a94; color:#fff;">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">NUEVO DESCUENTO</h4>
      </div>
      <div class="modal-body" style="background-color:#e69c2d">
        <div class="panel" style="background-color:#bd7406">
          <div class="panel-body">
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <select class="form-control input-sm" name="linea_id">
                    <option value="">SELECCIONE LINEA</option>
                    @foreach(\App\Linea::all() as $linea)
                      <option value="{{$linea->id}}">{{$linea->nombre}}</option>
                    @endforeach
                  </select>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <select class="form-control input-sm" name="familia_id">
                    <option value="">SELECCIONE FAMILIA</option>
                    @foreach(\App\Familia::all() as $familia)
                      <option value="{{$familia->id}}">{{$familia->nombre}}</option>
                    @endforeach
                  </select>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <select class="form-control input-sm" name="marca_id">
                    <option value="">SELECCIONE MARCA</option>
                    @foreach(\App\Marca::all() as $marca)
                      <option value="{{$marca->id}}">{{$marca->nombre}}</option>
                    @endforeach
                  </select>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <input type="text" name="porcentaje" class="form-control input-sm porcentaje" placeholder="PORCENTAJE" required>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <input type="date" name="fecha_fin" class="form-control input-sm" required>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                  <div class="checkbox" style="margin-top: 0px; margin-bottom: 0px;">
                    @foreach(\App\Tienda::all() as $tienda)
                    <label style="font-size: 14px;">
                      <input type="checkbox" name="tiendas[{{$tienda->id}}]" value="1"> {{$tienda->nombre}}
                    </label>
                    @endforeach
                  </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer" style="background-color:#385a94">
        <button type="button" class="btn btn-default" data-dismiss="modal">
          <span class="glyphicon glyphicon-ban-circle"></span> Cancelar</button>
        <button type="submit" class="btn btn-primary"><span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
      </div>
      {{Form::close()}}
    </div>
  </div>
</div>
