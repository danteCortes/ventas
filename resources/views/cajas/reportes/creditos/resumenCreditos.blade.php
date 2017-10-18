@if(count($cierre->creditos) > 0)
<hr>
<table class="table table-condensed" id="tblResumenVentas">
  <tr>
    <th colspan="3" style="text-align:center; border-top:rgba(255, 255, 255, 0);">RESUMEN DE CREDITOS</th>
  </tr>
  @foreach($cierre->creditos as $credito)
    <tr>
      <th colspan="3" style="text-align:left;">Credito Nro {{$credito->id}}</th>
    </tr>
      @foreach($credito->detalles as $detalle)
        <tr>
          <th style="text-align:left; border-top:rgba(255, 255, 255, 0); width:20px;">{{$detalle->cantidad}}</th>
          <th style="text-align:left; border-top:rgba(255, 255, 255, 0);">{{$detalle->producto->descripcion}}</th>
          <th style="text-align:right; border-top:rgba(255, 255, 255, 0);">{{$detalle->total}}</th>
        </tr>
      @endforeach
      <tr>
        <th colspan="2" style="text-align:right; border-top:rgba(255, 255, 255, 0);">Total</th>
        <th style="text-align:right; border-top:rgba(255, 255, 255, 0);">{{$credito->total}}</th>
      </tr>
  @endforeach
</table>
@endif
