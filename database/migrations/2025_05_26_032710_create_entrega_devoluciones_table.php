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
        Schema::create('entrega_devoluciones', function (Blueprint $table) {
            $table->integer('id_registro', true);
            $table->integer('id_reserva')->index('id_reserva');
            $table->date('fecha_entrega')->nullable();
            $table->date('fecha_devolucion')->nullable();
            $table->text('observaciones')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('entrega_devoluciones');
    }
};
