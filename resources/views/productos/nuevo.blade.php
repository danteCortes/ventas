<!--Boton para mostrar el modal con el formulario para ingresar los datos de un nuevo producto-->
<!--Fecha 13/09/2017-->
<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#nuevo">
  <span class="glyphicon glyphicon-plus"></span> Nuevo
</button>
<!--Modal con el formulario para ingresar los datos del nuevo producto-->
<!--Fecha 13/09/2017-->
<div class="modal fade" id="nuevo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      {{Form::open(['url'=>'producto', 'enctype'=>'multipart/form-data', 'id'=>'frmNuevoProducto'])}}
      {{ csrf_field() }}
      <div class="modal-header" style="background-color:#385a94; color:#fff;">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">NUEVO PRODUCTO</h4>
      </div>
      <div class="modal-body" style="background-color:#e69c2d">
        <div class="panel" style="background-color:#bd7406">
          <div class="panel-body">
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
                <input type="text" class="form-control mayuscula input-sm" placeholder="CÓDIGO*" required name="codigo" id="codigo">
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
              <input type="text" class="form-control mayuscula input-sm" placeholder="DESCRIPCIÓN" name="descripcion" id="descripcion"
                required>
            </div>
            <div class="form-group">
              <input type="date" class="form-control input-sm" placeholder="FECHA DE VENCIMIENTO" name="vencimiento" id="vencimiento">
            </div>
            <div class="form-group">
              <input type="text" class="form-control moneda input-sm" placeholder="PRECIO" name="precio" id="precio">
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
        <button type="submit" class="btn btn-primary"><span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
      </div>
      {{Form::close()}}
    </div>
  </div>
</div>
