@extends('plantillas.cajero')

@section('estilos')
{{Html::style('bootgrid/jquery.bootgrid.min.css')}}
@stop

@section('titulo')
Nuevo Traslado
@include('traslados.menu')
@stop

@section('contenido')
@include('plantillas.mensajes')
<div class="row">
  @include('traslados.tblProductos')
	@include('traslados.nuevo.frmAgregarDetalle')
</div>
@if($counttraslados > 0 )
<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
    <div class="table-responsive">
      <table class="table table-condensed table-bordered" style="background-color:#bfbfbf;">
        <thead>
          <tr>
            <th>Operación</th>
            <th>Cantidad</th>
            <th>Cod. Producto</th>
            <th>Descripción</th>
          </tr>
        </thead>
        <tbody id="detalles">
        	@foreach($traslados->detalle AS $detall)
        	<tr>
        		<td class="text-center">
        			{{Form::open(['url'=>'traslado/'.$detall->id, 'method'=>'delete', 'class'=>'pull-left'])}}
                    {{ csrf_field() }}
                    	{{Form::hidden('cantidad', $detall->cantidad, ['id'=>'cantidad'])}}
                    	{{Form::hidden('producto_codigo', $detall->producto_codigo, ['id'=>'producto_codigo'])}}
                    	<button class="btn btn-xs btn-danger del" title="{{$detall->id}}">Quitar</button>
                    {{Form::close()}}
                </td>
                <td>{{$detall->cantidad}}</td>
                <td>{{$detall->producto_codigo}}</td>
                <td>{{$detall->producto->descripcion}}</td>
        	</tr>
        	@endforeach
          <!-- <tr>
            <td colspan="4"><strong class="pull-right">CANTIDAD TOTAL DE PRODUCTOS TRASLADADOS: </strong></td>
          </tr> -->
        </tbody>
      </table>
    </div>
  </div>
</div>
<div class="row">
  {{ Form::open(['url'=>'traslado/terminar', 'method'=>'POST']) }}
  {{ csrf_field() }}
	  {{Form::hidden('id_traslado', $traslados->id, ['id'=>'id_traslado'])}}
	  <div class="col-sm-4">
	  	<select name="tienda_traslado" id="tienda_traslado" class="form-control" required>
	  		<option value>-- Seleccione una Tienda --</option>
	  		@foreach($tiendas AS $tienda)
	  			<option value="{{$tienda->id}}">{{$tienda->nombre}}</option>
	  		@endforeach
	  	</select>
	  </div>
	  <div class="col-sm-8">
	    <button type="submit" class="btn btn-primary"><span class="fa fa-check-square-o"> </span> Terminar</button>
	    <button type="button" class="btn btn-danger pull-right"><span class="fa fa-times"> </span> Cancelar</button>
	  </div>
  {{Form::close()}}
</div>
@endif
@stop

@section('scripts')
  {{Html::script('bootgrid/jquery.bootgrid.min.js')}}
	{{Html::script('assets/lib/mask/jquery.mask.js')}}
	@include('traslados.nuevo.scripts')
@stop
