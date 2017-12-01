<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Venta extends Model{

  public function getTotalAttribute($valor){
    return number_format($valor, 2, '.', ' ');
  }

  /*public function getUpdatedAtAttribute($value){
    return \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $value)->format('d/m/Y H:i A');
  }*/

  public function detalles(){
    return $this->hasMany('\App\Detalle');
  }

  public function cierre(){
    return $this->belongsTo('\App\Cierre');
  }

  public function usuario(){
    return $this->belongsTo('\App\Usuario');
  }
  
  public function tarjetaVenta(){
    return $this->hasOne('\App\TarjetaVenta');
  }

  public function recibo(){
    return $this->hasOne('\App\Recibo');
  }

  public function tienda(){
    return $this->belongsTo('\App\Tienda');
  }

  public function efectivo(){
    return $this->hasOne('\App\Efectivo');
  }

  public function dolar(){
    return $this->hasOne('\App\Dolar');
  }

  public function cambio(){
    return $this->hasOne('\App\Cambio');
  }

  public function reclamo(){
    return $this->hasOne('\App\Reclamo');
  }
}
