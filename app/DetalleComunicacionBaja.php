<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DetalleComunicacionBaja extends Model
{
    protected $table = 'detalles_comunicaciones_bajas';

    protected $fillable = ['comunicacion_baja_id', 'tipo_documento', 'serie', 'correlativo', 'descripcion'];
}
