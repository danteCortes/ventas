<?php

namespace App\Console\Commands;

use App\ComunicacionBaja;
use App\Jobs\SendComunicacionBajaSunat;
use Illuminate\Console\Command;

class SendComunicacionBajaProcesoSunat extends Command
{
    protected $signature = 'sunat:comunicacion-baja';

    protected $description = 'Envío de Comunicaciones de baja Eectrónicas';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $comunicaciones_bajas = ComunicacionBaja::whereNull('codigo_sunat')->get();
        foreach($comunicaciones_bajas as $comunicacion_baja)
        {
            SendComunicacionBajaSunat::dispatch(ComunicacionBaja::find($comunicacion_baja->id));
        }
    }
}
