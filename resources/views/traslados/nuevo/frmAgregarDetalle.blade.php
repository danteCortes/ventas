<div class="col-xs-12 col-sm-12 col-md-5 col-lg-5">
  <div class="panel panel-default">
    <div class="panel-heading" style="background-color:#575757; color: #FFF;">
      <h3 class="panel-title">Agregar Producto
        <button type="button" class="btn btn-xs btn-default pull-right" data-toggle="collapse" data-target="#panelAgregar"
           aria-controls="panelAgregar" id="btnAgregar">
          <span class="fa fa-minus"></span>
        </button>
      </h3>
    </div>
    <div class="panel-body collapse in" id="panelAgregar" style="background-color:#bfbfbf;">
      <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
          <div class="table-resposive">
            <table class="table table-bordered table-condensed">
              <thead>
                <tr>
                  <th>Código</th>
                  <th>Descripción</th>
                  <th style="width:80px;">P. Venta</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td class="codigo"></td>
                  <td class="descripcion"></td>
                  <td class="precio"></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-xs-6 col-sm-6 col-md-4 col-lg-4 foto">
          <img src="{{url('storage/productos').'/producto.png'}}" style="width:100px;">
        </div>
        {{Form::open(['url'=>'traslado'])}}
          {{ csrf_field() }}
          {{Form::hidden('producto_codigo', null, ['id'=>'producto_codigo', 'required'=>''])}}
          {{Form::hidden('stock', null, ['id'=>'stock', 'required'=>''])}}
          {{Form::hidden('tipo', 3)}}
          <div class="col-xs-6 col-sm-6 col-md-8 col-lg-8">
            <br>
            <div class="input-group">
              <span class="input-group-addon">Cantidad: </span>
              <input type="number" class="form-control" placeholder="CANTIDAD" name="cantidad" required id="cantidad">
              <span class="input-group-btn">
                <button class="btn btn-default" type="submit" id="guardar" disabled="true"><span class="fa fa-cab"></span> Trasladar</button>
              </span>
            </div>
          </div>
        {{Form::close()}}
      </div>
    </div>
  </div>
</div>
