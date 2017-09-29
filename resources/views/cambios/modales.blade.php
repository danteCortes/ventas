<!--Modal de confirmación para imprimir el ticket de la venta-->
<!--Fecha 29/09/2017-->
<div class="modal fade" id="verTicket" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header" style="background-color:#00bb00; color:#fff;">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
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
          <span class="glyphicon glyphicon-ban-circle"></span> Cancelar</button>
        <a href="javascript:void(0)" class="btn btn-success imprimir"><span class="glyphicon glyphicon-print"></span> Imprimir</a>
      </div>
    </div>
  </div>
</div>
<!--Modal de advertencia antes de eliminar la venta-->
<!--Fecha 29/09/2017-->
<div class="modal fade" id="eliminar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      {{Form::open(['id'=>'frmEliminar', 'method'=>'delete'])}}
      {{ csrf_field() }}
      <div class="modal-header" style="background-color:#bb0000; color:#fff;">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">ELIMINAR PRODUCTO</h4>
      </div>
      <div class="modal-body" style="background-color:#e69c2d">
        <div class="panel" style="background-color:#bd7406">
          <div class="panel-body">
            <p>ESTA A PUNTO DE ELIMINAR EL PRODUCTO <strong class="codigo"></strong>, CON ESTA ACCIÓN ELIMINARÁ TODOS
              LOS REGISTROS RELACIONADOS CON ESTE PRODUCTO INCLUYENDO SUS EXISTENCIAS E INGRESOS A LAS TIENDAS;
              DETALLES DE VENTAS, COMPRAS, CRÉDITOS Y PRÉSTAMOS.</p>
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
