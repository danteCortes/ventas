<div class="modal fade" id="verTicket" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background-color:#00bb00; color:#fff;">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">IMPRIMIR TICKET DE VENTA</h4>
            </div>
            <div class="modal-body" style="background-color:#e69c2d">
                <div class="panel" style="background-color:#bd7406">
                    <div class="panel-body">
                        <p>ESTA A PUNTO DE IMPRIMIR EL TIQUET DE VENTA N° <strong class="numeracion"></strong>.</p>
                        <div class="table-resposive" id="impTicket" style="text-align:center; background-color:#fff;">
                            ticket de venta
                        </div>
                        <p>SI QUIERE CONTINUAR CON ESTA ACCIÓN HAGA CLIC EN EL BOTÓN IMPRIMIR, DE LO CONTRARIO, EN EL BOTÓN
                        CANCELAR.</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="background-color:#00bb00">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    <span class="glyphicon glyphicon-ban-circle"></span> Cancelar
                </button>
                <button class="btn btn-success imprimir" onclick="imprimirTicket()">
                    <span class="glyphicon glyphicon-print"></span> Imprimir
                </button>
            </div>
        </div>
    </div>
</div>