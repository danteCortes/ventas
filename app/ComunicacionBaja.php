<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ComunicacionBaja extends Model
{
    protected $table = 'comunicaciones_bajas';

    protected $fillable = ['usuario_id', 'cierre_id', 'tienda_id', 'correlativo', 'fecha_generacion', 'fecha_comunicacion'];
}
