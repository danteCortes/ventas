<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Traslado extends Model
{
	public function detalle(){
	    return $this->hasMany('\App\Detalle', 'traslado_id', 'id');
	}
}
