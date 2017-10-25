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
      <strong class="descuento text-success" style="font-family: Cambria, Georgia"></strong>
      <div class="row">
        <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 foto">
          <img src="{{url('storage/productos').'/producto.png'}}" style="width:100px;">
        </div>
        {{Form::open(['url'=>'detalle-cambio'])}}
          {{ csrf_field() }}
          {{Form::hidden('producto_codigo', null, ['id'=>'producto_codigo', 'required'=>''])}}
          {{Form::hidden('stock', null, ['id'=>'stock', 'required'=>''])}}
          {{Form::hidden('venta_id', $venta->id)}}
          <div class="col-xs-8 col-sm-8 col-md-8 col-lg-8">
            <div class="input-group">
              <span class="input-group-addon">Prec. Unit.: S/</span>
              <input type="text" class="form-control precio" placeholder="PRECIO" id="precio_venta" data-mask="##9.00" name="precio_unidad" required>
            </div>
            <div class="input-group">
              <span class="input-group-addon">Cantidad: </span>
              <input type="text" class="form-control" placeholder="CANTIDAD" name="cantidad" required>
              <span class="input-group-btn">
                <button class="btn btn-default" type="submit"><span class="fa fa-check"></span> Agregar</button>
              </span>
            </div>
          </div>
        {{Form::close()}}
      </div>
    </div>
  </div>
</div>
