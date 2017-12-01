<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
    <div class="table-responsive">
      <table class="table table-bordered">
        <thead>
          <tr>
            <th style="width:130px;">Operaciones</th>
            <th style="width:50px;">Cant.</th>
            <th>Código</th>
            <th>Descripción</th>
            <th>P. unit.</th>
            <th>P. Total</th>
          </tr>
        </thead>
        <tbody id="detalles">
          @if($compra = \App\Compra::where('usuario_id', Auth::user()->id)->where('estado', 1)->first())
            @foreach($compra->detalles as $detalle)
              <tr>
                <td>
                  {{Form::open(['url'=>'detalle/'.$detalle->id, 'method'=>'delete', 'class'=>'pull-left'])}}
                    {{ csrf_field() }}
                    <button class="btn btn-xs btn-danger">Quitar</button>
                  {{Form::close()}}
                  <button type="button" class="btn btn-xs{{(!\App\Ingreso::where('detalle_id', $detalle->id)->first())? " btn-warning":" btn-success"}}" data-toggle="modal" data-target="#tiendas_{{$detalle->id}}" style="margin-left:5px;">
                    Tiendas</button>
                  <div class="modal fade" id="tiendas_{{$detalle->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                    <div class="modal-dialog" role="document">
                      <div class="modal-content">
                        <div class="modal-header" style="background-color:#329a15; color:#fff;">
                          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                          <h4 class="modal-title" id="myModalLabel">AGREGAR PRODUCTOS A TIENDA - TOTAL {{$detalle->cantidad}}</h4>
                        </div>
                        @if(!\App\Ingreso::where('detalle_id', $detalle->id)->first())
                          {{Form::open(['url'=>'producto-tienda'])}}
                          <div class="modal-body" style="background-color:#e69c2d">
                            <div class="panel" style="background-color:#bd7406">
                              <div class="panel-body">
                                <div class="table-responsive">
                                  <table class="table table-condensed table-bordered">
                                    <thead>
                                      <tr>
                                        <th style="text-align:center">Tienda</th>
                                        <th style="width:50px">Cantidad</th>
                                        <th>Ubicación</th>
                                      </tr>
                                    </thead>
                                        <tbody>
                                          @foreach(\App\Tienda::all() as $tienda)
                                            <tr>
                                              <th style="text-align:right; padding-right:15px; padding-top:10px;">{{$tienda->nombre}}</th>
                                              <td>
                                                <input type="text" name="cantidades[{{$tienda->id}}]" class="form-control input-sm"
                                                  style="width:50px" required>
                                              </td>
                                              <td>
                                                <input type="text" name="ubicaciones[{{$tienda->id}}]" class="form-control input-sm mayuscula">
                                              </td>
                                            </tr>
                                          @endforeach
                                        </tbody>
                                  </table>
                                </div>
                              </div>
                            </div>
                          </div>
                          <div class="modal-footer" style="background-color:#329a15">
                            <button type="button" class="btn btn-default" data-dismiss="modal">
                              <span class="glyphicon glyphicon-ban-circle"></span> Cancelar</button>
                            <button type="submit" class="btn btn-primary" id="btnAgregarTiendas">
                              <span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
                          </div>
                          <input type="hidden" name="detalle_id" value="{{$detalle->id}}">
                          {{Form::close()}}
                        @else
                          {{Form::open(['url'=>'producto-tienda'])}}
                          <div class="modal-body" style="background-color:#e69c2d">
                            <div class="panel" style="background-color:#bd7406">
                              <div class="panel-body">
                                <div class="table-responsive">
                                  <table class="table table-condensed table-bordered">
                                    <thead>
                                      <tr>
                                        <th style="text-align:center">Tienda</th>
                                        <th style="width:50px">Cantidad</th>
                                        <th>Ubicación</th>
                                      </tr>
                                    </thead>
                                        <tbody>
                                          @foreach(\App\Tienda::all() as $tienda)
                                            <tr>
                                              <th style="text-align:right; padding-right:15px; padding-top:10px;">{{$tienda->nombre}}</th>
                                              <td>
                                                <input type="text" name="cantidades[{{$tienda->id}}]" class="form-control input-sm"
                                                  style="width:50px" required value="{{\App\Ingreso::where('detalle_id', $detalle->id)->where('producto_tienda_id', \App\ProductoTienda::where('producto_codigo', $detalle->producto_codigo)->where('tienda_id', $tienda->id)->first()->id)->first()->cantidad}}">
                                              </td>
                                              <td>
                                                <input type="text" name="ubicaciones[{{$tienda->id}}]" class="form-control input-sm mayuscula"
                                                  value="{{\App\ProductoTienda::where('producto_codigo', $detalle->producto_codigo)->where('tienda_id', $tienda->id)->first()->ubicacion}}">
                                              </td>
                                            </tr>
                                          @endforeach
                                        </tbody>
                                  </table>
                                </div>
                              </div>
                            </div>
                          </div>
                          <div class="modal-footer" style="background-color:#329a15">
                            <button type="button" class="btn btn-default" data-dismiss="modal">
                              <span class="glyphicon glyphicon-ban-circle"></span> Cancelar</button>
                            <button type="submit" class="btn btn-primary" id="btnAgregarTiendas">
                              <span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
                          </div>
                          <input type="hidden" name="detalle_id" value="{{$detalle->id}}">
                          {{Form::close()}}
                        @endif
                      </div>
                    </div>
                  </div>
                </td>
                <td>{{$detalle->cantidad}}</td>
                <td>{{$detalle->producto->codigo}}</td>
                <td>{{$detalle->producto->familia->nombre}} {{$detalle->producto['descripcion']}}</td>
                <td style="text-align:right;">{{$detalle->precio_unidad}}</td>
                <td style="text-align:right;">{{$detalle->total}}</td>
              </tr>
            @endforeach
            <tr>
              <th colspan="5" style="text-align: right;">Total</th>
              <td style="text-align: right;">{{$compra->total}}</td>
            </tr>
          @endif
        </tbody>
      </table>
    </div>
  </div>
</div>
