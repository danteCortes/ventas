<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DetalleVentaAdministrador extends Model
{
    protected $table = 'detalles_ventas_administradores';

    protected $fillable = ['venta_administrador_id', 'descripcion', 'cantidad', 'valor_unitario', 'valor_venta', 'tipo_afectacion_igv',
        'precio_unitario'];
}
