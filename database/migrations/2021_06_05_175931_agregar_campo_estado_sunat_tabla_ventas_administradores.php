<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AgregarCampoEstadoSunatTablaVentasAdministradores extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ventas_administradores', function (Blueprint $table) {
            $table->text('estado_sunat')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ventas_administradores', function (Blueprint $table) {
            $table->dropColumn('estado_sunat');
        });
    }
}
