<?php

namespace App\Console\Commands;

use App\Cierre;
use App\Usuario;
use Illuminate\Console\Command;

class CerrarCajas extends Command
{
    protected $signature = 'caja:cerrar';

    protected $description = 'Cierra todas las cajas abiertas de los usuarios.';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $cierres = Cierre::where('estado', 1)->get();
        foreach($cierres as $cierre)
        {
            $cierre->estado = 0;
            $cierre->save();
        }
        $usuarios = Usuario::get();
        foreach($usuarios as $usuario)
        {
            $usuario->estado_caja = 0;
            $usuario->save();
        }
    }
}
