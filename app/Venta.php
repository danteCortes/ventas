<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Venta extends Model{

  public function getTotalAttribute($valor){
    return number_format($valor, 2, '.', ' ');
  }

  public function detalles(){
    return $this->hasMany('\App\Detalle');
  }

  public function cierre(){
    return $this->belongsTo('\App\Cierre');
  }

  public function tarjetaVenta(){
    return $this->hasOne('\App\TarjetaVenta');
  }
}
