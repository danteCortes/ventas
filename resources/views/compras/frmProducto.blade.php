<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
  <div class="panel panel-default">
    <div class="panel-heading" style="background-color:#575757; color: #FFF;">
      <h3 class="panel-title">Producto
        <button type="button" class="btn btn-xs btn-default pull-right" data-toggle="collapse" data-target="#panelBuscar"
           aria-controls="panelBuscar" id="btnbuscar">
          <span class="fa fa-minus"></span>
        </button>
      </h3>
    </div>
    {{Form::open(['url'=>'detalle', 'enctype'=>'multipart/form-data', 'id'=>'frmProducto'])}}
      {{ csrf_field() }}
      <div class="panel-body collapse in" id="panelBuscar" style="background-color:#bfbfbf;">
        <div class="form-group">
          <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div class="img-responsive" style="text-align:center;" id="imgProducto">
              <img src="{{url('storage/productos/producto.png')}}" alt="" class="img-responsive img-thumbnail"
                style="height:100px; margin-bottom:10px;">
            </div>
          </div>
        </div>
        <div class="form-group">
          <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6" style="padding:0px; margin-bottom:15px;">
            <input type="text" name="codigo" class="form-control input-sm mayuscula" placeholder="CÓDIGO" id="codigo">
          </div>
          <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6" style="padding-right:0px; margin-bottom:15px;">
            <button type="button" class="btn btn-sm btn-primary" id="btnGenerarCodigo">Generar Código</button>
          </div>
        </div>
        <div class="form-group">
          <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6" style="padding:0px; margin-bottom:15px;">
            <select class="form-control input-sm" name="linea_id" id="linea_id">
              <option value="">SELECCIONAR LÍNEA</option>
              @foreach(\App\Linea::all() as $linea)
                <option value="{{$linea->id}}">{{$linea->nombre}}</option>
              @endforeach
            </select>
          </div>
          <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6" style="margin-bottom:15px;">
            <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#nuevaLinea">
              <span class="glyphicon glyphicon-plus"></span> Línea
            </button>
          </div>
        </div>
        <div class="form-group">
          <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6" style="padding:0px; margin-bottom:15px;">
            <select class="form-control input-sm" name="familia_id" id="familia_id">
              <option value="">SELECCIONAR FAMILIA</option>
              @foreach(\App\Familia::all() as $familia)
              <option value="{{$familia->id}}">{{$familia->nombre}}</option>
              @endforeach
            </select>
          </div>
          <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6" style="margin-bottom:15px;">
            <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#nuevaFamilia">
              <span class="glyphicon glyphicon-plus"></span> Familia
            </button>
          </div>
        </div>
        <div class="form-group">
          <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6" style="padding:0px; margin-bottom:15px;">
            <select class="form-control input-sm" name="marca_id" id="marca_id">
              <option value="">SELECCIONAR MARCA</option>
              @foreach(\App\Marca::all() as $marca)
              <option value="{{$marca->id}}">{{$marca->nombre}}</option>
              @endforeach
            </select>
          </div>
          <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6" style="margin-bottom:15px;">
            <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#nuevaMarca">
              <span class="glyphicon glyphicon-plus"></span> Marca
            </button>
          </div>
        </div>
        <div class="form-group">
          <input type="text" name="descripcion" class="form-control input-sm mayuscula" placeholder="DESCRIPCIÓN"
            id="descripcion">
        </div>
        <div class="form-group">
          <input type="date" name="vencimiento" class="form-control input-sm" placeholder="VENCIMIENTO"
            id="vencimiento">
        </div>
        <div class="form-group">
          <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6" style="padding:0px; margin-bottom:15px;">
            <input type="text" name="precio" class="form-control input-sm moneda" placeholder="PRECIO DE VENTA" id="precio">
          </div>
          <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6" style="padding-right:0px; margin-bottom:15px;">
            <input type="file" name="foto" class="form-control input-sm" id="foto">
          </div>
        </div>
        <div class="form-group">
          <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6" style="padding:0px; margin-bottom:15px;">
            <input type="text" name="cantidad" class="form-control input-sm numero" placeholder="CANTIDAD" id="cantidad">
            <div class="modal fade" id="errorCantidad" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
              <div class="modal-dialog" role="document">
                <div class="modal-content">
                  <div class="modal-header" style="background-color:#bb0000; color:#fff;">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">HUBO UN ERROR</h4>
                  </div>
                  <div class="modal-body" style="background-color:#e69c2d">
                    <div class="panel" style="background-color:#bd7406">
                      <div class="panel-body" id="mensaje">

                      </div>
                    </div>
                  </div>
                  <div class="modal-footer" style="background-color:#bb0000">
                    <button type="button" class="btn btn-default" data-dismiss="modal">
                      <span class="glyphicon glyphicon-ban-circle"></span> Cancelar</button>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6" style="padding-right:0px; margin-bottom:15px;">
            <input type="text" name="costo" class="form-control input-sm moneda" placeholder="COSTO UNITARIO" id="costoUnitario">
          </div>
        </div>
        <div class="form-group">
          <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6" style="padding:0px; margin-bottom:15px;">
            <input type="text" name="total" class="form-control input-sm moneda" placeholder="COSTO TOTAL" readonly id="costoTotal">
          </div><div class="col-xs-6 col-sm-6 col-md-6 col-lg-6" style="padding:0px; margin-bottom:15px; height: 30px;">
          </div>
        </div>
        <div class="form-group">
          <button type="submit" class="btn btn-sm btn-primary" id="btnAgregarDetalle">Agregar Detalle</button>
        </div>
      </div>
      <input type="hidden" value="2" name="tipo">
    {{Form::close()}}
  </div>
  <div class="modal fade" id="nuevaLinea" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header" style="background-color:#385a94; color:#fff;">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="myModalLabel">NUEVA LÍNEA</h4>
        </div>
        <div class="modal-body" style="background-color:#e69c2d">
          <div class="panel" style="background-color:#bd7406">
            <div class="panel-body">
              <div class="form-group">
                <input type="text" class="form-control mayuscula input-sm" placeholder="NOMBRE*" required name="nombreLinea" id="nombreLinea">
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer" style="background-color:#385a94">
          <button type="button" class="btn btn-default" data-dismiss="modal">
            <span class="glyphicon glyphicon-ban-circle"></span> Cancelar</button>
          <button type="button" class="btn btn-primary" id="btnLinea">
            <span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="nuevaFamilia" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header" style="background-color:#385a94; color:#fff;">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="myModalLabel">NUEVA FAMILIA</h4>
        </div>
        <div class="modal-body" style="background-color:#e69c2d">
          <div class="panel" style="background-color:#bd7406">
            <div class="panel-body">
              <div class="form-group">
                <input type="text" class="form-control mayuscula input-sm" placeholder="NOMBRE*" required name="nombre"
                  id="nombreFamilia">
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer" style="background-color:#385a94">
          <button type="button" class="btn btn-default" data-dismiss="modal">
            <span class="glyphicon glyphicon-ban-circle"></span> Cancelar</button>
          <button type="button" class="btn btn-primary" id="btnFamilia">
            <span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="nuevaMarca" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header" style="background-color:#385a94; color:#fff;">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="myModalLabel">NUEVA MARCA</h4>
        </div>
        <div class="modal-body" style="background-color:#e69c2d">
          <div class="panel" style="background-color:#bd7406">
            <div class="panel-body">
              <div class="form-group">
                <input type="text" class="form-control mayuscula input-sm" placeholder="NOMBRE*" required name="nombre"
                id="nombreMarca">
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer" style="background-color:#385a94">
          <button type="button" class="btn btn-default" data-dismiss="modal">
            <span class="glyphicon glyphicon-ban-circle"></span> Cancelar</button>
          <button type="button" class="btn btn-primary" id="btnMarca">
            <span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
        </div>
      </div>
    </div>
  </div>
</div>
