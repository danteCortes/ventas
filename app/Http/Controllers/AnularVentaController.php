<?php

namespace App\Http\Controllers;

use App\Cierre;
use App\Detalle;
use App\DetalleNotaCredito;
use App\NotaCredito;
use App\ProductoTienda;
use App\Tienda;
use App\Venta;
use Carbon\Carbon;
use App\Jobs\SendNotaCreditoSunat;
use Illuminate\Http\Request;

class AnularVentaController extends Controller
{
    public function mdlAnularVenta(Request $request)
    {
        $venta = Venta::find($request->id);
        if($venta->codigo_sunat == null || $venta->codigo_sunat != 0){
            return response()->json(['Esta venta no fue enviado a SUNAT.'], 422);
        }
        if(NotaCredito::where('numero_documento_afectado', $venta->recibo->numeracion)->first()){
            return response()->json(['Esta venta ya tiene una nota de crédito asociada.'], 422);
        }
        
        return Venta::join('recibos as r', 'r.venta_id', '=', 'ventas.id')
            ->select(
                'ventas.id',
                'r.numeracion',
                \DB::raw("case
                    when length(substring_index(r.numeracion, '-', 1)) = 4 then
                        case
                            when substring(r.numeracion, 1, 1) = 'B' then 'NOTA DE CRÉDITO'
                            else case
                                when datediff(curdate(), ventas.updated_at) > 5 then 'NOTA DE CRÉDITO'
                                else 'COMUNICACIÓN DE BAJA'
                            end
                        end
                    else 'ANULACIÓN DE TICKET'
                end as tipo_anulacion")
            )
            ->where('ventas.id', $request->id)
            ->first()
        ;
    }

    public function anularVenta(Request $request)
    {
        $venta = Venta::find($request->id);
        $recibo = $venta->recibo;

        $serie = explode('-', $recibo->numeracion)[0];
        if(strlen($serie) == 4){
            if(substr($serie, 0, 1) == 'B'){
                return $this->generarNotaCredito($venta);
            }else{
                if(Carbon::createFromFormat('Y-m-d H:i:s', $venta->updated_at)->startOfDay()->diffInDays(Carbon::now()->startOfDay()) > 5){
                    return $this->generarNotaCredito($venta);
                }else{
                    return $this->generarComunicacionBaja($venta);
                }
            }
        }else{
            return 'anular ticket';
        }
    }

    private function generarNotaCredito(Venta $venta)
    {
        $cierre = Cierre::where('usuario_id', \Auth::user()->id)->where('estado', 1)->first();
        $recibo = $venta->recibo;
        $tienda = Tienda::find(\Auth::user()->tienda_id);
        $serie = substr($recibo->numeracion, 0, 1).'NC'.substr($tienda->serie, -1);
        $correlativo = 1;
        if($ultima_nota_credito = NotaCredito::where('serie', $serie)->orderBy('correlativo', 'desc')->first()){
            $correlativo = $ultima_nota_credito->correlativo + 1;
        }

        $nota_credito = new NotaCredito;
        $nota_credito->usuario_id = \Auth::user()->id;
        $nota_credito->cierre_id = $cierre->id;
        $nota_credito->tienda_id = \Auth::user()->tienda_id;
        $nota_credito->serie = $serie;
        $nota_credito->correlativo = $correlativo;
        $nota_credito->fecha_emision = Carbon::now();
        $nota_credito->tipo_documento_afectado = substr($recibo->numeracion, 0, 1) == 'B' ? '03' : '01';
        $nota_credito->numero_documento_afectado = $recibo->numeracion;
        $nota_credito->codigo_motivo = '01';
        $nota_credito->descripcion_motivo = 'DEVOLUCIÓN DE PRODUCTOS';
        $nota_credito->tipo_moneda = 'PEN';
        $nota_credito->persona_dni = $recibo->persona_dni;
        $nota_credito->empresa_ruc = $recibo->empresa_ruc;
        $nota_credito->total = $venta->total;
        $nota_credito->save();

        foreach(Detalle::where('venta_id', $venta->id)->get() as $detalle)
        {
            $detalle_nota_credito = new DetalleNotaCredito;
            $detalle_nota_credito->nota_credito_id = $nota_credito->id;
            $detalle_nota_credito->producto_codigo = $detalle->producto_codigo;
            $detalle_nota_credito->cantidad = $detalle->cantidad;
            $detalle_nota_credito->descripcion = $detalle->producto->familia->nombre.' '.$detalle->producto->marca->nombre.
                ' '.$detalle->producto->descripcion;
            $detalle_nota_credito->valor_unitario = $detalle->precio_unidad;
            $detalle_nota_credito->importe_detalle = $detalle->total;
            $detalle_nota_credito->save();

            $this->devolverProducto($detalle);
        }

        SendNotaCreditoSunat::dispatch($nota_credito);

        return $nota_credito;
    }

    private function devolverProducto(Detalle $detalle)
    {
        $producto_tienda = ProductoTienda::where('producto_codigo', $detalle->producto_codigo)
            ->where('tienda_id', $detalle->venta->tienda_id)
            ->first()
        ;
        $producto_tienda->cantidad += $detalle->cantidad;
        $producto_tienda->save();
    }

    private function generarComunicacionBaja(Venta $venta)
    {
        return 'generar comunicacion baja';
    }
}
