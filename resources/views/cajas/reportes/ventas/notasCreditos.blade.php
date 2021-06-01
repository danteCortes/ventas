<?php

  $notas_creditos = \App\NotaCredito::where('cierre_id', $cierre->id)->get();

?>
@if(count($notas_creditos) > 0)
<hr style="margin-bottom: 1px; margin-top: 0px;">
<table class="table table-condensed" id="tblResumenNotasCredito" style="margin-bottom:0px;">
    <tr>
        <th colspan="3" style="text-align:center; border-top:rgba(255, 255, 255, 0);">
            <p class="text-center" style="font-size: 12px; margin-bottom:1px;">RESUMEN DE NOTAS DE CRÉDITO</p>
        </th>
    </tr>
    @foreach($notas_creditos as $nota_credito)
        <tr>
            <td colspan="3" style="text-align:left;">
                <p class="text-center" style="font-size: 12px; margin-bottom:1px;">
                    Nota de Crédito Nro {{$nota_credito->serie}}-{{$nota_credito->correlativo}} Anula 
                    {{$nota_credito->numero_documento_afectado}}
                </p>
            </td>
        </tr>
        @foreach(\App\DetalleNotaCredito::where('nota_credito_id', $nota_credito->id)->get() as $detalle)
            <tr>
                <td style="border-top:rgba(130, 130, 130, 0.5); padding-top:1px; padding-bottom:0px;">
                <p class="text-left" style="font-size: 12px; margin-bottom:1px;">{{$detalle->cantidad}}</p></td>
                <td style="border-top:rgba(130, 130, 130, 0.5); padding-top:1px; padding-bottom:0px;">
                <p class="text-left" style="font-size: 12px; margin-bottom:1px;">{{$detalle->descripcion}}</p></td>
                <td style="border-top:rgba(130, 130, 130, 0.5); padding-top:1px; padding-bottom:0px;">
                <p class="text-right" style="font-size: 12px; margin-bottom:1px;">{{number_format($detalle->importe_detalle, 2, '.', ' ')}}</p></td>
            </tr>
        @endforeach
        <tr>
            <td colspan="2" style="border-top:rgba(130, 130, 130, 0.5); padding-top:1px; padding-bottom:0px;">
                <p class="text-right" style="font-size: 12px; margin-bottom:1px;">Total</p></td>
            <td style="border-top:rgba(130, 130, 130, 0.5); padding-top:1px; padding-bottom:0px;">
                <p class="text-right" style="font-size: 12px; margin-bottom:1px;">{{number_format($nota_credito->total, 2, '.', ' ')}}</p></td>
        </tr>
    @endforeach
</table>
@endif
