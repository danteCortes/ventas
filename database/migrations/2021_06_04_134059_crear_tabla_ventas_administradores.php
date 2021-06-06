<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CrearTablaVentasAdministradores extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ventas_administradores', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('usuario_id');
            $table->foreign('usuario_id')->on('usuarios')->references('id')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedInteger('tienda_id');
            $table->foreign('tienda_id')->on('tiendas')->references('id')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->string('persona_dni', 8)->nullable()->collation('utf8_spanish2_ci');
            $table->foreign('persona_dni')->on('personas')->references('dni')
                ->onUpdate('cascade')->onDelete('set null');
            $table->string('empresa_ruc', 11)->nullable()->collation('utf8_spanish2_ci');
            $table->foreign('empresa_ruc')->on('empresas')->references('ruc')
                ->onUpdate('cascade')->onDelete('set null');
            $table->string('tipo_documento', 2);
            $table->string('serie', 4);
            $table->unsignedInteger('correlativo');
            $table->datetime('fecha_emision');
            $table->string('tipo_moneda', 3);
            $table->float('valor_venta');
            $table->float('igv');
            $table->float('total');
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
        Schema::dropIfExists('ventas_administradores');
    }
}
