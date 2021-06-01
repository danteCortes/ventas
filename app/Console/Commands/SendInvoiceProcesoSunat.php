<?php

namespace App\Console\Commands;

use App\Venta;
use App\Jobs\SendInvoiceSunat;
use Illuminate\Console\Command;

class SendInvoiceProcesoSunat extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sunat:invoice';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envío de Boletas y Facturas Electrónicas.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $ventas = Venta::join('recibos as r', 'r.venta_id', '=', 'ventas.id')
            ->select('ventas.*')
            ->whereNull('ventas.codigo_sunat')
            ->where(\DB::raw("length(substring_index(r.numeracion, '-', 1))"), 4)
            ->get()
        ;
        foreach($ventas as $venta)
        {
            SendInvoiceSunat::dispatch(Venta::find($venta->id));
        }
    }
}
