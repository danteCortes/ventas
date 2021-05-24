<!DOCTYPE html>
<html lang='es'>
    <head>
        <title>
            <span>{{'Ticket N° '.$recibo->numeracion}}</span>
        </title>
        <style>
            #invoice-POS {
                box-shadow: 0 0 1in -0.25in rgba(0, 0, 0, 0.5);
                font-family: Arial, Helvetica, sans-serif;
            }
            #invoice-POS h1, h2, p {
                margin: 0 !important;
            }
            #invoice-POS h1 {
                font-size: 9px;
            }
            #invoice-POS h2 {
                font-size: 9px;
            }
        
            #invoice-POS #top {
                min-height: 100px;
            }

            #invoice-POS #top .logo {
                max-height: 150px !important;
                margin-bottom: 2px;
            }

            .qr{
                width: 2.5cm;
                height: 2.5cm;
            }
            
            .font-size-8{
                font-size: 8px;
            }
            
            .font-size-9{
                font-size: 9px;
            }
            
            .font-size-10{
                font-size: 10px;
            }

            .font-size-11{
                font-size: 11px;
            }        

            .mt-10{
                margin-top: 10px;
            }
            .mtb-10{
                margin: 10px 0px;
            }
            .text-center{
                text-align: center;
            }
            .text-right{
                text-align: right;
            }
            .text-bold{
                font-weight: bold;
            }

            @page { margin: 0px; }

            body { margin: 0px; }
        </style>
        <style>
            #invoice-POS {
                padding: 0mm;
                width: 72.1mm;
            }
        
            @media print{
                body {
                    margin: 0px;
                }
            }
        </style>
    </head>
    
    <body>
        <div id="invoice-POS">

            <div id="top" class="text-center">
                <?php
                    $logo = 'logo.png';
                    if($recibo->venta->tienda->logo){
                        $logo = 'storage/logos/'.$recibo->venta->tienda->logo;
                    }
                ?>
                <img class="logo" src="./{{$logo}}" alt="Logo"
                    style="max-width: 50%; height: 80px;">
                <h1>{{$recibo->venta->tienda->nombre}}</h1>
                <p class="font-size-9">{{$recibo->venta->tienda->direccion}}</p>                
            </div>

            <hr>

            <div id="datos-empresa" class="text-center font-size-9">
                <p> RAZÓN SOCIAL: TIENDAS TU R&L E.I.R.L.</p>
                <p>JR. GENERAL PRADO NRO. 584 HUANUCO - HUANUCO - HUANUCO</p>   
                <p>RUC: 20601867835</p>
                @if($recibo->venta->tienda->telefono)
                    <p>TELF: {{$recibo->venta->tienda->telefono}}</p>
                @endif
            </div>

            <div id="comprobante" class="mtb-10 text-center font-bold">
                <h2>TICKET</h2>
                <h2>N° {{$recibo->numeracion}}</h2>
            </div>

            <div id="datos-cliente" class="mtb-10 font-size-8">                
                <table style="width: 100%;">
                    <tr>
                        <td style="width: 30%;"><strong>Fecha Emisión:</strong></td>
                        <td>{{\Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $recibo->venta->updated_at)->format('d/m/Y')}}</td>
                    </tr>
                    <?php
                        if($empresa = $recibo->empresa){

                            $cliente_denominacion = $empresa->nombre;
                            $tipo_documento = 'RUC';
                            $documento = $empresa->ruc;
                            $direccion = $empresa->direccion;
                        }elseif($persona = $recibo->persona){
                            $cliente_denominacion = $persona->nombres.' '.$persona->apellidos;
                            $tipo_documento = 'DNI';
                            $documento = $persona->dni;
                            $direccion = $persona->direccion;
                        }else{
                            $cliente_denominacion = 'CLIENTE VARIOS';
                            $tipo_documento = 'DNI';
                            $documento = '00000000';
                            $direccion = '-';
                        }
                    ?>
                    <tr>
                        <td><strong>Cliente:</strong></td>
                        <td>{{$cliente_denominacion}}</td>
                    </tr>
                    <tr>
                        <td>
                            <strong>{{$tipo_documento}}:</strong>
                        </td>
                        <td>{{$documento}}</td>
                    </tr>
                    <tr>
                        <td ><strong>Dirección:</strong></td>
                        <td>{{$direccion}}</td>
                    </tr>
                    <tr>
                        <td><strong>Vendedor:</strong></td>
                        <td>{{$recibo->venta->usuario->persona->nombres}} {{$recibo->venta->usuario->persona->apellidos}}</td>
                    </tr>
                </table>             
            </div>

            <hr>

            <div id="listado-productos" class="mtb-10 font-size-9">
                <table id="productos" style="width: 100%;">
                    <tr class="text-bold">
                        <td width="50%">Descripción</td>
                        <td class="text-right">Cant.</td>
                        <td class="text-right">P.Unt.</td>
                        <td class="text-right">P.Total</td>
                    </tr>
                    @foreach($recibo->venta->detalles as $item)
                        <tr>
                            <td>
                                {{$item->producto->familia->nombre}} 
                                {{$item->producto->marca->nombre}} 
                                {{$item->producto->descripcion}}
                            </td>
                            <td class="text-right">{{$item->cantidad}}</td>
                            <td class="text-right">
                                {{number_format($item->precio_unidad, 2, '.', ' ')}}
                            </td>
                            <td class="text-right">
                                {{number_format($item->total, 2, '.', ' ')}}
                            </td>
                        </tr>
                    @endforeach
                </table>

                <?php $vuelto = $recibo->venta->total; ?>
                <div id="cobro" class="mtb-10">
                    <table style="width: 100%;">
                        @if($reclamo = $recibo->venta->reclamo)
                            <tr>
                                <td style="width: 75%">DESCUENTOS:</td>
                                <td class="text-right">
                                    S/ {{number_format($recibo->venta->descuento, 2, '.', ' ')}}
                                </td>
                            </tr>
                            <?php $vuelto = $recibo->venta->total - $recibo->venta->descuento; ?>
                        @endif
                        <tr class="text-bold">
                            <td style="width: 75%">TOTAL:</td>
                            <td class="text-right">S/ {{number_format($vuelto, 2, '.', ' ')}}</td>
                        </tr>
                    </table>
                    
                    <table class="mt-10" style="width: 100%;">
                        <tr>
                            <td><span class="text-bold">SON:</span> {{$letras}}</td>
                        </tr>
                        @if($recibo->venta->efectivo)
                            <tr>
                                <td>
                                    <span class="text-bold">EFECTIVO:</span> 
                                    S/ {{number_format($recibo->venta->efectivo->monto, 2, '.', ' ')}}
                                </td>
                            </tr>
                            <?php $vuelto = $recibo->venta->efectivo->monto - $vuelto; ?>
                        @endif
                        @if($recibo->venta->tarjetaVenta)
                            <tr>
                                <td>
                                    <span class="text-bold">TARJETA:</span> 
                                    S/ {{number_format($recibo->venta->tarjetaVenta->monto, 2, '.', ' ')}}
                                </td>
                            </tr>
                            <?php $vuelto = $recibo->venta->tarjetaVenta->monto - $vuelto; ?>
                        @endif
                        <tr>
                            <td>
                                <span class="text-bold">VUELTO:</span> 
                                S/ {{number_format($vuelto, 2, '.', ' ')}}
                            </td>
                        </tr>
                    </table>
                </div>

                <table class="mt-10" style="width: 100%;">
                    <tr>
                        <td class="text-center" style="width: 60%; vertical-align: middle;">
                            <p>Gracias por su preferencia.</p>
                            <p style="text-align: center; font-size: 9px;">
                                BIENES TRANSFERIDOS EN LA AMAZONIA PARA SER CONSUMIDOS EN LA MISMA
                            </p>                          
                            <div class="mtb-10">
                                <i>powered by</i><strong> ROCOTECH</strong>
                            </div>
                        </td>
                    </tr>
                </table>
                         
            </div>
        </div>
    </body>
</html>