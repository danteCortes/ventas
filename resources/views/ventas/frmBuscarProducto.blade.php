<div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
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
        <div class="input-group">
          <span class="input-group-addon">Código <span class="fa fa-barcode"></span></span>
          <input type="text" class="form-control" placeholder="CÓDIGO" id="txtCodigo">
          <span class="input-group-btn">
            <input type="hidden" name="tienda_id" value="{{Auth::user()->tienda_id}}" id="tienda_id">
            <button class="btn btn-default" type="button"><span class="fa fa-search"></span></button>
          </span>
        </div>
    </div>
  </div>
</div>
