<div class="modal fade" id="mdlAnularVenta">
    <div class="modal-dialog" id="busyAnularVenta">
        <div class="modal-content">
            <div class="modal-header" style="background-color:#bb0000; color:#fff;">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">ANULAR VENTA</h4>
            </div>
            <div class="modal-body" style="background-color:#e69c2d">
                <div class="panel" style="background-color:#bd7406">
                    <div class="panel-body">
                        <p>ESTA A PUNTO DE ANULAR LA VENTA <strong id="numeracion-anular"></strong> CON UNA 
                        <strong id="tipo-anulacion"></strong>, CON ESTA ACCIÓN ELIMINARÁ TODOS
                        LOS REGISTROS RELACIONADOS CON ESTA VENTA, LA CANTIDAD DE PRODUCTOS EN LOS DETALLES SE SUMARÁN AL STOCK
                        DE SU TIENDA DE SALIDA.</p>
                        <p>SI QUIERE CONTINUAR CON ESTA ACCIÓN HAGA CLIC EN EL BOTÓN ELIMINAR, DE LO CONTRARIO, EN EL BOTÓN
                        CANCELAR.</p>
                    </div>
                </div>
                <input type="hidden" id="id-venta-anular">
            </div>
            <div class="modal-footer" style="background-color:#bb0000">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    <span class="glyphicon glyphicon-ban-circle"></span> Cancelar
                </button>
                <button type="button" class="btn btn-danger" onclick="anularVenta()">
                    <span class="glyphicon glyphicon-trash"></span> Anular
                </button>
            </div>
        </div>
    </div>
</div>