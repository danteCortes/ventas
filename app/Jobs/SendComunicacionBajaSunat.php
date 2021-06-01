<?php

namespace App\Jobs;

use App\Certificado;
use App\DetalleComunicacionBaja;
use App\Empresa;
use App\Persona;
use App\Tienda;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use Greenter\See;
use Greenter\Model\Company\Company;
use Greenter\Model\Company\Address;
use Greenter\Model\Voided\Voided;
use Greenter\Model\Voided\VoidedDetail;
use Greenter\Ws\Services\SunatEndpoints;

class SendComunicacionBajaSunat implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $comunicacion_baja;

    public function __construct($comunicacion_baja)
    {
        $this->comunicacion_baja = $comunicacion_baja;
    }

    public function handle()
    {
        try {
            $certificado = Certificado::where('tienda_id', $this->comunicacion_baja->tienda_id)->first();
            $tienda = Tienda::find($this->comunicacion_baja->tienda_id);

            $see = new See;
            $see->setCertificate(file_get_contents(storage_path('app/public/certificados/certificado'.$tienda->id.'.pem')));
            $see->setService($tienda->produccion ? SunatEndpoints::FE_PRODUCCION : SunatEndpoints::FE_BETA);
            $see->setClaveSOL($tienda->ruc, $certificado->usuario_sunat, $certificado->clave_sunat);

            $voided = $this->getVoided();
            
            $result = $see->send($voided);

            if (!$result->isSuccess()) {
                // Mostrar error al conectarse a SUNAT.
                \Log::error('Codigo Error: '.$result->getError()->getCode());
                \Log::error('Mensaje Error: '.$result->getError()->getMessage());
            }else{
                $ticket = $result->getTicket();
                $result = $see->getStatus($ticket);
                if (!$result->isSuccess()) {
                    // Mostrar error al conectarse a SUNAT.
                    \Log::error('Codigo Error: '.$result->getError()->getCode());
                    \Log::error('Mensaje Error: '.$result->getError()->getMessage());
                }else{
                    // Guardamos el CDR
                    if(!is_dir(storage_path('app/public/documentos'))){
                        mkdir(storage_path('app/public/documentos'));
                    }
                    if(!is_dir(storage_path('app/public/documentos/cdrs'.$tienda->id))){
                        mkdir(storage_path('app/public/documentos/cdrs'.$tienda->id));
                    }
                    file_put_contents(storage_path('app/public/documentos/cdrs'.$tienda->id.'/R-'.$voided->getName().'.zip'), 
                        $result->getCdrZip());
    
                    $cdr = $result->getCdrResponse();
    
                    $code = (int)$cdr->getCode();
    
                    if ($code === 0) {
                        \Log::info('ESTADO: ACEPTADA'.PHP_EOL);
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
                    
                    \Log::info($cdr->getDescription().PHP_EOL);
            
                    $this->comunicacion_baja->codigo_sunat = $code;
                    $this->comunicacion_baja->estado_sunat = $cdr->getDescription();
                    $this->comunicacion_baja->save();
                }
            }
        } catch (\Throwable $th) {
            \Log::error($th);
        }
    }

    private function getDireccion()
    {
        $address = (new Address)
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

    private function getDetalles()
    {
        $detalles = [];
        foreach(DetalleComunicacionBaja::where('comunicacion_baja_id', $this->comunicacion_baja->id)->get() as $detalle)
        {
            $detail = (new VoidedDetail)
                ->setTipoDoc($detalle->tipo_documento)
                ->setSerie($detalle->serie)
                ->setCorrelativo($detalle->correlativo)
                ->setDesMotivoBaja($detalle->descripcion)
            ;
            array_push($detalles, $detail);
        }

        return $detalles;
    }

    private function getVoided()
    {
        return (new Voided)
            ->setCorrelativo($this->comunicacion_baja->correlativo)
            ->setFecGeneracion(new \DateTime($this->comunicacion_baja->fecha_generacion))
            ->setFecComunicacion(new \DateTime($this->comunicacion_baja->fecha_comunicacion))
            ->setCompany($this->getEmpresa())
            ->setDetails($this->getDetalles())
        ;
    }
}
