<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Descuento extends Model{

  public function linea(){
    return $this->belongsTo("\App\Linea");
  }

  public function familia(){
    return $this->belongsTo("\App\Familia");
  }

  public function marca(){
    return $this->belongsTo("\App\Marca");
  }

  public function tienda(){
    return $this->belongsTo("\App\Tienda");
  }
}
