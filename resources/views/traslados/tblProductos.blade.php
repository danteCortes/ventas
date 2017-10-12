<div class="col-xs-12 col-sm-12 col-md-7 col-lg-7">
  <div class="panel panel-default">
    <div class="panel-heading" style="background-color:#575757; color: #FFF;">
      <h3 class="panel-title">Buscar Producto
        <button type="button" class="btn btn-xs btn-default pull-right" data-toggle="collapse" data-target="#panelBuscar"
           aria-controls="panelBuscar" id="btnbuscar">
          <span class="fa fa-minus"></span>
        </button>
      </h3>
    </div>
    <div class="panel-body collapse in" id="panelBuscar" style="background-color:#bfbfbf;">
      <div class="table-responsive">
        <table class="table table-hover table-condensed table-bordered" id="tblProductos">
          <thead>
            <tr class="info">
              <th data-column-id="codigo" data-order="desc">Código</th>
              <th data-column-id="descripcion">Descripción</th>
              <th data-column-id="precio">Precio Venta</th>
              <th data-column-id="stock">Stock</th>
              <th data-column-id="commands" data-formatter="commands" data-sortable="false">Agregar</th>
            </tr>
          </thead>
        </table>
      </div>
    </div>
  </div>
</div>
