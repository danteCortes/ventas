<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Prestamo extends Model{

  public function detalles(){
    return $this->hasMany('\App\Detalle');
  }

  public function usuario(){
    return $this->belongsTo('\App\Usuario');
  }

  public function getCreatedAtAttribute($value){
    return \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $value)->format('d/m/Y');
  }

  public function getUpdatedAtAttribute($value){
    return \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $value)->format('d/m/Y');
  }

  public function getFechaAttribute($value){
    if ($value) {
      return \Carbon\Carbon::createFromFormat('Y-m-d', $value)->format('d/m/Y');
    }
    return "INDEFINIDO";
  }

  public function getDireccionAttribute($value){
    if ($value == 1) {
      return [$value, "PRESTAMO SALIDA"];
    }
    return [$value, "PRESTAMO ENTRADA"];
  }
}
