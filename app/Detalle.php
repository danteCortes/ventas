<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Detalle extends Model{

  public $timestamps = false;

  public function getPrecioUnidadAttribute($valor){
    return number_format($valor, 2, '.', ' ');
  }

  public function getTotalAttribute($valor){
    return number_format($valor, 2, '.', ' ');
  }

  public function producto(){
    return $this->belongsTo('\App\Producto', 'producto_codigo', 'codigo');
  }

  public function compra(){
  	return $this->belongsTo('\App\Compra');
  }

  public function venta(){
  	return $this->belongsTo('\App\Venta');
  }

  public function ingresos(){
    return $this->hasMany('\App\Ingreso');
  }
}
