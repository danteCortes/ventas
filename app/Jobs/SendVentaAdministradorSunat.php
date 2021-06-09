<?php

namespace App\Jobs;

use App\Certificado;
use App\Empresa;
use App\Persona;
use App\Tienda;
use App\DetalleVentaAdministrador;

use Greenter\Ws\Services\SunatEndpoints;
use Greenter\See;

use Greenter\Model\Client\Client;
use Greenter\Model\Company\Company;
use Greenter\Model\Company\Address;
use Greenter\Model\Sale\FormaPagos\FormaPagoContado;
use Greenter\Model\Sale\Invoice;
use Greenter\Model\Sale\SaleDetail;
use Greenter\Model\Sale\Legend;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendVentaAdministradorSunat implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $venta_administrador;

    public function __construct($venta_administrador)
    {
        $this->venta_administrador = $venta_administrador;
    }

    public function handle()
    {
        try{
            $certificado = Certificado::where('tienda_id', $this->venta_administrador->tienda_id)->first();
            $tienda = Tienda::find($this->venta_administrador->tienda_id);

            $see = new See;
            $see->setCertificate(file_get_contents(storage_path('app/public/certificados/certificado'.$tienda->id.'.pem')));
            $see->setService($tienda->produccion ? SunatEndpoints::FE_PRODUCCION : SunatEndpoints::FE_BETA);
            $see->setClaveSOL($tienda->ruc, $certificado->usuario_sunat, $certificado->clave_sunat);

            $invoice = $this->getInvoice();

            $result = $see->send($invoice);

            // Verificamos que la conexión con SUNAT fue exitosa.
            if (!$result->isSuccess()) {
                // Mostrar error al conectarse a SUNAT.
                \Log::error('Codigo Error: '.$result->getError()->getCode());
                \Log::error('Mensaje Error: '.$result->getError()->getMessage());
        
                $this->venta_administrador->codigo_sunat = $result->getError()->getCode();
                $this->venta_administrador->estado_sunat = $result->getError()->getMessage();
                $this->venta_administrador->save();
            }else{
        
                // Guardamos el CDR
                if(!is_dir(storage_path('app/public/documentos'))){
                    mkdir(storage_path('app/public/documentos'));
                }
                if(!is_dir(storage_path('app/public/documentos/cdrs'.$tienda->id))){
                    mkdir(storage_path('app/public/documentos/cdrs'.$tienda->id));
                }
                file_put_contents(storage_path('app/public/documentos/cdrs'.$tienda->id.'/R-'.$invoice->getName().'.zip'), 
                    $result->getCdrZip());
        
                $cdr = $result->getCdrResponse();
                
                $code = (int)$cdr->getCode();
                
                if ($code === 0) {
                        // \Log::info('ESTADO: ACEPTADA'.PHP_EOL);
                        if (count($cdr->getNotes()) > 0) {
                            \Log::info('OBSERVACIONES:'.PHP_EOL);
                            // Corregir estas observaciones en siguientes emisiones.
                            \Log::info(var_dump($cdr->getNotes()));
                        }  
                } else if ($code >= 2000 && $code <= 3999) {
                        \Log::error('ESTADO: RECHAZADA'.PHP_EOL);
                } else {
                        /* Esto no debería darse, pero si ocurre, es un CDR inválido que debería tratarse como un error-excepción. */
                        /*code: 0100 a 1999 */
                        \Log::error('Excepción');
                }
                
                // \Log::info($cdr->getDescription().PHP_EOL);
        
                $this->venta_administrador->codigo_sunat = $code;
                $this->venta_administrador->estado_sunat = $cdr->getDescription();
                $this->venta_administrador->save();
            }
        }catch(\Exception $error){
            \Log::error($error);
        }        
    }

    private function getDireccion()
    {
        // Emisor
        $address = (new Address())
            ->setUbigueo('100101')
            ->setDepartamento('HUANUCO')
            ->setProvincia('HUANUCO')
            ->setDistrito('HUANUCO')
            ->setUrbanizacion('-')
            ->setDireccion('JR. GENERAL PRADO NRO. 584')
            ->setCodLocal('0000')
        ;
        return $address;
    }

    private function getEmpresa()
    {
        $company = (new Company)
            ->setRuc('20601867835')
            ->setRazonSocial('TIENDAS TU R&L E.I.R.L.')
            ->setNombreComercial('TIENDAS TU')
            ->setAddress($this->getDireccion())
        ;
        return $company;
    }

    private function getCliente()
    {
        if($empresa = Empresa::find($this->venta_administrador->empresa_ruc)){
            $tipo_doc = '6';
            $documento = $empresa->ruc;
            $denominacion = $empresa->nombre;
        }elseif($persona = Persona::find($this->venta_administrador->persona_dni)){
            $tipo_doc = '1';
            $documento = $persona->dni;
            $denominacion = $persona->nombres.' '.$persona->apellidos;
        }else{
            $tipo_doc = '1';
            $documento = '00000000';
            $denominacion = 'CLIENTE VARIOS';
        }
        // Cliente
        $client = (new Client)
            ->setTipoDoc($tipo_doc)
            ->setNumDoc($documento)
            ->setRznSocial($denominacion)
        ;
        return $client;
    }

    private function getDetalles()
    {
        $detalles = [];
        foreach(DetalleVentaAdministrador::where('venta_administrador_id', $this->venta_administrador->id)->get() as $detalle)
        {
            $item = (new SaleDetail)
                ->setCodProducto('P002')
                ->setUnidad('NIU') // Unidad - Catalog. 03
                ->setCantidad($detalle->cantidad)
                ->setMtoValorUnitario($detalle->valor_unitario)
                ->setDescripcion($detalle->descripcion)
                ->setMtoBaseIgv($detalle->valor_venta)
                ->setPorcentajeIgv(18.00) // 18%
                ->setIgv(0)
                ->setTipAfeIgv('20') // Gravado Op. Onerosa - Catalog. 07
                ->setTotalImpuestos(0) // Suma de impuestos en el detalle
                ->setMtoValorVenta($detalle->valor_venta)
                ->setMtoPrecioUnitario($detalle->precio_unitario)
            ;
            array_push($detalles, $item);
        }
        return $detalles;
    }

    private function getInvoice()
    {
        $invoice = (new Invoice)
            ->setUblVersion('2.1')
            ->setTipoOperacion('0101') // Venta - Catalog. 51
            ->setTipoDoc($this->venta_administrador->tipo_documento) // Factura - Catalog. 01 
            ->setSerie($this->venta_administrador->serie)
            ->setCorrelativo($this->venta_administrador->correlativo)
            ->setFechaEmision(new \DateTime($this->venta_administrador->fecha_emision)) // Zona horaria: Lima
            ->setFormaPago(new FormaPagoContado()) // FormaPago: Contado
            ->setTipoMoneda($this->venta_administrador->tipo_moneda) // Sol - Catalog. 02
            ->setCompany($this->getEmpresa())
            ->setClient($this->getCliente())
            ->setMtoOperExoneradas($this->venta_administrador->valor_venta)
            ->setMtoIGV($this->venta_administrador->igv)
            ->setTotalImpuestos($this->venta_administrador->igv)
            ->setValorVenta($this->venta_administrador->valor_venta)
            ->setSubTotal($this->venta_administrador->total)
            ->setMtoImpVenta($this->venta_administrador->total)
        ;
        
        $legend = (new Legend)
            ->setCode('1000') // Monto en letras - Catalog. 52
            ->setValue($this->numtoletras($this->venta_administrador->total))
        ;
        
        $invoice->setDetails($this->getDetalles())
            ->setLegends([$legend])
        ;

        return $invoice;
    }

    private function numtoletras($xcifra)
    {
        $xarray = array(0 => "Cero",
                1 => "UN", "DOS", "TRES", "CUATRO", "CINCO", "SEIS", "SIETE", "OCHO", "NUEVE",
                "DIEZ", "ONCE", "DOCE", "TRECE", "CATORCE", "QUINCE", "DIECISEIS", "DIECISIETE", "DIECIOCHO", "DIECINUEVE",
                "VEINTI", 30 => "TREINTA", 40 => "CUARENTA", 50 => "CINCUENTA", 60 => "SESENTA", 70 => "SETENTA", 80 => "OCHENTA", 90 => "NOVENTA",
                100 => "CIENTO", 200 => "DOSCIENTOS", 300 => "TRESCIENTOS", 400 => "CUATROCIENTOS", 500 => "QUINIENTOS", 600 => "SEISCIENTOS", 700 => "SETECIENTOS", 800 => "OCHOCIENTOS", 900 => "NOVECIENTOS"
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
        for ($xz = 0; $xz < 3; $xz++) {
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
                        for ($xy = 1; $xy < 4; $xy++) { // ciclo para revisar centenas, decenas y unidades, en ese orden
                                switch ($xy) {
                                        case 1: // checa las centenas
                                                if (substr($xaux, 0, 3) < 100) { // si el grupo de tres dígitos es menor a una centena ( < 99) no hace nada y pasa a revisar las decenas
  
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
  
    private function subfijo($xx)
    {
        $xx = trim($xx);
        $xstrlen = strlen($xx);
        if ($xstrlen == 1 || $xstrlen == 2 || $xstrlen == 3)
                $xsub = "";
  
        if ($xstrlen == 4 || $xstrlen == 5 || $xstrlen == 6)
                $xsub = "MIL";
  
        return $xsub;
    }
}
