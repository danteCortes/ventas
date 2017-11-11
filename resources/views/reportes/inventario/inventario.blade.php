<div class="row oculto" id="inventario">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
    <div class="table-responsive" id="imprimirInventario">
      <table class="table table-bordered table-condensed">
        <thead>
          <tr>
            <th>Tienda:</th>
            <th colspan="2" class="tienda"></th>
            <th>RUC:</th>
            <th class="ruc"></th>
          </tr>
          <tr>
            <th>Dirección:</th>
            <th colspan="4" class="direccion"></th>
          </tr>
          <tr>
            <th>Código</th>
            <th>Descripción</th>
            <th>Cantidad</th>
            <th>V. Unit.</th>
            <th>V. Total</th>
          </tr>
        </thead>
        <tbody id="detalles-inventario">

        </tbody>
      </table>
    </div>
    <button type="button" class="btn btn-primary" id="imprimir-inventario"><span class="fa fa-print"></span> Imprimir</button>
    <a href="{{url('reporte')}}" class="btn btn-default"> Salir</a>
  </div>
</div>
