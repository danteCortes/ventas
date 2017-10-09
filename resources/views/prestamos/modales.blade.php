<div class="modal fade" id="ver" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header" style="background-color:#31b0d5; color:#fff;">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">DATOS DEL PRÉSTAMO</h4>
      </div>
      <div class="modal-body" style="background-color:#e69c2d">
        <div class="table-responsive" style="background-color:#bd7406">
          <table class="table table-condensed table-bordered table-hover" style="margin:0px; background-color:#bfbfbf;">
            <tr>
              <th>Número: </th>
              <td class="numero"></td>
            </tr>
            <tr>
              <th>Socio: </th>
              <td class="socio"></td>
            </tr>
            <tr>
              <th>Cajero: </th>
              <td>
                <div class="panel panel-default" style="margin-bottom:0px;">
                  <div class="panel-heading" style="background-color:#575757; color: #FFF;">
                    <h3 class="panel-title">Datos del Cajero
                      <button type="button" class="btn btn-xs btn-default pull-right" data-toggle="collapse" data-target="#panelUsuario"
                         aria-controls="panelUsuario">
                        <span class="fa fa-minus"></span>
                      </button>
                    </h3>
                  </div>
                  <div class="panel-body collapse" id="panelUsuario" style="background-color:#bfbfbf;">
                    <div class="table-responsive">
                      <table class="table table-condensed table-bordered" style="margin:0px; background-color:#bfbfbf; color:#ff;">
                        <tr>
                          <th>DNI:</th>
                          <td class="caj_dni"></td>
                        </tr>
                        <tr>
                          <th>Nombres y Apellidos:</th>
                          <td class="caj_nombres_apellidos"></td>
                        </tr>
                        <tr>
                          <th>Dirección:</th>
                          <td class="caj_direccion"></td>
                        </tr>
                        <tr>
                          <th>Teléfono:</th>
                          <td class="caj_telefono"></td>
                        </tr>
                      </table>
                    </div>
                  </div>
                </div>
              </td>
            </tr>
            <tr>
              <th>Detalles: </th>
              <td>
                <div class="panel panel-default" style="margin-bottom:0px;">
                  <div class="panel-heading" style="background-color:#575757; color: #FFF;">
                    <h3 class="panel-title">Detalles
                      <button type="button" class="btn btn-xs btn-default pull-right" data-toggle="collapse" data-target="#panelDetalles"
                         aria-controls="panelDetalles">
                        <span class="fa fa-minus"></span>
                      </button>
                    </h3>
                  </div>
                  <div class="panel-body collapse" id="panelDetalles" style="background-color:#bfbfbf;">
                    <div class="table-responsive">
                      <table class="table table-condensed table-bordered" style="margin:0px; background-color:#bfbfbf;">
                        <thead>
                          <tr>
                            <th>Cant.</th>
                            <th>Descripción</th>
                          </tr>
                        </thead>
                        <tbody class="detalles">
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </td>
            </tr>
            <tr>
              <th>Fecha de préstamo: </th>
              <td class="fecha_prestamo"></td>
            </tr>
            <tr>
              <th>Fecha de devolución: </th>
              <td class="fecha_devolucion"></td>
            </tr>
          </table>
        </div>
      </div>
      <div class="modal-footer" style="background-color:#31b0d5">
      <button type="button" class="btn btn-default" data-dismiss="modal">
        <span class="glyphicon glyphicon-remove"></span> Cerrar</button>
    </div>
    </div>
  </div>
</div>

<div class="modal fade" id="eliminar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      {{Form::open(['id'=>'frmEliminar', 'method'=>'delete'])}}
      {{ csrf_field() }}
      <div class="modal-header" style="background-color:#bb0000; color:#fff;">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">ELIMINAR PRÉSTAMO</h4>
      </div>
      <div class="modal-body" style="background-color:#e69c2d">
        <div class="panel" style="background-color:#bd7406">
          <div class="panel-body">
            <p>ESTA A PUNTO DE ELIMINAR EL PRÉSTAMO NÚMERO <strong class="numero"></strong>, CON ESTA ACCIÓN ELIMINARÁ TODOS
              LOS REGISTROS RELACIONADOS CON ESTE PRÉSTAMO, LA CANTIDAD DE PRODUCTOS EN LOS DETALLES SE SUMARÁN AL STOCK
              DE SU TIENDA DE SALIDA. DEBE CONFIRMAR CON LA CONTRASEÑA DEL ADMINISTRADOR.</p>
            <div class="form-group">
              <label for="password" class="control-label">Password del Administrador: </label>
              <input type="password" name="password" class="form-control" required>
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
<!--Modal que muestra los pagos hechos, el saldo y un form para guardar un pago.-->
<!--Fecha 07/10/2017-->
<div class="modal fade" id="devolver" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header" style="background-color:#00bb00; color:#fff;">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">DEVOLVER PRESTAMO.</h4>
      </div>
      {{Form::open(['id'=>'frmDevolver'])}}
      {{ csrf_field() }}
      <div class="modal-body" style="background-color:#e69c2d">
        <div class="panel" style="background-color:#bd7406">
          <div class="panel-body">
            <p class="direccion"></p>
            <div class="table-responsive">
              <table class="table table-condensed table-bordered" style="margin:0px; background-color:#bfbfbf;">
                <thead>
                  <tr>
                    <th>Cant.</th>
                    <th>Descripción</th>
                  </tr>
                </thead>
                <tbody class="detalles">
                </tbody>
              </table>
            </div>
            <p style="color: #FFF;" class="devuelto"></p>
          </div>
        </div>
      </div>
      <div class="modal-footer" style="background-color:#00bb00">
        <button type="button" class="btn btn-default" data-dismiss="modal">
          <span class="glyphicon glyphicon-ban-circle"></span> Cancelar</button>
        <button type="submit" class="btn btn-success" id="btnDevolver"> Devolver</button>
      </div>
      {{Form::close()}}
    </div>
  </div>
</div>
