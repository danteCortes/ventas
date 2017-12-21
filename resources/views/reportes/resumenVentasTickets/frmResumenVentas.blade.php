<div class="modal fade bs-example-modal-lg" id="frmResumenVentasTickets" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header" style="background-color:#385a94; color:#fff;">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Resumen de Ventas</h4>
      </div>
      <div class="modal-body" style="background-color:#e69c2d">
        <div class="panel" style="background-color:rgb(180, 180, 180)">
          <div class="panel-body">
            <div class="row">
              <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                <div class="form-group">
                  <input type="date" class="form-control input-sm" placeholder="INICIO*" required name="inicio" id="inicio_resumenVentasTickets">
                </div>
              </div>
              <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                <div class="form-group">
                  <input type="date" class="form-control input-sm" placeholder="FIN*" required name="fin" id="fin_resumenVentasTickets">
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                <div class="form-group">
                  <select class="form-control" name="tienda_id" id="tienda_resumenVentasTickets">
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
        <button type="button" class="btn btn-default" data-dismiss="modal">
          <span class="glyphicon glyphicon-ban-circle"></span> Cancelar</button>
        <button type="button" class="btn btn-primary" id="btnBuscarResumenVentasTickets">
          <span class="fa fa-address-card"></span> Resumen Ventas</button>
      </div>
    </div>
  </div>
</div>
