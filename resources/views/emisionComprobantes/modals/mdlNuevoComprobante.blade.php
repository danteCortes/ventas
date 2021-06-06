<div class="modal fade" id="mdlNuevoComprobante">
    <div class="modal-dialog" id="busyNuevoComprobante">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="moda-title">Nuevo Comprobante</h4>
            </div>
            <div class="modal-body">
                <div class="form-horizontal">
                    <div class="form-group">
                        <div class="col-sm-4">
                            <input type="text" placeholder="DOCUMENTO" class="form-control input-sm" id="txt-documento"
                                onkeyup="handleBuscarCliente()">
                        </div>
                        <div class="col-sm-4 datos-persona">
                            <input type="text" placeholder="NOMBRES" class="form-control input-sm" id="txt-nombres"
                                style="text-transform: uppercase;">
                        </div>
                        <div class="col-sm-4 datos-persona">
                            <input type="text" placeholder="APELLIDOS" class="form-control input-sm" id="txt-apellidos"
                                style="text-transform: uppercase;">
                        </div>
                        <div class="col-sm-8 datos-empresa" style="display: none;">
                            <input type="text" placeholder="RAZON SOCIAL" class="form-control input-sm" id="txt-razon-social"
                                style="text-transform: uppercase;">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-12">
                            <input type="text" placeholder="DIRECCION" class="form-control input-sm" id="txt-direccion"
                                style="text-transform: uppercase;">
                        </div>
                    </div>
                </div>
                <table class="table table-condensed table-bordered table-striped">
                    <thead>
                        <tr style="font-size: 11px;">
                            <th class="text-center" style="width: 60px; vertical-align: middle;">CANT.</th>
                            <th class="text-center" style="vertical-align: middle;">DESCRIPCIÓN</th>
                            <th class="text-center" style="width: 70px; vertical-align: middle;">VALOR UNIT.</th>
                            <th class="text-center" style="width: 70px; vertical-align: middle;">IMPORTE TOTAL</th>
                            <th class="text-center" style="width: 30px; vertical-align: middle;">...</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="padding: 0px;">
                                <input type="text" class="form-control input-sm" id="txt-detalle-cantidad" placeholder="Cant."
                                    value="1" onkeyup="calcularImporteTotal()">
                            </td>
                            <td style="padding: 0px;">
                                <input type="text" class="form-control input-sm" id="txt-detalle-descripcion"
                                    placeholder="DESCRIPCION" style="text-transform: uppercase;">
                            </td>
                            <td style="padding: 0px;">
                                <input type="text" class="form-control input-sm" id="txt-detalle-valor-unitario" value="0.00"
                                    placeholder="VALOR UNIT." onkeyup="calcularImporteTotal()">
                            </td>
                            <td class="text-right" id="importe-total-detalle">0.00</td>
                            <td style="padding: 0px;" class="text-center">
                                <button class="btn btn-sm btn-primary" onclick="agregarDetalle()">
                                    <i class="fa fa-plus"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                    <tbody id="body-detalles">
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button class="btn btn-sm btn-default" data-dismiss="modal">
                    <i class="fa fa-ban"> Cerrar</i>
                </button>
                <button class="btn btn-sm btn-primary" onclick="guardarComprobante()">
                    <i class="fa fa-save"> Guardar</i>
                </button>
            </div>
        </div>
    </div>
</div>