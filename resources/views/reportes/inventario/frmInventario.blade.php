<div class="modal fade" id="frmInventario" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header" style="background-color:#407994; color:#fff;">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Inventario por Tiendas</h4>
      </div>
      <div class="modal-body" style="background-color:#fbba00">
        <div class="panel" style="background-color:#eeeeee">
          <div class="panel-body">
            <div class="row">
              <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                <div class="form-group">
                  <select class="form-control" name="tienda_id" id="tienda_inventario">
                    <option value="">SELECCIONAR TIENDA</option>
                    @foreach(\App\Tienda::all() as $tienda)
                      <option value="{{$tienda->id}}">{{$tienda->nombre}}</option>
                    @endforeach
                  </select>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer" style="background-color:#407994">
        <button type="button" class="btn btn-default" data-dismiss="modal">
          <span class="glyphicon glyphicon-ban-circle"></span> Cancelar</button>
        <button type="button" class="btn btn-primary" id="btnBuscarInventario">
          <span class="fa fa-address-card"></span> Inventario</button>
      </div>
    </div>
  </div>
</div>
