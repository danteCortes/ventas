<div class="modal fade" id="editarGasto" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      {{Form::open(['id'=>'frmEditarGasto', 'method'=>'put'])}}
      {{ csrf_field() }}
      <div class="modal-header" style="background-color:#385a94; color:#fff;">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">MODIFICAR GASTO</h4>
      </div>
      <div class="modal-body" style="background-color:#e69c2d">
        <div class="panel" style="background-color:#bd7406">
          <div class="panel-body">
            <div class="form-group">
              <input type="text" class="form-control moneda input-sm" placeholder="MONTO *" name="monto" id="monto_gasto"
                required>
            </div>
            <div class="form-group">
              <textarea name="descripcion" rows="4" cols="80" class="form-control mayuscula" required placeholder="DESCRIPCIÓN"
                id="descripcion_gasto"></textarea>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer" style="background-color:#385a94">
        <button type="button" class="btn btn-default" data-dismiss="modal">
          <span class="glyphicon glyphicon-ban-circle"></span> Cancelar</button>
        <button type="submit" class="btn btn-primary"><span class="glyphicon glyphicon-floppy-disk"></span> Modificar</button>
      </div>
      {{Form::close()}}
    </div>
  </div>
</div>
