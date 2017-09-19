<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model{

  protected $table = 'proveedores';

  public $timestamps = false;

  public function empresa(){
    return $this->belongsTo('\App\Empresa', 'empresa_ruc', 'ruc');
  }
}
