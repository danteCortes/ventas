<?php

  $productosPorVencer = \DB::table('productos')->whereDate('vencimiento', '<=', date('Y-m-d', strtotime('+3 month')))
    ->whereDate('vencimiento', '>=', date('Y-m-d'))->get();

  $productosVencidos = \DB::table('productos')->whereDate('vencimiento', '<', date('Y-m-d'))->get();

 ?>
<a data-placement="bottom" data-original-title="Vencen dentro de 3 meses" href="{{url('reporte/por-vencer')}}" data-toggle="tooltip"
   class="btn btn-default btn-sm">
    <i class="fa fa-calendar-minus-o"></i>
    <span class="label label-warning">{{count($productosPorVencer)}}</span>
</a>
<a data-placement="bottom" data-original-title="Vencidos" href="{{url('reporte/vencidos')}}" data-toggle="tooltip"
   class="btn btn-default btn-sm">
    <i class="fa fa-calendar-times-o"></i>
    <span class="label label-danger">{{count($productosVencidos)}}</span>
</a>
