<div class="modal fade" id="mdlAnularComprobante">
    <div class="modal-dialog" id="busyAnularComprobante">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">ANULAR COMPROBANTE</h4>
            </div>
            <div class="modal-body">
                <div class="panel">
                    <div class="panel-body">
                        <p>ESTA A PUNTO DE ANULAR EL COMPROBANTE <strong id="numeracion-anular"></strong> CON UNA 
                        <strong id="tipo-anulacion"></strong>.</p>
                        <p>SI QUIERE CONTINUAR CON ESTA ACCIÓN HAGA CLIC EN EL BOTÓN ANULAR, DE LO CONTRARIO, EN EL BOTÓN
                        CANCELAR.</p>
                    </div>
                </div>
                <input type="hidden" id="id-comprobante-anular">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    <span class="glyphicon glyphicon-ban-circle"></span> Cancelar
                </button>
                <button type="button" class="btn btn-danger" onclick="anularComprobante()">
                    <span class="glyphicon glyphicon-trash"></span> Anular
                </button>
            </div>
        </div>
    </div>
</div>