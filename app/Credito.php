<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Credito extends Model{

  public function detalles(){
    return $this->hasMany('\App\Detalle');
  }

  public function persona(){
    return $this->belongsTo('\App\Persona', 'persona_dni', 'dni');
  }

  public function usuario(){
    return $this->belongsTo('\App\Usuario');
  }

  public function pagos(){
    return $this->hasMany('\App\Pago');
  }

  public function getTotalAttribute($value){
    return number_format($value, 2, '.', '');
  }

  public function getCreatedAtAttribute($value){
    return \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $value)->format('d/m/Y');
  }

  public function getFechaAttribute($value){
    if ($value) {
      return \Carbon\Carbon::createFromFormat('Y-m-d', $value)->format('d/m/Y');
    }
    return "INDEFINIDO";
  }

}
