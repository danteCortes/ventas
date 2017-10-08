<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pago extends Model{

  public function cierre(){
    return $this->belongsTo('\App\Cierre');
  }

  public function getMontoAttribute($value){
    return number_format($value, 2, '.', '');
  }

  public function getCreatedAtAttribute($value){
    return \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $value)->format('d/m/Y');
  }
}
