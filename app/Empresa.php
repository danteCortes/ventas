<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Empresa extends Model{

  public $primaryKey = 'ruc';
  
  protected $casts = [
    'ruc'=>'string',
  ];

  public $timestamps = false;
}
