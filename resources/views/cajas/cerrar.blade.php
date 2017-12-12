@extends('plantillas.cajero')

@section('contenido')
<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-4 col-md-offset-4 col-lg-4 col-lg-offset-4">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title">Cerrar Caja
        </h3>
      </div>
      <div data-spy="scroll" data-target="#cabecera" data-offset="0" class="panel-body" id="reporteDiario">
        <p class="text-center" style="font-size: 12px; margin-bottom:1px;" id="cabecera">
          CIERRE DE CAJA {{\Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $cierre->created_at)->format('d/m/Y')}}:
        </p>
        <hr style="margin-bottom: 1px; margin-top: 1px;">
        @include('cajas.reportes.ventas.efectivo')
        @include('cajas.reportes.ventas.resumenVentas')
        @include('cajas.reportes.ventas.resumenDescuentos')
        @include('cajas.reportes.ventas.ventasProductos')
        @include('cajas.reportes.ventas.resumenTarjetas')
        @include('cajas.reportes.cambios.resumenCambios')
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
          <button type="submit" class="btn btn-success"><span class="glyphicon glyphicon-save"></span> Cerrar Caja</button>
          <a href="{{url('cajero')}}" class="btn btn-default"><span class="glyphicon glyphicon-ban-circle"></span> Cancelar</a>
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
