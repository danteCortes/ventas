<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Compra extends Model{

 	public function detalles(){
 		return $this->hasMany('\App\Detalle');
 	}

  public function proveedor(){
    return $this->belongsTo('\App\Proveedor');
  }

  public function usuario(){
    return $this->belongsTo('\App\Usuario');
  }

  public function getTotalAttribute($value){
    return number_format($value, 2, '.', ' ');
  }

  public function getCreatedAtAttribute($value){
    return Carbon::createFromFormat('Y-m-d H:i:s', $value)->format('d/m/Y H:i A');
  }

  public function getUpdatedAtAttribute($value){
    return Carbon::createFromFormat('Y-m-d H:i:s', $value)->format('d/m/Y H:i A');
  }
}
