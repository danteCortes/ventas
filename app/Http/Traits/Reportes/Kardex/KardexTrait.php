<?php

namespace App\Http\Traits\Reportes\Kardex;

use Illuminate\Http\Request;
use App\Http\Traits\Reportes\Kardex\IngresosTrait;
use App\Http\Traits\Reportes\Kardex\SalidasTrait;

trait KardexTrait{

  use IngresosTrait, SalidasTrait;

/**
 * Este método fusiona todos los detalles de ingreso a la tienda que hubo de un
 * determinado producto entre fechas establecidas por el usuario.
 * @param array
 * @return array
 */
public function detalles(array $datos){

  $detalles = [];

  $this->juntarSalidas($detalles, $this->totalIngresos($datos));
  $this->juntarSalidas($detalles, $this->totalSalidas($datos));

  usort($detalles, function($a, $b){
    return strtotime($a['fecha']) - strtotime($b['fecha']);
  });

  return $detalles;
}



public function saldoAnterior(Array $datos){

  $actual = \App\ProductoTienda::where('producto_codigo', $datos['producto_codigo'])
    ->where('tienda_id', $datos['tienda_id'])->first()->cantidad;

  $ingresos = $this->cantidadIngresada($this->totalIngresos($datos));
  $salidas = $this->cantidadSalida($this->totalSalidas($datos));
  $movimiento = $ingresos - $salidas;

  return $actual-$movimiento;

}

}

 ?>
