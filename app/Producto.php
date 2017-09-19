<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model{

  public $primaryKey = 'codigo';

  protected $casts = [
      'codigo' => 'string',
  ];

  public $increment = false;

  public $timestamps = false;

  public function getPrecioAttribute($value){
    return number_format($value, 2, '.', ' ');
  }

  public function linea(){
    return $this->belongsTo('\App\Linea');
  }

  public function familia(){
    return $this->belongsTo('\App\Familia');
  }

  public function marca(){
    return $this->belongsTo('\App\Marca');
  }

  public function productoTiendas(){
    return $this->hasMany('\App\ProductoTienda', 'producto_codigo', 'codigo');
  }

}
