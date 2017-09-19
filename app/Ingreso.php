<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ingreso extends Model{

  public function productoTienda(){
    return $this->belongsTo('\App\productoTienda');
  }
}
