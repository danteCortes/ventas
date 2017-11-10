<div class="modal fade bs-example-modal-lg" id="frmKardex" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header" style="background-color:#385a94; color:#fff;">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Kardex por Productos</h4>
      </div>
      <div class="modal-body" style="background-color:#e69c2d">
        <div class="panel" style="background-color:rgb(180, 180, 180)">
          <div class="panel-body">
            <div class="row">
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <div class="table-responsive">
                  <table class="table table-hover table-condensed table-bordered" id="tblProductos">
                    <thead>
                      <tr class="info">
                        <th data-column-id="codigo" data-order="desc">Código</th>
                        <th data-column-id="descripcion">Descripción</th>
                        <th data-column-id="precio">Precio Venta</th>
                        <th data-column-id="commands" data-formatter="commands" data-sortable="false">Operaciones</th>
                      </tr>
                    </thead>
                  </table>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <div class="table-resposive">
                  <table class="table table-bordered table-condensed">
                    <thead>
                      <tr>
                        <th>Código</th>
                        <th>Descripción</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td class="codigo"></td>
                        <td class="descripcion"></td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                <div class="form-group">
                  <input type="date" class="form-control input-sm" placeholder="INICIO*" required name="inicio" id="inicio_kardex">
                </div>
              </div>
              <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                <div class="form-group">
                  <input type="date" class="form-control input-sm" placeholder="FIN*" required name="fin" id="fin_kardex">
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                <div class="form-group">
                  <select class="form-control" name="tienda_id" id="tienda_kardex">
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
      <div class="modal-footer" style="background-color:#385a94">
        <input type="hidden" name="producto_codigo" id="producto_codigo_kardex">
        <button type="button" class="btn btn-default" data-dismiss="modal">
          <span class="glyphicon glyphicon-ban-circle"></span> Cancelar</button>
        <button type="button" class="btn btn-primary" disabled id="btnBuscarKardex">
          <span class="fa fa-address-card"></span> Kardex</button>
      </div>
    </div>
  </div>
</div>
