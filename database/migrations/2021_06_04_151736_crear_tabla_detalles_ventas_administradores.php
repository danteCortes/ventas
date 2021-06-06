<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CrearTablaDetallesVentasAdministradores extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detalles_ventas_administradores', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('venta_administrador_id');
            $table->foreign('venta_administrador_id')->on('ventas_administradores')->references('id')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->string('descripcion');
            $table->unsignedInteger('cantidad');
            $table->float('valor_unitario');
            $table->float('valor_venta');
            $table->string('tipo_afectacion_igv', 2);
            $table->float('precio_unitario');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('detalles_ventas_administradores');
    }
}
