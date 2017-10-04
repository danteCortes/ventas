<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Persona extends Model{

  public $primaryKey = 'dni';

  protected $casts = [
    'dni'=>'string',
  ];

  public $timestamps = false;
}
