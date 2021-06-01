<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CrearTablaComunicacionesBajas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comunicaciones_bajas', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('usuario_id')->nullable();
            $table->foreign('usuario_id')->on('usuarios')->references('id')
                ->onUpdate('cascade')->onDelete('set null');
            $table->unsignedInteger('cierre_id')->nullable();
            $table->foreign('cierre_id')->on('cierres')->references('id')
                ->onUpdate('cascade')->onDelete('set null');
            $table->unsignedInteger('tienda_id')->nullable();
            $table->foreign('tienda_id')->on('tiendas')->references('id')
                ->onUpdate('cascade')->onDelete('set null');
            $table->unsignedInteger('correlativo');
            $table->date('fecha_generacion');
            $table->date('fecha_comunicacion');
            $table->string('codigo_sunat', 4)->nullable();
            $table->text('estado_sunat')->nullable();
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
        Schema::dropIfExists('comunicaciones_bajas');
    }
}
