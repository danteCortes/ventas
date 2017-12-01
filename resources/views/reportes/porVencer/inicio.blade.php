@extends('plantillas.administrador')

@section('estilos')
{{Html::style('bootgrid/jquery.bootgrid.min.css')}}
@stop

@section('titulo')
Productos por Vencer
@stop

@section('contenido')
  @include('plantillas.mensajes')
  @if(count($productos))
    <div class="row">
      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <div class="table-responsive" id="imprimirPorVencer">
          <table class="table table-bordered table-condensed">
            <thead>
              <tr>
                <th>Código</th>
                <th>Descripción</th>
                <th>Vencimiento</th>
                <th>Cantidad</th>
                <th>V. Unit.</th>
                <th>V. Total</th>
              </tr>
            </thead>
            <tbody>
              @foreach($productos as $producto)
                <tr>
                  <td><p style="font-size: 12px">{{$producto->codigo}}</p></td>
                  <td><p style="font-size: 12px">{{$producto->familia->nombre}} {{$producto->marca->nombre}} {{$producto->descripcion}}</p></td>
                  <td><p style="font-size: 12px">{{$producto->vencimiento}}</p></td>
                  <td>
                    @foreach(\App\Tienda::all() as $tienda)
                      <p style="font-size: 12px; margin-bottom: 0px;">{{$tienda->nombre}} = {{\App\ProductoTienda::where('producto_codigo', $producto->codigo)
                        ->where('tienda_id', $tienda->id)->first()->cantidad}}</p>
                    @endforeach
                  </td>
                  <td style="text-align:right;"><p style="font-size: 12px; margin-bottom: 0px;">{{number_format($producto->precio, 2, '.', ' ')}}</p></td>
                  <td style="text-align:right; width:70px;">
                    <?php $total = 0; ?>
                    @foreach(\App\Tienda::all() as $tienda)
                    <?php $total += \App\ProductoTienda::where('producto_codigo', $producto->codigo)
                      ->where('tienda_id', $tienda->id)->first()->cantidad * $producto->precio; ?>
                    @endforeach
                    <p style="font-size: 12px; margin-bottom: 0px;">{{number_format($total, 2, '.', ' ')}}</p>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        <button type="button" class="btn btn-primary" id="imprimir-por-vencer"><span class="fa fa-print"></span> Imprimir</button>
        <a href="{{url('reporte')}}" class="btn btn-default"> Salir</a>
      </div>
    </div>
  @endif
@stop

@section('scripts')
  {{Html::script('assets/js/jquery.printarea.js')}}
  <script type="text/javascript">
    $(document).ready(function() {
      $("#imprimir-por-vencer").click(function (){
        $("#imprimirPorVencer").printArea();
      });
    });
  </script>
@stop
