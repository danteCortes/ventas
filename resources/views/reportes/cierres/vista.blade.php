@extends('plantillas.administrador')

@section('contenido')
<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-4 col-md-offset-4 col-lg-4 col-lg-offset-4">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title">Cerrar Caja
        </h3>
      </div>
      <?php
        $usuarios = \App\Usuario::join('personas as p', 'p.dni', '=', 'usuarios.persona_dni')
          ->select(
            'usuarios.id',
            \DB::raw("concat(p.nombres, ' ', p.apellidos) as nombres_apellidos")
          )
          ->where('usuarios.tienda_id', $tienda->id)->get();
      ?>
      <div data-spy="scroll" data-target="#cabecera" data-offset="0" class="panel-body" id="reporteDiario">
        @foreach($usuarios as $usuario)
          <p class="text-center" style="font-size: 12px; margin-bottom:1px;" id="cabecera">
            <strong>CIERRE DE CAJA {{\Carbon\Carbon::createFromFormat('Y-m-d', $fecha)->format('d/m/Y')}}</strong>
          </p>
          <p class="text-center" style="font-size: 12px; margin-bottom:1px;" id="cabecera">
            <strong>TIENDA: {{$tienda->nombre}}</strong>
          </p>
          <p class="text-center" style="font-size: 12px; margin-bottom:1px;" id="cabecera">
            <strong>USUARIO: {{$usuario->nombres_apellidos}}</strong>
          </p>
          <hr style="margin-bottom: 1px; margin-top: 1px;">
          @include('cajas.reportes.ventas.efectivo')
          @include('cajas.reportes.ventas.resumenVentas')
          @include('cajas.reportes.cambios.resumenCambios')
          @include('cajas.reportes.ventas.resumenDescuentos')
          @include('cajas.reportes.ventas.notasCreditos')
          @include('cajas.reportes.ventas.comunicacionesBajas')
          @include('cajas.reportes.ventas.ventasProductos')
          @include('cajas.reportes.ventas.resumenTarjetas')
          @include('cajas.reportes.ingresos.resumenIngresos')
          @include('cajas.reportes.gastos.resumenGastos')
          @include('cajas.reportes.prestamos.prestamosHechos')
          @include('cajas.reportes.prestamos.prestamosEntrada')
          @include('cajas.reportes.prestamos.prestamosRecogidos')
          @include('cajas.reportes.prestamos.prestamosDevueltos')
          @include('cajas.reportes.creditos.resumenCreditos')
          @include('cajas.reportes.creditos.creditosProductos')
          @include('cajas.reportes.creditos.pagos')
          <hr>
        @endforeach
      </div>
      <div class="panel-footer">
          <a href="{{url('reporte')}}" class="btn btn-default"><span class="glyphicon glyphicon-ban-circle"></span> Cancelar</a>
          <button type="button" class="btn btn-primary imprimir pull-right"><span class="glyphicon glyphicon-print"></span> Imprimir</button>
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
