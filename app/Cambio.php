<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cambio extends Model{

  public function venta(){
    return $this->belongsTo('\App\Venta');
  }

  public function tarjetaVenta(){
    return $this->hasOne('\App\TarjetaVenta');
  }

  public function dolar(){
    return $this->hasOne('\App\Dolar');
  }
}
