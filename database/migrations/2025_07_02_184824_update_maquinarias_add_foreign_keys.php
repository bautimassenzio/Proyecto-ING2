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
    Schema::table('maquinarias', function (Blueprint $table) {
        // Eliminás las columnas viejas
        $table->dropColumn(['uso', 'localidad']);

        // Agregás las nuevas columnas con clave foránea
        $table->unsignedBigInteger('tipo_de_uso_id')->nullable();
        $table->unsignedBigInteger('localidad_id')->nullable();

        // Definís las claves foráneas
        $table->foreign('tipo_de_uso_id')->references('id')->on('tipos_de_uso')->onDelete('set null');
        $table->foreign('localidad_id')->references('id')->on('localidades')->onDelete('set null');
    });
}

public function down()
{
    Schema::table('maquinarias', function (Blueprint $table) {
        // Eliminás las claves foráneas
        $table->dropForeign(['tipo_de_uso_id']);
        $table->dropForeign(['localidad_id']);

        // Eliminás las columnas nuevas
        $table->dropColumn(['tipo_de_uso_id', 'localidad_id']);

        // Volvés a agregar las columnas viejas
        $table->string('uso')->nullable();
        $table->string('localidad')->nullable();
    });
}

};
