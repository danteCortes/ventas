<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Recibo extends Model{

  public $timestamps = false;

  public function venta(){
    return $this->belongsTo('\App\Venta');
  }
}
