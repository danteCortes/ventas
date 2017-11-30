<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ingreso extends Model{

  public function productoTienda(){
    return $this->belongsTo('\App\ProductoTienda');
  }

  public function detalle(){
    return $this->belongsTo('\App\Detalle');
  }
}
