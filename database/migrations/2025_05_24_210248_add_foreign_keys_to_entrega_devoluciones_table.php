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
        Schema::table('entrega_devoluciones', function (Blueprint $table) {
            $table->foreign(['id_reserva'], 'entrega_devoluciones_ibfk_1')->references(['id_reserva'])->on('reservas');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('entrega_devoluciones', function (Blueprint $table) {
            $table->dropForeign('entrega_devoluciones_ibfk_1');
        });
    }
};
