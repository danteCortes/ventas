<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cambio extends Model{

  public function venta(){
    return $this->belongsTo('\App\Venta');
  }
}
