<?php

namespace App\Console\Commands;

use App\NotaCredito;
use App\Jobs\SendNotaCreditoSunat;
use Illuminate\Console\Command;

class SendNotaCreditoProcesoSunat extends Command
{
    protected $signature = 'sunat:nota-credito';

    protected $description = 'Envío de Notas de Crédito Electrónicas.';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        foreach(NotaCredito::whereNull('codigo_sunat')->get() as $nota_credito)
        {
            SendNotaCreditoSunat::dispatch($nota_credito);
        }
    }
}
