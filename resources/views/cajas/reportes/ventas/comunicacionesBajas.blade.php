<?php

  $comunicaciones_bajas = \App\ComunicacionBaja::where('cierre_id', $cierre->id)->get();

?>
@if(count($comunicaciones_bajas) > 0)
<hr style="margin-bottom: 1px; margin-top: 0px;">
<table class="table table-condensed" id="tblResumenComunicacionesBajas" style="margin-bottom:0px;">
    <tr>
        <th colspan="3" style="text-align:center; border-top:rgba(255, 255, 255, 0);">
            <p class="text-center" style="font-size: 12px; margin-bottom:1px;">RESUMEN DE COMUNICACIONES DE BAJA</p>
        </th>
    </tr>
    @foreach($comunicaciones_bajas as $comunicacion_baja)
        <tr>
            <td colspan="3" style="text-align:left;">
                <p class="text-center" style="font-size: 12px; margin-bottom:1px;">
                    Comunicación de Baja Nro RA-{{str_replace('-', '', $comunicacion_baja->fecha_comunicacion)}}-
                    {{$comunicacion_baja->correlativo}}
                </p>
            </td>
        </tr>
        @foreach(\App\DetalleComunicacionBaja::where('comunicacion_baja_id', $comunicacion_baja->id)->get() as $detalle)
            <?php
                $factura = \App\Venta::join('recibos as r', 'r.venta_id', '=', 'ventas.id')
                    ->select('ventas.id', 'r.numeracion', 'ventas.total')
                    ->where('r.numeracion', $detalle->serie.'-'.$detalle->correlativo)
                    ->first()
                ;
                $detalles_factura = \App\Detalle::where('venta_id', $factura->id)->get();
            ?>
            <tr>
                <td colspan="3" style="text-align:left;">
                    <p class="text-center" style="font-size: 12px; margin-bottom:1px;">
                        Anula Factura {{$factura->numeracion}}
                    </p>
                </td>
            </tr>
            @foreach($detalles_factura as $detalle_factura)
                <tr>
                    <td style="border-top:rgba(130, 130, 130, 0.5); padding-top:1px; padding-bottom:0px;">
                    <p class="text-left" style="font-size: 12px; margin-bottom:1px;">{{$detalle_factura->cantidad}}</p></td>
                    <td style="border-top:rgba(130, 130, 130, 0.5); padding-top:1px; padding-bottom:0px;">
                    <p class="text-left" style="font-size: 12px; margin-bottom:1px;">{{$detalle_factura->producto->familia->nombre}}
                        {{$detalle_factura->producto->marca->nombre}} {{$detalle_factura->producto->descripcion}}</p></td>
                    <td style="border-top:rgba(130, 130, 130, 0.5); padding-top:1px; padding-bottom:0px;">
                    <p class="text-right" style="font-size: 12px; margin-bottom:1px;">{{$detalle_factura->total}}</p></td>
                </tr>
            @endforeach
            <tr>
                <td colspan="2" style="border-top:rgba(130, 130, 130, 0.5); padding-top:1px; padding-bottom:0px;">
                    <p class="text-right" style="font-size: 12px; margin-bottom:1px;">Total</p></td>
                <td style="border-top:rgba(130, 130, 130, 0.5); padding-top:1px; padding-bottom:0px;">
                    <p class="text-right" style="font-size: 12px; margin-bottom:1px;">{{$factura->total}}</p></td>
            </tr>
        @endforeach
    @endforeach
</table>
@endif
