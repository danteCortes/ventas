<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TarjetaVenta extends Model{

  protected $table = 'tarjeta_venta';

  public $timestamps = false;

  public function tarjeta(){
    return $this->belongsTo('\App\Tarjeta');
  }

  public function getComisionAttribute($value){
    return number_format($value, 2, '.', ' ');
  }

  public function getMontoAttribute($value){
    return number_format($value, 2, '.', ' ');
  }
}
