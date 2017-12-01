<!--Modal que muestra algunos errores del sistema.-->
<!--Fecha 13/09/2017-->
<div class="modal fade" id="errores" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
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
<!--Modal con formulario para ingresar una nueva línea de producto-->
<!--Fecha 13/09/2017-->
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
<!--Modal con formulario para ingresar una nueva familia de producto-->
<!--Fecha 13/09/2017-->
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
<!--Modal con formulario para ingresar una nueva marca para los productos-->
<!--Fecha 13/09/2017-->
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
<!--Modal con los datos del producto-->
<!--Fecha 14/09/2017-->
<div class="modal fade" id="ver" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header" style="background-color:#31b0d5; color:#fff;">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">DATOS DEL PRODUCTO</h4>
      </div>
      <div class="modal-body" style="background-color:#e69c2d">
        <div class="img-resposive imgMostrarProducto" style="text-align:center">
        </div>
        <div class="table-responsive" style="background-color:#bd7406">
          <table class="table table-condensed table-bordered table-hover" style="margin:0px; background-color:#bfbfbf;">
            <tr>
              <th>Código: </th>
              <td class="codigo"></td>
            </tr>
            <tr>
              <th>Línea: </th>
              <td class="linea"></td>
            </tr>
            <tr>
              <th>Familia: </th>
              <td class="familia"></td>
            </tr>
            <tr>
              <th>Marca: </th>
              <td class="marca"></td>
            </tr>
            <tr>
              <th>Descripción: </th>
              <td class="descripcion"></td>
            </tr>
            <tr>
              <th>Fecha de vencimiento: </th>
              <td class="vencimiento"></td>
            </tr>
            <tr>
              <th>Precio: </th>
              <td class="precio"></td>
            </tr>
            <tr>
              <th>Stock: </th>
              <td>
                <table class="table table-condensed table-bordered stock" style="background-color:#385a94; color:#fff;">

                </table>
              </td>
            </tr>
          </table>
        </div>
      </div>
      <div class="modal-footer" style="background-color:#31b0d5">
        <button type="button" class="btn btn-default" data-dismiss="modal">
          <span class="glyphicon glyphicon-remove"></span> Cerrar</button>
      </div>
    </div>
  </div>
</div>
<!--Modal con el formulario para modificar los datos del producto-->
<!--Fecha 14/09/2017-->
<div class="modal fade" id="editar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      {{Form::open(['id'=>'frmEditar', 'method'=>'put', 'enctype'=>'multipart/form-data'])}}
      {{ csrf_field() }}
      <div class="modal-header" style="background-color:#385a94; color:#fff;">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">MODIFICAR PRODUCTO</h4>
      </div>
      <div class="modal-body" style="background-color:#e69c2d">
        <div class="panel" style="background-color:#bd7406">
          <div class="panel-body">
            <div class="form-group">
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <div class="img-responsive imgMostrarProducto" style="text-align:center;">
                </div>
              </div>
            </div>
            <div class="form-group">
              <input type="text" class="form-control input-sm mayuscula codigo" name="codigo">
            </div>
            <div class="form-group">
              <select class="form-control input-sm linea" name="linea_id">
              </select>
            </div>
            <div class="form-group">
              <select class="form-control input-sm familia" name="familia_id">
              </select>
            </div>
            <div class="form-group">
              <select class="form-control input-sm marca" name="marca_id">
              </select>
            </div>
            <div class="form-group">
              <input type="text" class="form-control mayuscula input-sm descripcion" placeholder="DESCRIPCIÓN" name="descripcion"
                required>
            </div>
            <div class="form-group">
              <input type="date" class="form-control input-sm vencimiento" placeholder="VENCIMIENTO" name="vencimiento">
            </div>
            <div class="form-group">
              <input type="text" class="form-control moneda input-sm precio" placeholder="PRECIO" name="precio">
            </div>
            <div class="form-group">
              <input type="file" class="form-control mayuscula input-sm" name="foto">
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer" style="background-color:#385a94">
        <button type="button" class="btn btn-default" data-dismiss="modal">
          <span class="glyphicon glyphicon-ban-circle"></span> Cancelar</button>
        <button type="submit" class="btn btn-primary"><span class="glyphicon glyphicon-floppy-disk"></span> Modificar</button>
      </div>
      {{Form::close()}}
    </div>
  </div>
</div>
<!--Modal de advertencia antes de eliminar el producto-->
<!--Fecha 14/09/2017-->
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
<!--Modal de confirmación para imprimir el código de barras-->
<!--Fecha 24/09/2017-->
<div class="modal fade" id="barcode" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      {{Form::open(['id'=>'frmCodebar', 'method'=>'delete'])}}
      {{ csrf_field() }}
      <div class="modal-header" style="background-color:#00bb00; color:#fff;">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">IMPRIMIR CÓDIGO DE BARRAS</h4>
      </div>
      <div class="modal-body" style="background-color:#e69c2d">
        <div class="panel" style="background-color:#bd7406">
          <div class="panel-body">
            <p>ESTA A PUNTO DE IMPRIMIR EL CÓDIGO DE BARRAS PARA <strong class="codigo"></strong>.</p>
            <div class="imgBarcode" id="imgBarcode" style="text-align:center; background-color:#fff; height:25px;">
              imagen del codigo de barras
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
      {{Form::close()}}
    </div>
  </div>
</div>
