<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reservas', function (Blueprint $table) {
            $table->foreign(['id_maquinaria'], 'reservas_ibfk_3')->references(['id_maquinaria'])->on('maquinarias');
            $table->foreign(['id_empleado'], 'reservas_ibfk_2')->references(['id_usuario'])->on('usuarios');
            $table->foreign(['id_cliente'], 'reservas_ibfk_1')->references(['id_usuario'])->on('usuarios');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('reservas', function (Blueprint $table) {
            $table->dropForeign('reservas_ibfk_3');
            $table->dropForeign('reservas_ibfk_2');
            $table->dropForeign('reservas_ibfk_1');
        });
    }
};
