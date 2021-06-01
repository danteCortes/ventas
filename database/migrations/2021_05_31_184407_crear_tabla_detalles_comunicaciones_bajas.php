<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CrearTablaDetallesComunicacionesBajas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detalles_comunicaciones_bajas', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('comunicacion_baja_id');
            $table->foreign('comunicacion_baja_id')->on('comunicaciones_bajas')->references('id')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->string('tipo_documento', 2);
            $table->string('serie', 4);
            $table->unsignedInteger('correlativo');
            $table->string('descripcion');
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
        Schema::dropIfExists('detalles_comunicaciones_bajas');
    }
}
