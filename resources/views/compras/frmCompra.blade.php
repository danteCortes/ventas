<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
  <div class="panel panel-default">
    <div class="panel-heading" style="background-color:#575757; color: #FFF;">
      <h3 class="panel-title">Datos de la Compra
        <button type="button" class="btn btn-xs btn-default pull-right" data-toggle="collapse" data-target="#panelCompra"
           aria-controls="panelCompra" id="btnCompra">
          <span class="fa fa-minus"></span>
        </button>
      </h3>
    </div>
    {{Form::open(['url'=>'compra', 'id'=>'frmCompra'])}}
    {{ csrf_field() }}
    <div class="panel-body collapse in" id="panelCompra" style="background-color:#bfbfbf;">
      <div class="form-group">
        <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4" style="padding:0px; margin-bottom:15px;">
          <input type="text" name="ruc" class="form-control input-sm ruc" placeholder="RUC" id="ruc" required>
        </div>
        <div class="col-xs-8 col-sm-8 col-md-8 col-lg-8" style="padding-right:0px; margin-bottom:15px;">
          <input type="text" name="razonSocial" class="form-control input-sm mayuscula" placeholder="PROVEEDOR" id="razonSocial" required>
        </div>
      </div>
      <div class="form-group">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="padding:0px; margin-bottom:15px;">
          <input type="text" name="direccion" class="form-control input-sm mayuscula" placeholder="DIRECCIÓN" id="direccion">
        </div>
      </div>
      <div class="form-group">
        <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4" style="padding:0px; margin-bottom:15px;">
          <input type="text" name="telefono" class="form-control input-sm telefono" placeholder="TELEFONO" id="telefono">
        </div>
        <div class="col-xs-8 col-sm-8 col-md-8 col-lg-8" style="padding-right:0px; margin-bottom:15px;">
          <input type="text" name="representante" class="form-control input-sm mayuscula" placeholder="REPRESENTANTE" id="representante">
        </div>
      </div>
      <div class="form-group">
        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6" style="padding:0px; margin-bottom:15px;">
          <input type="text" name="numero" class="form-control input-sm mayuscula" placeholder="NRO RECIBO" id="numero">
        </div>
        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6" style="padding:0px; margin-bottom:15px; height: 30px;">
        </div>
      </div>
      <div class="form-group">
        <button type="submit" class="btn btn-sm btn-primary" id="btnAgregarDetalle">Guardar Compra</button>
      </div>
    </div>
    {{Form::close()}}
  </div>
</div>
