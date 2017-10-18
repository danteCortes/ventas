<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cierre extends Model{

  public function ventas(){
    return $this->hasMany('\App\Venta');
  }

  public function creditos(){
    return $this->hasMany('\App\Credito');
  }

  public function cambios(){
    return $this->hasMany('\App\Cambio');
  }

  public function prestamos(){
    return $this->hasMany('\App\Prestamo');
  }
}
