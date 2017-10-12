<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Separacion extends Model{

  protected $table = 'separaciones';

  public function usuario(){
  	return $this->belongsTo('\App\Usuario');
  }

  public function persona(){
  	return $this->belongsTo('\App\Persona');
  }

  public function detalles(){
    return $this->hasMany('\App\Detalle');
  }

  public function getCreatedAtAttribute($value){
  	return \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $value)->format('d/m/Y');
  }
}
