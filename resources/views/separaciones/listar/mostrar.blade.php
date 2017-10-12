<div class="modal fade" id="ver" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header" style="background-color:#31b0d5; color:#fff;">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">DATOS DE LA SEPARACIÓN</h4>
      </div>
      <div class="modal-body" style="background-color:#e69c2d">
        <div class="table-responsive" style="background-color:#bd7406">
          <table class="table table-condensed table-bordered table-hover" style="margin:0px; background-color:#bfbfbf;">
            <tr>
              <th>Número: </th>
              <td class="numero"></td>
            </tr>
            <tr>
              <th>Cliente: </th>
              <td>
                <div class="panel panel-default" style="margin-bottom:0px;">
                  <div class="panel-heading" style="background-color:#575757; color: #FFF;">
                    <h3 class="panel-title">Datos del Cliente
                      <button type="button" class="btn btn-xs btn-default pull-right" data-toggle="collapse" data-target="#panelProveedor"
                         aria-controls="panelProveedor">
                        <span class="fa fa-minus"></span>
                      </button>
                    </h3>
                  </div>
                  <div class="panel-body collapse" id="panelProveedor" style="background-color:#bfbfbf;">
                    <div class="table-responsive">
                      <table class="table table-condensed table-bordered" style="margin:0px; background-color:#bfbfbf; color:#ff;">
                        <tr>
                          <th>DNI:</th>
                          <td class="cli_dni"></td>
                        </tr>
                        <tr>
                          <th>Nombre:</th>
                          <td class="cli_nombre"></td>
                        </tr>
                        <tr>
                          <th>Dirección:</th>
                          <td class="cli_direccion"></td>
                        </tr>
                      </table>
                    </div>
                  </div>
                </div>
              </td>
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
                            <th>P. Unit.</th>
                            <th>P. Total</th>
                            <th>Separación</th>
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
              <th>Fecha de separación: </th>
              <td class="fecha_separacion"></td>
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