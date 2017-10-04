@extends('plantillas.cajero')

@section('titulo')
Traslados
@stop

@section('contenido')
@include('plantillas.mensajes')
<div class="row">
    <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
	  <div class="panel panel-default">
	    <div class="panel-heading" style="background-color:#575757; color: #FFF;">
	      <h3 class="panel-title">Buscar Producto
	        <button type="button" class="btn btn-xs btn-default pull-right" data-toggle="collapse" data-target="#panelBuscar"
	           aria-controls="panelBuscar" id="btnbuscar">
	          <span class="fa fa-minus"></span>
	        </button>
	      </h3>
	    </div>
	    <div class="panel-body collapse in" id="panelBuscar" style="background-color:#bfbfbf;">
	        <div class="input-group">
	          <span class="input-group-addon">Código <span class="fa fa-barcode"></span></span>
	          <input type="text" class="form-control" placeholder="CÓDIGO" id="txtCodigo">
	          <span class="input-group-btn">
	            <input type="hidden" name="tienda_id" value="{{Auth::user()->tienda_id}}" id="tienda_id">
	            <button class="btn btn-default" type="button"><span class="fa fa-search"></span></button>
	          </span>
	        </div>
	    </div>
	  </div>
	</div>
<!-- agregar detalle -->
	<div class="col-xs-12 col-sm-12 col-md-8 col-lg-8">
	  <div class="panel panel-default">
	    <div class="panel-heading" style="background-color:#575757; color: #FFF;">
	      <h3 class="panel-title">Agregar Producto
	        <button type="button" class="btn btn-xs btn-default pull-right" data-toggle="collapse" data-target="#panelAgregar"
	           aria-controls="panelAgregar" id="btnAgregar">
	          <span class="fa fa-minus"></span>
	        </button>
	      </h3>
	    </div>
	    <div class="panel-body collapse in" id="panelAgregar" style="background-color:#bfbfbf;">
	      <div class="row">
	        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
	          <div class="table-resposive">
	            <table class="table table-bordered table-condensed">
	              <thead>
	                <tr>
	                  <th>Código</th>
	                  <th>Descripción</th>
	                  <th style="width:80px;">P. Venta</th>
	                  <th>Stock</th>
	                </tr>
	              </thead>
	              <tbody>
	                <tr>
	                  <td class="codigo"></td>
	                  <td class="descripcion"></td>
	                  <td class="precio"></td>
	                  <td class="cantidad"></td>
	                </tr>
	              </tbody>
	            </table>
	          </div>
	        </div>
	      </div>
	      <div class="row">
	        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 foto">
	          <img src="{{url('storage/productos').'/producto.png'}}" style="width:100px;">
	        </div>
	        {{Form::open(['url'=>'traslado'])}}
	          {{ csrf_field() }}
	          {{Form::hidden('producto_codigo', null, ['id'=>'producto_codigo', 'required'=>''])}}
	          {{Form::hidden('stock', null, ['id'=>'stock', 'required'=>''])}}
	          {{Form::hidden('tipo', 3)}}
	          <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
	          	<br>
	            <div class="input-group">
	              <span class="input-group-addon">Cantidad: </span>
	              <input type="number" class="form-control" placeholder="CANTIDAD" name="cantidad" required>
	              <span class="input-group-btn">
	                <button class="btn btn-default" type="submit" id="guardar" disabled="true"><span class="fa fa-cab"></span> Trasladar</button>
	              </span>
	            </div>
	          </div>
	        {{Form::close()}}
	      </div>
	    </div>
	  </div>
	</div>
<!-- Fin agregar detalle -->
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
	{{Html::script('assets/lib/mask/jquery.mask.js')}}
	@include('traslados.scripts')
@stop
