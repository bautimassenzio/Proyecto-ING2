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
        Schema::create('reservas', function (Blueprint $table) {
            $table->integer('id_reserva', true);
            $table->integer('id_cliente')->index('id_cliente');
            $table->integer('id_empleado')->nullable()->index('id_empleado');
            $table->integer('id_maquinaria')->index('id_maquinaria');
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->timestamp('fecha_reserva')->useCurrent();
            $table->enum('estado', ['pendiente', 'aprobada', 'finalizada', 'cancelada'])->nullable()->default('pendiente');
            $table->decimal('total', 10)->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reservas');
    }
};
