<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CrearTablaDetallesNotasCreditos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detalles_notas_creditos', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('nota_credito_id');
            $table->foreign('nota_credito_id')->on('notas_creditos')->references('id')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->string('producto_codigo');
            $table->unsignedInteger('cantidad');
            $table->string('descripcion');
            $table->float('valor_unitario');
            $table->float('importe_detalle');
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
        Schema::dropIfExists('detalles_notas_creditos');
    }
}
