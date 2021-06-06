<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AgregarCampoCodigoSunatTablaVentasAdministradores extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ventas_administradores', function (Blueprint $table) {
            $table->string('codigo_sunat', 4)->nullable();
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
            $table->dropColumn('codigo_sunat');
        });
    }
}
