<?php

namespace App\Http\Controllers;

use App\Cierre;
use App\ComunicacionBaja;
use App\DetalleComunicacionBaja;
use App\DetalleNotaCredito;
use App\DetalleVentaAdministrador;
use App\Empresa;
use App\NotaCredito;
use App\Persona;
use App\Tienda;
use App\Venta;
use App\VentaAdministrador;
use Carbon\Carbon;
use App\Jobs\SendComunicacionBajaSunat;
use App\Jobs\SendNotaCreditoSunat;
use App\Jobs\SendVentaAdministradorSunat;
use Illuminate\Http\Request;

class EmisionComprobanteController extends Controller
{
    public function inicio()
    {
        return view('emisionComprobantes.inicio');
    }

    public function buscarPersona(Request $request)
    {
        return Persona::find($request->dni);
    }

    public function buscarEmpresa(Request $request)
    {
        return Empresa::find($request->ruc);
    }

    public function guardarComprobante(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'detalles' => 'required|min:1'
        ]);
        if($validator->fails()){
            return response()->json($validator->errors()->all(), 422);
        }

        $persona_dni = null;
        $empresa_ruc = null;
        $tipo_documento = '03';
        $serie = 'BA01';
        if($request->cliente['documento']){
            if(strlen($request->cliente['documento']) == 8){
                $validate = \Validator::make($request->cliente, [
                    'nombres' => 'required',
                    'apellidos' => 'required'
                ]);
                if($validate->fails()){
                    return response()->json($validate->errors()->all(), 422);
                }

                if($persona = Persona::find($request->cliente['documento'])){
                    
                    $persona->nombres = mb_strtoupper($request->cliente['nombres']);
                    $persona->apellidos = mb_strtoupper($request->cliente['apellidos']);
                    $persona->direccion = mb_strtoupper($request->cliente['direccion']);
                    $persona->save();
                }else{
                    $persona = new Persona;
                    $persona->dni = $request->cliente['documento'];
                    $persona->nombres = mb_strtoupper($request->cliente['nombres']);
                    $persona->apellidos = mb_strtoupper($request->cliente['apellidos']);
                    $persona->direccion = mb_strtoupper($request->cliente['direccion']);
                    $persona->save();
                }

                $persona_dni = $request->cliente['documento'];
            }elseif(strlen($request->cliente['documento']) == 11){
                $validate = \Validator::make($request->cliente, [
                    'razon_social' => 'required'
                ]);
                if($validate->fails()){
                    return response()->json($validate->errors()->all(), 422);
                }

                if($empresa = Empresa::find($request->cliente['documento'])){
                    
                    $empresa->nombre = mb_strtoupper($request->cliente['razon_social']);
                    $empresa->direccion = mb_strtoupper($request->cliente['direccion']);
                    $empresa->save();
                }else{
                    $empresa = new Empresa;
                    $empresa->ruc = $request->cliente['documento'];
                    $empresa->nombre = mb_strtoupper($request->cliente['razon_social']);
                    $empresa->direccion = mb_strtoupper($request->cliente['direccion']);
                    $empresa->save();
                }

                $empresa_ruc = $request->cliente['documento'];
                $tipo_documento = '01';
                $serie = 'FA01';
            }else{
                return response()->json(['El documento es incorrecto'], 422);
            }
        }
        $correlativo = 1;
        if($ultima_venta = VentaAdministrador::where('serie', $serie)->orderBy('correlativo', 'desc')->first()){
            $correlativo = $ultima_venta->correlativo + 1;
        }

        $venta_administrador = new VentaAdministrador;
        $venta_administrador->usuario_id = \Auth::user()->id;
        $venta_administrador->tienda_id = \Auth::user()->tienda_id ? \Auth::user()->tienda_id : Tienda::first()->id;
        $venta_administrador->persona_dni = $persona_dni;
        $venta_administrador->empresa_ruc = $empresa_ruc;
        $venta_administrador->tipo_documento = $tipo_documento;
        $venta_administrador->serie = $serie;
        $venta_administrador->correlativo = $correlativo;
        $venta_administrador->fecha_emision = Carbon::now();
        $venta_administrador->tipo_moneda = 'PEN';
        $venta_administrador->valor_venta = 0;
        $venta_administrador->igv = 0;
        $venta_administrador->total = 0;
        $venta_administrador->save();

        foreach($request->detalles as $detalle)
        {
            DetalleVentaAdministrador::create([
                'venta_administrador_id' => $venta_administrador->id,
                'descripcion' => mb_strtoupper($detalle['descripcion']),
                'cantidad' => $detalle['cantidad'],
                'valor_unitario' => $detalle['valor_unitario'],
                'valor_venta' => $detalle['importe_total_detalle'],
                'tipo_afectacion_igv' => '20',
                'precio_unitario' => $detalle['valor_unitario']
            ]);

            $venta_administrador->valor_venta += $detalle['importe_total_detalle'];
            $venta_administrador->total += $detalle['importe_total_detalle'];
            $venta_administrador->save();
        }

        SendVentaAdministradorSunat::dispatch($venta_administrador);

        return $venta_administrador;
    }

    public function mostrarComprobante(Request $request)
    {
        $cont = 1;
        $venta_administrador = VentaAdministrador::find($request->id);
        $letras = $this->numtoletras($venta_administrador->total);
        $dataQR = Tienda::find($venta_administrador->tienda_id)->ruc.'|'.
            ($venta_administrador->empresa_ruc ? '01|' : '03|').
            $venta_administrador->serie.'|'.$venta_administrador->correlativo.'|'.
            '0.00|'.
            $venta_administrador->total.'|'.
            \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $venta_administrador->fecha_emision)->format('d-m-Y').'|'.
            ($venta_administrador->empresa_ruc ? '6|' : '1|').
            ($venta_administrador->empresa_ruc ? $venta_administrador->empresa_ruc : ($venta_administrador->persona_dni ? $venta_administrador->persona_dni : '00000000'));
        $rutaQR = storage_path('app/public/qrTemp.svg');
        \QrCode::generate($dataQR, $rutaQR);
    
        $pdf = \PDF::loadView('emisionComprobantes.pdfs.comprobanteElectronico', compact('venta_administrador', 'letras'));
        $pdf->setPaper( array( 0 , 0 , 204 , 950 + $cont * 40 ), "portrait" );
        return $pdf->stream();
    }

    private function numtoletras($xcifra)
    {
        $xarray = array(0 => "Cero", 1 => "UN", "DOS", "TRES", "CUATRO", "CINCO", "SEIS", "SIETE", "OCHO", "NUEVE", "DIEZ", "ONCE",
            "DOCE", "TRECE", "CATORCE", "QUINCE", "DIECISEIS", "DIECISIETE", "DIECIOCHO", "DIECINUEVE", "VEINTI", 30 => "TREINTA",
            40 => "CUARENTA", 50 => "CINCUENTA", 60 => "SESENTA", 70 => "SETENTA", 80 => "OCHENTA", 90 => "NOVENTA", 100 => "CIENTO",
            200 => "DOSCIENTOS", 300 => "TRESCIENTOS", 400 => "CUATROCIENTOS", 500 => "QUINIENTOS", 600 => "SEISCIENTOS",
            700 => "SETECIENTOS", 800 => "OCHOCIENTOS", 900 => "NOVECIENTOS"
        );
  
        $xcifra = trim($xcifra);
        $xlength = strlen($xcifra);
        $xpos_punto = strpos($xcifra, ".");
        $xaux_int = $xcifra;
        $xdecimales = "00";
        if (!($xpos_punto === false)) {

            if ($xpos_punto == 0) {

                $xcifra = "0" . $xcifra;
                $xpos_punto = strpos($xcifra, ".");
            }
            $xaux_int = substr($xcifra, 0, $xpos_punto); // obtengo el entero de la cifra a covertir
            $xdecimales = substr($xcifra . "00", $xpos_punto + 1, 2); // obtengo los valores decimales
        }
  
        $XAUX = str_pad($xaux_int, 18, " ", STR_PAD_LEFT);
        $xcadena = "";
        for ($xz = 0; $xz < 3; $xz++)
        {
            $xaux = substr($XAUX, $xz * 6, 6);
            $xi = 0;
            $xlimite = 6; // inicializo el contador de centenas xi y establezco el límite a 6 dígitos en la parte entera
            $xexit = true; // bandera para controlar el ciclo del While
            while ($xexit) {
                if ($xi == $xlimite) { // si ya llegó al límite máximo de enteros
                        break; // termina el ciclo
                }

                $x3digitos = ($xlimite - $xi) * -1; // comienzo con los tres primeros digitos de la cifra, comenzando por la izquierda
                $xaux = substr($xaux, $x3digitos, abs($x3digitos)); // obtengo la centena (los tres dígitos)
                for ($xy = 1; $xy < 4; $xy++)
                { // ciclo para revisar centenas, decenas y unidades, en ese orden
                    switch ($xy)
                    {
                            case 1: // checa las centenas
                                if (substr($xaux, 0, 3) < 100) { 
                                    // si el grupo de tres dígitos es menor a una centena ( < 99) no hace nada y pasa a revisar las decenas

                                } else {
                                        $key = (int) substr($xaux, 0, 3);
                                        if (TRUE === array_key_exists($key, $xarray)){  // busco si la centena es número redondo (100, 200, 300, 400, etc..)
                                                $xseek = $xarray[$key];
                                                $xsub = $this->subfijo($xaux); // devuelve el subfijo correspondiente (Millón, Millones, Mil o nada)
                                                if (substr($xaux, 0, 3) == 100)
                                                        $xcadena = " " . $xcadena . " CIEN " . $xsub;
                                                else
                                                        $xcadena = " " . $xcadena . " " . $xseek . " " . $xsub;
                                                $xy = 3; // la centena fue redonda, entonces termino el ciclo del for y ya no reviso decenas ni unidades
                                        }
                                        else { // entra aquí si la centena no fue numero redondo (101, 253, 120, 980, etc.)
                                                $key = (int) substr($xaux, 0, 1) * 100;
                                                $xseek = $xarray[$key]; // toma el primer caracter de la centena y lo multiplica por cien y lo busca en el arreglo (para que busque 100,200,300, etc)
                                                $xcadena = " " . $xcadena . " " . $xseek;
                                        } // ENDIF ($xseek)
                                } // ENDIF (substr($xaux, 0, 3) < 100)
                                break;
                            case 2: // checa las decenas (con la misma lógica que las centenas)
                                if (substr($xaux, 1, 2) < 10) {
                                    //
                                } else {
                                        $key = (int) substr($xaux, 1, 2);
                                        if (TRUE === array_key_exists($key, $xarray)) {
                                            $xseek = $xarray[$key];
                                            $xsub = $this->subfijo($xaux);
                                            if (substr($xaux, 1, 2) == 20)
                                                    $xcadena = " " . $xcadena . " VEINTE " . $xsub;
                                            else
                                                    $xcadena = " " . $xcadena . " " . $xseek . " " . $xsub;
                                            $xy = 3;
                                        }
                                        else {
                                                $key = (int) substr($xaux, 1, 1) * 10;
                                                $xseek = $xarray[$key];
                                                if (20 == substr($xaux, 1, 1) * 10)
                                                        $xcadena = " " . $xcadena . " " . $xseek;
                                                else
                                                        $xcadena = " " . $xcadena . " " . $xseek . " Y ";
                                        } // ENDIF ($xseek)
                                } // ENDIF (substr($xaux, 1, 2) < 10)
                                break;
                            case 3: // checa las unidades
                                if (substr($xaux, 2, 1) < 1) { // si la unidad es cero, ya no hace nada

                                } else {
                                    $key = (int) substr($xaux, 2, 1);
                                    $xseek = $xarray[$key]; // obtengo directamente el valor de la unidad (del uno al nueve)
                                    $xsub = $this->subfijo($xaux);
                                    $xcadena = " " . $xcadena . " " . $xseek . " " . $xsub;
                                } // ENDIF (substr($xaux, 2, 1) < 1)
                                break;
                    } // END SWITCH
                } // END FOR
                $xi = $xi + 3;
            } // ENDDO

            if (substr(trim($xcadena), -5, 5) == "ILLON") // si la cadena obtenida termina en MILLON o BILLON, entonces le agrega al final la conjuncion DE
                $xcadena.= " DE";

            if (substr(trim($xcadena), -7, 7) == "ILLONES") // si la cadena obtenida en MILLONES o BILLONES, entoncea le agrega al final la conjuncion DE
                $xcadena.= " DE";

            // ----------- esta línea la puedes cambiar de acuerdo a tus necesidades o a tu país -------
            if (trim($xaux) != "") {
                    switch ($xz) {
                            case 0:
                                    if (trim(substr($XAUX, $xz * 6, 6)) == "1")
                                            $xcadena.= "UN BILLON ";
                                    else
                                            $xcadena.= " BILLONES ";
                                    break;
                            case 1:
                                    if (trim(substr($XAUX, $xz * 6, 6)) == "1")
                                            $xcadena.= "UN MILLON ";
                                    else
                                            $xcadena.= " MILLONES ";
                                    break;
                            case 2:
                                    if ($xcifra < 1) {
                                            $xcadena = "CERO Y $xdecimales/100 SOLES";
                                    }
                                    if ($xcifra >= 1 && $xcifra < 2) {
                                            $xcadena = "UNO Y $xdecimales/100 SOLES";
                                    }
                                    if ($xcifra >= 2) {
                                            $xcadena.= " Y $xdecimales/100 SOLES"; //
                                    }
                                    break;
                    } // endswitch ($xz)
            } // ENDIF (trim($xaux) != "")
            // ------------------      en este caso, para México se usa esta leyenda     ----------------
            $xcadena = str_replace("VEINTI ", "VEINTI", $xcadena); // quito el espacio para el VEINTI, para que quede: VEINTICUATRO, VEINTIUN, VEINTIDOS, etc
            $xcadena = str_replace("  ", " ", $xcadena); // quito espacios dobles
            $xcadena = str_replace("UN UN", "UN", $xcadena); // quito la duplicidad
            $xcadena = str_replace("  ", " ", $xcadena); // quito espacios dobles
            $xcadena = str_replace("BILLON DE MILLONES", "BILLON DE", $xcadena); // corrigo la leyenda
            $xcadena = str_replace("BILLONES DE MILLONES", "BILLONES DE", $xcadena); // corrigo la leyenda
            $xcadena = str_replace("DE UN", "UN", $xcadena); // corrigo la leyenda
        }
        return trim($xcadena);
    }
  
    private function subfijo($xx){
        $xx = trim($xx);
        $xstrlen = strlen($xx);
        if ($xstrlen == 1 || $xstrlen == 2 || $xstrlen == 3)
                $xsub = "";
  
        if ($xstrlen == 4 || $xstrlen == 5 || $xstrlen == 6)
                $xsub = "MIL";
  
        return $xsub;
    }

    public function listarComprobantes(Request $request)
    {
        $line_quantity = intVal($request->current);
        $line_number = intVal($request->rowCount);
        $where = $request->searchPhrase;
        $sort = $request->sort;
    
        if (isset($sort['serie_numero'])) {
            $order_by = 'serie_numero';
            $order_name = $sort['serie_numero'];
        }
        if (isset($sort['fecha_emision'])) {
            $order_by = 'ventas_administradores.fecha_emision';
            $order_name = $sort['fecha_emision'];
        }
        if (isset($sort['documento_cliente'])) {
            $order_by = 'documento_cliente';
            $order_name = $sort['documento_cliente'];
        }
        if (isset($sort['cliente'])) {
            $order_by = 'cliente';
            $order_name = $sort['cliente'];
        }
        if (isset($sort['total'])) {
            $order_by = 'ventas_administradores.total';
            $order_name = $sort['total'];
        }
        if (isset($sort['estado'])) {
            $order_by = 'estado';
            $order_name = $sort['estado'];
        }
    
        $skip = 0;
        $take = $line_number;
    
        if ($line_quantity > 1) {
            //DESDE QUE REGISTRO SE INICIA
            $skip = $line_number * ($line_quantity - 1);
            //CANTIDAD DE RANGO
            $take = $line_number;
        }
    
        //Grupo de datos que enviaremos al modelo para filtrar
        if ($request->rowCount < 0) {
    
        } else {
            if (empty($where)) {
                $ventas_administradores = VentaAdministrador::leftJoin('personas as p', 'p.dni', '=', 'ventas_administradores.persona_dni')
                    ->leftJoin('empresas as e', 'e.ruc', '=', 'ventas_administradores.empresa_ruc')
                    ->leftJoin('notas_creditos as nc', 'nc.numero_documento_afectado', '=', \DB::raw("concat(ventas_administradores.serie, '-', ventas_administradores.correlativo)"))
                    ->leftJoin('detalles_comunicaciones_bajas as dcb', \DB::raw("concat(dcb.serie, '-', dcb.correlativo)"), '=', \DB::raw("concat(ventas_administradores.serie, '-', ventas_administradores.correlativo)"))
                    ->leftJoin('comunicaciones_bajas as cb', 'cb.id', '=', 'dcb.comunicacion_baja_id')
                    ->select(
                        'ventas_administradores.id',
                        \DB::raw("date_format(ventas_administradores.fecha_emision, '%d/%m/%Y') as fecha_emision"),
                        \DB::raw("case
                            when e.ruc is not null then e.ruc
                            when p.dni is not null then p.dni
                            else '00000000'
                        end as documento_cliente"),
                        \DB::raw("case
                            when e.ruc is not null then e.nombre
                            when p.dni is not null then concat(p.nombres, ' ', p.apellidos)
                            else 'CLIENTE VARIOS'
                        end as cliente"),
                        \DB::raw("concat(ventas_administradores.serie, '-', ventas_administradores.correlativo) as serie_numero"),
                        \DB::raw("format(ventas_administradores.total, 2) as total"),
                        \DB::raw("case
                            when nc.id is not null then concat('De baja con Nota de Crédito ', nc.serie, '-', nc.correlativo)
                            when cb.id is not null then concat('De baja con Comunicación de Baja RA-', replace(cb.fecha_comunicacion, '-', ''), '-', cb.correlativo)
                            when ventas_administradores.estado_sunat is not null then ventas_administradores.estado_sunat
                            else 'EN PROCESO'
                        end as estado")
                    )
                    ->offset($skip)
                    ->limit($take)
                    ->orderBy($order_by, $order_name)
                    ->get()
                ;
            } else {
                $ventas_administradores = VentaAdministrador::leftJoin('personas as p', 'p.dni', '=', 'ventas_administradores.persona_dni')
                    ->leftJoin('empresas as e', 'e.ruc', '=', 'ventas_administradores.empresa_ruc')
                    ->select(
                        'ventas_administradores.id',
                        \DB::raw("date_format(ventas_administradores.fecha_emision, '%d/%m/%Y') as fecha_emision"),
                        \DB::raw("case
                            when e.ruc is not null then e.ruc
                            when p.dni is not null then p.dni
                            else '00000000'
                        end as documento_cliente"),
                        \DB::raw("case
                            when e.ruc is not null then e.nombre
                            when p.dni is not null then concat(p.nombres, ' ', p.apellidos)
                            else 'CLIENTE VARIOS'
                        end as cliente"),
                        \DB::raw("concat(ventas_administradores.serie, '-', ventas_administradores.correlativo) as serie_numero"),
                        \DB::raw("format(ventas_administradores.total, 2) as total")
                    )
                    ->where(\DB::raw("concat(ventas_administradores.serie, '-', ventas_administradores.correlativo)"),  'like', '%'.$where.'%')
                    ->orWhere(\DB::raw("date_format(ventas_administradores.fecha_emision, '%d/%m/%Y')"),  'like', '%'.$where.'%')
                    ->orWhere(\DB::raw("case
                        when e.ruc is not null then e.ruc
                        when p.dni is not null then p.dni
                        else '00000000'
                    end"),  'like', '%'.$where.'%')
                    ->orWhere(\DB::raw("case
                        when e.ruc is not null then e.nombre
                        when p.dni is not null then concat(p.nombres, ' ', p.apellidos)
                        else 'CLIENTE VARIOS'
                    end"),  'like', '%'.$where.'%')
                    ->offset($skip)
                    ->limit($take)
                    ->orderBy($order_by, $order_name)
                    ->get()
                ;
            }
        
            if (empty($where)) {
                $total = VentaAdministrador::leftJoin('personas as p', 'p.dni', '=', 'ventas_administradores.persona_dni')
                    ->leftJoin('empresas as e', 'e.ruc', '=', 'ventas_administradores.empresa_ruc')
                    ->leftJoin('notas_creditos as nc', 'nc.numero_documento_afectado', '=', \DB::raw("concat(ventas_administradores.serie, '-', ventas_administradores.correlativo)"))
                    ->leftJoin('detalles_comunicaciones_bajas as dcb', \DB::raw("concat(dcb.serie, '-', dcb.correlativo)"), '=', \DB::raw("concat(ventas_administradores.serie, '-', ventas_administradores.correlativo)"))
                    ->leftJoin('comunicaciones_bajas as cb', 'cb.id', '=', 'dcb.comunicacion_baja_id')
                    ->get()
                ;
        
                $total = count($total);
            } else {
                $total = VentaAdministrador::leftJoin('personas as p', 'p.dni', '=', 'ventas_administradores.persona_dni')
                    ->leftJoin('empresas as e', 'e.ruc', '=', 'ventas_administradores.empresa_ruc')
                    ->where(\DB::raw("concat(ventas_administradores.serie, '-', ventas_administradores.correlativo)"),  'like', '%'.$where.'%')
                    ->orWhere(\DB::raw("date_format(ventas_administradores.fecha_emision, '%d/%m/%Y')"),  'like', '%'.$where.'%')
                    ->orWhere(\DB::raw("case
                        when e.ruc is not null then e.ruc
                        when p.dni is not null then p.dni
                        else '00000000'
                    end"),  'like', '%'.$where.'%')
                    ->orWhere(\DB::raw("case
                        when e.ruc is not null then e.nombre
                        when p.dni is not null then concat(p.nombres, ' ', p.apellidos)
                        else 'CLIENTE VARIOS'
                    end"),  'like', '%'.$where.'%')
                    ->get()
                ;
        
                $total = count($total);
            }
        }
    
        return response()->json(
          array(
            'current' => $line_quantity,
            'rowCount' => $line_number,
            'rows' => $ventas_administradores,
            'total' => $total,
            'skip' => $skip,
            'take' => $take
          )
        );
        
    }

    public function mdlAnularComprobante(Request $request)
    {
        $venta_administrador = VentaAdministrador::find($request->id);
        if($venta_administrador->codigo_sunat == null || $venta_administrador->codigo_sunat != 0){
            return response()->json(['Este comprobante no fue enviado a SUNAT.'], 422);
        }
        if(NotaCredito::where('numero_documento_afectado', $venta_administrador->serie.'-'.$venta_administrador->correlativo)->first()){
            return response()->json(['Este comprobante ya tiene una nota de crédito asociada.'], 422);
        }
        if(DetalleComunicacionBaja::where('serie', $venta_administrador->serie)->where('correlativo', $venta_administrador->correlativo)->first()){
            return response()->json(['Este comprobante ya tiene una comunicacion de baja.'], 422);
        }
        
        return VentaAdministrador::select(
                'ventas_administradores.id',
                \DB::raw("concat(ventas_administradores.serie, '-', ventas_administradores.correlativo) as numeracion"),
                \DB::raw("case
                    when substring(ventas_administradores.serie, 1, 1) = 'B' then 'NOTA DE CRÉDITO'
                    else case
                        when datediff(curdate(), ventas_administradores.fecha_emision) > 5 then 'NOTA DE CRÉDITO'
                        else 'COMUNICACIÓN DE BAJA'
                    end
                end as tipo_anulacion")
            )
            ->where('ventas_administradores.id', $request->id)
            ->first()
        ;        
    }

    public function anularComprobante(Request $request)
    {
        $venta_administrador = VentaAdministrador::find($request->id);

        if(substr($venta_administrador->serie, 0, 1) == 'B'){
            return $this->generarNotaCredito($venta_administrador);
        }else{
            if(Carbon::createFromFormat('Y-m-d H:i:s', $venta_administrador->fecha_emision)->startOfDay()->diffInDays(Carbon::now()->startOfDay()) > 5){
                return $this->generarNotaCredito($venta_administrador);
            }else{
                return $this->generarComunicacionBaja($venta_administrador);
            }
        }
    }

    private function generarNotaCredito(VentaAdministrador $venta_administrador)
    {
        $tienda = Tienda::first();
        $serie = substr($venta_administrador->serie, 0, 1).'NC'.substr($tienda->serie, -1);
        $correlativo = 1;
        if($ultima_nota_credito = NotaCredito::where('serie', $serie)->orderBy('correlativo', 'desc')->first()){
            $correlativo = $ultima_nota_credito->correlativo + 1;
        }

        $nota_credito = new NotaCredito;
        $nota_credito->usuario_id = \Auth::user()->id;
        $nota_credito->tienda_id = $tienda->id;
        $nota_credito->serie = $serie;
        $nota_credito->correlativo = $correlativo;
        $nota_credito->fecha_emision = Carbon::now();
        $nota_credito->tipo_documento_afectado = substr($venta_administrador->serie, 0, 1) == 'B' ? '03' : '01';
        $nota_credito->numero_documento_afectado = $venta_administrador->serie.'-'.$venta_administrador->correlativo;
        $nota_credito->codigo_motivo = '01';
        $nota_credito->descripcion_motivo = 'ERROR EN LA VENTA';
        $nota_credito->tipo_moneda = 'PEN';
        $nota_credito->persona_dni = $venta_administrador->persona_dni;
        $nota_credito->empresa_ruc = $venta_administrador->empresa_ruc;
        $nota_credito->total = $venta_administrador->total;
        $nota_credito->save();

        foreach(DetalleVentaAdministrador::where('venta_administrador_id', $venta_administrador->id)->get() as $detalle)
        {
            $detalle_nota_credito = new DetalleNotaCredito;
            $detalle_nota_credito->nota_credito_id = $nota_credito->id;
            $detalle_nota_credito->producto_codigo = 'P002';
            $detalle_nota_credito->cantidad = $detalle->cantidad;
            $detalle_nota_credito->descripcion = $detalle->descripcion;
            $detalle_nota_credito->valor_unitario = $detalle->valor_unitario;
            $detalle_nota_credito->importe_detalle = $detalle->valor_venta;
            $detalle_nota_credito->save();
        }

        SendNotaCreditoSunat::dispatch($nota_credito);

        return $nota_credito;
    }

    private function generarComunicacionBaja(VentaAdministrador $venta_administrador)
    {
        $tienda = Tienda::first();
        $correlativo = 1;
        if($ultima_comunicacion_baja = ComunicacionBaja::where('fecha_comunicacion', Carbon::now()->format('Y-m-d'))
            ->orderBy('correlativo', 'desc')->first()){
            $correlativo = $ultima_comunicacion_baja->correlativo + 1;
        }

        $comunicacion_baja = ComunicacionBaja::create([
            'usuario_id' => \Auth::user()->id,
            'tienda_id' => $tienda->id,
            'correlativo' => $correlativo,
            'fecha_generacion' => Carbon::now()->format('Y-m-d'),
            'fecha_comunicacion' => Carbon::now()->format('Y-m-d')
        ]);

        DetalleComunicacionBaja::create([
            'comunicacion_baja_id' => $comunicacion_baja->id,
            'tipo_documento' =>  (substr($venta_administrador->serie, 0, 1) == 'B' ? '03' : '01'),
            'serie' => $venta_administrador->serie,
            'correlativo' => $venta_administrador->correlativo,
            'descripcion' => 'ERROR EN LA VENTA'
        ]);

        SendComunicacionBajaSunat::dispatch($comunicacion_baja);

        return $comunicacion_baja;
    }
}
