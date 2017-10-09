<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Detalle extends Model{

  public $timestamps = false;

  public function getPrecioUnidadAttribute($valor){
    return number_format($valor, 2, '.', ' ');
  }

  public function setPrecioUnidadAttribute($value){
    $this->attributes['precio_unidad'] = str_replace(' ', '', $value);
  }

  public function getTotalAttribute($valor){
    return number_format($valor, 2, '.', ' ');
  }

  public function setTotalAttribute($value){
    $this->attributes['total'] = str_replace(' ', '', $value);
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

  public function credito(){
  	return $this->belongsTo('\App\Credito');
  }

  public function prestamo(){
  	return $this->belongsTo('\App\Prestamo');
  }

  public function ingresos(){
    return $this->hasMany('\App\Ingreso');
  }
}
