<div class="modal fade" id="eliminar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      {{Form::open(['id'=>'frmEliminar', 'method'=>'delete'])}}
      {{ csrf_field() }}
      <div class="modal-header" style="background-color:#bb0000; color:#fff;">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">ELIMINAR SEPARACIÓN</h4>
      </div>
      <div class="modal-body" style="background-color:#e69c2d">
        <div class="panel" style="background-color:#bd7406">
          <div class="panel-body">
            <p>ESTA A PUNTO DE ELIMINAR LA SEPARACIÓN NÚMERO <strong class="numero"></strong>, CON ESTA ACCIÓN ELIMINARÁ TODOS
              LOS REGISTROS RELACIONADOS CON ESTA SEPARACIÓN. DEBE CONFIRMAR CON LA CONTRASEÑA DEL ADMINISTRADOR.</p>
            <div class="form-group">
              <label for="password" class="control-label">Password del Administrador: </label>
              <input type="password" name="password" class="form-control">
            </div>
            <p>SI QUIERE CONTINUAR CON ESTA ACCIÓN HAGA CLIC EN EL BOTÓN ELIMINAR, DE LO CONTRARIO, EN EL BOTÓN
              CANCELAR.</p>
          </div>
        </div>
      </div>
      <div class="modal-footer" style="background-color:#bb0000">
        <button type="button" class="btn btn-default" data-dismiss="modal">
          <span class="glyphicon glyphicon-ban-circle"></span> Cancelar</button>
        <button type="submit" class="btn btn-danger"><span class="glyphicon glyphicon-trash"></span> Eliminar</button>
      </div>
      {{Form::close()}}
    </div>
  </div>
</div>
