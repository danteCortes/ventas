<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductoTienda extends Model{

  protected $table = 'producto_tienda';

  public $timestamps = false;

  public function tienda(){
    return $this->belongsTo('\App\Tienda');
  }
}
