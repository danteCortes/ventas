<hr>
<table class="table table-condensed" id="tblResumenVentas">
  <tr>
    <th colspan="3" style="text-align:center; border-top:rgba(255, 255, 255, 0);">RESUMEN DE VENTAS</th>
  </tr>
  @foreach($cierre->ventas as $venta)
    <tr>
      <th colspan="3" style="text-align:left;">Venta Nro {{$venta->recibo->numeracion}}</th>
    </tr>
      @foreach($venta->detalles as $detalle)
        <tr>
          <th style="text-align:left; border-top:rgba(255, 255, 255, 0); width:20px;">{{$detalle->cantidad}}</th>
          <th style="text-align:left; border-top:rgba(255, 255, 255, 0);">{{$detalle->producto->descripcion}}</th>
          <th style="text-align:right; border-top:rgba(255, 255, 255, 0);">{{$detalle->total}}</th>
        </tr>
      @endforeach
      <tr>
        <th colspan="2" style="text-align:right; border-top:rgba(255, 255, 255, 0);">Total</th>
        <th style="text-align:right; border-top:rgba(255, 255, 255, 0);">{{$venta->total}}</th>
      </tr>
  @endforeach
</table>
