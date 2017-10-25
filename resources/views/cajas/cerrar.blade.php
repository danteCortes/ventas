@extends('plantillas.cajero')

@section('contenido')
<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-6 col-md-offset-3 col-lg-6 col-lg-offset-3">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title">Cerrar Caja
        </h3>
      </div>
      <div data-spy="scroll" data-target="#cabecera" data-offset="0" class="panel-body" id="reporteDiario">
        <p id="cabecera">ESTÁ A PUNTO DE CERRAR CAJA CON:</p>
        @include('cajas.reportes.ventas.efectivo')
        @include('cajas.reportes.ventas.resumenVentas')
        @include('cajas.reportes.ventas.resumenDescuentos')
        @include('cajas.reportes.ventas.ventasProductos')
        @include('cajas.reportes.ventas.resumenTarjetas')
        @include('cajas.reportes.prestamos.prestamosHechos')
        @include('cajas.reportes.prestamos.prestamosEntrada')
        @include('cajas.reportes.prestamos.prestamosRecogidos')
        @include('cajas.reportes.prestamos.prestamosDevueltos')
        @include('cajas.reportes.creditos.resumenCreditos')
        @include('cajas.reportes.creditos.creditosProductos')
        @include('cajas.reportes.creditos.pagos')
      </div>
      <div class="panel-footer">
        {{Form::open(['url'=>'cierre-caja/'.$cierre->id])}}
          <button type="submit" class="btn btn-default"><span class="glyphicon glyphicon-ban-circle"></span> Cerrar</button>
          <button type="button" class="btn btn-primary imprimir pull-right"><span class="glyphicon glyphicon-print"></span> Imprimir</button>
        {{Form::close()}}
      </div>
    </div>
  </div>
</div>
@stop

@section('scripts')
{{Html::script('assets/js/jquery.printarea.js')}}
<script type="text/javascript">
  $(document).ready(function() {
    $(".imprimir").click(function (){
      $("#reporteDiario").printArea();
    });
  });
</script>
@stop
