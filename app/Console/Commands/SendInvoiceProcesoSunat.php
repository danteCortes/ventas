<?php

namespace App\Console\Commands;

use App\Certificado;
use App\Tienda;
use App\Venta;
use App\Jobs\SendInvoiceSunat;
use Illuminate\Console\Command;

use Greenter\Model\Response\StatusCdrResult;
use Greenter\Ws\Services\ConsultCdrService;
use Greenter\Ws\Services\SoapClient;
use Greenter\Ws\Services\SunatEndpoints;

class SendInvoiceProcesoSunat extends Command
{
    protected $signature = 'sunat:invoice';

    protected $description = 'Envío de Boletas y Facturas Electrónicas.';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $ventas = Venta::join('recibos as r', 'r.venta_id', '=', 'ventas.id')
            ->select('ventas.*', 'r.numeracion')
            ->whereNull('ventas.codigo_sunat')
            ->where(\DB::raw("length(substring_index(r.numeracion, '-', 1))"), 4)
            ->get()
        ;
        foreach($ventas as $venta)
        {
            $venta_consultada = Venta::find($venta->id);
            $tienda = Tienda::find($venta->tienda_id);
            $certificado = Certificado::where('tienda_id', $venta->tienda_id)->first();
            $service = $this->getCdrStatusService($tienda->ruc.$certificado->usuario_sunat, $certificado->clave_sunat);
            
            $arguments = [
                $tienda->ruc,
                substr(explode('-', $venta->numeracion)[0], 0, 1) == 'B' ? '03' : '01',
                explode('-', $venta->numeracion)[0],
                intval(explode('-', $venta->numeracion)[1])
            ];
            
            $result = $service->getStatusCdr(...$arguments);
            if ($result->getCdrZip()) {
                $filename = 'R-'.implode('-', $arguments).'.zip';
                $this->savedFile($filename, $result->getCdrZip(), $tienda);
            }
            if ($result->isSuccess()):

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
        
                $venta_consultada->codigo_sunat = $code;
                $venta_consultada->estado_sunat = $cdr->getDescription();
                $venta_consultada->save();
            else:
                \Log::error('Codigo Error: '.$result->getError()->getCode());
                \Log::error('Mensaje Error: '.$result->getError()->getMessage());
                SendInvoiceSunat::dispatch(Venta::find($venta->id));
            endif;
        }
    }

    private function getCdrStatusService(?string $user, ?string $password): ConsultCdrService
    {
        $ws = new SoapClient(SunatEndpoints::FE_CONSULTA_CDR.'?wsdl');
        $ws->setCredentials($user, $password);
    
        $service = new ConsultCdrService;
        $service->setClient($ws);
    
        return $service;
    }

    private function savedFile(?string $filename, ?string $content, ?Tienda $tienda): void
    {
        if(!is_dir(storage_path('app/public/documentos'))){
            mkdir(storage_path('app/public/documentos'));
        }
        if(!is_dir(storage_path('app/public/documentos/cdrs'.$tienda->id))){
            mkdir(storage_path('app/public/documentos/cdrs'.$tienda->id));
        }
        
        $pathZip = storage_path('app/public/documentos/cdrs'.$tienda->id).DIRECTORY_SEPARATOR.$filename;
        file_put_contents($pathZip, $content);
    }
}
