<div class="modal fade" id="pagos" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header" style="background-color:#00bb00; color:#fff;">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">PAGAR SEPARACIÓN.</h4>
      </div>
      {{Form::open(['id'=>'frmPagar'])}}
      {{ csrf_field() }}
      <div class="modal-body" style="background-color:#e69c2d">
        <div class="panel" style="background-color:#bd7406">
          <div class="panel-body">
            <p>Saldo por cancelar: <span class="saldo">S/. </span></p>
            <div class="form-group">
              <label for="monto" class="control-label">Monto</label>
              <input type="text" name="monto" id="txtMonto" class="form-control moneda" placeholder="MONTO" required>
            </div>
            <div class="table-responsive">
              <table class="table table-condensed table-bordered" style="background-color:#449d44; color:#fff; border-color:#398439">
                <thead>
                  <tr>
                    <th style="border: 1px solid #398439">Fecha</th>
                    <th style="border: 1px solid #398439">Monto</th>
                  </tr>
                </thead>
                <tbody id="tblPagos">

                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer" style="background-color:#00bb00">
        <button type="button" class="btn btn-default" data-dismiss="modal">
          <span class="glyphicon glyphicon-ban-circle"></span> Cancelar</button>
        <button type="submit" class="btn btn-success" id="btnGuardarPago">Guardar</button>
      </div>
      {{Form::close()}}
    </div>
  </div>
</div>
