@extends('plantillas.administrador')

@section('estilos')
{{Html::style('bootgrid/jquery.bootgrid.min.css')}}
@stop

@section('titulo')
Productos Vencidos
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
                  <td>{{$producto->codigo}}</td>
                  <td>{{$producto->descripcion}}</td>
                  <td>{{$producto->vencimiento}}</td>
                  <td>{{$producto->cantidad}}</td>
                  <td>{{$producto->precio}}</td>
                  <td>{{$producto->precio*$producto->cantidad}}</td>
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
