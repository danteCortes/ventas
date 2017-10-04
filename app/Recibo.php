<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Recibo extends Model{

  public $timestamps = false;

  public function venta(){
    return $this->belongsTo('\App\Venta');
  }

  public function persona(){
    return $this->belongsTo('\App\Persona', 'persona_dni');
  }

  public function empresa(){
    return $this->belongsTo('\App\Empresa', 'empresa_ruc');
  }
}
