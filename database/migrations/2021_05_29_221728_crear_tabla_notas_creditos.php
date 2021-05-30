<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CrearTablaNotasCreditos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notas_creditos', function (Blueprint $table) {
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
            $table->string('serie', 4);
            $table->unsignedInteger('correlativo');
            $table->datetime('fecha_emision');
            $table->string('tipo_documento_afectado', 2);
            $table->string('numero_documento_afectado', 12);
            $table->string('codigo_motivo', 2);
            $table->string('descripcion_motivo');
            $table->string('tipo_moneda');
            $table->string('persona_dni', 8)->nullable()->collation('utf8_spanish2_ci');
            $table->foreign('persona_dni')->on('personas')->references('dni')
                ->onUpdate('cascade')->onDelete('set null');
            $table->string('empresa_ruc', 11)->nullable()->collation('utf8_spanish2_ci');
            $table->foreign('empresa_ruc')->on('empresas')->references('ruc')
                ->onUpdate('cascade')->onDelete('set null');
            $table->float('total');
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
        Schema::dropIfExists('notas_creditos');
    }
}
