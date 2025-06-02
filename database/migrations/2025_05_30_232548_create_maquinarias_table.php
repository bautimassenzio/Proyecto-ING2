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
        Schema::create('maquinarias', function (Blueprint $table) {
            $table->integer('id_maquinaria', true);
            $table->string('nro_inventario', 50)->unique('nro_inventario');
            $table->string('marca', 100);
            $table->string('modelo', 100);
            $table->year('anio');
            $table->enum('tipo_energia', ['electrica', 'combustion']);
            $table->string('uso', 100);
            $table->string('localidad', 100);
            $table->decimal('precio_dia', 10);
            $table->string('foto_url')->nullable();
            $table->enum('estado', ['disponible', 'inactiva'])->nullable()->default('disponible');
            $table->integer('id_politica')->nullable()->index('id_politica');
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
        Schema::dropIfExists('maquinarias');
    }
};
