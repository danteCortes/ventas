<?php

namespace App\Console\Commands;

use App\VentaAdministrador;
use App\Jobs\SendVentaAdministradorSunat;
use Illuminate\Console\Command;

class SendVentaAdministradorProcesoSunat extends Command
{
    protected $signature = 'sunat:venta-administrador';

    protected $description = 'Envío de Ventas de Administrador en proceso a SUNAT.';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        foreach(VentaAdministrador::whereNull('codigo_sunat')->get() as $venta_administrador)
        {
            SendVentaAdministradorSunat::dispatch($venta_administrador);
        }
    }
}
