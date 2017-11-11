<div class="row oculto" id="fichaKardex">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
    <div class="table-responsive" id="imprimirKardex">
      <table class="table table-bordered table-condensed">
        <thead>
          <tr>
            <th colspan="2">Artículo:</th>
            <th colspan="3" class="codigo"></th>
            <th colspan="3">Línea:</th>
            <th colspan="3" class="linea"></th>
          </tr>
          <tr>
            <th colspan="2">Familia:</th>
            <th colspan="3" class="familia"></th>
            <th colspan="3">Marca:</th>
            <th colspan="3" class="marca"></th>
          </tr>
          <tr>
            <th colspan="2">Descripción:</th>
            <th colspan="3" class="descripcion"></th>
            <th colspan="3">Tienda:</th>
            <th colspan="3" class="tienda"></th>
          </tr>
          <tr>
            <th rowspan="2">Fecha</th>
            <th rowspan="2">Detalle</th>
            <th colspan="3" style="text-align:center">INGRESOS</th>
            <th colspan="3" style="text-align:center">SALIDAS</th>
            <th colspan="3" style="text-align:center">EXISTENCIAS</th>
          </tr>
          <tr>
            <th>Cantidad</th>
            <th>V. Unit.</th>
            <th>V. Total</th>
            <th>Cantidad</th>
            <th>V. Unit.</th>
            <th>V. Total</th>
            <th>Cantidad</th>
            <th>V. Unit.</th>
            <th>V. Total</th>
          </tr>
        </thead>
        <tbody id="detalles-kardex">

        </tbody>
      </table>
    </div>
    <button type="button" class="btn btn-primary" id="imprimir-kardex"><span class="fa fa-print"></span> Imprimir</button>
    <a href="{{url('reporte')}}" class="btn btn-default"> Salir</a>
  </div>
</div>
