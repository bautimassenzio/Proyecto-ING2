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
        Schema::create('pagos', function (Blueprint $table) {
            $table->integer('id_pago', true);
            $table->integer('id_reserva')->index('id_reserva');
            $table->decimal('monto', 10);
            $table->timestamp('fecha_pago')->useCurrent();
            $table->enum('metodo_pago', ['mercadopago', 'tarjeta']);
            $table->enum('estado_pago', ['completo', 'pendiente', 'error'])->nullable()->default('pendiente');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pagos');
    }
};
