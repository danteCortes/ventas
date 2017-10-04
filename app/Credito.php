<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Credito extends Model{

  public function detalles(){
    return $this->hasMany('\App\Detalle');
  }
}
